<?php
session_start();
require_once __DIR__ . '/db.php';

// Výchozí hodnota pro page
$page = $_GET["page"] ?? "home";

// Cesta k souboru stránky
$pageFile = __DIR__ . "/pages/" . $page . ".php";

// Zobrazení chyb pro účely vývoje (lze v produkci skrýt)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Místo přímého echa používáme output buffering. 
// Díky tomu můžeme na konkrétních podstránkách provádět header řádek pro redirect (PRG) dříve, než se vypíše HTML.
ob_start();

if (file_exists($pageFile)) {
    require_once $pageFile;
} else {
    require_once __DIR__ . "/pages/not_found.php";
}

$content = ob_get_clean();
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Můj Profil - <?= htmlspecialchars(ucfirst($page)) ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>Můj IT Profil</h1>
</header>

<nav>
    <a href="?page=home" class="<?= $page === 'home' ? 'active' : '' ?>">Domů</a>
    <a href="?page=interests" class="<?= $page === 'interests' ? 'active' : '' ?>">Zájmy</a>
    <a href="?page=skills" class="<?= $page === 'skills' ? 'active' : '' ?>">Dovednosti</a>
</nav>

<main>
    <?php
    // Zobrazení flash messages (úspěch nebo chyba)
    if (isset($_SESSION['success'])) {
        echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success']) . '</div>';
        unset($_SESSION['success']);
    }
    if (isset($_SESSION['error'])) {
        echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error']) . '</div>';
        unset($_SESSION['error']);
    }

    // Vykreslení samotného obsahu vráceného z podstránky
    echo $content;
    ?>
</main>

<footer>
    <p>&copy; <?= date("Y") ?> Můj IT Profil</p>
</footer>

</body>
</html>
