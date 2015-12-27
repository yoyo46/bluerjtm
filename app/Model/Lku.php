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
        'completed_desc' => array(
            'completeValidate' => array(
                'rule' => array('completeValidate'),
                'message' => 'Keterangan proses selesai harap diisi'
            ),
        ),
        'completed_date' => array(
            'completeDateValidate' => array(
                'rule' => array('completeDateValidate'),
                'message' => 'Tgl selesai harap diisi'
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

    function completeValidate($data){
        if(!empty($this->data['Lku']['completed'])){
            if( !empty($this->data['Lku']['completed_desc']) ){
                return true;
            }else{
                return false;
            }
        }else{
            return true;
        }
    }

    function completeDateValidate($data){
        if(!empty($this->data['Lku']['completed'])){
            if( !empty($this->data['Lku']['completed_date']) ){
                return true;
            }else{
                return false;
            }
        }else{
            return true;
        }
    }

    function getData( $find, $options = false, $is_merge = true, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:'active';
        $branch = isset($elements['branch'])?$elements['branch']:true;

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'Lku.created' => 'DESC',
                'Lku.id' => 'DESC',
            ),
            'contain' => array(),
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

        if( !empty($branch) ) {
            $default_options['conditions']['Lku.branch_id'] = Configure::read('__Site.config_branch_id');
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

    public function _callRefineParams( $data = '', $default_options = false ) {
        $dateFrom = !empty($data['named']['DateFrom'])?$data['named']['DateFrom']:false;
        $dateTo = !empty($data['named']['DateTo'])?$data['named']['DateTo']:false;
        $nodoc = !empty($data['named']['nodoc'])?$data['named']['nodoc']:false;

        if( !empty($dateFrom) || !empty($dateTo) ) {
            if( !empty($dateFrom) ) {
                $default_options['conditions']['DATE_FORMAT(Lku.tgl_lku, \'%Y-%m-%d\') >='] = $dateFrom;
            }

            if( !empty($dateTo) ) {
                $default_options['conditions']['DATE_FORMAT(Lku.tgl_lku, \'%Y-%m-%d\') <='] = $dateTo;
            }
        }
        if(!empty($nodoc)){
            $default_options['conditions']['Lku.no_doc LIKE'] = '%'.$nodoc.'%';
        }
        
        return $default_options;
    }

    function _callTotalLkuFromTtuj( $data, $ttuj_id = false ) {
        $this->Ttuj->Lku->virtualFields['qty'] = 'SUM(Lku.total_klaim)';
        $value = $this->getData('first', array(
            'conditions' => array(
                'Lku.ttuj_id' => $ttuj_id
            ),
        ), true, array(
            'branch' => false,
        ));
        $data['Lku']['qty'] = !empty($value['Lku']['qty'])?$value['Lku']['qty']:false;

        return $data;
    }
}
?>