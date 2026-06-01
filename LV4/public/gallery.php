<?php
// gallery.php - Galerija s ocjenjivanjem slika
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

$poruka = '';

// Spremanje ocjene
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ocijeni'])) {
    zahtijevajPrijavu();
    $id_slike = (int) $_POST['id_slike'];
    $ocjena   = (int) $_POST['ocjena'];

    if ($ocjena >= 1 && $ocjena <= 5) {
        // INSERT ili UPDATE ocjene
        $stmt = $pdo->prepare("
            INSERT INTO ocjene (id_korisnika, id_slike, ocjena) 
            VALUES (?, ?, ?) 
            ON DUPLICATE KEY UPDATE ocjena = ?, vrijeme_ocjene = NOW()
        ");
        $stmt->execute([getKorisnikId(), $id_slike, $ocjena, $ocjena]);
        $poruka = 'Ocjena spremljena!';
    }
}

// Dohvati sve slike s prosječnom ocjenom
$sql = "
    SELECT s.*, 
           COALESCE(AVG(o.ocjena), 0) AS prosjecna_ocjena,
           COUNT(o.ocjena) AS broj_ocjena
    FROM slike s
    LEFT JOIN ocjene o ON s.id = o.id_slike
    GROUP BY s.id
    ORDER BY s.id
";
$slike = $pdo->query($sql)->fetchAll();

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
    <div class="alert alert-success"><?= htmlspecialchars($poruka) ?></div>
<?php endif; ?>

<?php if (!jePrijavljen()): ?>
    <p style="text-align:center;"><a href="login.php">Prijavite se</a> za ocjenjivanje slika.</p>
<?php endif; ?>

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
                    <span class="rating-text"><?= $avg ?>/5 (<?= $slika['broj_ocjena'] ?>)</span>
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
