<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Blocages Model
 *
 * @property \App\Model\Table\RessourcesTable&\Cake\ORM\Association\BelongsTo $Ressources
 *
 * @method \App\Model\Entity\Blocage newEmptyEntity()
 * @method \App\Model\Entity\Blocage newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Blocage> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Blocage get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Blocage findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Blocage patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Blocage> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Blocage|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Blocage saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Blocage>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Blocage>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Blocage>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Blocage> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Blocage>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Blocage>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Blocage>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Blocage> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class BlocagesTable extends Table
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

        $this->setTable('blocages');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Ressources', [
            'foreignKey' => 'ressource_id',
            'joinType' => 'INNER',
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
            ->integer('ressource_id')
            ->notEmptyString('ressource_id');

        $validator
            ->dateTime('debut')
            ->requirePresence('debut', 'create')
            ->notEmptyDateTime('debut');

        $validator
            ->dateTime('fin')
            ->requirePresence('fin', 'create')
            ->notEmptyDateTime('fin');

        $validator
            ->scalar('motif')
            ->maxLength('motif', 255)
            ->allowEmptyString('motif');

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
        $rules->add($rules->existsIn(['ressource_id'], 'Ressources'), ['errorField' => 'ressource_id']);

        return $rules;
    }
}
