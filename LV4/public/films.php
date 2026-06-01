<?php
// films.php - Prikaz i filtriranje filmova iz baze
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

$poruka = '';
$poruka_tip = '';

// Dodavanje filma u videoteku
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dodaj_film'])) {
    zahtijevajPrijavu();
    $id_filma = (int) $_POST['id_filma'];

    // Provjeri prosječnu ocjenu
    $stmt = $pdo->prepare("SELECT ocjena FROM filmovi WHERE id = ?");
    $stmt->execute([$id_filma]);
    $film = $stmt->fetch();

    // Provjeri duplikat
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM zeljeni_filmovi WHERE id_korisnika = ? AND id_filma = ?");
    $stmt->execute([getKorisnikId(), $id_filma]);
    $postoji = $stmt->fetchColumn();

    if ($postoji) {
        $poruka = 'Film je već u vašoj videoteci!';
        $poruka_tip = 'error';
    } else {
        $stmt = $pdo->prepare("INSERT INTO zeljeni_filmovi (id_korisnika, id_filma) VALUES (?, ?)");
        $stmt->execute([getKorisnikId(), $id_filma]);

        if ($film && $film['ocjena'] < 5.0) {
            $poruka = 'Film dodan u videoteku. ⚠️ Upozorenje: Ovaj film ima nisku ocjenu (' . $film['ocjena'] . ') – jeste li sigurni da ga želite zadržati?';
            $poruka_tip = 'warning';
        } else {
            $poruka = 'Film uspješno dodan u vašu videoteku!';
            $poruka_tip = 'success';
        }
    }
}

// Filtriranje
$where = [];
$params = [];

$filter_zanr   = $_GET['zanr'] ?? '';
$filter_godina  = $_GET['godina'] ?? '';
$filter_zemlja  = $_GET['zemlja'] ?? '';
$filter_search  = $_GET['search'] ?? '';

if (!empty($filter_zanr)) {
    $where[]  = "zanr LIKE ?";
    $params[] = "%$filter_zanr%";
}
if (!empty($filter_godina)) {
    $where[]  = "godina = ?";
    $params[] = (int) $filter_godina;
}
if (!empty($filter_zemlja)) {
    $where[]  = "zemlja LIKE ?";
    $params[] = "%$filter_zemlja%";
}
if (!empty($filter_search)) {
    $where[]  = "naslov LIKE ?";
    $params[] = "%$filter_search%";
}

$sql = "SELECT * FROM filmovi";
if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY ocjena DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$filmovi = $stmt->fetchAll();

// Dohvati jedinstvene žanrove i godine za filtere
$zanrovi = $pdo->query("SELECT DISTINCT zanr FROM filmovi ORDER BY zanr")->fetchAll(PDO::FETCH_COLUMN);
$godine  = $pdo->query("SELECT DISTINCT godina FROM filmovi ORDER BY godina DESC")->fetchAll(PDO::FETCH_COLUMN);
$zemlje  = $pdo->query("SELECT DISTINCT zemlja FROM filmovi ORDER BY zemlja")->fetchAll(PDO::FETCH_COLUMN);

$pageTitle = 'Filmovi';
require_once __DIR__ . '/includes/header.php';
?>

<h1>Filmovi iz baze podataka</h1>

<?php if ($poruka): ?>
    <div class="alert alert-<?= $poruka_tip ?>"><?= htmlspecialchars($poruka) ?></div>
<?php endif; ?>

<!-- FILTRI (serverski) -->
<div class="filters-container">
    <h2>Filtri</h2>
    <form method="GET" action="films.php" class="filter-form">
        <div class="filter-group">
            <label for="search">Pretraživanje:</label>
            <input type="text" id="search" name="search" value="<?= htmlspecialchars($filter_search) ?>" placeholder="Naziv filma...">
        </div>
        <div class="filter-group">
            <label for="zanr">Žanr:</label>
            <select id="zanr" name="zanr">
                <option value="">-- Svi žanrovi --</option>
                <?php foreach ($zanrovi as $z): ?>
                    <option value="<?= htmlspecialchars($z) ?>" <?= $filter_zanr === $z ? 'selected' : '' ?>><?= htmlspecialchars($z) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="filter-group">
            <label for="godina">Godina:</label>
            <select id="godina" name="godina">
                <option value="">-- Sve godine --</option>
                <?php foreach ($godine as $g): ?>
                    <option value="<?= $g ?>" <?= $filter_godina == $g ? 'selected' : '' ?>><?= $g ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="filter-group">
            <label for="zemlja">Država:</label>
            <select id="zemlja" name="zemlja">
                <option value="">-- Sve države --</option>
                <?php foreach ($zemlje as $z): ?>
                    <option value="<?= htmlspecialchars($z) ?>" <?= $filter_zemlja === $z ? 'selected' : '' ?>><?= htmlspecialchars($z) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Filtriraj</button>
        <a href="films.php" class="btn btn-reset">Resetiraj</a>
    </form>
</div>

<!-- TABLICA FILMOVA -->
<div class="table-wrapper" style="max-width:1200px; margin:20px auto; padding:0 20px;">
    <p><strong>Pronađeno filmova: <?= count($filmovi) ?></strong></p>
    <table>
        <thead>
            <tr>
                <th>ID</th><th>Naslov</th><th>Žanr</th><th>Godina</th>
                <th>Trajanje</th><th>Ocjena</th><th>Režiser</th><th>Država</th>
                <?php if (jePrijavljen()): ?><th>Akcija</th><?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($filmovi as $film): ?>
            <tr>
                <td><?= $film['id'] ?></td>
                <td><?= htmlspecialchars($film['naslov']) ?></td>
                <td><?= htmlspecialchars($film['zanr']) ?></td>
                <td><?= $film['godina'] ?></td>
                <td><?= $film['trajanje'] ?> min</td>
                <td><?= $film['ocjena'] ?></td>
                <td><?= htmlspecialchars($film['reziser']) ?></td>
                <td><?= htmlspecialchars($film['zemlja']) ?></td>
                <?php if (jePrijavljen()): ?>
                <td>
                    <form method="POST" action="films.php" style="display:inline;">
                        <input type="hidden" name="id_filma" value="<?= $film['id'] ?>">
                        <button type="submit" name="dodaj_film" class="add-cart-btn">Dodaj u videoteku</button>
                    </form>
                </td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
