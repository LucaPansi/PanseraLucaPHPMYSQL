<?php
// Configurazione di base
$title = "Aggiungi un nuovo artista";
$description = "Inserisci le informazioni di un nuovo artista nella tabella.";
include_once 'mysql_local.cfg.php';

// Messaggio di feedback per l'utente
$message = "";

// Gestione del form
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Recupera i dati dal form e sanitizza
    $nome = htmlspecialchars(trim($_POST['nome']));
    $genere = htmlspecialchars(trim($_POST['genere']));
    $paese = htmlspecialchars(trim($_POST['paese']));
    $cachet = (float)$_POST['cachet'];
    $email = htmlspecialchars(trim($_POST['email']));
    $telefono = htmlspecialchars(trim($_POST['telefono']));
    $priorita = (int)$_POST['priorita'];

    // Verifica che tutti i campi siano compilati
    if (!empty($nome) && !empty($genere) && !empty($paese) && $cachet > 0 && !empty($email) && !empty($telefono) && $priorita > 0) {
        try {
            // Include la connessione automatica di Altervista
            include_once 'mysql.cfg.php';

            // Prepara la query per inserire un nuovo artista
            $stmt = $conn->prepare("INSERT INTO artisti (Nome, Genere, Paese, Cachet, Email, Telefono, Priorità) 
                                    VALUES (:nome, :genere, :paese, :cachet, :email, :telefono, :priorita)");
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':genere', $genere);
            $stmt->bindParam(':paese', $paese);
            $stmt->bindParam(':cachet', $cachet);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':telefono', $telefono);
            $stmt->bindParam(':priorita', $priorita);
            $stmt->execute();

            $message = "Artista aggiunto con successo!";
        } catch (PDOException $e) {
            $message = "Errore durante l'aggiunta dell'artista: " . $e->getMessage();
        }
    } else {
        $message = "Compila tutti i campi in modo corretto.";
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
            justify-content: space-between;
            padding: 20px;
        }
        .form-container {
            flex: 3;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            margin-right: 20px;
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
            flex: 1;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
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
        @media (max-width: 768px) {
            main {
                flex-direction: column;
            }
            .form-container, .navigation {
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
        <div class="form-container">
            <form action="artisti_add.php" method="post">
                <div class="message <?php echo !empty($message) && strpos($message, 'Errore') !== false ? 'error' : ''; ?>">
                    <?php echo $message; ?>
                </div>
                <div>
                    <label for="nome">Nome</label>
                    <input type="text" id="nome" name="nome" required>
                </div>
                <div>
                    <label for="genere">Genere</label>
                    <select id="genere" name="genere" required>
                        <option value="">Seleziona un genere</option>
                        <option value="Rock">Rock</option>
                        <option value="Pop">Pop</option>
                        <option value="Jazz">Jazz</option>
                        <option value="Classica">Classica</option>
                        <option value="Hip-Hop">Hip-Hop</option>
                        <option value="EDM">EDM</option>
                        <option value="Latino">Latino</option>
                    </select>
                </div>
                <div>
                    <label for="paese">Paese</label>
                    <input type="text" id="paese" name="paese" required>
                </div>
                <div>
                    <label for="cachet">Cachet</label>
                    <input type="number" step="0.01" id="cachet" name="cachet" required>
                </div>
                <div>
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div>
                    <label for="telefono">Telefono</label>
                    <input type="text" id="telefono" name="telefono" required>
                </div>
                <div>
                    <label for="priorita">Priorità</label>
                    <input type="number" id="priorita" name="priorita" min="1" max="10" required>
                </div>
                <button type="submit">Aggiungi Artista</button>
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
