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
        $this->loadModel('Lku');
        $this->loadModel('Ttuj');
        $this->loadModel('Customer');

		$this->set('active_menu', 'lkus');
		$this->set('sub_module_title', __('Data LKU'));
        $conditions = array();
        
        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['nodoc'])){
                $no_doc = urldecode($refine['nodoc']);
                $this->request->data['Lku']['no_doc'] = $no_doc;
                $conditions['Lku.no_doc LIKE '] = '%'.$no_doc.'%';
            }

            if(!empty($refine['from'])){
                $from = urldecode($refine['from']);
                $this->request->data['Lku']['from_date'] = $this->MkCommon->getDate($from, true);
                $conditions['DATE_FORMAT(Lku.tgl_lku, \'%Y-%m-%d\') >='] = $from;
            }

            if(!empty($refine['to'])){
                $to = urldecode($refine['to']);
                $this->request->data['Lku']['to_date'] = $this->MkCommon->getDate($to, true);
                $conditions['DATE_FORMAT(Lku.tgl_lku, \'%Y-%m-%d\') <='] = $to;
            }

            if(!empty($refine['no_ttuj'])){
                $no_ttuj = urldecode($refine['no_ttuj']);
                $this->request->data['Lku']['no_ttuj'] = $no_ttuj;
                $conditions['Ttuj.no_ttuj LIKE '] = '%'.$no_ttuj.'%';
            }

            if(!empty($refine['customer'])){
                $customer = urldecode($refine['customer']);
                $this->request->data['Lku']['customer_id'] = $customer;
                $conditions['Ttuj.customer_id'] = $customer;
            }
        }

        $this->paginate = $this->Lku->getData('paginate', array(
            'conditions' => $conditions,
            'contain' => array(
                'Ttuj'
            ),
        ), true, array(
            'status' => 'all',
        ));
        $Lkus = $this->paginate('Lku');

        if(!empty($Lkus)){
            foreach ($Lkus as $key => $value) {
                $customer_data['Customer'] = array();
                
                if(!empty($value['Ttuj']['customer_id'])){
                    $customer_data = $this->Customer->getData('first', array(
                        'conditions' => array(
                            'Customer.id' => $value['Ttuj']['customer_id']
                        )
                    ), true, array(
                        'status' => 'all',
                    ));

                    if( !empty($customer_data['Customer']) ) {
                        $Lkus[$key]['Customer'] = $customer_data['Customer'];
                    }
                }
            }
        }

        $customers = $this->Customer->getData('list', array(
            'fields' => array(
                'Customer.id', 'Customer.customer_name_code'
            ),
        ));
        $this->set(compact(
            'Lkus', 'customers'
        ));
	}

    function detail($id = false){
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
    }

    function add(){
        $this->set('sub_module_title', __('Tambah LKU'));
        $this->DoLku();
    }

    function edit($id){
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
    }

    function DoLku($id = false, $data_local = false){
        $this->loadModel('Ttuj');
        $this->loadModel('TipeMotor');
        $this->loadModel('PartsMotor');
        $this->loadModel('TtujTipeMotor');

        if(!empty($this->request->data)){
            $data = $this->request->data;
            $data['Lku']['group_branch_id'] = Configure::read('__Site.config_branch_id');
            
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
                        $price = (!empty($data['LkuDetail']['price'][$key])) ? str_replace(',', '', trim($data['LkuDetail']['price'][$key])) : 0;
                        $qty = (!empty($data['LkuDetail']['qty'][$key])) ? $data['LkuDetail']['qty'][$key] : 0;
                        $total = $price * $qty;

                        $data_detail['LkuDetail'] = array(
                            'tipe_motor_id' => $value,
                            'no_rangka' => (!empty($data['LkuDetail']['no_rangka'][$key])) ? $data['LkuDetail']['no_rangka'][$key] : '',
                            'qty' => $qty,
                            'price' => $price,
                            'total_price' => $total,
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

                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s LKU'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Berhasil %s LKU #%s'), $msg, $lku_id), $this->user_data, $this->RequestHandler, $this->params );

                    $this->redirect(array(
                        'controller' => 'Lkus',
                        'action' => 'index',
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s LKU. Lengkapi field yang dibutuhkan'), $msg), 'error');
                    $this->Log->logActivity( sprintf(__('Berhasil %s LKU #%s'), $msg, $lku_id), $this->user_data, $this->RequestHandler, $this->params, 1 );
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s LKU. Lengkapi field yang dibutuhkan'), $msg), 'error');
            }
        } else if($id && $data_local){
            $data_local = $this->Lku->LkuDetail->getMerge($data_local, $id);
            $this->request->data = $data_local;

            if(!empty($this->request->data['LkuDetail'])){
                foreach ($this->request->data['LkuDetail'] as $key => $value) {
                    $tipe_motor = $this->TipeMotor->getData('first', array(
                        'conditions' => array(
                            'TipeMotor.id' => $value['LkuDetail']['tipe_motor_id']
                        ),
                    ));
                    
                    if(!empty($tipe_motor)){
                        $Ttuj_Tipe_Motor = $this->TtujTipeMotor->getData('first', array(
                            'conditions' => array(
                                'TtujTipeMotor.ttuj_id' => $this->request->data['Lku']['ttuj_id'],
                                'TtujTipeMotor.tipe_motor_id' => $value['LkuDetail']['tipe_motor_id']
                            )
                        ));
                        $this->request->data['LkuDetail'][$key] = $value['LkuDetail'];
                        $this->request->data['LkuDetail'][$key]['TipeMotor'] = array_merge($tipe_motor['TipeMotor'], $Ttuj_Tipe_Motor);
                        $this->request->data['LkuDetail'][$key]['TipeMotor'] = array_merge($tipe_motor['TipeMotor'], $Ttuj_Tipe_Motor);
                        $this->request->data['LkuDetail'][$key]['ColorMotor'] = !empty($Ttuj_Tipe_Motor['ColorMotor'])?$Ttuj_Tipe_Motor['ColorMotor']:false;
                    }
                }
            }

            $this->request->data['Lku']['tgl_lku'] = (!empty($this->request->data['Lku']['tgl_lku'])) ? $this->MkCommon->getDate($this->request->data['Lku']['tgl_lku'], true) : '';
        }

        if(!empty($this->request->data['LkuDetail']['tipe_motor_id'])){
            $temp = array();
            $idx = 1;

            foreach ($this->request->data['LkuDetail']['tipe_motor_id'] as $key => $value) {
                if( $idx != count($this->request->data['LkuDetail']['tipe_motor_id']) ){
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
                        $Ttuj_Tipe_Motor = $this->TtujTipeMotor->getData('first', array(
                            'conditions' => array(
                                'TtujTipeMotor.ttuj_id' => $this->request->data['Lku']['ttuj_id'],
                                'TtujTipeMotor.tipe_motor_id' => $value
                            )
                        ));
                        $temp['LkuDetail'][$key]['TipeMotor'] = array_merge($tipe_motor['TipeMotor'], $Ttuj_Tipe_Motor);
                        $temp['LkuDetail'][$key]['ColorMotor'] = !empty($Ttuj_Tipe_Motor['ColorMotor'])?$Ttuj_Tipe_Motor['ColorMotor']:false;
                    }
                }

                $idx++;
            }

            unset($this->request->data['LkuDetail']);

            if( !empty($temp['LkuDetail']) ) {
                $this->request->data['LkuDetail'] = $temp['LkuDetail'];
            }
        }

        $ttujs = $this->Ttuj->getData('list', array(
            'fields' => array(
                'Ttuj.id', 'Ttuj.no_ttuj'
            ),
            'conditions' => array(
                'Ttuj.is_draft' => 0,
                'Ttuj.is_laka' => 0,
                'OR' => array(
                    'Ttuj.is_bongkaran' => 1,
                    'Ttuj.id' => !empty($data_local['Lku']['ttuj_id']) ? $data_local['Lku']['ttuj_id'] : false,
                ),
            ),
        ));

        if(!empty($this->request->data['Lku']['ttuj_id'])){
            $ttuj_id = $this->request->data['Lku']['ttuj_id'];
            $data_ttuj = $this->Ttuj->getData('first', array(
                'conditions' => array(
                    'Ttuj.id' => $this->request->data['Lku']['ttuj_id']
                ),
                'contain' => array(
                    'UangJalan'
                )
            ), true, array(
                'status' => 'all',
            ));
            
            if(!empty($data_ttuj)){
                $this->loadModel('Driver');

                $tipe_motor_list = array();
                $driver_penganti_id = !empty($data_ttuj['Ttuj']['driver_penganti_id'])?$data_ttuj['Ttuj']['driver_penganti_id']:false;
                $data_ttuj = $this->TtujTipeMotor->getMergeTtujTipeMotor( $data_ttuj, $ttuj_id, 'all');
                $data_ttuj = $this->Driver->getMerge( $data_ttuj, $driver_penganti_id, 'DriverPenganti');

                if(!empty($data_ttuj['TtujTipeMotor'])){
                    foreach ($data_ttuj['TtujTipeMotor'] as $key => $value) {
                        $group_motor_id = !empty($value['TipeMotor']['group_motor_id'])?$value['TipeMotor']['group_motor_id']:false;
                        $groupMotor = $this->TipeMotor->GroupMotor->getData('first', array(
                            'conditions' => array(
                                'GroupMotor.id' => $group_motor_id,
                            ),
                        ), array(
                            'status' => 'all',
                        ));
                        $group_motor_name = !empty($groupMotor['GroupMotor']['name'])?$groupMotor['GroupMotor']['name']:false;
                        $tipe_motor_list[$value['TipeMotor']['id']] = sprintf('%s (%s)', $value['TipeMotor']['name'], $group_motor_name);
                    }
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

        $this->set('active_menu', 'lkus');
        $this->set(compact(
            'part_motors', 'tipe_motor_list',
            'ttujs', 'id'
        ));
        $this->render('lku_form');
    }

    function toggle($id, $action='inactive'){
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
        ), false);

        if($locale){
            $this->Lku->id = $id;

            $value = 0;
            if($status == 'activate'){
                $value = 1;
            }
            $this->Lku->set('status', $value);

            if($this->Lku->save()){
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
    }

    function payments() {
        $this->loadModel('LkuPayment');
        $this->loadModel('Customer');

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

            if(!empty($refine['customer'])){
                $customer = urldecode($refine['customer']);
                $this->request->data['Lku']['customer_id'] = $customer;
                $conditions['LkuPayment.customer_id'] = $customer;
            }
        }

        $this->paginate = $this->LkuPayment->getData('paginate', array(
            'conditions' => $conditions,
            'order' => array(
                'LkuPayment.created' => 'DESC'
            )
        ), true, array(
            'status' => 'all',
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

        $customers = $this->Customer->getData('list', array(
            'fields' => array(
                'Customer.id', 'Customer.customer_name_code'
            ),
        ));
        $this->set('customers', $customers);
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
            $this->MkCommon->setCustomFlash(__('ID Pembayaran LKU tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'Lkus',
                'action' => 'payments'
            ));
        }
    }

    function DoLkuPayment($id = false, $data_local = false){
        $this->loadModel('Lku');
        $this->loadModel('Ttuj');
        $this->loadModel('Customer');
        $this->loadModel('LkuPayment');

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
            $data['LkuPayment']['group_branch_id'] = Configure::read('__Site.config_branch_id');
            $total_price = 0;

            $validate_lku_detail = true;
            $validate_price_pay = true;
            if(!empty($data['LkuPaymentDetail']['lku_detail_id'])){
                $this->loadModel('LkuPaymentDetail');
                foreach ($data['LkuPaymentDetail']['lku_detail_id'] as $key => $value) {
                    if(!empty($value)){
                        $price = (!empty($data['LkuPaymentDetail']['total_biaya_klaim'][$key])) ? $this->MkCommon->convertPriceToString($data['LkuPaymentDetail']['total_biaya_klaim'][$key]) : 0;
                        $data_detail['LkuPaymentDetail'] = array(
                            'lku_detail_id' => $value,
                            'total_biaya_klaim' => $price
                        );

                        if(empty($price) || empty($data['LkuPaymentDetail']['total_biaya_klaim'][$value])){
                            $validate_lku_detail = false;
                            break;
                        }else{
                            $lku_has_paid = $this->LkuPaymentDetail->getData('first', array(
                                'conditions' => array(
                                    'LkuPaymentDetail.lku_detail_id' => $value,
                                    'LkuPayment.status' => 1,
                                    'LkuPayment.is_void' => 0,
                                ),
                                'fields' => array(
                                    'SUM(LkuPaymentDetail.total_biaya_klaim) as lku_has_paid'
                                ),
                                'contain' => array(
                                    'LkuPayment'
                                ),
                            ));

                            $lku_has_paid = (!empty($lku_has_paid[0]['lku_has_paid'])) ? $lku_has_paid[0]['lku_has_paid'] : 0;
                            $total_paid = $lku_has_paid + $price;

                            $lku_data = $this->Lku->LkuDetail->getData('first', array(
                                'conditions' => array(
                                    'LkuDetail.id' => $value
                                )
                            ));
                            
                            if(!empty($lku_data)){
                                if($total_paid > $lku_data['LkuDetail']['total_price']){
                                    $validate_price_pay = false;
                                    break;
                                }else{
                                    $data['LkuPaymentDetail']['total_biaya_klaim'][$key] = $price;
                                    $total_price += $price;
                                }
                            }
                        }
                    }
                }
            }else{
                $validate_lku_detail = false;
            }

            $temptotal = $total_price;
            $data['LkuPayment']['grandtotal'] = $total_price;

            if(!empty($data['LkuPayment']['pph'])){
                $temptotal -= $total_price*($data['LkuPayment']['pph']/100);
            }
            if(!empty($data['LkuPayment']['ppn'])){
                $temptotal += $total_price*($data['LkuPayment']['ppn']/100);
            }

            $total = $temptotal;
            $data['LkuPayment']['grand_total_payment'] = $total;
            $data['LkuPayment']['paid'] = 1;

            $this->LkuPayment->set($data);

            if($this->LkuPayment->validates($data) && $validate_lku_detail && $validate_price_pay){
                if($this->LkuPayment->save($data)){
                    $lku_payment_id = $this->LkuPayment->id;

                    if( !empty($total_price) ) {
                        $this->loadModel('Journal');
                        $document_no = !empty($data['LkuPayment']['no_doc'])?$data['LkuPayment']['no_doc']:false;
                        $this->Journal->setJournal( $lku_payment_id, $document_no, 'lku_payment_coa_credit_id', 0, $total_price, 'lku_payment' );
                        $this->Journal->setJournal( $lku_payment_id, $document_no, 'lku_payment_coa_debit_id', $total_price, 0, 'lku_payment' );
                    }

                    if($id && $data_local){
                        $this->LkuPayment->LkuPaymentDetail->deleteAll(array(
                            'LkuPaymentDetail.lku_payment_id' => $lku_payment_id
                        ));
                    }
                
                    if( !empty($data['LkuPaymentDetail']['total_biaya_klaim']) ) {
                        $collect_lku_detail_id = array();
                        foreach ($data['LkuPaymentDetail']['total_biaya_klaim'] as $key => $value) {
                            if(!empty($data['LkuPaymentDetail']['lku_detail_id'][$key])){
                                $lku_detail_id = $data['LkuPaymentDetail']['lku_detail_id'][$key];
                                array_push($collect_lku_detail_id, $lku_detail_id);

                                $this->LkuPayment->LkuPaymentDetail->create();
                                $this->LkuPayment->LkuPaymentDetail->set(array(
                                    'total_biaya_klaim' => trim($value),
                                    'lku_detail_id' => $lku_detail_id,
                                    'lku_payment_id' => $lku_payment_id,
                                    'status' => 1
                                ));
                                $this->LkuPayment->LkuPaymentDetail->save();

                                $default_conditions_detail = array(
                                    'LkuPaymentDetail.lku_detail_id' => $lku_detail_id,
                                    'LkuPaymentDetail.status' => 1
                                );

                                $lku_has_paid = $this->LkuPayment->LkuPaymentDetail->getData('first', array(
                                    'conditions' => $default_conditions_detail,
                                    'fields' => array(
                                        '*',
                                        'SUM(LkuPaymentDetail.total_biaya_klaim) as lku_has_paid'
                                    ),
                                    'contain' => array(
                                        'LkuDetail'
                                    )
                                ));
                                
                                $invoice_paid = !empty($lku_has_paid[0]['lku_has_paid'])?$lku_has_paid[0]['lku_has_paid']:0;
                                $invoice_total = !empty($lku_has_paid['LkuDetail']['total_price'])?$lku_has_paid['LkuDetail']['total_price']:0;
                                
                                if($invoice_paid >= $invoice_total){
                                    $this->Lku->LkuDetail->id = $lku_detail_id;
                                    $this->Lku->LkuDetail->set(array(
                                        'paid' => 1,
                                        'complete_paid' => 1
                                    ));
                                    $this->Lku->LkuDetail->save();
                                }else{
                                    $this->Lku->LkuDetail->id = $lku_detail_id;
                                    $this->Lku->LkuDetail->set(array(
                                        'paid' => 1,
                                        'complete_paid' => 0
                                    ));
                                    $this->Lku->LkuDetail->save();
                                }
                            }
                        }

                        if(!empty($collect_lku_detail_id)){
                            $this->updateStatusLku($collect_lku_detail_id);
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
                $text = sprintf(__('Gagal %s Pembayaran Invoice'), $msg);

                if( !$validate_lku_detail ){
                    $text .= ', mohon isi field pembayaran';
                }
                if(!$validate_price_pay){
                    $text .= ', Total Pembayaran tidak boleh lebih besar dari total pembayaran per unit LKU';
                }

                $this->MkCommon->setCustomFlash($text, 'error');
            }
        } else if($id && $data_local){
            $this->request->data = $data_local;
            $this->request->data['LkuPayment']['tgl_bayar'] = (!empty($this->request->data['LkuPayment']['tgl_bayar'])) ? $this->MkCommon->getDate($this->request->data['LkuPayment']['tgl_bayar'], true) : '';
        }

        if(!empty($this->request->data['LkuPaymentDetail']['lku_detail_id'])){
            $temp['LkuPaymentDetail'] = array();
            foreach ($this->request->data['LkuPaymentDetail']['lku_detail_id'] as $key => $value) {
                if( !empty($value) ){
                    $temp['LkuPaymentDetail'][$key] = array(
                        'lku_detail_id' => $value,
                        'total_klaim' => (!empty($data['LkuPaymentDetail']['total_klaim'][$key])) ? $data['LkuPaymentDetail']['total_klaim'][$key] : '',
                        'total_biaya_klaim' => (!empty($data['LkuPaymentDetail']['total_biaya_klaim'][$key])) ? $data['LkuPaymentDetail']['total_biaya_klaim'][$key] : '',
                    );
                }
            }

            unset($this->request->data['LkuPaymentDetail']);
            $this->request->data['LkuPaymentDetail'] = $temp['LkuPaymentDetail'];
        }

        if(!empty($this->request->data['LkuPaymentDetail'])){
            $lku_details = array();
            foreach ($this->request->data['LkuPaymentDetail'] as $key => $value) {
                if(!empty($value['lku_detail_id'])){
                    $lku_condition = array(
                        'LkuDetail.id' => $value['lku_detail_id'],
                        'LkuDetail.status' => 1,
                        'LkuDetail.complete_paid' => 0
                    );

                    $lku_data = $this->Lku->LkuDetail->getData('first', array(
                        'conditions' => $lku_condition,
                        'contain' => array(
                            'Lku',
                            'TipeMotor',
                            'PartsMotor'
                        )
                    ));
                    
                    if(!empty($lku_data)){
                        $ttuj = $this->Ttuj->getData('first', array(
                            'conditions' => array(
                                'Ttuj.id' => $lku_data['Lku']['ttuj_id']
                            ),
                        ), true, array(
                            'status' => 'all',
                        ));

                        if(!empty($ttuj['Ttuj'])){
                            $lku_data['Ttuj'] = $ttuj['Ttuj'];
                        }

                        $lku_has_paid = $this->LkuPayment->LkuPaymentDetail->getData('first', array(
                            'conditions' => array(
                                'LkuPaymentDetail.lku_detail_id' => $lku_data['LkuDetail']['id'],
                                'LkuPaymentDetail.status' => 1
                            ),
                            'fields' => array(
                                'SUM(LkuPaymentDetail.total_biaya_klaim) as lku_has_paid'
                            )
                        ));

                        $lku_details[$key]['lku_has_paid'] = $lku_has_paid[0]['lku_has_paid'];
                        $lku_details[$key] = array_merge($lku_details[$key], $lku_data);
                    }
                }
            }
            
            $this->set(compact('lku_details'));
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
                        'Lku.complete_paid' => 0,
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
        ), true, array(
            'status' => 'all',
        ));
        $ttujs = array();

        if(!empty($customers)){
            $list_customer = array();
            foreach ($customers as $key => $value) {
                $customer_id = !empty($value['Ttuj']['customer_id'])?$value['Ttuj']['customer_id']:false;
                $customer_name = !empty($value['Ttuj']['customer_name'])?$value['Ttuj']['customer_name']:false;
                $customer_code = !empty($value['Ttuj']['CustomerNoType']['code'])?$value['Ttuj']['CustomerNoType']['code']:false;

                $list_customer[$customer_id] = sprintf('%s - %s', $customer_name, $customer_code);
                $customers = $list_customer;

                $dataCust = $this->Ttuj->Customer->getData('first', array(
                    'conditions' => array(
                        'Customer.id' => $customer_id,
                    ),
                ), true, array(
                    'status' => 'all',
                ));

                if( !empty($dataCust) ) {
                    $ttujs[$customer_id] = $dataCust['Customer']['customer_name_code'];
                }
            }
        }

        $this->set('active_menu', 'lku_payments');
        $this->set(compact(
            'list_customer', 'id', 'action',
            'coas', 'ttujs'
        ));
        $this->render('lku_payment_form');
    }

    // public function lku_parts() {
    //     $this->loadModel('LkuPart');
    //     $this->set('active_menu', 'lku_parts');
    //     $this->set('sub_module_title', __('Data LKU Parts'));
    //     $conditions = array();
        
    //     if(!empty($this->params['named'])){
    //         $refine = $this->params['named'];

    //         if(!empty($refine['nodoc'])){
    //             $no_doc = urldecode($refine['nodoc']);
    //             $this->request->data['LkuPart']['no_doc'] = $no_doc;
    //             $conditions['LkuPart.no_doc LIKE '] = '%'.$no_doc.'%';
    //         }
    //     }

    //     $this->paginate = $this->LkuPart->getData('paginate', array(
    //         'conditions' => $conditions
    //     ));
    //     $LkuParts = $this->paginate('LkuPart');

    //     $this->set('LkuParts', $LkuParts);
    // }

    // function lku_part_detail($id = false){
    //     if(!empty($id)){
    //         $LkuPart = $this->LkuPart->getLkuPart($id);

    //         if(!empty($LkuPart)){
    //             $sub_module_title = __('Detail LKU Part');
    //             $this->set(compact('LkuPart', 'sub_module_title'));
    //         }else{
    //             $this->MkCommon->setCustomFlash(__('Lku Part tidak ditemukan.'), 'error');
    //             $this->redirect($this->referer());
    //         }
    //     }else{
    //         $this->MkCommon->setCustomFlash(__('Lku Part tidak ditemukan.'), 'error');
    //         $this->redirect($this->referer());
    //     }
    // }

    // function lku_part_add(){
    //     $this->set('sub_module_title', __('Tambah LKU Part'));
    //     $this->DoLkuPart();
    // }

    // function lku_part_edit($id){
    //     $this->loadModel('LkuPart');
    //     $this->set('sub_module_title', 'Rubah LKU Part');
    //     $LkuPart = $this->LkuPart->getData('first', array(
    //         'conditions' => array(
    //             'LkuPart.id' => $id
    //         ),
    //     ));

    //     if(!empty($LkuPart)){
    //         $this->DoLkuPart($id, $LkuPart);
    //     }else{
    //         $this->MkCommon->setCustomFlash(__('LKU Part tidak ditemukan'), 'error');  
    //         $this->redirect(array(
    //             'controller' => 'lkus',
    //             'action' => 'lku_parts'
    //         ));
    //     }
    // }

    // function DoLkuPart($id = false, $data_local = false){
    //     $this->loadModel('Ttuj');
    //     $this->loadModel('PartsMotor');

    //     if(!empty($this->request->data)){
    //         $data = $this->request->data;

    //         if($id && $data_local){
    //             $this->LkuPart->id = $id;
    //             $msg = 'merubah';
    //         }else{
    //             $this->loadModel('LkuPart');
    //             $this->LkuPart->create();
    //             $msg = 'menambah';
    //         }
            
    //         $data['LkuPart']['tgl_lku'] = (!empty($data['LkuPart']['tgl_lku'])) ? $this->MkCommon->getDate($data['LkuPart']['tgl_lku']) : '';
            
    //         $validate_lku_detail = true;
    //         $temp_detail = array();
    //         $total_price = 0;
    //         $total_klaim = 0;
    //         if(!empty($data['LkuPartDetail']['parts_motor_id'])){
    //             foreach ($data['LkuPartDetail']['parts_motor_id'] as $key => $value) {
    //                 if( !empty($value) ){
    //                     $data_detail['LkuPartDetail'] = array(
    //                         'parts_motor_id' => $value,
    //                         'qty' => (!empty($data['LkuPartDetail']['qty'][$key])) ? $data['LkuPartDetail']['qty'][$key] : '',
    //                         'price' => (!empty($data['LkuPartDetail']['price'][$key])) ? $data['LkuPartDetail']['price'][$key] : '',
    //                     );
                        
    //                     $temp_detail[] = $data_detail;
    //                     $this->LkuPart->LkuPartDetail->set($data_detail);
    //                     if( !$this->LkuPart->LkuPartDetail->validates() ){
    //                         $validate_lku_detail = false;
    //                         break;
    //                     }else{
    //                         $total_price += $data_detail['LkuPartDetail']['qty'] * $data_detail['LkuPartDetail']['price'];
    //                         $total_klaim += $data_detail['LkuPartDetail']['qty'];
    //                     }
    //                 }
    //             }
    //         }else{
    //             $validate_lku_detail = false;
    //         }
            
    //         $data['LkuPart']['total_price'] = $total_price;
    //         $data['LkuPart']['total_klaim'] = $total_klaim;
    //         $this->LkuPart->set($data);

    //         if($this->LkuPart->validates($data) && $validate_lku_detail){
    //             if($this->LkuPart->save($data)){
    //                 $lku_part_id = $this->LkuPart->id;

    //                 if($id && $data_local){
    //                     $this->LkuPart->LkuPartDetail->deleteAll(array(
    //                         'LkuPartDetail.lku_part_id' => $lku_part_id
    //                     ));
    //                 }

    //                 foreach ($temp_detail as $key => $value) {
    //                     $this->LkuPart->LkuPartDetail->create();
    //                     $value['LkuPartDetail']['lku_part_id'] = $lku_part_id;

    //                     $this->LkuPart->LkuPartDetail->set($value);
    //                     $this->LkuPart->LkuPartDetail->save();
    //                 }

    //                 $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s LKU Part'), $msg), 'success');
    //                 $this->Log->logActivity( sprintf(__('Berhasil %s LKU Part #%s'), $msg, $lku_part_id), $this->user_data, $this->RequestHandler, $this->params );

    //                 $this->redirect(array(
    //                     'controller' => 'LkuParts',
    //                     'action' => 'index',
    //                 ));
    //             }else{
    //                 $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s LKU Part'), $msg), 'error');
    //                 $this->Log->logActivity( sprintf(__('Berhasil %s LKU Part #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1 );
    //             }
    //         }else{
    //             $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s LKU Part'), $msg), 'error');
    //         }
    //     } else if($id && $data_local){
    //         $this->request->data = $data_local;

    //         if(!empty($this->request->data['LkuPartDetail'])){
    //             foreach ($this->request->data['LkuPartDetail'] as $key => $value) {
    //                 $tipe_motor = $this->PartsMotor->getData('first', array(
    //                     'conditions' => array(
    //                         'PartsMotor.id' => $value['parts_motor_id']
    //                     ),
    //                 ));
    //                 if(!empty($tipe_motor)){
    //                     $Ttuj_Tipe_Motor = $this->Ttuj->TtujPartsMotor->getData('first', array(
    //                         'conditions' => array(
    //                             'TtujPartsMotor.ttuj_id' => $this->request->data['LkuPart']['ttuj_id'],
    //                             'TtujPartsMotor.parts_motor_id' => $value['parts_motor_id']
    //                         )
    //                     ));
    //                     $this->request->data['LkuPartDetail'][$key]['PartsMotor'] = array_merge($tipe_motor['PartsMotor'], $Ttuj_Tipe_Motor);
    //                 }
    //             }
    //         }

    //         $this->request->data['LkuPart']['tgl_lku'] = (!empty($this->request->data['LkuPart']['tgl_lku'])) ? $this->MkCommon->getDate($this->request->data['LkuPart']['tgl_lku'], true) : '';
    //     }

    //     if(!empty($this->request->data['LkuPartDetail']['parts_motor_id'])){
    //         $temp = array();
    //         foreach ($this->request->data['LkuPartDetail']['parts_motor_id'] as $key => $value) {
    //             if( !empty($value) ){
    //                 $temp['LkuPartDetail'][$key] = array(
    //                     'parts_motor_id' => $value,
    //                     'qty' => (!empty($data['LkuPartDetail']['qty'][$key])) ? $data['LkuPartDetail']['qty'][$key] : '',
    //                     'price' => (!empty($data['LkuPartDetail']['price'][$key])) ? $data['LkuPartDetail']['price'][$key] : '',
    //                 );

    //                 $tipe_motor = $this->PartsMotor->getData('first', array(
    //                     'conditions' => array(
    //                         'PartsMotor.id' => $value
    //                     )
    //                 ));
    //                 if(!empty($tipe_motor)){
    //                     $Ttuj_Tipe_Motor = $this->Ttuj->TtujPartsMotor->getData('first', array(
    //                         'conditions' => array(
    //                             'TtujPartsMotor.ttuj_id' => $this->request->data['LkuPart']['ttuj_id'],
    //                             'TtujPartsMotor.parts_motor_id' => $value
    //                         )
    //                     ));
    //                     $temp['LkuPartDetail'][$key]['PartsMotor'] = array_merge($tipe_motor['PartsMotor'], $Ttuj_Tipe_Motor);
    //                 }
    //             }
    //         }

    //         unset($this->request->data['LkuPartDetail']);
    //         $this->request->data['LkuPartDetail'] = $temp['LkuPartDetail'];
    //     }

    //     $ttujs = $this->Ttuj->getData('list', array(
    //         'fields' => array(
    //             'Ttuj.id', 'Ttuj.no_ttuj'
    //         ),
    //         'conditions' => array(
    //             'Ttuj.is_pool' => 1,
    //             'Ttuj.is_draft' => 0,
    //         ),
    //     ));

    //     if(!empty($this->request->data['LkuPart']['ttuj_id'])){
    //         $data_ttuj = $this->Ttuj->getData('first', array(
    //             'conditions' => array(
    //                 'Ttuj.id' => $this->request->data['LkuPart']['ttuj_id']
    //             ),
    //         ));
            
    //         if(!empty($data_ttuj)){
    //             if(!empty($data_ttuj['TtujPartsMotor'])){
    //                 $tipe_motor_list = array();
    //                 foreach ($data_ttuj['TtujPartsMotor'] as $key => $value) {
    //                     $tipe_motor = $this->PartsMotor->getData('first', array(
    //                         'conditions' => array(
    //                             'PartsMotor.id' => $value['parts_motor_id']
    //                         )
    //                     ));
    //                     $tipe_motor_list[$tipe_motor['PartsMotor']['id']] = $tipe_motor['PartsMotor']['name'];
    //                 }
    //                 $this->set('tipe_motor_list', $tipe_motor_list);
    //             }
    //             $this->request->data = array_merge($this->request->data, $data_ttuj);
    //         }
            
    //     }

    //     $this->set('active_menu', 'lku_parts');
    //     $this->set('ttujs', $ttujs);
    //     $this->set('id', $id);
    //     $this->render('lku_form');
    // }

    // function lku_part_toggle($id){
    //     $this->loadModel('LkuPart');
    //     $locale = $this->LkuPart->getData('first', array(
    //         'conditions' => array(
    //             'LkuPart.id' => $id
    //         )
    //     ));

    //     if($locale){
    //         $value = true;
    //         if($locale['LkuPart']['status']){
    //             $value = false;
    //         }

    //         $this->LkuPart->id = $id;
    //         $this->LkuPart->set('status', 0);

    //         if($this->LkuPart->save()){
    //             $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
    //             $this->Log->logActivity( sprintf(__('Sukses merubah status LKU Part ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params );
    //         }else{
    //             $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
    //             $this->Log->logActivity( sprintf(__('Gagal merubah status LKU Part ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );
    //         }
    //     }else{
    //         $this->MkCommon->setCustomFlash(__('LKU Part tidak ditemukan.'), 'error');
    //     }

    //     $this->redirect($this->referer());
    // }

    public function ksus() {
        $this->loadModel('Ksu');
        $this->loadModel('Customer');

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

            if(!empty($refine['from'])){
                $from = urldecode($refine['from']);
                $this->request->data['Ksu']['from_date'] = $this->MkCommon->getDate($from, true);
                $conditions['DATE_FORMAT(Ksu.tgl_ksu, \'%Y-%m-%d\') >='] = $from;
            }

            if(!empty($refine['to'])){
                $to = urldecode($refine['to']);
                $this->request->data['Ksu']['to_date'] = $this->MkCommon->getDate($to, true);
                $conditions['DATE_FORMAT(Ksu.tgl_ksu, \'%Y-%m-%d\') <='] = $to;
            }

            if(!empty($refine['no_ttuj'])){
                $no_ttuj = urldecode($refine['no_ttuj']);
                $this->request->data['Ksu']['no_ttuj'] = $no_ttuj;
                $conditions['Ttuj.no_ttuj LIKE '] = '%'.$no_ttuj.'%';
            }

            if(!empty($refine['customer'])){
                $customer = urldecode($refine['customer']);
                $this->request->data['Ksu']['customer_id'] = $customer;
                $conditions['Ttuj.customer_id'] = $customer;
            }
        }

        $this->paginate = $this->Ksu->getData('paginate', array(
            'conditions' => $conditions,
            'contain' => array(
                'Ttuj'
            )
        ), true, array(
            'status' => 'all',
        ));
        $Ksus = $this->paginate('Ksu');

        if(!empty($Ksus)){
            foreach ($Ksus as $key => $value) {
                $customer_data['Customer'] = array();
                
                if(!empty($value['Ttuj']['customer_id'])){
                    $customer_data = $this->Customer->getData('first', array(
                        'conditions' => array(
                            'Customer.id' => $value['Ttuj']['customer_id']
                        )
                    ), true, array(
                        'status' => 'all',
                    ));
                }

                $Ksus[$key]['Customer'] = $customer_data['Customer'];
            }
        }

        $customers = $this->Customer->getData('list', array(
            'fields' => array(
                'Customer.id', 'Customer.customer_name_code'
            ),
        ));
        $this->set(compact(
            'Ksus', 'customers'
        ));
    }

    function detail_ksu($id = false){
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
    }

    function ksu_add(){
        $this->set('sub_module_title', __('Tambah KSU'));
        $this->DoKsu();
    }

    function ksu_edit($id){
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
    }

    function DoKsu($id = false, $data_local = false){
        $this->loadModel('Ttuj');
        $this->loadModel('Perlengkapan');
        $this->loadModel('TtujPerlengkapan');

        if(!empty($this->request->data)){
            $data = $this->request->data;
            $data['Ksu']['group_branch_id'] = Configure::read('__Site.config_branch_id');
            
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
                        $price = (!empty($data['KsuDetail']['price'][$key])) ? str_replace(',', '', trim($data['KsuDetail']['price'][$key])) : 0;
                        $qty = (!empty($data['KsuDetail']['qty'][$key])) ? $data['KsuDetail']['qty'][$key] : 0;
                        $total = $price * $qty;

                        $data_detail = array( 
                            'KsuDetail' => array(
                                'no_rangka' => (!empty($data['KsuDetail']['no_rangka'][$key])) ? $data['KsuDetail']['no_rangka'][$key] : '',
                                'qty' => $qty,
                                'price' => $price,
                                'total_price' => $total,
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
                    $ksu_id = $this->Ksu->id;

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
            $data_local = $this->Ksu->KsuDetail->getMerge($data_local, $id);
            $this->request->data = $data_local;

            if(!empty($this->request->data['KsuDetail'])){
                foreach ($this->request->data['KsuDetail'] as $key => $value) {
                    $perlengkapan = $this->Perlengkapan->getData('first', array(
                        'conditions' => array(
                            'Perlengkapan.id' => $value['KsuDetail']['perlengkapan_id']
                        ),
                    ));
                    if(!empty($perlengkapan)){
                        $Ttuj_perlengkapan = $this->TtujPerlengkapan->getData('first', array(
                            'conditions' => array(
                                'TtujPerlengkapan.ttuj_id' => $this->request->data['Ksu']['ttuj_id'],
                                'TtujPerlengkapan.perlengkapan_id' => $value['KsuDetail']['perlengkapan_id']
                            )
                        ));
                        $this->request->data['KsuDetail'][$key] = $value['KsuDetail'];
                        $this->request->data['KsuDetail'][$key]['Perlengkapan'] = array_merge($perlengkapan['Perlengkapan'], $Ttuj_perlengkapan);
                    }
                }
            }

            $this->request->data['Ksu']['tgl_ksu'] = (!empty($this->request->data['Ksu']['tgl_ksu'])) ? $this->MkCommon->getDate($this->request->data['Ksu']['tgl_ksu'], true) : '';
            $this->request->data['Ksu']['date_atpm'] = (!empty($this->request->data['Ksu']['date_atpm'])) ? $this->MkCommon->getDate($this->request->data['Ksu']['date_atpm'], true) : '';
        }

        if(!empty($this->request->data['KsuDetail']['perlengkapan_id'])){
            $temp = array();
            $idx = 1;

            foreach ($this->request->data['KsuDetail']['perlengkapan_id'] as $key => $value) {
                if( $idx != count($this->request->data['KsuDetail']['perlengkapan_id']) ){
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
                        $Ttuj_perlengkapan = $this->TtujPerlengkapan->getData('first', array(
                            'conditions' => array(
                                'TtujPerlengkapan.ttuj_id' => $this->request->data['Ksu']['ttuj_id'],
                                'TtujPerlengkapan.perlengkapan_id' => $value
                            )
                        ));
                        $temp['KsuDetail'][$key]['Perlengkapan'] = array_merge($perlengkapan['Perlengkapan'], $Ttuj_perlengkapan);
                    }
                }
                
                $idx++;
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
        ), true, array(
            'status' => 'all',
        ));

        if(!empty($this->request->data['Ksu']['ttuj_id'])){
            $ttuj_id = $this->request->data['Ksu']['ttuj_id'];
            $data_ttuj = $this->Ttuj->getData('first', array(
                'conditions' => array(
                    'Ttuj.id' => $ttuj_id,
                ),
                'contain' => array(
                    'UangJalan',
                )
            ), true, array(
                'status' => 'all',
            ));
            
            if(!empty($data_ttuj)){
                $this->loadModel('TtujPerlengkapan');
                $data_ttuj = $this->TtujPerlengkapan->getMerge($data_ttuj, $ttuj_id);
                $perlengkapan_list = array();

                if(!empty($data_ttuj['TtujPerlengkapan'])){
                    foreach ($data_ttuj['TtujPerlengkapan'] as $key => $value) {
                        $perlengkapan_data = $this->Perlengkapan->getData('first', array(
                            'conditions' => array(
                                'Perlengkapan.id' => $value['TtujPerlengkapan']['perlengkapan_id'],
                            )
                        ));
                
                        if( !empty($perlengkapan_data) ) {
                            $perlengkapan_list[$perlengkapan_data['Perlengkapan']['id']] = $perlengkapan_data['Perlengkapan']['name'];
                        }
                    }
                }
                $this->request->data = array_merge($this->request->data, $data_ttuj);
            }
        }

        $perlengkapans = $this->Perlengkapan->getListPerlengkapan(2);
        $this->set('active_menu', 'ksus');
        $this->set(compact(
            'perlengkapans', 'perlengkapan_list', 'ttujs',
            'id'
        ));
        $this->render('ksu_form');
    }

    function ksu_toggle($id, $action = 'inactive'){
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
        ), false);

        if($locale){
            $value = 0;
            if($status == 'activate'){
                $value = 1;
            }

            $this->Ksu->id = $id;
            $this->Ksu->set('status', $value);

            if($this->Ksu->save()){
                $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
                $this->Log->logActivity( sprintf(__('Sukses merubah status KSU ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params );
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal merubah status KSU ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Ksu tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    function ksu_payments() {
        $this->loadModel('KsuPayment');
        $this->loadModel('Customer');

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

            if(!empty($refine['customer'])){
                $customer = urldecode($refine['customer']);
                $this->request->data['Lku']['customer_id'] = $customer;
                $conditions['KsuPayment.customer_id'] = $customer;
            }
        }

        $this->paginate = $this->KsuPayment->getData('paginate', array(
            'conditions' => $conditions,
        ), true, array(
            'status' => 'all',
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

        $customers = $this->Customer->getData('list', array(
            'fields' => array(
                'Customer.id', 'Customer.customer_name_code'
            ),
        ));
        $this->set('customers', $customers);
    }

    function ksu_payment_add(){
        $this->set('sub_module_title', __('Tambah Pembayaran KSU'));
        $this->DoKsuPayment();
    }

    function ksu_payment_edit($id){
        $this->loadModel('KsuPayment');
        $this->set('sub_module_title', 'Rubah Pembayaran KSU');
        $Ksu = $this->KsuPayment->getData('first', array(
            'conditions' => array(
                'KsuPayment.id' => $id,
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
    }

    function DoKsuPayment($id = false, $data_local = false){
        $this->loadModel('Ksu');
        $this->loadModel('Ttuj');
        $this->loadModel('Customer');
        $this->loadModel('KsuPayment');

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
            $data['KsuPayment']['group_branch_id'] = Configure::read('__Site.config_branch_id');
            $total_price = 0;

            $validate_ksu_detail = true;
            $validate_price_pay = true;
            if(!empty($data['KsuPaymentDetail']['ksu_detail_id'])){
                $this->loadModel('KsuPaymentDetail');
                foreach ($data['KsuPaymentDetail']['ksu_detail_id'] as $key => $value) {
                    if(!empty($value)){
                        $price = (!empty($data['KsuPaymentDetail']['total_biaya_klaim'][$key])) ? $this->MkCommon->convertPriceToString($data['KsuPaymentDetail']['total_biaya_klaim'][$key]) : 0;
                        $data_detail['KsuPaymentDetail'] = array(
                            'ksu_detail_id' => $value,
                            'total_biaya_klaim' => $price
                        );
                        
                        // $temp_detail[] = $data_detail;
                        // $this->KsuPayment->KsuPaymentDetail->set($data_detail);
                        // if( !$this->KsuPayment->KsuPaymentDetail->validates() ){
                        //     $validate_ksu_detail = false;
                        //     break;
                        // }else{
                        //     $total_price += $data_detail['KsuPaymentDetail']['total_biaya_klaim'];
                        // }

                        if(empty($price) || empty($data['KsuPaymentDetail']['total_biaya_klaim'][$value])){
                            $validate_ksu_detail = false;
                            break;
                        }else{
                            $ksu_has_paid = $this->KsuPaymentDetail->getData('first', array(
                                'conditions' => array(
                                    'KsuPaymentDetail.ksu_detail_id' => $value,
                                    'KsuPayment.status' => 1,
                                    'KsuPayment.is_void' => 0,
                                ),
                                'fields' => array(
                                    'SUM(KsuPaymentDetail.total_biaya_klaim) as ksu_has_paid'
                                ),
                                'contain' => array(
                                    'KsuPayment'
                                ),
                            ));

                            $ksu_has_paid = (!empty($ksu_has_paid[0]['ksu_has_paid'])) ? $ksu_has_paid[0]['ksu_has_paid'] : 0;
                            $total_paid = $ksu_has_paid + $price;

                            $ksu_data = $this->Ksu->KsuDetail->getData('first', array(
                                'conditions' => array(
                                    'KsuDetail.id' => $value
                                )
                            ));
                            
                            if(!empty($ksu_data)){
                                if($total_paid > $ksu_data['KsuDetail']['total_price']){
                                    $validate_price_pay = false;
                                    break;
                                }else{
                                    $data['KsuPaymentDetail']['total_biaya_klaim'][$key] = $price;
                                    $total_price += $price;
                                }
                            }
                        }
                    }
                }
            }else{
                $validate_ksu_detail = false;
            }

            $temptotal = $total_price;
            $data['KsuPayment']['grandtotal'] = $total_price;

            if(!empty($data['KsuPayment']['pph'])){
                $temptotal -= $total_price*($data['KsuPayment']['pph']/100);
            }
            if(!empty($data['KsuPayment']['ppn'])){
                $temptotal += $total_price*($data['KsuPayment']['ppn']/100);
            }

            $total = $temptotal;
            $data['KsuPayment']['grand_total_payment'] = $total;
            $data['KsuPayment']['paid'] = 1;

            $this->KsuPayment->set($data);

            if($this->KsuPayment->validates($data) && $validate_ksu_detail && $validate_price_pay){
                if($this->KsuPayment->save($data)){
                    $ksu_payment_id = $this->KsuPayment->id;

                    if( !empty($total_price) ) {
                        $this->loadModel('Journal');
                        $document_no = !empty($data['KsuPayment']['no_doc'])?$data['KsuPayment']['no_doc']:false;
                        $this->Journal->setJournal( $ksu_payment_id, $document_no, 'ksu_payment_coa_credit_id', 0, $total_price, 'ksu_payment' );
                        $this->Journal->setJournal( $ksu_payment_id, $document_no, 'ksu_payment_coa_debit_id', $total_price, 0, 'ksu_payment' );
                    }

                    if($id && $data_local){
                        $this->KsuPayment->KsuPaymentDetail->deleteAll(array(
                            'KsuPaymentDetail.ksu_payment_id' => $ksu_payment_id
                        ));
                    }

                    // foreach ($temp_detail as $key => $value) {
                    //     $this->KsuPayment->KsuPaymentDetail->create();
                    //     $value['KsuPaymentDetail']['ksu_payment_id'] = $ksu_payment_id;

                    //     $this->KsuPayment->KsuPaymentDetail->set($value);
                    //     $this->KsuPayment->KsuPaymentDetail->save();

                    //     if(!empty($temp_detail[$key]['KsuPaymentDetail']['ksu_id'])){
                    //         $this->Ksu->id = $temp_detail[$key]['KsuPaymentDetail']['ksu_id'];
                    //         $this->Ksu->set('paid', 1);
                    //         $this->Ksu->save();
                    //     }
                    // }
                
                    if( !empty($data['KsuPaymentDetail']['total_biaya_klaim']) ) {
                        $collect_ksu_detail_id = array();
                        foreach ($data['KsuPaymentDetail']['total_biaya_klaim'] as $key => $value) {
                            if(!empty($data['KsuPaymentDetail']['ksu_detail_id'][$key])){
                                $ksu_detail_id = $data['KsuPaymentDetail']['ksu_detail_id'][$key];
                                array_push($collect_ksu_detail_id, $ksu_detail_id);

                                $this->KsuPayment->KsuPaymentDetail->create();
                                $this->KsuPayment->KsuPaymentDetail->set(array(
                                    'total_biaya_klaim' => trim($value),
                                    'ksu_detail_id' => $ksu_detail_id,
                                    'ksu_payment_id' => $ksu_payment_id,
                                    'status' => 1
                                ));
                                $this->KsuPayment->KsuPaymentDetail->save();

                                $default_conditions_detail = array(
                                    'KsuPaymentDetail.ksu_detail_id' => $ksu_detail_id,
                                    'KsuPaymentDetail.status' => 1
                                );

                                $ksu_has_paid = $this->KsuPayment->KsuPaymentDetail->getData('first', array(
                                    'conditions' => $default_conditions_detail,
                                    'fields' => array(
                                        '*',
                                        'SUM(KsuPaymentDetail.total_biaya_klaim) as ksu_has_paid'
                                    ),
                                    'contain' => array(
                                        'KsuDetail'
                                    )
                                ));
                                
                                $invoice_paid = !empty($ksu_has_paid[0]['ksu_has_paid'])?$ksu_has_paid[0]['ksu_has_paid']:0;
                                $invoice_total = !empty($ksu_has_paid['KsuDetail']['total_price'])?$ksu_has_paid['KsuDetail']['total_price']:0;
                                
                                if($invoice_paid >= $invoice_total){
                                    $this->Ksu->KsuDetail->id = $ksu_detail_id;
                                    $this->Ksu->KsuDetail->set(array(
                                        'paid' => 1,
                                        'complete_paid' => 1
                                    ));
                                    $this->Ksu->KsuDetail->save();
                                }else{
                                    $this->Ksu->KsuDetail->id = $ksu_detail_id;
                                    $this->Ksu->KsuDetail->set(array(
                                        'paid' => 1,
                                        'complete_paid' => 0
                                    ));
                                    $this->Ksu->KsuDetail->save();
                                }
                            }
                        }

                        if(!empty($collect_ksu_detail_id)){
                            $this->updateStatusKsu($collect_ksu_detail_id);
                        }
                    }

                    $this->Log->logActivity( sprintf(__('Sukses %s Pembayaran LKU ID #%s'), $msg, $ksu_payment_id), $this->user_data, $this->RequestHandler, $this->params );
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Pembayaran LKU'), $msg), 'success');
                    $this->redirect(array(
                        'controller' => 'lkus',
                        'action' => 'ksu_payments',
                    ));

                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Pembayaran LKU'), $msg), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal %s Pembayaran LKU #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1 );  
                }
            }else{
                $text = sprintf(__('Gagal %s Pembayaran Invoice'), $msg);

                if( !$validate_ksu_detail ){
                    $text .= ', mohon isi field pembayaran';
                }
                if(!$validate_price_pay){
                    $text .= ', Total Pembayaran tidak boleh lebih besar dari total LKU';
                }

                $this->MkCommon->setCustomFlash($text, 'error');
            }
        } else if($id && $data_local){
            $this->request->data = $data_local;

            // if(!empty($this->request->data['KsuPaymentDetail'])){
            //     foreach ($this->request->data['KsuPaymentDetail'] as $key => $value) {
            //         $ksu = $this->Ksu->KsuDetail->getData('first', array(
            //             'conditions' => array(
            //                 'KsuDetail.id' => $value['ksu_detail_id']
            //             ),
            //             'contain' => array(
            //                 'Ttuj'
            //             )
            //         ));

            //         if(!empty($ksu)){
            //             $this->request->data['KsuPaymentDetail'][$key]['Ttuj'] = $ksu['Ttuj'];
            //             $this->request->data['KsuPaymentDetail'][$key]['Ksu'] = $ksu['Ksu'];
            //         }
            //     }
            // }

            $this->request->data['KsuPayment']['tgl_bayar'] = (!empty($this->request->data['KsuPayment']['tgl_bayar'])) ? $this->MkCommon->getDate($this->request->data['KsuPayment']['tgl_bayar'], true) : '';
        }

        if(!empty($this->request->data['KsuPaymentDetail']['ksu_detail_id'])){
            $temp['KsuPaymentDetail'] = array();
            foreach ($this->request->data['KsuPaymentDetail']['ksu_detail_id'] as $key => $value) {
                if( !empty($value) ){
                    $temp['KsuPaymentDetail'][$key] = array(
                        'ksu_detail_id' => $value,
                        'total_klaim' => (!empty($data['KsuPaymentDetail']['total_klaim'][$key])) ? $data['KsuPaymentDetail']['total_klaim'][$key] : '',
                        'total_biaya_klaim' => (!empty($data['KsuPaymentDetail']['total_biaya_klaim'][$key])) ? $data['KsuPaymentDetail']['total_biaya_klaim'][$key] : '',
                    );
                }
            }

            unset($this->request->data['KsuPaymentDetail']);
            $this->request->data['KsuPaymentDetail'] = $temp['KsuPaymentDetail'];
        }

        if(!empty($this->request->data['KsuPaymentDetail'])){
            $ksu_details = array();
            foreach ($this->request->data['KsuPaymentDetail'] as $key => $value) {
                if(!empty($value['ksu_detail_id'])){
                    $ksu_condition = array(
                        'KsuDetail.id' => $value['ksu_detail_id'],
                        'KsuDetail.status' => 1,
                        'KsuDetail.complete_paid' => 0
                    );

                    $ksu_data = $this->Ksu->KsuDetail->getData('first', array(
                        'conditions' => $ksu_condition,
                        'contain' => array(
                            'Ksu',
                            'Perlengkapan',
                        )
                    ));
                    
                    if(!empty($ksu_data)){
                        $ttuj = $this->Ttuj->getData('first', array(
                            'conditions' => array(
                                'Ttuj.id' => $ksu_data['Ksu']['ttuj_id'],
                            )
                        ), true, array(
                            'status' => 'all',
                        ));

                        if(!empty($ttuj['Ttuj'])){
                            $ksu_data['Ttuj'] = $ttuj['Ttuj'];
                        }

                        $ksu_has_paid = $this->KsuPayment->KsuPaymentDetail->getData('first', array(
                            'conditions' => array(
                                'KsuPaymentDetail.ksu_detail_id' => $ksu_data['KsuDetail']['id'],
                                'KsuPaymentDetail.status' => 1
                            ),
                            'fields' => array(
                                'SUM(KsuPaymentDetail.total_biaya_klaim) as ksu_has_paid'
                            )
                        ));

                        $ksu_details[$key]['ksu_has_paid'] = $ksu_has_paid[0]['ksu_has_paid'];
                        $ksu_details[$key] = array_merge($ksu_details[$key], $ksu_data);
                    }
                }
            }
            
            $this->set(compact('ksu_details'));
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
                    'Ttuj.customer_id' => $this->request->data['KsuPayment']['customer_id'],
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
                'OR' => array(
                    array(
                        'Ksu.status' => 1,
                        'Ksu.complete_paid' => 0,
                        'Ksu.kekurangan_atpm' => 0
                    ),
                    array(
                        'Ksu.id' => $ttuj_customer_id,
                        'Ksu.paid' => array(0,1),
                        'Ksu.kekurangan_atpm' => 0
                    )
                )
            ),
            'contain' => array(
                'Ttuj' => array(
                    'CustomerNoType'
                )
            ),
        ), true, array(
            'status' => 'all',
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
                ), true, array(
                    'status' => 'all',
                ));

                if( !empty($dataCust) ) {
                    $ttujs[$customer_id] = $dataCust['Customer']['customer_name_code'];
                }
            }
        }

        $this->set(compact(
            'list_customer', 'id', 'action',
            'coas'
        ));

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
                'LkuPayment.is_void' => 0,
            ), 
            'contain' => array(
                'LkuPaymentDetail'
            )
        ));

        if(!empty($payments)){
            if(!empty($payments['LkuPaymentDetail'])){
                $collect_lku_detail_id = array();
                foreach ($payments['LkuPaymentDetail'] as $key => $value) {
                    $lku_detail_id = $value['lku_detail_id'];
                    array_push($collect_lku_detail_id, $lku_detail_id);

                    $lku_has_paid = $this->LkuPayment->LkuPaymentDetail->getData('first', array(
                        'conditions' => array(
                            'LkuPaymentDetail.lku_detail_id' => $lku_detail_id,
                            'LkuPayment.status' => 1,
                        ),
                        'fields' => array(
                            '*',
                            'SUM(LkuPaymentDetail.total_biaya_klaim) as lku_has_paid'
                        ),
                        'contain' => array(
                            'LkuDetail',
                            'LkuPayment'
                        )
                    ));

                    if(!empty($lku_has_paid)){
                        $total = $lku_has_paid[0]['lku_has_paid'] - $value['total_biaya_klaim'];

                        if($total < $lku_has_paid['LkuDetail']['total_price']){
                            $this->LkuPayment->LkuPaymentDetail->LkuDetail->id = $value['lku_detail_id'];
                            $this->LkuPayment->LkuPaymentDetail->LkuDetail->set(array(
                                'complete_paid' => 0,
                            ));
                            $this->LkuPayment->LkuPaymentDetail->LkuDetail->save();
                        }
                    }
                }

                $this->LkuPayment->LkuPaymentDetail->updateAll(array(
                    'LkuPaymentDetail.status' => 0
                ), array(
                    'LkuPaymentDetail.lku_payment_id' => $id
                ));
            }

            $this->LkuPayment->id = $id;
            $this->LkuPayment->set(array(
                'status' => 0,
                'is_void' => 1,
                'void_date' => date('Y-m-d')
            ));

            if($this->LkuPayment->save()){
                if(!empty($collect_lku_detail_id)){
                    $this->updateStatusLku($collect_lku_detail_id);
                }

                $this->MkCommon->setCustomFlash(__('Berhasil menghapus pembayaran LKU'), 'success');
                $this->Log->logActivity( sprintf(__('Berhasil menghapus pembayaran LKU ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params ); 
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal menghapus pembayaran LKU'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal menghapus pembayaran LKU ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Pembayaran LKU tidak ditemukan'), 'error');
        }

        $this->redirect($this->referer());
    }

    function ksu_payment_delete($id){
        $this->loadModel('KsuPayment');
        $payments = $this->KsuPayment->getData('first', array(
            'conditions' => array(
                'KsuPayment.id' => $id,
                'KsuPayment.is_void' => 0,
            ), 
            'contain' => array(
                'KsuPaymentDetail'
            )
        ));

        if(!empty($payments)){
            if(!empty($payments['KsuPaymentDetail'])){
                $collect_ksu_detail_id = array();
                foreach ($payments['KsuPaymentDetail'] as $key => $value) {
                    $ksu_detail_id = $value['ksu_detail_id'];
                    array_push($collect_ksu_detail_id, $ksu_detail_id);

                    $ksu_has_paid = $this->KsuPayment->KsuPaymentDetail->getData('first', array(
                        'conditions' => array(
                            'KsuPaymentDetail.ksu_detail_id' => $ksu_detail_id,
                            'KsuPayment.status' => 1,
                        ),
                        'fields' => array(
                            '*',
                            'SUM(KsuPaymentDetail.total_biaya_klaim) as ksu_has_paid'
                        ),
                        'contain' => array(
                            'KsuDetail',
                            'KsuPayment'
                        )
                    ));

                    if(!empty($ksu_has_paid)){
                        $total = $ksu_has_paid[0]['ksu_has_paid'] - $value['total_biaya_klaim'];

                        if($total < $ksu_has_paid['KsuDetail']['total_price']){
                            $this->KsuPayment->KsuPaymentDetail->KsuDetail->id = $value['ksu_detail_id'];
                            $this->KsuPayment->KsuPaymentDetail->KsuDetail->set(array(
                                'complete_paid' => 0,
                            ));
                            $this->KsuPayment->KsuPaymentDetail->KsuDetail->save();
                        }
                    }
                }

                $this->KsuPayment->KsuPaymentDetail->updateAll(array(
                    'KsuPaymentDetail.status' => 0
                ), array(
                    'KsuPaymentDetail.ksu_payment_id' => $id
                ));
            }

            $this->KsuPayment->id = $id;
            $this->KsuPayment->set(array(
                'status' => 0,
                'is_void' => 1,
                'void_date' => date('Y-m-d')
            ));

            if($this->KsuPayment->save()){
                if(!empty($collect_ksu_detail_id)){
                    $this->updateStatusKsu($collect_ksu_detail_id);
                }

                $this->MkCommon->setCustomFlash(__('Berhasil menghapus pembayaran KSU'), 'success');
                $this->Log->logActivity( sprintf(__('Berhasil menghapus pembayaran KSU ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params ); 
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal menghapus pembayaran KSU'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal menghapus pembayaran KSU ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Pembayaran KSU tidak ditemukan'), 'error');
        }

        $this->redirect($this->referer());
    }

    function detail_payment($id = false){
        if(!empty($id)){
            $this->loadModel('LkuPayment');
            $this->loadModel('PartsMotor');
            $this->loadModel('TipeMotor');

            $LkuPayment = $this->LkuPayment->getLkuPayment($id);
            
            if(!empty($LkuPayment)){
                if(!empty($LkuPayment['LkuPaymentDetail'])){
                    foreach ($LkuPayment['LkuPaymentDetail'] as $key => $value) {
                        $lku = $this->LkuPayment->LkuPaymentDetail->LkuDetail->getData('first', array(
                            'conditions' => array(
                                'LkuDetail.id' => $value['lku_detail_id']
                            ),
                            'contain' => array(
                                'Lku'
                            )
                        ));

                        if(!empty($lku)){
                            $part_motor = array();
                            if(!empty($lku['LkuDetail']['part_motor_id'])){
                                $part_motor = $this->PartsMotor->getData('first', array(
                                    'conditions' => array(
                                        'PartsMotor.id' => $lku['LkuDetail']['part_motor_id']
                                    )
                                ));
                            }
                            $LkuPayment['LkuPaymentDetail'][$key]['PartsMotor'] = !empty($part_motor['PartsMotor']) ? $part_motor['PartsMotor'] : array();

                            $tipe_motor = array();
                            if(!empty($lku['LkuDetail']['tipe_motor_id'])){
                                $tipe_motor = $this->TipeMotor->getData('first', array(
                                    'conditions' => array(
                                        'TipeMotor.id' => $lku['LkuDetail']['tipe_motor_id']
                                    )
                                ));
                            }
                            $LkuPayment['LkuPaymentDetail'][$key]['TipeMotor'] = !empty($tipe_motor['TipeMotor']) ? $tipe_motor['TipeMotor'] : array();

                            $LkuPayment['LkuPaymentDetail'][$key]['LkuDetail'] = $lku['LkuDetail'];
                            $LkuPayment['LkuPaymentDetail'][$key]['Lku'] = $lku['Lku'];
                        }
                    }
                }
                
                $sub_module_title = __('Detail Pembayaran LKU');
                $this->set(compact('LkuPayment', 'sub_module_title'));
                $this->set('active_menu', 'lku_payments');
            }else{
                $this->MkCommon->setCustomFlash(__('Pembayaran LKU tidak ditemukan.'), 'error');
                $this->redirect($this->referer());
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Pembayaran LKU tidak ditemukan.'), 'error');
            $this->redirect($this->referer());
        }
    }

    function detail_ksu_payment($id = false){
        if(!empty($id)){
            $this->loadModel('KsuPayment');
            $this->loadModel('Perlengkapan');

            $KsuPayment = $this->KsuPayment->getKsuPayment($id);
            
            if(!empty($KsuPayment)){
                if(!empty($KsuPayment['KsuPaymentDetail'])){
                    foreach ($KsuPayment['KsuPaymentDetail'] as $key => $value) {
                        $ksu = $this->KsuPayment->KsuPaymentDetail->KsuDetail->getData('first', array(
                            'conditions' => array(
                                'KsuDetail.id' => $value['ksu_detail_id']
                            ),
                            'contain' => array(
                                'Ksu'
                            )
                        ));

                        if(!empty($ksu)){
                            $Perlengkapan = array();
                            if(!empty($ksu['KsuDetail']['perlengkapan_id'])){
                                $Perlengkapan = $this->Perlengkapan->getData('first', array(
                                    'conditions' => array(
                                        'Perlengkapan.id' => $ksu['KsuDetail']['perlengkapan_id']
                                    )
                                ));
                            }
                            $KsuPayment['KsuPaymentDetail'][$key]['Perlengkapan'] = !empty($Perlengkapan['Perlengkapan']) ? $Perlengkapan['Perlengkapan'] : array();
                            $KsuPayment['KsuPaymentDetail'][$key]['KsuDetail'] = $ksu['KsuDetail'];
                            $KsuPayment['KsuPaymentDetail'][$key]['Ksu'] = $ksu['Ksu'];
                        }
                    }
                }
                
                $sub_module_title = __('Detail Pembayaran KSU');
                $this->set(compact('KsuPayment', 'sub_module_title'));
                $this->set('active_menu', 'lku_payments');
            }else{
                $this->MkCommon->setCustomFlash(__('Pembayaran KSU tidak ditemukan.'), 'error');
                $this->redirect($this->referer());
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Pembayaran KSU tidak ditemukan.'), 'error');
            $this->redirect($this->referer());
        }
    }

    function updateStatusLku($collect_lku_detail_id){
        $this->loadModel('Lku');
        $lkus = $this->Lku->LkuDetail->find('all', array(
            'conditions' => array(
                'LkuDetail.id' => $collect_lku_detail_id
            ),
            'group' => array(
                'LkuDetail.lku_id'
            ),
            'contain' => array(
                'Lku'
            )
        ));
        
        if(!empty($lkus)){
            foreach ($lkus as $key => $value) {
                $lku_detail_id = $this->Lku->LkuDetail->getdata('list', array(
                    'conditions' => array(
                        'LkuDetail.lku_id' => $value['LkuDetail']['lku_id']
                    )
                ));

                if(!empty($lku_detail_id)){
                    $lku_has_paid = $this->LkuPayment->LkuPaymentDetail->getData('first', array(
                        'conditions' => array(
                            'LkuPaymentDetail.lku_detail_id' => $lku_detail_id,
                            'LkuPaymentDetail.status' => 1,
                        ),
                        'fields' => array(
                            '*',
                            'SUM(LkuPaymentDetail.total_biaya_klaim) as lku_has_paid'
                        ),
                    ));
                    
                    $invoice_paid = !empty($lku_has_paid[0]['lku_has_paid'])?$lku_has_paid[0]['lku_has_paid']:0;
                    $invoice_total = !empty($value['Lku']['total_price'])?$value['Lku']['total_price']:0;
                    
                    if($invoice_paid >= $invoice_total){
                        $this->Lku->id = $value['Lku']['id'];
                        $this->Lku->set(array(
                            'paid' => 1,
                            'complete_paid' => 1
                        ));
                        $this->Lku->save();
                    }else{
                        $this->Lku->id = $value['Lku']['id'];
                        $this->Lku->set(array(
                            'paid' => !empty($invoice_paid) ? 1 : 0,
                            'complete_paid' => 0
                        ));
                        $this->Lku->save();
                    }
                }
            }
        }
    }

    function updateStatusKsu($collect_ksu_detail_id){
        $this->loadModel('Ksu');
        $ksus = $this->Ksu->KsuDetail->find('all', array(
            'conditions' => array(
                'KsuDetail.id' => $collect_ksu_detail_id
            ),
            'group' => array(
                'KsuDetail.ksu_id'
            ),
            'contain' => array(
                'Ksu'
            )
        ));
        
        if(!empty($ksus)){
            foreach ($ksus as $key => $value) {
                $ksu_detail_id = $this->Ksu->KsuDetail->getdata('list', array(
                    'conditions' => array(
                        'KsuDetail.ksu_id' => $value['KsuDetail']['ksu_id']
                    )
                ));

                if(!empty($ksu_detail_id)){
                    $ksu_has_paid = $this->KsuPayment->KsuPaymentDetail->getData('first', array(
                        'conditions' => array(
                            'KsuPaymentDetail.ksu_detail_id' => $ksu_detail_id,
                            'KsuPaymentDetail.status' => 1
                        ),
                        'fields' => array(
                            '*',
                            'SUM(KsuPaymentDetail.total_biaya_klaim) as ksu_has_paid'
                        ),
                    ));
                    
                    $invoice_paid = !empty($ksu_has_paid[0]['ksu_has_paid'])?$ksu_has_paid[0]['ksu_has_paid']:0;
                    $invoice_total = !empty($value['Ksu']['total_price'])?$value['Ksu']['total_price']:0;

                    if($invoice_paid >= $invoice_total){
                        $this->Ksu->id = $value['Ksu']['id'];
                        $this->Ksu->set(array(
                            'paid' => 1,
                            'complete_paid' => 1
                        ));
                        $this->Ksu->save();
                    }else{
                        $this->Ksu->id = $value['Ksu']['id'];
                        $this->Ksu->set(array(
                            'paid' => !empty($invoice_paid) ? 1 : 0,
                            'complete_paid' => 0
                        ));
                        $this->Ksu->save();
                    }
                }
            }
        }
    }
}