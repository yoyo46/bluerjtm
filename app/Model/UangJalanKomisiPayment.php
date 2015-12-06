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
		$sql = 'SELECT UangJalanKomisiPayment.*
	    		FROM (
	                '.$sql.'
	            ) AS UangJalanKomisiPayment
				WHERE 1 = 1
				'.$default_conditions;
	    $results = $this->query($sql);

	    return count($results);
	}
}
?>