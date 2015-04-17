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
    		$this->set('sub_module_title', __('Data LKU/KSU'));
            $conditions = array();
            
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
                $Lku = $this->Lku->getLku($id);

                if(!empty($Lku)){
                    $sub_module_title = __('Detail LKU/KSU');
                    $this->set(compact('Lku', 'sub_module_title'));
                }else{
                    $this->MkCommon->setCustomFlash(__('LKU/KSU tidak ditemukan.'), 'error');
                    $this->redirect($this->referer());
                }
            }else{
                $this->MkCommon->setCustomFlash(__('LKU/KSU tidak ditemukan.'), 'error');
                $this->redirect($this->referer());
            }
        } else {
            $this->redirect($this->referer());
        }
    }

    function add(){
        if( in_array('insert_lkus', $this->allowModule) ) {
            $this->set('sub_module_title', __('Tambah LKU/KSU'));
            $this->DoLku();
        } else {
            $this->redirect($this->referer());
        }
    }

    function edit($id){
        if( in_array('update_lkus', $this->allowModule) ) {
            $this->loadModel('Lku');
            $this->set('sub_module_title', 'Rubah LKU/KSU');
            $Lku = $this->Lku->getData('first', array(
                'conditions' => array(
                    'Lku.id' => $id
                ),
            ));

            if(!empty($Lku)){
                $this->DoLku($id, $Lku);
            }else{
                $this->MkCommon->setCustomFlash(__('LKU/KSU tidak ditemukan'), 'error');  
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
                            'price' => (!empty($data['LkuDetail']['price'][$key])) ? $data['LkuDetail']['price'][$key] : '',
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
                    $lku_id = $this->Lku->id;

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

                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s LKU/KSU'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Berhasil %s LKU/KSU'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );

                    $this->redirect(array(
                        'controller' => 'Lkus',
                        'action' => 'index',
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s LKU/KSU'), $msg), 'error');
                    $this->Log->logActivity( sprintf(__('Berhasil %s LKU/KSU #%s'), $msg, $lku_id), $this->user_data, $this->RequestHandler, $this->params, 1 );
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s LKU/KSU'), $msg), 'error');
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
                'Ttuj.is_pool' => 1,
                'Ttuj.is_draft' => 0,
                'Ttuj.status' => 1,
            ),
        ));

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
                            )
                        ));
                        $tipe_motor_list[$tipe_motor['TipeMotor']['id']] = $tipe_motor['TipeMotor']['name'];
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

    function toggle($id){
        if( in_array('delete_lkus', $this->allowModule) ) {
            $this->loadModel('Lku');
            $locale = $this->Lku->getData('first', array(
                'conditions' => array(
                    'Lku.id' => $id
                )
            ));

            if($locale){
                $value = true;
                if($locale['Lku']['status']){
                    $value = false;
                }

                $this->Lku->id = $id;
                $this->Lku->set('status', 0);

                if($this->Lku->save()){
                    $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses merubah status LKU/KSU ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal merubah status LKU/KSU ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );
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
            $this->set('sub_module_title', __('Data Pembayaran LKU/KSU'));
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
            $this->set('sub_module_title', __('Tambah Pembayaran LKU/KSU'));
            $this->DoLkuPayment();
        } else {
            $this->redirect($this->referer());
        }
    }

    function payment_edit($id){
        if( in_array('update_lku_payments', $this->allowModule) ) {
            $this->loadModel('LkuPayment');
            $this->set('sub_module_title', 'Rubah Pembayaran LKU/KSU');
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
                $this->MkCommon->setCustomFlash(__('ID Pembayaran LKU/KSU tidak ditemukan'), 'error');  
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

            $this->LkuPayment->set($data);

            if($this->LkuPayment->validates($data) && $validate_lku_detail){
                if($this->LkuPayment->save($data)){
                    $lku_payment_id = $this->LkuPayment->id;

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
                    }

                    $this->Log->logActivity( sprintf(__('Sukses %s Pembayaran LKU/KSU ID #%s'), $msg, $lku_payment_id), $this->user_data, $this->RequestHandler, $this->params, 1 );

                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Pembayaran LKU/KSU'), $msg), 'success');
                    $this->redirect(array(
                        'controller' => 'Lkus',
                        'action' => 'payments',
                    ));

                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Pembayaran LKU/KSU'), $msg), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal %s Pembayaran LKU/KSU')), $this->user_data, $this->RequestHandler, $this->params, 1 );  
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Pembayaran LKU/KSU'), $msg), 'error');
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
                        $temp['LkuPaymentDetail'][$key]['Ttuj'] = $lku;
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

        $customers = $this->Ttuj->getData('list', array(
            'conditions' => array(
                'Ttuj.is_revenue' => 0
            ),
            'fields' => array(
                'Ttuj.customer_id', 'Ttuj.customer_name'
            ),
        ));
        $ttujs = array();

        if( !empty($customers) ) {
            foreach ($customers as $customer_id => $value) {
                $dataCust = $this->Ttuj->Customer->getData('first', array(
                    'conditions' => array(
                        'Customer.id' => $customer_id,
                    ),
                ));

                if( !empty($dataCust) ) {
                    $ttujs[$customer_id] = $dataCust['Customer']['customer_name'];
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
                    $this->Log->logActivity( sprintf(__('Berhasil %s LKU Part'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );

                    $this->redirect(array(
                        'controller' => 'LkuParts',
                        'action' => 'index',
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s LKU Part'), $msg), 'error');
                    $this->Log->logActivity( sprintf(__('Berhasil %s LKU Part #%s'), $msg, $lku_part_id), $this->user_data, $this->RequestHandler, $this->params, 1 );
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
                    $this->Log->logActivity( sprintf(__('Sukses merubah status LKU Part ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );
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
}