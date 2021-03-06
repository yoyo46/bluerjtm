<?php
class Bank extends AppModel {
	var $name = 'Bank';
	var $validate = array(
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Bank name harap diisi'
            ),
        ),
        'branch' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Cabang harap diisi'
            ),
        ),
        'account_number' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No Rek harap diisi'
            ),
        ),
        'account_name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Atas Nama harap diisi'
            ),
        ),
	);

	var $belongsTo = array(
		'Coa' => array(
			'className' => 'Coa',
			'foreignKey' => 'coa_id',
		)
	);

    function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->virtualFields['bank_name'] = sprintf('CASE WHEN %s.account_number = \'\' THEN %s.name ELSE CONCAT(%s.name, \' - \', %s.account_number) END', $this->alias, $this->alias, $this->alias, $this->alias);
    }

	function getData( $find, $options = false, $is_merge = true ){
        $default_options = array(
            'conditions'=> array(
                'Bank.status' => 1,
            ),
            'order'=> array(
                'Bank.name' => 'ASC'
            ),
            'contain' => array(
                'Coa'
            ),
            'fields' => array(),
        );

        if( !empty($options) && $is_merge ){
            if(!empty($options['conditions'])){
                $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
            }
            if(!empty($options['order'])){
                $default_options['order'] = array_merge($default_options['order'], $options['order']);
            }
            if(!empty($options['contain'])){
                $default_options['contain'] = array_merge($default_options['contain'], $options['contain']);
            }
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
            }
            if(!empty($options['limit'])){
                $default_options['limit'] = $options['limit'];
            }
        } else if( !empty($options) ) {
            $default_options = $options;
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function getMerge( $data, $id, $with_contain = false ){
        if(empty($data['Bank'])){
            $data_merge = $this->find('first', array(
                'conditions' => array(
                    'Bank.id' => $id
                ),
            ));

            if(!empty($data_merge)){
                $data = array_merge($data, $data_merge);
            }
        }

        return $data;
    }
}
?>