<?php
// make_admin.php - Postavi korisnika kao admina (posjeti jednom pa obriši!)
require_once __DIR__ . '/includes/db.php';

$poruka = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ime = trim($_POST['korisnicko_ime'] ?? '');
    if (!empty($ime)) {
        $stmt = $pdo->prepare("UPDATE korisnici SET uloga = 'admin' WHERE korisnicko_ime = ?");
        $stmt->execute([$ime]);
        if ($stmt->rowCount() > 0) {
            $poruka = "Korisnik '$ime' je sada admin!";
        } else {
            $poruka = "Korisnik '$ime' nije pronađen.";
        }
    }
}

$korisnici = $pdo->query("SELECT korisnicko_ime, uloga FROM korisnici ORDER BY id")->fetchAll();
?>
<!DOCTYPE html>
<html><head><title>Make Admin</title></head>
<body style="font-family:sans-serif; max-width:500px; margin:50px auto; padding:20px;">
    <h1>Postavi admina</h1>
    <?php if ($poruka): ?>
        <p style="color:green; font-weight:bold;"><?= htmlspecialchars($poruka) ?></p>
    <?php endif; ?>
    <form method="POST">
        <label>Korisničko ime:</label><br>
        <select name="korisnicko_ime">
            <?php foreach ($korisnici as $k): ?>
                <option value="<?= htmlspecialchars($k['korisnicko_ime']) ?>">
                    <?= htmlspecialchars($k['korisnicko_ime']) ?> (<?= $k['uloga'] ?>)
                </option>
            <?php endforeach; ?>
        </select><br><br>
        <button type="submit">Postavi kao admin</button>
    </form>
    <p style="color:red; margin-top:20px;"><strong>VAŽNO: Obriši ovu datoteku s GitHuba nakon korištenja!</strong></p>
    <p><a href="index.php">← Početna</a></p>
</body></html>
