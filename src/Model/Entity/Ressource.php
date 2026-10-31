<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Ressource Entity
 *
 * @property int $id
 * @property int $etablissement_id
 * @property string $nom
 * @property string $type
 * @property int $capacite
 * @property string|null $prix
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 *
 * @property \App\Model\Entity\Etablissement $etablissement
 * @property \App\Model\Entity\Blocage[] $blocages
 * @property \App\Model\Entity\Reservation[] $reservations
 */
class Ressource extends Entity
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
        'etablissement_id' => true,
        'nom' => true,
        'type' => true,
        'capacite' => true,
        'prix' => true,
        'created' => true,
        'modified' => true,
        'etablissement' => true,
        'blocages' => true,
        'reservations' => true,
    ];
}
