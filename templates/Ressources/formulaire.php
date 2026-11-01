<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Ressource $ressource
 * @var array<string, string> $types
 */
$this->assign('title', $ressource->isNew() ? 'Ajouter une ressource' : 'Modifier la ressource');
echo $this->Html->css('reservation', ['block' => true]);
?>
<div class="ressources">
    <p><?= $this->Html->link('◀ Mes ressources', ['action' => 'gestion']) ?></p>
    <h1><?= $ressource->isNew() ? 'Ajouter une ressource' : 'Modifier la ressource' ?></h1>

    <?= $this->Form->create($ressource) ?>
    <?= $this->Form->control('nom', ['label' => 'Nom *', 'maxlength' => 150, 'required' => true]) ?>
    <?= $this->Form->control('type', ['label' => 'Type *', 'options' => $types, 'empty' => 'Choisir...', 'required' => true]) ?>
    <?= $this->Form->control('capacite', ['label' => 'Capacité (nombre de places) *', 'type' => 'number', 'min' => 1, 'required' => true]) ?>
    <?= $this->Form->control('prix', ['label' => 'Tarif en FCFA (facultatif)', 'type' => 'number', 'min' => 0, 'step' => 1]) ?>
    <?= $this->Form->button('Enregistrer') ?>
    <?= $this->Form->end() ?>
</div>