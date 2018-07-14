<?php
App::uses('AppController', 'Controller');
class CashbanksController extends AppController {
	public $uses = array(
        'CashBank', 'DocumentAuth'
    );
    public $components = array(
        'RjCashBank', 'RmReport'
    );
    public $helpers = array(
        'CashBank'
    );

    function beforeFilter() {
        parent::beforeFilter();
        $this->set('title_for_layout', __('ERP RJTM | Kas/Bank'));
        $this->set('module_title', __('Kas/Bank'));
    }

    function search( $index = 'index', $param_get = false ){
        $refine = array();

        if(!empty($this->request->data)) {
            $data = $this->request->data;
            $refine = $this->RjCashBank->processRefine($data);
            $result = $this->MkCommon->processFilter($data);
            $params = $this->RjCashBank->generateSearchURL($refine);
            $params = $this->MkCommon->getRefineGroupBranch($params, $data);

            $params = array_merge($params, $result);
            $params['action'] = $index;

            if( !empty($param_get) ) {
                $params[] = $param_get;
            }

            $this->redirect($params);
        }
        $this->redirect('/');
    }

    function index(){
        $this->loadModel('CashBank');
        $this->set('sub_module_title', 'index');
        
        $dateFrom = date('Y-m-d', strtotime('-1 Month'));
        $dateTo = date('Y-m-d');
        $conditionToApprove = $this->User->Employe->EmployePosition->Approval->_callGetDataToApprove('cash_bank');

        $named = $this->MkCommon->filterEmptyField($this->params, 'named');
        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));
        $options =  $this->CashBank->_callRefineParams($params, array(
            'conditions' => $conditionToApprove,
        ));
        $this->paginate = $this->CashBank->getData('paginate', $options, array(
            'branch' => false,
        ));
        $values = $this->paginate('CashBank');

        if(!empty($values)){
            foreach ($values as $key => $value) {
                $model = $this->MkCommon->filterEmptyField($value, 'CashBank', 'receiver_type');
                $receiver_id = $this->MkCommon->filterEmptyField($value, 'CashBank', 'receiver_id');

                $values[$key]['name_cash'] = $this->RjCashBank->_callReceiverName($receiver_id, $model);
            }
        }

        $coas = $this->User->Journal->Coa->_callOptGroup();
        $this->MkCommon->_layout_file('select');
        
        $this->set('active_menu', 'cash_bank');
        $this->set(compact(
            'values', 'coas'
        ));
    }

    public function cashbank_add() {
        $this->set('sub_module_title', 'Tambah transaksi Kas/Bank');
        $this->doCashBank();
    }

    public function cashbank_edit( $id = false ) {
        $this->set('sub_module_title', 'Rubah transaksi Kas/Bank');
        $coa = false;

        if( !empty($id) ) {
            $cashbank = $this->CashBank->getData('first', array(
                'conditions' => array(
                    'CashBank.id' => $id,
                    'CashBank.is_rejected' => 0,
                ),
                'contain' => array(
                    'CashBankDetail',
                    'DocumentAuth'
                )
            ), array(
                'branch' => false,
            ));

            if( !empty($cashbank) ) {
                $this->set('sub_module_title', 'Rubah Kas/Bank');
                $this->MkCommon->_callAllowClosing($cashbank, 'CashBank', 'tgl_cash_bank');
                $this->doCashBank( $id, $cashbank);
            } else {
                $this->MkCommon->setCustomFlash(__('Kas/Bank tidak ditemukan.'), 'error');
                $this->redirect($this->referer());
            }
        } 
    }

    public function cashbank_detail( $id = false ) {
        $this->set('sub_module_title', 'Detail Transaksi Kas/Bank');

        if( !empty($id) ) {
            $cashbank = $this->CashBank->getData('first', array(
                'conditions' => array(
                    'CashBank.id' => $id,
                ),
                'contain' => array(
                    'CashBankDetail',
                    'DocumentAuth',
                    'Coa'
                )
            ));

            if( !empty($cashbank) ) {
                $this->set('sub_module_title', 'Rubah Kas/Bank');
                $this->doCashBank( $id, $cashbank);
            } else {
                $this->MkCommon->setCustomFlash(__('Kas/Bank tidak ditemukan.'), 'error');
                $this->redirect($this->referer());
            }
        }  else {
            $this->MkCommon->setCustomFlash(__('Kas/Bank tidak ditemukan.'), 'error');
            $this->redirect($this->referer());
        }
    }

    function doCashBank($id = false, $data_local = false){
        $grand_total = $this->MkCommon->filterEmptyField($data_local, 'CashBank', 'grand_total');
        $user_id = $this->MkCommon->filterEmptyField($data_local, 'CashBank', 'user_id');
        $prepayment_out_id = $this->MkCommon->filterEmptyField($data_local, 'CashBank', 'document_id');

        if(!empty($this->request->data)){
            $data = $this->request->data;
            $data = $this->MkCommon->dataConverter($data, array(
                'date' => array(
                    'CashBank' => array(
                        'tgl_cash_bank',
                    ),
                )
            ));
            $this->MkCommon->_callAllowClosing($data, 'CashBank', 'tgl_cash_bank');

            $coas_validate = true;
            $totalTagihanCredit = 0;
            $totalTagihanDebit = 0;
            $debit_total = 0;
            $credit_total = 0;
            $total_coa = 0;
            $prepayment_status = false;

            $cogs_id = $this->MkCommon->filterEmptyField($data, 'CashBank', 'cogs_id');
            $document_id = $this->MkCommon->filterEmptyField($data, 'CashBank', 'document_id');
            $document_type = $this->MkCommon->filterEmptyField($data, 'CashBank', 'document_type');
            $document_no = $this->MkCommon->filterEmptyField($data, 'CashBank', 'nodoc');
            $document_coa_id = $this->MkCommon->filterEmptyField($data, 'CashBank', 'coa_id');
            $description = $this->MkCommon->filterEmptyField($data, 'CashBank', 'description');

            $receiver_id = $this->MkCommon->filterEmptyField($data, 'CashBank', 'receiver_id');
            $receiver_type = $this->MkCommon->filterEmptyField($data, 'CashBank', 'receiver_type');
            $tgl_cash_bank = $this->MkCommon->filterEmptyField($data, 'CashBank', 'tgl_cash_bank');
            $transaction_status = $this->MkCommon->filterEmptyField($data, 'CashBank', 'transaction_status');

            $data['CashBank']['is_revised'] = 0;
            $data['CashBank']['branch_id'] = Configure::read('__Site.config_branch_id');
            $data['CashBank']['user_id'] = $this->user_id;
            $redirectUrl = array(
                'controller' => 'cashbanks',
                'action' => 'cashbank_add',
                'admin' => false,
            );

            if($id && $data_local){
                $this->CashBank->id = $id;
                $msg = 'merubah';
            }else{
                $this->CashBank->create();
                $msg = 'menambah';
            }

            if( !empty($document_id) && $data['CashBank']['document_type'] == 'prepayment' ) {
                $this->loadModel('CashBankDetail');
            }

            if(!empty($data['CashBankDetail']['coa_id'])){
                $arr_list = array();

                foreach ($data['CashBankDetail']['coa_id'] as $key => $coa_id) {
                    // $total_coa_detail = (!empty($data['CashBankDetail']['total'][$key])) ? str_replace(array( '.', ',' ), array( '', '.' ), $data['CashBankDetail']['total'][$key]) : 0;
                    $total_coa_detail = (!empty($data['CashBankDetail']['total'][$key])) ? str_replace(array( ',' ), array( '' ), $data['CashBankDetail']['total'][$key]) : 0;
                    $document_detail_id = (!empty($data['CashBankDetail']['document_detail_id'][$key])) ? $data['CashBankDetail']['document_detail_id'][$key] : false;
                    $nopol = (!empty($data['CashBankDetail']['nopol'][$key])) ? $data['CashBankDetail']['nopol'][$key] : false;
                    $paid = false;

                    $valueTruck = $this->CashBank->CashBankDetail->Truck->getByNopol(array(), $nopol);
                    $truck_id = $this->MkCommon->filterEmptyField($valueTruck, 'Truck', 'id');

                    if( strstr($data['CashBank']['receiving_cash_type'], 'out') ){
                        $debit_total += $total_coa_detail;
                    }else{
                        $credit_total += $total_coa_detail;
                    }

                    if( !empty($document_id) && $data['CashBank']['document_type'] == 'prepayment' ) {
                        $cashBankDetail = $this->CashBankDetail->getData('first', array(
                            'conditions' => array(
                                'CashBankDetail.cash_bank_id' => $document_id,
                                'CashBankDetail.coa_id' => $coa_id,
                            ),
                        ));
                        $detail_id = !empty($cashBankDetail['CashBankDetail']['id'])?$cashBankDetail['CashBankDetail']['id']:false;
                        $totalDibayar = $this->CashBank->CashBankDetail->totalPrepaymentDibayarPerCoa($document_id, $coa_id, false, $detail_id);

                        $totalTagihanDetail = !empty($cashBankDetail['CashBankDetail']['total'])?$cashBankDetail['CashBankDetail']['total']-$totalDibayar:0;

                        if( $totalTagihanDetail > $total_coa_detail ) {
                            $paid = 'half_paid';
                        } else if( $totalTagihanDetail <= $total_coa_detail ) {
                            $paid = 'full_paid';
                        }
                    }

                    $arr_list[] = array(
                        'document_detail_id' => $document_detail_id,
                        'coa_id' => $coa_id,
                        'truck_id' => $truck_id,
                        'nopol' => $nopol,
                        'total' => $total_coa_detail,
                        'paid' => $paid,
                    );

                    $total_coa += $total_coa_detail;
                }

                $data['CashBankDetail'] = $arr_list;
                $data['CashBank']['debit_total'] = $debit_total;
                $data['CashBank']['credit_total'] = $credit_total;
            }else{
                $coas_validate = false;
            }

            $totalCashBank = $debit_total + $credit_total;

            if( !empty($document_id) && $data['CashBank']['document_type'] == 'prepayment' ) {
                $cashBankTagihan = $this->CashBank->getData('first', array(
                    'conditions' => array(
                        'CashBank.id' => $document_id,
                        'CashBank.prepayment_status <>' => 'full_paid',
                    ),
                ));

                if( !empty($cashBankTagihan) ) {
                    $totalTagihanDebit = !empty($cashBankTagihan['CashBank']['debit_total'])?$cashBankTagihan['CashBank']['debit_total']:0;
                    $totalTagihanCredit = !empty($cashBankTagihan['CashBank']['credit_total'])?$cashBankTagihan['CashBank']['credit_total']:0;
                    $totalDibayar = $this->CashBank->totalPrepaymentDibayar($document_id);
                    $totalTagihanCashBank = ($totalTagihanDebit + $totalTagihanCredit) - $totalDibayar;

                    if( $totalTagihanCashBank > $totalCashBank ) {
                        $prepayment_status = 'half_paid';
                    } else if( $totalTagihanCashBank <= $totalCashBank ) {
                        $prepayment_status = 'full_paid';
                    }
                } else {
                    $data['CashBank']['document_id'] = false;
                }
            }

            $allowApprovals = $this->User->Employe->EmployePosition->Approval->_callNeedApproval('cash-bank', $total_coa);

            if( $transaction_status == 'posting' && empty($allowApprovals) ) {
                $data['CashBank']['completed'] = 1;
            }

            $this->CashBank->set($data);

            if($this->CashBank->validates($data) && $coas_validate){
                if($this->CashBank->save($data)){
                    $cash_bank_id = $this->CashBank->id;
                    $receiving_cash_type = $data['CashBank']['receiving_cash_type'];

                    if( !empty($prepayment_status) && !empty($document_id) ) {
                        $this->CashBank->id = $document_id;
                        $this->CashBank->set('prepayment_status', $prepayment_status);
                        $this->CashBank->save();
                    }

                    if($id && $data_local){
                        $this->CashBank->CashBankDetail->deleteAll(array(
                            'CashBankDetail.cash_bank_id' => $cash_bank_id
                        ));

                        $this->CashBank->DocumentAuth->deleteAll(array(
                            'DocumentAuth.document_id' => $cash_bank_id,
                            'DocumentAuth.document_type' => 'cash_bank',
                        ));
                    }

                    if( !empty($document_id) ) {
                        switch ($document_type) {
                            case 'revenue':
                                $this->loadModel('Revenue');
                                $this->Revenue->changeStatusPPNPaid( $document_id, 1 );
                                break;
                        }
                    }

                    if(!empty($data['CashBankDetail'])){
                        $documentType = Configure::read('__Site.Journal.Documents');
                        $documentType = $this->MkCommon->filterEmptyField($documentType, $receiving_cash_type);
                        $receiver_name = $this->RjCashBank->_callReceiverName($receiver_id, $receiver_type);

                        if( !empty($description) ) {
                            $title = $description;
                        } else {
                            if( in_array($receiving_cash_type, array( 'out', 'ppn_out', 'prepayment_out' )) ) {
                                $title = sprintf(__('%s kepada %s'), $documentType, $receiver_name);
                            } else {
                                $title = sprintf(__('%s dari %s'), $documentType, $receiver_name);
                            }
                        }

                        foreach ($data['CashBankDetail'] as $key => $value) {
                            $value['cash_bank_id'] = $cash_bank_id;
                            $coa_id = $value['coa_id'];
                            $total = $value['total'];

                            $this->CashBank->CashBankDetail->create();
                            $this->CashBank->CashBankDetail->set($value);

                            if( $this->CashBank->CashBankDetail->save() ) {
                                if( !empty($value['paid']) ) {
                                    $this->CashBank->CashBankDetail->updateAll(array(
                                        'CashBankDetail.prepayment_paid'=> "'".$value['paid']."'"
                                    ), array(
                                        'CashBankDetail.cash_bank_id'=> $document_id,
                                        'CashBankDetail.coa_id'=> $coa_id,
                                    ));
                                }
                            }

                            if( $transaction_status == 'posting' && empty($allowApprovals) ) {
                                if( in_array($receiving_cash_type, array( 'out', 'ppn_out', 'prepayment_out' )) ) {
                                    $coaArr = array(
                                        'debit' => $coa_id
                                    );
                                } else {
                                    $coaArr = array(
                                        'credit' => $coa_id
                                    );
                                }

                                $this->User->Journal->setJournal($total, $coaArr, array(
                                    'cogs_id' => $cogs_id,
                                    'document_id' => $cash_bank_id,
                                    'title' => $title,
                                    'document_no' => $document_no,
                                    'type' => $receiving_cash_type,
                                    'date' => $tgl_cash_bank,
                                ));
                            }
                        }

                        if( $transaction_status == 'posting' && empty($allowApprovals) ) {
                            if( in_array($receiving_cash_type, array( 'out', 'ppn_out', 'prepayment_out' )) ) {
                                $coaArr = array(
                                    'credit' => $document_coa_id,
                                );
                            } else {
                                $coaArr = array(
                                    'debit' => $document_coa_id,
                                );
                            }

                            $this->User->Journal->setJournal($totalCashBank, $coaArr, array(
                                'cogs_id' => $cogs_id,
                                'document_id' => $cash_bank_id,
                                'title' => $title,
                                'document_no' => $document_no,
                                'type' => $receiving_cash_type,
                                'date' => $tgl_cash_bank,
                            ));
                        }
                    }

                    $this->params['old_data'] = $data_local;
                    $this->params['data'] = $data;

                    if( $transaction_status == 'posting' && !empty($allowApprovals) ) {
                        $this->MkCommon->_saveNotification(array(
                            'action' => __('Kas/Bank'),
                            'name' => sprintf(__('Kas/Bank dengan No Dokumen %s memerlukan ijin Approval'), $document_no),
                            'user_id' => $allowApprovals,
                            'document_id' => $cash_bank_id, 
                            'url' => array(
                                'controller' => 'cashbanks',
                                'action' => 'detail',
                                $cash_bank_id,
                                'admin' => false,
                            ),
                        ));
                    }

                    $this->Log->logActivity( sprintf(__('Sukses %s Kas/Bank #%s'), $msg, $this->CashBank->id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $cash_bank_id );

                    $noref = str_pad($this->CashBank->id, 6, '0', STR_PAD_LEFT);
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Kas/Bank #%s'), $msg, $noref), 'success');

                    $this->redirect($redirectUrl);
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Kas/Bank'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s Kas/Bank #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
                }
            }else{
                $text = sprintf(__('Gagal %s Kas/Bank'), $msg);
                if($coas_validate){
                    $text .= __(', COA Kas/Bank harap di pilih');
                }
                $this->MkCommon->setCustomFlash($text, 'error');
            }
        }else{
            if($id && $data_local){
                $data_local = $this->CashBank->getDataCashBank($data_local, $prepayment_out_id);

                $this->request->data = $data = $data_local;
                $this->request->data['CashBank']['tgl_cash_bank'] = $this->MkCommon->getDate($this->request->data['CashBank']['tgl_cash_bank'], true);
            } else {
                $lastCashBank = $this->CashBank->getData('first', array(
                    'conditions' => array(
                        'CashBank.user_id' => $this->user_id,
                    ),
                    'order' => array(
                        'CashBank.id' => 'DESC',
                        'CashBank.created' => 'DESC',
                    ),
                ), array(
                    'branch' => false,
                ));

                if( !empty($lastCashBank) ) {
                    $this->request->data['CashBank']['cogs_id'] = $this->MkCommon->filterEmptyField($lastCashBank, 'CashBank', 'cogs_id');
                    $this->request->data['CashBank']['coa_id'] = $this->MkCommon->filterEmptyField($lastCashBank, 'CashBank', 'coa_id');
                    $this->request->data['CashBank']['receiving_cash_type'] = $this->MkCommon->filterEmptyField($lastCashBank, 'CashBank', 'receiving_cash_type');
                    $this->request->data['CashBank']['receiver_type'] = $this->MkCommon->filterEmptyField($lastCashBank, 'CashBank', 'receiver_type');
                    $this->request->data['CashBank']['receiver_id'] = $this->MkCommon->filterEmptyField($lastCashBank, 'CashBank', 'receiver_id');
                }
            }
        }

        if(!empty($this->request->data['CashBank']['receiver_type'])){
            $model = $this->request->data['CashBank']['receiver_type'];
            $receiver_id = !empty($this->request->data['CashBank']['receiver_id'])?$this->request->data['CashBank']['receiver_id']:false;
            $this->request->data['CashBank']['receiver'] = $this->RjCashBank->_callReceiverName($receiver_id, $model);
        }

        if(!empty($data['CashBankDetail'])){
            $this->loadModel('Coa');

            foreach ($data['CashBankDetail'] as $key => $value) {
                $coa_id = $this->MkCommon->filterEmptyField($value, 'coa_id');
                $truck_id = $this->MkCommon->filterEmptyField($value, 'truck_id');
                
                $value = $this->User->Journal->Coa->getMerge($value, $coa_id);
                $value = $this->CashBank->CashBankDetail->Truck->getMerge($value, $truck_id);
                
                $code_coa = $this->MkCommon->filterEmptyField($value, 'Coa', 'code');
                $name_coa = $this->MkCommon->filterEmptyField($value, 'Coa', 'name');
                $nopol = $this->MkCommon->filterEmptyField($value, 'Truck', 'nopol');

                if(!empty($code_coa)){
                    $data['CashBankDetail'][$key]['name_coa'] = $name_coa;
                    $data['CashBankDetail'][$key]['code_coa'] = $code_coa;
                }

                if(!empty($nopol)){
                    $data['CashBankDetail'][$key]['nopol'] = $nopol;
                }
            }

            $coa_data['CashBankDetail'] = $data['CashBankDetail'];
            $this->set('coa_data', $coa_data);
        }

        $receiving_cash_type = $this->MkCommon->filterEmptyField($this->request->data, 'CashBank', 'receiving_cash_type');

        if( $receiving_cash_type == 'ppn_in' ) {
            $this->loadModel('Revenue');
            $docs_result = $this->Revenue->getDocumentCashBank( $receiving_cash_type );
            $urlBrowseDocument = array(
                'controller'=> 'ajax', 
                'action' => 'getCashBankPpnRevenue',
            );

            if( !empty($docs_result) ) {
                $docs = $docs_result['docs'];
                $this->request->data['CashBank']['document_type'] = $docs_result['docs_type'];
            }
        } else if( $receiving_cash_type == 'prepayment_in' ) {
            $docs_result = $this->CashBank->getDocumentCashBank($prepayment_out_id);
            $urlBrowseDocument = array(
                'controller'=> 'ajax', 
                'action' => 'getCashBankPrepayment',
                $prepayment_out_id,
            );

            if( !empty($docs_result) ) {
                $docs = $docs_result['docs'];
                $this->request->data['CashBank']['document_type'] = $docs_result['docs_type'];
            }
        }

        $user = $this->User->getData('first', array(
            'conditions' => array(
                'User.id' => $user_id,
            ),
        ));
        $employe_position_id = $this->MkCommon->filterEmptyField($user, 'Employe', 'employe_position_id');
        $user_otorisasi_approvals = $this->User->Employe->EmployePosition->Approval->getUserOtorisasiApproval('cash-bank', $employe_position_id, $grand_total, $id);

        $coas = $this->GroupBranch->Branch->BranchCoa->getCoas();
        $cogs = $this->MkCommon->_callCogsOptGroup('CashBank');
        $branches = $this->User->Branch->City->branchCities();

        if( !empty($id) ) {
            $this->MkCommon->getLogs($this->params['controller'], array( 'cashbank_edit', 'cashbank_add', 'cashbank_delete' ), $id);
        }
        
        $this->set('active_menu', 'cash_bank');
        $this->set('module_title', 'Kas/Bank');

        $this->MkCommon->_layout_file(array(
            'select',
            'typeahead',
        ));
        $this->set(compact(
            'coas', 'document_id', 'receiving_cash_type',
            'docs', 'urlBrowseDocument', 'prepayment_out_id',
            'id', 'data_local', 'branches',
            'user_otorisasi_approvals'
        ));

        $this->render('cashbank_form');
    }

    public function import() {
        App::import('Vendor', 'excelreader'.DS.'excel_reader2');

        $this->set('module_title', __('CashBank'));
        $this->set('active_menu', 'cash_bank');
        $this->set('sub_module_title', __('Import Excel'));

        if(!empty($this->request->data)) { 
            $Zipped = $this->request->data['Import']['importdata'];

            if($Zipped["name"]) {
                $filename = $Zipped["name"];
                $source = $Zipped["tmp_name"];
                $type = $Zipped["type"];
                $name = explode(".", $filename);
                $accepted_types = array('application/vnd.ms-excel', 'application/ms-excel');

                if(!empty($accepted_types)) {
                    foreach($accepted_types as $mime_type) {
                        if($mime_type == $type) {
                            $okay = true;
                            break;
                        }
                    }
                }

                $continue = strtolower($name[1]) == 'xls' ? true : false;

                if(!$continue) {
                    $this->MkCommon->setCustomFlash(__('Maaf, silahkan upload file dalam bentuk Excel.'), 'error');
                    $this->redirect(array('action'=>'import'));
                } else {
                    $path = APP.'webroot'.DS.'files'.DS.date('Y').DS.date('m').DS;
                    $filenoext = basename ($filename, '.xls');
                    $filenoext = basename ($filenoext, '.XLS');
                    $fileunique = uniqid() . '_' . $filenoext;

                    if( !file_exists($path) ) {
                        mkdir($path, 0755, true);
                    }

                    $targetdir = $path . $fileunique . $filename;
                     
                    ini_set('memory_limit', '96M');
                    ini_set('post_max_size', '64M');
                    ini_set('upload_max_filesize', '64M');

                    if(!move_uploaded_file($source, $targetdir)) {
                        $this->MkCommon->setCustomFlash(__('Maaf, terjadi kesalahan. Silahkan coba lagi, atau hubungi Admin kami.'), 'error');
                        $this->redirect(array('action'=>'import'));
                    }
                }
            } else {
                $this->MkCommon->setCustomFlash(__('Maaf, terjadi kesalahan. Silahkan coba lagi, atau hubungi Admin kami.'), 'error');
                $this->redirect(array('action'=>'import'));
            }

            $xls_files = glob( $targetdir );

            if(empty($xls_files)) {
                $this->MkCommon->setCustomFlash(__('Tidak terdapat file excel atau berekstensi .xls pada file zip Anda. Silahkan periksa kembali.'), 'error');
                $this->redirect(array('action'=>'import'));
            } else {
                $uploadedXls = $this->MkCommon->addToFiles('xls', $xls_files[0]);
                $uploaded_file = $uploadedXls['xls'];
                $file = explode(".", $uploaded_file['name']);
                $extension = array_pop($file);
                
                if($extension == 'xls') {
                    $dataimport = new Spreadsheet_Excel_Reader();
                    $dataimport->setUTFEncoder('iconv');
                    $dataimport->setOutputEncoding('UTF-8');
                    $dataimport->read($uploaded_file['tmp_name']);
                    
                    if(!empty($dataimport)) {
                        $data = $dataimport;
                        $row_submitted = 1;
                        $successfull_row = 0;
                        $failed_row = 0;
                        $error_message = '';
                        $cnt = 0;

                        for ($x=2;$x<=count($data->sheets[0]["cells"]); $x++) {
                            $datavar = array();
                            $flag = true;
                            $i = 1;

                            while ($flag) {
                                if( !empty($data->sheets[0]["cells"][1][$i]) ) {
                                    $variable = $this->MkCommon->toSlug($data->sheets[0]["cells"][1][$i], '_');
                                    $thedata = !empty($data->sheets[0]["cells"][$x][$i])?$data->sheets[0]["cells"][$x][$i]:NULL;
                                    $$variable = $thedata;
                                    $datavar[] = $thedata;
                                } else {
                                    $flag = false;
                                }
                                $i++;
                            }

                            if(array_filter($datavar)) {
                                $no_ref = !empty($no_ref)?$no_ref:false;
                                $note = !empty($note)?$note:false;

                                $no_ref = str_replace('#', '', $no_ref);
                                $no_ref = intval($no_ref);

                                if( !empty($note) ) {
                                    $flag = $this->CashBank->find('first', array(
                                        'conditions' => array(
                                            'CashBank.id' => $no_ref,
                                        ),
                                    ));

                                    if( !empty($flag) ) {
                                        $this->CashBank->id = $no_ref;
                                        $this->CashBank->set('description', $note);
                                        
                                        if( $this->CashBank->save() ){                                        
                                            $this->Log->logActivity( __('Sukses upload by Import Excel'), $this->user_data, $this->RequestHandler, $this->params );
                                            $successfull_row++;
                                        } else {
                                            $validationErrors = $this->CashBank->validationErrors;
                                            $textError = array();

                                            if( !empty($validationErrors) ) {
                                                foreach ($validationErrors as $key => $validationError) {
                                                    if( !empty($validationError) ) {
                                                        foreach ($validationError as $key => $error) {
                                                            $textError[] = $error;
                                                        }
                                                    }
                                                }
                                            }

                                            if( !empty($textError) ) {
                                                $textError = implode(', ', $textError);
                                            } else {
                                                $textError = '';
                                            }

                                            $failed_row++;
                                            $error_message .= sprintf(__('Gagal pada baris ke %s : Gagal Upload Data. %s'), $row_submitted, $textError) . '<br>';
                                        }

                                        $row_submitted++;
                                        $cnt++;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if(!empty($successfull_row)) {
                $message_import1 = sprintf(__('Import Berhasil: (%s baris), dari total (%s baris)'), $successfull_row, $cnt);
                $this->MkCommon->setCustomFlash(__($message_import1), 'success');
            }
            
            if(!empty($error_message)) {
                $this->MkCommon->setCustomFlash(__($error_message), 'error');
            }
            $this->redirect(array('action'=>'import'));
        }
    }

    function cashbank_delete($id){
        $locale = $this->CashBank->getData('first', array(
            'conditions' => array(
                'CashBank.id' => $id
            )
        ), array(
            'branch' => false,
        ));

        if(!empty($locale)){
            $value = true;
            $this->MkCommon->_callAllowClosing($locale, 'CashBank', 'tgl_cash_bank');

            $locale = $this->CashBank->CashBankDetail->getMerge($locale, $id);
            $receiving_cash_type = $this->MkCommon->filterEmptyField($locale, 'CashBank', 'receiving_cash_type');
            $receiver_id = $this->MkCommon->filterEmptyField($locale, 'CashBank', 'receiver_id');
            $receiver_type = $this->MkCommon->filterEmptyField($locale, 'CashBank', 'receiver_type');
            $nodoc = $this->MkCommon->filterEmptyField($locale, 'CashBank', 'nodoc');
            $tgl_cash_bank = $this->MkCommon->filterEmptyField($locale, 'CashBank', 'tgl_cash_bank');
            $document_coa_id = $this->MkCommon->filterEmptyField($locale, 'CashBank', 'coa_id');
            $grand_total = $this->MkCommon->filterEmptyField($locale, 'CashBank', 'grand_total');
            $description = $this->MkCommon->filterEmptyField($locale, 'CashBank', 'description');
            $completed = $this->MkCommon->filterEmptyField($locale, 'CashBank', 'completed');
            $cogs_id = $this->MkCommon->filterEmptyField($locale, 'CashBank', 'cogs_id');

            if($locale['CashBank']['status']){
                $value = false;
            }

            $this->CashBank->id = $id;
            $this->CashBank->set('is_rejected', 1);

            if($this->CashBank->save()){
                $document_id = !empty($locale['CashBank']['document_id'])?$locale['CashBank']['document_id']:false;
                $document_type = !empty($locale['CashBank']['document_type'])?$locale['CashBank']['document_type']:false;

                if( !empty($document_id) ) {
                    switch ($document_type) {
                        case 'revenue':
                            $this->loadModel('Revenue');
                            $this->Revenue->changeStatusPPNPaid( $document_id, 0 );
                            break;
                        case 'prepayment':
                            $this->CashBank->id = $document_id;
                            $this->CashBank->set('prepayment_status', $this->CashBank->getStatusPrepayment($document_id));
                            $this->CashBank->save();
                            break;
                    }
                }

                if( !empty($completed) ) {
                    if( !empty($locale['CashBankDetail']) ) {
                        foreach ($locale['CashBankDetail'] as $key => $value) {
                            $coa_id = $this->MkCommon->filterEmptyField($value, 'CashBankDetail', 'coa_id');
                            $total = $this->MkCommon->filterEmptyField($value, 'CashBankDetail', 'total');

                            $documentType = Configure::read('__Site.Journal.Documents');
                            $documentType = $this->MkCommon->filterEmptyField($documentType, $receiving_cash_type);
                            $receiver_name = $this->RjCashBank->_callReceiverName($receiver_id, $receiver_type);

                            if( in_array($receiving_cash_type, array( 'out', 'ppn_out', 'prepayment_out' )) ) {
                                $coaArr = array(
                                    'credit' => $coa_id,
                                );

                                if( !empty($description) ) {
                                    $title = __('<i>Pembatalan</i> ').$description;
                                } else {
                                    $title = sprintf(__('<i>Pembatalan</i> %s kepada %s'), $documentType, $receiver_name);
                                }
                            } else {
                                $coaArr = array(
                                    'debit' => $coa_id,
                                );

                                if( !empty($description) ) {
                                    $title = __('<i>Pembatalan</i> ').$description;
                                } else {
                                    $title = sprintf(__('<i>Pembatalan</i> %s dari %s'), $documentType, $receiver_name);
                                }
                            }

                            $this->User->Journal->setJournal($total, $coaArr, array(
                                'cogs_id' => $cogs_id,
                                'document_id' => $id,
                                'title' => $title,
                                'document_no' => $nodoc,
                                'type' => 'void_'.$receiving_cash_type,
                                'date' => $tgl_cash_bank,
                            ));
                        }
                    }

                    if( !empty($title) ) {
                        if( in_array($receiving_cash_type, array( 'out', 'ppn_out', 'prepayment_out' )) ) {
                            $coaArr = array(
                                'debit' => $document_coa_id,
                            );
                        } else {
                            $coaArr = array(
                                'credit' => $document_coa_id,
                            );
                        }

                        $this->User->Journal->setJournal($grand_total, $coaArr, array(
                            'cogs_id' => $cogs_id,
                            'document_id' => $id,
                            'title' => $title,
                            'document_no' => $nodoc,
                            'type' => 'void_'.$receiving_cash_type,
                            'date' => $tgl_cash_bank,
                        ));
                    }
                }

                $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
                $this->MkCommon->setCustomFlash(sprintf(__('Sukses merubah status Kas/Bank #%s.'), $noref), 'success');
                $this->Log->logActivity( sprintf(__('Sukses merubah status Kas/Bank ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal merubah status Kas/Bank ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id ); 
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Kas/Bank tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    function detail($id = false){
        $this->set('sub_module_title', 'Detail Kas/Bank');
        $conditions = array(
            'CashBank.id' => $id,
        );
        $conditionToApprove = $this->User->Employe->EmployePosition->Approval->_callGetDataToApprove('cash_bank');
        $conditions = array_merge($conditions, $conditionToApprove);

        $cashbank = $this->CashBank->getData('first', array(
            'conditions' => $conditions,
        ), array(
            'branch' => false,
        ));

        if( !empty($cashbank) ) {
            $user_id = $this->MkCommon->filterEmptyField($cashbank, 'CashBank', 'user_id');
            $document_id = $this->MkCommon->filterEmptyField($cashbank, 'CashBank', 'document_id');
            $document_coa_id = $this->MkCommon->filterEmptyField($cashbank, 'CashBank', 'coa_id');
            $receiver_id = $this->MkCommon->filterEmptyField($cashbank, 'CashBank', 'receiver_id');
            $cogs_id = $this->MkCommon->filterEmptyField($cashbank, 'CashBank', 'cogs_id');

            $tgl_cash_bank = $this->MkCommon->filterEmptyField($cashbank, 'CashBank', 'tgl_cash_bank');
            $document_type = $this->MkCommon->filterEmptyField($cashbank, 'CashBank', 'document_type');
            $receiver_type = $this->MkCommon->filterEmptyField($cashbank, 'CashBank', 'receiver_type');
            $receiving_cash_type = $this->MkCommon->filterEmptyField($cashbank, 'CashBank', 'receiving_cash_type');
            $nodoc = $this->MkCommon->filterEmptyField($cashbank, 'CashBank', 'nodoc');
            $debit_total = $this->MkCommon->filterEmptyField($cashbank, 'CashBank', 'debit_total', 0);
            $credit_total = $this->MkCommon->filterEmptyField($cashbank, 'CashBank', 'credit_total', 0);
            $grand_total = $this->MkCommon->filterEmptyField($cashbank, 'CashBank', 'grand_total', 0);
            $description = $this->MkCommon->filterEmptyField($cashbank, 'CashBank', 'description');
            
            $allow_closing = $this->MkCommon->_callAllowClosing($cashbank, 'CashBank', 'tgl_cash_bank', 'Y-m', false);
            
            $cashbank = $this->User->getMerge($cashbank, $user_id);
            $cashbank = $this->CashBank->Coa->getMerge($cashbank, $document_coa_id);
            $cashbank = $this->CashBank->CashBankDetail->getMerge($cashbank, $id, array(
                'contain' => array(
                    'Coa',
                    'Truck',
                ),
            ));
            $cashbank = $this->CashBank->getMergeList($cashbank, array(
                'contain' => array(
                    'Cogs',
                ),
            ));

            if( $receiving_cash_type == 'prepayment_in' ) {
                $cashbank = $this->CashBank->getMerge($cashbank, $document_id, 'PrepaymentOut');
            }

            if( !empty($allow_closing) ) {
                $user_position_id = $this->MkCommon->filterEmptyField($cashbank, 'Employe', 'employe_position_id');

                $user_otorisasi_approvals = $this->User->Employe->EmployePosition->Approval->getUserOtorisasiApproval('cash-bank', $user_position_id, $grand_total, $id);
                $position_approval = $this->User->Employe->EmployePosition->Approval->getPositionPriority($user_otorisasi_approvals);

                $position_priority = $this->MkCommon->filterEmptyField($position_approval, 'Priority');
                $position_normal = $this->MkCommon->filterEmptyField($position_approval, 'Normal');

                if( !empty($user_otorisasi_approvals) ) {
                    $position_otorisasi_approvals = Set::extract('/EmployePosition/id', $user_otorisasi_approvals);
                } else {
                    $position_otorisasi_approvals = array();
                }

                $approval = $this->user_data;

                $approval_employe_id = $this->MkCommon->filterEmptyField($approval, 'employe_id');
                $approval = $this->CashBank->Employe->getMerge($approval, $approval);

                $approval_position_id = $this->MkCommon->filterEmptyField($approval, 'Employe', 'employe_position_id');
                $idx_arr_otorisasi = array_search($approval_position_id, $position_otorisasi_approvals);
                $show_approval = false;

                if( is_numeric($idx_arr_otorisasi) && !empty($user_otorisasi_approvals[$idx_arr_otorisasi]) ) {
                    $dataOtorisasiApproval = $user_otorisasi_approvals[$idx_arr_otorisasi];

                    $approval_detail_id = $this->MkCommon->filterEmptyField($dataOtorisasiApproval, 'ApprovalDetailPosition', 'approval_detail_id');
                    $approval_detail_position_id = $this->MkCommon->filterEmptyField($dataOtorisasiApproval, 'ApprovalDetailPosition', 'id');

                    $approvalDetail = $this->User->Employe->EmployePosition->Approval->ApprovalDetail->getData('first', array(
                        'conditions' => array(
                            'ApprovalDetail.id' => $approval_detail_id,
                        ),
                    ));
                    $approval_id = $this->MkCommon->filterEmptyField($approvalDetail, 'ApprovalDetail', 'approval_id');

                    $auth = $this->CashBank->DocumentAuth->getData('first', array(
                        'conditions' => array(
                            'DocumentAuth.document_id' => $id,
                            'DocumentAuth.approval_id' => $approval_id,
                            'DocumentAuth.approval_detail_id' => $approval_detail_id,
                            'DocumentAuth.approval_detail_position_id' => $approval_detail_position_id,
                        ),
                    ));

                    if( empty($auth) ) {
                        $show_approval = in_array($approval_position_id, $position_otorisasi_approvals)?true:false;
                    }
                }
            } else {
                $show_approval = false;
            }

            if( !empty($this->request->data) ){
                if( !empty($show_approval) ){
                    $data = $this->request->data;
                    $is_priority = $this->MkCommon->filterEmptyField($dataOtorisasiApproval, 'ApprovalDetailPosition', 'is_priority');
                    $employe_position_id = $this->MkCommon->filterEmptyField($dataOtorisasiApproval, 'ApprovalDetailPosition', 'employe_position_id');
                    $status_document = $this->MkCommon->filterEmptyField($data, 'DocumentAuth', 'status_document');

                    $data['DocumentAuth']['document_id'] = $id;
                    $data['DocumentAuth']['approval_id'] = $approval_id;
                    $data['DocumentAuth']['approval_detail_id'] = $approval_detail_id;
                    $data['DocumentAuth']['approval_detail_position_id'] = $approval_detail_position_id;

                    $position_auths = $this->CashBank->DocumentAuth->getData('all', array(
                        'conditions' => array(
                            'DocumentAuth.document_id' => $id,
                        ),
                        'contain' => array(
                            'ApprovalDetailPosition',
                        ),
                    ));
                    $position_priority_auth = array();
                    $position_normal_auth = array();

                    if( !empty($position_auths) ) {
                        foreach ($position_auths as $key => $value) {
                            if( !empty($value['ApprovalDetailPosition']['employe_position_id']) ) {
                                if( !empty($value['ApprovalDetailPosition']['is_priority']) ) {
                                    $position_priority_auth[] = $value['ApprovalDetailPosition']['employe_position_id'];
                                } else {
                                    $position_normal_auth[] = $value['ApprovalDetailPosition']['employe_position_id'];
                                }
                            }
                        }
                    }

                    $position_priority_auth = array_values($position_priority_auth);

                    if( !empty($is_priority) ) {
                        $position_priority_auth[] = $employe_position_id;
                        $position_priority_auth = array_unique($position_priority_auth);
                    } else {
                        $position_normal_auth[] = $employe_position_id;
                        $position_normal_auth = array_unique($position_normal_auth);
                    }

                    if( !empty($status_document) ) {
                        $this->CashBank->DocumentAuth->create();
                        $this->CashBank->DocumentAuth->set($data);

                        if($this->CashBank->DocumentAuth->save()){
                            $data_arr = array();

                            if( $this->MkCommon->checkArrayApproval($position_priority_auth, $position_priority) || ( empty($position_priority) && $this->MkCommon->checkArrayApproval($position_normal_auth, $position_normal) ) ){
                                switch ($status_document) {
                                    case 'approve':
                                        $data_arr = array(
                                            'completed' => 1,
                                            'is_revised' => 0,
                                            'is_rejected' => 0
                                        );
                                        $msgRevision = sprintf(__('Kas/Bank dengan No Dokumen %s telah disetujui'), $nodoc);
                                        break;
                                    case 'revise':
                                        $data_arr = array(
                                            'completed' => 0,
                                            'is_revised' => 1,
                                            'is_rejected' => 0
                                        );
                                        $msgRevision = sprintf(__('Kas/Bank dengan No Dokumen %s memerlukan resivisi Anda'), $nodoc);
                                        break;
                                    case 'reject':
                                        $data_arr = array(
                                            'completed' => 0,
                                            'is_revised' => 0,
                                            'is_rejected' => 1
                                        );
                                        $msgRevision = sprintf(__('Kas/Bank dengan No Dokumen %s telah ditolak'), $nodoc);
                                        break;
                                }
                            }else if($status_document == 'revise'){
                                $data_arr = array(
                                    'is_revised' => 1,
                                );
                                $msgRevision = sprintf(__('Kas/Bank dengan No Dokumen %s memerlukan resivisi Anda'), $nodoc);
                            }

                            if( !empty($data_arr) ) {
                                $this->CashBank->id = $id;
                                $this->CashBank->set($data_arr);

                                if( $this->CashBank->save() ) {
                                    if( $status_document == 'reject' && !empty($document_id) ) {
                                        switch ($document_type) {
                                            case 'revenue':
                                                $this->loadModel('Revenue');
                                                $this->Revenue->changeStatusPPNPaid( $document_id, 0 );
                                                break;
                                            case 'prepayment':
                                                $this->CashBank->id = $document_id;
                                                $this->CashBank->set('prepayment_status', $this->CashBank->getStatusPrepayment($document_id));
                                                $this->CashBank->save();
                                                break;
                                        }
                                    }

                                    if( $status_document == 'approve' ) {
                                        $coaArr = array();

                                        if( !empty($cashbank['CashBankDetail']) ) {
                                            foreach ($cashbank['CashBankDetail'] as $key => $cashBankDetail) {
                                                $coa_id = $this->MkCommon->filterEmptyField($cashBankDetail, 'CashBankDetail', 'coa_id');
                                                $total = $this->MkCommon->filterEmptyField($cashBankDetail, 'CashBankDetail', 'total');

                                                $documentType = Configure::read('__Site.Journal.Documents');
                                                $documentType = $this->MkCommon->filterEmptyField($documentType, $receiving_cash_type);
                                                $receiver_name = $this->RjCashBank->_callReceiverName($receiver_id, $receiver_type);

                                                if( !empty($description) ) {
                                                    $title = $description;
                                                } else {
                                                    if( in_array($receiving_cash_type, array( 'out', 'ppn_out', 'prepayment_out' )) ) {
                                                        $title = sprintf(__('%s kepada %s'), $documentType, $receiver_name);
                                                        $coaArr = array(
                                                            'debit' => $coa_id
                                                        );
                                                    } else {
                                                        $title = sprintf(__('%s dari %s'), $documentType, $receiver_name);
                                                        $coaArr = array(
                                                            'credit' => $coa_id
                                                        );
                                                    }
                                                }

                                                $this->User->Journal->setJournal($total, $coaArr, array(
                                                    'cogs_id' => $cogs_id,
                                                    'document_id' => $id,
                                                    'title' => $title,
                                                    'document_no' => $nodoc,
                                                    'type' => $receiving_cash_type,
                                                    'date' => $tgl_cash_bank,
                                                ));
                                            }

                                            if( in_array($receiving_cash_type, array( 'out', 'ppn_out', 'prepayment_out' )) ) {
                                                $coaArr = array(
                                                    'credit' => $document_coa_id,
                                                );
                                            } else {
                                                $coaArr = array(
                                                    'debit' => $document_coa_id,
                                                );
                                            }

                                            $this->User->Journal->setJournal($grand_total, $coaArr, array(
                                                'cogs_id' => $cogs_id,
                                                'document_id' => $id,
                                                'title' => $title,
                                                'document_no' => $nodoc,
                                                'type' => $receiving_cash_type,
                                                'date' => $tgl_cash_bank,
                                            ));
                                        }
                                    }

                                    if( !empty($msgRevision) ) {
                                        $this->MkCommon->_saveNotification(array(
                                            'action' => __('Kas/Bank'),
                                            'name' => $msgRevision,
                                            'user_id' => $user_id,
                                            'document_id' => $id, 
                                            'url' => array(
                                                'controller' => 'cashbanks',
                                                'action' => 'cashbank_edit',
                                                $id,
                                                'admin' => false,
                                            ),
                                        ));
                                    }
                                }
                            }

                            $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
                            $this->MkCommon->setCustomFlash(sprintf(__('Berhasil melakukan Approval Kas/Bank #%s'), $noref), 'success');
                            $this->Log->logActivity( sprintf(__('Berhasil melakukan %s Kas/Bank #%s'), $status_document, $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
                        }else{
                            $this->MkCommon->setCustomFlash('Gagal melakukan Approval Kas/Bank.', 'error');
                            $this->Log->logActivity( sprintf(__('Berhasil melakukan %s Kas/Bank #%s'), $status_document, $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
                        }
                    }else{
                        $this->MkCommon->setCustomFlash('Silahkan pilih Status Approval', 'error');
                    }
                }else{
                    $this->MkCommon->setCustomFlash('Anda tidak mempunyai hak untuk mengakses kontent tersebut.', 'error');
                }

                $this->redirect($this->referer());
            }else{
                // if(!empty($cashbank['DocumentAuth'][0])){
                //     $this->request->data['DocumentAuth'] = $cashbank['DocumentAuth'][0];
                // }
            }

            if(!empty($cashbank['CashBank']['receiver_type'])){
                $model = $cashbank['CashBank']['receiver_type'];
                $receiver_id = !empty($cashbank['CashBank']['receiver_id'])?$cashbank['CashBank']['receiver_id']:false;
                $cashbank['CashBank']['receiver'] = $this->RjCashBank->_callReceiverName($receiver_id, $model);
            }

            switch ($document_type) {
                case 'revenue':
                    $this->loadModel('Revenue');
                    $revenue = $this->Revenue->getData('first', array(
                        'conditions' => array(
                            'Revenue.id' => $document_id,
                        ),
                    ), true, array(
                        'status' => 'all',
                    ));
                    $cashbank = array_merge($cashbank, $revenue);
                    break;
            }

            $this->MkCommon->getLogs($this->params['controller'], array( 'cashbank_edit', 'cashbank_add', 'cashbank_delete' ), $id);
            $this->set('active_menu', 'cash_bank');
            $this->set(compact(
                'user_otorisasi_approvals', 'cashbank',
                'show_approval'
            ));
        } else {
            $this->MkCommon->setCustomFlash(__('Kas/Bank tidak ditemukan.'), 'error');
            $this->redirect($this->referer());
        }
    }

    function settings(){
        $this->loadModel('CashBankSetting');
        $this->loadModel('Coa');

        if(!empty($this->request->data)){
            $data = $this->request->data;

            if(!empty($data['CashBankSetting']['id'])){
                foreach ($data['CashBankSetting']['id'] as $key => $value) {
                    $coa_credit_id = !empty($data['CashBankSetting']['coa_credit_id'][$key]) ? $data['CashBankSetting']['coa_credit_id'][$key] : 0;
                    $coa_debit_id = !empty($data['CashBankSetting']['coa_debit_id'][$key]) ? $data['CashBankSetting']['coa_debit_id'][$key] : 0;

                    $data_arr = array(
                        'coa_credit_id' => $coa_credit_id,
                        'coa_debit_id' => $coa_debit_id,
                    );

                    $this->CashBankSetting->id = $value;
                    $this->CashBankSetting->set($data_arr);
                    $this->CashBankSetting->save();
                    $transaction_id = $this->CashBankSetting->id;

                    if( $this->CashBankSetting->save() ) {
                        $this->Log->logActivity( sprintf(__('Berhasil mengubah setting #%s'), $transaction_id), $this->user_data,$this->RequestHandler, $this->params, 0, false, $transaction_id );
                    } else {
                        $this->Log->logActivity( sprintf(__('Gagal mengubah setting #%s'), $value), $this->user_data, $this->RequestHandler, $this->params, 1, false, $value );
                    }
                }
            }

            $this->MkCommon->setCustomFlash(__('Berhasil mengubah setting.'), 'success');
            $this->redirect($this->here);
        }

        $cash_bank_settings = $this->CashBankSetting->find('all');
        $coas = $this->GroupBranch->Branch->BranchCoa->getCoas();
        $sub_module_title = 'Setting COA';
        $this->set(compact('cash_bank_settings', 'coas', 'sub_module_title'));
    }

    public function coa_setting() {
        $coaSetting = $this->User->CoaSetting->getData('first', array(
            'conditions' => array(
                'CoaSetting.status' => 1
            ),
        ));
        $coaSettingDetails = $this->User->Coa->CoaSettingDetail->getData('all');

        if(!empty($this->request->data)){
            $data = $this->request->data;
            $data['CoaSetting']['user_id'] = Configure::read('__Site.config_user_id');
            $data = $this->RjCashBank->_callBeforeSaveCoaSetting($data);
            $dataDetail = $this->MkCommon->filterEmptyField($data, 'CoaSettingDetail');

            if( !empty($coaSetting['CoaSetting']['id']) ){
                $this->User->CoaSetting->id = $coaSetting['CoaSetting']['id'];
            }else{
                $this->User->CoaSetting->create();
            }

            $this->User->CoaSetting->set($data);

            if($this->User->CoaSetting->validates($data)){
                if( !empty($dataDetail) ) {
                    $flag = $this->User->Coa->CoaSettingDetail->saveAll($dataDetail);
                } else {
                    $flag = true;
                }

                if(!empty($flag) && $this->User->CoaSetting->save($data)){
                    $transaction_id = $this->User->CoaSetting->id;
                    $this->MkCommon->setCustomFlash(__('Sukses menyimpan pengaturan COA'), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses menyimpan pengaturan COA #%s'), $transaction_id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $transaction_id );
                    $this->redirect(array(
                        'controller' => 'cashbanks',
                        'action' => 'coa_setting'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal menyimpan pengaturan COA'), 'error');
                    $this->Log->logActivity( __('Gagal menyimpan pengaturan COA'), $this->user_data, $this->RequestHandler, $this->params, 1 );
                }
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal menyimpan pengaturan COA'), 'error');
            }
        } else if( !empty($coaSetting) ) {
            $this->request->data = $coaSetting;
        }
        
        $this->request->data = $this->RjCashBank->_callBeforeRenderCoaSetting($this->request->data, $coaSettingDetails);
        $this->set('active_menu', 'coa_setting');
    }

    public function journal_report( $data_action = false ) {
        $this->loadModel('Journal');

        $this->set('sub_module_title', 'Laporan Jurnal');
        $dateFrom = date('Y-m-d', strtotime('-1 Month'));
        $dateTo = date('Y-m-d');
        $allow_branch_id = Configure::read('__Site.config_allow_branch_id');
        $options = array(
            'conditions' => array(
                'Journal.branch_id' => $allow_branch_id,
            ),
            'contain' => false,
            'group' => array(
                'Journal.document_id',
                'Journal.type',
            ),
            'order'=> array(
                'Journal.date' => 'DESC',
                'Journal.document_id' => 'DESC',
                'Journal.id' => 'DESC',
            ),
        );

        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));

        $dateFrom = $this->MkCommon->filterEmptyField($params, 'named', 'DateFrom');
        $dateTo = $this->MkCommon->filterEmptyField($params, 'named', 'DateTo');

        $options =  $this->User->Journal->_callRefineParams($params, $options);

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            // Custom Otorisasi
            $options = $this->MkCommon->getConditionGroupBranch( $refine, 'Journal', $options );
        }

        $module_title = __('Laporan Jurnal');
        $values = array();

        if( !empty($dateFrom) && !empty($dateTo) ) {
            $module_title .= sprintf(' Periode %s', $this->MkCommon->getCombineDate($dateFrom, $dateTo));
        }

        if( !empty($data_action) ){
            $journals = $this->User->Journal->getData('all', $options);
        } else {
            $options['limit'] = Configure::read('__Site.config_pagination');
            $this->paginate = $this->User->Journal->getData('paginate', $options);
            $journals = $this->paginate('Journal');
            
            $this->MkCommon->_layout_file('select');
        }

        if( !empty($journals) ) {
            $this->User->Journal->virtualFields['sorting_journal'] = 'CASE WHEN Journal.credit <> 0 THEN 1 ELSE 0 END';

            foreach ($journals as $key => $value) {
                $document_id = $this->MkCommon->filterEmptyField($value, 'Journal', 'document_id');
                $type = $this->MkCommon->filterEmptyField($value, 'Journal', 'type');

                $journal = $this->Journal->getData('all', array(
                    'conditions' => array(
                        'Journal.document_id' => $document_id,
                        'Journal.type' => $type,
                    ),
                    'order'=> array(
                        'Journal.date' => 'DESC',
                        'Journal.document_id' => 'DESC',
                        'Journal.sorting_journal' => 'ASC',
                        'Journal.id' => 'ASC',
                    ),
                ));

                $values = array_merge($values, $journal);
            }
        }


        $coas = $this->User->Journal->Coa->_callOptGroup();

        $this->set('active_menu', 'journal_report');
        $this->set(compact(
            'values', 'module_title', 'data_action',
            'coas'
        ));

        if($data_action == 'pdf'){
            $this->layout = 'pdf';
        }else if($data_action == 'excel'){
            $this->layout = 'ajax';
        }
    }

    public function journal_rinci_report( $data_action = false ) {
        $this->loadModel('Journal');

        $this->set('sub_module_title', 'Laporan Jurnal Rinci');
        $dateFrom = date('Y-m-d', strtotime('-1 Month'));
        $dateTo = date('Y-m-d');
        $allow_branch_id = Configure::read('__Site.config_allow_branch_id');
        $options = array(
            'conditions' => array(
                'Journal.branch_id' => $allow_branch_id,
            ),
            'contain' => false,
            'group' => array(
                'Journal.document_id',
                'Journal.type',
            ),
            'order'=> array(
                'Journal.date' => 'DESC',
                'Journal.document_id' => 'DESC',
                'Journal.id' => 'DESC',
            ),
        );

        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));

        $dateFrom = $this->MkCommon->filterEmptyField($params, 'named', 'DateFrom');
        $dateTo = $this->MkCommon->filterEmptyField($params, 'named', 'DateTo');

        $options =  $this->User->Journal->_callRefineParams($params, $options);

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            // Custom Otorisasi
            $options = $this->MkCommon->getConditionGroupBranch( $refine, 'Journal', $options );
        }

        $module_title = __('Laporan Jurnal Rinci');
        $values = array();

        if( !empty($dateFrom) && !empty($dateTo) ) {
            $module_title .= sprintf(' Periode %s', $this->MkCommon->getCombineDate($dateFrom, $dateTo));
        }

        if( !empty($data_action) ){
            $journals = $this->User->Journal->getData('all', $options);
        } else {
            $options['limit'] = Configure::read('__Site.config_pagination');
            $this->paginate = $this->User->Journal->getData('paginate', $options);
            $journals = $this->paginate('Journal');
            
            $this->MkCommon->_layout_file(array(
                'select',
                'freeze',
            ));
        }

        if( !empty($journals) ) {
            $this->User->Journal->virtualFields['sorting_journal'] = 'CASE WHEN Journal.credit <> 0 THEN 1 ELSE 0 END';

            foreach ($journals as $key => $value) {
                $document_id = $this->MkCommon->filterEmptyField($value, 'Journal', 'document_id');
                $type = $this->MkCommon->filterEmptyField($value, 'Journal', 'type');

                $journal = $this->Journal->getData('all', array(
                    'conditions' => array(
                        'Journal.document_id' => $document_id,
                        'Journal.type' => $type,
                    ),
                    'order'=> array(
                        'Journal.date' => 'DESC',
                        'Journal.document_id' => 'DESC',
                        'Journal.sorting_journal' => 'ASC',
                        'Journal.id' => 'ASC',
                    ),
                ));

                $values = array_merge($values, $journal);
            }
        }


        $coas = $this->User->Journal->Coa->_callOptGroup();

        $this->set('active_menu', 'journal_rinci_report');
        $this->set(compact(
            'values', 'module_title', 'data_action',
            'coas'
        ));

        if($data_action == 'pdf'){
            $this->layout = 'pdf';
        }else if($data_action == 'excel'){
            $this->layout = 'ajax';
        }
    }

    public function ledger_report( $data_action = false ) {
        $dateFrom = date('Y-m-d', strtotime('-1 Month'));
        $dateTo = date('Y-m-d');

        $named = $this->MkCommon->filterEmptyField($this->params, 'named');
        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));

        $dateFrom = $this->MkCommon->filterEmptyField($params, 'named', 'DateFrom');

        if( !empty($named) ) {
            $allow_branch_id = Configure::read('__Site.config_allow_branch_id');
            $coa_id = $this->MkCommon->filterEmptyField($named, 'coa');
            $conditions = array(
                'Journal.branch_id' => $allow_branch_id,
            );

            // Custom Otorisasi
            $conditions = $this->MkCommon->getConditionGroupBranch( $named, 'Journal', $conditions, 'conditions' );

            if( !empty($coa_id) ) {
                $coa = $this->User->Journal->Coa->getMerge(array(), $coa_id);
                $coa_name = $this->MkCommon->filterEmptyField($coa, 'Coa', 'coa_name');
                $balance = $this->MkCommon->filterEmptyField($coa, 'Coa', 'balance', 0);

                $this->User->Journal->virtualFields['credit'] = 'SUM(Journal.credit)';
                $this->User->Journal->virtualFields['debit'] = 'SUM(Journal.debit)';
                $options =  $this->User->Journal->_callRefineParams($params, array(
                    'conditions' => $conditions,
                    'group' => array(
                        'Journal.coa_id',
                        'Journal.document_id',
                        'Journal.type',
                    ),
                    'order' => array(
                        'Journal.date' => 'ASC',
                        'Journal.id' => 'ASC',
                    ),
                ));
                $values = $this->User->Journal->getData('all', $options);

                $this->User->Journal->virtualFields['begining_balance_credit'] = 'SUM(Journal.credit)';
                $this->User->Journal->virtualFields['begining_balance_debit'] = 'SUM(Journal.debit)';

                $type = $this->MkCommon->filterEmptyField($params, 'named', 'status');

                $summaryBalance = $this->User->Journal->getData('first', array(
                    'conditions' => array_merge(array(
                        'Journal.coa_id' => $coa_id,
                        'DATE_FORMAT(Journal.date, \'%Y-%m-%d\') <' => $dateFrom,
                    ), $conditions),
                    'group' => array(
                        'Journal.coa_id',
                    ),
                ), true, array(
                    'type' => $type,
                ));

                $balance_credit = $this->MkCommon->filterEmptyField($summaryBalance, 'Journal', 'begining_balance_credit', 0);
                $balance_debit = $this->MkCommon->filterEmptyField($summaryBalance, 'Journal', 'begining_balance_debit', 0);
                $beginingBalance = $balance + ( $balance_debit - $balance_credit );
            } else {
                $this->MkCommon->redirectReferer(__('Mohon pilih COA terlebih dahulu'), 'error', array(
                    'action' => 'ledger_report',
                    'admin' => false,
                ));
            }
        }

        $coas = $this->User->Journal->Coa->_callOptGroup();

        $dateFrom = $this->MkCommon->filterEmptyField($params, 'named', 'DateFrom');
        $dateTo = $this->MkCommon->filterEmptyField($params, 'named', 'DateTo');
        $module_title = __('Laporan Ledger');

        if( !empty($dateFrom) && !empty($dateTo) ) {
            $module_title .= sprintf(' Periode %s', $this->MkCommon->getCombineDate($dateFrom, $dateTo));
        }

        if( !empty($data_action) ) {
            $module_title .= '<br>'.$coa_name;
        }

        $this->set('active_menu', 'ledger_report');
        $this->set(compact(
            'coas', 'values', 'module_title',
            'coa_name', 'data_action',
            'coa', 'beginingBalance'
        ));

        if($data_action == 'pdf'){
            $this->layout = 'pdf';
        }else if($data_action == 'excel'){
            $this->layout = 'ajax';
        } else {
            $this->MkCommon->_layout_file('select');
        }
    }

    function getPrepaymentMerge ( $prepayment ) {
        // Get Data Prepayment IN
        $prepayment_out_id = $this->MkCommon->filterEmptyField($prepayment, 'CashBank', 'id');
        $prepayment['PrepaymentIN'] = $this->CashBank->getDocumentCashBank($prepayment_out_id, 'prepayment_in', array(
            'branch' => false,
        ));

        if( !empty($prepayment['PrepaymentIN']) ) {
            foreach ($prepayment['PrepaymentIN'] as $key => $prepaymentIN) {
                $prepayment['PrepaymentIN'][$key] = $this->getPrepaymentMerge( $prepaymentIN );
            }
        }

        // Diterima/Dibayar Kepada
        $receiver_id = $this->MkCommon->filterEmptyField($prepayment, 'CashBank', 'receiver_id');
        $receiver_type = $this->MkCommon->filterEmptyField($prepayment, 'CashBank', 'receiver_type');
        $prepayment['Receiver']['name'] = $this->RjCashBank->_callReceiverName($receiver_id, $receiver_type);;

        // Get Data COA
        $coa_id = $this->MkCommon->filterEmptyField($prepayment, 'CashBank', 'coa_id');
        $prepayment = $this->Coa->getMerge($prepayment, $coa_id);

        return $prepayment;
    }

    public function prepayment_report( $data_action = false ) {
        $this->loadModel('CashBank');
        $allow_branch_id = Configure::read('__Site.config_allow_branch_id');
        $options = array(
            'conditions' => array(
                'CashBank.branch_id' => $allow_branch_id,
                'CashBank.is_rejected' => 0,
                'CashBank.receiving_cash_type' => 'prepayment_out',
            ),
            'order' => array(
                'CashBank.created' => 'DESC',
                'CashBank.id' => 'DESC',
            ),
        );
        $dateFrom = date('Y-m-01');
        $dateTo = date('Y-m-t');
        
        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));

        $dateFrom = $this->MkCommon->filterEmptyField($params, 'named', 'DateFrom');
        $dateTo = $this->MkCommon->filterEmptyField($params, 'named', 'DateTo');

        $options =  $this->CashBank->_callRefineParams($params, $options);

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            // Custom Otorisasi
            $options = $this->MkCommon->getConditionGroupBranch( $refine, 'CashBank', $options );
        }
        
        $prepayments = $this->CashBank->getData('all', $options);

        if( !empty($prepayments) ) {
            $this->loadModel('Coa');

            foreach ($prepayments as $key => $prepayment) {
                $prepayments[$key] = $this->getPrepaymentMerge( $prepayment );
            }
        }

        $module_title = $sub_module_title = __('Laporan Prepayment');

        if( !empty($dateFrom) && !empty($dateTo) ) {
            $module_title .= sprintf(' Periode %s', $this->MkCommon->getCombineDate($dateFrom, $dateTo));
        }

        $this->set('active_menu', 'prepayment_report');
        $this->set('period_label', sprintf(__('Periode : %s'), $this->MkCommon->getCombineDate($dateFrom, $dateTo, 'long', 's/d')));

        $this->set(compact(
            'prepayments', 'data_action', 'sub_module_title',
            'module_title'
        ));

        if($data_action == 'pdf'){
            $this->layout = 'pdf';
        }else if($data_action == 'excel'){
            $this->layout = 'ajax';
        }
    }

    public function closing() {
        $month = date('m');
        $year = date('Y');
        $this->loadModel('CoaClosingQueue');

        if( !empty($this->request->data) ) {
            $data = $this->request->data;
            $data = $this->RjCashBank->_callBeforeSaveClosing($data);

            $result = $this->CoaClosingQueue->doSave($data);
            $this->MkCommon->setProcessParams($result, array(
                'action' => 'closing',
            ));
        } else {
            $this->request->data['Journal']['periode']['month'] = $month;
            $this->request->data['Journal']['periode']['year'] = $year;
        }

        $this->paginate = $this->CoaClosingQueue->getData('paginate');
        $values = $this->paginate('CoaClosingQueue');

        if( !empty($values) ) {
            foreach ($values as $key => $value) {
                $user_id = $this->MkCommon->filterEmptyField($value, 'CoaClosingQueue', 'user_id');
                $value = $this->User->getMerge($value, $user_id);

                $values[$key] = $value;
            }
        }

        $this->set('active_menu', 'closing');
        $this->set('values', $values);
    }

    public function closing_toggle( $id ) {
        $this->loadModel('CoaClosingQueue');
        $result = $this->CoaClosingQueue->doDelete( $id );
        $this->MkCommon->setProcessParams($result);
    }

    function balances(){
        $this->loadModel('CoaBalance');

        $this->set('module_title', __('COA'));
        $this->set('sub_module_title', __('COA Balance'));
        $dateFrom = date('Y-m-d', strtotime('-1 Month'));
        $dateTo = date('Y-m-d');

        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));
        $options =  $this->CoaBalance->_callRefineParams($params);

        $this->paginate = $this->CoaBalance->getData('paginate', $options);
        $values = $this->paginate('CoaBalance');

        if(!empty($values)){
            foreach ($values as $key => $value) {
                $coa_id = $this->MkCommon->filterEmptyField($value, 'CoaBalance', 'coa_id');

                $value = $this->User->Journal->Coa->getMerge($value, $coa_id);
                $values[$key] = $value;
            }
        }

        $this->set('active_menu', 'balances');
        $this->set('values', $values);
    }

    function balance_add(){
        $this->set('sub_module_title', __('Tambah Balance'));

        $data = $this->request->data;
        $data = $this->MkCommon->dataConverter($data, array(
            'price' => array(
                'CoaBalance' => array(
                    'saldo',
                ),
            )
        ));
        $result = $this->User->Journal->Coa->CoaBalance->doSave($data);
        $this->MkCommon->setProcessParams($result, array(
            'controller' => 'cashbanks',
            'action' => 'balances',
            'admin' => false,
        ));

        $coas = $this->GroupBranch->Branch->BranchCoa->getCoas();

        $this->set('active_menu', 'balances');
        $this->set(compact(
            'coas'
        ));
    }

    function profit_loss ( $data_action = false ) {
        $module_title = $sub_module_title = __('Laporan Laba Rugi');
        $dateFrom = date('Y-m', strtotime('-1 Month'));
        $dateTo = $dateFrom;
        // $dateFrom = '2017-01';
        // $dateTo = '2017-01';

        $values = $this->User->Coa->getData('threaded', array(
            'conditions' => array(
                'Coa.coa_profit_loss >=' => 3,
                'Coa.status' => 1,
            ),
            'order' => array(
                'Coa.order_sort' => 'ASC',
                'Coa.order' => 'ASC',
                'Coa.code IS NULL' => 'ASC',
                'Coa.code' => 'ASC',
            )
        ));
        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'monthFrom' => $dateFrom,
            'monthTo' => $dateTo,
        ));
        $dateFrom = $this->MkCommon->filterEmptyField($params, 'named', 'MonthFrom');
        $dateTo = $this->MkCommon->filterEmptyField($params, 'named', 'MonthTo');

        $values = $this->RjCashBank->_callCalcProfitLoss($values, $dateFrom, $dateTo, $data_action);
        
        // $this->User->Journal->virtualFields['debit_total'] = 'SUM(Journal.debit)';
        // $this->User->Journal->virtualFields['credit_total'] = 'SUM(Journal.credit)';
        // $summaryProfitLoss = $this->User->Journal->getData('first', array(
        //     'conditions' => array(
        //         'DATE_FORMAT(Journal.date, \'%Y-%m\')' => $dateFrom,
        //         'CASE WHEN SUBSTR(Coa.code, 1, 1) REGEXP \'[0-9]+\' THEN SUBSTR(Coa.code, 1, 1) ELSE 5 END >=' => 3,
        //         'Coa.status' => 1,
        //     ),
        //     'contain' => array(
        //         'Coa',
        //     ),
        // ), true, array(
        //     'type' => 'active',
        // ));

        if( !empty($dateFrom) && !empty($dateTo) ) {
            $sub_module_title = sprintf('%s - Periode %s', $module_title, $this->MkCommon->getCombineDate($dateFrom, $dateTo, 'short'));
        } else {
            $sub_module_title = false;
        }

        if( !empty($data_action) ) {
            $module_title = $sub_module_title;

            if($data_action == 'pdf'){
                $this->layout = 'pdf';
            }else if($data_action == 'excel'){
                $this->layout = 'ajax';
            }
        }

        // debug($values);die();
        $this->set('active_menu', 'profit_loss');
        $this->set(compact(
            'values', 'module_title', 'dateFrom',
            'dateTo', 'sub_module_title', 'data_action'
            // 'summaryProfitLoss'
        ));
    }

    function balance_sheets ( $data_action = false ) {
        $module_title = __('Laporan Neraca');
        $dateFrom = date('Y-m', strtotime('-1 Month'));
        $dateTo = $dateFrom;

        $options = array(
            'conditions' => array(
                'Coa.coa_balance_sheets <' => 3,
                'Coa.status' => 1,
            ),
            'order' => array(
                'Coa.order_sort' => 'ASC',
                'Coa.order' => 'ASC',
                'Coa.code IS NULL' => 'ASC',
                'Coa.code' => 'ASC',
            )
        );

        $debitOptions = $options;
        $debitOptions['conditions']['Coa.type'] = 'debit';
        $debits = $this->User->Coa->getData('threaded', $debitOptions);

        $creditOptions = $options;
        $creditOptions['conditions']['Coa.type'] = 'credit';
        $credits = $this->User->Coa->getData('threaded', $creditOptions);

        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'monthFrom' => $dateFrom,
            'monthTo' => $dateTo,
        ));
        $dateFrom = $this->MkCommon->filterEmptyField($params, 'named', 'MonthFrom');
        $dateTo = $dateFrom;

        $debits = $this->RjCashBank->_callCalcBalanceSheet($debits, $dateFrom, $dateTo);
        $credits = $this->RjCashBank->_callCalcBalanceSheet($credits, $dateFrom, $dateTo);

        if( !empty($dateFrom) && !empty($dateTo) ) {
            $sub_module_title = sprintf('%s - Periode %s', $module_title, $this->MkCommon->getCombineDate($dateFrom, $dateTo, 'short'));
        } else {
            $sub_module_title = false;
        }

        if( !empty($data_action) ) {
            $module_title = $sub_module_title;

            if($data_action == 'pdf'){
                $this->layout = 'pdf';
            }else if($data_action == 'excel'){
                $this->layout = 'ajax';
            }
        }

        // debug($values);die();
        $this->set('active_menu', 'balance_sheets');
        $this->set(compact(
            'module_title', 'dateFrom',
            'dateTo', 'sub_module_title', 'data_action',
            'debits', 'credits'
        ));
    }

    function profit_loss_amount ( $id = NULL, $dateFrom = NULL, $dateTo = NULL ) {
        $this->User->Journal->virtualFields['balancing'] = 'CASE WHEN Coa.type = \'debit\' THEN SUM(Journal.debit) - SUM(Journal.credit) ELSE SUM(Journal.credit) - SUM(Journal.debit) END';
        $this->User->Journal->virtualFields['date_month'] = 'DATE_FORMAT(Journal.date, \'%Y-%m\')';

        $value = $this->User->Journal->Coa->getMerge(array(), $id);

        // $beginingBalance = Common::hashEmptyField($value, 'Coa.balance', 0);
        $parent_id = Common::hashEmptyField($value, 'Coa.parent_id');
        $coa_type = Common::hashEmptyField($value, 'Coa.type');
        $result = array();

        if( !empty($dateFrom) && !empty($dateTo) ) {
            $tmpDateFrom = $dateFrom;
            $tmpDateTo = $dateTo;

            while( $tmpDateFrom <= $tmpDateTo ) {                
                $summaryBalance = $this->User->Journal->getData('first', array(
                    'conditions' => array(
                        'Journal.coa_id' => $id,
                        'DATE_FORMAT(Journal.date, \'%Y-%m\')' => $tmpDateFrom,
                    ),
                    'contain' => array(
                        'Coa',
                    ),
                    'group' => array(
                        'Journal.coa_id',
                    ),
                ), true, array(
                    'type' => 'active',
                ));

                $total_journal = Common::hashEmptyField($summaryBalance, 'Journal.balancing', 0);
                // $balancing = $beginingBalance + $total_journal;
                // $result[$tmpDateFrom] = $balancing;
                $result[$tmpDateFrom] = $total_journal;

                $tmpDateFrom = date('Y-m', strtotime('+1 Month', strtotime($tmpDateFrom)));
            }
        }

        $this->set(compact(
            'result', 'id', 'tmpDateFrom', 'coa_type'
        ));
        $this->render('balance_sheet_amount');
    }

    function balance_sheet_amount ( $id = NULL, $dateFrom = NULL, $dateTo = NULL ) {
        $this->User->Journal->virtualFields['balancing'] = 'CASE WHEN Coa.type = \'debit\' THEN SUM(Journal.debit) - SUM(Journal.credit) ELSE SUM(Journal.credit) - SUM(Journal.debit) END';
        $this->User->Journal->virtualFields['date_month'] = 'DATE_FORMAT(Journal.date, \'%Y-%m\')';

        $value = $this->User->Journal->Coa->getMerge(array(), $id);

        $beginingBalance = Common::hashEmptyField($value, 'Coa.balance', 0);
        $parent_id = Common::hashEmptyField($value, 'Coa.parent_id');
        $coa_type = Common::hashEmptyField($value, 'Coa.type');
        $result = array();

        if( !empty($dateFrom) && !empty($dateTo) ) {
            $tmpDateFrom = $dateFrom;
            $tmpDateTo = $dateTo;

            while( $tmpDateFrom <= $tmpDateTo ) {                
                $summaryBalance = $this->User->Journal->getData('first', array(
                    'conditions' => array(
                        'Journal.coa_id' => $id,
                        'DATE_FORMAT(Journal.date, \'%Y-%m\') <=' => $tmpDateFrom,
                    ),
                    'contain' => array(
                        'Coa',
                    ),
                    'group' => array(
                        'Journal.coa_id',
                    ),
                ), true, array(
                    'type' => 'active',
                ));

                $total_journal = Common::hashEmptyField($summaryBalance, 'Journal.balancing', 0);
                $balancing = $beginingBalance + $total_journal;
                $result[$tmpDateFrom] = $balancing;

                $tmpDateFrom = date('Y-m', strtotime('+1 Month', strtotime($tmpDateFrom)));
            }
        }

        $this->set(compact(
            'result', 'id', 'tmpDateFrom', 'coa_type'
        ));
    }

    public function import_revision( $download = false ) {
        App::import('Vendor', 'excelreader'.DS.'excel_reader2');

        $this->set('module_title', __('Journal'));
        $this->set('active_menu', 'cash_bank');
        $this->set('sub_module_title', __('Import Journal'));

        if(!empty($this->request->data)) { 
            $targetdir = $this->MkCommon->_import_excel( $this->request->data );

            if( !empty($targetdir) ) {
                $xls_files = glob( $targetdir );

                if(empty($xls_files)) {
                    $this->MkCommon->setCustomFlash(__('Tidak terdapat file excel atau berekstensi .xls pada file zip Anda. Silahkan periksa kembali.'), 'error');
                    $this->redirect(array(
                        'action'=>'import_revision'
                    ));
                } else {
                    $uploadedXls = $this->MkCommon->addToFiles('xls', $xls_files[0]);
                    $uploaded_file = $uploadedXls['xls'];
                    $file = explode(".", $uploaded_file['name']);
                    $extension = array_pop($file);
                    
                    if($extension == 'xls') {
                        $dataimport = new Spreadsheet_Excel_Reader();
                        $dataimport->setUTFEncoder('iconv');
                        $dataimport->setOutputEncoding('UTF-8');
                        $dataimport->read($uploaded_file['tmp_name']);
                        
                        if(!empty($dataimport)) {
                            $data = $dataimport;
                            $row_submitted = 0;
                            $successfull_row = 0;
                            $failed_row = 0;
                            $error_message = '';

                            for ($x=2;$x<=count($data->sheets[0]["cells"]); $x++) {
                                $datavar = array();
                                $flag = true;
                                $i = 1;
                                $notFound = false;

                                while ($flag) {
                                    if( !empty($data->sheets[0]["cells"][1][$i]) ) {
                                        $variable = $this->MkCommon->toSlug($data->sheets[0]["cells"][1][$i], '_');
                                        $thedata = !empty($data->sheets[0]["cells"][$x][$i])?$data->sheets[0]["cells"][$x][$i]:NULL;
                                        $$variable = $thedata;
                                        $datavar[] = $thedata;
                                    } else {
                                        $flag = false;
                                    }
                                    $i++;
                                }

                                if(array_filter($datavar)) {
                                    if( !empty($koreksi_keterangan) ) {
                                        $no_ref = str_replace('#', '', $no_ref);

                                        $value = $this->User->Journal->find('first', array(
                                            'conditions' => array(
                                                'Journal.document_id' => $no_ref,
                                                'Journal.document_no' => $no_dokumen,
                                            ),
                                        ));

                                        if( !empty($value) ) {
                                            $document_id = $this->MkCommon->filterEmptyField($value, 'Journal', 'document_id');
                                            $type = $this->MkCommon->filterEmptyField($value, 'Journal', 'type');

                                            if( in_array($type, array( 'in', 'void_in', 'out', 'void_out', 'ppn_out', 'void_ppn_out', 'prepayment_out', 'void_prepayment_out', 'prepayment_in', 'void_prepayment_in' )) ) {
                                                $this->CashBank->id = $document_id;
                                                $this->CashBank->set('description', $koreksi_keterangan);
                                                $this->CashBank->save();
                                            } else if( in_array($type, array( 'leasing_payment', 'leasing_payment_void' )) ) {
                                                $this->loadModel('LeasingPayment');

                                                $this->LeasingPayment->id = $document_id;
                                                $this->LeasingPayment->set('note', $koreksi_keterangan);
                                                $this->LeasingPayment->save();
                                            } else if( in_array($type, array( 'lku_payment', 'lku_payment_void' )) ) {
                                                $this->loadModel('LkuPayment');
                                                
                                                $this->LkuPayment->id = $document_id;
                                                $this->LkuPayment->set('description', $koreksi_keterangan);
                                                $this->LkuPayment->save();
                                            } else if( in_array($type, array( 'ksu_payment', 'ksu_payment_void' )) ) {
                                                $this->loadModel('KsuPayment');
                                                
                                                $this->KsuPayment->id = $document_id;
                                                $this->KsuPayment->set('description', $koreksi_keterangan);
                                                $this->KsuPayment->save();
                                            } else if( in_array($type, array( 'invoice_payment', 'invoice_payment_void' )) ) {
                                                $this->loadModel('InvoicePayment');
                                                
                                                $this->InvoicePayment->id = $document_id;
                                                $this->InvoicePayment->set('description', $koreksi_keterangan);
                                                $this->InvoicePayment->save();
                                            } else if( in_array($type, array( 'biaya_ttuj_payment', 'biaya_ttuj_payment_void', 'uang_Jalan_commission_payment', 'uang_Jalan_commission_payment_void' )) ) {
                                                $this->loadModel('TtujPayment');
                                                
                                                $this->TtujPayment->id = $document_id;
                                                $this->TtujPayment->set('description', $koreksi_keterangan);
                                                $this->TtujPayment->save();
                                            }

                                            if( $this->User->Journal->updateAll(array(
                                                'Journal.title'=> "'".$koreksi_keterangan."'",
                                                'Journal.title_old'=> "'".$keterangan."'",
                                            ), array(
                                                'Journal.document_id'=> $no_ref,
                                                'Journal.document_no'=> $no_dokumen,
                                            )) ) {
                                                $successfull_row++;
                                            } else {
                                                $error_message .= sprintf(__('Gagal pada baris ke %s : Gagal mengubah keterangan'), $row_submitted+1) . '<br>';
                                                $failed_row++;
                                            }
                                        } else {
                                            $error_message .= sprintf(__('Gagal pada baris ke %s : Journal tidak ditemukan'), $row_submitted+1) . '<br>';
                                            $failed_row++;
                                        }
                                    } else {
                                        $error_message .= sprintf(__('Gagal pada baris ke %s : Tidak ada revisi keterangan'), $row_submitted+1) . '<br>';
                                        $failed_row++;
                                    }

                                    $row_submitted++;
                                }
                            }
                        }
                    }
                }

                if(!empty($successfull_row)) {
                    $message_import1 = sprintf(__('Import Berhasil: (%s baris), dari total (%s baris)'), $successfull_row, $row_submitted);
                    $this->MkCommon->setCustomFlash(__($message_import1), 'success');
                }
                
                if(!empty($error_message)) {
                    $this->MkCommon->setCustomFlash(__($error_message), 'error');
                }
                $this->redirect(array('action'=>'import_revision'));
            } else {
                $this->MkCommon->setCustomFlash(__('Maaf, terjadi kesalahan. Silahkan coba lagi, atau hubungi Admin kami.'), 'error');
                $this->redirect(array(
                    'action'=>'import_revision'
                ));
            }
        }
    }

    public function cash_flows( $data_action = false ) {
        $dateFrom = date('Y-m-d');
        $dateTo = date('Y-m-d');

        $options = array(
            'conditions'=> array(
                'Journal.coa_id <>' => 0,
            ),
            'contain' => false,
            'order'=> array(
                'Journal.created' => 'DESC',
                'Journal.document_id' => 'ASC',
            ),
            'group' => array(
                'Journal.document_id',
                'Journal.type',
            ),
        );

        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));
        $params['named']['status'] = 'active';

        $options =  $this->User->Journal->_callRefineParams($params, $options);
        $options = $this->MkCommon->getConditionGroupBranch( $params, 'Journal', $options );

        $values = $this->User->Journal->getData('all', $options);
        $data = array();
        $dataRequest = $this->request->data;

        if( !empty($values) ) {
            foreach ($values as $key => $value) {
                $coa_id = $this->MkCommon->filterEmptyField($value, 'Journal', 'coa_id');
                $debit = $this->MkCommon->filterEmptyField($value, 'Journal', 'debit');
                $credit = $this->MkCommon->filterEmptyField($value, 'Journal', 'credit');

                $value = $this->User->Journal->Coa->getMerge($value, $coa_id);
                $coa_type = $this->MkCommon->filterEmptyField($value, 'Coa', 'type');

                switch ($coa_type) {
                    case 'debit':
                        if( !empty($debit) ) {
                            $data = $this->User->Journal->_callCashFlow($data, $value, array(
                                'conditions' => array(
                                    'Journal.credit <>' => 0,
                                ),
                                'cashflow' => 'in',
                                'total_field' => 'total_credit',
                            ));
                        } else if( !empty($credit) ) {
                            $data = $this->User->Journal->_callCashFlow($data, $value, array(
                                'conditions' => array(
                                    'Journal.debit <>' => 0,
                                ),
                                'cashflow' => 'out',
                                'total_field' => 'total_debit',
                            ));
                        }
                        break;
                    
                    case 'credit':
                        if( !empty($debit) ) {
                            $data = $this->User->Journal->_callCashFlow($data, $value, array(
                                'conditions' => array(
                                    'Journal.debit <>' => 0,
                                ),
                                'cashflow' => 'out',
                                'total_field' => 'total_debit',
                            ));
                        } else if( !empty($credit) ) {
                            $data = $this->User->Journal->_callCashFlow($data, $value, array(
                                'conditions' => array(
                                    'Journal.credit <>' => 0,
                                ),
                                'cashflow' => 'in',
                                'total_field' => 'total_credit',
                            ));
                        }
                        break;
                }

                $values[$key] = $value;
            }
        }

        $this->RjCashBank->_callBeforeViewCashFlow($params, $data_action);
        $this->set(compact(
            'values', 'data'
        ));
    }

    function general_ledgers(){
        $this->loadModel('GeneralLedger');
        
        $dateFrom = date('Y-m-d', strtotime('-1 Month'));
        $dateTo = date('Y-m-d');

        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));
        $options =  $this->GeneralLedger->_callRefineParams($params);
        $this->paginate = $this->GeneralLedger->getData('paginate', $options);
        $values = $this->paginate('GeneralLedger');

        if( !empty($values) ) {
            foreach ($values as $key => &$value) {
                $allow_closing = $this->MkCommon->_callAllowClosing($value, 'GeneralLedger', 'transaction_date', 'Y-m', false);
                $value['GeneralLedger']['AllowClosing'] = $allow_closing;
            }
        }

        $this->set('sub_module_title', __('Jurnal Umum'));
        $this->set('active_menu', 'general_ledgers');
        $this->set(compact(
            'values'
        ));
    }

    public function general_ledger_add() {
        $this->set('sub_module_title', __('Tambah Jurnal Umum'));
        $data = $this->request->data;

        if( !empty($data) ) {
            $data = $this->RjCashBank->_callBeforeSaveGeneralLedger($data);
            $result = $this->User->GeneralLedger->doSave($data);
            $this->MkCommon->setProcessParams($result, array(
                'controller' => 'cashbanks',
                'action' => 'general_ledgers',
                'admin' => false,
            ));
        }

        $this->RjCashBank->_callBeforeRenderGeneralLedger();
        $this->set('active_menu', 'general_ledgers');
    }

    public function general_ledger_edit( $id = false ) {
        $this->set('sub_module_title', __('Edit Jurnal Umum'));

        $value = $this->User->GeneralLedger->getData('first', array(
            'conditions' => array(
                'GeneralLedger.id' => $id,
            ),
        ), array(
            'status' => array( 'unposting', 'posting' ),
        ));

        if( !empty($value) ) {
            $this->MkCommon->_callAllowClosing($value, 'GeneralLedger', 'transaction_date', 'Y-m');

            $data = $this->request->data;
            $value = $this->User->GeneralLedger->GeneralLedgerDetail->getMerge($value, $id);

            if( !empty($data) ) {
                $data = $this->RjCashBank->_callBeforeSaveGeneralLedger($data, $value);
                $result = $this->User->GeneralLedger->doSave($data, $value);
                $this->MkCommon->setProcessParams($result, array(
                    'controller' => 'cashbanks',
                    'action' => 'general_ledgers',
                    'admin' => false,
                ));
            }

            $this->RjCashBank->_callBeforeRenderGeneralLedger( $value );

            $this->set(array(
                'active_menu' => 'general_ledgers',
                'value' => $value,
            ));
            $this->render('general_ledger_add');
        } else {
            $this->MkCommon->redirectReferer(__('Jurnal Umum tidak ditemukan.'), 'error');
        }
    }

    public function general_ledger_detail( $id = false ) {
        $this->set('sub_module_title', __('Detail Jurnal Umum'));

        $value = $this->User->GeneralLedger->getData('first', array(
            'conditions' => array(
                'GeneralLedger.id' => $id,
            ),
        ), array(
            'branch' => false,
        ));

        if( !empty($value) ) {
            $value = $this->User->GeneralLedger->GeneralLedgerDetail->getMerge($value, $id);
            $this->RjCashBank->_callBeforeRenderGeneralLedger( $value );

            $this->set(array(
                'view' => true,
                'active_menu' => 'general_ledgers',
                'value' => $value,
            ));
            $this->render('general_ledger_add');
        } else {
            $this->MkCommon->redirectReferer(__('Jurnal Umum tidak ditemukan.'), 'error');
        }
    }

    public function general_ledger_toggle( $id ) {
        $value = $this->User->GeneralLedger->getData('first', array(
            'conditions' => array(
                'GeneralLedger.id' => $id,
                'GeneralLedger.transaction_status NOT' => array( 'void' ),
            ),
        ));

        if( !empty($value) ) {
            $transaction_status = $this->MkCommon->filterEmptyField($value, 'GeneralLedger', 'transaction_status');

            if( $transaction_status == 'posting' ) {
                $this->MkCommon->_callAllowClosing($value, 'GeneralLedger', 'transaction_date');
            }

            $is_ajax = $this->RequestHandler->isAjax();
            $msg = array(
                'msg' => '',
                'type' => 'error'
            );
            $data = $this->request->data;

            if(!empty($data)){
                $data = $this->MkCommon->dataConverter($data, array(
                    'date' => array(
                        'GeneralLedger' => array(
                            'canceled_date',
                        ),
                    )
                ));
                    
                $result = $this->User->GeneralLedger->doDelete( $id, $value, $data );
                $msg = array(
                    'msg' => $this->MkCommon->filterEmptyField($result, 'msg'),
                    'type' => $this->MkCommon->filterEmptyField($result, 'status'),
                );
                $this->MkCommon->setProcessParams($result, false, array(
                    'ajaxFlash' => true,
                    'noRedirect' => true,
                ));
            }

            $modelName = 'GeneralLedger';
            $canceled_date = $this->MkCommon->filterEmptyField($data, $modelName, 'canceled_date');
            $this->set('_flash', false);
            $this->set(compact(
                'msg', 'is_ajax',
                'canceled_date', 'modelName', 'value'
            ));
            $this->render('/Elements/blocks/common/form_delete');
        } else {
            $this->MkCommon->redirectReferer(__('Jurnal Umum tidak ditemukan.'), 'error');
        }
    }

    public function import_journal() {
        App::import('Vendor', 'excelreader'.DS.'excel_reader2');

        $this->set('module_title', __('Jurnal'));
        $this->set('active_menu', 'cash_bank');
        $this->set('sub_module_title', __('Import Excel'));

        if(!empty($this->request->data)) { 
            $Zipped = $this->request->data['Import']['importdata'];

            if($Zipped["name"]) {
                $filename = $Zipped["name"];
                $source = $Zipped["tmp_name"];
                $type = $Zipped["type"];
                $name = explode(".", $filename);
                $accepted_types = array('application/vnd.ms-excel', 'application/ms-excel');

                if(!empty($accepted_types)) {
                    foreach($accepted_types as $mime_type) {
                        if($mime_type == $type) {
                            $okay = true;
                            break;
                        }
                    }
                }

                $continue = strtolower($name[1]) == 'xls' ? true : false;

                if(!$continue) {
                    $this->MkCommon->setCustomFlash(__('Maaf, silahkan upload file dalam bentuk Excel.'), 'error');
                    $this->redirect(array('action'=>'import'));
                } else {
                    $path = APP.'webroot'.DS.'files'.DS.date('Y').DS.date('m').DS;
                    $filenoext = basename ($filename, '.xls');
                    $filenoext = basename ($filenoext, '.XLS');
                    $fileunique = uniqid() . '_' . $filenoext;

                    if( !file_exists($path) ) {
                        mkdir($path, 0755, true);
                    }

                    $targetdir = $path . $fileunique . $filename;
                     
                    ini_set('memory_limit', '96M');
                    ini_set('post_max_size', '64M');
                    ini_set('upload_max_filesize', '64M');

                    if(!move_uploaded_file($source, $targetdir)) {
                        $this->MkCommon->setCustomFlash(__('Maaf, terjadi kesalahan. Silahkan coba lagi, atau hubungi Admin kami.'), 'error');
                        $this->redirect(array('action'=>'import'));
                    }
                }
            } else {
                $this->MkCommon->setCustomFlash(__('Maaf, terjadi kesalahan. Silahkan coba lagi, atau hubungi Admin kami.'), 'error');
                $this->redirect(array('action'=>'import'));
            }

            $xls_files = glob( $targetdir );

            if(empty($xls_files)) {
                $this->MkCommon->setCustomFlash(__('Tidak terdapat file excel atau berekstensi .xls pada file zip Anda. Silahkan periksa kembali.'), 'error');
                $this->redirect(array('action'=>'import'));
            } else {
                $uploadedXls = $this->MkCommon->addToFiles('xls', $xls_files[0]);
                $uploaded_file = $uploadedXls['xls'];
                $file = explode(".", $uploaded_file['name']);
                $extension = array_pop($file);
                
                if($extension == 'xls') {
                    $dataimport = new Spreadsheet_Excel_Reader();
                    $dataimport->setUTFEncoder('iconv');
                    $dataimport->setOutputEncoding('UTF-8');
                    $dataimport->read($uploaded_file['tmp_name']);
                    
                    if(!empty($dataimport)) {
                        $data = $dataimport;
                        $row_submitted = 1;
                        $successfull_row = 0;
                        $failed_row = 0;
                        $error_message = '';
                        $cnt = 0;

                        for ($x=2;$x<=count($data->sheets[0]["cells"]); $x++) {
                            $datavar = array();
                            $flag = true;
                            $i = 1;

                            while ($flag) {
                                if( !empty($data->sheets[0]["cells"][1][$i]) ) {
                                    $variable = $this->MkCommon->toSlug($data->sheets[0]["cells"][1][$i], '_');
                                    $thedata = !empty($data->sheets[0]["cells"][$x][$i])?$data->sheets[0]["cells"][$x][$i]:NULL;
                                    $$variable = $thedata;
                                    $datavar[] = $thedata;
                                } else {
                                    $flag = false;
                                }
                                $i++;
                            }

                            if(array_filter($datavar)) {
                                $src = !empty($src)?$src:false;
                                $kode_acc = !empty($kode_acc)?$kode_acc:false;
                                $tgl = !empty($tgl)?$tgl:false;
                                $ket = !empty($ket)?$ket:false;
                                $debit = !empty($debit)?$debit:0;
                                $kredit = !empty($kredit)?$kredit:0;
                                
                                $debit = str_replace(array('*',' ',','), array('','',''), $debit);
                                $kredit = str_replace(array('*',' ',','), array('','',''), $kredit);
                                $tgl_tmp = Common::formatDate($tgl, 'Y-m-d');

                                if( $tgl_tmp == '1970-01-01' ) {
                                    $tgl = Common::getDate($tgl);
                                }

                                $coa = $this->User->Coa->getData('first', array(
                                    'conditions' => array(
                                        'Coa.code' => $kode_acc,
                                        'Coa.status' => 1,
                                    ),
                                ));

                                $dataTmp = array(
                                    'branch_id' => 15,
                                    'document_id' => $src,
                                    'coa_id' => Common::hashEmptyField($coa, 'Coa.id'),
                                    'date' => Common::formatDate($tgl, 'Y-m-d'),
                                    'title' => $ket,
                                    'debit' => $debit,
                                    'credit' => $kredit,
                                );

                                if( $this->User->Journal->saveAll($dataTmp) ){                                        
                                    $this->Log->logActivity( __('Sukses upload by Import Excel'), $this->user_data, $this->RequestHandler, $this->params );
                                    $successfull_row++;
                                } else {
                                    $validationErrors = $this->User->Coa->validationErrors;
                                    $textError = array();

                                    if( !empty($validationErrors) ) {
                                        foreach ($validationErrors as $key => $validationError) {
                                            if( !empty($validationError) ) {
                                                foreach ($validationError as $key => $error) {
                                                    $textError[] = $error;
                                                }
                                            }
                                        }
                                    }

                                    if( !empty($textError) ) {
                                        $textError = implode(', ', $textError);
                                    } else {
                                        $textError = '';
                                    }

                                    $failed_row++;
                                    $error_message .= sprintf(__('Gagal pada baris ke %s : Gagal Upload Data. %s'), $row_submitted, $textError) . '<br>';
                                }

                                $row_submitted++;
                                $cnt++;
                            }
                        }
                    }
                }
            }

            if(!empty($successfull_row)) {
                $message_import1 = sprintf(__('Import Berhasil: (%s baris), dari total (%s baris)'), $successfull_row, $cnt);
                $this->MkCommon->setCustomFlash(__($message_import1), 'success');
            }
            
            if(!empty($error_message)) {
                $this->MkCommon->setCustomFlash(__($error_message), 'error');
            }
            $this->redirect(array('action'=>'import_journal'));
        }
    }

    function profit_loss_per_point () {
        $module_title = $sub_module_title = __('Laporan Laba Rugi Per Poin');
        $dateFrom = date('Y-m-d', strtotime('-1 Month'));
        $dateTo = date('Y-m-d');

        $values = $this->User->Cogs->getData('threaded', array(
            'conditions' => array(
                'Cogs.status' => 1,
            ),
            'order' => array(
                'Cogs.order_sort' => 'ASC',
                'Cogs.order' => 'ASC',
                'Cogs.code' => 'ASC',
            )
        ));
        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'monthFrom' => $dateFrom,
            'monthTo' => $dateTo,
        ));
        $dateFrom = $this->MkCommon->filterEmptyField($params, 'named', 'MonthFrom');
        $dateTo = $this->MkCommon->filterEmptyField($params, 'named', 'MonthTo');

        if( !empty($dateFrom) && !empty($dateTo) ) {
            $sub_module_title = sprintf('%s - Periode %s', $module_title, $this->MkCommon->getCombineDate($dateFrom, $dateTo, 'short'));
        } else {
            $sub_module_title = false;
        }

        $this->set('active_menu', 'profit_loss_per_point');
        $this->set(compact(
            'values', 'module_title', 'dateFrom',
            'dateTo', 'sub_module_title'
        ));
    }

    function profit_loss_per_point_amount ( $id = NULL, $dateFrom = NULL, $dateTo = NULL ) {
        $this->User->Journal->virtualFields['total_debit'] = 'SUM(Journal.debit)';
        $this->User->Journal->virtualFields['total_credit'] = 'SUM(Journal.credit)';

        $value = $this->User->Journal->Cogs->getMerge(array(), $id);

        $parent_id = Common::hashEmptyField($value, 'Cogs.parent_id');
        $result = array();

        if( !empty($dateFrom) && !empty($dateTo) ) {
            $options = $this->User->Journal->getData('paginate', array(
                'conditions' => array(
                    'Journal.cogs_id' => $id,
                    'DATE_FORMAT(Journal.date, \'%Y-%m\') >=' => $dateFrom,
                    'DATE_FORMAT(Journal.date, \'%Y-%m\') <=' => $dateTo,
                ),
                'contain' => false,
                'group' => array(
                    'Journal.cogs_id',
                ),
            ));

            $optionsRev = $options;
            $optionsRev['conditions']['Journal.type'] = array( 'in','revenue','general_ledger','invoice_payment','asset_selling' );
            $summaryRev = $this->User->Journal->find('first', $optionsRev);

            $optionsExp = $options;
            $optionsExp['conditions']['Journal.type'] = array( 'out','document_payment','insurance_payment','lku_payment','ksu_payment','laka_payment','leasing_payment','po_payment','uang_Jalan_commission_payment','biaya_ttuj_payment' );
            $summaryExp = $this->User->Journal->find('first', $optionsExp);

            $optionsMaintain = $options;
            $optionsMaintain['conditions']['Journal.type'] = array( 'spk_payment' );
            $summaryMaintain = $this->User->Journal->find('first', $optionsMaintain);

            $revenue = Common::hashEmptyField($summaryRev, 'Journal.total_debit', 0);
            $expense = Common::hashEmptyField($summaryExp, 'Journal.total_credit', 0);
            $maintenance = Common::hashEmptyField($summaryMaintain, 'Journal.total_credit', 0);
            $out = $expense + $maintenance;
            $er = 0;

            if( !empty($out) ) {
                $er = $out / $revenue;
            }

            $result['revenue'] = $revenue;
            $result['expense'] = $expense;
            $result['maintenance'] = $maintenance;
            $result['gross-profit'] = $revenue - $out;
            $result['er'] = $er;
        }

        $this->set(compact(
            'result', 'id', 'tmpDateFrom', 'coa_type'
        ));
        $this->render('profit_loss_per_point_amount');
    }

    function budgets(){
        $this->loadModel('Budget');
        $options = array();

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['name'])){
                $name = urldecode($refine['name']);
                $this->request->data['Search']['name'] = $name;
                $coas = $this->User->Journal->Coa->getData('list', array(
                    'conditions' => array(
                        'OR' => array(
                            'Coa.code LIKE' => '%'.$name.'%',
                            'Coa.name LIKE' => '%'.$name.'%',
                        ),
                    ),
                    'fields' => array(
                        'Coa.id',
                    ),
                ));
                $options['conditions']['Budget.coa_id'] = $coas;
            }
        }

        $this->paginate = $this->Budget->getData('paginate', $options);
        $values = $this->paginate('Budget');
        $values = $this->Budget->getMergeList($values, array(
            'contain' => array(
                'Coa',
            ),
        ));

        $this->set('active_menu', 'budgets');
        $this->set('sub_module_title', __('Budget'));
        $this->set('values', $values);
    }

    function budget_add(){
        $this->set('sub_module_title', __('Tambah Budget'));
        $this->doBudget();
    }

    function budget_edit($id){
        $this->set('sub_module_title', __('Edit Budget'));
        $value = $this->User->Journal->Coa->Budget->getData('first', array(
            'conditions' => array(
                'Budget.id' => $id
            )
        ));

        if(!empty($value)){
            $value = $this->User->Journal->Coa->Budget->getMergeList($value, array(
                'contain' => array(
                    'BudgetDetail',
                ),
            ));
            $this->doBudget($id, $value);
        }else{
            $this->MkCommon->setCustomFlash(__('Budget tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'settings',
                'action' => 'budgets'
            ));
        }
    }

    function doBudget($id = false, $data_local = false){
        if(!empty($this->request->data)){
            $data = $this->request->data;

            if($id && $data_local){
                $this->User->Journal->Coa->Budget->id = $id;
                $msg = 'merubah';
            }else{
                $this->User->Journal->Coa->Budget->create();
                $msg = 'menambah';
            }
            $this->User->Journal->Coa->Budget->set($data);
            $dataDetail = array();
            $validateDetail = true;

            if( !empty($data['BudgetDetail']['budget']) ) {
                foreach ($data['BudgetDetail']['budget'] as $key => $budget) {
                    if( !empty($budget) ) {
                        $dataTemp = array(
                            'month' => date('m', mktime(0, 0, 0, $key+1, 1, date("Y"))),
                            'budget' => $budget,
                        );
                        $this->User->Journal->Coa->Budget->BudgetDetail->set($dataTemp);

                        if( !$this->User->Journal->Coa->Budget->BudgetDetail->validates() ) {
                            $validateDetail = false;
                        }
                    }
                }
            }

            if( $this->User->Journal->Coa->Budget->validates($data) && $validateDetail ){
                if($this->User->Journal->Coa->Budget->save($data)){
                    $id = $this->User->Journal->Coa->Budget->id;

                    if( !empty($data['BudgetDetail']['budget']) ) {
                        $idx = 0;
                        foreach ($data['BudgetDetail']['budget'] as $key => $budget) {
                            if( !empty($budget) ) {
                                $dataDetail[$idx]['BudgetDetail'] = array(
                                    'budget_id' => $id,
                                    'month' => date('m', mktime(0, 0, 0, $key+1, 1, date("Y"))),
                                    'budget' => Common::_callPriceConverter($budget),
                                );
                                $idx++;
                            }
                        }
                    }

                    $this->User->Journal->Coa->Budget->BudgetDetail->deleteAll(array( 
                        'BudgetDetail.budget_id' => $id,
                    ));

                    $this->User->Journal->Coa->Budget->BudgetDetail->saveMany( $dataDetail );

                    $this->params['old_data'] = $data_local;
                    $this->params['data'] = $data;

                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Budget'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s Budget #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
                    $this->redirect(array(
                        'controller' => 'cashbanks',
                        'action' => 'budgets'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Budget'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s Budget #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id ); 
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Budget'), $msg), 'error');
            }
        }else{
            if($id && $data_local){
                $this->request->data = $data_local;

                if( !empty($this->request->data['BudgetDetail']) ) {
                    $budgetDetail = $this->request->data['BudgetDetail'];
                    unset($this->request->data['BudgetDetail']);

                    foreach ($budgetDetail as $key => $value) {
                        $this->request->data['BudgetDetail']['budget'][$value['BudgetDetail']['month']-1] = $value['BudgetDetail']['budget'];
                    }
                }
            }
        }

        $coas = $this->User->Journal->Coa->_callOptGroup();
        $this->MkCommon->_layout_file('select');

        $this->set('active_menu', 'budgets');
        $this->set('coas', $coas);
        $this->render('budget_form');
    }

    function budget_toggle($id){
        $locale = $this->User->Journal->Coa->Budget->getData('first', array(
            'conditions' => array(
                'Budget.id' => $id
            )
        ));

        if($locale){
            $value = true;
            if($locale['Budget']['status']){
                $value = false;
            }

            $this->User->Journal->Coa->Budget->id = $id;
            $this->User->Journal->Coa->Budget->set('status', $value);
            if($this->User->Journal->Coa->Budget->save()){
                $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
                $this->Log->logActivity( sprintf(__('Sukses merubah status Budget ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id ); 
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal merubah status Budget ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id ); 
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Budget tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    public function budget_report() {
        $monthFrom = date('Y-01');
        $monthTo = date('Y-m');
        $params = $this->MkCommon->_callRefineParams($this->params->params, array(
            'monthFrom' => $monthFrom,
            'monthTo' => $monthTo,
        ));

        $dataReport = $this->RmReport->_callDataBudget_report($params, 30, 0, true);
        $values = Common::hashEmptyField($dataReport, 'data');

        $this->RjCashBank->_callBeforeViewBudgetReports($params);
        $this->MkCommon->_layout_file(array(
            'select',
            'freeze',
        ));
        $this->set(array(
            'values' => $values,
            'active_menu' => 'budget_report',
            '_freeze' => true,
        ));
    }
}