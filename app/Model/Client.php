<?php
class Client extends AppModel {
	var $name = 'Client';
	public $useTable = false;

	public function paginate($conditions, $fields, $order, $limit, $page = 1, $recursive = null, $extra = array()) {
        $this->Employe = ClassRegistry::init('Employe');
        $this->Customer = ClassRegistry::init('Customer');
        $this->Vendor = ClassRegistry::init('Vendor');
        $this->Driver = ClassRegistry::init('Driver');

		$page = !empty($page)?$page:1;
	    $recursive = -1;

		$db = $this->Employe->getDataSource();
		$empQuery = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'Client.id', 'CONCAT(Client.first_name, " ", Client.last_name) AS name', 
		        	'Client.address', "'Karyawan' AS type",
		        	"'Employe' AS model",
	        	),
		        'table'      => $db->fullTableName($this->Employe),
		        'alias'      => 'Client',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => array(
		        	'Client.status' => 1,
	        	),
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Employe
		);

		$db = $this->Customer->getDataSource();
		$custQuery = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'Client.id', 'CONCAT(Client.name, \' - \', Client.code) AS name', 
		        	'Client.address', "'Customer' AS type",
		        	"'Customer' AS model",
	        	),
		        'table'      => $db->fullTableName($this->Customer),
		        'alias'      => 'Client',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => array(
		        	'Client.status' => 1,
	        	),
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Customer
		);

		$db = $this->Vendor->getDataSource();
		$venQuery = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'Client.id', 'Client.name', 'Client.address', 
		        	"'Vendor' AS type", "'Vendor' AS model",
	        	),
		        'table'      => $db->fullTableName($this->Vendor),
		        'alias'      => 'Client',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => array(
		        	'Client.status' => 1,
	        	),
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Vendor
		);

		$db = $this->Driver->getDataSource();
		$driverQuery = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'Client.id', 'CASE WHEN Client.alias = \'\' THEN Client.name ELSE CONCAT(Client.name, \' ( \', Client.alias, \' )\') END AS name', 
		        	'Client.address', "'Supir' AS type", "'Driver' AS model",
	        	),
		        'table'      => $db->fullTableName($this->Driver),
		        'alias'      => 'Client',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => array(
		        	'Client.status' => 1,
	        	),
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Driver
		);

		$db = $this->Vendor->getDataSource();
		$conditionsQuery = $db->conditionKeysToString($conditions);
		$default_conditions = '';

		if( !empty($conditionsQuery) ) {
			foreach ($conditionsQuery as $key => $con) {
				$default_conditions .= ' AND '.$con;
			}
		}

		$sql = $empQuery . ' UNION ' . $custQuery . ' UNION '. $venQuery . ' UNION '. $driverQuery;
		$sql = 'SELECT Client.id, Client.name, Client.address, Client.type, Client.model
	    		FROM (
	                '.$sql.'
	            ) AS Client
				WHERE 1 = 1
				'.$default_conditions.'
				ORDER BY Client.name ASC
				Limit ' . (($page - 1) * $limit) . ', ' . $limit;
	    $results = $this->query($sql);
	    return $results;
	}

	public function paginateCount($conditions = null, $recursive = 0, $extra = array()) {
		$this->Employe = ClassRegistry::init('Employe');
        $this->Customer = ClassRegistry::init('Customer');
        $this->Vendor = ClassRegistry::init('Vendor');

		$db = $this->Employe->getDataSource();
		$empQuery = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'Client.id', 'CONCAT(Client.first_name, " ", Client.last_name) AS name', 
		        	'Client.address', "'Karyawan' AS type",
		        	"'Employe' AS model",
	        	),
		        'table'      => $db->fullTableName($this->Employe),
		        'alias'      => 'Client',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => array(
		        	'Client.status' => 1,
	        	),
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Employe
		);

		$db = $this->Customer->getDataSource();
		$custQuery = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'Client.id', 'CONCAT(Client.name, \' - \', Client.code) AS name', 
		        	'Client.address', "'Customer' AS type",
		        	"'Customer' AS model",
	        	),
		        'table'      => $db->fullTableName($this->Customer),
		        'alias'      => 'Client',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => array(
		        	'Client.status' => 1,
	        	),
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Customer
		);

		$db = $this->Vendor->getDataSource();
		$venQuery = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'Client.id', 'Client.name', 'Client.address', 
		        	"'Vendor' AS type", "'Vendor' AS model",
	        	),
		        'table'      => $db->fullTableName($this->Vendor),
		        'alias'      => 'Client',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => array(
		        	'Client.status' => 1,
	        	),
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Vendor
		);

		$db = $this->Driver->getDataSource();
		$driverQuery = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'Client.id', 'CASE WHEN Client.alias = \'\' THEN Client.name ELSE CONCAT(Client.name, \' ( \', Client.alias, \' )\') END AS name', 
		        	'Client.address', "'Supir' AS type", "'Driver' AS model",
	        	),
		        'table'      => $db->fullTableName($this->Driver),
		        'alias'      => 'Client',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(),
		        'conditions' => array(
		        	'Client.status' => 1,
	        	),
		        'order'      => null,
		        'group'      => null
		    ),
		    $this->Driver
		);

		$db = $this->Vendor->getDataSource();
		$conditionsQuery = $db->conditionKeysToString($conditions);
		$default_conditions = '';

		if( !empty($conditionsQuery) ) {
			foreach ($conditionsQuery as $key => $con) {
				$default_conditions .= ' AND '.$con;
			}
		}

		$sql = $empQuery . ' UNION ' . $custQuery . ' UNION '. $venQuery . ' UNION '. $driverQuery . (!empty($order)?' '.$order:'');
		$sql = 'SELECT COUNT(Client.id) AS cnt
	    		FROM (
	                '.$sql.'
	            ) AS Client
				WHERE 1 = 1'.
				$default_conditions;
	    $this->recursive = $recursive;
	    $results = $this->query($sql);

	    return !empty($results[0][0]['cnt'])?$results[0][0]['cnt']:0;
	}

    public function _callRefineParams( $data = '', $default_options = false ) {
        $name = !empty($data['named']['name'])?urldecode($data['named']['name']):false;
        $is_employee = !empty($data['named']['is_employee'])?urldecode($data['named']['is_employee']):false;
        $is_customer = !empty($data['named']['is_customer'])?urldecode($data['named']['is_customer']):false;
        $is_vendor = !empty($data['named']['is_vendor'])?urldecode($data['named']['is_vendor']):false;
        $is_driver = !empty($data['named']['is_driver'])?urldecode($data['named']['is_driver']):false;

		if(!empty($name)){
			$default_options['conditions']['Client.name LIKE'] = '%'.$name.'%';
		}
		if(!empty($is_employee)){
			$default_options['conditions']['Client.type'][] = 'Karyawan';
		}
		if(!empty($is_customer)){
			$default_options['conditions']['Client.type'][] = 'Customer';
		}
		if(!empty($is_vendor)){
			$default_options['conditions']['Client.type'][] = 'Vendor';
		}
		if(!empty($is_driver)){
			$default_options['conditions']['Client.type'][] = 'Supir';
		}
        
        return $default_options;
    }
}
?>