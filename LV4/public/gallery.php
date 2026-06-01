<?php
// gallery.php - Galerija s ocjenjivanjem slika + upload
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

$poruka = '';
$poruka_tip = '';

// Upload slike
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_sliku'])) {
    zahtijevajPrijavu();

    if (isset($_FILES['slika']) && $_FILES['slika']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['slika'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        if (!in_array($ext, $allowed)) {
            $poruka = 'Dozvoljeni formati su JPEG i PNG.';
            $poruka_tip = 'error';
        } elseif ($file['size'] > $maxSize) {
            $poruka = 'Slika ne smije biti veća od 5MB.';
            $poruka_tip = 'error';
        } else {
            $newName = 'slika_' . time() . '_' . rand(1000,9999) . '.' . $ext;
            $dest = __DIR__ . '/images/' . $newName;

            if (move_uploaded_file($file['tmp_name'], $dest)) {
                $opis = trim($_POST['opis'] ?? $newName);
                $stmt = $pdo->prepare("INSERT INTO slike (naziv_datoteke, opis, putanja) VALUES (?, ?, ?)");
                $stmt->execute([$newName, $opis, 'images/' . $newName]);
                $poruka = 'Slika uspješno uploadana!';
                $poruka_tip = 'success';
            } else {
                $poruka = 'Greška pri uploadu slike.';
                $poruka_tip = 'error';
            }
        }
    } else {
        $poruka = 'Odaberite sliku za upload.';
        $poruka_tip = 'error';
    }
}

// Spremanje ocjene
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ocijeni'])) {
    zahtijevajPrijavu();
    $id_slike = (int) $_POST['id_slike'];
    $ocjena   = (int) $_POST['ocjena'];

    if ($ocjena >= 1 && $ocjena <= 5) {
        $stmt = $pdo->prepare("
            INSERT INTO ocjene (id_korisnika, id_slike, ocjena) 
            VALUES (?, ?, ?) 
            ON DUPLICATE KEY UPDATE ocjena = ?, vrijeme_ocjene = NOW()
        ");
        $stmt->execute([getKorisnikId(), $id_slike, $ocjena, $ocjena]);
        $poruka = 'Ocjena spremljena!';
        $poruka_tip = 'success';
    }
}

// Brisanje slike (admin)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['brisi_sliku']) && jeAdmin()) {
    $id_slike = (int) $_POST['id_slike'];
    $stmt = $pdo->prepare("SELECT putanja FROM slike WHERE id = ?");
    $stmt->execute([$id_slike]);
    $s = $stmt->fetch();
    if ($s) {
        @unlink(__DIR__ . '/' . $s['putanja']);
        $pdo->prepare("DELETE FROM ocjene WHERE id_slike = ?")->execute([$id_slike]);
        $pdo->prepare("DELETE FROM slike WHERE id = ?")->execute([$id_slike]);
        $poruka = 'Slika obrisana.';
        $poruka_tip = 'success';
    }
}

// Dohvati sve slike s prosječnom ocjenom
$slike = $pdo->query("
    SELECT s.*, 
           COALESCE(AVG(o.ocjena), 0) AS prosjecna_ocjena,
           COUNT(o.ocjena) AS broj_ocjena
    FROM slike s
    LEFT JOIN ocjene o ON s.id = o.id_slike
    GROUP BY s.id
    ORDER BY s.id
")->fetchAll();

// Dohvati ocjene trenutnog korisnika
$moje_ocjene = [];
if (jePrijavljen()) {
    $stmt = $pdo->prepare("SELECT id_slike, ocjena FROM ocjene WHERE id_korisnika = ?");
    $stmt->execute([getKorisnikId()]);
    foreach ($stmt->fetchAll() as $row) {
        $moje_ocjene[$row['id_slike']] = $row['ocjena'];
    }
}

$pageTitle = 'Galerija';
$extraCSS = ['style_slike.css'];
require_once __DIR__ . '/includes/header.php';
?>

<h1>Galerija slika</h1>

<?php if ($poruka): ?>
    <div class="alert alert-<?= $poruka_tip ?>"><?= htmlspecialchars($poruka) ?></div>
<?php endif; ?>

<?php if (!jePrijavljen()): ?>
    <p style="text-align:center;"><a href="login.php">Prijavite se</a> za ocjenjivanje i upload slika.</p>
<?php endif; ?>

<!-- UPLOAD SLIKE -->
<?php if (jePrijavljen()): ?>
<div class="form-container" style="max-width:600px;">
    <h2>Dodaj novu sliku</h2>
    <p style="font-size:0.9em; color:#666;">Dozvoljeni formati: JPEG, PNG. Maksimalna veličina: 5MB.</p>
    <form method="POST" action="gallery.php" enctype="multipart/form-data">
        <div class="form-group">
            <label for="slika">Odaberi sliku: *</label>
            <input type="file" id="slika" name="slika" accept=".jpg,.jpeg,.png" required>
        </div>
        <div class="form-group">
            <label for="opis">Opis slike:</label>
            <input type="text" id="opis" name="opis" placeholder="Kratki opis slike...">
        </div>
        <button type="submit" name="upload_sliku" class="btn btn-primary">Upload</button>
    </form>
</div>
<?php endif; ?>

<!-- GALERIJA -->
<section class="galerija" aria-label="Galerija slika">
    <?php foreach ($slike as $slika): ?>
        <figure class="galerija_slika">
            <a href="#lightbox<?= $slika['id'] ?>">
                <img src="<?= htmlspecialchars($slika['putanja']) ?>" 
                     alt="<?= htmlspecialchars($slika['opis'] ?? $slika['naziv_datoteke']) ?>" loading="lazy">
            </a>
            <figcaption>
                <?= htmlspecialchars($slika['opis'] ?? $slika['naziv_datoteke']) ?>
                <div class="rating-display">
                    <?php
                    $avg = round($slika['prosjecna_ocjena'], 1);
                    for ($i = 1; $i <= 5; $i++):
                    ?>
                        <span class="star <?= $i <= round($avg) ? 'star-filled' : '' ?>">★</span>
                    <?php endfor; ?>
                    <span class="rating-text"><?= $avg ?>/5 (<?= $slika['broj_ocjena'] ?> ocjena)</span>
                </div>

                <?php if (jePrijavljen()): ?>
                <form method="POST" action="gallery.php" class="rating-form">
                    <input type="hidden" name="id_slike" value="<?= $slika['id'] ?>">
                    <label>Vaša ocjena:</label>
                    <select name="ocjena">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <option value="<?= $i ?>" <?= ($moje_ocjene[$slika['id']] ?? 0) == $i ? 'selected' : '' ?>>
                                <?= $i ?> ★
                            </option>
                        <?php endfor; ?>
                    </select>
                    <button type="submit" name="ocijeni" class="btn-small">Ocijeni</button>
                </form>
                <?php endif; ?>

                <?php if (jeAdmin()): ?>
                <form method="POST" action="gallery.php" style="margin-top:5px;" onsubmit="return confirm('Obrisati sliku?')">
                    <input type="hidden" name="id_slike" value="<?= $slika['id'] ?>">
                    <button type="submit" name="brisi_sliku" class="remove-btn" style="font-size:0.8em;">Obriši</button>
                </form>
                <?php endif; ?>
            </figcaption>
        </figure>
    <?php endforeach; ?>
</section>

<!-- Lightbox -->
<?php foreach ($slike as $slika): ?>
<div id="lightbox<?= $slika['id'] ?>" class="lightbox" aria-hidden="true">
    <a href="#" class="close" aria-label="Zatvori"></a>
    <img src="<?= htmlspecialchars($slika['putanja']) ?>" alt="<?= htmlspecialchars($slika['opis'] ?? '') ?>">
</div>
<?php endforeach; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
