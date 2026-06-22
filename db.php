<?php
// db.php
// Funzione per connettersi al database con privilegi specifici

function getDBConnection($role = 'lecture') {
    $host = 'localhost';
    $db   = 'gattile_db';
    $charset = 'utf8mb4';
    
    // Configurazione degli utenti come da requisiti
    $credentials = [
        'lecture' => [
            'user' => 'lecture',
            'pass' => 'P@ssw0rd!'
        ],
        'modifier' => [
            'user' => 'modifier',
            'pass' => 'Str0ng#Admin9'
        ],
        'registrator' => [
            'user' => 'registrator',
            'pass' => 'ToB31nsert?'
        ]
    ];
    
    if (!isset($credentials[$role])) {
        throw new Exception("Ruolo non valido");
    }
    
    $user = $credentials[$role]['user'];
    $pass = $credentials[$role]['pass'];
    
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    try {
        return new PDO($dsn, $user, $pass, $options);
    } catch (\PDOException $e) {
        throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }
}
?>
