<?php
App::uses('AppController', 'Controller');
class PurchasesController extends AppController {
	public $uses = array(
        'Product'
    );
    // public $components = array(
    //     'RjPurchase'
    // );

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

        $result = $this->Product->SupplierQuotationDetail->SupplierQuotation->doSave($this->request->data);
        $this->MkCommon->setProcessParams($result, array(
            'controller' => 'purchases',
            'action' => 'supplier_quotations',
            'admin' => false,
        ));

        $vendors = $this->Product->SupplierQuotationDetail->SupplierQuotation->Vendor->getData('list');
        $this->set('active_menu', 'Supplier Quotation');
        $this->set(compact(
            'vendors'
        ));
    }
}