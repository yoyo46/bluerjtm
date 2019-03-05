<?php
App::uses('AppController', 'Controller');
class InsurancesController extends AppController {
	public $uses = array(
        'Insurance',
    );

    public $components = array(
        'RjInsurance'
    );

    function beforeFilter() {
        parent::beforeFilter();
        $this->set('title_for_layout', __('ERP RJTM | Asuransi'));
        $this->set('module_title', __('Asuransi'));
    }

    function search( $index = 'index', $prefix = false ){
        $refine = array();

        if(!empty($this->request->data)) {
            $data = $this->request->data;
            $named = $this->MkCommon->filterEmptyField($this->params, 'named');
            
            $refine = $this->RjInsurance->processRefine($data);
            $result = $this->MkCommon->processFilter($data);
            $params = $this->RjInsurance->generateSearchURL($refine);
            $params = array_merge($params, $result);
            $params['action'] = $index;
            
            if( !empty($named) ) {
                foreach ($named as $key => $value) {
                    $params[] = $value;
                }
            }

            if( !empty($prefix) ) {
                $params[$prefix] = true;
            }

            $this->redirect($params);
        }

        $this->redirect('/');
    }

	public function index() {
        $options =  $this->Insurance->_callRefineParams($this->params);
        $this->MkCommon->_callRefineParams($this->params);

        $this->paginate = $this->Insurance->getData('paginate', $options);
        $values = $this->paginate('Insurance');
        $values = $this->Insurance->getMergeList($values, array(
            'contain' => array(
                'Branch',
            ),
        ));

        $this->set('active_menu', 'insurance');
        $this->set('sub_module_title', __('Asuransi'));
        $this->set(compact(
            'values'
        ));
	}

    function add(){
        $this->set('sub_module_title', __('Tambah Asuransi'));
        $this->doInsurance();
    }

    function edit($id){
        $this->set('sub_module_title', 'Edit Asuransi');
        $value = $this->Insurance->getData('first', array(
            'conditions' => array(
                'Insurance.id' => $id
            ),
        ), array(
            'status' => 'all',
        ));

        if(!empty($value)){
            $value = $this->Insurance->getMergeList($value, array(
                'contain' => array(
                    'InsuranceDetail',
                ),
            ));

            $this->MkCommon->getLogs($this->params['controller'], array( 'edit', 'add', 'toggle' ), $id);
            $this->doInsurance($id, $value);
        }else{
            $this->MkCommon->setCustomFlash(__('Asuransi tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'insurances',
                'action' => 'index'
            ));
        }
    }

    function doInsurance($id = false, $value = false){
        $data = $this->request->data;

        if( !empty($data) ){            
            $data = $this->MkCommon->dataConverter($data, array(
                'price' => array(
                    'Insurance' => array(
                        'disc',
                        'admin_fee',
                    ),
                )
            ));

            $disc = Common::hashEmptyField($data, 'Insurance.disc', 0);
            $admin_fee = Common::hashEmptyField($data, 'Insurance.admin_fee', 0);
            $details = Common::hashEmptyField($data, 'InsuranceDetail.condition');
            $date = Common::hashEmptyField($data, 'Insurance.date');
            $date_format = $this->MkCommon->_callDateRangeFormat(array(), $date);
            
            $data = Hash::insert($data, 'Insurance.id', $id);
            $data = Hash::insert($data, 'Insurance.start_date', Common::hashEmptyField($date_format, 'named.DateFrom'));
            $data = Hash::insert($data, 'Insurance.end_date', Common::hashEmptyField($date_format, 'named.DateTo'));
            $data = Hash::insert($data, 'Insurance.item', $details);

            if(!empty($details)){
                $grandtotal = 0;
                $dataDetail = array();

                foreach ($details as $truck_id => $detail) {
                    if( !empty($detail) ) {
                        $truck = $this->Insurance->InsuranceDetail->Truck->getMerge(array(), $truck_id);

                        foreach ($detail as $key => $condition) {
                            $price = Common::hashEmptyField($data, __('InsuranceDetail.price.%s.%s', $truck_id, $key), 0, array(
                                'type' => 'unprice',
                            ));
                            $rate = Common::hashEmptyField($data, __('InsuranceDetail.rate.%s.%s', $truck_id, $key));
                            $note = Common::hashEmptyField($data, __('InsuranceDetail.note.%s.%s', $truck_id, $key));
                            $premi = $price * ($rate/100);
                            
                            $dataDetail[] = array(
                                'InsuranceDetail' => array(
                                    'truck_id' => $truck_id,
                                    'nopol' => Common::hashEmptyField($truck, 'Truck.nopol'),
                                    'condition' => $condition,
                                    'price' => $price,
                                    'rate' => $rate,
                                    'premi' => $premi,
                                    'note' => $note,
                                ),
                            );

                            $grandtotal += $premi;
                        }
                    }
                }

                $grandtotal = $grandtotal - $disc + $admin_fee;
                $data = Hash::insert($data, 'Insurance.grandtotal', $grandtotal);
                $data = Hash::insert($data, 'InsuranceDetail', $dataDetail);
            }

            $flag = $this->Insurance->saveAll($data, array(
                'validate' => 'only',
                'deep' => true,
            ));

            if( !empty($flag) ){
                $this->Insurance->InsuranceDetail->updateAll(array(
                    'InsuranceDetail.status' => 0,
                ), array(
                    'InsuranceDetail.insurance_id' => $id,
                ));

                $flag = $this->Insurance->saveAll($data, array(
                    'deep' => true,
                ));
                $id = $this->Insurance->id;

                $this->params['old_data'] = $value;
                $this->params['data'] = $data;

                $this->MkCommon->setCustomFlash(__('Berhasil menyimpan Asuransi'), 'success');
                $this->Log->logActivity( sprintf(__('Sukses menyimpan insurance #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
                $this->redirect(array(
                    'controller' => 'insurances',
                    'action' => 'index'
                ));
            }else{
                $text = __('Gagal menyimpan Asuransi');
                $this->MkCommon->setCustomFlash($text, 'error');
            }
        } elseif( !empty($value) ){
            $start_date = Common::hashEmptyField($value, 'Insurance.start_date', null, array(
                'date' => 'd/m/Y',
            ));
            $end_date = Common::hashEmptyField($value, 'Insurance.end_date', null, array(
                'date' => 'd/m/Y',
            ));
            $date = __('%s - %s', $start_date, $end_date);
            $value = Hash::insert($value, 'Insurance.date', $date);

            $data = $value;
        }

        $details = Common::hashEmptyField($data, 'InsuranceDetail');

        if( !empty($details) ) {
            $rowspan = array();

            foreach ($details as &$detail) {
                $truck_id = Common::hashEmptyField($detail, 'InsuranceDetail.truck_id');
                $detail = $this->Insurance->InsuranceDetail->getMergeList($detail, array(
                    'contain' => array(
                        'Truck' => array(
                            'elements' => array(
                                'branch' => false,
                            ),
                        ),
                    ),
                ));
                $detail = $this->Insurance->InsuranceDetail->Truck->getMergeList($detail, array(
                    'contain' => array(
                        'Company',
                        'TruckBrand',
                        'TruckCategory',
                    ),
                ));

                if( !empty($rowspan[$truck_id]) ) {
                    $rowspan[$truck_id]++;
                } else {
                    $rowspan[$truck_id] = 1;
                }
            }
            
            $data = Hash::insert($data, 'InsuranceDetail', $details);
            $this->request->data = $data;

            if( !empty($value) ) {
                $value = Hash::insert($value, 'InsuranceDetail', $details);
            }
        }

        $this->set('active_menu', 'insurance');
        $this->set(compact(
            'value', 'rowspan'
        ));
        $this->render('add');
    }

    function detail($id = false){
        $value = $this->Insurance->getData('first', array(
            'conditions' => array(
                'Insurance.id' => $id
            ),
        ), array(
            'status' => 'all',
        ));

        if(!empty($value)){
            $sub_module_title = __('Detail Asuransi');
            $value = $this->Insurance->getMergeList($value, array(
                'contain' => array(
                    'InsuranceDetail',
                ),
            ));

            $details = Common::hashEmptyField($value, 'InsuranceDetail');
            $rowspan = array();

            if(!empty($details)){
                foreach ($details as &$detail) {
                    $truck_id = Common::hashEmptyField($detail, 'InsuranceDetail.truck_id');
                    $detail = $this->Insurance->InsuranceDetail->getMergeList($detail, array(
                        'contain' => array(
                            'Truck' => array(
                                'elements' => array(
                                    'branch' => false,
                                ),
                            ),
                        ),
                    ));
                    $detail = $this->Insurance->InsuranceDetail->Truck->getMergeList($detail, array(
                        'contain' => array(
                            'Company',
                            'TruckBrand',
                            'TruckCategory',
                        ),
                    ));

                    if( !empty($rowspan[$truck_id]) ) {
                        $rowspan[$truck_id]++;
                    } else {
                        $rowspan[$truck_id] = 1;
                    }
                }
            }
            
            $start_date = Common::hashEmptyField($value, 'Insurance.start_date', null, array(
                'date' => 'd/m/Y',
            ));
            $end_date = Common::hashEmptyField($value, 'Insurance.end_date', null, array(
                'date' => 'd/m/Y',
            ));
            $date = __('%s - %s', $start_date, $end_date);

            $value = Hash::insert($value, 'Insurance.date', $date);
            $value = Hash::insert($value, 'InsuranceDetail', $details);
            
            $this->request->data = $value;
            $this->MkCommon->getLogs($this->params['controller'], array( 'edit', 'add', 'toggle' ), $id);

            $this->set('active_menu', 'insurance');
            $this->set('view', true);
            $this->set(compact(
                'value', 'sub_module_title', 'rowspan'
            ));
            $this->render('add');
        }else{
            $this->MkCommon->setCustomFlash(__('Asuransi tidak ditemukan.'), 'error');
            $this->redirect($this->referer());
        }
    }

    function toggle($id){
        $locale = $this->Insurance->getData('first', array(
            'conditions' => array(
                'Insurance.id' => $id,
            )
        ), array(
            'status' => 'active',
        ));

        if( !empty($locale) ){
            $value = true;

            if( !empty($locale['Insurance']['status']) ){
                $value = false;
            }

            $this->Insurance->id = $id;
            $this->Insurance->set('status', $value);

            if($this->Insurance->save()){
                $this->MkCommon->setCustomFlash(__('Berhasil membatalkan asuransi'), 'success');
                $this->Log->logActivity( sprintf(__('Sukses merubah status Insurance ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id ); 
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal membatalkan asuransi'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal merubah status Insurance ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id ); 
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Asuransi tidak ditemukan'), 'error');
        }
        
        $this->redirect($this->referer());
    }

    function payments() {
        $this->set('active_menu', 'insurance_payments');
        $this->set('sub_module_title', __('Pembayaran Asuransi'));

        $dateFrom = date('Y-m-01');
        $dateTo = date('Y-m-t');
        
        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));
        $options =  $this->Insurance->InsurancePayment->_callRefineParams($params);
        $this->paginate = $this->Insurance->InsurancePayment->getData('paginate', $options, array(
            'status' => 'all',
        ));
        $values = $this->paginate('InsurancePayment');

        $this->set(compact(
            'values'
        ));
    }

    function _calDataIndexConvertion ( $data, $reverse = false ) {
        $data =  $this->MkCommon->dataConverter($data, array(
            'date' => array(
                'InsurancePayment' => array(
                    'payment_date',
                ),
            )
        ), $reverse);

        if( empty($reverse) && !empty($data) ) {
            $this->MkCommon->_callAllowClosing($data, 'InsurancePayment', 'payment_date');
            $data = Common::_callCheckCostCenter($data, 'InsurancePayment');
        }

        return $data;
    }

    function _callDataSupport () {
        $coas = $this->GroupBranch->Branch->BranchCoa->getCoas();
        $cogs = $this->MkCommon->_callCogsOptGroup('InsurancePayment');

        $this->MkCommon->_layout_file('select');
        $this->set(compact(
            'coas'
        ));
    }

    function payment_add(){
        $this->set('sub_module_title', __('Tambah Pembayaran Asuransi'));
        $this->set('active_menu', 'insurance_payments');

        $data = $this->request->data;

        if( !empty($data) ) {
            $data = $this->_calDataIndexConvertion( $data );
            $result = $this->Insurance->InsurancePayment->doSave( $data );
            $this->MkCommon->setProcessParams($result, array(
                'controller' => 'insurances',
                'action' => 'payments',
                'admin' => false,
            ));
            $this->request->data = $this->_calDataIndexConvertion($this->request->data, true);

            $details = Common::hashEmptyField($this->request->data, 'InsurancePaymentDetail');
            $details = $this->Insurance->InsurancePayment->InsurancePaymentDetail->getMergeList($details, array(
                'contain' => array(
                    'Insurance',
                ),
            ));

            $data = Hash::insert($data, 'InsurancePaymentDetail', $details);
            $this->request->data = $data;
        }

        $this->_callDataSupport();
        $this->render('payment_add');
    }

    function payment_edit( $id = false ){
        $this->set('sub_module_title', __('Edit Pembayaran Insurance'));
        $this->set('active_menu', 'insurance_payments');

        $value = $this->Insurance->InsurancePayment->getData('first', array(
            'conditions' => array(
                'InsurancePayment.id' => $id,
            ),
        ));

        if( !empty($value) ) {
            $value = $this->Insurance->InsurancePayment->InsurancePaymentDetail->getMerge($value, $id);
            $value = $this->Insurance->InsurancePayment->getMergeList($value, array(
                'contain' => array(
                    'Cogs',
                ),
            ));

            if( !empty($value['InsurancePaymentDetail']) ) {
                foreach ($value['InsurancePaymentDetail'] as $key => $detail) {
                    $insurance_id = $this->MkCommon->filterEmptyField($detail, 'InsurancePaymentDetail', 'insurance_id');
                    $insurance_installment_id = $this->MkCommon->filterEmptyField($detail, 'InsurancePaymentDetail', 'insurance_installment_id');

                    $detail = $this->Insurance->getMerge($detail, $insurance_id);
                    $detail = $this->Insurance->InsuranceInstallment->getMerge($detail, $insurance_installment_id);
                    $value['InsurancePaymentDetail'][$key] = $detail;
                }
            }

            $data = $this->request->data;
            $data = $this->_calDataIndexConvertion( $data );
            $result = $this->Insurance->InsurancePayment->doSave( $data, $value, $id );
            $this->MkCommon->setProcessParams($result, array(
                'controller' => 'insurances',
                'action' => 'payments',
                'admin' => false,
            ));
            $this->request->data = $this->_calDataIndexConvertion($this->request->data, true);
            $this->MkCommon->getLogs($this->params['controller'], array( 'payment_add', 'payment_edit', 'payment_delete' ), $id);

            $this->_callDataSupport();
            $this->set(compact(
                'id'
            ));
            $this->render('payment_form');
        } else {
            $this->MkCommon->redirectReferer(__('Pembayaran insurance tidak ditemukan'), 'error');
        }
    }

    function detail_payment( $id = false ){
        $this->set('sub_module_title', __('Info Pembayaran Asuransi'));
        $this->set('active_menu', 'insurance_payments');

        $value = $this->Insurance->InsurancePayment->getData('first', array(
            'conditions' => array(
                'InsurancePayment.id' => $id,
            ),
        ));

        if( !empty($value) ) {
            $value = $this->Insurance->InsurancePayment->getMergeList($value, array(
                'contain' => array(
                    'Cogs',
                    'Coa',
                    'InsurancePaymentDetail' => array(
                        'Insurance',
                    ),
                ),
            ));
            $value = $this->_calDataIndexConvertion($value);
            $this->MkCommon->getLogs($this->params['controller'], array( 'payment_add', 'payment_edit', 'payment_delete' ), $id);

            $this->set(compact(
                'id', 'value'
            ));
        } else {
            $this->MkCommon->redirectReferer(__('Pembayaran asuransi tidak ditemukan'), 'error');
        }
    }

    function payment_delete($id){
        $is_ajax = $this->RequestHandler->isAjax();
        $action_type = 'insurance_payments';
        $msg = array(
            'msg' => '',
            'type' => 'error'
        );
        $value = $this->Insurance->InsurancePayment->getData('first', array(
            'conditions' => array(
                'InsurancePayment.id' => $id
            ),
        ));

        if( !empty($value) ){
            $this->MkCommon->_callAllowClosing($value, 'InsurancePayment', 'payment_date');
            
            $value = $this->Insurance->InsurancePayment->InsurancePaymentDetail->getMerge($value, $id);
            $no_doc = $this->MkCommon->filterEmptyField($value, 'InsurancePayment', 'no_doc');
            $coa_id = $this->MkCommon->filterEmptyField($value, 'InsurancePayment', 'coa_id');
            $payment_date = $this->MkCommon->filterEmptyField($value, 'InsurancePayment', 'payment_date');
            $vendor_id = $this->MkCommon->filterEmptyField($value, 'InsurancePayment', 'vendor_id');
            $cogs_id = $this->MkCommon->filterEmptyField($value, 'InsurancePayment', 'cogs_id');
            
            $title = sprintf(__('Pembayaran Asuransi #%s'), $no_doc);
            $title = sprintf(__('<i>Pembatalan</i> %s'), $this->MkCommon->filterEmptyField($value, 'InsurancePayment', 'note', $title));

            if(!empty($this->request->data)){
                $data = $this->request->data;
                $rejected_date = $this->MkCommon->filterEmptyField($data, 'InsurancePayment', 'canceled_date');

                if(!empty($rejected_date)){
                    $data['InsurancePayment']['rejected_date'] = $this->MkCommon->getDate($rejected_date);
                    $data['InsurancePayment']['rejected'] = 1;

                    $this->Insurance->InsurancePayment->id = $id;
                    $this->Insurance->InsurancePayment->set($data);

                    if($this->Insurance->InsurancePayment->save()){
                        if( !empty($value['InsurancePaymentDetail']) ) {
                            $total = 0;

                            foreach ($value['InsurancePaymentDetail'] as $key => $detail) {
                                $detail_id = $this->MkCommon->filterEmptyField($detail, 'InsurancePaymentDetail', 'id');
                                $insurance_id = $this->MkCommon->filterEmptyField($detail, 'InsurancePaymentDetail', 'insurance_id');

                                $total += $this->MkCommon->filterEmptyField($detail, 'InsurancePaymentDetail', 'total', 0);

                                $totalInsurancePaid = $this->Insurance->InsurancePayment->InsurancePaymentDetail->getData('count', array(
                                    'conditions' => array(
                                        'InsurancePayment.status' => 1,
                                        'InsurancePayment.rejected' => 0,
                                        'InsurancePaymentDetail.insurance_id' => $insurance_id,
                                        'InsurancePaymentDetail.id <>' => $detail_id,
                                    ),
                                    'contain' => array(
                                        'InsurancePayment',
                                    ),
                                ));

                                if( !empty($totalInsurancePaid) ) {
                                    $statusInsurancePayment = 'half_paid';
                                } else {
                                    $statusInsurancePayment = 'unpaid';
                                }

                                $this->Insurance->id = $insurance_id;
                                $this->Insurance->set('transaction_status', $statusInsurancePayment);
                                $this->Insurance->save();
                            }

                            if( !empty($total) ) {
                                $coaAsuransi = $this->Insurance->InsurancePayment->Coa->CoaSettingDetail->getMerge(array(), 'Asuransi', 'CoaSettingDetail.label');
                                $insurance_coa_id = Common::hashEmptyField($coaAsuransi, 'CoaSettingDetail.coa_id');

                                $this->User->Journal->setJournal($total, array(
                                    'credit' => $insurance_coa_id,
                                    'debit' => $coa_id,
                                ), array(
                                    'cogs_id' => $cogs_id,
                                    'date' => $payment_date,
                                    'document_id' => $id,
                                    'title' => $title,
                                    'document_no' => $no_doc,
                                    'type' => 'insurance_payment_void',
                                ));
                            }
                        }

                        $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
                        $msg = array(
                            'msg' => sprintf(__('Berhasil membatalkan pembayaran asuransi #%s'), $noref),
                            'type' => 'success'
                        );
                        $this->MkCommon->setCustomFlash( $msg['msg'], $msg['type']);  
                        $this->Log->logActivity( sprintf(__('Berhasil membatalkan pembayaran asuransi #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id ); 
                    }else{
                        $this->Log->logActivity( sprintf(__('Gagal membatalkan pembayaran asuransi #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id ); 
                    }
                }else{
                    $msg = array(
                        'msg' => __('Harap masukkan tanggal pembatalan pembayaran asuransi.'),
                        'type' => 'error'
                    );
                }
            }

            $this->set('value', $value);
        }else{
            $msg = array(
                'msg' => __('Pembayaran asuransi tidak ditemukan'),
                'type' => 'error'
            );
        }

        $modelName = 'InsurancePayment';
        $canceled_date = !empty($this->request->data['InsurancePayment']['canceled_date']) ? $this->request->data['InsurancePayment']['canceled_date'] : false;
        $this->set(compact(
            'msg', 'is_ajax', 'action_type',
            'canceled_date', 'modelName'
        ));
        $this->render('/Elements/blocks/common/form_delete');
    }

    function bypass_insurance_unpaid(){
        $named = $this->MkCommon->filterEmptyField($this->params, 'named');
        $payment_id = $this->MkCommon->filterEmptyField($named, 'payment_id');

        $options = array(
            'conditions' => array(
                'Insurance.transaction_status' => array( 'unpaid', 'half_paid' ),
            ),
            'contain' => false,
            'limit' => Configure::read('__Site.config_pagination'),
        );

        $params = $this->MkCommon->_callRefineParams($this->params);
        $options =  $this->Insurance->_callRefineParams($params, $options, 'Insurance');

        $this->paginate = $this->Insurance->getData('paginate', $options, array(
            'status' => 'active',
        ));
        $values = $this->paginate('Insurance');

        if( !empty($values) ) {
            foreach ($values as $key => $value) {
                $insurance_id = $this->MkCommon->filterEmptyField($value, 'Insurance', 'id');

                $value = $this->Insurance->_callLastPaidInstallment($value, $insurance_id, $payment_id);
                $values[$key] = $value;
            }
        }
        
        $data_change = $data_action = 'browse-invoice';
        $title = __('Detail Pembayaran');
        $this->set(compact(
            'data_change', 'title', 'values',
            'data_action'
        ));
    }

    public function report() {
        $module_title = __('Laporan Asuransi');

        $this->set('sub_module_title', $module_title);
        $options =  $this->Insurance->getData('paginate', false, array(
            'branch' => false,
        ));

        $params = $this->MkCommon->_callRefineParams($this->params);
        $options =  $this->Insurance->_callRefineParams($params, $options);

        $options['limit'] = Configure::read('__Site.config_pagination');
        $this->paginate = $options;
        $values = $this->paginate('Insurance');

        if(!empty($values)){
            foreach ($values as $key => $value) {
                $id = $this->MkCommon->filterEmptyField($value, 'Insurance', 'id');
        
                $value = $this->Insurance->getMergeList($value, array(
                    'contain' => array(
                        'Branch',
                    ),
                ));

                $value = $this->Insurance->InsurancePayment->getPayment($value, $id);

                $values[$key] = $value;
            }
        }

        $this->set('active_menu', 'insurance_report');
        $this->set(compact(
            'values', 'module_title'
        ));

        $this->MkCommon->_layout_file(array(
            'select',
            // 'freeze',
        ));
    }

    function bypass_trucks(){
        $this->loadModel('Truck');

        $params = $this->params->params;
        $params = $this->MkCommon->_callRefineParams($params);
        $options =  $this->Truck->_callRefineParams($params, array(
            'order' => array(
                'Truck.nopol' => 'ASC',
            ),
            'limit' => Configure::read('__Site.config_pagination'),
        ));

        $this->paginate = $this->Truck->getData('paginate', $options, true);
        $values = $this->paginate('Truck');
        $values = $this->Truck->getMergeList($values, array(
            'contain' => array(
                'Branch',
                'Company',
                'TruckBrand',
                'TruckCategory',
                // 'Driver' => array(
                //     'elements' => array(
                //         'branch' => false,
                //     ),
                // ),
            ),
        ));

        $title = __('Pilih Truk');

        $this->set(compact(
            'title', 'values'
        ));
    }
}