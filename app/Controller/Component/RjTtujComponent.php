<?php
App::uses('Sanitize', 'Utility');
class RjTtujComponent extends Component {
	var $components = array('MkCommon'); 

	function initialize(Controller $controller, $settings = array()) {
		$this->controller = $controller;
	}

	function _callBeforeViewReportRecapSj( $params ) {
        $allow_branch_id = Configure::read('__Site.config_allow_branch_id');
        $customers = $this->controller->Ttuj->Customer->getData('list', array(
            'fields' => array(
                'Customer.id', 'Customer.customer_name_code'
            ),
            'conditions' => array(
                'Customer.branch_id' => $allow_branch_id,
            ),
        ), true, array(
            'branch' => false,
            'plant' => false,
        ));
        $cities = $this->controller->GroupBranch->Branch->City->getListCities();

        $dateFrom = $this->MkCommon->filterEmptyField($params, 'named', 'DateFrom', false, array(
        	'date' => 'd M Y',
    	));
        $dateTo = $this->MkCommon->filterEmptyField($params, 'named', 'DateTo', false, array(
        	'date' => 'd M Y',
    	));

        $period_text = __('Periode %s - %s', $dateFrom, $dateTo);

        $this->controller->set('sub_module_title', __('Laporan Rekap Penerimaan Surat Jalan'));
        $this->controller->set('active_menu', 'report_recap_sj');
        $this->controller->set(compact(
            'period_text', 'customers', 'cities'
        ));
	}
}
?>