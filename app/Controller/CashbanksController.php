<?php
App::uses('AppController', 'Controller');
class CashbanksController extends AppController {
	public $uses = array(
        'CashBank', 'CashBankAuth'
    );
    public $components = array(
        'RjCashBank'
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
        $conditionToApprove = $this->User->Employe->EmployePosition->Approval->_callGetDataToApprove(1);

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

        $this->set('active_menu', 'cash_bank');
        $this->set(compact(
            'values'
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
                ),
                'contain' => array(
                    'CashBankDetail',
                    'CashBankAuth'
                )
            ));

            if( !empty($cashbank) ) {
                $this->set('sub_module_title', 'Rubah Kas/Bank');
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
                    'CashBankAuth',
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
            $data['CashBank']['tgl_cash_bank'] = $this->MkCommon->getDate($data['CashBank']['tgl_cash_bank']);
            $coas_validate = true;
            $totalTagihanCredit = 0;
            $totalTagihanDebit = 0;
            $debit_total = 0;
            $credit_total = 0;
            $total_coa = 0;
            $prepayment_status = false;

            $document_id = $this->MkCommon->filterEmptyField($data, 'CashBank', 'document_id');
            $document_type = $this->MkCommon->filterEmptyField($data, 'CashBank', 'document_type');
            $document_no = $this->MkCommon->filterEmptyField($data, 'CashBank', 'nodoc');
            $document_coa_id = $this->MkCommon->filterEmptyField($data, 'CashBank', 'coa_id');
            $description = $this->MkCommon->filterEmptyField($data, 'CashBank', 'description');

            $receiver_id = $this->MkCommon->filterEmptyField($data, 'CashBank', 'receiver_id');
            $receiver_type = $this->MkCommon->filterEmptyField($data, 'CashBank', 'receiver_type');
            $tgl_cash_bank = $this->MkCommon->filterEmptyField($data, 'CashBank', 'tgl_cash_bank');

            $data['CashBank']['is_revised'] = 0;
            $data['CashBank']['branch_id'] = Configure::read('__Site.config_branch_id');
            $data['CashBank']['user_id'] = $this->user_id;

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
                    $total_coa_detail = (!empty($data['CashBankDetail']['total'][$key])) ? str_replace(',', '', $data['CashBankDetail']['total'][$key]) : 0;
                    $paid = false;

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
                        $totalDibayar = $this->CashBank->CashBankDetail->totalPrepaymentDibayarPerCoa($document_id, $coa_id);
                        $totalTagihanDetail = !empty($cashBankDetail['CashBankDetail']['total'])?$cashBankDetail['CashBankDetail']['total']-$totalDibayar:0;

                        if( $totalTagihanDetail > $total_coa_detail ) {
                            $paid = 'half_paid';
                        } else if( $totalTagihanDetail <= $total_coa_detail ) {
                            $paid = 'full_paid';
                        }
                    }

                    $arr_list[] = array(
                        'coa_id' => $coa_id,
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
                    ),
                ));
                $totalTagihanDebit = !empty($cashBankTagihan['CashBank']['debit_total'])?$cashBankTagihan['CashBank']['debit_total']:0;
                $totalTagihanCredit = !empty($cashBankTagihan['CashBank']['credit_total'])?$cashBankTagihan['CashBank']['credit_total']:0;
                $totalDibayar = $this->CashBank->totalPrepaymentDibayar($document_id);
                $totalTagihanCashBank = ($totalTagihanDebit + $totalTagihanCredit) - $totalDibayar;

                if( $totalTagihanCashBank > $totalCashBank ) {
                    $prepayment_status = 'half_paid';
                } else if( $totalTagihanCashBank <= $totalCashBank ) {
                    $prepayment_status = 'full_paid';
                }
            }

            $allowApprovals = $this->User->Employe->EmployePosition->Approval->_callNeedApproval(1, $total_coa);

            if( empty($allowApprovals) ) {
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

                        $this->CashBank->CashBankAuth->deleteAll(array(
                            'CashBankAuth.cash_bank_id' => $cash_bank_id
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

                            if( empty($allowApprovals) ) {
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
                                    'document_id' => $cash_bank_id,
                                    'title' => $title,
                                    'document_no' => $document_no,
                                    'type' => $receiving_cash_type,
                                    'date' => $tgl_cash_bank,
                                ));
                            }
                        }

                        if( empty($allowApprovals) ) {
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

                    if( !empty($allowApprovals) ) {
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

                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Kas/Bank'), $msg), 'success');
                    $this->redirect(array(
                        'controller' => 'cashbanks',
                        'action' => 'index'
                    ));
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
                $curr_coa = $this->User->Journal->Coa->getData('first', array(
                    'conditions' => array(
                        'Coa.id' => $value['coa_id']
                    )
                ));

                if(!empty($curr_coa)){
                    $data['CashBankDetail'][$key]['name_coa'] = $curr_coa['Coa']['name'];
                    $data['CashBankDetail'][$key]['code_coa'] = $curr_coa['Coa']['code'];
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

        $coas = $this->User->Journal->Coa->getData('list', array(
            'conditions' => array(
                'Coa.level' => 4,
                'Coa.is_cash_bank' => 1,
                'Coa.status' => 1
            ),
            'fields' => array(
                'Coa.id', 'Coa.coa_name'
            ),
        ));
        $branches = $this->User->Branch->City->branchCities();
        
        $this->set('active_menu', 'cash_bank');
        $this->set('module_title', 'Kas/Bank');

        $this->MkCommon->_layout_file('select');
        $this->set(compact(
            'coas', 'document_id', 'receiving_cash_type',
            'docs', 'urlBrowseDocument', 'prepayment_out_id',
            'id', 'data_local', 'branches',
            'user_otorisasi_approvals'
        ));

        $this->render('cashbank_form');
    }

    function cashbank_delete($id){
        $locale = $this->CashBank->getData('first', array(
            'conditions' => array(
                'CashBank.id' => $id
            )
        ));

        if(!empty($locale)){
            $value = true;
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

                $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
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
        $conditionToApprove = $this->User->Employe->EmployePosition->Approval->_callGetDataToApprove(1);
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

            $tgl_cash_bank = $this->MkCommon->filterEmptyField($cashbank, 'CashBank', 'tgl_cash_bank');
            $document_type = $this->MkCommon->filterEmptyField($cashbank, 'CashBank', 'document_type');
            $receiver_type = $this->MkCommon->filterEmptyField($cashbank, 'CashBank', 'receiver_type');
            $receiving_cash_type = $this->MkCommon->filterEmptyField($cashbank, 'CashBank', 'receiving_cash_type');
            $nodoc = $this->MkCommon->filterEmptyField($cashbank, 'CashBank', 'nodoc');
            $debit_total = $this->MkCommon->filterEmptyField($cashbank, 'CashBank', 'debit_total', 0);
            $credit_total = $this->MkCommon->filterEmptyField($cashbank, 'CashBank', 'credit_total', 0);
            $grand_total = $this->MkCommon->filterEmptyField($cashbank, 'CashBank', 'grand_total', 0);
            
            $cashbank = $this->User->getMerge($cashbank, $user_id);
            $cashbank = $this->CashBank->Coa->getMerge($cashbank, $document_coa_id);
            $cashbank = $this->CashBank->CashBankDetail->getMerge($cashbank, $id, array(
                'contain' => array(
                    'Coa'
                ),
            ));

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

                $auth = $this->CashBank->CashBankAuth->getData('first', array(
                    'conditions' => array(
                        'CashBankAuth.cash_bank_id' => $id,
                        'CashBankAuth.approval_id' => $approval_id,
                        'CashBankAuth.approval_detail_id' => $approval_detail_id,
                        'CashBankAuth.approval_detail_position_id' => $approval_detail_position_id,
                    ),
                ));

                if( empty($auth) ) {
                    $show_approval = in_array($approval_position_id, $position_otorisasi_approvals)?true:false;
                }
            }

            if( !empty($this->request->data) ){
                if( !empty($show_approval) ){
                    $data = $this->request->data;
                    $is_priority = $this->MkCommon->filterEmptyField($dataOtorisasiApproval, 'ApprovalDetailPosition', 'is_priority');
                    $employe_position_id = $this->MkCommon->filterEmptyField($dataOtorisasiApproval, 'ApprovalDetailPosition', 'employe_position_id');
                    $status_document = $this->MkCommon->filterEmptyField($data, 'CashBankAuth', 'status_document');

                    $data['CashBankAuth']['cash_bank_id'] = $id;
                    $data['CashBankAuth']['approval_id'] = $approval_id;
                    $data['CashBankAuth']['approval_detail_id'] = $approval_detail_id;
                    $data['CashBankAuth']['approval_detail_position_id'] = $approval_detail_position_id;

                    $position_auths = $this->CashBank->CashBankAuth->getData('all', array(
                        'conditions' => array(
                            'CashBankAuth.cash_bank_id' => $id,
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
                        $this->CashBank->CashBankAuth->create();
                        $this->CashBank->CashBankAuth->set($data);

                        if($this->CashBank->CashBankAuth->save()){
                            $data_arr = array();

                            if( $this->MkCommon->checkArrayApproval($position_priority_auth, $position_priority) || ( empty($position_priority) && $this->MkCommon->checkArrayApproval($position_normal_auth, $position_normal) ) ){
                                switch ($status_document) {
                                    case 'approve':
                                        $data_arr = array(
                                            'completed' => 1,
                                            'is_revised' => 0,
                                            'is_rejected' => 0
                                        );
                                        break;
                                    case 'revise':
                                        $data_arr = array(
                                            'completed' => 0,
                                            'is_revised' => 1,
                                            'is_rejected' => 0
                                        );
                                        break;
                                    case 'reject':
                                        $data_arr = array(
                                            'completed' => 0,
                                            'is_revised' => 0,
                                            'is_rejected' => 1
                                        );
                                        break;
                                }
                            }else if($status_document == 'revise'){
                                $data_arr = array(
                                    'is_revised' => 1,
                                );
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

                                                $this->User->Journal->setJournal($total, $coaArr, array(
                                                    'document_id' => $id,
                                                    'title' => $title,
                                                    'document_no' => $nodoc,
                                                    'type' => $receiving_cash_type,
                                                    'date' => $tgl_cash_bank,
                                                ));
                                            }
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
                                            'document_id' => $id,
                                            'title' => $title,
                                            'document_no' => $nodoc,
                                            'type' => $receiving_cash_type,
                                            'date' => $tgl_cash_bank,
                                        ));
                                    }
                                }
                            }

                            $this->MkCommon->setCustomFlash('Berhasil melakukan Approval Kas/Bank.', 'success');
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
                // if(!empty($cashbank['CashBankAuth'][0])){
                //     $this->request->data['CashBankAuth'] = $cashbank['CashBankAuth'][0];
                // }
            }

            if(!empty($cashbank['CashBank']['receiver_type'])){
                $model = $cashbank['CashBank']['receiver_type'];
                $receiver_id = !empty($cashbank['CashBank']['receiver_id'])?$cashbank['CashBank']['receiver_id']:false;
                $cashbank['CashBank']['receiver'] = $this->RjCashBank->_callReceiverName($receiver_id, $model);
            }

            // $cashBankAuth = $this->CashBankAuthMaster->CashBankAuth->find('all', array(
            //     'conditions' => array(
            //         'CashBankAuth.cash_bank_id' => $id,
            //         'CashBankAuthMaster.employe_id' => $this->user_id,
            //     ),
            //     'contain' => array(
            //         'CashBankAuthMaster'
            //     ),
            // ));

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

            $this->set('active_menu', 'cash_bank');
            // $this->set('cashBankAuth', $cashBankAuth);                
            // $this->set('cashbank_auth_id', $cashbank_auth_id);
            $this->set(compact(
                'user_otorisasi_approvals', 'cashbank',
                'show_approval'
            ));
        } else {
            $this->MkCommon->setCustomFlash(__('Kas/Bank tidak ditemukan.'), 'error');
            $this->redirect($this->referer());
        }
    }

    // function approval_setting(){
    //     $this->loadModel('CashBankAuthMaster');
    //     $cash_bank_auth_master = $this->CashBankAuthMaster->find('all');

    //     if(!empty($this->request->data)){
    //         $data = $this->request->data;

    //         $validate_auth = true;
    //         $user_collect = array();
    //         if(!empty($data['CashBankAuthMaster']['employe_id'])){
    //             $arr_list_auth = array();
    //             foreach ($data['CashBankAuthMaster']['employe_id'] as $key => $value) {
    //                 if(!in_array($value, $user_collect) && !empty($value)){
    //                     $id = !empty($data['CashBankAuthMaster']['id'][$key]) ? $data['CashBankAuthMaster']['id'][$key] : '';
    //                     $arr_list_auth[] = array(
    //                         'employe_id' => $value,
    //                         'level' => $key+1,
    //                         'id' => $id
    //                     );

    //                     array_push($user_collect, $value);
    //                 }
    //             }
    //             $data['CashBankAuthMaster'] = $arr_list_auth;
    //         }else{
    //             $validate_auth = false;
    //         }

    //         if($validate_auth && !empty($user_collect)){
    //             if(!empty($data['CashBankAuthMaster'])){
    //                 foreach ($data['CashBankAuthMaster'] as $key => $value) {
    //                     $id_cash = !empty($value['id'])?$value['id']:false;

    //                     if(!empty($id_cash)){
    //                         $this->CashBankAuthMaster->id = $id_cash;
    //                     }else{
    //                         $this->CashBankAuthMaster->create();
    //                     }

    //                     $this->CashBankAuthMaster->set($value);

    //                     if( $this->CashBankAuthMaster->save() ) {
    //                         $this->Log->logActivity( sprintf(__('Sukses melakukan setting approval #%s'), $this->CashBankAuthMaster->id), $this->user_data, $this->RequestHandler, $this->params );
    //                     } else {
    //                         $this->Log->logActivity( sprintf(__('Gagal melakukan setting approval #%s'), $id_cash), $this->user_data, $this->RequestHandler, $this->params, 1 );
    //                     }
    //                 }

    //                 $this->MkCommon->setCustomFlash('Sukses melakukan setting approval.', 'success');
    //                 $this->redirect(array(
    //                     'action' => 'approval_setting'
    //                 ));
    //             }
    //         }else{
    //             $this->MkCommon->setCustomFlash('Harap masukkan karyawan yang akan di jadikan approval Kas/Bank.', 'error');
    //         }
    //     }else{
    //         $data = array();
    //         if(!empty($cash_bank_auth_master)){
    //             foreach ($cash_bank_auth_master as $key => $value) {
    //                 $data['CashBankAuthMaster'][] = $value['CashBankAuthMaster'];
    //             }
    //         }
    //     }

    //     if(!empty($data['CashBankAuthMaster'])){
    //         $auth_data = array();
    //         foreach ($data['CashBankAuthMaster'] as $key => $value) {
    //             $group = $this->User->find('first', array(
    //                 'conditions' => array(
    //                     'User.id' => $value['employe_id']
    //                 ),
    //                 'contain' => array(
    //                     'Group'
    //                 )
    //             ));

    //             if(!empty($group['Group']['name'])){
    //                 $group = $group['Group']['name'];
    //             }
    //             $auth_data['CashBankAuthMaster'][] = array_merge($value, array(
    //                 'group' => $group
    //             ));
    //         }

    //         $this->set('auth_data', $auth_data);
    //     }

    //     $employes = $this->User->getData('list', array(
    //         'conditions' => array(
    //             'User.status' => 1,
    //         ),
    //         'fields' => array(
    //             'User.id', 'User.full_name'
    //         )
    //     ));

    //     $this->set('active_menu', 'approval_setting');
    //     $this->set(compact('employes', 'cash_bank_auth_master'));
    // }

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
        $coas = $this->Coa->getData('list', array(
            'conditions' => array(
                'Coa.level' => 4,
                'Coa.is_cash_bank' => 1,
                'Coa.status' => 1
            )
        ));
        $sub_module_title = 'Setting COA';
        $this->set(compact('cash_bank_settings', 'coas', 'sub_module_title'));
    }

    public function coa_setting() {
        $coaSetting = $this->User->CoaSetting->getData('first', array(
            'conditions' => array(
                'CoaSetting.status' => 1
            ),
        ));

        if(!empty($this->request->data)){
            $data = $this->request->data;
            $data['CoaSetting']['user_id'] = Configure::read('__Site.config_user_id');
            
            if( !empty($coaSetting['CoaSetting']['id']) ){
                $this->User->CoaSetting->id = $coaSetting['CoaSetting']['id'];
            }else{
                $this->User->CoaSetting->create();
            }

            $this->User->CoaSetting->set($data);

            if($this->User->CoaSetting->validates($data)){
                if($this->User->CoaSetting->save($data)){
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

        $coas = $this->User->Coa->getData('list', array(
            'conditions' => array(
                'Coa.level' => 4,
                'Coa.status' => 1
            ),
            'fields' => array(
                'Coa.id', 'Coa.coa_name'
            ),
        ));
        
        $this->MkCommon->_layout_file('select');
        $this->set('active_menu', 'coa_setting');
        $this->set(compact('coas'));
    }

    public function journal_report( $data_action = false ) {
        $this->set('sub_module_title', 'Laporan Jurnal');
        $dateFrom = date('Y-m-d', strtotime('-1 Month'));
        $dateTo = date('Y-m-d');
        $allow_branch_id = Configure::read('__Site.config_allow_branch_id');
        $conditions = array(
            'Journal.branch_id' => $allow_branch_id,
        );

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['document_no'])){
                $value = urldecode($refine['document_no']);
                $this->request->data['Journal']['document_no'] = $value;
                $conditions['Journal.document_no LIKE'] = '%'.$value.'%';
            }

            if(!empty($refine['date'])){
                $dateStr = urldecode($refine['date']);
                $date = explode('-', $dateStr);

                if( !empty($date) ) {
                    $date[0] = urldecode($date[0]);
                    $date[1] = urldecode($date[1]);
                    $dateFrom = $this->MkCommon->getDate($date[0]);
                    $dateTo = $this->MkCommon->getDate($date[1]);
                }
                $this->request->data['Ttuj']['date'] = $dateStr;
            }

            // Custom Otorisasi
            $conditions = $this->MkCommon->getConditionGroupBranch( $refine, 'Journal', $conditions, 'conditions' );
        }

        $module_title = __('Laporan Jurnal');

        if( !empty($dateFrom) && !empty($dateTo) ) {
            $this->request->data['Journal']['date'] = sprintf('%s - %s', date('d/m/Y', strtotime($dateFrom)), date('d/m/Y', strtotime($dateTo)));
            $module_title .= sprintf(' Periode %s', $this->MkCommon->getCombineDate($dateFrom, $dateTo));
        }

        $conditions['DATE_FORMAT(Journal.created, \'%Y-%m-%d\') >='] = $dateFrom;
        $conditions['DATE_FORMAT(Journal.created, \'%Y-%m-%d\') <='] = $dateTo;

        $values = $this->User->Journal->getData('all', array(
            'conditions' => $conditions,
        ));

        $this->set('active_menu', 'journal_report');
        $this->set(compact(
            'values', 'module_title', 'data_action'
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

                $options =  $this->User->Journal->_callRefineParams($params, array(
                    'conditions' => $conditions,
                    'group' => array(
                        'Journal.coa_id',
                        'Journal.document_no',
                        'Journal.type',
                    ),
                    'order' => array(
                        'Journal.created' => 'ASC',
                        'Journal.id' => 'ASC',
                    ),
                ));
                $values = $this->User->Journal->getData('all', $options);
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

        $this->set('active_menu', 'journal_report');
        $this->set(compact(
            'coas', 'values', 'module_title',
            'coa_name', 'data_action',
            'coa'
        ));

        if($data_action == 'pdf'){
            $this->layout = 'pdf';
        }else if($data_action == 'excel'){
            $this->layout = 'ajax';
        }
    }

    function getPrepaymentMerge ( $prepayment ) {
        // Get Data Prepayment IN
        $prepayment_out_id = $this->MkCommon->filterEmptyField($prepayment, 'CashBank', 'id');
        $prepayment['PrepaymentIN'] = $this->CashBank->getDocumentCashBank( $prepayment_out_id, 'prepayment_in' );

        if( !empty($prepayment['PrepaymentIN']) ) {
            foreach ($prepayment['PrepaymentIN'] as $key => $prepaymentIN) {
                $prepayment['PrepaymentIN'][$key] = $this->getPrepaymentMerge( $prepaymentIN );
            }
        }

        // Diterima/Dibayar Kepada
        $receiver_id = $this->MkCommon->filterEmptyField($prepayment, 'CashBank', 'receiver_id');
        $receiver_type = $this->MkCommon->filterEmptyField($prepayment, 'CashBank', 'receiver_type');
        $prepayment['Receiver']['name'] = $this->CashBank->getReceiver( $receiver_type, $receiver_id );

        // Get Data COA
        $coa_id = $this->MkCommon->filterEmptyField($prepayment, 'CashBank', 'coa_id');
        $prepayment = $this->Coa->getMerge($prepayment, $coa_id);

        return $prepayment;
    }

    public function prepayment_report( $data_action = false ) {
        $this->loadModel('CashBank');
        $conditions = array(
            'CashBank.is_rejected' => 0,
            'CashBank.receiving_cash_type' => 'prepayment_out',
        );
        $fromDate = date('01/m/Y');
        $toDate = date('t/m/Y');

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['date'])){
                $dateStr = urldecode($refine['date']);
                $date = explode('-', $dateStr);

                if( !empty($date) ) {
                    $date[0] = urldecode($date[0]);
                    $date[1] = urldecode($date[1]);
                    $dateStr = sprintf('%s-%s', $date[0], $date[1]);
                    $fromDate = $date[0];
                    $toDate = $date[1];
                }
            }

            if( !empty($refine['receiver']) ){
                $value = urldecode($refine['receiver']);
                $receivers = $this->CashBank->getReceiver('Customer', $value, 'search' );
                $receivers = array_merge($receivers, $this->CashBank->getReceiver('Vendor', $value, 'search' ));
                $receivers = array_merge($receivers, $this->CashBank->getReceiver('Employe', $value, 'search' ));
                $conditions['CashBank.receiver_id'] = $receivers;
                $this->request->data['CashBank']['receiver'] = $value;
            }

            if( !empty($refine['total']) ){
                $value = urldecode($refine['total']);
                $conditions['CashBank.debit_total LIKE'] = '%'.$value.'%';
                $this->request->data['CashBank']['total'] = $value;
            }

            if( !empty($refine['note']) ){
                $value = urldecode($refine['note']);
                $conditions['CashBank.description LIKE'] = '%'.$value.'%';
                $this->request->data['CashBank']['note'] = $value;
            }

            if( !empty($refine['document_type']) ){
                $value = urldecode($refine['document_type']);

                switch ($value) {
                    case 'outstanding':
                        $conditions['CashBank.prepayment_status <>'] = 'full_paid';
                        break;
                }
                $this->request->data['CashBank']['document_type'] = $value;
            }
        }

        $this->request->data['CashBank']['date'] = sprintf('%s - %s', $fromDate, $toDate);
        $conditions['DATE_FORMAT(CashBank.created, \'%Y-%m-%d\') >='] = $this->MkCommon->getDate($fromDate);
        $conditions['DATE_FORMAT(CashBank.created, \'%Y-%m-%d\') <='] = $this->MkCommon->getDate($toDate);

        $prepayments = $this->CashBank->getData('all', array(
            'conditions' => $conditions,
            'order' => array(
                'CashBank.created' => 'DESC',
                'CashBank.id' => 'DESC',
            ),
        ));

        if( !empty($prepayments) ) {
            $this->loadModel('Coa');

            foreach ($prepayments as $key => $prepayment) {
                $prepayments[$key] = $this->getPrepaymentMerge( $prepayment );
            }
        }

        $this->set('sub_module_title', __('Laporan Prepayment'));
        $this->set('active_menu', 'prepayment_report');
        $this->set('period_label', sprintf(__('Periode : %s s/d %s'), $fromDate, $toDate));

        $this->set(compact(
            'prepayments', 'data_action'
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

        if( !empty($this->request->data) ) {
            $data = $this->request->data;
            
            $value = $this->MkCommon->filterEmptyField($data, 'Journal');
            $closingMonth = $this->MkCommon->filterEmptyField($value, 'periode', 'month');
            $closingYear = $this->MkCommon->filterEmptyField($value, 'periode', 'year');
            $closingPeriod = sprintf('%s-%s', $closingYear, $closingMonth);

            $this->User->Journal->virtualFields['saldo_debit'] = 'SUM(Journal.debit)';
            $this->User->Journal->virtualFields['saldo_credit'] = 'SUM(Journal.credit)';
            
            $values = $this->User->Journal->getData('all', array(
                'conditions' => array(
                    'DATE_FORMAT(Journal.created, \'%Y-%m\')' => $closingPeriod,
                ),
                'group' => array(
                    'Journal.coa_id',
                ),
                'contain' => false,
            ));

            if( !empty($values) ) {
                $this->User->Journal->Coa->CoaHistory->updateAll(array(
                    'CoaHistory.status'=> 0,
                ), array(
                    'CoaHistory.status' => 1,
                    'CoaHistory.periode'=> $closingPeriod,
                ));

                foreach ($values as $key => $value) {
                    $coa_id = $this->MkCommon->filterEmptyField($value, 'Journal', 'coa_id');
                    $saldo_debit = $this->MkCommon->filterEmptyField($value, 'Journal', 'saldo_debit');
                    $saldo_credit = $this->MkCommon->filterEmptyField($value, 'Journal', 'saldo_credit');

                    $dataValue = array(
                        'CoaHistory' => array(
                            'branch_id' => Configure::read('__Site.config_branch_id'),
                            'user_id' => $this->user_id,
                            'coa_id' => $coa_id,
                            'periode' => $closingPeriod,
                            'saldo_debit' => $saldo_debit,
                            'saldo_credit' => $saldo_credit,
                        ),
                    );
                    $this->User->Journal->Coa->CoaHistory->create();
                    $this->User->Journal->Coa->CoaHistory->save($dataValue);
                }

                $this->MkCommon->redirectReferer(__('Berhasil melakukan closing COA'), 'success', array(
                    'action' => 'closing',
                    'admin' => false,
                ));
            }
        } else {
            $this->request->data['Journal']['periode']['month'] = $month;
            $this->request->data['Journal']['periode']['year'] = $year;
        }

        $this->MkCommon->_layout_file('progressbar');
        $this->set('active_menu', 'closing');
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

        $coas = $this->User->Journal->Coa->getData('list', array(
            'fields' => array(
                'Coa.id', 'Coa.coa_name'
            ),
        ), array(
            'status' => 'cash_bank_child',
        ));

        $this->set('active_menu', 'balances');
        $this->set(compact(
            'coas'
        ));
    }
}