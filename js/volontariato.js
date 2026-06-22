// js/volontariato.js

document.addEventListener('DOMContentLoaded', () => {
    caricaDisponibilita();
});

function caricaDisponibilita() {
    fetch('api/turni.php')
        .then(res => res.json())
        .then(data => {
            const select = document.getElementById('fascia_oraria');
            const options = select.querySelectorAll('option');
            
            // Crea una mappa fasce -> iscritti
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
    
    // Invia richiesta POST al backend per la prenotazione effettiva
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
            caricaDisponibilita(); // Aggiorna la tendina
        }
    })
    .catch(err => {
        // Intercetta e visualizza l'errore del server (limite raggiunto o duplicato)
        errorDiv.innerHTML = "Errore: " + err.message;
        errorDiv.style.display = 'block';
        caricaDisponibilita(); // Aggiorna per riflettere lo stato reale
    });
    
    return false;
}
