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
    );

    function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->virtualFields['coa_name'] = sprintf('CASE WHEN %s.with_parent_code IS NULL THEN %s.name ELSE CONCAT(%s.with_parent_code, \' - \', %s.name) END', $this->alias, $this->alias, $this->alias, $this->alias);
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
            } else if( !empty($this->data['Coa']['level']) && $this->data['Coa']['level'] == 3 ) {
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

    function getMerge($data, $id){
        if(empty($data['Coa'])){
            $data_merge = $this->find('first', array(
                'conditions' => array(
                    'Coa.id' => $id,
                )
            ));

            if(!empty($data_merge)){
                $data = array_merge($data, $data_merge);
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

    function _callOptGroup ( $id = false, $categories = false, $modelName = false ) {
        $result = array();
        
        if( empty($categories) ) {
            $categories = $this->getData('threaded');
        }

        if( !empty($categories) ) {
            foreach ($categories as $key => $value) {
                $cat_id = !empty($value['Coa']['id'])?$value['Coa']['id']:false;

                if( $id != $cat_id ) {
                    $name = !empty($value['Coa']['name'])?$value['Coa']['name']:false;
                    $child = !empty($value['children'])?$value['children']:false;

                    if( !empty($child) ) {
                        $result[$name] = $this->_callOptGroup($id, $child, $name);
                    } else {
                        // if( !empty($modelName) ) {
                        //     $result[$modelName][$cat_id] = $name;
                        // } else {
                            $result[$cat_id] = $name;
                        // }
                    }
                }
            }
        }

        return $result;
    }
}
?>