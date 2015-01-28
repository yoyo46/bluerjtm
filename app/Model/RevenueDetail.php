<?php
class RevenueDetail extends AppModel {
	var $name = 'RevenueDetail';
	var $validate = array(
        'no_do' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No DO harap diisi'
            ),
        ),
        'no_sj' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No SJ harap diisi'
            ),
        ),
        'qty_unit' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Quantity harap diisi'
            ),
        ),
        'price_unit' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Quantity harap diisi'
            ),
        ),
        'tipe_motor_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'tipe motor harap diisi'
            ),
        ),
        'tarif_angkutan_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'tarif angkutan tidak ditemukan'
            ),
        ),
	);

    var $belongsTo = array(
        'Revenue' => array(
            'className' => 'Revenue',
            'foreignKey' => 'revenue_id',
        ),
    );

	function getData($find, $options = false){
        $default_options = array(
            'conditions'=> array(),
            'order'=> array(),
            'contain' => array(),
            'fields' => array(),
        );

        if(!empty($options)){
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

    function getMerge($data, $id){
        if(empty($data['RevenueDetail'])){
            $data_merge = $this->find('first', array(
                'conditions' => array(
                    'id' => $id
                )
            ));

            if(!empty($data_merge)){
                $data = array_merge($data, $data_merge);
            }
        }

        return $data;
    }
}
?>