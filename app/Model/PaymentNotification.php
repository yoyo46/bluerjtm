<?php
class PaymentNotification extends AppModel {
	var $name = 'PaymentNotification';
	public $useTable = false;

	public function paginate($conditions, $fields, $order, $limit, $page = 1, $recursive = null, $extra = array()) {
        $this->LeasingInstallment = ClassRegistry::init('LeasingInstallment');

        $current_branch_id = Configure::read('__Site.config_branch_id');

		$page = !empty($page)?$page:1;
	    $recursive = -1;
	    $default_conditions = array(
        	'Leasing.status' => 1,
        	'PaymentNotification.status' => 1,
        	'PaymentNotification.payment_status <>' => 'paid',
    	);

        if( empty($head_office) ) {
            $default_conditions['Leasing.branch_id'] = Configure::read('__Site.config_branch_id');
        }

		$db = $this->LeasingInstallment->getDataSource();
		$leasingQuery = $db->buildStatement(
		    array(
		        'fields'     => array(
		        	'Leasing.id',
		        	'Vendor.name AS to_name',
		        	'Leasing.no_contract AS nodoc',
		        	'PaymentNotification.paid_date',
		        	'PaymentNotification.status',
		        	'PaymentNotification.payment_status',
		        	'PaymentNotification.leasing_id AS document_id',
		        	"'leasings' AS data_type",
	        	),
		        'table'      => $db->fullTableName($this->LeasingInstallment),
		        'alias'      => 'PaymentNotification',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(
			        array( 
						'table' => 'leasings',
						'alias' => 'Leasing',
						'conditions' => array(
							'Leasing.id = PaymentNotification.leasing_id',
						),
					),
			        array( 
						'table' => 'vendors',
						'alias' => 'Vendor',
						'conditions' => array(
							'Vendor.id = Leasing.vendor_id',
						),
					),
				),
		        'conditions' => $default_conditions,
		        'order'      => array(
                    'PaymentNotification.paid_date' => 'ASC',
                    'PaymentNotification.id' => 'ASC',
                ),
		        'group'      => array(
		        	'document_id',
	        	),
		    ),
		    $this->LeasingInstallment
		);

		$db = $this->LeasingInstallment->getDataSource();
		$conditionsQuery = $db->conditionKeysToString($conditions);
		$default_conditions = '';

		if( !empty($conditionsQuery) ) {
			foreach ($conditionsQuery as $key => $con) {
				$default_conditions .= ' AND '.$con;
			}
		}

		$sql = $leasingQuery;
		$sql = 'SELECT PaymentNotification.*
	    		FROM (
	                '.$sql.'
	            ) AS PaymentNotification
				WHERE 1 = 1
				'.$default_conditions.'
				ORDER BY PaymentNotification.paid_date DESC,
				PaymentNotification.id ASC
				Limit ' . (($page - 1) * $limit) . ', ' . $limit;
	    $results = $this->query($sql);
	    return $results;
	}

	public function paginateCount($conditions = null, $recursive = 0, $extra = array()) {
        $this->LeasingInstallment = ClassRegistry::init('LeasingInstallment');

        $current_branch_id = Configure::read('__Site.config_branch_id');

		$page = !empty($page)?$page:1;
	    $recursive = -1;
	    $default_conditions = array(
        	'Leasing.status' => 1,
        	'PaymentNotification.status' => 1,
        	'PaymentNotification.payment_status <>' => 'paid',
    	);

        if( empty($head_office) ) {
            $default_conditions['Leasing.branch_id'] = Configure::read('__Site.config_branch_id');
        }

		$db = $this->LeasingInstallment->getDataSource();
		$leasingQuery = $db->buildStatement(
		   array(
		        'fields'     => array(
		        	'Leasing.id',
		        	'Vendor.name AS to_name',
		        	'Leasing.no_contract AS nodoc',
		        	'PaymentNotification.paid_date',
		        	'PaymentNotification.status',
		        	'PaymentNotification.payment_status',
		        	'PaymentNotification.leasing_id AS document_id',
		        	"'leasings' AS data_type",
	        	),
		        'table'      => $db->fullTableName($this->LeasingInstallment),
		        'alias'      => 'PaymentNotification',
		        'limit'      => null,
		        'offset'     => null,
		        'joins'      => array(
			        array( 
						'table' => 'leasings',
						'alias' => 'Leasing',
						'conditions' => array(
							'Leasing.id = PaymentNotification.leasing_id',
						),
					),
			        array( 
						'table' => 'vendors',
						'alias' => 'Vendor',
						'conditions' => array(
							'Vendor.id = Leasing.vendor_id',
						),
					),
				),
		        'conditions' => $default_conditions,
		        'order'      => null,
		        'group'      => array(
		        	'document_id',
	        	),
		    ),
		    $this->LeasingInstallment
		);

		$db = $this->LeasingInstallment->getDataSource();
		$conditionsQuery = $db->conditionKeysToString($conditions);
		$default_conditions = '';

		if( !empty($conditionsQuery) ) {
			foreach ($conditionsQuery as $key => $con) {
				$default_conditions .= ' AND '.$con;
			}
		}

		$sql = $leasingQuery;
		$sql = 'SELECT COUNT(PaymentNotification.id) AS cnt
	    		FROM (
	                '.$sql.'
	            ) AS PaymentNotification
				WHERE 1 = 1
				'.$default_conditions;
	    $results = $this->query($sql);

	    return !empty($results[0][0]['cnt'])?$results[0][0]['cnt']:0;
	}
}
?>