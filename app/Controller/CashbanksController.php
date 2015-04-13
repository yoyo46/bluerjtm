<?php
App::uses('AppController', 'Controller');
class CashbanksController extends AppController {
	public $uses = array('CashBank', 'CashBankAuth');

    public $components = array(
        'RjCashBank'
    );

    function beforeFilter() {
        parent::beforeFilter();
        $this->set('title_for_layout', __('ERP RJTM | Kas Bank'));
        $this->set('module_title', __('Kas Bank'));
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
        // if( in_array('view_cash_bank', $this->allowModule) ) {
            $this->loadModel('CashBank');
            $this->set('sub_module_title', 'index');

            $default_conditions = array(
                'CashBank.status' => 1
            );

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
                $this->loadModel('Vendor');
                $this->loadModel('Employe');
                $this->loadModel('Customer');

                foreach ($cash_banks as $key => $value) {
                    $model = $value['CashBank']['receiver_type'];

                    switch ($model) {
                        case 'Vendor':
                            $list_result = $this->Vendor->getData('first', array(
                                'conditions' => array(
                                    'Vendor.status' => 1
                                )
                            ));
                            break;
                        case 'Employe':
                            $list_result = $this->Employe->getData('first', array(
                                'conditions' => array(
                                    'Employe.status' => 1
                                )
                            ));

                            break;
                        default:
                            $list_result = $this->Customer->getData('first', array(
                                'conditions' => array(
                                    'Customer.status' => 1
                                )
                            ));

                            break;
                    }

                    if(!empty($list_result)){
                        $cash_banks[$key]['name_cash'] = $list_result[$model]['name'];
                    }
                }
            }

            $this->set('cash_banks', $cash_banks);
            $this->set('active_menu', 'cash_bank');
        // } else {
        //     $this->redirect($this->referer());
        // }
    }

    public function cashbank_add() {
        // if( in_array('insert_cash_banks', $this->allowModule) ) {
            $this->set('sub_module_title', 'Tambah Kas Bank');
            $this->doCashBank();
        // } else {
        //     $this->redirect($this->referer());
        // }
    }

    public function cashbank_edit( $id = false ) {
        // if( in_array('update_cash_banks', $this->allowModule) ) {
            $this->set('sub_module_title', 'Rubah Kas Bank');
            $coa = false;

            if( !empty($id) ) {
                $cashbank = $this->CashBank->getData('first', array(
                    'conditions' => array(
                        'CashBank.id' => $id,
                        'CashBank.status' => 1,
                    ),
                    'contain' => array(
                        'CashBankDetail',
                        'CashBankAuth'
                    )
                ));

                if( !empty($cashbank) ) {
                    $this->set('sub_module_title', 'Rubah Kas Bank');
                    $this->doCashBank( $id, $cashbank);
                } else {
                    $this->MkCommon->setCustomFlash(__('Kas Bank tidak ditemukan.'), 'error');
                    $this->redirect($this->referer());
                }
            } 
        // } else {
        //     $this->redirect($this->referer());
        // }
    }

    function doCashBank($id = false, $data_local = false){
        $this->loadModel('Coa');
        $this->loadModel('User');

        if(!empty($this->request->data)){
            $data = $this->request->data;

            if($id && $data_local){
                $this->CashBank->id = $id;
                $msg = 'merubah';
            }else{
                $this->CashBank->create();
                $msg = 'menambah';
            }

            $data['CashBank']['tgl_cash_bank'] = $this->MkCommon->getDate($data['CashBank']['tgl_cash_bank']);
            
            $coas_validate = true;
            if(!empty($data['CashBankDetail']['coa_id'])){
                $arr_list = array();
                $debit_total = 0;
                $credit_total = 0;
                foreach ($data['CashBankDetail']['coa_id'] as $key => $coa_id) {
                    $debit = !empty($data['CashBankDetail']['debit'][$key]) ? str_replace(',', '', $data['CashBankDetail']['debit'][$key]) : 0;
                    $credit = !empty($data['CashBankDetail']['credit'][$key]) ? str_replace(',', '', $data['CashBankDetail']['credit'][$key]) : 0;

                    $debit_total += $debit;
                    $credit_total += $credit;

                    $arr_list[] = array(
                        'coa_id' => $coa_id,
                        'debit' => $debit,
                        'credit' => $credit
                    );
                }
                $data['CashBankDetail'] = $arr_list;
                $data['CashBank']['debit_total'] = $debit_total;
                $data['CashBank']['credit_total'] = $credit_total;
            }else{
                $coas_validate = false;
            }

            $this->CashBank->set($data);

            if($this->CashBank->validates($data) && $coas_validate){
                if($this->CashBank->save($data)){

                    $cash_bank_id = $this->CashBank->id;
                    if($id && $data_local){
                        $this->CashBank->CashBankDetail->deleteAll(array(
                            'CashBankDetail.cash_bank_id' => $cash_bank_id
                        ));

                        $this->CashBank->CashBankAuth->deleteAll(array(
                            'CashBankAuth.cash_bank_id' => $cash_bank_id
                        ));
                    }

                    if(!empty($data['CashBankDetail'])){
                        foreach ($data['CashBankDetail'] as $key => $value) {
                            $value['cash_bank_id'] = $cash_bank_id;
                            $this->CashBank->CashBankDetail->create();
                            $this->CashBank->CashBankDetail->set($value);
                            $this->CashBank->CashBankDetail->save();
                        }
                    }

                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Kas Bank'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s Kas Bank'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );   
                    $this->redirect(array(
                        'controller' => 'cashbanks',
                        'action' => 'index'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Kas Bank'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s Kas Bank'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );   
                }
            }else{
                $text = sprintf(__('Gagal %s Kas Bank'), $msg);
                if($coas_validate){
                    $text .= __(', COA Kas Bank harap di pilih');
                }
                $this->MkCommon->setCustomFlash($text, 'error');
            }
        }else{
            if($id && $data_local){
                $this->request->data = $data = $data_local;
                $this->request->data['CashBank']['tgl_cash_bank'] = $this->MkCommon->getDate($this->request->data['CashBank']['tgl_cash_bank']);
            }
        }

        if(!empty($this->request->data['CashBank']['receiver_type'])){
            $model = $this->request->data['CashBank']['receiver_type'];
            $this->loadModel($model);

            switch ($model) {
                case 'Vendor':
                    $list_result = $this->Vendor->getData('first', array(
                        'conditions' => array(
                            'Vendor.status' => 1
                        )
                    ));
                    break;
                case 'Employe':
                    $list_result = $this->Employe->getData('first', array(
                        'conditions' => array(
                            'Employe.status' => 1
                        )
                    ));

                    break;
                default:
                    $list_result = $this->Customer->getData('first', array(
                        'conditions' => array(
                            'Customer.status' => 1
                        )
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

        $coas = $this->Coa->getData('list', array(
            'conditions' => array(
                // 'Coa.level' => 4,
                'Coa.is_cash_bank' => 1,
                'Coa.status' => 1
            )
        ));
        $this->set(compact('coas'));

        $this->set('active_menu', 'cash_bank');
        $this->set('module_title', 'Kas Bank');
        $this->render('cashbank_form');
    }

    function cashbank_delete($id){
        // if( in_array('delete_cash_bank', $this->allowModule) ) {
            $locale = $this->CashBank->getData('first', array(
                'conditions' => array(
                    'CashBank.id' => $id
                )
            ));

            if($locale){
                $value = true;
                if($locale['CashBank']['status']){
                    $value = false;
                }

                $this->CashBank->id = $id;
                $this->CashBank->set('status', $value);
                if($this->CashBank->save()){
                    $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses merubah status Kas Bank ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal merubah status Kas Bank ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                }
            }else{
                $this->MkCommon->setCustomFlash(__('Kas Bank tidak ditemukan.'), 'error');
            }

            $this->redirect($this->referer());
        // } else {
        //     $this->redirect($this->referer());
        // }
    }

    function detail($id = false){
        $this->set('sub_module_title', 'Detail Kas Bank');
        $coa = false;

        if( !empty($id) ) {
            $this->loadModel('CashBankAuthMaster');
            $cash_bank_auth_master = $this->CashBankAuthMaster->find('all', array(
                'contain' => array(
                    'User' => array(
                        'Group'
                    )
                )
            ));
            if(!empty($cash_bank_auth_master)){
                foreach ($cash_bank_auth_master as $key => $value) {
                    $cash_bank_auth = $this->CashBank->CashBankAuth->getData('first', array(
                        'conditions' => array(
                            'CashBankAuth.cash_bank_auth_master_id' => $value['CashBankAuthMaster']['id']
                        )
                    ));

                    if(!empty($cash_bank_auth)){
                        $cash_bank_auth_master[$key] = array_merge($value, $cash_bank_auth);
                    }
                }
            }
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
                    'CashBank.status' => 1,
                ),
                'contain' => array(
                    'CashBankDetail' => array(
                        'Coa'
                    ),
                    'CashBankAuth' => array(
                        'conditions' => array(
                            'CashBankAuth.cash_bank_auth_master_id' => !empty($value['CashBankAuthMaster']['id']) ? $value['CashBankAuthMaster']['id'] : ''
                        )
                    )
                )
            ));
            
            if( !empty($cashbank) ) {
                $cashbank_auth_id = Set::extract('/CashBankAuthMaster/employe_id', $cash_bank_auth_master);

                if(!empty($this->request->data) && !empty($cash_bank_master_user)){
                    if(in_array($this->user_id, $cashbank_auth_id)){
                        $data = $this->request->data;

                        $data['CashBankAuth']['cash_bank_id'] = $id;

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
                            if($cash_bank_master_user['CashBankAuthMaster']['level'] == 1){
                                $data_arr = array();
                                switch ($data['CashBankAuth']['status_document']) {
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
                                $first_lvl = $this->CashBank->CashBankAuth->getData('first', array(
                                    'conditions' => array(
                                        'CashBankAuth.cash_bank_id' => $id,
                                        'CashBankAuth.level' => 1,
                                        'CashBankAuth.has_vote' => 1
                                    )
                                ));

                                if( empty($first_lvl) ){
                                    if($data['CashBankAuth']['status_document'] == 'revise'){
                                        $data_arr = array(
                                            'is_revised' => 1,
                                        );
                                    }
                                }
                            }

                            $this->CashBank->id = $id;
                            $this->CashBank->set($data_arr);
                            $this->CashBank->save();

                            $this->MkCommon->setCustomFlash('Berhasil melakukan Approval Kas Bank.', 'success');
                        }else{
                            $this->MkCommon->setCustomFlash('Gagal melakukan Approval Kas Bank.', 'error');
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
                    $this->loadModel($model);

                    switch ($model) {
                        case 'Vendor':
                            $list_result = $this->Vendor->getData('first', array(
                                'conditions' => array(
                                    'Vendor.status' => 1
                                )
                            ));
                            break;
                        case 'Employe':
                            $list_result = $this->Employe->getData('first', array(
                                'conditions' => array(
                                    'Employe.status' => 1
                                )
                            ));

                            break;
                        default:
                            $list_result = $this->Customer->getData('first', array(
                                'conditions' => array(
                                    'Customer.status' => 1
                                )
                            ));

                            break;
                    }

                    if(!empty($list_result)){
                        $cashbank['CashBank']['receiver'] = $list_result[$model]['name'];
                    }
                }
                // debug($cashbank);die();
                $this->set('cashbank', $cashbank);                
                $this->set('cashbank_auth_id', $cashbank_auth_id);
            } else {
                $this->MkCommon->setCustomFlash(__('Kas Bank tidak ditemukan.'), 'error');
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
                        if(!empty($value['id'])){
                            $this->CashBankAuthMaster->id = $value['id'];
                        }else{
                            $this->CashBankAuthMaster->create();
                        }

                        $this->CashBankAuthMaster->set($value);
                        $this->CashBankAuthMaster->save();
                    }

                    $this->MkCommon->setCustomFlash('Sukses melakukan setting approval.', 'success');
                    $this->redirect(array(
                        'action' => 'approval_setting'
                    ));
                }
            }else{
                $this->MkCommon->setCustomFlash('Harap masukkan karyawan yang akan di jadikan approval Kas Bank.', 'error');
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
}