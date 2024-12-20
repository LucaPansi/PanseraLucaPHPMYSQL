<?php
// Configurazione di base
$title = "Elimina Artista";
$description = "Seleziona un artista dalla lista per eliminarlo.";

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
    $stmt = $conn->query("SELECT ID, Nome, Genere, Paese FROM artisti ORDER BY Nome ASC");
    $artisti = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "Errore durante il recupero degli artisti: " . $e->getMessage();
}

// Gestione della richiesta di eliminazione
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
    $id = $_POST['id'] === '0' ? 0 : (int)$_POST['id'];

    if ($id >= 0) {
        try {
            // Verifica se l'ID esiste
            $checkStmt = $conn->prepare("SELECT COUNT(*) FROM artisti WHERE ID = :id");
            $checkStmt->bindParam(':id', $id, PDO::PARAM_INT);
            $checkStmt->execute();
            $exists = $checkStmt->fetchColumn();

            if ($exists) {
                // Esegui l'eliminazione
                $deleteStmt = $conn->prepare("DELETE FROM artisti WHERE ID = :id");
                $deleteStmt->bindParam(':id', $id, PDO::PARAM_INT);
                $deleteStmt->execute();

                if ($deleteStmt->rowCount() > 0) {
                    $message = "Artista eliminato con successo!";
                } else {
                    $message = "Errore durante l'eliminazione. Riprovare.";
                }
            } else {
                $message = "ID non trovato. Nessun artista corrisponde a questo ID.";
            }

            // Aggiorna la lista degli artisti con ordine alfabetico
            $stmt = $conn->query("SELECT ID, Nome, Genere, Paese FROM artisti ORDER BY ID ASC");
            $artisti = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $message = "Errore durante l'eliminazione dell'artista: " . $e->getMessage();
        }
    } else {
        $message = "ID non valido. Inserisci un ID maggiore o uguale a zero.";
    }
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
            flex-direction: row;
            padding: 20px;
            justify-content: space-between;
        }
        .list-container {
            flex: 3;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            margin-right: 20px;
        }
        .list-container table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .list-container table th, .list-container table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .list-container table th {
            background-color: #f4f4f4;
        }
        .form-container {
            flex: 3;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        .navigation {
            flex: 1;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
            margin-left: 20px;
            height: fit-content;
        }
        .navigation a {
            display: block;
            text-decoration: none;
            color: #00274d;
            font-weight: bold;
            margin: 10px 0;
            padding: 10px;
            border: 1px solid #a5673f;
            border-radius: 5px;
            background-color: #f5f5f5;
            transition: background-color 0.3s ease;
        }
        .navigation a:hover {
            background-color: #a5673f;
            color: white;
        }
        form div {
            margin-bottom: 15px;
        }
        form label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        form input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        form button {
            background-color: #a5673f;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
        }
        form button:hover {
            background-color: #00274d;
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
        @media (max-width: 768px) {
            main {
                flex-direction: column;
            }
            .list-container, .form-container, .navigation {
                margin-bottom: 20px;
            }
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
            <h2>Lista Artisti</h2>
            <?php if (!empty($artisti)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Genere</th>
                            <th>Paese</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($artisti as $artista): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($artista['ID']); ?></td>
                                <td><?php echo htmlspecialchars($artista['Nome']); ?></td>
                                <td><?php echo htmlspecialchars($artista['Genere']); ?></td>
                                <td><?php echo htmlspecialchars($artista['Paese']); ?></td>
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
            <a href="artisti_list.php">👥 Lista Artisti</a>
            <a href="festival_list.php">🎵 Lista Festival</a>
            <a href="associazioni.php">🔗 Gestione Associazioni</a>
            <form action="artisti_delete.php" method="post">
                <div class="message <?php echo !empty($message) && strpos($message, 'Errore') !== false ? 'error' : ''; ?>">
                    <?php echo $message; ?>
                </div>
                <div>
                    <label for="id">ID dell'Artista da Eliminare</label>
                    <input type="number" id="id" name="id" required>
                </div>
                <button type="submit">Elimina Artista</button>
            </form>
        </div>
    </main>
</body>
</html>
