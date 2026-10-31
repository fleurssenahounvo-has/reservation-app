<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Ressource $ressource
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Ressource'), ['action' => 'edit', $ressource->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Ressource'), ['action' => 'delete', $ressource->id], ['confirm' => __('Are you sure you want to delete # {0}?', $ressource->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Ressources'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Ressource'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="ressources view content">
            <h3><?= h($ressource->nom) ?></h3>
            <table>
                <tr>
                    <th><?= __('Etablissement') ?></th>
                    <td><?= $ressource->hasValue('etablissement') ? $this->Html->link($ressource->etablissement->nom, ['controller' => 'Etablissements', 'action' => 'view', $ressource->etablissement->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Nom') ?></th>
                    <td><?= h($ressource->nom) ?></td>
                </tr>
                <tr>
                    <th><?= __('Type') ?></th>
                    <td><?= h($ressource->type) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($ressource->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Capacite') ?></th>
                    <td><?= $this->Number->format($ressource->capacite) ?></td>
                </tr>
                <tr>
                    <th><?= __('Prix') ?></th>
                    <td><?= $ressource->prix === null ? '' : $this->Number->format($ressource->prix) ?></td>
                </tr>
                <tr>
                    <th><?= __('Created') ?></th>
                    <td><?= h($ressource->created) ?></td>
                </tr>
                <tr>
                    <th><?= __('Modified') ?></th>
                    <td><?= h($ressource->modified) ?></td>
                </tr>
            </table>
            <div class="related">
                <h4><?= __('Related Blocages') ?></h4>
                <?php if (!empty($ressource->blocages)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Debut') ?></th>
                            <th><?= __('Fin') ?></th>
                            <th><?= __('Motif') ?></th>
                            <th><?= __('Created') ?></th>
                            <th><?= __('Modified') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($ressource->blocages as $blocage) : ?>
                        <tr>
                            <td><?= h($blocage->id) ?></td>
                            <td><?= h($blocage->debut) ?></td>
                            <td><?= h($blocage->fin) ?></td>
                            <td><?= h($blocage->motif) ?></td>
                            <td><?= h($blocage->created) ?></td>
                            <td><?= h($blocage->modified) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Blocages', 'action' => 'view', $blocage->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Blocages', 'action' => 'edit', $blocage->id]) ?>
                                <?= $this->Form->postLink(
                                    __('Delete'),
                                    ['controller' => 'Blocages', 'action' => 'delete', $blocage->id],
                                    [
                                        'method' => 'delete',
                                        'confirm' => __('Are you sure you want to delete # {0}?', $blocage->id),
                                    ]
                                ) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Reservations') ?></h4>
                <?php if (!empty($ressource->reservations)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Utilisateur Id') ?></th>
                            <th><?= __('Nom Client') ?></th>
                            <th><?= __('Telephone Client') ?></th>
                            <th><?= __('Email Client') ?></th>
                            <th><?= __('Debut') ?></th>
                            <th><?= __('Fin') ?></th>
                            <th><?= __('Statut') ?></th>
                            <th><?= __('Source') ?></th>
                            <th><?= __('Created') ?></th>
                            <th><?= __('Modified') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($ressource->reservations as $reservation) : ?>
                        <tr>
                            <td><?= h($reservation->id) ?></td>
                            <td><?= h($reservation->utilisateur_id) ?></td>
                            <td><?= h($reservation->nom_client) ?></td>
                            <td><?= h($reservation->telephone_client) ?></td>
                            <td><?= h($reservation->email_client) ?></td>
                            <td><?= h($reservation->debut) ?></td>
                            <td><?= h($reservation->fin) ?></td>
                            <td><?= h($reservation->statut) ?></td>
                            <td><?= h($reservation->source) ?></td>
                            <td><?= h($reservation->created) ?></td>
                            <td><?= h($reservation->modified) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Reservations', 'action' => 'view', $reservation->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Reservations', 'action' => 'edit', $reservation->id]) ?>
                                <?= $this->Form->postLink(
                                    __('Delete'),
                                    ['controller' => 'Reservations', 'action' => 'delete', $reservation->id],
                                    [
                                        'method' => 'delete',
                                        'confirm' => __('Are you sure you want to delete # {0}?', $reservation->id),
                                    ]
                                ) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>