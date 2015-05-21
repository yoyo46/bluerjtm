<?php
App::uses('AppController', 'Controller');
class LkusController extends AppController {
	public $uses = array();

    public $components = array(
        'RjLku'
    );

    function beforeFilter() {
        parent::beforeFilter();
        $this->set('title_for_layout', __('ERP RJTM | Data LKU/KSU'));
        $this->set('module_title', __('LKU/KSU'));
    }

    function search( $index = 'index' ){
        $refine = array();
        if(!empty($this->request->data)) {
            $refine = $this->RjLku->processRefine($this->request->data);
            $params = $this->RjLku->generateSearchURL($refine);
            $params['action'] = $index;

            $this->redirect($params);
        }
        $this->redirect('/');
    }

	public function index() {
        if( in_array('view_lkus', $this->allowModule) ) {
            $this->loadModel('Lku');
    		$this->set('active_menu', 'lkus');
    		$this->set('sub_module_title', __('Data LKU'));
            $conditions = array(
                'Lku.status' => array( 0, 1 ),
            );
            
            if(!empty($this->params['named'])){
                $refine = $this->params['named'];

                if(!empty($refine['nodoc'])){
                    $no_doc = urldecode($refine['nodoc']);
                    $this->request->data['Lku']['no_doc'] = $no_doc;
                    $conditions['Lku.no_doc LIKE '] = '%'.$no_doc.'%';
                }
            }

            $this->paginate = $this->Lku->getData('paginate', array(
                'conditions' => $conditions
            ));
            $Lkus = $this->paginate('Lku');

            $this->set('Lkus', $Lkus);
        } else {
            $this->redirect($this->referer());
        }
	}

    function detail($id = false){
        if( in_array('view_lkus', $this->allowModule) ) {
            if(!empty($id)){
                $this->loadModel('Lku');
                $Lku = $this->Lku->getLku($id);
                
                if(!empty($Lku)){
                    if(!empty($Lku['LkuDetail'])){
                        $this->loadModel('PartsMotor');
                        $this->loadModel('TipeMotor');

                        foreach ($Lku['LkuDetail'] as $key => $value) {
                            $tipe_motor = $this->TipeMotor->getData('first', array(
                                'conditions' => array(
                                    'TipeMotor.id' => $value['tipe_motor_id']
                                ),
                                'contain' => array(
                                    'GroupMotor'
                                )
                            ));

                            if(!empty($tipe_motor)){
                                $part_motor = $this->PartsMotor->getData('first', array(
                                    'conditions' => array(
                                        'PartsMotor.id' => $value['part_motor_id'],
                                    )
                                ), false);
                                $Lku['LkuDetail'][$key]['TipeMotor'] = $tipe_motor['TipeMotor'];
                                $Lku['LkuDetail'][$key]['GroupMotor'] = !empty($tipe_motor['GroupMotor']) ? $tipe_motor['GroupMotor'] : false;
                                $Lku['LkuDetail'][$key]['PartsMotor'] = !empty($part_motor['PartsMotor'])?$part_motor['PartsMotor']:false;
                            }
                        }
                    }

                    $sub_module_title = __('Detail LKU');
                    $this->set(compact('Lku', 'sub_module_title'));
                    $this->set('active_menu', 'lkus');
                }else{
                    $this->MkCommon->setCustomFlash(__('LKU tidak ditemukan.'), 'error');
                    $this->redirect($this->referer());
                }
            }else{
                $this->MkCommon->setCustomFlash(__('LKU tidak ditemukan.'), 'error');
                $this->redirect($this->referer());
            }
        } else {
            $this->redirect($this->referer());
        }
    }

    function add(){
        if( in_array('insert_lkus', $this->allowModule) ) {
            $this->set('sub_module_title', __('Tambah LKU'));
            $this->DoLku();
        } else {
            $this->redirect($this->referer());
        }
    }

    function edit($id){
        if( in_array('update_lkus', $this->allowModule) ) {
            $this->loadModel('Lku');
            $this->set('sub_module_title', 'Rubah LKU');
            $Lku = $this->Lku->getData('first', array(
                'conditions' => array(
                    'Lku.id' => $id
                ),
            ));

            if(!empty($Lku)){
                $this->DoLku($id, $Lku);
            }else{
                $this->MkCommon->setCustomFlash(__('LKU tidak ditemukan'), 'error');  
                $this->redirect(array(
                    'controller' => 'Lkus',
                    'action' => 'index'
                ));
            }
        } else {
            $this->redirect($this->referer());
        }
    }

    function DoLku($id = false, $data_local = false){
        $this->loadModel('Ttuj');
        $this->loadModel('TipeMotor');
        $this->loadModel('PartsMotor');

        if(!empty($this->request->data)){
            $data = $this->request->data;
            
            if($id && $data_local){
                $this->Lku->id = $id;
                $msg = 'merubah';
            }else{
                $this->loadModel('Lku');
                $this->Lku->create();
                $msg = 'menambah';
            }
            
            $data['Lku']['tgl_lku'] = (!empty($data['Lku']['tgl_lku'])) ? $this->MkCommon->getDate($data['Lku']['tgl_lku']) : '';
            
            $validate_lku_detail = true;
            $temp_detail = array();
            $total_price = 0;
            $total_klaim = 0;
            if(!empty($data['LkuDetail']['tipe_motor_id'])){
                foreach ($data['LkuDetail']['tipe_motor_id'] as $key => $value) {
                    if( !empty($value) ){
                        $data_detail['LkuDetail'] = array(
                            'tipe_motor_id' => $value,
                            'no_rangka' => (!empty($data['LkuDetail']['no_rangka'][$key])) ? $data['LkuDetail']['no_rangka'][$key] : '',
                            'qty' => (!empty($data['LkuDetail']['qty'][$key])) ? $data['LkuDetail']['qty'][$key] : '',
                            'price' => (!empty($data['LkuDetail']['price'][$key])) ? str_replace(',', '', trim($data['LkuDetail']['price'][$key])) : '',
                            'part_motor_id' => (!empty($data['LkuDetail']['part_motor_id'][$key])) ? $data['LkuDetail']['part_motor_id'][$key] : '',
                            'note' => (!empty($data['LkuDetail']['note'][$key])) ? $data['LkuDetail']['note'][$key] : '',
                            'no_rangka' => (!empty($data['LkuDetail']['no_rangka'][$key])) ? $data['LkuDetail']['no_rangka'][$key] : '',
                        );
                        
                        $temp_detail[] = $data_detail;
                        $this->Lku->LkuDetail->set($data_detail);
                        if( !$this->Lku->LkuDetail->validates() ){
                            $validate_lku_detail = false;
                            break;
                        }else{
                            $total_price += $data_detail['LkuDetail']['qty'] * $data_detail['LkuDetail']['price'];
                            $total_klaim += $data_detail['LkuDetail']['qty'];
                        }
                    }
                }
            }else{
                $validate_lku_detail = false;
            }
            
            $data['Lku']['total_price'] = $total_price;
            $data['Lku']['total_klaim'] = $total_klaim;
            $this->Lku->set($data);

            if($this->Lku->validates($data) && $validate_lku_detail){
                if($this->Lku->save($data)){
                    // $this->loadModel('Journal');
                    $lku_id = $this->Lku->id;
                    // $this->Journal->deleteJournal( $lku_id, 'lku' );

                    // if( !empty($total_price) ) {
                    //     $document_no = !empty($data['Lku']['no_doc'])?$data['Lku']['no_doc']:false;
                    //     $this->Journal->setJournal( $lku_id, $document_no, 'lku_coa_debit_id', $total_price, 0, 'lku' );
                    //     $this->Journal->setJournal( $lku_id, $document_no, 'lku_coa_credit_id', 0, $total_price, 'lku' );
                    // }

                    if($id && $data_local){
                        $this->Lku->LkuDetail->deleteAll(array(
                            'LkuDetail.lku_id' => $lku_id
                        ));
                    }

                    foreach ($temp_detail as $key => $value) {
                        $this->Lku->LkuDetail->create();
                        $value['LkuDetail']['lku_id'] = $lku_id;

                        $this->Lku->LkuDetail->set($value);
                        $this->Lku->LkuDetail->save();
                    }

                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s LKU'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Berhasil %s LKU #%s'), $msg, $lku_id), $this->user_data, $this->RequestHandler, $this->params );

                    $this->redirect(array(
                        'controller' => 'Lkus',
                        'action' => 'index',
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s LKU'), $msg), 'error');
                    $this->Log->logActivity( sprintf(__('Berhasil %s LKU #%s'), $msg, $lku_id), $this->user_data, $this->RequestHandler, $this->params, 1 );
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s LKU'), $msg), 'error');
            }
        } else if($id && $data_local){
            $this->request->data = $data_local;

            if(!empty($this->request->data['LkuDetail'])){
                foreach ($this->request->data['LkuDetail'] as $key => $value) {
                    $tipe_motor = $this->TipeMotor->getData('first', array(
                        'conditions' => array(
                            'TipeMotor.id' => $value['tipe_motor_id']
                        ),
                    ));
                    if(!empty($tipe_motor)){
                        $Ttuj_Tipe_Motor = $this->Ttuj->TtujTipeMotor->getData('first', array(
                            'conditions' => array(
                                'TtujTipeMotor.ttuj_id' => $this->request->data['Lku']['ttuj_id'],
                                'TtujTipeMotor.tipe_motor_id' => $value['tipe_motor_id']
                            )
                        ));
                        $this->request->data['LkuDetail'][$key]['TipeMotor'] = array_merge($tipe_motor['TipeMotor'], $Ttuj_Tipe_Motor);
                        $this->request->data['LkuDetail'][$key]['ColorMotor'] = !empty($Ttuj_Tipe_Motor['ColorMotor'])?$Ttuj_Tipe_Motor['ColorMotor']:false;
                    }
                }
            }

            $this->request->data['Lku']['tgl_lku'] = (!empty($this->request->data['Lku']['tgl_lku'])) ? $this->MkCommon->getDate($this->request->data['Lku']['tgl_lku'], true) : '';
        }

        if(!empty($this->request->data['LkuDetail']['tipe_motor_id'])){
            $temp = array();
            foreach ($this->request->data['LkuDetail']['tipe_motor_id'] as $key => $value) {
                if( !empty($value) ){
                    $temp['LkuDetail'][$key] = array(
                        'tipe_motor_id' => $value,
                        'no_rangka' => (!empty($data['LkuDetail']['no_rangka'][$key])) ? $data['LkuDetail']['no_rangka'][$key] : '',
                        'qty' => (!empty($data['LkuDetail']['qty'][$key])) ? $data['LkuDetail']['qty'][$key] : '',
                        'price' => (!empty($data['LkuDetail']['price'][$key])) ? $data['LkuDetail']['price'][$key] : '',
                        'part_motor_id' => (!empty($data['LkuDetail']['part_motor_id'][$key])) ? $data['LkuDetail']['part_motor_id'][$key] : '',
                        'note' => (!empty($data['LkuDetail']['note'][$key])) ? $data['LkuDetail']['note'][$key] : '',
                        'no_rangka' => (!empty($data['LkuDetail']['no_rangka'][$key])) ? $data['LkuDetail']['no_rangka'][$key] : '',
                    );

                    $tipe_motor = $this->TipeMotor->getData('first', array(
                        'conditions' => array(
                            'TipeMotor.id' => $value
                        ),
                    ));
                    if(!empty($tipe_motor)){
                        $Ttuj_Tipe_Motor = $this->Ttuj->TtujTipeMotor->getData('first', array(
                            'conditions' => array(
                                'TtujTipeMotor.ttuj_id' => $this->request->data['Lku']['ttuj_id'],
                                'TtujTipeMotor.tipe_motor_id' => $value
                            )
                        ));
                        $temp['LkuDetail'][$key]['TipeMotor'] = array_merge($tipe_motor['TipeMotor'], $Ttuj_Tipe_Motor);
                        $temp['LkuDetail'][$key]['ColorMotor'] = !empty($Ttuj_Tipe_Motor['ColorMotor'])?$Ttuj_Tipe_Motor['ColorMotor']:false;
                    }
                }
            }

            unset($this->request->data['LkuDetail']);
            $this->request->data['LkuDetail'] = $temp['LkuDetail'];
        }

        $ttujs = $this->Ttuj->getData('list', array(
            'fields' => array(
                'Ttuj.id', 'Ttuj.no_ttuj'
            ),
            'conditions' => array(
                'OR' => array(
                    array(
                        'Ttuj.is_bongkaran' => 1,
                        'Ttuj.is_draft' => 0,
                        'Ttuj.status' => 1,
                    ),
                    array(
                        'Ttuj.id' => !empty($data_local['Lku']['ttuj_id']) ? $data_local['Lku']['ttuj_id'] : false
                    )
                )
            ),
        ), false);

        if(!empty($this->request->data['Lku']['ttuj_id'])){
            $data_ttuj = $this->Ttuj->getData('first', array(
                'conditions' => array(
                    'Ttuj.id' => $this->request->data['Lku']['ttuj_id']
                ),
                'contain' => array(
                    'UangJalan'
                )
            ));
            
            if(!empty($data_ttuj)){
                if(!empty($data_ttuj['TtujTipeMotor'])){
                    $tipe_motor_list = array();
                    foreach ($data_ttuj['TtujTipeMotor'] as $key => $value) {
                        $tipe_motor = $this->TipeMotor->getData('first', array(
                            'conditions' => array(
                                'TipeMotor.id' => $value['tipe_motor_id']
                            ),
                            'contain' => array(
                                'GroupMotor'
                            )
                        ));
                        $tipe_motor_list[$tipe_motor['TipeMotor']['id']] = sprintf('%s (%s)', $tipe_motor['TipeMotor']['name'], $tipe_motor['GroupMotor']['name']);
                    }
                    $this->set('tipe_motor_list', $tipe_motor_list);
                }
                $this->request->data = array_merge($this->request->data, $data_ttuj);
            }
            
        }

        $part_motors = $this->PartsMotor->getData('list', array(
            'conditions' => array(
                'PartsMotor.status' => 1
            ),
            'fields' => array(
                'PartsMotor.id', 'PartsMotor.name'
            )
        ));
        $this->set(compact('part_motors'));

        $this->set('active_menu', 'lkus');
        $this->set('ttujs', $ttujs);
        $this->set('id', $id);
        $this->render('lku_form');
    }

    function toggle($id, $action='inactive'){
        if( in_array('delete_lkus', $this->allowModule) && in_array($action, array('inactive', 'activate')) ) {
            $this->loadModel('Lku');

            $status = 1;
            if($action == 'activate'){
                $status = 0;
            }

            $locale = $this->Lku->getData('first', array(
                'conditions' => array(
                    'Lku.id' => $id,
                    'Lku.status' => $status
                ),
            ));

            if($locale){
                $this->Lku->id = $id;

                $value = 0;
                if($status == 'activate'){
                    $value = 1;
                }
                $this->Lku->set('status', $value);

                if($this->Lku->save()){
                    // if( !empty($locale['Lku']['total_price']) ) {
                    //     $this->loadModel('Journal');
                    //     $document_no = !empty($locale['Lku']['no_doc'])?$locale['Lku']['no_doc']:false;
                    //     $this->Journal->setJournal( $id, $document_no, 'lku_void_coa_debit_id', $locale['Lku']['total_price'], 0, 'lku_void' );
                    //     $this->Journal->setJournal( $id, $document_no, 'lku_void_coa_credit_id', 0, $locale['Lku']['total_price'], 'lku_void' );
                    // }

                    $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses merubah status LKU ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params );
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal merubah status LKU ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );
                }
            }else{
                $this->MkCommon->setCustomFlash(__('Lku tidak ditemukan.'), 'error');
            }

            $this->redirect($this->referer());
        } else {
            $this->redirect($this->referer());
        }
    }

    function payments() {
        if( in_array('view_lku_payments', $this->allowModule) ) {
            $this->loadModel('LkuPayment');
            $this->set('active_menu', 'lku_payments');
            $this->set('sub_module_title', __('Data Pembayaran LKU'));
            $conditions = array();
            
            if(!empty($this->params['named'])){
                $refine = $this->params['named'];

                if(!empty($refine['nodoc'])){
                    $no_doc = urldecode($refine['nodoc']);
                    $this->request->data['LkuPayment']['no_doc'] = $no_doc;
                    $conditions['LkuPayment.no_doc LIKE '] = '%'.$no_doc.'%';
                }
            }

            $this->paginate = $this->LkuPayment->getData('paginate', array(
                'conditions' => $conditions,
            ));
            $payments = $this->paginate('LkuPayment');

            if( !empty($payments) ) {
                $this->loadModel('Customer');
                foreach ($payments as $key => $payment) {
                    $payment = $this->Customer->getMerge($payment, $payment['LkuPayment']['customer_id']);
                    $payments[$key] = $payment;
                }
            }

            $this->set('payments', $payments);
        } else {
            $this->redirect($this->referer());
        }
    }

    function payment_add(){
        if( in_array('insert_lku_payments', $this->allowModule) ) {
            $this->set('sub_module_title', __('Tambah Pembayaran LKU'));
            $this->DoLkuPayment();
        } else {
            $this->redirect($this->referer());
        }
    }

    function payment_edit($id){
        if( in_array('update_lku_payments', $this->allowModule) ) {
            $this->loadModel('LkuPayment');
            $this->set('sub_module_title', 'Rubah Pembayaran LKU');
            $Lku = $this->LkuPayment->getData('first', array(
                'conditions' => array(
                    'LkuPayment.id' => $id
                ),
                'contain' => array(
                    'LkuPaymentDetail'
                )
            ));

            if(!empty($Lku)){
                $this->DoLkuPayment($id, $Lku);
            }else{
                $this->MkCommon->setCustomFlash(__('ID Pembayaran LKU tidak ditemukan'), 'error');  
                $this->redirect(array(
                    'controller' => 'Lkus',
                    'action' => 'payments'
                ));
            }
        } else {
            $this->redirect($this->referer());
        }
    }

    function DoLkuPayment($id = false, $data_local = false){
        $this->loadModel('Lku');

        $lku_ids = array();
        if(!empty($this->request->data)){
            $this->loadModel('LkuPayment');
            $data = $this->request->data;

            if($id && $data_local){
                $this->LkuPayment->id = $id;
                $msg = 'merubah';
            }else{
                $this->LkuPayment->create();
                $msg = 'menambah';
            }
            
            $data['LkuPayment']['tgl_bayar'] = (!empty($data['LkuPayment']['tgl_bayar'])) ? $this->MkCommon->getDate($data['LkuPayment']['tgl_bayar']) : '';
            $total_price = 0;

            $validate_lku_detail = true;
            if(!empty($data['LkuPaymentDetail']['lku_id'])){
                foreach ($data['LkuPaymentDetail']['lku_id'] as $key => $value) {
                    if(!empty($value)){
                        $data_detail['LkuPaymentDetail'] = array(
                            'lku_id' => $value,
                            'total_klaim' => (!empty($data['LkuPaymentDetail']['total_klaim'][$key])) ? $data['LkuPaymentDetail']['total_klaim'][$key] : '',
                            'total_biaya_klaim' => (!empty($data['LkuPaymentDetail']['total_biaya_klaim'][$key])) ? $data['LkuPaymentDetail']['total_biaya_klaim'][$key] : ''
                        );
                        
                        $temp_detail[] = $data_detail;
                        $this->LkuPayment->LkuPaymentDetail->set($data_detail);
                        if( !$this->LkuPayment->LkuPaymentDetail->validates() ){
                            $validate_lku_detail = false;
                            break;
                        }else{
                            $total_price += $data_detail['LkuPaymentDetail']['total_biaya_klaim'];
                        }
                    }
                }
            }else{
                $validate_lku_detail = false;
            }

            $data['LkuPayment']['grandtotal'] = $total_price;
            $data['LkuPayment']['paid'] = 1;

            $this->LkuPayment->set($data);

            if($this->LkuPayment->validates($data) && $validate_lku_detail){
                if($this->LkuPayment->save($data)){
                    $lku_payment_id = $this->LkuPayment->id;

                    if( !empty($total_price) ) {
                        $this->loadModel('Journal');
                        $document_no = !empty($data['LkuPayment']['no_doc'])?$data['LkuPayment']['no_doc']:false;
                        $this->Journal->setJournal( $lku_payment_id, $document_no, 'lku_payment_coa_debit_id', $total_price, 0, 'lku_payment' );
                        $this->Journal->setJournal( $lku_payment_id, $document_no, 'lku_payment_coa_credit_id', 0, $total_price, 'lku_payment' );
                    }

                    if($id && $data_local){
                        $this->LkuPayment->LkuPaymentDetail->deleteAll(array(
                            'LkuPaymentDetail.lku_payment_id' => $lku_payment_id
                        ));
                    }

                    foreach ($temp_detail as $key => $value) {
                        $this->LkuPayment->LkuPaymentDetail->create();
                        $value['LkuPaymentDetail']['lku_payment_id'] = $lku_payment_id;

                        $this->LkuPayment->LkuPaymentDetail->set($value);
                        $this->LkuPayment->LkuPaymentDetail->save();

                        if(!empty($temp_detail[$key]['LkuPaymentDetail']['lku_id'])){
                            $this->Lku->id = $temp_detail[$key]['LkuPaymentDetail']['lku_id'];
                            $this->Lku->set('paid', 1);
                            $this->Lku->save();
                        }
                    }

                    $this->Log->logActivity( sprintf(__('Sukses %s Pembayaran LKU ID #%s'), $msg, $lku_payment_id), $this->user_data, $this->RequestHandler, $this->params );
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Pembayaran LKU'), $msg), 'success');
                    $this->redirect(array(
                        'controller' => 'Lkus',
                        'action' => 'payments',
                    ));

                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Pembayaran LKU'), $msg), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal %s Pembayaran LKU #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1 );  
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Pembayaran LKU'), $msg), 'error');
            }
        } else if($id && $data_local){
            $this->request->data = $data_local;

            if(!empty($this->request->data['LkuPaymentDetail'])){
                foreach ($this->request->data['LkuPaymentDetail'] as $key => $value) {
                    $lku = $this->Lku->getData('first', array(
                        'conditions' => array(
                            'Lku.id' => $value['lku_id']
                        ),
                        'contain' => array(
                            'Ttuj'
                        )
                    ));

                    if(!empty($lku)){
                        $this->request->data['LkuPaymentDetail'][$key]['Ttuj'] = $lku['Ttuj'];
                        $this->request->data['LkuPaymentDetail'][$key]['Lku'] = $lku['Lku'];

                        $lku_ids[] = $value['lku_id'];
                    }
                }
            }

            $this->request->data['LkuPayment']['tgl_bayar'] = (!empty($this->request->data['LkuPayment']['tgl_bayar'])) ? $this->MkCommon->getDate($this->request->data['LkuPayment']['tgl_bayar'], true) : '';
        }

        if(!empty($this->request->data['LkuPaymentDetail']['lku_id'])){
            $temp = array();
            foreach ($this->request->data['LkuPaymentDetail']['lku_id'] as $key => $value) {
                if( !empty($value) ){
                    $temp['LkuPaymentDetail'][$key] = array(
                        'lku_id' => $value,
                        'total_klaim' => (!empty($data['LkuPaymentDetail']['total_klaim'][$key])) ? $data['LkuPaymentDetail']['total_klaim'][$key] : '',
                        'total_biaya_klaim' => (!empty($data['LkuPaymentDetail']['total_biaya_klaim'][$key])) ? $data['LkuPaymentDetail']['total_biaya_klaim'][$key] : '',
                    );

                    $lku = $this->Lku->getData('first', array(
                        'conditions' => array(
                            'Lku.id' => $value
                        ),
                        'contain' => array(
                            'Ttuj'
                        )
                    ));

                    if(!empty($lku)){
                        $temp['LkuPaymentDetail'][$key]['Ttuj'] = $lku['Ttuj'];
                        $temp['LkuPaymentDetail'][$key]['Lku'] = $lku['Lku'];
                    }
                }
            }

            unset($this->request->data['LkuPaymentDetail']);
            $this->request->data['LkuPaymentDetail'] = $temp['LkuPaymentDetail'];
        }

        $this->loadModel('Ttuj');
        
        if(!empty($this->request->data['LkuPayment']['customer_id'])){
            $ttuj_id = $this->Ttuj->getData('list', array(
                'conditions' => array(
                    'Ttuj.customer_id' => $this->request->data['LkuPayment']['customer_id']
                ),
                'group' => array(
                    'Ttuj.customer_id'
                ),
                'fields' => array(
                    'Ttuj.id'
                )
            ));

            if(!empty($ttuj_id)){
                $lkus = $this->Lku->getData('all', array(
                    'conditions' => array(
                        'Lku.ttuj_id' => $ttuj_id
                    ),
                    'contain' => array(
                        'Ttuj'
                    )
                ));
            }

            $arr = array();
            if(!empty($lkus)){
                foreach ($lkus as $key => $value) {
                    $arr[$value['Lku']['id']] = sprintf('%s (%s)', date('d F Y', strtotime($value['Ttuj']['ttuj_date'])), $value['Ttuj']['no_ttuj']);
                }
            }
            $lkus = $arr;
            $this->set('lkus', $lkus);
        }

        $this->Ttuj->bindModel(array(
            'belongsTo' => array(
                'CustomerNoType' => array(
                    'className' => 'CustomerNoType',
                    'foreignKey' => 'customer_id',
                ),
            ),
        ), false);

        $ttuj_customer_id = array();
        if(!empty($this->request->data['LkuPayment']['customer_id'])){
            $ttuj_customer_id = $this->Ttuj->getData('list', array(
                'conditions' => array(
                    'Ttuj.customer_id' => $this->request->data['LkuPayment']['customer_id']
                ),
                'group' => array(
                    'Ttuj.customer_id'
                ),
                'fields' => array(
                    'Ttuj.id'
                )
            ));
        }

        $customers = $this->Lku->getData('all', array(
            'conditions' => array(
                'OR' => array(
                    array(
                        'Lku.status' => 1,
                        'Lku.paid' => 0,
                    ),
                    array(
                        'Lku.id' => $ttuj_customer_id,
                        'Lku.paid' => array(0,1)
                    )
                )
            ),
            'contain' => array(
                'Ttuj' => array(
                    'CustomerNoType'
                )
            ),
        ));

        if(!empty($customers)){
            $list_customer = array();
            foreach ($customers as $key => $value) {
                $list_customer[$value['Ttuj']['customer_id']] = sprintf('%s - %s', $value['Ttuj']['customer_name'], $value['Ttuj']['CustomerNoType']['code']);
            }
            $customers = $list_customer;
        }
        $ttujs = array();

        if( !empty($customers) ) {
            foreach ($customers as $customer_id => $value) {
                $dataCust = $this->Ttuj->Customer->getData('first', array(
                    'conditions' => array(
                        'Customer.id' => $customer_id,
                    ),
                ));

                if( !empty($dataCust) ) {
                    $ttujs[$customer_id] = $dataCust['Customer']['customer_name_code'];
                }
            }
        }

        $this->set('active_menu', 'lku_payments');
        $this->set('id', $id);
        $this->set('ttujs', $ttujs);
        $this->render('lku_payment_form');
    }

    public function lku_parts() {
        // if( in_array('view_lku_parts', $this->allowModule) ) {
            $this->loadModel('LkuPart');
            $this->set('active_menu', 'lku_parts');
            $this->set('sub_module_title', __('Data LKU Parts'));
            $conditions = array();
            
            if(!empty($this->params['named'])){
                $refine = $this->params['named'];

                if(!empty($refine['nodoc'])){
                    $no_doc = urldecode($refine['nodoc']);
                    $this->request->data['LkuPart']['no_doc'] = $no_doc;
                    $conditions['LkuPart.no_doc LIKE '] = '%'.$no_doc.'%';
                }
            }

            $this->paginate = $this->LkuPart->getData('paginate', array(
                'conditions' => $conditions
            ));
            $LkuParts = $this->paginate('LkuPart');

            $this->set('LkuParts', $LkuParts);
        // } else {
        //     $this->redirect($this->referer());
        // }
    }

    function lku_part_detail($id = false){
        if( in_array('view_lku_parts', $this->allowModule) ) {
            if(!empty($id)){
                $LkuPart = $this->LkuPart->getLkuPart($id);

                if(!empty($LkuPart)){
                    $sub_module_title = __('Detail LKU Part');
                    $this->set(compact('LkuPart', 'sub_module_title'));
                }else{
                    $this->MkCommon->setCustomFlash(__('Lku Part tidak ditemukan.'), 'error');
                    $this->redirect($this->referer());
                }
            }else{
                $this->MkCommon->setCustomFlash(__('Lku Part tidak ditemukan.'), 'error');
                $this->redirect($this->referer());
            }
        } else {
            $this->redirect($this->referer());
        }
    }

    function lku_part_add(){
        // if( in_array('insert_lku_parts', $this->allowModule) ) {
            $this->set('sub_module_title', __('Tambah LKU Part'));
            $this->DoLkuPart();
        // } else {
        //     $this->redirect($this->referer());
        // }
    }

    function lku_part_edit($id){
        if( in_array('update_lku_parts', $this->allowModule) ) {
            $this->loadModel('LkuPart');
            $this->set('sub_module_title', 'Rubah LKU Part');
            $LkuPart = $this->LkuPart->getData('first', array(
                'conditions' => array(
                    'LkuPart.id' => $id
                ),
            ));

            if(!empty($LkuPart)){
                $this->DoLkuPart($id, $LkuPart);
            }else{
                $this->MkCommon->setCustomFlash(__('LKU Part tidak ditemukan'), 'error');  
                $this->redirect(array(
                    'controller' => 'lkus',
                    'action' => 'lku_parts'
                ));
            }
        } else {
            $this->redirect($this->referer());
        }
    }

    function DoLkuPart($id = false, $data_local = false){
        $this->loadModel('Ttuj');
        $this->loadModel('PartsMotor');

        if(!empty($this->request->data)){
            $data = $this->request->data;

            if($id && $data_local){
                $this->LkuPart->id = $id;
                $msg = 'merubah';
            }else{
                $this->loadModel('LkuPart');
                $this->LkuPart->create();
                $msg = 'menambah';
            }
            
            $data['LkuPart']['tgl_lku'] = (!empty($data['LkuPart']['tgl_lku'])) ? $this->MkCommon->getDate($data['LkuPart']['tgl_lku']) : '';
            
            $validate_lku_detail = true;
            $temp_detail = array();
            $total_price = 0;
            $total_klaim = 0;
            if(!empty($data['LkuPartDetail']['parts_motor_id'])){
                foreach ($data['LkuPartDetail']['parts_motor_id'] as $key => $value) {
                    if( !empty($value) ){
                        $data_detail['LkuPartDetail'] = array(
                            'parts_motor_id' => $value,
                            'qty' => (!empty($data['LkuPartDetail']['qty'][$key])) ? $data['LkuPartDetail']['qty'][$key] : '',
                            'price' => (!empty($data['LkuPartDetail']['price'][$key])) ? $data['LkuPartDetail']['price'][$key] : '',
                        );
                        
                        $temp_detail[] = $data_detail;
                        $this->LkuPart->LkuPartDetail->set($data_detail);
                        if( !$this->LkuPart->LkuPartDetail->validates() ){
                            $validate_lku_detail = false;
                            break;
                        }else{
                            $total_price += $data_detail['LkuPartDetail']['qty'] * $data_detail['LkuPartDetail']['price'];
                            $total_klaim += $data_detail['LkuPartDetail']['qty'];
                        }
                    }
                }
            }else{
                $validate_lku_detail = false;
            }
            
            $data['LkuPart']['total_price'] = $total_price;
            $data['LkuPart']['total_klaim'] = $total_klaim;
            $this->LkuPart->set($data);

            if($this->LkuPart->validates($data) && $validate_lku_detail){
                if($this->LkuPart->save($data)){
                    $lku_part_id = $this->LkuPart->id;

                    if($id && $data_local){
                        $this->LkuPart->LkuPartDetail->deleteAll(array(
                            'LkuPartDetail.lku_part_id' => $lku_part_id
                        ));
                    }

                    foreach ($temp_detail as $key => $value) {
                        $this->LkuPart->LkuPartDetail->create();
                        $value['LkuPartDetail']['lku_part_id'] = $lku_part_id;

                        $this->LkuPart->LkuPartDetail->set($value);
                        $this->LkuPart->LkuPartDetail->save();
                    }

                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s LKU Part'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Berhasil %s LKU Part #%s'), $msg, $lku_part_id), $this->user_data, $this->RequestHandler, $this->params );

                    $this->redirect(array(
                        'controller' => 'LkuParts',
                        'action' => 'index',
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s LKU Part'), $msg), 'error');
                    $this->Log->logActivity( sprintf(__('Berhasil %s LKU Part #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1 );
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s LKU Part'), $msg), 'error');
            }
        } else if($id && $data_local){
            $this->request->data = $data_local;

            if(!empty($this->request->data['LkuPartDetail'])){
                foreach ($this->request->data['LkuPartDetail'] as $key => $value) {
                    $tipe_motor = $this->PartsMotor->getData('first', array(
                        'conditions' => array(
                            'PartsMotor.id' => $value['parts_motor_id']
                        ),
                    ));
                    if(!empty($tipe_motor)){
                        $Ttuj_Tipe_Motor = $this->Ttuj->TtujPartsMotor->getData('first', array(
                            'conditions' => array(
                                'TtujPartsMotor.ttuj_id' => $this->request->data['LkuPart']['ttuj_id'],
                                'TtujPartsMotor.parts_motor_id' => $value['parts_motor_id']
                            )
                        ));
                        $this->request->data['LkuPartDetail'][$key]['PartsMotor'] = array_merge($tipe_motor['PartsMotor'], $Ttuj_Tipe_Motor);
                    }
                }
            }

            $this->request->data['LkuPart']['tgl_lku'] = (!empty($this->request->data['LkuPart']['tgl_lku'])) ? $this->MkCommon->getDate($this->request->data['LkuPart']['tgl_lku'], true) : '';
        }

        if(!empty($this->request->data['LkuPartDetail']['parts_motor_id'])){
            $temp = array();
            foreach ($this->request->data['LkuPartDetail']['parts_motor_id'] as $key => $value) {
                if( !empty($value) ){
                    $temp['LkuPartDetail'][$key] = array(
                        'parts_motor_id' => $value,
                        'qty' => (!empty($data['LkuPartDetail']['qty'][$key])) ? $data['LkuPartDetail']['qty'][$key] : '',
                        'price' => (!empty($data['LkuPartDetail']['price'][$key])) ? $data['LkuPartDetail']['price'][$key] : '',
                    );

                    $tipe_motor = $this->PartsMotor->getData('first', array(
                        'conditions' => array(
                            'PartsMotor.id' => $value
                        )
                    ));
                    if(!empty($tipe_motor)){
                        $Ttuj_Tipe_Motor = $this->Ttuj->TtujPartsMotor->getData('first', array(
                            'conditions' => array(
                                'TtujPartsMotor.ttuj_id' => $this->request->data['LkuPart']['ttuj_id'],
                                'TtujPartsMotor.parts_motor_id' => $value
                            )
                        ));
                        $temp['LkuPartDetail'][$key]['PartsMotor'] = array_merge($tipe_motor['PartsMotor'], $Ttuj_Tipe_Motor);
                    }
                }
            }

            unset($this->request->data['LkuPartDetail']);
            $this->request->data['LkuPartDetail'] = $temp['LkuPartDetail'];
        }

        $ttujs = $this->Ttuj->getData('list', array(
            'fields' => array(
                'Ttuj.id', 'Ttuj.no_ttuj'
            ),
            'conditions' => array(
                'Ttuj.is_pool' => 1,
                'Ttuj.is_draft' => 0,
                'Ttuj.status' => 1,
            ),
        ));

        if(!empty($this->request->data['LkuPart']['ttuj_id'])){
            $data_ttuj = $this->Ttuj->getData('first', array(
                'conditions' => array(
                    'Ttuj.id' => $this->request->data['LkuPart']['ttuj_id']
                ),
            ));
            
            if(!empty($data_ttuj)){
                if(!empty($data_ttuj['TtujPartsMotor'])){
                    $tipe_motor_list = array();
                    foreach ($data_ttuj['TtujPartsMotor'] as $key => $value) {
                        $tipe_motor = $this->PartsMotor->getData('first', array(
                            'conditions' => array(
                                'PartsMotor.id' => $value['parts_motor_id']
                            )
                        ));
                        $tipe_motor_list[$tipe_motor['PartsMotor']['id']] = $tipe_motor['PartsMotor']['name'];
                    }
                    $this->set('tipe_motor_list', $tipe_motor_list);
                }
                $this->request->data = array_merge($this->request->data, $data_ttuj);
            }
            
        }

        $this->set('active_menu', 'lku_parts');
        $this->set('ttujs', $ttujs);
        $this->set('id', $id);
        $this->render('lku_form');
    }

    function lku_part_toggle($id){
        if( in_array('delete_lku_parts', $this->allowModule) ) {
            $this->loadModel('LkuPart');
            $locale = $this->LkuPart->getData('first', array(
                'conditions' => array(
                    'LkuPart.id' => $id
                )
            ));

            if($locale){
                $value = true;
                if($locale['LkuPart']['status']){
                    $value = false;
                }

                $this->LkuPart->id = $id;
                $this->LkuPart->set('status', 0);

                if($this->LkuPart->save()){
                    $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses merubah status LKU Part ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params );
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal merubah status LKU Part ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );
                }
            }else{
                $this->MkCommon->setCustomFlash(__('LKU Part tidak ditemukan.'), 'error');
            }

            $this->redirect($this->referer());
        } else {
            $this->redirect($this->referer());
        }
    }

    public function ksus() {
        if( in_array('view_lkus', $this->allowModule) ) {
            $this->loadModel('Ksu');
            $this->set('active_menu', 'ksus');
            $this->set('sub_module_title', __('Data KSU'));
            $conditions = array();
            
            if(!empty($this->params['named'])){
                $refine = $this->params['named'];

                if(!empty($refine['nodoc'])){
                    $no_doc = urldecode($refine['nodoc']);
                    $this->request->data['Ksu']['no_doc'] = $no_doc;
                    $conditions['Ksu.no_doc LIKE '] = '%'.$no_doc.'%';
                }
            }

            $this->paginate = $this->Ksu->getData('paginate', array(
                'conditions' => $conditions
            ));
            $Ksus = $this->paginate('Ksu');

            $this->set('Ksus', $Ksus);
        } else {
            $this->redirect($this->referer());
        }
    }

    function detail_ksu($id = false){
        if( in_array('view_lkus', $this->allowModule) ) {
            if(!empty($id)){
                $this->loadModel('Ksu');
                $Ksu = $this->Ksu->getKsu($id);

                if(!empty($Ksu)){
                    if(!empty($Ksu['KsuDetail'])){
                        $this->loadModel('Perlengkapan');
                        foreach ($Ksu['KsuDetail'] as $key => $value) {
                            $Perlengkapan = $this->Perlengkapan->getData('first', array(
                                'conditions' => array(
                                    'Perlengkapan.id' => $value['perlengkapan_id']
                                ),
                            ));

                            if(!empty($Perlengkapan)){
                                $Ksu['KsuDetail'][$key]['Perlengkapan'] = $Perlengkapan['Perlengkapan'];
                            }
                        }
                    }

                    $sub_module_title = __('Detail KSU');
                    $this->set(compact('Ksu', 'sub_module_title'));
                    $this->set('active_menu', 'ksus');
                }else{
                    $this->MkCommon->setCustomFlash(__('KSU tidak ditemukan.'), 'error');
                    $this->redirect($this->referer());
                }
            }else{
                $this->MkCommon->setCustomFlash(__('KSU tidak ditemukan.'), 'error');
                $this->redirect($this->referer());
            }
        } else {
            $this->redirect($this->referer());
        }
    }

    function ksu_add(){
        if( in_array('insert_lkus', $this->allowModule) ) {
            $this->set('sub_module_title', __('Tambah KSU'));
            $this->DoKsu();
        } else {
            $this->redirect($this->referer());
        }
    }

    function ksu_edit($id){
        if( in_array('update_lkus', $this->allowModule) ) {
            $this->loadModel('Ksu');
            $this->set('sub_module_title', 'Rubah KSU');
            $Ksu = $this->Ksu->getData('first', array(
                'conditions' => array(
                    'Ksu.id' => $id
                ),
            ));

            if(!empty($Ksu)){
                $this->DoKsu($id, $Ksu);
            }else{
                $this->MkCommon->setCustomFlash(__('KSU tidak ditemukan'), 'error');  
                $this->redirect(array(
                    'controller' => 'Ksus',
                    'action' => 'ksus'
                ));
            }
        } else {
            $this->redirect($this->referer());
        }
    }

    function DoKsu($id = false, $data_local = false){
        $this->loadModel('Ttuj');
        $this->loadModel('Perlengkapan');

        if(!empty($this->request->data)){
            $data = $this->request->data;
            
            if($id && $data_local){
                $this->Ksu->id = $id;
                $msg = 'merubah';
            }else{
                $this->loadModel('Ksu');
                $this->Ksu->create();
                $msg = 'menambah';
            }
            
            $data['Ksu']['tgl_ksu'] = (!empty($data['Ksu']['tgl_ksu'])) ? $this->MkCommon->getDate($data['Ksu']['tgl_ksu']) : '';
            $data['Ksu']['date_atpm'] = (!empty($data['Ksu']['date_atpm'])) ? $this->MkCommon->getDate($data['Ksu']['date_atpm']) : '';
            
            $validate_ksu_detail = true;
            $temp_detail = array();
            $total_price = 0;
            $total_klaim = 0;

            $total_choosen = 0;
            if(!empty($data['KsuDetail']['perlengkapan_id'])){
                foreach ($data['KsuDetail']['perlengkapan_id'] as $key => $value) {
                    if( !empty($value) ){
                        $data_detail = array( 
                            'KsuDetail' => array(
                                'no_rangka' => (!empty($data['KsuDetail']['no_rangka'][$key])) ? $data['KsuDetail']['no_rangka'][$key] : '',
                                'qty' => (!empty($data['KsuDetail']['qty'][$key])) ? $data['KsuDetail']['qty'][$key] : '',
                                'price' => (!empty($data['KsuDetail']['price'][$key])) ? str_replace(',', '', trim($data['KsuDetail']['price'][$key])) : '',
                                'perlengkapan_id' => (!empty($data['KsuDetail']['perlengkapan_id'][$key])) ? $data['KsuDetail']['perlengkapan_id'][$key] : '',
                                'note' => (!empty($data['KsuDetail']['note'][$key])) ? $data['KsuDetail']['note'][$key] : '',
                            ),
                            'Ksu' => array(
                                'kekurangan_atpm' => $data['Ksu']['kekurangan_atpm']
                            )
                        );
                        
                        $temp_detail[] = $data_detail;
                        $this->Ksu->KsuDetail->set($data_detail);
                        if( !$this->Ksu->KsuDetail->validates() ){
                            $validate_ksu_detail = false;
                            break;
                        }else{
                            $total_choosen++;
                            $total_price += $data_detail['KsuDetail']['qty'] * $data_detail['KsuDetail']['price'];
                            $total_klaim += $data_detail['KsuDetail']['qty'];

                            unset($data_detail['Ksu']);
                        }
                    }
                }
            }else{
                $validate_ksu_detail = false;
            }
            
            $data['Ksu']['total_price'] = $total_price;
            $data['Ksu']['total_klaim'] = $total_klaim;
            $this->Ksu->set($data);

            if($this->Ksu->validates($data) && $validate_ksu_detail && $total_choosen > 0){
                if($this->Ksu->save($data)){
                    // $this->loadModel('Journal');
                    $ksu_id = $this->Ksu->id;
                    // $this->Journal->deleteJournal( $ksu_id, 'ksu' );

                    // if( !empty($total_price) ) {
                    //     $document_no = !empty($data['Ksu']['no_doc'])?$data['Ksu']['no_doc']:false;
                    //     $this->Journal->setJournal( $ksu_id, $document_no, 'ksu_coa_debit_id', $total_price, 0, 'ksu' );
                    //     $this->Journal->setJournal( $ksu_id, $document_no, 'ksu_coa_credit_id', 0, $total_price, 'ksu' );
                    // }

                    if($id && $data_local){
                        $this->Ksu->KsuDetail->deleteAll(array(
                            'KsuDetail.ksu_id' => $ksu_id
                        ));
                    }

                    foreach ($temp_detail as $key => $value) {
                        $this->Ksu->KsuDetail->create();
                        $value['KsuDetail']['ksu_id'] = $ksu_id;

                        $this->Ksu->KsuDetail->set($value);
                        $this->Ksu->KsuDetail->save();
                    }

                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s KSU'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Berhasil %s KSU #%s'), $msg, $ksu_id), $this->user_data, $this->RequestHandler, $this->params );

                    $this->redirect(array(
                        'controller' => 'lkus',
                        'action' => 'ksus',
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s KSU'), $msg), 'error');
                    $this->Log->logActivity( sprintf(__('Berhasil %s KSU #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1 );
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s KSU'), $msg), 'error');
            }
        } else if($id && $data_local){
            $this->request->data = $data_local;

            if(!empty($this->request->data['KsuDetail'])){
                foreach ($this->request->data['KsuDetail'] as $key => $value) {
                    $perlengkapan = $this->Perlengkapan->getData('first', array(
                        'conditions' => array(
                            'Perlengkapan.id' => $value['perlengkapan_id']
                        ),
                    ));
                    if(!empty($perlengkapan)){
                        $Ttuj_perlengkapan = $this->Ttuj->TtujPerlengkapan->getData('first', array(
                            'conditions' => array(
                                'TtujPerlengkapan.ttuj_id' => $this->request->data['Ksu']['ttuj_id'],
                                'TtujPerlengkapan.perlengkapan_id' => $value['perlengkapan_id']
                            )
                        ));
                        $this->request->data['KsuDetail'][$key]['Perlengkapan'] = array_merge($perlengkapan['Perlengkapan'], $Ttuj_perlengkapan);
                    }
                }
            }

            $this->request->data['Ksu']['tgl_ksu'] = (!empty($this->request->data['Ksu']['tgl_ksu'])) ? $this->MkCommon->getDate($this->request->data['Ksu']['tgl_ksu'], true) : '';
            $this->request->data['Ksu']['date_atpm'] = (!empty($this->request->data['Ksu']['date_atpm'])) ? $this->MkCommon->getDate($this->request->data['Ksu']['date_atpm'], true) : '';
        }

        if(!empty($this->request->data['KsuDetail']['perlengkapan_id'])){
            $temp = array();
            foreach ($this->request->data['KsuDetail']['perlengkapan_id'] as $key => $value) {
                if( !empty($value) ){
                    $temp['KsuDetail'][$key] = array(
                        'perlengkapan_id' => $value,
                        'no_rangka' => (!empty($data['KsuDetail']['no_rangka'][$key])) ? $data['KsuDetail']['no_rangka'][$key] : '',
                        'qty' => (!empty($data['KsuDetail']['qty'][$key])) ? $data['KsuDetail']['qty'][$key] : '',
                        'price' => (!empty($data['KsuDetail']['price'][$key])) ? $data['KsuDetail']['price'][$key] : '',
                        'perlengkapan_id' => (!empty($data['KsuDetail']['perlengkapan_id'][$key])) ? $data['KsuDetail']['perlengkapan_id'][$key] : '',
                        'note' => (!empty($data['KsuDetail']['note'][$key])) ? $data['KsuDetail']['note'][$key] : '',
                    );

                    $perlengkapan = $this->Perlengkapan->getData('first', array(
                        'conditions' => array(
                            'Perlengkapan.id' => $value,
                            'Perlengkapan.jenis_perlengkapan_id' => 2
                        ),
                    ));
                    if(!empty($perlengkapan)){
                        $Ttuj_perlengkapan = $this->Ttuj->TtujPerlengkapan->getData('first', array(
                            'conditions' => array(
                                'TtujPerlengkapan.ttuj_id' => $this->request->data['Ksu']['ttuj_id'],
                                'TtujPerlengkapan.perlengkapan_id' => $value
                            )
                        ));
                        $temp['KsuDetail'][$key]['Perlengkapan'] = array_merge($perlengkapan['Perlengkapan'], $Ttuj_perlengkapan);
                    }
                }
            }

            unset($this->request->data['KsuDetail']);
            if(!empty($temp['KsuDetail'])){
                $this->request->data['KsuDetail'] = $temp['KsuDetail'];
            }
        }

        $ttujs = $this->Ttuj->getData('list', array(
            'fields' => array(
                'Ttuj.id', 'Ttuj.no_ttuj'
            ),
            'conditions' => array(
                'OR' => array(
                    array(
                        'Ttuj.is_bongkaran' => 1,
                        'Ttuj.is_draft' => 0,
                        'Ttuj.status' => 1,
                    ),
                    array(
                        'Ttuj.id' => !empty($data_local['Ksu']['ttuj_id']) ? $data_local['Ksu']['ttuj_id'] : false
                    )
                )
            ),
        ), false);

        if(!empty($this->request->data['Ksu']['ttuj_id'])){
            $data_ttuj = $this->Ttuj->getData('first', array(
                'conditions' => array(
                    'Ttuj.id' => $this->request->data['Ksu']['ttuj_id']
                ),
                'contain' => array(
                    'UangJalan'
                )
            ));
            
            if(!empty($data_ttuj)){
                if(!empty($data_ttuj['TtujPerlengkapan'])){
                    $perlengkapan_list = array();
                    foreach ($data_ttuj['TtujPerlengkapan'] as $key => $value) {
                        $perlengkapan_data = $this->Perlengkapan->getData('first', array(
                            'conditions' => array(
                                'Perlengkapan.id' => $value['perlengkapan_id']
                            )
                        ));
                        $perlengkapan_list[$perlengkapan_data['Perlengkapan']['id']] = $perlengkapan_data['Perlengkapan']['name'];
                    }
                    $this->set('perlengkapan_list', $perlengkapan_list);
                }
                $this->request->data = array_merge($this->request->data, $data_ttuj);
            }
            
        }

        $perlengkapans = $this->Perlengkapan->getListPerlengkapan(2);
        $this->set(compact('perlengkapans'));

        $this->set('active_menu', 'ksus');
        $this->set('ttujs', $ttujs);
        $this->set('id', $id);
        $this->render('ksu_form');
    }

    function ksu_toggle($id, $action = 'inactive'){
        if( in_array('delete_lkus', $this->allowModule) && in_array($action, array('inactive', 'activate')) ) {
            $this->loadModel('Ksu');

            $status = 1;
            if($action == 'activate'){
                $status = 0;
            }
            $locale = $this->Ksu->getData('first', array(
                'conditions' => array(
                    'Ksu.id' => $id,
                    'Ksu.status' => $status
                )
            ));

            if($locale){
                $value = 0;
                if($status == 'activate'){
                    $value = 1;
                }

                $this->Ksu->id = $id;
                $this->Ksu->set('status', $value);

                if($this->Ksu->save()){
                    // if( !empty($locale['Ksu']['total_price']) ) {
                    //     $this->loadModel('Journal');
                    //     $document_no = !empty($locale['Ksu']['no_doc'])?$locale['Ksu']['no_doc']:false;
                    //     $this->Journal->setJournal( $id, $document_no, 'ksu_void_coa_debit_id', $locale['Ksu']['total_price'], 0, 'ksu_void' );
                    //     $this->Journal->setJournal( $id, $document_no, 'ksu_void_coa_credit_id', 0, $locale['Ksu']['total_price'], 'ksu_void' );
                    // }

                    $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses merubah status KSU ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params );
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal merubah status KSU ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );
                }
            }else{
                $this->MkCommon->setCustomFlash(__('Ksu tidak ditemukan.'), 'error');
            }
        }

        $this->redirect($this->referer());
    }

    function ksu_payments() {
        if( in_array('view_lku_payments', $this->allowModule) ) {
            $this->loadModel('KsuPayment');
            $this->set('active_menu', 'ksu_payments');
            $this->set('sub_module_title', __('Data Pembayaran KSU'));
            $conditions = array();
            
            if(!empty($this->params['named'])){
                $refine = $this->params['named'];

                if(!empty($refine['nodoc'])){
                    $no_doc = urldecode($refine['nodoc']);
                    $this->request->data['KsuPayment']['no_doc'] = $no_doc;
                    $conditions['KsuPayment.no_doc LIKE '] = '%'.$no_doc.'%';
                }
            }

            $this->paginate = $this->KsuPayment->getData('paginate', array(
                'conditions' => $conditions,
            ));
            $payments = $this->paginate('KsuPayment');

            if( !empty($payments) ) {
                $this->loadModel('Customer');
                foreach ($payments as $key => $payment) {
                    $payment = $this->Customer->getMerge($payment, $payment['KsuPayment']['customer_id']);
                    $payments[$key] = $payment;
                }
            }

            $this->set('payments', $payments);
        } else {
            $this->redirect($this->referer());
        }
    }

    function ksu_payment_add(){
        if( in_array('insert_lku_payments', $this->allowModule) ) {
            $this->set('sub_module_title', __('Tambah Pembayaran KSU'));
            $this->DoKsuPayment();
        } else {
            $this->redirect($this->referer());
        }
    }

    function ksu_payment_edit($id){
        if( in_array('update_lku_payments', $this->allowModule) ) {
            $this->loadModel('KsuPayment');
            $this->set('sub_module_title', 'Rubah Pembayaran KSU');
            $Ksu = $this->KsuPayment->getData('first', array(
                'conditions' => array(
                    'KsuPayment.id' => $id
                ),
                'contain' => array(
                    'KsuPaymentDetail'
                )
            ));

            if(!empty($Ksu)){
                $this->DoKsuPayment($id, $Ksu);
            }else{
                $this->MkCommon->setCustomFlash(__('ID Pembayaran KSU tidak ditemukan'), 'error');  
                $this->redirect(array(
                    'controller' => 'lkus',
                    'action' => 'ksu_payments'
                ));
            }
        } else {
            $this->redirect($this->referer());
        }
    }

    function DoKsuPayment($id = false, $data_local = false){
        $this->loadModel('Ksu');

        if(!empty($this->request->data)){
            $this->loadModel('KsuPayment');
            $data = $this->request->data;

            if($id && $data_local){
                $this->KsuPayment->id = $id;
                $msg = 'merubah';
            }else{
                $this->KsuPayment->create();
                $msg = 'menambah';
            }
            
            $data['KsuPayment']['tgl_bayar'] = (!empty($data['KsuPayment']['tgl_bayar'])) ? $this->MkCommon->getDate($data['KsuPayment']['tgl_bayar']) : '';
            $total_price = 0;

            $validate_ksu_detail = true;
            if(!empty($data['KsuPaymentDetail']['ksu_id'])){
                $id_choosen = 0;
                foreach ($data['KsuPaymentDetail']['ksu_id'] as $key => $value) {
                    if(!empty($value)){
                        $data_detail['KsuPaymentDetail'] = array(
                            'ksu_id' => $value,
                            'total_klaim' => (!empty($data['KsuPaymentDetail']['total_klaim'][$key])) ? $data['KsuPaymentDetail']['total_klaim'][$key] : '',
                            'total_biaya_klaim' => (!empty($data['KsuPaymentDetail']['total_biaya_klaim'][$key])) ? $data['KsuPaymentDetail']['total_biaya_klaim'][$key] : '',
                        );
                        
                        $temp_detail[] = $data_detail;
                        $this->KsuPayment->KsuPaymentDetail->set($data_detail);
                        if( !$this->KsuPayment->KsuPaymentDetail->validates() ){
                            $validate_ksu_detail = false;
                            break;
                        }else{
                            $total_price += $data_detail['KsuPaymentDetail']['total_biaya_klaim'];
                            $id_choosen++;
                        }
                    }
                }
            }else{
                $validate_ksu_detail = false;
            }

            $data['KsuPayment']['grandtotal'] = $total_price;
            $data['KsuPayment']['paid'] = 1;

            $this->KsuPayment->set($data);

            if($this->KsuPayment->validates($data) && $validate_ksu_detail && $id_choosen > 0){
                if($this->KsuPayment->save($data)){
                    $ksu_payment_id = $this->KsuPayment->id;
                    
                    if( !empty($total_price) ) {
                        $this->loadModel('Journal');
                        $document_no = !empty($data['KsuPayment']['no_doc'])?$data['KsuPayment']['no_doc']:false;
                        $this->Journal->setJournal( $ksu_payment_id, $document_no, 'ksu_payment_coa_debit_id', $total_price, 0, 'ksu_payment' );
                        $this->Journal->setJournal( $ksu_payment_id, $document_no, 'ksu_payment_coa_credit_id', 0, $total_price, 'ksu_payment' );
                    }

                    if($id && $data_local){
                        $this->KsuPayment->KsuPaymentDetail->deleteAll(array(
                            'KsuPaymentDetail.ksu_payment_id' => $ksu_payment_id
                        ));
                    }

                    foreach ($temp_detail as $key => $value) {
                        $this->KsuPayment->KsuPaymentDetail->create();
                        $value['KsuPaymentDetail']['ksu_payment_id'] = $ksu_payment_id;

                        $this->KsuPayment->KsuPaymentDetail->set($value);
                        $this->KsuPayment->KsuPaymentDetail->save();

                        if(!empty($temp_detail[$key]['KsuPaymentDetail']['ksu_id'])){
                            $this->Ksu->id = $temp_detail[$key]['KsuPaymentDetail']['ksu_id'];
                            $this->Ksu->set('paid', 1);
                            $this->Ksu->save();
                        }
                    }

                    $this->Log->logActivity( sprintf(__('Sukses %s Pembayaran KSU ID #%s'), $msg, $ksu_payment_id), $this->user_data, $this->RequestHandler, $this->params );
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Pembayaran KSU'), $msg), 'success');
                    $this->redirect(array(
                        'controller' => 'lkus',
                        'action' => 'ksu_payments',
                    ));

                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Pembayaran KSU'), $msg), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal %s Pembayaran KSU #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1 );  
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Pembayaran KSU'), $msg), 'error');
            }
        } else if($id && $data_local){
            $this->request->data = $data_local;

            if(!empty($this->request->data['KsuPaymentDetail'])){
                foreach ($this->request->data['KsuPaymentDetail'] as $key => $value) {
                    $ksu = $this->Ksu->getData('first', array(
                        'conditions' => array(
                            'Ksu.id' => $value['ksu_id'],
                            'Ksu.kekurangan_atpm' => 0
                        ),
                        'contain' => array(
                            'Ttuj'
                        )
                    ));

                    if(!empty($ksu)){
                        $this->request->data['KsuPaymentDetail'][$key]['Ttuj'] = $ksu['Ttuj'];
                        $this->request->data['KsuPaymentDetail'][$key]['Ksu'] = $ksu['Ksu'];
                    }
                }
            }

            $this->request->data['KsuPayment']['tgl_bayar'] = (!empty($this->request->data['KsuPayment']['tgl_bayar'])) ? $this->MkCommon->getDate($this->request->data['KsuPayment']['tgl_bayar'], true) : '';
        }

        if(!empty($this->request->data['KsuPaymentDetail']['ksu_id'])){
            $temp = array();
            foreach ($this->request->data['KsuPaymentDetail']['ksu_id'] as $key => $value) {
                if( !empty($value) ){
                    $temp['KsuPaymentDetail'][$key] = array(
                        'ksu_id' => $value,
                        'total_klaim' => (!empty($data['KsuPaymentDetail']['total_klaim'][$key])) ? $data['KsuPaymentDetail']['total_klaim'][$key] : '',
                        'total_biaya_klaim' => (!empty($data['KsuPaymentDetail']['total_biaya_klaim'][$key])) ? $data['KsuPaymentDetail']['total_biaya_klaim'][$key] : '',
                    );

                    $ksu = $this->Ksu->getData('first', array(
                        'conditions' => array(
                            'Ksu.id' => $value
                        ),
                        'contain' => array(
                            'Ttuj'
                        )
                    ));

                    if(!empty($ksu)){
                        $temp['KsuPaymentDetail'][$key]['Ttuj'] = $ksu['Ttuj'];
                        $temp['KsuPaymentDetail'][$key]['Ksu'] = $ksu['Ksu'];
                    }
                }
            }

            unset($this->request->data['KsuPaymentDetail']);

            if(!empty($temp['KsuPaymentDetail'])){
                $this->request->data['KsuPaymentDetail'] = $temp['KsuPaymentDetail'];
            }
        }

        $this->loadModel('Ttuj');
        
        if(!empty($this->request->data['KsuPayment']['customer_id'])){
            $ttuj_id = $this->Ttuj->getData('list', array(
                'conditions' => array(
                    'Ttuj.customer_id' => $this->request->data['KsuPayment']['customer_id']
                ),
                'group' => array(
                    'Ttuj.customer_id'
                ),
                'fields' => array(
                    'Ttuj.id'
                )
            ));

            if(!empty($ttuj_id)){
                $ksus = $this->Ksu->getData('all', array(
                    'conditions' => array(
                        'Ksu.ttuj_id' => $ttuj_id,
                        'Ksu.kekurangan_atpm' => 0
                    ),
                    'contain' => array(
                        'Ttuj'
                    )
                ));
            }

            $arr = array();
            if(!empty($ksus)){
                foreach ($ksus as $key => $value) {
                    $arr[$value['Ksu']['id']] = sprintf('%s (%s)', date('d F Y', strtotime($value['Ttuj']['ttuj_date'])), $value['Ttuj']['no_ttuj']);
                }
            }
            $ksus = $arr;
            $this->set('ksus', $ksus);
        }

        $this->Ttuj->bindModel(array(
            'belongsTo' => array(
                'CustomerNoType' => array(
                    'className' => 'CustomerNoType',
                    'foreignKey' => 'customer_id',
                ),
            ),
        ), false);

        $ttuj_customer_id = array();
        if(!empty($this->request->data['KsuPayment']['customer_id'])){
            $ttuj_customer_id = $this->Ttuj->getData('list', array(
                'conditions' => array(
                    'Ttuj.customer_id' => $this->request->data['KsuPayment']['customer_id']
                ),
                'group' => array(
                    'Ttuj.customer_id'
                ),
                'fields' => array(
                    'Ttuj.id'
                )
            ));
        }

        $customers = $this->Ksu->getData('all', array(
            'conditions' => array(
                'Ksu.kekurangan_atpm' => 0,
                'OR' => array(
                    array(
                        'Ksu.status' => 1,
                        'Ksu.paid' => 0
                    ),
                    array(
                        'Ksu.ttuj_id' => $ttuj_customer_id,
                        'Ksu.paid' => array(0,1)
                    )
                )
            ),
            'contain' => array(
                'Ttuj' => array(
                    'CustomerNoType'
                )
            ),
        ));

        if(!empty($customers)){
            $list_customer = array();
            foreach ($customers as $key => $value) {
                $list_customer[$value['Ttuj']['customer_id']] = sprintf('%s - %s', $value['Ttuj']['customer_name'], $value['Ttuj']['CustomerNoType']['code']);
            }
            $customers = $list_customer;
        }
        $ttujs = array();

        if( !empty($customers) ) {
            foreach ($customers as $customer_id => $value) {
                $dataCust = $this->Ttuj->Customer->getData('first', array(
                    'conditions' => array(
                        'Customer.id' => $customer_id,
                    ),
                ));

                if( !empty($dataCust) ) {
                    $ttujs[$customer_id] = $dataCust['Customer']['customer_name_code'];
                }
            }
        }

        $this->set('active_menu', 'ksu_payments');
        $this->set('id', $id);
        $this->set('ttujs', $ttujs);
        $this->render('ksu_payment_form');
    }

    function payment_delete($id){
        $this->loadModel('LkuPayment');
        $payments = $this->LkuPayment->getData('first', array(
            'conditions' => array(
                'LkuPayment.id' => $id,
                'LkuPayment.status' => 1,
                'LkuPayment.is_void' => 0,
            ), 
            'contain' => array(
                'LkuPaymentDetail'
            )
        ));

        if(!empty($payments)){
            $lku_id = Set::extract('/LkuPaymentDetail/lku_id', $payments);
            
            if(!empty($lku_id)){
                $this->loadModel('Lku');
                $this->Lku->updateAll(
                    array(
                        'Lku.paid' => 0
                    ),
                    array(
                        'Lku.id' => $lku_id
                    )
                );
            }

            $this->LkuPayment->id = $id;
            $this->LkuPayment->set(array(
                'paid' => 0,
                'status' => 1,
                'is_void' => 1
            ));

            if($this->LkuPayment->save()){
                $this->MkCommon->setCustomFlash(__('Berhasil melakukan pembatalan pembayaran LKU.'), 'success');    
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal melakukan pembatalan pembayaran LKU.'), 'error');    
            }
        }else{
            $this->MkCommon->setCustomFlash(__('ID Pembayaran LKU tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    function ksu_payment_delete($id){
        $this->loadModel('KsuPayment');
        $payments = $this->KsuPayment->getData('first', array(
            'conditions' => array(
                'KsuPayment.id' => $id,
                'KsuPayment.status' => 1,
                'KsuPayment.is_void' => 0,
            ), 
            'contain' => array(
                'KsuPaymentDetail'
            )
        ));

        if(!empty($payments)){
            $ksu_id = Set::extract('/KsuPaymentDetail/ksu_id', $payments);
            
            if(!empty($ksu_id)){
                $this->loadModel('Lku');
                $this->Lku->updateAll(
                    array(
                        'Lku.paid' => 0
                    ),
                    array(
                        'Lku.id' => $ksu_id
                    )
                );
            }

            $this->KsuPayment->id = $id;
            $this->KsuPayment->set(array(
                'paid' => 0,
                'status' => 1,
                'is_void' => 1
            ));

            if($this->KsuPayment->save()){
                $this->MkCommon->setCustomFlash(__('Berhasil melakukan pembatalan pembayaran KSU.'), 'success');    
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal melakukan pembatalan pembayaran KSU.'), 'error');    
            }
        }else{
            $this->MkCommon->setCustomFlash(__('ID Pembayaran KSU tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }
}