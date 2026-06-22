CREATE DATABASE IF NOT EXISTS gattile_db;
USE gattile_db;

CREATE TABLE IF NOT EXISTS utenti (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL,
    cognome VARCHAR(50) NOT NULL,
    indirizzo VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    is_admin BOOLEAN NOT NULL DEFAULT FALSE,
    INDEX indice_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS gatti (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL,
    descrizione TEXT NOT NULL,
    peso DECIMAL(4,2) NOT NULL,
    colore_mantello VARCHAR(30) NOT NULL,
    lunghezza_pelo VARCHAR(20) NOT NULL,
    razza VARCHAR(50) NOT NULL,
    colore_occhi VARCHAR(30) NOT NULL,
    eta INT NOT NULL,
    sesso ENUM('M', 'F') NOT NULL,
    data_arrivo DATE NOT NULL,
    INDEX indice_data_arrivo (data_arrivo),
    INDEX indice_nome_gatto (nome)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS prenotazioni_visite (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utente_id INT NOT NULL,
    data_ora DATETIME NOT NULL,
    FOREIGN KEY (utente_id) REFERENCES utenti(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS visita_gatti (
    prenotazione_id INT NOT NULL,
    gatto_id INT NOT NULL,
    PRIMARY KEY (prenotazione_id, gatto_id),
    FOREIGN KEY (prenotazione_id) REFERENCES prenotazioni_visite(id) ON DELETE CASCADE,
    FOREIGN KEY (gatto_id) REFERENCES gatti(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS turni_volontariato (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utente_id INT NOT NULL,
    fascia_oraria DATETIME NOT NULL,
    FOREIGN KEY (utente_id) REFERENCES utenti(id) ON DELETE CASCADE,
    UNIQUE KEY vincolo_unico_turno (utente_id, fascia_oraria)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE USER IF NOT EXISTS 'lecture'@'localhost' IDENTIFIED BY 'P@ssw0rd!';
CREATE USER IF NOT EXISTS 'modifier'@'localhost' IDENTIFIED BY 'Str0ng#Admin9';
CREATE USER IF NOT EXISTS 'registrator'@'localhost' IDENTIFIED BY 'ToB31nsert?';

GRANT SELECT ON gattile_db.* TO 'lecture'@'localhost';
GRANT SELECT,INSERT,UPDATE ON gattile_db.* TO 'modifier'@'localhost';
GRANT INSERT ON gattile_db.utenti TO 'registrator'@'localhost';

FLUSH PRIVILEGES;


INSERT INTO utenti (nome, cognome, indirizzo, username, password, is_admin) VALUES
('Anna', 'Verdi', 'Via Roma 10, Torino', 'anna_admin', 'Admin2026!', TRUE),
('Fabio', 'Rizzo', 'Corso Francia 45, Torino', 'fabio_admin', 'Admin2026!', TRUE),
('Mario', 'Rossi', 'Corso Duca degli Abruzzi 24, Torino', 'mario_volontario', 'Password123!', FALSE),
('Elena', 'Bianchi', 'Via Po 5, Torino', 'elena_b', 'Password123!', FALSE),
('Luca', 'Neri', 'Via Garibaldi 12, Torino', 'luca_neri', 'Password123!', FALSE),
('Giulia', 'Bruni', 'Via Nizza 88, Torino', 'giulia_b', 'Password123!', FALSE);

INSERT INTO gatti (nome, descrizione, peso, colore_mantello, lunghezza_pelo, razza, colore_occhi, eta, sesso, data_arrivo) VALUES
('Fuffi', 'Molto affettuoso, ama giocare con le palline di lana.', 4.20, 'Tigrato', 'Corto', 'Europeo', 'Verdi', 24, 'M', '2026-01-15'),
('Luna', 'Inizialmente timida, cerca una casa tranquilla.', 3.50, 'Bianco', 'Lungo', 'Persiano', 'Azzurri', 12, 'F', '2026-03-10'),
('Oliver', 'Gattino energico e instancabile, ottimo per i bambini.', 2.10, 'Nero', 'Corto', 'Europeo', 'Gialli', 5, 'M', '2026-05-20'),
('Chloè', 'Trovata per strada appena nata, ora è in salute.', 1.80, 'Calico', 'Medio', 'Incrocio', 'Verdi', 3, 'F', '2026-05-25');

INSERT INTO prenotazioni_visite (utente_id, data_ora) VALUES
(3, '2026-06-01 10:30:00'),
(4, '2026-06-02 15:00:00');

INSERT INTO visita_gatti (prenotazione_id, gatto_id) VALUES
(1, 1),
(1, 2),
(2, 4);

INSERT INTO turni_volontariato (utente_id, fascia_oraria) VALUES
(3, '2026-06-05 09:00:00'),
(4, '2026-06-05 09:00:00'),
(5, '2026-06-05 11:00:00');
