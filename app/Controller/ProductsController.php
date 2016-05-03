<?php
App::uses('AppController', 'Controller');
class ProductsController extends AppController {
	public $uses = array();

    public $components = array(
        'RjProduct'
    );

    function beforeFilter() {
        parent::beforeFilter();
        $this->set('title_for_layout', __('ERP RJTM | Barang'));
        $this->set('module_title', __('Barang'));
    }

    function search( $index = 'index' ){
        $refine = array();
        if(!empty($this->request->data)) {
            $data = $this->request->data;
            $refine = $this->RjProduct->processRefine($this->request->data);
            $params = $this->RjProduct->generateSearchURL($refine);
            $params = $this->MkCommon->getRefineGroupBranch($params, $data);
            $result = $this->MkCommon->processFilter($data);
            
            $params = array_merge($params, $result);
            $params['action'] = $index;

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
        $options = array();

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['name'])){
                $name = urldecode($refine['name']);
                $this->request->data['ProductUnit']['name'] = $name;
                $options['conditions']['ProductUnit.name LIKE '] = '%'.$name.'%';
            }
        }

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
        $this->loadModel('Product');

        $params = $this->MkCommon->_callRefineParams($this->params);
        $options =  $this->Product->_callRefineParams($params);

        $this->paginate = $this->Product->getData('paginate', $options);
        $values = $this->paginate('Product');

        if( !empty($values) ) {
            foreach ($values as $key => $value) {
                $product_unit_id = $this->MkCommon->filterEmptyField($value, 'Product', 'product_unit_id');
                $product_category_id = $this->MkCommon->filterEmptyField($value, 'Product', 'product_category_id');

                $value = $this->Product->ProductUnit->getMerge($value, $product_unit_id);
                $value = $this->Product->ProductCategory->getMerge($value, $product_category_id);
                $values[$key] = $value;
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
        $this->loadModel('Product');
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
        $this->loadModel('Product');
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
}