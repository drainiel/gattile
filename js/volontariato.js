// js/volontariato.js

document.addEventListener('DOMContentLoaded', () => {
    caricaDisponibilita();
});

/**
 * Recupera lo stato di occupazione dei turni dal server e aggiorna le opzioni del menu a tendina #fascia_oraria.
 *
 * Effettua una richiesta GET a api/turni.php per ottenere il conteggio  dei volontari iscritti per fascia.
 * Le fasce che hanno raggiunto il limite massimo (≥ 2 iscritti) vengono disabilitate.
 *
 * @return {void}
 */
function caricaDisponibilita() {
    fetch('api/turni.php')
        .then(res => res.json())
        .then(data => {
            const select = document.getElementById('fascia_oraria');
            const options = select.querySelectorAll('option');

            // Costruisce una mappa { fascia_oraria → numero_iscritti } per lookup O(1)
            const iscrittiMap = {};
            if (Array.isArray(data)) {
                data.forEach(item => {
                    iscrittiMap[item.fascia_oraria] = parseInt(item.iscritti, 10);
                });
            }

            options.forEach(opt => {
                if (opt.value) {
                    const iscritti = iscrittiMap[opt.value] || 0;
                    if (iscritti >= 2) {
                        opt.disabled = true;
                        opt.text = opt.text.replace(' (Pieno)', '') + ' (Pieno)';
                    } else {
                        opt.disabled = false;
                        opt.text = opt.text.replace(' (Pieno)', '');
                    }
                }
            });
        })
        .catch(err => console.error("Errore nel caricamento disponibilità", err));
}

/**
 * Gestisce il submit del form di prenotazione turno di volontariato.
 *
 * Invia una richiesta POST a api/turni.php con il payload JSON contenente utente_id e fascia_oraria. 
 * Gestisce le risposte di successo/errore aggiornando i relativi elementi di feedback nel DOM.
 * Al termine di ogni operazione richiama caricaDisponibilita() per mantenere sincronizzata UI con server.
 *
 * @param {Event} event Evento 'submit' del form di prenotazione. 
 * @return {boolean} Restituisce sempre false per impedire il submit del form.
 */
function prenotaTurno(event) {
    event.preventDefault();

    const utenteId = document.getElementById('utente_id').value;
    const fasciaOraria = document.getElementById('fascia_oraria').value;
    const errorDiv = document.getElementById('js-error-volontariato');
    const successDiv = document.getElementById('js-success-volontariato');

    errorDiv.style.display = 'none';
    successDiv.style.display = 'none';

    if (!fasciaOraria) {
        errorDiv.innerHTML = "Seleziona una fascia oraria.";
        errorDiv.style.display = 'block';
        return false;
    }

    fetch('api/turni.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            utente_id: utenteId,
            fascia_oraria: fasciaOraria
        })
    })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) {
                throw new Error(data.error || "Errore sconosciuto");
            }
            return data;
        })
        .then(data => {
            if (data.success) {
                successDiv.innerHTML = "Turno prenotato con successo!";
                successDiv.style.display = 'block';
                caricaDisponibilita();
            }
        })
        .catch(err => {
            errorDiv.innerHTML = "Errore: " + err.message;
            errorDiv.style.display = 'block';
            caricaDisponibilita();
        });

    return false;
}