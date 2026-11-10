<?php
/**
 * @var \App\View\AppView $this
 */
$cakeDescription = 'Réservation en ligne';
$identite = $this->request->getAttribute('identity');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        <?= $cakeDescription ?> :
        <?= $this->fetch('title') ?>
    </title>
    <link rel="icon" type="image/svg+xml" href="<?= $this->Url->image('favicon.svg') ?>">

    <?= $this->Html->css(['normalize.min', 'milligram.min', 'fonts', 'cake']) ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->Html->css('theme') ?>
    <?= $this->fetch('script') ?>
</head>
<body>
    <nav class="top-nav">
        <a class="marque" href="<?= $this->Url->build('/') ?>">
            <img src="<?= $this->Url->image('logo.svg') ?>" width="36" height="36" alt="">
            <span>Réservation en ligne</span>
        </a>
        <div class="top-nav-links">
            <?php if ($identite && $identite->get('role') === 'administrateur') : ?>
                <?= $this->Html->link('Établissements', ['controller' => 'Etablissements', 'action' => 'administration']) ?>
                <?= $this->Html->link('Déconnexion', ['controller' => 'Utilisateurs', 'action' => 'logout']) ?>
            <?php elseif ($identite) : ?>
                <?= $this->Html->link('Mes ressources', ['controller' => 'Ressources', 'action' => 'gestion']) ?>
                <?= $this->Html->link('Fermetures', ['controller' => 'Blocages', 'action' => 'gestion']) ?>
                <?= $this->Html->link('Mes réservations', ['controller' => 'Reservations', 'action' => 'gestion']) ?>
                <?= $this->Html->link('Déconnexion', ['controller' => 'Utilisateurs', 'action' => 'logout']) ?>
            <?php else : ?>
                <?= $this->Html->link('Inscrire mon établissement', ['controller' => 'Etablissements', 'action' => 'inscription']) ?>
                <?= $this->Html->link('Espace gestionnaire', ['controller' => 'Utilisateurs', 'action' => 'login']) ?>
            <?php endif; ?>
        </div>
    </nav>
    <main class="main">
        <div class="container">
            <?= $this->Flash->render() ?>
            <?= $this->fetch('content') ?>
        </div>
    </main>
    <footer class="pied">
        <div class="contenu">
            <div><strong>Réservation en ligne</strong> : salles de conférence, salles de réception et restaurants.</div>
            <div><?= $this->Html->link('Inscrire mon établissement', ['controller' => 'Etablissements', 'action' => 'inscription']) ?></div>
        </div>
    </footer>
</body>
</html>