<?php
require_once __DIR__ . '/includes/db.php';

$sql = file_get_contents(__DIR__ . '/../lv4_baza.sql');

// Ukloni komentare
$sql = preg_replace('/--.*$/m', '', $sql);
$sql = preg_replace('/\/\*.*?\*\//s', '', $sql);

$statements = array_filter(array_map('trim', explode(';', $sql)));

$success = 0;
$errors = [];

foreach ($statements as $stmt) {
    if (empty($stmt)) continue;
    try {
        $pdo->exec($stmt);
        $success++;
    } catch (PDOException $e) {
        $errors[] = substr($stmt, 0, 60) . "... → " . $e->getMessage();
    }
}

echo "<h1>Setup završen!</h1>";
echo "<p>Uspješno izvršeno: $success naredbi</p>";
if ($errors) {
    echo "<h2>Greške (normalno ako tablice već postoje):</h2><ul>";
    foreach ($errors as $err) {
        echo "<li>" . htmlspecialchars($err) . "</li>";
    }
    echo "</ul>";
}
echo "<p><a href='index.php'>Idi na početnu</a></p>";
