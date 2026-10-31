<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Reservation $reservation
 */
$this->assign('title', 'Réservation enregistrée');
echo $this->Html->css('reservation', ['block' => true]);
?>
<div class="ressources">
    <h1>Réservation enregistrée</h1>
    <div class="alerte ok">
        Votre réservation n° <?= (int)$reservation->id ?> est en attente de confirmation.
    </div>
    <p><strong>Ressource :</strong> <?= h($reservation->ressource->nom) ?> (<?= h($reservation->ressource->etablissement->nom) ?>)</p>
    <p><strong>Début :</strong> <?= h($reservation->debut->format('d/m/Y H:i')) ?></p>
    <p><strong>Fin :</strong> <?= h($reservation->fin->format('d/m/Y H:i')) ?></p>
    <p><?= $this->Html->link('Retour à la liste', ['controller' => 'Ressources', 'action' => 'index'], ['class' => 'bouton']) ?></p>
</div>