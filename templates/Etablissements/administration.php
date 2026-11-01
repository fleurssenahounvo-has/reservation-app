<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Etablissement> $etablissements
 * @var array<int, string[]> $gestionnaires
 */
$this->assign('title', 'Administration');
echo $this->Html->css('reservation', ['block' => true]);
?>
<div class="ressources gestion">
    <h1>Administration : établissements</h1>

    <?php if (count($etablissements) === 0) : ?>
        <p>Aucun établissement.</p>
    <?php else : ?>
        <div class="tableau-defilant">
        <table class="table-gestion">
            <thead><tr><th>Établissement</th><th>Type</th><th>Contact</th><th>Gestionnaire</th><th>Statut</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($etablissements as $e) : ?>
                <tr>
                    <td><?= h($e->nom) ?><br><small><?= h($e->adresse ?? '') ?></small></td>
                    <td><?= h($e->type) ?></td>
                    <td><?= h($e->telephone ?? '-') ?></td>
                    <td><?= h(implode(', ', $gestionnaires[$e->id] ?? ['-'])) ?></td>
                    <td><span class="badge <?= $e->valide ? 'statut-confirmee' : 'statut-en_attente' ?>"><?= $e->valide ? 'Validé' : 'À valider' ?></span></td>
                    <td class="actions-ligne">
                        <?php if ($e->valide) : ?>
                            <?= $this->Form->postLink('Suspendre', ['action' => 'suspendre', $e->id], ['class' => 'bouton petit secondaire', 'confirm' => 'Suspendre « ' . $e->nom . ' » ?']) ?>
                        <?php else : ?>
                            <?= $this->Form->postLink('Valider', ['action' => 'valider', $e->id], ['class' => 'bouton petit']) ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    <?php endif; ?>
</div>