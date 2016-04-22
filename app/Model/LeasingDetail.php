<?php
class LeasingDetail extends AppModel {
	var $name = 'LeasingDetail';
	var $validate = array(
        'price' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Harga harap diisi'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Harga harap diisi dengan angka',
            ),
        ),
        'nopol' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Nopol truk harap diisi'
            ),
            'checkNopol' => array(
                'rule' => array('checkNopol'),
                'message' => 'Nopol telah terdaftar. Mohon masukan nopol lain.',
            ),
        ),
        'asset_group_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Group asset harap dipilih'
            ),
        ),
	);

    var $belongsTo = array(
        'Leasing' => array(
            'className' => 'Leasing',
            'foreignKey' => 'leasing_id',
        ),
        'Truck' => array(
            'className' => 'Truck',
            'foreignKey' => 'truck_id',
        ),
        'AssetGroup' => array(
            'className' => 'AssetGroup',
            'foreignKey' => 'asset_group_id',
        ),
    );

    function checkNopol() {
        $truck_id = $this->filterEmptyField($this->data, 'LeasingDetail', 'truck_id');
        $nopol = $this->filterEmptyField($this->data, 'LeasingDetail', 'nopol');
        $leasing_id = $this->filterEmptyField($this->data, 'LeasingDetail', 'leasing_id');

        $value = $this->Truck->getData('first', array(
            'conditions' => array(
                'Truck.id <>' => $truck_id,
                'Truck.nopol' => $nopol,
            ),
        ));

        if( !empty($value) ) {
            return false;
        } else {
            $value = $this->getData('first', array(
                'conditions' => array(
                    'LeasingDetail.nopol' => $nopol,
                    'LeasingDetail.leasing_id <>' => $leasing_id,
                ),
            ));

            if( !empty($value) ) {
                return false;
            } else {
                return true;
            }
        }
    }

	function getData($find, $options = false){
        $default_options = array(
            'conditions'=> array(
                'LeasingDetail.status' => 1,
            ),
            'order'=> array(
                'LeasingDetail.status' => 'DESC'
            ),
            'contain' => array(),
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

    function getMerge( $data, $id, $field = 'LeasingDetail.truck_id' ){
        if( empty($data['LeasingDetail']) ) {
            $value = $this->getData('first', array(
                'conditions' => array(
                    $field => $id,
                ),
            ));

            if( !empty($value) ) {
                $data = array_merge($data, $value);
            }
        }

        return $data;
    }

    function getMergeAll( $data, $id, $field = 'LeasingDetail.leasing_id' ){
        if( empty($data['LeasingDetail']) ) {
            $values = $this->getData('all', array(
                'conditions' => array(
                    $field => $id,
                ),
            ));

            if( !empty($values) ) {
                $data['LeasingDetail'] = $values;
            }
        }

        return $data;
    }
}
?>