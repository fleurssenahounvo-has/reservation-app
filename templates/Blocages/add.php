<?php
/**
 * @var \App\View\AppView $this
 * @var array<int, string> $ressources
 * @var string|null $erreur
 */
$this->assign('title', 'Bloquer une période');
echo $this->Html->css('reservation', ['block' => true]);
?>
<div class="ressources">
    <p><?= $this->Html->link('◀ Fermetures', ['action' => 'gestion']) ?></p>
    <h1>Bloquer une période</h1>
    <p class="etab">Pour une fermeture, des travaux ou un événement privé. Pour fermer une journée entière, mettez 00:00 au début et 00:00 le lendemain.</p>

    <?php if ($erreur) : ?>
        <div class="alerte"><?= h($erreur) ?></div>
    <?php endif; ?>

    <?= $this->Form->create(null) ?>
    <?= $this->Form->control('ressource_id', ['label' => 'Ressource *', 'options' => $ressources, 'empty' => 'Choisir...', 'required' => true, 'value' => $this->request->getData('ressource_id')]) ?>
    <?= $this->Form->control('debut', ['type' => 'datetime-local', 'label' => 'Début *', 'required' => true, 'value' => $this->request->getData('debut')]) ?>
    <?= $this->Form->control('fin', ['type' => 'datetime-local', 'label' => 'Fin *', 'required' => true, 'value' => $this->request->getData('fin')]) ?>
    <?= $this->Form->control('motif', ['label' => 'Motif (facultatif)', 'maxlength' => 255, 'value' => $this->request->getData('motif')]) ?>
    <?= $this->Form->button('Bloquer la période') ?>
    <?= $this->Form->end() ?>
</div>
