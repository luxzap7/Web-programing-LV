<?php
// includes/header.php
require_once __DIR__ . '/auth.php';
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Web stranica o filmovima - LV4">
    <title><?= $pageTitle ?? 'LV4 - PHP' ?></title>
    <link rel="stylesheet" href="style/style.css">
    <?php if (isset($extraCSS)): ?>
        <?php foreach ($extraCSS as $css): ?>
            <link rel="stylesheet" href="style/<?= $css ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <header>
        <h1>Dobrodošli na moju web stranicu</h1>
    </header>

    <div class="navigation">
        <ul>
            <li><a href="index.php">Početna</a></li>
            <li><a href="films.php">Filmovi</a></li>
            <li><a href="gallery.php">Galerija</a></li>
            <li><a href="grafikon.php">Grafikon</a></li>
            <?php if (jePrijavljen()): ?>
                <li><a href="cart.php">Moja videoteka</a></li>
                <?php if (jeAdmin()): ?>
                    <li><a href="dashboard.php">Admin</a></li>
                <?php endif; ?>
                <li class="nav-right"><a href="logout.php">Odjava (<?= htmlspecialchars(getKorisnickoIme()) ?>)</a></li>
            <?php else: ?>
                <li class="nav-right"><a href="login.php">Prijava</a></li>
                <li><a href="register.php">Registracija</a></li>
            <?php endif; ?>
        </ul>
    </div>

    <div id="hamburger-menu">
        <input type="checkbox" id="menu-toggle" aria-label="Toggle navigation" />
        <label for="menu-toggle" class="hamburger-button">&#9776;</label>
        <nav class="nav-links" aria-hidden="true">
            <ul>
                <li><a href="index.php">Početna</a></li>
                <li><a href="films.php">Filmovi</a></li>
                <li><a href="gallery.php">Galerija</a></li>
                <li><a href="grafikon.php">Grafikon</a></li>
                <?php if (jePrijavljen()): ?>
                    <li><a href="cart.php">Moja videoteka</a></li>
                    <?php if (jeAdmin()): ?>
                        <li><a href="dashboard.php">Admin</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php">Odjava</a></li>
                <?php else: ?>
                    <li><a href="login.php">Prijava</a></li>
                    <li><a href="register.php">Registracija</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
