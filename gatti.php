<?php
// gatti.php
require_once 'db.php';
require_once 'includes/header.php';

// Controlla se utente loggato per mostrare il form di prenotazione
$is_logged_in = isset($_SESSION['username']);
?>

<section class="mb-2">
    <h2 class="auth-title">I Nostri Gatti</h2>
    <div id="react-root"></div>
</section>

<?php if ($is_logged_in): ?>
<section class="mt-2 mb-2" style="padding: 20px 0;">
    <h2 class="auth-title">Prenota una visita</h2>
    <p class="auth-subtitle">Seleziona i gatti che ti interessano dalla lista sopra e scegli una data.</p>
    
    <form id="form-prenotazione" style="display: flex; flex-wrap: wrap; gap: 20px; align-items: flex-end;">
        <output id="js-error-prenotazione" class="alert alert-error" style="display:none; width: 100%;"></output>
        <output id="js-success-prenotazione" class="alert alert-success" style="display:none; width: 100%;"></output>
        
        <div class="form-group" style="flex: 1; min-width: 250px; margin-bottom: 0;">
            <label>Gatti Selezionati</label>
            <div id="gatti-selezionati-list" style="font-weight: bold; color: var(--primary-color); margin-top: 4px; padding: 13px 0;">Nessun gatto selezionato</div>
            <input type="hidden" name="gatti_ids" id="gatti_ids" value="">
        </div>
        
        <div class="form-group" style="flex: 1; min-width: 250px; margin-bottom: 0;">
            <label for="data_visita">Data e Ora della visita</label>
            <input type="datetime-local" name="data_visita" id="data_visita" required>
        </div>
        
        <button type="button" id="btn-prenota" onclick="inviaPrenotazione()" style="width: auto; margin-top: 0; padding-left: 40px; padding-right: 40px;">Prenota Visita</button>
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
    
    if (new Date(dataVisita) < new Date()) { 
        errorDiv.innerHTML = "Non puoi viaggiare indietro nel tempo, inserisci una data futura ;)";
        errorDiv.style.display = 'block';
        return;
    }
    
    if (selectedCatsData.length === 0) {
        errorDiv.innerHTML = "Seleziona almeno un gatto dalla galleria prima di prenotare.";
        errorDiv.style.display = 'block';
        return;
    }
     
    fetch('api/prenotazioni.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            data_visita: dataVisita,
            gatti_ids: selectedCatsData.map(c => c.id)
        })
    })
    .then(response => response.json().then(data => ({ ok: response.ok, data })))
    .then(({ ok, data }) => {
        if (ok) {
            successDiv.innerHTML = "Visita prenotata con successo!";
            successDiv.style.display = 'block';
        } else {
            errorDiv.innerHTML = data.error || "Errore durante la prenotazione.";
            errorDiv.style.display = 'block';
        }
    })
    .catch(() => {
        errorDiv.innerHTML = "Errore di rete, riprova.";
        errorDiv.style.display = 'block';
    });
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
<script type="text/babel" src="js/GattiApp.jsx?v=2"></script>

<?php
require_once 'includes/footer.php';
?>