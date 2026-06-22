<?php
// inserimento.php
require_once 'db.php';
session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] == false) {
    header('Location: index.php');
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
            $stmt = $pdo->prepare("INSERT INTO gatti (nome, descrizione, peso, colore_mantello, lunghezza_pelo, razza, colore_occhi, eta, sesso, data_arrivo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nome, $descrizione, $peso, $colore_mantello, $lunghezza_pelo, $razza, $colore_occhi, $eta, $sesso, $data_arrivo]);
            
            $success = "Scheda gatto creata con successo! L'immagine placeholder è stata assegnata automaticamente.";
        } catch (Exception $e) {
            $error = "Errore durante l'inserimento: " . $e->getMessage();
        }
    }
}

require_once 'includes/header.php';
?>

<section class="form-container mt-2 mb-2" style="max-width: 600px;">
    <h2 class="text-center">Inserimento Nuovo Gatto</h2>
    
    <?php if ($error): ?>
        <output class="alert alert-error"><?php echo htmlspecialchars($error); ?></output>
    <?php endif; ?>
    <?php if ($success): ?>
        <output class="alert alert-success"><?php echo htmlspecialchars($success); ?></output>
    <?php endif; ?>
    
    <form method="post" action="inserimento.php" id="form-inserimento" onsubmit="return validateCatInsertion(event)">
        <output id="js-error-cat" class="alert alert-error" style="display:none;"></output>
        
        <label class="form-group" for="nome">Nome
            <input type="text" name="nome" id="nome" required>
        </label>
        <label class="form-group" for="descrizione">Descrizione (Carattere e storia)
            <textarea name="descrizione" id="descrizione" rows="4" required></textarea>
        </label>
        <label class="form-group" for="peso">Peso (kg)
            <input type="number" step="0.01" name="peso" id="peso" required>
        </label>
        <label class="form-group" for="colore_mantello">Colore del mantello
            <input type="text" name="colore_mantello" id="colore_mantello" required>
        </label>
        <label class="form-group" for="lunghezza_pelo">Lunghezza del pelo
            <input type="text" name="lunghezza_pelo" id="lunghezza_pelo" required>
        </label>
        <label class="form-group" for="razza">Razza
            <input type="text" name="razza" id="razza" required>
        </label>
        <label class="form-group" for="colore_occhi">Colore degli occhi
            <input type="text" name="colore_occhi" id="colore_occhi" required>
        </label>
        <label class="form-group" for="eta">Età (mesi)
            <input type="number" name="eta" id="eta" required>
        </label>
        <label class="form-group" for="sesso">Sesso
            <select name="sesso" id="sesso" required>
                <option value="">Seleziona...</option>
                <option value="M">Maschio</option>
                <option value="F">Femmina</option>
            </select>
        </label>
        <label class="form-group" for="data_arrivo">Data di arrivo
            <input type="date" name="data_arrivo" id="data_arrivo" required>
        </label>
        
        <button type="submit" id="btn-inserisci">Crea Scheda</button>
    </form>
</section>

<script src="js/validation.js"></script>

<?php
require_once 'includes/footer.php';
?>
