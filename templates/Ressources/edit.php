<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Ressource $ressource
 * @var string[]|\Cake\Collection\CollectionInterface $etablissements
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $ressource->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $ressource->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Ressources'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="ressources form content">
            <?= $this->Form->create($ressource) ?>
            <fieldset>
                <legend><?= __('Edit Ressource') ?></legend>
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
