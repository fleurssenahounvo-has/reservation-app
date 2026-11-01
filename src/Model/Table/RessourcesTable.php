<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Ressources Model
 *
 * @property \App\Model\Table\EtablissementsTable&\Cake\ORM\Association\BelongsTo $Etablissements
 * @property \App\Model\Table\BlocagesTable&\Cake\ORM\Association\HasMany $Blocages
 * @property \App\Model\Table\ReservationsTable&\Cake\ORM\Association\HasMany $Reservations
 *
 * @method \App\Model\Entity\Ressource newEmptyEntity()
 * @method \App\Model\Entity\Ressource newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Ressource> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Ressource get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Ressource findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Ressource patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Ressource> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Ressource|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Ressource saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Ressource>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Ressource>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Ressource>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Ressource> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Ressource>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Ressource>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Ressource>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Ressource> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class RessourcesTable extends Table
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

        $this->setTable('ressources');
        $this->setDisplayField('nom');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Etablissements', [
            'foreignKey' => 'etablissement_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Blocages', [
            'foreignKey' => 'ressource_id',
        ]);
        $this->hasMany('Reservations', [
            'foreignKey' => 'ressource_id',
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
            ->integer('etablissement_id')
            ->requirePresence('etablissement_id', 'create')
            ->notEmptyString('etablissement_id');

        $validator
            ->scalar('nom')
            ->maxLength('nom', 150)
            ->requirePresence('nom', 'create')
            ->notEmptyString('nom');

        $validator
            ->scalar('type')
            ->maxLength('type', 30)
            ->requirePresence('type', 'create')
            ->notEmptyString('type');

        $validator
            ->integer('capacite')
            ->requirePresence('capacite', 'create')
            ->notEmptyString('capacite');

        $validator
            ->decimal('prix')
            ->allowEmptyString('prix');

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
        $rules->add($rules->existsIn(['etablissement_id'], 'Etablissements'), ['errorField' => 'etablissement_id']);

        return $rules;
    }
}
