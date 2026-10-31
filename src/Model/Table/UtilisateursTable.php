<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Utilisateurs Model
 *
 * @property \App\Model\Table\ReservationsTable&\Cake\ORM\Association\HasMany $Reservations
 *
 * @method \App\Model\Entity\Utilisateur newEmptyEntity()
 * @method \App\Model\Entity\Utilisateur newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Utilisateur> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Utilisateur get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Utilisateur findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Utilisateur patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Utilisateur> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Utilisateur|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Utilisateur saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Utilisateur>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Utilisateur>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Utilisateur>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Utilisateur> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Utilisateur>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Utilisateur>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Utilisateur>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Utilisateur> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UtilisateursTable extends Table
{
    /**
     * Initialize method
     *
     * @param array<string, mixed> $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('utilisateurs');
        $this->setDisplayField('nom');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Reservations', [
            'foreignKey' => 'utilisateur_id',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('nom')
            ->maxLength('nom', 100)
            ->requirePresence('nom', 'create')
            ->notEmptyString('nom');

        $validator
            ->email('email')
            ->requirePresence('email', 'create')
            ->notEmptyString('email')
            ->add('email', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('mot_de_passe')
            ->maxLength('mot_de_passe', 255)
            ->requirePresence('mot_de_passe', 'create')
            ->notEmptyString('mot_de_passe');

        $validator
            ->scalar('role')
            ->maxLength('role', 20)
            ->notEmptyString('role');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['email']), ['errorField' => 'email']);

        return $rules;
    }
}
