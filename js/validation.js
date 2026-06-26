// js/validation.js 

/**
 * Valida il form di registrazione utente lato client.
 *.  
 * Controlla lo username, i vincoli di complessità della password e che la password e la conferma coincidano.
 * In caso di errori, blocca il submit del form e visualizza i messaggi nell'elemento #js-error.
 *
 * @param {Event} event Evento 'submit' del form di registrazione.
 * @return {boolean} true se la validazione ha successo, false altrimenti.
 */
function validateRegistration(event) {
    const errorDiv = document.getElementById('js-error');
    errorDiv.style.display = 'none';
    errorDiv.innerHTML = '';

    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const conferma = document.getElementById('conferma_password').value;

    let errors = [];

    // Validazione username: comincia con lettera
    const usernameRegex = /^[A-Za-z]/;
    if (!usernameRegex.test(username)) {
        errors.push("Lo username deve cominciare con un carattere alfabetico.");
    }

    /*
     * Regex per la validazione della complessità della password.
     *
     *   ^                  — ancoraggio.
     *   (?=.*[a-z])        — almeno una lettera minuscola.
     *   (?=.*[A-Z])        — almeno una lettera maiuscola.  
     *   (?=.*\d)           — almeno una cifra decimale.
     *   (?=.*[^A-Za-z0-9]) — almeno un carattere speciale.
     *   .{8,16}            — da 8 a 16 caratteri.
     *   $                  — Fine stringa (ancoraggio).
     *
     * I quattro lookahead verificano la presenza dei vincoli indipendentemente dall'ordine dei caratteri.
     */
    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,16}$/;

    if (!passwordRegex.test(password)) {
        errors.push("La password deve essere lunga tra 8 e 16 caratteri e contenere almeno una maiuscola, una minuscola, un numero e un carattere speciale.");
    }

    if (password !== conferma) {
        errors.push("La password e la conferma non coincidono.");
    }

    if (errors.length > 0) {
        event.preventDefault(); // Blocca l'invio del form
        errorDiv.innerHTML = errors.join('<br>');
        errorDiv.style.display = 'block';
        return false;
    }

    return true;
}

/**
 * Valida il form di inserimento di un nuovo gatto lato client.
 *
 * Integra la validazione HTML5 (attributi required) con controlli aggiuntivi sui vincoli di dominio: peso strettamente positivo
 * ed età non negativa. In caso di errori, blocca il submit e visualizza i messaggi nell'elemento #js-error-cat. 
 *
 * @param {Event} event Evento 'submit' del form di inserimento gatto.
 * @return {boolean} true se la validazione ha successo, false altrimenti.
 */
function validateCatInsertion(event) {
    const errorDiv = document.getElementById('js-error-cat');
    errorDiv.style.display = 'none';
    errorDiv.innerHTML = '';

    let errors = [];

    const peso = parseFloat(document.getElementById('peso').value);
    const eta = parseInt(document.getElementById('eta').value);

    if (isNaN(peso) || peso <= 0) {
        errors.push("Il peso deve essere un numero positivo.");
    }

    if (isNaN(eta) || eta < 0) {
        errors.push("L'età non può essere negativa.");
    }

    if (errors.length > 0) {
        event.preventDefault();
        errorDiv.innerHTML = errors.join('<br>');
        errorDiv.style.display = 'block';
        return false;
    }

    return true;
}