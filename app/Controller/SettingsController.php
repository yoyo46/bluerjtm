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
            if(!empty($refine['company_type_id'])){
                $company_type_id = urldecode($refine['company_type_id']);
                $this->request->data['Company']['company_type_id'] = $company_type_id;
                $options['conditions']['Company.company_type_id '] = $company_type_id;
            }
        }
        $this->paginate = $this->Company->getData('paginate', $options);
        $truck_companies = $this->paginate('Company');
        $companyTypes  = $this->Company->CompanyType->getData('list', false, true);

        $this->set('active_menu', 'companies');
        $this->set('module_title', 'Data Master');
        $this->set('sub_module_title', 'Customer');
        $this->set(compact(
            'companyTypes', 'truck_companies'
        ));
    }

    function company_add(){
        $this->loadModel('Company');
        $this->set('sub_module_title', 'Tambah Customer');
        $this->doCompany();
    }

    function company_edit($id){
        $this->loadModel('Company');
        $this->set('sub_module_title', 'Rubah Customer');
        $company = $this->Company->find('first', array(
            'conditions' => array(
                'Company.id' => $id
            )
        ));

        if(!empty($company)){
            $this->doCompany($id, $company);
        }else{
            $this->MkCommon->setCustomFlash(__('Customer tidak ditemukan'), 'error');  
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
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Customer'), $msg), 'success');
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'companies'
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

        $companyTypes  = $this->Company->CompanyType->getData('list', false, true);
        $this->set('active_menu', 'companies');
        $this->set('module_title', 'Data Master');
        $this->set(compact(
            'companyTypes'
        ));
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
            $this->MkCommon->setCustomFlash(__('Customer tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    public function company_types() {
        $this->loadModel('CompanyType');
        $options = array();

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['name'])){
                $name = urldecode($refine['name']);
                $this->request->data['CompanyType']['name'] = $name;
                $options['conditions']['CompanyType.name LIKE '] = '%'.$name.'%';
            }
        }
        $this->paginate = $this->CompanyType->getData('paginate', $options);
        $companyTypes = $this->paginate('CompanyType');

        $this->set('active_menu', 'company_types');
        $this->set('module_title', 'Data Master');
        $this->set('sub_module_title', 'Tipe Customer');
        $this->set('companyTypes', $companyTypes);        
    }

    function company_type_add(){
        $this->set('sub_module_title', 'Tambah Tipe Customer');
        $this->doCompanyType();
    }

    function company_type_edit($id){
        $this->loadModel('CompanyType');
        $this->set('sub_module_title', 'Rubah Tipe Customer');
        $companyType = $this->CompanyType->find('first', array(
            'conditions' => array(
                'CompanyType.id' => $id
            )
        ));

        if(!empty($companyType)){
            $this->doCompanyType($id, $companyType);
        }else{
            $this->MkCommon->setCustomFlash(__('Tipe Customer tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'settings',
                'action' => 'company_types'
            ));
        }
    }

    function doCompanyType($id = false, $data_local = false){
        if(!empty($this->request->data)){
            $data = $this->request->data;
            if($id && $data_local){
                $this->CompanyType->id = $id;
                $msg = 'merubah';
            }else{
                $this->loadModel('CompanyType');
                $this->CompanyType->create();
                $msg = 'menambah';
            }
            
            $this->CompanyType->set($data);

            if($this->CompanyType->validates($data)){
                if($this->CompanyType->save($data)){
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Tipe Customer'), $msg), 'success');
                    $this->redirect(array(
                        'controller' => 'settings',
                        'action' => 'company_types'
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

        $this->set('active_menu', 'company_types');
        $this->set('module_title', 'Data Master');
        $this->render('company_type_form');
    }

    function company_type_toggle($id){
        $this->loadModel('CompanyType');
        $locale = $this->CompanyType->getData('first', array(
            'conditions' => array(
                'CompanyType.id' => $id
            )
        ));

        if($locale){
            $value = true;
            if($locale['CompanyType']['status']){
                $value = false;
            }

            $this->CompanyType->id = $id;
            $this->CompanyType->set('status', $value);
            if($this->CompanyType->save()){
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
}
