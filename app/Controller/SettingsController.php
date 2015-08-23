<?php
App::uses('AppController', 'Controller');
class SettingsController extends AppController {
	public $uses = array();

    public $components = array(
        'RjSetting', 'RjImage'
    );

    function beforeFilter() {
        parent::beforeFilter();
        $this->set('title_for_layout', __('ERP RJTM | Data Setting'));
        $this->set('module_title', __('Setting'));
    }

    function search( $index = 'index', $param_get = false ){
        $refine = array();

        if(!empty($this->request->data)) {
            $data = $this->request->data;
            $refine = $this->RjSetting->processRefine($data);
            $params = $this->RjSetting->processRequest($data);
            $params = array_merge($params, $this->RjSetting->generateSearchURL($refine));
            $params['action'] = $index;

            if( !empty($param_get) ) {
                $params[] = $param_get;
            }

            $this->redirect($params);
        }
        $this->redirect('/');
    }

    function cities(){
        $this->loadModel('City');
        $options = array();

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['name'])){
                $name = urldecode($refine['name']);
                $this->request->data['City']['name'] = $name;
                $options['conditions']['City.name LIKE '] = '%'.$name.'%';
            }
            if(!empty($refine['code'])){
                $value = urldecode($refine['code']);
                $this->request->data['City']['code'] = $value;
                $options['conditions']['City.code LIKE '] = '%'.$value.'%';
            }
        }

        $this->paginate = $this->City->getData('paginate', $options);
        $cities = $this->paginate('City');

        if(!empty($cities)){
            $this->loadModel('Region');
            foreach ($cities as $key => $city) {
                $cities[$key] = $this->Region->getMerge($city, $city['City']['region_id']);
            }
        }

        $this->set('active_menu', 'cities');
        $this->set('sub_module_title', 'Kota');
        $this->set('cities', $cities);
    }

    function city_add(){
        $this->set('sub_module_title', 'Tambah Kota');
        $this->docity();
    }

    function city_edit($id){
        $this->loadModel('City');
        $this->set('sub_module_title', 'Rubah Kota');
        $type_property = $this->City->getData('first', array(
            'conditions' => array(
                'City.id' => $id
            )
        ));

        if(!empty($type_property)){
            $this->docity($id, $type_property);
        }else{
            $this->MkCommon->setCustomFlash(__('Kota tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'settings',
                'action' => 'citys'
            ));
        }
    }

    function docity($id = false, $data_local = false){
        if(!empty($this->request->data)){
            $data = $this->request->data;
            $head_office = $this->MkCommon->filterEmptyField($data, 'City', 'is_head_office');

            if($id && $data_local){
                $this->City->id = $id;
                $msg = 'merubah';
            }else{
                $this->loadModel('City');
                $this->City->create();
                $msg = 'menambah';
            }

            $this->City->set($data);

            if($this->City->validates($data)){
                if($this->City->save($data)){
                    $id = $this->City->id;

                    if( !empty($head_office) ) {
                        $this->City->updateAll( array(
                            'City.is_head_office' => 0,
                        ), array(
                            'City.id NOT' => $id,
                        ));
                    }

                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Kota'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s Kota #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params );
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'cities'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Kota'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s Kota #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Kota'), $msg), 'error');
            }
        }else{
            
            if($id && $data_local){
                
                $this->request->data = $data_local;
            }
        }

        $this->loadModel('Region');
        $regions = $this->Region->getData('list', array(
            'conditions' => array(
                'Region.status' => 1
            ),
            'fields' => array(
                'Region.id', 'Region.name'
            )
        ));

        $this->set('active_menu', 'cities');
        $this->set(compact(
            'regions'
        ));
        $this->render('city_form');
    }

    function city_toggle($id){
        $this->loadModel('City');
        $locale = $this->City->getData('first', array(
            'conditions' => array(
                'City.id' => $id
            )
        ));

        if($locale){
            $value = true;
            if($locale['City']['status']){
                $value = false;
            }

            $this->City->id = $id;
            $this->City->set('status', $value);
            if($this->City->save()){
                $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
                $this->Log->logActivity( sprintf(__('Sukses merubah status Kota ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params ); 
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal merubah status Kota ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Kota tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    function customers(){
        $this->loadModel('Customer');
        $this->loadModel('CustomerGroup');

        $options = array(
            'paramType' => 'querystring',
            'contain' => array(
                'CustomerGroup',
            ),
        );

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['name'])){
                $name = urldecode($refine['name']);
                $this->request->data['Customer']['name'] = $name;
                $options['conditions']['Customer.name LIKE '] = '%'.$name.'%';
            }
            if(!empty($refine['customer_type_id'])){
                $customer_type_id = urldecode($refine['customer_type_id']);
                $this->request->data['Customer']['customer_type_id'] = $customer_type_id;
                $options['conditions']['Customer.customer_type_id '] = $customer_type_id;
            }
        }
        $this->paginate = $this->Customer->getData('paginate', $options, true, array(
            'status' => 'all',
        ));
        $truck_customers = $this->paginate('Customer');
        $customerTypes  = $this->Customer->CustomerType->getData('list', false, true);
        $customerGroups  = $this->CustomerGroup->getData('list');

        $this->set('active_menu', 'customers');
        $this->set('module_title', __('Data Master'));
        $this->set('sub_module_title', __('Customer'));
        $this->set(compact(
            'customerTypes', 'truck_customers',
            'customerGroups'
        ));
    }

    function customer_add(){
        $this->loadModel('Customer');
        $this->set('sub_module_title', 'Tambah Customer');
        $this->doCustomer();
    }

    function customer_edit($id){
        $this->loadModel('Customer');
        $this->set('sub_module_title', 'Rubah Customer');
        $customer = $this->Customer->getData('first', array(
            'conditions' => array(
                'Customer.id' => $id,
            ),
        ), true, array(
            'status' => 'all',
            // 'branch' => false,
        ));

        if(!empty($customer)){
            // Custom Otorisasi
            $branch_id = $this->MkCommon->filterEmptyField($customer, 'Customer', 'branch_id');
            $this->MkCommon->allowPage($branch_id);
            $this->doCustomer($id, $customer);
        }else{
            $this->MkCommon->setCustomFlash(__('Customer tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'settings',
                'action' => 'customers'
            ));
        }
    }

    function doCustomer($id = false, $data_local = false){
        if(!empty($this->request->data)){
            $data = $this->request->data;
            if($id && $data_local){
                $this->Customer->id = $id;
                $msg = 'merubah';
            }else{
                $this->Customer->create();
                $msg = 'menambah';
            }
                        
            $data['Customer']['bank_id'] = !empty($data['Customer']['bank_id'])?$data['Customer']['bank_id']:0;
            $data['Customer']['billing_id'] = !empty($data['Customer']['billing_id'])?$data['Customer']['billing_id']:0;
            $data['Customer']['branch_id'] = Configure::read('__Site.config_branch_id');
            $this->Customer->set($data);

            if($this->Customer->validates($data)){
                if( $this->Customer->save($data) ){
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Customer'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s Customer #%s'), $msg, $this->Customer->id), $this->user_data, $this->RequestHandler, $this->params ); 

                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'customers'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Customer'), $msg), 'error');  
                    $this->Log->logActivity( sprintf(__('Gagal %s Customer #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Customer'), $msg), 'error');
            }
        }else{
            
            if($id && $data_local){
                
                $this->request->data = $data_local;
            }
        }

        $this->loadModel('Bank');
        $this->loadModel('CustomerGroup');

        $customerTypes  = $this->Customer->CustomerType->getData('list', false, true);
        $customerGroups  = $this->CustomerGroup->getData('list');
        $banks  = $this->Bank->getData('list', array(
            'fields' => array(
                'Bank.id', 'Bank.bank_name'
            ),
        ));
        $billings  = $this->User->Employe->getData('list', array(
            'fields' => array(
                'User.id', 'Employe.full_name'
            ),
            'conditions' => array(
                'User.status' => 1,
            ),
            'contain' => array(
                'User',
            ),
        ));

        $this->set('active_menu', 'customers');
        $this->set('module_title', 'Data Master');
        $this->set(compact(
            'customerTypes', 'customerGroups', 'banks',
            'billings'
        ));
        $this->render('customer_form');
    }

    function customer_toggle($id){
        $this->loadModel('Customer');

        $locale = $this->Customer->getData('first', array(
            'conditions' => array(
                'Customer.id' => $id
            )
        ), true, array(
            'status' => 'all',
            // 'branch' => false,
        ));

        if( !empty($locale) ){
            $value = true;
            // Custom Otorisasi
            $branch_id = $this->MkCommon->filterEmptyField($locale, 'Customer', 'branch_id');            
            $this->MkCommon->allowPage($branch_id);

            if( !empty($locale['Customer']['status']) ){
                $value = false;
            }

            $this->Customer->id = $id;
            $this->Customer->set('status', $value);

            if($this->Customer->save()){
                $this->MkCommon->setCustomFlash(__('Customer telah berhasil dihapus.'), 'success');
                $this->Log->logActivity( sprintf(__('Customer ID #%s telah berhasil dihapus.'), $id), $this->user_data, $this->RequestHandler, $this->params ); 
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal menghapus Customer.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal menghapus Customer ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Customer tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    public function customer_types() {
        $this->loadModel('CustomerType');
        $options = array();

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['name'])){
                $name = urldecode($refine['name']);
                $this->request->data['CustomerType']['name'] = $name;
                $options['conditions']['CustomerType.name LIKE '] = '%'.$name.'%';
            }
        }
        $this->paginate = $this->CustomerType->getData('paginate', $options);
        $customerTypes = $this->paginate('CustomerType');

        $this->set('active_menu', 'customer_types');
        $this->set('module_title', 'Data Master');
        $this->set('sub_module_title', 'Tipe Customer');
        $this->set('customerTypes', $customerTypes);        
    }

    function customer_type_add(){
        $this->set('sub_module_title', 'Tambah Tipe Customer');
        $this->doCustomerType();
    }

    function customer_type_edit($id){
        $this->loadModel('CustomerType');
        $this->set('sub_module_title', 'Rubah Tipe Customer');
        $customerType = $this->CustomerType->getData('first', array(
            'conditions' => array(
                'CustomerType.id' => $id
            )
        ));

        if(!empty($customerType)){
            $this->doCustomerType($id, $customerType);
        }else{
            $this->MkCommon->setCustomFlash(__('Tipe Customer tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'settings',
                'action' => 'customer_types'
            ));
        }
    }

    function doCustomerType($id = false, $data_local = false){
        if(!empty($this->request->data)){
            $data = $this->request->data;
            if($id && $data_local){
                $this->CustomerType->id = $id;
                $msg = 'merubah';
            }else{
                $this->loadModel('CustomerType');
                $this->CustomerType->create();
                $msg = 'menambah';
            }
            
            $this->CustomerType->set($data);

            if($this->CustomerType->validates($data)){
                if($this->CustomerType->save($data)){
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Tipe Customer'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s Tipe Customer #%s'), $msg, $this->CustomerType->id), $this->user_data, $this->RequestHandler, $this->params ); 
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'customer_types'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Tipe Customer'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s Tipe Customer #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1 );  
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Tipe Customer'), $msg), 'error');
            }
        }else{
            if($id && $data_local){
                $this->request->data = $data_local;
            }
        }

        $this->set('active_menu', 'customer_types');
        $this->set('module_title', 'Data Master');
        $this->render('customer_type_form');
    }

    function customer_type_toggle($id){
        $this->loadModel('CustomerType');
        $locale = $this->CustomerType->getData('first', array(
            'conditions' => array(
                'CustomerType.id' => $id
            )
        ));

        if($locale){
            $value = true;
            if($locale['CustomerType']['status']){
                $value = false;
            }

            $this->CustomerType->id = $id;
            $this->CustomerType->set('status', $value);
            if($this->CustomerType->save()){
                $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
                $this->Log->logActivity( sprintf(__('Sukses merubah status Tipe customer #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params );  
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal merubah status Tipe customer #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );  
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Tipe Customer tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    function vendors(){
        $this->loadModel('Vendor');
        $options = array();

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['name'])){
                $name = urldecode($refine['name']);
                $this->request->data['Vendor']['name'] = $name;
                $options['conditions']['Vendor.name LIKE '] = '%'.$name.'%';
            }
        }
        $this->paginate = $this->Vendor->getData('paginate', $options);
        $vendors = $this->paginate('Vendor');

        $this->set('active_menu', 'vendors');
        $this->set('module_title', 'Data Master');
        $this->set('sub_module_title', 'Vendor');
        $this->set(compact(
            'vendors'
        ));
    }

    function vendor_add(){
        $this->loadModel('Vendor');
        $this->set('sub_module_title', 'Tambah Vendor');
        $this->doVendor();
    }

    function vendor_edit($id){
        $this->loadModel('Vendor');
        $this->set('sub_module_title', 'Rubah Vendor');
        $vendor = $this->Vendor->getData('first', array(
            'conditions' => array(
                'Vendor.id' => $id
            )
        ));

        if(!empty($vendor)){
            $this->doVendor($id, $vendor);
        }else{
            $this->MkCommon->setCustomFlash(__('Vendor tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'settings',
                'action' => 'vendors'
            ));
        }
    }

    function doVendor($id = false, $data_local = false){
        if(!empty($this->request->data)){
            $data = $this->request->data;
            $data['Vendor']['branch_id'] = Configure::read('__Site.config_branch_id');
            
            if($id && $data_local){
                $this->Vendor->id = $id;
                $msg = 'merubah';
            }else{
                $this->Vendor->create();
                $msg = 'menambah';
            }
            
            $this->Vendor->set($data);

            if($this->Vendor->validates($data)){
                if($this->Vendor->save($data)){
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Vendor'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s Vendor #%s'), $msg, $this->Vendor->id), $this->user_data, $this->RequestHandler, $this->params );  
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'vendors'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Vendor'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s Vendor #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1 );   
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Vendor'), $msg), 'error');
            }
        }else{
            if($id && $data_local){
                $this->request->data = $data_local;
            }
        }

        $this->set('active_menu', 'vendors');
        $this->set('module_title', 'Data Master');
        $this->render('vendor_form');
    }

    function vendor_toggle($id){
        $this->loadModel('Vendor');
        $locale = $this->Vendor->getData('first', array(
            'conditions' => array(
                'Vendor.id' => $id
            )
        ));

        if($locale){
            $value = true;
            if($locale['Vendor']['status']){
                $value = false;
            }

            $this->Vendor->id = $id;
            $this->Vendor->set('status', $value);
            if($this->Vendor->save()){
                $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
                $this->Log->logActivity( sprintf(__('Sukses merubah status vendor ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params );   
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal merubah status vendor ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );   
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Vendor tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    public function coas() {
        $this->loadModel('Coa');
        $coas = $this->Coa->getData('threaded', array(
            'conditions' => array(
                'Coa.status' => 1
            ),
            'order' => array(
                'Coa.code IS NULL' => 'ASC',
                'Coa.code' => 'ASC',
            )
        ));

        $this->set('active_menu', 'coas');
        $this->set('module_title', 'Data Master');
        $this->set('sub_module_title', 'COA ( Chart Of Account )');
        $this->set('coas', $coas);
    }

    public function coa_add( $parent_id = false ) {
        $this->loadModel('Coa');
        $coa = false;

        if( !empty($parent_id) ) {
            $coa = $this->Coa->getData('first', array(
                'conditions' => array(
                    'Coa.status' => 1,
                    'Coa.id' => $parent_id
                ),
            ));

            if( !empty($coa) ) {
                $this->set('coa', $coa);
                $this->set('sub_module_title', sprintf(__('Tambah COA - %s', $coa['Coa']['name'])));
            } else {
                $this->MkCommon->setCustomFlash(__('Coa tidak ditemukan.'), 'error');
                $this->redirect($this->referer());
            }
        } else {
            $this->set('sub_module_title', 'Tambah COA');
        }

        $this->doCoa( false, false, $parent_id, $coa );
    }

    public function coa_edit( $id = false, $parent_id = false ) {
        $this->loadModel('Coa');
        $coa = false;

        if( !empty($id) ) {
            $coa_current = $this->Coa->getData('first', array(
                'conditions' => array(
                    'Coa.id' => $id,
                    'Coa.status' => 1,
                ),
            ));

            if( !empty($coa_current) ) {
                $coa = $this->Coa->getData('first', array(
                    'conditions' => array(
                        'Coa.id' => $parent_id,
                        'Coa.status' => 1,
                    ),
                ));
                $this->set('sub_module_title', 'Rubah COA');
                $this->set('coa', $coa);
                $this->doCoa( $id, $coa_current, $parent_id, $coa );
            } else {
                $this->MkCommon->setCustomFlash(__('Coa tidak ditemukan.'), 'error');
                $this->redirect($this->referer());
            }
        } 
    }

    function doCoa($id = false, $data_local = false, $parent_id = false, $coa = false ){
        if(!empty($this->request->data)){
            $data = $this->request->data;

            if($id && $data_local){
                $this->Coa->id = $id;
                $msg = 'merubah';
                $data['Coa']['level'] = $data_local['Coa']['level'];
                $data['Coa']['id'] = $id;
            }else{
                $this->Coa->create();
                $msg = 'menambah';
            }

            if( !empty($coa) ) {
                $data['Coa']['parent_id'] = $parent_id;

                if( !empty($coa['Coa']['code']) ) {
                    $coa_parent = $coa['Coa']['code'];
                    if(!empty($coa['Coa']['with_parent_code'])){
                        $coa_parent = $coa['Coa']['with_parent_code'];
                    }
                    $data['Coa']['with_parent_code'] = sprintf('%s-%s', $coa_parent, $data['Coa']['code']);
                }

                if(!empty($coa['Coa']['type'])){
                    $data['Coa']['type'] = $coa['Coa']['type'];
                }

                if(!empty($coa['Coa']['level']) && empty($id) && empty($data_local)){
                    $data['Coa']['level'] = $coa['Coa']['level']+1;
                }
            }

            if(isset($data['Coa']['balance'])){
                $data['Coa']['balance'] = $this->MkCommon->convertPriceToString($data['Coa']['balance'], 0);
            }
            
            $this->Coa->set($data);

            if($this->Coa->validates($data)){
                if($this->Coa->save($data)){
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Coa'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s Coa #%s'), $msg, $this->Coa->id), $this->user_data, $this->RequestHandler, $this->params );   
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'coas'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Coa'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s Coa #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1 );   
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Coa'), $msg), 'error');
            }
        }else{
            if($id && $data_local){
                $this->request->data = $data_local;
            }
        }

        $this->set('active_menu', 'coas');
        $this->set('module_title', 'Data Master');
        $this->set('parent_id', $parent_id);
        $this->render('coa_form');
    }

    function companies(){
        $this->loadModel('Company');
        $options = array();

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['name'])){
                $name = urldecode($refine['name']);
                $this->request->data['Company']['name'] = $name;
                $options['conditions']['Company.name LIKE '] = '%'.$name.'%';
            }
        }
        $this->paginate = $this->Company->getData('paginate', $options);
        $companies = $this->paginate('Company');

        $this->set('active_menu', 'companies');
        $this->set('module_title', 'Data Master');
        $this->set('sub_module_title', 'Company');
        $this->set(compact(
            'companies'
        ));
    }

    function company_add(){
        $this->loadModel('Company');
        $this->set('sub_module_title', 'Tambah Company');
        $this->doCompany();
    }

    function company_edit($id){
        $this->loadModel('Company');
        $this->set('sub_module_title', 'Rubah Company');
        $company = $this->Company->getData('first', array(
            'conditions' => array(
                'Company.id' => $id
            )
        ));

        if(!empty($company)){
            $this->doCompany($id, $company);
        }else{
            $this->MkCommon->setCustomFlash(__('Company tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'settings',
                'action' => 'companies'
            ));
        }
    }

    function doCompany($id = false, $data_local = false){
        if(!empty($this->request->data)){
            $data = $this->request->data;
            if($id && $data_local){
                $this->Company->id = $id;
                $msg = 'merubah';
            }else{
                $this->Company->create();
                $msg = 'menambah';
            }
            
            $this->Company->set($data);

            if($this->Company->validates($data)){
                if($this->Company->save($data)){
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Company'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s Company #%s'), $msg, $this->Company->id), $this->user_data, $this->RequestHandler, $this->params );
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'companies'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Company'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s Company #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1 );    
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Company'), $msg), 'error');
            }
        }else{
            
            if($id && $data_local){
                
                $this->request->data = $data_local;
            }
        }

        $this->set('active_menu', 'companies');
        $this->set('module_title', 'Data Master');
        $this->render('company_form');
    }

    function company_toggle($id){
        $this->loadModel('Company');
        $locale = $this->Company->getData('first', array(
            'conditions' => array(
                'Company.id' => $id
            )
        ));

        if($locale){
            $value = true;
            if($locale['Company']['status']){
                $value = false;
            }

            $this->Company->id = $id;
            $this->Company->set('status', $value);
            if($this->Company->save()){
                $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
                $this->Log->logActivity( sprintf(__('Sukses merubah status company ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params );
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal merubah status company ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );    
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Company tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    public function uang_jalan() {
        $this->loadModel('UangJalan');
        $this->loadModel('City');

        $options = array();

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['name'])){
                $name = urldecode($refine['name']);
                $this->request->data['UangJalan']['name'] = $name;
                $options['conditions']['UangJalan.title LIKE '] = '%'.$name.'%';
            }
            if(!empty($refine['capacity'])){
                $capacity = urldecode($refine['capacity']);
                $this->request->data['UangJalan']['capacity'] = $capacity;
                $options['conditions']['UangJalan.capacity LIKE'] = '%'.$capacity.'%';
            }

            if(!empty($refine['from'])){
                $name = urldecode($refine['from']);
                $this->request->data['UangJalan']['from_city'] = $name;
                $city_id = $this->City->getData('list', array(
                    'conditions' => array(
                        'City.name LIKE' => '%'.$name.'%',
                    ),
                    'fields' => array(
                        'City.id', 'City.id',
                    ),
                ), true, array(
                    'all',
                ));
                $options['conditions']['UangJalan.from_city_id'] = $city_id;
            }
            if(!empty($refine['to'])){
                $name = urldecode($refine['to']);
                $this->request->data['UangJalan']['to_city'] = $name;
                $city_id = $this->City->getData('list', array(
                    'conditions' => array(
                        'City.name LIKE' => '%'.$name.'%',
                    ),
                    'fields' => array(
                        'City.id', 'City.id',
                    ),
                ), true, array(
                    'all',
                ));
                $options['conditions']['UangJalan.to_city_id'] = $city_id;
            }
        }
        $this->paginate = $this->UangJalan->getData('paginate', $options);
        $uangJalans = $this->paginate('UangJalan');

        if( !empty($uangJalans) ) {
            foreach ($uangJalans as $key => $uangJalan) {
                $from_city_id = !empty($uangJalan['UangJalan']['from_city_id'])?$uangJalan['UangJalan']['from_city_id']:false;
                $to_city_id = !empty($uangJalan['UangJalan']['to_city_id'])?$uangJalan['UangJalan']['to_city_id']:false;
                $uangJalan = $this->City->getMerge($uangJalan, $from_city_id, 'FromCity');
                $uangJalan = $this->City->getMerge($uangJalan, $to_city_id, 'ToCity');
                $uangJalans[$key] = $uangJalan;
            }
        }

        $this->set('active_menu', 'uang_jalan');
        $this->set('module_title', __('TTUJ'));
        $this->set('sub_module_title', 'Uang Jalan');


        $this->set(compact(
            'uangJalans'
        ));
    }

    public function uang_jalan_add() {
        $this->loadModel('UangJalan');
        $this->set('sub_module_title', 'Tambah Uang Jalan');
        $this->doUangJalan();
    }

    function saveGroupMotor ( $data = false, $uang_jalan_id = false ) {
        $result = array(
            'validates' => true,
            'data' => false,
        );

        if( !empty($data['UangJalanTipeMotor']['group_motor_id']) ) {
            foreach ($data['UangJalanTipeMotor']['group_motor_id'] as $key => $group_motor_id) {
                $dataValidate['UangJalanTipeMotor']['group_motor_id'] = $group_motor_id;
                $dataValidate['UangJalanTipeMotor']['uang_jalan_1'] = !empty($data['UangJalanTipeMotor']['uang_jalan_1'][$key])?$this->MkCommon->convertPriceToString($data['UangJalanTipeMotor']['uang_jalan_1'][$key], 0):0;
                // $dataValidate['UangJalanTipeMotor']['uang_kuli_muat'] = !empty($data['UangJalanTipeMotor']['uang_kuli_muat'][$key])?$this->MkCommon->convertPriceToString($data['UangJalanTipeMotor']['uang_kuli_muat'][$key], 0):0;
                // $dataValidate['UangJalanTipeMotor']['uang_kuli_bongkar'] = !empty($data['UangJalanTipeMotor']['uang_kuli_bongkar'][$key])?$this->MkCommon->convertPriceToString($data['UangJalanTipeMotor']['uang_kuli_bongkar'][$key], 0):0;
                
                $this->UangJalan->UangJalanTipeMotor->set($dataValidate);

                if( !empty($uang_jalan_id) ) {
                    $dataValidate['UangJalanTipeMotor']['uang_jalan_id'] = $uang_jalan_id;
                    $this->UangJalan->UangJalanTipeMotor->create();
                    $this->UangJalan->UangJalanTipeMotor->save($dataValidate);
                } else {
                    if(!$this->UangJalan->UangJalanTipeMotor->validates($dataValidate)){
                        $result['validates'] = false;
                    } else {
                        $result['data'][$key] = $dataValidate;
                    }
                }
            }
        }

        return $result;
    }

    function saveCommissionGroupMotor ( $data = false, $uang_jalan_id = false ) {
        $result = array(
            'validates' => true,
            'data' => false,
        );

        if( !empty($data['CommissionGroupMotor']['group_motor_id']) ) {
            foreach ($data['CommissionGroupMotor']['group_motor_id'] as $key => $group_motor_id) {
                $dataValidate['CommissionGroupMotor']['group_motor_id'] = $group_motor_id;
                $dataValidate['CommissionGroupMotor']['commission'] = !empty($data['CommissionGroupMotor']['commission'][$key])?$this->MkCommon->convertPriceToString($data['CommissionGroupMotor']['commission'][$key], 0):0;
                
                $this->UangJalan->CommissionGroupMotor->set($dataValidate);

                if( !empty($uang_jalan_id) ) {
                    $dataValidate['CommissionGroupMotor']['uang_jalan_id'] = $uang_jalan_id;
                    $this->UangJalan->CommissionGroupMotor->create();
                    $this->UangJalan->CommissionGroupMotor->save($dataValidate);
                } else {
                    if(!$this->UangJalan->CommissionGroupMotor->validates($dataValidate)){
                        $result['validates'] = false;
                    } else {
                        $result['data'][$key] = $dataValidate;
                    }
                }
            }
        }

        return $result;
    }

    function saveAsdpGroupMotor ( $data = false, $uang_jalan_id = false ) {
        $result = array(
            'validates' => true,
            'data' => false,
        );

        if( !empty($data['AsdpGroupMotor']['group_motor_id']) ) {
            foreach ($data['AsdpGroupMotor']['group_motor_id'] as $key => $group_motor_id) {
                $dataValidate['AsdpGroupMotor']['group_motor_id'] = $group_motor_id;
                $dataValidate['AsdpGroupMotor']['asdp'] = !empty($data['AsdpGroupMotor']['asdp'][$key])?$this->MkCommon->convertPriceToString($data['AsdpGroupMotor']['asdp'][$key], 0):0;

                $this->UangJalan->AsdpGroupMotor->set($dataValidate);

                if( !empty($uang_jalan_id) ) {
                    $dataValidate['AsdpGroupMotor']['uang_jalan_id'] = $uang_jalan_id;
                    $this->UangJalan->AsdpGroupMotor->create();
                    $this->UangJalan->AsdpGroupMotor->save($dataValidate);
                } else {
                    if(!$this->UangJalan->AsdpGroupMotor->validates($dataValidate)){
                        $result['validates'] = false;
                    } else {
                        $result['data'][$key] = $dataValidate;
                    }
                }
            }
        }

        return $result;
    }

    function saveUangKawalGroupMotor ( $data = false, $uang_jalan_id = false ) {
        $result = array(
            'validates' => true,
            'data' => false,
        );

        if( !empty($data['UangKawalGroupMotor']['group_motor_id']) ) {
            foreach ($data['UangKawalGroupMotor']['group_motor_id'] as $key => $group_motor_id) {
                $dataValidate['UangKawalGroupMotor']['group_motor_id'] = $group_motor_id;
                $dataValidate['UangKawalGroupMotor']['uang_kawal'] = !empty($data['UangKawalGroupMotor']['uang_kawal'][$key])?$this->MkCommon->convertPriceToString($data['UangKawalGroupMotor']['uang_kawal'][$key], 0):0;

                $this->UangJalan->UangKawalGroupMotor->set($dataValidate);

                if( !empty($uang_jalan_id) ) {
                    $dataValidate['UangKawalGroupMotor']['uang_jalan_id'] = $uang_jalan_id;
                    $this->UangJalan->UangKawalGroupMotor->create();
                    $this->UangJalan->UangKawalGroupMotor->save($dataValidate);
                } else {
                    if(!$this->UangJalan->UangKawalGroupMotor->validates($dataValidate)){
                        $result['validates'] = false;
                    } else {
                        $result['data'][$key] = $dataValidate;
                    }
                }
            }
        }

        return $result;
    }

    function saveUangKeamananGroupMotor ( $data = false, $uang_jalan_id = false ) {
        $result = array(
            'validates' => true,
            'data' => false,
        );

        if( !empty($data['UangKeamananGroupMotor']['group_motor_id']) ) {
            foreach ($data['UangKeamananGroupMotor']['group_motor_id'] as $key => $group_motor_id) {
                $dataValidate['UangKeamananGroupMotor']['group_motor_id'] = $group_motor_id;
                $dataValidate['UangKeamananGroupMotor']['uang_keamanan'] = !empty($data['UangKeamananGroupMotor']['uang_keamanan'][$key])?$this->MkCommon->convertPriceToString($data['UangKeamananGroupMotor']['uang_keamanan'][$key], 0):0;

                $this->UangJalan->UangKeamananGroupMotor->set($dataValidate);

                if( !empty($uang_jalan_id) ) {
                    $dataValidate['UangKeamananGroupMotor']['uang_jalan_id'] = $uang_jalan_id;
                    $this->UangJalan->UangKeamananGroupMotor->create();
                    $this->UangJalan->UangKeamananGroupMotor->save($dataValidate);
                } else {
                    if(!$this->UangJalan->UangKeamananGroupMotor->validates($dataValidate)){
                        $result['validates'] = false;
                    } else {
                        $result['data'][$key] = $dataValidate;
                    }
                }
            }
        }

        return $result;
    }

    function saveUangKuliGroupMotor ( $data = false, $uang_kuli_id = false ) {
        $result = array(
            'validates' => true,
            'data' => false,
        );

        if( !empty($data['UangKuliGroupMotor']['group_motor_id']) ) {
            foreach ($data['UangKuliGroupMotor']['group_motor_id'] as $key => $group_motor_id) {
                $dataValidate['UangKuliGroupMotor']['group_motor_id'] = !empty($group_motor_id)?$group_motor_id:'';
                $dataValidate['UangKuliGroupMotor']['uang_kuli'] = !empty($data['UangKuliGroupMotor']['uang_kuli'][$key])?$this->MkCommon->convertPriceToString($data['UangKuliGroupMotor']['uang_kuli'][$key], 0):'';
                
                $this->UangKuli->UangKuliGroupMotor->set($dataValidate);

                if( !empty($uang_kuli_id) ) {
                    $dataValidate['UangKuliGroupMotor']['uang_kuli_id'] = $uang_kuli_id;
                    $this->UangKuli->UangKuliGroupMotor->create();
                    $this->UangKuli->UangKuliGroupMotor->save($dataValidate);
                } else {
                    if(!$this->UangKuli->UangKuliGroupMotor->validates($dataValidate)){
                        $result['validates'] = false;
                    } else {
                        $result['data'][$key] = $dataValidate;
                    }
                }
            }
        }

        return $result;
    }

    function saveUangKuliCapacity ( $data = false, $uang_kuli_id = false ) {
        $result = array(
            'validates' => true,
            'data' => false,
        );

        if( !empty($data['UangKuliCapacity']['capacity']) ) {
            foreach ($data['UangKuliCapacity']['capacity'] as $key => $capacity) {
                $dataValidate['UangKuliCapacity']['capacity'] = !empty($capacity)?$capacity:0;
                $dataValidate['UangKuliCapacity']['uang_kuli'] = !empty($data['UangKuliCapacity']['uang_kuli'][$key])?$this->MkCommon->convertPriceToString($data['UangKuliCapacity']['uang_kuli'][$key], 0):'';
                
                $this->UangKuli->UangKuliCapacity->set($dataValidate);

                if( !empty($uang_kuli_id) ) {
                    $dataValidate['UangKuliCapacity']['uang_kuli_id'] = $uang_kuli_id;
                    $this->UangKuli->UangKuliCapacity->create();
                    $this->UangKuli->UangKuliCapacity->save($dataValidate);
                } else {
                    if(!$this->UangKuli->UangKuliCapacity->validates($dataValidate)){
                        $result['validates'] = false;
                    } else {
                        $result['data'][$key] = $dataValidate;
                    }
                }
            }
        }

        return $result;
    }

    function doUangJalan($id = false, $data_local = false){
        $this->loadModel('City');
        $this->loadModel('GroupMotor');

        if(!empty($this->request->data)){
            $data = $this->request->data;
            if($id && $data_local){
                $this->UangJalan->id = $id;
                $msg = 'merubah';
            }else{
                $this->UangJalan->create();
                $msg = 'menambah';
            }

            $data['UangJalan']['commission'] = $this->MkCommon->convertPriceToString($data['UangJalan']['commission'], 0);
            $data['UangJalan']['commission_extra'] = $this->MkCommon->convertPriceToString($data['UangJalan']['commission_extra'], 0);
            $data['UangJalan']['commission_min_qty'] = $this->MkCommon->convertPriceToString($data['UangJalan']['commission_min_qty'], 0);
            $data['UangJalan']['uang_jalan_1'] = $this->MkCommon->convertPriceToString($data['UangJalan']['uang_jalan_1']);
            $data['UangJalan']['uang_jalan_2'] = $this->MkCommon->convertPriceToString($data['UangJalan']['uang_jalan_2'], 0);
            // $data['UangJalan']['uang_kuli_muat'] = $this->MkCommon->convertPriceToString($data['UangJalan']['uang_kuli_muat'], 0);
            // $data['UangJalan']['uang_kuli_bongkar'] = $this->MkCommon->convertPriceToString($data['UangJalan']['uang_kuli_bongkar'], 0);
            $data['UangJalan']['asdp'] = $this->MkCommon->convertPriceToString($data['UangJalan']['asdp'], 0);
            $data['UangJalan']['uang_kawal'] = $this->MkCommon->convertPriceToString($data['UangJalan']['uang_kawal'], 0);
            $data['UangJalan']['uang_keamanan'] = $this->MkCommon->convertPriceToString($data['UangJalan']['uang_keamanan'], 0);
            $data['UangJalan']['uang_jalan_extra'] = $this->MkCommon->convertPriceToString($data['UangJalan']['uang_jalan_extra'], 0);
            $data['UangJalan']['group_classification_1_id'] = !empty($data['UangJalan']['group_classification_1_id'])?$data['UangJalan']['group_classification_1_id']:0;
            $data['UangJalan']['group_classification_2_id'] = !empty($data['UangJalan']['group_classification_2_id'])?$data['UangJalan']['group_classification_2_id']:0;
            $data['UangJalan']['group_classification_3_id'] = !empty($data['UangJalan']['group_classification_3_id'])?$data['UangJalan']['group_classification_3_id']:0;
            $data['UangJalan']['group_classification_4_id'] = !empty($data['UangJalan']['group_classification_4_id'])?$data['UangJalan']['group_classification_4_id']:0;
            $data['UangJalan']['branch_id'] = Configure::read('__Site.config_branch_id');

            if( !empty($data['UangJalan']['uang_jalan_per_unit']) ) {
                $data['UangJalan']['uang_jalan_2'] = 0;
            }
            
            $this->UangJalan->set($data);

            if($this->UangJalan->validates($data)){
                $saveGroupMotor = false;
                $saveCommissionGroupMotor = false;
                $saveAsdpGroupMotor = false;
                $saveUangKawalGroupMotor = false;
                $saveUangKeamananGroupMotor = false;

                if( !empty($data['UangJalanTipeMotor']['group_motor_id']) ) {
                    $resultGroupMotor = $this->saveGroupMotor($data);
                    $saveGroupMotor = !empty($resultGroupMotor['validates'])?$resultGroupMotor['validates']:false;
                } else {
                    $saveGroupMotor = true;
                }

                if( !empty($data['CommissionGroupMotor']['group_motor_id']) ) {
                    $resultCommissionGroupMotor = $this->saveCommissionGroupMotor($data);
                    $saveCommissionGroupMotor = !empty($resultCommissionGroupMotor['validates'])?$resultCommissionGroupMotor['validates']:false;
                } else {
                    $saveCommissionGroupMotor = true;
                }

                if( !empty($data['AsdpGroupMotor']['group_motor_id']) ) {
                    $resultAsdpGroupMotor = $this->saveAsdpGroupMotor($data);
                    $saveAsdpGroupMotor = !empty($resultAsdpGroupMotor['validates'])?$resultAsdpGroupMotor['validates']:false;
                } else {
                    $saveAsdpGroupMotor = true;
                }

                if( !empty($data['UangKawalGroupMotor']['group_motor_id']) ) {
                    $resultUangKawalGroupMotor = $this->saveUangKawalGroupMotor($data);
                    $saveUangKawalGroupMotor = !empty($resultUangKawalGroupMotor['validates'])?$resultUangKawalGroupMotor['validates']:false;
                } else {
                    $saveUangKawalGroupMotor = true;
                }

                if( !empty($data['UangKeamananGroupMotor']['group_motor_id']) ) {
                    $resultUangKeamananGroupMotor = $this->saveUangKeamananGroupMotor($data);
                    $saveUangKeamananGroupMotor = !empty($resultUangKeamananGroupMotor['validates'])?$resultUangKeamananGroupMotor['validates']:false;
                } else {
                    $saveUangKeamananGroupMotor = true;
                }

                if( $saveGroupMotor && $saveCommissionGroupMotor && $saveAsdpGroupMotor && $saveUangKawalGroupMotor && $saveUangKeamananGroupMotor && $this->UangJalan->save($data) ){
                    if( !empty($id) ) {
                        $this->UangJalan->UangJalanTipeMotor->updateAll( array(
                            'UangJalanTipeMotor.status' => 0,
                        ), array(
                            'UangJalanTipeMotor.uang_jalan_id' => $id,
                        ));
                        $this->UangJalan->CommissionGroupMotor->updateAll( array(
                            'CommissionGroupMotor.status' => 0,
                        ), array(
                            'CommissionGroupMotor.uang_jalan_id' => $id,
                        ));
                        $this->UangJalan->AsdpGroupMotor->updateAll( array(
                            'AsdpGroupMotor.status' => 0,
                        ), array(
                            'AsdpGroupMotor.uang_jalan_id' => $id,
                        ));
                        $this->UangJalan->UangKawalGroupMotor->updateAll( array(
                            'UangKawalGroupMotor.status' => 0,
                        ), array(
                            'UangKawalGroupMotor.uang_jalan_id' => $id,
                        ));
                        $this->UangJalan->UangKeamananGroupMotor->updateAll( array(
                            'UangKeamananGroupMotor.status' => 0,
                        ), array(
                            'UangKeamananGroupMotor.uang_jalan_id' => $id,
                        ));
                    }

                    if( !empty($data['UangJalan']['uang_jalan_per_unit']) ) {
                        $this->saveGroupMotor($data, $this->UangJalan->id);
                    }

                    if( !empty($data['UangJalan']['commission_per_unit']) ) {
                        $this->saveCommissionGroupMotor($data, $this->UangJalan->id);
                    }

                    if( !empty($data['UangJalan']['asdp_per_unit']) ) {
                        $this->saveAsdpGroupMotor($data, $this->UangJalan->id);
                    }

                    if( !empty($data['UangJalan']['uang_kawal_per_unit']) ) {
                        $this->saveUangKawalGroupMotor($data, $this->UangJalan->id);
                    }

                    if( !empty($data['UangJalan']['uang_keamanan_per_unit']) ) {
                        $this->saveUangKeamananGroupMotor($data, $this->UangJalan->id);
                    }

                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Uang jalan'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s Uang jalan #%s'), $msg, $this->UangJalan->id), $this->user_data, $this->RequestHandler, $this->params );
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'uang_jalan'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Uang jalan'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s Uang jalan #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1 );     
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Uang jalan'), $msg), 'error');
            }
        }else{
            if($id && $data_local){
                $data_local = $this->UangJalan->gerMergeBiaya( $data_local, $id );
                $data_local = $this->MkCommon->getUangJalanGroupMotor($data_local);
                $this->request->data = $data_local;
                $this->request->data['UangJalan']['commission_min_qty'] = !empty($this->request->data['UangJalan']['commission_min_qty'])?$this->request->data['UangJalan']['commission_min_qty']:'';
            }
        }
        
        $fromCities = $this->City->getListCities();
        $toCities = $fromCities;
        $this->loadModel('GroupClassification');
        $groupClassifications = $this->GroupClassification->find('list', array(
            'conditions' => array(
                'status' => 1
            ),
        ));
        $groupMotors = $this->GroupMotor->getData('list', array(
            'fields' => array(
                'GroupMotor.id', 'GroupMotor.name',
            ),
        ));

        $branches = $this->City->branchCities();

        $this->set('active_menu', 'uang_jalan');
        $this->set('module_title', __('TTUJ'));
        $this->set(compact(
            'fromCities', 'groupClassifications', 'toCities',
            'groupMotors', 'branches'
        ));
        $this->render('uang_jalan_form');
    }

    function uang_jalan_edit($id){
        $this->loadModel('UangJalan');
        $this->set('sub_module_title', 'Rubah Uang Jalan');
        $uangJalan = $this->UangJalan->getData('first', array(
            'conditions' => array(
                'UangJalan.id' => $id
            )
        ));

        if(!empty($uangJalan)){
            $this->doUangJalan($id, $uangJalan);
        }else{
            $this->MkCommon->setCustomFlash(__('Uang jalan tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'settings',
                'action' => 'uang_jalan'
            ));
        }
    }

    function uang_jalan_toggle( $id = false ){
        $this->loadModel('UangJalan');
        $locale = $this->UangJalan->getData('first', array(
            'conditions' => array(
                'UangJalan.id' => $id
            )
        ));

        if($locale){
            $value = true;
            if($locale['UangJalan']['status']){
                $value = false;
            }

            $this->UangJalan->id = $id;
            $this->UangJalan->set('status', $value);
            if($this->UangJalan->save()){
                $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
                $this->Log->logActivity( sprintf(__('Sukses merubah status Uang Jalan ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params );
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal merubah status Uang Jalan ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );      
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Uang Jalan tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    public function uang_kuli( $data_action = 'muat' ) {
        $this->loadModel('UangKuli');
        $this->loadModel('Customer');
        $options = array(
            'conditions' => array(
                'UangKuli.category' => $data_action,
            ),
        );

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['city'])){
                $city = urldecode($refine['city']);
                $this->request->data['UangKuli']['city'] = $city;
                $options['conditions']['City.name LIKE '] = '%'.$city.'%';
            }
        }
        $this->paginate = $this->UangKuli->getData('paginate', $options);
        $uangKulis = $this->paginate('UangKuli');

        if( !empty($uangKulis) ) {
            foreach ($uangKulis as $key => $uangKuli) {
                unset($uangKuli['Customer']);
                $uangKulis[$key] = $this->Customer->getMerge($uangKuli, $uangKuli['UangKuli']['customer_id']);
            }
        }

        $this->set('active_menu', sprintf('uang_kuli_%s', $data_action));
        $this->set('module_title', __('TTUJ'));
        $this->set('sub_module_title', sprintf('Uang Kuli %s', ucwords($data_action)));
        $this->set(compact(
            'uangKulis', 'data_action'
        ));
    }

    public function uang_kuli_add( $data_action = 'muat' ) {
        $this->loadModel('UangKuli');
        $this->set('sub_module_title', sprintf('Tambah Uang Kuli %s', ucwords($data_action)));
        $this->doUangKuli( $data_action );
    }

    function doUangKuli( $data_action = false, $id = false, $data_local = false ){
        $this->loadModel('City');
        $this->loadModel('GroupMotor');
        $this->loadModel('Customer');

        if(!empty($this->request->data)){
            $data = $this->request->data;
            if($id && $data_local){
                $this->UangKuli->id = $id;
                $data['UangKuli']['id'] = $id;
                $msg = 'merubah';
            }else{
                $this->UangKuli->create();
                $msg = 'menambah';
            }

            $data['UangKuli']['uang_kuli'] = $this->MkCommon->convertPriceToString($data['UangKuli']['uang_kuli'], 0);
            $data['UangKuli']['category'] = $data_action;

            // if( !empty($data['UangKuli']['uang_kuli_type']) && $data['UangKuli']['uang_kuli_type'] == 'per_unit' ) {
            //     $data['UangKuli']['capacity'] = 0;
            // }

            $this->UangKuli->set($data);

            if($this->UangKuli->validates($data)){
                $saveGroupMotor = true;
                $saveCapacity = true;

                if( !empty($data['UangKuli']['uang_kuli_type']) && $data['UangKuli']['uang_kuli_type'] == 'per_unit' ) {
                    if( !empty($data['UangKuliGroupMotor']['group_motor_id']) ) {
                        $resultGroupMotor = $this->saveUangKuliGroupMotor($data);
                        $saveGroupMotor = !empty($resultGroupMotor['validates'])?$resultGroupMotor['validates']:false;
                    }
                } else if( !empty($data['UangKuli']['uang_kuli_type']) && $data['UangKuli']['uang_kuli_type'] == 'per_truck' ) {
                    if( !empty($data['UangKuliCapacity']['capacity']) ) {
                        $resultCapacity = $this->saveUangKuliCapacity($data);
                        $saveCapacity = !empty($resultCapacity['validates'])?$resultCapacity['validates']:false;
                    }
                }

                if( $saveGroupMotor && $saveCapacity && $this->UangKuli->save($data) ){
                    $this->UangKuli->UangKuliGroupMotor->updateAll( array(
                        'UangKuliGroupMotor.status' => 0,
                    ), array(
                        'UangKuliGroupMotor.uang_kuli_id' => $this->UangKuli->id,
                    ));

                    $this->UangKuli->UangKuliCapacity->updateAll( array(
                        'UangKuliCapacity.status' => 0,
                    ), array(
                        'UangKuliCapacity.uang_kuli_id' => $this->UangKuli->id,
                    ));

                    if( !empty($data['UangKuli']['uang_kuli_type']) && $data['UangKuli']['uang_kuli_type'] == 'per_unit' ) {
                        $this->saveUangKuliGroupMotor($data, $this->UangKuli->id);
                    } else if( !empty($data['UangKuli']['uang_kuli_type']) && $data['UangKuli']['uang_kuli_type'] == 'per_truck' ) {
                        $this->saveUangKuliCapacity($data, $this->UangKuli->id);
                    }

                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Uang Kuli'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s Uang Kuli #%s'), $msg, $this->UangKuli->id), $this->user_data, $this->RequestHandler, $this->params );
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'uang_kuli',
                        $data_action,
                    ));
                }else{
                    if( empty($saveGroupMotor) ) {
                        $this->MkCommon->setCustomFlash(sprintf(__('Biaya Per Group Motor harap dilengkapi'), $msg), 'error'); 
                    } else if( empty($saveCapacity) ) {
                        $this->MkCommon->setCustomFlash(sprintf(__('Biaya Per Kapasitas harap dilengkapi'), $msg), 'error'); 
                    } else {
                        $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Uang Kuli'), $msg), 'error'); 
                    }
                    $this->Log->logActivity( sprintf(__('Gagal %s Uang Kuli #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1 );
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Uang Kuli'), $msg), 'error');
            }
        }else{
            
            if($id && $data_local){
                $data_local = $this->MkCommon->getUangKuliGroupMotor($data_local);
                $data_local = $this->MkCommon->getUangKuliCapacity($data_local);
                $this->request->data = $data_local;
            }
        }

        $cities = $this->City->getData('list', array(
            'conditions' => array(
                'City.status' => 1,
            ),
            'order' => array(
                'City.name' => 'ASC',
            ),
        ), false);
        $groupMotors = $this->GroupMotor->getData('list', array(
            'fields' => array(
                'GroupMotor.id', 'GroupMotor.name',
            ),
        ));
        $customers = $this->Customer->getData('list', array(
            'fields' => array(
                'Customer.id', 'Customer.customer_name_code'
            )
        ));

        $this->set('active_menu', sprintf('uang_kuli_%s', $data_action));
        $this->set('module_title', __('TTUJ'));
        $this->set(compact(
            'groupClassifications', 'cities',
            'groupMotors', 'data_action', 'customers'
        ));
        $this->render('uang_kuli_form');
    }

    function uang_kuli_edit( $data_action = 'muat', $id = false ){
        $this->loadModel('UangKuli');
        $this->set('sub_module_title', 'Rubah Uang Kuli Muat');
        $uangKuli = $this->UangKuli->getData('first', array(
            'conditions' => array(
                'UangKuli.id' => $id,
                'UangKuli.category' => $data_action,
            )
        ));

        if(!empty($uangKuli)){
            $this->doUangKuli($data_action, $id, $uangKuli);
        }else{
            $this->MkCommon->setCustomFlash(__('Uang Kuli tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'settings',
                'action' => 'uang_kuli'
            ));
        }
    }

    function uang_kuli_toggle( $data_action = 'muat', $id = false ){
        $this->loadModel('UangKuli');
        $locale = $this->UangKuli->getData('first', array(
            'conditions' => array(
                'UangKuli.id' => $id
            )
        ));

        if($locale){
            $value = true;
            if($locale['UangKuli']['status']){
                $value = false;
            }

            $this->UangKuli->id = $id;
            $this->UangKuli->set('status', $value);
            if($this->UangKuli->save()){
                $this->MkCommon->setCustomFlash(__('Berhasil menghapus uang kuli'), 'success');
                $this->Log->logActivity( sprintf(__('Berhasil menghapus uang kuli ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params );
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal menghapus uang kuli'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal menghapus uang kuli ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );      
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Uang Kuli tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    function perlengkapan(){
        $this->loadModel('Perlengkapan');
        $options = array(
            'order' => array(
                'Perlengkapan.name' => 'ASC'
            ),
        );

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['name'])){
                $name = urldecode($refine['name']);
                $this->request->data['Perlengkapan']['name'] = $name;
                $options['conditions']['Perlengkapan.name LIKE '] = '%'.$name.'%';
            }
        }
        $this->paginate = $this->Perlengkapan->getData('paginate', $options);
        $perlengkapans = $this->paginate('Perlengkapan');

        $this->set('active_menu', 'perlengkapan');
        $this->set('module_title', 'Data Master');
        $this->set('sub_module_title', 'Perlengkapan');
        $this->set(compact(
            'perlengkapans'
        ));
    }

    function perlengkapan_add(){
        $this->loadModel('Perlengkapan');
        $this->set('sub_module_title', 'Tambah Perlengkapan');
        $this->doPerlengkapan();
    }

    function perlengkapan_edit($id){
        $this->loadModel('Perlengkapan');
        $this->set('sub_module_title', 'Rubah Perlengkapan');
        $perlengkapan = $this->Perlengkapan->getData('first', array(
            'conditions' => array(
                'Perlengkapan.id' => $id
            )
        ));

        if(!empty($perlengkapan)){
            $this->doPerlengkapan($id, $perlengkapan);
        }else{
            $this->MkCommon->setCustomFlash(__('Perlengkapan tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'settings',
                'action' => 'perlengkapan'
            ));
        }
    }

    function doPerlengkapan($id = false, $data_local = false){
        $this->loadModel('JenisPerlengkapan');

        if(!empty($this->request->data)){
            $data = $this->request->data;
            if($id && $data_local){
                $this->Perlengkapan->id = $id;
                $msg = 'merubah';
            }else{
                $this->Perlengkapan->create();
                $msg = 'menambah';
            }
            
            $this->Perlengkapan->set($data);

            if($this->Perlengkapan->validates($data)){
                if($this->Perlengkapan->save($data)){
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Perlengkapan'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s Perlengkapan #%s'), $msg, $this->Perlengkapan->id), $this->user_data, $this->RequestHandler, $this->params );
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'perlengkapan'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Perlengkapan'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s Perlengkapan #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1 );      
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Perlengkapan'), $msg), 'error');
            }
        }else{
            if($id && $data_local){
                $this->request->data = $data_local;
            }
        }

        $jenisPerlengkapans = $this->JenisPerlengkapan->find('list', array(
            'conditions' => array(
                'JenisPerlengkapan.status' => 1,
            ),
        ));

        $this->set('active_menu', 'perlengkapan');
        $this->set('module_title', 'Data Master');
        $this->set(compact(
            'jenisPerlengkapans'
        ));
        $this->render('perlengkapan_form');
    }

    function perlengkapan_toggle($id){
        $this->loadModel('Perlengkapan');
        $locale = $this->Perlengkapan->getData('first', array(
            'conditions' => array(
                'Perlengkapan.id' => $id
            )
        ));

        if($locale){
            $value = true;
            if($locale['Perlengkapan']['status']){
                $value = false;
            }

            $this->Perlengkapan->id = $id;
            $this->Perlengkapan->set('status', $value);
            if($this->Perlengkapan->save()){
                $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
                $this->Log->logActivity( sprintf(__('Sukses merubah status perlengkapan ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params );
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal merubah status perlengkapan ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );      
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Perlengkapan tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    function type_motors(){
        $this->loadModel('TipeMotor');
        $options = array( );

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['name'])){
                $name = urldecode($refine['name']);
                $this->request->data['TipeMotor']['name'] = $name;
                $options['conditions']['TipeMotor.name LIKE '] = '%'.$name.'%';
            }
            if(!empty($refine['code'])){
                $code = urldecode($refine['code']);
                $this->request->data['TipeMotor']['code'] = $code;
                $options['conditions']['TipeMotor.code_motor LIKE '] = '%'.$code.'%';
            }
        }

        $this->paginate = $this->TipeMotor->getData('paginate', $options);
        $type_motors = $this->paginate('TipeMotor');

        $this->set('active_menu', 'type_motors');
        $this->set('sub_module_title', 'Tipe Motor');
        $this->set('type_motors', $type_motors);
    }

    function type_motor_add(){
        $this->set('sub_module_title', 'Tambah Tipe Motor');
        $this->doTipeMotor();
    }

    function type_motor_edit($id){
        $this->loadModel('TipeMotor');
        $this->set('sub_module_title', 'Rubah Tipe Motor');
        $TipeMotor = $this->TipeMotor->getData('first', array(
            'conditions' => array(
                'TipeMotor.id' => $id
            )
        ));

        if(!empty($TipeMotor)){
            $this->doTipeMotor($id, $TipeMotor);
        }else{
            $this->MkCommon->setCustomFlash(__('Tipe Motor tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'settings',
                'action' => 'type_motors'
            ));
        }
    }

    function doTipeMotor($id = false, $data_local = false){
        if(!empty($this->request->data)){
            $data = $this->request->data;
            if($id && $data_local){
                $this->TipeMotor->id = $id;
                $msg = 'merubah';
            }else{
                $this->loadModel('TipeMotor');
                $this->TipeMotor->create();
                $msg = 'menambah';
            }
            $this->TipeMotor->set($data);

            if($this->TipeMotor->validates($data)){
                if($this->TipeMotor->save($data)){
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Tipe Motor'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s Tipe Motor #%s'), $msg, $this->TipeMotor->id), $this->user_data, $this->RequestHandler, $this->params );
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'type_motors'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Tipe Motor'), $msg), 'error');  
                    $this->Log->logActivity( sprintf(__('Gagal %s Tipe Motor #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1 );
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Tipe Motor'), $msg), 'error');
            }
        }else{
            
            if($id && $data_local){
                
                $this->request->data = $data_local;
            }
        }

        $this->loadModel('GroupMotor');

        $group_motors = $this->GroupMotor->getData('list', array(
            'fields' => array(
                'GroupMotor.id', 'GroupMotor.name'
            )
        ));
        $this->set('group_motors', $group_motors);
        $this->set('active_menu', 'type_motors');
        $this->render('type_motor_form');
    }

    function type_motor_toggle($id){
        $this->loadModel('TipeMotor');
        $locale = $this->TipeMotor->getData('first', array(
            'conditions' => array(
                'TipeMotor.id' => $id
            )
        ));

        if($locale){
            $value = true;
            if($locale['TipeMotor']['status']){
                $value = false;
            }

            $this->TipeMotor->id = $id;
            $this->TipeMotor->set('status', $value);
            if($this->TipeMotor->save()){
                $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
                $this->Log->logActivity( sprintf(__('Sukses merubah status tipe motor #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params );
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal merubah status tipe motor #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Tipe Motor tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    function colors(){
        $this->loadModel('ColorMotor');
        $options = array(
            'conditions' => array(
                'status' => 1
            )
        );

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['name'])){
                $name = urldecode($refine['name']);
                $this->request->data['ColorMotor']['name'] = $name;
                $options['conditions']['ColorMotor.name LIKE '] = '%'.$name.'%';
            }
        }

        $this->paginate = $this->ColorMotor->getData('paginate', $options);
        $colors = $this->paginate('ColorMotor');

        $this->set('active_menu', 'colors');
        $this->set('sub_module_title', 'Warna Motor');
        $this->set('colors', $colors);
    }

    function color_motor_add(){
        $this->set('sub_module_title', 'Tambah Warna Motor');
        $this->doColorMotor();
    }

    function color_motor_edit($id){
        $this->loadModel('ColorMotor');
        $this->set('sub_module_title', 'Rubah Warna Motor');
        $ColorMotor = $this->ColorMotor->getData('first', array(
            'conditions' => array(
                'ColorMotor.id' => $id
            )
        ));

        if(!empty($ColorMotor)){
            $this->doColorMotor($id, $ColorMotor);
        }else{
            $this->MkCommon->setCustomFlash(__('Warna Motor tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'settings',
                'action' => 'colors'
            ));
        }
    }

    function doColorMotor($id = false, $data_local = false){
        if(!empty($this->request->data)){
            $data = $this->request->data;
            if($id && $data_local){
                $this->ColorMotor->id = $id;
                $msg = 'merubah';
            }else{
                $this->loadModel('ColorMotor');
                $this->ColorMotor->create();
                $msg = 'menambah';
            }
            $this->ColorMotor->set($data);

            if($this->ColorMotor->validates($data)){
                if($this->ColorMotor->save($data)){
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Warna Motor'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s Warna Motor #%s'), $msg, $this->ColorMotor->id), $this->user_data, $this->RequestHandler, $this->params );
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'colors'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Warna Motor'), $msg), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal %s Warna Motor #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1 );
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Warna Motor'), $msg), 'error');
            }
        }else{
            
            if($id && $data_local){
                
                $this->request->data = $data_local;
            }
        }

        $this->set('active_menu', 'colors');
        $this->render('color_motor_form');
    }

    function color_motor_toggle($id){
        $this->loadModel('ColorMotor');
        $locale = $this->ColorMotor->getData('first', array(
            'conditions' => array(
                'ColorMotor.id' => $id
            )
        ));

        if($locale){
            $value = true;
            if($locale['ColorMotor']['status']){
                $value = false;
            }

            $this->ColorMotor->id = $id;
            $this->ColorMotor->set('status', $value);
            if($this->ColorMotor->save()){
                $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
                $this->Log->logActivity( sprintf(__('Sukses merubah status color motor ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params );
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal merubah status color motor ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Warna Motor tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    function regions(){
        $this->loadModel('Region');
        $options = array();

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['name'])){
                $name = urldecode($refine['name']);
                $this->request->data['Region']['name'] = $name;
                $options['conditions']['Region.name LIKE '] = '%'.$name.'%';
            }
        }

        $this->paginate = $this->Region->getData('paginate', $options);
        $regions = $this->paginate('Region');

        $this->set('active_menu', 'regions');
        $this->set('sub_module_title', 'Provinsi');
        $this->set('regions', $regions);
    }

    function region_add(){
        $this->set('sub_module_title', 'Tambah Provinsi');
        $this->doRegion();
    }

    function region_edit($id){
        $this->loadModel('Region');
        $this->set('sub_module_title', 'Rubah Provinsi');
        $type_property = $this->Region->getData('first', array(
            'conditions' => array(
                'Region.id' => $id
            )
        ));

        if(!empty($type_property)){
            $this->doRegion($id, $type_property);
        }else{
            $this->MkCommon->setCustomFlash(__('Provinsi tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'settings',
                'action' => 'citys'
            ));
        }
    }

    function doRegion($id = false, $data_local = false){
        if(!empty($this->request->data)){
            $data = $this->request->data;
            if($id && $data_local){
                $this->Region->id = $id;
                $msg = 'merubah';
            }else{
                $this->loadModel('Region');
                $this->Region->create();
                $msg = 'menambah';
            }
            $this->Region->set($data);

            if($this->Region->validates($data)){
                if($this->Region->save($data)){
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Provinsi'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s Provinsi #%s'), $msg, $this->Region->id), $this->user_data, $this->RequestHandler, $this->params );
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'regions'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Provinsi'), $msg), 'error');  
                    $this->Log->logActivity( sprintf(__('Gagal %s Provinsi #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1 );
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Provinsi'), $msg), 'error');
            }
        }else{
            
            if($id && $data_local){
                
                $this->request->data = $data_local;
            }
        }

        $this->set('active_menu', 'regions');
        $this->render('region_form');
    }

    function regions_toggle($id){
        $this->loadModel('Region');
        $locale = $this->Region->getData('first', array(
            'conditions' => array(
                'Region.id' => $id
            )
        ));

        if($locale){
            $value = true;
            if($locale['Region']['status']){
                $value = false;
            }

            $this->Region->id = $id;
            $this->Region->set('status', $value);
            if($this->Region->save()){
                $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
                $this->Log->logActivity( sprintf(__('Sukses merubah status Provinsi ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params );
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal merubah status Provinsi ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Provinsi tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    function group_motors(){
        $this->loadModel('GroupMotor');
        $options = array();

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['name'])){
                $name = urldecode($refine['name']);
                $this->request->data['GroupMotor']['name'] = $name;
                $options['conditions']['GroupMotor.name LIKE '] = '%'.$name.'%';
            }
        }

        $this->paginate = $this->GroupMotor->getData('paginate', $options);
        $group_motors = $this->paginate('GroupMotor');

        $this->set('active_menu', 'group_motors');
        $this->set('sub_module_title', 'Grup Motor');
        $this->set('group_motors', $group_motors);
    }

    function group_motor_add(){
        $this->set('sub_module_title', 'Tambah Grup Motor');
        $this->doGroupMotor();
    }

    function group_motor_edit($id){
        $this->loadModel('GroupMotor');
        $this->set('sub_module_title', 'Rubah Grup Motor');
        $GroupMotor = $this->GroupMotor->getData('first', array(
            'conditions' => array(
                'GroupMotor.id' => $id
            ),
        ));

        if(!empty($GroupMotor)){
            $this->doGroupMotor($id, $GroupMotor);
        }else{
            $this->MkCommon->setCustomFlash(__('Grup Motor tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'settings',
                'action' => 'group_motors'
            ));
        }
    }

    function doGroupMotor($id = false, $data_local = false){
        if(!empty($this->request->data)){
            $data = $this->request->data;
            if($id && $data_local){
                $this->GroupMotor->id = $id;
                $msg = 'merubah';
            }else{
                $this->loadModel('GroupMotor');
                $this->GroupMotor->create();
                $msg = 'menambah';
            }
            $this->GroupMotor->set($data);

            if($this->GroupMotor->validates($data)){
                if($this->GroupMotor->save($data)){
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Grup Motor'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s Grup Motor #%s'), $msg, $this->GroupMotor->id), $this->user_data, $this->RequestHandler, $this->params );
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'group_motors'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Grup Motor'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s Grup Motor #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1 );
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Grup Motor'), $msg), 'error');
            }
        }else{
            
            if($id && $data_local){
                
                $this->request->data = $data_local;
            }
        }

        $this->set('active_menu', 'group_motors');
        $this->render('group_motor_form');
    }

    function group_motor_toggle($id){
        $this->loadModel('GroupMotor');
        $locale = $this->GroupMotor->getData('first', array(
            'conditions' => array(
                'GroupMotor.id' => $id
            )
        ));

        if($locale){
            $value = true;
            if($locale['GroupMotor']['status']){
                $value = false;
            }

            $this->GroupMotor->id = $id;
            $this->GroupMotor->set('status', $value);
            if($this->GroupMotor->save()){
                $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
                $this->Log->logActivity( sprintf(__('Sukses merubah status group motor #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params );
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal merubah status group motor #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Grup Motor tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    function code_motors(){
        $this->loadModel('CodeMotor');
        $options = array(
            'conditions' => array(
                'status' => 1
            )
        );

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['name'])){
                $name = urldecode($refine['name']);
                $this->request->data['CodeMotor']['name'] = $name;
                $options['conditions']['CodeMotor.name LIKE '] = '%'.$name.'%';
            }
        }

        $this->paginate = $this->CodeMotor->getData('paginate', $options);
        $code_motors = $this->paginate('CodeMotor');

        $this->set('active_menu', 'code_motors');
        $this->set('sub_module_title', 'Kode Motor');
        $this->set('code_motors', $code_motors);
    }

    function code_motor_add(){
        $this->set('sub_module_title', 'Tambah Kode Motor');
        $this->doCodeMotor();
    }

    function code_motor_edit($id){
        $this->loadModel('CodeMotor');
        $this->set('sub_module_title', 'Rubah Kode Motor');
        $CodeMotor = $this->CodeMotor->getData('first', array(
            'conditions' => array(
                'CodeMotor.id' => $id
            )
        ));

        if(!empty($CodeMotor)){
            $this->doCodeMotor($id, $CodeMotor);
        }else{
            $this->MkCommon->setCustomFlash(__('Kode Motor tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'settings',
                'action' => 'code_motors'
            ));
        }
    }

    function doCodeMotor($id = false, $data_local = false){
        if(!empty($this->request->data)){
            $data = $this->request->data;
            if($id && $data_local){
                $this->CodeMotor->id = $id;
                $msg = 'merubah';
            }else{
                $this->loadModel('CodeMotor');
                $this->CodeMotor->create();
                $msg = 'menambah';
            }
            $this->CodeMotor->set($data);

            if($this->CodeMotor->validates($data)){
                if($this->CodeMotor->save($data)){
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Kode Motor'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s Kode Motor #%s'), $msg, $this->CodeMotor->id), $this->user_data, $this->RequestHandler, $this->params );
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'code_motors'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Kode Motor'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s Kode Motor #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1 );
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Kode Motor'), $msg), 'error');
            }
        }else{
            
            if($id && $data_local){
                
                $this->request->data = $data_local;
            }
        }

        $this->set('active_menu', 'code_motor');
        $this->render('code_motor_form');
    }

    function code_motor_toggle($id){
        $this->loadModel('CodeMotor');
        $locale = $this->CodeMotor->getData('first', array(
            'conditions' => array(
                'CodeMotor.id' => $id
            )
        ));

        if($locale){
            $value = true;
            if($locale['CodeMotor']['status']){
                $value = false;
            }

            $this->CodeMotor->id = $id;
            $this->CodeMotor->set('status', $value);
            if($this->CodeMotor->save()){
                $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
                $this->Log->logActivity( sprintf(__('Sukses merubah status code motor #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params );
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal merubah status code motor #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Kode Motor tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    public function customer_groups () {
        $this->loadModel('CustomerGroup');
        $options = array();

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['name'])){
                $name = urldecode($refine['name']);
                $this->request->data['CustomerGroup']['name'] = $name;
                $options['conditions']['CustomerGroup.name LIKE '] = '%'.$name.'%';
            }
        }
        $this->paginate = $this->CustomerGroup->getData('paginate', $options);
        $customerGroups = $this->paginate('CustomerGroup');

        $this->set('active_menu', 'customer_groups');
        $this->set('module_title', 'Data Master');
        $this->set('sub_module_title', 'Grup Customer');
        $this->set('customerGroups', $customerGroups);
    }

    function customer_group_add(){
        $this->loadModel('CustomerGroup');
        $this->set('sub_module_title', 'Tambah Grup Customer');
        $this->doCustomerGroup();
    }

    function customer_group_edit($id){
        $this->loadModel('CustomerGroup');
        $this->set('sub_module_title', 'Rubah Grup Customer');
        $customerGroup = $this->CustomerGroup->getData('first', array(
            'conditions' => array(
                'CustomerGroup.id' => $id
            )
        ));

        if(!empty($customerGroup)){
            $this->doCustomerGroup($id, $customerGroup);
        }else{
            $this->MkCommon->setCustomFlash(__('Grup Customer tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'settings',
                'action' => 'customer_groups'
            ));
        }
    }

    function doCustomerGroup($id = false, $data_local = false){
        if(!empty($this->request->data)){
            $data = $this->request->data;

            if($id && $data_local){
                $this->CustomerGroup->id = $id;
                $msg = 'merubah';
            }else{
                $this->CustomerGroup->create();
                $msg = 'menambah';
            }

            if( !empty($data_local['CustomerGroupPattern']) ){
                $this->CustomerGroup->CustomerGroupPattern->id = $data_local['CustomerGroupPattern']['id'];
            }else{
                $this->CustomerGroup->CustomerGroupPattern->create();
            }
            
            $this->CustomerGroup->set($data);
            $this->CustomerGroup->CustomerGroupPattern->set($data);

            if($this->CustomerGroup->validates($data) && $this->CustomerGroup->CustomerGroupPattern->validates($data)){
                if($this->CustomerGroup->save($data)){
                    $data['CustomerGroupPattern']['customer_group_id'] = $this->CustomerGroup->id;

                    if( $this->CustomerGroup->CustomerGroupPattern->save($data) ) {
                        $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Group Customer, Namun gagal menyimpan pattern'), $msg), 'success');
                        $this->Log->logActivity( sprintf(__('Sukses %s Group Customer, Namun gagal menyimpan pattern #%s'), $msg, $this->CustomerGroup->id), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                    } else {
                        $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Grup Customer'), $msg), 'success');
                        $this->Log->logActivity( sprintf(__('Sukses %s Grup Customer #%s'), $msg, $this->CustomerGroup->id), $this->user_data, $this->RequestHandler, $this->params );
                    }

                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'customer_groups'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Grup Customer'), $msg), 'error');  
                    $this->Log->logActivity( sprintf(__('Gagal %s Grup Customer #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1 );
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Grup Customer'), $msg), 'error');
            }
        }else{
            if($id && $data_local){
                $this->request->data = $data_local;
            }
        }

        $this->set('active_menu', 'customer_groups');
        $this->set('module_title', 'Data Master');
        $this->render('customer_group_form');
    }

    function customer_group_toggle($id){
        $this->loadModel('CustomerGroup');
        $locale = $this->CustomerGroup->getData('first', array(
            'conditions' => array(
                'CustomerGroup.id' => $id
            )
        ));

        if($locale){
            $value = true;
            if($locale['CustomerGroup']['status']){
                $value = false;
            }

            $this->CustomerGroup->id = $id;
            $this->CustomerGroup->set('status', 0);

            if($this->CustomerGroup->save()){
                $this->MkCommon->setCustomFlash(__('Grup customer telah berhasil dihapus.'), 'success');
                $this->Log->logActivity( sprintf(__('Grup customer ID #%s telah berhasil dihapus.'), $id), $this->user_data, $this->RequestHandler, $this->params );
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal menghapus Grup Customer.'), 'error');
                $this->Log->logActivity( sprintf(__('Grup customer ID #%s telah Gagal dihapus.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Grup Customer tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    function jenis_sim(){
        $this->loadModel('JenisSim');
        $options = array();

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['name'])){
                $name = urldecode($refine['name']);
                $this->request->data['JenisSim']['name'] = $name;
                $options['conditions']['JenisSim.name LIKE '] = '%'.$name.'%';
            }
        }

        $this->paginate = $this->JenisSim->getData('paginate', $options);
        $jenis_sim = $this->paginate('JenisSim');

        $this->set('active_menu', 'jenis_sim');
        $this->set('sub_module_title', 'Jenis SIM');
        $this->set('jenis_sim', $jenis_sim);
    }

    function sim_add(){
        $this->set('sub_module_title', 'Tambah Jenis SIM');
        $this->doSim();
    }

    function sim_edit($id){
        $this->loadModel('JenisSim');
        $this->set('sub_module_title', 'Rubah Jenis SIM');
        $jenis_sim = $this->JenisSim->getData('first', array(
            'conditions' => array(
                'JenisSim.id' => $id
            )
        ));

        if(!empty($jenis_sim)){
            $this->doSim($id, $jenis_sim);
        }else{
            $this->MkCommon->setCustomFlash(__('Jenis SIM tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'settings',
                'action' => 'sims'
            ));
        }
    }

    function doSim($id = false, $data_local = false){
        if(!empty($this->request->data)){
            $data = $this->request->data;
            if($id && $data_local){
                $this->JenisSim->id = $id;
                $msg = 'merubah';
            }else{
                $this->loadModel('JenisSim');
                $this->JenisSim->create();
                $msg = 'menambah';
            }
            $this->JenisSim->set($data);

            if($this->JenisSim->validates($data)){
                if($this->JenisSim->save($data)){
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Jenis SIM'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s Jenis SIM #%s'), $msg, $this->JenisSim->id), $this->user_data, $this->RequestHandler, $this->params );
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'jenis_sim'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Jenis SIM'), $msg), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal %s Jenis SIM #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1 );
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Jenis SIM'), $msg), 'error');
            }
        }else{
            
            if($id && $data_local){
                
                $this->request->data = $data_local;
            }
        }

        $this->set('active_menu', 'jenis_sim');
        $this->render('sim_form');
    }

    function sim_toggle($id){
        $this->loadModel('JenisSim');
        $locale = $this->JenisSim->getData('first', array(
            'conditions' => array(
                'JenisSim.id' => $id
            )
        ));

        if($locale){
            $value = true;
            if($locale['JenisSim']['status']){
                $value = false;
            }

            $this->JenisSim->id = $id;
            $this->JenisSim->set('status', $value);
            if($this->JenisSim->save()){
                $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
                $this->Log->logActivity( sprintf(__('Sukses merubah status jenis sim ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params );
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal merubah status jenis sim ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );   
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Jenis SIM tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    function jenis_perlengkapan(){
        $this->loadModel('JenisPerlengkapan');
        $options = array();

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['name'])){
                $name = urldecode($refine['name']);
                $this->request->data['JenisPerlengkapan']['name'] = $name;
                $options['conditions']['JenisPerlengkapan.name LIKE '] = '%'.$name.'%';
            }
        }

        $this->paginate = $this->JenisPerlengkapan->getData('paginate', $options);
        $jenis_perlengkapan = $this->paginate('JenisPerlengkapan');

        $this->set('active_menu', 'jenis_perlengkapan');
        $this->set('sub_module_title', 'Jenis Perlengkapan');
        $this->set('jenis_perlengkapan', $jenis_perlengkapan);
    }

    function jenis_perlengkapan_add(){
        $this->set('sub_module_title', 'Tambah Jenis Perlengkapan');
        $this->doJenisPerlengkapan();
    }

    function jenis_perlengkapan_edit($id){
        $this->loadModel('JenisPerlengkapan');
        $this->set('sub_module_title', 'Rubah Jenis Perlengkapan');
        $jenis_perlengkapan = $this->JenisPerlengkapan->getData('first', array(
            'conditions' => array(
                'JenisPerlengkapan.id' => $id
            )
        ));

        if(!empty($jenis_perlengkapan)){
            $this->doJenisPerlengkapan($id, $jenis_perlengkapan);
        }else{
            $this->MkCommon->setCustomFlash(__('Jenis Perlengkapan tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'settings',
                'action' => 'jenis_perlengkapan'
            ));
        }
    }

    function doJenisPerlengkapan($id = false, $data_local = false){
        if(!empty($this->request->data)){
            $data = $this->request->data;
            if($id && $data_local){
                $this->JenisPerlengkapan->id = $id;
                $msg = 'merubah';
            }else{
                $this->loadModel('JenisPerlengkapan');
                $this->JenisPerlengkapan->create();
                $msg = 'menambah';
            }
            $this->JenisPerlengkapan->set($data);

            if($this->JenisPerlengkapan->validates($data)){
                if($this->JenisPerlengkapan->save($data)){
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Jenis Perlengkapan'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s Jenis Perlengkapan #%s'), $msg, $this->JenisPerlengkapan->id), $this->user_data, $this->RequestHandler, $this->params );
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'jenis_perlengkapan'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Jenis Perlengkapan'), $msg), 'error');  
                    $this->Log->logActivity( sprintf(__('Gagal %s Jenis Perlengkapan #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1 );
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Jenis Perlengkapan'), $msg), 'error');
            }
        }else{
            
            if($id && $data_local){
                
                $this->request->data = $data_local;
            }
        }

        $this->set('active_menu', 'jenis_perlengkapan');
        $this->render('jenis_perlengkapan_form');
    }

    function jenis_perlengkapan_toggle($id){
        $this->loadModel('JenisPerlengkapan');
        $locale = $this->JenisPerlengkapan->getData('first', array(
            'conditions' => array(
                'JenisPerlengkapan.id' => $id
            )
        ));

        if($locale){
            $value = true;
            if($locale['JenisPerlengkapan']['status']){
                $value = false;
            }

            $this->JenisPerlengkapan->id = $id;
            $this->JenisPerlengkapan->set('status', $value);
            if($this->JenisPerlengkapan->save()){
                $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
                $this->Log->logActivity( sprintf(__('Sukses merubah status jenis perlengkapan ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params );
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal merubah status jenis perlengkapan ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );   
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Jenis Perlengkapan tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    function classifications(){
        $this->loadModel('GroupClassification');
        $options = array(
            'conditions' => array(
                'GroupClassification.status' => 1,
            ),
            'order' => array(
                'GroupClassification.name',
            ),
        );

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['name'])){
                $name = urldecode($refine['name']);
                $this->request->data['GroupClassification']['name'] = $name;
                $options['conditions']['GroupClassification.name LIKE '] = '%'.$name.'%';
            }
        }

        $this->paginate = $options;
        $groupClassifications = $this->paginate('GroupClassification');

        $this->set('active_menu', 'classifications');
        $this->set('sub_module_title', __('Klasifikasi'));
        $this->set('groupClassifications', $groupClassifications);
    }

    function classification_add(){
        $this->loadModel('GroupClassification');
        $this->set('sub_module_title', __('Tambah Klasifikasi'));
        $this->doclassification();
    }

    function classification_edit($id){
        $this->loadModel('GroupClassification');
        $this->set('sub_module_title', __('Rubah Klasifikasi'));
        $groupClassification = $this->GroupClassification->getData('first', array(
            'conditions' => array(
                'GroupClassification.id' => $id
            )
        ));

        if(!empty($groupClassification)){
            $this->doclassification($id, $groupClassification);
        }else{
            $this->MkCommon->setCustomFlash(__('Klasifikasi tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'settings',
                'action' => 'classifications'
            ));
        }
    }

    function doclassification($id = false, $data_local = false){
        if(!empty($this->request->data)){
            $data = $this->request->data;
            if($id && $data_local){
                $this->GroupClassification->id = $id;
                $msg = 'merubah';
            }else{
                $this->GroupClassification->create();
                $msg = 'menambah';
            }
            $this->GroupClassification->set($data);

            if($this->GroupClassification->validates($data)){
                if($this->GroupClassification->save($data)){
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Klasifikasi'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s GroupClassification #%s'), $msg, $this->GroupClassification->id), $this->user_data, $this->RequestHandler, $this->params );
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'classifications'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Klasifikasi'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s Klasifikasi #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Klasifikasi'), $msg), 'error');
            }
        }else{
            
            if($id && $data_local){
                
                $this->request->data = $data_local;
            }
        }

        $this->set('active_menu', 'classifications');
        $this->render('classification_form');
    }

    function classification_toggle($id){
        $this->loadModel('GroupClassification');
        $locale = $this->GroupClassification->getData('first', array(
            'conditions' => array(
                'GroupClassification.id' => $id
            )
        ));

        if($locale){
            $value = true;
            if($locale['GroupClassification']['status']){
                $value = false;
            }

            $this->GroupClassification->id = $id;
            $this->GroupClassification->set('status', $value);
            if($this->GroupClassification->save()){
                $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
                $this->Log->logActivity( sprintf(__('Sukses merubah status Klasifikasi ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params ); 
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal merubah status Klasifikasi ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Klasifikasi tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    function tarif_angkutan(){
        $this->loadModel('Customer');
        $this->loadModel('TarifAngkutan');
        $options = array();

        if(!empty($this->params['named'])){
            $this->loadModel('City');
            $refine = $this->params['named'];

            if(!empty($refine['name'])){
                $name = urldecode($refine['name']);
                $this->request->data['TarifAngkutan']['name'] = $name;
                $options['conditions']['TarifAngkutan.name_tarif LIKE '] = '%'.$name.'%';
            }
            if(!empty($refine['customer_name'])){
                $name = urldecode($refine['customer_name']);
                $this->request->data['TarifAngkutan']['customer_name'] = $name;
                $customers = $this->Customer->getData('list', array(
                    'conditions' => array(
                        'CONCAT(Customer.name, \' ( \', CustomerType.name, \' )\') LIKE' => '%'.$name.'%',
                    ),
                    'fields' => array(
                        'Customer.id', 'Customer.id'
                    ),
                ));
                $options['conditions']['TarifAngkutan.customer_id'] = $customers;
            }
            if(!empty($refine['capacity'])){
                $capacity = urldecode($refine['capacity']);
                $this->request->data['UangJalan']['capacity'] = $capacity;
                $options['conditions']['TarifAngkutan.capacity LIKE'] = '%'.$capacity.'%';
            }

            if(!empty($refine['jenis_unit'])){
                $name = urldecode($refine['jenis_unit']);
                $this->request->data['TarifAngkutan']['jenis_unit'] = $name;
                $options['conditions']['TarifAngkutan.jenis_unit LIKE'] = '%'.$name.'%';
            }

            if(!empty($refine['from'])){
                $name = urldecode($refine['from']);
                $this->request->data['UangJalan']['from_city'] = $name;
                $city_id = $this->City->getData('list', array(
                    'conditions' => array(
                        'City.name LIKE' => '%'.$name.'%',
                    ),
                    'fields' => array(
                        'City.id', 'City.id',
                    ),
                ), true, array(
                    'all',
                ));
                $options['conditions']['TarifAngkutan.from_city_id'] = $city_id;
            }
            if(!empty($refine['to'])){
                $name = urldecode($refine['to']);
                $this->request->data['UangJalan']['to_city'] = $name;
                $city_id = $this->City->getData('list', array(
                    'conditions' => array(
                        'City.name LIKE' => '%'.$name.'%',
                    ),
                    'fields' => array(
                        'City.id', 'City.id',
                    ),
                ), true, array(
                    'all',
                ));
                $options['conditions']['TarifAngkutan.to_city_id'] = $city_id;
            }
        }

        $this->paginate = $this->TarifAngkutan->getData('paginate', $options);
        $tarif_angkutan = $this->paginate('TarifAngkutan');

        if(!empty($tarif_angkutan)){
            $this->loadModel('GroupMotor');

            foreach ($tarif_angkutan as $key => $value) {
                $group_motor_id = !empty($value['TarifAngkutan']['group_motor_id'])?$value['TarifAngkutan']['group_motor_id']:false;
                $customer_id = !empty($value['TarifAngkutan']['customer_id'])?$value['TarifAngkutan']['customer_id']:false;

                $value = $this->Customer->getMerge($value, $customer_id);
                $value = $this->GroupMotor->getMerge($value, $group_motor_id);
                $tarif_angkutan[$key] = $value;
            }
        }

        $this->set('active_menu', 'tarif_angkutan');
        $this->set('sub_module_title', 'Tarif Angkutan');
        $this->set('tarif_angkutan', $tarif_angkutan);
    }

    function tarif_angkutan_add(){
        $this->set('sub_module_title', 'Tambah Tarif Angkutan');
        $this->doTarifAngkutan();
    }

    function tarif_angkutan_edit($id){
        $this->loadModel('TarifAngkutan');
        $this->set('sub_module_title', 'Rubah Tarif Angkutan');
        $tarif_angkutan = $this->TarifAngkutan->getData('first', array(
            'conditions' => array(
                'TarifAngkutan.id' => $id
            ),
        ));

        if(!empty($tarif_angkutan)){
            $this->doTarifAngkutan($id, $tarif_angkutan);
        }else{
            $this->MkCommon->setCustomFlash(__('Tarif Angkutan tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'settings',
                'action' => 'tarif_angkutan'
            ));
        }
    }

    function doTarifAngkutan($id = false, $data_local = false){
        $this->loadModel('City');

        if(!empty($this->request->data)){
            $data = $this->request->data;
            if($id && $data_local){
                $this->TarifAngkutan->id = $id;
                $msg = 'merubah';
                $data['TarifAngkutan']['id'] = $id; // ini utk validasi uniq tarif angkut
            }else{
                $this->loadModel('TarifAngkutan');
                $this->TarifAngkutan->create();
                $msg = 'menambah';
            }

            $data['TarifAngkutan']['group_motor_id'] = !empty($data['TarifAngkutan']['group_motor_id'])?$data['TarifAngkutan']['group_motor_id']:0;
            $data['TarifAngkutan']['tarif'] = !empty($data['TarifAngkutan']['tarif'])?str_replace(',', '', $data['TarifAngkutan']['tarif']):false;
            $data['TarifAngkutan']['capacity'] = !empty($data['TarifAngkutan']['capacity'])?$data['TarifAngkutan']['capacity']:0;
            $data['TarifAngkutan']['branch_id'] = Configure::read('__Site.config_branch_id');

            if(!empty($data['TarifAngkutan']['from_city_id'])){
                $city = $this->City->getData('first', array(
                    'conditions' => array(
                        'City.status' => 1,
                        'City.id' => $data['TarifAngkutan']['from_city_id']
                    ),
                    'fields' => array(
                        'City.name'
                    )
                ));

                if(!empty($city['City']['name'])){
                    $data['TarifAngkutan']['from_city_name'] = $city['City']['name'];
                }
            }

            if(!empty($data['TarifAngkutan']['to_city_id'])){
                $city = $this->City->getData('first', array(
                    'conditions' => array(
                        'City.status' => 1,
                        'City.id' => $data['TarifAngkutan']['to_city_id']
                    ),
                    'fields' => array(
                        'City.name'
                    )
                ));

                if(!empty($city['City']['name'])){
                    $data['TarifAngkutan']['to_city_name'] = $city['City']['name'];
                }
            }

            if( !empty($data['TarifAngkutan']['jenis_unit']) && $data['TarifAngkutan']['jenis_unit'] == 'per_truck' ){
                $data['TarifAngkutan']['group_motor_id'] = 0;
            }

            $this->TarifAngkutan->set($data);

            if($this->TarifAngkutan->validates($data)){
                if($this->TarifAngkutan->save($data)){
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Tarif Angkutan'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s Tarif Angkutan #%s'), $msg, $this->TarifAngkutan->id), $this->user_data, $this->RequestHandler, $this->params );
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'tarif_angkutan'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Tarif Angkutan'), $msg), 'error');  
                    $this->Log->logActivity( sprintf(__('Gagal %s Tarif Angkutan #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1 );
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Tarif Angkutan'), $msg), 'error');
            }
        }else{
            
            if($id && $data_local){
                
                $this->request->data = $data_local;
            }
        }

        $this->loadModel('Customer');
        $customers = $this->Customer->getData('list', array(
            'fields' => array(
                'Customer.id', 'Customer.customer_name_code'
            ),
        ));

        $this->loadModel('GroupMotor');
        $group_motors = $this->GroupMotor->getData('list');
        $fromCities = $this->City->getListCities();
        $toCities = $fromCities;

        $branches = $this->City->branchCities();

        $this->set(compact(
            'customers', 'group_motors', 'fromCities',
            'toCities', 'branches'
        ));

        $this->set('active_menu', 'tarif_angkutan');
        $this->render('tarif_angkutan_form');
    }

    function tarif_angkutan_toggle($id){
        $this->loadModel('TarifAngkutan');
        $locale = $this->TarifAngkutan->getData('first', array(
            'conditions' => array(
                'TarifAngkutan.id' => $id
            ),
        ));

        if($locale){
            $value = true;
            if($locale['TarifAngkutan']['status']){
                $value = false;
            }

            $this->TarifAngkutan->id = $id;
            $this->TarifAngkutan->set('status', $value);
            if($this->TarifAngkutan->save()){
                $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
                $this->Log->logActivity( sprintf(__('Sukses merubah status tarif angkutan ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params );   
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal merubah status tarif angkutan ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );   
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Tarif Angkutan tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    function customer_target_unit(){
        $this->loadModel('CustomerTargetUnit');
        $this->loadModel('Customer');
        $options = array();

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['name'])){
                $name = urldecode($refine['name']);
                $this->request->data['Customer']['name'] = $name;
                $customers = $this->Customer->getData('list', array(
                    'conditions' => array(
                        'CONCAT(Customer.name, \' ( \', CustomerType.name, \' )\') LIKE' => '%'.$name.'%',
                    ),
                    'fields' => array(
                        'Customer.id', 'Customer.id'
                    ),
                ));
                $options['conditions']['CustomerTargetUnit.customer_id'] = $customers;
            }
        }

        $this->paginate = $this->CustomerTargetUnit->getData('paginate', $options);
        $customerTargetUnits = $this->paginate('CustomerTargetUnit');

        if( !empty($customerTargetUnits) ) {
            foreach ($customerTargetUnits as $key => $customerTargetUnit) {
                $customerTargetUnit = $this->Customer->getMerge($customerTargetUnit, $customerTargetUnit['CustomerTargetUnit']['customer_id']);
                $customerTargetUnits[$key] = $customerTargetUnit;
            }
        }

        $this->set('active_menu', 'customer_target_unit');
        $this->set('sub_module_title', __('Target Unit'));
        $this->set('customerTargetUnits', $customerTargetUnits);
    }

    function customer_target_unit_add(){
        $this->loadModel('CustomerTargetUnit');
        $this->set('sub_module_title', __('Target Unit'));
        $this->doCustomerTargetUnit();
    }

    function customer_target_unit_edit($id){
        $this->loadModel('CustomerTargetUnit');
        $this->set('sub_module_title', __('Rubah Unit'));
        $customerTargetUnit = $this->CustomerTargetUnit->getData('first', array(
            'conditions' => array(
                'CustomerTargetUnit.id' => $id
            )
        ));

        if(!empty($customerTargetUnit)){
            $this->doCustomerTargetUnit($id, $customerTargetUnit);
        }else{
            $this->MkCommon->setCustomFlash(__('Target Unit tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'settings',
                'action' => 'customer_target_unit'
            ));
        }
    }

    function doCustomerTargetUnit($id = false, $data_local = false){
        $this->loadModel('Customer');
        $this->loadModel('CustomerTargetUnitDetail');

        if(!empty($this->request->data)){
            $data = $this->request->data;

            if($id && $data_local){
                $this->CustomerTargetUnit->id = $id;
                $msg = 'merubah';
            }else{
                $this->CustomerTargetUnit->create();
                $msg = 'menambah';
            }
            $this->CustomerTargetUnit->set($data);
            $dataDetail = array();
            $validateDetail = true;

            if( !empty($data['CustomerTargetUnitDetail']['unit']) ) {
                foreach ($data['CustomerTargetUnitDetail']['unit'] as $key => $unit) {
                    if( !empty($unit) ) {
                        $dataTemp = array(
                            'month' => date('m', mktime(0, 0, 0, $key+1, 1, date("Y"))),
                            'unit' => $unit,
                        );
                        $this->CustomerTargetUnitDetail->set($dataTemp);

                        if( !$this->CustomerTargetUnitDetail->validates() ) {
                            $validateDetail = false;
                        }
                    }
                }
            }

            if( $this->CustomerTargetUnit->validates($data) && $validateDetail ){
                if($this->CustomerTargetUnit->save($data)){
                    if( !empty($data['CustomerTargetUnitDetail']['unit']) ) {
                        $idx = 0;
                        foreach ($data['CustomerTargetUnitDetail']['unit'] as $key => $unit) {
                            if( !empty($unit) ) {
                                $dataDetail[$idx]['CustomerTargetUnitDetail'] = array(
                                    'customer_target_unit_id' => $this->CustomerTargetUnit->id,
                                    'month' => date('m', mktime(0, 0, 0, $key+1, 1, date("Y"))),
                                    'unit' => $unit,
                                );
                                $idx++;
                            }
                        }
                    }

                    $this->CustomerTargetUnitDetail->deleteAll(array( 
                        'CustomerTargetUnitDetail.customer_target_unit_id' => $this->CustomerTargetUnit->id,
                    ));

                    $this->CustomerTargetUnitDetail->saveMany( $dataDetail );

                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Target Unit'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s Target Unit #%s'), $msg, $this->CustomerTargetUnit->id), $this->user_data, $this->RequestHandler, $this->params );
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'customer_target_unit'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Target Unit'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s Target Unit #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Target Unit'), $msg), 'error');
            }
        }else{
            if($id && $data_local){
                $this->request->data = $data_local;

                if( !empty($this->request->data['CustomerTargetUnitDetail']) ) {
                    $customerTargetUnitDetail = $this->request->data['CustomerTargetUnitDetail'];
                    unset($this->request->data['CustomerTargetUnitDetail']);

                    foreach ($customerTargetUnitDetail as $key => $value) {
                        $this->request->data['CustomerTargetUnitDetail']['unit'][$value['month']-1] = $value['unit'];
                    }
                }
            }
        }

        $customers = $this->Customer->getData('list', array(
            'fields' => array(
                'Customer.id', 'Customer.customer_name_code'
            )
        ));

        $this->set('active_menu', 'customer_target_unit');
        $this->set('customers', $customers);
        $this->render('customer_target_unit_form');
    }

    function customer_target_unit_toggle($id){
        $this->loadModel('CustomerTargetUnit');
        $locale = $this->CustomerTargetUnit->getData('first', array(
            'conditions' => array(
                'CustomerTargetUnit.id' => $id
            )
        ));

        if($locale){
            $value = true;
            if($locale['CustomerTargetUnit']['status']){
                $value = false;
            }

            $this->CustomerTargetUnit->id = $id;
            $this->CustomerTargetUnit->set('status', $value);
            if($this->CustomerTargetUnit->save()){
                $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
                $this->Log->logActivity( sprintf(__('Sukses merubah status Target Unit ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params ); 
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal merubah status Target Unit ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Target Unit tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    function banks(){
        $this->loadModel('Bank');
        $options = array();

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['name'])){
                $name = urldecode($refine['name']);
                $this->request->data['Bank']['name'] = $name;
                $options['conditions']['Bank.name LIKE '] = '%'.$name.'%';
            }
        }

        $this->paginate = $this->Bank->getData('paginate', $options);
        $banks = $this->paginate('Bank');

        $this->set('active_menu', 'banks');
        $this->set('sub_module_title', 'Bank');
        $this->set('banks', $banks);
    }

    function bank_add(){
        $this->loadModel('Bank');
        $this->set('sub_module_title', 'Tambah Bank');
        $this->doBank();
    }

    function bank_edit($id){
        $this->loadModel('Bank');
        $this->set('sub_module_title', 'Rubah Bank');
        $bank = $this->Bank->getData('first', array(
            'conditions' => array(
                'Bank.id' => $id
            )
        ));

        if(!empty($bank)){
            $this->doBank($id, $bank);
        }else{
            $this->MkCommon->setCustomFlash(__('Bank tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'settings',
                'action' => 'banks'
            ));
        }
    }

    function doBank($id = false, $data_local = false){
        $this->loadModel('Coa');

        if(!empty($this->request->data)){
            $data = $this->request->data;
            if($id && $data_local){
                $this->Bank->id = $id;
                $msg = 'merubah';
            }else{
                $this->Bank->create();
                $msg = 'menambah';
            }

            $data['Bank']['coa_id'] = !empty($data['Bank']['coa_id'])?$data['Bank']['coa_id']:0;
            $this->Bank->set($data);

            if($this->Bank->validates($data)){
                if($this->Bank->save($data)){
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Bank'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s Bank #%s'), $msg, $this->Bank->id), $this->user_data, $this->RequestHandler, $this->params );
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'banks'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Bank'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s Bank #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Bank'), $msg), 'error');
            }
        }else{
            
            if($id && $data_local){
                
                $this->request->data = $data_local;
            }
        }

        $coas = $this->Coa->getData('list', array(
            'conditions' => array(
                'Coa.status' => 1,
            ),
            'fields' => array(
                'Coa.id', 'Coa.coa_name'
            ),
        ));

        $this->set('active_menu', 'banks');
        $this->set('coas', $coas);
        $this->render('bank_form');
    }

    function bank_toggle($id){
        $this->loadModel('Bank');
        $locale = $this->Bank->getData('first', array(
            'conditions' => array(
                'Bank.id' => $id
            )
        ));

        if($locale){
            $value = true;
            if($locale['Bank']['status']){
                $value = false;
            }

            $this->Bank->id = $id;
            $this->Bank->set('status', $value);
            if($this->Bank->save()){
                $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
                $this->Log->logActivity( sprintf(__('Sukses merubah status Bank ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params ); 
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal merubah status Bank ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Bank tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    function calendar_colors(){
        $this->loadModel('CalendarColor');
        $options = array(
            'conditions' => array(
                'CalendarColor.status' => 1
            )
        );

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['name'])){
                $name = urldecode($refine['name']);
                $this->request->data['CalendarColor']['name'] = $name;
                $options['conditions']['CalendarColor.name LIKE '] = '%'.$name.'%';
            }
        }

        $this->paginate = $this->CalendarColor->getData('paginate', $options);
        $calendarColors = $this->paginate('CalendarColor');

        $this->set('active_menu', 'calendar_colors');
        $this->set('sub_module_title', 'Warna Kalender');
        $this->set('calendarColors', $calendarColors);
    }

    function calendar_color_add(){
        $this->loadModel('CalendarColor');
        $this->set('sub_module_title', 'Tambah Warna Kalender');
        $this->doCalendarColor();
    }

    function calendar_color_edit($id){
        $this->loadModel('CalendarColor');
        $this->set('sub_module_title', 'Rubah Warna Kalender');
        $calendarColor = $this->CalendarColor->getData('first', array(
            'conditions' => array(
                'CalendarColor.id' => $id
            )
        ));

        if(!empty($calendarColor)){
            $this->doCalendarColor($id, $calendarColor);
        }else{
            $this->MkCommon->setCustomFlash(__('Warna Kalender tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'settings',
                'action' => 'calendar_colors'
            ));
        }
    }

    function doCalendarColor($id = false, $data_local = false){
        if(!empty($this->request->data)){
            $data = $this->request->data;
            if($id && $data_local){
                $this->CalendarColor->id = $id;
                $msg = 'merubah';
            }else{
                $this->CalendarColor->create();
                $msg = 'menambah';
            }
            $this->CalendarColor->set($data);

            if($this->CalendarColor->validates($data)){
                if($this->CalendarColor->save($data)){
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Warna Kalender'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s Warna Kalender #%s'), $msg, $this->CalendarColor->id), $this->user_data, $this->RequestHandler, $this->params );
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'calendar_colors'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Warna Kalender'), $msg), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal %s Warna Kalender #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1 );
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Warna Kalender'), $msg), 'error');
            }
        }else{
            
            if($id && $data_local){
                
                $this->request->data = $data_local;
            }
        }

        $layout_js = array(
            'plugins/colorpicker/bootstrap-colorpicker.min'
        );
        $layout_css = array(
            'colorpicker/bootstrap-colorpicker.min'
        );

        $this->set('active_menu', 'calendar_colors');
        $this->set(compact(
            'layout_js', 'layout_css'
        ));
        $this->render('calendar_color_form');
    }

    function calendar_color_toggle($id){
        $this->loadModel('CalendarColor');
        $locale = $this->CalendarColor->getData('first', array(
            'conditions' => array(
                'CalendarColor.id' => $id
            )
        ));

        if($locale){
            $value = true;
            if($locale['CalendarColor']['status']){
                $value = false;
            }

            $this->CalendarColor->id = $id;
            $this->CalendarColor->set('status', $value);
            if($this->CalendarColor->save()){
                $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
                $this->Log->logActivity( sprintf(__('Sukses merubah status Warna Kalender ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params );
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal merubah status Warna Kalender ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Warna Motor tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    function calendar_icons(){
        $this->loadModel('CalendarIcon');
        $options = array(
            'conditions' => array(
                'CalendarIcon.status' => 1
            )
        );

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['name'])){
                $name = urldecode($refine['name']);
                $this->request->data['CalendarIcon']['name'] = $name;
                $options['conditions']['CalendarIcon.name LIKE '] = '%'.$name.'%';
            }
        }

        $this->paginate = $this->CalendarIcon->getData('paginate', $options);
        $calendarIcons = $this->paginate('CalendarIcon');

        $this->set('active_menu', 'calendar_icons');
        $this->set('sub_module_title', 'Icon Kalender');
        $this->set('calendarIcons', $calendarIcons);
    }

    function calendar_icon_add(){
        $this->loadModel('CalendarIcon');
        $this->set('sub_module_title', 'Tambah Icon Kalender');
        $this->doCalendarIcon();
    }

    function calendar_icon_edit($id){
        $this->loadModel('CalendarIcon');
        $this->set('sub_module_title', 'Rubah Icon Kalender');
        $calendarIcon = $this->CalendarIcon->getData('first', array(
            'conditions' => array(
                'CalendarIcon.id' => $id
            )
        ));

        if(!empty($calendarIcon)){
            $this->doCalendarIcon($id, $calendarIcon);
        }else{
            $this->MkCommon->setCustomFlash(__('Icon Kalender tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'settings',
                'action' => 'calendar_icons'
            ));
        }
    }

    function doCalendarIcon($id = false, $data_local = false){
        if(!empty($this->request->data)){
            $data = $this->request->data;

            if($id && $data_local){
                $this->CalendarIcon->id = $id;
                $msg = 'merubah';
            }else{
                $this->CalendarIcon->create();
                $msg = 'menambah';
            }

            if( !empty($data['CalendarIcon']['image']['name']) ){
                $temp_image = $data['CalendarIcon']['image'];
                $data['CalendarIcon']['photo'] = $data['CalendarIcon']['image']['name'];
            }else{
                if($id && $data_local){
                    unset($data['CalendarIcon']['photo']);
                }else{
                    $data['CalendarIcon']['photo'] = '';
                }
            }

            $this->CalendarIcon->set($data);

            if($this->CalendarIcon->validates($data)){
                $errorUpload = false;

                if( !empty($temp_image) ){
                    $uploaded = $this->RjImage->upload($temp_image, '/'.Configure::read('__Site.truck_photo_folder').'/', String::uuid());

                    if(!empty($uploaded)) {
                        if($uploaded['error']) {
                            $errorUpload = true;
                            $this->MkCommon->setCustomFlash($uploaded['message'], 'error');
                        } else {
                            $data['CalendarIcon']['photo'] = $uploaded['imageName'];
                        }
                    }
                }

                if( $this->CalendarIcon->save($data) && empty($errorUpload) ){
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Icon Kalender'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s Icon Kalender #%s'), $msg, $this->CalendarIcon->id), $this->user_data, $this->RequestHandler, $this->params );
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'calendar_icons'
                    ));
                } else if( empty($errorUpload) ) {
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Icon Kalender'), $msg), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal %s Icon Kalender #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1 );
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Icon Kalender'), $msg), 'error');
            }
        }else{
            
            if($id && $data_local){
                
                $this->request->data = $data_local;
            }
        }

        $this->set('active_menu', 'calendar_icons');
        $this->set('data_local', $data_local);
        $this->render('calendar_icon_form');
    }

    function calendar_icon_toggle($id){
        $this->loadModel('CalendarIcon');
        $locale = $this->CalendarIcon->getData('first', array(
            'conditions' => array(
                'CalendarIcon.id' => $id
            )
        ));

        if($locale){
            $value = true;
            if($locale['CalendarIcon']['status']){
                $value = false;
            }

            $this->CalendarIcon->id = $id;
            $this->CalendarIcon->set('status', $value);
            if($this->CalendarIcon->save()){
                $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
                $this->Log->logActivity( sprintf(__('Sukses merubah status Icon Kalender ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params );
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal merubah status Icon Kalender ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Icon Motor tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    function parts_motor(){
        $this->loadModel('PartsMotor');
        $options = array();

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['name'])){
                $name = urldecode($refine['name']);
                $this->request->data['PartsMotor']['name'] = $name;
                $options['conditions']['PartsMotor.name LIKE '] = '%'.$name.'%';
            }
        }

        $this->paginate = $this->PartsMotor->getData('paginate', $options);
        $parts_motor = $this->paginate('PartsMotor');

        $this->set('active_menu', 'parts_motor');
        $this->set('sub_module_title', 'Part Motor');
        $this->set('parts_motor', $parts_motor);
    }

    function parts_motor_add(){
        $this->set('sub_module_title', 'Tambah Part Motor');
        $this->doPartsMotor();
    }

    function parts_motor_edit($id){
        $this->loadModel('PartsMotor');
        $this->set('sub_module_title', 'Rubah Part Motor');
        $parts_motor = $this->PartsMotor->getData('first', array(
            'conditions' => array(
                'PartsMotor.id' => $id
            )
        ));

        if(!empty($parts_motor)){
            $this->doPartsMotor($id, $parts_motor);
        }else{
            $this->MkCommon->setCustomFlash(__('Part Motor tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'settings',
                'action' => 'parts_motor'
            ));
        }
    }

    function doPartsMotor($id = false, $data_local = false){
        if(!empty($this->request->data)){
            $data = $this->request->data;
            if($id && $data_local){
                $this->PartsMotor->id = $id;
                $msg = 'merubah';
            }else{
                $this->loadModel('PartsMotor');
                $this->PartsMotor->create();
                $msg = 'menambah';
            }

            $data['PartsMotor']['biaya_claim'] = !empty($data['PartsMotor']['biaya_claim']) ? str_replace(',', '', $data['PartsMotor']['biaya_claim']) : 0;
            $data['PartsMotor']['biaya_claim_unit'] = !empty($data['PartsMotor']['biaya_claim_unit']) ? str_replace(',', '', $data['PartsMotor']['biaya_claim_unit']) : 0;

            $this->PartsMotor->set($data);

            if($this->PartsMotor->validates($data)){
                if($this->PartsMotor->save($data)){
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Part Motor'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s Part Motor #%s'), $msg, $this->PartsMotor->id), $this->user_data, $this->RequestHandler, $this->params );
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'parts_motor'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Part Motor'), $msg), 'error');  
                    $this->Log->logActivity( sprintf(__('Gagal %s Part Motor #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1 );   
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Part Motor'), $msg), 'error');
            }
        }else{
            
            if($id && $data_local){
                
                $this->request->data = $data_local;
            }
        }

        $this->set('active_menu', 'parts_motor');
        $this->render('parts_motor_form');
    }

    function parts_motor_toggle($id){
        $this->loadModel('PartsMotor');
        $locale = $this->PartsMotor->getData('first', array(
            'conditions' => array(
                'PartsMotor.id' => $id
            )
        ));

        if($locale){
            $value = true;
            if($locale['PartsMotor']['status']){
                $value = false;
            }

            $this->PartsMotor->id = $id;
            $this->PartsMotor->set('status', $value);
            if($this->PartsMotor->save()){
                $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
                $this->Log->logActivity( sprintf(__('Sukses merubah status jenis perlengkapan ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params );
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal merubah status jenis perlengkapan ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );   
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Part Motor tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    public function uang_jalan_import( $download = false ) {
        if(!empty($download)){
            $link_url = FULL_BASE_URL . '/files/uang_jalan.xls';
            $this->redirect($link_url);
            exit;
        } else {
            $this->loadModel('City');
            $this->loadModel('GroupClassification');
            $this->loadModel('GroupMotor');
            $this->loadModel('UangJalan');
            App::import('Vendor', 'excelreader'.DS.'excel_reader2');

            $this->set('module_title', 'TTUJ');
            $this->set('active_menu', 'uang_jalan');
            $this->set('sub_module_title', __('Import Uang Jalan'));

            if(!empty($this->request->data)) { 
                $Zipped = $this->request->data['Import']['importdata'];

                if($Zipped["name"]) {
                    $filename = $Zipped["name"];
                    $source = $Zipped["tmp_name"];
                    $type = $Zipped["type"];
                    $name = explode(".", $filename);
                    $accepted_types = array('application/vnd.ms-excel', 'application/ms-excel');

                    if(!empty($accepted_types)) {
                        foreach($accepted_types as $mime_type) {
                            if($mime_type == $type) {
                                $okay = true;
                                break;
                            }
                        }
                    }

                    $continue = strtolower($name[1]) == 'xls' ? true : false;

                    if(!$continue) {
                        $this->MkCommon->setCustomFlash(__('Maaf, silahkan upload file dalam bentuk Excel.'), 'error');
                        $this->redirect(array('action'=>'uang_jalan_import'));
                    } else {
                        $path = APP.'webroot'.DS.'files'.DS;
                        $filenoext = basename ($filename, '.xls');
                        $filenoext = basename ($filenoext, '.XLS');
                        $fileunique = uniqid() . '_' . $filenoext;

                        $targetdir = $path . $fileunique . $filename;
                         
                        ini_set('memory_limit', '96M');
                        ini_set('post_max_size', '64M');
                        ini_set('upload_max_filesize', '64M');

                        if(!move_uploaded_file($source, $targetdir)) {
                            $this->MkCommon->setCustomFlash(__('Maaf, terjadi kesalahan. Silahkan coba lagi, atau hubungi Admin kami.'), 'error');
                            $this->redirect(array('action'=>'uang_jalan_import'));
                        }
                    }
                } else {
                    $this->MkCommon->setCustomFlash(__('Maaf, terjadi kesalahan. Silahkan coba lagi, atau hubungi Admin kami.'), 'error');
                    $this->redirect(array('action'=>'uang_jalan_import'));
                }

                $xls_files = glob( $targetdir );

                if(empty($xls_files)) {
                    $this->MkCommon->setCustomFlash(__('Tidak terdapat file excel atau berekstensi .xls pada file zip Anda. Silahkan periksa kembali.'), 'error');
                    $this->redirect(array('action'=>'uang_jalan_import'));
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
                            $data = $dataimport;

                            for ($x=2;$x<=count($data->sheets[0]["cells"]); $x++) {
                                $datavar = array();
                                $flag = true;
                                $i = 1;

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
                                    $branch = $this->GroupBranch->Branch->getData('first', array(
                                        'conditions' => array(
                                            'Branch.code' => $kode_cabang,
                                        ),
                                    ));
                                    $fromCity = $this->City->getData('first', array(
                                        'conditions' => array(
                                            'City.name' => $dari,
                                            'City.status' => 1,
                                        ),
                                    ));
                                    $toCity = $this->City->getData('first', array(
                                        'conditions' => array(
                                            'City.name' => $tujuan,
                                            'City.status' => 1,
                                        ),
                                    ));
                                    $groupClassifications = $this->GroupClassification->getData('list', array(
                                        'conditions' => array(
                                            'GroupClassification.status' => 1,
                                        ),
                                        'fields' => array(
                                            'GroupClassification.lower_name', 'GroupClassification.id'
                                        ),
                                    ));
                                    $groupMotors = $this->GroupMotor->getData('list', array(
                                        'fields' => array(
                                            'GroupMotor.lower_name', 'GroupMotor.id'
                                        ),
                                    ));

                                    if( !empty($fromCity) ) {
                                        $from_city_id = $fromCity['City']['id'];
                                    }
                                    if( !empty($toCity) ) {
                                        $to_city_id = $toCity['City']['id'];
                                    }

                                    if( !empty($uang_jalan_per_unit) ) {
                                        $uang_jalan_kedua = 0;
                                    }

                                    $branch_id = !empty($branch['Branch']['id'])?$branch['Branch']['id']:false;
                                    $requestData['ROW'.($x-1)] = array(
                                        'UangJalan' => array(
                                            'title' => !empty($nama)?$nama:false,
                                            'group_classification_1_id' => !empty($groupClassifications[strtolower($klasifikasi_1)])?$groupClassifications[strtolower($klasifikasi_1)]:0,
                                            'group_classification_2_id' => !empty($groupClassifications[strtolower($klasifikasi_2)])?$groupClassifications[strtolower($klasifikasi_2)]:0,
                                            'group_classification_3_id' => !empty($groupClassifications[strtolower($klasifikasi_3)])?$groupClassifications[strtolower($klasifikasi_3)]:0,
                                            'group_classification_4_id' => !empty($groupClassifications[strtolower($klasifikasi_4)])?$groupClassifications[strtolower($klasifikasi_4)]:0,
                                            'from_city_id' => !empty($from_city_id)?$from_city_id:false,
                                            'to_city_id' => !empty($to_city_id)?$to_city_id:'',
                                            'distance' => !empty($jarak_tempuh)?$jarak_tempuh:false,
                                            'capacity' => !empty($kapasitas)?$kapasitas:false,
                                            'arrive_lead_time' => !empty($lead_time_sampai_tujuan)?$lead_time_sampai_tujuan:false,
                                            'back_lead_time' => !empty($lead_time_ke_pool)?$lead_time_ke_pool:false,
                                            'uang_jalan_1' => !empty($uang_jalan_pertama)?$this->MkCommon->convertPriceToString($uang_jalan_pertama):false, // Borongan
                                            'uang_jalan_2' => !empty($uang_jalan_kedua)?$this->MkCommon->convertPriceToString($uang_jalan_kedua):0,
                                            'uang_jalan_per_unit' => !empty($uang_jalan_per_unit)?$uang_jalan_per_unit:0,
                                            'commission' => !empty($komisi)?$this->MkCommon->convertPriceToString($komisi):0,
                                            'commission_per_unit' => !empty($komisi_per_unit)?$komisi_per_unit:0,
                                            'commission_extra' => !empty($komisi_extra)?$this->MkCommon->convertPriceToString($komisi_extra):0,
                                            'commission_extra_per_unit' => !empty($komisi_extra_per_unit)?$komisi_extra_per_unit:0,
                                            'commission_min_qty' => !empty($min_kapasitas_komisi_extra)?$min_kapasitas_komisi_extra:0,
                                            'asdp' => !empty($uang_penyebrangan)?$this->MkCommon->convertPriceToString($uang_penyebrangan):0,
                                            'asdp_per_unit' => !empty($uang_penyebrangan_per_unit)?$uang_penyebrangan_per_unit:0,
                                            'uang_kawal' => !empty($uang_kawal)?$this->MkCommon->convertPriceToString($uang_kawal):0,
                                            'uang_kawal_per_unit' => !empty($uang_kawal_per_unit)?$uang_kawal_per_unit:0,
                                            'uang_keamanan' => !empty($uang_keamanan)?$this->MkCommon->convertPriceToString($uang_keamanan):0,
                                            'uang_keamanan_per_unit' => !empty($uang_keamanan_per_unit)?$uang_keamanan_per_unit:0,
                                            'uang_jalan_extra' => !empty($uang_jalan_extra)?$this->MkCommon->convertPriceToString($uang_jalan_extra):0,
                                            'uang_jalan_extra_per_unit' => !empty($uang_jalan_extra_per_unit)?$uang_jalan_extra_per_unit:0,
                                            'min_capacity' => !empty($min_kapasitas_ujalan_extra)?$min_kapasitas_ujalan_extra:0,
                                            'branch_id' => $branch_id,
                                        ),
                                    );
                                    
                                    $i = 1;
                                    $idx = 0;
                                    $flag = true;

                                    while ($flag) {
                                        $varGroup = sprintf('group_motor_uang_jalan_%s', $i);

                                        if( !empty($$varGroup) ) {
                                            $varBiaya = sprintf('biaya_uang_jalan_per_group_%s', $i);
                                            $group_motor_id = !empty($groupMotors[strtolower($$varGroup)])?$groupMotors[strtolower($$varGroup)]:'';
                                            $biaya = !empty($$varBiaya)?$this->MkCommon->convertPriceToString($$varBiaya):'';
                                            $requestData['ROW'.($x-1)]['UangJalanTipeMotor']['group_motor_id'][$i] = $group_motor_id;
                                            $requestData['ROW'.($x-1)]['UangJalanTipeMotor']['uang_jalan_1'][$i] = $biaya;
                                            $idx++;
                                        } else {
                                            $flag = false;
                                        }
                                        $i++;
                                    }
                                    
                                    $i = 1;
                                    $idx = 0;
                                    $flag = true;

                                    while ($flag) {
                                        $varGroup = sprintf('group_motor_komisi_%s', $i);

                                        if( !empty($$varGroup) ) {
                                            $varBiaya = sprintf('biaya_komisi_per_group_%s', $i);
                                            $group_motor_id = !empty($groupMotors[strtolower($$varGroup)])?$groupMotors[strtolower($$varGroup)]:'';
                                            $biaya = !empty($$varBiaya)?$this->MkCommon->convertPriceToString($$varBiaya):'';
                                            $requestData['ROW'.($x-1)]['CommissionGroupMotor']['group_motor_id'][$i] = $group_motor_id;
                                            $requestData['ROW'.($x-1)]['CommissionGroupMotor']['commission'][$i] = $biaya;
                                            $idx++;
                                        } else {
                                            $flag = false;
                                        }
                                        $i++;
                                    }

                                    $i = 1;
                                    $idx = 0;
                                    $flag = true;

                                    while ($flag) {
                                        $varGroup = sprintf('group_motor_uang_penyebrangan_%s', $i);

                                        if( !empty($$varGroup) ) {
                                            $varBiaya = sprintf('biaya_uang_penyebrangan_per_group_%s', $i);
                                            $group_motor_id = !empty($groupMotors[strtolower($$varGroup)])?$groupMotors[strtolower($$varGroup)]:'';
                                            $biaya = !empty($$varBiaya)?$this->MkCommon->convertPriceToString($$varBiaya):'';
                                            $requestData['ROW'.($x-1)]['AsdpGroupMotor']['group_motor_id'][$i] = $group_motor_id;
                                            $requestData['ROW'.($x-1)]['AsdpGroupMotor']['asdp'][$i] = $biaya;
                                            $idx++;
                                        } else {
                                            $flag = false;
                                        }
                                        $i++;
                                    }
                                    
                                    $i = 1;
                                    $idx = 0;
                                    $flag = true;

                                    while ($flag) {
                                        $varGroup = sprintf('group_motor_uang_kawal_%s', $i);

                                        if( !empty($$varGroup) ) {
                                            $varBiaya = sprintf('biaya_uang_kawal_per_group_%s', $i);
                                            $group_motor_id = !empty($groupMotors[strtolower($$varGroup)])?$groupMotors[strtolower($$varGroup)]:'';
                                            $biaya = !empty($$varBiaya)?$this->MkCommon->convertPriceToString($$varBiaya):'';
                                            $requestData['ROW'.($x-1)]['UangKawalGroupMotor']['group_motor_id'][$i] = $group_motor_id;
                                            $requestData['ROW'.($x-1)]['UangKawalGroupMotor']['uang_kawal'][$i] = $biaya;
                                            $idx++;
                                        } else {
                                            $flag = false;
                                        }
                                        $i++;
                                    }
                                    
                                    $i = 1;
                                    $idx = 0;
                                    $flag = true;

                                    while ($flag) {
                                        $varGroup = sprintf('group_motor_uang_keamanan_%s', $i);

                                        if( !empty($$varGroup) ) {
                                            $varBiaya = sprintf('biaya_uang_keamanan_per_group_%s', $i);
                                            $group_motor_id = !empty($groupMotors[strtolower($$varGroup)])?$groupMotors[strtolower($$varGroup)]:'';
                                            $biaya = !empty($$varBiaya)?$this->MkCommon->convertPriceToString($$varBiaya):'';
                                            $requestData['ROW'.($x-1)]['UangKeamananGroupMotor']['group_motor_id'][$i] = $group_motor_id;
                                            $requestData['ROW'.($x-1)]['UangKeamananGroupMotor']['uang_keamanan'][$i] = $biaya;
                                            $idx++;
                                        } else {
                                            $flag = false;
                                        }
                                        $i++;
                                    }
                                }
                            }

                            if(!empty($requestData)) {
                                $row_submitted = 1;
                                $successfull_row = 0;
                                $failed_row = 0;
                                $error_message = '';

                                foreach($requestData as $request){
                                    $saveGroupMotor = false;
                                    $saveCommissionGroupMotor = false;
                                    $saveAsdpGroupMotor = false;
                                    $saveUangKawalGroupMotor = false;
                                    $saveUangKeamananGroupMotor = false;
                                    $data = $request;

                                    if( !empty($data['UangJalanTipeMotor']['group_motor_id']) ) {
                                        $resultGroupMotor = $this->saveGroupMotor($data);
                                        $saveGroupMotor = !empty($resultGroupMotor['validates'])?$resultGroupMotor['validates']:false;
                                    } else {
                                        $saveGroupMotor = true;
                                    }

                                    if( !empty($data['CommissionGroupMotor']['group_motor_id']) ) {
                                        $resultCommissionGroupMotor = $this->saveCommissionGroupMotor($data);
                                        $saveCommissionGroupMotor = !empty($resultCommissionGroupMotor['validates'])?$resultCommissionGroupMotor['validates']:false;
                                    } else {
                                        $saveCommissionGroupMotor = true;
                                    }

                                    if( !empty($data['AsdpGroupMotor']['group_motor_id']) ) {
                                        $resultAsdpGroupMotor = $this->saveAsdpGroupMotor($data);
                                        $saveAsdpGroupMotor = !empty($resultAsdpGroupMotor['validates'])?$resultAsdpGroupMotor['validates']:false;
                                    } else {
                                        $saveAsdpGroupMotor = true;
                                    }

                                    if( !empty($data['UangKawalGroupMotor']['group_motor_id']) ) {
                                        $resultUangKawalGroupMotor = $this->saveUangKawalGroupMotor($data);
                                        $saveUangKawalGroupMotor = !empty($resultUangKawalGroupMotor['validates'])?$resultUangKawalGroupMotor['validates']:false;
                                    } else {
                                        $saveUangKawalGroupMotor = true;
                                    }

                                    if( !empty($data['UangKeamananGroupMotor']['group_motor_id']) ) {
                                        $resultUangKeamananGroupMotor = $this->saveUangKeamananGroupMotor($data);
                                        $saveUangKeamananGroupMotor = !empty($resultUangKeamananGroupMotor['validates'])?$resultUangKeamananGroupMotor['validates']:false;
                                    } else {
                                        $saveUangKeamananGroupMotor = true;
                                    }

                                    $this->UangJalan->create();
                                    
                                    if( $saveGroupMotor && $saveCommissionGroupMotor && $saveAsdpGroupMotor && $saveUangKawalGroupMotor && $saveUangKeamananGroupMotor && $this->UangJalan->save($data) ){
                                        if( !empty($data['UangJalan']['uang_jalan_per_unit']) ) {
                                            $this->saveGroupMotor($data, $this->UangJalan->id);
                                        }

                                        if( !empty($data['UangJalan']['commission_per_unit']) ) {
                                            $this->saveCommissionGroupMotor($data, $this->UangJalan->id);
                                        }

                                        if( !empty($data['UangJalan']['asdp_per_unit']) ) {
                                            $this->saveAsdpGroupMotor($data, $this->UangJalan->id);
                                        }

                                        if( !empty($data['UangJalan']['uang_kawal_per_unit']) ) {
                                            $this->saveUangKawalGroupMotor($data, $this->UangJalan->id);
                                        }

                                        if( !empty($data['UangJalan']['uang_keamanan_per_unit']) ) {
                                            $this->saveUangKeamananGroupMotor($data, $this->UangJalan->id);
                                        }

                                        $this->Log->logActivity( __('Sukses upload Uang jalan by Import Excel'), $this->user_data, $this->RequestHandler, $this->params, 1 );
                                        $successfull_row++;
                                    } else {
                                        $failed_row++;
                                        $error_message .= sprintf(__('Gagal pada baris ke %s : Gagal Upload Listing.'), $row_submitted) . '<br>';
                                    }

                                    $row_submitted++;
                                }
                            }
                        }
                    }
                }

                if(!empty($successfull_row)) {
                    $message_import1 = sprintf(__('Import Berhasil: (%s baris), dari total (%s baris)'), $successfull_row, count($requestData));
                    $this->MkCommon->setCustomFlash(__($message_import1), 'success');
                }
                
                if(!empty($error_message)) {
                    $this->MkCommon->setCustomFlash(__($error_message), 'error');
                }
                $this->redirect(array('action'=>'uang_jalan_import'));
            }
        }
    }

    function index(){
        $this->loadModel('Setting');
        $this->set('sub_module_title', 'Pengaturan');
        $data_local = $this->Setting->find('first');
    
        if(!empty($this->request->data)){
            $data = $this->request->data;

            if(!empty($data['Setting']['img_favicon']['name']) && is_array($data['Setting']['img_favicon'])){
                $temp_favicon = $data['Setting']['img_favicon'];
                $data['Setting']['favicon'] = $data['Setting']['img_favicon']['name'];
            } else if( empty($data_local) ){
                $data['Setting']['favicon'] = '';
            }

            if(!empty($data['Setting']['img_logo']['name']) && is_array($data['Setting']['img_logo'])){
                $temp_logo = $data['Setting']['img_logo'];
                $data['Setting']['logo'] = $data['Setting']['img_logo']['name'];
            } else if( empty($data_local) ){
                $data['Setting']['logo'] = '';
            }

            if(!empty($data['Setting']['img_berangkat']['name']) && is_array($data['Setting']['img_berangkat'])){
                $temp_berangkat = $data['Setting']['img_berangkat'];
                $data['Setting']['icon_berangkat'] = $data['Setting']['img_berangkat']['name'];
            } else if( empty($data_local) ){
                $data['Setting']['icon_berangkat'] = '';
            }

            if(!empty($data['Setting']['img_tiba']['name']) && is_array($data['Setting']['img_tiba'])){
                $temp_tiba = $data['Setting']['img_tiba'];
                $data['Setting']['icon_tiba'] = $data['Setting']['img_tiba']['name'];
            } else if( empty($data_local) ){
                $data['Setting']['icon_tiba'] = '';
            }

            if(!empty($data['Setting']['img_bongkaran']['name']) && is_array($data['Setting']['img_bongkaran'])){
                $temp_bongkaran = $data['Setting']['img_bongkaran'];
                $data['Setting']['icon_bongkaran'] = $data['Setting']['img_bongkaran']['name'];
            } else if( empty($data_local) ){
                $data['Setting']['icon_bongkaran'] = '';
            }

            if(!empty($data['Setting']['img_balik']['name']) && is_array($data['Setting']['img_balik'])){
                $temp_balik = $data['Setting']['img_balik'];
                $data['Setting']['icon_balik'] = $data['Setting']['img_balik']['name'];
            } else if( empty($data_local) ){
                $data['Setting']['icon_balik'] = '';
            }

            if(!empty($data['Setting']['img_pool']['name']) && is_array($data['Setting']['img_pool'])){
                $temp_pool = $data['Setting']['img_pool'];
                $data['Setting']['icon_pool'] = $data['Setting']['img_pool']['name'];
            } else if( empty($data_local) ){
                $data['Setting']['icon_pool'] = '';
            }

            if(!empty($data['Setting']['img_laka']['name']) && is_array($data['Setting']['img_laka'])){
                $temp_laka = $data['Setting']['img_laka'];
                $data['Setting']['icon_laka'] = $data['Setting']['img_laka']['name'];
            } else if( empty($data_local) ){
                $data['Setting']['icon_laka'] = '';
            }

            if( !empty($data_local) ){
                $this->Setting->id = $data_local['Setting']['id'];
            }else{
                $this->Setting->create();
            }
            $this->Setting->set($data);

            if($this->Setting->validates($data)){
                if(!empty($temp_favicon) && is_array($temp_favicon)){
                    $uploaded = $this->RjImage->upload($temp_favicon, '/'.Configure::read('__Site.profile_photo_folder').'/', String::uuid());
                    if(!empty($uploaded)) {
                        if($uploaded['error']) {
                            $this->MkCommon->setCustomFlash($uploaded['message'], 'error');
                        } else {
                            $data['Setting']['favicon'] = $uploaded['imageName'];
                        }
                    }
                }
                if(!empty($temp_logo) && is_array($temp_logo)){
                    $uploaded = $this->RjImage->upload($temp_logo, '/'.Configure::read('__Site.profile_photo_folder').'/', String::uuid());
                    if(!empty($uploaded)) {
                        if($uploaded['error']) {
                            $this->MkCommon->setCustomFlash($uploaded['message'], 'error');
                        } else {
                            $data['Setting']['logo'] = $uploaded['imageName'];
                        }
                    }
                }
                if(!empty($temp_berangkat) && is_array($temp_berangkat)){
                    $uploaded = $this->RjImage->upload($temp_berangkat, '/'.Configure::read('__Site.truck_photo_folder').'/', String::uuid());
                    if(!empty($uploaded)) {
                        if($uploaded['error']) {
                            $this->MkCommon->setCustomFlash($uploaded['message'], 'error');
                        } else {
                            $data['Setting']['icon_berangkat'] = $uploaded['imageName'];
                        }
                    }
                }
                if(!empty($temp_tiba) && is_array($temp_tiba)){
                    $uploaded = $this->RjImage->upload($temp_tiba, '/'.Configure::read('__Site.truck_photo_folder').'/', String::uuid());
                    if(!empty($uploaded)) {
                        if($uploaded['error']) {
                            $this->MkCommon->setCustomFlash($uploaded['message'], 'error');
                        } else {
                            $data['Setting']['icon_tiba'] = $uploaded['imageName'];
                        }
                    }
                }
                if(!empty($temp_bongkaran) && is_array($temp_bongkaran)){
                    $uploaded = $this->RjImage->upload($temp_bongkaran, '/'.Configure::read('__Site.truck_photo_folder').'/', String::uuid());
                    if(!empty($uploaded)) {
                        if($uploaded['error']) {
                            $this->MkCommon->setCustomFlash($uploaded['message'], 'error');
                        } else {
                            $data['Setting']['icon_bongkaran'] = $uploaded['imageName'];
                        }
                    }
                }
                if(!empty($temp_balik) && is_array($temp_balik)){
                    $uploaded = $this->RjImage->upload($temp_balik, '/'.Configure::read('__Site.truck_photo_folder').'/', String::uuid());
                    if(!empty($uploaded)) {
                        if($uploaded['error']) {
                            $this->MkCommon->setCustomFlash($uploaded['message'], 'error');
                        } else {
                            $data['Setting']['icon_balik'] = $uploaded['imageName'];
                        }
                    }
                }
                if(!empty($temp_pool) && is_array($temp_pool)){
                    $uploaded = $this->RjImage->upload($temp_pool, '/'.Configure::read('__Site.truck_photo_folder').'/', String::uuid());
                    if(!empty($uploaded)) {
                        if($uploaded['error']) {
                            $this->MkCommon->setCustomFlash($uploaded['message'], 'error');
                        } else {
                            $data['Setting']['icon_pool'] = $uploaded['imageName'];
                        }
                    }
                }
                if(!empty($temp_laka) && is_array($temp_laka)){
                    $uploaded = $this->RjImage->upload($temp_laka, '/'.Configure::read('__Site.truck_photo_folder').'/', String::uuid());
                    if(!empty($uploaded)) {
                        if($uploaded['error']) {
                            $this->MkCommon->setCustomFlash($uploaded['message'], 'error');
                        } else {
                            $data['Setting']['icon_laka'] = $uploaded['imageName'];
                        }
                    }
                }

                if($this->Setting->save($data)){
                    $this->MkCommon->setCustomFlash(__('Sukses menyimpan data'), 'success');
                    $this->Log->logActivity( __('Sukses menyimpan data'), $this->user_data, $this->RequestHandler, $this->params, 1 );
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'index'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal menyimpan data'), 'error'); 
                    $this->Log->logActivity( __('Gagal menyimpan data'), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                }
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal menyimpan data'), 'error');
            }
        }else if($data_local){
            $this->request->data = $data_local;
        }

        $this->set('active_menu', 'settings');
    }

    public function customer_pattern( $customer_id = false ) {
        $this->loadModel('Customer');
        $this->loadModel('CustomerPattern');

        $customer = $this->Customer->getData('first', array(
            'conditions' => array(
                'Customer.id' => $customer_id
            )
        ));

        if( !empty($customer) ) {
            $this->set('sub_module_title', __('Pattern Customer'));
            $customerPattern = $this->CustomerPattern->getData('first', array(
                'conditions' => array(
                    'CustomerPattern.customer_id' => $customer_id
                )
            ));

            $this->doCustomerPattern($customer, $customerPattern);
        } else {
            $this->MkCommon->setCustomFlash(__('Customer tidak ditemukan'), 'error');  
            $this->redirect($this->referer());
        }
    }

    function doCustomerPattern($customer = false, $data_local = false){
        if(!empty($this->request->data)){
            $data = $this->request->data;
            $data['CustomerPattern']['customer_id'] = $customer['Customer']['id'];

            if( !empty($data_local) ){
                $this->CustomerPattern->id = $data_local['CustomerPattern']['id'];
            }else{
                $this->CustomerPattern->create();
            }
            
            $this->CustomerPattern->set($data);

            if($this->CustomerPattern->validates($data)){
                if($this->CustomerPattern->save($data)){
                    $this->MkCommon->setCustomFlash(__('Berhasil menyimpan kode pattern'), 'success');
                    $this->Log->logActivity( sprintf(__('Berhasil menyimpan kode pattern #%s'), $this->CustomerPattern->id), $this->user_data, $this->RequestHandler, $this->params ); 
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'customer_pattern',
                        $customer['Customer']['id'],
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal menyimpan kode pattern Customer #%s'), 'error');  
                    $this->Log->logActivity( sprintf(__('Gagal menyimpan kode pattern Customer #%s'), $customer['Customer']['id']), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                }
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal menyimpan kode pattern Customer'), 'error');
            }
        } else if( !empty($data_local) ){
            $this->request->data = $data_local;
        }

        $this->set('active_menu', 'customers');
        $this->set('module_title', 'Data Master');
        $this->set(compact(
            'customer', 'data_local'
        ));
    }

    function coa_toggle($id){
        $this->loadModel('Coa');
        $locale = $this->Coa->getData('first', array(
            'conditions' => array(
                'Coa.id' => $id
            )
        ));

        if($locale){
            $value = true;
            if($locale['Coa']['status']){
                $value = false;
            }

            $this->Coa->id = $id;
            $this->Coa->set('status', $value);
            if($this->Coa->save()){
                $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
                $this->Log->logActivity( sprintf(__('Sukses merubah status COA ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params );
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal merubah status COA ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
            }
        }else{
            $this->MkCommon->setCustomFlash(__('COA tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    public function tarif_angkut_import( $download = false ) {
        if(!empty($download)){
            $link_url = FULL_BASE_URL . '/files/tarif_angkut.xls';
            $this->redirect($link_url);
            exit;
        } else {
            $this->loadModel('City');
            $this->loadModel('GroupMotor');
            $this->loadModel('TarifAngkutan');
            $this->loadModel('Customer');
            App::import('Vendor', 'excelreader'.DS.'excel_reader2');

            $this->set('active_menu', 'tarif_angkutan');
            $this->set('sub_module_title', __('Import Tarif Angkut'));

            if(!empty($this->request->data)) { 
                $Zipped = $this->request->data['Import']['importdata'];

                if($Zipped["name"]) {
                    $filename = $Zipped["name"];
                    $source = $Zipped["tmp_name"];
                    $type = $Zipped["type"];
                    $name = explode(".", $filename);
                    $accepted_types = array('application/vnd.ms-excel', 'application/ms-excel');

                    if(!empty($accepted_types)) {
                        foreach($accepted_types as $mime_type) {
                            if($mime_type == $type) {
                                $okay = true;
                                break;
                            }
                        }
                    }

                    $continue = strtolower($name[1]) == 'xls' ? true : false;

                    if(!$continue) {
                        $this->MkCommon->setCustomFlash(__('Maaf, silahkan upload file dalam bentuk Excel.'), 'error');
                        $this->redirect(array('action'=>'tarif_angkut_import'));
                    } else {
                        $path = APP.'webroot'.DS.'files'.DS;
                        $filenoext = basename ($filename, '.xls');
                        $filenoext = basename ($filenoext, '.XLS');
                        $fileunique = uniqid() . '_' . $filenoext;

                        $targetdir = $path . $fileunique . $filename;
                         
                        ini_set('memory_limit', '96M');
                        ini_set('post_max_size', '64M');
                        ini_set('upload_max_filesize', '64M');

                        if(!move_uploaded_file($source, $targetdir)) {
                            $this->MkCommon->setCustomFlash(__('Maaf, terjadi kesalahan. Silahkan coba lagi, atau hubungi Admin kami.'), 'error');
                            $this->redirect(array('action'=>'tarif_angkut_import'));
                        }
                    }
                } else {
                    $this->MkCommon->setCustomFlash(__('Maaf, terjadi kesalahan. Silahkan coba lagi, atau hubungi Admin kami.'), 'error');
                    $this->redirect(array('action'=>'tarif_angkut_import'));
                }

                $xls_files = glob( $targetdir );

                if(empty($xls_files)) {
                    $this->MkCommon->setCustomFlash(__('Tidak terdapat file excel atau berekstensi .xls pada file zip Anda. Silahkan periksa kembali.'), 'error');
                    $this->redirect(array('action'=>'tarif_angkut_import'));
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
                            $data = $dataimport;

                            for ($x=2;$x<=count($data->sheets[0]["cells"]); $x++) {
                                $datavar = array();
                                $flag = true;
                                $i = 1;

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
                                    $branch = $this->GroupBranch->Branch->getData('first', array(
                                        'conditions' => array(
                                            'Branch.code' => $kode_cabang,
                                        ),
                                    ));
                                    $fromCity = $this->City->getData('first', array(
                                        'conditions' => array(
                                            'City.name' => $dari,
                                            'City.status' => 1,
                                        ),
                                    ));
                                    $toCity = $this->City->getData('first', array(
                                        'conditions' => array(
                                            'City.name' => $tujuan,
                                            'City.status' => 1,
                                        ),
                                    ));
                                    $groupMotor = $this->GroupMotor->getData('first', array(
                                        'conditions' => array(
                                            'GroupMotor.name' => $group_motor,
                                        ),
                                        'fields' => array(
                                            'GroupMotor.lower_name', 'GroupMotor.id'
                                        ),
                                    ));
                                    $customer = $this->Customer->getData('first', array(
                                        'conditions' => array(
                                            'Customer.code' => $kode_customer,
                                        ),
                                        'fields' => array(
                                            'Customer.code', 'Customer.id'
                                        ),
                                    ));

                                    if( !empty($fromCity) ) {
                                        $from_city_id = $fromCity['City']['id'];
                                        $from_city_name = $fromCity['City']['name'];
                                    }
                                    if( !empty($toCity) ) {
                                        $to_city_id = $toCity['City']['id'];
                                        $to_city_name = $toCity['City']['name'];
                                    }
                                    if( !empty($customer) ) {
                                        $customer_id = $customer['Customer']['id'];
                                    }
                                    if( !empty($groupMotor) ) {
                                        $group_motor_id = $groupMotor['GroupMotor']['id'];
                                    }

                                    $branch_id = !empty($branch['Branch']['id'])?$branch['Branch']['id']:false;
                                    $requestData['ROW'.($x-1)] = array(
                                        'TarifAngkutan' => array(
                                            'type' => !empty($tipe_tarif)?strtolower($tipe_tarif):'',
                                            'name_tarif' => !empty($nama)?$nama:false,
                                            'from_city_name' => !empty($from_city_name)?$from_city_name:false,
                                            'to_city_name' => !empty($to_city_name)?$to_city_name:false,
                                            'from_city_id' => !empty($from_city_id)?$from_city_id:false,
                                            'to_city_id' => !empty($to_city_id)?$to_city_id:'',
                                            'customer_id' => !empty($customer_id)?$customer_id:'',
                                            'capacity' => !empty($kapasitas)?$kapasitas:false,
                                            'jenis_unit' => !empty($jenis_tarif)?strtolower($jenis_tarif):false,
                                            'tarif' => !empty($tarif_angkutan)?$this->MkCommon->convertPriceToString($tarif_angkutan):false,
                                            'group_motor_id' => !empty($group_motor_id)?$group_motor_id:0,
                                            'branch_id' => $branch_id,
                                        ),
                                    );
                                }
                            }

                            if(!empty($requestData)) {
                                $row_submitted = 1;
                                $successfull_row = 0;
                                $failed_row = 0;
                                $error_message = '';

                                foreach($requestData as $request){
                                    $data = $request;
                                    $this->TarifAngkutan->create();
                                    
                                    if( $this->TarifAngkutan->save($data) ){
                                        $this->Log->logActivity( __('Sukses upload Tarif Angkut by Import Excel'), $this->user_data, $this->RequestHandler, $this->params );
                                        $successfull_row++;
                                    } else {
                                        $failed_row++;
                                        $error_message .= sprintf(__('Gagal pada baris ke %s : Gagal Upload Listing.'), $row_submitted) . '<br>';
                                    }

                                    $row_submitted++;
                                }
                            }
                        }
                    }
                }

                if(!empty($successfull_row)) {
                    $message_import1 = sprintf(__('Import Berhasil: (%s baris), dari total (%s baris)'), $successfull_row, count($requestData));
                    $this->MkCommon->setCustomFlash(__($message_import1), 'success');
                }
                
                if(!empty($error_message)) {
                    $this->MkCommon->setCustomFlash(__($error_message), 'error');
                }
                $this->redirect(array('action'=>'tarif_angkut_import'));
            }
        }
    }

    function toggle_city($id, $type){
        if(!empty($id) && !empty($type) && in_array($type, array('pool', 'branch'))){
            $this->loadModel('City');

            $city = $this->City->getData('first', array(
                'conditions' => array(
                    'City.id' => $id
                )
            ));

            if(!empty($city)){
                $field = 'is_'.$type;

                $text = 'pool';
                if($tyep == 'branch'){
                    $text = 'cabang';
                }

                $value = 1;
                if($city['City'][$field]){
                    $value = 0;
                }

                $this->City->id = $id;
                $this->City->set($field, $value);

                if($this->City->save()){
                    $this->MkCommon->setCustomFlash(sprintf(__('Berhasil merubah status %s.'), $text), 'success');
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal merubah status %s.'), $text), 'error');
                }
            }else{
                $this->MkCommon->setCustomFlash(__('Kota tidak ditemukan.'), 'error');
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Kota tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    function saveApprovalDetail ( $data, $approval_id = 0 ) {
        $result = true;

        if( isset($data['ApprovalDetail']['min_amount']) ) {
            $this->loadModel('ApprovalDetail');

            foreach ($data['ApprovalDetail']['min_amount'] as $key => $min_amount) {
                $min_amount = !empty($min_amount)?$this->MkCommon->convertPriceToString($min_amount, 0):0;
                $max_amount = !empty($data['ApprovalDetail']['max_amount'][$key])?$this->MkCommon->convertPriceToString($data['ApprovalDetail']['max_amount'][$key], 0):0;
                
                if( $min_amount <= $max_amount || empty($max_amount) ) {
                    $dataDetail['ApprovalDetail'] = array(
                        'min_amount' => $min_amount,
                        'max_amount' => $max_amount,
                        'approval_id' => $approval_id,
                    );

                    $this->ApprovalDetail->create();
                    $this->ApprovalDetail->set($dataDetail);

                    if( !empty($approval_id) ) {
                        $flagSave = $this->ApprovalDetail->save();
                        $approval_detail_id = $this->ApprovalDetail->id;
                    } else {
                        $flagSave = $this->ApprovalDetail->validates();
                        $approval_detail_id = 0;
                    }

                    if( !$flagSave ) {
                        $result = false;
                    } else {
                        if( !empty($data['ApprovalDetailPosition']['employe_position_id'][$key]) ) {
                            $this->loadModel('ApprovalDetailPosition');

                            if( !empty($approval_detail_id) ) {
                                $this->Approval->ApprovalDetail->ApprovalDetailPosition->updateAll( array(
                                    'ApprovalDetailPosition.status' => 0,
                                ), array(
                                    'ApprovalDetailPosition.approval_detail_id' => $approval_detail_id,
                                ));
                            }

                            foreach ($data['ApprovalDetailPosition']['employe_position_id'][$key] as $idx => $employe_position_id) {
                                $is_priority = !empty($data['ApprovalDetailPosition']['is_priority'][$key][$idx])?$data['ApprovalDetailPosition']['is_priority'][$key][$idx]:'';
                                
                                $dataUser['ApprovalDetailPosition'] = array(
                                    'is_priority' => $is_priority,
                                    'employe_position_id' => $employe_position_id,
                                    'approval_detail_id' => $approval_detail_id,
                                );

                                $this->ApprovalDetailPosition->create();
                                $this->ApprovalDetailPosition->set($dataUser);

                                if( !empty($approval_detail_id) ) {
                                    $flagSave = $this->ApprovalDetailPosition->save();
                                } else {
                                    $flagSave = $this->ApprovalDetailPosition->validates();
                                }

                                if( !$flagSave ) {
                                    $result = false;
                                }
                            }
                        } else {
                            $result = false;
                        }
                    }
                } else {
                    $result = array(
                        'status' => 0,
                        'msg' => __('Nominal transaksi tidak boleh lebih besar dari maksimalnya'),
                    );
                }
            }
        } else {
            $result = false;
        }
        
        return $result;
    }

    function approval_setting(){
        $this->loadModel('Approval');
        $options = array();

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['module'])){
                $name = urldecode($refine['module']);
                $this->request->data['Approval']['module'] = $name;
                $options['conditions']['ApprovalModule.name LIKE '] = '%'.$name.'%';
            }
            if(!empty($refine['position'])){
                $name = urldecode($refine['position']);
                $this->request->data['Approval']['position'] = $name;
                $options['conditions']['EmployePosition.name LIKE '] = '%'.$name.'%';
            }
        }

        $this->paginate = $this->Approval->getData('paginate', $options);
        $approvals = $this->paginate('Approval');

        $this->set('active_menu', 'approval_setting');
        $this->set('sub_module_title', __('Pengaturan Approval'));
        $this->set('approvals', $approvals);
    }

    function approval_setting_add(){
        $this->loadModel('Approval');
        $this->set('sub_module_title', __('Tambah Pengaturan Approval'));
        $this->doApproval();
    }

    function approval_setting_edit( $id ){
        $this->loadModel('Approval');
        $this->set('sub_module_title', __('Edit Pengaturan Approval'));
        $approval = $this->Approval->getData('first', array(
            'conditions' => array(
                'Approval.id' => $id
            ),
        ));

        if(!empty($approval)){
            $this->doApproval($id, $approval);
        }else{
            $this->MkCommon->setCustomFlash(__('Pengaturan approval tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'settings',
                'action' => 'approval_setting'
            ));
        }
    }

    function doApproval( $id = false, $approval = false ){
        $this->loadModel('ApprovalModule');
        $this->loadModel('EmployePosition');

        if(!empty($this->request->data)){
            $data = $this->request->data;
            $error_message = __('Gagal menyimpan pengaturan approval.');

            if( !empty($id) ) {
                $this->Approval->id = $id;
                $msg = __('merubah');
            } else {
                $this->Approval->create();
                $msg = __('membuat');
            }

            $this->Approval->set($data);

            $validate_approval = $this->Approval->validates();
            $validate_approval_detail = $this->saveApprovalDetail($data);

            if( is_array($validate_approval_detail) ) {
                $error_message = !empty($validate_approval_detail['msg'])?$validate_approval_detail['msg']:$error_message;
                $validate_approval_detail = !empty($validate_approval_detail['status'])?$validate_approval_detail['status']:false;
            }

            if( $validate_approval && $validate_approval_detail ){
                if( $this->Approval->save() ){
                    $approval_id = $this->Approval->id;
                    $this->Approval->ApprovalDetail->updateAll( array(
                        'ApprovalDetail.status' => 0,
                    ), array(
                        'ApprovalDetail.approval_id' => $approval_id,
                    ));

                    $validate_approval_detail = $this->saveApprovalDetail($data, $approval_id);

                    $this->Log->logActivity( sprintf(__('Berhasil %s data approval ID #%s'), $msg, $approval_id), $this->user_data, $this->RequestHandler, $this->params );
                    $this->MkCommon->setCustomFlash('Berhasil melakukan pengaturan approval.', 'success');
                    $this->redirect(array(
                        'action' => 'approval_setting'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash($error_message, 'error');
                }
            }else{
                $this->MkCommon->setCustomFlash($error_message, 'error');
            }
        } else if( !empty($approval) ) {
            $this->request->data = $approval;
            $approval = $this->Approval->ApprovalDetail->getMerge($approval, $id);

            if( !empty($approval['ApprovalDetail']) ) {
                foreach ($approval['ApprovalDetail'] as $key => $value) {
                    $approval_detail_id = !empty($value['ApprovalDetail']['id'])?$value['ApprovalDetail']['id']:false;
                    $min_amount = !empty($value['ApprovalDetail']['min_amount'])?$value['ApprovalDetail']['min_amount']:false;
                    $max_amount = !empty($value['ApprovalDetail']['max_amount'])?$value['ApprovalDetail']['max_amount']:false;
                    $approvalDetailPosition = $this->Approval->ApprovalDetail->ApprovalDetailPosition->getMerge($value, $approval_detail_id);

                    $this->request->data['ApprovalDetail']['min_amount'][$key] = $min_amount;
                    $this->request->data['ApprovalDetail']['max_amount'][$key] = $max_amount;

                    if( !empty($approvalDetailPosition['ApprovalDetailPosition']) ) {
                        foreach ($approvalDetailPosition['ApprovalDetailPosition'] as $key_user => $approvalPosition) {
                            $employe_position_id = !empty($approvalPosition['ApprovalDetailPosition']['employe_position_id'])?$approvalPosition['ApprovalDetailPosition']['employe_position_id']:false;
                            $is_priority = !empty($approvalPosition['ApprovalDetailPosition']['is_priority'])?$approvalPosition['ApprovalDetailPosition']['is_priority']:false;

                            $this->request->data['ApprovalDetailPosition']['employe_position_id'][$key][$key_user] = $employe_position_id;
                            $this->request->data['ApprovalDetailPosition']['is_priority'][$key][$key_user] = $is_priority;
                        }
                    }
                }
            }
        }

        $approvalModules = $this->ApprovalModule->find('list', array(
            'conditions' => array(
                'ApprovalModule.status' => 1,
            ),
            'order' => array(
                'ApprovalModule.name' => 'ASC',
            ),
        ));
        $employePositions = $this->EmployePosition->getData('list');

        $this->set('active_menu', 'approval_setting');
        $this->set(compact(
            'approvalModules', 'employePositions'
        ));
        $this->render('approval_setting_form');
    }

    function approval_setting_toggle( $id = false ){
        $this->loadModel('Approval');
        $locale = $this->Approval->getData('first', array(
            'conditions' => array(
                'Approval.id' => $id
            )
        ));

        if($locale){
            $this->Approval->id = $id;
            $this->Approval->set('status', 0);

            if($this->Approval->save()){
                $this->MkCommon->setCustomFlash(__('Sukses menghapus data approval.'), 'success');
                $this->Log->logActivity( sprintf(__('Sukses menghapus data approval ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params ); 
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal menghapus data approval_id.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal menghapus data approval ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Data tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    public function branches() {
        $this->loadModel('Branch');
        $conditions = $this->RjSetting->_processRefine(array(), $this->params);

        $this->paginate = $this->Branch->getData('paginate', array(
            'conditions' => $conditions,
            'limit' => 10,
        ));
        $branchs = $this->paginate('Branch');

        $this->set('active_menu', 'branches');
        $this->set('sub_module_title', __('Cabang'));
        $this->set('branchs', $branchs);
    }

    public function _callSupportBranch( $id = false ) {
        $urlReferer = array(
            'controller' => 'settings',
            'action' => 'branches',
            'admin' => false,
        );

        if( !empty($id) ) {
            $value = $this->Branch->getData('first', array(
                'conditions' => array(
                    'Branch.id' => $id,
                ),
            ));

            if( empty($value) ) {
                $this->MkCommon->redirectReferer(__('Cabang tidak ditemukan'), 'error', $urlReferer);
            }
        } else {
            $value = false;
        }

        $result = $this->Branch->doSave( $this->request->data, $value, $id );
        $this->MkCommon->setProcessParams($result, $urlReferer);

        $cities = $this->Branch->City->getData('list');
        $coas = $this->Branch->Coa->getData('list', false, true, array(
            'status' => 'cash_bank_child',
        ));
        $branch_cities = $this->Branch->getData('list');
        $this->MkCommon->_layout_file('select');

        $this->set('active_menu', 'branches');
        $this->set(compact(
            'cities', 'coas', 'branch_cities'
        ));
        $this->render('branch_form');
    }

    public function branch_add() {
        $this->loadModel('Branch');
        $this->set('sub_module_title', __('Tambah Cabang'));
        $this->_callSupportBranch();
    }

    public function branch_edit( $id = false ) {
        $this->loadModel('Branch');
        $this->set('sub_module_title', __('Edit Cabang'));
        $this->_callSupportBranch( $id );
    }

    function branch_toggle( $id = false ){
        $this->loadModel('Branch');

        $value = $this->Branch->getData('first', array(
            'conditions' => array(
                'Branch.id' => $id
            )
        ));

        if(!empty($value)){
            $result = $this->Branch->doToggle( $id );
            $this->MkCommon->setProcessParams($result);
        }else{
            $this->MkCommon->redirectReferer(__('Cabang tidak ditemukan'), 'error');
        }
    }
}