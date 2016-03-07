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
        $this->set('sub_module_title', 'Supplier Quotation');
        
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

        $this->set('active_menu', 'Supplier Quotation');
        $this->set(compact(
            'values', 'vendors'
        ));
    }

    function supplier_quotation_add(){
        $this->set('sub_module_title', __('Tambah Supplier Quotation'));

        $data = $this->request->data;

        if( !empty($data) ) {
            $data = $this->RjPurchase->_callBeforeSaveQuotation($data);

            $nodoc = $this->MkCommon->filterEmptyField($data, 'SupplierQuotation', 'nodoc');
            $grandtotal = $this->MkCommon->filterEmptyField($data, 'SupplierQuotation', 'grandtotal', 0);
            $userApprovals = $this->User->Employe->EmployePosition->Approval->_callNeedApproval(2, $grandtotal);

            $result = $this->SupplierQuotation->doSave($data);
            $document_id = $this->MkCommon->filterEmptyField($result, 'id');

            if( !empty($document_id) ) {
                $this->MkCommon->_saveNotification(array(
                    'action' => __('Supplier Quotation'),
                    'name' => sprintf(__('Supplier Quotation dengan No Dokumen %s memerlukan ijin Approval'), $nodoc),
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

        $vendors = $this->SupplierQuotation->Vendor->getData('list');
        $this->set('active_menu', 'Supplier Quotation');
        $this->set(compact(
            'vendors'
        ));
    }

    public function supplier_quotation_edit( $id = false ) {
        $this->set('sub_module_title', __('Edit Supplier Quotation'));

        $value = $this->SupplierQuotation->getData('first', array(
            'conditions' => array(
                'SupplierQuotation.id' => $id,
            ),
        ));

        if( !empty($value) ) {
            $value = $this->SupplierQuotation->SupplierQuotationDetail->getMerge($value, $id);
            $is_po = $this->MkCommon->filterEmptyField($value, 'SupplierQuotation', 'is_po');

            if( empty($is_po) ) {
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

            $vendors = $this->SupplierQuotation->Vendor->getData('list');
            $this->set('active_menu', 'Supplier Quotation');
            $this->set(compact(
                'vendors', 'value'
            ));
            $this->render('supplier_quotation_add');
        } else {
            $this->MkCommon->setCustomFlash(__('Quotation tidak ditemukan.'), 'error');
        }
    }

    public function supplier_quotation_toggle( $id ) {
        $result = $this->SupplierQuotation->doDelete( $id );
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
        $this->paginate = $this->PurchaseOrder->getData('paginate', $options);
        $values = $this->paginate('PurchaseOrder');
        $values = $this->PurchaseOrder->Vendor->getMerge($values, false, 'PurchaseOrder');

        $vendors = $this->PurchaseOrder->Vendor->getData('list');

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
        ));

        if( !empty($value) ) {
            $value = $this->PurchaseOrder->PurchaseOrderDetail->getMerge($value, $id);

            $data = $this->request->data;
            $data = $this->RjPurchase->_callBeforeSavePO($data);
            $result = $this->PurchaseOrder->doSave($data, $value, $id);
            $this->MkCommon->setProcessParams($result, array(
                'controller' => 'purchases',
                'action' => 'purchase_orders',
                'admin' => false,
            ));
            $this->request->data = $this->RjPurchase->_callBeforeRenderPO($this->request->data);

            $vendors = $this->PurchaseOrder->Vendor->getData('list');
            $this->set('active_menu', 'Purchase Order');
            $this->set(compact(
                'vendors', 'value'
            ));
            $this->render('purchase_order_add');
        } else {
            $this->MkCommon->setCustomFlash(__('PO tidak ditemukan.'), 'error');
        }
    }

    public function purchase_order_toggle( $id ) {
        $result = $this->PurchaseOrder->doDelete( $id );
        $this->MkCommon->setProcessParams($result);
    }

    function supplier_quotation_approval($id = false){
        $this->loadModel('SupplierQuotation');
        $this->set('sub_module_title', __('Supplier Quotation Approval'));

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
                                    'action' => __('Supplier Quotation'),
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
            $this->MkCommon->setCustomFlash(__('Supplier Quotation tidak ditemukan.'), 'error');
            $this->redirect($this->referer());
        }
    }
}