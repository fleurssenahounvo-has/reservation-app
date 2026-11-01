<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Ressource> $ressources
 */
$this->assign('title', 'Salles et restaurants disponibles');
echo $this->Html->css('reservation', ['block' => true]);
$types = [
    'conference' => 'Salle de conférence',
    'reception' => 'Salle de réception',
    'table' => 'Table de restaurant',
];
?>
<div class="ressources">
    <h1>Salles et restaurants disponibles</h1>

    <?= $this->Form->create(null, ['type' => 'get', 'valueSources' => ['query']]) ?>
    <div class="filtres">
        <?= $this->Form->control('type', ['label' => 'Type', 'options' => $types, 'empty' => 'Tous les types']) ?>
        <?= $this->Form->control('capacite', ['label' => 'Capacité minimale', 'type' => 'number', 'min' => 1]) ?>
        <?= $this->Form->button('Rechercher') ?>
    </div>
    <?= $this->Form->end() ?>

    <?php foreach ($ressources as $ressource) : ?>
        <article class="carte">
            <div class="infos">
                <h2><?= h($ressource->nom) ?></h2>
                <p><?= h($types[$ressource->type] ?? $ressource->type) ?> · <?= (int)$ressource->capacite ?> places</p>
                <p class="etab"><?= h($ressource->etablissement->nom) ?></p>
                <?php if ($ressource->prix !== null) : ?>
                    <p>Tarif : <?= h(number_format((float)$ressource->prix, 0, ',', ' ')) ?> FCFA</p>
                <?php endif; ?>
            </div>
            <div class="actions">
                <?= $this->Html->link('Voir les disponibilités', ['action' => 'view', $ressource->id], ['class' => 'bouton']) ?>
                <?= $this->Html->link('Réserver', ['controller' => 'Reservations', 'action' => 'add', $ressource->id], ['class' => 'bouton secondaire']) ?>
            </div> 
        </article>
    <?php endforeach; ?>

    <?php if (count($ressources) === 0) : ?>
        <p>Aucune ressource ne correspond à votre recherche.</p>
    <?php endif; ?>
</div>