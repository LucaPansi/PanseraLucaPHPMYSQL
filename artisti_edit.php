<?php
// Configurazione di base
$title = "Modifica Artista";
$description = "Cerca e modifica le informazioni di un artista.";

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

// Recupero della lista rapida
$quickList = [];
try {
    $stmt = $conn->query("SELECT ID, Nome FROM artisti ORDER BY Nome ASC");
    $quickList = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Errore durante il recupero della lista rapida: " . $e->getMessage());
}

// Gestione dell'aggiornamento dei dati
$message = "";
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update'])) {
    $id = (int)$_POST['id'];
    $nome = htmlspecialchars(trim($_POST['nome']));
    $genere = htmlspecialchars(trim($_POST['genere']));
    $paese = htmlspecialchars(trim($_POST['paese']));
    $cachet = (float)$_POST['cachet'];
    $email = htmlspecialchars(trim($_POST['email']));
    $telefono = htmlspecialchars(trim($_POST['telefono']));
    $priorita = (int)$_POST['priorita'];

    if ($id > 0 && !empty($nome)) {
        try {
            $stmt = $conn->prepare("UPDATE artisti SET Nome = :nome, Genere = :genere, Paese = :paese, Cachet = :cachet, Email = :email, Telefono = :telefono, Priorità = :priorita WHERE ID = :id");
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':genere', $genere);
            $stmt->bindParam(':paese', $paese);
            $stmt->bindParam(':cachet', $cachet);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':telefono', $telefono);
            $stmt->bindParam(':priorita', $priorita);
            $stmt->execute();

            $message = "Artista aggiornato con successo!";
        } catch (PDOException $e) {
            $message = "Errore durante l'aggiornamento: " . $e->getMessage();
        }
    } else {
        $message = "Compila tutti i campi obbligatori.";
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
        .search-container {
            margin-bottom: 20px;
        }
        .search-container input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
        }
        .results-container {
            margin-top: 20px;
        }
        .results-container ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .results-container ul li {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            cursor: pointer;
            background-color: white;
        }
        .results-container ul li:hover {
            background-color: #f0f0f0;
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
        form input, form select {
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
    </style>
    <script>
        // Ricerca rapida tramite AJAX
        function liveSearch() {
            const query = document.getElementById("search").value;
            const results = document.getElementById("results");

            if (query.length === 0) {
                results.innerHTML = "";
                return;
            }

            const xhr = new XMLHttpRequest();
            xhr.open("GET", `search_artists.php?query=${encodeURIComponent(query)}`, true);
            xhr.onload = function () {
                if (this.status === 200) {
                    results.innerHTML = this.responseText;
                }
            };
            xhr.send();
        }

        function selectArtist(id, nome, genere, paese, cachet, email, telefono, priorita) {
            document.getElementById("id").value = id;
            document.getElementById("nome").value = nome;
            document.getElementById("genere").value = genere;
            document.getElementById("paese").value = paese;
            document.getElementById("cachet").value = cachet;
            document.getElementById("email").value = email;
            document.getElementById("telefono").value = telefono;
            document.getElementById("priorita").value = priorita;
        }
    </script>
</head>
<body>
    <header>
        <h1><?php echo $title; ?></h1>
        <p><?php echo $description; ?></p>
    </header>
    <main>
        <div class="quick-list">
            <h2>Lista Rapida Artisti</h2>
            <ul>
                <?php foreach ($quickList as $artist): ?>
                    <li><?php echo htmlspecialchars($artist['Nome']); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="form-container">
            <div class="search-container">
                <input type="text" id="search" onkeyup="liveSearch()" placeholder="Cerca artista per nome...">
                <div id="results" class="results-container"></div>
            </div>
            <form action="artisti_edit.php" method="post">
                <div>
                    <label for="id">ID</label>
                    <input type="text" id="id" name="id" readonly>
                </div>
                <div>
                    <label for="nome">Nome</label>
                    <input type="text" id="nome" name="nome" required>
                </div>
                <div>
                    <label for="genere">Genere</label>
                    <input type="text" id="genere" name="genere">
                </div>
                <div>
                    <label for="paese">Paese</label>
                    <input type="text" id="paese" name="paese">
                </div>
                <div>
                    <label for="cachet">Cachet</label>
                    <input type="number" step="0.01" id="cachet" name="cachet">
                </div>
                <div>
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email">
                </div>
                <div>
                    <label for="telefono">Telefono</label>
                    <input type="text" id="telefono" name="telefono">
                </div>
                <div>
                    <label for="priorita">Priorità</label>
                    <input type="number" id="priorita" name="priorita" min="1" max="10">
                </div>
                <button type="submit" name="update">Aggiorna Artista</button>
            </form>
        </div>
        <div class="navigation">
            <a href="index.php">🏠 Torna alla Home</a>
            <a href="artisti_list.php">👥 Lista Artisti</a>
            <a href="festival_list.php">🎵 Lista Festival</a>
            <a href="associazioni.php">🔗 Gestione Associazioni</a>
        </div>
    </main>
</body>
</html>
