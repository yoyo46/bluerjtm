<?php
App::uses('AppController', 'Controller');

class CrontabController extends AppController {
	public $components = array(
		'RjAsset',
	);
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow(array(
			'closing_bank', 'generate_sj',
		));

		$this->autoLayout = false;
		$this->autoRender = false;
	}

	function closing_bank () {
        $this->loadModel('CoaClosingQueue');
    	$flag = array(
    		'status' => true
		);
        $value = $this->CoaClosingQueue->getData('first', false, array(
        	'status' => 'pending',
    	));
    	$progress = 0;

    	if( !empty($value) ) {
    		// Depr Asset
        	$this->loadModel('Asset');

            $queue_id = $this->MkCommon->filterEmptyField($value, 'CoaClosingQueue', 'id');
            $periode = $this->MkCommon->filterEmptyField($value, 'CoaClosingQueue', 'periode');
            $branch_id = $this->MkCommon->filterEmptyField($value, 'CoaClosingQueue', 'branch_id');
            $user_id = $this->MkCommon->filterEmptyField($value, 'CoaClosingQueue', 'user_id');
            $progress_closing = $this->MkCommon->filterEmptyField($value, 'CoaClosingQueue', 'progress', 0);
            $is_journal = $this->MkCommon->filterEmptyField($value, 'CoaClosingQueue', 'is_journal', 0);

			$last_depreciation = $this->Asset->AssetDepreciation->getData('first', array(
				'conditions' => array(
                    'AssetDepreciation.queue_id' => $queue_id,
					'AssetDepreciation.periode' => $periode,
				),
	            'order' => array(
	                'AssetDepreciation.asset_id' => 'DESC',
	            ),
			));
            $last_asset_id = $this->MkCommon->filterEmptyField($last_depreciation, 'AssetDepreciation', 'asset_id', 0);

        	$options = array(
        		'conditions' => array(
        			'Asset.nilai_buku >' => 0,
    			),
    			'order' => array(
    				'Asset.id' => 'ASC',
				),
    		);
    		$status = array(
        		'branch' => false,
        		'status' => 'available',
    		);

        	$cnt_asset = $this->Asset->getData('count', $options, $status);
        	$limit = 200;

            $this->Asset->bindModel(array(
                'hasOne' => array(
                    'AssetDepreciation' => array(
                        'foreignKey' => 'asset_id',
                    ),
                )
            ), false);
            $this->Asset->unBindModel(array(
                'hasMany' => array(
                    'AssetDepreciation'
                )
            ));

    		$assets = $this->Asset->getData('all', array_merge($options, array(
        		'conditions' => array(
                    'OR' => array(
                        'Asset.nilai_buku >' => 0,
                        'AssetDepreciation.id NOT' => NULL,
                    ),
                    'Asset.id >' => $last_asset_id,
        			// 'Asset.id' => 186,
    			),
                'contain' => array(
                    'AssetDepreciation' => array(
                        'conditions' => array(
                            'AssetDepreciation.periode' => $periode,
                            'AssetDepreciation.status' => 1,
                        ),
                    ),
                ),
    			'limit' => $limit,
			)), $status);
                // debug($assets);die();

    		if( !empty($assets) ) {
    			$dataSave = array();
    			$progress += ((count($assets)/$cnt_asset) * 100) / 2;
                $periode_short = $this->MkCommon->customDate($periode, 'F Y');

                $this->Asset->bindModel(array(
                    'hasMany' => array(
                        'AssetDepreciation' => array(
                            'foreignKey' => 'asset_id',
                        ),
                    )
                ), false);
                $this->Asset->unBindModel(array(
                    'hasOne' => array(
                        'AssetDepreciation'
                    )
                ));

                $last_gl = $this->User->GeneralLedger->getData('first', array(
                    'conditions' => array(
                        'GeneralLedger.is_closing' => 1,
                        'DATE_FORMAT(GeneralLedger.transaction_date, \'%Y-%m\') >='=> $this->MkCommon->customDate($periode, 'Y-m'),
                    ),
                ));
                $nodoc = $this->MkCommon->filterEmptyField($last_gl, 'GeneralLedger', 'nodoc', $this->User->GeneralLedger->generateNoDoc());
                
                $journal_title = sprintf(__('Depresiasi Asset - %s'), $periode_short);
                $groupDepr = array(
                    'GeneralLedger' => array(
                        'branch_id' => $branch_id,
                        'user_id' => $user_id,
                        'nodoc' => $nodoc,
                        'transaction_date' => date('Y-m-t', strtotime($periode)),
                        'note' => $journal_title,
                        'transaction_status' => 'posting',
                        'is_closing' => 1,
                    ),
                );
                $debit_total = 0;
                $creadit_total = 0;

    			foreach ($assets as $key => $asset) {
            		$id = $this->MkCommon->filterEmptyField($asset, 'Asset', 'id');
                    $asset_group_id = $this->MkCommon->filterEmptyField($asset, 'Asset', 'asset_group_id');
                    $last_depreciation = $this->MkCommon->filterEmptyField($asset, 'AssetDepreciation', 'id');

                    if( !empty($last_depreciation) ) {
                        $depr_bulan = $this->MkCommon->filterEmptyField($asset, 'AssetDepreciation', 'depr_bulan', 0);
                    } else {
                        $depr_bulan = $this->MkCommon->filterEmptyField($asset, 'Asset', 'depr_bulan', 0);
                    }

                    $depresiasiAcc = $this->Asset->AssetGroup->AssetGroupCoa->getMerge($asset, $asset_group_id, 'first', 'Depresiasi');
                    $accumulationDeprAcc = $this->Asset->AssetGroup->AssetGroupCoa->getMerge($asset, $asset_group_id, 'first', 'AccumulationDepr');

                    $depresiasiAccId = $this->MkCommon->filterEmptyField($depresiasiAcc, 'AssetGroupCoa', 'coa_id');
                    $accumulationDeprAccId = $this->MkCommon->filterEmptyField($accumulationDeprAcc, 'AssetGroupCoa', 'coa_id');
                    
                    $debit_alias = __('%s-%s', $asset_group_id, $depresiasiAccId);
                    $credit_alias = __('%s-%s', $asset_group_id, $accumulationDeprAccId);
                    $debit = !empty($groupDepr['GeneralLedgerDetail'][$debit_alias]['GeneralLedgerDetail']['debit'])?$groupDepr['GeneralLedgerDetail'][$debit_alias]['GeneralLedgerDetail']['debit']:0;
                    $credit = !empty($groupDepr['GeneralLedgerDetail'][$credit_alias]['GeneralLedgerDetail']['credit'])?$groupDepr['GeneralLedgerDetail'][$credit_alias]['GeneralLedgerDetail']['credit']:0;

                    $debit_total = $depr_bulan+$debit;
                    $credit_total = $depr_bulan+$credit;

                    $groupDepr['GeneralLedgerDetail'][$debit_alias]['GeneralLedgerDetail'] = array(
                        'coa_id' => $depresiasiAccId,
                        'debit' => $debit_total,
                    );
                    $groupDepr['GeneralLedgerDetail'][$credit_alias]['GeneralLedgerDetail'] = array(
                        'coa_id' => $accumulationDeprAccId,
                        'credit' => $credit_total,
                    );

    				$dataSave[] = $this->RjAsset->_callBeforeSaveDepreciation($queue_id, $asset, $periode, $branch_id, $user_id);
    			}

                $groupDepr['GeneralLedger']['debit_total'] = $debit_total;
                $groupDepr['GeneralLedger']['credit_total'] = $credit_total;

	    		$result = $this->Asset->doDepreciation($dataSave, $periode, $groupDepr);
                $status = $this->MkCommon->filterEmptyField($result, 'status');
	            $this->MkCommon->setProcessSave($result);

	            if( $status == 'error' ) {
	            	$flag = array(
			    		'status' => false,
			    		'type' => 'depreciation',
					);
	            }
    		} else {
    			$progress += 50;
    		}

            $status = $this->MkCommon->filterEmptyField($flag, 'status');
            $this->CoaClosingQueue->id = $queue_id;

            if( empty($is_journal) ) {
                if( !empty($status) ) {
                    // Closing Bank
                    $closingPeriod = $this->MkCommon->customDate($periode, 'Y-m');
                    $monthClosingPeriod = intval($this->MkCommon->customDate($periode, 'm'));
                    $lastPeriod = date('Y-m', strtotime($closingPeriod." -1 month"));

                    $this->User->Journal->virtualFields['saldo_debit'] = 'SUM(Journal.debit)';
                    $this->User->Journal->virtualFields['saldo_credit'] = 'SUM(Journal.credit)';
                    
                    $values = $this->User->Journal->getData('all', array(
                        'conditions' => array(
                            'DATE_FORMAT(Journal.date, \'%Y-%m\')' => $closingPeriod,
                            'Journal.coa_id <>' => 0,
                        ),
                        'group' => array(
                            'Journal.coa_id',
                        ),
                        'contain' => false,
                    ));
                    $month_closing = Configure::read('__Site.Closing.Year.SettingGeneral.value');

                    if( !empty($values) ) {
                        $dataValue = array();
                        $dataSaldoAkhir = array();
                        $this->User->Journal->Coa->CoaClosing->updateAll(array(
                            'CoaClosing.status'=> 0,
                        ), array(
                            'CoaClosing.status' => 1,
                            'DATE_FORMAT(CoaClosing.periode, \'%Y-%m\') >='=> $closingPeriod,
                        ));

                        foreach ($values as $key => $value) {
                            $coa_id = $this->MkCommon->filterEmptyField($value, 'Journal', 'coa_id');
                            $saldo_debit = $this->MkCommon->filterEmptyField($value, 'Journal', 'saldo_debit', 0);
                            $saldo_credit = $this->MkCommon->filterEmptyField($value, 'Journal', 'saldo_credit', 0);

                            $value = $this->User->Journal->Coa->getMerge($value, $coa_id);
                            $periode_reset = $this->MkCommon->filterEmptyField($value, 'Coa', 'periode_reset');
                            $coa_type = $this->MkCommon->filterEmptyField($value, 'Coa', 'type');
                            $balance = $this->MkCommon->filterEmptyField($value, 'Coa', 'balance', 0);
                            $last_closing = $this->User->Journal->Coa->CoaClosing->getData('first', array(
                                'conditions' => array(
                                    'DATE_FORMAT(CoaClosing.periode, \'%Y-%m\') <=' => $lastPeriod,
                                    'CoaClosing.coa_id' => $coa_id,
                                ),
                                'contain' => false,
                                'order'=> array(
                                    'DATE_FORMAT(CoaClosing.periode, \'%Y-%m\')' => 'DESC',
                                ),
                            ));
                            $already_closing = $this->User->Journal->Coa->CoaClosing->getData('all', array(
                                'conditions' => array(
                                    'DATE_FORMAT(CoaClosing.periode, \'%Y-%m\') >' => $closingPeriod,
                                    'CoaClosing.coa_id' => $coa_id,
                                ),
                                'contain' => false,
                                'order' => array(
                                    'CoaClosing.periode' => 'ASC',
                                ),
                            ));

                            $closing_periode_reset = $this->MkCommon->filterEmptyField($last_closing, 'CoaClosing', 'periode_reset');

                            if( !empty($closing_periode_reset) ) {
                                $saldo_awal = $balance;
                            } else {
                                $saldo_awal = $this->MkCommon->filterEmptyField($last_closing, 'CoaClosing', 'saldo_akhir', $balance);
                            }

                            if( $coa_type == 'credit' ) {
                                $saldo_akhir = $saldo_awal + $saldo_credit - $saldo_debit;
                            } else {
                                $saldo_akhir = $saldo_awal + $saldo_debit - $saldo_credit;
                            }

                            $dataSaldoAkhir[$coa_id][$closingPeriod] = $saldo_akhir;

                            $coaDataValue = array(
                                'CoaClosing' => array(
                                    'user_id' => $user_id,
                                    'coa_id' => $coa_id,
                                    'periode' => $this->MkCommon->customDate($closingPeriod, 'Y-m-t'),
                                    'saldo_debit' => $saldo_debit,
                                    'saldo_credit' => $saldo_credit,
                                    'saldo_awal' => $saldo_awal,
                                    'saldo_akhir' => $saldo_akhir,
                                ),
                            );

                            if( !empty($periode_reset) ) {
                                if( $periode_reset == 'yearly' && $monthClosingPeriod == $month_closing ) {
                                    $coaDataValue['CoaClosing']['Coa'] = array(
                                        'id' => $coa_id,
                                        'balance' => 0,
                                    );
                                    $coaDataValue['CoaClosing']['periode_reset'] = $periode_reset;
                                } else if( $periode_reset == 'monthly' ) {
                                    $coaDataValue['CoaClosing']['Coa'] = array(
                                        'id' => $coa_id,
                                        'balance' => 0,
                                    );
                                    $coaDataValue['CoaClosing']['periode_reset'] = $periode_reset;
                                }
                            }

                            $dataValue[] = $coaDataValue;

                            if( !empty($already_closing) ) {
                                foreach ($already_closing as $key => $already) {
                                    $already_id = $this->MkCommon->filterEmptyField($already, 'CoaClosing', 'id');
                                    $already_coa_id = $this->MkCommon->filterEmptyField($already, 'CoaClosing', 'coa_id');
                                    $already_periode = $this->MkCommon->filterEmptyField($already, 'CoaClosing', 'periode');
                                    $already_debit = $this->MkCommon->filterEmptyField($already, 'CoaClosing', 'saldo_debit');
                                    $already_credit = $this->MkCommon->filterEmptyField($already, 'CoaClosing', 'saldo_credit');
                                    $alreadyClosingPeriod = $this->MkCommon->customDate($already_periode, 'Y-m');
                                    $alreadyLastPeriod = date('Y-m', strtotime($alreadyClosingPeriod." -1 month"));
                                    $alreadySaldoAwal = $this->MkCommon->filterEmptyField($dataSaldoAkhir, $already_coa_id, $alreadyLastPeriod, 0);
                                    
                                    $already = $this->User->Journal->Coa->getMerge($already, $already_coa_id);
                                    $already_coa_type = $this->MkCommon->filterEmptyField($already, 'Coa', 'type');
                                    
                                    if( $coa_type == 'credit' ) {
                                        $already_saldo_akhir = $alreadySaldoAwal + $already_credit - $already_debit;
                                    } else {
                                        $already_saldo_akhir = $alreadySaldoAwal + $already_debit - $already_credit;
                                    }

                                    $dataValue[] = array(
                                        'CoaClosing' => array(
                                            'id' => $already_id,
                                            'saldo_awal' => $alreadySaldoAwal,
                                            'saldo_akhir' => $already_saldo_akhir,
                                        ),
                                    );
                                    $dataSaldoAkhir[$already_coa_id][$alreadyClosingPeriod] = $already_saldo_akhir;
                                }
                            }
                        }

                        if(!$this->User->Journal->Coa->CoaClosing->saveAll($dataValue, array(
                            'deep' => true,
                        ))) {
                            $this->MkCommon->_saveLog(array(
                                'activity' => __('Gagal melakukan closing bank'),
                                'old_data' => $dataValue,
                            ));
                            $flag = array(
                                'status' => false,
                                'type' => 'journal',
                            );
                        }
                    }
                    
                    $progress += 50;
                }
                
                $this->CoaClosingQueue->set('is_journal', 1);
            }

            $progress += $progress_closing;

	        if( $progress >= 100 ) {
	        	$progress = 100;
            	$this->CoaClosingQueue->set('transaction_status', 'completed');
	        } else {
                $this->CoaClosingQueue->set('transaction_status', 'progress');
            }

            $this->CoaClosingQueue->set('progress', $progress);
            $this->CoaClosingQueue->save();

            $status = $this->MkCommon->filterEmptyField($flag, 'status');

            if( !empty($status) ) {
    			echo __('Berhasil melakukan closing bank ').$progress;
            } else {
    			echo __('Gagal melakukan closing bank');
            }
    	} else {
    		echo __('Data tidak tersedia');
    	}

    	die();
	}

    function generate_sj () {
        $this->loadModel('Ttuj');
        $values = $this->Ttuj->getData('all', array(
            'conditions' => array(
                'Ttuj.is_draft' => 0,
                // 'Ttuj.status_sj' => 'none',
                'DATE_FORMAT(Ttuj.ttuj_date, \'%Y-%m\') <=' => '2016-05',
            ),
            'group' => array(
                'Ttuj.ttuj_date',
            ),
            // 'limit' => Configure::read('__Site.config_pagination'),
            'order' => array(
                'Ttuj.ttuj_date' => 'ASC',
                'Ttuj.id' => 'ASC',
            ),
        ), true, array(
            'plant' => false,
            'branch' => false,
        ));

        if( !empty($values) ) {
            foreach ($values as $key => $value) {
                $ttuj_date = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'ttuj_date');

                $dataSave = array(
                    'SuratJalan' => array(
                        'branch_id' => 15,
                        'nodoc' => '',
                        'tgl_surat_jalan' => $ttuj_date,
                    ),
                );

                $ttujs = $this->Ttuj->getData('all', array(
                    'conditions' => array(
                        'Ttuj.is_draft' => 0,
                        'DATE_FORMAT(Ttuj.ttuj_date, \'%Y-%m-%d\')' => $ttuj_date,
                    ),
                    'order' => array(
                        'Ttuj.ttuj_date' => 'ASC',
                        'Ttuj.id' => 'ASC',
                    ),
                ), true, array(
                    'plant' => false,
                    'branch' => false,
                ));

                if( !empty($ttujs) ) {
                    foreach ($ttujs as $key => $ttuj) {
                        $ttuj_id = $this->MkCommon->filterEmptyField($ttuj, 'Ttuj', 'id');
                        $qty = $this->Ttuj->TtujTipeMotor->getTotalMuatan( $ttuj_id );

                        $dataSave['SuratJalanDetail'][] = array(
                            'SuratJalanDetail' => array(
                                'ttuj_id' => $ttuj_id,
                                'qty' => $qty,
                            ),
                            'Ttuj' => array(
                                'id' => $ttuj_id,
                                'status_sj' => 'full',
                            ),
                        );
                    }

                    if($this->Ttuj->SuratJalanDetail->SuratJalan->saveAll($dataSave, array(
                        'deep' => true,
                    ))) {
                        printf(__('Berhasil generate surat jalan utk tgl ttuj %s<br><br>'), $ttuj_date);
                    } else {
                        printf(__('Gagal generate surat jalan utk tgl ttuj %s<br><br>'), $ttuj_date);
                    }
                } else {
                    printf(__('Ttuj tidak ditemukan utk tgl %s<br><br>'), $ttuj_date);
                }
            }
        } else {
                printf(__('Ttuj tidak ditemukan<br><br>'));
        }
    }
}
