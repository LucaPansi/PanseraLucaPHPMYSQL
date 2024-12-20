<?php
// Includi il file di configurazione del database
include_once 'mysql_local.cfg.php';

// Ottieni la query dalla richiesta
$query = $_GET['query'] ?? '';

// Cerca nel database
if (!empty($query)) {
    $stmt = $conn->prepare("SELECT * FROM artisti WHERE Nome LIKE :query LIMIT 10");
    $stmt->execute(['query' => "%$query%"]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($results as $result) {
        echo "<li onclick=\"selectArtist(
            '{$result['ID']}', 
            '{$result['Nome']}', 
            '{$result['Genere']}', 
            '{$result['Paese']}', 
            '{$result['Cachet']}', 
            '{$result['Email']}', 
            '{$result['Telefono']}', 
            '{$result['Priorità']}'
        )\">{$result['Nome']}</li>";
    }
}
?>
