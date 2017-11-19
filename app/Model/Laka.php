<?php
class Laka extends AppModel {
	var $name = 'Laka';
	var $validate = array(
        'nodoc' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No Dokumen name harap diisi'
            ),
        ),
        'nopol' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Nopol harap diisi'
            ),
        ),
        'tgl_laka' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl LAKA harap dipilih'
            ),
        ),
        'ttuj_id' => array(
            // 'notempty' => array(
            //     'rule' => array('notempty'),
            //     'message' => 'TTUJ harap dipilih'
            // ),
            'validateTtuj' => array(
                'rule' => array('validateTtuj'),
                'message' => 'Harap Pilih TTUJ atau supir pengganti untuk bisa melanjutkan LAKA'
            )
        ),
        'change_driver_id' => array(
            'validateChangeDriver' => array(
                'rule' => array('validateChangeDriver'),
                'message' => 'Harap Pilih TTUJ atau supir pengganti untuk bisa melanjutkan LAKA'
            )
        ),
        'truck_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Truk harap dipilih'
            ),
        ),
        'lokasi_laka' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Lokasi LAKA harap diisi'
            ),
        ),
        // 'from_city_name' => array(
        //     'notempty' => array(
        //         'rule' => array('notempty'),
        //         'message' => 'Kota dari harap diisi'
        //     ),
        // ),
        // 'to_city_name' => array(
        //     'notempty' => array(
        //         'rule' => array('notempty'),
        //         'message' => 'Kota ke harap diisi'
        //     ),
        // ),
        'status_muatan' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Status muatan harap dipilih'
            ),
        ),
        'driver_condition' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Kondisi supir harap diisi'
            ),
        ),
        'truck_condition' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Kondisi armada dan muatan harap diisi'
            ),
        ),
        'description_laka' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Deskripsi LAKA harap diisi'
            ),
        ),
        'complete_desc' => array(
            'completeValidate' => array(
                'rule' => array('completeValidate'),
                'message' => 'Keterangan selesai LAKA harap diisi'
            ),
        ),
        'completed_date' => array(
            'completeDateValidate' => array(
                'rule' => array('completeDateValidate'),
                'message' => 'Tgl selesai LAKA harap diisi'
            ),
        )
	);

    var $belongsTo = array(
        'Ttuj' => array(
            'className' => 'Ttuj',
            'foreignKey' => 'ttuj_id',
        ),
        'Truck' => array(
            'className' => 'Truck',
            'foreignKey' => 'truck_id',
        ),
    );

    var $hasOne = array(
        'LakaDetail' => array(
            'className' => 'LakaDetail',
            'foreignKey' => 'laka_id',
        ),
    );

    var $hasMany = array(
        'LakaMedias' => array(
            'className' => 'LakaMedias',
            'foreignKey' => 'laka_id',
        ),
        'LakaPaymentDetail' => array(
            'className' => 'LakaPaymentDetail',
            'foreignKey' => 'laka_id',
        ),
    );

    function validateTtuj($data){
        $result = false;
        if(empty($this->data['Laka']['driver_name']) && (!empty($this->data['Laka']['change_driver_id']) || !empty($this->data['Laka']['ttuj_id']) ) ){
            if(!empty($this->data['Laka']['ttuj_id']) && !empty($this->data['Laka']['change_driver_id'])){
                $result = true;
            }else if(empty($this->data['Laka']['ttuj_id']) && !empty($this->data['Laka']['change_driver_id'])){
                $result = true;
            }
        }else if(!empty($this->data['Laka']['driver_name'])){
            $result = true;
        }

        return $result;
    }

    function validateChangeDriver($data){
        $result = false;
        if(empty($this->data['Laka']['driver_name']) && (!empty($this->data['Laka']['change_driver_id']) || !empty($this->data['Laka']['ttuj_id']) ) ){
            $result = true;
        }else if(!empty($this->data['Laka']['driver_name'])){
            $result = true;
        }
        
        return $result;
    }

    function completeValidate($data){
        if(!empty($this->data['Laka']['completed'])){
            if( !empty($this->data['Laka']['complete_desc']) ){
                return true;
            }else{
                return false;
            }
        }else{
            return true;
        }
    }

    function completeDateValidate($data){
        if(!empty($this->data['Laka']['completed'])){
            if( !empty($this->data['Laka']['completed_date']) ){
                return true;
            }else{
                return false;
            }
        }else{
            return true;
        }
    }

    function getData( $find, $options = false, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:'active';
        $branch = isset($elements['branch'])?$elements['branch']:true;

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'Laka.created' => 'DESC',
                'Laka.id' => 'DESC',
            ),
            'contain' => array(),
            'fields' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'all':
                $default_options['conditions']['Laka.status'] = array( 0, 1 );
                break;

            case 'non-active':
                $default_options['conditions']['Laka.status'] = 0;
                break;
            
            default:
                $default_options['conditions']['Laka.status'] = 1;
                break;
        }

        if( !empty($branch) ) {
            $default_options['Laka.branch_id'] = Configure::read('__Site.config_branch_id');
        }

        if( !empty($options) ){
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
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function getMerge( $data, $id ){
        if( empty($data['Laka'])){
            $data_merge = $this->getData('first', array(
                'conditions' => array(
                    'Laka.id' => $id,
                ),
            ), array(
                'branch' => false,
                'status' => 'all',
            ));

            if(!empty($data_merge)){
                $data = array_merge($data, $data_merge);
            }
        }

        return $data;
    }

    function getMergeTtuj( $ttuj_id, $data, $conditions = false ){
        if( empty($data['Laka'])){
            $condition_default = array(
                'Laka.ttuj_id' => $ttuj_id,
            );

            if( !empty($conditions) ) {
                $condition_default = array_merge($condition_default, $conditions);
            }

            $data_merge = $this->getData('first', array(
                'conditions' => $condition_default,
            ), array(
                'branch' => false,
            ));

            if(!empty($data_merge)){
                $data = array_merge($data, $data_merge);
            }
        }

        return $data;
    }

    function getMergeTruck( $truck_id, $data, $conditions = false ){
        if( empty($data['Laka'])){
            $condition_default = array(
                'Laka.truck_id' => $truck_id,
            );

            if( !empty($conditions) ) {
                $condition_default = array_merge($condition_default, $conditions);
            }

            $data_merge = $this->getData('first', array(
                'conditions' => $condition_default,
            ), array(
                'branch' => false,
            ));

            if(!empty($data_merge)){
                $data = array_merge($data, $data_merge);
            }
        }

        return $data;
    }

    public function _callRefineParams( $data = '', $default_options = false ) {
        $dateFrom = !empty($data['named']['DateFrom'])?$data['named']['DateFrom']:false;
        $dateTo = !empty($data['named']['DateTo'])?$data['named']['DateTo']:false;
        $nopol = !empty($data['named']['nopol'])?$data['named']['nopol']:false;
        $type = !empty($data['named']['type'])?$data['named']['type']:1;
        $nottuj = !empty($data['named']['nottuj'])?$data['named']['nottuj']:false;
        $nodoc = !empty($data['named']['nodoc'])?$data['named']['nodoc']:false;
        $noref = !empty($data['named']['noref'])?$data['named']['noref']:false;
        $status = !empty($data['named']['status'])?$data['named']['status']:false;
        $insurance = !empty($data['named']['insurance'])?$data['named']['insurance']:false;

        if( !empty($dateFrom) || !empty($dateTo) ) {
            if( !empty($dateFrom) ) {
                $default_options['conditions']['DATE_FORMAT(Laka.tgl_laka, \'%Y-%m-%d\') >='] = $dateFrom;
            }

            if( !empty($dateTo) ) {
                $default_options['conditions']['DATE_FORMAT(Laka.tgl_laka, \'%Y-%m-%d\') <='] = $dateTo;
            }
        }
        if(!empty($nopol)){
            if( $type == 2 ) {
                $conditionsNopol = array(
                    'Truck.id' => $nopol,
                );
            } else {
                $conditionsNopol = array(
                    'Truck.nopol LIKE' => '%'.$nopol.'%',
                );
            }

            $truckSearch = $this->Truck->getData('list', array(
                'conditions' => $conditionsNopol,
                'fields' => array(
                    'Truck.id', 'Truck.id',
                ),
            ), true, array(
                'status' => 'all',
                'branch' => false,
            ));

            $default_options['conditions']['Laka.truck_id'] = $truckSearch;
        }
        if(!empty($nottuj)){
            $default_options['conditions']['Ttuj.no_ttuj LIKE'] = '%'.$nottuj.'%';
        }
        if(!empty($nodoc)){
            $default_options['conditions']['Laka.nodoc LIKE'] = '%'.$nodoc.'%';
        }
        if(!empty($noref)){
            $default_options['conditions']['LPAD(Laka.id, 6, 0) LIKE'] = '%'.$noref.'%';
        }
        if(!empty($status)){
            $tmpArry = array(
                0 => 'active',
                1 => 'completed',
            );

            if( in_array($status, $tmpArry) ) {
                $status = array_search($status, $tmpArry);
                $default_options['conditions']['Laka.completed'] = $status;
            }
        }
        
        return $default_options;
    }
}
?>