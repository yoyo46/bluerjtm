<?php
App::uses('Sanitize', 'Utility');
class RjAssetComponent extends Component {
	var $components = array(
		'MkCommon'
	);

	function initialize(Controller $controller, $settings = array()) {
		$this->controller = $controller;
	}

    function _callBeforeSaveGroup ( $data, $id = false ) {
        if( !empty($data) ) {
            $dataSave = array();
            $dataDetail = $this->MkCommon->filterEmptyField($data, 'AssetGroupCoa');

            $data = $this->MkCommon->dataConverter($data, array(
                'price' => array(
                    'AssetGroup' => array(
                        'nilai_sisa',
                    ),
                )
            ));

            if( !empty($dataDetail) ) {
                $values = array_filter($dataDetail);
                unset($data['AssetGroupCoa']);

                foreach ($values as $type => $coas) {
                    $coa_field = key($coas);
                    $coa_id = current($coas);

                    $detail['AssetGroupCoa'] = array(
                        $coa_field => $coa_id,
                        'document_type' => $type,
                    );
                    $dataSave[$type] = $detail;
                }
            }

            if( !empty($dataSave) ) {
                $data['AssetGroupCoa'] = $dataSave;
            }

            if( !empty($id) ) {
                $data['AssetGroup']['id'] = $id;
            }
        }

        return $data;
    }

    function _callBeforeRenderGroup ( $data ) {
        if( !empty($data) ) {
            $dataDetail = $this->MkCommon->filterEmptyField($data, 'AssetGroupCoa');
            
            if( !empty($dataDetail[0]) ) {
                foreach ($dataDetail as $key => $value) {
                    $document_type = $this->MkCommon->filterEmptyField($value, 'AssetGroupCoa', 'document_type');
                    $coa_id = $this->MkCommon->filterEmptyField($value, 'AssetGroupCoa', 'coa_id');

                    $data['AssetGroupCoa'][$document_type]['coa_id'] = $coa_id;
                }
            }
        }

        $coas = $this->controller->GroupBranch->Branch->BranchCoa->getCoas();
        $this->MkCommon->_layout_file('select');

        $this->controller->set(compact(
            'coas'
        ));

        return $data;
    }
}
?>