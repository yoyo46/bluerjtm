<?php
class TtujOutstanding extends AppModel {
	var $name = 'TtujOutstanding';
	public $useTable = false;

	public function paginate($conditions, $fields, $order, $limit, $page = 1, $recursive = null, $extra = array()) {
        $this->Ttuj = ClassRegistry::init('Ttuj');

		$page = !empty($page)?$page:1;
	    $recursive = -1;
	    $default_conditions = array(
        	'TtujOutstanding.status' => 1,
        	'TtujOutstanding.is_draft' => 0,
    	);

		$db = $this->Ttuj->getDataSource();
		$uj1Query = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'TtujOutstanding.*',
		        	"'uang_jalan' AS data_type",
		        	'paid_uang_jalan AS paid_status',
	        	),
		        'table'      => $db->fullTableName($this->Ttuj),
		        'alias'      => 'TtujOutstanding',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => array_merge($default_conditions, array(
		        	'TtujOutstanding.uang_jalan_1 <>' => 0,
	        	)),
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Ttuj
		);

		$db = $this->Ttuj->getDataSource();
		$uj2Query = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'TtujOutstanding.*',
		        	"'uang_jalan_2' AS data_type",
		        	'paid_uang_jalan_2 AS paid_status',
	        	),
		        'table'      => $db->fullTableName($this->Ttuj),
		        'alias'      => 'TtujOutstanding',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => array_merge($default_conditions, array(
		        	'TtujOutstanding.uang_jalan_2 <>' => 0,
	        	)),
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Ttuj
		);

		$db = $this->Ttuj->getDataSource();
		$ujExtraQuery = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'TtujOutstanding.*',
		        	"'uang_jalan_extra' AS data_type",
		        	'paid_uang_jalan_extra AS paid_status',
	        	),
		        'table'      => $db->fullTableName($this->Ttuj),
		        'alias'      => 'TtujOutstanding',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => array_merge($default_conditions, array(
		        	'TtujOutstanding.uang_jalan_extra <>' => 0,
	        	)),
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Ttuj
		);

		$db = $this->Ttuj->getDataSource();
		$commissionQuery = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'TtujOutstanding.*',
		        	"'commission' AS data_type",
		        	'paid_commission AS paid_status',
	        	),
		        'table'      => $db->fullTableName($this->Ttuj),
		        'alias'      => 'TtujOutstanding',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => array_merge($default_conditions, array(
		        	'TtujOutstanding.commission <>' => 0,
	        	)),
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Ttuj
		);

		$db = $this->Ttuj->getDataSource();
		$commissionExtraQuery = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'TtujOutstanding.*',
		        	"'commission_extra' AS data_type",
		        	'paid_commission_extra AS paid_status',
	        	),
		        'table'      => $db->fullTableName($this->Ttuj),
		        'alias'      => 'TtujOutstanding',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => array_merge($default_conditions, array(
		        	'TtujOutstanding.commission_extra <>' => 0,
	        	)),
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Ttuj
		);

		$db = $this->Ttuj->getDataSource();
		$uangKuliQuery = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'TtujOutstanding.*',
		        	"'uang_kuli_muat' AS data_type",
		        	'paid_uang_kuli_muat AS paid_status',
	        	),
		        'table'      => $db->fullTableName($this->Ttuj),
		        'alias'      => 'TtujOutstanding',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => array_merge($default_conditions, array(
		        	'TtujOutstanding.uang_kuli_muat <>' => 0,
	        	)),
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Ttuj
		);

		$db = $this->Ttuj->getDataSource();
		$uangKuliBongkarQuery = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'TtujOutstanding.*',
		        	"'uang_kuli_bongkar' AS data_type",
		        	'paid_uang_kuli_bongkar AS paid_status',
	        	),
		        'table'      => $db->fullTableName($this->Ttuj),
		        'alias'      => 'TtujOutstanding',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => array_merge($default_conditions, array(
		        	'TtujOutstanding.uang_kuli_bongkar <>' => 0,
	        	)),
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Ttuj
		);

		$db = $this->Ttuj->getDataSource();
		$asdpQuery = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'TtujOutstanding.*',
		        	"'asdp' AS data_type",
		        	'paid_asdp AS paid_status',
	        	),
		        'table'      => $db->fullTableName($this->Ttuj),
		        'alias'      => 'TtujOutstanding',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => array_merge($default_conditions, array(
		        	'TtujOutstanding.asdp <>' => 0,
	        	)),
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Ttuj
		);

		$db = $this->Ttuj->getDataSource();
		$uangKawalQuery = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'TtujOutstanding.*',
		        	"'uang_kawal' AS data_type",
		        	'paid_uang_kawal AS paid_status',
	        	),
		        'table'      => $db->fullTableName($this->Ttuj),
		        'alias'      => 'TtujOutstanding',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => array_merge($default_conditions, array(
		        	'TtujOutstanding.uang_kawal <>' => 0,
	        	)),
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Ttuj
		);

		$db = $this->Ttuj->getDataSource();
		$uangKeamananQuery = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'TtujOutstanding.*',
		        	"'uang_keamanan' AS data_type",
		        	'paid_uang_keamanan AS paid_status',
	        	),
		        'table'      => $db->fullTableName($this->Ttuj),
		        'alias'      => 'TtujOutstanding',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => array_merge($default_conditions, array(
		        	'TtujOutstanding.uang_keamanan <>' => 0,
	        	)),
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Ttuj
		);

		$db = $this->Ttuj->getDataSource();
		$conditionsQuery = $db->conditionKeysToString($conditions);
		$default_conditions = '';

		if( !empty($conditionsQuery) ) {
			foreach ($conditionsQuery as $key => $con) {
				$default_conditions .= ' AND '.$con;
			}
		}

		$sql = $uj1Query . ' UNION ' . $uj2Query . ' UNION '. $ujExtraQuery . ' UNION '. $commissionQuery . ' UNION '. $commissionExtraQuery . ' UNION ' . $uangKuliQuery . ' UNION ' . $uangKuliBongkarQuery . ' UNION '. $asdpQuery . ' UNION '. $uangKawalQuery . ' UNION '. $uangKeamananQuery;
		$sql = 'SELECT TtujOutstanding.*
	    		FROM (
	                '.$sql.'
	            ) AS TtujOutstanding
				WHERE 1 = 1
				'.$default_conditions.'
				ORDER BY TtujOutstanding.ttuj_date DESC,
				TtujOutstanding.id DESC
				Limit ' . (($page - 1) * $limit) . ', ' . $limit;
	    $results = $this->query($sql);
	    return $results;
	}

	public function paginateCount($conditions = null, $recursive = 0, $extra = array()) {
		$this->Ttuj = ClassRegistry::init('Ttuj');

		$default_conditions = array(
        	'TtujOutstanding.status' => 1,
        	'TtujOutstanding.is_draft' => 0,
    	);

		$db = $this->Ttuj->getDataSource();
		$uj1Query = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'TtujOutstanding.*',
		        	"'uang_jalan' AS data_type",
		        	'paid_uang_jalan AS paid_status',
	        	),
		        'table'      => $db->fullTableName($this->Ttuj),
		        'alias'      => 'TtujOutstanding',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => array_merge($default_conditions, array(
		        	'TtujOutstanding.uang_jalan_1 <>' => 0,
	        	)),
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Ttuj
		);

		$db = $this->Ttuj->getDataSource();
		$uj2Query = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'TtujOutstanding.*',
		        	"'uang_jalan_2' AS data_type",
		        	'paid_uang_jalan_2 AS paid_status',
	        	),
		        'table'      => $db->fullTableName($this->Ttuj),
		        'alias'      => 'TtujOutstanding',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => array_merge($default_conditions, array(
		        	'TtujOutstanding.uang_jalan_2 <>' => 0,
	        	)),
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Ttuj
		);

		$db = $this->Ttuj->getDataSource();
		$ujExtraQuery = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'TtujOutstanding.*',
		        	"'uang_jalan_extra' AS data_type",
		        	'paid_uang_jalan_extra AS paid_status',
	        	),
		        'table'      => $db->fullTableName($this->Ttuj),
		        'alias'      => 'TtujOutstanding',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => array_merge($default_conditions, array(
		        	'TtujOutstanding.uang_jalan_extra <>' => 0,
	        	)),
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Ttuj
		);

		$db = $this->Ttuj->getDataSource();
		$commissionQuery = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'TtujOutstanding.*',
		        	"'commission' AS data_type",
		        	'paid_commission AS paid_status',
	        	),
		        'table'      => $db->fullTableName($this->Ttuj),
		        'alias'      => 'TtujOutstanding',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => array_merge($default_conditions, array(
		        	'TtujOutstanding.commission <>' => 0,
	        	)),
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Ttuj
		);

		$db = $this->Ttuj->getDataSource();
		$commissionExtraQuery = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'TtujOutstanding.*',
		        	"'commission_extra' AS data_type",
		        	'paid_commission_extra AS paid_status',
	        	),
		        'table'      => $db->fullTableName($this->Ttuj),
		        'alias'      => 'TtujOutstanding',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => array_merge($default_conditions, array(
		        	'TtujOutstanding.commission_extra <>' => 0,
	        	)),
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Ttuj
		);

		$db = $this->Ttuj->getDataSource();
		$uangKuliQuery = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'TtujOutstanding.*',
		        	"'uang_kuli_muat' AS data_type",
		        	'paid_uang_kuli_muat AS paid_status',
	        	),
		        'table'      => $db->fullTableName($this->Ttuj),
		        'alias'      => 'TtujOutstanding',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => array_merge($default_conditions, array(
		        	'TtujOutstanding.uang_kuli_muat <>' => 0,
	        	)),
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Ttuj
		);

		$db = $this->Ttuj->getDataSource();
		$uangKuliBongkarQuery = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'TtujOutstanding.*',
		        	"'uang_kuli_bongkar' AS data_type",
		        	'paid_uang_kuli_bongkar AS paid_status',
	        	),
		        'table'      => $db->fullTableName($this->Ttuj),
		        'alias'      => 'TtujOutstanding',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => array_merge($default_conditions, array(
		        	'TtujOutstanding.uang_kuli_bongkar <>' => 0,
	        	)),
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Ttuj
		);

		$db = $this->Ttuj->getDataSource();
		$asdpQuery = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'TtujOutstanding.*',
		        	"'asdp' AS data_type",
		        	'paid_asdp AS paid_status',
	        	),
		        'table'      => $db->fullTableName($this->Ttuj),
		        'alias'      => 'TtujOutstanding',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => array_merge($default_conditions, array(
		        	'TtujOutstanding.asdp <>' => 0,
	        	)),
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Ttuj
		);

		$db = $this->Ttuj->getDataSource();
		$uangKawalQuery = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'TtujOutstanding.*',
		        	"'uang_kawal' AS data_type",
		        	'paid_uang_kawal AS paid_status',
	        	),
		        'table'      => $db->fullTableName($this->Ttuj),
		        'alias'      => 'TtujOutstanding',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => array_merge($default_conditions, array(
		        	'TtujOutstanding.uang_kawal <>' => 0,
	        	)),
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Ttuj
		);

		$db = $this->Ttuj->getDataSource();
		$uangKeamananQuery = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'TtujOutstanding.*',
		        	"'uang_keamanan' AS data_type",
		        	'paid_uang_keamanan AS paid_status',
	        	),
		        'table'      => $db->fullTableName($this->Ttuj),
		        'alias'      => 'TtujOutstanding',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => array_merge($default_conditions, array(
		        	'TtujOutstanding.uang_keamanan <>' => 0,
	        	)),
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Ttuj
		);

		$db = $this->Ttuj->getDataSource();
		$conditionsQuery = $db->conditionKeysToString($conditions);
		$default_conditions = '';

		if( !empty($conditionsQuery) ) {
			foreach ($conditionsQuery as $key => $con) {
				$default_conditions .= ' AND '.$con;
			}
		}

		$sql = $uj1Query . ' UNION ' . $uj2Query . ' UNION '. $ujExtraQuery . ' UNION '. $commissionQuery . ' UNION '. $commissionExtraQuery . ' UNION ' . $uangKuliQuery . ' UNION ' . $uangKuliBongkarQuery . ' UNION '. $asdpQuery . ' UNION '. $uangKawalQuery . ' UNION '. $uangKeamananQuery;
		$sql = 'SELECT TtujOutstanding.*
	    		FROM (
	                '.$sql.'
	            ) AS TtujOutstanding
				WHERE 1 = 1
				'.$default_conditions;
	    $results = $this->query($sql);

	    return count($results);
	}

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
        $status = !empty($data['named']['status'])?$data['named']['status']:false;

        if( !empty($dateFrom) || !empty($dateTo) ) {
            if( !empty($dateFrom) ) {
                $default_options['conditions']['DATE_FORMAT(TtujOutstanding.ttuj_date, \'%Y-%m-%d\') >='] = $dateFrom;
            }

            if( !empty($dateTo) ) {
                $default_options['conditions']['DATE_FORMAT(TtujOutstanding.ttuj_date, \'%Y-%m-%d\') <='] = $dateTo;
            }
        }
        if(!empty($nopol)){
            if( $type == 2 ) {
                $default_options['conditions']['TtujOutstanding.truck_id'] = $nopol;
            } else {
                $default_options['conditions']['TtujOutstanding.nopol LIKE'] = '%'.$nopol.'%';
            }
        }
        if(!empty($company)){
            $default_options['conditions']['Truck.company_id'] = $company;
            $default_options['contain'][] = 'Truck';
        }
        if(!empty($nodoc)){
            $default_options['conditions']['TtujOutstanding.no_ttuj LIKE'] = '%'.$nodoc.'%';
        }
        if(!empty($driver)){
            $default_options['conditions']['TtujOutstanding.driver_name LIKE'] = '%'.$driver.'%';
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
            $default_options['conditions']['TtujOutstanding.customer_id'] = $customers;
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

        if( !empty($typeOpt) ) {
            $default_options['conditions']['TtujOutstanding.data_type'] = $typeOpt;
        }

        if(!empty($fromcity)){
            $default_options['conditions']['TtujOutstanding.from_city_id'] = $fromcity;
        }
        if(!empty($tocity)){
            $default_options['conditions']['TtujOutstanding.to_city_id'] = $tocity;
        }
        if(!empty($note)){
            $default_options['conditions']['TtujOutstanding.note LIKE'] = '%'.$note.'%';
        }
        if(!empty($status)){
            switch ($status) {
                case 'paid':
                        $default_options['conditions']['TtujOutstanding.paid_status'] = 'full';
                    break;
                case 'unpaid':
                        $default_options['conditions']['TtujOutstanding.paid_status'] = 'none';
                    break;
            }
        }
        
        return $default_options;
    }
}
?>