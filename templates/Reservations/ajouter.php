<?php
/**
 * @var \App\View\AppView $this
 * @var array<int, string> $ressources
 * @var string|null $erreur
 */
$this->assign('title', 'Enregistrer une réservation');
echo $this->Html->css('reservation', ['block' => true]);
?>
<div class="ressources">
    <p><?= $this->Html->link('◀ Mes réservations', ['action' => 'gestion']) ?></p>
    <h1>Enregistrer une réservation</h1>
    <p class="etab">Pour un client qui a appelé ou qui s'est présenté sur place. La réservation est confirmée tout de suite.</p>

    <?php if ($erreur) : ?>
        <div class="alerte"><?= h($erreur) ?></div>
    <?php endif; ?>

    <?= $this->Form->create(null) ?>
    <?= $this->Form->control('ressource_id', ['label' => 'Ressource *', 'options' => $ressources, 'empty' => 'Choisir...', 'required' => true, 'value' => $this->request->getData('ressource_id')]) ?>
    <?= $this->Form->control('debut', ['type' => 'datetime-local', 'label' => 'Début *', 'required' => true, 'value' => $this->request->getData('debut')]) ?>
    <?= $this->Form->control('fin', ['type' => 'datetime-local', 'label' => 'Fin *', 'required' => true, 'value' => $this->request->getData('fin')]) ?>
    <?= $this->Form->control('nom_client', ['label' => 'Nom du client *', 'required' => true, 'maxlength' => 100, 'value' => $this->request->getData('nom_client')]) ?>
    <?= $this->Form->control('telephone_client', ['label' => 'Téléphone (facultatif)', 'maxlength' => 30, 'value' => $this->request->getData('telephone_client')]) ?>
    <?= $this->Form->control('email_client', ['label' => 'E-mail (facultatif)', 'type' => 'email', 'maxlength' => 150, 'value' => $this->request->getData('email_client')]) ?>
    <?= $this->Form->control('source', ['label' => 'Prise de la réservation', 'options' => ['telephone' => 'Par téléphone', 'comptoir' => 'Au comptoir'], 'value' => $this->request->getData('source') ?? 'telephone']) ?>
    <?= $this->Form->button('Enregistrer la réservation') ?>
    <?= $this->Form->end() ?>
</div>