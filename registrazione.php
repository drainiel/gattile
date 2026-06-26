<?php
// registrazione.php
require_once 'db.php';
session_start();

if (isset($_SESSION['username'])) {
    header('Location: home.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $cognome = trim($_POST['cognome'] ?? '');
    $indirizzo = trim($_POST['indirizzo'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $conferma = $_POST['conferma_password'] ?? '';
    
    if ($password !== $conferma) {
        $error = "Le password non coincidono.";
    } elseif (empty($nome) || empty($cognome) || empty($indirizzo) || empty($username) || empty($password)) {
        $error = "Tutti i campi sono obbligatori.";
    } else {
        try {
            $pdo = getDBConnection('registrator');
            $stmt = $pdo->prepare("INSERT INTO utenti (nome, cognome, indirizzo, username, password, is_admin) VALUES (?, ?, ?, ?, ?, 0)");            
            $stmt->execute([$nome, $cognome, $indirizzo, $username, $password]);
            
            $success = "Registrazione completata con successo! Ora puoi effettuare il login.";
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = "Username già in uso.";
            } else {
                $error = "Errore durante la registrazione: " . $e->getMessage();
            }
        }
    }
}

require_once 'includes/header.php';
?>

<section class="form-container mt-2 mb-2">
    <h2 class="auth-title text-center">Crea un account</h2>
    
    <?php if ($error): ?>
        <output class="alert alert-error"><?php echo htmlspecialchars($error); ?></output>
    <?php endif; ?>
    <?php if ($success): ?>
        <output class="alert alert-success"><?php echo htmlspecialchars($success); ?></output>
    <?php endif; ?>
    
    <form method="post" action="registrazione.php" id="form-registrazione" onsubmit="return validateRegistration(event)">
        <output id="js-error" class="alert alert-error" style="display:none;"></output>
        
        <div class="form-group">
            <label for="nome">Nome</label>
            <input type="text" name="nome" id="nome" placeholder="Il tuo nome" required>
        </div>
        <div class="form-group">
            <label for="cognome">Cognome</label>
            <input type="text" name="cognome" id="cognome" placeholder="Il tuo cognome" required>
        </div>
        <div class="form-group">
            <label for="indirizzo">Indirizzo</label>
            <input type="text" name="indirizzo" id="indirizzo" placeholder="Il tuo indirizzo" required>
        </div>
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" placeholder="Scegli un username" required>
            <small class="form-hint">Deve iniziare con una lettera.</small>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" placeholder="Crea una password" required>
            <small class="form-hint">8-16 caratteri, almeno: 1 maiuscola, 1 minuscola, 1 numero, 1 carattere speciale.</small>
        </div>
        <div class="form-group">
            <label for="conferma_password">Conferma Password</label>
            <input type="password" name="conferma_password" id="conferma_password" placeholder="Conferma la tua password" required>
        </div>
        <button type="submit" id="btn-registrati">Crea account</button>
    </form>
</section>

<script src="js/validation.js"></script>

<?php
require_once 'includes/footer.php';
?>