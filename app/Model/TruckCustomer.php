<?php
class TruckCustomer extends AppModel {
	var $name = 'TruckCustomer';

	var $belongsTo = array(
		'Truck' => array(
			'className' => 'Truck',
			'foreignKey' => 'truck_id',
		),
		'Customer' => array(
			'className' => 'Customer',
			'foreignKey' => 'customer_id',
		),
		'CustomerNoType' => array(
			'className' => 'CustomerNoType',
			'foreignKey' => 'customer_id',
		),
	);

	function getMergeTruckCustomer ( $data = false ) {
		if( !empty($data['Truck']['id']) && empty($data['TruckCustomer']) ) {
			$truckCustomers = $this->find('all', array(
				'conditions' => array(
					'TruckCustomer.truck_id'=> $data['Truck']['id'],
				),
				'order' => array(
					'TruckCustomer.id' => 'ASC',
				),
			));

			if( !empty($truckCustomers) ) {
				foreach ($truckCustomers as $key => $truckCustomer) {
                	$truckCustomer = $this->Customer->getMerge($truckCustomer, $truckCustomer['TruckCustomer']['customer_id']);
                	$truckCustomers[$key] = $truckCustomer;
				}
				$data['TruckCustomer'] = $truckCustomers;
			}
		}

		return $data;
	}

	/**
	* get data
	*
	* @param string $find - all, list
	*		string all - Pick semua field berupa array
	*		string list - Pick semua field berupa key dan value array
	* @param array $options - Menampung semua opsi-opsi yang dibutuhkan dalam melakukan query
	* @return hasil ditemukan return array, hasil tidak ditemukan return false
	*/
	function getData( $find = 'all', $options = array() ){
		$default_options = array();

		if(!empty($options)){
			$default_options = array_merge($default_options, $options);
		}

		if( $find == 'paginate' ) {
			$result = $default_options;
		} else {
			$result = $this->find($find, $default_options);
		}
        return $result;
	}

	/**
	* getPerformanceReport
	*
	* @param int $user_id = user id
	* @param string $from_date = tanggal laporan dari tanggal
	* @param string $to_date = tanggal laporan sampai tanggal
	* @param string $period = pilihan waktu "year" dan defaultnya = 1 bulan terakhir 
	* @return $result
	*/
	function getPerformanceReport ( $user_id, $from_date = false, $to_date = false, $period = falase ) {		
		$conditionProperty = array(
			'Property.active'=> 1,
			'Property.status'=> 1,
			'Property.published'=> 1,
			'Property.deleted'=> 0,
			'Property.sold'=> 0,
			'Property.inactive'=> 0,
			'Property.user_id' => $user_id,
		);

		$Property = $this->Property->find('all', array(
			'conditions' => $conditionProperty,
			'fields' => array(
				'Property.id', 'Property.title'
			)
		));

		$result = array();
		if(!empty($Property)){
			switch ($period) {
				case 'year':
					$formatDate = '%Y-%m';
					$groupReport = array(
						'Property.id',
						'MONTH(PropertyLead.created)',
						'YEAR(PropertyLead.created)',
					);
					$orderReport = array(
						'MONTH(PropertyLead.created) ASC',
						'YEAR(PropertyLead.created) ASC',
					);
					break;
				
				default:
					$formatDate = '%Y-%m-%d';
					$groupReport = array(
						'Property.id',
						'DAY(PropertyLead.created)', 
						'MONTH(PropertyLead.created)',
						'YEAR(PropertyLead.created)',
					);
					$orderReport = array(
						'DAY(PropertyLead.created) ASC', 
						'MONTH(PropertyLead.created) ASC',
						'YEAR(PropertyLead.created) ASC',
					);
					break;
			}

			$conditionReport = array();
			foreach ($Property as $key => $value) {
				$conditionReport = array(
					'Property.id' => $value['Property']['id']
				);

				if( !empty($from_date) && !empty($to_date) ) {
					$conditionReport = array_merge($conditionReport, array(
						"DATE_FORMAT(PropertyLead.created, '".$formatDate."')  BETWEEN '".$from_date."' AND '".$to_date."'",
					));
				}
				$reportPerformance = $this->find('all', array(
					'conditions' => $conditionReport,
					'group'=> $groupReport,
					'fields'=> array(
						'DAY(PropertyLead.created) as day', 
						'MONTH(PropertyLead.created) as month', 
						'YEAR(PropertyLead.created) as year',
						'DATE_FORMAT(PropertyLead.created, \''.$formatDate.'\') dt',
						'COUNT(DATE_FORMAT(PropertyLead.created, \''.$formatDate.'\')) cnt',
					),
					'order' => $orderReport,
					'contain' => array(
						'Property'
					)
				));
				$Property[$key]['Property']['report_performance'] = $reportPerformance;
			}
			$result = $Property;
		}
		
		return $result;
	}
}
?>