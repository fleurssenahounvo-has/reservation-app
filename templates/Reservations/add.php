<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Ressource $ressource
 * @var string|null $erreur
 */
$this->assign('title', 'Réserver : ' . $ressource->nom);
echo $this->Html->css('reservation', ['block' => true]);
?>
<div class="ressources">
    <p><?= $this->Html->link('◀ Retour à la liste', ['controller' => 'Ressources', 'action' => 'index']) ?></p>
    <h1>Réserver : <?= h($ressource->nom) ?></h1>
    <p class="etab"><?= h($ressource->etablissement->nom) ?> · <?= (int)$ressource->capacite ?> places</p>

    <?php if ($erreur) : ?>
        <div class="alerte"><?= h($erreur) ?></div>
    <?php endif; ?>

    <?= $this->Form->create(null) ?>
        <?= $this->Form->control('debut', ['type' => 'datetime-local', 'label' => 'Début *', 'required' => true, 'value' => $this->request->getData('debut') ?? $this->request->getQuery('debut')]) ?>
    <?= $this->Form->control('fin', ['type' => 'datetime-local', 'label' => 'Fin *', 'required' => true, 'value' => $this->request->getData('fin') ?? $this->request->getQuery('fin')]) ?>
    <?= $this->Form->control('nom_client', ['label' => 'Nom complet *', 'required' => true, 'maxlength' => 100, 'value' => $this->request->getData('nom_client')]) ?>
    <?= $this->Form->control('telephone_client', ['label' => 'Téléphone *', 'required' => true, 'maxlength' => 30, 'value' => $this->request->getData('telephone_client')]) ?>
    <?= $this->Form->control('email_client', ['label' => 'E-mail (facultatif)', 'type' => 'email', 'maxlength' => 150, 'value' => $this->request->getData('email_client')]) ?>
    <?= $this->Form->button('Confirmer la réservation') ?>
    <?= $this->Form->end() ?>
    <p class="etab">Aucun compte n'est nécessaire.</p>
</div>