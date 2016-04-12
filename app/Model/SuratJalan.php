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

    public function _callRefineParams( $data = '', $default_options = false ) {
        $dateFrom = !empty($data['named']['DateFrom'])?$data['named']['DateFrom']:false;
        $dateTo = !empty($data['named']['DateTo'])?$data['named']['DateTo']:false;

        $dateFromTtuj = !empty($data['named']['DateFromTtuj'])?$data['named']['DateFromTtuj']:false;
        $dateToTtuj = !empty($data['named']['DateToTtuj'])?$data['named']['DateToTtuj']:false;
        
        $nopol = !empty($data['named']['nopol'])?$data['named']['nopol']:false;
        $type = !empty($data['named']['type'])?$data['named']['type']:1;

        $nodoc = !empty($data['named']['nodoc'])?$data['named']['nodoc']:false;
        $customer = !empty($data['named']['customer'])?$data['named']['customer']:false;

        $status = !empty($data['named']['status'])?$data['named']['status']:false;

        if( !empty($dateFromTtuj) || !empty($dateToTtuj) ) {
            if( !empty($dateFromTtuj) ) {
                $default_options['conditions']['DATE_FORMAT(Ttuj.tgljam_berangkat, \'%Y-%m-%d\') >='] = $dateFromTtuj;
            }

            if( !empty($dateToTtuj) ) {
                $default_options['conditions']['DATE_FORMAT(Ttuj.tgljam_berangkat, \'%Y-%m-%d\') <='] = $dateToTtuj;
            }
        }
        if( !empty($dateFrom) || !empty($dateTo) ) {
            if( !empty($dateFrom) ) {
                $default_options['conditions']['DATE_FORMAT(Ttuj.ttuj_date, \'%Y-%m-%d\') >='] = $dateFrom;
            }

            if( !empty($dateTo) ) {
                $default_options['conditions']['DATE_FORMAT(Ttuj.ttuj_date, \'%Y-%m-%d\') <='] = $dateTo;
            }
        }
        if(!empty($nodoc)){
            $default_options['conditions']['Ttuj.no_ttuj LIKE'] = '%'.$nodoc.'%';
        }
        if(!empty($nopol)){
            if( $type == 2 ) {
                $default_options['conditions']['Ttuj.truck_id'] = $nopol;
            } else {
                $default_options['conditions']['Ttuj.nopol LIKE'] = '%'.$nopol.'%';
            }
        }
        if(!empty($customer)){
            $customers = $this->Ttuj->Customer->getData('list', array(
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
}
?>