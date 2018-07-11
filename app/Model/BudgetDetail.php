<?php
class BudgetDetail extends AppModel {
	var $name = 'BudgetDetail';
	var $validate = array(
        'budget_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Coa harap dipilih'
            ),
        ),
        'month' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Bulan harap dipilih'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Bulan harap dipilih'
            ),
        ),
        'unit' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Target Unit harap dipilih'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Target Unit dipilih'
            ),
        ),
	);

    var $belongsTo = array(
        'Budget' => array(
            'className' => 'Budget',
            'foreignKey' => 'budget_id',
        ),
    );

    function getData( $find, $options = false ){
        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'BudgetDetail.id' => 'ASC'
            ),
            'fields' => array(),
            'contain' => array(),
        );

        if( !empty($options) ){
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