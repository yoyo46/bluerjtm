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
        $this->set('title_for_layout', __('ERP RJTM | Kas/Bank'));
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
        $data = $this->RjPurchase->_callBeforeSaveQuotation($data);
        $result = $this->SupplierQuotation->doSave($data);
        $this->MkCommon->setProcessParams($result, array(
            'controller' => 'purchases',
            'action' => 'supplier_quotations',
            'admin' => false,
        ));
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
}