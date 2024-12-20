<?php
// Configurazione di base
$title = "Modifica Associazione";
$description = "Seleziona un'associazione dalla lista e aggiornane i dettagli.";

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

// Recupero della lista delle associazioni
$associazioni = [];
try {
    $stmt = $conn->query(
        "SELECT a.ID, ar.Nome AS Artista, f.Nome AS Festival, a.Ruolo
        FROM associazioni a
        INNER JOIN artisti ar ON a.ArtistaID = ar.ID
        INNER JOIN festivals f ON a.FestivalID = f.ID
        ORDER BY a.ID ASC"
    );
    $associazioni = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "Errore durante il recupero delle associazioni: " . $e->getMessage();
}

// Gestione del form di aggiornamento
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = (int)$_POST['id'];
    $artistaID = (int)$_POST['artista_id'];
    $festivalID = (int)$_POST['festival_id'];
    $ruolo = htmlspecialchars(trim($_POST['ruolo']));

    if ($id > 0 && $artistaID > 0 && $festivalID > 0) {
        try {
            $stmt = $conn->prepare(
                "UPDATE associazioni SET ArtistaID = :artistaID, FestivalID = :festivalID, Ruolo = :ruolo WHERE ID = :id"
            );
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':artistaID', $artistaID);
            $stmt->bindParam(':festivalID', $festivalID);
            $stmt->bindParam(':ruolo', $ruolo);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $message = "Associazione aggiornata con successo!";
            } else {
                $message = "Nessuna modifica effettuata.";
            }
        } catch (PDOException $e) {
            $message = "Errore durante l'aggiornamento dell'associazione: " . $e->getMessage();
        }
    } else {
        $message = "Tutti i campi sono obbligatori.";
    }
}

// Recupero delle liste di artisti e festival
$artisti = [];
$festivals = [];
try {
    $stmtArtisti = $conn->query("SELECT ID, Nome FROM artisti ORDER BY Nome ASC");
    $artisti = $stmtArtisti->fetchAll(PDO::FETCH_ASSOC);

    $stmtFestivals = $conn->query("SELECT ID, Nome FROM festivals ORDER BY Nome ASC");
    $festivals = $stmtFestivals->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "Errore durante il recupero dei dati: " . $e->getMessage();
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
        .list-container, .form-container {
            flex: 3;
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
        .navigation {
            flex: 1;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
            margin-left: 20px;
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
        form select, form input {
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
            <h2>Lista Associazioni</h2>
            <?php if (!empty($associazioni)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Artista</th>
                            <th>Festival</th>
                            <th>Ruolo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($associazioni as $associazione): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($associazione['ID']); ?></td>
                                <td><?php echo htmlspecialchars($associazione['Artista']); ?></td>
                                <td><?php echo htmlspecialchars($associazione['Festival']); ?></td>
                                <td><?php echo htmlspecialchars($associazione['Ruolo']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Nessuna associazione trovata.</p>
            <?php endif; ?>
        </div>
        <div class="form-container">
            <h2>Modifica Associazione</h2>
            <form action="associazioni_edit.php" method="post">
                <?php if (!empty($message)): ?>
                    <div class="message <?php echo strpos($message, 'Errore') !== false ? 'error' : ''; ?>">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                <div>
                    <label for="id">ID Associazione</label>
                    <input type="number" id="id" name="id" required>
                </div>
                <div>
                    <label for="artista_id">Seleziona Artista</label>
                    <select id="artista_id" name="artista_id" required>
                        <option value="">-- Seleziona un Artista --</option>
                        <?php foreach ($artisti as $artista): ?>
                            <option value="<?php echo htmlspecialchars($artista['ID']); ?>"><?php echo htmlspecialchars($artista['Nome']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="festival_id">Seleziona Festival</label>
                    <select id="festival_id" name="festival_id" required>
                        <option value="">-- Seleziona un Festival --</option>
                        <?php foreach ($festivals as $festival): ?>
                            <option value="<?php echo htmlspecialchars($festival['ID']); ?>"><?php echo htmlspecialchars($festival['Nome']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="ruolo">Ruolo (Opzionale)</label>
                    <input type="text" id="ruolo" name="ruolo" placeholder="Esempio: Main Performer">
                </div>
                <button type="submit">Modifica Associazione</button>
            </form>
        </div>
        <div class="navigation">
            <a href="index.php">🏠 Torna alla Home</a>
            <a href="associazioni_list.php">📋 Lista Associazioni</a>
        </div>
    </main>
</body>
</html>
