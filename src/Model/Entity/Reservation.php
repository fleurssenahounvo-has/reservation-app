<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Reservation Entity
 *
 * @property int $id
 * @property int $ressource_id
 * @property int|null $utilisateur_id
 * @property string|null $nom_client
 * @property string|null $telephone_client
 * @property string|null $email_client
 * @property \Cake\I18n\DateTime $debut
 * @property \Cake\I18n\DateTime $fin
 * @property string $statut
 * @property string $source
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 *
 * @property \App\Model\Entity\Ressource $ressource
 * @property \App\Model\Entity\Utilisateur $utilisateur
 */
class Reservation extends Entity
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
        'utilisateur_id' => true,
        'nom_client' => true,
        'telephone_client' => true,
        'email_client' => true,
        'debut' => true,
        'fin' => true,
        'statut' => true,
        'source' => true,
        'created' => true,
        'modified' => true,
        'ressource' => true,
        'utilisateur' => true,
    ];
}
