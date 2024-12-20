<?php
// Configurazione di base
$title = "Elimina Festival";
$description = "Seleziona un festival dalla lista per eliminarlo.";

// Messaggio di feedback per l'utente
$message = "";

// Includi il file di configurazione del database
if (file_exists('db.php')) {
    include_once 'db.php';
} else {
    die("Errore: il file di configurazione 'db.php' non è stato trovato.");
}

// Verifica la connessione
if (!isset($conn)) {
    die("Errore: Connessione al database non stabilita.");
}

// Recupero della lista dei festival
$festivals = [];
try {
    $stmt = $conn->query("SELECT ID, Nome, Luogo, Data FROM festivals ORDER BY ID ASC");
    $festivals = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "Errore durante il recupero dei festival: " . htmlspecialchars($e->getMessage());
}

// Gestione della richiesta di eliminazione
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if ($id && $id > 0) {
        try {
            $stmt = $conn->prepare("DELETE FROM festivals WHERE ID = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $message = "Festival eliminato con successo!";
                // Aggiorna la lista dei festival
                $stmt = $conn->query("SELECT ID, Nome, Luogo, Data FROM festivals ORDER BY ID ASC");
                $festivals = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $message = "Nessun festival trovato con l'ID specificato.";
            }
        } catch (PDOException $e) {
            $message = "Errore durante l'eliminazione del festival: " . htmlspecialchars($e->getMessage());
        }
    } else {
        $message = "ID non valido. Inserisci un ID maggiore di zero.";
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
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
            flex-wrap: wrap;
        }
        .list-container {
            flex: 3;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            margin-right: 20px;
            min-width: 300px;
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
            flex: 1;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            min-width: 250px;
        }
        .form-container form div {
            margin-bottom: 15px;
        }
        .form-container form label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        .form-container form input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-container form button {
            background-color: #a5673f;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            width: 100%;
        }
        .form-container form button:hover {
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
        .navigation {
            width: 100%;
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
        @media (max-width: 768px) {
            main {
                flex-direction: column;
            }
            .list-container, .form-container {
                margin-right: 0;
                margin-bottom: 20px;
            }
            .navigation a {
                width: calc(100% - 40px);
                max-width: 400px;
                display: block;
                margin: 10px auto;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1><?php echo htmlspecialchars($title); ?></h1>
        <p><?php echo htmlspecialchars($description); ?></p>
    </header>
    <main>
        <div class="list-container">
            <h2>Lista Festival</h2>
            <?php if (!empty($festivals)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Luogo</th>
                            <th>Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($festivals as $festival): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($festival['ID']); ?></td>
                                <td><?php echo htmlspecialchars($festival['Nome']); ?></td>
                                <td><?php echo htmlspecialchars($festival['Luogo']); ?></td>
                                <td><?php echo htmlspecialchars($festival['Data']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Nessun festival trovato.</p>
            <?php endif; ?>
        </div>
        <div class="form-container">
            <form action="festival_delete.php" method="post">
                <?php if (!empty($message)): ?>
                    <div class="message <?php echo strpos($message, 'Errore') !== false ? 'error' : ''; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>
                <div>
                    <label for="id">ID del Festival da Eliminare</label>
                    <input type="number" id="id" name="id" required>
                </div>
                <button type="submit">Elimina Festival</button>
            </form>
            <div class="navigation">
            <a href="index.php">🏠 Torna alla Home</a>
            <a href="festival_list.php">📋 Lista Festival</a>
        </div>
        </div>
        
    </main>
</body>
</html>
