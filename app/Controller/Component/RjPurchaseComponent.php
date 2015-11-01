<?php
App::uses('Sanitize', 'Utility');
class RjPurchaseComponent extends Component {
	var $components = array(
		'MkCommon'
	);

	function initialize(Controller $controller, $settings = array()) {
		$this->controller = $controller;
	}

    function _callBeforeSaveDetailQuotation ( $data ) {
        $dataSave = array();
        $dataDetailPrice = $this->MkCommon->filterEmptyField($data, 'SupplierQuotationDetail', 'price');

        if( !empty($dataDetailPrice) ) {
            $values = array_filter($dataDetailPrice);

            foreach ($values as $key => $value) {
                $dataSave[]['SupplierQuotationDetail'] = array(
                    'price' => trim($value),
                );
            }
        }
        
        if( !empty($data['PropertyFacility']['other_id']) ) {
            $text = !empty($data['PropertyFacility']['other_text'])?$data['PropertyFacility']['other_text']:false;

            $dataSave[]['PropertyFacility'] = array(
                'facility_id' => -1,
                'other_text' => $text,
            );
        }

        return $dataSave;
    }
}
?>