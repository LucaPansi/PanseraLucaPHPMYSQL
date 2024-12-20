<?php
// Configurazione di base
$title = "Aggiungi Associazione";
$description = "Crea una nuova associazione tra un artista e un festival.";

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

// Recupero della lista degli artisti e festival
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

// Gestione del form
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $artistaID = (int)$_POST['artista_id'];
    $festivalID = (int)$_POST['festival_id'];
    $ruolo = htmlspecialchars(trim($_POST['ruolo']));

    if ($artistaID > 0 && $festivalID > 0) {
        try {
            $stmt = $conn->prepare("INSERT INTO associazioni (ArtistaID, FestivalID, Ruolo) VALUES (:artistaID, :festivalID, :ruolo)");
            $stmt->bindParam(':artistaID', $artistaID);
            $stmt->bindParam(':festivalID', $festivalID);
            $stmt->bindParam(':ruolo', $ruolo);
            $stmt->execute();

            $message = "Associazione aggiunta con successo!";
        } catch (PDOException $e) {
            $message = "Errore durante l'aggiunta dell'associazione: " . $e->getMessage();
        }
    } else {
        $message = "Seleziona un artista e un festival validi.";
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
        .form-container {
            flex: 3;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        .form-container form div {
            margin-bottom: 15px;
        }
        .form-container form label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        .form-container form select, .form-container form input {
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
        }
        .form-container form button:hover {
            background-color: #00274d;
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
    </style>
</head>
<body>
    <header>
        <h1><?php echo $title; ?></h1>
        <p><?php echo $description; ?></p>
    </header>
    <main>
        <div class="form-container">
            <form action="associazioni_add.php" method="post">
                <?php if (!empty($message)): ?>
                    <div class="message <?php echo strpos($message, 'Errore') !== false ? 'error' : ''; ?>">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
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
                <button type="submit">Aggiungi Associazione</button>
            </form>
        </div>
        <div class="navigation">
            <a href="index.php">🏠 Torna alla Home</a>
            <a href="associazioni_list.php">📋 Lista Associazioni</a>
        </div>
    </main>
</body>
</html>
