<?php
App::uses('AppController', 'Controller');
class TrucksController extends AppController {
	public $uses = array();

    function beforeFilter() {
        parent::beforeFilter();
        $this->set('title_for_layout', __('ERP RJTM | Data Truk'));
        $this->set('module_title', __('Truk'));
    }

	public function index() {
        $this->loadModel('Truck');
		$this->set('active_menu', 'trucks');
		$this->set('sub_module_title', __('Data Truk'));

        $this->paginate = $this->Truck->getData('paginate');
        $trucks = $this->paginate('Truck');

        if(!empty($trucks)){
            foreach ($trucks as $key => $truck) {
                $data = $truck['Truck'];

                $truck = $this->Truck->TruckCategory->getMerge($truck, $data['truck_category_id']);
                $truck = $this->Truck->TruckBrand->getMerge($truck, $data['truck_brand_id']);
                $truck = $this->Truck->Company->getMerge($truck, $data['company_id']);
                $truck = $this->Truck->Driver->getMerge($truck, $data['driver_id']);

                $trucks[$key] = $truck;
            }
        }

        $this->set('trucks', $trucks);
	}

    function detail($id = false){
        if(!empty($id)){
            $truck = $this->Truck->getTruck($id);

            if(!empty($truck)){
                $sub_module_title = __('Detail Truk');
                $this->set(compact('truck', 'sub_module_title'));
            }else{
                $this->MkCommon->setCustomFlash(__('Truk tidak ditemukan.'), 'error');
                $this->redirect($this->referer());
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Truk tidak ditemukan.'), 'error');
            $this->redirect($this->referer());
        }
    }

    function add(){
        $this->set('sub_module_title', __('Tambah Truk'));
        $this->doTruck();
    }

    function edit($id){
        $this->loadModel('Truck');
        $this->set('sub_module_title', 'Rubah truk');
        $truck = $this->Truck->find('first', array(
            'conditions' => array(
                'Truck.id' => $id
            )
        ));

        if(!empty($truck)){
            $this->doTruck($id, $truck);
        }else{
            $this->MkCommon->setCustomFlash(__('truk tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'trucks',
                'action' => 'index'
            ));
        }
    }

    function doTruck($id = false, $data_local = false){
        if(!empty($this->request->data)){
            $data = $this->request->data;
            if($id && $data_local){
                $this->Truck->id = $id;
                $msg = 'merubah';
            }else{
                $this->loadModel('Truck');
                $this->Truck->create();
                $msg = 'menambah';
            }
            
            $data['Truck']['tgl_bpkb'] = (!empty($data['Truck']['tgl_bpkb'])) ? date('Y-m-d', strtotime($data['Truck']['tgl_bpkb'])) : '';

            $this->Truck->set($data);

            if($this->Truck->validates($data)){
                if($this->Truck->save($data)){
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s truk'), $msg), 'success');
                    $this->redirect(array(
                        'controller' => 'trucks',
                        'action' => 'index'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s truk'), $msg), 'error');  
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s truk'), $msg), 'error');
            }
        }else{
            
            if($id && $data_local){
                
                $this->request->data = $data_local;
            }
        }

        $truck_brands = $this->Truck->TruckBrand->getData('list', array(
            'conditions' => array(
                'TruckBrand.status' => 1
            ),
            'fields' => array(
                'TruckBrand.id', 'TruckBrand.name'
            )
        ));
        $truck_categories = $this->Truck->TruckCategory->getData('list', array(
            'conditions' => array(
                'TruckCategory.status' => 1
            ),
            'fields' => array(
                'TruckCategory.id', 'TruckCategory.name'
            )
        ));
        $truck_companies = $this->Truck->Company->getData('list', array(
            'conditions' => array(
                'Company.status' => 1
            ),
            'fields' => array(
                'Company.id', 'Company.name'
            )
        ));
        $drivers = $this->Truck->Driver->getData('list', array(
            'conditions' => array(
                'Driver.status' => 1
            ),
            'fields' => array(
                'Driver.id', 'Driver.name'
            )
        ));

        $this->set(compact('truck_brands', 'truck_categories', 'truck_brands', 'truck_companies', 'drivers'));
        $this->render('truck_form');
    }

    function toggle($id){
        $this->loadModel('Truck');
        $locale = $this->Truck->getData('first', array(
            'conditions' => array(
                'Truck.id' => $id
            )
        ));

        if($locale){
            $value = true;
            if($locale['Truck']['status']){
                $value = false;
            }

            $this->Truck->id = $id;
            $this->Truck->set('status', $value);
            if($this->Truck->save()){
                $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
            }
        }else{
            $this->MkCommon->setCustomFlash(__('truk tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

	function brands(){
		$this->loadModel('TruckBrand');
		$this->paginate = $this->TruckBrand->getData('paginate');
		$truck_brands = $this->paginate('TruckBrand');

		$this->set('sub_module_title', 'Merek Truk');
		$this->set('truck_brands', $truck_brands);
	}

	function brand_add(){
        $this->set('sub_module_title', 'Tambah Merek Truk');
        $this->doBrand();
    }

    function brand_edit($id){
    	$this->loadModel('TruckBrand');
        $this->set('sub_module_title', 'Rubah Merek Truk');
        $TruckBrand = $this->TruckBrand->find('first', array(
            'conditions' => array(
                'TruckBrand.id' => $id
            )
        ));

        if(!empty($TruckBrand)){
            $this->doBrand($id, $TruckBrand);
        }else{
            $this->MkCommon->setCustomFlash(__('Merek Truk tidak ditemukan'), 'error');  
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
                $msg = 'merubah';
            }else{
            	$this->loadModel('TruckBrand');
                $this->TruckBrand->create();
                $msg = 'menambah';
            }
            $this->TruckBrand->set($data);

            if($this->TruckBrand->validates($data)){
                if($this->TruckBrand->save($data)){
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Merek Truk'), $msg), 'success');
                    $this->redirect(array(
                        'controller' => 'trucks',
                        'action' => 'brands'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Merek Truk'), $msg), 'error');  
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Merek Truk'), $msg), 'error');
            }
        }else{
            
            if($id && $data_local){
                
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
                $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Merek Truk tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    function categories(){
		$this->loadModel('TruckCategory');
		$this->paginate = $this->TruckCategory->getData('paginate');
		$truck_categories = $this->paginate('TruckCategory');

		$this->set('sub_module_title', 'Kategori Truk');
		$this->set('truck_categories', $truck_categories);
	}

	function category_add(){
        $this->set('sub_module_title', 'Tambah Kategori Truk');
        $this->doCategory();
    }

    function category_edit($id){
    	$this->loadModel('TruckCategory');
        $this->set('sub_module_title', 'Rubah Kategori Truk');
        $type_property = $this->TruckCategory->find('first', array(
            'conditions' => array(
                'TruckCategory.id' => $id
            )
        ));

        if(!empty($type_property)){
            $this->doCategory($id, $type_property);
        }else{
            $this->MkCommon->setCustomFlash(__('Kategori Truk tidak ditemukan'), 'error');  
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
                $msg = 'merubah';
            }else{
            	$this->loadModel('TruckCategory');
                $this->TruckCategory->create();
                $msg = 'menambah';
            }
            $this->TruckCategory->set($data);

            if($this->TruckCategory->validates($data)){
                if($this->TruckCategory->save($data)){
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Kategori Truk'), $msg), 'success');
                    $this->redirect(array(
                        'controller' => 'trucks',
                        'action' => 'categories'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Kategori Truk'), $msg), 'error');  
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Kategori Truk'), $msg), 'error');
            }
        }else{
            
            if($id && $data_local){
                
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
                $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Kategori Truk tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    function cities(){
        $this->loadModel('City');
        $this->paginate = $this->City->getData('paginate');
        $cities = $this->paginate('City');

        $this->set('sub_module_title', 'City');
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
                'controller' => 'trucks',
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
                        'controller' => 'trucks',
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

    function drivers(){
        $this->loadModel('Driver');
        $this->paginate = $this->Driver->getData('paginate');
        $truck_drivers = $this->paginate('Driver');

        $this->set('sub_module_title', 'Supir Truk');
        $this->set('truck_drivers', $truck_drivers);
    }

    function driver_add(){
        $this->set('sub_module_title', 'Tambah Supir Truk');
        $this->doDriver();
    }

    function driver_edit($id){
        $this->loadModel('Driver');
        $this->set('sub_module_title', 'Rubah Supir Truk');
        $driver = $this->Driver->find('first', array(
            'conditions' => array(
                'Driver.id' => $id
            )
        ));

        if(!empty($driver)){
            $this->doDriver($id, $driver);
        }else{
            $this->MkCommon->setCustomFlash(__('Supir Truk tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'trucks',
                'action' => 'drivers'
            ));
        }
    }

    function doDriver($id = false, $data_local = false){
        if(!empty($this->request->data)){
            $data = $this->request->data;
            if($id && $data_local){
                $this->Driver->id = $id;
                $msg = 'merubah';
            }else{
                $this->loadModel('Driver');
                $this->Driver->create();
                $msg = 'menambah';
            }

            $data['Driver']['uang_makan'] = (!empty($data['Driver']['uang_makan'])) ? Sanitize::paranoid($data['Driver']['uang_makan']) : '';
            $data['Driver']['phone'] = (!empty($data['Driver']['phone'])) ? Sanitize::paranoid($data['Driver']['phone']) : '';
            $data['Driver']['phone_2'] = (!empty($data['Driver']['phone_2'])) ? Sanitize::paranoid($data['Driver']['phone_2']) : '';
            
            $this->Driver->set($data);

            if($this->Driver->validates($data)){
                if($this->Driver->save($data)){
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Supir Truk'), $msg), 'success');
                    $this->redirect(array(
                        'controller' => 'trucks',
                        'action' => 'drivers'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Supir Truk'), $msg), 'error');  
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Supir Truk'), $msg), 'error');
            }
        }else{
            
            if($id && $data_local){
                
                $this->request->data = $data_local;
            }
        }

        $this->render('driver_form');
    }

    function driver_toggle($id){
        $this->loadModel('Driver');
        $locale = $this->Driver->getData('first', array(
            'conditions' => array(
                'Driver.id' => $id
            )
        ));

        if($locale){
            $value = true;
            if($locale['Driver']['status']){
                $value = false;
            }

            $this->Driver->id = $id;
            $this->Driver->set('status', $value);
            if($this->Driver->save()){
                $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Supir Truk tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    function companies(){
        $this->loadModel('Company');
        $this->paginate = $this->Company->getData('paginate');
        $truck_companies = $this->paginate('Company');

        $this->set('sub_module_title', 'Perusahaan Truk');
        $this->set('truck_companies', $truck_companies);
    }

    function company_add(){
        $this->set('sub_module_title', 'Tambah Perusahaan Truk');
        $this->doCompany();
    }

    function company_edit($id){
        $this->loadModel('Company');
        $this->set('sub_module_title', 'Rubah Perusahaan Truk');
        $company = $this->Company->find('first', array(
            'conditions' => array(
                'Company.id' => $id
            )
        ));

        if(!empty($company)){
            $this->doCompany($id, $company);
        }else{
            $this->MkCommon->setCustomFlash(__('Perusahaan Truk tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'trucks',
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
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Perusahaan Truk'), $msg), 'success');
                    $this->redirect(array(
                        'controller' => 'trucks',
                        'action' => 'companies'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Perusahaan Truk'), $msg), 'error');  
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Perusahaan Truk'), $msg), 'error');
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
            $this->MkCommon->setCustomFlash(__('Perusahaan Truk tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    function kir($id = false){
        if(!empty($id)){
            $truck = $this->Truck->getTruck($id);

            if(!empty($truck)){
                $this->paginate = $this->Truck->Kir->getData('paginate', array(
                    'conditions' => array(
                        'truck_id' => $id
                    ),
                    'order' => array(
                        'Kir.created'
                    )
                ));
                $kir = $this->paginate('Kir');

                $sub_module_title = __('Histori KIR Truk');
                $this->set(compact('truck', 'kir', 'sub_module_title', 'id'));
            }else{
                $this->MkCommon->setCustomFlash(__('Truk tidak ditemukan.'), 'error');
                $this->redirect($this->referer());
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Truk tidak ditemukan.'), 'error');
            $this->redirect($this->referer());
        }
    }

    function kir_add($truck_id){
        $this->set('sub_module_title', 'Tambah KIR Truk');

        $truck = $this->Truck->find('first', array(
            'conditions' => array(
                'truck.id' => $truck_id
            )
        ));

        if(!empty($truck)){
            $this->doKir($truck_id);
        }else{
            $this->MkCommon->setCustomFlash(__('Truk tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'trucks',
                'action' => 'kir'
            ));
        }
    }

    function kir_edit($truck_id, $id){
        $this->loadModel('Kir');
        $this->set('sub_module_title', 'Rubah KIR Truk');
        $Kir = $this->Kir->find('first', array(
            'conditions' => array(
                'Kir.id' => $id,
                'Kir.truck_id' => $truck_id
            )
        ));

        if(!empty($Kir)){
            $this->doKir($truck_id, $id, $Kir);
        }else{
            $this->MkCommon->setCustomFlash(__('KIR Truk tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'trucks',
                'action' => 'kir',
                $truck_id
            ));
        }
    }

    function doKir($truck_id, $id = false, $data_local = false){
        if(!empty($this->request->data)){
            $data = $this->request->data;
            if($id && $data_local){
                $this->Kir->id = $id;
                $msg = 'merubah';
            }else{
                $this->loadModel('Kir');
                $this->Kir->create();
                $msg = 'menambah';
            }

            $truck = $this->Truck->find('first', array(
                'conditions' => array(
                    'truck.id' => $truck_id
                )
            ));
            
            $data['Kir']['truck_id'] = $truck_id;
            $data['Kir']['tgl_kir'] = (!empty($data['Kir']['tgl_kir'])) ? date('Y-m-d', strtotime($data['Kir']['tgl_kir'])) : '';

            $date_old = strtotime($truck['Truck']['kir']);
            $date_new = strtotime($data['Kir']['tgl_kir']);
            
            $check = false;
            if($date_new >= $date_old){
                $check = true;
            }
            $this->Kir->set($data);

            if($this->Kir->validates($data) && $check){
                if($this->Kir->save($data)){

                    $this->Truck->id = $truck_id;
                    $this->Truck->set('kir', $data['Kir']['tgl_kir']);
                    $this->Truck->save();

                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s KIR Truk'), $msg), 'success');
                    $this->redirect(array(
                        'controller' => 'trucks',
                        'action' => 'kir',
                        $truck_id
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s KIR Truk'), $msg), 'error');  
                }
            }else{
                $text = sprintf(__('Gagal %s KIR Truk'), $msg);
                if( !$check ){
                    $text .= ', tanggal KIR harus lebih besar dari sebelumnya';
                }
                $this->MkCommon->setCustomFlash($text, 'error');
            }
        }else{
            
            if($id && $data_local){
                
                $this->request->data = $data_local;
            }
        }

        $sub_module_title = __('Histori KIR Truk');
        $this->set(compact('truck_id', 'sub_module_title'));
        $this->render('kir_form');
    }

    function siup($id = false){
        if(!empty($id)){
            $truck = $this->Truck->getTruck($id);

            if(!empty($truck)){
                $this->paginate = $this->Truck->Siup->getData('paginate', array(
                    'conditions' => array(
                        'truck_id' => $id
                    ),
                    'order' => array(
                        'Siup.created'
                    )
                ));
                $siup = $this->paginate('Siup');

                $sub_module_title = __('Histori SIUP Truk');
                $this->set(compact('truck', 'siup', 'sub_module_title', 'id'));
            }else{
                $this->MkCommon->setCustomFlash(__('Truk tidak ditemukan.'), 'error');
                $this->redirect($this->referer());
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Truk tidak ditemukan.'), 'error');
            $this->redirect($this->referer());
        }
    }

    function siup_add($truck_id){
        $this->set('sub_module_title', 'Tambah SIUP Truk');

        $truck = $this->Truck->find('first', array(
            'conditions' => array(
                'truck.id' => $truck_id
            )
        ));

        if(!empty($truck)){
            $this->doSiup($truck_id);
        }else{
            $this->MkCommon->setCustomFlash(__('Truk tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'trucks',
                'action' => 'siup'
            ));
        }
    }

    function siup_edit($truck_id, $id){
        $this->loadModel('Siup');
        $this->set('sub_module_title', 'Rubah SIUP Truk');
        $Siup = $this->Siup->find('first', array(
            'conditions' => array(
                'Siup.id' => $id,
                'Siup.truck_id' => $truck_id
            )
        ));

        if(!empty($Siup)){
            $this->doSiup($truck_id, $id, $Siup);
        }else{
            $this->MkCommon->setCustomFlash(__('SIUP Truk tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'trucks',
                'action' => 'siup',
                $truck_id
            ));
        }
    }

    function doSiup($truck_id, $id = false, $data_local = false){
        if(!empty($this->request->data)){
            $data = $this->request->data;
            if($id && $data_local){
                $this->Siup->id = $id;
                $msg = 'merubah';
            }else{
                $this->loadModel('Siup');
                $this->Siup->create();
                $msg = 'menambah';
            }

            $truck = $this->Truck->find('first', array(
                'conditions' => array(
                    'truck.id' => $truck_id
                )
            ));
            
            $data['Siup']['truck_id'] = $truck_id;
            $data['Siup']['tgl_siup'] = (!empty($data['Siup']['tgl_siup'])) ? date('Y-m-d', strtotime($data['Siup']['tgl_siup'])) : '';

            $date_old = (!empty($truck['Truck']['siup'])) ? strtotime($truck['Truck']['siup']) : 0;
            $date_new = strtotime($data['Siup']['tgl_siup']);
            
            $check = false;
            if($date_new >= $date_old){
                $check = true;
            }
            $this->Siup->set($data);

            if($this->Siup->validates($data) && $check){
                if($this->Siup->save($data)){

                    $this->Truck->id = $truck_id;
                    $this->Truck->set('siup', $data['Siup']['tgl_siup']);
                    $this->Truck->save();

                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s SIUP Truk'), $msg), 'success');
                    $this->redirect(array(
                        'controller' => 'trucks',
                        'action' => 'siup',
                        $truck_id
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s SIUP Truk'), $msg), 'error');  
                }
            }else{
                $text = sprintf(__('Gagal %s SIUP Truk'), $msg);
                if( !$check ){
                    $text .= ', tanggal SIUP harus lebih besar dari sebelumnya';
                }
                $this->MkCommon->setCustomFlash($text, 'error');
            }
        }else{
            
            if($id && $data_local){
                
                $this->request->data = $data_local;
            }
        }

        $sub_module_title = __('Histori SIUP Truk');
        $this->set(compact('truck_id', 'sub_module_title'));
        $this->render('siup_form');
    }
}
