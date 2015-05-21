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

	function getData($find, $options = false, $is_merge = false){
        $default_options = array(
            'conditions'=> array(
                'Ksu.status' => 1,
            ),
            'order'=> array(
                'Ksu.created' => 'DESC',
                'Ksu.id' => 'DESC',
            ),
            'contain' => array(
                'KsuDetail'
            ),
            'fields' => array(),
        );

        if(!empty($options) && $is_merge){
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
        return $this->find('first', array(
            'conditions' => array(
                'Ksu.id' => $id
            ),
            'contain' => array(
                'KsuDetail',
                'Ttuj'
            )
        ));
    }
}
?>