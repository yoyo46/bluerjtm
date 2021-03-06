<?php
class SuratJalanDetail extends AppModel {
	var $name = 'SuratJalanDetail';
	var $validate = array(
        'ttuj_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'TTUJ harap dipilih'
            ),
        ),
        'qty' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Jumlah Unit diterima harap diisi'
            ),
            'amountValidate' => array(
                'rule' => array('amountValidate'),
                'message' => 'Jumlah Unit harus diisi dan berupa angka',
            ),
        ),
	);

	var $belongsTo = array(
        'SuratJalan' => array(
            'className' => 'SuratJalan',
            'foreignKey' => 'surat_jalan_id',
        ),
        'Ttuj' => array(
            'className' => 'Ttuj',
            'foreignKey' => 'ttuj_id',
        ),
	);

    function amountValidate () {
        if( empty($this->data['SuratJalanDetail']['qty']) ) {
            return false;
        } else if( !is_numeric($this->data['SuratJalanDetail']['qty']) ) {
            return false;
        } else {
            return true;
        }
    }

    function getData( $find, $options = false ){
        $default_options = array(
            'conditions'=> array(
                'SuratJalanDetail.status' => 1,
            ),
            'order'=> array(
                'SuratJalanDetail.id' => 'ASC',
            ),
            'contain' => array(),
            'fields' => array(),
        );

        if( !empty($options) ){
            if(!empty($options['conditions'])){
                $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
            }
            if(isset($options['order'])){
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

    function getMerge( $data, $id ){
        if( empty($data['SuratJalanDetail']) ) {
            $values = $this->getData('all', array(
                'conditions' => array(
                    'SuratJalanDetail.surat_jalan_id' => $id,
                    'SuratJalanDetail.status' => array( 0,1 ),
                ),
            ));

            if( !empty($values) ) {
                $data['SuratJalanDetail'] = $values;
            }
        }

        return $data;
    }

    function getMergeList( $data, $id, $fieldName = 'SuratJalanDetail.ttuj_id', $group = 'SuratJalanDetail.surat_jalan_id' ){
        if( empty($data['SuratJalanDetail']) ) {
            $values = $this->getData('list', array(
                'conditions' => array(
                    $fieldName => $id,
                ),
                'fields' => array(
                    'SuratJalanDetail.id', 'SuratJalanDetail.surat_jalan_id',
                ),
                'group' => $group,
            ));

            if( !empty($values) ) {
                $data['SuratJalanDetail'] = $values;
            }
        }

        return $data;
    }

    function getMergeFirst( $data, $id, $fieldName = 'SuratJalanDetail.surat_jalan_id', $options = array() ){
        if( empty($data['SuratJalanDetail']) ) {
            $options['conditions'][$fieldName] = $id;

            $values = $this->getData('first', $options);

            if( !empty($values) ) {
                $data = array_merge($data, $values);
            }
        }

        return $data;
    }

    function _callQtyDetail ( $id = false, $fieldName = 'SuratJalanDetail.ttuj_id' ) {
        $this->virtualFields['qty_diterima'] = 'SUM(qty)';
        $value = $this->getData('first', array(
            'conditions' => array(
                $fieldName => $id,
            ),
        ));

        return !empty($value['SuratJalanDetail']['qty_diterima'])?$value['SuratJalanDetail']['qty_diterima']:0;
    }

    function _callTotalQtyDiterima ( $id = false, $fieldName = 'SuratJalanDetail.ttuj_id' ) {
        $this->virtualFields['qty_diterima'] = 'SUM(qty)';
        $value = $this->getData('first', array(
            'conditions' => array(
                $fieldName => $id,
                'SuratJalan.status' => 1,
                'SuratJalan.is_canceled' => 0,
            ),
            'contain' => array(
                'SuratJalan',
            ),
        ));

        return !empty($value['SuratJalanDetail']['qty_diterima'])?$value['SuratJalanDetail']['qty_diterima']:0;
    }

    function _callTotalTtujDiterima ( $id = false ) {
        $this->virtualFields['cnt_ttuj'] = 'COUNT(SuratJalanDetail.ttuj_id)';
        $value = $this->getData('first', array(
            'conditions' => array(
                'SuratJalanDetail.surat_jalan_id' => $id,
            ),
        ));

        return !empty($value['SuratJalanDetail']['cnt_ttuj'])?$value['SuratJalanDetail']['cnt_ttuj']:0;
    }
}
?>