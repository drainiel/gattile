<?php
// registrazione.php
require_once 'db.php';
session_start();

if (isset($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // In un'applicazione reale la validazione server-side dovrebbe rispecchiare quella client-side.
    // Qui aggiungiamo un controllo di base e l'inserimento, delegando al JS i controlli stringenti come richiesto.
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
            // Usa il privilegio minimo: solo INSERIMENTO
            $pdo = getDBConnection('registrator');
            
            // Verifica duplicati? L'utente registrator ha solo INSERT. Se il vincolo UNIQUE fallisce, genererà eccezione.
            $stmt = $pdo->prepare("INSERT INTO utenti (nome, cognome, indirizzo, username, password, is_admin) VALUES (?, ?, ?, ?, ?, 0)");
            
            // ATTENZIONE: Salvataggio password in chiaro (NON SICURO). 
            // In un ambiente di produzione deve essere utilizzato password_hash().
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
    <h2 class="text-center">Registrazione</h2>
    
    <?php if ($error): ?>
        <output class="alert alert-error"><?php echo htmlspecialchars($error); ?></output>
    <?php endif; ?>
    <?php if ($success): ?>
        <output class="alert alert-success"><?php echo htmlspecialchars($success); ?></output>
    <?php endif; ?>
    
    <form method="post" action="registrazione.php" id="form-registrazione" onsubmit="return validateRegistration(event)">
        <output id="js-error" class="alert alert-error" style="display:none;"></output>
        
        <label class="form-group" for="nome">Nome
            <input type="text" name="nome" id="nome" required>
        </label>
        <label class="form-group" for="cognome">Cognome
            <input type="text" name="cognome" id="cognome" required>
        </label>
        <label class="form-group" for="indirizzo">Indirizzo
            <input type="text" name="indirizzo" id="indirizzo" required>
        </label>
        <label class="form-group" for="username">Username
            <input type="text" name="username" id="username" required>
            <small>Deve iniziare con una lettera.</small>
        </label>
        <label class="form-group" for="password">Password
            <input type="password" name="password" id="password" required>
            <small>8-16 caratteri, almeno: 1 maiuscola, 1 minuscola, 1 numero, 1 carattere speciale.</small>
        </label>
        <label class="form-group" for="conferma_password">Conferma Password
            <input type="password" name="conferma_password" id="conferma_password" required>
        </label>
        <button type="submit" id="btn-registrati">Registrati</button>
    </form>
</section>

<script src="js/validation.js"></script>

<?php
require_once 'includes/footer.php';
?>
