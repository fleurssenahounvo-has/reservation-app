<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Etablissements Model
 *
 * @property \App\Model\Table\RessourcesTable&\Cake\ORM\Association\HasMany $Ressources
 *
 * @method \App\Model\Entity\Etablissement newEmptyEntity()
 * @method \App\Model\Entity\Etablissement newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Etablissement> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Etablissement get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Etablissement findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Etablissement patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Etablissement> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Etablissement|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Etablissement saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Etablissement>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Etablissement>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Etablissement>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Etablissement> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Etablissement>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Etablissement>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Etablissement>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Etablissement> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class EtablissementsTable extends Table
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

        $this->setTable('etablissements');
        $this->setDisplayField('nom');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Ressources', [
            'foreignKey' => 'etablissement_id',
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
            ->maxLength('nom', 150)
            ->requirePresence('nom', 'create')
            ->notEmptyString('nom');

        $validator
            ->scalar('type')
            ->maxLength('type', 30)
            ->requirePresence('type', 'create')
            ->notEmptyString('type');

        $validator
            ->scalar('adresse')
            ->maxLength('adresse', 255)
            ->allowEmptyString('adresse', 'create')
            ->notEmptyString('adresse');

        $validator
            ->scalar('telephone')
            ->maxLength('telephone', 30)
            ->allowEmptyString('telephone', 'create')
            ->notEmptyString('telephone');

        return $validator;
    }
}
