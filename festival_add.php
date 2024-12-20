<?php
// Configurazione di base
$title = "Aggiungi un nuovo festival";
$description = "Inserisci le informazioni di un nuovo festival nella tabella.";

// Messaggio di feedback per l'utente
$message = "";

// Includi il file di configurazione del database
if (file_exists('db.php')) {
    include_once 'db.php';
} else {
    die("Errore: il file di configurazione 'db.php' non è stato trovato.");
}

// Gestione del form
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Recupera i dati dal form e sanitizza
    $nome = htmlspecialchars(trim($_POST['nome']));
    $luogo = htmlspecialchars(trim($_POST['luogo']));
    $data = $_POST['data'];
    $durata = htmlspecialchars(trim($_POST['durata']));
    $budget = (float)$_POST['budget'];
    $tipologia = htmlspecialchars(trim($_POST['tipologia']));
    $prezzo_biglietto = (float)$_POST['prezzo_biglietto'];
    $organizzatore = htmlspecialchars(trim($_POST['organizzatore']));
    $email = htmlspecialchars(trim($_POST['email']));
    $sito_web = htmlspecialchars(trim($_POST['sito_web']));

    // Verifica che tutti i campi obbligatori siano compilati
    if (!empty($nome) && !empty($luogo) && !empty($data) && !empty($durata) && $budget > 0 && !empty($tipologia) && $prezzo_biglietto > 0 && !empty($organizzatore) && !empty($email)) {
        try {
            // Prepara la query per inserire un nuovo festival
            $stmt = $conn->prepare("INSERT INTO festivals (Nome, Luogo, Data, Durata, Budget, Tipologia, Prezzo_Biglietto, Organizzatore, Email, Sito_Web) 
                                    VALUES (:nome, :luogo, :data, :durata, :budget, :tipologia, :prezzo_biglietto, :organizzatore, :email, :sito_web)");
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':luogo', $luogo);
            $stmt->bindParam(':data', $data);
            $stmt->bindParam(':durata', $durata);
            $stmt->bindParam(':budget', $budget);
            $stmt->bindParam(':tipologia', $tipologia);
            $stmt->bindParam(':prezzo_biglietto', $prezzo_biglietto);
            $stmt->bindParam(':organizzatore', $organizzatore);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':sito_web', $sito_web);
            $stmt->execute();

            $message = "Festival aggiunto con successo!";
        } catch (PDOException $e) {
            $message = "Errore durante l'aggiunta del festival: " . $e->getMessage();
        }
    } else {
        $message = "Compila tutti i campi obbligatori in modo corretto.";
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
            <form action="festival_add.php" method="post">
                <?php if (!empty($message)): ?>
                    <div class="message <?php echo strpos($message, 'Errore') !== false ? 'error' : ''; ?>">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
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
                <div>
                    <label for="durata">Durata</label>
                    <input type="text" id="durata" name="durata" required>
                </div>
                <div>
                    <label for="budget">Budget</label>
                    <input type="number" step="0.01" id="budget" name="budget" required>
                </div>
                <div>
                    <label for="tipologia">Tipologia</label>
                    <input type="text" id="tipologia" name="tipologia" required>
                </div>
                <div>
                    <label for="prezzo_biglietto">Prezzo Biglietto</label>
                    <input type="number" step="0.01" id="prezzo_biglietto" name="prezzo_biglietto" required>
                </div>
                <div>
                    <label for="organizzatore">Organizzatore</label>
                    <input type="text" id="organizzatore" name="organizzatore" required>
                </div>
                <div>
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div>
                    <label for="sito_web">Sito Web</label>
                    <input type="url" id="sito_web" name="sito_web">
                </div>
                <button type="submit">Aggiungi Festival</button>
            </form>
        </div>
        <div class="navigation">
            <a href="index.php">🏠 Torna alla Home</a>
            <a href="festival_list.php">📋 Lista Festival</a>
        </div>
    </main>
</body>
</html>
