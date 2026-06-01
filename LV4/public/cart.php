<?php
// cart.php - Osobna videoteka
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
zahtijevajPrijavu();

$poruka = '';

// Brisanje filma iz videoteke
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ukloni_film'])) {
    $id_filma = (int) $_POST['id_filma'];
    $stmt = $pdo->prepare("DELETE FROM zeljeni_filmovi WHERE id_korisnika = ? AND id_filma = ?");
    $stmt->execute([getKorisnikId(), $id_filma]);
    $poruka = 'Film uklonjen iz videoteke.';
}

// Dohvati filmove iz videoteke
$stmt = $pdo->prepare("
    SELECT f.*, zf.dodano_at 
    FROM zeljeni_filmovi zf 
    JOIN filmovi f ON zf.id_filma = f.id 
    WHERE zf.id_korisnika = ? 
    ORDER BY zf.dodano_at DESC
");
$stmt->execute([getKorisnikId()]);
$moji_filmovi = $stmt->fetchAll();

$pageTitle = 'Moja videoteka';
require_once __DIR__ . '/includes/header.php';
?>

<h1>Moja videoteka</h1>

<?php if ($poruka): ?>
    <div class="alert alert-success"><?= htmlspecialchars($poruka) ?></div>
<?php endif; ?>

<div class="cart-container">
    <h2>Odabranih filmova: <?= count($moji_filmovi) ?></h2>

    <?php if (empty($moji_filmovi)): ?>
        <p>Vaša videoteka je prazna. <a href="films.php">Pregledajte filmove</a> i dodajte ih!</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Naslov</th><th>Žanr</th><th>Godina</th><th>Ocjena</th><th>Dodano</th><th>Akcija</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($moji_filmovi as $film): ?>
                <tr>
                    <td><?= htmlspecialchars($film['naslov']) ?></td>
                    <td><?= htmlspecialchars($film['zanr']) ?></td>
                    <td><?= $film['godina'] ?></td>
                    <td><?= $film['ocjena'] ?></td>
                    <td><?= date('d.m.Y H:i', strtotime($film['dodano_at'])) ?></td>
                    <td>
                        <form method="POST" action="cart.php" style="display:inline;">
                            <input type="hidden" name="id_filma" value="<?= $film['id'] ?>">
                            <button type="submit" name="ukloni_film" class="remove-btn">Ukloni</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
