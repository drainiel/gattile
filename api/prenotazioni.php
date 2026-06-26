<?php
/**
 * API REST — Prenotazione Visite Gatti
 *
 * Endpoint: api/prenotazioni.php
 *
 * Metodi supportati: 
 *
 *   POST — Registra una nuova prenotazione di visita conoscitiva,
 *          associando uno o più gatti selezionati dall'utente.
 *     Payload JSON (Content-Type: application/json):
 *       {
 *         "data_visita":  string  — Data e ora della visita (formato datetime-local),
 *         "gatti_ids":    int[]   — Array di ID dei gatti selezionati
 *       }
 *     Risposta (200 OK):
 *       { "success": true }
 *
 * Codici di errore:
 *   401 Unauthorized        — Utente non autenticato (sessione assente).
 *                              Corpo: { "error": "<messaggio>" }
 *   400 Bad Request         — Campi obbligatori mancanti nel payload.
 *                              Corpo: { "error": "<messaggio>" }
 *   500 Internal Server Error — Errore generico del server o del database.
 *                              Corpo: { "error": "<messaggio>" }
 */
session_start();
header('Content-Type: application/json');

// Verifica autenticazione
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Utente non autenticato.']);
    exit;
}

// Leggi e valida il payload JSON
$input = json_decode(file_get_contents('php://input'), true);

$data_visita = trim($input['data_visita'] ?? '');
$gatti_ids = $input['gatti_ids'] ?? [];

if (empty($data_visita) || !is_array($gatti_ids) || count($gatti_ids) === 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Dati mancanti: seleziona una data e almeno un gatto.']);
    exit;
}

require_once '../db.php';

try {
    $pdo = getDBConnection('modifier');
    $pdo->beginTransaction();

    // Inserisci la prenotazione della visita
    $stmt = $pdo->prepare("INSERT INTO prenotazioni_visite (utente_id, data_ora) VALUES (?, ?)");
    $stmt->execute([$_SESSION['user_id'], $data_visita]);
    $prenotazione_id = $pdo->lastInsertId();

    // Inserisci i gatti associati
    $stmt = $pdo->prepare("INSERT INTO visita_gatti (prenotazione_id, gatto_id) VALUES (?, ?)");
    foreach ($gatti_ids as $gatto_id) {
        $stmt->execute([$prenotazione_id, (int)$gatto_id]);
    }

    $pdo->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['error' => 'Errore durante il salvataggio: ' . $e->getMessage()]);
}
?>
