<?php
App::uses('AppController', 'Controller');
class SettingsController extends AppController {
	public $uses = array();

    public $components = array(
        'RjSetting'
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
        $type_property = $this->City->find('first', array(
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
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'cities'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Kota'), $msg), 'error');  
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Kota'), $msg), 'error');
            }
        }else{
            
            if($id && $data_local){
                
                $this->request->data = $data_local;
            }
        }

        $this->set('active_menu', 'cities');
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
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Kota tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    function customers(){
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
        }
        $this->paginate = $this->Customer->getData('paginate', $options);
        $truck_customers = $this->paginate('Customer');
        $customerTypes  = $this->Customer->CustomerType->getData('list', false, true);

        $this->set('active_menu', 'customers');
        $this->set('module_title', 'Data Master');
        $this->set('sub_module_title', 'Customer');
        $this->set(compact(
            'customerTypes', 'truck_customers'
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
        $customer = $this->Customer->find('first', array(
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
            
            $this->Customer->set($data);

            if($this->Customer->validates($data)){
                if($this->Customer->save($data)){
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Customer'), $msg), 'success');
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'customers'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Customer'), $msg), 'error');  
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
        $this->set('active_menu', 'customers');
        $this->set('module_title', 'Data Master');
        $this->set(compact(
            'customerTypes'
        ));
        $this->render('customer_form');
    }

    function customer_toggle($id){
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
            $this->Customer->set('status', $value);
            if($this->Customer->save()){
                $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
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
        $customerType = $this->CustomerType->find('first', array(
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
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'customer_types'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Tipe Customer'), $msg), 'error');  
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
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
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
        $vendor = $this->Vendor->find('first', array(
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
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'vendors'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Vendor'), $msg), 'error');  
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
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
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
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'coas'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Coa'), $msg), 'error');  
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
        $company = $this->Company->find('first', array(
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
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'companies'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Company'), $msg), 'error');  
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
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Company tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    public function uang_jalan() {
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
    }

    public function uang_jalan_add() {
        $this->loadModel('UangJalan');
        $this->set('sub_module_title', 'Tambah Uang Jalan');
        $this->doUangJalan();
    }

    function doUangJalan($id = false, $data_local = false){
        if(!empty($this->request->data)){
            $data = $this->request->data;
            if($id && $data_local){
                $this->UangJalan->id = $id;
                $msg = 'merubah';
            }else{
                $this->UangJalan->create();
                $msg = 'menambah';
            }

            $data['UangJalan']['commission'] = !empty($data['UangJalan']['commission'])?str_replace(',', '', $data['UangJalan']['commission']):false;
            $data['UangJalan']['uang_jalan_1'] = !empty($data['UangJalan']['uang_jalan_1'])?str_replace(',', '', $data['UangJalan']['uang_jalan_1']):false;
            $data['UangJalan']['uang_jalan_2'] = !empty($data['UangJalan']['uang_jalan_2'])?str_replace(',', '', $data['UangJalan']['uang_jalan_2']):false;
            $data['UangJalan']['uang_kuli_muat'] = !empty($data['UangJalan']['uang_kuli_muat'])?str_replace(',', '', $data['UangJalan']['uang_kuli_muat']):false;
            $data['UangJalan']['uang_kuli_bongkar'] = !empty($data['UangJalan']['uang_kuli_bongkar'])?str_replace(',', '', $data['UangJalan']['uang_kuli_bongkar']):false;
            $data['UangJalan']['asdp'] = !empty($data['UangJalan']['asdp'])?str_replace(',', '', $data['UangJalan']['asdp']):false;
            $data['UangJalan']['uang_kawal'] = !empty($data['UangJalan']['uang_kawal'])?str_replace(',', '', $data['UangJalan']['uang_kawal']):false;
            $data['UangJalan']['uang_keamanan'] = !empty($data['UangJalan']['uang_keamanan'])?str_replace(',', '', $data['UangJalan']['uang_keamanan']):false;
            $data['UangJalan']['uang_jalan_extra'] = !empty($data['UangJalan']['uang_jalan_extra'])?str_replace(',', '', $data['UangJalan']['uang_jalan_extra']):false;

            if( !empty($data['UangJalan']['is_unit']) ) {
                $data['UangJalan']['uang_jalan_2'] = 0;
            }
            
            $this->UangJalan->set($data);

            if($this->UangJalan->validates($data)){
                if($this->UangJalan->save($data)){
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Uang jalan'), $msg), 'success');
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'uang_jalan'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Uang jalan'), $msg), 'error');  
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Uang jalan'), $msg), 'error');
            }
        }else{
            
            if($id && $data_local){
                
                $this->request->data = $data_local;
            }
        }
        $customers = $this->UangJalan->Customer->getData('list', array(
            'conditions' => array(
                'Customer.status' => 1
            ),
        ));
        $cities = $this->UangJalan->FromCity->getData('list', array(
            'conditions' => array(
                'status' => 1
            ),
        ));
        $groupClassifications = $this->UangJalan->GroupClassification->find('list', array(
            'conditions' => array(
                'status' => 1
            ),
        ));

        $this->set('active_menu', 'uang_jalan');
        $this->set('module_title', 'Data Master');
        $this->set(compact(
            'customers', 'cities', 'groupClassifications'
        ));
        $this->render('uang_jalan_form');
    }

    function uang_jalan_edit($id){
        $this->loadModel('UangJalan');
        $this->set('sub_module_title', 'Rubah Uang Jalan');
        $uangJalan = $this->UangJalan->find('first', array(
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
        $perlengkapan = $this->Perlengkapan->find('first', array(
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
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'perlengkapan'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Perlengkapan'), $msg), 'error');  
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Perlengkapan'), $msg), 'error');
            }
        }else{
            if($id && $data_local){
                $this->request->data = $data_local;
            }
        }

        $this->set('active_menu', 'perlengkapan');
        $this->set('module_title', 'Data Master');
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
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Perlengkapan tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
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
        $TipeMotor = $this->TipeMotor->find('first', array(
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
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'type_motors'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Tipe Motor'), $msg), 'error');  
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
        $colors = $this->ColorMotor->getData('list', array(
            'conditions' => array(
                'ColorMotor.status' => 1
            ),
            'fields' => array(
                'ColorMotor.id', 'ColorMotor.name'
            )
        ));
        $this->set('colors', $colors);
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
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
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
        $ColorMotor = $this->ColorMotor->find('first', array(
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
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'colors'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Warna Motor'), $msg), 'error');  
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
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Warna Motor tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }
}
