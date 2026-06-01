<?php
// film_delete.php - Brisanje filma (samo admin)
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
zahtijevajPrijavu();

if (!jeAdmin()) {
    header('Location: films.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_filma'])) {
    $id_filma = (int) $_POST['id_filma'];

    // Prvo obriši povezane zapise u zeljeni_filmovi
    $stmt = $pdo->prepare("DELETE FROM zeljeni_filmovi WHERE id_filma = ?");
    $stmt->execute([$id_filma]);

    // Obriši film
    $stmt = $pdo->prepare("DELETE FROM filmovi WHERE id = ?");
    $stmt->execute([$id_filma]);
}

header('Location: films.php');
exit;
