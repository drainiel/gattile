<?php
// volontariato.php
require_once 'db.php';
session_start();

$loggato = isset($_SESSION['username']);

if ($loggato) {
    $utente_id = $_SESSION['user_id'];
}

// Fasce orarie a partire da date specifiche presenti nel DB 
$fasce_orarie = [
    '2026-06-05 09:00:00',
    '2026-06-05 11:00:00',
    '2026-06-05 15:00:00',
    '2026-06-06 09:00:00',
    '2026-06-06 11:00:00',
    '2026-06-06 15:00:00'
];

require_once 'includes/header.php';
?>

<?php if (!$loggato): ?>
    <aside class="alert alert-warning text-center mt-2 mb-2">
        Per prenotare una visita conoscitiva o fare volontariato, <a href="login.php">effettua l'accesso</a> o <a href="registrazione.php">registrati</a>.
    </aside>
<?php else: ?>
<section class="mt-2 mb-2" style="padding: 20px 0; text-align: center;">
    <h2 class="auth-title">Prenota Turno di Volontariato</h2>
    <p class="auth-subtitle">Seleziona una fascia oraria. Massimo 2 volontari per fascia.</p>
    
    <output id="js-error-volontariato" class="alert alert-error" style="display:none; width: 50%; margin: 0 auto 20px auto;"></output>
    <output id="js-success-volontariato" class="alert alert-success" style="display:none; width: 50%; margin: 0 auto 20px auto;"></output>

    <form id="form-volontariato" onsubmit="return prenotaTurno(event)" style="display: flex; flex-wrap: wrap; gap: 20px; align-items: flex-end; justify-content: center;">
        
        <input type="hidden" id="utente_id" value="<?php echo htmlspecialchars($utente_id); ?>">
        
        <div class="form-group" style="flex: 1; max-width: 350px; margin-bottom: 0; text-align: left;"> 
            <label for="fascia_oraria">Fascia Oraria</label>
            <select name="fascia_oraria" id="fascia_oraria" required>
                <option value="">Seleziona...</option>
                <?php foreach ($fasce_orarie as $fascia): ?>
                    <option value="<?php echo htmlspecialchars($fascia); ?>"><?php echo htmlspecialchars($fascia); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" id="btn-prenota-turno" style="width: auto; margin-top: 0; padding-left: 40px; padding-right: 40px;">Prenota Turno</button>
    </form>
</section>

<script src="js/volontariato.js"></script>
<?php endif; ?>

<?php
require_once 'includes/footer.php';
?>