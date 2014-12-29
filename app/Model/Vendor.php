<?php
class Vendor extends AppModel {
	var $name = 'Vendor';
	var $validate = array(
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Nama Vendor harap diisi'
            ),
        ),
        'address' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Alamat harap diisi'
            ),
        ),
        'phone_number' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Nomor telepon harap diisi'
            ),
        ),
        'pic' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Nama PIC harap diisi'
            ),
        ),
        'pic_phone' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Nomor telepon PIC harap diisi'
            ),
        )
	);

	function getData($find, $options = false){
        $default_options = array(
            'conditions'=> array(
                'Vendor.status' => 1,
            ),
            'order'=> array(
                'Vendor.name' => 'ASC'
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
}
?>