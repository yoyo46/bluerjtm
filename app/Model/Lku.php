<?php
class Lku extends AppModel {
	var $name = 'Lku';
	var $validate = array(
        'no_doc' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No Dokumen harap diisi'
            ),
            'isUnique' => array(
                'rule' => array('isUnique'),
                'message' => 'No Dokumen telah terdaftar',
            ),
        ),
        'tgl_lku' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl Lku harap dipilih'
            ),
        ),
        'ttuj_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'TTUJ harap dipilih'
            ),
        ),
	);

    var $belongsTo = array(
        'Ttuj' => array(
            'className' => 'Ttuj',
            'foreignKey' => 'ttuj_id',
        )
    );

    var $hasMany = array(
        'LkuDetail' => array(
            'className' => 'LkuDetail',
            'foreignKey' => 'lku_id',
            'conditions' => array(
                'LkuDetail.status' => 1,
            ),
            'order'=> array(
                'LkuDetail.id' => 'ASC',
                'LkuDetail.created' => 'ASC',
            ),
        ),
    );

    function getData( $find, $options = false, $is_merge = true, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:'active';
        $default_options = array(
            'conditions'=> array(
                'Lku.branch_id' => Configure::read('__Site.config_branch_id'),
            ),
            'order'=> array(
                'Lku.created' => 'DESC',
                'Lku.id' => 'DESC',
            ),
            'contain' => array(),
            // 'contain' => array(
            //     'LkuDetail' => array(
            //         'order'=> array(
            //             'LkuDetail.id' => 'ASC',
            //             'LkuDetail.created' => 'ASC',
            //         ),
            //     ),
            // ),
            'fields' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'all':
                $default_options['conditions']['Lku.status'] = array( 0, 1 );
                break;

            case 'non-active':
                $default_options['conditions']['Lku.status'] = 0;
                break;
            
            default:
                $default_options['conditions']['Lku.status'] = 1;
                break;
        }

        if(!empty($options) && $is_merge){
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
            if(!empty($options['limit'])){
                $default_options['limit'] = $options['limit'];
            }
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
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

    function getMerge( $data, $id ){
        if(empty($data['Lku'])){
            $data_merge = $this->getData('first', array(
                'conditions' => array(
                    'Lku.id' => $id,
                ),
            ));

            if(!empty($data_merge)){
                $data = array_merge($data, $data_merge);
            }
        }

        return $data;
    }

    function getLku($id){
        return $this->getData('first', array(
            'conditions' => array(
                'Lku.id' => $id,
            ),
            'contain' => array(
                'LkuDetail',
                'Ttuj'
            )
        ));
    }
}
?>