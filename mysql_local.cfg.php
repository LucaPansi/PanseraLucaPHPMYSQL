<?php
// Configurazione della connessione al database locale su XAMPP
$host = 'localhost'; // Host del server MySQL (di solito 'localhost' su XAMPP)
$db = 'LavoroPansera'; // Nome del database locale creato in phpMyAdmin
$user = 'root'; // Nome utente del database (di default su XAMPP è 'root')
$pass = ''; // Password del database (di default su XAMPP è vuota)

try {
    // Creazione della connessione al database utilizzando PDO
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    
    // Configura PDO per lanciare eccezioni in caso di errore
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Termina l'esecuzione dello script in caso di errore di connessione
    die("Errore di connessione al database: " . $e->getMessage());
}
?>
