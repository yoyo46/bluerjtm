<?php
class DocumentTruck extends AppModel {
	var $name = 'DocumentTruck';
	public $useTable = false;

	var $hasMany = array(
		'DocumentPaymentDetail' => array(
			'className' => 'DocumentPaymentDetail',
			'foreignKey' => 'document_id',
		)
	);

	public function paginate($conditions, $fields, $order, $limit, $page = 1, $recursive = null, $extra = array()) {
        $this->Truck = ClassRegistry::init('Truck');

        $current_branch_id = Configure::read('__Site.config_branch_id');
        $branch_city_id = Configure::read('__Site.Branch.City.id');
        $head_office = Configure::read('__Site.config_branch_head_office');

		$page = !empty($page)?$page:1;
	    $recursive = -1;
	    $default_conditions = array(
        	'DocumentTruck.status' => 1,
        	'DocumentTruck.paid <>' => 'full',
    	);

        if( empty($head_office) ) {
            $default_conditions['DocumentTruck.branch_id'] = Configure::read('__Site.config_branch_id');
        }

		$db = $this->Truck->Kir->getDataSource();
		$kirQuery = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'DocumentTruck.id',
		        	'DocumentTruck.truck_id',
		        	'DocumentTruck.no_pol',
		        	'DocumentTruck.tgl_kir AS document_date',
		        	'DocumentTruck.from_date',
		        	'DocumentTruck.to_date',
		        	'DocumentTruck.price_estimate',
		        	'DocumentTruck.price',
		        	'DocumentTruck.denda',
		        	'DocumentTruck.biaya_lain',
		        	'DocumentTruck.note',
		        	"'kir' AS data_type",
	        	),
		        'table'      => $db->fullTableName($this->Truck->Kir),
		        'alias'      => 'DocumentTruck',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => $default_conditions,
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Truck->Kir
		);

		$db = $this->Truck->Stnk->getDataSource();
		$stnkQuery = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'DocumentTruck.id',
		        	'DocumentTruck.truck_id',
		        	'DocumentTruck.no_pol',
		        	'DocumentTruck.tgl_bayar AS document_date',
		        	'DocumentTruck.from_date',
		        	'DocumentTruck.to_date',
		        	'DocumentTruck.price_estimate',
		        	'DocumentTruck.price',
		        	'DocumentTruck.denda',
		        	'DocumentTruck.biaya_lain',
		        	'DocumentTruck.note',
		        	"CASE WHEN is_change_plat = 1 THEN 'stnk_5_thn' ELSE 'stnk' END AS data_type",
	        	),
		        'table'      => $db->fullTableName($this->Truck->Stnk),
		        'alias'      => 'DocumentTruck',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => $default_conditions,
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Truck->Stnk
		);

		$db = $this->Truck->Siup->getDataSource();
		$siupQuery = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'DocumentTruck.id',
		        	'DocumentTruck.truck_id',
		        	'DocumentTruck.no_pol',
		        	'DocumentTruck.tgl_siup AS document_date',
		        	'DocumentTruck.from_date',
		        	'DocumentTruck.to_date',
		        	'DocumentTruck.price_estimate',
		        	'DocumentTruck.price',
		        	'DocumentTruck.denda',
		        	'DocumentTruck.biaya_lain',
		        	'DocumentTruck.note',
		        	"'siup' AS data_type",
	        	),
		        'table'      => $db->fullTableName($this->Truck->Siup),
		        'alias'      => 'DocumentTruck',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => $default_conditions,
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Truck->Siup
		);

		$db = $this->Truck->getDataSource();
		$conditionsQuery = $db->conditionKeysToString($conditions);
		$default_conditions = '';

		if( !empty($conditionsQuery) ) {
			foreach ($conditionsQuery as $key => $con) {
				$default_conditions .= ' AND '.$con;
			}
		}

		$sql = $kirQuery . ' UNION ' . $stnkQuery . ' UNION '. $siupQuery;
		$sql = 'SELECT DocumentTruck.*
	    		FROM (
	                '.$sql.'
	            ) AS DocumentTruck
				WHERE 1 = 1
				'.$default_conditions.'
				ORDER BY DocumentTruck.document_date DESC,
				DocumentTruck.id DESC
				Limit ' . (($page - 1) * $limit) . ', ' . $limit;
	    $results = $this->query($sql);
	    return $results;
	}

	public function paginateCount($conditions = null, $recursive = 0, $extra = array()) {
		$this->Truck = ClassRegistry::init('Truck');

        $current_branch_id = Configure::read('__Site.config_branch_id');
        $branch_city_id = Configure::read('__Site.Branch.City.id');
        $head_office = Configure::read('__Site.config_branch_head_office');

		$page = !empty($page)?$page:1;
	    $recursive = -1;
	    $default_conditions = array(
        	'DocumentTruck.status' => 1,
        	'DocumentTruck.paid <>' => 'full',
    	);

        if( empty($head_office) ) {
            $default_conditions['DocumentTruck.branch_id'] = Configure::read('__Site.config_branch_id');
        }

		$db = $this->Truck->Kir->getDataSource();
		$kirQuery = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'DocumentTruck.id',
		        	'DocumentTruck.truck_id',
		        	'DocumentTruck.no_pol',
		        	'DocumentTruck.tgl_kir AS document_date',
		        	'DocumentTruck.from_date',
		        	'DocumentTruck.to_date',
		        	'DocumentTruck.price_estimate',
		        	'DocumentTruck.price',
		        	'DocumentTruck.denda',
		        	'DocumentTruck.biaya_lain',
		        	'DocumentTruck.note',
		        	"'kir' AS data_type",
	        	),
		        'table'      => $db->fullTableName($this->Truck->Kir),
		        'alias'      => 'DocumentTruck',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => $default_conditions,
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Truck->Kir
		);

		$db = $this->Truck->Stnk->getDataSource();
		$stnkQuery = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'DocumentTruck.id',
		        	'DocumentTruck.truck_id',
		        	'DocumentTruck.no_pol',
		        	'DocumentTruck.tgl_bayar AS document_date',
		        	'DocumentTruck.from_date',
		        	'DocumentTruck.to_date',
		        	'DocumentTruck.price_estimate',
		        	'DocumentTruck.price',
		        	'DocumentTruck.denda',
		        	'DocumentTruck.biaya_lain',
		        	'DocumentTruck.note',
		        	"CASE WHEN is_change_plat = 1 THEN 'stnk' ELSE 'stnk_5_thn' END AS data_type",
	        	),
		        'table'      => $db->fullTableName($this->Truck->Stnk),
		        'alias'      => 'DocumentTruck',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => $default_conditions,
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Truck->Stnk
		);

		$db = $this->Truck->Siup->getDataSource();
		$siupQuery = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'DocumentTruck.id',
		        	'DocumentTruck.truck_id',
		        	'DocumentTruck.no_pol',
		        	'DocumentTruck.tgl_siup AS document_date',
		        	'DocumentTruck.from_date',
		        	'DocumentTruck.to_date',
		        	'DocumentTruck.price_estimate',
		        	'DocumentTruck.price',
		        	'DocumentTruck.denda',
		        	'DocumentTruck.biaya_lain',
		        	'DocumentTruck.note',
		        	"'siup' AS data_type",
	        	),
		        'table'      => $db->fullTableName($this->Truck->Siup),
		        'alias'      => 'DocumentTruck',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => $default_conditions,
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Truck->Siup
		);

		$db = $this->Truck->getDataSource();
		$conditionsQuery = $db->conditionKeysToString($conditions);
		$default_conditions = '';

		if( !empty($conditionsQuery) ) {
			foreach ($conditionsQuery as $key => $con) {
				$default_conditions .= ' AND '.$con;
			}
		}

		$sql = $kirQuery . ' UNION ' . $stnkQuery . ' UNION '. $siupQuery;
		$sql = 'SELECT COUNT(DocumentTruck.id) AS cnt
	    		FROM (
	                '.$sql.'
	            ) AS DocumentTruck
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
        $kir = !empty($data['named']['kir'])?$data['named']['kir']:false;
        $siup = !empty($data['named']['siup'])?$data['named']['siup']:false;
        $stnk = !empty($data['named']['stnk'])?$data['named']['stnk']:false;
        $stnk_5_thn = !empty($data['named']['stnk_5_thn'])?$data['named']['stnk_5_thn']:false;

        if( !empty($dateFrom) || !empty($dateTo) ) {
            if( !empty($dateFrom) ) {
                $default_options['conditions']['DATE_FORMAT(DocumentTruck.to_date, \'%Y-%m-%d\') >='] = $dateFrom;
            }

            if( !empty($dateTo) ) {
                $default_options['conditions']['DATE_FORMAT(DocumentTruck.to_date, \'%Y-%m-%d\') <='] = $dateTo;
            }
        }
        if(!empty($nopol)){
            if( $type == 2 ) {
                $default_options['conditions']['DocumentTruck.truck_id'] = $nopol;
            } else {
                $default_options['conditions']['DocumentTruck.no_pol LIKE'] = '%'.$nopol.'%';
            }
        }

        if(!empty($kir) || !empty($stnk) || !empty($stnk_5_thn) || !empty($siup) ){
        	$idx = 0;
    	}

        if(!empty($kir)){
    		$default_options['conditions']['OR'][$idx]['DocumentTruck.data_type'] = 'kir';
    		$idx++;
    	}
        if(!empty($stnk)){
    		$default_options['conditions']['OR'][$idx]['DocumentTruck.data_type'] = 'stnk';
    		$idx++;
    	}
        if(!empty($stnk_5_thn)){
    		$default_options['conditions']['OR'][$idx]['DocumentTruck.data_type'] = 'stnk_5_thn';
    		$idx++;
    	}
        if(!empty($siup)){
    		$default_options['conditions']['OR'][$idx]['DocumentTruck.data_type'] = 'siup';
    		$idx++;
    	}

        return $default_options;
    }
}
?>