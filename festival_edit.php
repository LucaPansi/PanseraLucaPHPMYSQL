<?php
// Configurazione di base
$title = "Modifica Festival";
$description = "Seleziona un festival dalla lista per modificarne i dettagli.";

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

// Gestione aggiornamento festival
$message = "";
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update'])) {
    $id = (int)$_POST['id'];
    $nome = htmlspecialchars(trim($_POST['nome']));
    $luogo = htmlspecialchars(trim($_POST['luogo']));
    $data = $_POST['data'];

    if ($id > 0 && !empty($nome) && !empty($luogo) && !empty($data)) {
        try {
            $stmt = $conn->prepare("UPDATE festivals SET Nome = :nome, Luogo = :luogo, Data = :data WHERE ID = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
            $stmt->bindParam(':luogo', $luogo, PDO::PARAM_STR);
            $stmt->bindParam(':data', $data, PDO::PARAM_STR);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $message = "Festival aggiornato con successo!";
            } else {
                $message = "Nessuna modifica effettuata (i dati potrebbero essere già aggiornati).";
            }
        } catch (PDOException $e) {
            $message = "Errore durante l'aggiornamento: " . htmlspecialchars($e->getMessage());
        }
    } else {
        $message = "Compila tutti i campi obbligatori.";
    }
}

// Recupero della lista rapida dei festival (fatto dopo l'update, così è aggiornata)
$quickList = [];
try {
    $stmt = $conn->query("SELECT ID, Nome, Luogo, Data FROM festivals ORDER BY ID ASC");
    $quickList = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Errore durante il recupero della lista rapida: " . $e->getMessage());
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
        }
        .quick-list {
            flex: 1;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            margin-right: 20px;
            max-height: 400px;
            overflow-y: auto;
        }
        .quick-list h2 {
            text-align: center;
            font-size: 1.5em;
            margin-bottom: 10px;
        }
        .quick-list ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .quick-list ul li {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            cursor: pointer;
        }
        .quick-list ul li:hover {
            background-color: #f0f0f0;
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
        form {
            margin-top: 20px;
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
        }
        .message.error {
            color: red;
        }
        .message:not(.error) {
            color: green;
        }
        @media (max-width: 768px) {
            main {
                flex-direction: column;
            }
            .quick-list, .form-container, .navigation {
                margin-bottom: 20px;
            }
        }
    </style>
    <script>
        function selectFestival(id, nome, luogo, data) {
            document.getElementById("id").value = id;
            document.getElementById("nome").value = nome;
            document.getElementById("luogo").value = luogo;
            document.getElementById("data").value = data;
        }
    </script>
</head>
<body>
    <header>
        <h1><?php echo htmlspecialchars($title); ?></h1>
        <p><?php echo htmlspecialchars($description); ?></p>
    </header>
    <main>
        <div class="quick-list">
            <h2>Lista Festival (ID - Nome)</h2>
            <ul>
                <?php foreach ($quickList as $festival): 
                    $id = htmlspecialchars($festival['ID'], ENT_QUOTES);
                    $nome = htmlspecialchars($festival['Nome'], ENT_QUOTES);
                    $luogo = htmlspecialchars($festival['Luogo'], ENT_QUOTES);
                    $data = htmlspecialchars($festival['Data'], ENT_QUOTES);
                ?>
                    <li onclick="selectFestival('<?php echo $id; ?>', '<?php echo $nome; ?>', '<?php echo $luogo; ?>', '<?php echo $data; ?>')">
                        <?php echo $id . " - " . $nome; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="form-container">
            <?php if (!empty($message)): ?>
                <div class="message <?php echo (strpos($message, 'Errore') !== false)? 'error' : ''; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            <form action="festival_edit.php" method="post">
                <div>
                    <label for="id">ID</label>
                    <input type="text" id="id" name="id" readonly>
                </div>
                <div>
                    <label for="nome">Nome</label>
                    <input type="text" id="nome" name="nome" required>
                </div>
                <div>
                    <label for="luogo">Luogo</label>
                    <input type="text" id="luogo" name="luogo" required>
                </div>
                <div>
                    <label for="data">Data</label>
                    <input type="date" id="data" name="data" required>
                </div>
                <button type="submit" name="update">Aggiorna Festival</button>
            </form>
        </div>
        <div class="navigation">
            <a href="index.php">🏠 Torna alla Home</a>
            <a href="festival_list.php">📋 Lista Festival</a>
        </div>
    </main>
</body>
</html>
