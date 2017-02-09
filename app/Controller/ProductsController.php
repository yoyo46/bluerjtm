<?php
App::uses('AppController', 'Controller');
class ProductsController extends AppController {
	public $uses = array(
        'Product',
    );

    public $components = array(
        'RjProduct'
    );

    function beforeFilter() {
        parent::beforeFilter();
        $this->set('title_for_layout', __('ERP RJTM | Barang'));
        $this->set('module_title', __('Barang'));
    }

    function search( $index = 'index', $add_param1 = null, $add_param2 = null, $add_param3 = null ){
        $refine = array();
        if(!empty($this->request->data)) {
            $data = $this->request->data;
            $refine = $this->RjProduct->processRefine($this->request->data);
            $params = $this->RjProduct->generateSearchURL($refine);
            $params = $this->MkCommon->getRefineGroupBranch($params, $data);
            $result = $this->MkCommon->processFilter($data);
            
            $params = array_merge($params, $result);
            $params['action'] = $index;

            if( $add_param1 != null ) {
                $params[] = $add_param1;
            }
            if( $add_param2 != null ) {
                $params[] = $add_param2;
            }
            if( $add_param3 != null ) {
                $params[] = $add_param3;
            }

            $this->redirect($params);
        }
        $this->redirect('/');
    }

	public function categories() {
        $this->loadModel('ProductCategory');
		$this->set('active_menu', 'product_categories');
		$this->set('sub_module_title', __('Grup Barang'));
        $conditions = array();

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];
            $parent_id = false;

            if( !empty($refine['parent']) ) {
                $value = urldecode($refine['parent']);
                $parent_id = $this->ProductCategory->getData('list', array(
                    'conditions' => array(
                        'ProductCategory.name LIKE' => '%'.$value.'%',
                    ),
                    'fields' => array(
                        'ProductCategory.id', 'ProductCategory.id',
                    ),
                ));
            }

            $conditions = $this->MkCommon->_callRefineGenerating($conditions, $refine, array(
                array(
                    'modelName' => 'ProductCategory',
                    'fieldName' => 'name',
                    'conditionName' => 'ProductCategory.name',
                    'operator' => 'LIKE',
                ),
                array(
                    'modelName' => 'ProductCategory',
                    'fieldName' => 'parent',
                    'conditionName' => 'ProductCategory.parent_id',
                    'keyword' => $parent_id,
                ),
            ));
        }

        $this->paginate = $this->ProductCategory->getData('paginate', array(
            'conditions' => $conditions,
        ));
        $productCategories = $this->paginate('ProductCategory');

        if( !empty($productCategories) ) {
            foreach ($productCategories as $key => $value) {
                $parent_id = $this->MkCommon->filterEmptyField($value, 'ProductCategory', 'parent_id');
                
                $value = $this->ProductCategory->getMerge($value, $parent_id, 'Parent');
                $productCategories[$key] = $value;
            }
        }

        $this->set('productCategories', $productCategories);
	}

    function category_add(){
        $this->loadModel('ProductCategory');
        $this->set('sub_module_title', __('Tambah Grup Barang'));
        $this->doProductCategory();
    }

    function category_edit($id){
        $this->loadModel('ProductCategory');
        $this->set('sub_module_title', 'Ubah Grup Barang');
        $productCategory = $this->ProductCategory->getData('first', array(
            'conditions' => array(
                'ProductCategory.id' => $id
            ),
        ));

        if(!empty($productCategory)){
            $this->doProductCategory($id, $productCategory);
        }else{
            $this->MkCommon->setCustomFlash(__('Grup Barang tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'products',
                'action' => 'categories'
            ));
        }
    }

    function doProductCategory($id = false, $data_local = false){
        if(!empty($this->request->data)){
            $data = $this->request->data;

            if($id && $data_local){
                $this->ProductCategory->id = $id;
                $msg = 'merubah';
            }else{
                $this->ProductCategory->create();
                $msg = 'menambah';
            }

            $this->ProductCategory->set($data);

            if( $this->ProductCategory->validates($data) ){
                if($this->ProductCategory->save($data)){
                    $id = $this->ProductCategory->id;

                    $this->params['old_data'] = $data_local;
                    $this->params['data'] = $data;

                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Grup Barang'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s Grup Barang #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
                    $this->redirect(array(
                        'controller' => 'products',
                        'action' => 'categories'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Grup Barang'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s Grup Barang'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id ); 
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Grup Barang'), $msg), 'error');
            }
        } else if( !empty($data_local) ){
            $this->request->data = $data_local;
        }

        $categories = $this->ProductCategory->getListParent( $id );

        $this->set(compact(
            'data_local', 'categories'
        ));
        $this->set('active_menu', 'product_categories');
        $this->render('category_form');
    }

    function category_toggle($id){
        $this->loadModel('ProductCategory');
        $locale = $this->ProductCategory->getData('first', array(
            'conditions' => array(
                'ProductCategory.id' => $id
            )
        ));

        if($locale){
            $value = true;
            if($locale['ProductCategory']['status']){
                $value = false;
            }

            $this->ProductCategory->id = $id;
            $this->ProductCategory->set('status', $value);

            if($this->ProductCategory->save()){
                $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
                $this->Log->logActivity( sprintf(__('Sukses merubah status Grup Barang ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id ); 
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal merubah status Grup Barang ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id ); 
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Grup Barang tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    function units(){
        $this->loadModel('ProductUnit');
        
        $params = $this->MkCommon->_callRefineParams($this->params);
        $options =  $this->ProductUnit->_callRefineParams($params);

        $this->paginate = $this->ProductUnit->getData('paginate', $options);
        $values = $this->paginate('ProductUnit');

        $this->set('active_menu', 'product_units');
        $this->set('sub_module_title', __('Satuan Barang'));
        $this->set('values', $values);
    }

    function unit_add(){
        $this->loadModel('ProductUnit');
        $this->set('sub_module_title', __('Tambah Satuan Barang'));
        $this->doProductUnit();
    }

    function unit_edit($id){
        $this->loadModel('ProductUnit');
        $this->set('sub_module_title', 'Ubah Satuan Barang');
        $value = $this->ProductUnit->getData('first', array(
            'conditions' => array(
                'ProductUnit.id' => $id
            ),
        ));

        if(!empty($value)){
            $this->doProductUnit($id, $value);
        }else{
            $this->MkCommon->setCustomFlash(__('Satuan Barang tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'products',
                'action' => 'units'
            ));
        }
    }

    function doProductUnit($id = false, $data_local = false){
        if(!empty($this->request->data)){
            $data = $this->request->data;

            if($id && $data_local){
                $this->ProductUnit->id = $id;
                $msg = 'merubah';
            }else{
                $this->ProductUnit->create();
                $msg = 'menambah';
            }

            $this->ProductUnit->set($data);

            if( $this->ProductUnit->validates($data) ){
                if($this->ProductUnit->save($data)){
                    $id = $this->ProductUnit->id;

                    $this->params['old_data'] = $data_local;
                    $this->params['data'] = $data;

                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s satuan barang'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s satuan barang #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
                    $this->redirect(array(
                        'controller' => 'products',
                        'action' => 'units'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s satuan barang'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s satuan barang #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id ); 
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s satuan barang'), $msg), 'error');
            }
        } else if( !empty($data_local) ){
            $this->request->data = $data_local;
        }

        $this->set(compact(
            'data_local'
        ));
        $this->set('active_menu', 'product_units');
        $this->render('unit_form');
    }

    function unit_toggle($id){
        $this->loadModel('ProductUnit');
        $locale = $this->ProductUnit->getData('first', array(
            'conditions' => array(
                'ProductUnit.id' => $id
            )
        ));

        if($locale){
            $value = true;
            if($locale['ProductUnit']['status']){
                $value = false;
            }

            $this->ProductUnit->id = $id;
            $this->ProductUnit->set('status', $value);

            if($this->ProductUnit->save()){
                $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
                $this->Log->logActivity( sprintf(__('Sukses merubah status satuan barang ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id ); 
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal merubah status satuan barang ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id ); 
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Satuan barang tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    function index(){
        $params = $this->MkCommon->_callRefineParams($this->params);
        $options =  $this->Product->_callRefineParams($params);

        $this->paginate = $this->Product->getData('paginate', $options);
        $values = $this->paginate('Product');

        if( !empty($values) ) {
            foreach ($values as $key => &$value) {
                $id = $this->MkCommon->filterEmptyField($value, 'Product', 'id');
                $product_unit_id = $this->MkCommon->filterEmptyField($value, 'Product', 'product_unit_id');
                $product_category_id = $this->MkCommon->filterEmptyField($value, 'Product', 'product_category_id');

                $value = $this->Product->ProductUnit->getMerge($value, $product_unit_id);
                $value = $this->Product->ProductCategory->getMerge($value, $product_category_id);
                $value['Product']['product_stock_cnt'] = $this->Product->ProductStock->_callStock($id);
            }
        }

        $productCategories = $this->Product->ProductCategory->getData('list');
        $this->MkCommon->_layout_file('select');

        $this->set('active_menu', 'products');
        $this->set('sub_module_title', __('Barang'));
        $this->set(compact(
            'values', 'productCategories'
        ));
    }

    function _callGeneralProduct () {
        $productUnits = $this->Product->ProductUnit->getData('list');
        $productCategories = $this->Product->ProductCategory->getData('list');
        $truck_categories = $this->Product->TruckCategory->getData('list', array(
            'fields' => array(
                'TruckCategory.id', 'TruckCategory.name'
            )
        ));

        $this->set('active_menu', 'products');
        $this->set(compact(
            'productUnits', 'productCategories',
            'truck_categories'
        ));
        $this->render('add');
    }

    function add(){
        $this->set('sub_module_title', __('Tambah Barang'));

        $result = $this->Product->doSave($this->request->data);
        $this->MkCommon->setProcessParams($result, array(
            'controller' => 'products',
            'action' => 'index',
            'admin' => false,
        ));

        $this->_callGeneralProduct();
    }

    function edit( $id = false ){
        $this->set('sub_module_title', __('Edit Barang'));

        $value = $this->Product->getData('first', array(
            'conditions' => array(
                'Product.id' => $id,
            ),
        ));

        if( !empty($value) ) {
            $result = $this->Product->doSave($this->request->data, $value, $id);
            $this->MkCommon->setProcessParams($result, array(
                'controller' => 'products',
                'action' => 'index',
                'admin' => false,
            ));

            $this->_callGeneralProduct();
        } else {
            $this->MkCommon->setCustomFlash(__('Barang tidak ditemukan.'), 'error');
        }
    }

    public function receipts() {
        $this->loadModel('ProductReceipt');
        $this->set('sub_module_title', __('Penerimaan Barang'));
        
        $dateFrom = date('Y-m-d', strtotime('-1 Month'));
        $dateTo = date('Y-m-d');

        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));
        $options =  $this->ProductReceipt->_callRefineParams($params);
        $this->paginate = $this->ProductReceipt->getData('paginate', $options, array(
            'status' => 'all',
        ));
        $values = $this->paginate('ProductReceipt');
        $values = $this->ProductReceipt->getMergeList($values, array(
            'contain' => array(
                'Vendor' => array(
                    'elements' => array(
                        'status' => 'all',
                        'branch' => false,
                    ),
                ),
                'Employe',
                'Warehouse' => array(
                    'uses' => 'Branch',
                    'primaryKey' => 'id',
                    'foreignKey' => 'to_branch_id',
                    'type' => 'first',
                ),
            ),
        ));

        if( !empty($values) ) {
            foreach ($values as $key => $value) {
                $value = $this->RjProduct->_callGetDocReceipt($value);
                $values[$key] = $value;
            }
        }

        $this->RjProduct->_callBeforeRenderReceipts();

        $this->MkCommon->_layout_file('select');
        $this->set('active_menu', 'receipts');
        $this->set(compact(
            'values'
        ));
    }

    function receipt_add(){
        $this->set('sub_module_title', __('Penerimaan Barang'));

        $data = $this->request->data;

        if( !empty($data) ) {
            $data = $this->RjProduct->_callBeforeSaveReceipt($data);
            $result = $this->Product->ProductReceiptDetail->ProductReceipt->doSave($data);
            $this->MkCommon->setProcessParams($result, array(
                'controller' => 'products',
                'action' => 'receipts',
                'admin' => false,
            ));
        }

        $this->RjProduct->_callBeforeRenderReceipt($data);

        $this->set(array(
            'active_menu' => 'receipts',
        ));
    }

    public function receipt_edit( $id = false ) {
        $this->set('sub_module_title', __('Edit Penerimaan'));

        $value = $this->Product->ProductReceiptDetail->ProductReceipt->getData('first', array(
            'conditions' => array(
                'ProductReceipt.id' => $id,
            ),
        ), array(
            'status' => 'pending',
        ));

        if( !empty($value) ) {
            $value = $this->RjProduct->_callGetDocReceipt($value);
            $value = $this->Product->ProductReceiptDetail->getMerge($value, $id);
            $value = $this->Product->ProductReceiptDetail->ProductReceipt->DocumentAuth->getMerge($value, $id, 'po');

            $data = $this->request->data;

            if( !empty($data) ) {
                $data = $this->RjProduct->_callBeforeSaveReceipt($data, $id);
                $result = $this->Product->ProductReceiptDetail->ProductReceipt->doSave($data, $value, $id);
                $this->MkCommon->setProcessParams($result, array(
                    'controller' => 'products',
                    'action' => 'receipts',
                    'admin' => false,
                ));
            }

            $this->RjProduct->_callBeforeRenderReceipt($data, $value);

            $this->set(array(
                'value' => $value,
                'active_menu' => 'receipts',
            ));
            $this->render('receipt_add');
        } else {
            $this->MkCommon->redirectReferer(__('Penerimaan tidak ditemukan.'), 'error');
        }
    }

    public function receipt_detail( $id = false ) {
        $this->set('sub_module_title', __('Detail Penerimaan Barang'));
        $value = $this->Product->ProductReceiptDetail->ProductReceipt->getData('first', array(
            'conditions' => array(
                'ProductReceipt.id' => $id,
            ),
        ), array(
            'status' => false,
        ));

        if( !empty($value) ) {
            $value = $this->Product->ProductReceiptDetail->getMerge($value, $id);
            $value = $this->Product->ProductReceiptDetail->ProductReceipt->DocumentAuth->getMerge($value, $id, 'product_receipt');

            $user_id = $this->MkCommon->filterEmptyField($value, 'ProductReceipt', 'user_id');
            $grandtotal = $this->MkCommon->filterEmptyField($value, 'ProductReceipt', 'grandtotal');
            $nodoc = $this->MkCommon->filterEmptyField($value, 'ProductReceipt', 'nodoc');
            $document_type = $this->MkCommon->filterEmptyField($value, 'ProductReceipt', 'document_type');

            switch ($document_type) {
                case 'spk':
                    $documentModel = 'Spk';
                    break;
                
                case 'wht':
                    $documentModel = 'ProductExpenditure';
                    break;

                case 'production':
                    $documentModel = 'Spk';
                    break;
                
                default:
                    $documentModel = 'PurchaseOrder';
                    break;
            }

            $value = $this->Product->ProductReceiptDetail->ProductReceipt->getMergeList($value, array(
                'contain' => array(
                    'Document' => array(
                        'uses' => $documentModel,
                        'elements' => array(
                            'branch' => false,
                        ),
                    ),
                ),
            ));

            $details = $this->MkCommon->filterEmptyField($value, 'ProductReceiptDetail');
            $value = $this->User->getMerge($value, $user_id);
            $user_position_id = $this->MkCommon->filterEmptyField($value, 'Employe', 'employe_position_id');

            $user_otorisasi_approvals = $this->User->Employe->EmployePosition->Approval->getUserOtorisasiApproval('product_receipt', $user_position_id, $grandtotal, $id);
            $show_approval = $this->User->Employe->EmployePosition->Approval->_callAuthApproval($user_otorisasi_approvals);
            $data = $this->request->data;

            if( !empty($show_approval) && !empty($data) ) {
                $data = $this->MkCommon->_callBeforeSaveApproval($data, array(
                    'user_id' => $user_id,
                    'nodoc' => $nodoc,
                    'user_position_id' => $user_position_id,
                    'document_id' => $id,
                    'document_type' => 'product_receipt',
                    'document_url' => array(
                        'controller' => 'products',
                        'action' => 'receipt_detail',
                        $id,
                        'admin' => false,
                    ),
                    'document_revised_url' => array(
                        'controller' => 'products',
                        'action' => 'receipt_edit',
                        $id,
                        'admin' => false,
                    ),
                ));
                $result = $this->Product->ProductReceiptDetail->ProductReceipt->doApproval($data, $id);
                $this->MkCommon->setProcessParams($result, array(
                    'controller' => 'products',
                    'action' => 'receipt_detail',
                    $id,
                    'admin' => false,
                ));
            }

            $this->RjProduct->_callBeforeRenderReceipt(false, $value);

            $this->set('active_menu', 'receipts');
            $this->set('view', 'detail');
            $this->set(compact(
                'vendors', 'value',
                'user_otorisasi_approvals', 'show_approval'
            ));
            $this->render('receipt_add');
        } else {
            $this->MkCommon->redirectReferer(__('Penerimaan barang tidak ditemukan.'), 'error');
        }
    }

    public function receipt_toggle( $id, $type = null ) {
        $result = $this->Product->ProductReceiptDetail->ProductReceipt->doDelete( $id, $type );
        $this->MkCommon->setProcessParams($result);
    }

    function bypass_receipt_serial_numbers ( $id = false ) {
        $data = $this->request->data;
        $session_id = $this->MkCommon->filterEmptyField($data, 'ProductReceipt', 'session_id');
        $number = $this->MkCommon->filterEmptyField($this->params, 'named', 'picker', 0);
        $view = $this->MkCommon->filterEmptyField($this->params, 'named', 'view', 0);
        $value = $this->Product->getData('first', array(
            'conditions' => array(
                'Product.id' => $id,
            ),
        ));

        if( !empty($session_id) && !empty($value) ) {
            $serial_numbers = $this->MkCommon->filterEmptyField($data, 'ProductReceiptDetailSerialNumber', 'serial_number');

            if( !empty($serial_numbers) ) {
                $serial_numbers = $this->RjProduct->_callBeforeSaveSerialNumber($serial_numbers, $id, $session_id);
                $result = $this->Product->ProductReceiptDetailSerialNumber->doSave($serial_numbers, $id, $session_id);

                $this->MkCommon->setProcessParams($result, false, array(
                    'ajaxFlash' => false,
                    'ajaxRedirect' => false,
                ));
            } else {
                $values = $this->Product->ProductReceiptDetailSerialNumber->getData('all', array(
                    'conditions' => array(
                        'ProductReceiptDetailSerialNumber.product_id' => $id,
                        'ProductReceiptDetailSerialNumber.session_id' => $session_id,
                    ),
                ));
                $this->RjProduct->_callBeforeViewSerialNumber($values, $session_id);
            }

            $this->set('_flash', false);
            $this->set(compact(
                'number', 'value', 'id',
                'result', 'view'
            ));
        } else {
            if( empty($value) ) {
                $this->set('message', __('Barang tidak ditemukan. Mohon cek kembali barang yang ingin Anda proses'));
            }

            $this->render('/Ajax/page_not_found');
        }
    }

    function receipt_choose_documents ( $type = false ) {
        switch ($type) {
            case 'spk':
                $vendors = $this->Product->SpkProduct->Spk->_callVendors('unreceipt_draft');
                break;
            case 'wht':
                $vendors = $this->Product->ProductExpenditureDetail->ProductExpenditure->_callVendors('untransfer_draft');
                break;
            case 'production':
                $vendors = $this->Product->SpkProduct->Spk->_callVendors('unreceipt_draft', false, 'production');
                break;
            default:
                $vendors = $this->Product->PurchaseOrderDetail->PurchaseOrder->_callVendors('unreceipt_draft');
                break;
        }

        $this->set(compact(
            'vendors', 'type'
        ));
        $this->render('/Elements/blocks/products/receipts/forms/receipt_choose_document');
    }

    function receipt_documents ( $type = false, $vendor_id = false ) {
        $vendor_id = $this->MkCommon->filterEmptyField($this->params, 'named', 'vendor_id', $vendor_id);
        $receipt_id = $this->MkCommon->filterEmptyField($this->params, 'named', 'receipt_id');
        $params = $this->MkCommon->_callRefineParams($this->params);
        $render = __('receipt_documents_%s', $type);

        switch ($type) {
            case 'spk':
                $settings = $this->MkCommon->_callSettingGeneral('Product', 'spk_internal_policy', false);
                $spk_internal_policy = $this->MkCommon->filterEmptyField($settings, 'Product', 'spk_internal_policy');

                if( $spk_internal_policy == 'receipt' ) {
                    $values = $this->RjProduct->_callSpkInternals($params, $vendor_id);
                }
                break;
            case 'wht':
                $values = $this->RjProduct->_callWHts($params);
                $render = 'receipt_documents_wht';
                break;
            case 'production':
                $values = $this->RjProduct->_callProductions($params);
                $render = 'receipt_documents_spk';
                break;
            default:
                $values = $this->RjProduct->_callPurchaseOrders($params, $vendor_id);
                $render = 'receipt_documents';
                break;
        }

        $this->MkCommon->_layout_file('select');
        $this->set(compact(
            'values', 'type',
            'receipt_id', 'vendor_id'
        ));
        $this->render($render);
    }

    // function receipt_pick_document () {
    //     $data = $this->request->data;
    //     $type = $this->MkCommon->filterEmptyField($data, 'ProductReceipt', 'document_type');

    //     switch ($type) {
    //         case 'spk':
    //             $value = $this->RjProduct->_callSpkInternal($data);
    //             break;
    //         case 'wht':
    //             $value = $this->RjProduct->_callWht($data);
    //             break;
    //         default:
    //             $value = $this->RjProduct->_callPurchaseOrder($data);
    //             break;
    //     }

    //     $this->set(compact(
    //         'value', 'type'
    //     ));
    // }

    function receipt_document_products ( $transaction_id = false, $nodoc = null, $document_type = 'spk' ) {
        $data = $this->request->data;
        $nodoc = $this->MkCommon->filterEmptyField($data, 'ProductReceipt', 'document_number', $nodoc);
        $document_type = $this->MkCommon->filterEmptyField($data, 'ProductReceipt', 'document_type', $document_type);
        $values = false;

        $params = $this->MkCommon->_callRefineParams($this->params);
        $productCategories = $this->Product->ProductCategory->getData('list');

        $this->set(array(
            'nodoc' => $nodoc,
            'transaction_id' => $transaction_id,
            'document_type' => $document_type,
            'productCategories' => $productCategories,
        ));

        switch ($document_type) {
            case 'spk':
                $value = $this->Product->SpkProduct->Spk->getData('first', array(
                    'conditions' => array(
                        'Spk.nodoc' => $nodoc,
                    ),
                ), array(
                    'status' => 'unreceipt_draft',
                ));
                $document_id = $this->MkCommon->filterEmptyField($value, 'Spk', 'id');

                $options =  $this->Product->SpkProduct->_callRefineParams($params, array(
                    'conditions' => array(
                        'SpkProduct.spk_id' => $document_id,
                    ),
                    'limit' => 10,
                ));
                $this->paginate = $this->Product->SpkProduct->getData('paginate', $options);
                $values = $this->paginate('SpkProduct');
                $this->RjProduct->_callBeforeRenderReceiptSpkProducts($values, $transaction_id);
                $this->render('receipt_spk_products');
                break;
            case 'wht':
                $value = $this->Product->ProductExpenditureDetail->ProductExpenditure->getData('first', array(
                    'conditions' => array(
                        'ProductExpenditure.nodoc' => $nodoc,
                    ),
                ), array(
                    'status' => 'untransfer_draft',
                    'branch' => false,
                ));
                $document_id = $this->MkCommon->filterEmptyField($value, 'ProductExpenditure', 'id');

                $options =  $this->Product->ProductExpenditureDetail->_callRefineParams($params, array(
                    'conditions' => array(
                        'ProductExpenditureDetail.product_expenditure_id' => $document_id,
                    ),
                    'limit' => 10,
                ));
                $this->paginate = $this->Product->ProductExpenditureDetail->getData('paginate', $options);
                $values = $this->paginate('ProductExpenditureDetail');
                $this->RjProduct->_callBeforeRenderReceiptSpkProducts($values, $transaction_id);
                $this->render('receipt_spk_products');
                break;
            case 'production':
                $value = $this->Product->SpkProduction->Spk->getData('first', array(
                    'conditions' => array(
                        'Spk.nodoc' => $nodoc,
                    ),
                ), array(
                    'status' => 'unreceipt_draft',
                    'type' => 'production',
                ));
                $document_id = $this->MkCommon->filterEmptyField($value, 'Spk', 'id');

                $options =  $this->Product->SpkProduction->_callRefineParams($params, array(
                    'conditions' => array(
                        'SpkProduction.spk_id' => $document_id,
                    ),
                    'limit' => 10,
                ));
                $this->paginate = $this->Product->SpkProduction->getData('paginate', $options);
                $values = $this->paginate('SpkProduction');
                $this->RjProduct->_callBeforeRenderReceiptSpkProducts($values, $transaction_id);

                $this->set('modelName', 'SpkProduction');
                $this->render('receipt_spk_products');
                break;
            default:
                $value = $this->Product->PurchaseOrderDetail->PurchaseOrder->getData('first', array(
                    'conditions' => array(
                        'PurchaseOrder.nodoc' => $nodoc,
                    ),
                ), array(
                    'status' => 'unreceipt_draft',
                ));
                $document_id = $this->MkCommon->filterEmptyField($value, 'PurchaseOrder', 'id');

                $options =  $this->Product->PurchaseOrderDetail->_callRefineParams($params, array(
                    'conditions' => array(
                        'PurchaseOrderDetail.purchase_order_id' => $document_id,
                    ),
                    'limit' => 10,
                ));
                $this->paginate = $this->Product->PurchaseOrderDetail->getData('paginate', $options);
                $values = $this->paginate('PurchaseOrderDetail');
                $this->RjProduct->_callBeforeRenderReceiptPODetails($values, $transaction_id);
                $this->render('receipt_po_products');
                break;
        }
    }

    public function expenditures() {
        $this->loadModel('ProductExpenditure');
        $this->set('sub_module_title', __('Pengeluaran Barang'));
        
        $dateFrom = date('Y-m-d', strtotime('-1 Month'));
        $dateTo = date('Y-m-d');

        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));
        $options =  $this->ProductExpenditure->_callRefineParams($params);
        $this->paginate = $this->ProductExpenditure->getData('paginate', $options, array(
            'status' => 'all',
        ));
        $values = $this->paginate('ProductExpenditure');
        $values = $this->ProductExpenditure->getMergeList($values, array(
            'contain' => array(
                'Staff',
                'Spk' => array(
                    'Truck',
                ),
            ),
        ));
        $this->RjProduct->_callBeforeRenderExpenditures();

        $this->MkCommon->_layout_file('select');
        $this->set('active_menu', 'expenditures');
        $this->set(compact(
            'values'
        ));
    }

    function expenditure_add(){
        $this->set('sub_module_title', __('Pengeluaran Barang'));

        $data = $this->request->data;

        if( !empty($data) ) {
            $data = $this->RjProduct->_callBeforeSaveExpenditure($data);
            $result = $this->Product->ProductExpenditureDetail->ProductExpenditure->doSave($data);
            $this->MkCommon->setProcessParams($result, array(
                'controller' => 'products',
                'action' => 'expenditures',
                'admin' => false,
            ));
        }

        $this->RjProduct->_callBeforeRenderExpenditure($data);

        $this->set(array(
            'active_menu' => 'expenditures',
        ));
    }

    public function expenditure_edit( $id = false ) {
        $this->set('sub_module_title', __('Edit Pengeluaran'));

        $value = $this->Product->ProductExpenditureDetail->ProductExpenditure->getData('first', array(
            'conditions' => array(
                'ProductExpenditure.id' => $id,
            ),
        ), array(
            'status' => 'pending',
        ));

        if( !empty($value) ) {
            $value = $this->Product->ProductExpenditureDetail->ProductExpenditure->getMergeList($value, array(
                'contain' => array(
                    'ProductExpenditureDetail' => array(
                        'Product',
                        'ProductExpenditureDetailSerialNumber',
                    ),
                    'Spk',
                ),
            ));
            // debug($value);die();

            $data = $this->request->data;

            if( !empty($data) ) {
                $data = $this->RjProduct->_callBeforeSaveExpenditure($data, $id);
                $result = $this->Product->ProductExpenditureDetail->ProductExpenditure->doSave($data, $value, $id);
                $this->MkCommon->setProcessParams($result, array(
                    'controller' => 'products',
                    'action' => 'expenditures',
                    'admin' => false,
                ));
            }

            $this->RjProduct->_callBeforeRenderExpenditure($data, $value);

            $this->set(array(
                'value' => $value,
                'active_menu' => 'expenditures',
            ));
            $this->render('expenditure_add');
        } else {
            $this->MkCommon->redirectReferer(__('Pengeluaran tidak ditemukan.'), 'error');
        }
    }

    public function expenditure_detail( $id = false ) {
        $this->set('sub_module_title', __('Detail Pengeluaran'));

        $value = $this->Product->ProductExpenditureDetail->ProductExpenditure->getData('first', array(
            'conditions' => array(
                'ProductExpenditure.id' => $id,
            ),
        ), array(
            'status' => 'all',
        ));

        if( !empty($value) ) {
            $value = $this->Product->ProductExpenditureDetail->ProductExpenditure->getMergeList($value, array(
                'contain' => array(
                    'ProductExpenditureDetail' => array(
                        'Product',
                        'ProductExpenditureDetailSerialNumber',
                    ),
                    'Spk',
                ),
            ));
            $this->RjProduct->_callBeforeRenderExpenditure(false, $value);

            $this->set(array(
                'value' => $value,
                'active_menu' => 'expenditures',
                'view' => true,
            ));
            $this->render('expenditure_add');
        } else {
            $this->MkCommon->redirectReferer(__('Pengeluaran tidak ditemukan.'), 'error');
        }
    }

    public function expenditure_toggle( $id ) {
        $result = $this->Product->ProductExpenditureDetail->ProductExpenditure->doDelete( $id );
        $this->MkCommon->setProcessParams($result);
    }

    function expenditure_documents () {
        $this->loadModel('Spk');
        $params = $this->MkCommon->_callRefineParams($this->params);
        $options =  $this->Spk->_callRefineParams($params, array(
            'limit' => 10,
        ));
        $this->paginate = $this->Spk->getData('paginate', $options, array(
            'status' => 'pending-out',
        ));
        $values = $this->paginate('Spk');
        $values = $this->Spk->getMergeList($values, array(
            'contain' => array(
                'Truck',
            ),
        ));

        $this->MkCommon->_layout_file('select');
        $this->set(compact(
            'values'
        ));
    }

    function spk_products ( $transaction_id = false, $nodoc = false ) {
        $this->loadModel('SpkProduct');

        $nodoc = urldecode($nodoc);
        $value = $this->SpkProduct->Spk->getData('first', array(
            'conditions' => array(
                'Spk.nodoc' => $nodoc,
            ),
        ), array(
            'status' => 'pending-out',
        ));
        $spk_id = $this->MkCommon->filterEmptyField($value, 'Spk', 'id');

        $params = $this->MkCommon->_callRefineParams($this->params);
        $options =  $this->SpkProduct->_callRefineParams($params, array(
            'conditions' => array(
                'SpkProduct.spk_id' => $spk_id,
            ),
            'limit' => 10,
        ));
        $this->paginate = $this->SpkProduct->getData('paginate', $options);
        $values = $this->paginate('SpkProduct');

        $productCategories = $this->Product->ProductCategory->getData('list');
        $this->RjProduct->_callBeforeRenderSpkProducts($values, $transaction_id);
        $this->set(compact(
            'nodoc', 'productCategories'
        ));
    }

    function stocks ( $id = false ) {
        $params = $this->MkCommon->_callRefineParams($this->params);
        $options =  $this->Product->ProductStock->_callRefineParams($params, array(
            'conditions' => array(
                'ProductStock.product_id' => $id,
            ),
            'limit' => 10,
        ));
        $this->paginate = $this->Product->ProductStock->getData('paginate', $options, array(
            'status' => 'in_stock',
            'sort' => 'fifo',
        ));
        $values = $this->paginate('ProductStock');
        $values = $this->Product->ProductStock->getMergeList($values, array(
            'contain' => array(
                'Product',
            ),
        ));

        $productCategories = $this->Product->ProductCategory->getData('list');
        $this->set(compact(
            'values', 'productCategories', 'id'
        ));
    }

    public function current_stock_reports() {
        $this->Product->unBindModel(array(
            'hasMany' => array(
                'ProductStock'
            )
        ));
        $this->Product->bindModel(array(
            'hasOne' => array(
                'ProductStock' => array(
                    'className' => 'ProductStock',
                    'foreignKey' => 'product_id',
                ),
            )
        ), false);
        $this->Product->ProductStock->virtualFields['total_qty'] = 'SUM(ProductStock.qty - ProductStock.qty_use)';
        $this->Product->ProductStock->virtualFields['avg_price'] = 'SUM(ProductStock.price) / SUM(ProductStock.qty - ProductStock.qty_use)';

        $options = array(
            'contain' => array(
                'ProductStock',
            ),
            'group' => array(
                'Product.id',
            ),
        );

        $params = $this->MkCommon->_callRefineParams($this->params);
        $options =  $this->Product->_callRefineParams($params, $options);
        $options = $this->MkCommon->getConditionGroupBranch( $params, 'ProductStock', $options );

        $this->paginate = $this->Product->getData('paginate', array_merge($options, array(
            'limit' => Configure::read('__Site.config_pagination'),
        )), array(
            'branch' => false,
        ));
        $values = $this->paginate('Product');

        $values = $this->Product->getMergeList($values, array(
            'contain' => array(
                'ProductUnit',
            ),
        ));

        $this->RjProduct->_callBeforeViewCurrentStockReports($params);
        $this->MkCommon->_layout_file(array(
            'select',
            'freeze',
        ));
        $this->set(compact(
            'values'
        ));
    }

    public function stock_cards() {
        $this->Product->unBindModel(array(
            'hasMany' => array(
                'ProductStock'
            )
        ));
        $this->Product->bindModel(array(
            'hasOne' => array(
                'ProductStock' => array(
                    'className' => 'ProductStock',
                    'foreignKey' => 'product_id',
                ),
            )
        ), false);
        $this->Product->ProductStock->virtualFields['total_qty'] = 'SUM(ProductStock.qty - ProductStock.qty_use)';
        $this->Product->ProductStock->virtualFields['avg_price'] = 'SUM(ProductStock.price) / SUM(ProductStock.qty - ProductStock.qty_use)';

        $options = array(
            'contain' => array(
                'ProductStock',
            ),
            'group' => array(
                'Product.id',
            ),
        );

        $params = $this->MkCommon->_callRefineParams($this->params);
        $options =  $this->Product->_callRefineParams($params, $options);
        $options = $this->MkCommon->getConditionGroupBranch( $params, 'ProductStock', $options );

        $this->paginate = $this->Product->getData('paginate', array_merge($options, array(
            'limit' => Configure::read('__Site.config_pagination'),
        )), array(
            'branch' => false,
        ));
        $values = $this->paginate('Product');

        $values = $this->Product->getMergeList($values, array(
            'contain' => array(
                'ProductUnit',
            ),
        ));

        $this->RjProduct->_callBeforeViewCurrentStockReports($params);
        $this->MkCommon->_layout_file(array(
            'select',
            'freeze',
        ));
        $this->set(compact(
            'values'
        ));
    }
}