<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Blocage Entity
 *
 * @property int $id
 * @property int $ressource_id
 * @property \Cake\I18n\DateTime $debut
 * @property \Cake\I18n\DateTime $fin
 * @property string|null $motif
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 *
 * @property \App\Model\Entity\Ressource $ressource
 */
class Blocage extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'ressource_id' => true,
        'debut' => true,
        'fin' => true,
        'motif' => true,
        'created' => true,
        'modified' => true,
        'ressource' => true,
    ];
}
