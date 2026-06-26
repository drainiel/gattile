<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gattile - Gattile Sabaudo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/svg+xml" href="images/fav-icon.svg">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="logo-container"> 
            <h1><a href="home.php" style="color: inherit; text-decoration: none;">Gattile Sabaudo</a></h1>
        </div>
        <nav>
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="gatti.php">I nostri gatti</a></li>
                <?php if (isset($_SESSION['username'])): ?>
                    <li><a href="volontariato.php">Volontariato</a></li>
                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                        <li><a href="inserimento.php">Inserisci Gatto</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="registrazione.php">Registrati</a></li> 
                <?php endif; ?>
            </ul>
            <div class="user-status" style="margin-left: 15px;">
                <span style="margin-right: 15px; color: var(--text-light);">|</span>
                <?php if (isset($_SESSION['username'])): ?>
                    <span>Ciao, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <?php else: ?>
                    <span>Non loggato</span>
                <?php endif; ?>
            </div>
        </nav>
    </header>
    <main>
