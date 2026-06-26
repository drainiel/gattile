<?php
// inserimento.php
require_once 'db.php';
session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] == false) {
    header('Location: home.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $descrizione = trim($_POST['descrizione'] ?? '');
    $peso = (float)($_POST['peso'] ?? 0);
    $colore_mantello = trim($_POST['colore_mantello'] ?? '');
    $lunghezza_pelo = trim($_POST['lunghezza_pelo'] ?? '');
    $razza = trim($_POST['razza'] ?? '');
    $colore_occhi = trim($_POST['colore_occhi'] ?? '');
    $eta = (int)($_POST['eta'] ?? 0);
    $sesso = trim($_POST['sesso'] ?? '');
    $data_arrivo = trim($_POST['data_arrivo'] ?? '');
    
    if (empty($nome) || empty($descrizione) || empty($colore_mantello) || empty($lunghezza_pelo) || empty($razza) || empty($colore_occhi) || empty($sesso) || empty($data_arrivo) || $peso <= 0 || $eta < 0) {
        $error = "Dati non validi. Compila tutti i campi correttamente.";
    } else {
        try {
            $pdo = getDBConnection('modifier');
            $stmt = $pdo->prepare("INSERT INTO gatti (nome, descrizione, peso, colore_mantello, lunghezza_pelo, razza, colore_occhi, eta, sesso, data_arrivo, foto) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nome, $descrizione, $peso, $colore_mantello, $lunghezza_pelo, $razza, $colore_occhi, $eta, $sesso, $data_arrivo, 'images/placeholder.jpg']);
            
            $success = "Nuovo gatto inserito con successo!";
        } catch (Exception $e) {
            $error = "Errore durante l'inserimento: " . $e->getMessage();
        }
    }
}

require_once 'includes/header.php';
?>

<section class="form-container mt-2 mb-2" style="max-width: 600px;">
    <h2 class="auth-title">Inserimento Nuovo Gatto</h2>
    <p class="auth-subtitle">Compila il form per registrare un nuovo ospite del gattile.</p>
    
    <?php if ($error): ?>
        <output class="alert alert-error"><?php echo htmlspecialchars($error); ?></output>
    <?php endif; ?>
    <?php if ($success): ?>
        <output class="alert alert-success"><?php echo htmlspecialchars($success); ?></output>
    <?php endif; ?>
    
    <form method="post" action="inserimento.php" id="form-inserimento" onsubmit="return validateCatInsertion(event)">
        <output id="js-error-cat" class="alert alert-error" style="display:none;"></output>
        
        <div class="form-group">
            <label for="nome">Nome</label>
            <input type="text" name="nome" id="nome" placeholder="Es. Fuffi" required>
        </div>
        <div class="form-group">
            <label for="descrizione">Descrizione (Carattere e storia)</label>
            <textarea name="descrizione" id="descrizione" rows="4" placeholder="Breve descrizione del carattere, abitudini o storia del gatto..." required></textarea>
        </div>
        <div class="form-group">
            <label for="peso">Peso (kg)</label>
            <input type="number" step="0.01" name="peso" id="peso" placeholder="Es. 4.5" required>
        </div>
        <div class="form-group">
            <label for="colore_mantello">Colore del mantello</label>
            <input type="text" name="colore_mantello" id="colore_mantello" placeholder="Es. Tigrato, Nero, Bianco e rosso..." required>
        </div>
        <div class="form-group">
            <label for="lunghezza_pelo">Lunghezza del pelo</label>
            <input type="text" name="lunghezza_pelo" id="lunghezza_pelo" placeholder="Es. Corto, Medio, Lungo" required>
        </div>
        <div class="form-group">
            <label for="razza">Razza</label>
            <input type="text" name="razza" id="razza" placeholder="Es. Europeo, Persiano, Meticcio..." required>
        </div>
        <div class="form-group">
            <label for="colore_occhi">Colore degli occhi</label>
            <input type="text" name="colore_occhi" id="colore_occhi" placeholder="Es. Verdi, Gialli, Azzurri..." required>
        </div>
        <div class="form-group">
            <label for="eta">Età (mesi)</label>
            <input type="number" name="eta" id="eta" placeholder="Es. 24" required>
        </div>
        <div class="form-group">
            <label for="sesso">Sesso</label>
            <select name="sesso" id="sesso" required>
                <option value="">Seleziona...</option>
                <option value="M">Maschio</option>
                <option value="F">Femmina</option>
            </select>
        </div>
        <div class="form-group">
            <label for="data_arrivo">Data di arrivo</label>
            <input type="date" name="data_arrivo" id="data_arrivo" required>
        </div>
        
        <button type="submit" id="btn-inserisci">Inserisci gatto</button>
    </form>
</section>

<script src="js/validation.js"></script>

<?php
require_once 'includes/footer.php';
?>