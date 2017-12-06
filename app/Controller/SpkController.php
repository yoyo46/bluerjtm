<?php
App::uses('AppController', 'Controller');
class SpkController extends AppController {
    public $uses = array(
        'Spk',
    );
    public $components = array(
        'RjSpk', 'RmReport'
    );
    public $helpers = array(
        'Spk'
    );

    function beforeFilter() {
        parent::beforeFilter();
        $this->set('title_for_layout', __('ERP RJTM | Gudang'));
        $this->set('module_title', __('Gudang'));
    }

    function search( $index = 'index', $id = null ){
        $refine = array();
        if(!empty($this->request->data)) {
            $data = $this->request->data;
            $params = $this->MkCommon->getRefineGroupBranch(array(), $data);
            $result = $this->MkCommon->processFilter($data);
            
            if(!empty($id)){
                array_push($params, $id);
            }

            $params = array_merge($params, $result);
            $params['action'] = $index;

            $this->redirect($params);
        }
        $this->redirect('/');
    }

    public function index() {
        $this->set('sub_module_title', __('SPK'));
        
        $dateFrom = date('Y-m-01', strtotime('-1 Month'));
        $dateTo = date('Y-m-d');

        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));
        $role = $this->MkCommon->filterEmptyField($params, 'named', 'status');
        $options =  $this->Spk->_callRefineParams($params);

        $this->paginate = $this->Spk->getData('paginate', $options, array(
            'role' => $role,
            'status' => 'all',
        ));
        $values = $this->paginate('Spk');
        $values = $this->Spk->getMergeList($values, array(
            'contain' => array(
                'Vendor' => array(
                    'elements' => array(
                        'status' => 'all',
                    ),
                ),
                'Employe',
                'Truck',
            ),
        ));

        $this->RjSpk->_callBeforeRender();

        $settings = $this->MkCommon->_callSettingGeneral('Product', 'spk_internal_policy', false);
        $spk_internal_policy = $this->MkCommon->filterEmptyField($settings, 'Product', 'spk_internal_policy');

        $this->set('active_menu', 'spk');
        $this->set(compact(
            'values', 'spk_internal_policy'
        ));
    }

    function add(){
        $this->set('sub_module_title', __('Buat SPK'));

        $data = $this->request->data;

        if( !empty($data) ) {
            $data = $this->RjSpk->_callBeforeSave($data);
            $result = $this->Spk->doSave($data);
            $this->MkCommon->setProcessParams($result, array(
                'controller' => 'spk',
                'action' => 'index',
                'admin' => false,
            ));
        }

        $this->RjSpk->_callSpkBeforeRender($data);

        $this->set(array(
            'active_menu' => 'spk',
        ));
    }

    function edit( $id = false ){
        $this->set('sub_module_title', __('Edit SPK'));

        $value = $this->Spk->getData('first', array(
            'conditions' => array(
                'Spk.id' => $id,
            ),
        ), array(
            'role' => 'open',
        ));

        if( !empty($value) ) {
            $value = $this->Spk->getMergeList($value, array(
                'contain' => array(
                    'SpkProduct' => array(
                        'contain' => array(
                            'SpkProductTire',
                        ),
                    ),
                    'SpkProduction',
                    'SpkMechanic',
                    'Truck',
                ),
            ));
            $data = $this->request->data;

            if( !empty($data) ) {
                $data = $this->RjSpk->_callBeforeSave($data);
                $result = $this->Spk->doSave($data, $value, $id);
                $this->MkCommon->setProcessParams($result, array(
                    'controller' => 'spk',
                    'action' => 'index',
                    'admin' => false,
                ));
            }

            $this->RjSpk->_callSpkBeforeRender($data, $value);

            $this->set(array(
                'active_menu' => 'spk',
            ));
            $this->render('add');
        } else {
            $this->MkCommon->redirectReferer(__('Penerimaan tidak ditemukan.'), 'error');
        }
    }

    function detail( $id = false ){
        $this->set('sub_module_title', __('Lihat SPK'));

        $value = $this->Spk->getData('first', array(
            'conditions' => array(
                'Spk.id' => $id,
            ),
        ), array(
            'status' => 'all',
        ));

        if( !empty($value) ) {
            $value = $this->Spk->getMergeList($value, array(
                'contain' => array(
                    'SpkProduct' => array(
                        'contain' => array(
                            'SpkProductTire',
                        ),
                    ),
                    'SpkProduction',
                    'SpkMechanic',
                    'Truck',
                ),
            ));

            $this->RjSpk->_callSpkBeforeRender(array(), $value);

            $this->set(array(
                'view' => true,
                'active_menu' => 'spk',
            ));
            $this->render('add');
        } else {
            $this->MkCommon->redirectReferer(__('Penerimaan tidak ditemukan.'), 'error');
        }
    }

    public function toggle( $id = false ) {
        $result = $this->Spk->doDelete( $id );
        $this->MkCommon->setProcessParams($result);
    }

    function completed($id = null){
        $is_ajax = $this->RequestHandler->isAjax();
        $msg = array(
            'msg' => '',
            'type' => 'error'
        );
        $value = $this->Spk->getData('first', array(
            'conditions' => array(
                'Spk.id' => $id
            ),
        ));

        if( !empty($value) ){
            $data = $this->request->data;

            if(!empty($data['Spk']['complete_date'])){
                $data = $this->MkCommon->dataConverter($data, array(
                    'date' => array(
                        'Spk' => array(
                            'complete_date',
                        ),
                    )
                ));
                $complete_date = Common::hashEmptyField($data, 'Spk.complete_date');
                $complete_time = Common::hashEmptyField($data, 'Spk.complete_time');

                if( !empty($complete_date) && !empty($complete_time) ) {
                    $this->Spk->set('complete_date', __('%s %s', $complete_date, $complete_time));
                }

                $this->Spk->id = $id;
                $this->Spk->set('transaction_status', 'finish');

                if($this->Spk->save()){
                    $msg = array(
                        'msg' => __('Berhasil mengubah status SPK menjadi selesai.'),
                        'type' => 'success'
                    );
                    $this->MkCommon->setCustomFlash( $msg['msg'], $msg['type']);  
                    $this->Log->logActivity( sprintf(__('Berhasil mengubah status SPK menjadi selesai #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id ); 
                }else{
                    $this->Log->logActivity( sprintf(__('Gagal mengubah status SPK menjadi selesai #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id ); 
                }
            } else {
                $this->request->data['Spk']['complete_date'] = date('d/m/Y');
            }
        }else{
            $msg = array(
                'msg' => __('SPK tidak ditemukan'),
                'type' => 'error'
            );
        }

        $modelName = 'Spk';
        $this->set(array(
            'message_alert' => __('Mohon masukan tanggal SPK ini selesai.'),
            '_flash' => false,
        ));
        $this->set(compact(
            'msg', 'is_ajax',
            'modelName'
        ));
        $this->render('/Elements/blocks/common/only_date');
    }

    public function history( $id = null ) {
        $value = $this->Spk->Truck->getData('first', array(
            'conditions' => array(
                'Truck.id' => $id,
            )
        ), true, array(
            'plant' => true,
        ));

        if( !empty($value) ) {
            $params = $this->MkCommon->_callRefineParams($this->params);
            $role = $this->MkCommon->filterEmptyField($params, 'named', 'status');
            $options =  $this->Spk->SpkProduct->_callRefineParams($params, array(
                'conditions' => array(
                    'Spk.truck_id' => $id,
                    'SpkProduct.status' => 1,
                ),
                'contain' => array(
                    'Spk',
                ),
            ));
            $options = $this->Spk->getData('paginate', $options, array(
                'status' => 'all',
                'branch' => false,
                'role' => $role,
            ));

            $this->paginate = $options;
            $values = $this->paginate('SpkProduct');

            if( !empty($values) ) {
                foreach ($values as $key => &$val) {
                    // $val = $this->Spk->getMergeList($val, array(
                    //     'contain' => array(
                    //         'Vendor' => array(
                    //             'elements' => array(
                    //                 'status' => 'all',
                    //             ),
                    //         ),
                    //         'Employe',
                    //         'Truck',
                    //         'SpkMechanic' => array(
                    //             'Employe',
                    //         ),
                    //     ),
                    // ));
                    $val = $this->Spk->SpkProduct->getMergeList($val, array(
                        'contain' => array(
                            'Product' => array(
                                'ProductUnit'
                            ),
                        ),
                    ));
                }
            }

            $this->set('sub_module_title', __('History Perbaikan - %s', Common::hashEmptyField($value, 'Truck.nopol')));
            $this->set('active_menu', 'spk');
            $this->set('values', $values);
            $this->set('id', $id);
        } else {
            $this->MkCommon->setCustomFlash(__('Truk tidak ditemukan.'), 'error');
            $this->redirect($this->referer());
        }
    }

    function driver_truck($nopol = null ) {
        $nopol = urldecode($nopol);
        $value = $this->Spk->Truck->getInfoTruck($nopol, null, 'Truck.nopol');

        $this->request->data['Spk']['driver_id'] = Common::hashEmptyField($value, 'Truck.driver_id');
        
        $current_truck = $value;
        $current_truck_id = Common::hashEmptyField($value, 'Truck.id');

        $drivers = $this->Spk->Driver->getData('list', array(
            'fields' => array(
                'Driver.id', 'Driver.driver_name'
            ),
        ), array(
            'branch' => false,
        ));
        $this->set(array(
            'value' => $value,
            'drivers' => $drivers,
            'current_truck_id' => $current_truck_id,
            'current_truck' => $current_truck,
            'ajax_truck_history' => true,
        ));
        $this->render('/Elements/blocks/spk/forms/driver');
    }

    function wheel_position( $id = null, $qty = null ) {
        $this->set(array(
            'id' => $id,
            'qty' => $qty,
        ));
    }

    public function tire_reports() {
        $dateFrom = date('Y-m-d', strtotime('-1 Month'));
        $dateTo = date('Y-m-d');
        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));

        $dataReport = $this->RmReport->_callDataTire_reports($params, 30, 0, true);
        $values = Common::hashEmptyField($dataReport, 'data');

        $this->RjSpk->_callBeforeViewTireReports($params);
        $this->MkCommon->_layout_file(array(
            'select',
            'freeze',
        ));
        $this->set(array(
            'values' => $values,
            'active_menu' => 'tire_reports',
            '_freeze' => true,
        ));
    }

    public function spk_reports() {
        $dateFrom = date('Y-m-d', strtotime('-1 Month'));
        $dateTo = date('Y-m-d');
        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));

        $dataReport = $this->RmReport->_callDataSpk_reports($params, 30, 0, true);
        $values = Common::hashEmptyField($dataReport, 'data');

        $this->RjSpk->_callBeforeViewSpkReports($params);
        $this->MkCommon->_layout_file(array(
            'select',
            'freeze',
        ));
        $this->set(array(
            'values' => $values,
            'active_menu' => 'spk_reports',
            '_freeze' => true,
        ));
    }

    public function maintenance_cost_report() {
        $params = $this->MkCommon->_callRefineParams($this->params);

        $dataReport = $this->RmReport->_callDataMaintenance_cost_report($params, 30, 0, true);
        $values = Common::hashEmptyField($dataReport, 'data');

        $this->RjSpk->_callBeforeViewMaintenanceCostReports($params);
        $this->MkCommon->_layout_file(array(
            'select',
            'freeze',
        ));
        $this->set(array(
            'values' => $values,
            'active_menu' => 'maintenance_cost_report',
            '_freeze' => true,
        ));
    }

    public function payments() {
        $this->loadModel('SpkPayment');
        $this->set('sub_module_title', __('Pembayaran SPK'));
        
        $dateFrom = date('Y-m-d', strtotime('-1 Month'));
        $dateTo = date('Y-m-d');

        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));
        $options =  $this->SpkPayment->_callRefineParams($params);
        $this->paginate = $this->SpkPayment->getData('paginate', $options, array(
            'status' => 'void-active',
        ));
        $values = $this->paginate('SpkPayment');
        $values = $this->Spk->Vendor->getMerge($values, false, 'SpkPayment');

        $vendors = $this->Spk->Vendor->getData('list');

        $this->MkCommon->_layout_file('select');
        $this->set('active_menu', 'spk_payment');
        $this->set(compact(
            'values', 'vendors'
        ));
    }

    function payment_add(){
        $this->set('sub_module_title', __('Pembayaran SPK'));

        $data = $this->request->data;
        $dataSave = $this->RjSpk->_callBeforeSavePayment($data);
        $result = $this->Spk->SpkPaymentDetail->SpkPayment->doSave($dataSave);
        $this->MkCommon->setProcessParams($result, array(
            'controller' => 'spk',
            'action' => 'payments',
            'admin' => false,
        ));
        $this->request->data = $this->RjSpk->_callBeforeRenderPayment($this->request->data);

        $this->set('active_menu', 'spk_payment');
    }

    public function payment_edit( $id = false ) {
        $this->set('sub_module_title', __('Edit Pembayaran SPK'));

        $value = $this->Spk->SpkPaymentDetail->SpkPayment->getData('first', array(
            'conditions' => array(
                'SpkPayment.id' => $id,
            ),
        ), array(
            'status' => 'unposting',
        ));

        if( !empty($value) ) {
            $value = $this->Spk->SpkPaymentDetail->getMerge($value, $id);
            $spk_id = Set::extract('/SpkPaymentDetail/SpkPaymentDetail/spk_id', $value);

            $data = $this->request->data;
            $dataSave = $this->RjSpk->_callBeforeSavePayment($data, $id);
            $result = $this->Spk->SpkPaymentDetail->SpkPayment->doSave($dataSave, $value, $id);
            $this->MkCommon->setProcessParams($result, array(
                'controller' => 'spk',
                'action' => 'payments',
                'admin' => false,
            ));
            $this->request->data = $this->RjSpk->_callBeforeRenderPayment($this->request->data, $spk_id);

            $this->set('active_menu', 'spk_payment');
            $this->set(compact(
                'value'
            ));
            $this->render('payment_add');
        } else {
            $this->MkCommon->redirectReferer(__('Pembayaran SPK tidak ditemukan.'), 'error');
        }
    }

    public function payment_detail( $id ) {
        $this->set('sub_module_title', __('Detail Pembayaran SPK'));

        $value = $this->Spk->SpkPaymentDetail->SpkPayment->getData('first', array(
            'conditions' => array(
                'SpkPayment.id' => $id,
            ),
        ), array(
            'status' => 'void-active',
        ));

        if( !empty($value) ) {
            $value = $this->Spk->SpkPaymentDetail->SpkPayment->SpkPaymentDetail->getMerge($value, $id);
            $spk_id = Set::extract('/SpkPaymentDetail/SpkPaymentDetail/spk_id', $value);
            $this->request->data = $this->RjSpk->_callBeforeRenderPayment($value, $spk_id);

            $this->set('view', 'detail');
            $this->set('active_menu', 'spk_payment');
            $this->set(compact(
                'value'
            ));
            $this->render('payment_add');
        } else {
            $this->MkCommon->redirectReferer(__('Pembayaran SPK tidak ditemukan.'), 'error');
        }
    }

    public function payment_toggle( $id ) {
        $is_ajax = $this->RequestHandler->isAjax();
        $action_type = 'spk_payments';
        $msg = array(
            'msg' => '',
            'type' => 'error'
        );
        $value = $this->Spk->SpkPaymentDetail->SpkPayment->getData('first', array(
            'conditions' => array(
                'SpkPayment.id' => $id,
            ),
        ));
        $data = $this->request->data;

        if( !empty($value) ) {
            if(!empty($data)){
                $result = $this->Spk->SpkPaymentDetail->SpkPayment->doDelete( $id, $value, $data );
                $msg = array(
                    'msg' => $this->MkCommon->filterEmptyField($result, 'msg'),
                    'type' => $this->MkCommon->filterEmptyField($result, 'status'),
                );
                $this->MkCommon->setProcessParams($result, false, array(
                    'ajaxFlash' => true,
                    'noRedirect' => true,
                ));
            }
        } else {
            $msg = array(
                'msg' => __('Pembayaran leasing tidak ditemukan'),
                'type' => 'error'
            );
        }

        $modelName = 'SpkPayment';
        $canceled_date = $this->MkCommon->filterEmptyField($data, 'LeasingPayment', 'canceled_date');
        $this->set(compact(
            'msg', 'is_ajax', 'action_type',
            'canceled_date', 'modelName', 'value'
        ));
        $this->render('/Elements/blocks/common/form_delete');
    }

    function spk_documents () {
        $payment_id = $this->MkCommon->filterEmptyField($this->params, 'named', 'payment_id');
        $vendor_id = $this->MkCommon->filterEmptyField($this->params, 'named', 'vendor_id');
        
        $params = $this->MkCommon->_callRefineParams($this->params);
        $options =  $this->Spk->_callRefineParams($params, array(
            'conditions' => array(
                'Spk.vendor_id' => $vendor_id,
            ),
            'limit' => 10,
        ));

        $this->paginate = $this->Spk->getData('paginate', $options, array(
            'payment_status' => 'unpaid',
        ));
        $values = $this->paginate('Spk');

        if( !empty($values) ) {
            foreach ($values as $key => $value) {
                $id = $this->MkCommon->filterEmptyField($value, 'Spk', 'id');
                $vendor_id = $this->MkCommon->filterEmptyField($value, 'Spk', 'vendor_id');

                $grandtotal = $this->Spk->SpkProduct->_callGrandtotal($id);

                $paid = $this->Spk->SpkPaymentDetail->_callPaidSpk($id, $payment_id);
                $total_remain = $grandtotal - $paid;
                $value['Spk']['total_paid'] = ($paid <= 0)?0:$paid;
                $value['Spk']['total_remain'] = ($total_remain <= 0)?0:$total_remain;
                $value['Spk']['grandtotal'] = $grandtotal;

                $value = $this->Spk->Vendor->getMerge($value, $vendor_id);
                $values[$key] = $value;
            }
        }

        $this->set('module_title', __('spk'));
        $this->set(compact(
            'values', 'payment_id', 'vendor_id'
        ));
    }
}