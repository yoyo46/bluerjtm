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

    function _callBeforeSaveSell ( $data, $id = false ) {
        $dataSave = array();

        if( !empty($data) ) {
            $data = $this->MkCommon->dataConverter($data, array(
                'date' => array(
                    'AssetSell' => array(
                        'transaction_date',
                        'transfer_date',
                    ),
                ),
            ));

            $values = $this->MkCommon->filterEmptyField($data, 'AssetSellDetail', 'asset_id');
            $transaction_status = $this->MkCommon->filterEmptyField($data, 'AssetSell', 'transaction_status');

            $dataSave['AssetSell'] = $this->MkCommon->filterEmptyField($data, 'AssetSell');
            $dataSave['AssetSell']['id'] = $id;
            $dataSave['AssetSell']['branch_id'] = Configure::read('__Site.config_branch_id');
            $dataSave['AssetSell']['user_id'] = Configure::read('__Site.config_user_id');

            if( !empty($values) ) {
                $grandtotal = 0;
                $grandtotal_nilai_perolehan = 0;
                $grandtotal_ak_penyusutan = 0;

                foreach ($values as $key => $asset_id) {
                    $idArr = $this->MkCommon->filterEmptyField($data, 'AssetSellDetail', 'id');
                    $priceArr = $this->MkCommon->filterEmptyField($data, 'AssetSellDetail', 'price');
                    
                    $asset = $this->controller->Asset->getMerge(array(), $asset_id, 'Asset.id', 'available');
                    
                    $asset_group_id = $this->MkCommon->filterEmptyField($asset, 'Asset', 'asset_group_id');
                    $assetCoa = $this->controller->Asset->AssetGroup->AssetGroupCoa->getMerge($asset, $asset_group_id, 'first', 'Asset');
                    $accumulationDepr = $this->controller->Asset->AssetGroup->AssetGroupCoa->getMerge($asset, $asset_group_id, 'first', 'AccumulationDepr');
                    $profitAsset = $this->controller->Asset->AssetGroup->AssetGroupCoa->getMerge($asset, $asset_group_id, 'first', 'ProfitAsset');

                    $name = $this->MkCommon->filterEmptyField($asset, 'Asset', 'name');
                    $transaction_date = $this->MkCommon->filterEmptyField($asset, 'Asset', 'transaction_date');
                    $note = $this->MkCommon->filterEmptyField($asset, 'Asset', 'note');
                    $nilai_perolehan = $this->MkCommon->filterEmptyField($asset, 'Asset', 'nilai_perolehan');
                    $ak_penyusutan = $this->MkCommon->filterEmptyField($asset, 'Asset', 'ak_penyusutan');
                    $truck_id = $this->MkCommon->filterEmptyField($asset, 'Asset', 'truck_id');

                    $nilai_perolehan_coa_id = $this->MkCommon->filterEmptyField($assetCoa, 'AssetGroupCoa', 'coa_id');
                    $ak_penyusutan_coa_id = $this->MkCommon->filterEmptyField($accumulationDepr, 'AssetGroupCoa', 'coa_id');
                    $price_coa_id = $this->MkCommon->filterEmptyField($profitAsset, 'AssetGroupCoa', 'coa_id');

                    $idDetail = !empty($idArr[$key])?$idArr[$key]:false;
                    $price = !empty($priceArr[$key])?$this->MkCommon->_callPriceConverter($priceArr[$key]):false;
                    
                    $grandtotal += $price;
                    $grandtotal_nilai_perolehan += $nilai_perolehan;
                    $grandtotal_ak_penyusutan += $ak_penyusutan;
                    
                    $dataSave['AssetSellDetail'][$key] = array(
                        'AssetSellDetail' => array(
                            'asset_id' => $asset_id,
                            'price' => $price,
                            'name' => $name,
                            'note' => $note,
                            'nilai_perolehan' => $nilai_perolehan,
                            'ak_penyusutan' => $ak_penyusutan,
                            'nilai_perolehan_coa_id' => $nilai_perolehan_coa_id,
                            'ak_penyusutan_coa_id' => $ak_penyusutan_coa_id,
                            'price_coa_id' => $price_coa_id,
                        ),
                    );

                    if( $transaction_status == 'posting' ) {
                        $dataSave['AssetSellDetail'][$key]['Asset'] = array(
                            'id' => $asset_id,
                            'status_document' => 'sold',
                        );

                        if( !empty($truck_id) ) {
                            $dataSave['AssetSellDetail'][$key]['Asset']['Truck'] = array(
                                'id' => $truck_id,
                                'sold' => 1,
                            );
                        }
                    }
                }

                $dataSave['AssetSell']['grandtotal'] = $grandtotal;
                $dataSave['AssetSell']['grandtotal_nilai_perolehan'] = $grandtotal_nilai_perolehan;
                $dataSave['AssetSell']['grandtotal_ak_penyusutan'] = $grandtotal_ak_penyusutan;
            }
        }

        return $dataSave;
    }

    function _callBeforeRenderSell ( $data, $asset_id = false ) {
        if( !empty($data) ) {
            $data = $this->MkCommon->dataConverter($data, array(
                'date' => array(
                    'AssetSell' => array(
                        'transaction_date',
                        'transfer_date',
                    ),
                ),
            ), true);
        } else {
            $data['AssetSell']['transaction_date'] = date('d/m/Y');
            $data['AssetSell']['transfer_date'] = date('d/m/Y');
        }

        $coas = $this->controller->GroupBranch->Branch->BranchCoa->getCoas();
        $this->MkCommon->_layout_file(array(
            'select',
        ));

        $this->controller->set(compact(
            'coas'
        ));

        return $data;
    }
}
?>