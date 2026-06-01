<?php
require_once __DIR__ . '/includes/db.php';

$sql = file_get_contents(__DIR__ . '/../lv4_baza.sql');

// Razdvoji na pojedinačne naredbe
$statements = array_filter(array_map('trim', explode(';', $sql)));

$success = 0;
$errors = [];

foreach ($statements as $stmt) {
    if (empty($stmt) || strpos($stmt, '--') === 0) continue;
    try {
        $pdo->exec($stmt);
        $success++;
    } catch (PDOException $e) {
        $errors[] = $e->getMessage();
    }
}

echo "<h1>Setup završen!</h1>";
echo "<p>Uspješno izvršeno: $success naredbi</p>";
if ($errors) {
    echo "<h2>Greške (mogu biti normalne ako tablice već postoje):</h2>";
    echo "<ul>";
    foreach ($errors as $err) {
        echo "<li>" . htmlspecialchars($err) . "</li>";
    }
    echo "</ul>";
}
echo "<p><a href='index.php'>Idi na početnu</a></p>";
