<?php
// home.php
require_once 'db.php';
require_once 'includes/header.php';

try {
    $pdo = getDBConnection('lecture');
    // Fetch ultimi 2 gatti arrivati
    $stmt = $pdo->query("SELECT * FROM gatti ORDER BY data_arrivo DESC LIMIT 2");
    $ultimi_gatti = $stmt->fetchAll();
} catch (Exception $e) {
    $errore_db = "Impossibile caricare i nuovi arrivi: " . $e->getMessage();
}
?>

<section class="mb-2">
    <h2 class="auth-title">Benvenuti al Gattile Sabaudo</h2>
    <p class="auth-subtitle">Ogni anno, centinaia di gatti vengono abbandonati o nascono in strada, necessitando di cure e di una famiglia. Allo stesso tempo, molte persone desiderano accogliere un felino o dedicare il proprio tempo come volontari. Questo sito nasce per facilitare le adozioni e organizzare il supporto attivo alla struttura ospitante.</p>
</section>

<section class="mt-2">
    <h2 class="auth-title">Nuovi Arrivi</h2>
    <?php if (isset($errore_db)): ?>
        <output class="alert alert-error"><?php echo htmlspecialchars($errore_db); ?></output>
    <?php elseif (empty($ultimi_gatti)): ?>
        <p>Nessun gatto presente in struttura al momento.</p>
    <?php else: ?>
        <ul class="cat-gallery">
            <?php foreach ($ultimi_gatti as $gatto): ?>
                <li>
                    <a href="gatti.php" style="text-decoration: none; color: inherit; display: block;">
                        <article class="cat-card">
                            <figure class="cat-img" style="background: url('data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'><text y=\'50\' x=\'50\' text-anchor=\'middle\' dominant-baseline=\'middle\' font-size=\'40\'>🐈</text></svg>') center/cover; background-color: #eee;"></figure>
                            <div class="cat-info">
                                <h3><?php echo htmlspecialchars($gatto['nome']); ?></h3>
                                <p><strong>Età:</strong> <?php echo htmlspecialchars($gatto['eta']); ?> mesi</p>
                                <p><strong>Sesso:</strong> <?php echo htmlspecialchars($gatto['sesso']); ?></p>
                                <p><strong>Arrivo:</strong> <?php echo date('d/m/Y', strtotime($gatto['data_arrivo'])); ?></p>
                            </div>
                        </article>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>

<?php
require_once 'includes/footer.php';
?>