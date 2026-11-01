<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Ressource> $ressources
 * @var array<string, string> $types
 */
$this->assign('title', 'Mes ressources');
echo $this->Html->css('reservation', ['block' => true]);
?>
<div class="ressources gestion">
    <h1>Mes ressources</h1>
    <p><?= $this->Html->link('Ajouter une ressource', ['action' => 'add'], ['class' => 'bouton']) ?></p>

    <?php if (count($ressources) === 0) : ?>
        <p>Aucune ressource pour le moment.</p>
    <?php else : ?>
        <div class="tableau-defilant">
        <table class="table-gestion">
            <thead>
                <tr><th>Nom</th><th>Type</th><th>Capacité</th><th>Tarif</th><th>Actions</th></tr>
            </thead>
            <tbody>
            <?php foreach ($ressources as $r) : ?>
                <tr>
                    <td><?= h($r->nom) ?></td>
                    <td><?= h($types[$r->type] ?? $r->type) ?></td>
                    <td><?= (int)$r->capacite ?></td>
                    <td><?= $r->prix !== null ? h(number_format((float)$r->prix, 0, ',', ' ')) . ' FCFA' : '-' ?></td>
                    <td class="actions-ligne">
                        <?= $this->Html->link('Calendrier', ['action' => 'view', $r->id], ['class' => 'bouton petit secondaire']) ?>
                        <?= $this->Html->link('Modifier', ['action' => 'edit', $r->id], ['class' => 'bouton petit']) ?>
                        <?= $this->Form->postLink('Supprimer', ['action' => 'delete', $r->id], [
                            'class' => 'bouton petit secondaire',
                            'confirm' => 'Supprimer « ' . $r->nom . ' » ?',
                        ]) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    <?php endif; ?>
</div>