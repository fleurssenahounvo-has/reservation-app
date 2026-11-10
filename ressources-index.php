<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Ressource> $ressources
 */
$this->assign('title', 'Réservez une salle ou une table');
echo $this->Html->css('reservation', ['block' => true]);

$types = [
    'conference' => 'Salle de conférence',
    'reception' => 'Salle de réception',
    'table' => 'Table de restaurant',
];
$icones = [
    'conference' => '<svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="12" rx="2"/><path d="M8 20h8M12 16v4"/></svg>',
    'reception' => '<svg viewBox="0 0 24 24"><path d="M12 3l2.4 5.6 6 .6-4.5 4 1.3 5.9L12 16.1l-5.2 3 1.3-5.9-4.5-4 6-.6z"/></svg>',
    'table' => '<svg viewBox="0 0 24 24"><path d="M7 3v7M5 3v5a2 2 0 0 0 4 0V3M7 10v11M17 3c-2 1.5-3 4-3 7h3v11"/></svg>',
];
$filtreActif = !empty($this->request->getQuery('type')) || (int)$this->request->getQuery('capacite') > 0;
?>
<div class="accueil">
    <section class="hero">
        <div class="hero-texte">
            <h1>Réservez votre salle ou votre table sans vous déplacer</h1>
            <p>Voyez les créneaux libres avant de partir, puis réservez en quelques minutes. Aucun compte à créer.</p>
            <a class="bouton-jaune" href="#ressources">Voir les salles et les tables</a>
        </div>
        <div class="hero-demo" aria-hidden="true">
            <p class="titre">Salle de conférence</p>
            <p class="sous-titre">Jeudi</p>
            <div class="slot"><span>08:00 – 10:00</span><span>Libre</span></div>
            <div class="slot choisi"><span>10:00 – 12:00</span><span>Votre créneau</span></div>
            <div class="slot"><span>12:00 – 14:00</span><span>Libre</span></div>
            <div class="slot pris"><span>14:00 – 16:00</span><span>Réservé</span></div>
            <p class="legende">Exemple de calendrier</p>
        </div>
    </section>

    <section class="recherche" id="ressources" aria-label="Rechercher une ressource">
        <?= $this->Form->create(null, ['type' => 'get', 'valueSources' => ['query']]) ?>
        <div class="filtres">
            <?= $this->Form->control('type', ['label' => 'Type', 'options' => $types, 'empty' => 'Tous les types']) ?>
            <?= $this->Form->control('capacite', ['label' => 'Capacité minimale', 'type' => 'number', 'min' => 1]) ?>
            <?= $this->Form->button('Rechercher') ?>
        </div>
        <?= $this->Form->end() ?>
    </section>

    <?php if (count($ressources) > 0) : ?>
        <div class="liste">
            <?php foreach ($ressources as $ressource) : ?>
                <article class="ressource type-<?= h($ressource->type) ?>">
                    <div class="tuile" aria-hidden="true"><?= $icones[$ressource->type] ?? $icones['conference'] ?></div>
                    <div>
                        <h2><?= h($ressource->nom) ?></h2>
                        <p class="meta"><?= h($types[$ressource->type] ?? $ressource->type) ?>, <?= (int)$ressource->capacite ?> places</p>
                        <p class="etab"><?= h($ressource->etablissement->nom) ?></p>
                    </div>
                    <div class="prix">
                        <?php if ($ressource->prix !== null) : ?>
                            <strong><?= h(number_format((float)$ressource->prix, 0, ',', ' ')) ?> FCFA</strong>
                            <small>tarif</small>
                        <?php else : ?>
                            <strong>Sur demande</strong>
                            <small>tarif</small>
                        <?php endif; ?>
                    </div>
                    <div class="actions">
                        <?= $this->Html->link('Voir les disponibilités', ['action' => 'view', $ressource->id], ['class' => 'bouton']) ?>
                        <?= $this->Html->link('Réserver', ['controller' => 'Reservations', 'action' => 'add', $ressource->id], ['class' => 'bouton secondaire']) ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <div class="vide">
            <?php if ($filtreActif) : ?>
                <h2>Aucune ressource ne correspond</h2>
                <p>Essayez un autre type de lieu ou un nombre de places plus petit.</p>
                <?= $this->Html->link('Voir toutes les ressources', ['action' => 'index'], ['class' => 'bouton']) ?>
            <?php else : ?>
                <h2>Les premières salles et tables arrivent</h2>
                <p>Aucun établissement n'est encore en ligne. Vous gérez une salle ou un restaurant ? Inscrivez-le pour recevoir des réservations.</p>
                <?= $this->Html->link('Inscrire mon établissement', ['controller' => 'Etablissements', 'action' => 'inscription'], ['class' => 'bouton']) ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <ol class="etapes">
        <li>
            <h3>Choisissez</h3>
            <p>Filtrez par type de lieu et par nombre de places.</p>
        </li>
        <li>
            <h3>Vérifiez</h3>
            <p>Le calendrier montre les jours et les créneaux encore libres.</p>
        </li>
        <li>
            <h3>Réservez</h3>
            <p>Votre nom et votre téléphone suffisent. Pas de compte à créer.</p>
        </li>
    </ol>

    <div class="invitation">
        <div>
            <strong>Vous gérez une salle ou un restaurant ?</strong>
            <span>Chaque créneau n'est réservé qu'une fois, même si plusieurs personnes réservent au même instant.</span>
        </div>
        <?= $this->Html->link('Inscrire mon établissement', ['controller' => 'Etablissements', 'action' => 'inscription'], ['class' => 'bouton']) ?>
    </div>
</div>
