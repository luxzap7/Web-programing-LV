<?php
// login.php - Prijava korisnika
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

// Ako je već prijavljen, preusmjeri
if (jePrijavljen()) {
    header('Location: index.php');
    exit;
}

$greska = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $korisnicko_ime = trim($_POST['korisnicko_ime'] ?? '');
    $lozinka        = $_POST['lozinka'] ?? '';

    if (empty($korisnicko_ime) || empty($lozinka)) {
        $greska = 'Sva polja su obavezna.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM korisnici WHERE korisnicko_ime = ?");
        $stmt->execute([$korisnicko_ime]);
        $korisnik = $stmt->fetch();

        if ($korisnik && password_verify($lozinka, $korisnik['lozinka'])) {
            $_SESSION['korisnik_id']    = $korisnik['id'];
            $_SESSION['korisnicko_ime'] = $korisnik['korisnicko_ime'];
            $_SESSION['uloga']          = $korisnik['uloga'];
            header('Location: index.php');
            exit;
        } else {
            $greska = 'Neispravno korisničko ime ili lozinka.';
        }
    }
}

$pageTitle = 'Prijava';
require_once __DIR__ . '/includes/header.php';
?>

<div class="form-container">
    <h2>Prijava</h2>

    <?php if ($greska): ?>
        <div class="alert alert-error"><?= htmlspecialchars($greska) ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <div class="form-group">
            <label for="korisnicko_ime">Korisničko ime:</label>
            <input type="text" id="korisnicko_ime" name="korisnicko_ime" 
                   value="<?= htmlspecialchars($korisnicko_ime ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label for="lozinka">Lozinka:</label>
            <input type="password" id="lozinka" name="lozinka" required>
        </div>

        <button type="submit" class="btn btn-primary">Prijavi se</button>
    </form>

    <p class="form-link">Nemate račun? <a href="register.php">Registrirajte se</a></p>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
