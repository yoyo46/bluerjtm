<?php
class AssetGroupCoa extends AppModel {
	var $name = 'AssetGroupCoa';
	var $validate = array(
		'asset_group_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
                'message' => 'Group asset harap dipilih'
			),
		),
        'document_type' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Akun harap dipilih'
            ),
        ),
        'coa_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Akun harap dipilih'
            ),
        ),
	);
    
    var $belongsTo = array(
        'AssetGroup' => array(
            'className' => 'AssetGroup',
            'foreignKey' => 'asset_group_id',
        ),
    );

    public function getData( $find = 'all', $options = array() ) {
        $default_options = array(
            'conditions'=> array(
                'AssetGroupCoa.status' => 1,
            ),
            'contain' => array(),
            'fields'=> array(),
            'group'=> array(),
            'order' => array(
                'AssetGroupCoa.id' => 'ASC',
            ),
        );

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

    function getMerge($data, $id, $list = 'all', $document_type = false){
        if(empty($data['AssetGroupCoa'])){
            $conditions = array(
                'AssetGroupCoa.asset_group_id' => $id
            );

            if( !empty($document_type) ) {
                $conditions['AssetGroupCoa.document_type'] = $document_type;
            }

            $values = $this->getData($list, array(
                'conditions' => $conditions,
            ));

            if(!empty($values)){
                if( $list == 'first' ) {
                    $data = array_merge($data, $values);
                } else {
                    $data['AssetGroupCoa'] = $values;
                }
            }
        }

        return $data;
    }
}
?>