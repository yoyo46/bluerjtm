<?php
class AssetSell extends AppModel {
	var $name = 'AssetSell';

    var $belongsTo = array(
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
        ),
        'Coa' => array(
            'className' => 'Coa',
            'foreignKey' => 'coa_id',
        ),
    );

    var $hasMany = array(
        'AssetSellDetail' => array(
            'className' => 'AssetSellDetail',
            'foreignKey' => 'asset_sell_id',
        ),
    );

	var $validate = array(
        'nodoc' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No dokumen harap diisi'
            ),
            'unique' => array(
                'rule' => 'isUnique',
                'message' => 'No dokumen sudah terdaftar, mohon masukkan no dokumen lain.'
            ),
        ),
        'coa_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Kas/Bank harap dipilih'
            ),
        ),
        'transaction_date' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl penjualan harap dipilih'
            ),
        ),
        'transfer_date' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl transfer harap dipilih'
            ),
        ),
	);

	function getData( $find, $options = false, $elements = false ){
        $branch = isset($elements['branch'])?$elements['branch']:true;
        $status = isset($elements['status'])?$elements['status']:'active';

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'AssetSell.status' => 'DESC',
                'AssetSell.created' => 'DESC',
                'AssetSell.id' => 'DESC',
            ),
            'fields' => array(),
            'contain' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'active':
                $default_options['conditions']['AssetSell.status'] = 1;
                $default_options['conditions']['AssetSell.transaction_status <>'] = 'void';
                break;
            case 'unposting':
                $default_options['conditions']['AssetSell.status'] = 1;
                $default_options['conditions']['AssetSell.transaction_status'] = 'unposting';
                break;
            case 'void-active':
                $default_options['conditions']['AssetSell.status'] = 1;
                break;
            case 'non-active':
                $default_options['conditions']['AssetSell.status'] = 0;
                break;
            default:
                $default_options['conditions']['AssetSell.status'] = array( 0, 1 );
                break;
        }

        if( !empty($branch) ) {
            $default_options['conditions']['AssetSell.branch_id'] = Configure::read('__Site.config_branch_id');
        }

        if( !empty($options) ) {
            if(!empty($options['conditions'])){
                $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
            }
            if(!empty($options['order'])){
                $default_options['order'] = $options['order'];
            }
            if( isset($options['contain']) && empty($options['contain']) ) {
                $default_options['contain'] = false;
            } else if(!empty($options['contain'])){
                $default_options['contain'] = array_merge($default_options['contain'], $options['contain']);
            }
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
            }
            if(!empty($options['limit'])){
                $default_options['limit'] = $options['limit'];
            }
            if(!empty($options['group'])){
                $default_options['group'] = $options['group'];
            }
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function getMerge( $data, $id, $status = 'active' ){
        $data_merge = $this->getData('first', array(
            'conditions' => array(
                'AssetSell.id' => $id
            ),
        ), array(
            'status' => $status,
        ));

        if(!empty($data_merge)){
            $data = array_merge($data, $data_merge);
        }

        return $data;
    }

    public function _callRefineParams( $data = '', $default_options = false ) {
        $nodoc = !empty($data['named']['nodoc'])?$data['named']['nodoc']:false;
        $dateFrom = !empty($data['named']['DateFrom'])?$data['named']['DateFrom']:false;
        $dateTo = !empty($data['named']['DateTo'])?$data['named']['DateTo']:false;
        $status = !empty($data['named']['status'])?$data['named']['status']:false;

        if( !empty($dateFrom) || !empty($dateTo) ) {
            if( !empty($dateFrom) ) {
                $default_options['conditions']['DATE_FORMAT(AssetSell.transaction_date, \'%Y-%m-%d\') >='] = $dateFrom;
            }

            if( !empty($dateTo) ) {
                $default_options['conditions']['DATE_FORMAT(AssetSell.transaction_date, \'%Y-%m-%d\') <='] = $dateTo;
            }
        }
        if( !empty($nodoc) ) {
            $default_options['conditions']['AssetSell.nodoc LIKE'] = '%'.$nodoc.'%';
        }
        
        return $default_options;
    }

    function doDelete( $id ) {
        $result = false;
        $value = $this->getData('first', array(
            'conditions' => array(
                'PurchaseOrder.id' => $id,
            ),
        ));

        if ( !empty($value) ) {
            $sq_id = !empty($value['PurchaseOrder']['supplier_quotation_id'])?$value['PurchaseOrder']['supplier_quotation_id']:false;
            $nodoc = !empty($value['PurchaseOrder']['nodoc'])?$value['PurchaseOrder']['nodoc']:false;
            $default_msg = sprintf(__('menghapus PO #%s'), $nodoc);

            $this->id = $id;
            $this->set('status', 0);

            if( $this->save() ) {
                $this->SupplierQuotation->id = $sq_id;
                $this->SupplierQuotation->set('is_po', 0);
                $this->SupplierQuotation->save();

                $msg = sprintf(__('Berhasil %s'), $default_msg);
                $result = array(
                    'msg' => $msg,
                    'status' => 'success',
                    'Log' => array(
                        'activity' => $msg,
                        'old_data' => $value,
                    ),
                );
            } else {
                $msg = sprintf(__('Gagal %s'), $default_msg);
                $result = array(
                    'msg' => $msg,
                    'status' => 'error',
                    'Log' => array(
                        'activity' => $msg,
                        'old_data' => $value,
                        'error' => 1,
                    ),
                );
            }
        } else {
            $result = array(
                'msg' => __('Gagal menghapus PO. Data tidak ditemukan'),
                'status' => 'error',
            );
        }

        return $result;
    }

    function _callSetJournal ( $id, $data ) {
        $transaction_status = $this->filterEmptyField($data, 'AssetSell', 'transaction_status');

        if( $transaction_status == 'posting' ) {
            $grandtotal = $this->filterEmptyField($data, 'AssetSell', 'grandtotal');
            $transfer_date = $this->filterEmptyField($data, 'AssetSell', 'transfer_date');
            $nodoc = $this->filterEmptyField($data, 'AssetSell', 'nodoc');
            $coa_id = $this->filterEmptyField($data, 'AssetSell', 'coa_id');
            $cogs_id = $this->filterEmptyField($data, 'AssetSell', 'cogs_id');

            $this->User->Journal->deleteJournal($id, array(
                'asset_selling',
            ));

            $details = !empty($data['AssetSellDetail'])?$data['AssetSellDetail']:false;
            $titleJournal = sprintf(__('Penjualan asset #%s '), $nodoc);
            $titleJournal = $this->filterEmptyField($data, 'AssetSell', 'note', $titleJournal);
            $options = array(
                'cogs_id' => $cogs_id,
                'date' => $transfer_date,
                'document_id' => $id,
                'title' => $titleJournal,
                'document_no' => $nodoc,
                'type' => 'asset_selling',
            );

            $this->User->Journal->setJournal($grandtotal, array(
                'debit' => $coa_id,
            ), $options);

            if( !empty($details) ) {
                foreach ($details as $key => $value) {
                    $nilai_perolehan = $this->filterEmptyField($value, 'AssetSellDetail', 'nilai_perolehan');
                    $ak_penyusutan = $this->filterEmptyField($value, 'AssetSellDetail', 'ak_penyusutan');
                    $price = $this->filterEmptyField($value, 'AssetSellDetail', 'price');
                    $grandtotal = $nilai_perolehan - $price - $ak_penyusutan;

                    $nilai_perolehan_coa_id = $this->filterEmptyField($value, 'AssetSellDetail', 'nilai_perolehan_coa_id');
                    $ak_penyusutan_coa_id = $this->filterEmptyField($value, 'AssetSellDetail', 'ak_penyusutan_coa_id');
                    $price_coa_id = $this->filterEmptyField($value, 'AssetSellDetail', 'price_coa_id');

                    $this->User->Journal->setJournal($nilai_perolehan, array(
                        'credit' => $nilai_perolehan_coa_id,
                    ), $options);
                    $this->User->Journal->setJournal($ak_penyusutan, array(
                        'debit' => $ak_penyusutan_coa_id,
                    ), $options);

                    if( $grandtotal >= 0 ) {
                        $journalType = 'debit';
                    } else {
                        $journalType = 'credit';
                    }

                    $this->User->Journal->setJournal($grandtotal, array(
                        $journalType => $price_coa_id,
                    ), $options);
                }
            }

            // $details = !empty($data['AssetSellDetail'])?$data['AssetSellDetail']:false;
            // $titleJournal = sprintf(__('Penjualan asset #%s '), $nodoc);
            // $titleJournal = $this->filterEmptyField($data, 'AssetSell', 'note', $titleJournal);
            // $options = array(
            //     'date' => $transfer_date,
            //     'document_id' => $id,
            //     'title' => $titleJournal,
            //     'document_no' => $nodoc,
            //     'type' => 'asset_selling',
            // );

            // $this->User->Journal->setJournal($grandtotal, array(
            //     'debit' => $coa_id,
            // ), $options);
            // $this->User->Journal->setJournal($grandtotal_ak_penyusutan, array(
            //     'debit' => $ak_penyusutan_coa_id,
            // ), $options);
            // $this->User->Journal->setJournal($grandtotal_nilai_perolehan, array(
            //     'credit' => $nilai_perolehan_coa_id,
            // ), $options);


            // if( $grandtotalAll >= 0 ) {
            //     $journalType = 'debit';
            // } else {
            //     $journalType = 'credit';
            // }

            // $this->User->Journal->setJournal($grandtotalAll, array(
            //     $journalType => $price_coa_id,
            // ), $options);
        }
    }

    function doSave( $data, $value = false, $id = false ) {
        $msg = __('Gagal melakukan penjualan asset');

        if( !empty($data) ) {
            $flag = $this->saveAll($data, array(
                'validate' => 'only',
                'deep' => true,
            ));

            if( !empty($flag) ) {
                $flag = $this->AssetSellDetail->updateAll(array(
                    'AssetSellDetail.status' => 0,
                ), array(
                    'AssetSellDetail.asset_sell_id' => $id,
                ));

                if( !empty($flag) ) {
                    $msg = __('Berhasil melakukan penjualan asset');
                    $this->saveAll($data, array(
                        'deep' => true,
                    ));
                    $this->_callSetJournal($id, $data);

                    $result = array(
                        'msg' => $msg,
                        'status' => 'success',
                        'Log' => array(
                            'activity' => $msg,
                            'old_data' => $value,
                        ),
                        'data' => $data,
                    );
                } else {
                    $result = array(
                        'msg' => $msg,
                        'status' => 'error',
                        'Log' => array(
                            'activity' => $msg,
                            'old_data' => $value,
                            'error' => 1,
                        ),
                        'data' => $data,
                    );
                }
            } else {
                $result = array(
                    'msg' => $msg,
                    'status' => 'error',
                    'Log' => array(
                        'activity' => $msg,
                        'old_data' => $value,
                        'error' => 1,
                    ),
                    'data' => $data,
                );
            }
        } else {
            $result['data'] = $value;
        }

        return $result;
    }
}
?>