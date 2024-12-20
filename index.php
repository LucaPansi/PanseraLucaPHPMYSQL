<?php
// Configurazione di base
$title = "Benvenuto nella mia applicazione di gestione!";
$description = "Esplora le funzionalità della nostra applicazione: gestione di entità con operazioni CRUD complete e ricerca avanzata.";
$menus = [
    "Gestione Artisti" => "artisti_list.php",
    "Gestione Festival" => "festival_list.php",
    "Gestione Associazioni" => "associazioni_list.php",
    "Visualizza Codice Sorgente" => "source_code.php"
];
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <style>
        /* Stili simili a quelli già forniti */
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
            padding: 40px 20px;
            text-align: center;
            border-bottom: 5px solid #a5673f;
        }
        header h1 {
            font-size: 3em;
            margin: 0;
        }
        header p {
            font-size: 1.5em;
            margin-top: 10px;
        }
        main {
            padding: 30px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .section {
            background-color: white;
            margin: 20px 0;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            max-width: 900px;
            width: 100%;
        }
        .section h2 {
            margin-bottom: 20px;
            font-size: 2em;
            color: #00274d;
            text-align: center;
        }
        nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        nav ul li {
            margin: 15px 0;
            text-align: center;
        }
        nav ul li a {
            text-decoration: none;
            color: white;
            font-weight: bold;
            background-color: #a5673f;
            padding: 15px 25px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
            display: inline-block;
            width: calc(100% - 40px);
            max-width: 400px;
        }
        nav ul li a:hover {
            background-color: #00274d;
            transform: scale(1.05);
        }
        footer {
            margin-top: 40px;
            padding: 15px;
            background-color: transparent;
            text-align: center;
            font-size: 1em;
        }
        footer p {
            margin: 0;
            color: #00274d;
            font-weight: bold;
        }
        @media (max-width: 768px) {
            header h1 {
                font-size: 2.5em;
            }
            header p {
                font-size: 1.2em;
            }
            .section {
                padding: 20px;
            }
            nav ul li a {
                width: calc(100% - 20px);
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
        <div class="section">
            <h2>Esplora le funzionalità</h2>
            <nav>
                <ul>
                    <?php foreach ($menus as $menuName => $menuLink): ?>
                        <li><a href="<?php echo htmlspecialchars($menuLink); ?>">📋 <?php echo htmlspecialchars($menuName); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        </div>
    </main>
    <footer>
        <p>Made by Pansera Luca</p>
    </footer>
</body>
</html>
