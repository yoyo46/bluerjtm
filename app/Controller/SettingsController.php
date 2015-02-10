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

    function search( $index = 'index' ){
        $refine = array();

        if(!empty($this->request->data)) {
            $refine = $this->RjSetting->processRefine($this->request->data);
            $params = $this->RjSetting->generateSearchURL($refine);
            $params['action'] = $index;

            $this->redirect($params);
        }
        $this->redirect('/');
    }

    function cities(){
        if( in_array('view_cities', $this->allowModule) ) {
            $this->loadModel('City');
            $options = array();

            if(!empty($this->params['named'])){
                $refine = $this->params['named'];

                if(!empty($refine['name'])){
                    $name = urldecode($refine['name']);
                    $this->request->data['City']['name'] = $name;
                    $options['conditions']['City.name LIKE '] = '%'.$name.'%';
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
        } else {
            $this->redirect($this->referer());
        }
    }

    function city_add(){
        if( in_array('insert_cities', $this->allowModule) ) {
            $this->set('sub_module_title', 'Tambah Kota');
            $this->docity();
        } else {
            $this->redirect($this->referer());
        }
    }

    function city_edit($id){
        if( in_array('update_cities', $this->allowModule) ) {
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
        } else {
            $this->redirect($this->referer());
        }
    }

    function docity($id = false, $data_local = false){
        if(!empty($this->request->data)){
            $data = $this->request->data;
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
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Kota'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s Kota'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'cities'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Kota'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s Kota'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
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
        if( in_array('delete_cities', $this->allowModule) ) {
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
                    $this->Log->logActivity( sprintf(__('Sukses merubah status Kota ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal merubah status Kota ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                }
            }else{
                $this->MkCommon->setCustomFlash(__('Kota tidak ditemukan.'), 'error');
            }

            $this->redirect($this->referer());
        } else {
            $this->redirect($this->referer());
        }
    }

    function customers(){
        if( in_array('view_customers', $this->allowModule) ) {
            $this->loadModel('Customer');
            $options = array();

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
                if(!empty($refine['customer_group_id'])){
                    $customer_group_id = urldecode($refine['customer_group_id']);
                    $this->request->data['Customer']['customer_group_id'] = $customer_group_id;
                    $options['conditions']['Customer.customer_group_id '] = $customer_group_id;
                }
            }
            $this->paginate = $this->Customer->getData('paginate', $options);
            $truck_customers = $this->paginate('Customer');
            $customerTypes  = $this->Customer->CustomerType->getData('list', false, true);
            $customerGroups  = $this->Customer->CustomerGroup->getData('list');

            $this->set('active_menu', 'customers');
            $this->set('module_title', 'Data Master');
            $this->set('sub_module_title', 'Customer');
            $this->set(compact(
                'customerTypes', 'truck_customers',
                'customerGroups'
            ));
        } else {
            $this->redirect($this->referer());
        }
    }

    function customer_add(){
        if( in_array('insert_customers', $this->allowModule) ) {
            $this->loadModel('Customer');
            $this->set('sub_module_title', 'Tambah Customer');
            $this->doCustomer();
        } else {
            $this->redirect($this->referer());
        }
    }

    function customer_edit($id){
        if( in_array('update_customers', $this->allowModule) ) {
            $this->loadModel('Customer');
            $this->set('sub_module_title', 'Rubah Customer');
            $customer = $this->Customer->getData('first', array(
                'conditions' => array(
                    'Customer.id' => $id
                )
            ));

            if(!empty($customer)){
                $this->doCustomer($id, $customer);
            }else{
                $this->MkCommon->setCustomFlash(__('Customer tidak ditemukan'), 'error');  
                $this->redirect(array(
                    'controller' => 'settings',
                    'action' => 'customers'
                ));
            }
        } else {
            $this->redirect($this->referer());
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
            $this->Customer->set($data);

            if($this->Customer->validates($data)){
                if($this->Customer->save($data)){
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Customer'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s Customer'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'customers'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Customer'), $msg), 'error');  
                    $this->Log->logActivity( sprintf(__('Gagal %s Customer'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Customer'), $msg), 'error');
            }
        }else{
            
            if($id && $data_local){
                
                $this->request->data = $data_local;
            }
        }

        $customerTypes  = $this->Customer->CustomerType->getData('list', false, true);
        $customerGroups  = $this->Customer->CustomerGroup->getData('list');
        $banks  = $this->Customer->Bank->getData('list', array(
            'fields' => array(
                'Bank.id', 'Bank.bank_name'
            ),
        ));

        $this->set('active_menu', 'customers');
        $this->set('module_title', 'Data Master');
        $this->set(compact(
            'customerTypes', 'customerGroups', 'banks'
        ));
        $this->render('customer_form');
    }

    function customer_toggle($id){
        if( in_array('delete_customers', $this->allowModule) ) {
            $this->loadModel('Customer');

            $locale = $this->Customer->getData('first', array(
                'conditions' => array(
                    'Customer.id' => $id
                )
            ));

            if($locale){
                $value = true;
                if($locale['Customer']['status']){
                    $value = false;
                }

                $this->Customer->id = $id;
                $this->Customer->set('status', 0);

                if($this->Customer->save()){
                    $this->MkCommon->setCustomFlash(__('Customer telah berhasil dihapus.'), 'success');
                    $this->Log->logActivity( sprintf(__('Customer ID #%s telah berhasil dihapus.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal menghapus Customer.'), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal menghapus Customer ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                }
            }else{
                $this->MkCommon->setCustomFlash(__('Customer tidak ditemukan.'), 'error');
            }

            $this->redirect($this->referer());
        } else {
            $this->redirect($this->referer());
        }
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
                    
                    $this->Log->logActivity( sprintf(__('Sukses %s Tipe Customer'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 ); 

                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'customer_types'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Tipe Customer'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s Tipe Customer'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );  
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
                $this->Log->logActivity( sprintf(__('Sukses merubah status Tipe customer.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );  
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal merubah status Tipe customer.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );  
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Tipe Customer tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    function vendors(){
        if( in_array('view_vendors', $this->allowModule) ) {
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
        } else {
            $this->redirect($this->referer());
        }
    }

    function vendor_add(){
        if( in_array('insert_vendors', $this->allowModule) ) {
            $this->loadModel('Vendor');
            $this->set('sub_module_title', 'Tambah Vendor');
            $this->doVendor();
        } else {
            $this->redirect($this->referer());
        }
    }

    function vendor_edit($id){
        if( in_array('update_vendors', $this->allowModule) ) {
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
        } else {
            $this->redirect($this->referer());
        }
    }

    function doVendor($id = false, $data_local = false){
        if(!empty($this->request->data)){
            $data = $this->request->data;
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
                    $this->Log->logActivity( sprintf(__('Sukses %s Vendor'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );  
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'vendors'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Vendor'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s Vendor'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );   
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
        if( in_array('delete_vendors', $this->allowModule) ) {
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
                    $this->Log->logActivity( sprintf(__('Sukses merubah status vendor ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );   
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal merubah status vendor ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );   
                }
            }else{
                $this->MkCommon->setCustomFlash(__('Vendor tidak ditemukan.'), 'error');
            }

            $this->redirect($this->referer());
        } else {
            $this->redirect($this->referer());
        }
    }

    public function coas() {
        if( in_array('view_coas', $this->allowModule) ) {
            $this->loadModel('Coa');
            $coas = $this->Coa->getData('threaded', array(
                'conditions' => array(
                    'Coa.status' => 1
                ),
            ));

            $this->set('active_menu', 'coas');
            $this->set('module_title', 'Data Master');
            $this->set('sub_module_title', 'COA ( Chart Of Account )');
            $this->set('coas', $coas);
        } else {
            $this->redirect($this->referer());
        }
    }

    public function coa_add( $parent_id = false ) {
        if( in_array('insert_coas', $this->allowModule) ) {
            $this->loadModel('Coa');
            $coa = false;

            if( !empty($parent_id) ) {
                $coa = $this->Coa->getData('first', array(
                    'conditions' => array(
                        'Coa.status' => 1
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
        } else {
            $this->redirect($this->referer());
        }
    }

    function doCoa($id = false, $data_local = false, $parent_id = false, $coa = false ){
        if(!empty($this->request->data)){
            $data = $this->request->data;

            if($id && $data_local){
                $this->Coa->id = $id;
                $msg = 'merubah';
            }else{
                $this->Coa->create();
                $msg = 'menambah';
            }

            if( !empty($coa) ) {
                $data['Coa']['parent_id'] = $parent_id;

                if( !empty($data['Coa']['code']) ) {
                    $data['Coa']['code'] = sprintf('%s%s', $coa['Coa']['code'], $data['Coa']['code']);
                }
            }
            
            $this->Coa->set($data);

            if($this->Coa->validates($data)){
                if($this->Coa->save($data)){
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Coa'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s Coa'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );   
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'coas'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Coa'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s Coa'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );   
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
        $this->render('coa_form');
    }

    function companies(){
        if( in_array('view_companies', $this->allowModule) ) {
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
        } else {
            $this->redirect($this->referer());
        }
    }

    function company_add(){
        if( in_array('insert_companies', $this->allowModule) ) {
            $this->loadModel('Company');
            $this->set('sub_module_title', 'Tambah Company');
            $this->doCompany();
        } else {
            $this->redirect($this->referer());
        }
    }

    function company_edit($id){
        if( in_array('update_companies', $this->allowModule) ) {
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
        } else {
            $this->redirect($this->referer());
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
                    $this->Log->logActivity( sprintf(__('Sukses %s Company'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );   
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'companies'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Company'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s Company'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );    
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
        if( in_array('delete_companies', $this->allowModule) ) {
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
                    $this->Log->logActivity( sprintf(__('Sukses merubah status company ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );    
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal merubah status company ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );    
                }
            }else{
                $this->MkCommon->setCustomFlash(__('Company tidak ditemukan.'), 'error');
            }

            $this->redirect($this->referer());
        } else {
            $this->redirect($this->referer());
        }
    }

    public function uang_jalan() {
        if( in_array('view_uang_jalan', $this->allowModule) ) {
            $this->loadModel('UangJalan');
            $options = array();

            if(!empty($this->params['named'])){
                $refine = $this->params['named'];

                if(!empty($refine['name'])){
                    $name = urldecode($refine['name']);
                    $this->request->data['Company']['name'] = $name;
                    $options['conditions']['Company.name LIKE '] = '%'.$name.'%';
                }
            }
            $this->paginate = $this->UangJalan->getData('paginate', $options);
            $uangJalans = $this->paginate('UangJalan');

            $this->set('active_menu', 'uang_jalan');
            $this->set('module_title', 'Data Master');
            $this->set('sub_module_title', 'Uang Jalan');
            $this->set(compact(
                'uangJalans'
            ));
        } else {
            $this->redirect($this->referer());
        }
    }

    public function uang_jalan_add() {
        if( in_array('insert_uang_jalan', $this->allowModule) ) {
            $this->loadModel('UangJalan');
            $this->set('sub_module_title', 'Tambah Uang Jalan');
            $this->doUangJalan();
        } else {
            $this->redirect($this->referer());
        }
    }

    function saveTipeMotor ( $data = false, $uang_jalan_id = false ) {
        $result = array(
            'validates' => true,
            'data' => false,
        );
        if( !empty($uang_jalan_id) ) {
            $this->UangJalan->UangJalanTipeMotor->updateAll( array(
                'UangJalanTipeMotor.status' => 0,
            ), array(
                'UangJalanTipeMotor.uang_jalan_id' => $uang_jalan_id,
            ));
        }

        if( !empty($data['UangJalanTipeMotor']['tipe_motor_id']) ) {
            foreach ($data['UangJalanTipeMotor']['tipe_motor_id'] as $key => $tipe_motor_id) {
                $dataValidate['UangJalanTipeMotor']['tipe_motor_id'] = $tipe_motor_id;
                $dataValidate['UangJalanTipeMotor']['uang_jalan_1'] = !empty($data['UangJalanTipeMotor']['uang_jalan_1'][$key])?$this->MkCommon->convertPriceToString($data['UangJalanTipeMotor']['uang_jalan_1'][$key], 0):0;
                $dataValidate['UangJalanTipeMotor']['uang_kuli_muat'] = !empty($data['UangJalanTipeMotor']['uang_kuli_muat'][$key])?$this->MkCommon->convertPriceToString($data['UangJalanTipeMotor']['uang_kuli_muat'][$key], 0):0;
                $dataValidate['UangJalanTipeMotor']['uang_kuli_bongkar'] = !empty($data['UangJalanTipeMotor']['uang_kuli_bongkar'][$key])?$this->MkCommon->convertPriceToString($data['UangJalanTipeMotor']['uang_kuli_bongkar'][$key], 0):0;
                
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

    function doUangJalan($id = false, $data_local = false){
        $this->loadModel('City');
        $this->loadModel('TipeMotor');

        if(!empty($this->request->data)){
            $data = $this->request->data;
            if($id && $data_local){
                $this->UangJalan->id = $id;
                $msg = 'merubah';
            }else{
                $this->UangJalan->create();
                $msg = 'menambah';
            }

            $data['UangJalan']['commission'] = $this->MkCommon->convertPriceToString($data['UangJalan']['commission']);
            $data['UangJalan']['commission_extra'] = $this->MkCommon->convertPriceToString($data['UangJalan']['commission_extra'], 0);
            $data['UangJalan']['commission_min_qty'] = $this->MkCommon->convertPriceToString($data['UangJalan']['commission_min_qty'], 0);
            $data['UangJalan']['uang_jalan_1'] = $this->MkCommon->convertPriceToString($data['UangJalan']['uang_jalan_1']);
            $data['UangJalan']['uang_jalan_2'] = $this->MkCommon->convertPriceToString($data['UangJalan']['uang_jalan_2'], 0);
            $data['UangJalan']['uang_kuli_muat'] = $this->MkCommon->convertPriceToString($data['UangJalan']['uang_kuli_muat'], 0);
            $data['UangJalan']['uang_kuli_bongkar'] = $this->MkCommon->convertPriceToString($data['UangJalan']['uang_kuli_bongkar'], 0);
            $data['UangJalan']['asdp'] = $this->MkCommon->convertPriceToString($data['UangJalan']['asdp'], 0);
            $data['UangJalan']['uang_kawal'] = $this->MkCommon->convertPriceToString($data['UangJalan']['uang_kawal'], 0);
            $data['UangJalan']['uang_keamanan'] = $this->MkCommon->convertPriceToString($data['UangJalan']['uang_keamanan'], 0);
            $data['UangJalan']['uang_jalan_extra'] = $this->MkCommon->convertPriceToString($data['UangJalan']['uang_jalan_extra'], 0);
            $data['UangJalan']['group_classification_1_id'] = !empty($data['UangJalan']['group_classification_1_id'])?$data['UangJalan']['group_classification_1_id']:0;
            $data['UangJalan']['group_classification_2_id'] = !empty($data['UangJalan']['group_classification_2_id'])?$data['UangJalan']['group_classification_2_id']:0;
            $data['UangJalan']['group_classification_3_id'] = !empty($data['UangJalan']['group_classification_3_id'])?$data['UangJalan']['group_classification_3_id']:0;
            $data['UangJalan']['group_classification_4_id'] = !empty($data['UangJalan']['group_classification_4_id'])?$data['UangJalan']['group_classification_4_id']:0;

            if( !empty($data['UangJalan']['uang_jalan_per_unit']) ) {
                $data['UangJalan']['uang_jalan_2'] = 0;
            }
            
            $this->UangJalan->set($data);

            if($this->UangJalan->validates($data)){
                $saveTipeMotor = false;

                if( !empty($data['UangJalanTipeMotor']['tipe_motor_id']) ) {
                    $resultTipeMotor = $this->saveTipeMotor($data);
                    $saveTipeMotor = !empty($resultTipeMotor['validates'])?$resultTipeMotor['validates']:false;
                } else {
                    $saveTipeMotor = true;
                }

                if( $saveTipeMotor && $this->UangJalan->save($data) ){
                    $this->saveTipeMotor($data, $this->UangJalan->id);

                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Uang jalan'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s Uang jalan'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );    
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'uang_jalan'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Uang jalan'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s Uang jalan'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );     
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Uang jalan'), $msg), 'error');
            }
        }else{
            
            if($id && $data_local){
                $data_local = $this->MkCommon->getUangJalanTipeMotor($data_local);
                $this->request->data = $data_local;
                $this->request->data['UangJalan']['commission_min_qty'] = !empty($this->request->data['UangJalan']['commission_min_qty'])?$this->request->data['UangJalan']['commission_min_qty']:'';
            }
        }
        // $customers = $this->UangJalan->Customer->getData('list', array(
        //     'conditions' => array(
        //         'Customer.status' => 1
        //     ),
        // ));
        $fromCities = $this->City->getData('list', array(
            'conditions' => array(
                'City.status' => 1,
                // 'City.is_asal' => 1
            ),
            'order' => array(
                'City.name' => 'ASC',
            ),
        ), false);
        $toCities = $this->City->getData('list', array(
            'conditions' => array(
                'City.status' => 1,
                // 'City.is_tujuan' => 1
            ),
            'order' => array(
                'City.name' => 'ASC',
            ),
        ), false);
        $this->loadModel('GroupClassification');
        $groupClassifications = $this->GroupClassification->find('list', array(
            'conditions' => array(
                'status' => 1
            ),
        ));
        $tipeMotors = $this->TipeMotor->getData('list', array(
            'fields' => array(
                'TipeMotor.id', 'TipeMotor.tipe_motor_color',
            ),
        ));

        $this->set('active_menu', 'uang_jalan');
        $this->set('module_title', 'Data Master');
        $this->set(compact(
            'fromCities', 'groupClassifications', 'toCities',
            'tipeMotors'
        ));
        $this->render('uang_jalan_form');
    }

    function uang_jalan_edit($id){
        if( in_array('update_uang_jalan', $this->allowModule) ) {
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
        } else {
            $this->redirect($this->referer());
        }
    }

    function uang_jalan_toggle( $id = false ){
        if( in_array('delete_uang_jalan', $this->allowModule) ) {
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
                    $this->Log->logActivity( sprintf(__('Sukses merubah status Uang Jalan ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );      
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal merubah status Uang Jalan ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );      
                }
            }else{
                $this->MkCommon->setCustomFlash(__('Uang Jalan tidak ditemukan.'), 'error');
            }

            $this->redirect($this->referer());
        } else {
            $this->redirect($this->referer());
        }
    }

    function perlengkapan(){
        if( in_array('view_perlengkapan', $this->allowModule) ) {
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
        } else {
            $this->redirect($this->referer());
        }
    }

    function perlengkapan_add(){
        if( in_array('insert_perlengkapan', $this->allowModule) ) {
            $this->loadModel('Perlengkapan');
            $this->set('sub_module_title', 'Tambah Perlengkapan');
            $this->doPerlengkapan();
        } else {
            $this->redirect($this->referer());
        }
    }

    function perlengkapan_edit($id){
        if( in_array('update_perlengkapan', $this->allowModule) ) {
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
        } else {
            $this->redirect($this->referer());
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
                    $this->Log->logActivity( sprintf(__('Sukses %s Perlengkapan'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );     
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'perlengkapan'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Perlengkapan'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s Perlengkapan'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );      
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
        if( in_array('delete_perlengkapan', $this->allowModule) ) {
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
                    $this->Log->logActivity( sprintf(__('Sukses merubah status perlengkapan ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );      
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal merubah status perlengkapan ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );      
                }
            }else{
                $this->MkCommon->setCustomFlash(__('Perlengkapan tidak ditemukan.'), 'error');
            }

            $this->redirect($this->referer());
        } else {
            $this->redirect($this->referer());
        }
    }

    function type_motors(){
        $this->loadModel('TipeMotor');
        $options = array(
            'contain' => array(
                'ColorMotor'
            )
        );

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['name'])){
                $name = urldecode($refine['name']);
                $this->request->data['TipeMotor']['name'] = $name;
                $options['conditions']['TipeMotor.name LIKE '] = '%'.$name.'%';
            }
        }

        $this->paginate = $this->TipeMotor->getData('paginate', $options);
        $type_motors = $this->paginate('TipeMotor');

        $this->set('active_menu', 'type_motor');
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
                    $this->Log->logActivity( sprintf(__('Sukses %s Tipe Motor'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );      
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'type_motors'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Tipe Motor'), $msg), 'error');  
                    $this->Log->logActivity( sprintf(__('Gagal %s Tipe Motor'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );      
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Tipe Motor'), $msg), 'error');
            }
        }else{
            
            if($id && $data_local){
                
                $this->request->data = $data_local;
            }
        }

        $this->loadModel('ColorMotor');
        $this->loadModel('CodeMotor');
        $this->loadModel('GroupMotor');

        $colors = $this->ColorMotor->getData('list', array(
            'conditions' => array(
                'ColorMotor.status' => 1
            ),
            'fields' => array(
                'ColorMotor.id', 'ColorMotor.name'
            )
        ));
        $this->set('colors', $colors);

        $group_motors = $this->GroupMotor->getData('list', array(
            'conditions' => array(
                'GroupMotor.status' => 1
            ),
            'fields' => array(
                'GroupMotor.id', 'GroupMotor.name'
            )
        ));
        $this->set('group_motors', $group_motors);

        $code_motors = $this->CodeMotor->getData('list', array(
            'conditions' => array(
                'CodeMotor.status' => 1
            ),
            'fields' => array(
                'CodeMotor.id', 'CodeMotor.name'
            )
        ));
        $this->set('code_motors', $code_motors);


        $this->set('active_menu', 'type_motor');
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
                $this->Log->logActivity( sprintf(__('Sukses merubah status tipe motor.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );      
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal merubah status tipe motor.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );      
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

        $this->set('active_menu', 'type_motor');
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
                    $this->Log->logActivity( sprintf(__('Sukses %s Warna Motor'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );      
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'colors'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Warna Motor'), $msg), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal %s Warna Motor'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );        
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Warna Motor'), $msg), 'error');
            }
        }else{
            
            if($id && $data_local){
                
                $this->request->data = $data_local;
            }
        }

        $this->set('active_menu', 'type_motor');
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
                $this->Log->logActivity( sprintf(__('Sukses merubah status color motor ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );        
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
                    $this->Log->logActivity( sprintf(__('Sukses %s Provinsi'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );        
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'regions'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Provinsi'), $msg), 'error');  
                    $this->Log->logActivity( sprintf(__('Gagal %s Provinsi'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );        
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
                $this->Log->logActivity( sprintf(__('Sukses merubah status Provinsi ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );        
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
        $options = array(
            'conditions' => array(
                'status' => 1
            )
        );

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

        $this->set('active_menu', 'type_motor');
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
            )
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
                    $this->Log->logActivity( sprintf(__('Sukses %s Grup Motor'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );        
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'group_motors'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Grup Motor'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s Grup Motor'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );         
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Grup Motor'), $msg), 'error');
            }
        }else{
            
            if($id && $data_local){
                
                $this->request->data = $data_local;
            }
        }

        $this->set('active_menu', 'type_motor');
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
                $this->Log->logActivity( sprintf(__('Sukses merubah status group motor.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );         
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal merubah status group motor.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );         
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
                    $this->Log->logActivity( sprintf(__('Sukses %s Kode Motor'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );         
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'code_motors'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Kode Motor'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s Kode Motor'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );          
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
                $this->Log->logActivity( sprintf(__('Sukses merubah status code motor #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal merubah status code motor #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Kode Motor tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    function branches(){
        if( in_array('view_branches', $this->allowModule) ) {
            $this->loadModel('Branch');
            $options = array();

            if(!empty($this->params['named'])){
                $refine = $this->params['named'];

                if(!empty($refine['name'])){
                    $name = urldecode($refine['name']);
                    $this->request->data['Branch']['name'] = $name;
                    $options['conditions']['Branch.name LIKE '] = '%'.$name.'%';
                }
            }

            $this->paginate = $this->Branch->getData('paginate', $options);
            $branches = $this->paginate('Branch');

            $this->set('active_menu', 'branches');
            $this->set('sub_module_title', 'Cabang Perusahaan');
            $this->set('branches', $branches);
        } else {
            $this->redirect($this->referer());
        }
    }

    function branch_add(){
        if( in_array('insert_branches', $this->allowModule) ) {
            $this->loadModel('Branch');
            $this->set('sub_module_title', 'Tambah Cabang');
            $this->doBranch();
        } else {
            $this->redirect($this->referer());
        }
    }

    function branch_edit($id){
        if( in_array('update_branches', $this->allowModule) ) {
            $this->loadModel('Branch');
            $this->set('sub_module_title', 'Rubah Cabang');
            $branch = $this->Branch->getData('first', array(
                'conditions' => array(
                    'Branch.id' => $id
                )
            ));

            if(!empty($branch)){
                $this->doBranch($id, $branch);
            }else{
                $this->MkCommon->setCustomFlash(__('Cabang tidak ditemukan'), 'error');  
                $this->redirect(array(
                    'controller' => 'settings',
                    'action' => 'branches'
                ));
            }
        } else {
            $this->redirect($this->referer());
        }
    }

    function doBranch($id = false, $data_local = false){
        if(!empty($this->request->data)){
            $data = $this->request->data;

            if($id && $data_local){
                $this->Branch->id = $id;
                $msg = 'merubah';
            }else{
                $this->Branch->create();
                $msg = 'menambah';
            }
            $this->Branch->set($data);

            if($this->Branch->validates($data)){
                if($this->Branch->save($data)){
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Cabang'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s Cabang'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'branches'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Branch'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s Cabang'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Branch'), $msg), 'error');
            }
        } else if($id && $data_local){
            $this->request->data = $data_local;
        }

        $this->set('active_menu', 'branches');
        $this->render('branch_form');
    }

    function branch_toggle($id){
        if( in_array('delete_branches', $this->allowModule) ) {
            $this->loadModel('Branch');
            $locale = $this->Branch->getData('first', array(
                'conditions' => array(
                    'Branch.id' => $id
                )
            ));

            if($locale){
                $value = true;
                if($locale['Branch']['status']){
                    $value = false;
                }

                $this->Branch->id = $id;
                $this->Branch->set('status', 0);

                if($this->Branch->save()){
                    $this->MkCommon->setCustomFlash(__('Sukses menghapus data cabang.'), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses menghapus data cabang ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal menghapus data cabang.'), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal menghapus data cabang ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                }
            }else{
                $this->MkCommon->setCustomFlash(__('Cabang tidak ditemukan.'), 'error');
            }

            $this->redirect($this->referer());
        } else {
            $this->redirect($this->referer());
        }
    }

    public function customer_groups () {
        if( in_array('view_group_customers', $this->allowModule) ) {
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
        } else {
            $this->redirect($this->referer());
        }
    }

    function customer_group_add(){
        if( in_array('insert_group_customers', $this->allowModule) ) {
            $this->loadModel('CustomerGroup');
            $this->set('sub_module_title', 'Tambah Grup Customer');
            $this->doCustomerGroup();
        } else {
            $this->redirect($this->referer());
        }
    }

    function customer_group_edit($id){
        if( in_array('update_group_customers', $this->allowModule) ) {
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
        } else {
            $this->redirect($this->referer());
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
            
            $this->CustomerGroup->set($data);

            if($this->CustomerGroup->validates($data)){
                if($this->CustomerGroup->save($data)){
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Grup Customer'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s Grup Customer'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'customer_groups'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Grup Customer'), $msg), 'error');  
                    $this->Log->logActivity( sprintf(__('Gagal %s Grup Customer'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
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
        if( in_array('delete_group_customers', $this->allowModule) ) {
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
                    $this->Log->logActivity( sprintf(__('Grup customer ID #%s telah berhasil dihapus.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal menghapus Grup Customer.'), 'error');
                    $this->Log->logActivity( sprintf(__('Grup customer ID #%s telah Gagal dihapus.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                }
            }else{
                $this->MkCommon->setCustomFlash(__('Grup Customer tidak ditemukan.'), 'error');
            }

            $this->redirect($this->referer());
        } else {
            $this->redirect($this->referer());
        }
    }

    function jenis_sim(){
        if( in_array('view_jenis_sim', $this->allowModule) ) {
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
        } else {
            $this->redirect($this->referer());
        }
    }

    function sim_add(){
        if( in_array('insert_jenis_sim', $this->allowModule) ) {
            $this->set('sub_module_title', 'Tambah Jenis SIM');
            $this->doSim();
        } else {
            $this->redirect($this->referer());
        }
    }

    function sim_edit($id){
        if( in_array('update_jenis_sim', $this->allowModule) ) {
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
        } else {
            $this->redirect($this->referer());
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
                    $this->Log->logActivity( sprintf(__('Sukses %s Jenis SIM'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'jenis_sim'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Jenis SIM'), $msg), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal %s Jenis SIM'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );   
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
        if( in_array('delete_jenis_sim', $this->allowModule) ) {
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
                    $this->Log->logActivity( sprintf(__('Sukses merubah status jenis sim ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );   
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal merubah status jenis sim ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );   
                }
            }else{
                $this->MkCommon->setCustomFlash(__('Jenis SIM tidak ditemukan.'), 'error');
            }

            $this->redirect($this->referer());
        } else {
            $this->redirect($this->referer());
        }
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
                    $this->Log->logActivity( sprintf(__('Sukses %s Jenis Perlengkapan'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );   
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'jenis_perlengkapan'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Jenis Perlengkapan'), $msg), 'error');  
                    $this->Log->logActivity( sprintf(__('Gagal %s Jenis Perlengkapan'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );   
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
                $this->Log->logActivity( sprintf(__('Sukses merubah status jenis perlengkapan ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );   
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
        if( in_array('view_classifications', $this->allowModule) ) {
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
        } else {
            $this->redirect($this->referer());
        }
    }

    function classification_add(){
        if( in_array('insert_classifications', $this->allowModule) ) {
            $this->loadModel('GroupClassification');
            $this->set('sub_module_title', __('Tambah Klasifikasi'));
            $this->doclassification();
        } else {
            $this->redirect($this->referer());
        }
    }

    function classification_edit($id){
        if( in_array('update_classifications', $this->allowModule) ) {
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
        } else {
            $this->redirect($this->referer());
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
                    $this->Log->logActivity( sprintf(__('Sukses %s GroupClassification'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'classifications'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Klasifikasi'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s Klasifikasi'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
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
        if( in_array('delete_classifications', $this->allowModule) ) {
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
                    $this->Log->logActivity( sprintf(__('Sukses merubah status Klasifikasi ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal merubah status Klasifikasi ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                }
            }else{
                $this->MkCommon->setCustomFlash(__('Klasifikasi tidak ditemukan.'), 'error');
            }

            $this->redirect($this->referer());
        } else {
            $this->redirect($this->referer());
        }
    }

    function tarif_angkutan(){
        if( in_array('view_tarif_angkutan', $this->allowModule) ) {
            $this->loadModel('Customer');
            $this->loadModel('TarifAngkutan');
            $options = array();

            if(!empty($this->params['named'])){
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
            }

            $this->paginate = $this->TarifAngkutan->getData('paginate', $options);
            $tarif_angkutan = $this->paginate('TarifAngkutan');

            if(!empty($tarif_angkutan)){
                foreach ($tarif_angkutan as $key => $value) {
                    $tarif_angkutan[$key] = $this->Customer->getMerge($value, $value['TarifAngkutan']['customer_id']);
                }
            }

            $this->set('active_menu', 'tarif_angkutan');
            $this->set('sub_module_title', 'Tarif Angkutan');
            $this->set('tarif_angkutan', $tarif_angkutan);
        } else {
            $this->redirect($this->referer());
        }
    }

    function tarif_angkutan_add(){
        if( in_array('insert_tarif_angkutan', $this->allowModule) ) {
            $this->set('sub_module_title', 'Tambah Tarif Angkutan');
            $this->doTarifAngkutan();
        } else {
            $this->redirect($this->referer());
        }
    }

    function tarif_angkutan_edit($id){
        if( in_array('update_tarif_angkutan', $this->allowModule) ) {
            $this->loadModel('TarifAngkutan');
            $this->set('sub_module_title', 'Rubah Tarif Angkutan');
            $tarif_angkutan = $this->TarifAngkutan->getData('first', array(
                'conditions' => array(
                    'TarifAngkutan.id' => $id
                )
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
        } else {
            $this->redirect($this->referer());
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
            // if(!empty($data['TarifAngkutan']['from_city_name']) && !empty($data['TarifAngkutan']['to_city_name'])){
            //     $data['TarifAngkutan']['name_tarif'] = sprintf('%s - %s', $data['TarifAngkutan']['from_city_name'], $data['TarifAngkutan']['to_city_name']);
            // }

            $this->TarifAngkutan->set($data);
            $check_availability = $this->TarifAngkutan->check_availability( $data, $id );

            if($this->TarifAngkutan->validates($data) && $check_availability){
                if($this->TarifAngkutan->save($data)){
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Tarif Angkutan'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s Tarif Angkutan'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );   
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'tarif_angkutan'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Tarif Angkutan'), $msg), 'error');  
                    $this->Log->logActivity( sprintf(__('Gagal %s Tarif Angkutan'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );   
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
            'conditions' => array(
                'Customer.status' => 1
            ),
            'fields' => array(
                'Customer.id', 'Customer.customer_name'
            ),
        ));

        $this->loadModel('GroupMotor');
        $group_motors = $this->GroupMotor->getData('list', array(
            'conditions' => array(
                'GroupMotor.status' => 1
            ),
        ));
        
        $fromCities = $this->City->fromCities();
        $toCities = $this->City->toCities();

        $this->set(compact(
            'customers', 'group_motors', 'fromCities',
            'toCities'
        ));

        $this->set('active_menu', 'tarif_angkutan');
        $this->render('tarif_angkutan_form');
    }

    function tarif_angkutan_toggle($id){
        $this->loadModel('TarifAngkutan');
        $locale = $this->TarifAngkutan->getData('first', array(
            'conditions' => array(
                'TarifAngkutan.id' => $id
            )
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
                $this->Log->logActivity( sprintf(__('Sukses merubah status tarif angkutan ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );   
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
        if( in_array('view_customer_target_unit', $this->allowModule) ) {
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
        } else {
            $this->redirect($this->referer());
        }
    }

    function customer_target_unit_add(){
        if( in_array('insert_customer_target_unit', $this->allowModule) ) {
            $this->loadModel('CustomerTargetUnit');
            $this->set('sub_module_title', __('Target Unit'));
            $this->doCustomerTargetUnit();
        } else {
            $this->redirect($this->referer());
        }
    }

    function customer_target_unit_edit($id){
        if( in_array('update_customer_target_unit', $this->allowModule) ) {
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
        } else {
            $this->redirect($this->referer());
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
                    $this->Log->logActivity( sprintf(__('Sukses %s Target Unit'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'customer_target_unit'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Target Unit'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s Target Unit'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
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
            'conditions' => array(
                'Customer.status' => 1
            ),
            'fields' => array(
                'Customer.id', 'Customer.customer_name'
            )
        ));

        $this->set('active_menu', 'customer_target_unit');
        $this->set('customers', $customers);
        $this->render('customer_target_unit_form');
    }

    function customer_target_unit_toggle($id){
        if( in_array('delete_customer_target_unit', $this->allowModule) ) {
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
                    $this->Log->logActivity( sprintf(__('Sukses merubah status Target Unit ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal merubah status Target Unit ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                }
            }else{
                $this->MkCommon->setCustomFlash(__('Target Unit tidak ditemukan.'), 'error');
            }

            $this->redirect($this->referer());
        } else {
            $this->redirect($this->referer());
        }
    }

    function banks(){
        if( in_array('view_banks', $this->allowModule) ) {
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
        } else {
            $this->redirect($this->referer());
        }
    }

    function bank_add(){
        if( in_array('insert_banks', $this->allowModule) ) {
            $this->loadModel('Bank');
            $this->set('sub_module_title', 'Tambah Bank');
            $this->doBank();
        } else {
            $this->redirect($this->referer());
        }
    }

    function bank_edit($id){
        if( in_array('update_banks', $this->allowModule) ) {
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
        } else {
            $this->redirect($this->referer());
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
                    $this->Log->logActivity( sprintf(__('Sukses %s Bank'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'banks'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Bank'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s Bank'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
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
        if( in_array('delete_banks', $this->allowModule) ) {
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
                    $this->Log->logActivity( sprintf(__('Sukses merubah status Bank ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal merubah status Bank ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                }
            }else{
                $this->MkCommon->setCustomFlash(__('Bank tidak ditemukan.'), 'error');
            }

            $this->redirect($this->referer());
        } else {
            $this->redirect($this->referer());
        }
    }

    function calendar_colors(){
        if( in_array('view_calendar_colors', $this->allowModule) ) {
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
        } else {
            $this->redirect($this->referer());
        }
    }

    function calendar_color_add(){
        if( in_array('insert_calendar_colors', $this->allowModule) ) {
            $this->loadModel('CalendarColor');
            $this->set('sub_module_title', 'Tambah Warna Kalender');
            $this->doCalendarColor();
        } else {
            $this->redirect($this->referer());
        }
    }

    function calendar_color_edit($id){
        if( in_array('update_calendar_colors', $this->allowModule) ) {
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
        } else {
            $this->redirect($this->referer());
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
                    $this->Log->logActivity( sprintf(__('Sukses %s Warna Kalender'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );      
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'calendar_colors'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Warna Kalender'), $msg), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal %s Warna Kalender'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );        
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
        if( in_array('delete_calendar_colors', $this->allowModule) ) {
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
                    $this->Log->logActivity( sprintf(__('Sukses merubah status Warna Kalender ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );        
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal merubah status Warna Kalender ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );        
                }
            }else{
                $this->MkCommon->setCustomFlash(__('Warna Motor tidak ditemukan.'), 'error');
            }

            $this->redirect($this->referer());
        } else {
            $this->redirect($this->referer());
        }
    }

    function calendar_icons(){
        if( in_array('view_calendar_icons', $this->allowModule) ) {
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
        } else {
            $this->redirect($this->referer());
        }
    }

    function calendar_icon_add(){
        if( in_array('insert_calendar_icons', $this->allowModule) ) {
            $this->loadModel('CalendarIcon');
            $this->set('sub_module_title', 'Tambah Icon Kalender');
            $this->doCalendarIcon();
        } else {
            $this->redirect($this->referer());
        }
    }

    function calendar_icon_edit($id){
        if( in_array('update_calendar_icons', $this->allowModule) ) {
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
        } else {
            $this->redirect($this->referer());
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
                    $this->Log->logActivity( sprintf(__('Sukses %s Icon Kalender'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );      
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'calendar_icons'
                    ));
                } else if( empty($errorUpload) ) {
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Icon Kalender'), $msg), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal %s Icon Kalender'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );        
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
        $this->render('calendar_icon_form');
    }

    function calendar_icon_toggle($id){
        if( in_array('delete_calendar_icons', $this->allowModule) ) {
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
                    $this->Log->logActivity( sprintf(__('Sukses merubah status Icon Kalender ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );        
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal merubah status Icon Kalender ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );        
                }
            }else{
                $this->MkCommon->setCustomFlash(__('Icon Motor tidak ditemukan.'), 'error');
            }

            $this->redirect($this->referer());
        } else {
            $this->redirect($this->referer());
        }
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
            $this->PartsMotor->set($data);

            if($this->PartsMotor->validates($data)){
                if($this->PartsMotor->save($data)){
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Part Motor'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s Part Motor'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );   
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'parts_motor'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Part Motor'), $msg), 'error');  
                    $this->Log->logActivity( sprintf(__('Gagal %s Part Motor'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );   
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
                $this->Log->logActivity( sprintf(__('Sukses merubah status jenis perlengkapan ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );   
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal merubah status jenis perlengkapan ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );   
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Part Motor tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }
}
