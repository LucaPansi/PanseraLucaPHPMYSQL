<?php
// Rilevamento dell'ambiente
if ($_SERVER['HTTP_HOST'] === 'localhost') {
    // Configurazione locale (XAMPP)
    $db_host = 'localhost';
    $db_name = 'LavoroPansera';
    $db_user = 'root';
    $db_password = '';
} else {
    // Configurazione Altervista
    $db_host = 'localhost'; // Sempre 'localhost' su Altervista
    $db_name = 'my_lucapansera'; // Nome database, di solito uguale al nome utente
    $db_user = 'lucapansera'; // Nome utente di Altervista
    $db_password = '2RK5Zh3P3HD2'; // Password fornita da Altervista
}

// Opzioni PDO per la connessione
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    // Creazione della connessione al database
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_password, $options);
} catch (PDOException $e) {
    // Gestione degli errori di connessione
    die("Errore di connessione al database: " . $e->getMessage());
}
