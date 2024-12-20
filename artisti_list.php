<?php
// Configurazione di base
$title = "Lista Artisti";
$description = "Visualizza tutti gli artisti nella tabella.";

// Messaggio di feedback per l'utente
$message = "";

// Includi il file di configurazione del database
if (file_exists('mysql_local.cfg.php')) {
    include_once 'mysql_local.cfg.php';
} else {
    die("Errore: il file di configurazione 'mysql_local.cfg.php' non è stato trovato.");
}

// Verifica la connessione
if (!isset($conn)) {
    die("Errore: Connessione al database non stabilita.");
}

// Recupero della lista degli artisti
$artisti = [];
try {
    $stmt = $conn->query("SELECT ID, Nome, Genere, Paese, Cachet, Email, Telefono, Priorità FROM artisti ORDER BY ID ASC");
    $artisti = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "Errore durante il recupero degli artisti: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            color: #3d3d3d;
        }
        header {
            background: linear-gradient(90deg, #00274d, #a5673f);
            color: white;
            padding: 20px;
            text-align: center;
            border-bottom: 5px solid #a5673f;
        }
        header h1 {
            font-size: 2.5em;
            margin: 0;
        }
        header p {
            font-size: 1.2em;
            margin-top: 5px;
        }
        main {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }
        .list-container {
            width: 100%;
            max-width: 1200px;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        .list-container table {
            width: 100%;
            border-collapse: collapse;
        }
        .list-container table th, .list-container table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .list-container table th {
            background-color: #f4f4f4;
        }
        .message {
            margin-bottom: 15px;
            font-weight: bold;
            text-align: center;
            color: green;
        }
        .message.error {
            color: red;
        }
        .navigation {
            margin-top: 20px;
            text-align: center;
        }
        .navigation a {
            text-decoration: none;
            color: #00274d;
            font-weight: bold;
            margin: 0 10px;
            padding: 10px 20px;
            border: 1px solid #a5673f;
            border-radius: 5px;
            background-color: #f5f5f5;
            transition: background-color 0.3s ease;
        }
        .navigation a:hover {
            background-color: #a5673f;
            color: white;
        }
    </style>
</head>
<body>
    <header>
        <h1><?php echo $title; ?></h1>
        <p><?php echo $description; ?></p>
    </header>
    <main>
        <div class="list-container">
            <h2>Artisti Registrati</h2>
            <?php if (!empty($message)): ?>
                <div class="message <?php echo strpos($message, 'Errore') !== false ? 'error' : ''; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($artisti)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Genere</th>
                            <th>Paese</th>
                            <th>Cachet</th>
                            <th>Email</th>
                            <th>Telefono</th>
                            <th>Priorità</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($artisti as $artista): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($artista['ID']); ?></td>
                                <td><?php echo htmlspecialchars($artista['Nome']); ?></td>
                                <td><?php echo htmlspecialchars($artista['Genere']); ?></td>
                                <td><?php echo htmlspecialchars($artista['Paese']); ?></td>
                                <td><?php echo htmlspecialchars(number_format($artista['Cachet'], 2, ',', '.')); ?> €</td>
                                <td><?php echo htmlspecialchars($artista['Email']); ?></td>
                                <td><?php echo htmlspecialchars($artista['Telefono']); ?></td>
                                <td><?php echo htmlspecialchars($artista['Priorità']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Nessun artista trovato.</p>
            <?php endif; ?>
        </div>
        <div class="navigation">
            <a href="index.php">🏠 Torna alla Home</a>
            <a href="artisti_add.php">➕ Aggiungi Artista</a>
            <a href="artisti_edit.php">✏️ Modifica Artista</a>
            <a href="artisti_delete.php">❌ Elimina Artista</a>
        </div>
    </main>
</body>
</html>
