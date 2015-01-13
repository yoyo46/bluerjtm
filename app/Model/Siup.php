<?php
class Siup extends AppModel {
    var $name = 'Siup';
    var $validate = array(
        'truck_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Truk harap diisi'
            ),
        ),
        'no_pol' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Truk harap diisi'
            ),
        ),
        'tgl_siup' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tanggal SIUP harap diisi'
            ),
        ),
        'price' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Biaya SIUP harap diisi'
            ),
        ),
    );

    var $belongsTo = array(
        'Truck' => array(
            'className' => 'Truck',
            'foreignKey' => 'truck_id',
        )
    );

    function getData($find, $options = false){
        $default_options = array(
            'conditions'=> array(
                'Siup.status' => 1,
            ),
            'order'=> array(
                'Siup.rejected' => 'ASC',
                'Siup.paid' => 'ASC',
                'Siup.tgl_siup' => 'DESC',
            ),
            'contain' => array(
                'Truck'
            ),
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
}
?>