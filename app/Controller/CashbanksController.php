<?php
App::uses('AppController', 'Controller');
class CashbanksController extends AppController {
	public $uses = array('CashBank', 'CashBankAuth');

    public $components = array(
        'RjCashBank'
    );

    function beforeFilter() {
        parent::beforeFilter();
        $this->set('title_for_layout', __('ERP RJTM | Kas/Bank'));
        $this->set('module_title', __('Kas/Bank'));
    }

    function search( $index = 'index', $param_get = false ){
        $refine = array();

        if(!empty($this->request->data)) {
            $refine = $this->RjCashBank->processRefine($this->request->data);
            $params = $this->RjCashBank->generateSearchURL($refine);
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

        $default_conditions = array();

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['nodoc'])){
                $nodoc = urldecode($refine['nodoc']);
                $this->request->data['CashBank']['nodoc'] = $nodoc;
                $default_conditions['CashBank.nodoc LIKE '] = '%'.$nodoc.'%';
            }
            if(!empty($refine['cash'])){
                $cash = urldecode($refine['cash']);
                $this->request->data['CashBank']['receiving_cash_type'] = $cash;
                $default_conditions['CashBank.receiving_cash_type'] = $cash;
            }
            if(!empty($refine['from'])){
                $from = date('Y-m-d', urldecode($refine['from']));
                $this->request->data['CashBank']['date_from'] = date('d/m/Y', urldecode($refine['from']));
                $default_conditions['DATE_FORMAT(CashBank.tgl_cash_bank, \'%Y-%m-%d\') >='] = $from;
            }
            if(!empty($refine['to'])){
                $to = date('Y-m-d', urldecode($refine['to']));
                $this->request->data['CashBank']['date_to'] = date('d/m/Y', urldecode($refine['to']));
                $default_conditions['DATE_FORMAT(CashBank.tgl_cash_bank, \'%Y-%m-%d\') <='] = $to;
            }
        }
        
        $this->paginate = $this->CashBank->getData('paginate', array(
            'conditions' => $default_conditions,
            'order' => array(
                'CashBank.created' => 'DESC'
            )
        ));

        $cash_banks = $this->paginate('CashBank');

        if(!empty($cash_banks)){
            foreach ($cash_banks as $key => $value) {
                $model = $value['CashBank']['receiver_type'];
                $receiver_id = $value['CashBank']['receiver_id'];
                $this->loadModel($model);

                switch ($model) {
                    case 'Vendor':
                        $list_result = $this->Vendor->getData('first', array(
                            'conditions' => array(
                                'Vendor.id' => $receiver_id,
                            ),
                        ));
                        break;
                    case 'Employe':
                        $list_result = $this->Employe->getData('first', array(
                            'conditions' => array(
                                'Employe.id' => $receiver_id,
                            )
                        ));

                        break;
                    default:
                        $list_result = $this->Customer->getData('first', array(
                            'conditions' => array(
                                'Customer.id' => $receiver_id,
                            )
                        ));

                        break;
                }

                if(!empty($list_result)){
                    $cash_banks[$key]['name_cash'] = $list_result[$model]['name'];
                }
            }
        }
        $this->loadModel('CashBankAuthMaster');
        $cash_bank_auth_master = $this->CashBankAuthMaster->getUserApproval();
        $cashbank_auth_id = Set::extract('/CashBankAuthMaster/employe_id', $cash_bank_auth_master);

        $this->set('cashbank_auth_id', $cashbank_auth_id);
        $this->set('cash_banks', $cash_banks);
        $this->set('active_menu', 'cash_bank');
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
        $coa = false;

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
        $this->loadModel('Coa');
        $this->loadModel('User');
        $prepayment_out_id = !empty($data_local['CashBank']['document_id'])?$data_local['CashBank']['document_id']:false;

        if(!empty($this->request->data)){
            $data = $this->request->data;
            $data['CashBank']['tgl_cash_bank'] = $this->MkCommon->getDate($data['CashBank']['tgl_cash_bank']);
            $document_id = !empty($data['CashBank']['document_id'])?$data['CashBank']['document_id']:false;
            $document_type = !empty($data['CashBank']['document_type'])?$data['CashBank']['document_type']:false;
            $coas_validate = true;
            $totalTagihanCredit = 0;
            $totalTagihanDebit = 0;
            $debit_total = 0;
            $credit_total = 0;
            $total_coa = 0;
            $prepayment_status = false;
            $document_no = !empty($data['CashBank']['nodoc'])?$data['CashBank']['nodoc']:false;
            $document_coa_id = !empty($data['CashBank']['coa_id'])?$data['CashBank']['coa_id']:false;
            $data['CashBank']['is_revised'] = 0;
            $data['CashBank']['group_branch_id'] = Configure::read('__Site.config_branch_id');

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

            if( !empty($document_id) && $data['CashBank']['document_type'] == 'prepayment' ) {
                $cashBankTagihan = $this->CashBank->getData('first', array(
                    'conditions' => array(
                        'CashBank.id' => $document_id,
                    ),
                ));
                $totalCashBank = $debit_total + $credit_total;
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
                        }
                    }

                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Kas/Bank'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s Kas/Bank #%s'), $msg, $this->CashBank->id), $this->user_data, $this->RequestHandler, $this->params );
                    $this->redirect(array(
                        'controller' => 'cashbanks',
                        'action' => 'index'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Kas/Bank'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s Kas/Bank #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1 );   
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
            $this->loadModel($model);

            switch ($model) {
                case 'Vendor':
                    $list_result = $this->Vendor->getData('first', array(
                        'conditions' => array(
                            'Vendor.id' => $receiver_id,
                        ),
                    ));
                    break;
                case 'Employe':
                    $list_result = $this->Employe->getData('first', array(
                        'conditions' => array(
                            'Employe.id' => $receiver_id,
                        ),
                    ));

                    break;
                default:
                    $list_result = $this->Customer->getData('first', array(
                        'conditions' => array(
                            'Customer.id' => $receiver_id,
                        ),
                    ));

                    break;
            }

            if(!empty($list_result)){
                $this->request->data['CashBank']['receiver'] = $list_result[$model]['name'];
            }
        }

        if(!empty($data['CashBankDetail'])){
            $this->loadModel('Coa');
            foreach ($data['CashBankDetail'] as $key => $value) {
                $curr_coa = $this->Coa->getData('first', array(
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

        $receiving_cash_type = !empty($this->request->data['CashBank']['receiving_cash_type'])?$this->request->data['CashBank']['receiving_cash_type']:false;

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

        $coas = $this->Coa->getData('list', array(
            'conditions' => array(
                'Coa.level' => 4,
                'Coa.is_cash_bank' => 1,
                'Coa.status' => 1
            ),
            'fields' => array(
                'Coa.id', 'Coa.coa_name'
            ),
        ));
        $this->set(compact(
            'coas', 'document_id', 'receiving_cash_type',
            'docs', 'urlBrowseDocument', 'prepayment_out_id',
            'id', 'data_local'
        ));

        $this->loadModel('City');
        $branches = $this->City->branchCities();
        $this->set('branches', $branches);
        
        $this->set('active_menu', 'cash_bank');
        $this->set('module_title', 'Kas/Bank');

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
            $this->CashBank->set('status', $value);
            if($this->CashBank->save()){
                $this->loadModel('Journal');
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
                $this->Log->logActivity( sprintf(__('Sukses merubah status Kas/Bank ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params );
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal merubah status Kas/Bank ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Kas/Bank tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    function detail($id = false){
        $this->set('sub_module_title', 'Detail Kas/Bank');
        $coa = false;

        if( !empty($id) ) {
            $this->loadModel('CashBankAuthMaster');
            $cash_bank_auth_master = $this->CashBankAuthMaster->getUserApproval($id);
            $this->set('cash_bank_auth_master', $cash_bank_auth_master);

            $cash_bank_master_user = $this->CashBankAuthMaster->find('first', array(
                'conditions' => array(
                    'CashBankAuthMaster.employe_id' => $this->user_id
                )
            ));
            $this->set('cash_bank_master_user', $cash_bank_master_user);

            $cashbank = $this->CashBank->getData('first', array(
                'conditions' => array(
                    'CashBank.id' => $id,
                ),
                'contain' => array(
                    'CashBankDetail' => array(
                        'Coa'
                    ),
                    'CashBankAuth' => array(
                        'conditions' => array(
                            'CashBankAuth.cash_bank_auth_master_id' => !empty($cash_bank_master_user['CashBankAuthMaster']['id']) ? $cash_bank_master_user['CashBankAuthMaster']['id'] : ''
                        )
                    )
                )
            ));

            if( !empty($cashbank) ) {
                $cashbank_auth_id = Set::extract('/CashBankAuthMaster/employe_id', $cash_bank_auth_master);
                $document_type = !empty($cashbank['CashBank']['document_type'])?$cashbank['CashBank']['document_type']:false;
                $document_id = !empty($cashbank['CashBank']['document_id'])?$cashbank['CashBank']['document_id']:false;
                $debit_total = !empty($cashbank['CashBank']['debit_total'])?$cashbank['CashBank']['debit_total']:0;
                $credit_total = !empty($cashbank['CashBank']['credit_total'])?$cashbank['CashBank']['credit_total']:0;
                $receiving_cash_type = !empty($cashbank['CashBank']['receiving_cash_type'])?$cashbank['CashBank']['receiving_cash_type']:false;
                $nodoc = !empty($cashbank['CashBank']['nodoc'])?$cashbank['CashBank']['nodoc']:false;
                $document_coa_id = !empty($cashbank['CashBank']['coa_id'])?$cashbank['CashBank']['coa_id']:false;

                if(!empty($this->request->data) && !empty($cash_bank_master_user)){
                    if( in_array($this->user_id, $cashbank_auth_id) ){
                        $data = $this->request->data;
                        $data['CashBankAuth']['cash_bank_id'] = $id;

                        if( !empty($data['CashBankAuth']['status_document']) ) {
                            $auth = $this->CashBank->CashBankAuth->getData('first', array(
                                'conditions' => array(
                                    'CashBankAuth.cash_bank_auth_master_id' => $cash_bank_master_user['CashBankAuthMaster']['id']
                                ),
                            ));

                            if(empty($auth)){
                                $this->CashBank->CashBankAuth->create();
                            }else{
                                $this->CashBank->CashBankAuth->id = $auth['CashBankAuth']['id'];
                            }

                            $this->CashBank->CashBankAuth->set($data);

                            if($this->CashBank->CashBankAuth->save()){
                                $status_document = !empty($data['CashBankAuth']['status_document'])?$data['CashBankAuth']['status_document']:false;

                                if($cash_bank_master_user['CashBankAuthMaster']['level'] == 1){
                                    $data_arr = array();

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
                                }else{
                                    $cashBankmaster = $this->CashBank->CashBankAuth->getData('first', array(
                                        'conditions' => array(
                                            'CashBankAuth.cash_bank_id' => $id
                                        ),
                                        'contain' => array(
                                            'CashBankAuthMaster' => array(
                                                'conditions' => array(
                                                    'CashBankAuthMaster.level' => 4
                                                )
                                            )
                                        )
                                    ));
                                    
                                    if(empty($cashBankmaster['CashBankAuthMaster']['id'])){
                                        if($status_document == 'revise'){
                                            $data_arr = array(
                                                'is_revised' => 1,
                                            );
                                        }
                                    }
                                }

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
                                        $this->loadModel('Journal');

                                        if( !empty($cashbank['CashBankDetail']) ) {
                                            foreach ($cashbank['CashBankDetail'] as $key => $cashBankDetail) {
                                                $coa_id = !empty($cashBankDetail['coa_id'])?$cashBankDetail['coa_id']:false;
                                                $total = !empty($cashBankDetail['total'])?$cashBankDetail['total']:false;

                                                if( in_array($receiving_cash_type, array( 'out', 'ppn_out', 'prepayment_out' )) ) {
                                                    $this->Journal->setJournal( $id, $nodoc, $coa_id, 0, $total, $receiving_cash_type );
                                                } else {
                                                    $this->Journal->setJournal( $id, $nodoc, $coa_id, $total, 0, $receiving_cash_type );
                                                }
                                            }
                                        }

                                        if( in_array($receiving_cash_type, array( 'out', 'ppn_out', 'prepayment_out' )) ) {
                                            $this->Journal->setJournal( $id, $nodoc, $document_coa_id, $debit_total, 0, $receiving_cash_type );
                                        } else {
                                            $this->Journal->setJournal( $id, $nodoc, $document_coa_id, 0, $credit_total, $receiving_cash_type );
                                        }
                                    }
                                }

                                $this->MkCommon->setCustomFlash('Berhasil melakukan Approval Kas/Bank.', 'success');
                                $this->Log->logActivity( sprintf(__('Berhasil melakukan %s Kas/Bank #%s'), $status_document, $id), $this->user_data, $this->RequestHandler, $this->params );
                            }else{
                                $this->MkCommon->setCustomFlash('Gagal melakukan Approval Kas/Bank.', 'error');
                                $this->Log->logActivity( sprintf(__('Berhasil melakukan %s Kas/Bank #%s'), $status_document, $id), $this->user_data, $this->RequestHandler, $this->params, 1 );
                            }
                        }else{
                            $this->MkCommon->setCustomFlash('Silahkan pilih Status Approval', 'error');
                        }
                    }else{
                        $this->MkCommon->setCustomFlash('Anda tidak mempunyai hak untuk mengakses kontent tersebut.', 'error');
                    }

                    $this->redirect($this->referer());
                }else{
                    if(!empty($cashbank['CashBankAuth'][0])){
                        $this->request->data['CashBankAuth'] = $cashbank['CashBankAuth'][0];
                    }
                }

                if(!empty($cashbank['CashBank']['receiver_type'])){
                    $model = $cashbank['CashBank']['receiver_type'];
                    $receiver_id = !empty($cashbank['CashBank']['receiver_id'])?$cashbank['CashBank']['receiver_id']:false;
                    $this->loadModel($model);

                    switch ($model) {
                        case 'Vendor':
                            $list_result = $this->Vendor->getData('first', array(
                                'conditions' => array(
                                    'Vendor.id' => $receiver_id,
                                )
                            ));
                            break;
                        case 'Employe':
                            $list_result = $this->Employe->getData('first', array(
                                'conditions' => array(
                                    'Employe.id' => $receiver_id,
                                )
                            ));

                            break;
                        default:
                            $list_result = $this->Customer->getData('first', array(
                                'conditions' => array(
                                    'Customer.id' => $receiver_id,
                                )
                            ));

                            break;
                    }

                    if(!empty($list_result)){
                        $cashbank['CashBank']['receiver'] = $list_result[$model]['name'];
                    }
                }

                $cashBankAuth = $this->CashBankAuthMaster->CashBankAuth->find('all', array(
                    'conditions' => array(
                        'CashBankAuth.cash_bank_id' => $id,
                        'CashBankAuthMaster.employe_id' => $this->user_id,
                    ),
                    'contain' => array(
                        'CashBankAuthMaster'
                    ),
                ));

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
                $this->set('cashBankAuth', $cashBankAuth);                
                $this->set('cashbank', $cashbank);                
                $this->set('cashbank_auth_id', $cashbank_auth_id);
            } else {
                $this->MkCommon->setCustomFlash(__('Kas/Bank tidak ditemukan.'), 'error');
                $this->redirect($this->referer());
            }
        } 
    }

    function approval_setting(){
        $this->loadModel('User');
        $this->loadModel('CashBankAuthMaster');
        $cash_bank_auth_master = $this->CashBankAuthMaster->find('all');

        if(!empty($this->request->data)){
            $data = $this->request->data;

            $validate_auth = true;
            $user_collect = array();
            if(!empty($data['CashBankAuthMaster']['employe_id'])){
                $arr_list_auth = array();
                foreach ($data['CashBankAuthMaster']['employe_id'] as $key => $value) {
                    if(!in_array($value, $user_collect) && !empty($value)){
                        $id = !empty($data['CashBankAuthMaster']['id'][$key]) ? $data['CashBankAuthMaster']['id'][$key] : '';
                        $arr_list_auth[] = array(
                            'employe_id' => $value,
                            'level' => $key+1,
                            'id' => $id
                        );

                        array_push($user_collect, $value);
                    }
                }
                $data['CashBankAuthMaster'] = $arr_list_auth;
            }else{
                $validate_auth = false;
            }

            if($validate_auth && !empty($user_collect)){
                if(!empty($data['CashBankAuthMaster'])){
                    foreach ($data['CashBankAuthMaster'] as $key => $value) {
                        $id_cash = !empty($value['id'])?$value['id']:false;

                        if(!empty($id_cash)){
                            $this->CashBankAuthMaster->id = $id_cash;
                        }else{
                            $this->CashBankAuthMaster->create();
                        }

                        $this->CashBankAuthMaster->set($value);

                        if( $this->CashBankAuthMaster->save() ) {
                            $this->Log->logActivity( sprintf(__('Sukses melakukan setting approval #%s'), $this->CashBankAuthMaster->id), $this->user_data, $this->RequestHandler, $this->params );
                        } else {
                            $this->Log->logActivity( sprintf(__('Gagal melakukan setting approval #%s'), $id_cash), $this->user_data, $this->RequestHandler, $this->params, 1 );
                        }
                    }

                    $this->MkCommon->setCustomFlash('Sukses melakukan setting approval.', 'success');
                    $this->redirect(array(
                        'action' => 'approval_setting'
                    ));
                }
            }else{
                $this->MkCommon->setCustomFlash('Harap masukkan karyawan yang akan di jadikan approval Kas/Bank.', 'error');
            }
        }else{
            $data = array();
            if(!empty($cash_bank_auth_master)){
                foreach ($cash_bank_auth_master as $key => $value) {
                    $data['CashBankAuthMaster'][] = $value['CashBankAuthMaster'];
                }
            }
        }

        if(!empty($data['CashBankAuthMaster'])){
            $auth_data = array();
            foreach ($data['CashBankAuthMaster'] as $key => $value) {
                $group = $this->User->find('first', array(
                    'conditions' => array(
                        'User.id' => $value['employe_id']
                    ),
                    'contain' => array(
                        'Group'
                    )
                ));

                if(!empty($group['Group']['name'])){
                    $group = $group['Group']['name'];
                }
                $auth_data['CashBankAuthMaster'][] = array_merge($value, array(
                    'group' => $group
                ));
            }

            $this->set('auth_data', $auth_data);
        }

        $employes = $this->User->getData('list', array(
            'conditions' => array(
                'User.status' => 1,
            ),
            'fields' => array(
                'User.id', 'User.full_name'
            )
        ));

        $this->set('active_menu', 'approval_setting');
        $this->set(compact('employes', 'cash_bank_auth_master'));
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

                    if( $this->CashBankSetting->save() ) {
                        $this->Log->logActivity( sprintf(__('Berhasil mengubah setting #%s'), $this->CashBankSetting->id), $this->user_data, $this->RequestHandler, $this->params );
                    } else {
                        $this->Log->logActivity( sprintf(__('Gagal mengubah setting #%s'), $value), $this->user_data, $this->RequestHandler, $this->params, 1 );
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
        $this->loadModel('Coa');
        $this->loadModel('CoaSetting');
        $coaSetting = $this->CoaSetting->getData('first', array(
            'conditions' => array(
                'CoaSetting.status' => 1
            ),
        ));

        if(!empty($this->request->data)){
            $data = $this->request->data;
            
            if( !empty($coaSetting['CoaSetting']['id']) ){
                $this->CoaSetting->id = $coaSetting['CoaSetting']['id'];
            }else{
                $this->CoaSetting->create();
            }

            $this->CoaSetting->set($data);

            if($this->CoaSetting->validates($data)){
                if($this->CoaSetting->save($data)){
                    $this->MkCommon->setCustomFlash(__('Sukses menyimpan pengaturan COA'), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses menyimpan pengaturan COA #%s'), $this->CoaSetting->id), $this->user_data, $this->RequestHandler, $this->params );
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

        $coas = $this->Coa->getData('list', array(
            'conditions' => array(
                'Coa.level' => 4,
                'Coa.status' => 1
            ),
            'fields' => array(
                'Coa.id', 'Coa.coa_name'
            ),
        ));
        $this->set('active_menu', 'coa_setting');
        $this->set(compact('coas'));
    }

    public function journal_report( $data_action = false ) {
        $this->loadModel('Journal');
        $this->set('sub_module_title', 'Laporan Jurnal');
        $this->paginate = $this->Journal->getData('paginate');
        $journals = $this->paginate('Journal');
        $this->set('active_menu', 'journal_report');

        $this->set(compact(
            'journals', 'data_action'
        ));
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
}