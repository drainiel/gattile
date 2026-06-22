<?php
// login.php
require_once 'db.php';
session_start();

if (isset($_SESSION['username'])) {
    header('Location: index.php');
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
            
            // Per scopi accademici/testing controlliamo se la password corrisponde in chiaro
            // Nel mondo reale si userebbe password_verify e password_hash
            if ($user && $user['password'] === $password) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['is_admin'] = (bool)$user['is_admin'];
                
                if ($ricordami) {
                    // Cookie valido per 72 ore
                    setcookie('ricordami_user', $username, time() + (72 * 3600), '/');
                } else {
                    // Rimuove il cookie se non selezionato
                    setcookie('ricordami_user', '', time() - 3600, '/');
                }
                
                header('Location: index.php');
                exit;
            } else {
                $error = "Credenziali non valide.";
            }
        } catch (Exception $e) {
            $error = "Errore di connessione: " . $e->getMessage();
        }
    }
}

// Pre-compila username se cookie presente
$saved_username = $_COOKIE['ricordami_user'] ?? '';

require_once 'includes/header.php';
?>

<section class="form-container mt-2 mb-2">
    <h2 class="text-center">Login</h2>
    <?php if ($error): ?>
        <output class="alert alert-error"><?php echo htmlspecialchars($error); ?></output>
    <?php endif; ?>
    
    <form method="post" action="login.php" id="form-login">
        <label class="form-group" for="username">Username
            <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($saved_username); ?>" required>
        </label>
        <label class="form-group" for="password">Password
            <input type="password" name="password" id="password" value="" required>
        </label>
        <label class="form-group">
            <input type="checkbox" name="ricordami" id="ricordami" <?php echo $saved_username ? 'checked' : ''; ?>>
            Ricordami (per 72 ore)
        </label>
        <button type="submit" id="btn-login">Accedi</button>
    </form>
</section>

<?php
require_once 'includes/footer.php';
?>
