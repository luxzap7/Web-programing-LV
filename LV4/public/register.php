<?php
// register.php - Registracija korisnika
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

if (jePrijavljen()) {
    header('Location: index.php');
    exit;
}

$greska  = '';
$uspjeh  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $korisnicko_ime = trim($_POST['korisnicko_ime'] ?? '');
    $email          = trim($_POST['email'] ?? '');
    $lozinka        = $_POST['lozinka'] ?? '';
    $lozinka2       = $_POST['lozinka2'] ?? '';

    // Validacija
    if (empty($korisnicko_ime) || empty($email) || empty($lozinka) || empty($lozinka2)) {
        $greska = 'Sva polja su obavezna.';
    } elseif (strlen($korisnicko_ime) < 3) {
        $greska = 'Korisničko ime mora imati barem 3 znaka.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $greska = 'Neispravna email adresa.';
    } elseif (strlen($lozinka) < 6) {
        $greska = 'Lozinka mora imati barem 6 znakova.';
    } elseif ($lozinka !== $lozinka2) {
        $greska = 'Lozinke se ne podudaraju.';
    } else {
        // Provjeri postoji li korisnik
        $stmt = $pdo->prepare("SELECT id FROM korisnici WHERE korisnicko_ime = ? OR email = ?");
        $stmt->execute([$korisnicko_ime, $email]);

        if ($stmt->fetch()) {
            $greska = 'Korisničko ime ili email već postoji.';
        } else {
            // Hashiraj lozinku i spremi
            $hash = password_hash($lozinka, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO korisnici (korisnicko_ime, email, lozinka) VALUES (?, ?, ?)");
            $stmt->execute([$korisnicko_ime, $email, $hash]);

            $uspjeh = 'Registracija uspješna! Možete se prijaviti.';
            $korisnicko_ime = $email = '';
        }
    }
}

$pageTitle = 'Registracija';
require_once __DIR__ . '/includes/header.php';
?>

<div class="form-container">
    <h2>Registracija</h2>

    <?php if ($greska): ?>
        <div class="alert alert-error"><?= htmlspecialchars($greska) ?></div>
    <?php endif; ?>
    <?php if ($uspjeh): ?>
        <div class="alert alert-success"><?= htmlspecialchars($uspjeh) ?></div>
    <?php endif; ?>

    <form method="POST" action="register.php">
        <div class="form-group">
            <label for="korisnicko_ime">Korisničko ime:</label>
            <input type="text" id="korisnicko_ime" name="korisnicko_ime" 
                   value="<?= htmlspecialchars($korisnicko_ime ?? '') ?>" required minlength="3">
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" 
                   value="<?= htmlspecialchars($email ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label for="lozinka">Lozinka:</label>
            <input type="password" id="lozinka" name="lozinka" required minlength="6">
        </div>

        <div class="form-group">
            <label for="lozinka2">Potvrdite lozinku:</label>
            <input type="password" id="lozinka2" name="lozinka2" required minlength="6">
        </div>

        <button type="submit" class="btn btn-primary">Registriraj se</button>
    </form>

    <p class="form-link">Već imate račun? <a href="login.php">Prijavite se</a></p>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
