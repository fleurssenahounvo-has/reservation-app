<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Ressource $ressource
 * @var \Cake\Collection\CollectionInterface|string[] $etablissements
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Ressources'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="ressources form content">
            <?= $this->Form->create($ressource) ?>
            <fieldset>
                <legend><?= __('Add Ressource') ?></legend>
                <?php
                    echo $this->Form->control('etablissement_id', ['options' => $etablissements]);
                    echo $this->Form->control('nom');
                    echo $this->Form->control('type');
                    echo $this->Form->control('capacite');
                    echo $this->Form->control('prix');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
