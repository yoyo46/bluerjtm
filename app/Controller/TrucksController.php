<?php
App::uses('AppController', 'Controller');
class TrucksController extends AppController {
	public $uses = array();

	public function index() {
		$this->set('active_menu', 'trucks');
		$this->set('title_for_layout', __('ERP RJTM | Data Truk'));
		$this->set('module_title', __('Truk'));
		$this->set('sub_module_title', __('Data Truk'));
	}

	function brands(){
		$this->loadModel('TruckBrand');
		$this->paginate = $this->TruckBrand->getData('paginate');
		$truck_brands = $this->paginate('TruckBrand');

		$this->set('module_title', 'Truck Brands');
		$this->set('truck_brands', $truck_brands);
	}

	function brand_add(){
        $this->set('module_title', 'add truck brand');
        $this->doBrand();
    }

    function brand_edit($id){
    	$this->loadModel('TruckBrand');
        $this->set('module_title', 'edit truck brand');
        $type_property = $this->TruckBrand->find('first', array(
            'conditions' => array(
                'TruckBrand.id' => $id
            )
        ));

        if(!empty($type_property)){
            $this->doBrand($id, $type_property);
        }else{
            $this->MkCommon->setCustomFlash(__('truck brand not found'), 'error');  
            $this->redirect(array(
                'controller' => 'trucks',
                'action' => 'brands'
            ));
        }
    }

    function doBrand($id = false, $data_local = false){
        if(!empty($this->request->data)){
            $data = $this->request->data;
            if($id && $data_local){
                $this->TruckBrand->id = $id;
                $msg = 'editing';
            }else{
            	$this->loadModel('TruckBrand');
                $this->TruckBrand->create();
                $msg = 'adding';
            }
            $this->TruckBrand->set($data);

            if($this->TruckBrand->validates($data)){
                if($this->TruckBrand->save($data)){
                    $this->MkCommon->setCustomFlash(sprintf(__('success %s truck brand'), $msg), 'success');
                    $this->redirect(array(
                        'controller' => 'trucks',
                        'action' => 'brands'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('fail %s truck brand'), $msg), 'error');  
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('fail %s truck brand'), $msg), 'error');
            }
        }else{
            $this->set('disabled', false);
            if($id && $data_local){
                $this->set('disabled', true);
                $this->request->data = $data_local;
            }
        }

        $this->render('brand_form');
    }

    function brand_toggle($id){
    	$this->loadModel('TruckBrand');
        $locale = $this->TruckBrand->getData('first', array(
            'conditions' => array(
                'TruckBrand.id' => $id
            )
        ));

        if($locale){
            $value = true;
            if($locale['TruckBrand']['status']){
                $value = false;
            }

            $this->TruckBrand->id = $id;
            $this->TruckBrand->set('status', $value);
            if($this->TruckBrand->save()){
                $this->MkCommon->setCustomFlash(__('success change status.'), 'success');
            }else{
                $this->MkCommon->setCustomFlash(__('fail change status.'), 'error');
            }
        }else{
            $this->MkCommon->setCustomFlash(__('truck brand not found.'), 'error');
        }

        $this->redirect($this->referer());
    }

    function categories(){
		$this->loadModel('TruckCategory');
		$this->paginate = $this->TruckCategory->getData('paginate');
		$truck_categories = $this->paginate('TruckCategory');

		$this->set('module_title', 'Truck Categories');
		$this->set('truck_categories', $truck_categories);
	}

	function category_add(){
        $this->set('module_title', 'add truck category');
        $this->doCategory();
    }

    function category_edit($id){
    	$this->loadModel('TruckCategory');
        $this->set('module_title', 'edit truck category');
        $type_property = $this->TruckCategory->find('first', array(
            'conditions' => array(
                'TruckCategory.id' => $id
            )
        ));

        if(!empty($type_property)){
            $this->doCategory($id, $type_property);
        }else{
            $this->MkCommon->setCustomFlash(__('truck category not found'), 'error');  
            $this->redirect(array(
                'controller' => 'trucks',
                'action' => 'categories'
            ));
        }
    }

    function doCategory($id = false, $data_local = false){
        if(!empty($this->request->data)){
            $data = $this->request->data;
            if($id && $data_local){
                $this->TruckCategory->id = $id;
                $msg = 'editing';
            }else{
            	$this->loadModel('TruckCategory');
                $this->TruckCategory->create();
                $msg = 'adding';
            }
            $this->TruckCategory->set($data);

            if($this->TruckCategory->validates($data)){
                if($this->TruckCategory->save($data)){
                    $this->MkCommon->setCustomFlash(sprintf(__('success %s truck category'), $msg), 'success');
                    $this->redirect(array(
                        'controller' => 'trucks',
                        'action' => 'categories'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('fail %s truck category'), $msg), 'error');  
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('fail %s truck category'), $msg), 'error');
            }
        }else{
            $this->set('disabled', false);
            if($id && $data_local){
                $this->set('disabled', true);
                $this->request->data = $data_local;
            }
        }

        $this->render('category_form');
    }

    function category_toggle($id){
    	$this->loadModel('TruckCategory');
        $locale = $this->TruckCategory->getData('first', array(
            'conditions' => array(
                'TruckCategory.id' => $id
            )
        ));

        if($locale){
            $value = true;
            if($locale['TruckCategory']['status']){
                $value = false;
            }

            $this->TruckCategory->id = $id;
            $this->TruckCategory->set('status', $value);
            if($this->TruckCategory->save()){
                $this->MkCommon->setCustomFlash(__('success change status.'), 'success');
            }else{
                $this->MkCommon->setCustomFlash(__('fail change status.'), 'error');
            }
        }else{
            $this->MkCommon->setCustomFlash(__('truck category not found.'), 'error');
        }

        $this->redirect($this->referer());
    }
}
