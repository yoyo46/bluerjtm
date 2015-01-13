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
        $this->set('regions', $regions);
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
        $customerGroups  = $this->Customer->CustomerGroup->getData('list');

        $this->set('active_menu', 'customers');
        $this->set('module_title', 'Data Master');
        $this->set(compact(
            'customerTypes', 'customerGroups'
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
            $this->Customer->set('status', 0);

            if($this->Customer->save()){
                $this->MkCommon->setCustomFlash(__('Customer telah berhasil dihapus.'), 'success');
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal menghapus Customer.'), 'error');
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
        // $customers = $this->UangJalan->Customer->getData('list', array(
        //     'conditions' => array(
        //         'Customer.status' => 1
        //     ),
        // ));
        $cities = $this->UangJalan->FromCity->getData('list', array(
            'conditions' => array(
                'FromCity.status' => 1
            ),
        ), false);
        $groupClassifications = $this->UangJalan->GroupClassification->find('list', array(
            'conditions' => array(
                'status' => 1
            ),
        ));

        $this->set('active_menu', 'uang_jalan');
        $this->set('module_title', 'Data Master');
        $this->set(compact(
            // 'customers', 
            'cities', 'groupClassifications'
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
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'regions'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Provinsi'), $msg), 'error');  
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
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
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
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'group_motors'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Grup Motor'), $msg), 'error');  
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
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
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
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'code_motors'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Kode Motor'), $msg), 'error');  
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
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Kode Motor tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    function branches(){
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
    }

    function branch_add(){
        $this->loadModel('Branch');
        $this->set('sub_module_title', 'Tambah Cabang');
        $this->doBranch();
    }

    function branch_edit($id){
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
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'branches'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Branch'), $msg), 'error');  
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
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal menghapus data cabang.'), 'error');
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Cabang tidak ditemukan.'), 'error');
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
            
            $this->CustomerGroup->set($data);

            if($this->CustomerGroup->validates($data)){
                if($this->CustomerGroup->save($data)){
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Grup Customer'), $msg), 'success');
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'customer_groups'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Grup Customer'), $msg), 'error');  
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
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal menghapus Grup Customer.'), 'error');
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
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'jenis_sim'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Jenis SIM'), $msg), 'error');  
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
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
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

    function jenis_per_lengkapan_edit($id){
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
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'jenis_perlengkapan'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Jenis Perlengkapan'), $msg), 'error');  
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
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Jenis Perlengkapan tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }
}
