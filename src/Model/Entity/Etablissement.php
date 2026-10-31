<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Etablissement Entity
 *
 * @property int $id
 * @property string $nom
 * @property string $type
 * @property string $adresse
 * @property string $telephone
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 *
 * @property \App\Model\Entity\Ressource[] $ressources
 */
class Etablissement extends Entity
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
        'nom' => true,
        'type' => true,
        'adresse' => true,
        'telephone' => true,
        'created' => true,
        'modified' => true,
        'ressources' => true,
    ];
}
