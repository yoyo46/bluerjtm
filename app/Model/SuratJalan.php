<?php
class SuratJalan extends AppModel {
	var $name = 'SuratJalan';
	var $validate = array(
        // 'nodoc' => array(
        //     'notempty' => array(
        //         'rule' => array('notempty'),
        //         'message' => 'No. Sj harap diisi'
        //     ),
        // ),
        'total_qty' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Total unit harap diisi'
            ),
        ),
        'tgl_surat_jalan' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tanggal Sj harap diisi'
            ),
        ),
	);

    var $hasMany = array(
        'SuratJalanDetail' => array(
            'className' => 'SuratJalanDetail',
            'foreignKey' => 'surat_jalan_id',
            'conditions' => array(
                'SuratJalanDetail.status' => 1,
            ),
        ),
    );

    function getData( $find, $options = false, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:'active';
        $branch = isset($elements['branch'])?$elements['branch']:true;
        $branch_is_plant = Configure::read('__Site.config_branch_plant');

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'SuratJalan.status' => 'DESC',
                'SuratJalan.is_canceled' => 'ASC',
                'SuratJalan.created' => 'DESC',
                'SuratJalan.id' => 'DESC',
            ),
            'contain' => array(),
            'fields' => array(),
        );

        switch ($status) {
            case 'all':
                $default_options['conditions']['SuratJalan.status'] = array( 0, 1 );
                break;

            case 'non-active':
                $default_options['conditions']['SuratJalan.status'] = 0;
                break;
            
            default:
                $default_options['conditions']['SuratJalan.status'] = 1;
                $default_options['conditions']['SuratJalan.is_canceled'] = 0;
                break;
        }

        if( !empty($branch_is_plant) ) {
            $default_options['conditions']['SuratJalan.branch_id'] = Configure::read('__Site.Branch.Plant.id');
        } else if( !empty($branch) ) {
            $default_options['conditions']['SuratJalan.branch_id'] = Configure::read('__Site.config_branch_id');
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
        if( empty($data['SuratJalan']) ) {
            $value = $this->getData('first', array(
                'conditions' => array(
                    'SuratJalan.id' => $id,
                ),
            ));

            if( !empty($value) ) {
                $data = array_merge($data, $value);
            }
        }

        return $data;
    }

    public function _callRefineParams( $data = '', $default_options = false ) {
        $dateFrom = !empty($data['named']['DateFrom'])?$data['named']['DateFrom']:false;
        $dateTo = !empty($data['named']['DateTo'])?$data['named']['DateTo']:false;

        $dateFromTtuj = !empty($data['named']['DateFromTtuj'])?$data['named']['DateFromTtuj']:false;
        $dateToTtuj = !empty($data['named']['DateToTtuj'])?$data['named']['DateToTtuj']:false;
        
        $nopol = !empty($data['named']['nopol'])?$data['named']['nopol']:false;
        $type = !empty($data['named']['type'])?$data['named']['type']:1;

        $noref = !empty($data['named']['noref'])?$data['named']['noref']:false;
        $nodoc = !empty($data['named']['nodoc'])?$data['named']['nodoc']:false;
        $nottuj = !empty($data['named']['nottuj'])?$data['named']['nottuj']:false;
        $customer = !empty($data['named']['customer'])?$data['named']['customer']:false;
        $customer_id = !empty($data['named']['customer_id'])?$data['named']['customer_id']:false;
        $note_ttuj = !empty($data['named']['note_ttuj'])?$data['named']['note_ttuj']:false;

        $status = !empty($data['named']['status'])?$data['named']['status']:false;

        if( !empty($dateFromTtuj) || !empty($dateToTtuj) ) {
            $default_options['contain'][] = 'Ttuj';

            if( !empty($dateFromTtuj) ) {
                $default_options['conditions']['DATE_FORMAT(Ttuj.ttuj_date, \'%Y-%m-%d\') >='] = $dateFromTtuj;
            }

            if( !empty($dateToTtuj) ) {
                $default_options['conditions']['DATE_FORMAT(Ttuj.ttuj_date, \'%Y-%m-%d\') <='] = $dateToTtuj;
            }
        }
        if( !empty($dateFrom) || !empty($dateTo) ) {
            if( !empty($dateFrom) ) {
                $default_options['conditions']['DATE_FORMAT(SuratJalan.tgl_surat_jalan, \'%Y-%m-%d\') >='] = $dateFrom;
            }

            if( !empty($dateTo) ) {
                $default_options['conditions']['DATE_FORMAT(SuratJalan.tgl_surat_jalan, \'%Y-%m-%d\') <='] = $dateTo;
            }
        }
        if(!empty($nodoc)){
            $default_options['conditions']['SuratJalan.nodoc LIKE'] = '%'.$nodoc.'%';
        }
        if(!empty($noref)){
            $default_options['conditions']['LPAD(SuratJalan.id, 6, 0) LIKE'] = '%'.$noref.'%';
        }
        if(!empty($nopol)){
            if( $type == 2 ) {
                $default_options['conditions']['Ttuj.truck_id'] = $nopol;
            } else {
                $default_options['conditions']['Ttuj.nopol LIKE'] = '%'.$nopol.'%';
            }
            $default_options['contain'][] = 'Ttuj';
        }
        if(!empty($customer) || !empty($customer_id)){
            if( !empty($customer) ){
                $customers = $this->SuratJalanDetail->Ttuj->Customer->getData('list', array(
                    'conditions' => array(
                        'Customer.customer_name_code LIKE' => '%'.$customer.'%',
                    ),
                    'fields' => array(
                        'Customer.id', 'Customer.id'
                    ),
                ), true, array(
                    'status' => 'all',
                    'branch' => false,
                ));
                $default_options['conditions']['Ttuj.customer_id'] = $customers;
            } else {
                $default_options['conditions']['Ttuj.customer_id'] = $customer_id;
            }

            $default_options['contain'][] = 'Ttuj';
        }
        if(!empty($nottuj)){
            $default_options['conditions']['Ttuj.no_ttuj LIKE'] = '%'.$nottuj.'%';
            $default_options['contain'][] = 'Ttuj';
        }
        if(!empty($note_ttuj)){
            $default_options['conditions']['Ttuj.note LIKE'] = '%'.$note_ttuj.'%';
            $default_options['contain'][] = 'Ttuj';
        }

        if( !empty($status) ) {
            $default_options['contain'][] = 'Ttuj';
            
            switch ($status) {
                case 'pending':
                    $default_options['conditions']['Ttuj.status_sj'] = 'none';
                    break;
                case 'half_receipt':
                    $default_options['conditions']['Ttuj.status_sj'] = 'half';
                    $default_options['conditions']['SuratJalan.id <>'] = NULL;
                    break;
                case 'receipt':
                    $default_options['conditions']['Ttuj.status_sj'] = 'full';
                    $default_options['conditions']['SuratJalan.id <>'] = NULL;
                    break;
            }
        }

        return $default_options;
    }

    function getRequestData ( $data, $property_id ) {
        $dataFacility = $this->find('all', array(
            'conditions' => array(
                'PropertyFacility.property_id' => $property_id,
            ),
            'order' => array(
                'PropertyFacility.id' => 'ASC',
            ),
        ));
        $requestData = array();

        if( !empty($dataFacility) ) {
            foreach ($dataFacility as $key => $value) {
                $id = !empty($value['PropertyFacility']['facility_id'])?$value['PropertyFacility']['facility_id']:false;
                $other_text = !empty($value['PropertyFacility']['other_text'])?$value['PropertyFacility']['other_text']:false;

                if( $id == -1 ) {
                    $requestData['PropertyFacility']['other_id'] = true;
                    $requestData['PropertyFacility']['other_text'] = $other_text;
                } else {
                    $requestData['PropertyFacility']['facility_id'][$id] = true;
                }
            }
        }

        $data = array_merge($data, $requestData);

        return $data;
    }

    function recoverTtuj ( $details = false ) {
        if( !empty($details) ) {
            foreach ($details as $key => $value) {
                $ttuj_id = $this->filterEmptyField($value, 'SuratJalanDetail', 'ttuj_id');
                $muatan = $this->SuratJalanDetail->Ttuj->TtujTipeMotor->getTotalMuatan( $ttuj_id );
                $qtyDiterima = $this->SuratJalanDetail->_callTotalQtyDiterima( $ttuj_id );

                if( $qtyDiterima >= $muatan ) {
                    $this->SuratJalanDetail->Ttuj->set('status_sj', 'full');
                } else if( !empty($qtyDiterima) ) {
                    $this->SuratJalanDetail->Ttuj->set('status_sj', 'half');
                } else {
                    $this->SuratJalanDetail->Ttuj->set('status_sj', 'none');
                }

                $this->SuratJalanDetail->Ttuj->id = $ttuj_id;
                $this->SuratJalanDetail->Ttuj->save();
            }
        }
    }
}
?>