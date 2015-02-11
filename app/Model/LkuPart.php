<?php
class LkuPart extends AppModel {
	var $name = 'LkuPart';
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
                'message' => 'Tgl Lku Part harap dipilih'
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
        'LkuPartDetail' => array(
            'className' => 'LkuPartDetail',
            'foreignKey' => 'lku_part_id',
            'conditions' => array(
                'LkuPartDetail.status' => 1,
            ),
        ),
    );

	function getData($find, $options = false){
        $default_options = array(
            'conditions'=> array(
                'LkuPart.status' => 1,
            ),
            'order'=> array(
                'LkuPart.created' => 'DESC',
                'LkuPart.id' => 'DESC',
            ),
            'contain' => array(
                'LkuPartDetail'
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
            if(!empty($options['limit'])){
                $default_options['limit'] = $options['limit'];
            }
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
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