<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Authentication\PasswordHasher\DefaultPasswordHasher;
use Cake\ORM\Entity;

/**
 * Utilisateur Entity
 *
 * @property int $id
 * @property string $nom
 * @property string $email
 * @property string $mot_de_passe
 * @property string $role
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 * @property int|null $etablissement_id
 *
 * @property \App\Model\Entity\Reservation[] $reservations
 */
class Utilisateur extends Entity
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
        'email' => true,
        'mot_de_passe' => true,
        'role' => true,
        'created' => true,
        'modified' => true,
        'etablissement_id' => true,
        'reservations' => true,
    ];
        protected array $_hidden = ['mot_de_passe'];

    protected function _setMotDePasse(string $password): string
    {
        return $password !== '' ? (new DefaultPasswordHasher())->hash($password) : $password;
    }

}
