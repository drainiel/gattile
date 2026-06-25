// js/validation.js

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

    // Validazione password: lunga 8-16, 1 maiuscola, 1 minuscola, 1 numero, 1 speciale
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

function validateCatInsertion(event) {
    // Validazione base, tutti i campi HTML5 required fanno la maggior parte del lavoro.
    // Si può aggiungere validazione extra per età o peso (positivi).
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
