<?php
class Ksu extends AppModel {
	var $name = 'Ksu';
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
        'tgl_ksu' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl Ksu harap dipilih'
            ),
        ),
        'ttuj_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'TTUJ harap dipilih'
            ),
        ),
        'date_atpm' => array(
            'validateATPM' => array(
                'rule' => array('validateATPM'),
                'message' => 'Tanggal ATPM harap diisi'
            )
        )
	);

    var $belongsTo = array(
        'Ttuj' => array(
            'className' => 'Ttuj',
            'foreignKey' => 'ttuj_id',
        )
    );

    var $hasMany = array(
        'KsuDetail' => array(
            'className' => 'KsuDetail',
            'foreignKey' => 'ksu_id',
            'conditions' => array(
                'KsuDetail.status' => 1,
            ),
            'order'=> array(
                'KsuDetail.id' => 'ASC',
                'KsuDetail.created' => 'ASC',
            ),
        ),
    );

    function validateATPM($data){
        $result = true;
        if(!empty($this->data['Ksu']['kekurangan_atpm'])){
            if(empty($data['date_atpm'])){
                $result = false;
            }
        }

        return $result;
    }

    function getData( $find, $options = false, $is_merge = true, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:'active';
        $default_options = array(
            'conditions'=> array(
                'Ksu.branch_id' => Configure::read('__Site.config_branch_id'),
            ),
            'order'=> array(
                'Ksu.created' => 'DESC',
                'Ksu.id' => 'DESC',
            ),
            'contain' => array(),
            // 'contain' => array(
            //     'KsuDetail' => array(
            //         'order'=> array(
            //             'KsuDetail.id' => 'ASC',
            //             'KsuDetail.created' => 'ASC',
            //         ),
            //     )
            // ),
            'fields' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'all':
                $default_options['conditions']['Ksu.status'] = array( 0, 1 );
                break;

            case 'non-active':
                $default_options['conditions']['Ksu.status'] = 0;
                break;
            
            default:
                $default_options['conditions']['Ksu.status'] = 1;
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

    function getKsu($id){
        return $this->getData('first', array(
            'conditions' => array(
                'Ksu.id' => $id,
            ),
            'contain' => array(
                'KsuDetail',
                'Ttuj'
            )
        ));
    }
}
?>