<?php
class SuratJalan extends AppModel {
	var $name = 'SuratJalan';
	var $validate = array(
        'ttuj_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'TTUJ harap dipilih'
            ),
        ),
        'tgl_surat_jalan' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl terima harap diisi'
            ),
        ),
        'qty' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Quantity harap diisi'
            ),
        ),
	);

    var $belongsTo = array(
        'Ttuj' => array(
            'className' => 'Ttuj',
            'foreignKey' => 'ttuj_id',
        ),
    );

	function getData($find, $options = false, $is_merge = true){
        $default_options = array(
            'conditions'=> array(
                'SuratJalan.status' => 1,
            ),
            'order'=> array(
                'SuratJalan.tgl_surat_jalan' => 'DESC',
                'SuratJalan.id' => 'DESC',
            ),
            'contain' => array(
                'Ttuj'
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
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
            }
            if(!empty($options['limit'])){
                $default_options['limit'] = $options['limit'];
            }
        } else if( !empty($options) ) {
            $default_options = $options;
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function getSJ ( $data, $ttuj_id, $list = 'first' ) {
        $sj = $this->getData($list, array(
            'conditions' => array(
                'SuratJalan.ttuj_id' => $ttuj_id,
            ),
        ));

        if( !empty($sj) ) {
            if( $list == 'first' ) {
                $data = array_merge($data, $sj);
            } else {
                $data['SuratJalan'] = $sj;
            }
        }

        return $data;
    }

    function getSJKembali ( $ttuj_id ) {
        $muatan_kembali = 0;
        $sj = $this->getData('first', array(
            'conditions' => array(
                'SuratJalan.ttuj_id' => $ttuj_id,
            ),
            'fields' => array(
                'SUM(qty) muatan'
            ),
        ));

        if( !empty($sj[0]['muatan']) ) {
            $muatan_kembali = $sj[0]['muatan'];
        }

        return $muatan_kembali;
    }
}
?>