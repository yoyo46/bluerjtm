<?php
App::uses('AppController', 'Controller');
class LkusController extends AppController {
	public $uses = array();

    public $components = array(
        'RjLku'
    );

    function beforeFilter() {
        parent::beforeFilter();
        $this->set('title_for_layout', __('ERP RJTM | Data LKU'));
        $this->set('module_title', __('Lku'));
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
        $this->loadModel('Lku');
		$this->set('active_menu', 'Lkus');
		$this->set('sub_module_title', __('Data Lku'));
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
	}

    function detail($id = false){
        if(!empty($id)){
            $Lku = $this->Lku->getLku($id);

            if(!empty($Lku)){
                $sub_module_title = __('Detail Lku');
                $this->set(compact('Lku', 'sub_module_title'));
            }else{
                $this->MkCommon->setCustomFlash(__('Lku tidak ditemukan.'), 'error');
                $this->redirect($this->referer());
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Lku tidak ditemukan.'), 'error');
            $this->redirect($this->referer());
        }
    }

    function add(){
        $this->set('sub_module_title', __('Tambah Lku'));
        $this->DoLku();
    }

    function edit($id){
        $this->loadModel('Lku');
        $this->set('sub_module_title', 'Rubah Lku');
        $Lku = $this->Lku->getData('first', array(
            'conditions' => array(
                'Lku.id' => $id
            ),
        ));

        if(!empty($Lku)){
            $this->DoLku($id, $Lku);
        }else{
            $this->MkCommon->setCustomFlash(__('Lku tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'Lkus',
                'action' => 'index'
            ));
        }
    }

    function DoLku($id = false, $data_local = false){
        $this->loadModel('Ttuj');
        $this->loadModel('TipeMotor');

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

                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s LKU'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Berhasil %s LKU'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );

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
                        'contain' => array(
                            'ColorMotor'
                        )
                    ));
                    if(!empty($tipe_motor)){
                        $Ttuj_Tipe_Motor = $this->Ttuj->TtujTipeMotor->getData('first', array(
                            'conditions' => array(
                                'TtujTipeMotor.ttuj_id' => $this->request->data['Lku']['ttuj_id'],
                                'TtujTipeMotor.tipe_motor_id' => $value['tipe_motor_id']
                            )
                        ));
                        $this->request->data['LkuDetail'][$key]['TipeMotor'] = array_merge($tipe_motor['TipeMotor'], $Ttuj_Tipe_Motor);
                        $this->request->data['LkuDetail'][$key]['ColorMotor'] = $tipe_motor['ColorMotor'];
                    }
                }
            }
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
                    );

                    $tipe_motor = $this->TipeMotor->getData('first', array(
                        'conditions' => array(
                            'TipeMotor.id' => $value
                        ),
                        'contain' => array(
                            'ColorMotor'
                        )
                    ));
                    if(!empty($tipe_motor)){
                        $Ttuj_Tipe_Motor = $this->Ttuj->TtujTipeMotor->getData('first', array(
                            'conditions' => array(
                                'TtujTipeMotor.ttuj_id' => $this->request->data['Lku']['ttuj_id'],
                                'TtujTipeMotor.tipe_motor_id' => $value
                            )
                        ));
                        $temp['LkuDetail'][$key]['TipeMotor'] = array_merge($tipe_motor['TipeMotor'], $Ttuj_Tipe_Motor);
                        $temp['LkuDetail'][$key]['ColorMotor'] = $tipe_motor['ColorMotor'];
                    }
                }
            }

            unset($this->request->data['LkuDetail']);
            $this->request->data['LkuDetail'] = $temp['LkuDetail'];
        }

        $ttujs = $this->Ttuj->getData('list', array(
            'fields' => array(
                'Ttuj.id', 'Ttuj.no_ttuj'
            )
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
                }
                $this->request->data = array_merge($this->request->data, $data_ttuj);
            }
            
            $this->set('tipe_motor_list', $tipe_motor_list);
        }

        $this->set('active_menu', 'Lkus');
        $this->set('ttujs', $ttujs);
        $this->set('id', $id);
        $this->render('lku_form');
    }

    function toggle($id){
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
                $this->Log->logActivity( sprintf(__('Sukses merubah status LKU ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal merubah status LKU ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Lku tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    function payments() {
        $this->loadModel('LkuPayment');
        $this->set('active_menu', 'Lkus');
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
            'contain' => array(
                'Customer'
            )
        ));
        $payments = $this->paginate('LkuPayment');

        $this->set('payments', $payments);
    }

    function payment_add(){
        $this->set('sub_module_title', __('Tambah Pembayaran LKU'));
        $this->DoLkuPayment();
    }

    function payment_edit($id){
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
            $this->MkCommon->setCustomFlash(__('ID Pembayaran Lku tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'Lkus',
                'action' => 'payments'
            ));
        }
    }

    function DoLkuPayment($id = false, $data_local = false){
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

                    $this->Log->logActivity( sprintf(__('Sukses %s Pembayaran LKU ID #%s'), $lku_payment_id), $this->user_data, $this->RequestHandler, $this->params, 1 );

                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Pembayaran LKU'), $msg), 'success');
                    $this->redirect(array(
                        'controller' => 'Lkus',
                        'action' => 'payments',
                    ));

                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Pembayaran LKU'), $msg), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal %s Pembayaran LKU')), $this->user_data, $this->RequestHandler, $this->params, 1 );  
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Pembayaran LKU'), $msg), 'error');
            }
        } else if($id && $data_local){
            $this->request->data = $data_local;

            if(!empty($this->request->data['LkuPaymentDetail'])){
                $this->loadModel('Lku');
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
        }

        if(!empty($this->request->data['LkuPaymentDetail']['lku_id'])){
            $temp = array();
            foreach ($this->request->data['LkuPaymentDetail']['lku_id'] as $key => $value) {
                if( !empty($value) ){
                    $temp['LkuPaymentDetail'][$key] = array(
                        'lku_id' => $value,
                        'total_klaim' => (!empty($data['LkuPaymentDetail']['total_klaim'][$key])) ? $data['LkuDetail']['total_klaim'][$key] : '',
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

        $ttujs = $this->Ttuj->getData('list', array(
            'conditions' => array(
                'Ttuj.is_revenue' => 0
            ),
            'fields' => array(
                'Customer.id', 'Customer.name'
            ),
            'contain' => array(
                'Customer'
            )
        ));

        $this->set('active_menu', 'Lkus');
        $this->set('id', $id);
        $this->set('ttujs', $ttujs);
        $this->render('lku_payment_form');
    }
}