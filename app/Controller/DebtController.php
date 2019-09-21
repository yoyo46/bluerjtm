<?php
App::uses('AppController', 'Controller');
class DebtController extends AppController {
	public $uses = array(
        'Debt',
    );
    public $components = array(
        'RjDebt', 'RmReport'
    );
    public $module_title = 'Hutang Karyawan';

    function beforeFilter() {
        parent::beforeFilter();
        $this->set('title_for_layout', __('ERP RJTM | Hutang Karyawan'));
        $this->set('module_title', $this->module_title);
        $this->set('active_menu', 'debt');
    }

    public function search ( $action, $addParam = false ) {
        $params = Common::_search($this, $action, $addParam);
        $this->redirect($params);
    }

    public function bypass_search ( $action, $addParam = false ) {
        $params = Common::_search($this, $action, $addParam);

        $params['bypass'] = true;
        $this->redirect($params);
    }

    function index(){
        $params = $this->params->params;
        $dateFrom = Common::hashEmptyField($params, 'named.DateFrom');
        $dateTo = Common::hashEmptyField($params, 'named.DateTo');

        $params = $this->MkCommon->_callRefineParams($params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));

        $options =  $this->Debt->_callRefineParams($params, array(
            'limit' => Configure::read('__Site.config_pagination'),
            'group' => array(
                'Debt.id',
            ),
        ));

        $this->paginate = $this->Debt->getData('paginate', $options);
        $values = $this->paginate('Debt');

        $values = $this->Debt->getMergeList($values, array(
            'contain' => array(
                'Coa',
                'DebtDetail' => array(
                    'type' => 'all',
                    'contain' => array(
                        'ViewStaff',
                    ),
                    'forceMerge' => true,
                ),
            ),
        ));

        $this->MkCommon->_layout_file(array(
            'select',
        ));

        $coas = $this->User->Journal->Coa->_callOptGroup();
        $this->set('sub_module_title', __('Hutang Karyawan'));
        $this->set('values', $values);
        $this->set('coas', $coas);
    }

    function add(){
        $this->set('sub_module_title', __('Tambah %s', $this->module_title));
        $this->doDebt();
    }

    function edit($id = NULL){
        $this->set('sub_module_title', 'Ubah %s', $this->module_title);
        $value = $this->Debt->getData('first', array(
            'conditions' => array(
                'Debt.id' => $id
            ),
            'contain' => array(
                'DebtDetail',
            ),
        ));

        if(!empty($value)){
            $this->doDebt($id, $value);
            $this->render('add');
        }else{
            $this->MkCommon->setCustomFlash(__('%s tidak ditemukan', $this->module_title), 'error');  
            $this->redirect(array(
                'action' => 'index'
            ));
        }
    }

    function doDebt($id = false, $value = false){
        $grand_total = Common::hashEmptyField($value, 'Debt.total');
        $user_id = Common::hashEmptyField($value, 'Debt.user_id');

        if(!empty($this->request->data)){
            $no_data = false;
            $data = $this->request->data;
            $data = $this->MkCommon->dataConverter($data, array(
                'date' => array(
                    'Debt' => array(
                        'transaction_date',
                    ),
                )
            ));
            $this->MkCommon->_callAllowClosing($data, 'Debt.transaction_date');
            $data = Common::_callCheckCostCenter($data, 'Debt');

            $validate = true;
            $grandtotal = 0;

            $cogs_id = Common::hashEmptyField($data, 'Debt.cogs_id');
            $document_no = Common::hashEmptyField($data, 'Debt.nodoc');
            $coa_id = Common::hashEmptyField($data, 'Debt.coa_id');
            $note = Common::hashEmptyField($data, 'Debt.note');
            $nodoc = Common::hashEmptyField($data, 'Debt.nodoc');

            $transaction_date = Common::hashEmptyField($data, 'Debt.transaction_date');
            $transaction_status = Common::hashEmptyField($data, 'Debt.transaction_status');

            $data['Debt']['branch_id'] = Configure::read('__Site.config_branch_id');
            $data['Debt']['user_id'] = $this->user_id;
            $redirectUrl = array(
                'controller' => 'debt',
                'action' => 'add',
                'admin' => false,
            );

            if( empty($nodoc) ) {
                $data['Debt']['nodoc'] = $this->Debt->generateNoDoc();
            }

            if($id && $value){
                $data['Debt']['id'] = $id;
                $msg = 'mengubah';
            }else{
                $msg = 'menambah';
            }

            if(!empty($data['DebtDetail']['employe_id'])){
                $arr_list = array();

                foreach ($data['DebtDetail']['employe_id'] as $key => $employe_id) {
                    $detail_note = (!empty($data['DebtDetail']['note'][$key])) ? $data['DebtDetail']['note'][$key] : false;
                    $type = (!empty($data['DebtDetail']['type'][$key])) ? $data['DebtDetail']['type'][$key] : false;
                    $total_detail = (!empty($data['DebtDetail']['total'][$key])) ? str_replace(array( ',' ), array( '' ), $data['DebtDetail']['total'][$key]) : 0;

                    $grandtotal += $total_detail;

                    $arr_list[] = array(
                        'employe_id' => $employe_id,
                        'type' => $type,
                        'note' => $detail_note,
                        'total' => $total_detail,
                    );
                }

                $data['DebtDetail'] = $arr_list;
                $data['Debt']['total'] = $grandtotal;
            }else{
                $validate = false;
            }

            if($this->Debt->saveAll($data, array(
                'validate' => 'only',
            )) && $validate){
                if($this->Debt->save($data)){
                    $id = $this->Debt->id;

                    $coaDebt = $this->User->Coa->CoaSettingDetail->getMerge(array(), 'Debt', 'CoaSettingDetail.label');
                    $debt_coa_id = !empty($coaDebt['CoaSettingDetail']['coa_id'])?$coaDebt['CoaSettingDetail']['coa_id']:false;

                    if($id && $value){
                        $this->Debt->DebtDetail->deleteAll(array(
                            'DebtDetail.debt_id' => $id
                        ));
                    }

                    if(!empty($data['DebtDetail'])){
                        if( !empty($note) ) {
                            $title = $note;
                        } else {
                            $title = __('Hutang Karyawan Tgl %s', Common::formatDate($transaction_date, 'd M Y'));
                        }

                        foreach ($data['DebtDetail'] as $key => $value) {
                            $value['debt_id'] = $id;
                            $employe_id = $value['employe_id'];
                            $total = $value['total'];

                            $this->Debt->DebtDetail->create();
                            $this->Debt->DebtDetail->set($value);

                            $flag = $this->Debt->DebtDetail->save();

                            if( $transaction_status == 'posting' && !empty($debt_coa_id) ) {
                                $this->User->Journal->setJournal($total, array(
                                    'debit' => $debt_coa_id
                                ), array(
                                    'cogs_id' => $cogs_id,
                                    'document_id' => $id,
                                    'title' => $title,
                                    'document_no' => $document_no,
                                    'type' => 'debt',
                                    'date' => $transaction_date,
                                ));
                            }
                        }

                        if( $transaction_status == 'posting' && !empty($debt_coa_id) ) {
                            $this->User->Journal->setJournal($grandtotal, array(
                                'credit' => $coa_id,
                            ), array(
                                'cogs_id' => $cogs_id,
                                'document_id' => $id,
                                'title' => $title,
                                'document_no' => $document_no,
                                'type' => 'debt',
                                'date' => $transaction_date,
                            ));
                        }
                    }

                    $this->params['old_data'] = $value;
                    $this->params['data'] = $data;

                    $this->Log->logActivity( sprintf(__('Sukses %s Hutang Karyawan #%s'), $msg, $this->Debt->id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );

                    $noref = str_pad($this->Debt->id, 6, '0', STR_PAD_LEFT);
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Hutang Karyawan #%s'), $msg, $noref), 'success');

                    $this->redirect($redirectUrl);
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Hutang Karyawan'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s Hutang Karyawan #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
                }
            }else{
                $text = sprintf(__('Gagal %s Hutang Karyawan'), $msg);

                // if($validate){
                //     $text .= __(', COA harap di pilih');
                // }
                $this->MkCommon->setCustomFlash($text, 'error');
            }

            $this->request->data['Debt']['cogs_id'] = Common::hashEmptyField($data, 'Debt.cogs_id');
        }else{
            if($id && $value){
                $this->request->data = $data = $value;
                $this->request->data['Debt']['transaction_date'] = $this->MkCommon->getDate($this->request->data['Debt']['transaction_date'], true);
            }
        }

        if(!empty($data['DebtDetail'])){
            $this->loadModel('Coa');

            foreach ($data['DebtDetail'] as $key => $value) {
                $karyawan_id = Common::hashEmptyField($value, 'employe_id');
                $type = Common::hashEmptyField($value, 'type');
                $value = $this->Debt->DebtDetail->ViewStaff->getMerge($value, $karyawan_id, $type);

                $karyawan_name = Common::hashEmptyField($value, 'ViewStaff.name_code');

                if(!empty($karyawan_name)){
                    $data['DebtDetail'][$key]['karyawan_name'] = $karyawan_name;
                    $data['DebtDetail'][$key]['type'] = $type;
                }
            }

            $detail_data['DebtDetail'] = $data['DebtDetail'];
            $this->set('detail_data', $detail_data);
        }

        $coas = $this->GroupBranch->Branch->BranchCoa->getCoas();
        $cogs = $this->MkCommon->_callCogsOptGroup('Debt');

        if( !empty($id) ) {
            $this->MkCommon->getLogs($this->params['controller'], array( 'edit', 'add', 'delete' ), $id);
        }
        
        $this->MkCommon->_layout_file(array(
            'select',
        ));
        $this->set(compact(
            'coas', 'id', 'value'
        ));
    }

    function detail($id = false){
        $this->set('sub_module_title', __('Detail %s', $this->module_title));
        $value = $this->Debt->getData('first', array(
            'conditions' => array(
                'Debt.id' => $id,
            ),
        ), array(
            'branch' => false,
        ));

        if( !empty($value) ) {
            $user_id = Common::hashEmptyField($value, 'Debt.user_id');
            $coa_id = Common::hashEmptyField($value, 'Debt.coa_id');
            $cogs_id = Common::hashEmptyField($value, 'Debt.cogs_id');

            $transaction_date = Common::hashEmptyField($value, 'Debt.transaction_date');
            $nodoc = Common::hashEmptyField($value, 'Debt.nodoc');
            $total = Common::hashEmptyField($value, 'Debt.total', 0);
            $note = Common::hashEmptyField($value, 'Debt.note');
            
            $allow_closing = $this->MkCommon->_callAllowClosing($value, 'Debt', 'transaction_date', 'Y-m', false);
            
            $value = $this->User->getMerge($value, $user_id);
            $value = $this->Debt->Coa->getMerge($value, $coa_id);

            $value = $this->Debt->getMergeList($value, array(
                'contain' => array(
                    'DebtDetail' => array(
                        'contain' => array(
                            'ViewStaff',
                        ),
                    ),
                    'Cogs',
                ),
            ));

            $this->MkCommon->getLogs($this->params['controller'], array( 'edit', 'add', 'delete' ), $id);
            $this->set(compact(
                'value'
            ));
        } else {
            $this->MkCommon->setCustomFlash(__('Hutang Karyawan tidak ditemukan.'), 'error');
            $this->redirect($this->referer());
        }
    }

    function delete($id = NULL){
        $value = $this->Debt->getData('first', array(
            'conditions' => array(
                'Debt.id' => $id,
                'Debt.transaction_status <>' => 'void',
            )
        ), array(
            'branch' => false,
        ));

        if(!empty($value)){
            $this->MkCommon->_callAllowClosing($value, 'Debt', 'transaction_date');

            $value = $this->Debt->getMergeList($value, array(
                'contain' => array(
                    'DebtDetail',
                ),
            ));

            $nodoc = Common::hashEmptyField($value, 'Debt.nodoc');
            $transaction_date = Common::hashEmptyField($value, 'Debt.transaction_date');
            $document_coa_id = Common::hashEmptyField($value, 'Debt.coa_id');
            $grand_total = Common::hashEmptyField($value, 'Debt.total');
            $note = Common::hashEmptyField($value, 'Debt.note');
            $cogs_id = Common::hashEmptyField($value, 'Debt.cogs_id');

            $this->Debt->id = $id;
            $this->Debt->set('transaction_status', 'void');

            if($this->Debt->save()){
                $coaDebt = $this->User->Coa->CoaSettingDetail->getMerge(array(), 'Debt', 'CoaSettingDetail.label');
                $debt_coa_id = !empty($coaDebt['CoaSettingDetail']['coa_id'])?$coaDebt['CoaSettingDetail']['coa_id']:false;

                if( !empty($value['DebtDetail']) && !empty($debt_coa_id) ) {
                    foreach ($value['DebtDetail'] as $key => $value) {
                        $employe_id = Common::hashEmptyField($value, 'DebtDetail.employe_id');
                        $total = Common::hashEmptyField($value, 'DebtDetail.total');


                        if( !empty($note) ) {
                            $title = __('<i>Pembatalan</i> ').$note;
                        } else {
                            $title = sprintf(__('<i>Pembatalan</i> Hutang Karyawan Tgl %s'), Common::formatDate($transaction_date, 'd M Y'));
                        }

                        $this->User->Journal->setJournal($total, array(
                            'credit' => $debt_coa_id,
                        ), array(
                            'cogs_id' => $cogs_id,
                            'document_id' => $id,
                            'title' => $title,
                            'document_no' => $nodoc,
                            'type' => 'void_debt',
                            'date' => $transaction_date,
                        ));
                    }
                }

                if( !empty($title) ) {
                    $this->User->Journal->setJournal($grand_total, array(
                        'debit' => $document_coa_id,
                    ), array(
                        'cogs_id' => $cogs_id,
                        'document_id' => $id,
                        'title' => $title,
                        'document_no' => $nodoc,
                        'type' => 'void_debt',
                        'date' => $transaction_date,
                    ));
                }

                $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
                $this->MkCommon->setCustomFlash(sprintf(__('Sukses merubah status Hutang Karyawan #%s.'), $noref), 'success');
                $this->Log->logActivity( sprintf(__('Sukses merubah status Hutang Karyawan ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal merubah status Hutang Karyawan ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id ); 
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Hutang Karyawan tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    function bypass_note(){
        $values = array();
        $keyword = Common::hashEmptyField($this->request->data, 'query');
        
        if( !empty($keyword) ) {
            $values = $this->Debt->getData('list', array(
                'conditions' => array(
                    'Debt.user_id' => $this->user_id,
                    'Debt.note LIKE' => '%'.$keyword.'%',
                ),
                'fields' => array(
                    'Debt.id', 'Debt.note',
                ),
                'order' => array(
                    'Debt.id' => 'DESC',
                ),
                'limit' => 10,
            ));

            if( !empty($values) ) {
                $values = array_values($values);
            }
        }

        $this->autoRender = false;
        $this->layout = false;

        return json_encode($values);
    }

    function bypass_users(){
        $this->loadModel('ViewStaff');

        $params = $this->params->params;
        $params = $this->MkCommon->_callRefineParams($params);
        $options =  $this->ViewStaff->_callRefineParams($params, array(
            'limit' => Configure::read('__Site.config_pagination'),
        ));

        $this->paginate = $this->ViewStaff->getData('paginate', $options);
        $values = $this->paginate('ViewStaff');

        $data_action = 'browse-cash-banks';
        $title = __('List Karyawan');

        $this->set(compact(
            'data_action', 'title', 'values'
        ));
    }

    function payments(){
        $this->loadModel('DebtPayment');
        $options = array(
            'order' => array(
                'DebtPayment.id' => 'DESC',
            ),
            'group' => array(
                'DebtPayment.id',
            ),
        );

        $this->set('active_menu', 'debt_payments');
        $this->set('sub_module_title', __('Pembayaran Hutang'));
        
        $dateFrom = date('Y-m-d', strtotime('-1 Month'));
        $dateTo = date('Y-m-d');

        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));
        $options =  $this->DebtPayment->_callRefineParams($params, $options);

        $this->paginate = $this->DebtPayment->getData('paginate', $options);
        $values = $this->paginate('DebtPayment');

        $values = $this->DebtPayment->getMergeList($values, array(
            'contain' => array(
                'DebtPaymentDetail' => array(
                    'type' => 'all',
                    'contain' => array(
                        'DebtDetail' => array(
                            'contain' => array(
                                'ViewStaff',
                            ),
                        ),
                    ),
                    'forceMerge' => true,
                ),
            ),
        ));

        $this->set(compact(
            'values'
        )); 
    }

    function payment_add(){
        $this->loadModel('DebtPayment');
        $module_title = __('Tambah Pembayaran Hutang');
        $this->set('sub_module_title', $module_title);

        $this->doDebtPayment();
    }

    function payment_edit( $id = false ){
        $this->loadModel('DebtPayment');
        $module_title = __('Edit Pembayaran Hutang');
        $this->set('sub_module_title', $module_title);

        $head_office = Configure::read('__Site.config_branch_head_office');

        if( !empty($head_office) ) {
            $elementRevenue = array(
                'branch' => false,
            );
        } else {
            $elementRevenue = false;
        }

        $value = $this->DebtPayment->getData('first', array(
            'conditions' => array(
                'DebtPayment.id' => $id
            ),
        ), $elementRevenue);
        $value = $this->DebtPayment->DebtPaymentDetail->getMerge($value, $id);

        $this->MkCommon->getLogs($this->params['controller'], array( 'payment_add', 'payment_edit', 'payment_delete' ), $id);
        $this->doDebtPayment( $id, $value );
    }

    function doDebtPayment( $id = false, $value = false ){
        $this->set('active_menu', 'debt_payments');
        $data = $this->request->data;

        if(!empty($data)){
            $data = $this->MkCommon->dataConverter($data, array(
                'date' => array(
                    'DebtPayment' => array(
                        'date_payment',
                    ),
                )
            ));
            $this->MkCommon->_callAllowClosing($data, 'DebtPayment', 'date_payment');
            // $nodoc = Common::hashEmptyField($data, 'DebtPayment.nodoc');

            $data['DebtPayment']['branch_id'] = Configure::read('__Site.config_branch_id');
            $data = Common::_callCheckCostCenter($data, 'DebtPayment');

            // if( empty($nodoc) ) {
            //     $data['DebtPayment']['nodoc'] = $this->Debt->generateNoDoc();
            // }

            $dataAmount = Common::hashEmptyField($data, 'DebtPaymentDetail.amount');
            $flagPaymentDetail = $this->doDebtPaymentDetail($dataAmount, $data, $id);

            if( !empty($id) ) {
                $this->DebtPayment->id = $id;
            } else {
                $this->DebtPayment->create();
            }

            $this->DebtPayment->set($data);

            if( $this->DebtPayment->validates() && !empty($flagPaymentDetail) ){
                if($this->DebtPayment->save()){
                    $id = $this->DebtPayment->id;
                    $flagPaymentDetail = $this->doDebtPaymentDetail($dataAmount, $data, $id);

                    $this->params['old_data'] = $value;
                    $this->params['data'] = $data;

                    $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
                    $this->MkCommon->setCustomFlash(sprintf(__('Berhasil melakukan Pembayaran Hutang #%s'), $noref), 'success'); 
                    $this->Log->logActivity( sprintf(__('Berhasil melakukan Pembayaran Hutang #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
                    
                    $this->redirect(array(
                        'action' => 'payments',
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal melakukan Pembayaran Hutang'), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal melakukan Pembayaran Hutang #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
                }
            }else{
                $msgError = array();

                if( !empty($this->DebtPayment->DebtPaymentDetail->validationErrors) ) {
                    $errorPaymentDetails = $this->DebtPayment->DebtPaymentDetail->validationErrors;

                    foreach ($errorPaymentDetails as $key => $errorPaymentDetail) {
                        if( !empty($errorPaymentDetail) ) {
                            foreach ($errorPaymentDetail as $key => $err_msg) {
                                if( is_array($err_msg) ) {
                                    $msgError[] = reset($err_msg);
                                } else {
                                    $msgError[] = $err_msg;
                                }
                            }
                        }
                    }
                }

                if( !empty($msgError) ) {
                    $this->MkCommon->setCustomFlash('<ul><li>'.implode('</li><li>', $msgError).'</li></ul>', 'error'); 
                } else if( $flagPaymentDetail ) {
                    $this->MkCommon->setCustomFlash(__('Gagal melakukan Pembayaran Hutang'), 'error'); 
                }
            }

            $this->request->data['DebtPayment']['date_payment'] = !empty($data['DebtPayment']['date_payment']) ? $data['DebtPayment']['date_payment'] : '';
            $this->request->data['DebtPayment']['cogs_id'] = Common::hashEmptyField($data, 'DebtPayment.cogs_id');;
        } else if( !empty($value) ) {
            if( !empty($value['DebtPaymentDetail']) ) {
                foreach ($value['DebtPaymentDetail'] as $key => $val) {
                    $debt_id = Common::hashEmptyField($val, 'DebtPaymentDetail.debt_id');
                    $debt_detail_id = Common::hashEmptyField($val, 'DebtPaymentDetail.debt_detail_id');
                    $amount = Common::hashEmptyField($val, 'DebtPaymentDetail.amount');

                    $debt = $this->Debt->DebtDetail->findById($debt_detail_id);
                    $debt = $this->Debt->DebtDetail->getMergeList($debt, array(
                        'contain' => array(
                            'Debt',
                            'ViewStaff',
                        ),
                    ));
                    $total_debt = Common::hashEmptyField($debt, 'DebtDetail.total');
                    $total_dibayar = $this->DebtPayment->DebtPaymentDetail->getTotalPayment($debt_detail_id, $id);

                    $this->request->data['DebtDetail'][$key] = $debt;
                    $this->request->data['DebtDetail'][$key]['DebtDetail']['last_paid'] = $total_debt - $total_dibayar;

                    $this->request->data['DebtPaymentDetail']['amount'][$key] = $amount;
                    $this->request->data['DebtPaymentDetail']['debt_id'][$key] = $debt_id;
                    $this->request->data['DebtPaymentDetail']['debt_detail_id'][$key] = $debt_detail_id;
                }
            }

            $this->request->data['DebtPayment'] = Common::hashEmptyField($value, 'DebtPayment');
        }

        $coas = $this->GroupBranch->Branch->BranchCoa->getCoas();
        $cogs = $this->MkCommon->_callCogsOptGroup('DebtPayment');

        $this->MkCommon->_layout_file('select');
        $this->set(compact(
            'id', 'coas'
        ));
        $this->render('payment_add');
    }

    function doDebtPaymentDetail ( $dataAmount, $data, $debt_payment_id = false ) {
        $flagPaymentDetail = true;
        $totalPayment = 0;
        $date_payment = Common::hashEmptyField($data, 'DebtPayment.date_payment');
        $cogs_id = Common::hashEmptyField($data, 'DebtPayment.cogs_id');
        $debt_no = Common::hashEmptyField($data, 'DebtPayment.nodoc');
        $data = $this->request->data;

        if( !empty($debt_payment_id) ) {
            $this->DebtPayment->DebtPaymentDetail->updateAll( array(
                'DebtPaymentDetail.status' => 0,
            ), array(
                'DebtPaymentDetail.debt_payment_id' => $debt_payment_id,
            ));
        }


        if( !empty($dataAmount) ) {
            $dataDetail = array();

            foreach ($dataAmount as $key => $amount) {
                $debt_id = !empty($data['DebtPaymentDetail']['debt_id'][$key])?$data['DebtPaymentDetail']['debt_id'][$key]:false;
                $debt_detail_id = !empty($data['DebtPaymentDetail']['debt_detail_id'][$key])?$data['DebtPaymentDetail']['debt_detail_id'][$key]:false;
                $employe_id = !empty($data['DebtPaymentDetail']['employe_id'][$key])?$data['DebtPaymentDetail']['employe_id'][$key]:false;
                $amount = !empty($amount)?$this->MkCommon->convertPriceToString($amount, 0):0;
                
                $value = $this->Debt->DebtDetail->findById($debt_detail_id);
                $value = $this->Debt->DebtDetail->getMergeList($value, array(
                    'contain' => array(
                        'Debt',
                        'ViewStaff',
                    ),
                ));
                $total_debt = Common::hashEmptyField($value, 'DebtDetail.total');

                $dataPaymentDetail = array(
                    'DebtPaymentDetail' => array(
                        'debt_id' => $debt_id,
                        'debt_detail_id' => $debt_detail_id,
                        'employe_id' => $employe_id,
                        'amount' => $amount,
                    ),
                );

                $totalPayment += $amount;
                $total_dibayar = $this->DebtPayment->DebtPaymentDetail->getTotalPayment($debt_detail_id, $debt_payment_id);

                $this->request->data['DebtDetail'][$key] = $value;
                $this->request->data['DebtDetail'][$key]['DebtDetail']['last_paid'] = $total_debt - $total_dibayar;
                
                $total_dibayar += $amount;
                
                if( $total_debt < $total_dibayar ) {
                    $dataPaymentDetail['DebtPaymentDetail']['out_of_total'] = false;
                }

                if( !empty($debt_payment_id) ) {
                    $dataPaymentDetail['DebtPaymentDetail']['debt_payment_id'] = $debt_payment_id;

                    if( !empty($total_dibayar) ) {
                        $flagPaid = 'half';

                        if( $total_debt <= $total_dibayar ) {
                            $flagPaid = 'full';
                        }
                    
                        $this->Debt->DebtDetail->set('paid_status', $flagPaid);
                        $this->Debt->DebtDetail->id = $debt_detail_id;

                        if( !$this->Debt->DebtDetail->save() ) {
                            $this->Log->logActivity( sprintf(__('Gagal mengubah status pembayaran Hutang Karyawan #%s'), $debt_detail_id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $debt_detail_id );
                        }
                    }
                }

                $dataDetail[] = $dataPaymentDetail;
            }

            if( !empty($debt_payment_id) ) {
                $flagPaymentDetail = $this->DebtPayment->DebtPaymentDetail->saveAll($dataDetail);
            } else {
                $flagPaymentDetail = $this->DebtPayment->DebtPaymentDetail->saveAll($dataDetail, array(
                    'validate' => 'only',
                ));
            }
        } else {
            $flagPaymentDetail = false;
            $this->MkCommon->setCustomFlash(__('Mohon hutang yang akan dibayar.'), 'error'); 
        }

        if( !empty($totalPayment) && !empty($debt_payment_id) ) {
            $this->DebtPayment->id = $debt_payment_id;
            $this->DebtPayment->set('total_payment', $totalPayment);

            if( !$this->DebtPayment->save() ) {
                $this->Log->logActivity( sprintf(__('Gagal mengubah pembayaran Hutang Karyawan #%s'), $debt_payment_id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $debt_payment_id );
            } else {
                $coa_id = Common::hashEmptyField($data, 'DebtPayment.coa_id');
                $coaDebt = $this->User->Coa->CoaSettingDetail->getMerge(array(), 'Debt', 'CoaSettingDetail.label');
                $debt_coa_id = !empty($coaDebt['CoaSettingDetail']['coa_id'])?$coaDebt['CoaSettingDetail']['coa_id']:false;

                if( !empty($debt_coa_id) ) {
                    $titleJournal = sprintf(__('Pembayaran Hutang Karyawan'));
                    $titleJournal = Common::hashEmptyField($data, 'DebtPayment.description', $titleJournal);

                    $this->User->Journal->deleteJournal($debt_payment_id, array(
                        'debt_payment',
                    ));
                    $this->User->Journal->setJournal($totalPayment, array(
                        'debit' => $coa_id,
                    ), array(
                        'cogs_id' => $cogs_id,
                        'date' => $date_payment,
                        'document_id' => $debt_payment_id,
                        'title' => $titleJournal,
                        'document_no' => $debt_no,
                        'type' => 'debt_payment',
                    ));
                }

                if( !empty($dataAmount) ) {
                    $total_amount = 0;
                    foreach ($dataAmount as $key => $amount) {
                        $debt_id = !empty($data['DebtPaymentDetail']['debt_id'][$key])?$data['DebtPaymentDetail']['debt_id'][$key]:false;
                        $debt_detail_id = !empty($data['DebtPaymentDetail']['debt_detail_id'][$key])?$data['DebtPaymentDetail']['debt_detail_id'][$key]:false;
                        $employe_id = !empty($data['DebtPaymentDetail']['employe_id'][$key])?$data['DebtPaymentDetail']['employe_id'][$key]:false;
                        $amount = !empty($amount)?$this->MkCommon->convertPriceToString($amount, 0):0;
                
                        $value = $this->Debt->DebtDetail->findById($debt_detail_id);
                        $total_debt = Common::hashEmptyField($value, 'DebtDetail.total');
                        
                        $total_amount += $amount;
                    }

                    if( !empty($debt_coa_id) ) {
                        $titleJournal = __('Pembayaran Hutang Karyawan');
                        $titleJournal = Common::hashEmptyField($data, 'DebtPayment.description', $titleJournal);

                        $this->User->Journal->setJournal($total_amount, array(
                            'credit' => $debt_coa_id,
                        ), array(
                            'cogs_id' => $cogs_id,
                            'date' => $date_payment,
                            'document_id' => $debt_payment_id,
                            'title' => $titleJournal,
                            'document_no' => $debt_no,
                            'type' => 'debt_payment',
                        ));
                    }
                }
            }
        }

        return $flagPaymentDetail;
    }

    function payment_detail($id = false){
        $this->loadModel('DebtPayment');
        $module_title = __('Hutang Karyawan');
        $elementRevenue = false;
        $head_office = Configure::read('__Site.config_branch_head_office');

        if( !empty($head_office) ) {
            $elementRevenue = array(
                'branch' => false,
            );
        }

        $value = $this->DebtPayment->getData('first', array(
            'conditions' => array(
                'DebtPayment.id' => $id
            ),
        ), $elementRevenue);

        $this->set('active_menu', 'debt_payments');
        $sub_module_title = $title_for_layout = 'Detail Pembayaran Hutang Karyawan';

        if(!empty($value)){
            $coa_id = Common::hashEmptyField($value, 'DebtPayment.coa_id');

            $value = $this->User->Journal->Coa->getMerge($value, $coa_id);
            $value = $this->DebtPayment->DebtPaymentDetail->getMerge($value, $id);

            if( !empty($value['DebtPaymentDetail']) ) {
                foreach ($value['DebtPaymentDetail'] as $key => $val) {
                    $debt_id = Common::hashEmptyField($val, 'DebtPaymentDetail.debt_id');
                    $debt_detail_id = Common::hashEmptyField($val, 'DebtPaymentDetail.debt_detail_id');
                    $amount = Common::hashEmptyField($val, 'DebtPaymentDetail.amount');

                    $debt = $this->Debt->DebtDetail->findById($debt_detail_id);
                    $debt = $this->Debt->DebtDetail->getMergeList($debt, array(
                        'contain' => array(
                            'Debt',
                            'ViewStaff',
                        ),
                    ));
                    $total_debt = Common::hashEmptyField($debt, 'DebtDetail.total');
                    $total_dibayar = $this->DebtPayment->DebtPaymentDetail->getTotalPayment($debt_detail_id, $id);

                    $value['DebtDetail'][$key] = $debt;
                    $value['DebtDetail'][$key]['DebtDetail']['last_paid'] = $total_debt - $total_dibayar;

                    $value['DebtPaymentDetail']['amount'][$key] = $amount;
                    $value['DebtPaymentDetail']['debt_id'][$key] = $debt_id;
                    $value['DebtPaymentDetail']['debt_detail_id'][$key] = $debt_detail_id;
                }
            }

            $this->request->data = $value;
            $this->MkCommon->getLogs($this->params['controller'], array( 'payment_add', 'payment_edit', 'payment_delete' ), $id);

            $coas = $this->GroupBranch->Branch->BranchCoa->getCoas();
            $cogs = $this->MkCommon->_callCogsOptGroup('DebtPayment');

            $this->MkCommon->_layout_file('select');
            $this->set('view', true);
            $this->set(compact(
                'value', 'sub_module_title', 'title_for_layout',
                'module_title', 'coas'
            ));

            $this->render('payment_add');
        }else{
            $this->MkCommon->setCustomFlash(__('Data tidak ditemukan'), 'error');
            $this->redirect($this->referer());
        }
    }

    function payment_delete($id = false){
        $this->loadModel('DebtPayment');
        $is_ajax = $this->RequestHandler->isAjax();
        $msg = array(
            'msg' => '',
            'type' => 'error'
        );
        $value = $this->DebtPayment->getData('first', array(
            'conditions' => array(
                'DebtPayment.id' => $id,
                'OR' => array(
                    array( 'DebtPayment.ttuj_payment_id' => 0 ),
                    array( 'DebtPayment.ttuj_payment_id' => NULL ),
                ),
            ),
        ));

        if( !empty($value) ){
            $this->MkCommon->_callAllowClosing($value, 'DebtPayment', 'date_payment');
            
            if(!empty($this->request->data)){
                $data = $this->request->data;
                $data = $this->MkCommon->dataConverter($data, array(
                    'date' => array(
                        'DebtPayment' => array(
                            'canceled_date',
                        ),
                    )
                ));

                $value = $this->DebtPayment->DebtPaymentDetail->getMerge($value, $id);
                $date_payment = Common::hashEmptyField($value, 'DebtPayment.date_payment');
                $cogs_id = Common::hashEmptyField($value, 'DebtPayment.cogs_id');

                if(!empty($data['DebtPayment']['canceled_date'])){
                    $data['DebtPayment']['canceled_date'] = Common::hashEmptyField($data, 'DebtPayment.canceled_date');
                    $data['DebtPayment']['is_canceled'] = 1;

                    $this->DebtPayment->id = $id;
                    $this->DebtPayment->set($data);

                    if($this->DebtPayment->save()){
                        $debt_no = Common::hashEmptyField($value, 'DebtPayment.nodoc');
                        $coa_id = Common::hashEmptyField($value, 'DebtPayment.coa_id');

                        if( !empty($value['DebtPaymentDetail']) ) {
                            foreach ($value['DebtPaymentDetail'] as $key => $detail) {
                                $debt_id = Common::hashEmptyField($detail, 'DebtPaymentDetail.debt_id');
                                $debt_detail_id = Common::hashEmptyField($detail, 'DebtPaymentDetail.debt_detail_id');
                                $total_dibayar = $this->DebtPayment->DebtPaymentDetail->getTotalPayment($debt_detail_id);
                                $flagPaid = 'none';

                                if( !empty($total_dibayar) ) {
                                    $flagPaid = 'half';
                                }
                                
                                $debt = $this->Debt->DebtDetail->findById($debt_detail_id);
                                $debt = $this->Debt->DebtDetail->getMergeList($debt, array(
                                    'contain' => array(
                                        'Debt',
                                        'ViewStaff',
                                    ),
                                ));

                                $debt_date = Common::hashEmptyField($debt, 'Debt.transaction_date');

                                $this->Debt->DebtDetail->set('paid_status', $flagPaid);
                                $this->Debt->DebtDetail->id = $debt_detail_id;
                                
                                if( !$this->Debt->DebtDetail->save() ) {
                                    $this->Log->logActivity( sprintf(__('Gagal mengubah status pembayaran Hutang Karyawan #%s'), $debt_detail_id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $debt_detail_id );
                                }
                            }
                        }

                        if( !empty($value['DebtPayment']['total_payment']) ) {
                            $titleJournal = __('pembayaran biaya Hutang Karyawan');
                            $titleJournal = sprintf(__('<i>Pembatalan</i> %s'), Common::hashEmptyField($value, 'DebtPayment.description', $titleJournal));
                            $totalPayment = Common::hashEmptyField($value, 'DebtPayment.total_payment');

                            $coaDebt = $this->User->Coa->CoaSettingDetail->getMerge(array(), 'Debt', 'CoaSettingDetail.label');
                            $debt_coa_id = !empty($coaDebt['CoaSettingDetail']['coa_id'])?$coaDebt['CoaSettingDetail']['coa_id']:false;

                            $this->User->Journal->setJournal($totalPayment, array(
                                'debit' => $debt_coa_id,
                                'credit' => $coa_id,
                            ), array(
                                'cogs_id' => $cogs_id,
                                'date' => $date_payment,
                                'document_id' => $id,
                                'title' => $titleJournal,
                                'document_no' => $debt_no,
                                'type' => 'debt_payment_void',
                            ));
                        }

                        $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
                        $msg = array(
                            'msg' => sprintf(__('Berhasil membatalkan pembayaran Hutang Karyawan #%s'), $noref),
                            'type' => 'success'
                        );
                        $this->MkCommon->setCustomFlash( $msg['msg'], $msg['type']);  
                        $this->Log->logActivity( sprintf(__('Berhasil membatalkan pembayaran Hutang Karyawan #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id ); 
                    }else{
                        $this->Log->logActivity( sprintf(__('Gagal membatalkan pembayaran Hutang Karyawan #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id ); 
                    }
                }else{
                    $msg = array(
                        'msg' => __('Harap masukkan tanggal pembatalan pembayaran.'),
                        'type' => 'error'
                    );
                }
            }

            $this->set('value', $value);
        }else{
            $msg = array(
                'msg' => __('Data tidak ditemukan'),
                'type' => 'error'
            );
        }

        $modelName = 'DebtPayment';
        $canceled_date = !empty($this->request->data['DebtPayment']['canceled_date']) ? $this->request->data['DebtPayment']['canceled_date'] : false;
        $this->set(compact(
            'msg', 'is_ajax', 'action_type',
            'canceled_date', 'modelName'
        ));
        $this->render('/Elements/blocks/common/form_delete');
    }

    function bypass_get_debt(){
        $this->loadModel('DebtDetail');

        $title = __('List Hutang');
        $params = $this->params->params;
        $named = Common::hashEmptyField($params, 'named');
        $payment_id = Common::hashEmptyField($named, 'payment_id');
        $dateFrom = Common::hashEmptyField($params, 'named.DateFrom');
        $dateTo = Common::hashEmptyField($params, 'named.DateTo');

        $params = $this->MkCommon->_callRefineParams($params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));
        $options =  $this->Debt->DebtDetail->_callRefineParams($params, array(
            'contain' => array(
                'Debt',
            ),
            'order' => array(
                'Debt.transaction_date' => 'ASC',
                'Debt.id' => 'ASC',
            ),
            'limit' => Configure::read('__Site.config_pagination'),
        ));

        $options =  $this->Debt->getData('paginate', $options, array(
            'transaction_status' => 'posting',
        ));
        $options =  $this->Debt->DebtDetail->getData('paginate', $options, array(
            'status' => 'unpaid',
        ));
        $this->paginate = $options;
        $values = $this->paginate('DebtDetail');

        if( !empty($values) ) {
            foreach ($values as $key => $value) {
                $document_id = Common::hashEmptyField($value, 'DebtDetail.id');

                $value['DebtDetail']['last_paid'] = $this->Debt->DebtPaymentDetail->getTotalPayment($document_id, $payment_id);
                $value = $this->Debt->DebtDetail->getMergeList($value, array(
                    'contain' => array(
                        'ViewStaff',
                    ),
                ));

                $values[$key] = $value;
            }
        }

        $this->layout = false;

        $data_action = 'browse-check-docs';
        $this->set(compact(
            'data_action', 'title', 'values',
            'payment_id'
        ));
    }

    public function reports() {
        $params = $this->MkCommon->_callRefineParams($this->params);

        $dataReport = $this->RmReport->_callDataDebt_reports($params, 30, 0, true);
        $values = Common::hashEmptyField($dataReport, 'data');

        $this->RmReport->_callBeforeView($params, __('Kartu Hutang'));
        $this->MkCommon->_layout_file(array(
            'select',
        ));
        $this->set(array(
            'values' => $values,
            'active_menu' => 'debt_reports',
        ));
    }

    // public function debt_card( $id = NULL ) {
    //     $params = $this->params->params;
    //     $params = $this->MkCommon->_callRefineParams($params);
    //     $id = Common::hashEmptyField($params, 'named.id');

    //     if( !empty($id) ) {
    //         $dataReport = $this->RmReport->_callDataDebt_card($params, 30, 0, true);
    //         $values = Common::hashEmptyField($dataReport, 'data');

    //         $this->RmReport->_callBeforeView($params, __('Kartu Hutang'));
    //         $this->MkCommon->_layout_file(array(
    //             'select',
    //         ));
    //         $this->set(array(
    //             'values' => $values,
    //             'active_menu' => 'debt_reports',
    //         ));
    //     } else {
    //         $this->MkCommon->setCustomFlash(__('Karyawan tidak ditemukan'), 'error');  
    //         $this->redirect(array(
    //             'action' => 'index'
    //         ));
    //     }
    // }

    public function debt_card( $data_action = NULL ) {
        $dateFrom = date('Y-m-d', strtotime('-1 Month'));
        $dateTo = date('Y-m-d');

        $params = $this->params->params;
        $id = Common::hashEmptyField($params, 'named.id');
        $type = Common::hashEmptyField($params, 'named.type');
        $dateFrom = Common::hashEmptyField($params, 'named.DateFrom', $dateFrom);
        $dateTo = Common::hashEmptyField($params, 'named.DateTo', $dateTo);

        $params = $this->MkCommon->_callRefineParams($params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));

        $dateFrom = Common::hashEmptyField($params, 'named.DateFrom');

        if( !empty($id) ) {
            $this->loadModel('ViewStaff');
            $this->loadModel('ViewDebtCard');

            $staff = $this->ViewStaff->getMerge(array(), $id, $type);

            $options =  $this->ViewDebtCard->_callRefineParams($params, array(
                'conditions' => array(
                    'ViewDebtCard.employe_id' => $id,
                    'ViewDebtCard.type' => $type,
                ),
                'order' => array(
                    'ViewDebtCard.transaction_date' => 'ASC',
                    'ViewDebtCard.debt_id' => 'ASC',
                ),
            ));
            $values = $this->ViewDebtCard->getData('all', $options);

            $this->ViewDebtCard->virtualFields['begining_balance_credit'] = 'SUM(ViewDebtCard.credit)';
            $this->ViewDebtCard->virtualFields['begining_balance_debit'] = 'SUM(ViewDebtCard.debit)';

            $summaryBalance = $this->ViewDebtCard->getData('first', array(
                'conditions' => array(
                    'ViewDebtCard.employe_id' => $id,
                    'ViewDebtCard.type' => $type,
                    'DATE_FORMAT(ViewDebtCard.transaction_date, \'%Y-%m-%d\') <' => $dateFrom,
                ),
                'group' => array(
                    'ViewDebtCard.employe_id',
                    'ViewDebtCard.type',
                ),
            ));

            $balance_credit = Common::hashEmptyField($summaryBalance, 'ViewDebtCard.begining_balance_credit', 0);
            $balance_debit = Common::hashEmptyField($summaryBalance, 'ViewDebtCard.begining_balance_debit', 0);
            $beginingBalance = $balance_credit- $balance_debit;

            $this->set(compact(
                'values', 'staff', 'beginingBalance'
            ));

            $dateFrom = $this->MkCommon->filterEmptyField($params, 'named', 'DateFrom');
            $dateTo = $this->MkCommon->filterEmptyField($params, 'named', 'DateTo');
            $module_title = __('Kartu Hutang');

            if( !empty($dateFrom) && !empty($dateTo) ) {
                $module_title .= sprintf(' Periode %s', $this->MkCommon->getCombineDate($dateFrom, $dateTo));
            }

            $this->set(array(
                'active_menu' => 'debt_reports',
                'values' => $values,
                'module_title' => $module_title,
                'data_action' => $data_action,
                'beginingBalance' => $beginingBalance,
                'id' => $id,
                'type' => $type,
            ));

            if($data_action == 'pdf'){
                $this->layout = 'pdf';
            }else if($data_action == 'excel'){
                $this->layout = 'ajax';
            }
        } else {
            $this->MkCommon->redirectReferer(__('Karyawan tidak ditemukan'), 'error', array(
                'action' => 'reports',
            ));
        }
    }

    public function import( $download = false ) {
        if(!empty($download)){
            $link_url = FULL_BASE_URL . '/files/debt.xls';
            $this->redirect($link_url);
            exit;
        } else {
            App::import('Vendor', 'excelreader'.DS.'excel_reader2');

            $this->set('module_title', __('Import Saldo Hutang'));
            $this->set('sub_module_title', __('Import Excel'));

            if(!empty($this->request->data)) { 
                $targetdir = $this->MkCommon->_import_excel( $this->request->data );

                if( !empty($targetdir) ) {
                    $xls_files = glob( $targetdir );

                    if(empty($xls_files)) {
                        $this->MkCommon->setCustomFlash(__('Tidak terdapat file excel atau berekstensi .xls pada file zip Anda. Silahkan periksa kembali.'), 'error');
                        $this->redirect(array(
                            'action'=>'import'
                        ));
                    } else {
                        $uploadedXls = $this->MkCommon->addToFiles('xls', $xls_files[0]);
                        $uploaded_file = $uploadedXls['xls'];
                        $file = explode(".", $uploaded_file['name']);
                        $extension = array_pop($file);
                        
                        if($extension == 'xls') {
                            $dataimport = new Spreadsheet_Excel_Reader();
                            $dataimport->setUTFEncoder('iconv');
                            $dataimport->setOutputEncoding('UTF-8');
                            $dataimport->read($uploaded_file['tmp_name']);
                            
                            if(!empty($dataimport)) {
                                $this->loadModel('Debt');

                                $data = $dataimport;
                                $row_submitted = 0;
                                $successfull_row = 0;
                                $failed_row = 0;
                                $error_message = '';
                                $dataHeader = array(
                                    'import' => true,
                                    'transaction_status' => 'posting',
                                );
                                // $dataSave = $dataHeader;
                                $grandtotal = 0;

                                for ($x=2;$x<=count($data->sheets[0]["cells"]); $x++) {
                                    $datavar = array();
                                    $flag = true;
                                    $i = 1;
                                    $tarifNotFound = false;

                                    while ($flag) {
                                        if( !empty($data->sheets[0]["cells"][1][$i]) ) {
                                            $variable = $this->MkCommon->toSlug($data->sheets[0]["cells"][1][$i], '_');
                                            $thedata = !empty($data->sheets[0]["cells"][$x][$i])?$data->sheets[0]["cells"][$x][$i]:NULL;
                                            $$variable = $thedata;
                                            $datavar[] = $thedata;
                                        } else {
                                            $flag = false;
                                        }
                                        $i++;
                                    }

                                    if(array_filter($datavar)) {
                                        $cabang = !empty($cabang)?$cabang:false;
                                        $id_supir = !empty($id_supir)?$id_supir:false;
                                        $keterangan = !empty($keterangan)?$keterangan:false;

                                        $tanggal = !empty($tanggal)?$tanggal:false;
                                        $tanggal = Common::formatDate($tanggal, 'Y-m-d');

                                        $per_tgl = !empty($per_tgl)?$per_tgl:false;
                                        $per_tgl = Common::formatDate($per_tgl, 'Y-m-d');

                                        $saldo_hutang = !empty($saldo_hutang)?Common::_callPriceConverter($saldo_hutang):0;
                                        $saldo_hutang = str_replace(array('*'), array(''), $saldo_hutang);
                                        $saldo_hutang = trim($saldo_hutang);


                                        $branch = $this->GroupBranch->Branch->getData('first', array(
                                            'conditions' => array(
                                                'Branch.code' => $cabang,
                                            ),
                                        ), array(
                                            'include_city' => false,
                                        ));
                                        $driver = $this->GroupBranch->Branch->Driver->getData('first', array(
                                            'conditions' => array(
                                                'Driver.no_id' => $id_supir,
                                            ),
                                        ), array(
                                            'branch' => false,
                                        ));

                                        $branch_id = Common::hashEmptyField($branch, 'Branch.id');
                                        $driver_id = Common::hashEmptyField($driver, 'Driver.id');
                                        $slug = __('%s-%s', $branch_id, $tanggal);

                                        $dataDetail = array(
                                            'employe_id' => $driver_id,
                                            'type' => 'Supir',
                                            'note' => $keterangan,
                                            'total' => $saldo_hutang,
                                            'per_tgl' => $per_tgl,
                                        );
                                        $dataCheck = array(
                                            'Debt' => $dataHeader,
                                            'DebtDetail' => array(
                                                $dataDetail,
                                            ),
                                        );

                                        $dataSave[$slug]['DebtDetail'][] = $dataDetail;

                                        if( empty($dataSave[$slug]['Debt']) ) {
                                            $dataSave[$slug]['Debt'] = $dataHeader;
                                            $dataSave[$slug]['Debt']['transaction_date'] = $tanggal;
                                            $dataSave[$slug]['Debt']['branch_id'] = $branch_id;
                                        }

                                        if( !empty($dataSave[$slug]['Debt']['total']) ) {
                                            $dataSave[$slug]['Debt']['total'] += $saldo_hutang;
                                        } else {
                                            $dataSave[$slug]['Debt']['total'] = $saldo_hutang;
                                        }

                                        $grandtotal += $saldo_hutang;
                                        $resultSave = $this->Debt->saveAll($dataCheck, array(
                                            'validate' => 'only',
                                        ));

                                        if( !empty($resultSave) ) {
                                            $successfull_row++;
                                        } else {
                                            $msgSave = $this->MkCommon->_callMsgValidationErrors($this->Debt->validationErrors, 'string');
                                            $error_message .= sprintf(__('Gagal pada baris ke %s : %s'), $row_submitted+2, $msgSave) . '<br>';
                                            $failed_row++;
                                        }

                                        $row_submitted++;
                                    }
                                }
                            }
                        }
                    }

                    if(!empty($error_message)) {
                        $this->MkCommon->setCustomFlash(__($error_message), 'error');
                    } else if( !empty($successfull_row) ) {
                        if( !empty($dataSave) ) {
                            $dataSave = array_values($dataSave);
                            $this->Debt->saveAll($dataSave, array(
                                'deep' => true,
                            ));
                        }

                        $message_import1 = sprintf(__('Import Berhasil: (%s baris), dari total (%s baris)'), $successfull_row, $row_submitted);
                        $this->MkCommon->setCustomFlash(__($message_import1), 'success');
                    }
                    $this->redirect(array('action'=>'import'));
                } else {
                    $this->MkCommon->setCustomFlash(__('Maaf, terjadi kesalahan. Silahkan coba lagi, atau hubungi Admin kami.'), 'error');
                    $this->redirect(array(
                        'action'=>'import'
                    ));
                }
            }
        }
    }
}