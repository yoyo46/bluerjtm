<?php
App::uses('AppController', 'Controller');
class ProductsController extends AppController {
	public $uses = array(
        'Product',
    );

    public $components = array(
        'RjProduct', 'RmReport'
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
        $values = $this->ProductCategory->getData('threaded', array(
            'order' => array(
                'ProductCategory.id' => 'ASC',
            )
        ));

        $this->set('active_menu', 'product_categories');
        $this->set('sub_module_title', 'Grup Barang');
        $this->set('values', $values);

  //       $this->loadModel('ProductCategory');
		// $this->set('active_menu', 'product_categories');
		// $this->set('sub_module_title', __('Grup Barang'));
  //       $conditions = array();

  //       if(!empty($this->params['named'])){
  //           $refine = $this->params['named'];
  //           $parent_id = false;

  //           if( !empty($refine['parent']) ) {
  //               $value = urldecode($refine['parent']);
  //               $parent_id = $this->ProductCategory->getData('list', array(
  //                   'conditions' => array(
  //                       'ProductCategory.name LIKE' => '%'.$value.'%',
  //                   ),
  //                   'fields' => array(
  //                       'ProductCategory.id', 'ProductCategory.id',
  //                   ),
  //               ));
  //           }

  //           $conditions = $this->MkCommon->_callRefineGenerating($conditions, $refine, array(
  //               array(
  //                   'modelName' => 'ProductCategory',
  //                   'fieldName' => 'name',
  //                   'conditionName' => 'ProductCategory.name',
  //                   'operator' => 'LIKE',
  //               ),
  //               array(
  //                   'modelName' => 'ProductCategory',
  //                   'fieldName' => 'parent',
  //                   'conditionName' => 'ProductCategory.parent_id',
  //                   'keyword' => $parent_id,
  //               ),
  //           ));
  //       }

  //       $this->paginate = $this->ProductCategory->getData('paginate', array(
  //           'conditions' => $conditions,
  //       ));
  //       $productCategories = $this->paginate('ProductCategory');

  //       if( !empty($productCategories) ) {
  //           foreach ($productCategories as $key => $value) {
  //               $parent_id = $this->MkCommon->filterEmptyField($value, 'ProductCategory', 'parent_id');
                
  //               $value = $this->ProductCategory->getMerge($value, $parent_id, 'Parent');
  //               $productCategories[$key] = $value;
  //           }
  //       }

  //       $this->set('productCategories', $productCategories);
	}

    function category_add( $parent_id = null ){
        $this->loadModel('ProductCategory');
        $this->set('sub_module_title', __('Tambah Grup Barang'));
        $this->doProductCategory( null, null, $parent_id );
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

    function doProductCategory($id = false, $data_local = false, $parent_id = null){
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
        } else if( !empty($parent_id) ) {
            $this->request->data['ProductCategory']['parent_id'] = $parent_id;
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
                $value['Product']['product_stock_cnt'] = $this->Product->ProductStock->_callStock($id);
                
                $value = $this->Product->getMergeList($value, array(
                    'contain' => array(
                        'ProductUnit',
                        'ProductCategory',
                        'ProductMinStock' => array(
                            'type' => 'first',
                            'conditions' => array(
                                'ProductMinStock.branch_id' => Configure::read('__Site.config_branch_id'),
                            ),
                            'elements' => array(
                                'branch' => false,
                            ),
                        ),
                    ),
                ));
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
            $value = $this->Product->getMergeList($value, array(
                'contain' => array(
                    'ProductMinStock' => array(
                        'type' => 'first',
                        'conditions' => array(
                            'ProductMinStock.branch_id' => Configure::read('__Site.config_branch_id'),
                        ),
                        'elements' => array(
                            'branch' => false,
                        ),
                    ),
                ),
            ));

            $result = $this->Product->doSave($this->request->data, $value, $id);
            $this->MkCommon->setProcessParams($result, array(
                'controller' => 'products',
                'action' => 'index',
                'admin' => false,
            ));

            $this->MkCommon->getLogs($this->params['controller'], array( 'add', 'edit', 'toggle' ), $id);
            $this->_callGeneralProduct();
        } else {
            $this->MkCommon->setCustomFlash(__('Barang tidak ditemukan.'), 'error');
        }
    }

    function toggle($id){
        $locale = $this->Product->getData('first', array(
            'conditions' => array(
                'Product.id' => $id
            )
        ));

        if($locale){
            $value = true;
            if($locale['Product']['status']){
                $value = false;
            }

            $this->Product->id = $id;
            $this->Product->set('status', $value);

            if($this->Product->save()){
                $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
                $this->Log->logActivity( sprintf(__('Sukses merubah status Barang ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id ); 
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal merubah status Barang ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id ); 
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Barang tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
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

        if( !empty($values) ) {
            foreach ($values as $key => $value) {
                $value = $this->ProductReceipt->getMergeList($value, array(
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
                        'ProductReceiptDetail' => array(
                            'contain' => array(
                                'ProductHistory' => array(
                                    'conditions' => array(
                                        'transaction_type' => 'product_receipt',
                                    ),
                                    'contain' => array(
                                        'ProductStock',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ));
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

        $nodoc = urldecode($nodoc);
        $nodoc = str_replace('/', '[slash]', $nodoc);

        $this->set(array(
            'nodoc' => $nodoc,
            'transaction_id' => $transaction_id,
            'document_type' => $document_type,
            'productCategories' => $productCategories,
        ));

        $nodoc = str_replace('[slash]', '/', $nodoc);

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
                $this->paginate = $this->Product->ProductExpenditureDetail->getData('paginate', $options, array(
                    'status' => 'unreceipt',
                ));
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
                'Laka',
                'Truck',
                'Driver' => array(
                    'elements' => array(
                        'branch' => false,
                    ),
                ),
            ),
        ));

        $this->MkCommon->_layout_file('select');
        $this->set(compact(
            'values'
        ));
    }

    function spk_products ( $transaction_id = false, $nodoc = false ) {
        $this->loadModel('SpkProduct');

        $tmp_nodoc = urldecode($nodoc);
        $tmp_nodoc = str_replace('[slash]', '/', $tmp_nodoc);

        $value = $this->SpkProduct->Spk->getData('first', array(
            'conditions' => array(
                'Spk.nodoc' => $tmp_nodoc,
            ),
        ), array(
            'status' => 'spk-product-pending-out',
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
        $this->RjProduct->_callBeforeRenderSpkProducts($tmp_nodoc, $values, $transaction_id);
        $this->set(compact(
            'nodoc', 'productCategories'
        ));
    }

    function stocks ( $id = false ) {
        $data = $this->request->data;

        if( !empty($data) ) {
            $data = $this->MkCommon->dataConverter($data, array(
                'date' => array(
                    'ProductExpenditure' => array(
                        'transaction_date',
                    ),
                )
            ));
        }

        $transaction_date = Common::hashEmptyField($data, 'ProductExpenditure.transaction_date');
        $params = $this->MkCommon->_callRefineParams($this->params);
        $options =  $this->Product->ProductStock->_callRefineParams($params, array(
            'conditions' => array(
                'ProductStock.product_id' => $id,
            ),
            'limit' => 10,
        ));

        if( !empty($transaction_date) ) {
            $data = $this->MkCommon->dataConverter($data, array(
                'date' => array(
                    'ProductReceipt' => array(
                        'transaction_date',
                    ),
                )
            ));
            $options['conditions']['ProductStock.transaction_date <='] = $transaction_date;
        }

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
        // $this->Product->ProductHistory->virtualFields['total_balance'] = 'SUM(CASE WHEN ProductHistory.transaction_type = \'product_receipt\' THEN ProductHistory.price*ProductHistory.qty ELSE 0 END) - SUM(CASE WHEN ProductHistory.transaction_type = \'product_expenditure\' THEN ProductHistory.price*ProductHistory.qty ELSE 0 END)';
        // $this->Product->ProductHistory->virtualFields['total_receipt'] = 'SUM(CASE WHEN ProductHistory.transaction_type = \'product_receipt\' THEN ProductHistory.price*ProductHistory.qty ELSE 0 END)';
        // $this->Product->ProductHistory->virtualFields['total_ex'] = 'SUM(CASE WHEN ProductHistory.transaction_type = \'product_expenditure\' THEN ProductHistory.price*ProductHistory.qty ELSE 0 END)';
        // $this->Product->ProductHistory->virtualFields['total_qty'] = 'SUM(CASE WHEN ProductHistory.type = \'in\' THEN ProductHistory.qty ELSE 0 END) - SUM(CASE WHEN ProductHistory.type = \'out\' THEN ProductHistory.qty ELSE 0 END)';

        $this->Product->ProductStock->virtualFields['total_qty'] = 'SUM(ProductStock.qty - ProductStock.qty_use)';
        $this->Product->ProductStock->virtualFields['total_balance'] = 'SUM(ProductStock.price*(ProductStock.qty - ProductStock.qty_use))';

        $options = array(
            'conditions' => array(
                'ProductStock.status' => 1,
            ),
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
        ));
        $this->set(array(
            'values' => $values,
            'active_menu' => 'current_stock_reports',
        ));
    }

    public function stock_cards() {
        $dateFrom = date('Y-m-01');
        $dateTo = date('Y-m-t');

        $this->Product->ProductHistory->ProductStock->bindModel(array(
            'belongsTo' => array(
                'ProductHistory' => array(
                    'className' => 'ProductHistory',
                    'foreignKey' => 'product_history_id',
                ),
            )
        ), false);

        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));
        $options =  $this->Product->ProductHistory->_callRefineParams($params, array(
            'contain' => array(
                'Product',
            ),
            'order'=> array(
                'ProductHistory.product_id' => 'ASC',
                'ProductHistory.branch_id' => 'ASC',
                'ProductHistory.transaction_date' => 'ASC',
                'ProductHistory.id' => 'ASC',
            ),
            'group'=> array(
                'ProductHistory.product_id',
            ),
            'limit' => 10,
        ));
        $options = $this->MkCommon->getConditionGroupBranch( $params, 'ProductHistory', $options );
        $options = $this->Product->ProductHistory->getData('paginate', $options, array(
            'branch' => false,
        ));
        $this->paginate = $options;
        $tmp_values = $this->paginate('ProductHistory');
        $result = array();
        $values = array();

        if( !empty($tmp_values) ) {
            foreach ($tmp_values as $key => $val) {
                $product_id = Common::hashEmptyField($val, 'ProductHistory.product_id');
                $tmp = $options;
                $tmp = Common::_callUnset($tmp, array(
                    'limit',
                    'group',
                ));

                $tmp['conditions']['ProductHistory.product_id'] = $product_id;
                $values = $this->Product->ProductHistory->getData('all', $tmp, array(
                    'branch' => false,
                ));

                foreach ($values as $key => $value) {
                    $product_history_id = Common::hashEmptyField($value, 'ProductHistory.id');
                    $product_id = Common::hashEmptyField($value, 'ProductHistory.product_id');
                    $transaction_type = Common::hashEmptyField($value, 'ProductHistory.transaction_type');
                    $transaction_id = Common::hashEmptyField($value, 'ProductHistory.transaction_id');
                    $branch_id = Common::hashEmptyField($value, 'ProductHistory.branch_id');
                    $price = Common::hashEmptyField($value, 'ProductHistory.price');

                    $value = $this->Product->getMergeList($value, array(
                        'contain' => array(
                            'ProductUnit',
                        ),
                    ));
                    $value = $this->Product->ProductHistory->getMergeList($value, array(
                        'contain' => array(
                            'Branch',
                        ),
                    ));

                    switch ($transaction_type) {
                        case 'product_receipt':
                            $modelName = 'ProductReceipt';
                            break;
                        case 'product_expenditure':
                            $modelName = 'ProductExpenditure';
                            break;
                        case 'product_expenditure_void':
                            $modelName = 'ProductExpenditure';
                            break;
                        case 'product_adjustment_min':
                            $modelName = 'ProductAdjustment';
                            break;
                        case 'product_adjustment_min_void':
                            $modelName = 'ProductAdjustment';
                            break;
                        case 'product_adjustment_plus':
                            $modelName = 'ProductAdjustment';
                            break;
                        case 'product_adjustment_plus_void':
                            $modelName = 'ProductAdjustment';
                            break;
                    }

                    if( !empty($modelName) ) {
                        $value = $this->Product->ProductHistory->getMergeList($value, array(
                            'contain' => array(
                                'DocumentDetail' => array(
                                    'uses' => $modelName.'Detail',
                                    'contain' => array(
                                        'Document' => array(
                                            'uses' => $modelName,
                                            'elements' => array(
                                                'branch' => false,
                                                'status' => false,
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                        ));
                    }

                    if( $transaction_type == 'product_receipt' ) {
                        $product_receipt_id = Common::hashEmptyField($value, 'DocumentDetail.Document.id');

                        $value['DocumentDetail']['SerialNumber'] = $this->Product->ProductHistory->ProductReceiptDetail->ProductReceipt->ProductReceiptDetailSerialNumber->getData('list', array(
                            'fields' => array(
                                'ProductReceiptDetailSerialNumber.serial_number',
                                'ProductReceiptDetailSerialNumber.serial_number',
                            ),
                            'conditions' => array(
                                'ProductReceiptDetailSerialNumber.product_receipt_id' => $product_receipt_id,
                                'ProductReceiptDetailSerialNumber.product_id' => $product_id,
                            ),
                        ), array(
                            'status' => 'confirm',
                        ));
                    } else if( $transaction_type == 'product_expenditure' ) {
                        $product_expenditure_detail_id = Common::hashEmptyField($value, 'DocumentDetail.id');

                        $value['DocumentDetail']['SerialNumber'] = $this->Product->ProductHistory->ProductExpenditureDetail->ProductExpenditureDetailSerialNumber->getData('list', array(
                            'fields' => array(
                                'ProductExpenditureDetailSerialNumber.serial_number',
                                'ProductExpenditureDetailSerialNumber.serial_number',
                            ),
                            'conditions' => array(
                                'ProductExpenditureDetailSerialNumber.product_expenditure_detail_id' => $product_expenditure_detail_id,
                                'ProductExpenditureDetailSerialNumber.product_id' => $product_id,
                                'ProductExpenditureDetailSerialNumber.price' => $price,
                            ),
                        ));

                        // if( $product_history_id == 1806 ) {
                        //     debug($value);die();
                        // }
                    } else if( in_array($transaction_type, array('product_adjustment_min', 'product_adjustment_plus')) ) {
                        $product_adjustment_detail_id = Common::hashEmptyField($value, 'DocumentDetail.id');

                        $value['DocumentDetail']['SerialNumber'] = $this->Product->ProductHistory->ProductAdjustmentDetail->ProductAdjustmentDetailSerialNumber->getData('list', array(
                            'fields' => array(
                                'ProductAdjustmentDetailSerialNumber.serial_number',
                                'ProductAdjustmentDetailSerialNumber.serial_number',
                            ),
                            'conditions' => array(
                                'ProductAdjustmentDetailSerialNumber.product_adjustment_detail_id' => $product_adjustment_detail_id,
                                'ProductAdjustmentDetailSerialNumber.product_id' => $product_id,
                            ),
                        ));
                    } else if( empty($transaction_type) ) {
                        $value['DocumentDetail']['SerialNumber'] = $this->Product->ProductHistory->ProductStock->find('list', array(
                            'fields' => array(
                                'ProductStock.serial_number',
                                'ProductStock.serial_number',
                            ),
                            'contain' => array(
                                'ProductHistory',
                            ),
                            'conditions' => array(
                                'ProductStock.product_history_id' => $product_history_id,
                                'ProductHistory.status' => 1,
                                'ProductHistory.transaction_type = \'\' ',
                                'ProductHistory.product_id' => $product_id,
                                'ProductHistory.branch_id' => $branch_id,
                            ),
                        ));
                    }

                    if( in_array($transaction_type, array('product_adjustment_min', 'product_adjustment_plus', 'product_adjustment_min_void', 'product_adjustment_plus_void')) ) {
                        $result[$product_id][$branch_id]['Branch'] = Common::hashEmptyField($value, 'Branch');
                        $result[$product_id][$branch_id]['Product'] = Common::hashEmptyField($value, 'Product');
                        $result[$product_id][$branch_id]['ProductHistory'][] = $value;
                    } else {
                        $document_type = Common::hashEmptyField($value, 'DocumentDetail.Document.document_type');
                        $document_id = Common::hashEmptyField($value, 'DocumentDetail.Document.document_id');

                        switch ($document_type) {
                            case 'po':
                                $transactionName = 'PurchaseOrder';
                                break;
                            
                            default:
                                $transactionName = 'Spk';
                                break;
                        }

                        if( !empty($modelName) ) {
                            $modelNameDetail = $modelName.'Detail';
                            $value = $this->Product->ProductHistory->$modelNameDetail->$modelName->$transactionName->getMerge($value, $document_id, $transactionName.'.id', 'all', 'Transaction');
                            $truck_id = Common::hashEmptyField($value, 'Transaction.truck_id');

                            if( !empty($truck_id) ) {
                                $value = $this->Product->ProductHistory->$modelNameDetail->$modelName->$transactionName->Truck->getMerge($value, $truck_id);
                            }
                        }

                        $result[$product_id][$branch_id]['Branch'] = Common::hashEmptyField($value, 'Branch');
                        $result[$product_id][$branch_id]['Product'] = Common::hashEmptyField($value, 'Product');
                        $result[$product_id][$branch_id]['ProductHistory'][] = $value;
                    }
                }
            }

            $values = $result;
        }

        if(!empty($values)){
            $this->Product->ProductHistory->virtualFields['total_begining_balance'] = 'SUM(CASE WHEN ProductHistory.transaction_type = \'product_receipt\' OR ProductHistory.transaction_type = \'product_adjustment_plus\' OR ProductHistory.transaction_type = \'\' THEN ProductHistory.price*ProductHistory.qty ELSE 0 END) - SUM(CASE WHEN ProductHistory.transaction_type = \'product_expenditure\' OR ProductHistory.transaction_type = \'product_adjustment_min\' THEN ProductHistory.price*ProductHistory.qty ELSE 0 END)';
            $this->Product->ProductHistory->virtualFields['total_qty_in'] = 'SUM(CASE WHEN ProductHistory.type = \'in\' THEN ProductHistory.qty ELSE 0 END)';
            $this->Product->ProductHistory->virtualFields['total_qty_out'] = 'SUM(CASE WHEN ProductHistory.type = \'out\' THEN ProductHistory.qty ELSE 0 END)';
            $this->Product->ProductStock->virtualFields['label'] = 'CONCAT(ProductStock.id, \'|\', ProductStock.price)';
                    
            foreach ($values as $key => &$product) {
                if(!empty($product)){
                    foreach ($product as $key => &$branch) {
                        $product_id = Common::hashEmptyField($branch, 'Product.id');
                        $branch_id = Common::hashEmptyField($branch, 'Branch.id');

                        $dateFrom = Common::hashEmptyField($params, 'named.DateFrom');
                        $options = Common::_callUnset($options, array(
                            'group',
                            'limit',
                            'conditions' => array(
                                'ProductHistory.product_id',
                                'ProductHistory.branch_id',
                                'DATE_FORMAT(ProductHistory.transaction_date, \'%Y-%m-%d\') >=',
                                'DATE_FORMAT(ProductHistory.transaction_date, \'%Y-%m-%d\') <=',
                            ),
                        ));

                        $options['conditions']['DATE_FORMAT(ProductHistory.transaction_date, \'%Y-%m-%d\') <'] = $dateFrom;
                        // $options['conditions']['ProductHistory.product_type'] = 'default';
                        $options['conditions']['ProductHistory.product_id'] = $product_id;
                        $options['conditions']['ProductHistory.branch_id'] = $branch_id;
                        $options['order'] = array(
                            'ProductHistory.transaction_date' => 'DESC',
                            'ProductHistory.created' => 'DESC',
                        );

                        $lastHistory = $this->Product->ProductHistory->getData('first', $options, array(
                            'branch' => false,
                        ));

                        $tmpOption = $options;
                        $tmpOption['group'] = array(
                            'ProductHistory.price',
                        );
                        $tmpOption['fields'] = array(
                            'ProductHistory.price',
                            'ProductHistory.total_qty_in',
                            'ProductHistory.total_qty_out',
                        );
                        $lastHistoryByPrice = $this->Product->ProductHistory->getData('all', $tmpOption, array(
                            'branch' => false,
                        ));

                        $receiptSN = $this->Product->ProductHistory->ProductReceiptDetail->ProductReceipt->ProductReceiptDetailSerialNumber->getData('list', array(
                            'fields' => array(
                                'ProductReceiptDetailSerialNumber.serial_number',
                                'ProductReceiptDetailSerialNumber.serial_number',
                            ),
                            'contain' => array(
                                'ProductReceipt',
                            ),
                            'conditions' => array(
                                'ProductReceiptDetailSerialNumber.product_id' => $product_id,
                                'DATE_FORMAT(ProductReceipt.transaction_date, \'%Y-%m-%d\') <' => $dateFrom,
                                'ProductReceipt.branch_id' => $branch_id,
                                'ProductReceipt.status' => 1,
                                'ProductReceipt.transaction_status NOT' => array( 'unposting', 'revised', 'void' ),
                                // 'ProductReceipt.document_type <>' => 'spk',
                            ),
                        ), array(
                            'status' => 'confirm',
                        ));

                        $this->Product->ProductHistory->ProductExpenditureDetail->ProductExpenditureDetailSerialNumber->bindModel(array(
                            'hasOne' => array(
                                'ProductExpenditure' => array(
                                    'className' => 'ProductExpenditure',
                                    'foreignKey' => false,
                                    'conditions' => array(
                                        'ProductExpenditure.id = ProductExpenditureDetail.product_expenditure_id',
                                    ),
                                ),
                            )
                        ), false);
                        $expenditureSN = $this->Product->ProductHistory->ProductExpenditureDetail->ProductExpenditureDetailSerialNumber->getData('list', array(
                            'fields' => array(
                                'ProductExpenditureDetailSerialNumber.serial_number',
                                'ProductExpenditureDetailSerialNumber.serial_number',
                            ),
                            'contain' => array(
                                'ProductExpenditureDetail',
                                'ProductExpenditure',
                            ),
                            'conditions' => array(
                                'ProductExpenditureDetail.status' => 1,
                                'ProductExpenditureDetailSerialNumber.product_id' => $product_id,
                                'DATE_FORMAT(ProductExpenditure.transaction_date, \'%Y-%m-%d\') <' => $dateFrom,
                                'ProductExpenditure.branch_id' => $branch_id,
                                'ProductExpenditure.status' => 1,
                                'ProductExpenditure.transaction_status NOT' => array( 'unposting', 'revised', 'void' ),
                            ),
                        ));
                        $last_serial_number = array_diff($receiptSN, $expenditureSN);

                        $this->Product->ProductHistory->ProductAdjustmentDetail->ProductAdjustmentDetailSerialNumber->bindModel(array(
                            'hasOne' => array(
                                'ProductAdjustment' => array(
                                    'className' => 'ProductAdjustment',
                                    'foreignKey' => false,
                                    'conditions' => array(
                                        'ProductAdjustment.id = ProductAdjustmentDetail.product_adjustment_id',
                                    ),
                                ),
                            )
                        ), false);
                        $adjustmentPlus = $this->Product->ProductHistory->ProductAdjustmentDetail->ProductAdjustmentDetailSerialNumber->getData('list', array(
                            'fields' => array(
                                'ProductAdjustmentDetailSerialNumber.serial_number',
                                'ProductAdjustmentDetailSerialNumber.serial_number',
                            ),
                            'contain' => array(
                                'ProductAdjustmentDetail',
                                'ProductAdjustment',
                            ),
                            'conditions' => array(
                                'ProductAdjustmentDetail.status' => 1,
                                'ProductAdjustmentDetail.type' => 'plus',
                                'ProductAdjustmentDetailSerialNumber.product_id' => $product_id,
                                'DATE_FORMAT(ProductAdjustment.transaction_date, \'%Y-%m-%d\') <' => $dateFrom,
                                'ProductAdjustment.branch_id' => $branch_id,
                                'ProductAdjustment.status' => 1,
                                'ProductAdjustment.transaction_status NOT' => array( 'unposting', 'revised', 'void' ),
                            ),
                        ));
                        $last_serial_number = array_merge($last_serial_number, $adjustmentPlus);

                        $adjustmentMin = $this->Product->ProductHistory->ProductAdjustmentDetail->ProductAdjustmentDetailSerialNumber->getData('list', array(
                            'fields' => array(
                                'ProductAdjustmentDetailSerialNumber.serial_number',
                                'ProductAdjustmentDetailSerialNumber.serial_number',
                            ),
                            'contain' => array(
                                'ProductAdjustmentDetail',
                                'ProductAdjustment',
                            ),
                            'conditions' => array(
                                'ProductAdjustmentDetail.status' => 1,
                                'ProductAdjustmentDetail.type' => 'min',
                                'ProductAdjustmentDetailSerialNumber.product_id' => $product_id,
                                'DATE_FORMAT(ProductAdjustment.transaction_date, \'%Y-%m-%d\') <' => $dateFrom,
                                'ProductAdjustment.branch_id' => $branch_id,
                                'ProductAdjustment.status' => 1,
                                'ProductAdjustment.transaction_status NOT' => array( 'unposting', 'revised', 'void' ),
                            ),
                        ));
                        $last_serial_number = array_diff($last_serial_number, $adjustmentMin);

                        // $importStok = $this->Product->ProductHistory->ProductStock->find('list', array(
                        //     'fields' => array(
                        //         'ProductStock.serial_number',
                        //         'ProductStock.serial_number',
                        //     ),
                        //     'contain' => array(
                        //         'ProductHistory',
                        //     ),
                        //     'conditions' => array(
                        //         'ProductHistory.status' => 1,
                        //         'ProductHistory.transaction_type = \'\' ',
                        //         'ProductHistory.product_id' => $product_id,
                        //         'DATE_FORMAT(ProductHistory.transaction_date, \'%Y-%m-%d\') <' => $dateFrom,
                        //         'ProductHistory.branch_id' => $branch_id,
                        //     ),
                        // ), array(
                        //     'status' => 'barang_jadi',
                        // ));
                        // $last_serial_number = array_diff($last_serial_number, $importStok);

                        $total_qty_in = Common::hashEmptyField($lastHistory, 'ProductHistory.total_qty_in', 0);
                        $total_qty_out = Common::hashEmptyField($lastHistory, 'ProductHistory.total_qty_out', 0);
                        $total_qty = $total_qty_in - $total_qty_out;

                        $stock = $this->Product->ProductStock->getData('list', array(
                            'fields' => array(
                                'ProductStock.label',
                                'ProductStock.serial_number',
                            ),
                            'conditions' => array(
                                'ProductStock.product_id' => $product_id,
                                'ProductStock.serial_number' => $last_serial_number,
                                'ProductStock.branch_id' => $branch_id,
                                'DATE_FORMAT(ProductStock.transaction_date, \'%Y-%m-%d\') <' => $dateFrom,
                                // 'ProductStock.type' => 'default',
                            ),
                        ), array(
                            'status' => false,
                            'branch' => false,
                        ));

                        $lastHistory['ProductHistory']['ending'] = $total_qty;
                        $lastHistory['ProductHistory']['last_serial_number'] = $stock;
                        $lastHistory['ProductHistory']['by_price'] = $lastHistoryByPrice;

                        $branch['LastHistory'] = $this->Product->getMergeList($lastHistory, array(
                            'contain' => array(
                                'ProductUnit',
                            ),
                        ));
                    }
                }
            }
        }

        $this->RjProduct->_callBeforeViewStockCards($params);
        $this->MkCommon->_layout_file(array(
            'select',
            'freeze',
        ));
        $this->set(array(
            'values' => $values,
            'active_menu' => 'stock_cards',
        ));
    }

    public function expenditure_reports() {
        $dateFrom = date('Y-m-d', strtotime('-1 Month'));
        $dateTo = date('Y-m-d');
        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));

        $dataReport = $this->RmReport->_callDataExpenditure_reports($params, 30, 0, true);
        $values = Common::hashEmptyField($dataReport, 'data');

        $this->RjProduct->_callBeforeViewExpenditureReports($params);
        $this->MkCommon->_layout_file(array(
            'select',
            'freeze',
        ));
        $this->set(array(
            'values' => $values,
            'active_menu' => 'expenditure_reports',
            '_freeze' => true,
        ));
    }

    public function receipt_reports() {
        $dateFrom = date('Y-m-d', strtotime('-1 Month'));
        $dateTo = date('Y-m-d');
        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));

        $dataReport = $this->RmReport->_callDataReceipt_reports($params, 30, 0, true);
        $values = Common::hashEmptyField($dataReport, 'data');

        $this->RjProduct->_callBeforeViewReceiptReports($params);
        $this->MkCommon->_layout_file(array(
            'select',
            'freeze',
        ));
        $this->set(array(
            'values' => $values,
            'active_menu' => 'receipt_reports',
            '_freeze' => true,
        ));
    }

    // SerialNumber
    public function import_sn( $download = false ) {
        if(!empty($download)){
            $link_url = FULL_BASE_URL . '/files/product.xls';
            $this->redirect($link_url);
            exit;
        } else {
            App::import('Vendor', 'excelreader'.DS.'excel_reader2');

            $this->set('module_title', __('Produk'));
            $this->set('active_menu', 'products');
            $this->set('sub_module_title', __('Import Produk'));

            if(!empty($this->request->data)) { 
                $Zipped = $this->request->data['Import']['importdata'];

                if($Zipped["name"]) {
                    $filename = $Zipped["name"];
                    $source = $Zipped["tmp_name"];
                    $type = $Zipped["type"];
                    $name = explode(".", $filename);
                    $ext = end($name);
                    $accepted_types = array('application/vnd.ms-excel', 'application/ms-excel');

                    if(!empty($accepted_types)) {
                        foreach($accepted_types as $mime_type) {
                            if($mime_type == $type) {
                                $okay = true;
                                break;
                            }
                        }
                    }

                    $continue = strtolower($ext) == 'xls' ? true : false;

                    if(!$continue) {
                        $this->MkCommon->setCustomFlash(__('Maaf, silahkan upload file dalam bentuk Excel.'), 'error');
                        $this->redirect(array('action'=>'import'));
                    } else {
                        $path = APP.'webroot'.DS.'files'.DS.date('Y').DS.date('m').DS;
                        $filenoext = basename ($filename, '.xls');
                        $filenoext = basename ($filenoext, '.XLS');
                        $fileunique = uniqid() . '_' . $filenoext;

                        if( !file_exists($path) ) {
                            mkdir($path, 0755, true);
                        }

                        $targetdir = $path . $fileunique . $filename;
                         
                        ini_set('memory_limit', '96M');
                        ini_set('post_max_size', '64M');
                        ini_set('upload_max_filesize', '64M');

                        if(!move_uploaded_file($source, $targetdir)) {
                            $this->MkCommon->setCustomFlash(__('Maaf, terjadi kesalahan. Silahkan coba lagi, atau hubungi Admin kami.'), 'error');
                            $this->redirect(array('action'=>'import'));
                        }
                    }
                } else {
                    $this->MkCommon->setCustomFlash(__('Maaf, terjadi kesalahan. Silahkan coba lagi, atau hubungi Admin kami.'), 'error');
                    $this->redirect(array('action'=>'import'));
                }

                $xls_files = glob( $targetdir );

                if(empty($xls_files)) {
                    $this->MkCommon->setCustomFlash(__('Tidak terdapat file excel atau berekstensi .xls pada file zip Anda. Silahkan periksa kembali.'), 'error');
                    $this->redirect(array('action'=>'import'));
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
                            $successfull_row = 0;
                            $failed_row = 0;
                            $row_submitted = 1;
                            $error_message = '';
                            $textError = array();
                            $data = $dataimport;

                            for ($x=2;$x<=count($data->sheets[0]["cells"]); $x++) {
                                $datavar = array();
                                $flag = true;
                                $i = 1;

                                while ($flag) {
                                    if( !empty($data->sheets[0]["cells"][1][$i]) ) {
                                        $variable = $this->MkCommon->toSlug($data->sheets[0]["cells"][1][$i], '_');
                                        $thedata = !empty($data->sheets[0]["cells"][$x][$i])?$data->sheets[0]["cells"][$x][$i]:NULL;
                                        $$variable = trim($thedata);
                                        $datavar[] = trim($thedata);
                                    } else {
                                        $flag = false;
                                    }
                                    $i++;
                                }

                                if(array_filter($datavar)) {
                                    $kode_barang = !empty($kode_barang)?$kode_barang:false;
                                    $nomor_seri = !empty($nomor_seri)?$nomor_seri:false;
                                    $harga_barang = !empty($harga_barang)?$harga_barang:false;
                                    $harga_barang = !empty($harga_barang)?Common::_callPriceConverter($harga_barang):0;
                                    $harga_barang = !empty($harga_barang)?str_replace(array('Rp*', '*'), array('', ''), $harga_barang):0;
                                    $harga_barang = trim($harga_barang);

                                    $product = $this->Product->find('first', array(
                                        'conditions' => array(
                                            'Product.code' => $kode_barang,
                                        ),
                                    ));
                                    $product_id = Common::hashEmptyField($product, 'Product.id');
                                    $history = $this->Product->ProductHistory->getMerge(array(), $product_id);
                                    $ending = Common::hashEmptyField($history, 'ProductHistory.ending', 0);
                                    $index = __('%s-%s', $product_id, $harga_barang);

                                    $dataArr[$product_id]['Product']['id'] = $product_id;
                                    $dataArr[$product_id]['Product']['code'] = $kode_barang;
                                    $dataArr[$product_id]['Product']['is_serial_number'] = true;

                                    if( !empty($dataArr[$product_id]['Product']['ProductHistory'][$index]) ) {
                                        $qty = $dataArr[$product_id]['Product']['ProductHistory'][$index]['qty'] + 1;

                                        $dataArr[$product_id]['Product']['ProductHistory'][$index]['qty'] = $qty;
                                        $dataArr[$product_id]['Product']['ProductHistory'][$index]['ending'] = $qty;
                                        $dataArr[$product_id]['Product']['ProductHistory'][$index]['ProductStock'][] = array(
                                            'branch_id' => Configure::read('__Site.config_branch_id'),
                                            'transaction_date' => date('Y-m-d'),
                                            'qty' => 1,
                                            'qty_use' => 0,
                                            'price' => $harga_barang,
                                            'serial_number' => $nomor_seri,
                                            'product_id' => $product_id,
                                        );
                                    } else {
                                        $dataArr[$product_id]['Product']['ProductHistory'][$index] = array(
                                            'branch_id' => Configure::read('__Site.config_branch_id'),
                                            'balance' => 0,
                                            'transaction_type' => 'stok_awal',
                                            'transaction_date' => '2017-10-31',
                                            'qty' => 1,
                                            'price' => $harga_barang,
                                            'type' => 'in',
                                            'ending' => 1,
                                            'ProductStock' => array(
                                                array(
                                                    'branch_id' => Configure::read('__Site.config_branch_id'),
                                                    'transaction_date' => '2017-10-31',
                                                    'qty' => 1,
                                                    'qty_use' => 0,
                                                    'price' => $harga_barang,
                                                    'serial_number' => $nomor_seri,
                                                    'product_id' => $product_id,
                                                ),
                                            ),
                                        );
                                    }
                                }
                            }
                        }
                    }
                }

                if( !empty($dataArr) ) {
                    foreach ($dataArr as $key => $value) {
                        $result = $this->Product->saveAll($value, array(
                            'deep' => true,
                        ));
                        $status = $this->MkCommon->filterEmptyField($result, 'status');

                        $validationErrors = $this->MkCommon->filterEmptyField($result, 'validationErrors');
                        $textError = $this->MkCommon->_callMsgValidationErrors($validationErrors, 'string');

                        $this->MkCommon->setProcessParams($result, false, array(
                            'flash' => false,
                            'noRedirect' => true,
                        ));

                        if( $status == 'error' ) {
                            $failed_row++;
                            $error_message .= sprintf(__('Gagal pada baris ke %s : Gagal Upload Data %s'), $row_submitted, $kode) . '<br>';
                        } else {
                            $successfull_row++;
                        }
                        
                        $row_submitted++;
                    }
                }

                if(!empty($successfull_row)) {
                    $message_import1 = sprintf(__('Import Berhasil: (%s baris), dari total (%s baris)'), $successfull_row, $row_submitted-1);
                    $this->MkCommon->setCustomFlash($message_import1, 'success');
                }
                
                if(!empty($error_message)) {
                    $this->MkCommon->setCustomFlash($error_message, 'error');
                }

                $this->redirect(array('action'=>'import'));
            }
        }
    }

    // Real
    public function import( $download = false ) {
        if(!empty($download)){
            $link_url = FULL_BASE_URL . '/files/product.xls';
            $this->redirect($link_url);
            exit;
        } else {
            App::import('Vendor', 'excelreader'.DS.'excel_reader2');

            $this->set('module_title', __('Produk'));
            $this->set('active_menu', 'products');
            $this->set('sub_module_title', __('Import Produk'));

            if(!empty($this->request->data)) { 
                $Zipped = $this->request->data['Import']['importdata'];

                if($Zipped["name"]) {
                    $filename = $Zipped["name"];
                    $source = $Zipped["tmp_name"];
                    $type = $Zipped["type"];
                    $name = explode(".", $filename);
                    $ext = end($name);
                    $accepted_types = array('application/vnd.ms-excel', 'application/ms-excel');

                    if(!empty($accepted_types)) {
                        foreach($accepted_types as $mime_type) {
                            if($mime_type == $type) {
                                $okay = true;
                                break;
                            }
                        }
                    }

                    $continue = strtolower($ext) == 'xls' ? true : false;

                    if(!$continue) {
                        $this->MkCommon->setCustomFlash(__('Maaf, silahkan upload file dalam bentuk Excel.'), 'error');
                        $this->redirect(array('action'=>'import'));
                    } else {
                        $path = APP.'webroot'.DS.'files'.DS.date('Y').DS.date('m').DS;
                        $filenoext = basename ($filename, '.xls');
                        $filenoext = basename ($filenoext, '.XLS');
                        $fileunique = uniqid() . '_' . $filenoext;

                        if( !file_exists($path) ) {
                            mkdir($path, 0755, true);
                        }

                        $targetdir = $path . $fileunique . $filename;
                         
                        ini_set('memory_limit', '96M');
                        ini_set('post_max_size', '64M');
                        ini_set('upload_max_filesize', '64M');

                        if(!move_uploaded_file($source, $targetdir)) {
                            $this->MkCommon->setCustomFlash(__('Maaf, terjadi kesalahan. Silahkan coba lagi, atau hubungi Admin kami.'), 'error');
                            $this->redirect(array('action'=>'import'));
                        }
                    }
                } else {
                    $this->MkCommon->setCustomFlash(__('Maaf, terjadi kesalahan. Silahkan coba lagi, atau hubungi Admin kami.'), 'error');
                    $this->redirect(array('action'=>'import'));
                }

                $xls_files = glob( $targetdir );

                if(empty($xls_files)) {
                    $this->MkCommon->setCustomFlash(__('Tidak terdapat file excel atau berekstensi .xls pada file zip Anda. Silahkan periksa kembali.'), 'error');
                    $this->redirect(array('action'=>'import'));
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
                            $successfull_row = 0;
                            $failed_row = 0;
                            $row_submitted = 1;
                            $error_message = '';
                            $textError = array();
                            $data = $dataimport;

                            for ($x=2;$x<=count($data->sheets[0]["cells"]); $x++) {
                                $datavar = array();
                                $flag = true;
                                $i = 1;

                                while ($flag) {
                                    if( !empty($data->sheets[0]["cells"][1][$i]) ) {
                                        $variable = $this->MkCommon->toSlug($data->sheets[0]["cells"][1][$i], '_');
                                        $thedata = !empty($data->sheets[0]["cells"][$x][$i])?$data->sheets[0]["cells"][$x][$i]:NULL;
                                        $$variable = trim($thedata);
                                        $datavar[] = trim($thedata);
                                    } else {
                                        $flag = false;
                                    }
                                    $i++;
                                }

                                if(array_filter($datavar)) {
                                    $kode = !empty($kode)?$kode:false;
                                    $kategori = !empty($kategori)?$kategori:false;
                                    $sub_kategori = !empty($sub_kategori)?$sub_kategori:false;
                                    $nama_item = !empty($nama_item)?$nama_item:false;
                                    $satuan = !empty($satuan)?$satuan:false;
                                    $stock_akhir = !empty($stock_akhir)?$stock_akhir:false;
                                    $ns = !empty($ns)?$ns:false;
                                    $ps = !empty($ps)?$ps:0;
                                    $min_stok = !empty($min_stok)?$min_stok:0;
                                    $max_stok = !empty($max_stok)?$max_stok:0;

                                    $harga_satuan = !empty($harga_satuan)?$harga_satuan:false;
                                    $harga_satuan = !empty($harga_satuan)?Common::_callPriceConverter($harga_satuan):0;
                                    $harga_satuan = !empty($harga_satuan)?str_replace(array('Rp*', '*'), array('', ''), $harga_satuan):0;
                                    $harga_satuan = trim($harga_satuan);

                                    // if( !empty($ns) ) {
                                        $unit = $this->Product->ProductUnit->getMerge(array(), $satuan, 'ProductUnit.name');
                                        $grupmodel = $this->Product->ProductCategory->getMerge(array(), $kategori, 'ProductCategory', 'ProductCategory.name');
                                        $subgrupmodel = $this->Product->ProductCategory->getMerge(array(), $sub_kategori, 'ProductCategory', 'ProductCategory.name');

                                        $product = $this->Product->find('first', array(
                                            'conditions' => array(
                                                'Product.code' => $kode,
                                            ),
                                        ));
                                        $id = Common::hashEmptyField($product, 'Product.id');

                                        if( empty($unit) ) {
                                            $unit = array(
                                                'ProductUnit' => array(
                                                    'name' => $satuan,
                                                ),
                                            );

                                            $this->Product->ProductUnit->create();
                                            $this->Product->ProductUnit->save($unit);
                                            $unit['ProductUnit']['id'] = $this->Product->ProductUnit->id;
                                        }
                                        if( empty($grupmodel) ) {
                                            $grupmodel = array(
                                                'ProductCategory' => array(
                                                    'name' => $kategori,
                                                ),
                                            );

                                            $this->Product->ProductCategory->create();
                                            $this->Product->ProductCategory->save($grupmodel);
                                            $grupmodel['ProductCategory']['id'] = $this->Product->ProductCategory->id;
                                        }
                                        if( empty($subgrupmodel) ) {
                                            $subgrupmodel = array(
                                                'ProductCategory' => array(
                                                    'parent_id' => Common::hashEmptyField($grupmodel, 'ProductCategory.id', 0),
                                                    'name' => $sub_kategori,
                                                ),
                                            );

                                            $this->Product->ProductCategory->create();
                                            $this->Product->ProductCategory->save($subgrupmodel);
                                            $subgrupmodel['ProductCategory']['id'] = $this->Product->ProductCategory->id;
                                        }

                                        $dataArr = array(
                                            'Product' => array(
                                                'id' => $id,
                                                'code' => $kode,
                                                'name' => $nama_item,
                                                'product_unit_id' => Common::hashEmptyField($unit, 'ProductUnit.id', 0),
                                                'product_category_id' => Common::hashEmptyField($subgrupmodel, 'ProductCategory.id', 0),
                                                'is_supplier_quotation' => $ps,
                                                'is_serial_number' => $ns,
                                                'type' => 'barang_jadi',
                                                'price' => $harga_satuan,
                                            ),
                                        );
                                        
                                        if( !empty($min_stok) ) {
                                            $dataArr['ProductMinStock'] = array(
                                                array(
                                                    'branch_id' => 15,
                                                    'min_stock' => $min_stok,
                                                ),
                                            );
                                        }
                                        if( !empty($stock_akhir) ) {
                                            $dataArr['Product']['product_stock_cnt'] = $stock_akhir;
                                            $dataArr['ProductHistory'] = array(
                                                array(
                                                    'branch_id' => Configure::read('__Site.config_branch_id'),
                                                    'balance' => 0,
                                                    'transaction_type' => 'stok_awal',
                                                    'transaction_date' => '2017-12-31',
                                                    'qty' => $stock_akhir,
                                                    'price' => $harga_satuan,
                                                    'type' => 'in',
                                                    'ending' => $stock_akhir,
                                                ),
                                            );
                                            $dataArr['ProductStock'] = array(
                                                array(
                                                    'branch_id' => Configure::read('__Site.config_branch_id'),
                                                    'transaction_date' => '2017-12-31',
                                                    'qty' => $stock_akhir,
                                                    'qty_use' => 0,
                                                    'price' => $harga_satuan,
                                                ),
                                            );
                                        }

                                        $result = $this->Product->saveAll($dataArr);
                                        $status = $this->MkCommon->filterEmptyField($result, 'status');

                                        $validationErrors = $this->MkCommon->filterEmptyField($result, 'validationErrors');
                                        $textError = $this->MkCommon->_callMsgValidationErrors($validationErrors, 'string');

                                        $this->MkCommon->setProcessParams($result, false, array(
                                            'flash' => false,
                                            'noRedirect' => true,
                                        ));

                                        if( $status == 'error' ) {
                                            $failed_row++;
                                            $error_message .= sprintf(__('Gagal pada baris ke %s : Gagal Upload Data %s'), $row_submitted, $kode) . '<br>';
                                        } else {
                                            $successfull_row++;
                                        }
                                        
                                        $row_submitted++;
                                    // }
                                }
                            }
                        }
                    }
                }

                if(!empty($successfull_row)) {
                    $message_import1 = sprintf(__('Import Berhasil: (%s baris), dari total (%s baris)'), $successfull_row, $row_submitted-1);
                    $this->MkCommon->setCustomFlash($message_import1, 'success');
                }
                
                if(!empty($error_message)) {
                    $this->MkCommon->setCustomFlash($error_message, 'error');
                }

                $this->redirect(array('action'=>'import'));
            }
        }
    }

    public function import_bak( $download = false ) {
        if(!empty($download)){
            $link_url = FULL_BASE_URL . '/files/products.xls';
            $this->redirect($link_url);
            exit;
        } else {
            App::import('Vendor', 'excelreader'.DS.'excel_reader2');

            $this->set('module_title', __('Produk'));
            $this->set('active_menu', 'products');
            $this->set('sub_module_title', __('Import Produk'));

            if(!empty($this->request->data)) { 
                $Zipped = $this->request->data['Import']['importdata'];

                if($Zipped["name"]) {
                    $filename = $Zipped["name"];
                    $source = $Zipped["tmp_name"];
                    $type = $Zipped["type"];
                    $name = explode(".", $filename);
                    $ext = end($name);
                    $accepted_types = array('application/vnd.ms-excel', 'application/ms-excel');

                    if(!empty($accepted_types)) {
                        foreach($accepted_types as $mime_type) {
                            if($mime_type == $type) {
                                $okay = true;
                                break;
                            }
                        }
                    }

                    $continue = strtolower($ext) == 'xls' ? true : false;

                    if(!$continue) {
                        $this->MkCommon->setCustomFlash(__('Maaf, silahkan upload file dalam bentuk Excel.'), 'error');
                        $this->redirect(array('action'=>'import'));
                    } else {
                        $path = APP.'webroot'.DS.'files'.DS.date('Y').DS.date('m').DS;
                        $filenoext = basename ($filename, '.xls');
                        $filenoext = basename ($filenoext, '.XLS');
                        $fileunique = uniqid() . '_' . $filenoext;

                        if( !file_exists($path) ) {
                            mkdir($path, 0755, true);
                        }

                        $targetdir = $path . $fileunique . $filename;
                         
                        ini_set('memory_limit', '96M');
                        ini_set('post_max_size', '64M');
                        ini_set('upload_max_filesize', '64M');

                        if(!move_uploaded_file($source, $targetdir)) {
                            $this->MkCommon->setCustomFlash(__('Maaf, terjadi kesalahan. Silahkan coba lagi, atau hubungi Admin kami.'), 'error');
                            $this->redirect(array('action'=>'import'));
                        }
                    }
                } else {
                    $this->MkCommon->setCustomFlash(__('Maaf, terjadi kesalahan. Silahkan coba lagi, atau hubungi Admin kami.'), 'error');
                    $this->redirect(array('action'=>'import'));
                }

                $xls_files = glob( $targetdir );

                if(empty($xls_files)) {
                    $this->MkCommon->setCustomFlash(__('Tidak terdapat file excel atau berekstensi .xls pada file zip Anda. Silahkan periksa kembali.'), 'error');
                    $this->redirect(array('action'=>'import'));
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
                            $successfull_row = 0;
                            $failed_row = 0;
                            $row_submitted = 1;
                            $error_message = '';
                            $textError = array();
                            $data = $dataimport;

                            for ($x=2;$x<=count($data->sheets[0]["cells"]); $x++) {
                                $datavar = array();
                                $flag = true;
                                $i = 1;

                                while ($flag) {
                                    if( !empty($data->sheets[0]["cells"][1][$i]) ) {
                                        $variable = $this->MkCommon->toSlug($data->sheets[0]["cells"][1][$i], '_');
                                        $thedata = !empty($data->sheets[0]["cells"][$x][$i])?$data->sheets[0]["cells"][$x][$i]:NULL;
                                        $$variable = trim($thedata);
                                        $datavar[] = trim($thedata);
                                    } else {
                                        $flag = false;
                                    }
                                    $i++;
                                }

                                if(array_filter($datavar)) {
                                    $kode = !empty($kode)?$kode:false;
                                    $grup = !empty($grup)?$grup:false;
                                    $nama = !empty($nama)?$nama:false;
                                    $satuan = !empty($satuan)?$satuan:false;
                                    $penawaran_supplier = !empty($penawaran_supplier)?$penawaran_supplier:false;
                                    $nomor_seri = !empty($nomor_seri)?$nomor_seri:false;
                                    $tipe_barang = !empty($tipe_barang)?$this->MkCommon->toSlug($tipe_barang, '_'):false;

                                    $penawaran_supplier = strtolower($penawaran_supplier);
                                    $nomor_seri = strtolower($nomor_seri);

                                    $unit = $this->Product->ProductUnit->getMerge(array(), $satuan, 'ProductUnit.name');
                                    $grupmodel = $this->Product->ProductCategory->getMerge(array(), $grup, 'ProductCategory', 'ProductCategory.name');

                                    switch ($penawaran_supplier) {
                                        case 'ya':
                                            $penawaran_supplier = true;
                                            break;
                                        default:
                                            $penawaran_supplier = false;
                                            break;
                                    }

                                    switch ($nomor_seri) {
                                        case 'ya':
                                            $nomor_seri = true;
                                            break;
                                        default:
                                            $nomor_seri = false;
                                            break;
                                    }

                                    $dataArr = array(
                                        'Product' => array(
                                            'code' => $kode,
                                            'name' => $nama,
                                            'product_unit_id' => Common::hashEmptyField($unit, 'ProductUnit.id', 0),
                                            'product_category_id' => Common::hashEmptyField($grupmodel, 'ProductCategory.id', 0),
                                            'is_supplier_quotation' => $penawaran_supplier,
                                            'is_serial_number' => $nomor_seri,
                                            'type' => $tipe_barang,
                                        ),
                                    );

                                    if( empty($unit) ) {
                                        $dataArr['ProductUnit'] = array(
                                            'name' => $satuan,
                                        );
                                    }

                                    if( empty($grupmodel) ) {
                                        $dataArr['ProductCategory'] = array(
                                            'name' => $grup,
                                        );
                                    }

                                    $result = $this->Product->saveAll($dataArr);
                                    $status = $this->MkCommon->filterEmptyField($result, 'status');

                                    if( $status == 'success' ) {
                                        $dataUpdate = array();

                                        if( !empty($this->Product->ProductUnit->id) ) {
                                            $dataUpdate['Product.product_unit_id'] = $this->Product->ProductUnit->id;
                                        }
                                        if( !empty($this->Product->ProductCategory->id) ) {
                                            $dataUpdate['Product.product_category_id'] = $this->Product->ProductCategory->id;
                                        }

                                        $this->Product->updateAll($dataUpdate, array(
                                            'Product.id' => $this->Product->id,
                                        ));
                                    }

                                    $validationErrors = $this->MkCommon->filterEmptyField($result, 'validationErrors');
                                    $textError = $this->MkCommon->_callMsgValidationErrors($validationErrors, 'string');

                                    $this->MkCommon->setProcessParams($result, false, array(
                                        'flash' => false,
                                        'noRedirect' => true,
                                    ));

                                    if( $status == 'error' ) {
                                        $failed_row++;
                                        $error_message .= sprintf(__('Gagal pada baris ke %s : Gagal Upload Data. %s'), $row_submitted, $textError) . '<br>';
                                    } else {
                                        $successfull_row++;
                                    }
                                    
                                    $row_submitted++;
                                }
                            }
                        }
                    }
                }

                if(!empty($successfull_row)) {
                    $message_import1 = sprintf(__('Import Berhasil: (%s baris), dari total (%s baris)'), $successfull_row, $row_submitted-1);
                    $this->MkCommon->setCustomFlash($message_import1, 'success');
                }
                
                if(!empty($error_message)) {
                    $this->MkCommon->setCustomFlash($error_message, 'error');
                }

                $this->redirect(array('action'=>'import'));
            }
        }
    }

    public function retur() {
        $this->loadModel('ProductRetur');
        $this->set('sub_module_title', __('Retur Barang'));
        
        $dateFrom = date('Y-m-d', strtotime('-1 Month'));
        $dateTo = date('Y-m-d');

        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));
        $options =  $this->ProductRetur->_callRefineParams($params);
        $this->paginate = $this->ProductRetur->getData('paginate', $options, array(
            'status' => 'all',
        ));
        $values = $this->paginate('ProductRetur');
        $values = $this->ProductRetur->getMergeList($values, array(
            'contain' => array(
                'Vendor' => array(
                    'elements' => array(
                        'status' => 'all',
                        'branch' => false,
                    ),
                ),
                'Employe',
                'ProductReturDetail' => array(
                    'contain' => array(
                        'ProductHistory' => array(
                            'conditions' => array(
                                'ProductHistory.transaction_type' => 'product_returs',
                            ),
                            'contain' => array(
                                'ProductStock',
                            ),
                        ),
                    ),
                ),
            ),
        ));

        if( !empty($values) ) {
            foreach ($values as $key => $value) {
                $value = $this->RjProduct->_callGetDocRetur($value);
                $values[$key] = $value;
            }
        }

        $this->RjProduct->_callBeforeRenderReceipts();

        $this->MkCommon->_layout_file('select');
        $this->set('active_menu', 'retur');
        $this->set(compact(
            'values'
        ));
    }

    function retur_add(){
        $this->set('sub_module_title', __('Retur Barang'));

        $data = $this->request->data;

        if( !empty($data) ) {
            $data = $this->RjProduct->_callBeforeSaveRetur($data);
            $result = $this->Product->ProductReturDetail->ProductRetur->doSave($data);
            $this->MkCommon->setProcessParams($result, array(
                'controller' => 'products',
                'action' => 'retur',
                'admin' => false,
            ));
        }

        $this->RjProduct->_callBeforeRenderRetur($data);

        $this->set(array(
            'active_menu' => 'retur',
        ));
    }

    public function retur_edit( $id = false ) {
        $this->set('sub_module_title', __('Edit Retur'));

        $value = $this->Product->ProductReturDetail->ProductRetur->getData('first', array(
            'conditions' => array(
                'ProductRetur.id' => $id,
            ),
        ), array(
            'status' => 'pending',
        ));

        if( !empty($value) ) {
            $value = $this->RjProduct->_callGetDocRetur($value);
            $value = $this->Product->ProductReturDetail->getMerge($value, $id);

            $data = $this->request->data;

            if( !empty($data) ) {
                $data = $this->RjProduct->_callBeforeSaveRetur($data, $id);
                $result = $this->Product->ProductReturDetail->ProductRetur->doSave($data, $value, $id);
                $this->MkCommon->setProcessParams($result, array(
                    'controller' => 'products',
                    'action' => 'retur',
                    'admin' => false,
                ));
            }

            $this->RjProduct->_callBeforeRenderRetur($data, $value);

            $this->set(array(
                'value' => $value,
                'active_menu' => 'retur',
            ));
            $this->render('retur_add');
        } else {
            $this->MkCommon->redirectReferer(__('Retur tidak ditemukan.'), 'error');
        }
    }

    public function retur_detail( $id = false ) {
        $this->set('sub_module_title', __('Detail Retur Barang'));
        $value = $this->Product->ProductReturDetail->ProductRetur->getData('first', array(
            'conditions' => array(
                'ProductRetur.id' => $id,
            ),
        ), array(
            'status' => false,
        ));

        if( !empty($value) ) {
            $value = $this->Product->ProductReturDetail->getMerge($value, $id);

            $user_id = $this->MkCommon->filterEmptyField($value, 'ProductRetur', 'user_id');
            $grandtotal = $this->MkCommon->filterEmptyField($value, 'ProductRetur', 'grandtotal');
            $nodoc = $this->MkCommon->filterEmptyField($value, 'ProductRetur', 'nodoc');
            $document_type = $this->MkCommon->filterEmptyField($value, 'ProductRetur', 'document_type');

            switch ($document_type) {
                default:
                    $documentModel = 'PurchaseOrder';
                    break;
            }

            $value = $this->Product->ProductReturDetail->ProductRetur->getMergeList($value, array(
                'contain' => array(
                    'Document' => array(
                        'uses' => $documentModel,
                        'elements' => array(
                            'branch' => false,
                        ),
                    ),
                ),
            ));

            $details = $this->MkCommon->filterEmptyField($value, 'ProductReturDetail');
            $value = $this->User->getMerge($value, $user_id);
            $this->RjProduct->_callBeforeRenderRetur(false, $value);

            $this->set('active_menu', 'retur');
            $this->set('view', 'detail');
            $this->set(compact(
                'vendors', 'value',
                'user_otorisasi_approvals', 'show_approval'
            ));
            $this->render('retur_add');
        } else {
            $this->MkCommon->redirectReferer(__('Retur barang tidak ditemukan.'), 'error');
        }
    }

    public function retur_toggle( $id, $type = null ) {
        $result = $this->Product->ProductReturDetail->ProductRetur->doDelete( $id, $type );
        $this->MkCommon->setProcessParams($result);
    }

    function retur_documents ( $type = false, $vendor_id = false ) {
        $vendor_id = $this->MkCommon->filterEmptyField($this->params, 'named', 'vendor_id', $vendor_id);
        $retur_id = $this->MkCommon->filterEmptyField($this->params, 'named', 'retur_id');
        $params = $this->MkCommon->_callRefineParams($this->params);
        $render = __('retur_documents_%s', $type);

        switch ($type) {
            case 'spk':
                $values = $this->RjProduct->_callSpkInternals($params, $vendor_id, 'eksternal');
                break;
            default:
                $values = $this->RjProduct->_callPurchaseOrders($params, $vendor_id, 'unretur_draft');
                $render = 'retur_documents';
                break;
        }

        $this->MkCommon->_layout_file('select');
        $this->set(compact(
            'values', 'type',
            'retur_id', 'vendor_id'
        ));
        $this->render($render);
    }

    function retur_document_products ( $transaction_id = false, $nodoc = null, $document_type = 'spk' ) {
        $data = $this->request->data;
        $nodoc = $this->MkCommon->filterEmptyField($data, 'ProductRetur', 'document_number', $nodoc);
        $document_type = $this->MkCommon->filterEmptyField($data, 'ProductRetur', 'document_type', $document_type);
        $values = false;

        $params = $this->MkCommon->_callRefineParams($this->params);
        $productCategories = $this->Product->ProductCategory->getData('list');

        $nodoc = urldecode($nodoc);
        $nodoc = str_replace('/', '[slash]', $nodoc);

        $this->set(array(
            'nodoc' => $nodoc,
            'transaction_id' => $transaction_id,
            'document_type' => $document_type,
            'productCategories' => $productCategories,
        ));

        $nodoc = str_replace('[slash]', '/', $nodoc);

        switch ($document_type) {
            case 'spk':
                $value = $this->Product->SpkProduct->Spk->getData('first', array(
                    'conditions' => array(
                        'Spk.nodoc' => $nodoc,
                    ),
                ), array(
                    'status' => 'unreceipt_draft',
                    'type' => 'eksternal',
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
                $this->RjProduct->_callBeforeRenderReturSpkProducts($values, $transaction_id);
                $this->render('retur_spk_products');
                break;
            default:
                $value = $this->Product->PurchaseOrderDetail->PurchaseOrder->getData('first', array(
                    'conditions' => array(
                        'PurchaseOrder.nodoc' => $nodoc,
                    ),
                ), array(
                    'status' => 'unretur_draft',
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
                $this->RjProduct->_callBeforeRenderReturPODetails($values, $transaction_id);
                $this->render('retur_po_products');
                break;
        }
    }

    function adjustment () {
        $this->loadModel('ProductAdjustment');
        $this->set('sub_module_title', 'Penyesuaian Qty');
        
        $dateFrom = date('Y-m-d', strtotime('-1 Month'));
        $dateTo = date('Y-m-d');

        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));
        $options =  $this->ProductAdjustment->_callRefineParams($params);
        $this->paginate = $this->ProductAdjustment->getData('paginate', $options, array(
            'status' => 'all',
        ));
        $values = $this->paginate('ProductAdjustment');

        if( !empty($values) ) {
            foreach ($values as $key => &$value) {
                $transaction_status = Common::hashEmptyField($value, 'ProductAdjustment.transaction_status');
                
                if( $transaction_status <> 'void' ) {
                    $tmp = $this->ProductAdjustment->getMergeList($value, array(
                        'contain' => array(
                            'ProductAdjustmentDetail' => array(
                                'contain' => array(
                                    'ProductHistory' => array(
                                        'contain' => array(
                                            'ProductStock',
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ));
                    $disabled_void = Set::extract('/ProductAdjustmentDetail/ProductHistory/ProductStock/ProductStock/qty_use', $tmp);
                    $disabled_void = array_filter($disabled_void);

                    if( !empty($disabled_void) ) {
                        $value['ProductAdjustment']['disabled_void'] = $disabled_void;
                    }
                }
            }
        }

        $this->MkCommon->_layout_file('select');
        $this->set('active_menu', 'adjustment');
        $this->set(compact(
            'values'
        ));
    }

    public function adjustment_import( $download = false ) {
        if(!empty($download)){
            $link_url = FULL_BASE_URL . '/files/adjustment.xls';
            $this->redirect($link_url);
            exit;
        } else {
            App::import('Vendor', 'excelreader'.DS.'excel_reader2');

            $this->set('module_title', __('Penyesuaian Qty'));
            $this->set('active_menu', 'products');
            $this->set('sub_module_title', __('Import Penyesuaian Qty'));

            if(!empty($this->request->data)) { 
                $Zipped = $this->request->data['Import']['importdata'];

                if($Zipped["name"]) {
                    $filename = $Zipped["name"];
                    $source = $Zipped["tmp_name"];
                    $type = $Zipped["type"];
                    $name = explode(".", $filename);
                    $ext = end($name);
                    $accepted_types = array('application/vnd.ms-excel', 'application/ms-excel');

                    if(!empty($accepted_types)) {
                        foreach($accepted_types as $mime_type) {
                            if($mime_type == $type) {
                                $okay = true;
                                break;
                            }
                        }
                    }

                    $continue = strtolower($ext) == 'xls' ? true : false;

                    if(!$continue) {
                        $this->MkCommon->setCustomFlash(__('Maaf, silahkan upload file dalam bentuk Excel.'), 'error');
                        $this->redirect(array('action'=>'adjustment_import'));
                    } else {
                        $path = APP.'webroot'.DS.'files'.DS.date('Y').DS.date('m').DS;
                        $filenoext = basename ($filename, '.xls');
                        $filenoext = basename ($filenoext, '.XLS');
                        $fileunique = uniqid() . '_' . $filenoext;

                        if( !file_exists($path) ) {
                            mkdir($path, 0755, true);
                        }

                        $targetdir = $path . $fileunique . $filename;
                         
                        ini_set('memory_limit', '96M');
                        ini_set('post_max_size', '64M');
                        ini_set('upload_max_filesize', '64M');

                        if(!move_uploaded_file($source, $targetdir)) {
                            $this->MkCommon->setCustomFlash(__('Maaf, terjadi kesalahan. Silahkan coba lagi, atau hubungi Admin kami.'), 'error');
                            $this->redirect(array('action'=>'adjustment_import'));
                        }
                    }
                } else {
                    $this->MkCommon->setCustomFlash(__('Maaf, terjadi kesalahan. Silahkan coba lagi, atau hubungi Admin kami.'), 'error');
                    $this->redirect(array('action'=>'adjustment_import'));
                }

                $xls_files = glob( $targetdir );

                if(empty($xls_files)) {
                    $this->MkCommon->setCustomFlash(__('Tidak terdapat file excel atau berekstensi .xls pada file zip Anda. Silahkan periksa kembali.'), 'error');
                    $this->redirect(array('action'=>'adjustment_import'));
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
                            $successfull_row = 0;
                            $failed_row = 0;
                            $row_submitted = 1;
                            $error_message = '';
                            $textError = array();
                            $data = $dataimport;

                            for ($x=2;$x<=count($data->sheets[0]["cells"]); $x++) {
                                $datavar = array();
                                $flag = true;
                                $i = 1;

                                while ($flag) {
                                    if( !empty($data->sheets[0]["cells"][1][$i]) ) {
                                        $variable = $this->MkCommon->toSlug($data->sheets[0]["cells"][1][$i], '_');
                                        $thedata = !empty($data->sheets[0]["cells"][$x][$i])?$data->sheets[0]["cells"][$x][$i]:NULL;
                                        $$variable = trim($thedata);
                                        $datavar[] = trim($thedata);
                                    } else {
                                        $flag = false;
                                    }
                                    $i++;
                                }

                                if(array_filter($datavar)) {
                                    $tgl_penyesuaian = !empty($tgl_penyesuaian)?Common::getDate($tgl_penyesuaian):false;
                                    $keterangan = !empty($keterangan)?$keterangan:false;
                                    $kode_barang = !empty($kode_barang)?$kode_barang:false;
                                    $ket_item = !empty($ket_item)?$ket_item:false;
                                    $qty_penyesuaian = !empty($qty_penyesuaian)?$qty_penyesuaian:false;
                                    $harga = !empty($harga)?Common::_callPriceConverter($harga):false;
                                    $no_seri = !empty($no_seri)?$no_seri:0;
                                    $kode_cabang = !empty($kode_cabang)?$kode_cabang:false;                                    

                                    $product = $this->Product->find('first', array(
                                        'conditions' => array(
                                            'Product.code' => $kode_barang,
                                        ),
                                    ));
                                    $branch = $this->GroupBranch->Branch->getData('first', array(
                                        'conditions' => array(
                                            'Branch.code' => $kode_cabang,
                                        ),
                                    ));
                                    $index = Common::toSlug($tgl_penyesuaian.$keterangan);
                                    $product_id = Common::hashEmptyField($product, 'Product.id');

                                    $dataArr[$index]['ProductAdjustment']['transaction_date'] = $tgl_penyesuaian;
                                    $dataArr[$index]['ProductAdjustment']['note'] = $keterangan;
                                    $dataArr[$index]['ProductAdjustment']['transaction_status'] = 'posting';
                                    $dataArr[$index]['ProductAdjustment']['import'] = true;

                                    $dataArr[$index]['ProductAdjustmentDetail']['product_id'][$product_id] = $product_id;
                                    $dataArr[$index]['ProductAdjustmentDetail']['note'][$product_id] = $ket_item;
                                    $dataArr[$index]['ProductAdjustmentDetail']['qty'][$product_id] = $qty_penyesuaian;
                                    $dataArr[$index]['ProductAdjustmentDetail']['price'][$product_id] = $harga;
                                    $dataArr[$index]['ProductAdjustmentDetail']['price'][$product_id] = $harga;
                                    $dataArr[$index]['ProductAdjustmentDetail']['row'][$product_id] = $row_submitted;
                                    
                                    if( !empty($no_seri) ) {
                                        $no_seri = explode(',', $no_seri);

                                        if( is_array($no_seri) ) {
                                            $dataArr[$index]['ProductAdjustmentDetailSerialNumber']['serial_numbers'][$product_id] = $no_seri;
                                        }
                                    }
                                    
                                    $row_submitted++;
                                }
                            }
                        }
                    }
                }

                if( !empty($dataArr) ) {
                    foreach ($dataArr as $key => $data) {
                        $rows = Common::hashEmptyField($data, 'ProductAdjustmentDetail.row', array());
                        $row_cnt = count($rows);
                        $row_cnt = !empty($row_cnt)?$row_cnt:1;
                        $rows = implode(',', $rows);

                        $data = $this->RjProduct->_callBeforeSaveAdjustment($data);
                        $result = $this->User->ProductAdjustment->doSave($data);

                        $status = Common::hashEmptyField($result, 'status');

                        if( $status == 'error' ) {
                            $failed_row++;


                            $error_message .= sprintf(__('Gagal pada baris ke %s'), $rows) . '<br>';
                        } else {
                            $successfull_row += $row_cnt;
                        }
                    }
                }

                if(!empty($successfull_row)) {
                    $message_import1 = sprintf(__('Import Berhasil: (%s baris), dari total (%s baris)'), $successfull_row, $row_submitted-1);
                    $this->MkCommon->setCustomFlash($message_import1, 'success');
                }
                
                if(!empty($error_message)) {
                    $this->MkCommon->setCustomFlash($error_message, 'error');
                }

                $this->redirect(array('action'=>'adjustment_import'));
            }
        }
    }

    public function adjustment_add() {
        $this->set('sub_module_title', __('Adjust Qty Barang'));

        $data = $this->request->data;
        $data = $this->RjProduct->_callBeforeSaveAdjustment($data);
        $result = $this->User->ProductAdjustment->doSave($data);
        $this->MkCommon->setProcessParams($result, array(
            'action' => 'adjustment',
            'admin' => false,
        ));
        $this->request->data = $this->RjProduct->_callBeforeRenderAdjustment($this->request->data);

        $this->MkCommon->_layout_file('select');
        $this->set('active_menu', 'adjustment');
    }

    public function adjustment_edit( $id = null ) {
        $this->set('sub_module_title', __('Edit Adjust Barang'));
        $value = $this->Product->ProductAdjustmentDetail->ProductAdjustment->getData('first', array(
            'conditions' => array(
                'ProductAdjustment.id' => $id,
            ),
        ), array(
            'status' => 'all',
        ));

        if( !empty($value) ) {
            $data = $this->request->data;

            if( !empty($data) ) {
                $data = $this->RjProduct->_callBeforeSaveAdjustment($data, $id);
                $result = $this->User->ProductAdjustment->doSave($data, $value, $id);
                $this->MkCommon->setProcessParams($result, array(
                    'action' => 'adjustment',
                    'admin' => false,
                ));
            }

            $this->request->data = $this->RjProduct->_callBeforeRenderAdjustment($this->request->data, $value);
            $this->MkCommon->getLogs($this->paramController, array( 'adjustment_add', 'adjustment_edit', 'adjustment_toggle' ), $id);

            $this->MkCommon->_layout_file('select');
            $this->set('active_menu', 'adjustment');
            $this->render('adjustment_add');
        } else {
            $this->MkCommon->redirectReferer(__('Penyesuaian tidak ditemukan.'), 'error');
        }
    }

    function adjustment_products ( $transaction_id = false ) {
        $data = $this->request->data;
        $values = false;

        $params = $this->MkCommon->_callRefineParams($this->params);
        $productCategories = $this->Product->ProductCategory->getData('list');

        $options =  $this->Product->_callRefineParams($params, array(
            'limit' => 10,
        ));

        $this->paginate = $this->Product->getData('paginate', $options, array(
            'status' => 'active',
        ));
        $values = $this->paginate('Product');


        if( !empty($values) ) {
            foreach ($values as $key => &$value) {
                $id = Common::hashEmptyField($value, 'Product.id');

                $value = $this->Product->getMergeList($value, array(
                    'contain' => array(
                        'ProductUnit',
                        'ProductCategory',
                    ),
                ));
                $value['Product']['product_stock_cnt'] = $this->Product->ProductStock->_callStock($id);
            }
        }

        $this->set(array(
            'values' => $values,
            'transaction_id' => $transaction_id,
            'productCategories' => $productCategories,
        ));
    }

    function bypass_adjust_serial_numbers ( $id = false ) {
        $data = $this->request->data;
        $session_id = $this->MkCommon->filterEmptyField($data, 'ProductAdjustment', 'session_id');
        $number = $this->MkCommon->filterEmptyField($this->params, 'named', 'picker', 0);
        $view = $this->MkCommon->filterEmptyField($this->params, 'named', 'view', 0);

        $value = $this->Product->getData('first', array(
            'conditions' => array(
                'Product.id' => $id,
            ),
        ));

        if( !empty($session_id) && !empty($value) ) {
            $serial_numbers = $this->MkCommon->filterEmptyField($data, 'ProductAdjustmentDetailSerialNumber', 'serial_number');

            if( !empty($serial_numbers) ) {
                $serial_numbers = $this->RjProduct->_callBeforeSaveSerialNumber($serial_numbers, $id, $session_id, 'ProductAdjustmentDetailSerialNumber');
                $result = $this->Product->ProductAdjustmentDetailSerialNumber->doSave($serial_numbers, $id, $session_id);

                $this->MkCommon->setProcessParams($result, false, array(
                    'ajaxFlash' => false,
                    'ajaxRedirect' => false,
                ));
            } else {
                $values = $this->Product->ProductAdjustmentDetailSerialNumber->getData('all', array(
                    'conditions' => array(
                        'ProductAdjustmentDetailSerialNumber.product_id' => $id,
                        'ProductAdjustmentDetailSerialNumber.session_id' => $session_id,
                    ),
                ));
                $this->RjProduct->_callBeforeViewAdjustSerialNumber($values, $session_id);
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

    public function adjustment_detail( $id = false ) {
        $this->set('sub_module_title', __('Detail Penyesuaian Qty'));

        $value = $this->Product->ProductAdjustmentDetail->ProductAdjustment->getData('first', array(
            'conditions' => array(
                'ProductAdjustment.id' => $id,
            ),
        ), array(
            'status' => 'all',
        ));

        if( !empty($value) ) {
            $this->request->data = $this->RjProduct->_callBeforeRenderAdjustment(false, $value);
            $this->MkCommon->getLogs($this->paramController, array( 'adjustment_add', 'adjustment_edit', 'adjustment_toggle' ), $id);
            $this->MkCommon->_layout_file('select');

            $this->set(array(
                'value' => $value,
                'active_menu' => 'adjustment',
                'view' => true,
            ));
            $this->render('adjustment_add');
        } else {
            $this->MkCommon->redirectReferer(__('Penyesuaian Qty tidak ditemukan.'), 'error');
        }
    }

    public function adjustment_toggle( $id ) {
        $result = $this->Product->ProductAdjustmentDetail->ProductAdjustment->doDelete( $id );
        $this->MkCommon->setProcessParams($result);
    }

    public function adjustment_report() {
        $dateFrom = date('Y-m-d', strtotime('-1 Month'));
        $dateTo = date('Y-m-d');
        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));

        $dataReport = $this->RmReport->_callDataAdjustment_report($params, 30, 0, true);
        $values = Common::hashEmptyField($dataReport, 'data');

        $this->RjProduct->_callBeforeViewAdjustmentReports($params);
        $this->MkCommon->_layout_file(array(
            'select',
            'freeze',
        ));
        $this->set(array(
            'values' => $values,
            'active_menu' => 'adjustment_report',
            '_freeze' => true,
        ));
    }

    public function min_stock_report() {
        $params = $this->MkCommon->_callRefineParams($this->params);

        $dataReport = $this->RmReport->_callDataMin_stock_report($params, 30, 0, true);
        $values = Common::hashEmptyField($dataReport, 'data');

        $this->RjProduct->_callBeforeViewMinStockReport($params);
        $this->MkCommon->_layout_file(array(
            'select',
        ));
        $this->set(array(
            'values' => $values,
            'active_menu' => 'min_stock_report',
        ));
    }

    public function category_report() {
        $params = $this->MkCommon->_callRefineParams($this->params);

        $dataReport = $this->RmReport->_callDataCategory_report($params, 30, 0, true);
        $values = Common::hashEmptyField($dataReport, 'data');

        $this->RjProduct->_callBeforeViewProductCategoryReports($params);
        $this->MkCommon->_layout_file(array(
            'select',
            'freeze',
        ));
        $this->set(array(
            'values' => $values,
            'active_menu' => 'category_report',
            '_freeze' => true,
        ));
    }

    function retur_choose_documents ( $type = false ) {
        switch ($type) {
            case 'spk':
                $vendors = $this->Product->SpkProduct->Spk->_callVendors('unreceipt_draft', false, 'eksternal');
                break;
            default:
                $vendors = $this->Product->PurchaseOrderDetail->PurchaseOrder->_callVendors('unreceipt_draft');
                break;
        }

        $this->set(compact(
            'vendors', 'type'
        ));
        $this->render('/Elements/blocks/products/retur/forms/choose_document');
    }

    public function target_categories() {
        $this->loadModel('ProductCategoryTarget');
        $params = $this->MkCommon->_callRefineParams($this->params);
        $options =  $this->ProductCategoryTarget->_callRefineParams($params);

        $this->paginate = $this->ProductCategoryTarget->getData('paginate', $options);
        $values = $this->paginate('ProductCategoryTarget');

        $values = $this->ProductCategoryTarget->getMergeList($values, array(
            'contain' => array(
                'ProductCategory',
            ),
        ));

        $productCategories = $this->Product->ProductCategory->getData('list');
        $this->MkCommon->_layout_file('select');

        $this->set('active_menu', 'target_categories');
        $this->set('sub_module_title', __('Target Grup Barang'));
        $this->set(compact(
            'values', 'productCategories'
        ));
    }

    function target_category_add(){
        $this->loadModel('ProductCategoryTarget');
        $this->set('sub_module_title', __('Tambah Target Grup Barang'));

        $result = $this->ProductCategoryTarget->doSave($this->request->data);
        $this->MkCommon->setProcessParams($result, array(
            'controller' => 'products',
            'action' => 'target_categories',
            'admin' => false,
        ));

        $productCategories = $this->Product->ProductCategory->getData('list');

        $this->set('active_menu', 'target_categories');
        $this->set(compact(
            'productCategories'
        ));
    }

    function target_category_edit( $id = false ){
        $this->loadModel('ProductCategoryTarget');
        $this->set('sub_module_title', __('Edit Target Grup Barang'));

        $value = $this->ProductCategoryTarget->getData('first', array(
            'conditions' => array(
                'ProductCategoryTarget.id' => $id,
            ),
        ));

        if( !empty($value) ) {
            $value = $this->ProductCategoryTarget->getMergeList($value, array(
                'contain' => array(
                    'ProductCategory',
                ),
            ));

            $result = $this->ProductCategoryTarget->doSave($this->request->data, $value, $id);
            $this->MkCommon->setProcessParams($result, array(
                'controller' => 'products',
                'action' => 'target_categories',
                'admin' => false,
            ));

            $productCategories = $this->Product->ProductCategory->getData('list');

            $this->set('active_menu', 'target_categories');
            $this->set(compact(
                'productCategories'
            ));

            $this->render('target_category_add');
        } else {
            $this->MkCommon->setCustomFlash(__('Target grup barang tidak ditemukan.'), 'error');
        }
    }

    function target_category_toggle($id){
        $this->loadModel('ProductCategoryTarget');
        $locale = $this->ProductCategoryTarget->getData('first', array(
            'conditions' => array(
                'ProductCategoryTarget.id' => $id
            )
        ));

        if($locale){
            $value = true;
            if($locale['ProductCategoryTarget']['status']){
                $value = false;
            }

            $this->ProductCategoryTarget->id = $id;
            $this->ProductCategoryTarget->set('status', $value);

            if($this->ProductCategoryTarget->save()){
                $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
                $this->Log->logActivity( sprintf(__('Sukses merubah status target grup barang ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id ); 
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal merubah status target grup barang ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id ); 
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Target grup barang tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    public function indicator_maintenance() {
        $params = $this->MkCommon->_callRefineParams($this->params);

        $dataReport = $this->RmReport->_callDataIndicator_maintenance($params, 30, 0, true);
        $values = Common::hashEmptyField($dataReport, 'data');

        $this->RjProduct->_callBeforeViewIndicatorMaintenanceReports($params);
        $this->MkCommon->_layout_file(array(
            'select',
            'freeze',
        ));
        $this->set(array(
            'values' => $values,
            'active_menu' => 'indicator_maintenance',
            '_freeze' => true,
        ));
    }
}