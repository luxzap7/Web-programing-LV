<?php
// film_edit.php - Uređivanje filma (samo admin)
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
zahtijevajPrijavu();

if (!jeAdmin()) {
    header('Location: films.php');
    exit;
}

$greska = '';
$uspjeh = '';
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: films.php');
    exit;
}

// Dohvati film
$stmt = $pdo->prepare("SELECT * FROM filmovi WHERE id = ?");
$stmt->execute([$id]);
$film = $stmt->fetch();

if (!$film) {
    header('Location: films.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $film['naslov']   = trim($_POST['naslov'] ?? '');
    $film['zanr']     = trim($_POST['zanr'] ?? '');
    $film['godina']   = trim($_POST['godina'] ?? '');
    $film['trajanje'] = trim($_POST['trajanje'] ?? '');
    $film['ocjena']   = trim($_POST['ocjena'] ?? '');
    $film['reziser']  = trim($_POST['reziser'] ?? '');
    $film['zemlja']   = trim($_POST['zemlja'] ?? '');

    // Serverska validacija
    if (empty($film['naslov'])) {
        $greska = 'Naslov filma je obavezan.';
    } elseif (strlen($film['naslov']) > 200) {
        $greska = 'Naslov ne smije biti duži od 200 znakova.';
    } elseif (empty($film['zanr'])) {
        $greska = 'Žanr je obavezan.';
    } elseif (empty($film['godina']) || !is_numeric($film['godina'])) {
        $greska = 'Godina mora biti broj.';
    } elseif ((int)$film['godina'] < 1888 || (int)$film['godina'] > (int)date('Y') + 1) {
        $greska = 'Godina mora biti između 1888 i ' . (date('Y') + 1) . '.';
    } elseif (empty($film['trajanje']) || !is_numeric($film['trajanje'])) {
        $greska = 'Trajanje mora biti broj.';
    } elseif ((int)$film['trajanje'] < 1 || (int)$film['trajanje'] > 600) {
        $greska = 'Trajanje mora biti između 1 i 600 minuta.';
    } elseif (empty($film['ocjena']) || !is_numeric($film['ocjena'])) {
        $greska = 'Ocjena mora biti broj.';
    } elseif ((float)$film['ocjena'] < 0 || (float)$film['ocjena'] > 10) {
        $greska = 'Ocjena mora biti između 0 i 10.';
    } elseif (empty($film['reziser'])) {
        $greska = 'Režiser je obavezan.';
    } elseif (empty($film['zemlja'])) {
        $greska = 'Država je obavezna.';
    } else {
        $stmt = $pdo->prepare("UPDATE filmovi SET naslov=?, zanr=?, godina=?, trajanje=?, ocjena=?, reziser=?, zemlja=? WHERE id=?");
        $stmt->execute([
            $film['naslov'], $film['zanr'], (int)$film['godina'],
            (int)$film['trajanje'], (float)$film['ocjena'],
            $film['reziser'], $film['zemlja'], $id
        ]);
        $uspjeh = 'Film uspješno ažuriran!';
    }
}

$pageTitle = 'Uredi film';
require_once __DIR__ . '/includes/header.php';
?>

<div class="form-container" style="max-width:600px;">
    <h2>Uredi film: <?= htmlspecialchars($film['naslov']) ?></h2>

    <?php if ($greska): ?>
        <div class="alert alert-error"><?= htmlspecialchars($greska) ?></div>
    <?php endif; ?>
    <?php if ($uspjeh): ?>
        <div class="alert alert-success"><?= htmlspecialchars($uspjeh) ?></div>
    <?php endif; ?>

    <form method="POST" action="film_edit.php?id=<?= $id ?>">
        <div class="form-group">
            <label for="naslov">Naslov: *</label>
            <input type="text" id="naslov" name="naslov" value="<?= htmlspecialchars($film['naslov']) ?>" required maxlength="200">
        </div>
        <div class="form-group">
            <label for="zanr">Žanr: *</label>
            <input type="text" id="zanr" name="zanr" value="<?= htmlspecialchars($film['zanr']) ?>" required>
        </div>
        <div class="form-group">
            <label for="godina">Godina: *</label>
            <input type="number" id="godina" name="godina" value="<?= htmlspecialchars($film['godina']) ?>" required min="1888" max="<?= date('Y')+1 ?>">
        </div>
        <div class="form-group">
            <label for="trajanje">Trajanje (min): *</label>
            <input type="number" id="trajanje" name="trajanje" value="<?= htmlspecialchars($film['trajanje']) ?>" required min="1" max="600">
        </div>
        <div class="form-group">
            <label for="ocjena">Ocjena (0-10): *</label>
            <input type="number" id="ocjena" name="ocjena" value="<?= htmlspecialchars($film['ocjena']) ?>" required min="0" max="10" step="0.1">
        </div>
        <div class="form-group">
            <label for="reziser">Režiser: *</label>
            <input type="text" id="reziser" name="reziser" value="<?= htmlspecialchars($film['reziser']) ?>" required>
        </div>
        <div class="form-group">
            <label for="zemlja">Država: *</label>
            <input type="text" id="zemlja" name="zemlja" value="<?= htmlspecialchars($film['zemlja']) ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Spremi promjene</button>
    </form>
    <p class="form-link"><a href="films.php">← Povratak na filmove</a></p>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
