<?php
App::uses('AppController', 'Controller');
class TrucksController extends AppController {
	public $uses = array();

    public $components = array(
        'RjTruck'
    );

    function beforeFilter() {
        parent::beforeFilter();
        $this->set('title_for_layout', __('ERP RJTM | Data Truk'));
        $this->set('module_title', __('Truk'));
    }

    function search( $index = 'index' ){
        $refine = array();
        if(!empty($this->request->data)) {
            $refine = $this->RjTruck->processRefine($this->request->data);
            $params = $this->RjTruck->generateSearchURL($refine);
            $params['action'] = $index;

            $this->redirect($params);
        }
        $this->redirect('/');
    }

	public function index() {
        $this->loadModel('Truck');
		$this->set('active_menu', 'trucks');
		$this->set('sub_module_title', __('Data Truk'));
        $conditions = array();
        
        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['nopol'])){
                $nopol = urldecode($refine['nopol']);
                $this->request->data['Truck']['nopol'] = $nopol;
                $conditions['Truck.nopol LIKE '] = '%'.$nopol.'%';
            }
        }

        $this->paginate = $this->Truck->getData('paginate', array(
            'conditions' => $conditions
        ));
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
            ),
            'contain' => array(
                'TruckCustomer',
                'Leasing'
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
        $driverConditions = array(
            'Driver.status' => 1,
            'Truck.id' => NULL,
        );

        if(!empty($this->request->data)){
            $data = $this->request->data;
            $leasing_id = 0;

            if($id && $data_local){
                $this->Truck->id = $id;
                if(!empty($data['Leasing']['id'])){
                    $leasing_id = $data['Leasing']['id'];
                }
                $msg = 'merubah';
            }else{
                $this->loadModel('Truck');
                $this->Truck->create();
                $msg = 'menambah';
            }
            
            $data['Truck']['tgl_bpkb'] = (!empty($data['Truck']['tgl_bpkb'])) ? date('Y-m-d', strtotime($data['Truck']['tgl_bpkb'])) : '';
            $data['Leasing']['paid_date'] = (!empty($data['Leasing']['paid_date'])) ? date('Y-m-d', strtotime($data['Leasing']['paid_date'])) : '';

            $this->Truck->set($data);
            $check_alokasi = false;
            if( !empty($data['TruckCustomer']['customer_id']) ){
                foreach ($data['TruckCustomer']['customer_id'] as $key => $value) {
                    if($value){
                        $check_alokasi = true;
                        break;
                    }
                }
            }

            if($this->Truck->validates($data) && $check_alokasi){
                if($this->Truck->save($data)){
                    $truck_id = $this->Truck->id;
                    
                    /*Begin Alokasi*/
                    $this->Truck->TruckCustomer->deleteAll(array(
                        'truck_id' => $truck_id
                    ));
                    $data_customer = array();
                    foreach ($data['TruckCustomer']['customer_id'] as $key => $value) {
                        if(!empty($value)){
                            $data_customer[$key]['TruckCustomer']['customer_id'] = $value;
                            $data_customer[$key]['TruckCustomer']['truck_id'] = $truck_id;
                        }
                    }
                    $this->Truck->TruckCustomer->saveMany($data_customer);
                    /*End Alokasi*/

                    /*Begin Leasing*/
                    $data['Leasing']['truck_id'] = $truck_id;
                    if($leasing_id){
                        $this->Truck->Leasing->id = $leasing_id;
                    }else{
                        $this->Truck->Leasing->create();
                    }
                    $this->Truck->Leasing->set($data);
                    $this->Truck->Leasing->save();
                    /*End Leasing*/

                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s truk'), $msg), 'success');
                    $this->redirect(array(
                        'controller' => 'trucks',
                        'action' => 'perlengkapan',
                        $this->Truck->id
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s truk'), $msg), 'error');  
                }
            }else{
                $text_error = '';
                if(!$check_alokasi){
                    $text_error = 'minimal 1 alokasi setiap truk';
                }
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s truk, %s'), $msg, $text_error), 'error');
            }
        }else{
            if($id && $data_local){
                $this->request->data = $data_local;

                if(!empty($data_local['TruckCustomer'])){
                    $data_temp = $data_local['TruckCustomer'];
                    unset($this->request->data['TruckCustomer']);
                    foreach ($data_temp as $key => $value) {
                        $this->request->data['TruckCustomer']['customer_id'][$key] = $value['customer_id'];
                    }
                }
            }
        }

        if( !empty($data_local)) {
            unset($driverConditions['Truck.id']);
            $driverConditions['OR'] = array(
                'Truck.id' => NULL,
                'Driver.id' => $data_local['Truck']['driver_id'],
            );
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
        $companies = $this->Truck->Company->getData('list', array(
            'conditions' => array(
                'Company.status' => 1
            ),
            'fields' => array(
                'Company.id', 'Company.name'
            )
        ));
        $drivers = $this->Truck->Driver->getData('list', array(
           'conditions' => $driverConditions,
            'fields' => array(
                'Driver.id', 'Driver.name'
            ),
            'contain' => array(
                'Truck'
            )
        ));

        $this->loadModel('Customer');
        $customers = $this->Customer->getData('list', array(
            'conditions' => array(
                'Customer.status' => 1
            ),
            'fields' => array(
                'Customer.id', 'Customer.name'
            )
        ));

        $now_year = date('Y');
        $start_year = 1984;

        $years = array();
        for($now_year;$now_year >= $start_year;$now_year--){
            $years[$now_year] = $now_year;
        }

        $this->set('active_menu', 'trucks');
        $this->set(compact(
            'truck_brands', 'truck_categories', 'truck_brands', 
            'companies', 'drivers', 'years', 'customers'
        ));
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
            $this->Truck->set('status', 0);
            // $this->Truck->set('status', $value);

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
        $options = array(
            'conditions' => array(
                'TruckBrand.status' => 1
            )
        );

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['name'])){
                $name = urldecode($refine['name']);
                $this->request->data['TruckBrand']['name'] = $name;
                $options['conditions']['TruckBrand.name LIKE '] = '%'.$name.'%';
            }
        }

		$this->paginate = $this->TruckBrand->getData('paginate', $options);
		$truck_brands = $this->paginate('TruckBrand');

        $this->set('active_menu', 'trucks');
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

        $this->set('active_menu', 'trucks');
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
        $options = array(
            'conditions' => array(
                'TruckCategory.status' => 1
            )
        );

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['name'])){
                $name = urldecode($refine['name']);
                $this->request->data['TruckCategory']['name'] = $name;
                $options['conditions']['TruckCategory.name LIKE '] = '%'.$name.'%';
            }
        }
		$this->paginate = $this->TruckCategory->getData('paginate', $options);
		$truck_categories = $this->paginate('TruckCategory');

        $this->set('active_menu', 'trucks');
		$this->set('sub_module_title', 'Jenis Truk');
		$this->set('truck_categories', $truck_categories);
	}

	function category_add(){
        $this->set('sub_module_title', 'Tambah Jenis Truk');
        $this->doCategory();
    }

    function category_edit($id){
    	$this->loadModel('TruckCategory');
        $this->set('sub_module_title', 'Rubah Jenis Truk');
        $type_property = $this->TruckCategory->find('first', array(
            'conditions' => array(
                'TruckCategory.id' => $id
            )
        ));

        if(!empty($type_property)){
            $this->doCategory($id, $type_property);
        }else{
            $this->MkCommon->setCustomFlash(__('Jenis Truk tidak ditemukan'), 'error');  
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
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Jenis Truk'), $msg), 'success');
                    $this->redirect(array(
                        'controller' => 'trucks',
                        'action' => 'categories'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Jenis Truk'), $msg), 'error');  
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Jenis Truk'), $msg), 'error');
            }
        }else{
            
            if($id && $data_local){
                
                $this->request->data = $data_local;
            }
        }

        $this->set('active_menu', 'trucks');
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
            $this->MkCommon->setCustomFlash(__('Jenis Truk tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    function drivers(){
        $this->loadModel('Driver');

        $conditions = array(
            'Driver.status' => 1
        );
        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['name'])){
                $name = urldecode($refine['name']);
                $this->request->data['Driver']['name'] = $name;
                $conditions['Driver.name LIKE '] = '%'.$name.'%';
            }
        }

        $this->paginate = $this->Driver->getData('paginate', array(
            'conditions' => $conditions
        ));
        $truck_drivers = $this->paginate('Driver');

        $this->set('active_menu', 'drivers');
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

            $data['Driver']['phone'] = (!empty($data['Driver']['phone'])) ? Sanitize::paranoid($data['Driver']['phone']) : '';
            $data['Driver']['phone_2'] = (!empty($data['Driver']['phone_2'])) ? Sanitize::paranoid($data['Driver']['phone_2']) : '';
            $data['Driver']['expired_date_sim'] = (!empty($data['Driver']['expired_date_sim'])) ? date('Y-m-d', strtotime($data['Driver']['expired_date_sim'])) : '';
            $data['Driver']['birth_date'] = $this->MkCommon->getDateSelectbox($data['Driver']['tgl_lahir']);
            $data['Driver']['join_date'] = $this->MkCommon->getDateSelectbox($data['Driver']['tgl_penerimaan']);
            $data['Driver']['expired_date_sim'] = $this->MkCommon->getDateSelectbox($data['Driver']['tgl_expire_sim']);
            // $data['Driver']['photo'] = $this->MkCommon->getFilePhoto($data['Driver']['photo']);

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
        } else if($id && $data_local){
            if( !empty($data_local['Driver']['expired_date_sim']) && $data_local['Driver']['expired_date_sim'] == '0000-00-00' ) {
                unset($data_local['Driver']['expired_date_sim']);
            }

            $data_local = $this->Driver->getGenerateDate($data_local);
            $this->request->data = $data_local;
        }

        $this->loadModel('DriverRelation');
        $this->loadModel('Branch');

        $driverRelations = $this->DriverRelation->find('list', array(
            'conditions' => array(
                'DriverRelation.status' => 1
            ),
            'fields' => array(
                'DriverRelation.id', 'DriverRelation.name'
            )
        ));
        $branches = $this->Branch->getData('list', array(
            'conditions' => array(
                'Branch.status' => 1
            ),
            'fields' => array(
                'Branch.id', 'Branch.name'
            )
        ));

        $this->set('active_menu', 'drivers');
        $this->set(compact(
            'driverRelations', 'branches'
        ));
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

    function kir($id = false){
        $this->paginate = $this->Truck->Kir->getData('paginate', array(
            'conditions' => array(
                'status' => 1
            ),
            'order' => array(
                'Kir.created'
            )
        ));
        $kir = $this->paginate('Kir');
        
        if(!empty($kir)){
            foreach ($kir as $key => $value) {
                $truck = $this->Truck->getInfoTruck($value['Kir']['truck_id']);
                
                if(!empty($truck)){
                    $kir[$key] = array_merge($value, $truck);
                }else{
                    $kir[$key]['Truck'] = array();
                }
            }
        }
        
        $sub_module_title = __('Histori KIR Truk');
        $this->set(compact('kir', 'sub_module_title'));
    }

    function kir_add(){
        $this->set('sub_module_title', 'Tambah KIR Truk');
        $this->doKir();
    }

    function kir_edit($id){
        $this->loadModel('Kir');
        $this->set('sub_module_title', 'Rubah KIR Truk');
        $Kir = $this->Kir->find('first', array(
            'conditions' => array(
                'Kir.id' => $id
            )
        ));

        if(!empty($Kir)){
            $this->doKir($id, $Kir);
            $this->set(compact('truck'));
        }else{
            $this->MkCommon->setCustomFlash(__('KIR Truk tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'trucks',
                'action' => 'kir'
            ));
        }
    }

    function doKir($id = false, $data_local = false){
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
            
            $data['Kir']['user_id'] = $this->user_id;
            $data['Kir']['tgl_kir'] = (!empty($data['Kir']['tgl_kir'])) ? date('Y-m-d', strtotime($data['Kir']['tgl_kir'])) : '';
            $data['Kir']['tgl_next_kir'] = (!empty($data['Kir']['tgl_next_kir'])) ? date('Y-m-d', strtotime($data['Kir']['tgl_next_kir'])) : '';

            $date_old = '';
            if(!empty($value['Kir']['truck_id'])){
                $truck = $this->Truck->getInfoTruck($value['Kir']['truck_id']);
                $date_old = strtotime($truck['Truck']['kir']);
            }
            
            $date_new = strtotime($data['Kir']['tgl_kir']);
            
            $check = false;
            if($date_new >= $date_old){
                $check = true;
            }else if(empty($date_old)){
                $check = true;
            }
            $this->Kir->set($data);

            if($this->Kir->validates($data) && $check){
                $truck_id = $data['Kir']['truck_id'];
                if($this->Kir->save($data)){

                    $this->Truck->id = $truck_id;
                    $this->Truck->set('kir', $data['Kir']['tgl_kir']);
                    $this->Truck->save();

                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s KIR Truk'), $msg), 'success');
                    $this->redirect(array(
                        'controller' => 'trucks',
                        'action' => 'kir'
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

        $this->loadModel('Truck');
        $trucks = $this->Truck->getData('list', array(
            'conditions' => array(
                'Truck.status' => 1
            ),
            'fields' => array(
                'Truck.id', 'Truck.nopol'
            )
        ));

        $this->set('active_menu', 'trucks');
        $sub_module_title = __('Histori KIR Truk');
        $this->set(compact('truck_id', 'sub_module_title', 'trucks'));
        $this->render('kir_form');
    }

    function siup($id = false){
        $this->paginate = $this->Truck->Siup->getData('paginate', array(
            'conditions' => array(
                'Siup.status' => 1
            ),
            'order' => array(
                'Siup.created'
            )
        ));
        $siup = $this->paginate('Siup');
        
        if(!empty($siup)){
            foreach ($siup as $key => $value) {
                $truck = $this->Truck->getInfoTruck($value['Siup']['truck_id']);
                
                if(!empty($truck)){
                    $siup[$key] = array_merge($value, $truck);
                }else{
                    $siup[$key]['Truck'] = array();
                }
            }
        }
        
        $sub_module_title = __('Histori KIR Truk');
        $this->set(compact('siup', 'sub_module_title'));
    }

    function siup_add(){
        $this->set('sub_module_title', 'Tambah SIUP Truk');
        $this->doSiup();
    }

    function siup_edit($id){
        $this->loadModel('Siup');
        $this->set('sub_module_title', 'Rubah SIUP Truk');
        $Siup = $this->Siup->find('first', array(
            'conditions' => array(
                'Siup.id' => $id
            )
        ));

        if(!empty($Siup)){
            $this->doSiup($id, $Siup);
        }else{
            $this->MkCommon->setCustomFlash(__('SIUP Truk tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'trucks',
                'action' => 'siup'
            ));
        }
    }

    function doSiup($id = false, $data_local = false){
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
            
            $data['Siup']['user_id'] = $this->user_id;
            $data['Siup']['tgl_siup'] = (!empty($data['Siup']['tgl_siup'])) ? date('Y-m-d', strtotime($data['Siup']['tgl_siup'])) : '';
            $data['Siup']['tgl_next_siup'] = (!empty($data['Siup']['tgl_next_siup'])) ? date('Y-m-d', strtotime($data['Siup']['tgl_next_siup'])) : '';

            $date_old = '';
            if(!empty($data['Siup']['truck_id'])){
                $truck = $this->Truck->getInfoTruck($data['Siup']['truck_id']);
                $date_old = strtotime($truck['Truck']['siup']);
            }
            
            $date_new = strtotime($data['Siup']['tgl_siup']);
            
            $check = false;
            if($date_new >= $date_old){
                $check = true;
            }else if(empty($date_old)){
                $check = true;
            }

            $this->Siup->set($data);

            if($this->Siup->validates($data) && $check){
                if($this->Siup->save($data)){

                    $this->Truck->id = $data['Siup']['truck_id'];
                    $this->Truck->set('siup', $data['Siup']['tgl_siup']);
                    $this->Truck->save();

                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s SIUP Truk'), $msg), 'success');
                    $this->redirect(array(
                        'controller' => 'trucks',
                        'action' => 'siup'
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

        $this->loadModel('Truck');
        $trucks = $this->Truck->getData('list', array(
            'conditions' => array(
                'Truck.status' => 1
            ),
            'fields' => array(
                'Truck.id', 'Truck.nopol'
            )
        ));

        $this->set('active_menu', 'trucks');
        $sub_module_title = __('Histori SIUP Truk');
        $this->set(compact('truck_id', 'sub_module_title', 'trucks'));
        $this->render('siup_form');
    }

    function alocations($id = false){
        if(!empty($id)){
            $truck = $this->Truck->getTruck($id);

            if(!empty($truck)){
                $this->paginate = $this->Truck->TruckAlocation->getData('paginate', array(
                    'conditions' => array(
                        'truck_id' => $id
                    ),
                    'order' => array(
                        'TruckAlocation.created'
                    )
                ));
                $alocations = $this->paginate('TruckAlocation');

                if(!empty($alocations)){
                    $this->loadModel('City');
                    foreach ($alocations as $key => $alocation) {
                        $alocations[$key] = $this->City->getMerge($alocation, $alocation['TruckAlocation']['city_id']);
                    }
                }

                $this->set('active_menu', 'trucks');
                $sub_module_title = __('Alokasi Truk');
                $this->set(compact('truck', 'alocations', 'sub_module_title', 'id'));
            }else{
                $this->MkCommon->setCustomFlash(__('Alokasi truk tidak ditemukan.'), 'error');
                $this->redirect($this->referer());
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Alokasi truk tidak ditemukan.'), 'error');
            $this->redirect($this->referer());
        }
    }

    function alocation_add($truck_id){
        $this->set('sub_module_title', 'Tambah Alokasi Truk');

        $truck = $this->Truck->find('first', array(
            'conditions' => array(
                'truck.id' => $truck_id
            )
        ));

        if(!empty($truck)){
            $this->doTruckAlocation($truck_id);
        }else{
            $this->MkCommon->setCustomFlash(__('Alokasi truk tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'trucks',
                'action' => 'alocations'
            ));
        }
    }

    function alocation_edit($truck_id, $id){
        $this->loadModel('TruckAlocation');
        $this->set('sub_module_title', 'Rubah alokasi Truk');
        $TruckAlocation = $this->TruckAlocation->find('first', array(
            'conditions' => array(
                'TruckAlocation.id' => $id,
                'TruckAlocation.truck_id' => $truck_id
            )
        ));

        if(!empty($TruckAlocation)){
            $this->doTruckAlocation($truck_id, $id, $TruckAlocation);
        }else{
            $this->MkCommon->setCustomFlash(__('Alokasi Truk tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'trucks',
                'action' => 'alocations',
                $truck_id
            ));
        }
    }

    function doTruckAlocation($truck_id, $id = false, $data_local = false){
        if(!empty($this->request->data)){
            $data = $this->request->data;
            if($id && $data_local){
                $this->TruckAlocation->id = $id;
                $msg = 'merubah';
            }else{
                $this->loadModel('TruckAlocation');
                $this->TruckAlocation->create();
                $msg = 'menambah';
            }

            $truck = $this->Truck->find('first', array(
                'conditions' => array(
                    'truck.id' => $truck_id
                )
            ));
            
            $data['TruckAlocation']['truck_id'] = $truck_id;
            $check = true;
            if(!$id && !empty($data['TruckAlocation']['city_id'])){
                $check = false;
                $alokasi = $this->Truck->TruckAlocation->getData('first', array(
                    'conditions' => array(
                        'truck_id' => $truck_id,
                        'city_id' => $data['TruckAlocation']['city_id']
                    )
                ));

                if(empty($alokasi)){
                    $check = true;
                }
            }

            $this->TruckAlocation->set($data);

            if($this->TruckAlocation->validates($data) && $check){
                if($this->TruckAlocation->save($data)){

                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s alokasi Truk'), $msg), 'success');
                    $this->redirect(array(
                        'controller' => 'trucks',
                        'action' => 'alocations',
                        $truck_id
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s alokasi Truk'), $msg), 'error');  
                }
            }else{
                $text = sprintf(__('Gagal %s alokasi Truk'), $msg);
                if(!$check){
                    $text .= ', alokasi untuk lokasi ini sudah tersedia untuk truk ini.';
                }
                $this->MkCommon->setCustomFlash($text, 'error');
            }
        }else{
            if($id && $data_local){
                
                $this->request->data = $data_local;
            }
        }

        $this->loadModel('City');
        $cities = $this->City->getData('list', array(
            'conditions' => array(
                'status' => 1
            ),
            'fields' => array(
                'City.id', 'City.name'
            )
        ));

        $this->set('active_menu', 'trucks');
        $sub_module_title = __('Alokasi Truk');
        $this->set(compact('truck_id', 'sub_module_title', 'cities'));
        $this->render('alocation_form');
    }

    function directions($id = false){
        $this->loadModel('Direction');
        $this->paginate = $this->Direction->getData('paginate');
        $directions = $this->paginate('Direction');

        if(!empty($directions)){
            $this->loadModel('City');
            foreach ($directions as $key => $direction) {
                $directions[$key] = $this->City->getMergeDirection($direction);
            }
        }

        $this->set('active_menu', 'directions');
        $sub_module_title = __('Rute Truk');
        $this->set(compact('sub_module_title', 'directions'));
    }

    function direction_add(){
        $this->set('sub_module_title', 'Tambah Rute Truk');
        $this->doDirection();
    }

    function direction_edit($id){
        $this->loadModel('Direction');
        $this->set('sub_module_title', 'Rubah Rute Truk');
        $Direction = $this->Direction->find('first', array(
            'conditions' => array(
                'Direction.id' => $id
            )
        ));

        if(!empty($Direction)){
            $this->doDirection($id, $Direction);
        }else{
            $this->MkCommon->setCustomFlash(__('Rute Truk tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'trucks',
                'action' => 'directions'
            ));
        }
    }

    function doDirection($id = false, $data_local = false){
        if(!empty($this->request->data)){
            $data = $this->request->data;
            if($id && $data_local){
                $this->Direction->id = $id;
                $msg = 'merubah';
            }else{
                $this->loadModel('Direction');
                $this->Direction->create();
                $msg = 'menambah';
            }

            $check = false;
            if(!empty($data['Direction']['from_city_id']) && !empty($data['Direction']['to_city_id'])){
                $defaul_condition = array(
                    'from_city_id' => $data['Direction']['from_city_id'],
                    'to_city_id' => $data['Direction']['to_city_id']
                );
                if( !empty($id) ){
                    $defaul_condition['id'] = $id;
                }
                $rute = $this->Direction->getData('first', array(
                    'conditions' => $defaul_condition
                ));

                if( !empty($id) && !empty($rute) ){
                    $check = true;
                }else{
                    if(empty($rute)){
                        $check = true;
                    }
                }
            }

            $this->Direction->set($data);

            if($this->Direction->validates($data) && $check){
                if($this->Direction->save($data)){
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Rute Truk'), $msg), 'success');
                    $this->redirect(array(
                        'controller' => 'trucks',
                        'action' => 'directions'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Rute Truk'), $msg), 'error');  
                }
            }else{
                $text = sprintf(__('Gagal %s Rute Truk'), $msg);
                if(!$check){
                    $text .= ', rute ini sudah tersedia.';
                }
                $this->MkCommon->setCustomFlash($text, 'error');
            }
        }else{
            if($id && $data_local){
                
                $this->request->data = $data_local;
            }
        }

        $this->loadModel('City');
        $cities = $this->City->getData('list', array(
            'conditions' => array(
                'status' => 1
            ),
            'fields' => array(
                'City.id', 'City.name'
            )
        ));

        $this->set('active_menu', 'directions');
        $sub_module_title = __('Rute Truk');
        $this->set(compact('sub_module_title', 'cities'));
        $this->render('direction_form');
    }

    function direction_toggle($id){
        $this->loadModel('Direction');
        $locale = $this->Direction->getData('first', array(
            'conditions' => array(
                'Direction.id' => $id
            )
        ));

        if($locale){
            $value = true;
            if($locale['Direction']['status']){
                $value = false;
            }

            $this->Direction->id = $id;
            $this->Direction->set('status', $value);
            if($this->Direction->save()){
                $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Kota tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }


    function gas_edit(){
        $this->loadModel('Gases');
        $gas = $this->Gases->find('first', array('conditions' => array('status' => 1)));
        
        if(!empty($this->request->data)){
            $data = $this->request->data;
            if(!empty($gas)){
                $this->Gases->id = $gas['Gases']['id'];
                $msg = 'merubah';
            }else{
                $this->Gases->create();
                $msg = 'menambah';
            }

            $this->Gases->set($data);

            if($this->Gases->validates($data)){
                if($this->Gases->save($data)){
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s rincian bahan bakar'), $msg), 'success');
                    $this->redirect(array(
                        'controller' => 'trucks',
                        'action' => 'gas_edit'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s rincian bahan bakar'), $msg), 'error');  
                }
            }else{
                $text = sprintf(__('Gagal %s rincian bahan bakar'), $msg);
                $this->MkCommon->setCustomFlash($text, 'error');
            }
        }else{
            $this->request->data = $gas;
        }

        $sub_module_title = __('rincian bahan bakar');
        $this->set(compact('sub_module_title'));
    }

    function perlengkapan($truck_id = false){
        if(!empty($truck_id)){
            $truck = $this->Truck->getTruck($truck_id);

            if(!empty($truck)){
                $this->loadModel('TruckPerlengkapan');
                
                $truckPerlengkapans = $this->TruckPerlengkapan->getData('all', array(
                    'conditions' => array(
                        'TruckPerlengkapan.truck_id' => $truck_id
                    )
                ));

                $message = 'dimasukkan';
                if(!empty($truckPerlengkapans)){
                    $message = 'dirubah';
                }

                if(!empty($this->request->data)){
                    $data = $this->request->data;
                    
                    $this->TruckPerlengkapan->updateAll( array(
                        'TruckPerlengkapan.status' => 0,
                    ), array(
                        'TruckPerlengkapan.truck_id' => $truck_id
                    ));

                    $result_data = array();

                    if( !empty($data['TruckPerlengkapan']['perlengkapan_id']) ) {
                        foreach ($data['TruckPerlengkapan']['perlengkapan_id'] as $key => $perlengkapan_id) {
                            if(!empty($perlengkapan_id)){
                                $result_data[$key]['TruckPerlengkapan']['perlengkapan_id'] = $perlengkapan_id;
                                $result_data[$key]['TruckPerlengkapan']['truck_id'] = $truck_id;
                            }
                        }
                    
                        $this->TruckPerlengkapan->create();

                        if($this->TruckPerlengkapan->saveMany($result_data)){
                            $this->MkCommon->setCustomFlash(sprintf(__('kelengkapan truk berhasil %s'), $message), 'success'); 
                            $this->redirect(array(
                                'controller' => 'trucks',
                                'action' => 'index'
                            ));
                        } else {
                            $this->MkCommon->setCustomFlash(sprintf(__('kelengkapan truk gagal %s'), $message), 'error'); 
                        }
                    } else {
                        $this->MkCommon->setCustomFlash(sprintf(__('kelengkapan truk gagal %s'), $message), 'error'); 
                    }
                }else{
                    if(!empty($truckPerlengkapans)){
                        foreach ($truckPerlengkapans as $key => $value) {
                            $this->request->data['TruckPerlengkapan']['perlengkapan_id'][$key] = $value['TruckPerlengkapan']['perlengkapan_id'];
                        }
                    }
                }
            }else{
                $this->MkCommon->setCustomFlash(__('truk tidak ditemukan'), 'error');  
                $this->redirect(array(
                    'controller' => 'trucks',
                    'action' => 'index'
                ));
            }

            $this->loadModel('Perlengkapan');
            $perlengkapans = $this->Perlengkapan->getData('list', array(
                'conditions' => array(
                    'Perlengkapan.status' => 1
                ),
                'fields' => array(
                    'Perlengkapan.id', 'Perlengkapan.name'
                )
            ));

            $sub_module_title = __('Perlengkapan Truk');
            $this->set(compact(
                'truck_id', 'sub_module_title', 'truck',
                'perlengkapans'
            ));
        }else{
            $this->MkCommon->setCustomFlash(__('ID Truk tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'trucks',
                'action' => 'index'
            ));
        }
    }

    function stnk(){
        $this->paginate = $this->Truck->Stnk->getData('paginate', array(
            'conditions' => array(
                'Stnk.status' => 1
            ),
            'order' => array(
                'Stnk.created'
            )
        ));
        $stnk = $this->paginate('Stnk');
        
        if(!empty($stnk)){
            foreach ($stnk as $key => $value) {
                $truck = $this->Truck->getInfoTruck($value['Stnk']['truck_id']);
                
                if(!empty($truck)){
                    $stnk[$key] = array_merge($value, $truck);
                }else{
                    $stnk[$key]['Truck'] = array();
                }
            }
        }
        
        $sub_module_title = __('Histori STNK Truk');
        $this->set(compact('stnk', 'sub_module_title'));
    }

    function stnk_add(){
        $this->set('sub_module_title', 'Perpanjang STNK Truk');
        $this->doStnk();
    }

    function stnk_edit($id){
        $this->loadModel('Stnk');
        $this->set('sub_module_title', 'Rubah STNK Truk');
        $Stnk = $this->Stnk->find('first', array(
            'conditions' => array(
                'Stnk.id' => $id
            )
        ));

        if(!empty($Stnk)){
            $this->doStnk($id, $Stnk);
            $this->set(compact('truck'));
        }else{
            $this->MkCommon->setCustomFlash(__('STNK Truk tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'trucks',
                'action' => 'stnk'
            ));
        }
    }

    function doStnk($id = false, $data_local = false){
        if(!empty($this->request->data)){
            $data = $this->request->data;
            if($id && $data_local){
                $this->Stnk->id = $id;
                $msg = 'merubah';
            }else{
                $this->loadModel('Stnk');
                $this->Stnk->create();
                $msg = 'perpanjang';
            }
            
            $temp_data = $data;

            $data['Stnk']['user_id'] = $this->user_id;
            $data['Stnk']['tgl_bayar'] = (!empty($data['Stnk']['tgl_bayar'])) ? date('Y-m-d', strtotime($data['Stnk']['tgl_bayar'])) : '';
            $data['Stnk']['tgl_berakhir'] = (!empty($data['Stnk']['tgl_berakhir'])) ? date('Y-m-d', strtotime($data['Stnk']['tgl_berakhir'])) : '';
            
            unset($data['Stnk']['is_change_plat']);

            $date_old = '';
            if(!empty($value['Stnk']['truck_id'])){
                $last_pay = $this->Stnk->getData('first', array(
                    'conditions' => array(
                        'Stnk.truck_id' => $value['Stnk']['truck_id'],
                    ),
                    'order' => array(
                        'Stnk.id' => 'DESC'
                    )
                ));
                $date_old = strtotime($last_pay['Stnk']['tgl_bayar']);
            }
            
            $date_new = strtotime($data['Stnk']['tgl_bayar']);
            
            $check = false;
            if($date_new >= $date_old){
                $check = true;
            }else if(empty($date_old)){
                $check = true;
            }

            $this->Stnk->set($data);

            if($this->Stnk->validates($data) && $check){
                if($this->Stnk->save($data)){
                    
                    if(!empty($temp_data['Stnk']['is_change_plat'])){
                        $data['Stnk']['tgl_berakhir'] = date('Y-m-d', strtotime('+5 years', strtotime($data['Stnk']['tgl_bayar'])));
                        $data['Stnk']['is_change_plat'] = 1;
                        $this->Stnk->create();
                        $this->Stnk->set($data);
                        $this->Stnk->save();
                    }

                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s STNK Truk'), $msg), 'success');
                    $this->redirect(array(
                        'controller' => 'trucks',
                        'action' => 'stnk'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s STNK Truk'), $msg), 'error');  
                }
            }else{
                $text = sprintf(__('Gagal %s STNK Truk'), $msg);
                if( !$check ){
                    $text .= ', tanggal STNK harus lebih besar dari sebelumnya';
                }
                $this->MkCommon->setCustomFlash($text, 'error');
            }
        }else{
            
            if($id && $data_local){
                
                $this->request->data = $data_local;
            }
        }

        $this->loadModel('Truck');
        $trucks = $this->Truck->getData('list', array(
            'conditions' => array(
                'Truck.status' => 1
            ),
            'fields' => array(
                'Truck.id', 'Truck.nopol'
            )
        ));

        $this->set('active_menu', 'truck');
        $sub_module_title = __('Histori STNK Truk');
        $this->set(compact('truck_id', 'sub_module_title', 'trucks'));
        $this->render('stnk_form');
    }
}