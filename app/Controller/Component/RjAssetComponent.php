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

    function _callBeforeRender ( $data = false ) {
        if( !empty($data) ) {
            $data = $this->MkCommon->dataConverter($data, array(
                'date' => array(
                    'Asset' => array(
                        'purchase_date',
                        'neraca_date',
                    ),
                ),
            ), true);
        } else {
            $data['Asset']['purchase_date'] = date('d/m/Y');
            $data['Asset']['neraca_date'] = date('d/m/Y');
        }

        $assetGroups = $this->controller->Asset->AssetGroup->getData('list', array(
            'fields' => array(
                'AssetGroup.id', 'AssetGroup.group_name',
            ),
        ));
        $this->MkCommon->_layout_file('select');

        $this->controller->set(compact(
            'assetGroups'
        ));

        return $data;
    }

    function _callBeforeSave ( $data, $id = false ) {
        if( !empty($data) ) {
            $dataSave = array();
            $data = $this->MkCommon->dataConverter($data, array(
                'price' => array(
                    'Asset' => array(
                        'nilai_perolehan',
                        'depr_bulan',
                        'ak_penyusutan',
                        'nilai_buku',
                    ),
                ),
                'date' => array(
                    'Asset' => array(
                        'purchase_date',
                        'neraca_date',
                    ),
                ),
            ));
            $data = $this->MkCommon->_callUnset(array(
                'AssetGroup',
            ), $data);

            $nopol = $this->MkCommon->filterEmptyField($data, 'Asset', 'name');
            $truck = $this->controller->Asset->Truck->getMerge(array(), $nopol, 'Truck.nopol');
            $truck_id = $this->MkCommon->filterEmptyField($truck, 'Truck', 'id');

            if( !empty($id) ) {
                $data['Asset']['id'] = $id;
            }
            if( !empty($truck_id) ) {
                $data['Asset']['truck_id'] = $truck_id;
            }
        }

        return $data;
    }
}
?>