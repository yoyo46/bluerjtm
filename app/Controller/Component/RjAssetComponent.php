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

            $data['Asset']['branch_id'] = Configure::read('__Site.config_branch_id');
        }

        return $data;
    }

    function _callBeforeSavePO ( $data, $id = false ) {
        $dataSave = array();

        if( !empty($data) ) {
            $data = $this->MkCommon->dataConverter($data, array(
                'date' => array(
                    'PurchaseOrder' => array(
                        'transaction_date',
                    ),
                ),
            ));

            $values = $this->MkCommon->filterEmptyField($data, 'PurchaseOrderAsset', 'name');
            $transaction_date = $this->MkCommon->filterEmptyField($data, 'PurchaseOrder', 'transaction_date');

            $dataSave['PurchaseOrder'] = $this->MkCommon->filterEmptyField($data, 'PurchaseOrder');
            $dataSave['PurchaseOrder']['id'] = $id;
            $dataSave['PurchaseOrder']['branch_id'] = Configure::read('__Site.config_branch_id');
            $dataSave['PurchaseOrder']['user_id'] = Configure::read('__Site.config_user_id');
            $dataSave['PurchaseOrder']['is_asset'] = 1;

            if( !empty($values) ) {
                $grandtotal = 0;

                foreach ($values as $key => $name) {
                    $truckArr = $this->MkCommon->filterEmptyField($data, 'PurchaseOrderAsset', 'truck_id');
                    $noteArr = $this->MkCommon->filterEmptyField($data, 'PurchaseOrderAsset', 'note');
                    $assetArr = $this->MkCommon->filterEmptyField($data, 'PurchaseOrderAsset', 'asset_group_id');
                    $priceArr = $this->MkCommon->filterEmptyField($data, 'PurchaseOrderAsset', 'price');

                    $truck_id = !empty($truckArr[$key])?$truckArr[$key]:false;
                    $note = !empty($noteArr[$key])?$noteArr[$key]:false;
                    $asset_group_id = !empty($assetArr[$key])?$assetArr[$key]:false;
                    $price = !empty($priceArr[$key])?$this->MkCommon->_callPriceConverter($priceArr[$key]):false;
                    $grandtotal += $price;

                    $assetGroup = $this->controller->Asset->AssetGroup->getMerge(array(), $asset_group_id);
                    $assetGroup = $this->controller->Asset->AssetGroup->AssetGroupCoa->getMerge($assetGroup, $asset_group_id, 'first', 'Asset');
                    $is_truck = $this->MkCommon->filterEmptyField($assetGroup, 'AssetGroup', 'is_truck');
                    $coa_id = $this->MkCommon->filterEmptyField($assetGroup, 'AssetGroupCoa', 'coa_id');

                    if( !empty($is_truck) ) {
                        $company = $this->controller->Asset->AssetGroup->PurchaseOrderAsset->Truck->Company->getData('first', array(
                            'conditions' => array(
                                'Company.name LIKE' => '%RJTM%'
                            ),
                        ));
                        $company_id = $this->MkCommon->filterEmptyField($company, 'Company', 'id', 0);
                        $thn = $this->MkCommon->customDate($transaction_date, 'Y');

                        $dataSave['PurchaseOrderAsset'][$key]['Truck'] = array(
                            'id' => $truck_id,
                            'branch_id' => Configure::read('__Site.config_branch_id'),
                            'company_id' => $company_id,
                            'nopol' => $name,
                            'tahun' => $thn,
                            'tahun_neraca' => $thn,
                            'purchase_date' => $transaction_date,
                            'is_asset' => 1,
                        );
                    }

                    $dataSave['PurchaseOrderAsset'][$key]['PurchaseOrderAsset'] = array(
                        'name' => $name,
                        'note' => $note,
                        'asset_group_id' => $asset_group_id,
                        'coa_id' => $coa_id,
                        'price' => $price,
                    );
                }

                $dataSave['PurchaseOrder']['grandtotal'] = $grandtotal;
            }
        }

        return $dataSave;
    }

    function _callBeforeRenderPO ( $data ) {
        if( !empty($data) ) {
            $data = $this->MkCommon->dataConverter($data, array(
                'date' => array(
                    'PurchaseOrder' => array(
                        'transaction_date',
                    ),
                ),
            ), true);
        } else {
            $data['PurchaseOrder']['transaction_date'] = date('d/m/Y');
        }

        $assetGroups = $this->controller->Asset->AssetGroup->getData('list', array(
            'fields' => array(
                'AssetGroup.id', 'AssetGroup.group_name',
            ),
        ));
        $vendors = $this->controller->Asset->Truck->PurchaseOrderAsset->PurchaseOrder->Vendor->getData('list');

        $this->controller->set(compact(
            'assetGroups', 'vendors'
        ));

        return $data;
    }
}
?>