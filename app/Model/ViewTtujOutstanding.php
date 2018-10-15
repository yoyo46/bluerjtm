<?php
class ViewTtujOutstanding extends AppModel {
	var $name = 'ViewTtujOutstanding';

    public function _callRefineParams( $data = '', $default_options = false ) {
        $dateFrom = !empty($data['named']['DateFrom'])?$data['named']['DateFrom']:false;
        $dateTo = !empty($data['named']['DateTo'])?$data['named']['DateTo']:false;
        $nopol = !empty($data['named']['nopol'])?$data['named']['nopol']:false;
        $type = !empty($data['named']['type'])?$data['named']['type']:1;
        $company = !empty($data['named']['company'])?$data['named']['company']:false;
        $nodoc = !empty($data['named']['nodoc'])?$data['named']['nodoc']:false;
        $to_city = !empty($data['named']['to_city'])?$data['named']['to_city']:false;
        $customer = !empty($data['named']['customer'])?$data['named']['customer']:false;
        $driver = !empty($data['named']['driver'])?$data['named']['driver']:false;
        $fromcity = !empty($data['named']['fromcity'])?$data['named']['fromcity']:false;
        $tocity = !empty($data['named']['tocity'])?$data['named']['tocity']:false;
        $note = !empty($data['named']['note'])?$data['named']['note']:false;

        $uj1 = !empty($data['named']['uj1'])?$data['named']['uj1']:false;
        $uj2 = !empty($data['named']['uj2'])?$data['named']['uj2']:false;
        $uje = !empty($data['named']['uje'])?$data['named']['uje']:false;
        $com = !empty($data['named']['com'])?$data['named']['com']:false;
        $come = !empty($data['named']['come'])?$data['named']['come']:false;
        $kuli_muat = !empty($data['named']['kuli_muat'])?$data['named']['kuli_muat']:false;
        $kuli_bongkar = !empty($data['named']['kuli_bongkar'])?$data['named']['kuli_bongkar']:false;
        $asdp = !empty($data['named']['asdp'])?$data['named']['asdp']:false;
        $uang_kawal = !empty($data['named']['uang_kawal'])?$data['named']['uang_kawal']:false;
        $uang_keamanan = !empty($data['named']['uang_keamanan'])?$data['named']['uang_keamanan']:false;

        $status = !empty($data['named']['status'])?$data['named']['status']:false;

        if( !empty($dateFrom) || !empty($dateTo) ) {
            if( !empty($dateFrom) ) {
                $default_options['conditions']['DATE_FORMAT(ViewTtujOutstanding.ttuj_date, \'%Y-%m-%d\') >='] = $dateFrom;
            }

            if( !empty($dateTo) ) {
                $default_options['conditions']['DATE_FORMAT(ViewTtujOutstanding.ttuj_date, \'%Y-%m-%d\') <='] = $dateTo;
            }
        }
        if(!empty($nopol)){
            if( $type == 2 ) {
                $default_options['conditions']['ViewTtujOutstanding.truck_id'] = $nopol;
            } else {
                $default_options['conditions']['ViewTtujOutstanding.nopol LIKE'] = '%'.$nopol.'%';
            }
        }
        if(!empty($company)){
            $default_options['conditions']['Truck.company_id'] = $company;
            $default_options['contain'][] = 'Truck';
        }
        if(!empty($nodoc)){
            $default_options['conditions']['ViewTtujOutstanding.no_ttuj LIKE'] = '%'.$nodoc.'%';
        }
        if(!empty($driver)){
            $default_options['conditions']['ViewTtujOutstanding.driver_name LIKE'] = '%'.$driver.'%';
        }
        if(!empty($customer)){
            $customers = $this->Customer->getData('list', array(
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
            $default_options['conditions']['ViewTtujOutstanding.customer_id'] = $customers;
        }

        $typeOpt = array();

        if(!empty($uj1)){
            $typeOpt[] = 'uang_jalan';
        }
        if(!empty($uj2)){
            $typeOpt[] = 'uang_jalan_2';
        }
        if(!empty($uje)){
            $typeOpt[] = 'uang_jalan_extra';
        }
        if(!empty($com)){
            $typeOpt[] = 'commission';
        }
        if(!empty($come)){
            $typeOpt[] = 'commission_extra';
        }
        if(!empty($kuli_muat)){
            $typeOpt[] = 'uang_kuli_muat';
        }
        if(!empty($kuli_bongkar)){
            $typeOpt[] = 'uang_kuli_bongkar';
        }
        if(!empty($asdp)){
            $typeOpt[] = 'asdp';
        }
        if(!empty($uang_kawal)){
            $typeOpt[] = 'uang_kawal';
        }
        if(!empty($uang_keamanan)){
            $typeOpt[] = 'uang_keamanan';
        }

        if( !empty($typeOpt) ) {
            $default_options['conditions']['ViewTtujOutstanding.data_type'] = $typeOpt;
        }

        if(!empty($fromcity)){
            $default_options['conditions']['ViewTtujOutstanding.from_city_id'] = $fromcity;
        }
        if(!empty($tocity)){
            $default_options['conditions']['ViewTtujOutstanding.to_city_id'] = $tocity;
        }
        if(!empty($note)){
            $default_options['conditions']['ViewTtujOutstanding.note LIKE'] = '%'.$note.'%';
        }
        if(!empty($status)){
            switch ($status) {
                case 'paid':
                        $default_options['conditions']['ViewTtujOutstanding.paid_status'] = 'full';
                    break;
                case 'unpaid':
                        $default_options['conditions']['ViewTtujOutstanding.paid_status'] = 'none';
                    break;
            }
        }
        
        return $default_options;
    }
}
?>