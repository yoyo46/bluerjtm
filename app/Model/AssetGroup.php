<?php
class AssetGroup extends AppModel {
	var $name = 'AssetGroup';
	var $validate = array(
		'code' => array(
			'notempty' => array(
				'rule' => array('notempty'),
                'message' => 'Kode group asset harap diisi'
			),
            'unique' => array(
                'rule' => 'isUnique',
                'message' => 'Kode group asset sudah terdaftar'
            ),
		),
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Nama group asset harap diisi'
            ),
        ),
        'umur_ekonomis' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Umur ekomonis harap diisi'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Format umur ekomonis tidak valid',
            ),
        ),
        'nilai_sisa' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Nilai sisa harap diisi'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Nilai sisa harus berupa angka',
            ),
        ),
	);

    public function getData( $find = 'all', $options = array(), $elements = array()  ) {
        $status = isset($elements['status']) ? $elements['status']:'active';

        $default_options = array(
            'conditions'=> array(),
            'contain' => array(),
            'fields'=> array(),
            'group'=> array(),
            'order' => array(
                'AssetGroup.code' => 'ASC',
                'AssetGroup.name' => 'ASC',
            ),
        );

        switch ($status) {
            case 'non-active':
                $default_options['conditions'] = array_merge($default_options['conditions'], array(
                    'AssetGroup.status' => 0,
                ));
                break;
            case 'active':
                $default_options['conditions'] = array_merge($default_options['conditions'], array(
                    'AssetGroup.status' => 1,
                ));
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
        if(empty($data['AssetGroup'])){
            $data_merge = $this->getData('first', array(
                'conditions' => array(
                    'AssetGroup.id' => $id
                )
            ), array(
                'status' => 'all',
            ));

            if(!empty($data_merge)){
                $data = array_merge($data, $data_merge);
            }
        }

        return $data;
    }

    public function _callRefineParams( $data = '', $default_options = false ) {
        $code = !empty($data['named']['code'])?$data['named']['code']:false;
        $name = !empty($data['named']['name'])?$data['named']['name']:false;

        if( !empty($code) ) {
            $default_options['conditions']['AssetGroup.code LIKE'] = '%'.$code.'%';
        }
        if( !empty($name) ) {
            $default_options['conditions']['AssetGroup.name LIKE'] = '%'.$name.'%';
        }
        
        return $default_options;
    }
}
?>