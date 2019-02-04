<?php

namespace App\Model\Table;

use App\Lib\Traits\Cake2ResultTableTrait;
use App\Lib\Traits\PaginationAndScrollIndexTrait;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use itnovum\openITCOCKPIT\Database\PaginateOMat;
use itnovum\openITCOCKPIT\Filter\TimeperiodsFilter;

/**
 * Timeperiods Model
 *
 * @property \App\Model\Table\ContainersTable|\Cake\ORM\Association\BelongsTo $Containers
 * @property \App\Model\Table\CalendarsTable|\Cake\ORM\Association\BelongsTo $Calendars
 * @property \App\Model\Table\AutoreportsTable|\Cake\ORM\Association\HasMany $Autoreports
 * @property \App\Model\Table\HostdependenciesTable|\Cake\ORM\Association\HasMany $Hostdependencies
 * @property \App\Model\Table\HostescalationsTable|\Cake\ORM\Association\HasMany $Hostescalations
 * @property \App\Model\Table\HostsTable|\Cake\ORM\Association\HasMany $Hosts
 * @property \App\Model\Table\HosttemplatesTable|\Cake\ORM\Association\HasMany $Hosttemplates
 * @property \App\Model\Table\InstantreportsTable|\Cake\ORM\Association\HasMany $Instantreports
 * @property \App\Model\Table\NagiosTimeperiodTimerangesTable|\Cake\ORM\Association\HasMany $NagiosTimeperiodTimeranges
 * @property \App\Model\Table\NagiosTimeperiodsTable|\Cake\ORM\Association\HasMany $NagiosTimeperiods
 * @property \App\Model\Table\ServicedependenciesTable|\Cake\ORM\Association\HasMany $Servicedependencies
 * @property \App\Model\Table\ServiceescalationsTable|\Cake\ORM\Association\HasMany $Serviceescalations
 * @property \App\Model\Table\ServicetemplatesTable|\Cake\ORM\Association\HasMany $Servicetemplates
 * @property \App\Model\Table\TimeperiodTimerangesTable|\Cake\ORM\Association\HasMany $TimeperiodTimeranges
 *
 * @method \App\Model\Entity\Timeperiod get($primaryKey, $options = [])
 * @method \App\Model\Entity\Timeperiod newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Timeperiod[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Timeperiod|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Timeperiod|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Timeperiod patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Timeperiod[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Timeperiod findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class TimeperiodsTable extends Table {

    use Cake2ResultTableTrait;
    use PaginationAndScrollIndexTrait;

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config) {
        parent::initialize($config);

        $this->setTable('timeperiods');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Containers', [
            'foreignKey' => 'container_id',
            'joinType'   => 'INNER'
        ]);

        $this->hasMany('TimeperiodTimeranges', [
            'foreignKey' => 'timeperiod_id'
        ])->setDependent(true);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator) {
        $validator
            ->integer('id')
            ->allowEmptyString('id', 'create');

        $validator
            ->integer('container_id')
            ->allowEmptyString('container_id');

        $validator
            ->scalar('uuid')
            ->maxLength('uuid', 37)
            ->requirePresence('uuid', 'create')
            ->allowEmptyString('uuid', false);

        $validator
            ->scalar('name')
            ->maxLength('name', 255)
            ->requirePresence('name', 'create')
            ->allowEmptyString('name', false)
            ->add('name', 'unique', [
                'rule'     => 'validateUnique',
                'provider' => 'table',
                'message'  => __('This timeperiod name has already been taken.')
            ]);

        $validator
            ->scalar('description')
            ->maxLength('description', 255)
            ->allowEmptyString('description');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules) {
        $rules->add($rules->isUnique(['name']));
        $rules->add($rules->existsIn(['container_id'], 'Containers'));

        return $rules;
    }

    /**
     * @param array $MY_RIGHTS
     * @return array
     */
    public function getAllTimeperiodsAsCake2($MY_RIGHTS) {
        $query = $this->find()
            ->where([
                'Timeperiods.container_id IN' => $MY_RIGHTS
            ])
            ->order(['Timeperiods.name' => 'asc'])
            ->disableHydration()
            ->all();
        return $this->formatResultAsCake2($query->toArray(), false);
    }

    /**
     * @param TimeperiodsFilter $TimeperiodsFilter
     * @param null|PaginateOMat $PaginateOMat
     * @return array
     */
    public function getTimeperiodsIndex(TimeperiodsFilter $TimeperiodsFilter, $PaginateOMat = null) {
        $query = $this->find('all')->disableHydration();
        $query->where($TimeperiodsFilter->indexFilter());
        $query->order($TimeperiodsFilter->getOrderForPaginator('Timeperiods.name', 'asc'));

        $result = [];
        if ($PaginateOMat === null) {
            //Just execute query
            $result = $this->formatResultAsCake2($query->toArray(), false);
        } else {
            if ($PaginateOMat->useScroll()) {
                $result = $this->scroll($query, $PaginateOMat->getHandler(), false);
            } else {
                $result = $this->paginate($query, $PaginateOMat->getHandler(), false);
            }
        }

        return $result;
    }

    public function getTimeperiodById($id) {
        $query = $this->find()
            ->where([
                'Timeperiods.id' => $id
            ])
            ->first();
        return $this->formatFirstResultAsCake2($query->toArray(), false);
    }
}
