<?php
/**
 * @var \App\View\AppView $this
 * @var string[] $erreurs
 * @var array $donnees
 */
$this->assign('title', 'Inscrire mon établissement');
echo $this->Html->css('reservation', ['block' => true]);
?>
<div class="ressources">
    <h1>Inscrire mon établissement</h1>
    <p class="etab">Après l'inscription, l'administrateur vérifie votre établissement avant de le rendre visible aux clients.</p>

    <?php foreach ($erreurs as $erreur) : ?>
        <div class="alerte"><?= h($erreur) ?></div>
    <?php endforeach; ?>

    <?= $this->Form->create(null) ?>
    <h2>Votre établissement</h2>
    <?= $this->Form->control('nom', ['label' => "Nom de l'établissement *", 'maxlength' => 150, 'required' => true, 'value' => $donnees['nom'] ?? '']) ?>
    <?= $this->Form->control('type', ['label' => 'Type *', 'options' => ['salle' => 'Salles (conférence, réception)', 'restaurant' => 'Restaurant', 'hotel' => 'Hôtel ou auberge'], 'empty' => 'Choisir...', 'required' => true, 'value' => $donnees['type'] ?? '']) ?>
    <?= $this->Form->control('adresse', ['label' => 'Adresse (facultatif)', 'maxlength' => 255, 'value' => $donnees['adresse'] ?? '']) ?>
    <?= $this->Form->control('telephone', ['label' => 'Téléphone (facultatif)', 'maxlength' => 30, 'value' => $donnees['telephone'] ?? '']) ?>

    <h2>Votre compte gestionnaire</h2>
    <?= $this->Form->control('nom_gestionnaire', ['label' => 'Votre nom *', 'maxlength' => 100, 'required' => true, 'value' => $donnees['nom_gestionnaire'] ?? '']) ?>
    <?= $this->Form->control('email', ['label' => 'E-mail *', 'type' => 'email', 'maxlength' => 150, 'required' => true, 'value' => $donnees['email'] ?? '']) ?>
    <?= $this->Form->control('mot_de_passe', ['label' => 'Mot de passe (8 caractères minimum) *', 'type' => 'password', 'required' => true]) ?>
    <?= $this->Form->control('confirmation', ['label' => 'Confirmer le mot de passe *', 'type' => 'password', 'required' => true]) ?>
    <?= $this->Form->button("Inscrire l'établissement") ?>
    <?= $this->Form->end() ?>
</div>