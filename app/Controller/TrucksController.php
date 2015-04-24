<?php
App::uses('AppController', 'Controller');
class TrucksController extends AppController {
	public $uses = array('Truck');

    public $components = array(
        'RjTruck', 'RjImage'
    );

    public $helper = array(
        'PhpExcel'
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
        if( in_array('view_trucks', $this->allowModule) ) {
            $this->loadModel('Laka');
            $this->loadModel('Ttuj');
    		$this->set('active_menu', 'trucks');
    		$this->set('sub_module_title', __('Data Truk'));
            $conditions = array();
            $contain = array(
                'Driver'
            );
            
            if(!empty($this->params['named'])){
                $refine = $this->params['named'];

                if(!empty($refine['nopol'])){
                    $nopol = urldecode($refine['nopol']);
                    $this->request->data['Truck']['nopol'] = $nopol;
                    $conditions['Truck.nopol LIKE '] = '%'.$nopol.'%';
                }
                if(!empty($refine['name'])){
                    $data = urldecode($refine['name']);
                    $conditions['CASE WHEN Driver.alias = \'\' THEN Driver.name ELSE CONCAT(Driver.name, \' ( \', Driver.alias, \' )\') END LIKE'] = '%'.$data.'%';
                    $this->request->data['Driver']['name'] = $data;
                }
                if(!empty($refine['status'])){
                    $data = urldecode($refine['status']);

                    if( in_array($data, array( 'laka', 'away', 'available' )) ) {
                        $this->Truck->bindModel(array(
                            'hasOne' => array(
                                'Laka' => array(
                                    'className' => 'Laka',
                                    'foreignKey' => 'truck_id',
                                    'conditions' => array(
                                        'Laka.status' => 1,
                                        'Laka.completed' => 0,
                                    ),
                                ),
                                'Ttuj' => array(
                                    'className' => 'Ttuj',
                                    'foreignKey' => 'truck_id',
                                    'conditions' => array(
                                        'Ttuj.status' => 1,
                                        'Ttuj.is_pool' => 0,
                                    ),
                                ),
                            )
                        ));
                        $contain[] = 'Laka';
                        $contain[] = 'Ttuj';
                    }

                    switch ($data) {
                        case 'sold':
                            $conditions['Truck.sold'] = 1;
                            break;

                        case 'laka':
                            $contain[] = 'Laka';
                            $conditions['Truck.sold'] = 0;
                            $conditions['Laka.id <>'] = NULL;
                            break;

                        case 'away':
                            $conditions['Truck.sold'] = 0;
                            $conditions['Laka.id'] = NULL;
                            $conditions['Ttuj.id <>'] = NULL;
                            break;
                        
                        case 'available':
                            $conditions['Truck.sold'] = 0;
                            $conditions['Laka.id'] = NULL;
                            $conditions['Ttuj.id'] = NULL;
                            break;
                    }
                    $this->request->data['Truck']['status'] = $data;
                }
            }

            $this->paginate = $this->Truck->getData('paginate', array(
                'conditions' => $conditions,
                'contain' => $contain,
            ));
            $trucks = $this->paginate('Truck');

            if(!empty($trucks)){
                foreach ($trucks as $key => $truck) {
                    $data = $truck['Truck'];

                    $truck = $this->Truck->TruckCategory->getMerge($truck, $data['truck_category_id']);
                    $truck = $this->Truck->TruckBrand->getMerge($truck, $data['truck_brand_id']);
                    $truck = $this->Truck->Company->getMerge($truck, $data['company_id']);
                    $truck = $this->Laka->getMerge($data['id'], $truck);
                    $truck = $this->Ttuj->getTruckStatus($truck, $data['id']);

                    $trucks[$key] = $truck;
                }
            }

            $this->set('trucks', $trucks);
        } else {
            $this->redirect($this->referer());
        }
	}

    function detail($id = false){
        if(!empty($id)){
            $this->loadModel('TruckCustomer');
            $this->loadModel('TruckPerlengkapan');
            $this->loadModel('LeasingDetail');

            $truck = $this->Truck->getTruck($id);

            if(!empty($truck)){
                $truck = $this->TruckCustomer->getMergeTruckCustomer($truck);
                $truckPerlengkapans = $this->TruckPerlengkapan->getData('all', array(
                    'conditions' => array(
                        'TruckPerlengkapan.truck_id' => $truck['Truck']['id'],
                    )
                ));
                $leasing = $this->LeasingDetail->getData('first', array(
                    'conditions' => array(
                        'LeasingDetail.truck_id' => $id,
                        'Leasing.status' => 1,
                    ),
                    'contain' => array(
                        'Leasing' => array(
                            'LeasingCompany'
                        )
                    )
                ));
                
                $_show_perlengkapan = true;
                $sub_module_title = __('Detail Truk');
                $this->set('active_menu', 'trucks');
                $this->set(compact(
                    'truck', 'sub_module_title', 'truckPerlengkapans',
                    '_show_perlengkapan', 'leasing'
                ));
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
        if( in_array('insert_trucks', $this->allowModule) ) {
            $this->set('sub_module_title', __('Tambah Truk'));
            $this->doTruck();
        } else {
            $this->redirect($this->referer());
        }
    }

    function edit($id){
        if( in_array('update_trucks', $this->allowModule) ) {
            $this->set('sub_module_title', 'Rubah truk');
            $truck = $this->Truck->getData('first', array(
                'conditions' => array(
                    'Truck.id' => $id
                ),
            ));

            if(!empty($truck)){
                $truck = $this->Truck->TruckCustomer->getMergeTruckCustomer($truck);
                $this->doTruck($id, $truck);
            }else{
                $this->MkCommon->setCustomFlash(__('truk tidak ditemukan'), 'error');  
                $this->redirect(array(
                    'controller' => 'trucks',
                    'action' => 'index'
                ));
            }
        } else {
            $this->redirect($this->referer());
        }
    }

    function doTruck($id = false, $data_local = false){
        $this->loadModel('Driver');
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
                $data_local = $this->Driver->getGenerateDate($data_local);
            }
            
            $data['Truck']['driver_id'] = (!empty($data['Truck']['driver_id'])) ? $data['Truck']['driver_id'] : 0;
            $data['Truck']['tgl_bpkb'] = (!empty($data['Truck']['tgl_bpkb'])) ? $this->MkCommon->getDate($data['Truck']['tgl_bpkb']) : '';
            $data['Truck']['tgl_stnk'] = (!empty($data['Truck']['tgl_stnk'])) ? $this->MkCommon->getDate($data['Truck']['tgl_stnk']) : '';
            $data['Truck']['tgl_stnk_plat'] = (!empty($data['Truck']['tgl_stnk_plat'])) ? $this->MkCommon->getDate($data['Truck']['tgl_stnk_plat']) : '';
            $data['Truck']['bbnkb'] = $this->MkCommon->convertPriceToString($data['Truck']['bbnkb']);
            $data['Truck']['pkb'] = $this->MkCommon->convertPriceToString($data['Truck']['pkb']);
            $data['Truck']['swdkllj'] = $this->MkCommon->convertPriceToString($data['Truck']['swdkllj']);
            $data['Truck']['tgl_siup'] = (!empty($data['Truck']['tgl_siup'])) ? $this->MkCommon->getDate($data['Truck']['tgl_siup']) : '';
            $data['Truck']['siup'] = $this->MkCommon->convertPriceToString($data['Truck']['siup']);
            $data['Truck']['tgl_kir'] = (!empty($data['Truck']['tgl_kir'])) ? $this->MkCommon->getDate($data['Truck']['tgl_kir']) : '';
            $data['Truck']['kir'] = $this->MkCommon->convertPriceToString($data['Truck']['kir']);
            $data['Leasing']['paid_date'] = (!empty($data['Leasing']['paid_date'])) ? $this->MkCommon->getDate($data['Leasing']['paid_date']) : '';
            $data['Truck']['emergency_call'] = (!empty($data['Truck']['emergency_call'])) ? $data['Truck']['emergency_call'] : '';
            $data['Truck']['emergency_name'] = (!empty($data['Truck']['emergency_name'])) ? $data['Truck']['emergency_name'] : '';
            $data['Truck']['is_gps'] = (!empty($data['Truck']['is_gps'])) ? $data['Truck']['is_gps'] : 0;

            if(!empty($data['Truck']['photo']['name']) && is_array($data['Truck']['photo'])){
                $temp_image = $data['Truck']['photo'];
                $data['Truck']['photo'] = $data['Truck']['photo']['name'];
            }else{
                if($id && $data_local){
                    unset($data['Truck']['photo']);
                    $data['Truck']['id'] = $id;
                }else{
                    $data['Truck']['photo'] = '';
                }
            }

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
                if(!empty($temp_image) && is_array($temp_image)){
                    $uploaded = $this->RjImage->upload($temp_image, '/'.Configure::read('__Site.truck_photo_folder').'/', String::uuid());
                    if(!empty($uploaded)) {
                        if($uploaded['error']) {
                            $this->MkCommon->setCustomFlash($uploaded['message'], 'error');
                        } else {
                            $data['Truck']['photo'] = $uploaded['imageName'];
                        }
                    }
                }

                if($this->Truck->save($data)){
                    $truck_id = $this->Truck->id;
                    
                    /*Begin Alokasi*/
                    $this->Truck->TruckCustomer->deleteAll(array(
                        'truck_id' => $truck_id
                    ));
                    $data_customer = array();
                    foreach ($data['TruckCustomer']['customer_id'] as $key => $value) {
                        if(!empty($value)){
                            if( empty($key) ) {
                                $data_customer[$key]['TruckCustomer']['primary'] = 1;
                            }
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

                    $this->Log->logActivity( sprintf(__('Sukses %s truk'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );

                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s truk'), $msg), 'success');
                    $this->redirect(array(
                        'controller' => 'trucks',
                        'action' => 'perlengkapan',
                        $this->Truck->id
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s truk'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s truk'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                }
            }else{
                $text_error = '';
                if(!$check_alokasi){
                    $text_error = 'minimal 1 alokasi setiap truk';
                }
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s truk, %s'), $msg, $text_error), 'error');
            }
        } else if($id && $data_local){
            $this->request->data= $data_local;

            if(!empty($data_local['TruckCustomer'])){
                $data_temp = $data_local['TruckCustomer'];
                unset($this->request->data['TruckCustomer']);

                foreach ($data_temp as $key => $value) {
                    $this->request->data['TruckCustomer']['customer_id'][$key] = $value['TruckCustomer']['customer_id'];
                }
            }

            if( !empty($this->request->data['Truck']['tgl_bpkb']) ) {
                $this->request->data['Truck']['tgl_bpkb'] = $this->MkCommon->customDate($this->request->data['Truck']['tgl_bpkb'], 'd/m/Y', '');
            }

            if( !empty($this->request->data['Leasing']['paid_date']) ) {
                $this->request->data['Leasing']['paid_date'] = $this->MkCommon->customDate($this->request->data['Leasing']['paid_date'], 'd/m/Y', '');
            }

            if( !empty($this->request->data['Truck']['tgl_stnk']) ) {
                $this->request->data['Truck']['tgl_stnk'] = $this->MkCommon->customDate($this->request->data['Truck']['tgl_stnk'], 'd/m/Y', '');
            }

            if( !empty($this->request->data['Truck']['tgl_stnk_plat']) ) {
                $this->request->data['Truck']['tgl_stnk_plat'] = $this->MkCommon->customDate($this->request->data['Truck']['tgl_stnk_plat'], 'd/m/Y', '');
            }

            if( !empty($this->request->data['Truck']['tgl_siup']) ) {
                $this->request->data['Truck']['tgl_siup'] = $this->MkCommon->customDate($this->request->data['Truck']['tgl_siup'], 'd/m/Y', '');
            }

            if( !empty($this->request->data['Truck']['tgl_kir']) ) {
                $this->request->data['Truck']['tgl_kir'] = $this->MkCommon->customDate($this->request->data['Truck']['tgl_kir'], 'd/m/Y', '');
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
        $truck_facilities = $this->Truck->TruckFacility->getData('list', array(
            'conditions' => array(
                'TruckFacility.status' => 1
            ),
            'fields' => array(
                'TruckFacility.id', 'TruckFacility.name'
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
                'Driver.id', 'Driver.driver_name'
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
                'Customer.id', 'Customer.customer_name_code'
            ),
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
            'companies', 'drivers', 'years', 'customers',
            'truck_facilities', 'data_local', 'id'
        ));
        $this->render('truck_form');
    }

    function toggle($id){
        if( in_array('delete_trucks', $this->allowModule) ) {
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
                    $this->Log->logActivity( sprintf(__('Sukses merubah status Truk ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal merubah status Truk ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );
                }
            }else{
                $this->MkCommon->setCustomFlash(__('truk tidak ditemukan.'), 'error');
            }

            $this->redirect($this->referer());
        } else {
            $this->redirect($this->referer());
        }
    }

	function brands(){
        if( in_array('insert_trucks', $this->allowModule) ) {
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
        } else {
            $this->redirect($this->referer());
        }
	}

	function brand_add(){
        if( in_array('insert_trucks', $this->allowModule) ) {
            $this->set('sub_module_title', 'Tambah Merek Truk');
            $this->doBrand();
        } else {
            $this->redirect($this->referer());
        }
    }

    function brand_edit($id){
        if( in_array('insert_trucks', $this->allowModule) ) {
        	$this->loadModel('TruckBrand');
            $this->set('sub_module_title', 'Rubah Merek Truk');
            $TruckBrand = $this->TruckBrand->getData('first', array(
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
        } else {
            $this->redirect($this->referer());
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
                    $this->Log->logActivity( sprintf(__('Sukses %s Merek Truk'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );
                    $this->redirect(array(
                        'controller' => 'trucks',
                        'action' => 'brands'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Merek Truk'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s Merek Truk'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Merek Truk'), $msg), 'error');
            }
        }else{
            
            if($id && $data_local){
                
                $this->request->data= $data_local;
            }
        }

        $this->set('active_menu', 'trucks');
        $this->render('brand_form');
    }

    function brand_toggle($id){
        if( in_array('insert_trucks', $this->allowModule) ) {
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
                    $this->Log->logActivity( sprintf(__('Sukses merubah status merek truk ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal merubah status merek truk ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );
                }
            }else{
                $this->MkCommon->setCustomFlash(__('Merek Truk tidak ditemukan.'), 'error');
            }

            $this->redirect($this->referer());
        } else {
            $this->redirect($this->referer());
        }
    }

    function categories(){
        if( in_array('insert_trucks', $this->allowModule) ) {
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
        } else {
            $this->redirect($this->referer());
        }
	}

	function category_add(){
        if( in_array('insert_trucks', $this->allowModule) ) {
            $this->set('sub_module_title', 'Tambah Jenis Truk');
            $this->doCategory();
        } else {
            $this->redirect($this->referer());
        }
    }

    function category_edit($id){
        if( in_array('insert_trucks', $this->allowModule) ) {
        	$this->loadModel('TruckCategory');
            $this->set('sub_module_title', 'Rubah Jenis Truk');
            $type_property = $this->TruckCategory->getData('first', array(
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
        } else {
            $this->redirect($this->referer());
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
                    $this->Log->logActivity( sprintf(__('Sukses %s Jenis Truk'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );
                    $this->redirect(array(
                        'controller' => 'trucks',
                        'action' => 'categories'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Jenis Truk'), $msg), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal %s Jenis Truk'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );  
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Jenis Truk'), $msg), 'error');
            }
        }else{
            
            if($id && $data_local){
                
                $this->request->data= $data_local;
            }
        }

        $this->set('active_menu', 'trucks');
        $this->render('category_form');
    }

    function category_toggle($id){
        if( in_array('insert_trucks', $this->allowModule) ) {
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
                    $this->Log->logActivity( sprintf(__('Sukses merubah status Jenis Truk ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );  
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal merubah status Jenis Truk ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );  
                }
            }else{
                $this->MkCommon->setCustomFlash(__('Jenis Truk tidak ditemukan.'), 'error');
            }

            $this->redirect($this->referer());
        } else {
            $this->redirect($this->referer());
        }
    }

    function drivers(){
        if( in_array('view_drivers', $this->allowModule) ) {
            $this->loadModel('Driver');

            $conditions = array(
                'Driver.status' => array( 0, 1 )
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
                'conditions' => $conditions,
                'order' => array(
                    'Driver.status' => 'DESC',
                    'Driver.name' => 'ASC',
                ),
            ), false);
            $truck_drivers = $this->paginate('Driver');

            $this->set('active_menu', 'drivers');
            $this->set('sub_module_title', 'Supir Truk');
            $this->set('truck_drivers', $truck_drivers);
        } else {
            $this->redirect($this->referer());
        }
    }

    function driver_add(){
        if( in_array('insert_drivers', $this->allowModule) ) {
            $this->loadModel('Driver');
            $this->set('sub_module_title', 'Tambah Supir Truk');
            $this->doDriver();
        } else {
            $this->redirect($this->referer());
        }
    }

    function driver_edit($id){
        if( in_array('update_drivers', $this->allowModule) ) {
            $this->loadModel('Driver');
            $this->set('sub_module_title', 'Rubah Supir Truk');
            $driver = $this->Driver->getData('first', array(
                'conditions' => array(
                    'Driver.id' => $id,
                    'Driver.status' => array( 0, 1 ),
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
        } else {
            $this->redirect($this->referer());
        }
    }

    function doDriver($id = false, $data_local = false){
        if(!empty($this->request->data)){
            $data = $this->request->data;

            $data['Driver']['phone'] = (!empty($data['Driver']['phone'])) ? Sanitize::paranoid($data['Driver']['phone']) : '';
            $data['Driver']['phone_2'] = (!empty($data['Driver']['phone_2'])) ? Sanitize::paranoid($data['Driver']['phone_2']) : '';
            $data['Driver']['expired_date_sim'] = (!empty($data['Driver']['expired_date_sim'])) ? $this->MkCommon->getDate($data['Driver']['expired_date_sim']) : '';
            $data['Driver']['birth_date'] = $this->MkCommon->getDateSelectbox($data['Driver']['tgl_lahir']);
            $data['Driver']['join_date'] = $this->MkCommon->getDateSelectbox($data['Driver']['tgl_penerimaan']);
            $data['Driver']['expired_date_sim'] = $this->MkCommon->getDateSelectbox($data['Driver']['tgl_expire_sim']);
            // $data['Driver']['photo'] = $this->MkCommon->getFilePhoto($data['Driver']['photo']);
            
            if(!empty($data['Driver']['photo']['name']) && is_array($data['Driver']['photo'])){
                $temp_image = $data['Driver']['photo'];
                $data['Driver']['photo'] = $data['Driver']['photo']['name'];
            }else{
                if($id && $data_local){
                    unset($data['Driver']['photo']);
                }else{
                    $data['Driver']['photo'] = '';
                }
            }

            if(!empty($data['Driver']['date_resign']['day']) && !empty($data['Driver']['date_resign']['month']) && $data['Driver']['date_resign']['year']){
                $data['Driver']['date_resign'] = sprintf('%s-%s-%s', $data['Driver']['date_resign']['year'], $data['Driver']['date_resign']['month'], $data['Driver']['date_resign']['day']);
                $data['Driver']['is_resign'] = 1;
            }else{
                $data['Driver']['date_resign'] = '';
            }

            if(!empty($data['Driver']['is_resign'])){
                $data['Driver']['status'] = 0;
            }

            if($id && $data_local){
                $this->Driver->id = $id;
                $msg = 'merubah';
                $data['Driver']['id'] = $id;
            }else{
                $this->loadModel('Driver');
                $this->Driver->create();
                $msg = 'menambah';
            }

            $this->Driver->set($data);
            
            if($this->Driver->validates()){
                if(!empty($temp_image) && is_array($temp_image)){
                    $uploaded = $this->RjImage->upload($temp_image, '/'.Configure::read('__Site.profile_photo_folder').'/', String::uuid());
                    if(!empty($uploaded)) {
                        if($uploaded['error']) {
                            $this->MkCommon->setCustomFlash($uploaded['message'], 'error');
                        } else {
                            $data['Driver']['photo'] = $uploaded['imageName'];
                        }
                    }
                }
                
                if($this->Driver->save($data)){
                    $text = sprintf(__('Sukses %s Supir Truk'), $msg);
                    if(!empty($data['Driver']['is_resign'])){
                        $text .= ' dan mengubah status supir menjadi resign.';
                    }

                    $this->MkCommon->setCustomFlash($text, 'success');
                    $this->Log->logActivity( $text, $this->user_data, $this->RequestHandler, $this->params, 1 );  
                    $this->redirect(array(
                        'controller' => 'trucks',
                        'action' => 'drivers'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Supir Truk'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s Supir Truk'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );   
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Supir Truk'), $msg), 'error');
            }
        } else if($id && $data_local){
            if( !empty($data_local['Driver']['expired_date_sim']) && $data_local['Driver']['expired_date_sim'] == '0000-00-00' ) {
                unset($data_local['Driver']['expired_date_sim']);
            }

            $data_local = $this->Driver->getGenerateDate($data_local);
            $this->request->data= $data_local;

            if( !empty($this->request->data['Driver']['expired_date_sim']) && $this->request->data['Driver']['expired_date_sim'] != '0000-00-00' ) {
                $this->request->data['Driver']['expired_date_sim'] = date('d/m/Y', strtotime($this->request->data['Driver']['expired_date_sim']));
            } else {
                $this->request->data['Driver']['expired_date_sim'] = '';
            }

            if(!empty($this->request->data['Driver']['date_resign'])){
                $date_arr = explode('-', $this->request->data['Driver']['date_resign']);
                $this->request->data['Driver']['date_resign'] = array(
                    'day' => $date_arr[2],
                    'month' => $date_arr[1],
                    'year' => $date_arr[0]
                );
            }
        }

        if( empty($id) && empty($this->request->data['Driver']['no_id']) ){
            $this->request->data['Driver']['no_id'] = $this->Driver->generateNoId();
        }

        $this->loadModel('DriverRelation');
        $this->loadModel('Branch');
        $this->loadModel('JenisSim');

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
        $jenisSims = $this->JenisSim->find('list', array(
            'conditions' => array(
                'JenisSim.status' => 1
            ),
            'fields' => array(
                'JenisSim.id', 'JenisSim.name'
            )
        ));

        $this->set('active_menu', 'drivers');
        $this->set(compact(
            'driverRelations', 'branches', 'jenisSims', 'id'
        ));
        $this->render('driver_form');
    }

    function driver_toggle($id){
        if( in_array('delete_drivers', $this->allowModule) ) {
            $this->loadModel('Driver');
            $locale = $this->Driver->getData('first', array(
                'conditions' => array(
                    'Driver.id' => $id,
                    'Driver.status' => array( 0, 1 ),
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
                    $this->Log->logActivity( sprintf(__('Sukses merubah status Supir Truk ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );   
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal merubah status Supir Truk ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );   
                }
            }else{
                $this->MkCommon->setCustomFlash(__('Supir Truk tidak ditemukan.'), 'error');
            }

            $this->redirect($this->referer());
        } else {
            $this->redirect($this->referer());
        }
    }

    function kir($id = false){
        if( in_array('view_kirs', $this->allowModule) ) {
            $this->loadModel('Kir');
            $conditions = array();

            if(!empty($this->params['named'])){
                $refine = $this->params['named'];

                if(!empty($refine['nopol'])){
                    $name = urldecode($refine['nopol']);
                    $this->request->data['Truck']['nopol'] = $name;
                    $conditions['Kir.no_pol LIKE'] = '%'.$name.'%';
                }
            }
            $this->paginate = $this->Kir->getData('paginate', array(
                'conditions' => $conditions,
                'contain' => array(
                    'Truck'
                ),
            ));
            $kir = $this->paginate('Kir');
            
            $this->set('active_menu', 'kir');
            $sub_module_title = __('KIR');
            $this->set(compact('kir', 'sub_module_title'));
        } else {
            $this->redirect($this->referer());
        }
    }

    function kir_add(){
        if( in_array('insert_kirs', $this->allowModule) ) {
            $this->loadModel('Kir');
            $this->set('active_menu', 'kir');
            $this->set('sub_module_title', 'Tambah KIR');
            $this->doKir();
        } else {
            $this->redirect($this->referer());
        }
    }

    function kir_edit($id){
        if( in_array('update_kirs', $this->allowModule) ) {
            $this->loadModel('Kir');
            $this->set('sub_module_title', 'Rubah KIR Truk');
            $kir = $this->Kir->getData('first', array(
                'conditions' => array(
                    'Kir.id' => $id,
                )
            ));

            if(!empty($kir)){
                $this->doKir($id, $kir);
                $this->set(compact('truck', 'kir'));
            }else{
                $this->MkCommon->setCustomFlash(__('KIR Truk tidak ditemukan'), 'error');  
                $this->redirect(array(
                    'controller' => 'trucks',
                    'action' => 'kir'
                ));
            }
        } else {
            $this->redirect($this->referer());
        }
    }

    function doKir($id = false, $kir = false){
        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['truck_id'])){
                $name = urldecode($refine['truck_id']);
                $this->request->data['Kir']['truck_id'] = $name;
                $truck = $this->Truck->getData('first', array(
                    'conditions' => array(
                        'Truck.status' => 1,
                        'Truck.id' => $name,
                    ),
                ));
            }
        }

        if( !empty($this->request->data) && !empty($this->request->data['Kir']) && count($this->request->data['Kir']) > 1 ){
            $data = $this->request->data;

            if($id && $kir){
                $this->Kir->id = $id;
                $msg = 'merubah data perpanjang';
            }else{
                $this->Kir->create();
                $msg = 'perpanjang';
            }

            if( empty($truck) && !empty($kir['Truck']) ) {
                $truck['Truck'] = $kir['Truck'];
            }
            
            $data['Kir']['user_id'] = $this->user_id;
            $data['Kir']['truck_id'] = $truck['Truck']['id'];
            $data['Kir']['no_pol'] = $truck['Truck']['nopol'];
            $data['Kir']['tgl_kir'] = (!empty($data['Kir']['tgl_kir'])) ? $this->MkCommon->getDate($data['Kir']['tgl_kir']) : '';
            $data['Kir']['from_date'] = (!empty($data['Kir']['from_date'])) ? $this->MkCommon->getDate($data['Kir']['from_date']) : '';
            $data['Kir']['to_date'] = (!empty($data['Kir']['to_date'])) ? $this->MkCommon->getDate($data['Kir']['to_date']) : '';
            $data['Kir']['price_estimate'] = $this->MkCommon->convertPriceToString($truck['Truck']['kir']);
            $data['Kir']['price'] = $this->MkCommon->convertPriceToString($data['Kir']['price']);
            $this->Kir->set($data);

            if( $this->Kir->validates($data) ){
                if( $this->Kir->save($data) ){
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s KIR Truk'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s KIR Truk'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );   
                    $this->redirect(array(
                        'controller' => 'trucks',
                        'action' => 'kir'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s KIR Truk'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s KIR Truk'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );    
                }
            }else{
                $text = sprintf(__('Gagal %s KIR Truk'), $msg);
                $this->MkCommon->setCustomFlash($text, 'error');
            }
        } else if($id && $kir){
            $this->request->data = $kir;

            if( !empty($this->request->data['Kir']['tgl_kir']) && $this->request->data['Kir']['tgl_kir'] != '0000-00-00' ) {
                $this->request->data['Kir']['tgl_kir'] = date('d/m/Y', strtotime($this->request->data['Kir']['tgl_kir']));
            } else {
                $this->request->data['Kir']['tgl_kir'] = '';
            }

            $this->request->data['Kir']['from_date'] = (!empty($this->request->data['Kir']['from_date'])) ? $this->MkCommon->customDate($this->request->data['Kir']['from_date'], 'd/m/Y') : '';
            $this->request->data['Kir']['to_date'] = (!empty($this->request->data['Kir']['to_date'])) ? $this->MkCommon->customDate($this->request->data['Kir']['to_date'], 'd/m/Y') : '';
        }

        if( !empty($this->params['named']) && !empty($truck) ){
            $refine = $this->params['named'];

            if(!empty($refine['truck_id'])){
                if( !empty($truck) ) {
                    $this->request->data['Kir']['from_date'] = $this->MkCommon->customDate($truck['Truck']['tgl_kir'], 'd/m/Y', '');
                    $this->request->data['Kir']['price_estimate'] = $this->MkCommon->convertPriceToString($truck['Truck']['kir']);

                    if( !empty($this->request->data['Kir']['from_date']) ) {
                        $toDate = date('d/m/Y', strtotime('+1 year', strtotime($truck['Truck']['tgl_kir'])) );
                        $this->request->data['Kir']['to_date'] = $toDate;
                    }
                }
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

        $this->set('active_menu', 'kir');
        $this->set(compact(
            'truck_id', 'sub_module_title', 'trucks',
            'truck', 'kir'
        ));
        $this->render('kir_form');
    }

    function kir_payments(){
        if( in_array('view_kir_payments', $this->allowModule) ) {
            $this->loadModel('KirPayment');
            $conditions = array(
                'OR' => array(
                    'Kir.paid' => 1,
                    'Kir.rejected' => 1,
                ),
            );

            if(!empty($this->params['named'])){
                $refine = $this->params['named'];

                if(!empty($refine['nopol'])){
                    $name = urldecode($refine['nopol']);
                    $this->request->data['Truck']['nopol'] = $name;
                    $conditions['Kir.no_pol LIKE'] = '%'.$name.'%';
                }
            }
            $this->paginate = $this->KirPayment->getData('paginate', array(
                'conditions' => $conditions,
                'limit' => Configure::read('__Site.config_pagination'),
            ));
            $kirPayments = $this->paginate('KirPayment');

            $this->set('active_menu', 'kir_payments');
            $sub_module_title = __('Pembayaran KIR');
            $this->set(compact('kirPayments', 'sub_module_title'));
        } else {
            $this->redirect($this->referer());
        }
    }

    function kir_payment_add( $kir_id = false ){
        if( in_array('insert_kir_payments', $this->allowModule) ) {
            $this->loadModel('Kir');
            $kir = false;
            
            if( !empty($kir_id) ) {
                $kir = $this->Kir->getData('first', array(
                    'conditions' => array(
                        'Kir.rejected' => 0,
                        'Kir.paid' => 0,
                        'Kir.id' => $kir_id,
                    ),
                ));
            }

            $this->doKirPayment($kir_id, $kir);
            $kirs = $this->Kir->getData('list', array(
                'conditions' => array(
                    'Kir.status' => 1,
                    'Kir.paid' => 0,
                    'Kir.rejected' => 0,
                ),
                'fields' => array(
                    'Kir.id', 'Kir.no_pol'
                )
            ));

            $sub_module_title = __('Pembayaran KIR');
            $this->set(compact(
                'kirs', 'sub_module_title'
            ));
            $this->render('kir_payment_form');
        } else {
            $this->redirect($this->referer());
        }
    }

    public function kir_detail( $id = false ) {
        $this->loadModel('KirPayment');
        $kir = $this->KirPayment->getData('first', array(
            'conditions' => array(
                'KirPayment.id' => $id,
            ),
        ));

        if( !empty($kir) ) {
            $this->doKirPayment($id, $kir);
            $this->set('sub_module_title', __('Detail Pembayaran KIR'));
        } else {
            $this->MkCommon->setCustomFlash(__('Data Pembayaran KIR tidak ditemukan'), 'error');
            $this->redirect($this->referer());
        }
    }

    public function doKirPayment( $id = false, $kir = false ) {
        if(!empty($this->request->data)){
            if( !empty($this->request->data['KirPayment']['kir_id']) && !empty($kir) ){
                $kir_id = $this->request->data['KirPayment']['kir_id'];

                $this->loadModel('KirPayment');
                $this->KirPayment->create();
                $data = $this->request->data;

                $data['KirPayment']['user_id'] = $this->user_id;
                $data['KirPayment']['kir_id'] = $kir_id;
                $data['KirPayment']['kir_payment_date'] = (!empty($data['KirPayment']['kir_payment_date'])) ? $this->MkCommon->getDate($data['KirPayment']['kir_payment_date']) : '';
                $data['Truck']['tgl_kir'] = (!empty($kir['Kir']['to_date'])) ? $this->MkCommon->getDate($kir['Kir']['to_date']) : '';

                if( !empty($data['KirPayment']['rejected']) ) {
                    $data['Kir']['rejected'] = 1;
                } else {
                    $data['Kir']['paid'] = 1;
                }

                $this->KirPayment->set($data);
                $this->Truck->set($data);
                $this->Kir->set($data);
                $this->Truck->id = $kir['Kir']['truck_id'];
                $this->Kir->id = $kir['Kir']['id'];

                if( $this->KirPayment->validates($data) && $this->Truck->validates($data) && $this->Kir->validates($data) ){
                    if( $this->KirPayment->save($data) && $this->Truck->save($data) && $this->Kir->save($data) ){
                        $this->MkCommon->setCustomFlash(sprintf(__('KIR Truk %s telah dibayar'), $kir['Kir']['no_pol']), 'success');
                        $this->Log->logActivity( sprintf(__('KIR Truk %s telah dibayar'), $kir['Kir']['no_pol']), $this->user_data, $this->RequestHandler, $this->params, 1 );    
                        $this->redirect(array(
                            'controller' => 'trucks',
                            'action' => 'kir_payments'
                        ));
                    } else {
                        $this->MkCommon->setCustomFlash(sprintf(__('Gagal membayar KIR Truk %s'), $kir['Kir']['no_pol']), 'error'); 
                        $this->Log->logActivity( sprintf(__('Gagal membayar KIR Truk %s'), $kir['Kir']['no_pol']), $this->user_data, $this->RequestHandler, $this->params, 1 );     
                    }
                } else {
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal membayar KIR Truk %s'), $kir['Kir']['no_pol']), 'error');  
                }
            } else {
                $this->MkCommon->setCustomFlash(__('Mohon pilih No. Pol Truk'), 'error');
            }
        } else if( !empty($kir) ) {
            $this->request->data['KirPayment']['kir_id'] = $id;
            $this->request->data['KirPayment']['tgl_kir'] = $this->MkCommon->customDate($kir['Kir']['tgl_kir'], 'd/m/Y', '');
            $this->request->data['KirPayment']['from_date'] = date('d/m/Y', strtotime($kir['Kir']['from_date']));
            $this->request->data['KirPayment']['to_date'] = date('d/m/Y', strtotime($kir['Kir']['to_date']));
            $this->request->data['KirPayment']['price'] = $kir['Kir']['price'];
            $this->request->data['KirPayment']['price_estimate'] = $this->MkCommon->convertPriceToString($kir['Kir']['price_estimate']);
        }

        $this->set('active_menu', 'kir_payments');
        $this->set(compact(
            'id', 'kir', 'sub_module_title'
        ));
    }

    public function kir_delete( $id ) {
        if( in_array('delete_kirs', $this->allowModule) ) {
            $this->loadModel('Kir');
            $kir = $this->Kir->getData('first', array(
                'conditions' => array(
                    'Kir.paid' => 0,
                    'Kir.rejected' => 0,
                    'Kir.id' => $id,
                ),
            ));

            if( !empty($kir) ) {
                $this->Kir->id = $id;
                $this->Kir->set('status', 0);

                if($this->Kir->save()){
                    $this->MkCommon->setCustomFlash(sprintf(__('KIR Truk %s telah berhasil dihapus'), $kir['Kir']['no_pol']), 'success');
                    $this->Log->logActivity( sprintf(__('KIR Truk %s telah berhasil dihapus'), $kir['Kir']['no_pol']), $this->user_data, $this->RequestHandler, $this->params, 1 );     
                } else {
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal menghapus KIR Truk %s'), $kir['Kir']['no_pol']), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal menghapus KIR Truk %s'), $kir['Kir']['no_pol']), $this->user_data, $this->RequestHandler, $this->params, 1 );      
                }
            } else {
                $this->MkCommon->setCustomFlash(__('Data KIR tidak ditemukan'), 'error');
            }

            $this->redirect($this->referer());
        } else {
            $this->redirect($this->referer());
        }
    }

    function siup($id = false){
        if( in_array('view_siup', $this->allowModule) ) {
            $this->loadModel('Siup');
            $conditions = array();

            if(!empty($this->params['named'])){
                $refine = $this->params['named'];

                if(!empty($refine['nopol'])){
                    $name = urldecode($refine['nopol']);
                    $this->request->data['Truck']['nopol'] = $name;
                    $conditions['Siup.no_pol LIKE'] = '%'.$name.'%';
                }
            }
            $this->paginate = $this->Siup->getData('paginate', array(
                'conditions' => $conditions,
                'contain' => array(
                    'Truck'
                ),
            ));
            $siup = $this->paginate('Siup');
            
            $this->set('active_menu', 'siup');
            $sub_module_title = __('SIUP');
            $this->set(compact('siup', 'sub_module_title'));
        } else {
            $this->redirect($this->referer());
        }
    }

    function siup_add(){
        if( in_array('insert_siup', $this->allowModule) ) {
            $this->loadModel('Siup');
            $this->set('active_menu', 'siup');
            $this->set('sub_module_title', 'Tambah SIUP');
            $this->doSiup();
        } else {
            $this->redirect($this->referer());
        }
    }

    function siup_edit($id){
        if( in_array('update_siup', $this->allowModule) ) {
            $this->loadModel('Siup');
            $this->set('sub_module_title', 'Rubah SIUP Truk');
            $siup = $this->Siup->getData('first', array(
                'conditions' => array(
                    'Siup.id' => $id,
                )
            ));

            if(!empty($siup)){
                $this->doSiup($id, $siup);
                $this->set(compact('truck', 'siup'));
            }else{
                $this->MkCommon->setCustomFlash(__('Siup Truk tidak ditemukan'), 'error');  
                $this->redirect(array(
                    'controller' => 'trucks',
                    'action' => 'siup'
                ));
            }
        } else {
            $this->redirect($this->referer());
        }
    }

    function doSiup($id = false, $siup = false){
        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['truck_id'])){
                $name = urldecode($refine['truck_id']);
                $this->request->data['Siup']['truck_id'] = $name;
                $truck = $this->Truck->getData('first', array(
                    'conditions' => array(
                        'Truck.status' => 1,
                        'Truck.id' => $name,
                    ),
                ));

                if( !empty($truck) ) {
                    $this->request->data['Siup']['from_date'] = $this->MkCommon->customDate($truck['Truck']['tgl_siup'], 'd/m/Y', '');
                    $this->request->data['Siup']['price_estimate'] = $this->MkCommon->convertPriceToString($truck['Truck']['siup']);

                    if( !empty($this->request->data['Siup']['from_date']) ) {
                        $toDate = date('d/m/Y', strtotime('+1 year', strtotime($truck['Truck']['tgl_siup'])) );
                        $this->request->data['Siup']['to_date'] = $toDate;
                    }
                }
            }
        }

        if( !empty($this->request->data['Siup']['tgl_siup']) ){
            $data = $this->request->data;

            if($id && $siup){
                $this->Siup->id = $id;
                $msg = 'merubah data perpanjang';
            }else{
                $this->Siup->create();
                $msg = 'perpanjang';
            }

            if( empty($truck) && !empty($siup['Truck']) ) {
                $truck['Truck'] = $siup['Truck'];
            }
            
            $data['Siup']['user_id'] = $this->user_id;
            $data['Siup']['truck_id'] = $truck['Truck']['id'];
            $data['Siup']['no_pol'] = $truck['Truck']['nopol'];
            $data['Siup']['tgl_siup'] = (!empty($data['Siup']['tgl_siup'])) ? $this->MkCommon->getDate($data['Siup']['tgl_siup']) : '';
            $data['Siup']['from_date'] = (!empty($data['Siup']['from_date'])) ? $this->MkCommon->getDate($data['Siup']['from_date']) : '';
            $data['Siup']['to_date'] = (!empty($data['Siup']['to_date'])) ? $this->MkCommon->getDate($data['Siup']['to_date']) : '';
            $data['Siup']['price_estimate'] = $this->MkCommon->convertPriceToString($truck['Truck']['siup']);
            $data['Siup']['price'] = $this->MkCommon->convertPriceToString($data['Siup']['price']);
            $this->Siup->set($data);

            if( $this->Siup->validates($data) ){
                if( $this->Siup->save($data) ){
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s SIUP Truk'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s SIUP Truk'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );      
                    $this->redirect(array(
                        'controller' => 'trucks',
                        'action' => 'siup'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s SIUP Truk'), $msg), 'error');  
                    $this->Log->logActivity( sprintf(__('Gagal %s SIUP Truk'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );      
                }
            }else{
                $text = sprintf(__('Gagal %s SIUP Truk'), $msg);
                $this->MkCommon->setCustomFlash($text, 'error');
            }
        } else if($id && $siup){
            $this->request->data = $siup;

            if( !empty($this->request->data['Siup']['tgl_siup']) && $this->request->data['Siup']['tgl_siup'] != '0000-00-00' ) {
                $this->request->data['Siup']['tgl_siup'] = date('d/m/Y', strtotime($this->request->data['Siup']['tgl_siup']));
            } else {
                $this->request->data['Siup']['tgl_siup'] = '';
            }

            $this->request->data['Siup']['from_date'] = (!empty($this->request->data['Siup']['from_date'])) ? $this->MkCommon->customDate($this->request->data['Siup']['from_date'], 'd/m/Y') : '';
            $this->request->data['Siup']['to_date'] = (!empty($this->request->data['Siup']['to_date'])) ? $this->MkCommon->customDate($this->request->data['Siup']['to_date'], 'd/m/Y') : '';
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

        $this->set('active_menu', 'siup');
        $this->set(compact(
            'truck_id', 'sub_module_title', 'trucks',
            'truck', 'siup'
        ));
        $this->render('siup_form');
    }

    function siup_payments(){
        if( in_array('view_siup_payments', $this->allowModule) ) {
            $this->loadModel('SiupPayment');
            $conditions = array(
                'OR' => array(
                    'Siup.paid' => 1,
                    'Siup.rejected' => 1,
                ),
            );

            if(!empty($this->params['named'])){
                $refine = $this->params['named'];

                if(!empty($refine['nopol'])){
                    $name = urldecode($refine['nopol']);
                    $this->request->data['Truck']['nopol'] = $name;
                    $conditions['Siup.no_pol LIKE'] = '%'.$name.'%';
                }
            }
            $this->paginate = $this->SiupPayment->getData('paginate', array(
                'conditions' => $conditions,
                'limit' => Configure::read('__Site.config_pagination'),
            ));
            $siupPayments = $this->paginate('SiupPayment');

            $this->set('active_menu', 'siup_payments');
            $sub_module_title = __('Pembayaran SIUP');
            $this->set(compact('siupPayments', 'sub_module_title'));
        } else {
            $this->redirect($this->referer());
        }
    }

    function siup_payment_add( $siup_id = false ){
        if( in_array('insert_siup_payments', $this->allowModule) ) {
            $this->loadModel('Siup');
            $siup = false;
            
            if( !empty($siup_id) ) {
                $siup = $this->Siup->getData('first', array(
                    'conditions' => array(
                        'Siup.rejected' => 0,
                        'Siup.paid' => 0,
                        'Siup.id' => $siup_id,
                    ),
                ));
            }

            $this->doSiupPayment($siup_id, $siup);
            $siups = $this->Siup->getData('list', array(
                'conditions' => array(
                    'Siup.status' => 1,
                    'Siup.paid' => 0,
                    'Siup.rejected' => 0,
                ),
                'fields' => array(
                    'Siup.id', 'Siup.no_pol'
                )
            ));

            $sub_module_title = __('Pembayaran SIUP');
            $this->set(compact(
                'siups', 'sub_module_title'
            ));
            $this->render('siup_payment_form');
        } else {
            $this->redirect($this->referer());
        }
    }

    public function siup_detail( $id = false ) {
        if( in_array('view_siup_payments', $this->allowModule) ) {
            $this->loadModel('SiupPayment');
            $siup = $this->SiupPayment->getData('first', array(
                'conditions' => array(
                    'SiupPayment.id' => $id,
                ),
            ));

            if( !empty($siup) ) {
                $this->doSiupPayment($id, $siup);
                $this->set('sub_module_title', __('Detail Pembayaran SIUP'));
            } else {
                $this->MkCommon->setCustomFlash(__('Data Pembayaran SIUP tidak ditemukan'), 'error');
                $this->redirect($this->referer());
            }
        } else {
            $this->redirect($this->referer());
        }
    }

    public function doSiupPayment( $id = false, $siup = false ) {
        if(!empty($this->request->data)){
            if( !empty($this->request->data['SiupPayment']['siup_id']) && !empty($siup) ){
                $siup_id = $this->request->data['SiupPayment']['siup_id'];

                $this->loadModel('SiupPayment');
                $this->SiupPayment->create();
                $data = $this->request->data;

                $data['SiupPayment']['user_id'] = $this->user_id;
                $data['SiupPayment']['siup_id'] = $siup_id;
                $data['SiupPayment']['siup_payment_date'] = (!empty($data['SiupPayment']['siup_payment_date'])) ? $this->MkCommon->getDate($data['SiupPayment']['siup_payment_date']) : '';
                $data['Truck']['tgl_siup'] = (!empty($siup['Siup']['to_date'])) ? $this->MkCommon->getDate($siup['Siup']['to_date']) : '';

                if( !empty($data['SiupPayment']['rejected']) ) {
                    $data['Siup']['rejected'] = 1;
                } else {
                    $data['Siup']['paid'] = 1;
                }

                $this->SiupPayment->set($data);
                $this->Truck->set($data);
                $this->Siup->set($data);
                $this->Truck->id = $siup['Siup']['truck_id'];
                $this->Siup->id = $siup['Siup']['id'];

                if( $this->SiupPayment->validates($data) && $this->Truck->validates($data) && $this->Siup->validates($data) ){
                    if( $this->SiupPayment->save($data) && $this->Truck->save($data) && $this->Siup->save($data) ){
                        $this->MkCommon->setCustomFlash(sprintf(__('SIUP Truk %s telah dibayar'), $siup['Siup']['no_pol']), 'success');
                        $this->Log->logActivity( sprintf(__('SIUP Truk %s telah dibayar'), $siup['Siup']['no_pol']), $this->user_data, $this->RequestHandler, $this->params, 1 );      
                        $this->redirect(array(
                            'controller' => 'trucks',
                            'action' => 'siup_payments'
                        ));
                    } else {
                        $this->MkCommon->setCustomFlash(sprintf(__('Gagal membayar SIUP Truk %s'), $siup['Siup']['no_pol']), 'error');  
                        $this->Log->logActivity( sprintf(__('Gagal membayar SIUP Truk %s'), $siup['Siup']['no_pol']), $this->user_data, $this->RequestHandler, $this->params, 1 );
                    }
                } else {
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal membayar SIUP Truk %s'), $siup['Siup']['no_pol']), 'error');  
                }
            } else {
                $this->MkCommon->setCustomFlash(__('Mohon pilih No. Pol Truk'), 'error');
            }
        } else if( !empty($siup) ) {
            $this->request->data['SiupPayment']['siup_id'] = $id;
            $this->request->data['SiupPayment']['tgl_siup'] = $this->MkCommon->customDate($siup['Siup']['tgl_siup'], 'd/m/Y', '');
            $this->request->data['SiupPayment']['from_date'] = date('d/m/Y', strtotime($siup['Siup']['from_date']));
            $this->request->data['SiupPayment']['to_date'] = date('d/m/Y', strtotime($siup['Siup']['to_date']));
            $this->request->data['SiupPayment']['price'] = $siup['Siup']['price'];
            $this->request->data['SiupPayment']['price_estimate'] = $this->MkCommon->convertPriceToString($siup['Siup']['price_estimate']);
        }

        $this->set('active_menu', 'siup_payments');
        $this->set(compact(
            'id', 'siup', 'sub_module_title'
        ));
    }

    public function siup_delete( $id ) {
        if( in_array('delete_siup', $this->allowModule) ) {
            $this->loadModel('Siup');
            $siup = $this->Siup->getData('first', array(
                'conditions' => array(
                    'Siup.paid' => 0,
                    'Siup.rejected' => 0,
                    'Siup.id' => $id,
                ),
            ));

            if( !empty($siup) ) {
                $this->Siup->id = $id;
                $this->Siup->set('status', 0);

                if($this->Siup->save()){
                    $this->MkCommon->setCustomFlash(sprintf(__('SIUP Truk %s telah berhasil dihapus'), $siup['Siup']['no_pol']), 'success');
                    $this->Log->logActivity( sprintf(__('SIUP Truk %s telah berhasil dihapus'), $siup['Siup']['no_pol']), $this->user_data, $this->RequestHandler, $this->params, 1 );
                } else {
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal menghapus SIUP Truk %s'), $siup['Siup']['no_pol']), 'error');  
                    $this->Log->logActivity( sprintf(__('Gagal menghapus SIUP Truk %s'), $siup['Siup']['no_pol']), $this->user_data, $this->RequestHandler, $this->params, 1 );
                }
            } else {
                $this->MkCommon->setCustomFlash(__('Data SIUP tidak ditemukan'), 'error');
            }

            $this->redirect($this->referer());
        } else {
            $this->redirect($this->referer());
        }
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

        $truck = $this->Truck->getData('first', array(
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
        $TruckAlocation = $this->TruckAlocation->getData('first', array(
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

            $truck = $this->Truck->getData('first', array(
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
                    $this->Log->logActivity(sprintf(__('Sukses %s alokasi Truk'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );
                    $this->redirect(array(
                        'controller' => 'trucks',
                        'action' => 'alocations',
                        $truck_id
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s alokasi Truk'), $msg), 'error');  
                    $this->Log->logActivity(sprintf(__('Gagal %s alokasi Truk'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );
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
                
                $this->request->data= $data_local;
            }
        }

        $this->loadModel('City');
        $cities = $this->City->getData('list', array(
            'conditions' => array(
                'City.status' => 1,
                // 'City.is_tujuan' => 1,
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
        $Direction = $this->Direction->getData('first', array(
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
                    $this->Log->logActivity(sprintf(__('Sukses %s Rute Truk'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );
                    $this->redirect(array(
                        'controller' => 'trucks',
                        'action' => 'directions'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Rute Truk'), $msg), 'error');  
                    $this->Log->logActivity(sprintf(__('Gagal %s Rute Truk'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );
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
                
                $this->request->data= $data_local;
            }
        }

        $this->loadModel('City');
        $cities = $this->City->getData('list', array(
            'conditions' => array(
                'City.status' => 1,
                // 'City.is_tujuan' => 1,
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
                $this->Log->logActivity(sprintf(__('Sukses merubah status  rute ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                $this->Log->logActivity(sprintf(__('Gagal merubah status  rute ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );
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
                    $this->Log->logActivity(sprintf(__('Sukses %s rincian bahan bakar'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );
                    $this->redirect(array(
                        'controller' => 'trucks',
                        'action' => 'gas_edit'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s rincian bahan bakar'), $msg), 'error');  
                    $this->Log->logActivity(sprintf(__('Gagal %s rincian bahan bakar'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );
                }
            }else{
                $text = sprintf(__('Gagal %s rincian bahan bakar'), $msg);
                $this->MkCommon->setCustomFlash($text, 'error');
            }
        }else{
            $this->request->data= $gas;
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
                                $result_data[$key]['TruckPerlengkapan']['qty'] = !empty($data['TruckPerlengkapan']['qty'][$key])?$data['TruckPerlengkapan']['qty'][$key]:'';
                                $result_data[$key]['TruckPerlengkapan']['truck_id'] = $truck_id;
                            }
                        }

                        $this->TruckPerlengkapan->create();

                        if( !empty($result_data) ) {
                            if($this->TruckPerlengkapan->saveMany($result_data)){
                                $this->MkCommon->setCustomFlash(sprintf(__('kelengkapan truk berhasil %s'), $message), 'success'); 
                                $this->Log->logActivity(sprintf(__('kelengkapan truk berhasil %s'), $message), $this->user_data, $this->RequestHandler, $this->params, 1 );
                                $this->redirect(array(
                                    'controller' => 'trucks',
                                    'action' => 'index'
                                ));
                            } else {
                                $this->MkCommon->setCustomFlash(sprintf(__('kelengkapan truk gagal %s'), $message), 'error');
                                $this->Log->logActivity(sprintf(__('kelengkapan truk gagal %s'), $message), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                            }
                        } else {
                            $this->MkCommon->setCustomFlash(__('Mohon lengkapi Perlengkapan Truk'), 'error'); 
                        }
                    } else {
                        $this->MkCommon->setCustomFlash(sprintf(__('kelengkapan truk gagal %s'), $message), 'error'); 
                    }
                }else{
                    if(!empty($truckPerlengkapans)){
                        foreach ($truckPerlengkapans as $key => $value) {
                            $this->request->data['TruckPerlengkapan']['perlengkapan_id'][$key] = $value['TruckPerlengkapan']['perlengkapan_id'];
                            $this->request->data['TruckPerlengkapan']['qty'][$key] = $value['TruckPerlengkapan']['qty'];
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
                    'Perlengkapan.status' => 1,
                    'Perlengkapan.jenis_perlengkapan_id' => 1,
                ),
                'fields' => array(
                    'Perlengkapan.id', 'Perlengkapan.name'
                )
            ));

            $sub_module_title = __('Perlengkapan Truk');
            $this->set('active_menu', 'trucks');
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

    

    function stnk( $id = false ) {
        if( in_array('view_stnk', $this->allowModule) ) {
            $this->loadModel('Stnk');
            $conditions = array();

            if(!empty($this->params['named'])){
                $refine = $this->params['named'];

                if(!empty($refine['nopol'])){
                    $name = urldecode($refine['nopol']);
                    $this->request->data['Truck']['nopol'] = $name;
                    $conditions['Stnk.no_pol LIKE'] = '%'.$name.'%';
                }
            }
            $this->paginate = $this->Stnk->getData('paginate', array(
                'conditions' => $conditions
            ));
            $stnks = $this->paginate('Stnk');
            
            $this->set('active_menu', 'stnk');
            $sub_module_title = __('STNK');
            $this->set(compact('stnks', 'sub_module_title'));
        } else {
            $this->redirect($this->referer());
        }
    }

    function stnk_add(){
        if( in_array('insert_stnk', $this->allowModule) ) {
            $this->loadModel('Stnk');
            $this->set('active_menu', 'stnk');
            $this->set('sub_module_title', 'Tambah STNK');
            $this->doStnk();
        } else {
            $this->redirect($this->referer());
        }
    }

    function stnk_edit($id){
        if( in_array('update_stnk', $this->allowModule) ) {
            $this->loadModel('Stnk');
            $this->set('sub_module_title', 'Rubah Perpanjang STNK');
            $Stnk = $this->Stnk->getData('first', array(
                'conditions' => array(
                    'Stnk.id' => $id,
                ),
                'contain' => array(
                    'Truck'
                ),
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
        } else {
            $this->redirect($this->referer());
        }
    }

    function doStnk($id = false, $stnk = false){
        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['truck_id'])){
                $name = urldecode($refine['truck_id']);
                $this->request->data['Stnk']['truck_id'] = $name;
                $truck = $this->Truck->getData('first', array(
                    'conditions' => array(
                        'Truck.status' => 1,
                        'Truck.id' => $name,
                    ),
                ));

                if( !empty($truck) ) {
                    $this->request->data['Stnk']['from_date'] = $this->MkCommon->customDate($truck['Truck']['tgl_stnk'], 'd/m/Y', '');
                    $this->request->data['Stnk']['plat_from_date'] = $this->MkCommon->customDate($truck['Truck']['tgl_stnk_plat'], 'd/m/Y', '');
                    $this->request->data['Stnk']['price_estimate'] = $this->MkCommon->convertPriceToString($truck['Truck']['bbnkb']+$truck['Truck']['pkb']);

                    if( !empty($this->request->data['Stnk']['from_date']) ) {
                        $toDate = date('d/m/Y', strtotime('+1 year', strtotime($truck['Truck']['tgl_stnk'])) );
                        $this->request->data['Stnk']['to_date'] = $toDate;
                    }
                    if( !empty($this->request->data['Stnk']['plat_from_date']) ) {
                        $toDate = date('d/m/Y', strtotime('+5 year', strtotime($truck['Truck']['tgl_stnk_plat'])) );
                        $this->request->data['Stnk']['plat_to_date'] = $toDate;
                    }
                }
            }
        }

        if( !empty($this->request->data['Stnk']['tgl_bayar']) && empty($stnk['Stnk']['paid']) && empty($stnk['Stnk']['rejected']) ){
            $data = $this->request->data;

            if($id && $stnk){
                $this->Stnk->id = $id;
                $msg = 'merubah';
            }else{
                $this->Stnk->create();
                $msg = 'perpanjang';
            }
            
            $temp_data = $data;

            if( empty($truck) && !empty($stnk['Truck']) ) {
                $truck['Truck'] = $stnk['Truck'];
            }

            $price = trim($data['Stnk']['price']);
            $data['Stnk']['user_id'] = $this->user_id;
            $data['Stnk']['truck_id'] = $truck['Truck']['id'];
            $data['Stnk']['no_pol'] = $truck['Truck']['nopol'];
            $data['Stnk']['tgl_bayar'] = (!empty($data['Stnk']['tgl_bayar'])) ? $this->MkCommon->getDate($data['Stnk']['tgl_bayar']) : '';
            $data['Stnk']['from_date'] = (!empty($data['Stnk']['from_date'])) ? $this->MkCommon->getDate($data['Stnk']['from_date']) : '';
            $data['Stnk']['to_date'] = (!empty($data['Stnk']['to_date'])) ? $this->MkCommon->getDate($data['Stnk']['to_date']) : '';

            if( !empty($data['Stnk']['is_change_plat']) ) {
                $data['Stnk']['plat_from_date'] = (!empty($data['Stnk']['plat_from_date'])) ? $this->MkCommon->getDate($data['Stnk']['plat_from_date']) : '';
                $data['Stnk']['plat_to_date'] = (!empty($data['Stnk']['plat_to_date'])) ? $this->MkCommon->getDate($data['Stnk']['plat_to_date']) : '';
            } else {
                unset($data['Stnk']['plat_from_date']);
                unset($data['Stnk']['plat_to_date']);
            }

            $data['Stnk']['price_estimate'] = $this->MkCommon->convertPriceToString($truck['Truck']['bbnkb']+$truck['Truck']['pkb']);
            $data['Stnk']['price'] = $this->MkCommon->convertPriceToString($data['Stnk']['price']);

            if( $this->Stnk->validates($data) ){
                if( $this->Stnk->save($data) ){
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s STNK Truk'), $msg), 'success');
                    $this->Log->logActivity(sprintf(__('Sukses %s STNK Truk'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                    $this->redirect(array(
                        'controller' => 'trucks',
                        'action' => 'stnk'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s STNK Truk'), $msg), 'error');
                    $this->Log->logActivity(sprintf(__('Gagal %s STNK Truk'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );   
                }
            }else{
                $text = sprintf(__('Gagal %s Stnk Truk'), $msg);
                $this->MkCommon->setCustomFlash($text, 'error');
            }
        } else if($id && $stnk){
            $this->request->data= $stnk;
            $this->request->data['Stnk']['price_estimate'] = $this->MkCommon->convertPriceToString($stnk['Stnk']['price_estimate']);

            if( !empty($this->request->data['Stnk']['tgl_bayar']) && $this->request->data['Stnk']['tgl_bayar'] != '0000-00-00' ) {
                $this->request->data['Stnk']['tgl_bayar'] = date('d/m/Y', strtotime($this->request->data['Stnk']['tgl_bayar']));
            } else {
                $this->request->data['Stnk']['tgl_bayar'] = '';
            }

            $this->request->data['Stnk']['from_date'] = (!empty($this->request->data['Stnk']['from_date'])) ? $this->MkCommon->customDate($this->request->data['Stnk']['from_date'], 'd/m/Y') : '';
            $this->request->data['Stnk']['to_date'] = (!empty($this->request->data['Stnk']['to_date'])) ? $this->MkCommon->customDate($this->request->data['Stnk']['to_date'], 'd/m/Y') : '';


            if( !empty($stnk['Stnk']['is_change_plat']) ) {
                $this->request->data['Stnk']['plat_from_date'] = (!empty($this->request->data['Stnk']['plat_from_date'])) ? $this->MkCommon->customDate($this->request->data['Stnk']['plat_from_date'], 'd/m/Y') : '';
                $this->request->data['Stnk']['plat_to_date'] = (!empty($this->request->data['Stnk']['plat_to_date'])) ? $this->MkCommon->customDate($this->request->data['Stnk']['plat_to_date'], 'd/m/Y') : '';
            } else if( !empty($stnk['Truck']) ) {
                $this->request->data['Stnk']['plat_from_date'] = $this->MkCommon->customDate($stnk['Truck']['tgl_stnk_plat'], 'd/m/Y', '');

                if( !empty($this->request->data['Stnk']['plat_from_date']) ) {
                    $toDate = date('d/m/Y', strtotime('+5 year', strtotime($this->request->data['Stnk']['plat_from_date'])) );
                    $this->request->data['Stnk']['plat_to_date'] = $toDate;
                }
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

        $this->set('active_menu', 'stnk');
        $sub_module_title = __('Perpanjang STNK');
        $this->set(compact(
            'truck_id', 'sub_module_title', 'trucks',
            'stnk'
        ));
        $this->render('stnk_form');
    }

    public function stnk_delete( $id ) {
        if( in_array('delete_stnk', $this->allowModule) ) {
            $this->loadModel('Stnk');
            $stnk = $this->Stnk->getData('first', array(
                'conditions' => array(
                    'Stnk.paid' => 0,
                    'Stnk.rejected' => 0,
                    'Stnk.id' => $id,
                ),
            ));

            if( !empty($stnk) && empty($stnk['Stnk']['paid']) && empty($stnk['Stnk']['rejected']) ) {
                $this->Stnk->id = $id;
                $this->Stnk->set('status', 0);

                if($this->Stnk->save()){
                    $this->MkCommon->setCustomFlash(sprintf(__('STNK Truk %s telah berhasil dihapus'), $stnk['Stnk']['no_pol']), 'success');
                    $this->Log->logActivity(sprintf(__('STNK Truk %s telah berhasil dihapus'), $stnk['Stnk']['no_pol']), $this->user_data, $this->RequestHandler, $this->params, 1 );   
                } else {
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal menghapus STNK Truk %s'), $stnk['Stnk']['no_pol']), 'error');  
                    $this->Log->logActivity(sprintf(__('Gagal menghapus STNK Truk %s'), $stnk['Stnk']['no_pol']), $this->user_data, $this->RequestHandler, $this->params, 1 );   
                }
            } else {
                $this->MkCommon->setCustomFlash(__('Data STNK tidak ditemukan'), 'error');
            }

            $this->redirect($this->referer());
        } else {
            $this->redirect($this->referer());
        }
    }

    function stnk_payments(){
        if( in_array('view_stnk_payments', $this->allowModule) ) {
            $this->loadModel('StnkPayment');
            $conditions = array(
                'OR' => array(
                    'Stnk.paid' => 1,
                    'Stnk.rejected' => 1,
                ),
            );

            if(!empty($this->params['named'])){
                $refine = $this->params['named'];

                if(!empty($refine['nopol'])){
                    $name = urldecode($refine['nopol']);
                    $this->request->data['Truck']['nopol'] = $name;
                    $conditions['Stnk.no_pol LIKE'] = '%'.$name.'%';
                }
            }
            $this->paginate = $this->StnkPayment->getData('paginate', array(
                'conditions' => $conditions,
                'limit' => Configure::read('__Site.config_pagination'),
            ));
            $stnkPayments = $this->paginate('StnkPayment');

            $this->set('active_menu', 'stnk_payments');
            $sub_module_title = __('Pembayaran STNK');
            $this->set(compact('stnkPayments', 'sub_module_title'));
        } else {
            $this->redirect($this->referer());
        }
    }

    function stnk_payment_add( $stnk_id = false ){
        if( in_array('insert_stnk_payments', $this->allowModule) ) {
            $this->loadModel('Stnk');
            $stnk = false;
            
            if( !empty($stnk_id) ) {
                $stnk = $this->Stnk->getData('first', array(
                    'conditions' => array(
                        'Stnk.rejected' => 0,
                        'Stnk.paid' => 0,
                        'Stnk.id' => $stnk_id,
                    ),
                ));
            }

            $this->doStnkPayment($stnk_id, $stnk);
            $stnks = $this->Stnk->getData('list', array(
                'conditions' => array(
                    'Stnk.status' => 1,
                    'Stnk.paid' => 0,
                    'Stnk.rejected' => 0,
                ),
                'fields' => array(
                    'Stnk.id', 'Stnk.no_pol'
                )
            ));

            $sub_module_title = __('Pembayaran STNK');
            $this->set(compact(
                'stnks', 'sub_module_title'
            ));
            $this->render('stnk_payment_form');
        } else {
            $this->redirect($this->referer());
        }
    }

    public function stnk_detail( $id = false ) {
        if( in_array('view_stnk_payments', $this->allowModule) ) {
            $this->loadModel('StnkPayment');
            $stnk = $this->StnkPayment->getData('first', array(
                'conditions' => array(
                    'StnkPayment.id' => $id,
                ),
            ));

            if( !empty($stnk) ) {
                $this->doStnkPayment($id, $stnk);
                $this->set('sub_module_title', __('Detail Pembayaran STNK'));
            } else {
                $this->MkCommon->setCustomFlash(__('Data Pembayaran STNK tidak ditemukan'), 'error');
                $this->redirect($this->referer());
            }
        } else {
            $this->redirect($this->referer());
        }
    }

    public function doStnkPayment( $id = false, $stnk = false ) {
        if(!empty($this->request->data)){
            if( !empty($this->request->data['StnkPayment']['stnk_id']) && !empty($stnk) ){
                $stnk_id = $this->request->data['StnkPayment']['stnk_id'];

                $this->loadModel('StnkPayment');
                $this->StnkPayment->create();
                $data = $this->request->data;

                $data['StnkPayment']['user_id'] = $this->user_id;
                $data['StnkPayment']['stnk_id'] = $stnk_id;
                $data['StnkPayment']['stnk_payment_date'] = (!empty($data['StnkPayment']['stnk_payment_date'])) ? $this->MkCommon->getDate($data['StnkPayment']['stnk_payment_date']) : '';
                $data['Truck']['tgl_stnk'] = (!empty($stnk['Stnk']['to_date'])) ? $this->MkCommon->getDate($stnk['Stnk']['to_date']) : '';

                if( !empty($stnk['Stnk']['is_change_plat']) ) {
                    $data['Truck']['tgl_stnk_plat'] = (!empty($stnk['Stnk']['plat_to_date'])) ? $this->MkCommon->getDate($stnk['Stnk']['plat_to_date']) : '';
                }

                if( !empty($data['StnkPayment']['rejected']) ) {
                    $data['Stnk']['rejected'] = 1;
                } else {
                    $data['Stnk']['paid'] = 1;
                }

                $this->StnkPayment->set($data);
                $this->Truck->set($data);
                $this->Stnk->set($data);
                $this->Truck->id = $stnk['Stnk']['truck_id'];
                $this->Stnk->id = $stnk['Stnk']['id'];

                if( $this->StnkPayment->validates($data) && $this->Truck->validates($data) && $this->Stnk->validates($data) ){
                    if( $this->StnkPayment->save($data) && $this->Truck->save($data) && $this->Stnk->save($data) ){
                        $this->MkCommon->setCustomFlash(sprintf(__('STNK Truk %s telah dibayar'), $stnk['Stnk']['no_pol']), 'success');
                        $this->Log->logActivity(sprintf(__('STNK Truk %s telah dibayar'), $stnk['Stnk']['no_pol']), $this->user_data, $this->RequestHandler, $this->params, 1 );   
                        $this->redirect(array(
                            'controller' => 'trucks',
                            'action' => 'stnk_payments'
                        ));
                    } else {
                        $this->MkCommon->setCustomFlash(sprintf(__('Gagal membayar STNK Truk %s'), $stnk['Stnk']['no_pol']), 'error'); 
                        $this->Log->logActivity(sprintf(__('Gagal membayar STNK Truk %s'), $stnk['Stnk']['no_pol']), $this->user_data, $this->RequestHandler, $this->params, 1 );    
                    }
                } else {
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal membayar STNK Truk %s'), $stnk['Stnk']['no_pol']), 'error');  
                }
            } else {
                $this->MkCommon->setCustomFlash(__('Mohon pilih No. Pol Truk'), 'error');
            }
        } else if( !empty($stnk) ) {
            $this->request->data['StnkPayment']['stnk_id'] = $id;
            $this->request->data['StnkPayment']['tgl_bayar'] = $this->MkCommon->customDate($stnk['Stnk']['tgl_bayar'], 'd/m/Y', '');
            $this->request->data['StnkPayment']['from_date'] = date('d/m/Y', strtotime($stnk['Stnk']['from_date']));
            $this->request->data['StnkPayment']['to_date'] = date('d/m/Y', strtotime($stnk['Stnk']['to_date']));
            $this->request->data['StnkPayment']['price'] = $stnk['Stnk']['price'];
            $this->request->data['StnkPayment']['price_estimate'] = $this->MkCommon->convertPriceToString($stnk['Stnk']['price_estimate']);

            if( !empty($stnk['Stnk']['is_change_plat']) ) {
                $this->request->data['StnkPayment']['plat_from_date'] = date('d/m/Y', strtotime($stnk['Stnk']['plat_from_date']));
                $this->request->data['StnkPayment']['plat_to_date'] = date('d/m/Y', strtotime($stnk['Stnk']['plat_to_date']));
            }
        }

        $this->set('active_menu', 'stnk_payments');
        $this->set(compact(
            'id', 'stnk', 'sub_module_title'
        ));
    }

    function reports($data_action = false) {
        // if( in_array('view_truck_reports', $this->allowModule) ) {
            $this->set('active_menu', 'reports');
            $this->set('sub_module_title', __('Laporan Truk'));
            
            $defaul_condition = array();

            $from_date = '';
            $to_date = '';
            if(!empty($this->params['named'])){
                $refine = $this->params['named'];
                if(!empty($refine['from'])){
                    $data = date('Y-m-d', urldecode($refine['from']));
                    $from_date = $data;
                    $this->request->data['Truck']['from_date'] = $data;
                }
                if(!empty($refine['to'])){
                    $data = date('Y-m-d', urldecode($refine['to']));
                    $to_date = $data;
                    $this->request->data['Truck']['to_date'] = $data;
                }
                if(!empty($refine['nopol'])){
                    $data = urldecode($refine['nopol']);
                    $defaul_condition['Truck.nopol LIKE'] = '%'.$data.'%';
                    $this->request->data['Truck']['nopol'] = $data;
                }
                if(!empty($refine['name'])){
                    $data = urldecode($refine['name']);
                    $defaul_condition['CASE WHEN Driver.alias = \'\' THEN Driver.name ELSE CONCAT(Driver.name, \' ( \', Driver.alias, \' )\') END LIKE'] = '%'.$data.'%';
                    $this->request->data['Driver']['name'] = $data;
                }
            }

            if(!empty($from_date)){
                $defaul_condition['DATE_FORMAT(Truck.created, \'%Y-%m-%d\') >= '] = $from_date;
            }
            if(!empty($to_date)){
                $defaul_condition['DATE_FORMAT(Truck.created, \'%Y-%m-%d\') <= '] = $to_date;
            }

            $options = $this->Truck->getData('paginate', array(
                'conditions' => $defaul_condition,
                'contain' => array(
                    'TruckBrand', 
                    'TruckCategory',
                    'TruckFacility',
                    'Driver'
                )
            ));

            if( !empty($data_action) ) {
                $options['limit'] = Configure::read('__Site.config_pagination_unlimited');
            } else {
                $options['limit'] = 20;
            }

            $this->paginate = $options;
            $trucks = $this->paginate('Truck');

            $this->set(compact('trucks', 'from_date', 'to_date', 'data_action'));

            if($data_action == 'pdf'){
                $this->layout = 'pdf';
            }else if($data_action == 'excel'){
                $this->layout = 'ajax';
            }
        // } else {
        //     $this->redirect($this->referer());
        // }
    }

    function facilities(){
        if( in_array('insert_trucks', $this->allowModule) ) {
            $this->loadModel('TruckFacility');
            $options = array(
                'conditions' => array(
                    'TruckFacility.status' => 1
                )
            );

            if(!empty($this->params['named'])){
                $refine = $this->params['named'];

                if(!empty($refine['name'])){
                    $name = urldecode($refine['name']);
                    $this->request->data['TruckFacility']['name'] = $name;
                    $options['conditions']['TruckFacility.name LIKE '] = '%'.$name.'%';
                }
            }
            $this->paginate = $this->TruckFacility->getData('paginate', $options);
            $truckFacilities = $this->paginate('TruckFacility');

            $this->set('active_menu', 'trucks');
            $this->set('sub_module_title', 'Fasilitas Truk');
            $this->set('truckFacilities', $truckFacilities);
        } else {
            $this->redirect($this->referer());
        }
    }

    function facility_add(){
        if( in_array('insert_trucks', $this->allowModule) ) {
            $this->loadModel('TruckFacility');
            $this->set('sub_module_title', 'Tambah Fasilitas Truk');
            $this->doFacility();
        } else {
            $this->redirect($this->referer());
        }
    }

    function facility_edit($id){
        if( in_array('insert_trucks', $this->allowModule) ) {
            $this->loadModel('TruckFacility');
            $this->set('sub_module_title', 'Rubah Fasilitas Truk');
            $truckFacility = $this->TruckFacility->getData('first', array(
                'conditions' => array(
                    'TruckFacility.id' => $id
                )
            ));

            if(!empty($truckFacility)){
                $this->doFacility($id, $truckFacility);
            }else{
                $this->MkCommon->setCustomFlash(__('Fasilitas Truk tidak ditemukan'), 'error');  
                $this->redirect(array(
                    'controller' => 'trucks',
                    'action' => 'facilities'
                ));
            }
        } else {
            $this->redirect($this->referer());
        }
    }

    function doFacility($id = false, $data_local = false){
        if(!empty($this->request->data)){
            $data = $this->request->data;

            if($id && $data_local){
                $this->TruckFacility->id = $id;
                $msg = 'merubah';
            }else{
                $this->TruckFacility->create();
                $msg = 'menambah';
            }
            $this->TruckFacility->set($data);

            if($this->TruckFacility->validates($data)){
                if($this->TruckFacility->save($data)){
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Fasilitas Truk'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s Fasilitas Truk'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );
                    $this->redirect(array(
                        'controller' => 'trucks',
                        'action' => 'facilities'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Fasilitas Truk'), $msg), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal %s Fasilitas Truk'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );  
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Fasilitas Truk'), $msg), 'error');
            }
        }else{
            
            if($id && $data_local){
                
                $this->request->data= $data_local;
            }
        }

        $this->set('active_menu', 'trucks');
        $this->render('facility_form');
    }

    function facility_toggle($id){
        if( in_array('insert_trucks', $this->allowModule) ) {
            $this->loadModel('TruckFacility');
            $locale = $this->TruckFacility->getData('first', array(
                'conditions' => array(
                    'TruckFacility.id' => $id
                )
            ));

            if($locale){
                $value = true;
                if($locale['TruckFacility']['status']){
                    $value = false;
                }

                $this->TruckFacility->id = $id;
                $this->TruckFacility->set('status', $value);
                if($this->TruckFacility->save()){
                    $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses merubah status Fasilitas Truk ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );  
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal merubah status Fasilitas Truk ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );  
                }
            }else{
                $this->MkCommon->setCustomFlash(__('Fasilitas Truk tidak ditemukan.'), 'error');
            }

            $this->redirect($this->referer());
        } else {
            $this->redirect($this->referer());
        }
    }

    public function capacity_report( $data_action = false ) {
        if( in_array('view_capacity_report', $this->allowModule) ) {
            $this->loadModel('TruckCustomer');
            $this->loadModel('Customer');
            $this->set('active_menu', 'capacity_report');
            $this->set('sub_module_title', __('Laporan Truk Per Kapasitas'));
            
            $options = $this->Customer->getData('paginate', array(
                'conditions' => array(
                    'Customer.status' => 1,
                ),
                'limit' => 20,
            ));

            $options = $this->Customer->getData('paginate', array(
                'conditions' => array(
                    'Customer.status' => 1,
                ),
            ));

            if( !empty($data_action) ) {
                $options['limit'] = Configure::read('__Site.config_pagination_unlimited');
            } else {
                $options['limit'] = 50;
            }

            $this->paginate = $options;
            $customers = $this->paginate('Customer');

            $capacities = $this->Truck->getData('list', array(
                'conditions' => array(
                    'Truck.status' => 1,
                ),
                'group' => array(
                    'Truck.capacity',
                ),
                'fields' => array(
                    'Truck.id',
                    'Truck.capacity',
                ),
            ), false);
            $truckArr = array();

            if( !empty($customers) ) {
                $customerArr = Set::extract('/Customer/id', $customers);
                $trucks = $this->TruckCustomer->getData('all', array(
                    'conditions' => array(
                        'Truck.status' => 1,
                        'TruckCustomer.customer_id' => $customerArr,
                        'TruckCustomer.primary' => $customerArr,
                    ),
                    'contain' => array(
                        'Truck',
                    ),
                    'group' => array(
                        'Truck.capacity',
                        'TruckCustomer.customer_id',
                    ),
                    'fields' => array(
                        'Truck.id',
                        'Truck.capacity',
                        'TruckCustomer.customer_id',
                        'COUNT(Truck.id) AS cnt',
                    ),
                ));

                if( !empty($trucks) ) {
                    foreach ($trucks as $key => $truck) {
                        if( !empty($truck[0]['cnt']) ) {
                            $customer_id = $truck['TruckCustomer']['customer_id'];
                            $capacity = $truck['Truck']['capacity'];
                            $truckArr[$customer_id][$capacity] = $truck[0]['cnt'];
                        }
                    }
                }
            }

            $this->set(compact(
                'data_action', 'customers', 'capacities',
                'truckArr'
            ));

            if($data_action == 'pdf'){
                $this->layout = 'pdf';
            }else if($data_action == 'excel'){
                $this->layout = 'ajax';
            }
        } else {
            $this->redirect($this->referer());
        }
    }

    public function point_perday_report( $data_action = false ) {
        if( in_array('view_point_perday_report', $this->allowModule) ) {
            $this->loadModel('Ttuj');
            $this->loadModel('TtujTipeMotor');
            $this->loadModel('Customer');
            $this->loadModel('CustomerTargetUnitDetail');
            $this->set('active_menu', 'point_perday_report');
            $this->set('sub_module_title', __('Laporan Pencapaian Per Point Per Day'));
            
            if( !empty($this->params['named']) ) {
                $refine = $this->params['named'];

                if( !empty($refine['month']) && !empty($refine['year']) ) {
                    $monthArr[0] = $refine['month'];
                    $monthArr[1] = $refine['year'];

                    if( !empty($monthArr[0]) && !empty($monthArr[1]) ) {
                        $monthNumber = $monthArr[0];

                        if( !empty($monthArr[1]) && !empty($monthNumber) ) {
                            $currentMonth = sprintf("%s-%s", $monthArr[1], $monthNumber);
                        }
                    }
                }
            }

            $currentMonth = !empty($currentMonth)?$currentMonth:date('Y-m');
            $lastDay = date('t', strtotime($currentMonth));
            $options = $this->Customer->getData('paginate', array(
                'conditions' => array(
                    'Customer.status' => 1,
                ),
            ));

            if( !empty($data_action) ) {
                $options['limit'] = Configure::read('__Site.config_pagination_unlimited');
            } else {
                $options['limit'] = 20;
            }

            $this->paginate = $options;
            $customers = $this->paginate('Customer');

            $customerArr = Set::extract('/Customer/id', $customers);
            $ttujs = $this->TtujTipeMotor->getData('all', array(
                'conditions' => array(
                    'TtujTipeMotor.status'=> 1,
                    'Ttuj.status'=> 1,
                    'Ttuj.is_draft'=> 0,
                    'DATE_FORMAT(Ttuj.ttuj_date, \'%Y-%m\')' => $currentMonth,
                    'Ttuj.customer_id' => $customerArr,
                ),
                'contain' => array(
                    'Ttuj',
                ),
                'order' => array(
                    'Ttuj.customer_name' => 'ASC', 
                ),
                'fields' => array(
                    'Ttuj.id', 'Ttuj.ttuj_date',
                    'Ttuj.customer_id', 'SUM(TtujTipeMotor.qty) cnt'
                ),
                'group' => array(
                    'DATE_FORMAT(Ttuj.ttuj_date, \'%Y-%m-%d\')',
                    'Ttuj.customer_id',
                ),
            ), false);
            $dataTtuj = array();
            $targetUnit = array();
            $customerTargetUnits = $this->CustomerTargetUnitDetail->find('all', array(
                'conditions' => array(
                    'CustomerTargetUnit.status' => 1,
                    'CustomerTargetUnit.customer_id' => $customerArr,
                    'DATE_FORMAT(CONCAT(CustomerTargetUnit.year, \'-\', CustomerTargetUnitDetail.month, \'-\', 1), \'%Y-%m\')' => $currentMonth,
                ),
                'order' => array(
                    'CustomerTargetUnit.customer_id' => 'ASC', 
                ),
                'contain' => array(
                    'CustomerTargetUnit'
                ),
            ));

            if( !empty($customerTargetUnits) ) {
                foreach ($customerTargetUnits as $key => $customerTargetUnit) {
                    $idx = sprintf('%s-%s', $customerTargetUnit['CustomerTargetUnit']['year'], date('m', mktime(0, 0, 0, $customerTargetUnit['CustomerTargetUnitDetail']['month'], 10)));
                    $targetUnit[$customerTargetUnit['CustomerTargetUnit']['customer_id']][$idx] = $customerTargetUnit['CustomerTargetUnitDetail']['unit'];
                }
            }

            if( !empty($ttujs) ) {
                foreach ($ttujs as $key => $value) {
                    $totalMuatan = 0;
                    $dayBerangkat = date('d', strtotime($value['Ttuj']['ttuj_date']));
                    $customer_id = $value['Ttuj']['customer_id'];

                    if( !empty($value[0]['cnt']) ) {
                        $totalMuatan = $value[0]['cnt'];
                    }

                    $dataTtuj[$customer_id][$dayBerangkat] = $totalMuatan;
                }
            }

            if( !empty($currentMonth) ) {
                $this->request->data['Truck']['month'] = date('m', strtotime($currentMonth));
                $this->request->data['Truck']['year'] = date('Y', strtotime($currentMonth));
            }

            $this->set(compact(
                'customers', 'data_action',
                'lastDay', 'currentMonth', 'dataTtuj',
                'targetUnit'
            ));

            if($data_action == 'pdf'){
                $this->layout = 'pdf';
            }else if($data_action == 'excel'){
                $this->layout = 'ajax';
            } else {
                $layout_js = array(
                    'freeze',
                );
                $layout_css = array(
                    'freeze',
                );

                $this->set(compact(
                    'layout_css', 'layout_js'
                ));
            }
        } else {
            $this->redirect($this->referer());
        }
    }

    public function point_perplant_report( $data_type = 'depo', $data_action = false ) {
        if( in_array('view_point_perplant_report', $this->allowModule) ) {
            // $this->loadModel('UangJalan');
            $this->loadModel('City');
            $this->loadModel('Ttuj');
            $this->loadModel('TtujTipeMotor');
            $this->loadModel('Customer');
            $this->loadModel('CustomerTargetUnitDetail');
            $this->set('sub_module_title', __('Laporan Pencapaian Per Point Per Plant'));
            
            if( !empty($this->params['named']) ) {
                $refine = $this->params['named'];

                if( !empty($refine['month']) && !empty($refine['year']) ) {
                    $monthArr[0] = $refine['month'];
                    $monthArr[1] = $refine['year'];

                    if( !empty($monthArr[0]) && !empty($monthArr[1]) ) {
                        $monthNumber = $monthArr[0];

                        if( !empty($monthArr[1]) && !empty($monthNumber) ) {
                            $currentMonth = sprintf("%s-%s", $monthArr[1], $monthNumber);
                        }
                    }
                }
            }

            $currentMonth = !empty($currentMonth)?$currentMonth:date('Y-m');
            $lastDay = date('t', strtotime($currentMonth));
            $conditionsCustomer = array(
                'Customer.status' => 1,
                'Customer.customer_type_id' => 2,
            );

            if( $data_type == 'retail' ) {
                $conditionsCustomer['Customer.customer_type_id'] = 1;
            } else {
                $conditionsCustomer['Customer.customer_type_id'] = 2;
            }

            $options = array(
                'conditions' => $conditionsCustomer,
            );

            if( !empty($data_action) ) {
                $options['limit'] = Configure::read('__Site.config_pagination_unlimited');
            } else {
                $options['limit'] = 20;
            }

            $this->paginate = $this->Customer->getData('paginate', $options);
            $customers = $this->paginate('Customer');
            
            $customerArr = Set::extract('/Customer/id', $customers);
            $group = array(
                'Ttuj.from_city_id',
                'Ttuj.customer_id',
            );

            if( $data_type == 'retail' ) {
                unset($group['Ttuj.from_city_id']);
                $this->set('active_menu', 'retail_point_perplant_report');
            } else {
                // $cities = $this->UangJalan->getData('list', array(
                //     'conditions' => array(
                //         'UangJalan.status' => 1,
                //     ),
                //     'fields' => array(
                //         'FromCity.id', 'FromCity.name'
                //     ),
                //     'contain' => array(
                //         'FromCity'
                //     ),
                //     'order' => array(
                //         'FromCity.name' => 'ASC',
                //     ),
                //     'group' => array(
                //         'FromCity.id',
                //     )
                // ), false);
                $this->set('active_menu', 'point_perplant_report');
            }

            $ttujs = $this->TtujTipeMotor->getData('all', array(
                'conditions' => array(
                    'TtujTipeMotor.status'=> 1,
                    'Ttuj.status'=> 1,
                    'Ttuj.is_draft'=> 0,
                    'DATE_FORMAT(Ttuj.ttuj_date, \'%Y-%m\')' => $currentMonth,
                    'Ttuj.customer_id' => $customerArr,
                    'Ttuj.is_retail' => 0,
                ),
                'contain' => array(
                    'Ttuj',
                ),
                'order' => array(
                    'Ttuj.customer_name' => 'ASC', 
                ),
                'fields' => array(
                    'Ttuj.id', 'Ttuj.from_city_id',
                    'Ttuj.customer_id', 'SUM(TtujTipeMotor.qty) cnt'
                ),
                'group' => $group,
            ), false);
            $dataTtuj = array();
            $targetUnit = array();
            $cities = array();
            $customerTargetUnits = $this->CustomerTargetUnitDetail->find('all', array(
                'conditions' => array(
                    'CustomerTargetUnit.status' => 1,
                    'CustomerTargetUnit.customer_id' => $customerArr,
                    'DATE_FORMAT(CONCAT(CustomerTargetUnit.year, \'-\', CustomerTargetUnitDetail.month, \'-\', 1), \'%Y-%m\')' => $currentMonth,
                ),
                'order' => array(
                    'CustomerTargetUnit.customer_id' => 'ASC', 
                ),
                'contain' => array(
                    'CustomerTargetUnit'
                ),
            ));

            if( !empty($customerTargetUnits) ) {
                foreach ($customerTargetUnits as $key => $customerTargetUnit) {
                    $idx = sprintf('%s-%s', $customerTargetUnit['CustomerTargetUnit']['year'], date('m', mktime(0, 0, 0, $customerTargetUnit['CustomerTargetUnitDetail']['month'], 10)));
                    $targetUnit[$customerTargetUnit['CustomerTargetUnit']['customer_id']][$idx] = $customerTargetUnit['CustomerTargetUnitDetail']['unit'];
                }
            }

            if( !empty($ttujs) ) {
                foreach ($ttujs as $key => $value) {
                    $totalMuatan = 0;
                    $customer_id = $value['Ttuj']['customer_id'];
                    $from_city_id = $value['Ttuj']['from_city_id'];

                    if( empty($cities[$from_city_id]) ) {
                        $value = $this->City->getMerge( $value, $from_city_id );

                        if( !empty($value['City']['name']) ) {
                            $cities[$from_city_id] = $value['City']['name'];
                        }
                    }

                    if( !empty($value[0]['cnt']) ) {
                        $totalMuatan = $value[0]['cnt'];
                    }

                    if( $data_type == 'retail' ) {
                        $dataTtuj[$customer_id] = $totalMuatan;
                    } else {
                        $dataTtuj[$customer_id][$from_city_id] = $totalMuatan;
                    }
                }
            }

            if( !empty($currentMonth) ) {
                $this->request->data['Truck']['month'] = date('m', strtotime($currentMonth));
                $this->request->data['Truck']['year'] = date('Y', strtotime($currentMonth));
            }

            $this->set(compact(
                'customers', 'data_action',
                'lastDay', 'currentMonth', 'dataTtuj',
                'targetUnit', 'cities', 'data_type'
            ));

            if($data_action == 'pdf'){
                $this->layout = 'pdf';
            }else if($data_action == 'excel'){
                $this->layout = 'ajax';
            }
        } else {
            $this->redirect($this->referer());
        }
    }

    function licenses_report($data_action = false){
        // if( in_array('view_license_report', $this->allowModule) ) {
            $this->loadModel('Truck');
            $this->loadModel('Customer');

            $conditions = array();
            if(!empty($this->params['named'])){
                $refine = $this->params['named'];

                if(!empty($refine['nopol'])){
                    $data = urldecode($refine['nopol']);
                    $conditions['Truck.nopol LIKE'] = '%'.$data.'%';
                    $this->request->data['Truck']['nopol'] = $data;
                }
                if(!empty($refine['license_stat'])){
                    $data = urldecode($refine['license_stat']);
                    $now_date = date('Y-m-d');
                    if($data == 'expired'){
                        $conditions['OR']['DATE_FORMAT(Truck.tgl_stnk, \'%Y-%m-%d\') <= '] = $now_date;
                        $conditions['OR']['DATE_FORMAT(Truck.tgl_stnk_plat, \'%Y-%m-%d\') <= '] = $now_date;
                        $conditions['OR']['DATE_FORMAT(Truck.tgl_kir, \'%Y-%m-%d\') <= '] = $now_date;
                        $conditions['OR']['DATE_FORMAT(Truck.tgl_siup, \'%Y-%m-%d\') <= '] = $now_date;
                    }else if($data == 'expired_soon'){
                        // $conditions['OR']['DATE_ADD(Truck.tgl_stnk, INTERVAL -30 DAY) <= '] = $now_date;
                        // $conditions['OR']['DATE_ADD(Truck.tgl_stnk_plat, INTERVAL -30 DAY) <= '] = $now_date;
                        // $conditions['OR']['DATE_ADD(Truck.tgl_kir, INTERVAL -30 DAY) <= '] = $now_date;
                        // $conditions['OR']['DATE_ADD(Truck.tgl_siup, INTERVAL -30 DAY) <= '] = $now_date;

                        $conditions['OR'] = array(
                            array(
                                'DATE_ADD(Truck.tgl_stnk, INTERVAL -30 DAY) <=  NOW()',
                                'DATE_FORMAT(Truck.tgl_stnk, \'%Y-%m-%d\') >= NOW()'
                            ),
                            array(
                                'DATE_ADD(Truck.tgl_stnk_plat, INTERVAL -30 DAY) <= NOW()',
                                'DATE_FORMAT(Truck.tgl_stnk_plat, \'%Y-%m-%d\') >= NOW()'
                            ),
                            array(
                                'DATE_ADD(Truck.tgl_kir, INTERVAL -30 DAY) <= NOW()',
                                'DATE_FORMAT(Truck.tgl_kir, \'%Y-%m-%d\') >= NOW()'
                            ),
                            array(
                                'DATE_ADD(Truck.tgl_siup, INTERVAL -30 DAY) <= NOW()',
                                'DATE_FORMAT(Truck.tgl_siup, \'%Y-%m-%d\') >= NOW()'
                            ),
                        );
                    }else if($data == 'active'){
                        $conditions['OR'] = array(
                            array('DATE_FORMAT(Truck.tgl_stnk, \'%Y-%m-%d\') >= NOW()'),
                            array('DATE_FORMAT(Truck.tgl_stnk_plat, \'%Y-%m-%d\') >= NOW()'),
                            array('DATE_FORMAT(Truck.tgl_kir, \'%Y-%m-%d\') >= NOW()'),
                            array('DATE_FORMAT(Truck.tgl_siup, \'%Y-%m-%d\') >= NOW()'),
                        );
                    }
                    $this->request->data['Truck']['status_expired'] = $data;
                }
                if(!empty($refine['alocation'])){
                    $data = urldecode($refine['alocation']);
                    $conditions['TruckCustomer.customer_id'] = $data;
                    $this->request->data['TruckCustomer']['customer_id'] = $data;
                }
            }
// debug($conditions);die();
            $this->Truck->unBindModel(array(
                'hasMany' => array(
                    'TruckCustomer'
                )
            ));

            $this->Truck->bindModel(array(
                'hasOne' => array(
                    'TruckCustomer' => array(
                        'className' => 'TruckCustomer',
                        'foreignKey' => 'truck_id',
                        'conditions' => array(
                            'TruckCustomer.primary' => 1
                        )
                    )
                )
            ), false);

            $this->paginate = $this->Truck->getData('paginate', array(
                'conditions' => $conditions,
                'contain' => array(
                    'TruckCustomer' => array(
                        'CustomerNoType'
                    )
                ),
                'limit' => 10
            ));

            $trucks = $this->paginate('Truck');

            $this->loadModel('Customer');
            $customers = $this->Customer->getData('list', array(
                'conditions' => array(
                    'Customer.status' => 1
                ),
                'fields' => array(
                    'Customer.id', 'Customer.customer_name_code'
                ),
            ));
            $this->set('active_menu', 'licenses_report');
            $sub_module_title = __('Laporan Surat-surat Truk');
            $this->set(compact('trucks', 'customers', 'sub_module_title', 'data_action'));

            if($data_action == 'pdf'){
                $this->layout = 'pdf';
            }else if($data_action == 'excel'){
                $this->layout = 'ajax';
            }
        // } else {
        //     $this->redirect($this->referer());
        // }
    }

    function getMimeType( $filename ) {
        $mime_types = array(
            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

        $ext1 = explode('.',$filename);
        $ext2 = strtolower(end($ext1));
        $ext3 = end($ext1);
        if (array_key_exists($ext2, $mime_types)) {
            return $mime_types[$ext2];
        }
        elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
        }
        else {
            return 'application/octet-stream';
        }
    }

    function addToFiles($key, $url) {
        $tempName = tempnam('C:/tmps', 'php_files');
        $originalName = basename(parse_url($url, PHP_URL_PATH));

        $imgRawData = file_get_contents($url);
        file_put_contents($tempName, $imgRawData);

        $_FILES[$key] = array(
            'name' => $originalName,
            'type' => $this->getMimeType($originalName),
            'tmp_name' => $tempName,
            'error' => 0,
            'size' => strlen($imgRawData),
        );
        return $_FILES;
    }

    function saveTruckCustomer ( $data = false, $truck_id = false ) {
        $result = array(
            'validates' => true,
            'data' => false,
        );

        if( !empty($data['TruckCustomer']['customer_id']) ) {
            foreach ($data['TruckCustomer']['customer_id'] as $key => $customer_id) {
                $dataValidate['TruckCustomer']['customer_id'] = $customer_id;
                $dataValidate['TruckCustomer']['primary'] = !empty($data['TruckCustomer']['primary'][$key])?$data['TruckCustomer']['primary'][$key]:false;
                
                $this->Truck->TruckCustomer->set($dataValidate);

                if( !empty($truck_id) ) {
                    $dataValidate['TruckCustomer']['truck_id'] = $truck_id;
                    $this->Truck->TruckCustomer->create();
                    $this->Truck->TruckCustomer->save($dataValidate);
                } else {
                    if(!$this->Truck->TruckCustomer->validates($dataValidate)){
                        $result['validates'] = false;
                    } else {
                        $result['data'][$key] = $dataValidate;
                    }
                }
            }
        }

        return $result;
    }

    function saveTruckPerlengkapan ( $data = false, $truck_id = false ) {
        $result = array(
            'validates' => true,
            'data' => false,
        );

        if( !empty($data['TruckPerlengkapan']['perlengkapan_id']) ) {
            foreach ($data['TruckPerlengkapan']['perlengkapan_id'] as $key => $perlengkapan_id) {
                $dataValidate['TruckPerlengkapan']['perlengkapan_id'] = $perlengkapan_id;
                $dataValidate['TruckPerlengkapan']['qty'] = !empty($data['TruckPerlengkapan']['qty'][$key])?$data['TruckPerlengkapan']['qty'][$key]:false;

                $this->Truck->TruckPerlengkapan->set($dataValidate);

                if( !empty($truck_id) ) {
                    $dataValidate['TruckPerlengkapan']['truck_id'] = $truck_id;
                    $this->Truck->TruckPerlengkapan->create();
                    $this->Truck->TruckPerlengkapan->save($dataValidate);
                } else {
                    if(!$this->Truck->TruckPerlengkapan->validates($dataValidate)){
                        $result['validates'] = false;
                    } else {
                        $result['data'][$key] = $dataValidate;
                    }
                }
            }
        }

        return $result;
    }

    public function add_import( $download = false ) {
        if(!empty($download)){
            $link_url = FULL_BASE_URL . '/files/trucks.xls';
            $this->redirect($link_url);
            exit;
        } else {
            $this->loadModel('Driver');
            $this->loadModel('Customer');
            $this->loadModel('Perlengkapan');
            App::import('Vendor', 'excelreader'.DS.'excel_reader2');

            $this->set('active_menu', 'truck_import');
            $this->set('sub_module_title', __('Import Truk'));

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
                        $this->MkCommon->setCustomFlash(__('Maaf, silahkan upload file Zip.'), 'error');
                        $this->redirect(array('action'=>'add_import'));
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
                            $this->redirect(array('action'=>'add_import'));
                        }
                    }
                } else {
                    $this->MkCommon->setCustomFlash(__('Maaf, terjadi kesalahan. Silahkan coba lagi, atau hubungi Admin kami.'), 'error');
                    $this->redirect(array('action'=>'add_import'));
                }

                $xls_files = glob( $targetdir );

                if(empty($xls_files)) {
                    $this->rmdir_recursive ( $targetdir);
                    $this->MkCommon->setCustomFlash(__('Tidak terdapat file excel atau berekstensi .xls pada file zip Anda. Silahkan periksa kembali.'), 'error');
                    $this->redirect(array('action'=>'add_import'));
                } else {
                    $uploadedXls = $this->addToFiles('xls', $xls_files[0]);
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
                                    $truckBrand = $this->Truck->TruckBrand->getData('first', array(
                                        'conditions' => array(
                                            'TruckBrand.name' => $merek_truk,
                                            'TruckBrand.status' => 1,
                                        ),
                                    ));
                                    $truckCategory = $this->Truck->TruckCategory->getData('first', array(
                                        'conditions' => array(
                                            'TruckCategory.name' => $jenis_truk,
                                            'TruckCategory.status' => 1,
                                        ),
                                    ));
                                    $truckFacility = $this->Truck->TruckFacility->getData('first', array(
                                        'conditions' => array(
                                            'TruckFacility.name' => $fasilitas_truk,
                                            'TruckFacility.status' => 1,
                                        ),
                                    ));
                                    $company = $this->Truck->Company->getData('first', array(
                                        'conditions' => array(
                                            'Company.name' => $pemilik_truk,
                                            'Company.status' => 1,
                                        ),
                                    ));
                                    $driver = $this->Truck->Driver->getData('first', array(
                                        'conditions' => array(
                                            'Driver.no_id' => $no_id_supir,
                                            'Driver.status' => 1,
                                        ),
                                    ));

                                    if( !empty($truckBrand) ) {
                                        $truck_brand_id = $truckBrand['TruckBrand']['id'];
                                    }
                                    if( !empty($truckCategory) ) {
                                        $truck_category_id = $truckCategory['TruckCategory']['id'];
                                    }
                                    if( !empty($truckFacility) ) {
                                        $truck_facility_id = $truckFacility['TruckFacility']['id'];
                                    }
                                    if( !empty($company) ) {
                                        $company_id = $company['Company']['id'];
                                    }
                                    if( !empty($driver) ) {
                                        $driver_id = $driver['Driver']['id'];
                                    }

                                    $requestData['ROW'.($x-1)] = array(
                                        'Truck' => array(
                                            'truck_brand_id' => !empty($truck_brand_id)?$truck_brand_id:false,
                                            'emergency_call' => !empty($telepon_darurat)?$telepon_darurat:false,
                                            'emergency_name' => !empty($nama_panggilan_darurat)?$nama_panggilan_darurat:false,
                                            'company_id' => !empty($company_id)?$company_id:false,
                                            'truck_category_id' => !empty($truck_category_id)?$truck_category_id:false,
                                            'truck_facility_id' => !empty($truck_facility_id)?$truck_facility_id:false,
                                            'driver_id' => !empty($driver_id)?$driver_id:false,
                                            'nopol' => !empty($nopol)?$nopol:false,
                                            'kir' => !empty($biaya_kir)?str_replace(array('.', ',', '* '), array('', '', ''), $biaya_kir):0,
                                            'siup' => !empty($biaya_siup)?str_replace(array('.', ',', '* '), array('', '', ''), $biaya_siup):0,
                                            'bpkb' => !empty($bpkb)?str_replace(array('.', ',', '* '), array('', '', ''), $bpkb):0,
                                            'atas_nama' => !empty($atas_nama)?$atas_nama:false,
                                            'no_stnk' => !empty($no_stnk)?$no_stnk:false,
                                            'no_rangka' => !empty($no_rangka)?$no_rangka:false,
                                            'bbnkb' => !empty($biaya_bbnkb)?str_replace(array('.', ',', '* '), array('', '', ''), $biaya_bbnkb):0,
                                            'pkb' => !empty($biaya_pkb)?str_replace(array('.', ',', '* '), array('', '', ''), $biaya_pkb):0,
                                            'swdkllj' => !empty($biaya_swdkllj)?str_replace(array('.', ',', '* '), array('', '', ''), $biaya_swdkllj):0,
                                            'no_machine' => !empty($no_mesin)?$no_mesin:false,
                                            'capacity' => !empty($kapasitas)?$kapasitas:false,
                                            'tahun' => !empty($tahun)?$tahun:false,
                                            'tahun_neraca' => !empty($tahun_neraca)?$tahun_neraca:false,
                                            'tgl_bpkb' => !empty($tanggal_bpkb)?$tanggal_bpkb:false,
                                            'is_asset' => !empty($ini_asset)?$ini_asset:0,
                                            'is_gps' => !empty($dilengkapi_gps)?$dilengkapi_gps:0,
                                            'description' => !empty($keterangan)?$keterangan:false,
                                            'tgl_stnk' => !empty($tgl_perpanjang_stnk_1thn)?$tgl_perpanjang_stnk_1thn:false,
                                            'tgl_stnk_plat' => !empty($tgl_perpanjang_stnk_5thn)?$tgl_perpanjang_stnk_5thn:false,
                                            'tgl_siup' => !empty($tgl_perpanjang_siup)?$tgl_perpanjang_siup:false,
                                            'tgl_kir' => !empty($tgl_perpanjang_kir)?$tgl_perpanjang_kir:false,
                                        ),
                                    );
                                    
                                    $i = 1;
                                    $idx = 0;
                                    $flag = true;

                                    while ($flag) {
                                        $varGroup = sprintf('alokasi_truk_%s', $i);

                                        if( !empty($$varGroup) ) {
                                            $customer_code = !empty($$varGroup)?$$varGroup:'';
                                            $customer = $this->Customer->getData('first', array(
                                                'conditions' => array(
                                                    'Customer.code' => $customer_code,
                                                    'Customer.status' => 1,
                                                ),
                                            ));

                                            if( !empty($customer) ) {
                                                $requestData['ROW'.($x-1)]['TruckCustomer']['customer_id'][$i] = $customer['Customer']['id'];
                                            }

                                            if( $i == 1 ) {
                                                $requestData['ROW'.($x-1)]['TruckCustomer']['primary'][$i] = true;
                                            }
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
                                        $varGroup = sprintf('perlengkapan_%s', $i);

                                        if( !empty($$varGroup) ) {
                                            $varJml = sprintf('jumlah_perlengkapan_%s', $i);
                                            $varJml = !empty($$varJml)?$$varJml:false;
                                            $perlengkapan = !empty($$varGroup)?$$varGroup:'';
                                            $perlengkapan = $this->Perlengkapan->getData('first', array(
                                                'conditions' => array(
                                                    'Perlengkapan.name' => $perlengkapan,
                                                    'Perlengkapan.status' => 1,
                                                ),
                                            ));
                                            $perlengkapan_id = !empty($perlengkapan['Perlengkapan']['id'])?$perlengkapan['Perlengkapan']['id']:false;
                                            $requestData['ROW'.($x-1)]['TruckPerlengkapan']['perlengkapan_id'][$i] = $perlengkapan_id;
                                            $requestData['ROW'.($x-1)]['TruckPerlengkapan']['qty'][$i] = $varJml;
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
                                    $saveTruckCustomer = false;
                                    $saveTruckPerlengkapan = false;
                                    $data = $request;

                                    if( !empty($data['TruckCustomer']['customer_id']) ) {
                                        $resultTruckCustomer = $this->saveTruckCustomer($data);
                                        $saveTruckCustomer = !empty($resultTruckCustomer['validates'])?$resultTruckCustomer['validates']:false;
                                    } else {
                                        $saveTruckCustomer = true;
                                    }

                                    if( !empty($data['TruckPerlengkapan']['perlengkapan_id']) ) {
                                        $resultTruckPerlengkapan = $this->saveTruckPerlengkapan($data);
                                        $saveTruckPerlengkapan = !empty($resultTruckPerlengkapan['validates'])?$resultTruckPerlengkapan['validates']:false;
                                    } else {
                                        $saveTruckPerlengkapan = true;
                                    }

                                    $this->Truck->create();
                                    
                                    if( $saveTruckCustomer && $saveTruckPerlengkapan && $this->Truck->save($data) ){
                                        if( !empty($data['TruckCustomer']['customer_id']) ) {
                                            $this->saveTruckCustomer($data, $this->Truck->id);
                                        }
                                        if( !empty($data['TruckPerlengkapan']['perlengkapan_id']) ) {
                                            $this->saveTruckPerlengkapan($data, $this->Truck->id);
                                        }

                                        $this->Log->logActivity( __('Sukses upload Truk by Import Excel'), $this->user_data, $this->RequestHandler, $this->params, 1 );
                                        $successfull_row++;
                                    } else {
                                        $validationErrors = $this->Truck->validationErrors;
                                        $textError = array();

                                        if( !empty($validationErrors) ) {
                                            foreach ($validationErrors as $key => $validationError) {
                                                if( !empty($validationError) ) {
                                                    foreach ($validationError as $key => $error) {
                                                        $textError[] = $error;
                                                    }
                                                }
                                            }
                                        }

                                        if( !empty($textError) ) {
                                            $textError = implode(', ', $textError);
                                        } else {
                                            $textError = '';
                                        }

                                        $failed_row++;
                                        $error_message .= sprintf(__('Gagal pada baris ke %s : Gagal Upload Listing. %s'), $row_submitted, $textError) . '<br>';
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
                $this->redirect(array('action'=>'add_import'));
            }
        }
    }
}