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

        $coas = $this->controller->GroupBranch->Branch->BranchCoa->getCoas(false, false);
        $this->MkCommon->_layout_file('select');

        $this->controller->set(compact(
            'coas'
        ));

        return $data;
    }

    function _callBeforeRender ( $data = false ) {
        $id = Common::hashEmptyField($data, 'Asset.id');

        if( !empty($data) ) {
            $asset_group_id = $this->MkCommon->filterEmptyField($data, 'Asset', 'asset_group_id');
            $data = $this->controller->Asset->AssetGroup->getMerge($data, $asset_group_id);

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

        if( !empty($id) ) {
            $this->MkCommon->getLogs($this->controller->params['controller'], array( 'add', 'edit', 'toggle' ), $id);
        }

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
            $this->MkCommon->_callAllowClosing($data, 'PurchaseOrder', 'transaction_date');

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
                    $assetArr = $this->MkCommon->filterEmptyField($data, 'PurchaseOrderAsset', 'asset_id');
                    $noteArr = $this->MkCommon->filterEmptyField($data, 'PurchaseOrderAsset', 'note');
                    $assetGroupArr = $this->MkCommon->filterEmptyField($data, 'PurchaseOrderAsset', 'asset_group_id');
                    $priceArr = $this->MkCommon->filterEmptyField($data, 'PurchaseOrderAsset', 'price');

                    $truck_id = !empty($truckArr[$key])?$truckArr[$key]:false;
                    $asset_id = !empty($assetArr[$key])?$assetArr[$key]:false;
                    $note = !empty($noteArr[$key])?$noteArr[$key]:false;
                    $asset_group_id = !empty($assetGroupArr[$key])?$assetGroupArr[$key]:false;
                    $price = !empty($priceArr[$key])?$this->MkCommon->_callPriceConverter($priceArr[$key]):false;
                    $grandtotal += $price;

                    $asset = $this->controller->Asset->getMerge(array(), $asset_id);
                    $asset = $this->controller->Asset->AssetGroup->getMerge($asset, $asset_group_id);
                    $asset = $this->controller->Asset->AssetGroup->AssetGroupCoa->getMerge($asset, $asset_group_id, 'first', 'Asset');

                    $ak_penyusutan = $this->MkCommon->filterEmptyField($asset, 'Asset', 'ak_penyusutan', 0);
                    $is_truck = $this->MkCommon->filterEmptyField($asset, 'AssetGroup', 'is_truck');
                    $nilai_sisa = $this->MkCommon->filterEmptyField($asset, 'AssetGroup', 'nilai_sisa');
                    $umur_ekonomis = $this->MkCommon->filterEmptyField($asset, 'AssetGroup', 'umur_ekonomis');
                    $coa_id = $this->MkCommon->filterEmptyField($asset, 'AssetGroupCoa', 'coa_id');

                    $nilai_buku = $price - $ak_penyusutan;
                    $depr_bulan = 0;

                    if( !empty($umur_ekonomis) ) {
                        $depr_bulan = ( ( $price - $nilai_sisa ) / $umur_ekonomis );
                    }

                    if( !empty($depr_bulan) ) {
                        $depr_bulan = $depr_bulan / 12;
                    }

                    if( !empty($is_truck) ) {
                        $company = $this->controller->Asset->AssetGroup->PurchaseOrderAsset->Truck->Company->getData('first', array(
                            'conditions' => array(
                                'Company.name LIKE' => '%code%'
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
                            'description' => $note,
                            'is_asset' => 1,
                        );
                    }

                    $dataSave['PurchaseOrderAsset'][$key]['Asset'] = array(
                        'id' => $asset_id,
                        'branch_id' => Configure::read('__Site.config_branch_id'),
                        'truck_id' => $truck_id,
                        'asset_group_id' => $asset_group_id,
                        'name' => $name,
                        'purchase_date' => $transaction_date,
                        'neraca_date' => $transaction_date,
                        'nilai_perolehan' => $price,
                        'depr_bulan' => $depr_bulan,
                        'nilai_buku' => $nilai_buku,
                        'note' => $note,
                        'is_po' => true,
                    );

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
        $id = Common::hashEmptyField($data, 'PurchaseOrder.id');

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

        if( !empty($id) ) {
            $this->MkCommon->getLogs($this->controller->params['controller'], array( 'purchase_order_add', 'purchase_order_edit', 'purchase_order_toggle' ), $id);
        }

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
            $this->MkCommon->_callAllowClosing($data, 'AssetSell', 'transaction_date');

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
        $id = Common::hashEmptyField($data, 'AssetSell.id');

        if( !empty($data) ) {
            $data = $this->MkCommon->dataConverter($data, array(
                'date' => array(
                    'AssetSell' => array(
                        'transaction_date',
                        'transfer_date',
                    ),
                ),
            ), true);
            $data_empty = false;
        } else {
            $data['AssetSell']['transaction_date'] = date('d/m/Y');
            $data['AssetSell']['transfer_date'] = date('d/m/Y');
            $data_empty = true;
        }

        $coas = $this->controller->GroupBranch->Branch->BranchCoa->getCoas();
        
        $cogs_result = $this->MkCommon->_callCogsOptGroup('AssetSell');
        $cogs_id = Common::hashEmptyField($cogs_result, 'cogs_id');
        
        if( !empty($data_empty) ) {
            $data['AssetSell']['cogs_id'] = $cogs_id;
        }

        if( !empty($id) ) {
            $this->MkCommon->getLogs($this->controller->params['controller'], array( 'sell_add', 'sell_edit', 'sell_toggle' ), $id);
        }

        $this->MkCommon->_layout_file(array(
            'select',
        ));

        $this->controller->set(compact(
            'coas'
        ));

        return $data;
    }

    function _callBeforeSaveDepreciation ( $queue_id = null, $value, $periode, $branch_id, $user_id ) {
        $dataSave = false;

        if( !empty($value) ) {
            // $periode_short = $this->MkCommon->customDate($periode, 'F Y');
            // $old_depr_id = $this->MkCommon->filterEmptyField($old_date, 'AssetDepreciation', 'id', 0);
            // $old_depr_bulan = $this->MkCommon->filterEmptyField($old_date, 'AssetDepreciation', 'depr_bulan', 0);

            $asset_group_id = $this->MkCommon->filterEmptyField($value, 'Asset', 'asset_group_id');
            // $accumulationDeprAcc = $this->controller->Asset->AssetGroup->AssetGroupCoa->getMerge($value, $asset_group_id, 'first', 'AccumulationDepr');
            // $depresiasiAcc = $this->controller->Asset->AssetGroup->AssetGroupCoa->getMerge($value, $asset_group_id, 'first', 'Depresiasi');

            // $accumulationDeprAccId = $this->MkCommon->filterEmptyField($accumulationDeprAcc, 'AssetGroupCoa', 'coa_id');
            // $depresiasiAccId = $this->MkCommon->filterEmptyField($depresiasiAcc, 'AssetGroupCoa', 'coa_id');

            $id = $this->MkCommon->filterEmptyField($value, 'Asset', 'id');
            $name = $this->MkCommon->filterEmptyField($value, 'Asset', 'name');
            $nilai_buku = $this->MkCommon->filterEmptyField($value, 'Asset', 'nilai_buku');
            $nilai_perolehan = $this->MkCommon->filterEmptyField($value, 'Asset', 'nilai_perolehan');
            $depr_bulan = $this->MkCommon->filterEmptyField($value, 'Asset', 'depr_bulan');

            if( !empty($value['AssetDepreciation']['id']) ) {
                $last_depr_bulan = $this->MkCommon->filterEmptyField($value, 'AssetDepreciation', 'depr_bulan');
                $ak_penyusutan = $this->MkCommon->filterEmptyField($value, 'AssetDepreciation', 'ak_penyusutan', 0) - $last_depr_bulan;
                
                $ak_penyusutan = $ak_penyusutan + $depr_bulan;
                $nilai_buku = $nilai_perolehan - $ak_penyusutan;
            } else {
                $ak_penyusutan = $this->MkCommon->filterEmptyField($value, 'Asset', 'ak_penyusutan');

                if( $depr_bulan > $nilai_buku  ) {
                    $ak_penyusutan = $nilai_perolehan;
                    $depr_bulan = $nilai_buku;
                    $nilai_buku = 0;
                } else {
                    $ak_penyusutan = $ak_penyusutan + $depr_bulan;
                    $nilai_buku = $nilai_buku - $depr_bulan;
                }
            }

            // $ak_penyusutan = $ak_penyusutan - $old_depr_bulan + $depr_bulan;
            // $nilai_buku = $nilai_buku + $old_depr_bulan - $depr_bulan;

            // $journal_title = sprintf(__('Depresiasi Asset %s - %s'), $name, $periode_short);
            // $journal_options = array(
            //     'branch_id' => $branch_id,
            //     'user_id' => $user_id,
            //     'title' => $journal_title,
            //     'type' => 'depr_asset',
            //     'date' => date('Y-m-t', strtotime($periode)),
            // );

            if( $nilai_buku < 0 ) {
                $nilai_buku = 0;
            }

            $dataSave = array(
                'Asset' => array(
                    'id' => $id,
                    'ak_penyusutan' => $ak_penyusutan,
                    'nilai_buku' => $nilai_buku,
                ),
                'AssetDepreciation' => array(
                    array(
                        'AssetDepreciation' => array(
                            'queue_id' => $queue_id,
                            'user_id' => $user_id,
                            'asset_id' => $id,
                            'depr_bulan' => $depr_bulan,
                            'ak_penyusutan' => $ak_penyusutan,
                            'periode' => $periode,
                        ),
                        // 'Journal' => array(
                        //     array(
                        //         'Journal' => array_merge($journal_options, array(
                        //             'coa_id' => $depresiasiAccId,
                        //             'debit' => $depr_bulan,
                        //         )),
                        //     ),
                        //     array(
                        //         'Journal' => array_merge($journal_options, array(
                        //             'coa_id' => $accumulationDeprAccId,
                        //             'credit' => $depr_bulan,
                        //         )),
                        //     ),
                        // ),
                    ),
                ),
            );

            // if( !empty($old_depr_id) ) {
            //     $dataSave['AssetDepreciation'][] = array(
            //         'AssetDepreciation' => array(
            //             'id' => $old_depr_id,
            //             'status' => 0,
            //         ),
            //         'Journal' => array(
            //             array(
            //                 'Journal' => array_merge($journal_options, array(
            //                     'document_id' => $old_depr_id,
            //                     'title' => __('<i>Pembatalan</i> ').$journal_title,
            //                     'coa_id' => $accumulationDeprAccId,
            //                     'debit' => $depr_bulan,
            //                     'type' => 'void_depr_asset',
            //                 )),
            //             ),
            //             array(
            //                 'Journal' => array_merge($journal_options, array(
            //                     'document_id' => $old_depr_id,
            //                     'title' => __('<i>Pembatalan</i> ').$journal_title,
            //                     'coa_id' => $depresiasiAccId,
            //                     'credit' => $depr_bulan,
            //                     'type' => 'void_depr_asset',
            //                 )),
            //             ),
            //         ),
            //     );
            // }
        }

        return $dataSave;
    }
}
?>