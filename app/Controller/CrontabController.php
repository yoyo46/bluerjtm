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
        	$limit = 2;

    		$assets = $this->Asset->getData('all', array_merge($options, array(
        		'conditions' => array(
        			'Asset.nilai_buku >' => 0,
        			'Asset.id >' => $last_asset_id,
    			),
    			'limit' => $limit,
			)), $status);

    		if( !empty($assets) ) {
    			$dataSave = array();
    			$progress += ((count($assets)/$cnt_asset) * 100) / 2;

    			foreach ($assets as $key => $asset) {
            		$id = $this->MkCommon->filterEmptyField($asset, 'Asset', 'id');

    				$dataSave[] = $this->RjAsset->_callBeforeSaveDepreciation($asset, $periode, $branch_id, $user_id);
    			}

	    		$result = $this->Asset->doDepreciation($dataSave);
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

                    if( !empty($values) ) {
                        $dataValue = array();
                        $dataSaldoAkhir = array();
                        $this->User->Journal->Coa->CoaClosing->updateAll(array(
                            'CoaClosing.status'=> 0,
                        ), array(
                            'CoaClosing.status' => 1,
                            'DATE_FORMAT(CoaClosing.periode, \'%Y-%m\')'=> $closingPeriod,
                        ));

                        foreach ($values as $key => $value) {
                            $coa_id = $this->MkCommon->filterEmptyField($value, 'Journal', 'coa_id');
                            $saldo_debit = $this->MkCommon->filterEmptyField($value, 'Journal', 'saldo_debit', 0);
                            $saldo_credit = $this->MkCommon->filterEmptyField($value, 'Journal', 'saldo_credit', 0);

                            $value = $this->User->Journal->Coa->getMerge($value, $coa_id);
                            $coa_type = $this->MkCommon->filterEmptyField($value, 'Coa', 'type');
                            $last_closing = $this->User->Journal->Coa->CoaClosing->getData('first', array(
                                'conditions' => array(
                                    'DATE_FORMAT(CoaClosing.periode, \'%Y-%m\')' => $lastPeriod,
                                    'CoaClosing.coa_id' => $coa_id,
                                ),
                                'contain' => false,
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

                            $saldo_awal = $this->MkCommon->filterEmptyField($last_closing, 'CoaClosing', 'saldo_akhir', 0);

                            if( $coa_type == 'credit' ) {
                                $saldo_akhir = $saldo_awal + $saldo_credit - $saldo_debit;
                            } else {
                                $saldo_akhir = $saldo_awal + $saldo_debit - $saldo_credit;
                            }

                            $dataSaldoAkhir[$coa_id][$closingPeriod] = $saldo_akhir;
                            $dataValue[] = array(
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

                        if( !$this->User->Journal->Coa->CoaClosing->saveAll($dataValue) ) {
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
                // 'Ttuj.is_sj_completed' => 0,
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
                                'is_sj_completed' => 1,
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
