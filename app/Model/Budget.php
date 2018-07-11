<?php
class Budget extends AppModel {
	var $name = 'Budget';
	var $validate = array(
        'coa_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'COA harap dipilih'
            ),
        ),
        'year' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tahun harap dipilih'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Tahun harap dipilih'
            ),
        ),
	);

    var $belongsTo = array(
        'Coa' => array(
            'className' => 'Coa',
            'foreignKey' => 'coa_id',
        ),
    );

    var $hasMany = array(
        'BudgetDetail' => array(
            'className' => 'BudgetDetail',
            'foreignKey' => 'budget_id',
            'dependent' => false,
            'conditions' => array(
                'BudgetDetail.status' => 1,
            ),
            'fields' => '',
            'order' => array(
                'BudgetDetail.month' => 'ASC'
            ),
        ),
    );

	function getData( $find, $options = false, $is_merge = true ){
        $default_options = array(
            'conditions'=> array(
                'Budget.status' => 1,
            ),
            'order'=> array(
                'Budget.coa_id' => 'ASC'
            ),
            'fields' => array(),
            'contain' => array(),
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
}
?>