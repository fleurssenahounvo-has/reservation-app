<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 * @var \App\View\AppView $this
 */

$cakeDescription = 'CakePHP: the rapid development php framework';
?>
<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        <?= $cakeDescription ?>:
        <?= $this->fetch('title') ?>
    </title>
    <?= $this->Html->meta('icon') ?>

    <?= $this->Html->css(['normalize.min', 'milligram.min', 'fonts', 'cake']) ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>
<body>
<nav class="top-nav">
    <div class="top-nav-title">
        <a href="<?= $this->Url->build('/') ?>"><span>Réservation</span> en ligne</a>
    </div>
    <div class="top-nav-links">
    <?php $identite = $this->request->getAttribute('identity'); ?>
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
    <footer>
    </footer>
</body>
</html>
