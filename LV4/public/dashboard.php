<?php
// dashboard.php - Administracijsko sučelje
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
zahtijevajPrijavu();

if (!jeAdmin()) {
    header('Location: index.php');
    exit;
}

$poruka = '';
$poruka_tip = '';

// CSV Import filmova
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['import_csv'])) {
    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($file, 'r');
        $header = fgetcsv($handle); // Preskoči zaglavlje
        $count = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) >= 7) {
                $stmt = $pdo->prepare("INSERT INTO filmovi (naslov, zanr, godina, trajanje, ocjena, reziser, zemlja) VALUES (?, ?, ?, ?, ?, ?, ?)");
                try {
                    $stmt->execute([$row[0], $row[1], (int)$row[2], (int)$row[3], (float)$row[4], $row[5], $row[6]]);
                    $count++;
                } catch (PDOException $e) {
                    // Preskoči duplikate
                }
            }
        }
        fclose($handle);
        $poruka = "Uspješno uvezeno $count filmova iz CSV-a.";
        $poruka_tip = 'success';
    } else {
        $poruka = 'Greška pri uploadu CSV datoteke.';
        $poruka_tip = 'error';
    }
}

// Statistike
$ukupno_filmova    = $pdo->query("SELECT COUNT(*) FROM filmovi")->fetchColumn();
$ukupno_korisnika  = $pdo->query("SELECT COUNT(*) FROM korisnici")->fetchColumn();
$ukupno_videoteka  = $pdo->query("SELECT COUNT(*) FROM zeljeni_filmovi")->fetchColumn();
$ukupno_ocjena     = $pdo->query("SELECT COUNT(*) FROM ocjene")->fetchColumn();
$prosjecna_ocjena   = $pdo->query("SELECT ROUND(AVG(ocjena),1) FROM filmovi")->fetchColumn();

// Najaktivniji korisnici
$aktivni = $pdo->query("
    SELECT k.korisnicko_ime, COUNT(zf.id_filma) AS broj_filmova
    FROM korisnici k
    LEFT JOIN zeljeni_filmovi zf ON k.id = zf.id_korisnika
    GROUP BY k.id
    ORDER BY broj_filmova DESC
    LIMIT 5
")->fetchAll();

$pageTitle = 'Admin Dashboard';
require_once __DIR__ . '/includes/header.php';
?>

<h1>Admin Dashboard</h1>

<?php if ($poruka): ?>
    <div class="alert alert-<?= $poruka_tip ?>"><?= htmlspecialchars($poruka) ?></div>
<?php endif; ?>

<!-- STATISTIKE -->
<div class="dashboard-grid">
    <div class="stat-card">
        <h3><?= $ukupno_filmova ?></h3>
        <p>Filmova</p>
    </div>
    <div class="stat-card">
        <h3><?= $ukupno_korisnika ?></h3>
        <p>Korisnika</p>
    </div>
    <div class="stat-card">
        <h3><?= $ukupno_videoteka ?></h3>
        <p>Filmova u videotekama</p>
    </div>
    <div class="stat-card">
        <h3><?= $ukupno_ocjena ?></h3>
        <p>Ocjena slika</p>
    </div>
    <div class="stat-card">
        <h3><?= $prosjecna_ocjena ?: '0' ?></h3>
        <p>Prosječna ocjena filmova</p>
    </div>
</div>

<!-- UPRAVLJANJE FILMOVIMA -->
<div class="admin-section">
    <h2>Upravljanje filmovima</h2>
    <a href="film_add.php" class="btn btn-primary" style="width:auto; display:inline-block; margin-bottom:15px;">+ Dodaj novi film</a>

    <h3>Uvoz filmova iz CSV-a</h3>
    <p>CSV format: Naslov, Žanr, Godina, Trajanje_min, Ocjena, Režiser, Zemlja</p>
    <form method="POST" action="dashboard.php" enctype="multipart/form-data" class="csv-form">
        <input type="file" name="csv_file" accept=".csv" required>
        <button type="submit" name="import_csv" class="btn-small">Uvezi CSV</button>
    </form>
</div>

<!-- NAJAKTIVNIJI KORISNICI -->
<div class="admin-section">
    <h2>Najaktivniji korisnici</h2>
    <table>
        <thead>
            <tr><th>Korisnik</th><th>Filmova u videoteci</th></tr>
        </thead>
        <tbody>
            <?php foreach ($aktivni as $k): ?>
            <tr>
                <td><?= htmlspecialchars($k['korisnicko_ime']) ?></td>
                <td><?= $k['broj_filmova'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- SVIH KORISNIKA -->
<div class="admin-section">
    <h2>Svi korisnici</h2>
    <table>
        <thead>
            <tr><th>ID</th><th>Korisničko ime</th><th>Email</th><th>Uloga</th><th>Registriran</th></tr>
        </thead>
        <tbody>
            <?php
            $svi_korisnici = $pdo->query("SELECT * FROM korisnici ORDER BY id")->fetchAll();
            foreach ($svi_korisnici as $k):
            ?>
            <tr>
                <td><?= $k['id'] ?></td>
                <td><?= htmlspecialchars($k['korisnicko_ime']) ?></td>
                <td><?= htmlspecialchars($k['email']) ?></td>
                <td><?= $k['uloga'] ?></td>
                <td><?= date('d.m.Y', strtotime($k['created_at'])) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
