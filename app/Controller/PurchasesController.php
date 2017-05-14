<?php
App::uses('AppController', 'Controller');
class PurchasesController extends AppController {
	public $uses = array(
        'SupplierQuotation', 'PurchaseOrder',
    );
    public $components = array(
        'RjPurchase'
    );
    public $helpers = array(
        'Purchase'
    );

    function beforeFilter() {
        parent::beforeFilter();
        $this->set('title_for_layout', __('ERP RJTM | Purchase Order'));
        $this->set('module_title', __('Purchase Order'));
    }

    function search( $index = 'index' ){
        if(!empty($this->request->data)) {
            $data = $this->request->data;
            $params = array(
                'controller' => 'purchases',
                'action' => $index,
                'false' => false,
            );

            $result = $this->MkCommon->processFilter($data);
            $params = array_merge($params, $result);

            $this->redirect($params);
        } else {
            $this->redirect('/');
        }
    }

    function supplier_quotations(){
        $this->loadModel('SupplierQuotation');
        $this->set('sub_module_title', 'Penawaran Supplier');
        
        $dateFrom = date('Y-m-d', strtotime('-1 Month'));
        $dateTo = date('Y-m-d');

        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));
        $options =  $this->SupplierQuotation->_callRefineParams($params);
        $this->paginate = $this->SupplierQuotation->getData('paginate', $options);
        $values = $this->paginate('SupplierQuotation');
        $values = $this->SupplierQuotation->Vendor->getMerge($values);

        $vendors = $this->SupplierQuotation->Vendor->getData('list');

        $this->set('active_menu', 'Penawaran Supplier');
        $this->set(compact(
            'values', 'vendors'
        ));
    }

    function supplier_quotation_add(){
        $this->set('sub_module_title', __('Tambah Penawaran Supplier'));

        $data = $this->request->data;

        if( !empty($data) ) {
            $data = $this->RjPurchase->_callBeforeSaveQuotation($data);

            $nodoc = $this->MkCommon->filterEmptyField($data, 'SupplierQuotation', 'nodoc');
            $grandtotal = $this->MkCommon->filterEmptyField($data, 'SupplierQuotation', 'grandtotal', 0);
            $userApprovals = $this->User->Employe->EmployePosition->Approval->_callNeedApproval('sq', $grandtotal);

            $result = $this->SupplierQuotation->doSave($data);
            $document_id = $this->MkCommon->filterEmptyField($result, 'id');

            if( !empty($document_id) ) {
                $this->MkCommon->_saveNotification(array(
                    'action' => __('Penawaran Supplier'),
                    'name' => sprintf(__('Penawaran Supplier dengan No Dokumen %s memerlukan ijin Approval'), $nodoc),
                    'user_id' => $userApprovals,
                    'document_id' => $document_id, 
                    'url' => array(
                        'controller' => 'purchases',
                        'action' => 'supplier_quotation_approval',
                        $document_id,
                        'admin' => false,
                    ),
                ));
            }
        
            $this->MkCommon->setProcessParams($result, array(
                'controller' => 'purchases',
                'action' => 'supplier_quotations',
                'admin' => false,
            ));
        }

        $this->request->data = $this->RjPurchase->_callBeforeRenderQuotation($this->request->data);
    }

    public function supplier_quotation_edit( $id = false ) {
        $this->set('sub_module_title', __('Edit Penawaran Supplier'));

        $value = $this->SupplierQuotation->getData('first', array(
            'conditions' => array(
                'SupplierQuotation.id' => $id,
            ),
        ), array(
            'status' => 'pending',
        ));

        if( !empty($value) ) {
            $value = $this->SupplierQuotation->SupplierQuotationDetail->getMerge($value, $id);
            $value = $this->SupplierQuotation->DocumentAuth->getMerge($value, $id, 'sq');
            $transaction_status = $this->MkCommon->filterEmptyField($value, 'SupplierQuotation', 'transaction_status');

            if( in_array($transaction_status, array( 'unposting', 'revised' )) ) {
                $data = $this->request->data;
                $data = $this->RjPurchase->_callBeforeSaveQuotation($data);
            } else {
                $data = false;
            }
            
            $result = $this->SupplierQuotation->doSave($data, $value, $id);
            $this->MkCommon->setProcessParams($result, array(
                'controller' => 'purchases',
                'action' => 'supplier_quotations',
                'admin' => false,
            ));
            $this->request->data = $this->RjPurchase->_callBeforeRenderQuotation($this->request->data);

            $this->set(compact(
                'value'
            ));
            $this->render('supplier_quotation_add');
        } else {
            $this->MkCommon->redirectReferer(__('Quotation tidak ditemukan.'), 'error');
        }
    }

    public function supplier_quotation_detail( $id = false ) {
        $this->set('sub_module_title', __('Detail Penawaran Supplier'));

        $value = $this->SupplierQuotation->getData('first', array(
            'conditions' => array(
                'SupplierQuotation.id' => $id,
            ),
        ));

        if( !empty($value) ) {
            $value = $this->SupplierQuotation->SupplierQuotationDetail->getMerge($value, $id);
            $value = $this->SupplierQuotation->DocumentAuth->getMerge($value, $id, 'sq');

            $user_id = $this->MkCommon->filterEmptyField($value, 'SupplierQuotation', 'user_id');
            $grandtotal = $this->MkCommon->filterEmptyField($value, 'SupplierQuotation', 'grandtotal');
            $nodoc = $this->MkCommon->filterEmptyField($value, 'SupplierQuotation', 'nodoc');

            $value = $this->User->getMerge($value, $user_id);
            $user_position_id = $this->MkCommon->filterEmptyField($value, 'Employe', 'employe_position_id');

            $user_otorisasi_approvals = $this->User->Employe->EmployePosition->Approval->getUserOtorisasiApproval('sq', $user_position_id, $grandtotal, $id);
            $show_approval = $this->User->Employe->EmployePosition->Approval->_callAuthApproval($user_otorisasi_approvals);
            $data = $this->request->data;

            if( !empty($show_approval) && !empty($data) ) {
                $data = $this->MkCommon->_callBeforeSaveApproval($data, array(
                    'user_id' => $user_id,
                    'nodoc' => $nodoc,
                    'user_position_id' => $user_position_id,
                    'document_id' => $id,
                    'document_type' => 'sq',
                    'document_url' => array(
                        'controller' => 'purchases',
                        'action' => 'supplier_quotation_detail',
                        $id,
                        'admin' => false,
                    ),
                    'document_revised_url' => array(
                        'controller' => 'purchases',
                        'action' => 'supplier_quotation_edit',
                        $id,
                        'admin' => false,
                    ),
                ));
                $result = $this->SupplierQuotation->doApproval($data, $id);
                $this->MkCommon->setProcessParams($result, array(
                    'controller' => 'purchases',
                    'action' => 'supplier_quotation_detail',
                    $id,
                    'admin' => false,
                ));
            }

            $this->request->data = $this->RjPurchase->_callBeforeRenderQuotation($value);

            $this->set('view', 'detail');
            $this->set(compact(
                'vendors', 'value',
                'user_otorisasi_approvals', 'show_approval'
            ));
            $this->render('supplier_quotation_add');
        } else {
            $this->MkCommon->redirectReferer(__('Quotation tidak ditemukan.'), 'error');
        }
    }

    public function supplier_quotation_toggle( $id ) {
        $result = $this->SupplierQuotation->doChangeStatus( $id, 'void', __('membatalkan') );
        $this->MkCommon->setProcessParams($result);
    }

    public function purchase_orders() {
        $this->set('sub_module_title', 'Purchase Order');
        
        $dateFrom = date('Y-m-d', strtotime('-1 Month'));
        $dateTo = date('Y-m-d');

        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));
        $options =  $this->PurchaseOrder->_callRefineParams($params);
        $this->paginate = $this->PurchaseOrder->getData('paginate', $options, array(
            'status' => 'all',
        ));
        $values = $this->paginate('PurchaseOrder');
        $values = $this->PurchaseOrder->Vendor->getMerge($values, false, 'PurchaseOrder');

        $vendors = $this->PurchaseOrder->Vendor->getData('list');

        $this->MkCommon->_layout_file('select');
        $this->set('active_menu', 'Purchase Order');
        $this->set(compact(
            'values', 'vendors'
        ));
    }

    function purchase_order_add(){
        $this->set('sub_module_title', __('Tambah PO'));

        $data = $this->request->data;
        $data = $this->RjPurchase->_callBeforeSavePO($data);
        $result = $this->PurchaseOrder->doSave($data);
        $this->MkCommon->setProcessParams($result, array(
            'controller' => 'purchases',
            'action' => 'purchase_orders',
            'admin' => false,
        ));
        $this->request->data = $this->RjPurchase->_callBeforeRenderPO($this->request->data);

        $vendors = $this->PurchaseOrder->Vendor->getData('list');
        $this->MkCommon->_layout_file('select');
        $this->set('active_menu', 'Purchase Order');
        $this->set(compact(
            'vendors'
        ));
    }

    public function purchase_order_edit( $id = false ) {
        $this->set('sub_module_title', __('Edit PO'));

        $value = $this->PurchaseOrder->getData('first', array(
            'conditions' => array(
                'PurchaseOrder.id' => $id,
            ),
        ), array(
            'status' => 'pending',
        ));

        if( !empty($value) ) {
            $value = $this->PurchaseOrder->PurchaseOrderDetail->getMerge($value, $id);
            $value = $this->PurchaseOrder->DocumentAuth->getMerge($value, $id, 'po');

            $data = $this->request->data;
            $data = $this->RjPurchase->_callBeforeSavePO($data, $id);
            $result = $this->PurchaseOrder->doSave($data, $value, $id);
            $this->MkCommon->setProcessParams($result, array(
                'controller' => 'purchases',
                'action' => 'purchase_orders',
                'admin' => false,
            ));
            $this->request->data = $this->RjPurchase->_callBeforeRenderPO($this->request->data);

            $vendors = $this->PurchaseOrder->Vendor->getData('list');
            $this->MkCommon->_layout_file('select');
            $this->set('active_menu', 'Purchase Order');
            $this->set(compact(
                'vendors', 'value', 'id'
            ));
            $this->render('purchase_order_add');
        } else {
            $this->MkCommon->redirectReferer(__('PO tidak ditemukan.'), 'error');
        }
    }

    public function purchase_order_detail( $id = false ) {
        $this->set('sub_module_title', __('Detail PO'));
        $value = $this->PurchaseOrder->getData('first', array(
            'conditions' => array(
                'PurchaseOrder.id' => $id,
            ),
        ));

        if( !empty($value) ) {
            $value = $this->PurchaseOrder->PurchaseOrderDetail->getMerge($value, $id);
            $value = $this->PurchaseOrder->DocumentAuth->getMerge($value, $id, 'po');

            $user_id = $this->MkCommon->filterEmptyField($value, 'PurchaseOrder', 'user_id');
            $grandtotal = $this->MkCommon->filterEmptyField($value, 'PurchaseOrder', 'grandtotal');
            $nodoc = $this->MkCommon->filterEmptyField($value, 'PurchaseOrder', 'nodoc');

            $value = $this->User->getMerge($value, $user_id);
            $user_position_id = $this->MkCommon->filterEmptyField($value, 'Employe', 'employe_position_id');

            $user_otorisasi_approvals = $this->User->Employe->EmployePosition->Approval->getUserOtorisasiApproval('po', $user_position_id, $grandtotal, $id);
            $show_approval = $this->User->Employe->EmployePosition->Approval->_callAuthApproval($user_otorisasi_approvals);
            $data = $this->request->data;

            if( !empty($show_approval) && !empty($data) ) {
                $data = $this->MkCommon->_callBeforeSaveApproval($data, array(
                    'user_id' => $user_id,
                    'nodoc' => $nodoc,
                    'user_position_id' => $user_position_id,
                    'document_id' => $id,
                    'document_type' => 'po',
                    'document_url' => array(
                        'controller' => 'purchases',
                        'action' => 'purchase_order_detail',
                        $id,
                        'admin' => false,
                    ),
                    'document_revised_url' => array(
                        'controller' => 'purchases',
                        'action' => 'purchase_order_edit',
                        $id,
                        'admin' => false,
                    ),
                ));
                $result = $this->PurchaseOrder->doApproval($data, $id);
                $this->MkCommon->setProcessParams($result, array(
                    'controller' => 'purchases',
                    'action' => 'purchase_order_detail',
                    $id,
                    'admin' => false,
                ));
            }

            $this->request->data = $this->RjPurchase->_callBeforeRenderPO($value);

            $vendors = $this->PurchaseOrder->Vendor->getData('list');
            $this->set('active_menu', 'Purchase Order');
            $this->set('view', 'detail');
            $this->set(compact(
                'vendors', 'value',
                'user_otorisasi_approvals', 'show_approval',
                'id'
            ));
            $this->render('purchase_order_add');
        } else {
            $this->MkCommon->redirectReferer(__('PO tidak ditemukan.'), 'error');
        }
    }

    public function purchase_order_toggle( $id, $type = null ) {
        $result = $this->PurchaseOrder->doDelete( $id, $type );
        $this->MkCommon->setProcessParams($result);
    }

    function supplier_quotation_approval($id = false){
        $this->loadModel('SupplierQuotation');
        $this->set('sub_module_title', __('Penawaran Supplier Approval'));

        $conditions = array(
            'SupplierQuotation.id' => $id,
        );
        $conditionToApprove = $this->User->Employe->EmployePosition->Approval->_callGetDataToApprove('supplier_quotation');
        $conditions = array_merge($conditions, $conditionToApprove);

        $value = $this->SupplierQuotation->getData('first', array(
            'conditions' => $conditions,
        ), array(
            'branch' => false,
        ));

        if( !empty($value) ) {
            $vendor_id = $this->MkCommon->filterEmptyField($value, 'SupplierQuotation', 'vendor_id');
            $user_id = $this->MkCommon->filterEmptyField($value, 'SupplierQuotation', 'user_id');
            $date = $this->MkCommon->filterEmptyField($value, 'SupplierQuotation', 'transaction_date');
            $nodoc = $this->MkCommon->filterEmptyField($value, 'SupplierQuotation', 'nodoc');

            $value = $this->SupplierQuotation->Vendor->getMerge($value, $vendor_id);
            $value = $this->SupplierQuotation->SupplierQuotationDetail->getMerge($value, $id);
            $result_approval = $this->MkCommon->_callAllowApproval( $value, $user_id, $id, 'supplier-quotation' );
            $show_approval = $this->MkCommon->filterEmptyField($result_approval, 'show_approval');

            if( !empty($this->request->data) ){
                if( !empty($show_approval) ){
                    $data = $this->request->data;
                    $status_document = $this->MkCommon->filterEmptyField($data, 'DocumentAuth', 'status_document');

                    $result_process = $this->MkCommon->_callProcessApproval($result_approval, $value, $id, 'supplier_quotation');
                    $data_arr = $this->MkCommon->filterEmptyField($result_process, 'data');
                    $msgRevision = $this->MkCommon->filterEmptyField($result_process, 'msg_revision');

                    if( !empty($data_arr) ) {
                        $this->SupplierQuotation->id = $id;
                        $this->SupplierQuotation->set($data_arr);

                        if( $this->SupplierQuotation->save() ) {
                            if( !empty($msgRevision) ) {
                                $this->MkCommon->_saveNotification(array(
                                    'action' => __('Penawaran Supplier'),
                                    'name' => $msgRevision,
                                    'user_id' => $user_id,
                                    'document_id' => $id, 
                                    'url' => array(
                                        'controller' => 'purchases',
                                        'action' => 'supplier_quotation_edit',
                                        $id,
                                        'admin' => false,
                                    ),
                                ));
                            }
                        }
                    }

                    $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
                    $this->MkCommon->setCustomFlash(sprintf(__('Berhasil melakukan Approval SQ #%s'), $noref), 'success');
                    $this->Log->logActivity( sprintf(__('Berhasil melakukan %s SQ #%s'), $status_document, $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
                }else{
                    $this->MkCommon->setCustomFlash('Anda tidak mempunyai hak untuk mengakses kontent tersebut.', 'error');
                }

                $this->redirect($this->referer());
            }

            $this->set('active_menu', 'supplier_quotations');
            $this->set(compact(
                'result_approval', 'value'
            ));
        } else {
            $this->MkCommon->setCustomFlash(__('Penawaran Supplier tidak ditemukan.'), 'error');
            $this->redirect($this->referer());
        }
    }

    public function payments() {
        $this->loadModel('PurchaseOrderPayment');
        $this->set('sub_module_title', __('Pembayaran PO'));
        
        $dateFrom = date('Y-m-d', strtotime('-1 Month'));
        $dateTo = date('Y-m-d');

        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));
        $options =  $this->PurchaseOrderPayment->_callRefineParams($params);
        $this->paginate = $this->PurchaseOrderPayment->getData('paginate', $options, array(
            'status' => 'void-active',
        ));
        $values = $this->paginate('PurchaseOrderPayment');
        $values = $this->PurchaseOrder->Vendor->getMerge($values, false, 'PurchaseOrderPayment');

        $vendors = $this->PurchaseOrder->Vendor->getData('list');

        $this->MkCommon->_layout_file('select');
        $this->set('active_menu', 'Pembayaran PO');
        $this->set(compact(
            'values', 'vendors'
        ));
    }

    function payment_add(){
        $this->set('sub_module_title', __('Pembayaran PO'));

        $data = $this->request->data;
        $dataSave = $this->RjPurchase->_callBeforeSavePayment($data);
        $result = $this->PurchaseOrder->PurchaseOrderPaymentDetail->PurchaseOrderPayment->doSave($dataSave);
        $this->MkCommon->setProcessParams($result, array(
            'controller' => 'purchases',
            'action' => 'payments',
            'admin' => false,
        ));
        $this->request->data = $this->RjPurchase->_callBeforeRenderPayment($this->request->data);

        $this->set('active_menu', 'Purchase Order');
    }

    public function payment_edit( $id = false ) {
        $this->set('sub_module_title', __('Edit Pembayaran PO'));

        $value = $this->PurchaseOrder->PurchaseOrderPaymentDetail->PurchaseOrderPayment->getData('first', array(
            'conditions' => array(
                'PurchaseOrderPayment.id' => $id,
            ),
        ), array(
            'status' => 'unposting',
        ));

        if( !empty($value) ) {
            $value = $this->PurchaseOrder->PurchaseOrderPaymentDetail->getMerge($value, $id);
            $purchase_order_id = Set::extract('/PurchaseOrderPaymentDetail/PurchaseOrderPaymentDetail/purchase_order_id', $value);

            $data = $this->request->data;
            $dataSave = $this->RjPurchase->_callBeforeSavePayment($data, $id);
            $result = $this->PurchaseOrder->PurchaseOrderPaymentDetail->PurchaseOrderPayment->doSave($dataSave, $value, $id);
            $this->MkCommon->setProcessParams($result, array(
                'controller' => 'purchases',
                'action' => 'payments',
                'admin' => false,
            ));
            $this->request->data = $this->RjPurchase->_callBeforeRenderPayment($this->request->data, $purchase_order_id);

            $this->set('active_menu', 'Pembayaran PO');
            $this->set(compact(
                'value'
            ));
            $this->render('payment_add');
        } else {
            $this->MkCommon->redirectReferer(__('Pembayaran PO tidak ditemukan.'), 'error');
        }
    }

    public function payment_detail( $id ) {
        $this->set('sub_module_title', __('Detail Pembayaran PO'));

        $value = $this->PurchaseOrder->PurchaseOrderPaymentDetail->PurchaseOrderPayment->getData('first', array(
            'conditions' => array(
                'PurchaseOrderPayment.id' => $id,
            ),
        ), array(
            'status' => 'void-active',
        ));

        if( !empty($value) ) {
            $value = $this->PurchaseOrder->PurchaseOrderPaymentDetail->PurchaseOrderPayment->PurchaseOrderPaymentDetail->getMerge($value, $id);
            $purchase_order_id = Set::extract('/PurchaseOrderPaymentDetail/PurchaseOrderPaymentDetail/purchase_order_id', $value);
            $this->request->data = $this->RjPurchase->_callBeforeRenderPayment($value, $purchase_order_id);

            $this->set('view', 'detail');
            $this->set('active_menu', 'Purchase Order');
            $this->set(compact(
                'value'
            ));
            $this->render('payment_add');
        } else {
            $this->MkCommon->redirectReferer(__('Pembayaran PO tidak ditemukan.'), 'error');
        }
    }

    public function payment_toggle( $id ) {
        $is_ajax = $this->RequestHandler->isAjax();
        $action_type = 'purchase_payments';
        $msg = array(
            'msg' => '',
            'type' => 'error'
        );
        $value = $this->PurchaseOrder->PurchaseOrderPaymentDetail->PurchaseOrderPayment->getData('first', array(
            'conditions' => array(
                'PurchaseOrderPayment.id' => $id,
            ),
        ));
        $data = $this->request->data;

        if( !empty($value) ) {
            if(!empty($data)){
                $result = $this->PurchaseOrder->PurchaseOrderPaymentDetail->PurchaseOrderPayment->doDelete( $id, $value, $data );
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

        $modelName = 'PurchaseOrderPayment';
        $canceled_date = $this->MkCommon->filterEmptyField($data, 'LeasingPayment', 'canceled_date');
        $this->set(compact(
            'msg', 'is_ajax', 'action_type',
            'canceled_date', 'modelName', 'value'
        ));
        $this->render('/Elements/blocks/common/form_delete');
    }

    function po_documents () {
        $payment_id = $this->MkCommon->filterEmptyField($this->params, 'named', 'payment_id');
        $vendor_id = $this->MkCommon->filterEmptyField($this->params, 'named', 'vendor_id');
        
        $params = $this->MkCommon->_callRefineParams($this->params);
        $options =  $this->PurchaseOrder->_callRefineParams($params, array(
            'conditions' => array(
                'PurchaseOrder.vendor_id' => $vendor_id,
            ),
            'limit' => 10,
        ));

        $this->paginate = $this->PurchaseOrder->getData('paginate', $options, array(
            'status' => 'unpaid',
        ));
        $values = $this->paginate('PurchaseOrder');

        if( !empty($values) ) {
            foreach ($values as $key => $value) {
                $id = $this->MkCommon->filterEmptyField($value, 'PurchaseOrder', 'id');
                $vendor_id = $this->MkCommon->filterEmptyField($value, 'PurchaseOrder', 'vendor_id');

                $grandtotal = $this->PurchaseOrder->PurchaseOrderDetail->_callGrandtotal($id);

                $paid = $this->PurchaseOrder->PurchaseOrderPaymentDetail->_callPaidPO($id, $payment_id);
                $total_remain = $grandtotal - $paid;
                $value['PurchaseOrder']['total_paid'] = ($paid <= 0)?0:$paid;
                $value['PurchaseOrder']['total_remain'] = ($total_remain <= 0)?0:$total_remain;
                $value['PurchaseOrder']['grandtotal'] = $grandtotal;

                $value = $this->PurchaseOrder->Vendor->getMerge($value, $vendor_id);
                $values[$key] = $value;
            }
        }

        $this->set('module_title', __('Purchase Order'));
        $this->set(compact(
            'values', 'payment_id', 'vendor_id'
        ));
    }

    public function reports( $data_action = false ) {
        $dateFrom = date('Y-m-01');
        $dateTo = date('Y-m-t');

        $options = array(
            'order'=> array(
                'PurchaseOrder.transaction_date' => 'DESC',
                'PurchaseOrder.id' => 'DESC',
            ),
        );

        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));
        $options =  $this->PurchaseOrder->_callRefineParams($params, $options);
        $options = $this->MkCommon->getConditionGroupBranch( $params, 'PurchaseOrder', $options );

        if( !empty($data_action) ){
            $values = $this->PurchaseOrder->getData('all', $options, array(
                'status' => false,
                'branch' => false,
            ));
        } else {
            $this->paginate = $this->PurchaseOrder->getData('paginate', array_merge($options, array(
                'limit' => Configure::read('__Site.config_pagination'),
            )), array(
                'status' => false,
                'branch' => false,
            ));
            $values = $this->paginate('PurchaseOrder');
        }

        if( !empty($values) ) {
            $this->PurchaseOrder->PurchaseOrderDetail->virtualFields['total_qty'] = 'SUM(PurchaseOrderDetail.qty)';
            $this->PurchaseOrder->PurchaseOrderDetail->virtualFields['total_price'] = 'SUM(PurchaseOrderDetail.price)';
            $this->PurchaseOrder->PurchaseOrderDetail->virtualFields['total_disc'] = 'SUM(PurchaseOrderDetail.disc)';
            $this->PurchaseOrder->PurchaseOrderDetail->virtualFields['total_ppn'] = 'SUM(PurchaseOrderDetail.ppn)';
            $this->PurchaseOrder->PurchaseOrderDetail->virtualFields['total_total'] = 'SUM(PurchaseOrderDetail.total)';
            
            foreach ($values as $key => &$value) {
                $value = $this->PurchaseOrder->getMergeList($value, array(
                    'contain' => array(
                        'Vendor' => array(
                            'elements' => array(
                                'branch' => false,
                                'status' => 'all',
                            ),
                        ),
                        'SupplierQuotation' => array(
                            'elements' => array(
                                'branch' => false,
                                'status' => false,
                            ),
                        ),
                        'PurchaseOrderDetail' => array(
                            'type' => 'first',
                        ),
                    ),
                ));

                $id = $this->MkCommon->filterEmptyField($value, 'PurchaseOrder', 'id');
                $details = $this->MkCommon->filterEmptyField($value, 'PurchaseOrderDetail');

                if( !empty($details) ) {
                    $total_qty = $this->MkCommon->filterEmptyField($value, 'PurchaseOrderDetail', 'total_qty');
                    $qty_retur = $this->PurchaseOrder->PurchaseOrderDetail->Product->ProductReturDetail->getTotalRetur(false, $id, 'po');
                    $total_qty = $total_qty - $qty_retur;

                    if( $total_qty < 0 ) {
                        $total_qty = 0;
                    }

                    $value['PurchaseOrderDetail']['total_qty_final'] = $total_qty;
                    $value['PurchaseOrderDetail']['qty_retur'] = $qty_retur;
                }
            }
        }

        $this->RjPurchase->_callBeforeViewReport($params);
        $this->MkCommon->_callBeforeViewReport($data_action, array(
            'layout_file' => array(
                'select',
                'freeze',
            ),
        ));
        $this->set(compact(
            'values', 'data_action'
        ));
    }
}