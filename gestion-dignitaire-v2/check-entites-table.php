<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=gestion_dignitaire', 'root', 'root');

echo "Tables contenant 'entit':\n";
$stmt = $pdo->query("SHOW TABLES LIKE '%entit%'");
while($row = $stmt->fetch(PDO::FETCH_NUM)) {
    echo "  - " . $row[0] . "\n";
}

echo "\nToutes les tables:\n";
$stmt = $pdo->query("SHOW TABLES");
while($row = $stmt->fetch(PDO::FETCH_NUM)) {
    echo "  - " . $row[0] . "\n";
}
