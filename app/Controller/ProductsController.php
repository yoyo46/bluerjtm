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
            $refine = $this->RjProduct->processRefine($this->request->data);
            $params = $this->RjProduct->generateSearchURL($refine);
            $params['action'] = $index;

            $this->redirect($params);
        }
        $this->redirect('/');
    }

	public function categories() {
        // if( in_array('view_leasing', $this->allowModule) ) {
            $this->loadModel('ProductCategory');
    		$this->set('active_menu', 'product_categories');
    		$this->set('sub_module_title', __('Kategori Barang'));
            $conditions = array();

            if(!empty($this->params['named'])){
                $refine = $this->params['named'];

                if(!empty($refine['category'])){
                    $value = urldecode($refine['category']);
                    $this->request->data['ProductCategory']['name'] = $value;
                    $conditions['ProductCategory.name LIKE'] = '%'.$value.'%';
                }
            }

            $this->paginate = $this->ProductCategory->getData('paginate', array(
                'conditions' => $conditions,
            ));
            $productCategories = $this->paginate('ProductCategory');

            $this->set('productCategories', $productCategories);
        // } else {
        //     $this->redirect($this->referer());
        // }
	}

    function category_add(){
        // if( in_array('insert_leasing', $this->allowModule) ) {
            $this->loadModel('ProductCategory');
            $this->set('sub_module_title', __('Tambah Kategori Barang'));
            $this->doProductCategory();
        // } else {
        //     $this->redirect($this->referer());
        // }
    }

    function category_edit($id){
        // if( in_array('update_leasing', $this->allowModule) ) {
            $this->loadModel('ProductCategory');
            $this->set('sub_module_title', 'Ubah Kategori Barang');
            $productCategory = $this->ProductCategory->getData('first', array(
                'conditions' => array(
                    'ProductCategory.id' => $id
                ),
            ));

            if(!empty($productCategory)){
                $this->doProductCategory($id, $productCategory);
            }else{
                $this->MkCommon->setCustomFlash(__('Kategori Barang tidak ditemukan'), 'error');  
                $this->redirect(array(
                    'controller' => 'products',
                    'action' => 'categories'
                ));
            }
        // } else {
        //     $this->redirect($this->referer());
        // }
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
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Kategori Barang'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s Kategori Barang'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );
                    $this->redirect(array(
                        'controller' => 'products',
                        'action' => 'categories'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Kategori Barang'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s Kategori Barang'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Kategori Barang'), $msg), 'error');
            }
        } else if( !empty($data_local) ){
            $this->request->data = $data_local;
        }

        $this->set(compact(
            'data_local'
        ));
        $this->set('active_menu', 'product_categories');
        $this->render('category_form');
    }

    function category_toggle($id){
        // if( in_array('delete_leasing', $this->allowModule) ) {
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
                    $this->Log->logActivity( sprintf(__('Sukses merubah status Kategori Barang ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal merubah status Kategori Barang ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                }
            }else{
                $this->MkCommon->setCustomFlash(__('Kategori Barang tidak ditemukan.'), 'error');
            }
        // }

        $this->redirect($this->referer());
    }

    function brands(){
        // if( in_array('view_leasing', $this->allowModule) ) {
            $this->loadModel('ProductBrand');
            $options = array();

            if(!empty($this->params['named'])){
                $refine = $this->params['named'];

                if(!empty($refine['name'])){
                    $name = urldecode($refine['name']);
                    $this->request->data['ProductBrand']['name'] = $name;
                    $options['conditions']['ProductBrand.name LIKE '] = '%'.$name.'%';
                }
            }

            $this->paginate = $this->ProductBrand->getData('paginate', $options);
            $productBrands = $this->paginate('ProductBrand');

            $this->set('active_menu', 'product_brands');
            $this->set('sub_module_title', __('Merk Barang'));
            $this->set('productBrands', $productBrands);
        // } else {
        //     $this->redirect($this->referer());
        // }
    }

    function brand_add(){
        // if( in_array('insert_leasing', $this->allowModule) ) {
            $this->loadModel('ProductBrand');
            $this->set('sub_module_title', __('Tambah Merk Barang'));
            $this->doProductBrand();
        // } else {
        //     $this->redirect($this->referer());
        // }
    }

    function brand_edit($id){
        // if( in_array('update_leasing', $this->allowModule) ) {
            $this->loadModel('ProductBrand');
            $this->set('sub_module_title', 'Ubah Merk Barang');
            $productBrand = $this->ProductBrand->getData('first', array(
                'conditions' => array(
                    'ProductBrand.id' => $id
                ),
            ));

            if(!empty($productBrand)){
                $this->doProductBrand($id, $productBrand);
            }else{
                $this->MkCommon->setCustomFlash(__('Merk Barang tidak ditemukan'), 'error');  
                $this->redirect(array(
                    'controller' => 'products',
                    'action' => 'brands'
                ));
            }
        // } else {
        //     $this->redirect($this->referer());
        // }
    }

    function doProductBrand($id = false, $data_local = false){
        if(!empty($this->request->data)){
            $data = $this->request->data;

            if($id && $data_local){
                $this->ProductBrand->id = $id;
                $msg = 'merubah';
            }else{
                $this->ProductBrand->create();
                $msg = 'menambah';
            }

            $this->ProductBrand->set($data);

            if( $this->ProductBrand->validates($data) ){
                if($this->ProductBrand->save($data)){
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Merk Barang'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s Merk Barang'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );
                    $this->redirect(array(
                        'controller' => 'products',
                        'action' => 'brands'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Merk Barang'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s Merk Barang'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Merk Barang'), $msg), 'error');
            }
        } else if( !empty($data_local) ){
            $this->request->data = $data_local;
        }

        $this->set(compact(
            'data_local'
        ));
        $this->set('active_menu', 'product_brands');
        $this->render('brand_form');
    }

    function brand_toggle($id){
        // if( in_array('delete_leasing', $this->allowModule) ) {
            $this->loadModel('ProductBrand');
            $locale = $this->ProductBrand->getData('first', array(
                'conditions' => array(
                    'ProductBrand.id' => $id
                )
            ));

            if($locale){
                $value = true;
                if($locale['ProductBrand']['status']){
                    $value = false;
                }

                $this->ProductBrand->id = $id;
                $this->ProductBrand->set('status', $value);

                if($this->ProductBrand->save()){
                    $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses merubah status Merk Barang ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal merubah status Merk Barang ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                }
            }else{
                $this->MkCommon->setCustomFlash(__('Merk Barang tidak ditemukan.'), 'error');
            }
        // }

        $this->redirect($this->referer());
    }

    function index(){
        // if( in_array('view_leasing', $this->allowModule) ) {
            $this->loadModel('Product');
            $options = array();

            if(!empty($this->params['named'])){
                $refine = $this->params['named'];

                if(!empty($refine['name'])){
                    $name = urldecode($refine['name']);
                    $this->request->data['ProductBrand']['name'] = $name;
                    $options['conditions']['ProductBrand.name LIKE '] = '%'.$name.'%';
                }
            }

            $this->paginate = $this->ProductBrand->getData('paginate', $options);
            $productBrands = $this->paginate('ProductBrand');

            $this->set('active_menu', 'product_brands');
            $this->set('sub_module_title', __('Merk Barang'));
            $this->set('productBrands', $productBrands);
        // } else {
        //     $this->redirect($this->referer());
        // }
    }

    function add(){
        // if( in_array('insert_leasing', $this->allowModule) ) {
            $this->loadModel('ProductBrand');
            $this->set('sub_module_title', __('Tambah Merk Barang'));
            $this->doProductBrand();
        // } else {
        //     $this->redirect($this->referer());
        // }
    }

    function edit($id){
        // if( in_array('update_leasing', $this->allowModule) ) {
            $this->loadModel('ProductBrand');
            $this->set('sub_module_title', 'Ubah Merk Barang');
            $productBrand = $this->ProductBrand->getData('first', array(
                'conditions' => array(
                    'ProductBrand.id' => $id
                ),
            ));

            if(!empty($productBrand)){
                $this->doProductBrand($id, $productBrand);
            }else{
                $this->MkCommon->setCustomFlash(__('Merk Barang tidak ditemukan'), 'error');  
                $this->redirect(array(
                    'controller' => 'products',
                    'action' => 'brands'
                ));
            }
        // } else {
        //     $this->redirect($this->referer());
        // }
    }

    function doProduct($id = false, $data_local = false){
        if(!empty($this->request->data)){
            $data = $this->request->data;

            if($id && $data_local){
                $this->ProductBrand->id = $id;
                $msg = 'merubah';
            }else{
                $this->ProductBrand->create();
                $msg = 'menambah';
            }

            $this->ProductBrand->set($data);

            if( $this->ProductBrand->validates($data) ){
                if($this->ProductBrand->save($data)){
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Merk Barang'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s Merk Barang'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );
                    $this->redirect(array(
                        'controller' => 'products',
                        'action' => 'brands'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Merk Barang'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s Merk Barang'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Merk Barang'), $msg), 'error');
            }
        } else if( !empty($data_local) ){
            $this->request->data = $data_local;
        }

        $this->set(compact(
            'data_local'
        ));
        $this->set('active_menu', 'product_brands');
        $this->render('brand_form');
    }

    function toggle($id){
        // if( in_array('delete_leasing', $this->allowModule) ) {
            $this->loadModel('ProductBrand');
            $locale = $this->ProductBrand->getData('first', array(
                'conditions' => array(
                    'ProductBrand.id' => $id
                )
            ));

            if($locale){
                $value = true;
                if($locale['ProductBrand']['status']){
                    $value = false;
                }

                $this->ProductBrand->id = $id;
                $this->ProductBrand->set('status', $value);

                if($this->ProductBrand->save()){
                    $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses merubah status Merk Barang ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal merubah status Merk Barang ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                }
            }else{
                $this->MkCommon->setCustomFlash(__('Merk Barang tidak ditemukan.'), 'error');
            }
        // }

        $this->redirect($this->referer());
    }
}