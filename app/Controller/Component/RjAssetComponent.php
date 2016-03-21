<?php
App::uses('Sanitize', 'Utility');
class RjAssetComponent extends Component {
	var $components = array(
		'MkCommon'
	);

	function initialize(Controller $controller, $settings = array()) {
		$this->controller = $controller;
	}

    function _callBeforeSaveGroup ( $data ) {
        if( !empty($data) ) {
            $dataSave = array();
            $dataDetail = $this->MkCommon->filterEmptyField($data, 'AssetGroupCoa');

            if( !empty($dataDetail) ) {
                $values = array_filter($dataDetail);
                unset($data['AssetGroupCoa']);

                foreach ($values as $type => $coa_id) {
                    $detail['AssetGroupCoa'] = array(
                        'coa_id' => $coa_id,
                        'document_type' => $type,
                    );
                    $dataSave[] = $detail;
                }
            }

            if( !empty($dataSave) ) {
                $data['AssetGroupCoa'] = $dataSave;
            }
        }

        return $data;
    }

    function _callBeforeRenderGroup ( $data ) {        
        $coas = $this->controller->GroupBranch->Branch->BranchCoa->getCoas();
        $this->controller->set(compact(
            'coas'
        ));

        return $data;
    }
}
?>