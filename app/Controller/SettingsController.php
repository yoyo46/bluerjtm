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
        }
        $this->paginate = $this->Company->getData('paginate', $options);
        $truck_companies = $this->paginate('Company');

        $this->set('sub_module_title', 'Customer');
        $this->set('truck_companies', $truck_companies);
    }

    function company_add(){
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
                $this->loadModel('Company');
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
}
