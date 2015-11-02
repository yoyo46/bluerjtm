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

    function getData( $find, $options = false, $is_merge = true, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:'active';
        
        $default_options = array(
            'conditions'=> array(
                'Vendor.branch_id' => Configure::read('__Site.config_branch_id'),
            ),
            'order'=> array(
                'Vendor.name' => 'ASC'
            ),
            'fields' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'all':
                $default_options['conditions']['Vendor.status'] = array( 0, 1 );
                break;

            case 'non-active':
                $default_options['conditions']['Vendor.status'] = 0;
                break;
            
            default:
                $default_options['conditions']['Vendor.status'] = 1;
                break;
        }

        if($is_merge){
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
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
            }
            if(!empty($options['limit'])){
                $default_options['limit'] = $options['limit'];
            }
            if(!empty($options['group'])){
                $default_options['group'] = $options['group'];
            }
        }else{
            $default_options = $options;
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function getMerge( $data, $id = false, $modelName = 'SupplierQuotation' ){
        if( !empty($data[0]) ) {
            foreach ($data as $key => $value) {
                $id = !empty($value[$modelName]['vendor_id'])?$value[$modelName]['vendor_id']:false;
                $value = $this->getData('first', array(
                    'conditions' => array(
                        'Vendor.id' => $id,
                    ),
                ));

                if( !empty($value) ) {
                    $data[$key] = array_merge($data[$key], $value);
                }
            }
        } else if( empty($data['Vendor']) && !empty($id) ) {
            $value = $this->getData('first', array(
                'conditions' => array(
                    'Vendor.id' => $id,
                ),
            ));

            if( !empty($value) ) {
                $data = array_merge($data, $value);
            }
        }

        return $data;
    }
}
?>