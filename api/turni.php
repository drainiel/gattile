<?php
// api/turni.php
require_once '../db.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

try {
    $pdo = getDBConnection('modifier');

    if ($method === 'GET') {
        // Ritorna le prenotazioni correnti aggregate per fascia oraria
        $stmt = $pdo->query("SELECT fascia_oraria, COUNT(*) as iscritti FROM turni_volontariato GROUP BY fascia_oraria");
        $turni = $stmt->fetchAll();
        echo json_encode($turni);
    } elseif ($method === 'POST') {
        // Ricevi json input
        $input = json_decode(file_get_contents('php://input'), true);
        $utente_id = $input['utente_id'] ?? null;
        $fascia_oraria = $input['fascia_oraria'] ?? null;

        if (!$utente_id || !$fascia_oraria) {
            http_response_code(400);
            echo json_encode(['error' => 'Dati mancanti.']);
            exit;
        }

        // Controllo lato server: max 2 volontari per fascia
        $stmt = $pdo->prepare("SELECT COUNT(*) as iscritti FROM turni_volontariato WHERE fascia_oraria = ?");
        $stmt->execute([$fascia_oraria]);
        $result = $stmt->fetch();

        if ($result && $result['iscritti'] >= 2) {
            http_response_code(409); // Conflict
            echo json_encode(['error' => 'La fascia oraria selezionata ha già raggiunto il limite massimo di volontari (2).']);
            exit;
        }

        // Inserimento
        $stmt = $pdo->prepare("INSERT INTO turni_volontariato (utente_id, fascia_oraria) VALUES (?, ?)");
        $stmt->execute([$utente_id, $fascia_oraria]);
        
        echo json_encode(['success' => true]);
    }
} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        http_response_code(409);
        echo json_encode(['error' => 'Hai già prenotato questo turno.']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
