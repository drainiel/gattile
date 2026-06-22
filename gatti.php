<?php
// gatti.php
require_once 'db.php';
require_once 'includes/header.php';

// Controlla se utente loggato per mostrare il form di prenotazione
$is_logged_in = isset($_SESSION['username']);
?>

<section class="mb-2">
    <h2 class="text-center">I Nostri Gatti</h2>
    <div id="react-root"></div>
</section>

<?php if ($is_logged_in): ?>
<section class="form-container mt-2 mb-2">
    <h3 class="text-center">Prenota una visita</h3>
    <p>Seleziona i gatti che ti interessano dalla lista sopra e scegli una data.</p>
    
    <form id="form-prenotazione">
        <output id="js-error-prenotazione" class="alert alert-error" style="display:none;"></output>
        <output id="js-success-prenotazione" class="alert alert-success" style="display:none;"></output>
        
        <label class="form-group">Gatti Selezionati:
            <div id="gatti-selezionati-list" style="font-weight: bold; margin-bottom: 10px; color: var(--primary-color);">Nessun gatto selezionato</div>
            <input type="hidden" name="gatti_ids" id="gatti_ids" value="">
        </label>
        
        <label class="form-group" for="data_visita">Data e Ora della visita
            <input type="datetime-local" name="data_visita" id="data_visita" required>
        </label>
        
        <button type="button" id="btn-prenota" onclick="inviaPrenotazione()">Prenota Visita</button>
    </form>
</section>

<script>
// Vanilla JS per la prenotazione visita
let selectedCatsData = [];

// Ascolta l'evento personalizzato dal componente React
document.addEventListener('catsSelected', function(e) {
    selectedCatsData = e.detail;
    updateSelectedCatsUI();
});

function updateSelectedCatsUI() {
    const listDiv = document.getElementById('gatti-selezionati-list');
    const hiddenInput = document.getElementById('gatti_ids');
    
    if (selectedCatsData.length === 0) {
        listDiv.innerHTML = "Nessun gatto selezionato";
        hiddenInput.value = "";
    } else {
        const names = selectedCatsData.map(c => c.nome).join(', ');
        listDiv.innerHTML = names;
        hiddenInput.value = JSON.stringify(selectedCatsData.map(c => c.id));
    }
}

function inviaPrenotazione() {
    const dataVisita = document.getElementById('data_visita').value;
    const errorDiv = document.getElementById('js-error-prenotazione');
    const successDiv = document.getElementById('js-success-prenotazione');
    
    errorDiv.style.display = 'none';
    successDiv.style.display = 'none';
    
    if (!dataVisita) {
        errorDiv.innerHTML = "Seleziona una data per la visita.";
        errorDiv.style.display = 'block';
        return;
    }
    
    if (selectedCatsData.length === 0) {
        errorDiv.innerHTML = "Seleziona almeno un gatto dalla galleria prima di prenotare.";
        errorDiv.style.display = 'block';
        return;
    }
    
    // In una versione completa qui ci sarebbe una chiamata fetch per salvare la visita_gatti e prenotazioni_visite nel DB
    successDiv.innerHTML = "Visita prenotata con successo per " + dataVisita + " con i gatti: " + selectedCatsData.map(c => c.nome).join(', ');
    successDiv.style.display = 'block';
}
</script>

<?php else: ?>
<aside class="alert alert-warning text-center mt-2 mb-2">
    Per prenotare una visita conoscitiva o fare volontariato, <a href="login.php">effettua l'accesso</a> o <a href="registrazione.php">registrati</a>.
</aside>
<?php endif; ?>

<!-- React e Babel -->
<script src="https://unpkg.com/react@18/umd/react.development.js" crossorigin></script>
<script src="https://unpkg.com/react-dom@18/umd/react-dom.development.js" crossorigin></script>
<script src="https://unpkg.com/@babel/standalone@7.21.0/babel.min.js"></script>

<!-- Componente React -->
<script type="text/babel">
    window.IS_LOGGED_IN = <?php echo $is_logged_in ? 'true' : 'false'; ?>;
</script>
<script type="text/babel" src="js/GattiApp.jsx"></script>

<?php
require_once 'includes/footer.php';
?>
