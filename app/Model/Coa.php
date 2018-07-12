<?php
class Coa extends AppModel {
    public $actsAs = array('Tree');
	var $name = 'Coa';
	var $validate = array(
		'code' => array(
			'validateCode' => array(
				'rule' => array('validateCode'),
                'message' => 'Kode COA harap diisi'
			),
            'validateCodeWithParent' => array(
                'rule' => array('validateCodeWithParent'),
                'message' => 'Kode COA telah terdaftar',
            ),
		),
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Nama COA harap diisi'
            ),
        ),
        'type' => array(
            'validateType' => array(
                'rule' => array('validateType'),
                'message' => 'Tipe COA harap diisi'
            ),
        )
	);

    var $belongsTo = array(
        'ParentCoa' => array(
            'className' => 'Coa',
            'foreignKey' => 'parent_id',
            'conditions' => array(
                'ParentCoa.status' => 1,
            ),
            'fields' => '',
            'order' => ''
        )
    );

    var $hasMany = array(
        'ChildCoa' => array(
            'className' => 'AdviceCategory',
            'foreignKey' => 'parent_id',
            'dependent' => false,
            'conditions' => array(
                'ChildCoa.status' => 1,
            ),
            'fields' => '',
            'order' => array(
                'ChildCoa.order' => 'ASC'
            ),
        ),
        'CoaClosing' => array(
            'className' => 'CoaClosing',
            'foreignKey' => 'coa_id',
        ),
        'CashBankDetail' => array(
            'className' => 'CashBankDetail',
            'foreignKey' => 'coa_id',
        ),
        'BranchCoa' => array(
            'className' => 'BranchCoa',
            'foreignKey' => 'coa_id',
        ),
        'CoaSettingDetail' => array(
            'className' => 'CoaSettingDetail',
            'foreignKey' => 'coa_id',
        ),
        'Journal' => array(
            'className' => 'Journal',
            'foreignKey' => 'coa_id',
        ),
        'Budget' => array(
            'className' => 'Budget',
            'foreignKey' => 'coa_id',
        ),
    );

    function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->virtualFields['coa_code'] = 'CASE WHEN Coa.with_parent_code IS NOT NULL AND Coa.with_parent_code <> \'\' THEN Coa.with_parent_code ELSE Coa.code END';
        $this->virtualFields['coa_name'] = 'CASE WHEN Coa.with_parent_code IS NOT NULL AND Coa.with_parent_code <> \'\' THEN CONCAT(Coa.with_parent_code, \' - \', Coa.name) WHEN Coa.code <> \'\' THEN CONCAT(Coa.code, \' - \', Coa.name) ELSE Coa.name END';
        $this->virtualFields['coa_profit_loss'] = 'CASE WHEN SUBSTR(Coa.code, 1, 1) REGEXP \'[0-9]+\' THEN SUBSTR(Coa.code, 1, 1) ELSE 5 END';
        $this->virtualFields['coa_balance_sheets'] = 'CASE WHEN SUBSTR(Coa.code, 1, 1) REGEXP \'[0-9]+\' THEN SUBSTR(Coa.code, 1, 1) ELSE 1 END';
        $this->virtualFields['order_sort'] = sprintf('CASE WHEN %s.order IS NULL THEN 1 ELSE 0 END', $this->alias);
    }

    function getData( $find, $options = false, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:false;

        $default_options = array(
            'conditions'=> array(
                'Coa.status' => 1,
                'Coa.name <>' => '',
            ),
            'order'=> array(
                'Coa.with_parent_code' => 'ASC',
                'Coa.code' => 'ASC',
                'Coa.id' => 'ASC',
            ),
            'contain' => array(),
            'fields' => array(),
        );

        switch ($status) {
            case 'cash_bank_child':
                $default_options['conditions'] = array_merge($default_options['conditions'], array(
                    'Coa.level' => 4,
                    'Coa.is_cash_bank' => 1,
                ));
                break;
        }

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

    function checkUniqCoa ( $coa, $id = false, $fields = 'with_parent_code' ) {
        $existCoa = $this->getData('first', array(
            'conditions' => array(
                'Coa.'.$fields => $coa,
                'Coa.status' => 1,
                'Coa.id <>' => $id,
            ),
        ));

        if( !empty($existCoa) ) {
            return false;
        } else {
            return true;
        }
    }

    function validateCode () {
        if( empty($this->data['Coa']['code']) ) {
            if( !empty($this->data['Coa']['level']) && $this->data['Coa']['level'] == 4 ) {
                return false;
            } else {
                return true;
            }
        } else if( empty($this->data['Coa']['with_parent_code']) ) {
            $coa_id = !empty($this->data['Coa']['id'])?$this->data['Coa']['id']:false;
            return $this->checkUniqCoa($this->data['Coa']['code'], $coa_id, 'code');
        } else {
            return true;
        }
    }

    function validateCodeWithParent () {
        if( !empty($this->data['Coa']['with_parent_code']) ) {
            $coa_id = !empty($this->data['Coa']['id'])?$this->data['Coa']['id']:false;
            return $this->checkUniqCoa($this->data['Coa']['with_parent_code'], $coa_id, 'with_parent_code');
        } else {
            return true;
        }
    }

    function validateType () {
        if( empty($this->data['Coa']['type']) ) {
            if( !empty($this->data['Coa']['level']) && $this->data['Coa']['level'] == 4 ) {
                return false;
            } else if( !empty($this->data['Coa']['level']) && $this->data['Coa']['level'] == 3 ) {
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }

    function getMerge($data, $id, $modelName = 'Coa'){
        if(empty($data[$modelName])){
            $data_merge = $this->find('first', array(
                'conditions' => array(
                    'Coa.id' => $id,
                )
            ));

            if(!empty($data_merge['Coa'])){
                $data[$modelName] = $data_merge['Coa'];
            }
        }

        return $data;
    }

    function getMergeAll($data, $modelName = false){
        if( !empty($data) ){
            foreach ($data as $key => $value) {
                $id = !empty($value[$modelName]['coa_id'])?$value[$modelName]['coa_id']:false;

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
                $cat_id = !empty($value['Coa']['id'])?$value['Coa']['id']:false;

                if( $id != $cat_id ) {
                    $i = $idx;
                    $name = !empty($value['Coa']['name'])?$value['Coa']['name']:false;
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

    function _callOptGroup ( $id = false, $categories = false, $modelName = false, $allowCoas = false ) {
        $result = array();
        
        if( empty($categories) ) {
            $categories = $this->getData('threaded');
        }

        if( !empty($categories) ) {
            foreach ($categories as $key => $value) {
                $cat_id = !empty($value['Coa']['id'])?$value['Coa']['id']:false;

                if( $id != $cat_id ) {
                    $name = !empty($value['Coa']['coa_name'])?$value['Coa']['coa_name']:false;
                    $child = !empty($value['children'])?$value['children']:false;

                    if( !empty($child) ) {
                        $result[$name] = $this->_callOptGroup($id, $child, $name, $allowCoas);
                    } else {

                        if( !empty($allowCoas) ) {
                            if( in_array($cat_id, $allowCoas) ) {
                                $flag = true;
                            } else {
                                $flag = false;
                            }
                        } else {
                            $flag = true;
                        }

                        // if( !empty($modelName) ) {
                        //     $result[$modelName][$cat_id] = $name;
                        // } else {
                        if( !empty($flag) ) {
                            $result[$cat_id] = $name;
                        }
                        // }
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
            $default_options['conditions']['Coa.name LIKE'] = '%'.$name.'%';
        }
        if(!empty($code)){
            $code = trim($code);
            $default_options['conditions']['OR']['Coa.code LIKE'] = '%'.$code.'%';
            $default_options['conditions']['OR']['Coa.with_parent_code LIKE'] = '%'.$code.'%';
        }
        
        return $default_options;
    }

    function _callGenerateParent ( $values, $data ) {
        $result = array();
        if( !empty($values) ) {
            foreach ($values as $key => $value) {
                $flag = false;
                $id = Common::hashEmptyField($value, 'Coa.id');
                $name = Common::hashEmptyField($value, 'Coa.coa_name');
                $children = Common::hashEmptyField($value, 'children');
                $result_tmp = array();

                if( !empty($children) ) {
                    $tmp = $this->_callGenerateParent($children, $data);
                    $result_tmp = Common::hashEmptyField($tmp, 'data');
                    $flag = Common::hashEmptyField($tmp, 'flag');
                }

                if( !empty($data[$id]) && empty($flag) ) {
                    $flag = true;
                }

                if( !empty($flag) ) {
                    $result[$id] = array(
                        'name' => $name,
                    );

                    if( !empty($result_tmp) ) {
                        $result[$id] = $result[$id] + $result_tmp;
                    }
                }
            }
        }

        return array(
            'data' => $result,
            'flag' => $flag,
        );
    }
}
?>