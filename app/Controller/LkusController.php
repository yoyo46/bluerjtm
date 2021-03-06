<?php
App::uses('AppController', 'Controller');
class LkusController extends AppController {
    public $uses = array();
	public $helpers = array(
        'Lku'
    );
    public $components = array(
        'RjLku'
    );

    function beforeFilter() {
        parent::beforeFilter();
        $this->set('title_for_layout', __('ERP RJTM | Data LKU/KSU'));
        $this->set('module_title', __('LKU/KSU'));
    }

    function search( $index = 'index', $parameter = false ){
        $refine = array();
        if(!empty($this->request->data)) {
            $data = $this->request->data;
            $refine = $this->RjLku->processRefine($data);
            $params = $this->RjLku->generateSearchURL($refine);
            $params = $this->MkCommon->getRefineGroupBranch($params, $data);
            $result = $this->MkCommon->processFilter($data);

            $params = array_merge($params, $result);
            $params['action'] = $index;

            if( !empty($parameter) ) {
                array_unshift($params, $parameter);
            }

            $this->redirect($params);
        }
        $this->redirect('/');
    }

	public function index() {
        $this->loadModel('Lku');

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

                $list_ttuj_id = $this->Lku->Ttuj->getData('list', array(
                    'conditions' => array(
                        'Ttuj.no_ttuj LIKE' => '%'.$no_ttuj.'%',
                    ),
                    'fields' => array(
                        'Ttuj.id', 'Ttuj.id',
                    ),
                ), true, array(
                    'status' => 'all',
                ));
                $conditions['Lku.ttuj_id'] = $list_ttuj_id;
            }

            if(!empty($refine['customer'])){
                $customer = urldecode($refine['customer']);
                $this->request->data['Lku']['customer_id'] = $customer;

                $list_ttuj_id = $this->Lku->Ttuj->getData('list', array(
                    'conditions' => array(
                        'Ttuj.customer_id' => $customer,
                    ),
                    'fields' => array(
                        'Ttuj.id', 'Ttuj.id',
                    ),
                ), true, array(
                    'status' => 'all',
                ));
                $conditions['Lku.ttuj_id'] = $list_ttuj_id;
            }

            if(!empty($refine['closing'])){
                $value = urldecode($refine['closing']);
                $this->request->data['Ksu']['closing'] = $value;
                $conditions['Lku.completed'] = $value;
            }

            if(!empty($refine['paid'])){
                $value = urldecode($refine['paid']);
                $this->request->data['Ksu']['paid'] = $value;
                $conditions['Lku.paid'] = 1;
                $conditions['Lku.complete_paid'] = 1;
            }

            if(!empty($refine['half_paid'])){
                $value = urldecode($refine['half_paid']);
                $this->request->data['Ksu']['half_paid'] = $value;
                $conditions['Lku.paid'] = 1;
                $conditions['Lku.complete_paid'] = 0;
            }
            if(!empty($refine['nopol'])){
                $nopol = urldecode($refine['nopol']);
                $this->request->data['Lku']['nopol'] = $nopol;
                $typeTruck = !empty($refine['type'])?$refine['type']:1;
                $this->request->data['Lku']['type'] = $typeTruck;

                if( $typeTruck == 2 ) {
                    $conditionsNopol = array(
                        'Ttuj.truck_id' => $nopol,
                    );
                } else {
                    $conditionsNopol = array(
                        'Ttuj.nopol LIKE' => '%'.$nopol.'%',
                    );
                }

                $truckSearch = $this->Lku->Ttuj->getData('list', array(
                    'conditions' => $conditionsNopol,
                    'fields' => array(
                        'Ttuj.id', 'Ttuj.id',
                    ),
                ));
                $conditions['Lku.ttuj_id'] = $truckSearch;
            }
        }

        $this->paginate = $this->Lku->getData('paginate', array(
            'conditions' => $conditions,
        ), true, array(
            'status' => 'all',
        ));
        $Lkus = $this->paginate('Lku');

        if(!empty($Lkus)){
            foreach ($Lkus as $key => $value) {
                $ttuj_id = $this->MkCommon->filterEmptyField($value, 'Lku', 'ttuj_id');

                $value = $this->Lku->Ttuj->getMerge($value, $ttuj_id);
                $customer_id = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'customer_id');

                $value = $this->Lku->Ttuj->Customer->getMerge($value, $customer_id);
                $Lkus[$key] = $value;
            }
        }

        $customers = $this->Lku->Ttuj->Customer->getData('list', array(
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
            $Lku = $this->Lku->getLku($id, 'all');
            
            if(!empty($Lku)){
                $this->loadModel('Customer');

                $customer_id = $this->MkCommon->filterEmptyField($Lku, 'Ttuj', 'customer_id');
                $Lku = $this->Customer->getMerge($Lku, $customer_id);

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

                $this->MkCommon->getLogs($this->params['controller'], array( 'add', 'edit', 'toggle' ), $id);

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
        $this->set('sub_module_title', 'Edit LKU');
        $Lku = $this->Lku->getData('first', array(
            'conditions' => array(
                'Lku.id' => $id,
                'Lku.paid' => 0,
                'Lku.completed' => 0,
            ),
        ));

        if(!empty($Lku)){
            $this->MkCommon->getLogs($this->params['controller'], array( 'add', 'edit', 'toggle' ), $id);
            $this->DoLku($id, $Lku);
        }else{
            $this->MkCommon->setCustomFlash(__('LKU tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'lkus',
                'action' => 'index'
            ));
        }
    }

    function DoLku($id = false, $data_local = false){
        $this->loadModel('Ttuj');
        $this->loadModel('TipeMotor');
        $this->loadModel('PartsMotor');
        $this->loadModel('TtujTipeMotor');

        $ttuj_id = false;

        if(!empty($this->request->data)){
            $data = $this->request->data;
            $no_ttuj = $this->MkCommon->filterEmptyField($data, 'Lku', 'no_ttuj');

            if( !empty($no_ttuj) ) {
                $ttuj = $this->Ttuj->getMerge(array(), $no_ttuj, 'Ttuj.no_ttuj');
                $ttuj_id = $this->MkCommon->filterEmptyField($ttuj, 'Ttuj', 'id');
            }

            $data['Lku']['branch_id'] = Configure::read('__Site.config_branch_id');
            $data['Lku']['ttuj_id'] = $ttuj_id;
            
            if($id && $data_local){
                $this->Lku->id = $id;
                $msg = 'mengubah';
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
            
            if( empty($data['Lku']['completed']) ) {
                $data['Lku']['completed_desc'] = NULL;
                $data['Lku']['completed_nodoc'] = NULL;
                $data['Lku']['completed_date'] = NULL;
            } else {
                $data['Lku']['completed_date'] = (!empty($data['Lku']['completed_date'])) ? $this->MkCommon->getDate($data['Lku']['completed_date']) : '';
            }

            if( !empty($data['LkuDetail']['tipe_motor_id']) ) {
                $data['LkuDetail']['tipe_motor_id'] = array_filter($data['LkuDetail']['tipe_motor_id']);
            }

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
                    } else {
                        $validate_lku_detail = false;
                    }
                }
            } else {
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

                    $this->params['old_data'] = $data_local;
                    $this->params['data'] = $data;

                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s LKU'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Berhasil %s LKU #%s'), $msg, $lku_id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $lku_id );

                    $this->redirect(array(
                        'controller' => 'Lkus',
                        'action' => 'index',
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s LKU. Lengkapi field yang dibutuhkan'), $msg), 'error');
                    $this->Log->logActivity( sprintf(__('Berhasil %s LKU #%s'), $msg, $lku_id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s LKU. Lengkapi field yang dibutuhkan'), $msg), 'error');
            }
        } else if($id && $data_local){
            $ttuj_id = $this->MkCommon->filterEmptyField($data_local, 'Lku', 'ttuj_id');
            $data_local = $this->Lku->LkuDetail->getMerge($data_local, $id);
            $ttuj = $this->Ttuj->getMerge(array(), $ttuj_id);

            $data_local['Lku']['no_ttuj'] = $this->MkCommon->filterEmptyField($ttuj, 'Ttuj', 'no_ttuj');
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
                                'TtujTipeMotor.ttuj_id' => $ttuj_id,
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
                                'TtujTipeMotor.ttuj_id' => $ttuj_id,
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

        // $ttujs = $this->Ttuj->getData('paginate', array(
        //     'fields' => array(
        //         'Ttuj.id', 'Ttuj.no_ttuj'
        //     ),
        //     'conditions' => array(
        //         'Ttuj.is_draft' => 0,
        //         'Ttuj.is_laka' => 0,
        //         'OR' => array(
        //             'Ttuj.is_bongkaran' => 1,
        //             'Ttuj.id' => !empty($data_local['Lku']['ttuj_id']) ? $data_local['Lku']['ttuj_id'] : false,
        //         ),
        //     ),
        // ));

        if(!empty($ttuj)){

            $tipe_motor_list = array();
            $ttuj = $this->TtujTipeMotor->getMergeTtujTipeMotor( $ttuj, $ttuj_id, 'all');
            $ttuj = $this->Ttuj->getMergeList($ttuj, array(
                'contain' => array(
                    'DriverPengganti' => array(
                        'uses' => 'Driver',
                        'primaryKey' => 'id',
                        'foreignKey' => 'driver_pengganti_id',
                        'elements' => array(
                            'branch' => false,
                        ),
                    ),
                    'Driver' => array(
                        'elements' => array(
                            'branch' => false,
                        ),
                    ),
                ),
            ));
            $ttuj['Ttuj']['driver_name'] = $this->MkCommon->filterEmptyField($ttuj, 'Driver', 'driver_name');

            if(!empty($ttuj['TtujTipeMotor'])){
                foreach ($ttuj['TtujTipeMotor'] as $key => $value) {
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
            $this->request->data = array_merge($this->request->data, $ttuj);
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
            'id'
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
                $this->MkCommon->setCustomFlash(__('Sukses mengubah status.'), 'success');
                $this->Log->logActivity( sprintf(__('Sukses mengubah status LKU ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal mengubah status.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal mengubah status LKU ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
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
        
        $params = $this->MkCommon->_callRefineParams($this->params);
        $options =  $this->LkuPayment->_callRefineParams($params, array(
            'order' => array(
                'LkuPayment.created' => 'DESC'
            )
        ));

        $this->paginate = $this->LkuPayment->getData('paginate', $options, true, array(
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
        $this->loadModel('LkuPayment');
        $this->set('sub_module_title', __('Tambah Pembayaran LKU'));
        $this->DoLkuPayment();
    }

    function payment_edit($id){
        $this->loadModel('LkuPayment');
        $this->set('sub_module_title', 'Edit Pembayaran LKU');
        $value = $this->LkuPayment->getData('first', array(
            'conditions' => array(
                'LkuPayment.id' => $id
            ),
        ));

        if(!empty($value)){
            $transaction_status = $this->MkCommon->filterEmptyField($value, 'LkuPayment', 'transaction_status');
            $value = $this->LkuPayment->LkuPaymentDetail->getMerge($value, $id);
            $value = $this->LkuPayment->getMergeList($value, array(
                'contain' => array(
                    'Cogs',
                ),
            ));

            if( $transaction_status == 'posting' ) {
                $this->MkCommon->setCustomFlash(__('Data tidak ditemukan'), 'error');
                $this->redirect($this->referer());
                die();
            }

            $this->MkCommon->getLogs($this->params['controller'], array( 'payment_add', 'payment_edit', 'payment_delete' ), $id);
            $this->DoLkuPayment($id, $value);
        }else{
            $this->MkCommon->setCustomFlash(__('ID Pembayaran LKU tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'Lkus',
                'action' => 'payments'
            ));
        }
    }

    function DoLkuPayment($id = false, $data_local = false){
        if(!empty($this->request->data)){
            $data = $this->request->data;
            $data = $this->MkCommon->dataConverter($data, array(
                'date' => array(
                    'LkuPayment' => array(
                        'tgl_bayar',
                    ),
                )
            ));
            $tgl_bayar = $this->MkCommon->filterEmptyField($data, 'LkuPayment', 'tgl_bayar');
            $this->MkCommon->_callAllowClosing($data, 'LkuPayment', 'tgl_bayar');
            $data = Common::_callCheckCostCenter($data, 'LkuKsuPayment', 'LkuPayment');

            $customer_id = $this->MkCommon->filterEmptyField($data, 'LkuPayment', 'customer_id');
            $coa_id = $this->MkCommon->filterEmptyField($data, 'LkuPayment', 'coa_id');
            $transaction_status = $this->MkCommon->filterEmptyField($data, 'LkuPayment', 'transaction_status');
            $cogs_id = $this->MkCommon->filterEmptyField($data, 'LkuPayment', 'cogs_id');

            $customer = $this->LkuPayment->Customer->getMerge(array(), $customer_id);
            $customer_name = $this->MkCommon->filterEmptyField($customer, 'Customer', 'customer_name_code');

            if(!empty($id)){
                $this->LkuPayment->id = $id;
                $msg = 'mengubah';
            }else{
                $this->LkuPayment->create();
                $msg = 'menambah';
            }
            
            $data['LkuPayment']['branch_id'] = Configure::read('__Site.config_branch_id');
            $total_price = 0;

            $validate_lku_detail = true;
            $validate_price_pay = true;

            if(!empty($data['LkuPaymentDetail']['lku_detail_id'])){
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
                            $lku_has_paid = $this->LkuPayment->LkuPaymentDetail->getData('first', array(
                                'conditions' => array(
                                    'LkuPaymentDetail.lku_detail_id' => $value,
                                    'LkuPayment.status' => 1,
                                    'LkuPayment.is_void' => 0,
                                    'LkuPayment.id <>' => $id,
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

                            $lku_data = $this->LkuPayment->LkuPaymentDetail->LkuDetail->getData('first', array(
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

                    if( $transaction_status == 'posting' ) {
                        if( !empty($total_price) ) {
                            $title = sprintf(__('Pembayaran LKU kepada customer %s'), $customer_name);
                            $title = $this->MkCommon->filterEmptyField($data, 'LkuPayment', 'description', $title);
                            $document_no = $this->MkCommon->filterEmptyField($data, 'LkuPayment', 'no_doc');

                            $this->User->Journal->setJournal($total_price, array(
                                'credit' => $coa_id,
                                'debit' => 'lku_payment_coa_id',
                            ), array(
                                'cogs_id' => $cogs_id,
                                'date' => $tgl_bayar,
                                'document_id' => $lku_payment_id,
                                'title' => $title,
                                'document_no' => $document_no,
                                'type' => 'lku_payment',
                            ));
                        }
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

                                if( $transaction_status == 'posting' ) {
                                    $default_conditions_detail = array(
                                        'LkuPaymentDetail.lku_detail_id' => $lku_detail_id,
                                        'LkuPaymentDetail.status' => 1,
                                        'LkuPayment.transaction_status' => 'posting',
                                    );

                                    $lku_has_paid = $this->LkuPayment->LkuPaymentDetail->getData('first', array(
                                        'conditions' => $default_conditions_detail,
                                        'fields' => array(
                                            '*',
                                            'SUM(LkuPaymentDetail.total_biaya_klaim) as lku_has_paid'
                                        ),
                                        'contain' => array(
                                            'LkuDetail',
                                            'LkuPayment',
                                        )
                                    ));
                                    
                                    $invoice_paid = !empty($lku_has_paid[0]['lku_has_paid'])?$lku_has_paid[0]['lku_has_paid']:0;
                                    $invoice_total = !empty($lku_has_paid['LkuDetail']['total_price'])?$lku_has_paid['LkuDetail']['total_price']:0;
                                    
                                    if($invoice_paid >= $invoice_total){
                                        $this->LkuPayment->LkuPaymentDetail->LkuDetail->id = $lku_detail_id;
                                        $this->LkuPayment->LkuPaymentDetail->LkuDetail->set(array(
                                            'paid' => 1,
                                            'complete_paid' => 1
                                        ));
                                        $this->LkuPayment->LkuPaymentDetail->LkuDetail->save();
                                    }else{
                                        $this->LkuPayment->LkuPaymentDetail->LkuDetail->id = $lku_detail_id;
                                        $this->LkuPayment->LkuPaymentDetail->LkuDetail->set(array(
                                            'paid' => 1,
                                            'complete_paid' => 0
                                        ));
                                        $this->LkuPayment->LkuPaymentDetail->LkuDetail->save();
                                    }
                                }
                            }
                        }

                        if( $transaction_status == 'posting' ) {
                            if(!empty($collect_lku_detail_id)){
                                $this->updateStatusLku($collect_lku_detail_id);
                            }
                        }
                    }

                    $this->params['old_data'] = $data_local;
                    $this->params['data'] = $data;

                    $noref = str_pad($lku_payment_id, 6, '0', STR_PAD_LEFT);
                    $this->Log->logActivity( sprintf(__('Sukses %s Pembayaran LKU ID #%s'), $msg, $lku_payment_id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $lku_payment_id );
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Pembayaran LKU #%s'), $msg, $noref), 'success');
                    $this->redirect(array(
                        'controller' => 'Lkus',
                        'action' => 'payments',
                    ));

                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Pembayaran LKU'), $msg), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal %s Pembayaran LKU #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );  
                }
            }else{
                $text = sprintf(__('Gagal %s pembayaran lku'), $msg);

                if( !$validate_lku_detail ){
                    $text .= ', mohon isi field pembayaran';
                }
                if(!$validate_price_pay){
                    $text .= ', Total Pembayaran tidak boleh lebih besar dari total pembayaran per unit LKU';
                }

                $this->MkCommon->setCustomFlash($text, 'error');
            }

            $this->request->data['LkuPayment']['cogs_id'] = Common::hashEmptyField($data, 'LkuPayment.cogs_id');
        } else if($id && $data_local){
            $dataDetail = $this->MkCommon->filterEmptyField($data_local, 'LkuPaymentDetail');
            unset($data_local['LkuPaymentDetail']);

            $this->request->data = $data_local;
            $this->request->data['LkuPayment']['tgl_bayar'] = (!empty($this->request->data['LkuPayment']['tgl_bayar'])) ? $this->MkCommon->getDate($this->request->data['LkuPayment']['tgl_bayar'], true) : '';

            if( !empty($dataDetail) ) {
                foreach ($dataDetail as $key => $value) {
                    $lku_detail_id = $this->MkCommon->filterEmptyField($value, 'LkuPaymentDetail', 'lku_detail_id');
                    $total_biaya_klaim = $this->MkCommon->filterEmptyField($value, 'LkuPaymentDetail', 'total_biaya_klaim');

                    $this->request->data['LkuPaymentDetail']['lku_detail_id'][$lku_detail_id] = $lku_detail_id;
                    $this->request->data['LkuPaymentDetail']['total_biaya_klaim'][$lku_detail_id] = $total_biaya_klaim;
                }
            }

            $data = $this->request->data;
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
                    );

                    if( empty($id) ) {
                        $lku_condition['LkuDetail.complete_paid'] = 0;
                    }

                    $lku_data = $this->LkuPayment->LkuPaymentDetail->LkuDetail->getData('first', array(
                        'conditions' => $lku_condition,
                        'contain' => array(
                            'Lku',
                            'TipeMotor',
                            'PartsMotor'
                        )
                    ));
                    
                    if(!empty($lku_data)){
                        $ttuj = $this->LkuPayment->LkuPaymentDetail->LkuDetail->Lku->Ttuj->getData('first', array(
                            'conditions' => array(
                                'Ttuj.id' => $lku_data['Lku']['ttuj_id']
                            ),
                        ), true, array(
                            'status' => 'all',
                        ));
                        $ttuj = $this->LkuPayment->LkuPaymentDetail->LkuDetail->Lku->Ttuj->getMergeList($ttuj, array(
                            'contain' => array(
                                'DriverPengganti' => array(
                                    'uses' => 'Driver',
                                    'primaryKey' => 'id',
                                    'foreignKey' => 'driver_pengganti_id',
                                    'elements' => array(
                                        'branch' => false,
                                    ),
                                ),
                                'Driver' => array(
                                    'elements' => array(
                                        'branch' => false,
                                    ),
                                ),
                            ),
                        ));

                        if(!empty($ttuj)){
                            $lku_data = array_merge($lku_data, $ttuj);
                        }

                        $lku_has_paid = $this->LkuPayment->LkuPaymentDetail->getData('first', array(
                            'conditions' => array(
                                'LkuPaymentDetail.lku_detail_id' => $lku_data['LkuDetail']['id'],
                                'LkuPaymentDetail.lku_payment_id <>' => $id,
                                'LkuPaymentDetail.status' => 1,
                                'LkuPayment.transaction_status' => 'posting',
                            ),
                            'fields' => array(
                                'SUM(LkuPaymentDetail.total_biaya_klaim) as lku_has_paid'
                            ),
                            'contain' => array(
                                'LkuPayment',
                            ),
                        ));

                        $lku_details[$key]['lku_has_paid'] = $lku_has_paid[0]['lku_has_paid'];
                        $lku_details[$key] = array_merge($lku_details[$key], $lku_data);
                    }
                }
            }
            
            $this->set(compact('lku_details'));
        }

        $this->LkuPayment->LkuPaymentDetail->LkuDetail->Lku->Ttuj->bindModel(array(
            'belongsTo' => array(
                'CustomerNoType' => array(
                    'className' => 'CustomerNoType',
                    'foreignKey' => 'customer_id',
                ),
            ),
        ), false);
        $ttuj_customer_id = array();

        if(!empty($this->request->data['LkuPayment']['customer_id'])){
            $ttuj_customer_id = $this->LkuPayment->LkuPaymentDetail->LkuDetail->Lku->Ttuj->getData('list', array(
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

        $customers = $this->LkuPayment->LkuPaymentDetail->LkuDetail->Lku->getData('all', array(
            'conditions' => array(
                'Lku.completed' => 0,
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

                $dataCust = $this->LkuPayment->LkuPaymentDetail->LkuDetail->Lku->Ttuj->Customer->getData('first', array(
                    'conditions' => array(
                        'Customer.id' => $customer_id,
                    ),
                ), true, array(
                    'status' => 'all',
                    'branch' => false,
                ));

                if( !empty($dataCust) ) {
                    $ttujs[$customer_id] = $dataCust['Customer']['customer_name_code'];
                }
            }
        }
        
        $coas = $this->GroupBranch->Branch->BranchCoa->getCoas();
        $cogs = $this->MkCommon->_callCogsOptGroup('LkuKsuPayment', 'LkuPayment');

        $this->MkCommon->_layout_file('select');
        $this->set('active_menu', 'lku_payments');
        $this->set(compact(
            'list_customer', 'id', 'action',
            'coas', 'ttujs', 'data_local'
        ));
        $this->render('lku_payment_form');
    }

    public function ksus() {
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

                $list_ttuj_id = $this->Ksu->Ttuj->getData('list', array(
                    'conditions' => array(
                        'Ttuj.no_ttuj LIKE' => '%'.$no_ttuj.'%',
                    ),
                    'fields' => array(
                        'Ttuj.id', 'Ttuj.id',
                    ),
                ), true, array(
                    'status' => 'all',
                ));
                $conditions['Ksu.ttuj_id'] = $list_ttuj_id;
            }

            if(!empty($refine['customer'])){
                $customer = urldecode($refine['customer']);
                $this->request->data['Ksu']['customer_id'] = $customer;

                $list_ttuj_id = $this->Ksu->Ttuj->getData('list', array(
                    'conditions' => array(
                        'Ttuj.customer_id' => $customer,
                    ),
                    'fields' => array(
                        'Ttuj.id', 'Ttuj.id',
                    ),
                ), true, array(
                    'status' => 'all',
                ));
                $conditions['Ksu.ttuj_id'] = $list_ttuj_id;
            }

            if(!empty($refine['atpm'])){
                $value = urldecode($refine['atpm']);
                $this->request->data['Ksu']['atpm'] = $value;
                $conditions['Ksu.kekurangan_atpm'] = $value;
            }

            if(!empty($refine['closing'])){
                $value = urldecode($refine['closing']);
                $this->request->data['Ksu']['closing'] = $value;
                $conditions['Ksu.completed'] = $value;
            }

            if(!empty($refine['paid'])){
                $value = urldecode($refine['paid']);
                $this->request->data['Ksu']['paid'] = $value;
                $conditions['Ksu.paid'] = 1;
                $conditions['Ksu.complete_paid'] = 1;
            }

            if(!empty($refine['half_paid'])){
                $value = urldecode($refine['half_paid']);
                $this->request->data['Ksu']['half_paid'] = $value;
                $conditions['Ksu.paid'] = 1;
                $conditions['Ksu.complete_paid'] = 0;
            }
        }

        $this->paginate = $this->Ksu->getData('paginate', array(
            'conditions' => $conditions,
        ), true, array(
            'status' => 'all',
        ));
        $Ksus = $this->paginate('Ksu');

        if(!empty($Ksus)){
            foreach ($Ksus as $key => $value) {
                $ttuj_id = $this->MkCommon->filterEmptyField($value, 'Ksu', 'ttuj_id');

                $value = $this->Ksu->Ttuj->getMerge($value, $ttuj_id);
                $customer_id = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'customer_id');

                $value = $this->Ksu->Ttuj->Customer->getMerge($value, $customer_id);
                $Ksus[$key] = $value;
            }
        }

        $customers = $this->Ksu->Ttuj->Customer->getData('list', array(
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
            $Ksu = $this->Ksu->getKsu($id, 'all');

            if(!empty($Ksu)){
                $Ksu = $this->Ksu->Ttuj->getMergeList($Ksu, array(
                    'contain' => array(
                        'DriverPengganti' => array(
                            'uses' => 'Driver',
                            'primaryKey' => 'id',
                            'foreignKey' => 'driver_pengganti_id',
                            'elements' => array(
                                'branch' => false,
                            ),
                        ),
                        'Driver' => array(
                            'elements' => array(
                                'branch' => false,
                            ),
                        ),
                    ),
                ));
                $customer_id = $this->MkCommon->filterEmptyField($Ksu, 'Ttuj', 'customer_id');

                $Ksu = $this->Ksu->Ttuj->Customer->getMerge($Ksu, $customer_id);

                if(!empty($Ksu['KsuDetail'])){
                    foreach ($Ksu['KsuDetail'] as $key => $value) {
                        $Perlengkapan = $this->Ksu->KsuDetail->Perlengkapan->getData('first', array(
                            'conditions' => array(
                                'Perlengkapan.id' => $value['perlengkapan_id']
                            ),
                        ));

                        if(!empty($Perlengkapan)){
                            $Ksu['KsuDetail'][$key]['Perlengkapan'] = $Perlengkapan['Perlengkapan'];
                        }
                    }
                }
                $this->MkCommon->getLogs($this->params['controller'], array( 'ksu_add', 'ksu_edit', 'ksu_toggle' ), $id);

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
        $this->loadModel('Ksu');
        $this->set('sub_module_title', __('Tambah KSU'));
        $this->DoKsu();
    }

    function ksu_edit($id){
        $this->loadModel('Ksu');
        $this->set('sub_module_title', 'Rubah KSU');
        $Ksu = $this->Ksu->getData('first', array(
            'conditions' => array(
                'Ksu.id' => $id,
                'Ksu.paid' => 0,
                'Ksu.completed' => 0,
            ),
        ));

        if(!empty($Ksu)){
            $this->MkCommon->getLogs($this->params['controller'], array( 'ksu_add', 'ksu_edit', 'ksu_toggle' ), $id);
            $this->DoKsu($id, $Ksu);
        }else{
            $this->MkCommon->setCustomFlash(__('KSU tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'lkus',
                'action' => 'ksus'
            ));
        }
    }

    function DoKsu($id = false, $data_local = false){
        $this->loadModel('Ttuj');
        $this->loadModel('TtujPerlengkapan');

        $ttuj_id = false;

        if(!empty($this->request->data)){
            $data = $this->request->data;
            $no_ttuj = $this->MkCommon->filterEmptyField($data, 'Ksu', 'no_ttuj');
            $kekurangan_atpm = $this->MkCommon->filterEmptyField($data, 'Ksu', 'kekurangan_atpm');

            if( !empty($no_ttuj) ) {
                $ttuj = $this->Ttuj->getMerge(array(), $no_ttuj, 'Ttuj.no_ttuj');
                $ttuj_id = $this->MkCommon->filterEmptyField($ttuj, 'Ttuj', 'id');
            }

            $data['Ksu']['branch_id'] = Configure::read('__Site.config_branch_id');
            $data['Ksu']['ttuj_id'] = $ttuj_id;
            
            if($id && $data_local){
                $this->Ksu->id = $id;
                $msg = 'mengubah';
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
            
            if( empty($data['Ksu']['completed']) ) {
                $data['Ksu']['completed_desc'] = NULL;
                $data['Ksu']['completed_nodoc'] = NULL;
                $data['Ksu']['completed_date'] = NULL;
            } else {
                $data['Ksu']['completed_date'] = (!empty($data['Ksu']['completed_date'])) ? $this->MkCommon->getDate($data['Ksu']['completed_date']) : '';
            }

            if( !empty($data['KsuDetail']['perlengkapan_id']) ) {
                $data['KsuDetail']['perlengkapan_id'] = array_filter($data['KsuDetail']['perlengkapan_id']);
            }

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
                    } else {
                        $validate_ksu_detail = false;
                    }
                }
            } else if( !empty($kekurangan_atpm) ) {
                $validate_ksu_detail = true;
                $total_choosen = 1;
            } else {
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

                    if( !empty($temp_detail) ) {
                        foreach ($temp_detail as $key => $value) {
                            $this->Ksu->KsuDetail->create();
                            $value['KsuDetail']['ksu_id'] = $ksu_id;

                            $this->Ksu->KsuDetail->set($value);
                            $this->Ksu->KsuDetail->save();
                        }
                    }

                    $this->params['old_data'] = $data_local;
                    $this->params['data'] = $data;

                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s KSU'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Berhasil %s KSU #%s'), $msg, $ksu_id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $ksu_id );

                    $this->redirect(array(
                        'controller' => 'lkus',
                        'action' => 'ksus',
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s KSU'), $msg), 'error');
                    $this->Log->logActivity( sprintf(__('Berhasil %s KSU #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s KSU'), $msg), 'error');
            }
        } else if($id && $data_local){
            $ttuj_id = $this->MkCommon->filterEmptyField($data_local, 'Ksu', 'ttuj_id');
            $data_local = $this->Ksu->KsuDetail->getMerge($data_local, $id);
            $ttuj = $this->Ttuj->getMerge(array(), $ttuj_id);

            $data_local['Ksu']['no_ttuj'] = $this->MkCommon->filterEmptyField($ttuj, 'Ttuj', 'no_ttuj');
            $this->request->data = $data_local;

            if(!empty($this->request->data['KsuDetail'])){
                foreach ($this->request->data['KsuDetail'] as $key => $value) {
                    $perlengkapan = $this->Ksu->KsuDetail->Perlengkapan->getData('first', array(
                        'conditions' => array(
                            'Perlengkapan.id' => $value['KsuDetail']['perlengkapan_id']
                        ),
                    ));
                    if(!empty($perlengkapan)){
                        $Ttuj_perlengkapan = $this->TtujPerlengkapan->getData('first', array(
                            'conditions' => array(
                                'TtujPerlengkapan.ttuj_id' => $ttuj_id,
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

                    $perlengkapan = $this->Ksu->KsuDetail->Perlengkapan->getData('first', array(
                        'conditions' => array(
                            'Perlengkapan.id' => $value,
                            'Perlengkapan.jenis_perlengkapan_id' => 2
                        ),
                    ));
                    if(!empty($perlengkapan)){
                        $Ttuj_perlengkapan = $this->TtujPerlengkapan->getData('first', array(
                            'conditions' => array(
                                'TtujPerlengkapan.ttuj_id' => $ttuj_id,
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

        // $ttujs = $this->Ttuj->getData('list', array(
        //     'fields' => array(
        //         'Ttuj.id', 'Ttuj.no_ttuj'
        //     ),
        //     'conditions' => array(
        //         'OR' => array(
        //             array(
        //                 'Ttuj.is_bongkaran' => 1,
        //                 'Ttuj.is_draft' => 0,
        //                 'Ttuj.status' => 1,
        //             ),
        //             array(
        //                 'Ttuj.id' => !empty($data_local['Ksu']['ttuj_id']) ? $data_local['Ksu']['ttuj_id'] : false
        //             )
        //         )
        //     ),
        // ), true, array(
        //     'status' => 'all',
        // ));

        if(!empty($ttuj)){
            $perlengkapan_list = array();

            $ttuj = $this->Ttuj->TtujPerlengkapan->getMerge($ttuj, $ttuj_id);
            $ttuj = $this->Ttuj->getMergeList($ttuj, array(
                'contain' => array(
                    'DriverPengganti' => array(
                        'uses' => 'Driver',
                        'primaryKey' => 'id',
                        'foreignKey' => 'driver_pengganti_id',
                        'elements' => array(
                            'branch' => false,
                        ),
                    ),
                    'Driver' => array(
                        'elements' => array(
                            'branch' => false,
                        ),
                    ),
                ),
            ));
            $ttuj['Ttuj']['driver_name'] = $this->MkCommon->filterEmptyField($ttuj, 'Driver', 'driver_name');

            if(!empty($ttuj['TtujPerlengkapan'])){
                foreach ($ttuj['TtujPerlengkapan'] as $key => $value) {
                    $perlengkapan_data = $this->Ksu->KsuDetail->Perlengkapan->getData('first', array(
                        'conditions' => array(
                            'Perlengkapan.id' => $value['TtujPerlengkapan']['perlengkapan_id'],
                        )
                    ));
            
                    if( !empty($perlengkapan_data) ) {
                        $perlengkapan_list[$perlengkapan_data['Perlengkapan']['id']] = $perlengkapan_data['Perlengkapan']['name'];
                    }
                }
            }
            $this->request->data = array_merge($this->request->data, $ttuj);
        }

        $perlengkapans = $this->Ksu->KsuDetail->Perlengkapan->getListPerlengkapan(2);
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
                $this->MkCommon->setCustomFlash(__('Sukses mengubah status.'), 'success');
                $this->Log->logActivity( sprintf(__('Sukses mengubah status KSU ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal mengubah status.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal mengubah status KSU ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Ksu tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    function ksu_payments() {
        $this->loadModel('KsuPayment');

        $this->set('active_menu', 'ksu_payments');
        $this->set('sub_module_title', __('Data Pembayaran KSU'));
        
        $params = $this->MkCommon->_callRefineParams($this->params);
        $options =  $this->KsuPayment->_callRefineParams($params, array(
            'order' => array(
                'KsuPayment.created' => 'DESC'
            )
        ));
        $this->paginate = $this->KsuPayment->getData('paginate', $options, true, array(
            'status' => 'all',
        ));
        $payments = $this->paginate('KsuPayment');

        if( !empty($payments) ) {
            foreach ($payments as $key => $payment) {
                $payment = $this->KsuPayment->Customer->getMerge($payment, $payment['KsuPayment']['customer_id']);
                $payments[$key] = $payment;
            }
        }

        $this->set('payments', $payments);

        $customers = $this->KsuPayment->Customer->getData('list', array(
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
        $this->set('sub_module_title', 'Edit Pembayaran KSU');
        $value = $this->KsuPayment->getData('first', array(
            'conditions' => array(
                'KsuPayment.id' => $id
            ),
        ));

        if(!empty($value)){
            $transaction_status = $this->MkCommon->filterEmptyField($value, 'KsuPayment', 'transaction_status');
            $value = $this->KsuPayment->KsuPaymentDetail->getMerge($value, $id);

            if( $transaction_status == 'posting' ) {
                $this->MkCommon->setCustomFlash(__('Data tidak ditemukan'), 'error');
                $this->redirect($this->referer());
                die();
            }

            $this->MkCommon->getLogs($this->params['controller'], array( 'ksu_payment_add', 'ksu_payment_edit', 'ksu_payment_delete' ), $id);
            $this->DoKsuPayment($id, $value);
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
        $this->loadModel('KsuPayment');

        if(!empty($this->request->data)){
            $data = $this->request->data;
            $data = $this->MkCommon->dataConverter($data, array(
                'date' => array(
                    'KsuPayment' => array(
                        'tgl_bayar',
                    ),
                )
            ));
            $tgl_bayar = $this->MkCommon->filterEmptyField($data, 'KsuPayment', 'tgl_bayar');
            $this->MkCommon->_callAllowClosing($data, 'KsuPayment', 'tgl_bayar');
            $data = Common::_callCheckCostCenter($data, 'LkuKsuPayment', 'KsuPayment');

            $customer_id = $this->MkCommon->filterEmptyField($data, 'KsuPayment', 'customer_id');
            $coa_id = $this->MkCommon->filterEmptyField($data, 'KsuPayment', 'coa_id');
            $transaction_status = $this->MkCommon->filterEmptyField($data, 'KsuPayment', 'transaction_status');
            $cogs_id = $this->MkCommon->filterEmptyField($data, 'KsuPayment', 'cogs_id');

            $customer = $this->KsuPayment->Customer->getMerge(array(), $customer_id);
            $customer_name = $this->MkCommon->filterEmptyField($customer, 'Customer', 'customer_name_code');

            if($id && $data_local){
                $this->KsuPayment->id = $id;
                $msg = 'mengubah';
            }else{
                $this->KsuPayment->create();
                $msg = 'menambah';
            }
            
            $data['KsuPayment']['branch_id'] = Configure::read('__Site.config_branch_id');
            $total_price = 0;

            $validate_ksu_detail = true;
            $validate_price_pay = true;
            if(!empty($data['KsuPaymentDetail']['ksu_detail_id'])){
                foreach ($data['KsuPaymentDetail']['ksu_detail_id'] as $key => $value) {
                    if(!empty($value)){
                        $price = (!empty($data['KsuPaymentDetail']['total_biaya_klaim'][$key])) ? $this->MkCommon->convertPriceToString($data['KsuPaymentDetail']['total_biaya_klaim'][$key]) : 0;
                        $data_detail['KsuPaymentDetail'] = array(
                            'ksu_detail_id' => $value,
                            'total_biaya_klaim' => $price
                        );

                        if(empty($price) || empty($data['KsuPaymentDetail']['total_biaya_klaim'][$value])){
                            $validate_ksu_detail = false;
                            break;
                        }else{
                            $ksu_has_paid = $this->KsuPayment->KsuPaymentDetail->getData('first', array(
                                'conditions' => array(
                                    'KsuPaymentDetail.ksu_detail_id' => $value,
                                    'KsuPayment.status' => 1,
                                    'KsuPayment.is_void' => 0,
                                    'KsuPayment.id <>' => $id,
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

                    if( $transaction_status == 'posting' ) {
                        if( !empty($total_price) ) {
                            $document_no = !empty($data['KsuPayment']['no_doc'])?$data['KsuPayment']['no_doc']:false;
                            $title = sprintf(__('Pembayaran KSU kepada customer %s'), $customer_name);
                            $title = $this->MkCommon->filterEmptyField($data, 'KsuPayment', 'description', $title);

                            $this->User->Journal->setJournal($total_price, array(
                                'credit' => $coa_id,
                                'debit' => 'ksu_payment_coa_id',
                            ), array(
                                'cogs_id' => $cogs_id,
                                'date' => $tgl_bayar,
                                'document_id' => $ksu_payment_id,
                                'title' => $title,
                                'document_no' => $document_no,
                                'type' => 'ksu_payment',
                            ));
                        }
                    }

                    if($id && $data_local){
                        $this->KsuPayment->KsuPaymentDetail->deleteAll(array(
                            'KsuPaymentDetail.ksu_payment_id' => $ksu_payment_id
                        ));
                    }
                
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

                                if( $transaction_status == 'posting' ) {
                                    $default_conditions_detail = array(
                                        'KsuPaymentDetail.ksu_detail_id' => $ksu_detail_id,
                                        'KsuPaymentDetail.status' => 1,
                                        'KsuPayment.transaction_status' => 'posting',
                                    );

                                    $ksu_has_paid = $this->KsuPayment->KsuPaymentDetail->getData('first', array(
                                        'conditions' => $default_conditions_detail,
                                        'fields' => array(
                                            '*',
                                            'SUM(KsuPaymentDetail.total_biaya_klaim) as ksu_has_paid'
                                        ),
                                        'contain' => array(
                                            'KsuDetail',
                                            'KsuPayment',
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
                        }

                        if( $transaction_status == 'posting' ) {
                            if(!empty($collect_ksu_detail_id)){
                                $this->updateStatusKsu($collect_ksu_detail_id);
                            }
                        }
                    }

                    $this->params['old_data'] = $data_local;
                    $this->params['data'] = $data;

                    $noref = str_pad($ksu_payment_id, 6, '0', STR_PAD_LEFT);
                    $this->Log->logActivity( sprintf(__('Sukses %s Pembayaran LKU ID #%s'), $msg, $ksu_payment_id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $ksu_payment_id );
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Pembayaran LKU #%s'), $msg, $noref), 'success');
                    $this->redirect(array(
                        'controller' => 'lkus',
                        'action' => 'ksu_payments',
                    ));

                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Pembayaran LKU'), $msg), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal %s Pembayaran LKU #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );  
                }
            }else{
                $text = sprintf(__('Gagal %s pembayaran ksu'), $msg);

                if( !$validate_ksu_detail ){
                    $text .= ', mohon isi field pembayaran';
                }
                if(!$validate_price_pay){
                    $text .= ', Total Pembayaran tidak boleh lebih besar dari total LKU';
                }

                $this->MkCommon->setCustomFlash($text, 'error');
            }
            
            $this->request->data['KsuPayment']['cogs_id'] = Common::hashEmptyField($data, 'KsuPayment.cogs_id');
        } else if($id && $data_local){
            $dataDetail = $this->MkCommon->filterEmptyField($data_local, 'KsuPaymentDetail');
            unset($data_local['KsuPaymentDetail']);

            $this->request->data = $data_local;
            $this->request->data['KsuPayment']['tgl_bayar'] = (!empty($this->request->data['KsuPayment']['tgl_bayar'])) ? $this->MkCommon->getDate($this->request->data['KsuPayment']['tgl_bayar'], true) : '';

            if( !empty($dataDetail) ) {
                foreach ($dataDetail as $key => $value) {
                    $ksu_detail_id = $this->MkCommon->filterEmptyField($value, 'KsuPaymentDetail', 'ksu_detail_id');
                    $total_biaya_klaim = $this->MkCommon->filterEmptyField($value, 'KsuPaymentDetail', 'total_biaya_klaim');

                    $this->request->data['KsuPaymentDetail']['ksu_detail_id'][$ksu_detail_id] = $ksu_detail_id;
                    $this->request->data['KsuPaymentDetail']['total_biaya_klaim'][$ksu_detail_id] = $total_biaya_klaim;
                }
            }

            $data = $this->request->data;
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
                    );

                    if( empty($id) ) {
                        $ksu_condition['KsuDetail.complete_paid'] = 0;
                    }

                    $ksu_data = $this->Ksu->KsuDetail->getData('first', array(
                        'conditions' => $ksu_condition,
                        'contain' => array(
                            'Ksu',
                            'Perlengkapan',
                        )
                    ));
                    
                    if(!empty($ksu_data)){
                        $ttuj = $this->Ksu->Ttuj->getData('first', array(
                            'conditions' => array(
                                'Ttuj.id' => $ksu_data['Ksu']['ttuj_id'],
                            )
                        ), true, array(
                            'status' => 'all',
                        ));
                        $ttuj = $this->Ksu->Ttuj->getMergeList($ttuj, array(
                            'contain' => array(
                                'DriverPengganti' => array(
                                    'uses' => 'Driver',
                                    'primaryKey' => 'id',
                                    'foreignKey' => 'driver_pengganti_id',
                                    'elements' => array(
                                        'branch' => false,
                                    ),
                                ),
                                'Driver' => array(
                                    'elements' => array(
                                        'branch' => false,
                                    ),
                                ),
                            ),
                        ));

                        if(!empty($ttuj)){
                            $ksu_data = array_merge($ksu_data, $ttuj);
                        }

                        $ksu_has_paid = $this->KsuPayment->KsuPaymentDetail->getData('first', array(
                            'conditions' => array(
                                'KsuPaymentDetail.ksu_detail_id' => $ksu_data['KsuDetail']['id'],
                                'KsuPaymentDetail.ksu_payment_id <>' => $id,
                                'KsuPaymentDetail.status' => 1,
                                'KsuPayment.transaction_status' => 'posting',
                            ),
                            'fields' => array(
                                'SUM(KsuPaymentDetail.total_biaya_klaim) as ksu_has_paid'
                            ),
                            'contain' => array(
                                'KsuPayment',
                            ),
                        ));

                        $ksu_details[$key]['ksu_has_paid'] = $ksu_has_paid[0]['ksu_has_paid'];
                        $ksu_details[$key] = array_merge($ksu_details[$key], $ksu_data);
                    }
                }
            }
            
            $this->set(compact('ksu_details'));
        }

        $this->Ksu->Ttuj->bindModel(array(
            'belongsTo' => array(
                'CustomerNoType' => array(
                    'className' => 'CustomerNoType',
                    'foreignKey' => 'customer_id',
                ),
            ),
        ), false);

        $ttuj_customer_id = array();
        $customer_id = $this->MkCommon->filterEmptyField($data_local, 'KsuPayment', 'customer_id');

        if(!empty($this->request->data['KsuPayment']['customer_id'])){
            $ttuj_customer_id = $this->Ksu->Ttuj->getData('list', array(
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
                        'Ksu.kekurangan_atpm' => 0,
                        'Ksu.completed' => 0,
                    ),
                    array(
                        'Ksu.id' => $ttuj_customer_id,
                        'Ksu.paid' => array(0,1),
                        'Ksu.kekurangan_atpm' => 0,
                        'Ksu.completed' => 0,
                    ),
                    array(
                        'Ttuj.customer_id' => $customer_id,
                    ),
                ),
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
                $dataCust = $this->KsuPayment->Customer->getData('first', array(
                    'conditions' => array(
                        'Customer.id' => $customer_id,
                    ),
                ), true, array(
                    'status' => 'all',
                    'branch' => false,
                ));

                if( !empty($dataCust) ) {
                    $ttujs[$customer_id] = $dataCust['Customer']['customer_name_code'];
                }
            }
        }

        $coas = $this->GroupBranch->Branch->BranchCoa->getCoas();
        $cogs = $this->MkCommon->_callCogsOptGroup('LkuKsuPayment', 'KsuPayment');

        $this->MkCommon->_layout_file('select');
        $this->set(compact(
            'list_customer', 'id', 'action',
            'coas', 'ttujs', 'data_local'
        ));
        $this->set('active_menu', 'ksu_payments');
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
            $this->MkCommon->_callAllowClosing($payments, 'LkuPayment', 'tgl_bayar');

            $customer_id = $this->MkCommon->filterEmptyField($payments, 'LkuPayment', 'customer_id');
            $coa_id = $this->MkCommon->filterEmptyField($payments, 'LkuPayment', 'coa_id');
            $grandtotal = $this->MkCommon->filterEmptyField($payments, 'LkuPayment', 'grandtotal');
            $no_doc = $this->MkCommon->filterEmptyField($payments, 'LkuPayment', 'no_doc');
            $tgl_bayar = $this->MkCommon->filterEmptyField($payments, 'LkuPayment', 'tgl_bayar');
            $transaction_status = $this->MkCommon->filterEmptyField($payments, 'LkuPayment', 'transaction_status');
            $cogs_id = $this->MkCommon->filterEmptyField($payments, 'LkuPayment', 'cogs_id');

            $customer = $this->LkuPayment->Customer->getMerge(array(), $customer_id);
            $customer_name = $this->MkCommon->filterEmptyField($customer, 'Customer', 'customer_name_code');
            $collect_lku_detail_id = array();

            if(!empty($payments['LkuPaymentDetail'])){
                if( $transaction_status == 'posting' ) {
                    foreach ($payments['LkuPaymentDetail'] as $key => $value) {
                        $lku_detail_id = $value['lku_detail_id'];
                        array_push($collect_lku_detail_id, $lku_detail_id);

                        $lku_has_paid = $this->LkuPayment->LkuPaymentDetail->getData('first', array(
                            'conditions' => array(
                                'LkuPaymentDetail.lku_detail_id' => $lku_detail_id,
                                'LkuPayment.status' => 1,
                                'LkuPayment.transaction_status' => 'posting',
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
                $title = sprintf(__('pembayaran LKU kepada customer %s'), $customer_name);
                $title = sprintf(__('<i>Pembatalan</i> %s'), $this->MkCommon->filterEmptyField($payments, 'LkuPayment', 'description', $title));

                if( $transaction_status == 'posting' ) {
                    $this->User->Journal->setJournal($grandtotal, array(
                        'credit' => 'lku_payment_coa_id',
                        'debit' => $coa_id,
                    ), array(
                        'cogs_id' => $cogs_id,
                        'date' => $tgl_bayar,
                        'document_id' => $id,
                        'title' => $title,
                        'document_no' => $no_doc,
                        'type' => 'lku_payment_void',
                    ));

                    if(!empty($collect_lku_detail_id)){
                        $this->updateStatusLku($collect_lku_detail_id);
                    }
                }

                $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
                $this->MkCommon->setCustomFlash(sprintf(__('Berhasil menghapus pembayaran LKU #%s'), $noref), 'success');
                $this->Log->logActivity( sprintf(__('Berhasil menghapus pembayaran LKU ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id ); 
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal menghapus pembayaran LKU'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal menghapus pembayaran LKU ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id ); 
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
            $this->MkCommon->_callAllowClosing($payments, 'KsuPayment', 'tgl_bayar');
            
            $customer_id = $this->MkCommon->filterEmptyField($payments, 'KsuPayment', 'customer_id');
            $coa_id = $this->MkCommon->filterEmptyField($payments, 'KsuPayment', 'coa_id');
            $grandtotal = $this->MkCommon->filterEmptyField($payments, 'KsuPayment', 'grandtotal');
            $no_doc = $this->MkCommon->filterEmptyField($payments, 'KsuPayment', 'no_doc');
            $tgl_bayar = $this->MkCommon->filterEmptyField($payments, 'KsuPayment', 'tgl_bayar');
            $transaction_status = $this->MkCommon->filterEmptyField($payments, 'KsuPayment', 'transaction_status');
            $cogs_id = $this->MkCommon->filterEmptyField($payments, 'KsuPayment', 'cogs_id');

            $customer = $this->KsuPayment->Customer->getMerge(array(), $customer_id);
            $customer_name = $this->MkCommon->filterEmptyField($customer, 'Customer', 'customer_name_code');
            $collect_ksu_detail_id = array();

            if(!empty($payments['KsuPaymentDetail'])){
                if( $transaction_status == 'posting' ) {
                    foreach ($payments['KsuPaymentDetail'] as $key => $value) {
                        $ksu_detail_id = $value['ksu_detail_id'];
                        array_push($collect_ksu_detail_id, $ksu_detail_id);

                        $ksu_has_paid = $this->KsuPayment->KsuPaymentDetail->getData('first', array(
                            'conditions' => array(
                                'KsuPaymentDetail.ksu_detail_id' => $ksu_detail_id,
                                'KsuPayment.status' => 1,
                                'KsuPayment.transaction_status' => 'posting',
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
                $title = sprintf(__('pembayaran KSU kepada customer %s'), $customer_name);
                $title = sprintf(__('<i>Pembatalan</i> %s'), $this->MkCommon->filterEmptyField($payments, 'KsuPayment', 'description', $title));

                if( $transaction_status == 'posting' ) {
                    $this->User->Journal->setJournal($grandtotal, array(
                        'credit' => 'ksu_payment_coa_id',
                        'debit' => $coa_id,
                    ), array(
                        'cogs_id' => $cogs_id,
                        'date' => $tgl_bayar,
                        'document_id' => $id,
                        'title' => $title,
                        'document_no' => $no_doc,
                        'type' => 'ksu_payment_void',
                    ));
                }

                if(!empty($collect_ksu_detail_id)){
                    $this->updateStatusKsu($collect_ksu_detail_id);
                }

                $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
                $this->MkCommon->setCustomFlash(sprintf(__('Berhasil menghapus pembayaran KSU #%s'), $noref), 'success');
                $this->Log->logActivity( sprintf(__('Berhasil menghapus pembayaran KSU ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id ); 
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal menghapus pembayaran KSU'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal menghapus pembayaran KSU ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id ); 
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
                $coa_id = $this->MkCommon->filterEmptyField($LkuPayment, 'LkuPayment', 'coa_id');
                $LkuPayment = $this->LkuPayment->getMergeList($LkuPayment, array(
                    'contain' => array(
                        'Cogs',
                        'Coa',
                    ),
                ));

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
                            $ttuj_id = $this->MkCommon->filterEmptyField($lku, 'Lku', 'ttuj_id');
                            $ttuj = $this->LkuPayment->LkuPaymentDetail->LkuDetail->Lku->Ttuj->getMerge(array(), $ttuj_id);
                            $ttuj = $this->LkuPayment->LkuPaymentDetail->LkuDetail->Lku->Ttuj->getMergeList($ttuj, array(
                                'contain' => array(
                                    'DriverPengganti' => array(
                                        'uses' => 'Driver',
                                        'primaryKey' => 'id',
                                        'foreignKey' => 'driver_pengganti_id',
                                        'elements' => array(
                                            'branch' => false,
                                        ),
                                    ),
                                    'Driver' => array(
                                        'elements' => array(
                                            'branch' => false,
                                        ),
                                    ),
                                ),
                            ));

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

                            if( !empty($ttuj) ) {
                                $LkuPayment['LkuPaymentDetail'][$key] = array_merge($LkuPayment['LkuPaymentDetail'][$key], $ttuj);
                            }
                        }
                    }
                }
                
                $this->MkCommon->getLogs($this->params['controller'], array( 'ksu_payment_add', 'ksu_payment_edit', 'ksu_payment_delete' ), $id);
                
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

            $KsuPayment = $this->KsuPayment->getKsuPayment($id);
            
            if(!empty($KsuPayment)){
                $coa_id = $this->MkCommon->filterEmptyField($KsuPayment, 'KsuPayment', 'coa_id');
                $KsuPayment = $this->KsuPayment->getMergeList($KsuPayment, array(
                    'contain' => array(
                        'Cogs',
                        'Coa',
                    ),
                ));

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
                            $ttuj_id = $this->MkCommon->filterEmptyField($ksu, 'Ksu', 'ttuj_id');
                            $ttuj = $this->KsuPayment->KsuPaymentDetail->KsuDetail->Ksu->Ttuj->getMerge(array(), $ttuj_id);
                            $ttuj = $this->KsuPayment->KsuPaymentDetail->KsuDetail->Ksu->Ttuj->getMergeList($ttuj, array(
                                'contain' => array(
                                    'DriverPengganti' => array(
                                        'uses' => 'Driver',
                                        'primaryKey' => 'id',
                                        'foreignKey' => 'driver_pengganti_id',
                                        'elements' => array(
                                            'branch' => false,
                                        ),
                                    ),
                                    'Driver' => array(
                                        'elements' => array(
                                            'branch' => false,
                                        ),
                                    ),
                                ),
                            ));

                            if(!empty($ksu['KsuDetail']['perlengkapan_id'])){
                                $Perlengkapan = $this->KsuPayment->KsuPaymentDetail->KsuDetail->Perlengkapan->getData('first', array(
                                    'conditions' => array(
                                        'Perlengkapan.id' => $ksu['KsuDetail']['perlengkapan_id']
                                    )
                                ));
                            }
                            $KsuPayment['KsuPaymentDetail'][$key]['Perlengkapan'] = !empty($Perlengkapan['Perlengkapan']) ? $Perlengkapan['Perlengkapan'] : array();
                            $KsuPayment['KsuPaymentDetail'][$key]['KsuDetail'] = $ksu['KsuDetail'];
                            $KsuPayment['KsuPaymentDetail'][$key]['Ksu'] = $ksu['Ksu'];

                            if( !empty($ttuj) ) {
                                $KsuPayment['KsuPaymentDetail'][$key] = array_merge($KsuPayment['KsuPaymentDetail'][$key], $ttuj);
                            }
                        }
                    }
                }
                
                $this->MkCommon->getLogs($this->params['controller'], array( 'ksu_payment_add', 'ksu_payment_edit', 'ksu_payment_delete' ), $id);
                $sub_module_title = __('Detail Pembayaran KSU');
                $this->set(compact('KsuPayment', 'sub_module_title'));
                $this->set('active_menu', 'ksu_payments');
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
                            'LkuPayment.transaction_status' => 'posting',
                        ),
                        'fields' => array(
                            '*',
                            'SUM(LkuPaymentDetail.total_biaya_klaim) as lku_has_paid'
                        ),
                        'contain' => array(
                            'LkuPayment',
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
                            'KsuPaymentDetail.status' => 1,
                            'KsuPayment.transaction_status' => 'posting',
                        ),
                        'fields' => array(
                            '*',
                            'SUM(KsuPaymentDetail.total_biaya_klaim) as ksu_has_paid'
                        ),
                        'contain' => array(
                            'KsuPayment',
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

    public function reports( $type = 'lku', $data_action = false ) {
        if( in_array($type, array( 'lku', 'ksu' )) ) {
            switch ($type) {
                case 'ksu':
                    $modelName = 'Ksu';
                    break;
                
                default:
                    $modelName = 'Lku';
                    break;
            }

            $this->loadModel($modelName);

            $dateFrom = date('Y-m-d', strtotime('-1 month'));
            $dateTo = date('Y-m-d');
            $allow_branch_id = Configure::read('__Site.config_allow_branch_id');
            $conditions = array(
                $modelName.'.branch_id' => $allow_branch_id,
            );

            if(!empty($this->params['named'])){
                $refine = $this->params['named'];

                if(!empty($refine['nodoc'])){
                    $value = urldecode($refine['nodoc']);
                    $this->request->data['Lku']['no_doc'] = $value;
                    $conditions[$modelName.'.no_doc LIKE '] = '%'.$value.'%';
                }

                if(!empty($refine['status'])){
                    $value = urldecode($refine['status']);
                    $this->request->data['Lku']['status'] = $value;

                    switch ($value) {
                        case 'closing':
                            $conditions['AND']['OR'][$modelName.'.complete_paid'] = 1;
                            $conditions['AND']['OR'][$modelName.'.completed'] = 1;
                            break;
                        case 'pending':
                            $conditions[$modelName.'.complete_paid'] = 0;
                            $conditions[$modelName.'.completed'] = 0;
                            break;
                    }
                }

                if(!empty($refine['nopol'])){
                    $nopol = urldecode($refine['nopol']);
                    $this->request->data['Lku']['nopol'] = $nopol;
                    $typeTruck = !empty($refine['type'])?$refine['type']:1;
                    $this->request->data['Lku']['type'] = $typeTruck;

                    if( $typeTruck == 2 ) {
                        $conditionsNopol = array(
                            'Truck.id' => $nopol,
                        );
                    } else {
                        $conditionsNopol = array(
                            'Truck.nopol LIKE' => '%'.$nopol.'%',
                        );
                    }

                    $truckSearch = $this->$modelName->Ttuj->Truck->getData('list', array(
                        'conditions' => $conditionsNopol,
                        'fields' => array(
                            'Truck.id', 'Truck.id',
                        ),
                    ), true, array(
                        'branch' => false,
                    ));
                    $conditions['Ttuj.truck_id'] = $truckSearch;
                }

                if(!empty($refine['driver_name'])){
                    $value = urldecode($refine['driver_name']);
                    $this->request->data['Lku']['driver_name'] = $value;

                    $dataSearchId = $this->$modelName->Ttuj->Truck->Driver->getData('list', array(
                        'conditions' => array(
                            'Driver.driver_name LIKE' => '%'.$value.'%',
                        ),
                        'fields' => array(
                            'Driver.id', 'Driver.id',
                        ),
                    ), array(
                        'branch' => false,
                    ));
                    $conditions['OR']['Ttuj.driver_id'] = $dataSearchId;
                    $conditions['OR']['Ttuj.driver_pengganti_id'] = $dataSearchId;
                }

                if(!empty($refine['status'])){
                    $value = urldecode($refine['status']);
                    $tmpArry = array(
                        0 => 'active',
                        1 => 'completed',
                    );
                    $this->request->data['Laka']['status'] = $value;

                    if( in_array($value, $tmpArry) ) {
                        $value = array_search($value, $tmpArry);
                        $conditions['Laka.completed'] = $value;
                    }
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
                    $this->request->data['Laka']['tgl_laka'] = $dateStr;
                }

                if(!empty($refine['atpm'])){
                    $value = urldecode($refine['atpm']);
                    $this->request->data['Ksu']['atpm'] = $value;

                    switch ($value) {
                        case 'yes':
                            $conditions['Ksu.kekurangan_atpm'] = 1;
                            break;
                        case 'no':
                            $conditions['Ksu.kekurangan_atpm'] = 0;
                            break;
                    }
                }

                // Custom Otorisasi
                $conditions = $this->MkCommon->getConditionGroupBranch( $refine, $modelName, $conditions, 'conditions' );
            }

            $conditions['DATE_FORMAT('.$modelName.'.tgl_'.$type.', \'%Y-%m-%d\') >='] = $dateFrom;
            $conditions['DATE_FORMAT('.$modelName.'.tgl_'.$type.', \'%Y-%m-%d\') <='] = $dateTo;
            $options = array(
                'conditions' => $conditions,
                'order' => array(
                    $modelName.'.created' => 'ASC', 
                ),
                'contain' => array(
                    'Ttuj',
                ),
            );

            if( !empty($data_action) ) {
                $datas = $this->$modelName->getData('all', $options, true, array(
                    'status' => 'all',
                ));
            } else {
                $options['limit'] = Configure::read('__Site.config_pagination');
                $this->paginate = $this->$modelName->getData('paginate', $options, true, array(
                    'status' => 'all',
                ));
                $datas = $this->paginate($modelName);
            }

            if( !empty($datas) ) {
                foreach ($datas as $key => $value) {
                    $id = $this->MkCommon->filterEmptyField($value, $modelName, 'id');
                    $branch_id = $this->MkCommon->filterEmptyField($value, $modelName, 'branch_id');
                    $truck_id = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'truck_id');

                    $value = $this->$modelName->Ttuj->Truck->getMerge($value, $truck_id);
                    $category_id = $this->MkCommon->filterEmptyField($value, 'Truck', 'truck_category_id');

                    $value = $this->$modelName->Ttuj->Truck->TruckCategory->getMerge($value, $category_id);
                    $value = $this->$modelName->Ttuj->getMergeList($value, array(
                        'contain' => array(
                            'DriverPengganti' => array(
                                'uses' => 'Driver',
                                'primaryKey' => 'id',
                                'foreignKey' => 'driver_pengganti_id',
                                'elements' => array(
                                    'branch' => false,
                                ),
                            ),
                            'Driver' => array(
                                'elements' => array(
                                    'branch' => false,
                                ),
                            ),
                        ),
                    ));

                    if( $type == 'ksu' ) {
                        $value = $this->$modelName->KsuDetail->getGroupMerge($value, $id);
                    } else {
                        $value = $this->$modelName->LkuDetail->getGroupMerge($value, $id);
                    }

                    $datas[$key] = $value;
                }
            }

            $module_title = __('Laporan '.strtoupper($modelName));

            if( !empty($dateFrom) && !empty($dateTo) ) {
                $this->request->data['Lku']['date'] = sprintf('%s - %s', date('d/m/Y', strtotime($dateFrom)), date('d/m/Y', strtotime($dateTo)));
                $module_title .= sprintf(' Periode %s', $this->MkCommon->getCombineDate($dateFrom, $dateTo));
            }

            $this->set('sub_module_title', $module_title);
            $this->set('active_menu', $type.'_reports');

            $this->set(compact(
                'datas', 'cities', 'data_action',
                'modelName', 'type'
            ));

            if($data_action == 'pdf'){
                $this->layout = 'pdf';
            }else if($data_action == 'excel'){
                $this->layout = 'ajax';
            } else {
                $this->MkCommon->_layout_file('freeze');
            }

            $this->render('reports');
        } else {
            $this->redirect($this->referer());
        }
    }
}