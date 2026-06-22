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
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="logo-container">
            <h1>Gattile Sabaudo</h1>
        </div>
        <div class="user-status">
            <?php if (isset($_SESSION['username'])): ?>
                <span>Benvenuto, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <?php else: ?>
                <span>Non loggato</span>
            <?php endif; ?>
        </div>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="gatti.php">I nostri gatti</a></li>
                <?php if (isset($_SESSION['username'])): ?>
                    <li><a href="volontariato.php">Volontariato</a></li>
                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                        <li><a href="inserimento.php">Inserisci Gatto (Admin)</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="registrazione.php">Registrati</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main>
