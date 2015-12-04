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
		$venQuery = $db->buildStatement(
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

		$sql = $empQuery . ' UNION ' . $custQuery . ' UNION '. $venQuery;
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
		$venQuery = $db->buildStatement(
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

		$sql = $empQuery . ' UNION ' . $custQuery . ' UNION '. $venQuery . (!empty($order)?' '.$order:'');
		$sql = 'SELECT Client.id, Client.name, Client.address, Client.type, Client.model
	    		FROM (
	                '.$sql.'
	            ) AS Client
				WHERE 1 = 1'.
				$default_conditions;
	    $this->recursive = $recursive;
	    $results = $this->query($sql);

	    return count($results);
	}
}
?>