<?php
App::uses('AppController', 'Controller');
class LeasingsController extends AppController {
	public $uses = array(
        'Leasing',
    );

    public $components = array(
        'RjLeasing'
    );

    function beforeFilter() {
        parent::beforeFilter();
        $this->set('title_for_layout', __('ERP RJTM | Leasing'));
        $this->set('module_title', __('Leasing'));
    }

    function search( $index = 'index' ){
        $refine = array();

        if(!empty($this->request->data)) {
            $data = $this->request->data;
            $named = $this->MkCommon->filterEmptyField($this->params, 'named');
            
            $refine = $this->RjLeasing->processRefine($data);
            $result = $this->MkCommon->processFilter($data);
            $params = $this->RjLeasing->generateSearchURL($refine);
            $params = array_merge($params, $result);
            $params['action'] = $index;
            
            if( !empty($named) ) {
                foreach ($named as $key => $value) {
                    $params[] = $value;
                }
            }

            $this->redirect($params);
        }

        $this->redirect('/');
    }

	public function index() {
        $options =  $this->Leasing->_callRefineParams($this->params);
        $this->MkCommon->_callRefineParams($this->params);

        $this->paginate = $this->Leasing->getData('paginate', $options, array(
            'status' => 'all',
        ));
        $leasings = $this->paginate('Leasing');

        if( !empty($leasings) ) {
            $this->loadModel('City');

            foreach ($leasings as $key => $value) {
                // Custom Otorisasi
                $branch_id = $this->MkCommon->filterEmptyField($value, 'Leasing', 'branch_id');
                $value = $this->GroupBranch->Branch->getMerge($value, $branch_id);
                $leasings[$key] = $value;
            }
        }
        $vendors = $this->Leasing->Vendor->getData('list');

        $this->set('active_menu', 'view_leasing');
        $this->set('sub_module_title', __('Leasing'));
        $this->set(compact(
            'leasings', 'vendors'
        ));
	}

    function detail($id = false){
        if(!empty($id)){
            $value = $this->Leasing->getData('first', array(
                'conditions' => array(
                    'Leasing.id' => $id
                ),
                'contain' => array(
                    'LeasingDetail'
                ),
            ), array(
                'status' => 'all',
            ));

            if(!empty($value)){
                $sub_module_title = __('Detail Leasing');
                $vendor_id = $this->MkCommon->filterEmptyField($value, 'Leasing', 'vendor_id');
                $paid_date = $this->MkCommon->filterEmptyField($value, 'Leasing', 'paid_date');
                $date_first_installment = $this->MkCommon->filterEmptyField($value, 'Leasing', 'date_first_installment');
                $date_last_installment = $this->MkCommon->filterEmptyField($value, 'Leasing', 'date_last_installment');

                $value = $this->Leasing->Vendor->getMerge($value, $vendor_id);

                if(!empty($paid_date)){
                    $value['Leasing']['paid_date'] = $this->MkCommon->getDate($paid_date, true);
                }
                if(!empty($date_first_installment)){
                    $value['Leasing']['date_first_installment'] = $this->MkCommon->getDate($date_first_installment, true);
                }
                if(!empty($date_last_installment)){
                    $value['Leasing']['date_last_installment'] = $this->MkCommon->getDate($date_last_installment, true);
                }

                if( !empty($value['LeasingDetail']) ) {
                    foreach ($value['LeasingDetail'] as $key => $detail) {
                        $truck_id = $this->MkCommon->filterEmptyField($detail, 'truck_id');

                        $detail = $this->Leasing->LeasingDetail->Truck->getMerge($detail, $truck_id);
                        $value['LeasingDetail'][$key] = $detail;
                    }
                }

                $this->request->data = $value;
                $this->set(compact(
                    'value', 'sub_module_title'
                ));
                $this->render('leasing_form');
            }else{
                $this->MkCommon->setCustomFlash(__('Leasing tidak ditemukan.'), 'error');
                $this->redirect($this->referer());
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Leasing tidak ditemukan.'), 'error');
            $this->redirect($this->referer());
        }
    }

    function add(){
        $this->set('sub_module_title', __('Tambah Leasing'));
        $this->doLeasing();
    }

    function edit($id){
        $this->set('sub_module_title', 'Rubah Leasing');
        $value = $this->Leasing->getData('first', array(
            'conditions' => array(
                'Leasing.id' => $id
            ),
            'contain' => array(
                'LeasingDetail'
            ),
        ), array(
            'status' => 'all',
        ));

        if(!empty($value)){
            // Custom Otorisasi
            // $branch_id = $this->MkCommon->filterEmptyField($value, 'Leasing', 'branch_id');
            // $this->MkCommon->allowPage($branch_id);

            $this->doLeasing($id, $value);
        }else{
            $this->MkCommon->setCustomFlash(__('Leasing tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'trucks',
                'action' => 'index'
            ));
        }
    }

    function doLeasing($id = false, $data_local = false){
        $leasing_status = $this->MkCommon->filterEmptyField($data_local, 'Leasing', 'payment_status', 'unpaid');
        $leasingDetails = $this->Leasing->LeasingDetail->getData('list', array(
            'fields' => array(
                'LeasingDetail.truck_id', 'LeasingDetail.truck_id',
            ),
            'group' => array(
                'LeasingDetail.truck_id'
            ),
        ));
        $trucks = $this->Leasing->LeasingDetail->Truck->getData('list', array(
            'conditions' => array(
                'Truck.id NOT' => $leasingDetails,
            ),
            'fields' => array(
                'Truck.id', 'Truck.nopol'
            ),
        ));

        if( !empty($this->request->data) && $leasing_status == 'unpaid' ){
            $data = $this->request->data;

            if($id && $data_local){
                $this->Leasing->id = $id;
                $msg = 'merubah';
            }else{
                $this->Leasing->create();
                $msg = 'menambah';
            }

            $data['Leasing']['paid_date'] = (!empty($data['Leasing']['paid_date'])) ? $this->MkCommon->getDate($data['Leasing']['paid_date']) : '';
            $data['Leasing']['date_first_installment'] = (!empty($data['Leasing']['date_first_installment'])) ? $this->MkCommon->getDate($data['Leasing']['date_first_installment']) : '';
            $data['Leasing']['date_last_installment'] = !empty($data['Leasing']['tgl_last_installment'])?$this->MkCommon->getDateSelectbox($data['Leasing']['tgl_last_installment']):false;

            $data['Leasing']['down_payment'] = $this->MkCommon->convertPriceToString($this->MkCommon->filterEmptyField($data, 'Leasing', 'down_payment'), '');
            $data['Leasing']['installment'] = $this->MkCommon->convertPriceToString($this->MkCommon->filterEmptyField($data, 'Leasing', 'installment'), '');
            $data['Leasing']['installment_rate'] = $this->MkCommon->convertPriceToString($this->MkCommon->filterEmptyField($data, 'Leasing', 'installment_rate'), '');
            $data['Leasing']['denda'] = $this->MkCommon->convertPriceToString($this->MkCommon->filterEmptyField($data, 'Leasing', 'denda'), 0);
            $data['Leasing']['total_leasing'] = $this->MkCommon->convertPriceToString($this->MkCommon->filterEmptyField($data, 'Leasing', 'total_leasing'), 0);

            $data['Leasing']['total_biaya'] = $data['Leasing']['installment'] + $data['Leasing']['denda'];
            $data['Leasing']['branch_id'] = Configure::read('__Site.config_branch_id');

            $validate_leasing_detail = true;
            $temp_detail = array();
            $total_price = 0;
            $truck_collect = array();
            $truck_same = true;

            if(!empty($data['LeasingDetail']['truck_id'])){
                foreach ($data['LeasingDetail']['truck_id'] as $key => $value) {
                    if( !empty($value) && !in_array($value, $truck_collect)){
                        $truck_collect[] = $value;
                        $data_detail['LeasingDetail'] = array(
                            'truck_id' => $value,
                            'price' => (!empty($data['LeasingDetail']['price'][$key])) ? $this->MkCommon->convertPriceToString($data['LeasingDetail']['price'][$key], 0) : 0,
                        );
                        
                        $temp_detail[] = $data_detail;
                        $this->Leasing->LeasingDetail->set($data_detail);

                        if( !$this->Leasing->LeasingDetail->validates() ){
                            $validate_leasing_detail = false;
                            break;
                        }else{
                            $total_price += $data_detail['LeasingDetail']['price'];
                        }
                    }else{
                        if(in_array($value, $truck_collect)){
                            $truck_same = false;
                        }
                    }
                }
            }else{
                $validate_leasing_detail = false;
            }

            $this->Leasing->set($data);

            if($this->Leasing->validates($data) && $validate_leasing_detail && $truck_same){
                if($this->Leasing->save($data)){
                    $leasing_id = $this->Leasing->id;
                    $this->Leasing->LeasingInstallment->doSave($leasing_id, $data);

                    if($id && $data_local){
                        $this->Leasing->LeasingDetail->deleteAll(array(
                            'LeasingDetail.leasing_id' => $leasing_id
                        ));
                    }

                    if(!empty($temp_detail)){
                        foreach ($temp_detail as $key => $value) {
                            $temp_detail[$key]['LeasingDetail']['leasing_id'] = $leasing_id;
                        }

                        $this->Leasing->LeasingDetail->saveMany($temp_detail);
                    }

                    $this->params['old_data'] = $data_local;
                    $this->params['data'] = $data;

                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s leasing'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s leasing #%s'), $msg, $leasing_id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $leasing_id );
                    $this->redirect(array(
                        'controller' => 'leasings',
                        'action' => 'index'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s leasing'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s leasing #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id ); 
                }
            }else{
                $text = sprintf(__('Gagal %s leasing'), $msg);
                if(!$validate_leasing_detail){
                    $text .= '<br>* harap isi semua field yang terdapat di leasing detail.';
                }
                if(!$truck_same){
                    $text .= '<br>* Tidak boleh terdapat truk yang sama dalam 1 kontrak leasing.';
                }
                $this->MkCommon->setCustomFlash($text, 'error');
            }
        }else{
            if($id && $data_local){
                $this->request->data = $data_local;
                $this->request->data = $this->MkCommon->mergeDate($this->request->data, 'Leasing', 'date_last_installment', 'tgl_last_installment');

                if(!empty($data_local['LeasingDetail'])){
                    foreach ($data_local['LeasingDetail'] as $key => $value) {
                        $truck = $this->Leasing->LeasingDetail->Truck->getData('first', array(
                            'conditions' => array(
                                'Truck.id' => $value['truck_id']
                            )
                        ));

                        if(!empty($truck)){
                            $trucks[$value['truck_id']] = $truck['Truck']['nopol'];
                        }
                    }
                }

                if(!empty($this->request->data['Leasing']['paid_date'])){
                    $this->request->data['Leasing']['paid_date'] = $this->MkCommon->getDate($this->request->data['Leasing']['paid_date'], true);
                }
                if(!empty($this->request->data['Leasing']['date_first_installment'])){
                    $this->request->data['Leasing']['date_first_installment'] = $this->MkCommon->getDate($this->request->data['Leasing']['date_first_installment'], true);
                }
                if(!empty($this->request->data['Leasing']['date_last_installment'])){
                    $this->request->data['Leasing']['date_last_installment'] = $this->MkCommon->getDate($this->request->data['Leasing']['date_last_installment'], true);
                }
            }
        
        }

        if(!empty($this->request->data['LeasingDetail']['truck_id'])){
            $temp_arr = array();
            foreach ($this->request->data['LeasingDetail']['truck_id'] as $key => $value) {
                $temp_arr[$key] = array(
                    'truck_id' => $value,
                    'price' => $this->request->data['LeasingDetail']['price'][$key]
                );
            }
            unset($this->request->data['LeasingDetail']);
            $this->request->data['LeasingDetail'] = $temp_arr;
        }
        
        $leasing_companies = $this->Leasing->Vendor->getData('list', array(
            'fields' => array(
                'Vendor.id', 'Vendor.name'
            )
        ));

        $this->set('active_menu', 'view_leasing');
        $this->set(compact(
            'leasing_companies', 'trucks', 'data_local'
        ));
        $this->render('leasing_form');
    }

    function toggle($id){
        $locale = $this->Leasing->getData('first', array(
            'conditions' => array(
                'Leasing.id' => $id,
            )
        ), array(
            'status' => 'all',
        ));

        if( !empty($locale) ){
            $value = true;

            if( !empty($locale['Leasing']['status']) ){
                $value = false;
            }

            $this->Leasing->id = $id;
            $this->Leasing->set('status', $value);

            if($this->Leasing->save()){
                $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
                $this->Log->logActivity( sprintf(__('Sukses merubah status Leasing ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id ); 
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal merubah status Leasing ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id ); 
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Leasing tidak ditemukan.'), 'error');
        }
        
        $this->redirect($this->referer());
    }

    function payments() {
        $this->set('active_menu', 'leasing_payments');
        $this->set('sub_module_title', __('Data Pembayaran Leasing'));

        $dateFrom = date('Y-m-01');
        $dateTo = date('Y-m-t');
        
        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));
        $options =  $this->Leasing->LeasingPayment->_callRefineParams($params);
        $this->paginate = $this->Leasing->LeasingPayment->getData('paginate', $options, array(
            'status' => 'all',
        ));
        $payments = $this->paginate('LeasingPayment');

        if( !empty($payments) ) {
            foreach ($payments as $key => $value) {
                $vendor_id = $this->MkCommon->filterEmptyField($value, 'LeasingPayment', 'vendor_id');

                $value = $this->Leasing->LeasingPayment->Vendor->getMerge($value, $vendor_id);
                $payments[$key] = $value;
            }
        }
        $vendors = $this->Leasing->Vendor->getData('list');

        if( !empty($dateFrom) && !empty($dateTo) ) {
            $this->request->data['LeasingPayment']['date'] = $this->MkCommon->_callDateView($dateFrom, $dateTo);
        }

        $this->set(compact(
            'payments', 'vendors'
        ));
    }

    function _calDataIndexConvertion ( $data, $reverse = false ) {
        return $this->MkCommon->dataConverter($data, array(
            'date' => array(
                'LeasingPayment' => array(
                    'payment_date',
                ),
            )
        ), $reverse);
    }

    function _callDataSupport () {
        $coas = $this->GroupBranch->Branch->BranchCoa->getCoas();
        $vendors = $this->Leasing->getData('list', array(
            'fields' => array(
                'Leasing.vendor_id', 'Vendor.name',
            ),
            'contain' => array(
                'Vendor',
            ),
            'group' => array(
                'Leasing.vendor_id',
            ),
        ), array(
            'status' => 'unpaid',
        ));

        $this->MkCommon->_layout_file('select');
        $this->set(compact(
            'coas', 'vendors'
        ));
    }

    function payment_add(){
        $this->set('sub_module_title', __('Tambah Pembayaran Leasing'));
        $this->set('active_menu', 'leasing_payments');

        $data = $this->request->data;
        $data = $this->_calDataIndexConvertion( $data );
        $result = $this->Leasing->LeasingPayment->doSave( $data );
        $this->MkCommon->setProcessParams($result, array(
            'controller' => 'leasings',
            'action' => 'payments',
            'admin' => false,
        ));
        $this->request->data = $this->_calDataIndexConvertion($this->request->data, true);

        $this->_callDataSupport();
        $this->render('payment_form');
    }

    function payment_edit( $id = false ){
        $this->set('sub_module_title', __('Edit Pembayaran Leasing'));
        $this->set('active_menu', 'leasing_payments');

        $value = $this->Leasing->LeasingPayment->getData('first', array(
            'conditions' => array(
                'LeasingPayment.id' => $id,
            ),
        ));

        if( !empty($value) ) {
            $value = $this->Leasing->LeasingPayment->LeasingPaymentDetail->getMerge($value, $id);

            if( !empty($value['LeasingPaymentDetail']) ) {
                foreach ($value['LeasingPaymentDetail'] as $key => $detail) {
                    $leasing_id = $this->MkCommon->filterEmptyField($detail, 'LeasingPaymentDetail', 'leasing_id');
                    $leasing_installment_id = $this->MkCommon->filterEmptyField($detail, 'LeasingPaymentDetail', 'leasing_installment_id');

                    $detail = $this->Leasing->getMerge($detail, $leasing_id);
                    $detail = $this->Leasing->LeasingInstallment->getMerge($detail, $leasing_installment_id);
                    $value['LeasingPaymentDetail'][$key] = $detail;
                }
            }

            $data = $this->request->data;
            $data = $this->_calDataIndexConvertion( $data );
            $result = $this->Leasing->LeasingPayment->doSave( $data, $value, $id );
            $this->MkCommon->setProcessParams($result, array(
                'controller' => 'leasings',
                'action' => 'payments',
                'admin' => false,
            ));
            $this->request->data = $this->_calDataIndexConvertion($this->request->data, true);

            $this->_callDataSupport();
            $this->render('payment_form');
        } else {
            $this->MkCommon->redirectReferer(__('Pembayaran leasing tidak ditemukan'), 'error');
        }
    }

    function detail_payment( $id = false ){
        $this->set('sub_module_title', __('Info Pembayaran Leasing'));
        $this->set('active_menu', 'leasing_payments');

        $value = $this->Leasing->LeasingPayment->getData('first', array(
            'conditions' => array(
                'LeasingPayment.id' => $id,
            ),
        ));

        if( !empty($value) ) {
            $vendor_id = $this->MkCommon->filterEmptyField($value, 'LeasingPayment', 'vendor_id');
            $coa_id = $this->MkCommon->filterEmptyField($value, 'LeasingPayment', 'coa_id');

            $value = $this->Leasing->LeasingPayment->Coa->getMerge($value, $coa_id);
            $value = $this->Leasing->LeasingPayment->Vendor->getMerge($value, $vendor_id);
            $value = $this->Leasing->LeasingPayment->LeasingPaymentDetail->getMerge($value, $id);
            $value = $this->_calDataIndexConvertion($value, true);

            if( !empty($value['LeasingPaymentDetail']) ) {
                foreach ($value['LeasingPaymentDetail'] as $key => $detail) {
                    $leasing_id = $this->MkCommon->filterEmptyField($detail, 'LeasingPaymentDetail', 'leasing_id');

                    $detail = $this->Leasing->getMerge($detail, $leasing_id);
                    $value['LeasingPaymentDetail'][$key] = $detail;
                }
            }

            $this->request->data = $value;

            $this->set(compact(
                'id'
            ));
            $this->render('payment_form');
        } else {
            $this->MkCommon->redirectReferer(__('Pembayaran leasing tidak ditemukan'), 'error');
        }
    }

    function payment_delete($id){
        $is_ajax = $this->RequestHandler->isAjax();
        $action_type = 'leasing_payments';
        $msg = array(
            'msg' => '',
            'type' => 'error'
        );
        $value = $this->Leasing->LeasingPayment->getData('first', array(
            'conditions' => array(
                'LeasingPayment.id' => $id
            ),
        ));

        if( !empty($value) ){
            $value = $this->Leasing->LeasingPayment->LeasingPaymentDetail->getMerge($value, $id);
            $no_doc = $this->MkCommon->filterEmptyField($value, 'LeasingPayment', 'no_doc');
            $coa_id = $this->MkCommon->filterEmptyField($value, 'LeasingPayment', 'coa_id');
            $payment_date = $this->MkCommon->filterEmptyField($value, 'LeasingPayment', 'payment_date');
            $vendor_id = $this->MkCommon->filterEmptyField($value, 'LeasingPayment', 'vendor_id');
            
            $value = $this->Leasing->Vendor->getMerge($value, $vendor_id);
            $vendor_name = $this->MkCommon->filterEmptyField($value, 'Vendor', 'name');

            $title = sprintf(__('Pembayaran Leasing #%s kepada vendor %s'), $no_doc, $vendor_name);
            $title = sprintf(__('<i>Pembatalan</i> %s'), $this->MkCommon->filterEmptyField($value, 'LeasingPayment', 'note', $title));

            if(!empty($this->request->data)){
                $data = $this->request->data;
                $rejected_date = $this->MkCommon->filterEmptyField($data, 'LeasingPayment', 'canceled_date');

                if(!empty($rejected_date)){
                    $data['LeasingPayment']['rejected_date'] = $this->MkCommon->getDate($rejected_date);
                    $data['LeasingPayment']['rejected'] = 1;

                    $this->Leasing->LeasingPayment->id = $id;
                    $this->Leasing->LeasingPayment->set($data);

                    if($this->Leasing->LeasingPayment->save()){
                        if( !empty($value['LeasingPaymentDetail']) ) {
                            $installment = 0;
                            $installment_rate = 0;
                            $denda = 0;

                            foreach ($value['LeasingPaymentDetail'] as $key => $detail) {
                                $leasing_payment_id = $this->MkCommon->filterEmptyField($detail, 'LeasingPaymentDetail', 'leasing_installment_id');
                                $leasing_id = $this->MkCommon->filterEmptyField($detail, 'LeasingPaymentDetail', 'leasing_id');

                                $installment += $this->MkCommon->filterEmptyField($detail, 'LeasingPaymentDetail', 'installment', 0);
                                $installment_rate += $this->MkCommon->filterEmptyField($detail, 'LeasingPaymentDetail', 'installment_rate', 0);
                                $denda += $this->MkCommon->filterEmptyField($detail, 'LeasingPaymentDetail', 'denda', 0);

                                $totalInstallmentPaid = $this->Leasing->LeasingPayment->LeasingPaymentDetail->getData('count', array(
                                    'conditions' => array(
                                        'LeasingPaymentDetail.leasing_installment_id <>' => $leasing_payment_id,
                                    ),
                                ));
                                $totalLeasingPaid = $this->Leasing->LeasingPayment->LeasingPaymentDetail->getData('count', array(
                                    'conditions' => array(
                                        'LeasingPaymentDetail.leasing_id <>' => $leasing_id,
                                    ),
                                ));

                                if( !empty($totalLeasingPaid) ) {
                                    $statusLeasingPayment = 'half_paid';
                                } else {
                                    $statusLeasingPayment = 'unpaid';
                                }

                                if( !empty($totalInstallmentPaid) ) {
                                    $statusInstallmentPayment = 'half_paid';
                                } else {
                                    $statusInstallmentPayment = 'unpaid';
                                }

                                $this->Leasing->id = $leasing_id;
                                $this->Leasing->set('payment_status', $statusLeasingPayment);
                                $this->Leasing->save();

                                $this->Leasing->LeasingInstallment->id = $leasing_payment_id;
                                $this->Leasing->LeasingInstallment->set('payment_status', $statusInstallmentPayment);
                                $this->Leasing->LeasingInstallment->save();
                            }

                            if( !empty($installment) ) {
                                $this->User->Journal->setJournal($installment, array(
                                    'credit' => 'leasing_installment_coa_id',
                                    'debit' => $coa_id,
                                ), array(
                                    'date' => $payment_date,
                                    'document_id' => $id,
                                    'title' => $title,
                                    'document_no' => $no_doc,
                                    'type' => 'leasing_payment_void',
                                ));
                            }
                            if( !empty($installment_rate) ) {
                                $this->User->Journal->setJournal($installment_rate, array(
                                    'credit' => 'leasing_installment_rate_coa_id',
                                    'debit' => $coa_id,
                                ), array(
                                    'date' => $payment_date,
                                    'document_id' => $id,
                                    'title' => $title,
                                    'document_no' => $no_doc,
                                    'type' => 'leasing_payment_void',
                                ));
                            }
                            if( !empty($denda) ) {
                                $this->User->Journal->setJournal($denda, array(
                                    'credit' => 'leasing_denda_coa_id',
                                    'debit' => $coa_id,
                                ), array(
                                    'date' => $payment_date,
                                    'document_id' => $id,
                                    'title' => $title,
                                    'document_no' => $no_doc,
                                    'type' => 'leasing_payment_void',
                                ));
                            }
                        }

                        $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
                        $msg = array(
                            'msg' => sprintf(__('Berhasil membatalkan pembayaran leasing #%s'), $noref),
                            'type' => 'success'
                        );
                        $this->MkCommon->setCustomFlash( $msg['msg'], $msg['type']);  
                        $this->Log->logActivity( sprintf(__('Berhasil membatalkan pembayaran leasing #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id ); 
                    }else{
                        $this->Log->logActivity( sprintf(__('Gagal membatalkan pembayaran leasing #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id ); 
                    }
                }else{
                    $msg = array(
                        'msg' => __('Harap masukkan tanggal pembatalan pembayaran leasing.'),
                        'type' => 'error'
                    );
                }
            }

            $this->set('value', $value);
        }else{
            $msg = array(
                'msg' => __('Pembayaran leasing tidak ditemukan'),
                'type' => 'error'
            );
        }

        $modelName = 'LeasingPayment';
        $canceled_date = !empty($this->request->data['LeasingPayment']['canceled_date']) ? $this->request->data['LeasingPayment']['canceled_date'] : false;
        $this->set(compact(
            'msg', 'is_ajax', 'action_type',
            'canceled_date', 'modelName'
        ));
        $this->render('/Elements/blocks/common/form_delete');
    }

    function leasings_unpaid($vendor_id = false){
        $dateFrom = date('Y-m-d', strtotime('-1 Month'));
        $dateTo = date('Y-m-d');

        $options = array(
            'conditions' => array(
                'Leasing.vendor_id' => $vendor_id,
                'Leasing.payment_status' => array( 'unpaid', 'half_paid' ),
            ),
            'contain' => false,
            'limit' => Configure::read('__Site.config_pagination'),
        );

        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));
        $options =  $this->Leasing->_callRefineParams($params, $options);

        $this->paginate = $this->Leasing->getData('paginate', $options);
        $values = $this->paginate('Leasing');

        if( !empty($values) ) {
            foreach ($values as $key => $value) {
                $leasing_id = $this->MkCommon->filterEmptyField($value, 'Leasing', 'id');

                $value = $this->Leasing->LeasingInstallment->_callLastPayment($value, $leasing_id);
                $values[$key] = $value;
            }
        }
        
        $data_change = $data_action = 'browse-invoice';
        $title = __('Pembayaran Leasing');
        $this->set(compact(
            'data_change', 'title', 'values',
            'vendor_id', 'data_action'
        ));
    }

    public function leasing_report( $data_action = false ) {
        $module_title = __('Laporan Leasing');

        $this->set('sub_module_title', $module_title);
        $options =  $this->Leasing->getData('paginate', false, array(
            'branch' => false,
        ));

        $params = $this->MkCommon->_callRefineParams($this->params);
        $options =  $this->Leasing->_callRefineParams($params, $options);

        if( !empty($data_action) ){
            $values = $this->Leasing->find('all', $options);
        } else {
            $options['limit'] = Configure::read('__Site.config_pagination');
            $this->paginate = $options;
            $values = $this->paginate('Leasing');
        }

        if(!empty($values)){
            foreach ($values as $key => $value) {
                $id = $this->MkCommon->filterEmptyField($value, 'Leasing', 'id');

                $value = $this->Leasing->LeasingPayment->getPayment($value, $id);
                $value = $this->Leasing->LeasingInstallment->getCountInstallment($value, $id);

                $values[$key] = $value;
            }
        }

        $vendors = $this->Leasing->Vendor->getData('list');

        $this->set('active_menu', 'leasing_report');
        $this->set(compact(
            'values', 'module_title', 'data_action',
            'vendors'
        ));

        if($data_action == 'pdf'){
            $this->layout = 'pdf';
        }else if($data_action == 'excel'){
            $this->layout = 'ajax';
        } else {
            $this->MkCommon->_layout_file(array(
                'select',
                'freeze',
            ));
        }
    }
}