<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Blocage> $blocages
 */
$this->assign('title', 'Fermetures');
echo $this->Html->css('reservation', ['block' => true]);
?>
<div class="ressources gestion">
    <h1>Fermetures et indisponibilités</h1>
    <p><?= $this->Html->link('Bloquer une période', ['action' => 'add'], ['class' => 'bouton']) ?></p>

    <?php if (count($blocages) === 0) : ?>
        <p>Aucune période bloquée.</p>
    <?php else : ?>
        <div class="tableau-defilant">
        <table class="table-gestion">
            <thead><tr><th>Ressource</th><th>Début</th><th>Fin</th><th>Motif</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($blocages as $b) : ?>
                <tr>
                    <td><?= h($b->ressource->nom) ?></td>
                    <td><?= h($b->debut->format('d/m/Y H:i')) ?></td>
                    <td><?= h($b->fin->format('d/m/Y H:i')) ?></td>
                    <td><?= h($b->motif ?? '-') ?></td>
                    <td class="actions-ligne">
                        <?= $this->Form->postLink('Supprimer', ['action' => 'delete', $b->id], [
                            'class' => 'bouton petit secondaire',
                            'confirm' => 'Supprimer ce blocage ?',
                        ]) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    <?php endif; ?>
</div>