<?php
class Company extends AppModel {
	var $name = 'Company';
	var $validate = array(
        'code' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Kode company harap diisi'
            ),
            'unique' => array(
                'rule' => 'isUnique',
                'message' => 'Kode sudah terdatar, mohon masukkan kode lain.'
            ),
        ),
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Nama harap diisi'
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
                'message' => 'Telepon harap diisi'
            ),
        )
	);

	function getData($find, $options = false, $elements = array()){
        $status = isset($elements['status'])?$elements['status']:'active';

        $default_options = array(
            'conditions'=> array(
                'Company.status' => 1,
            ),
            'order'=> array(
                'Company.name' => 'ASC'
            ),
        );

        switch ($status) {
            case 'all':
                $default_options['conditions']['Company.status'] = array( 0, 1 );
                break;

            case 'non-active':
                $default_options['conditions']['Company.status'] = 0;
                break;

            case 'invoice':
                $default_options['conditions']['Company.status'] = 1;
                $default_options['conditions']['Company.is_invoice'] = 1;
                break;
            
            default:
                $default_options['conditions']['Company.status'] = 1;
                break;
        }

        if( !empty($options) ) {
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
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function getMerge($data, $id){
        if(empty($data['Company'])){
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