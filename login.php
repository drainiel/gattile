<?php
// login.php
require_once 'db.php';
session_start();

// Percorso del file JSON che associa token opachi agli username (funzionalità "Ricordami")
define('TOKENS_FILE', __DIR__ . '/data/tokens.json');

if (isset($_SESSION['username'])) {
    header('Location: home.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $ricordami = isset($_POST['ricordami']);
    
    if (empty($username) || empty($password)) {
        $error = "Inserisci username e password.";
    } else {
        try {
            $pdo = getDBConnection('lecture');
            $stmt = $pdo->prepare("SELECT * FROM utenti WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user && $user['password'] === $password) {
                session_regenerate_id(true);
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['is_admin'] = (bool)$user['is_admin'];
                
                if ($ricordami) {
                    // Genera un token opaco e lo associa allo username nel file JSON lato server
                    $token = bin2hex(random_bytes(16));

                    $tokens = [];
                    if (file_exists(TOKENS_FILE)) {
                        $json = file_get_contents(TOKENS_FILE);
                        $tokens = json_decode($json, true) ?? [];
                    }
                    $tokens[$token] = $username;
                    file_put_contents(TOKENS_FILE, json_encode($tokens, JSON_PRETTY_PRINT), LOCK_EX);

                    // Il cookie contiene solo il token opaco, non lo username (valido 72 ore)
                    setcookie('ricordami_user', $token, time() + (72 * 3600), '/');
                } else {
                    // Rimuove il cookie e l'eventuale token dal file JSON
                    if (isset($_COOKIE['ricordami_user'])) {
                        $oldToken = $_COOKIE['ricordami_user'];
                        if (file_exists(TOKENS_FILE)) {
                            $tokens = json_decode(file_get_contents(TOKENS_FILE), true) ?? [];
                            unset($tokens[$oldToken]);
                            file_put_contents(TOKENS_FILE, json_encode($tokens, JSON_PRETTY_PRINT), LOCK_EX);
                        }
                    }
                    setcookie('ricordami_user', '', time() - 3600, '/');
                }
                
                header('Location: home.php');
                exit;
            } else {
                $error = "Credenziali non valide.";
            }
        } catch (Exception $e) {
            $error = "Errore di connessione: " . $e->getMessage();
        }
    }
}

// Pre-compila il campo username risolvendo il token opaco dal cookie tramite il file JSON
$saved_username = '';
if (isset($_COOKIE['ricordami_user'])) {
    $token = $_COOKIE['ricordami_user'];
    if (file_exists(TOKENS_FILE)) {
        $tokens = json_decode(file_get_contents(TOKENS_FILE), true) ?? [];
        if (isset($tokens[$token])) {
            $saved_username = $tokens[$token];
        }
    }
}

require_once 'includes/header.php';
?> 

<section class="form-container mt-2 mb-2">
    <h2 class="auth-title text-center">Login</h2>
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <form method="post" action="login.php" id="form-login">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($saved_username); ?>" placeholder="Il tuo username" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" value="" placeholder="Inserisci la tua password" required>
        </div>
        <div class="form-group" style="display: flex; flex-direction: row; align-items: center;">
            <input type="checkbox" name="ricordami" id="ricordami" <?php echo $saved_username ? 'checked' : ''; ?> style="width: auto; margin: 0 10px 0 0; cursor: pointer;">
            <label for="ricordami" style="margin: 0; font-weight: normal; cursor: pointer; font-size: var(--font-size-sm);">Ricordami (per 72 ore)</label>
        </div>
        <button type="submit" id="btn-login">Accedi</button>
    </form>
</section>

<?php
require_once 'includes/footer.php';
?>