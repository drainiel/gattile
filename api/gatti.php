<?php
/**
 * API REST — Catalogo Gatti
 *
 * Endpoint: api/gatti.php
 *
 * Metodi supportati:
 *   GET — Restituisce l'elenco completo dei gatti presenti nel database.
 *
 * Formato risposta (GET — 200 OK):
 *   [
 *     {
 *       "id":        int,
 *       "nome":      string,
 *       "razza":     string,
 *       "eta":       int,
 *       "peso":      float,
 *       "sesso":     string   ("M" | "F"),
 *       "foto":      string   (path relativo all'immagine)
 *     },
 *     ...
 *   ]
 *
 * Codici di errore:
 *   500 Internal Server Error — Errore di connessione o query al database.
 *       Corpo: { "error": "<messaggio>" }
 */
require_once '../db.php';
header('Content-Type: application/json');

try {
    $pdo = getDBConnection('lecture');
    $stmt = $pdo->query("SELECT * FROM gatti");
    $gatti = $stmt->fetchAll();
    echo json_encode($gatti);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>