<?php
// Initialize SQLite database
$dbFile = __DIR__ . '/profile.db';

try {
    $db = new PDO("sqlite:" . $dbFile);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Create table if not exists
    $db->exec("
        CREATE TABLE IF NOT EXISTS interests (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL
        )
    ");
} catch (PDOException $e) {
    die("Chyba připojení k databázi: " . $e->getMessage());
}
?>
