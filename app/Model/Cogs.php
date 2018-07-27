<?php
class Cogs extends AppModel {
    public $actsAs = array('Tree');
	var $name = 'Cogs';
	var $validate = array(
		'code' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Kode Cost Center harap diisi'
            ),
		),
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Nama Cost Center harap diisi'
            ),
        ),
	);

    var $belongsTo = array(
        'ParentCogs' => array(
            'className' => 'Cogs',
            'foreignKey' => 'parent_id',
            'conditions' => array(
                'ParentCogs.status' => 1,
            ),
            'fields' => '',
            'order' => ''
        )
    );

    var $hasMany = array(
        'ChildCogs' => array(
            'className' => 'Cogs',
            'foreignKey' => 'parent_id',
            'dependent' => false,
            'conditions' => array(
                'ChildCogs.status' => 1,
            ),
            'fields' => '',
            'order' => array(
                'ChildCogs.order' => 'ASC'
            ),
        ),
        'CogsSetting' => array(
            'className' => 'CogsSetting',
            'foreignKey' => 'cogs_id',
        ),
    );

    function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->virtualFields['cogs_code'] = 'Cogs.code';
        $this->virtualFields['cogs_name'] = 'CONCAT(Cogs.code, \' - \', Cogs.name)';
        $this->virtualFields['cogs_profit_loss'] = 'CASE WHEN SUBSTR(Cogs.code, 1, 1) REGEXP \'[0-9]+\' THEN SUBSTR(Cogs.code, 1, 1) ELSE 5 END';
        $this->virtualFields['cogs_balance_sheets'] = 'CASE WHEN SUBSTR(Cogs.code, 1, 1) REGEXP \'[0-9]+\' THEN SUBSTR(Cogs.code, 1, 1) ELSE 1 END';
        $this->virtualFields['order_sort'] = sprintf('CASE WHEN %s.order IS NULL THEN 1 ELSE 0 END', $this->alias);
    }

    function beforeSave( $options = array() ) {
        $id = $this->id;
        $id = Common::hashEmptyField($this->data, 'Cogs.id', $id);

        if( empty($id) ) {
            $this->data = Hash::insert($this->data, 'Cogs.branch_id', Configure::read('__Site.config_branch_id'));
        }
    }

    function getData( $find, $options = false, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:false;
        // $branch = isset($elements['branch'])?$elements['branch']:true;

        $default_options = array(
            'conditions'=> array(
                'Cogs.status' => 1,
                'Cogs.name <>' => '',
            ),
            'order'=> array(
                'Cogs.code' => 'ASC',
                'Cogs.id' => 'ASC',
            ),
            'contain' => array(),
            'fields' => array(),
        );

        // if( !empty($branch) ) {
        //     $default_options['conditions']['Cogs.branch_id'] = Configure::read('__Site.config_branch_id');
        // }

        if(!empty($options['conditions'])){
            $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
        }
        if(!empty($options['order'])){
            $default_options['order'] = $options['order'];
        }
        if( isset($options['contain']) && empty($options['contain']) ) {
            $default_options['contain'] = false;
        } else if(!empty($options['contain'])){
            $default_options['contain'] = array_merge($default_options['contain'], $options['contain']);
        }
        if(!empty($options['limit'])){
            $default_options['limit'] = $options['limit'];
        }
        if(!empty($options['fields'])){
            $default_options['fields'] = $options['fields'];
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function checkUniqCogs ( $cogs, $id = false, $fields = 'with_parent_code' ) {
        $existCogs = $this->getData('first', array(
            'conditions' => array(
                'Cogs.'.$fields => $cogs,
                'Cogs.status' => 1,
                'Cogs.id <>' => $id,
            ),
        ));

        if( !empty($existCogs) ) {
            return false;
        } else {
            return true;
        }
    }

    function getMerge($data, $id, $modelName = 'Cogs'){
        if(empty($data[$modelName])){
            $data_merge = $this->find('first', array(
                'conditions' => array(
                    'Cogs.id' => $id,
                )
            ));

            if(!empty($data_merge['Cogs'])){
                $data[$modelName] = $data_merge['Cogs'];
            }
        }

        return $data;
    }

    function getMergeAll($data, $modelName = false){
        if( !empty($data) ){
            foreach ($data as $key => $value) {
                $id = !empty($value[$modelName]['cogs_id'])?$value[$modelName]['cogs_id']:false;

                $value = $this->getMerge($value, $id);
                $data[$key] = $value;
            }
        }

        return $data;
    }

    function getListParent ( $id = false, $categories = false, $idx = 0 ) {
        $result = array();
        $separator = str_pad('', $idx, '-', STR_PAD_LEFT);

        if( empty($categories) ) {
            $categories = $this->getData('threaded');
        }

        if( !empty($categories) ) {
            foreach ($categories as $key => $value) {
                $cat_id = !empty($value['Cogs']['id'])?$value['Cogs']['id']:false;

                if( $id != $cat_id ) {
                    $i = $idx;
                    $name = !empty($value['Cogs']['name'])?$value['Cogs']['name']:false;
                    $child = !empty($value['children'])?$value['children']:false;

                    $result[$cat_id] = trim(sprintf('%s %s', $separator, $name));

                    if( !empty($child) ) {
                        $i += 2;
                        $result = $result + $this->getListParent($id, $child, $i);
                    }
                }
            }
        }

        return $result;
    }

    function _callOptGroup ( $id = false, $categories = false, $allowCogs = false ) {
        $result = array();
        
        if( empty($categories) ) {
            $categories = $this->getData('threaded');
        }

        if( !empty($categories) ) {
            foreach ($categories as $key => $value) {
                $cat_id = !empty($value['Cogs']['id'])?$value['Cogs']['id']:false;

                if( $id != $cat_id ) {
                    $name = !empty($value['Cogs']['cogs_name'])?$value['Cogs']['cogs_name']:false;
                    $child = !empty($value['children'])?$value['children']:false;

                    if( !empty($child) ) {
                        $result[$name] = $this->_callOptGroup($id, $child, $allowCogs);
                    } else {

                        if( !empty($allowCogs) ) {
                            if( in_array($cat_id, $allowCogs) ) {
                                $flag = true;
                            } else {
                                $flag = false;
                            }
                        } else {
                            $flag = true;
                        }

                        if( !empty($flag) ) {
                            $result[$cat_id] = $name;
                        }
                    }
                }
            }
        }

        return $result;
    }

    public function _callRefineParams( $data = '', $default_options = false ) {
        $name = !empty($data['named']['name'])?urldecode($data['named']['name']):false;
        $code = !empty($data['named']['code'])?urldecode($data['named']['code']):false;

        if(!empty($name)){
            $default_options['conditions']['Cogs.name LIKE'] = '%'.$name.'%';
        }
        if(!empty($code)){
            $code = trim($code);
            $default_options['conditions']['OR']['Cogs.code LIKE'] = '%'.$code.'%';
        }
        
        return $default_options;
    }
}
?>