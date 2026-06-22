<?php
// volontariato.php
require_once 'db.php';
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}

$utente_id = $_SESSION['user_id'];

require_once 'includes/header.php';

// Generiamo alcune fasce orarie a partire da date specifiche presenti nel DB
// Il DB fornito ha turni il 2026-06-05, creiamo fasce attorno a quelle date per testabilità
$fasce_orarie = [
    '2026-06-05 09:00:00',
    '2026-06-05 11:00:00',
    '2026-06-05 15:00:00',
    '2026-06-06 09:00:00',
    '2026-06-06 11:00:00',
    '2026-06-06 15:00:00'
];
?>

<section class="form-container mt-2 mb-2">
    <h2 class="text-center">Prenota Turno di Volontariato</h2>
    <p class="text-center">Seleziona una fascia oraria. Massimo 2 volontari per fascia.</p>
    
    <form id="form-volontariato" onsubmit="return prenotaTurno(event)">
        <output id="js-error-volontariato" class="alert alert-error" style="display:none;"></output>
        <output id="js-success-volontariato" class="alert alert-success" style="display:none;"></output>
        
        <input type="hidden" id="utente_id" value="<?php echo htmlspecialchars($utente_id); ?>">
        
        <label class="form-group" for="fascia_oraria">Fascia Oraria
            <select name="fascia_oraria" id="fascia_oraria" required>
                <option value="">Seleziona...</option>
                <?php foreach ($fasce_orarie as $fascia): ?>
                    <option value="<?php echo htmlspecialchars($fascia); ?>"><?php echo htmlspecialchars($fascia); ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        
        <button type="submit" id="btn-prenota-turno">Prenota Turno</button>
    </form>
</section>

<script src="js/volontariato.js"></script>

<?php
require_once 'includes/footer.php';
?>
