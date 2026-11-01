<?php
/**
 * @var \App\View\AppView $this
 */
$this->assign('title', 'Connexion');
echo $this->Html->css('reservation', ['block' => true]);
?>
<div class="ressources">
    <h1>Connexion gestionnaire</h1>
    <?= $this->Form->create(null) ?>
    <?= $this->Form->control('email', ['label' => 'E-mail', 'type' => 'email', 'required' => true]) ?>
    <?= $this->Form->control('mot_de_passe', ['label' => 'Mot de passe', 'type' => 'password', 'required' => true]) ?>
    <?= $this->Form->button('Se connecter') ?>
    <?= $this->Form->end() ?>
</div>