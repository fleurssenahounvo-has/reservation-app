<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Ressource $ressource
 * @var array<int, string> $jours
 * @var \Cake\I18n\DateTime $premier
 * @var string $mois
 * @var string $precedent
 * @var string $suivant
 * @var \Cake\I18n\DateTime|null $jourChoisi
 * @var array $creneaux
 */
$this->assign('title', 'Disponibilités : ' . $ressource->nom);
echo $this->Html->css('reservation', ['block' => true]);
$moisNoms = ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
$jourNoms = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
$jourLongs = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'];
$etats = ['libre' => 'Libre', 'partiel' => 'Partiel', 'complet' => 'Complet', 'ferme' => 'Fermé', 'passe' => 'Passé'];
$decalage = (int)$premier->format('N') - 1;
$jourChoisiNum = $jourChoisi ? (int)$jourChoisi->format('j') : 0;
?>
<div class="ressources">
    <p><?= $this->Html->link('◀ Retour à la liste', ['action' => 'index']) ?></p>
    <h1><?= h($ressource->nom) ?> : disponibilités</h1>
    <p class="etab"><?= h($ressource->etablissement->nom) ?> · <?= (int)$ressource->capacite ?> places</p>

    <div class="calendrier-zone">
        <div class="calendrier">
            <div class="cal-entete">
                <?= $this->Html->link('◀', ['action' => 'view', $ressource->id, '?' => ['mois' => $precedent]]) ?>
                <strong><?= h($moisNoms[(int)$premier->format('n')] . ' ' . $premier->format('Y')) ?></strong>
                <?= $this->Html->link('▶', ['action' => 'view', $ressource->id, '?' => ['mois' => $suivant]]) ?>
            </div>
            <div class="cal-grille">
                <?php foreach ($jourNoms as $nom) : ?>
                    <div class="cal-nom"><?= h($nom) ?></div>
                <?php endforeach; ?>
                <?php for ($i = 0; $i < $decalage; $i++) : ?>
                    <div></div>
                <?php endfor; ?>
                <?php foreach ($jours as $numero => $statut) : ?>
                    <?php $classe = 'cal-jour etat-' . $statut . ($numero === $jourChoisiNum ? ' choisi' : ''); ?>
                    <?php if (in_array($statut, ['libre', 'partiel'], true)) : ?>
                        <?= $this->Html->link(
                            (string)$numero,
                            ['action' => 'view', $ressource->id, '?' => ['mois' => $mois, 'jour' => sprintf('%s-%02d', $mois, $numero)]],
                            ['class' => $classe],
                        ) ?>
                    <?php else : ?>
                        <span class="<?= h($classe) ?>"><?= $numero ?></span>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <div class="cal-legende">
                <?php foreach ($etats as $cle => $libelle) : ?>
                    <span><i class="pastille etat-<?= h($cle) ?>"></i><?= h($libelle) ?></span>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="creneaux">
            <?php if ($jourChoisi) : ?>
                <h2><?= h(ucfirst($jourLongs[(int)$jourChoisi->format('N') - 1]) . ' ' . $jourChoisi->format('d/m/Y')) ?></h2>
                <?php foreach ($creneaux as $c) : ?>
                    <div class="creneau <?= $c['libre'] ? 'libre' : 'occupe' ?>">
                        <span><?= h($c['debut']->format('H:i') . ' – ' . $c['fin']->format('H:i')) ?></span>
                        <?php if ($c['libre']) : ?>
                            <?= $this->Html->link('Réserver', [
                                'controller' => 'Reservations',
                                'action' => 'add',
                                $ressource->id,
                                '?' => [
                                    'debut' => $c['debut']->format('Y-m-d\TH:i'),
                                    'fin' => $c['fin']->format('Y-m-d\TH:i'),
                                ],
                            ], ['class' => 'bouton']) ?>
                        <?php else : ?>
                            <em>Indisponible</em>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <p class="etab">Choisissez un jour libre ou partiel dans le calendrier pour voir les créneaux.</p>
            <?php endif; ?>
            <p><?= $this->Html->link('Autre horaire', ['controller' => 'Reservations', 'action' => 'add', $ressource->id]) ?></p>
        </div>
    </div>
</div>