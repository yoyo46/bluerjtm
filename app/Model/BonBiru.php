<?php
class BonBiru extends AppModel {
	var $validate = array(
        'tgl_bon_biru' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tanggal terima harap diisi'
            ),
        ),
	);

    var $hasMany = array(
        'BonBiruDetail' => array(
            'foreignKey' => 'bon_biru_id',
            'conditions' => array(
                'BonBiruDetail.status' => 1,
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
                'BonBiru.status' => 'DESC',
                'BonBiru.is_canceled' => 'ASC',
                'BonBiru.created' => 'DESC',
                'BonBiru.id' => 'DESC',
            ),
            'contain' => array(),
            'fields' => array(),
        );

        switch ($status) {
            case 'all':
                $default_options['conditions']['BonBiru.status'] = array( 0, 1 );
                break;

            case 'non-active':
                $default_options['conditions']['BonBiru.status'] = 0;
                break;
            
            default:
                $default_options['conditions']['BonBiru.status'] = 1;
                $default_options['conditions']['BonBiru.is_canceled'] = 0;
                break;
        }

        if( !empty($branch_is_plant) ) {
            $default_options['conditions']['BonBiru.branch_id'] = Configure::read('__Site.Branch.Plant.id');
        } else if( !empty($branch) ) {
            $default_options['conditions']['BonBiru.branch_id'] = Configure::read('__Site.config_branch_id');
        }

        return $this->full_merge_options($default_options, $options, $find);
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
                $default_options['conditions']['DATE_FORMAT(BonBiru.tgl_bon_biru, \'%Y-%m-%d\') >='] = $dateFrom;
            }

            if( !empty($dateTo) ) {
                $default_options['conditions']['DATE_FORMAT(BonBiru.tgl_bon_biru, \'%Y-%m-%d\') <='] = $dateTo;
            }
        }
        if(!empty($nodoc)){
            $default_options['conditions']['BonBiru.nodoc LIKE'] = '%'.$nodoc.'%';
        }
        if(!empty($noref)){
            $default_options['conditions']['LPAD(BonBiru.id, 6, 0) LIKE'] = '%'.$noref.'%';
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
                $customers = $this->BonBiruDetail->Ttuj->Customer->getData('list', array(
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
                    $default_options['conditions']['Ttuj.status_bon_biru'] = 'none';
                    break;
                case 'half_receipt':
                    $default_options['conditions']['Ttuj.status_bon_biru'] = 'half';
                    $default_options['conditions']['BonBiru.id <>'] = NULL;
                    break;
                case 'receipt':
                    $default_options['conditions']['Ttuj.status_bon_biru'] = 'full';
                    $default_options['conditions']['BonBiru.id <>'] = NULL;
                    break;
            }
        }

        return $default_options;
    }

    function recoverTtuj ( $details = false ) {
        if( !empty($details) ) {
            foreach ($details as $key => $value) {
                $ttuj_id = $this->filterEmptyField($value, 'BonBiruDetail', 'ttuj_id');
                    
                $this->BonBiruDetail->Ttuj->set('status_bon_biru', 'none');
                $this->BonBiruDetail->Ttuj->id = $ttuj_id;
                $this->BonBiruDetail->Ttuj->save();
            }
        }
    }
}
?>