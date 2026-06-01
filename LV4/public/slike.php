<?php
// Čitanje slika iz mape images/
$folderPath = __DIR__ . '/images';
$images = [];

if (is_dir($folderPath)) {
    $files = scandir($folderPath);
    foreach ($files as $file) {
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $images[] = [
                'url'   => 'images/' . $file,
                'title' => $file
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galerija slika</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/style_slike.css">
</head>
<body>
    <header>
        <h1>Dobrodošli na moju web stranicu</h1>
    </header>

    <div class="navigation">
        <ul>
            <li><a href="index.php">Početna</a></li>
            <li><a href="slike.php">Slike</a></li>
            <li><a href="grafikon.php">Grafikon</a></li>
        </ul>
    </div>

    <div id="hamburger-menu">
        <input type="checkbox" id="menu-toggle" aria-label="Toggle navigation" />
        <label for="menu-toggle" class="hamburger-button">&#9776;</label>
        <nav class="nav-links" aria-hidden="true">
            <ul>
                <li><a href="index.php">Početna</a></li>
                <li><a href="slike.php">Slike</a></li>
                <li><a href="grafikon.php">Grafikon</a></li>
            </ul>
        </nav>
    </div>

    <h1>Galerija slika</h1>

    <?php if (count($images) > 0): ?>
        <section class="galerija" aria-label="Galerija slika">
            <?php foreach ($images as $index => $image): ?>
                <figure class="galerija_slika">
                    <a href="#lightbox<?= $index + 1 ?>">
                        <img src="<?= htmlspecialchars($image['url']) ?>" alt="<?= htmlspecialchars($image['title']) ?>" loading="lazy">
                    </a>
                    <figcaption><?= htmlspecialchars($image['title']) ?></figcaption>
                </figure>
            <?php endforeach; ?>
        </section>

        <?php foreach ($images as $index => $image): ?>
            <div id="lightbox<?= $index + 1 ?>" class="lightbox" aria-hidden="true">
                <a href="#" class="close" aria-label="Zatvori prikaz slike"></a>
                <img src="<?= htmlspecialchars($image['url']) ?>" alt="<?= htmlspecialchars($image['title']) ?>">
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>U mapi images/ nema dostupnih slika.</p>
    <?php endif; ?>

    <footer>
        <p>&copy; 2026. Web Programiranje. Sva prava pridržana.</p>
    </footer>
</body>
</html>
