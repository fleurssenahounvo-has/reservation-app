<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Reservation> $reservations
 * @var string $statut
 */
$this->assign('title', 'Mes réservations');
echo $this->Html->css('reservation', ['block' => true]);
$libelles = ['en_attente' => 'En attente', 'confirmee' => 'Confirmée', 'annulee' => 'Annulée', 'expiree' => 'Expirée'];
$filtres = ['' => 'Toutes'] + $libelles;
$sources = ['en_ligne' => 'En ligne', 'telephone' => 'Téléphone', 'comptoir' => 'Comptoir'];
?>
<div class="ressources gestion">
    <h1>Mes réservations</h1>

    <p class="filtres-statut">
        <?php foreach ($filtres as $cle => $libelle) : ?>
            <?= $this->Html->link(
                $libelle,
                ['action' => 'gestion', '?' => $cle === '' ? [] : ['statut' => $cle]],
                ['class' => 'filtre' . ($statut === $cle ? ' actif' : '')],
            ) ?>
        <?php endforeach; ?>
    </p>

    <?php if (count($reservations) === 0) : ?>
        <p>Aucune réservation.</p>
    <?php else : ?>
        <div class="tableau-defilant">
        <table class="table-gestion">
            <thead>
                <tr><th>N°</th><th>Ressource</th><th>Client</th><th>Début</th><th>Fin</th><th>Statut</th><th>Source</th><th>Actions</th></tr>
            </thead>
            <tbody>
            <?php foreach ($reservations as $r) : ?>
                <tr>
                    <td><?= (int)$r->id ?></td>
                    <td><?= h($r->ressource->nom) ?></td>
                    <td>
                        <?= h($r->nom_client ?? '') ?><br>
                        <small><?= h($r->telephone_client ?? '') ?></small>
                    </td>
                    <td><?= h($r->debut->format('d/m/Y H:i')) ?></td>
                    <td><?= h($r->fin->format('d/m/Y H:i')) ?></td>
                    <td><span class="badge statut-<?= h($r->statut) ?>"><?= h($libelles[$r->statut] ?? $r->statut) ?></span></td>
                    <td><?= h($sources[$r->source] ?? $r->source) ?></td>
                    <td class="actions-ligne">
                        <?php if ($r->statut === 'en_attente') : ?>
                            <?= $this->Form->postLink('Confirmer', ['action' => 'confirmer', $r->id], ['class' => 'bouton petit']) ?>
                        <?php endif; ?>
                        <?php if (in_array($r->statut, ['en_attente', 'confirmee'], true)) : ?>
                            <?= $this->Form->postLink('Annuler', ['action' => 'annuler', $r->id], [
                                'class' => 'bouton petit secondaire',
                                'confirm' => 'Annuler la réservation n° ' . $r->id . ' ?',
                            ]) ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <ul class="pagination">
            <?= $this->Paginator->prev('◀') ?>
            <?= $this->Paginator->next('▶') ?>
        </ul>
        <p><?= $this->Paginator->counter('Page {{page}} sur {{pages}} ({{count}} réservations)') ?></p>
    <?php endif; ?>
</div>