<?php
class UangJalanKomisiPayment extends AppModel {
	var $name = 'UangJalanKomisiPayment';
	public $useTable = false;

	public function paginate($conditions, $fields, $order, $limit, $page = 1, $recursive = null, $extra = array()) {
        $this->Ttuj = ClassRegistry::init('Ttuj');

        $current_branch_id = Configure::read('__Site.config_branch_id');
        $branch_city_id = Configure::read('__Site.Branch.City.id');
        $head_office = Configure::read('__Site.config_branch_head_office');

		$page = !empty($page)?$page:1;
	    $recursive = -1;
	    $default_conditions = array(
        	'UangJalanKomisiPayment.status' => 1,
        	'UangJalanKomisiPayment.is_draft' => 0,
        	'UangJalanKomisiPayment.is_rjtm' => 1,
    	);
    	$condition_uj2 = array();
    	$condition_branch = array();

        if( empty($head_office) ) {
            $condition_branch['UangJalanKomisiPayment.branch_id'] = Configure::read('__Site.config_branch_id');
	    	$condition_uj2 = array(
	    		'OR' => array(
	        		'UangJalanKomisiPayment.to_city_id' => $branch_city_id,
	        		'UangJalanKomisiPayment.branch_id' => $current_branch_id,
	    		),
			);
        }

		$db = $this->Ttuj->getDataSource();
		$uj1Query = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'UangJalanKomisiPayment.*',
		        	"'uang_jalan' AS data_type",
	        	),
		        'table'      => $db->fullTableName($this->Ttuj),
		        'alias'      => 'UangJalanKomisiPayment',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => array_merge($default_conditions, array(
		        	'UangJalanKomisiPayment.uang_jalan_1 <>' => 0,
		        	'UangJalanKomisiPayment.paid_uang_jalan <>' => 'full',
	        	))+$condition_branch,
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Ttuj
		);

		$db = $this->Ttuj->getDataSource();
		$uj2Query = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'UangJalanKomisiPayment.*',
		        	"'uang_jalan_2' AS data_type",
	        	),
		        'table'      => $db->fullTableName($this->Ttuj),
		        'alias'      => 'UangJalanKomisiPayment',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => array_merge($default_conditions, array(
		        	'UangJalanKomisiPayment.uang_jalan_2 <>' => 0,
		        	'UangJalanKomisiPayment.paid_uang_jalan_2 <>' => 'full',
	        	))+$condition_uj2,
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Ttuj
		);

		$db = $this->Ttuj->getDataSource();
		$ujExtraQuery = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'UangJalanKomisiPayment.*',
		        	"'uang_jalan_extra' AS data_type",
	        	),
		        'table'      => $db->fullTableName($this->Ttuj),
		        'alias'      => 'UangJalanKomisiPayment',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => array_merge($default_conditions, array(
		        	'UangJalanKomisiPayment.uang_jalan_extra <>' => 0,
		        	'UangJalanKomisiPayment.paid_uang_jalan_extra <>' => 'full',
	        	))+$condition_branch,
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Ttuj
		);

		$db = $this->Ttuj->getDataSource();
		$commissionQuery = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'UangJalanKomisiPayment.*',
		        	"'commission' AS data_type",
	        	),
		        'table'      => $db->fullTableName($this->Ttuj),
		        'alias'      => 'UangJalanKomisiPayment',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => array_merge($default_conditions, array(
		        	'UangJalanKomisiPayment.commission <>' => 0,
		        	'UangJalanKomisiPayment.paid_commission <>' => 'full',
	        	))+$condition_branch,
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Ttuj
		);

		$db = $this->Ttuj->getDataSource();
		$commissionExtraQuery = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'UangJalanKomisiPayment.*',
		        	"'commission_extra' AS data_type",
	        	),
		        'table'      => $db->fullTableName($this->Ttuj),
		        'alias'      => 'UangJalanKomisiPayment',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => array_merge($default_conditions, array(
		        	'UangJalanKomisiPayment.commission_extra <>' => 0,
		        	'UangJalanKomisiPayment.paid_commission_extra <>' => 'full',
	        	))+$condition_branch,
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Ttuj
		);

		$db = $this->Ttuj->getDataSource();
		$uangKuliQuery = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'UangJalanKomisiPayment.*',
		        	"'uang_kuli_muat' AS data_type",
	        	),
		        'table'      => $db->fullTableName($this->Ttuj),
		        'alias'      => 'UangJalanKomisiPayment',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => array_merge($default_conditions, array(
		        	'UangJalanKomisiPayment.uang_kuli_muat <>' => 0,
		        	'UangJalanKomisiPayment.paid_uang_kuli_muat <>' => 'full',
	        	))+$condition_branch,
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Ttuj
		);

		$db = $this->Ttuj->getDataSource();
		$uangKuliBongkarQuery = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'UangJalanKomisiPayment.*',
		        	"'uang_kuli_bongkar' AS data_type",
	        	),
		        'table'      => $db->fullTableName($this->Ttuj),
		        'alias'      => 'UangJalanKomisiPayment',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => array_merge($default_conditions, array(
		        	'UangJalanKomisiPayment.uang_kuli_bongkar <>' => 0,
		        	'UangJalanKomisiPayment.paid_uang_kuli_bongkar <>' => 'full',
	        	))+$condition_branch,
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Ttuj
		);

		$db = $this->Ttuj->getDataSource();
		$asdpQuery = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'UangJalanKomisiPayment.*',
		        	"'asdp' AS data_type",
	        	),
		        'table'      => $db->fullTableName($this->Ttuj),
		        'alias'      => 'UangJalanKomisiPayment',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => array_merge($default_conditions, array(
		        	'UangJalanKomisiPayment.asdp <>' => 0,
		        	'UangJalanKomisiPayment.paid_asdp <>' => 'full',
	        	))+$condition_branch,
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Ttuj
		);

		$db = $this->Ttuj->getDataSource();
		$uangKawalQuery = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'UangJalanKomisiPayment.*',
		        	"'uang_kawal' AS data_type",
	        	),
		        'table'      => $db->fullTableName($this->Ttuj),
		        'alias'      => 'UangJalanKomisiPayment',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => array_merge($default_conditions, array(
		        	'UangJalanKomisiPayment.uang_kawal <>' => 0,
		        	'UangJalanKomisiPayment.paid_uang_kawal <>' => 'full',
	        	))+$condition_branch,
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Ttuj
		);

		$db = $this->Ttuj->getDataSource();
		$uangKeamananQuery = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'UangJalanKomisiPayment.*',
		        	"'uang_keamanan' AS data_type",
	        	),
		        'table'      => $db->fullTableName($this->Ttuj),
		        'alias'      => 'UangJalanKomisiPayment',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => array_merge($default_conditions, array(
		        	'UangJalanKomisiPayment.uang_keamanan <>' => 0,
		        	'UangJalanKomisiPayment.paid_uang_keamanan <>' => 'full',
	        	))+$condition_branch,
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
		$sql = 'SELECT UangJalanKomisiPayment.*
	    		FROM (
	                '.$sql.'
	            ) AS UangJalanKomisiPayment
				WHERE 1 = 1
				'.$default_conditions.'
				ORDER BY UangJalanKomisiPayment.ttuj_date DESC,
				UangJalanKomisiPayment.id DESC
				Limit ' . (($page - 1) * $limit) . ', ' . $limit;
	    $results = $this->query($sql);
	    return $results;
	}

	public function paginateCount($conditions = null, $recursive = 0, $extra = array()) {
		$this->Ttuj = ClassRegistry::init('Ttuj');

        $current_branch_id = Configure::read('__Site.config_branch_id');
        $branch_city_id = Configure::read('__Site.Branch.City.id');
        $head_office = Configure::read('__Site.config_branch_head_office');
        
		$default_conditions = array(
        	'UangJalanKomisiPayment.status' => 1,
        	'UangJalanKomisiPayment.is_draft' => 0,
        	'UangJalanKomisiPayment.is_rjtm' => 1,
    	);
    	$condition_uj2 = array();
    	$condition_branch = array();

        if( empty($head_office) ) {
            $condition_branch['UangJalanKomisiPayment.branch_id'] = Configure::read('__Site.config_branch_id');
	    	$condition_uj2 = array(
	    		'OR' => array(
	        		'UangJalanKomisiPayment.to_city_id' => $branch_city_id,
	        		'UangJalanKomisiPayment.branch_id' => $current_branch_id,
	    		),
			);
        }

		$db = $this->Ttuj->getDataSource();
		$uj1Query = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'UangJalanKomisiPayment.*',
		        	"'uang_jalan' AS data_type",
	        	),
		        'table'      => $db->fullTableName($this->Ttuj),
		        'alias'      => 'UangJalanKomisiPayment',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => array_merge($default_conditions, array(
		        	'UangJalanKomisiPayment.uang_jalan_1 <>' => 0,
		        	'UangJalanKomisiPayment.paid_uang_jalan <>' => 'full',
	        	))+$condition_branch,
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Ttuj
		);

		$db = $this->Ttuj->getDataSource();
		$uj2Query = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'UangJalanKomisiPayment.*',
		        	"'uang_jalan_2' AS data_type",
	        	),
		        'table'      => $db->fullTableName($this->Ttuj),
		        'alias'      => 'UangJalanKomisiPayment',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => array_merge($default_conditions, array(
		        	'UangJalanKomisiPayment.uang_jalan_2 <>' => 0,
		        	'UangJalanKomisiPayment.paid_uang_jalan_2 <>' => 'full',
	        	))+$condition_uj2,
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Ttuj
		);

		$db = $this->Ttuj->getDataSource();
		$ujExtraQuery = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'UangJalanKomisiPayment.*',
		        	"'uang_jalan_extra' AS data_type",
	        	),
		        'table'      => $db->fullTableName($this->Ttuj),
		        'alias'      => 'UangJalanKomisiPayment',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => array_merge($default_conditions, array(
		        	'UangJalanKomisiPayment.uang_jalan_extra <>' => 0,
		        	'UangJalanKomisiPayment.paid_uang_jalan_extra <>' => 'full',
	        	))+$condition_branch,
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Ttuj
		);

		$db = $this->Ttuj->getDataSource();
		$commissionQuery = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'UangJalanKomisiPayment.*',
		        	"'commission' AS data_type",
	        	),
		        'table'      => $db->fullTableName($this->Ttuj),
		        'alias'      => 'UangJalanKomisiPayment',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => array_merge($default_conditions, array(
		        	'UangJalanKomisiPayment.commission <>' => 0,
		        	'UangJalanKomisiPayment.paid_commission <>' => 'full',
	        	))+$condition_branch,
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Ttuj
		);

		$db = $this->Ttuj->getDataSource();
		$commissionExtraQuery = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'UangJalanKomisiPayment.*',
		        	"'commission_extra' AS data_type",
	        	),
		        'table'      => $db->fullTableName($this->Ttuj),
		        'alias'      => 'UangJalanKomisiPayment',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => array_merge($default_conditions, array(
		        	'UangJalanKomisiPayment.commission_extra <>' => 0,
		        	'UangJalanKomisiPayment.paid_commission_extra <>' => 'full',
	        	))+$condition_branch,
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Ttuj
		);

		$db = $this->Ttuj->getDataSource();
		$uangKuliQuery = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'UangJalanKomisiPayment.*',
		        	"'uang_kuli_muat' AS data_type",
	        	),
		        'table'      => $db->fullTableName($this->Ttuj),
		        'alias'      => 'UangJalanKomisiPayment',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => array_merge($default_conditions, array(
		        	'UangJalanKomisiPayment.uang_kuli_muat <>' => 0,
		        	'UangJalanKomisiPayment.paid_uang_kuli_muat <>' => 'full',
	        	))+$condition_branch,
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Ttuj
		);

		$db = $this->Ttuj->getDataSource();
		$uangKuliBongkarQuery = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'UangJalanKomisiPayment.*',
		        	"'uang_kuli_bongkar' AS data_type",
	        	),
		        'table'      => $db->fullTableName($this->Ttuj),
		        'alias'      => 'UangJalanKomisiPayment',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => array_merge($default_conditions, array(
		        	'UangJalanKomisiPayment.uang_kuli_bongkar <>' => 0,
		        	'UangJalanKomisiPayment.paid_uang_kuli_bongkar <>' => 'full',
	        	))+$condition_branch,
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Ttuj
		);

		$db = $this->Ttuj->getDataSource();
		$asdpQuery = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'UangJalanKomisiPayment.*',
		        	"'asdp' AS data_type",
	        	),
		        'table'      => $db->fullTableName($this->Ttuj),
		        'alias'      => 'UangJalanKomisiPayment',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => array_merge($default_conditions, array(
		        	'UangJalanKomisiPayment.asdp <>' => 0,
		        	'UangJalanKomisiPayment.paid_asdp <>' => 'full',
	        	))+$condition_branch,
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Ttuj
		);

		$db = $this->Ttuj->getDataSource();
		$uangKawalQuery = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'UangJalanKomisiPayment.*',
		        	"'uang_kawal' AS data_type",
	        	),
		        'table'      => $db->fullTableName($this->Ttuj),
		        'alias'      => 'UangJalanKomisiPayment',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => array_merge($default_conditions, array(
		        	'UangJalanKomisiPayment.uang_kawal <>' => 0,
		        	'UangJalanKomisiPayment.paid_uang_kawal <>' => 'full',
	        	))+$condition_branch,
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Ttuj
		);

		$db = $this->Ttuj->getDataSource();
		$uangKeamananQuery = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'UangJalanKomisiPayment.*',
		        	"'uang_keamanan' AS data_type",
	        	),
		        'table'      => $db->fullTableName($this->Ttuj),
		        'alias'      => 'UangJalanKomisiPayment',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => array_merge($default_conditions, array(
		        	'UangJalanKomisiPayment.uang_keamanan <>' => 0,
		        	'UangJalanKomisiPayment.paid_uang_keamanan <>' => 'full',
	        	))+$condition_branch,
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
		$sql = 'SELECT COUNT(UangJalanKomisiPayment.id) AS cnt
	    		FROM (
	                '.$sql.'
	            ) AS UangJalanKomisiPayment
				WHERE 1 = 1
				'.$default_conditions;
	    $results = $this->query($sql);

	    return !empty($results[0][0]['cnt'])?$results[0][0]['cnt']:0;
	}

    public function _callRefineParams( $data = '', $default_options = false ) {
        $dateFrom = !empty($data['named']['DateFrom'])?$data['named']['DateFrom']:false;
        $dateTo = !empty($data['named']['DateTo'])?$data['named']['DateTo']:false;
        $nopol = !empty($data['named']['nopol'])?$data['named']['nopol']:false;
        $type = !empty($data['named']['type'])?$data['named']['type']:1;
        $driver = !empty($data['named']['driver'])?$data['named']['driver']:false;
        $nodoc = !empty($data['named']['nodoc'])?$data['named']['nodoc']:false;
        $customer = !empty($data['named']['customer'])?$data['named']['customer']:false;
        $from_city = !empty($data['named']['from_city'])?$data['named']['from_city']:false;
        $to_city = !empty($data['named']['to_city'])?$data['named']['to_city']:false;
        $note = !empty($data['named']['note'])?$data['named']['note']:false;
        $uang_jalan_1 = !empty($data['named']['uang_jalan_1'])?$data['named']['uang_jalan_1']:false;
        $uang_jalan_2 = !empty($data['named']['uang_jalan_2'])?$data['named']['uang_jalan_2']:false;
        $uang_jalan_extra = !empty($data['named']['uang_jalan_extra'])?$data['named']['uang_jalan_extra']:false;
        $commission = !empty($data['named']['commission'])?$data['named']['commission']:false;
        $commission_extra = !empty($data['named']['commission_extra'])?$data['named']['commission_extra']:false;
        $uang_kuli_muat = !empty($data['named']['uang_kuli_muat'])?$data['named']['uang_kuli_muat']:false;
        $uang_kuli_bongkar = !empty($data['named']['uang_kuli_bongkar'])?$data['named']['uang_kuli_bongkar']:false;
        $asdp = !empty($data['named']['asdp'])?$data['named']['asdp']:false;
        $uang_kawal = !empty($data['named']['uang_kawal'])?$data['named']['uang_kawal']:false;
        $uang_keamanan = !empty($data['named']['uang_keamanan'])?$data['named']['uang_keamanan']:false;

        if( !empty($dateFrom) || !empty($dateTo) ) {
            if( !empty($dateFrom) ) {
                $default_options['conditions']['DATE_FORMAT(UangJalanKomisiPayment.ttuj_date, \'%Y-%m-%d\') >='] = $dateFrom;
            }

            if( !empty($dateTo) ) {
                $default_options['conditions']['DATE_FORMAT(UangJalanKomisiPayment.ttuj_date, \'%Y-%m-%d\') <='] = $dateTo;
            }
        }
        if(!empty($nodoc)){
            $default_options['conditions']['UangJalanKomisiPayment.no_ttuj LIKE'] = '%'.$nodoc.'%';
        }
        if(!empty($nopol)){
            if( $type == 2 ) {
                $default_options['conditions']['UangJalanKomisiPayment.truck_id'] = $nopol;
            } else {
                $default_options['conditions']['UangJalanKomisiPayment.nopol LIKE'] = '%'.$nopol.'%';
            }
        }
        if(!empty($driver)){
            $this->Driver = ClassRegistry::init('Driver');
            $driverId = $this->Driver->getData('list', array(
            	'conditions' => array(
            		'Driver.name LIKE' => '%'.$driver.'%',
        		),
        		'fields' => array(
        			'Driver.id', 'Driver.id'
    			),
        	), true, array(
        		'branch' => false,
        	));
            $default_options['conditions']['AND']['OR'] = array(
            	'UangJalanKomisiPayment.driver_id' => $driverId,
            	'UangJalanKomisiPayment.driver_penganti_id' => $driverId,
        	);
        }
        if(!empty($customer)){
            $this->Customer = ClassRegistry::init('Customer');
            $customers = $this->Customer->getData('list', array(
            	'conditions' => array(
            		'Customer.customer_name_code LIKE' => '%'.$customer.'%',
        		),
        		'fields' => array(
        			'Customer.id', 'Customer.id'
    			),
        	), true, array(
                'status' => 'all',
            ));
            $default_options['conditions']['UangJalanKomisiPayment.customer_id'] = $customers;
        }
        if(!empty($from_city)){
            $default_options['conditions']['UangJalanKomisiPayment.from_city_id'] = $from_city;
        }
        if(!empty($to_city)){
            $default_options['conditions']['UangJalanKomisiPayment.to_city_id'] = $to_city;
        }

        if(!empty($uang_jalan_1) || !empty($uang_jalan_2) || !empty($uang_jalan_extra) || !empty($commission) || !empty($commission_extra) || !empty($uang_kuli_muat) || !empty($uang_kuli_bongkar) || !empty($asdp) || !empty($uang_kawal) || !empty($uang_keamanan)){
        	$idx = 0;
    	}

        if(!empty($uang_jalan_1)){
    		$default_options['conditions']['OR'][$idx]['UangJalanKomisiPayment.data_type'] = 'uang_jalan';
    		$idx++;
    	}
        if(!empty($uang_jalan_2)){
    		$default_options['conditions']['OR'][$idx]['UangJalanKomisiPayment.data_type'] = 'uang_jalan_2';
    		$idx++;
    	}
        if(!empty($uang_jalan_extra)){
    		$default_options['conditions']['OR'][$idx]['UangJalanKomisiPayment.data_type'] = 'uang_jalan_extra';
    		$idx++;
    	}
        if(!empty($commission)){
    		$default_options['conditions']['OR'][$idx]['UangJalanKomisiPayment.data_type'] = 'commission';
    		$idx++;
    	}
        if(!empty($commission_extra)){
    		$default_options['conditions']['OR'][$idx]['UangJalanKomisiPayment.data_type'] = 'commission_extra';
    		$idx++;
    	}
        if(!empty($uang_kuli_muat)){
    		$default_options['conditions']['OR'][$idx]['UangJalanKomisiPayment.data_type'] = 'uang_kuli_muat';
    		$idx++;
    	}
        if(!empty($uang_kuli_bongkar)){
    		$default_options['conditions']['OR'][$idx]['UangJalanKomisiPayment.data_type'] = 'uang_kuli_bongkar';
    		$idx++;
    	}
        if(!empty($asdp)){
    		$default_options['conditions']['OR'][$idx]['UangJalanKomisiPayment.data_type'] = 'asdp';
    		$idx++;
    	}
        if(!empty($uang_kawal)){
    		$default_options['conditions']['OR'][$idx]['UangJalanKomisiPayment.data_type'] = 'uang_kawal';
    		$idx++;
    	}
        if(!empty($uang_keamanan)){
    		$default_options['conditions']['OR'][$idx]['UangJalanKomisiPayment.data_type'] = 'uang_keamanan';
    		$idx++;
        }
        if(!empty($note)){
            $default_options['conditions']['UangJalanKomisiPayment.note LIKE '] = '%'.$note.'%';
        }

        return $default_options;
    }
}
?>