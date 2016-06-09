<?php
App::uses('AppController', 'Controller');
class TrucksController extends AppController {
	public $uses = array('Truck');

    public $components = array(
        'RjTruck', 'RjImage'
    );

    public $helper = array(
        'PhpExcel', 'Truck',
    );

    function beforeFilter() {
        parent::beforeFilter();
        $this->set('title_for_layout', __('ERP RJTM | Data Truk'));
        $this->set('module_title', __('Truk'));
    }

    function search( $index = 'index' ){
        $refine = array();
        if(!empty($this->request->data)) {
            $data = $this->request->data;
            $refine = $this->RjTruck->processRefine($data);
            $params = $this->RjTruck->generateSearchURL($refine);
            $params = $this->MkCommon->getRefineGroupBranch($params, $data);
            $result = $this->MkCommon->processFilter($data);

            $params = array_merge($params, $result);
            $params['action'] = $index;

            $this->redirect($params);
        }
        $this->redirect('/');
    }

	public function index() {
        $this->loadModel('Laka');

        $conditions = array();
        $contain = array(
            'Driver'
        );
        
        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['nopol'])){
                $nopol = urldecode($refine['nopol']);
                $this->request->data['Truck']['nopol'] = $nopol;
                $typeTruck = !empty($refine['type'])?$refine['type']:1;
                $this->request->data['Truck']['type'] = $typeTruck;

                if( $typeTruck == 2 ) {
                    $conditionsNopol = array(
                        'Truck.id' => $nopol,
                    );
                } else {
                    $conditionsNopol = array(
                        'Truck.nopol LIKE' => '%'.$nopol.'%',
                    );
                }
                
                $truckSearch = $this->Truck->getData('list', array(
                    'conditions' => $conditionsNopol,
                    'fields' => array(
                        'Truck.id', 'Truck.id',
                    ),
                ));
                $conditions['Truck.id'] = $truckSearch;
            }
            if(!empty($refine['name'])){
                $data = urldecode($refine['name']);
                $conditions['CASE WHEN Driver.alias = \'\' THEN Driver.name ELSE CONCAT(Driver.name, \' ( \', Driver.alias, \' )\') END LIKE'] = '%'.$data.'%';
                $this->request->data['Driver']['name'] = $data;
            }
            if(!empty($refine['status'])){
                $data = urldecode($refine['status']);
                $truck_ongoing = array();

                if( in_array($data, array( 'laka', 'away', 'available' )) ) {
                    $truck_ongoing = $this->Truck->Ttuj->_callTtujOngoing();
                    
                    $contain[] = 'Laka';
                }

                switch ($data) {
                    case 'sold':
                        $conditions['Truck.sold'] = 1;
                        break;

                    case 'laka':
                        $conditions['Truck.sold'] = 0;
                        $conditions['Laka.id <>'] = NULL;
                        break;

                    case 'away':
                        $conditions['Truck.sold'] = 0;
                        $conditions['Laka.id'] = NULL;
                        $conditions['Truck.id'] = $truck_ongoing;
                        break;
                    
                    case 'available':
                        $conditions['Truck.sold'] = 0;
                        $conditions['Laka.id'] = NULL;
                        $conditions['Truck.id NOT'] = $truck_ongoing;
                        break;
                }
                $this->request->data['Truck']['status'] = $data;
            }
            if(!empty($refine['capacity'])){
                $data = urldecode($refine['capacity']);
                $conditions['Truck.capacity LIKE'] = '%'.$data.'%';
                $this->request->data['Truck']['capacity'] = $data;
            }
            if(!empty($refine['category'])){
                $data = urldecode($refine['category']);
                $conditions['TruckCategory.name LIKE'] = '%'.$data.'%';
                $this->request->data['Truck']['category'] = $data;
                $contain[] = 'TruckCategory';
            }
        }

        $this->paginate = $this->Truck->getData('paginate', array(
            'conditions' => $conditions,
            'contain' => $contain,
        ));
        $trucks = $this->paginate('Truck');

        if(!empty($trucks)){
            // $this->loadModel('City');

            foreach ($trucks as $key => $value) {
                $id = $this->MkCommon->filterEmptyField($value, 'Truck', 'id');
                // $branch_id = $this->MkCommon->filterEmptyField($value, 'Truck', 'branch_id');
                $truck_category_id = $this->MkCommon->filterEmptyField($value, 'Truck', 'truck_category_id');
                $truck_brand_id = $this->MkCommon->filterEmptyField($value, 'Truck', 'truck_brand_id');
                $company_id = $this->MkCommon->filterEmptyField($value, 'Truck', 'company_id');

                // $value = $this->City->getMerge($value, $branch_id);
                $value = $this->Truck->TruckCategory->getMerge($value, $truck_category_id);
                $value = $this->Truck->TruckBrand->getMerge($value, $truck_brand_id);
                $value = $this->Truck->Company->getMerge($value, $company_id);
                $value = $this->Laka->getMerge($id, $value);
                $value = $this->Truck->Ttuj->getTruckStatus($value, $id);

                $trucks[$key] = $value;
            }
        }

        $this->set('active_menu', 'trucks');
        $this->set('sub_module_title', __('Data Truk'));
        $this->set(compact(
            'trucks'
        ));
	}

    function detail($id = false){
        if(!empty($id)){
            $this->loadModel('TruckCustomer');
            $this->loadModel('TruckPerlengkapan');
            $this->loadModel('LeasingDetail');

            $truck = $this->Truck->getTruck($id, array(
                // 'branch' => false,
            ));

            if(!empty($truck)){
                $branch_id = $this->MkCommon->filterEmptyField($truck, 'Truck', 'branch_id');
                // $this->MkCommon->allowPage($branch_id);

                $truck = $this->GroupBranch->Branch->getMerge($truck, $branch_id);
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
                            'Vendor'
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
        $this->set('sub_module_title', __('Tambah Truk'));
        $this->doTruck();
    }

    function edit($id){
        $this->set('sub_module_title', 'Rubah truk');
        $truck = $this->Truck->getData('first', array(
            'conditions' => array(
                'Truck.id' => $id
            ),
        ), true, array(
            // 'branch' => false,
        ));

        if(!empty($truck)){
            // $branch_id = $this->MkCommon->filterEmptyField($truck, 'Truck', 'branch_id');
            // $this->MkCommon->allowPage($branch_id);

            // $truck = $this->Truck->Asset->getMerge($truck, $id, 'Asset.truck_id');
            $truck = $this->Truck->TruckCustomer->getMergeTruckCustomer($truck);
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
        $this->loadModel('Driver');

        $driverConditions = array(
            'Truck.id' => NULL,
        );
        $allowEditAsset = $this->MkCommon->checkAllowFunction($this->params);

        if(!empty($this->request->data)){
            $data = $this->request->data;

            if($id && $data_local){
                $this->Truck->id = $id;
                $msg = 'merubah';
                // $assetValidate = true;
            }else{
                $this->loadModel('Truck');
                $this->Truck->create();
                $msg = 'menambah';
                $data_local = $this->Driver->getGenerateDate($data_local);

                // $data = $this->MkCommon->dataConverter($data, array(
                //     'price' => array(
                //         'Truck' => array(
                //             'nilai_perolehan',
                //             'ak_penyusutan',
                //         ),
                //     ),
                //     'date' => array(
                //         'Truck' => array(
                //             'purchase_date',
                //         ),
                //     ),
                // ));

                // $assetData = $this->RjTruck->_callBeforeSave($data);
                // $assetValidate = $this->Truck->Asset->_callBeforeSave($assetData, true);
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

            if( !empty($id) ) {
                $data = $this->MkCommon->unsetArr($data, array(
                    'Truck' => array(
                        'nopol',
                        'branch_id',
                        'truck_category_id',
                        'truck_facility_id',
                        'driver_id',
                        'capacity',
                    ),
                ));
            } else {
                $data['Truck']['branch_id'] = Configure::read('__Site.config_branch_id');
            }

            $this->Truck->set($data);
            $check_alokasi = false;
            $allowSaveTruckCustomer = (!empty($id) && !empty($data_local['TruckCustomer']))?true:false;

            if( !empty($allowSaveTruckCustomer) ) {
                $check_alokasi = true;
            } else if( !empty($data['TruckCustomer']['customer_id']) ){
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

                    // if( !empty($assetData) ) {
                    //     $assetData['Asset']['truck_id'] = $truck_id;
                    //     $this->Truck->Asset->_callBeforeSave($assetData);
                    // }
                    
                    if( empty($allowSaveTruckCustomer) ) {
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
                                $data_customer[$key]['TruckCustomer']['branch_id'] = Configure::read('__Site.config_branch_id');
                            }
                        }
                        $this->Truck->TruckCustomer->saveMany($data_customer);
                        /*End Alokasi*/
                    }

                    $this->params['old_data'] = $data_local;
                    $this->params['data'] = $data;

                    $this->Log->logActivity( sprintf(__('Sukses %s truk #%s'), $msg, $truck_id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $truck_id );
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s truk'), $msg), 'success');
                    $this->redirect(array(
                        'controller' => 'trucks',
                        'action' => 'perlengkapan',
                        $this->Truck->id
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s truk'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s truk #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id ); 
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

        $this->loadModel('Customer');
        $this->loadModel('City');

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
            ),
        ), true, array(
            'branch' => false,
        ));
        $branches = $this->City->branchCities();
        $customers = $this->Customer->getData('list', array(
            'fields' => array(
                'Customer.id', 'Customer.customer_name_code'
            ),
        ), true, array(
            'branch' => false,
            'plant' => false,
        ));
        // $assetGroups = $this->Truck->Asset->AssetGroup->getData('list', array(
        //     'fields' => array(
        //         'AssetGroup.id', 'AssetGroup.group_name',
        //     ),
        // ));

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
            'truck_facilities', 'data_local', 'id',
            'branches', 'allowEditAsset'
        ));
        $this->render('truck_form');
    }

    function toggle($id){
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

            if($this->Truck->save()){
                $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
                $this->Log->logActivity( sprintf(__('Sukses merubah status Truk ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal merubah status Truk ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
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
                    $id = $this->TruckBrand->id;
                    $this->params['old_data'] = $data_local;
                    $this->params['data'] = $data;

                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Merek Truk'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s Merek Truk #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
                    $this->redirect(array(
                        'controller' => 'trucks',
                        'action' => 'brands'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Merek Truk'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s Merek Truk #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id ); 
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
                $this->Log->logActivity( sprintf(__('Sukses merubah status merek truk ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal merubah status merek truk ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
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
                    $id = $this->TruckCategory->id;
                    $this->params['old_data'] = $data_local;
                    $this->params['data'] = $data;

                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Jenis Truk'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s Jenis Truk #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
                    $this->redirect(array(
                        'controller' => 'trucks',
                        'action' => 'categories'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Jenis Truk'), $msg), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal %s Jenis Truk #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
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
                $this->Log->logActivity( sprintf(__('Sukses merubah status Jenis Truk ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal merubah status Jenis Truk ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Jenis Truk tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    function drivers(){
        $this->loadModel('Driver');

        $conditions = array();

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['name'])){
                $name = urldecode($refine['name']);
                $this->request->data['Driver']['name'] = $name;
                $conditions['Driver.name LIKE '] = '%'.$name.'%';
            }

            if(!empty($refine['no_id'])){
                $value = urldecode($refine['no_id']);
                $this->request->data['Driver']['no_id'] = $value;
                $conditions['Driver.no_id LIKE '] = '%'.$value.'%';
            }
        }

        $this->paginate = $this->Driver->getData('paginate', array(
            'conditions' => $conditions,
            'order' => array(
                'Driver.status' => 'DESC',
                'Driver.name' => 'ASC',
            ),
        ), true, array(
            'status' => 'all',
        ));
        $truck_drivers = $this->paginate('Driver');

        $this->set('active_menu', 'drivers');
        $this->set('sub_module_title', __('Supir Truk'));
        $this->set(compact(
            'truck_drivers'
        ));
    }

    function driver_add(){
        $this->loadModel('Driver');
        $this->set('sub_module_title', 'Tambah Supir Truk');
        $this->doDriver();
    }

    function driver_edit($id){
        $this->loadModel('Driver');
        $this->set('sub_module_title', 'Rubah Supir Truk');
        $driver = $this->Driver->getData('first', array(
            'conditions' => array(
                'Driver.id' => $id,
            ),
        ), true, array(
            'status' => 'all',
            // 'branch' => false,
        ));

        if(!empty($driver)){
            // $branch_id = $this->MkCommon->filterEmptyField($driver, 'Driver', 'branch_id');
            // $this->MkCommon->allowPage($branch_id);
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

            $data['Driver']['phone'] = (!empty($data['Driver']['phone'])) ? Sanitize::paranoid($data['Driver']['phone']) : '';
            $data['Driver']['phone_2'] = (!empty($data['Driver']['phone_2'])) ? Sanitize::paranoid($data['Driver']['phone_2']) : '';
            $data['Driver']['expired_date_sim'] = (!empty($data['Driver']['expired_date_sim'])) ? $this->MkCommon->getDate($data['Driver']['expired_date_sim']) : '';
            $data['Driver']['birth_date'] = $this->MkCommon->getDateSelectbox($data['Driver']['tgl_lahir']);
            $data['Driver']['join_date'] = $this->MkCommon->getDateSelectbox($data['Driver']['tgl_penerimaan']);
            $data['Driver']['expired_date_sim'] = $this->MkCommon->getDateSelectbox($data['Driver']['tgl_expire_sim']);
            $data['Driver']['branch_id'] = Configure::read('__Site.config_branch_id');
            
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
                $data['Driver']['no_id'] = $this->MkCommon->filterEmptyField($data_local, 'Driver', 'no_id');
            }else{
                $this->loadModel('Driver');

                $data['Driver']['no_id'] = $this->Driver->generateNoId();
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
                    $id = $this->Driver->id;
                    $text = sprintf(__('Sukses %s Supir Truk'), $msg);
                    if(!empty($data['Driver']['is_resign'])){
                        $text .= ' dan mengubah status supir menjadi resign.';
                    }

                    $this->params['old_data'] = $data_local;
                    $this->params['data'] = $data;

                    $this->MkCommon->setCustomFlash($text, 'success');
                    $this->Log->logActivity( $text, $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
                    $this->redirect(array(
                        'controller' => 'trucks',
                        'action' => 'drivers'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Supir Truk'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s Supir Truk #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
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

        $this->loadModel('DriverRelation');
        $this->loadModel('JenisSim');

        $driverRelations = $this->DriverRelation->find('list', array(
            'conditions' => array(
                'DriverRelation.status' => 1
            ),
            'fields' => array(
                'DriverRelation.id', 'DriverRelation.name'
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
            'driverRelations', 'jenisSims', 'id'
        ));
        $this->render('driver_form');
    }

    function driver_toggle($id){
        $this->loadModel('Driver');

        $locale = $this->Driver->getData('first', array(
            'conditions' => array(
                'Driver.id' => $id,
            ),
        ), true, array(
            'status' => 'all',
            // 'branch' => false,
        ));

        if( !empty($locale) ){
            $value = true;
            // $branch_id = $this->MkCommon->filterEmptyField($locale, 'Driver', 'branch_id');            
            // $this->MkCommon->allowPage($branch_id);

            if( !empty($locale['Driver']['status']) ){
                $value = false;
            }

            $this->Driver->id = $id;
            $this->Driver->set('status', $value);

            if($this->Driver->save()){
                $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
                $this->Log->logActivity( sprintf(__('Sukses merubah status Supir Truk ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal merubah status Supir Truk ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Supir Truk tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    function kir($id = false){
        $this->loadModel('Kir');
        $conditions = array();

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['nopol'])){
                $name = urldecode($refine['nopol']);
                $this->request->data['Truck']['nopol'] = $name;
                $typeTruck = !empty($refine['type'])?$refine['type']:1;
                $this->request->data['Truck']['type'] = $typeTruck;

                if( $typeTruck == 2 ) {
                    $conditionsNopol = array(
                        'Truck.id' => $name,
                    );
                } else {
                    $conditionsNopol = array(
                        'Truck.nopol LIKE' => '%'.$name.'%',
                    );
                }

                $truckSearch = $this->Kir->Truck->getData('list', array(
                    'conditions' => $conditionsNopol,
                    'fields' => array(
                        'Truck.id', 'Truck.id',
                    ),
                ));
                $conditions['Kir.truck_id'] = $truckSearch;
            }
        }
        $this->paginate = $this->Kir->getData('paginate', array(
            'conditions' => $conditions,
            'order'=> array(
                'Kir.status' => 'DESC',
                'Kir.rejected' => 'ASC',
                'Kir.status_paid' => 'ASC',
                'Kir.tgl_kir' => 'DESC',
            ),
        ), true, array(
            'status' => 'all',
        ));
        $kir = $this->paginate('Kir');
        
        $this->set('active_menu', 'kir');
        $sub_module_title = __('KIR');
        $this->set(compact(
            'kir', 'sub_module_title'
        ));
    }

    function kir_add(){
        $this->loadModel('Kir');
        $this->set('active_menu', 'kir');
        $this->set('sub_module_title', 'Tambah KIR');
        $this->doKir();
    }

    function kir_edit($id){
        $this->loadModel('Kir');
        $this->set('sub_module_title', 'Rubah KIR Truk');
        $kir = $this->Kir->getData('first', array(
            'conditions' => array(
                'Kir.id' => $id,
            ),
        ), true, array(
            'status' => 'all',
            // 'branch' => false,
        ));

        if(!empty($kir)){
            // $branch_id = $this->MkCommon->filterEmptyField($kir, 'Kir', 'branch_id');
            // $this->MkCommon->allowPage($branch_id);
            $this->MkCommon->getLogs($this->paramController, array( 'kir_edit', 'kir_add' ), $id);

            $this->doKir($id, $kir);
            $this->set(compact(
                'truck', 'kir'
            ));
        }else{
            $this->MkCommon->setCustomFlash(__('KIR Truk tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'trucks',
                'action' => 'kir'
            ));
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
            $data['Kir']['truck_id'] = !empty($truck['Truck']['id'])?$truck['Truck']['id']:false;
            $data['Kir']['no_pol'] = !empty($truck['Truck']['nopol'])?$truck['Truck']['nopol']:false;
            $data['Kir']['tgl_kir'] = (!empty($data['Kir']['tgl_kir'])) ? $this->MkCommon->getDate($data['Kir']['tgl_kir']) :  date('Y-m-d');
            $data['Kir']['from_date'] = (!empty($data['Kir']['from_date'])) ? $this->MkCommon->getDate($data['Kir']['from_date']) : false;
            $data['Kir']['to_date'] = (!empty($data['Kir']['to_date'])) ? $this->MkCommon->getDate($data['Kir']['to_date']) :  false;
            $data['Kir']['price_estimate'] = !empty($truck['Truck']['kir'])?$this->MkCommon->convertPriceToString($truck['Truck']['kir']):0;
            $data['Kir']['price'] = !empty($data['Kir']['price'])?$this->MkCommon->convertPriceToString($data['Kir']['price']):0;
            $data['Kir']['denda'] = (!empty($data['Kir']['denda'])) ? $this->MkCommon->convertPriceToString($data['Kir']['denda']) : 0;
            $data['Kir']['biaya_lain'] = (!empty($data['Kir']['biaya_lain'])) ? $this->MkCommon->convertPriceToString($data['Kir']['biaya_lain']) : 0;
            $data['Kir']['branch_id'] = Configure::read('__Site.config_branch_id');
            $this->Kir->set($data);

            if( $this->Kir->validates($data) ){
                if( $this->Kir->save($data) ){
                    $id = $this->Kir->id;

                    $this->params['old_data'] = $kir;
                    $this->params['data'] = $data;

                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s KIR Truk'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s KIR Truk #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
                    $this->redirect(array(
                        'controller' => 'trucks',
                        'action' => 'kir'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s KIR Truk'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s KIR Truk #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
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
            'fields' => array(
                'Truck.id', 'Truck.nopol'
            ),
        ));

        $this->MkCommon->_layout_file('select');
        $this->set('active_menu', 'kir');
        $this->set(compact(
            'trucks', 'truck', 'kir'
        ));
        $this->render('kir_form');
    }

    function kir_payments(){
        $this->loadModel('KirPayment');
        
        $dateFrom = date('Y-m-d', strtotime('-1 Month'));
        $dateTo = date('Y-m-d');

        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));
        $options =  $this->KirPayment->_callRefineParams($params, array(
            'limit' => Configure::read('__Site.config_pagination'),
        ));
        $this->paginate = $this->KirPayment->getData('paginate', $options, true, array(
            'status' => 'all',
        ));

        $kirPayments = $this->paginate('KirPayment');

        $this->set('active_menu', 'kir_payments');
        $sub_module_title = __('Pembayaran KIR');
        $this->set(compact('kirPayments', 'sub_module_title'));
    }

    function kir_payment_add( $kir_id = false ){
        $this->loadModel('KirPayment');
        $this->loadModel('Kir');
        $kir = false;
        
        if( !empty($kir_id) ) {
            $kir = $this->Kir->getData('first', array(
                'conditions' => array(
                    'Kir.rejected' => 0,
                    'Kir.paid <>' => 'full',
                    'Kir.id' => $kir_id,
                ),
            ));
        }

        $this->doKirPayment($kir_id, $kir);
        $kirs = $this->Kir->getData('list', array(
            'conditions' => array(
                'Kir.paid <>' => 'full',
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
    }

    public function kir_detail( $id = false ) {
        $this->loadModel('KirPayment');
        $kir = $this->KirPayment->getData('first', array(
            'conditions' => array(
                'KirPayment.id' => $id,
            ),
        ), true, array(
            'status' => 'all',
        ));

        if( !empty($kir) ) {
            $coa_id = $this->MkCommon->filterEmptyField($kir, 'KirPayment', 'coa_id');
            $kir = $this->KirPayment->Coa->getMerge( $kir, $coa_id );

            $this->MkCommon->getLogs($this->paramController, array( 'kir_payment_add', 'kir_payment_delete', 'kir_payment_rejected', 'kir_payment_void' ), $id);
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
                $data = $this->request->data;
                $kir_id = $this->MkCommon->filterEmptyField($data, 'KirPayment', 'kir_id');
                $coa_id = $this->MkCommon->filterEmptyField($data, 'KirPayment', 'coa_id');
                $nopol = $this->MkCommon->filterEmptyField($kir, 'Kir', 'no_pol');
                $truck_id = $this->MkCommon->filterEmptyField($kir, 'Kir', 'truck_id');

                $this->KirPayment->create();

                $logActionName = false;
                $default_msg = 'dibayar';

                $data['KirPayment']['user_id'] = $this->user_id;
                $data['KirPayment']['kir_id'] = $kir_id;
                $data['KirPayment']['kir_payment_date'] = $kir_payment_date = (!empty($data['KirPayment']['kir_payment_date'])) ? $this->MkCommon->getDate($data['KirPayment']['kir_payment_date']) : '';
                $data['Truck']['tgl_kir'] = (!empty($kir['Kir']['to_date'])) ? $this->MkCommon->getDate($kir['Kir']['to_date']) : '';

                if( !empty($data['KirPayment']['rejected']) ) {
                    $data['Kir']['rejected'] = 1;
                    $logActionName = 'kir_payment_rejected';
                    $default_msg = 'ditolak';
                } else {
                    $data['Kir']['paid'] = 1;
                }

                $data['KirPayment']['biaya_perpanjang'] = $this->MkCommon->convertPriceToString($kir['Kir']['price']);
                $data['KirPayment']['denda'] = $this->MkCommon->convertPriceToString($kir['Kir']['denda']);
                $data['KirPayment']['biaya_lain'] = $this->MkCommon->convertPriceToString($kir['Kir']['biaya_lain']);
                $data['KirPayment']['total_pembayaran'] = intval($data['KirPayment']['biaya_perpanjang']) + intval($data['KirPayment']['denda']) + intval($data['KirPayment']['biaya_lain']);
                $data['KirPayment']['branch_id'] = Configure::read('__Site.config_branch_id');

                $this->KirPayment->set($data);
                $this->Truck->set($data);
                $this->Truck->id = $kir['Kir']['truck_id'];
                $this->Kir->set($data);
                $this->Kir->id = $kir['Kir']['id'];

                if( $this->KirPayment->validates($data) && $this->Truck->validates($data) && $this->Kir->validates($data) ){
                    if( $this->KirPayment->save($data) && $this->Truck->save($data) && $this->Kir->save($data) ){
                        $id = $document_id = $this->KirPayment->id;
                        $document_no = str_pad($this->KirPayment->id, 5, '0', STR_PAD_LEFT);

                        if( !empty($data['Kir']['paid']) && !empty($data['KirPayment']['total_pembayaran']) ) {
                            $total = $data['KirPayment']['total_pembayaran'];
                            $title = sprintf(__('Pembayaran KIR Truk %s'), $nopol);
                            $title = $this->MkCommon->filterEmptyField($data, 'KirPayment', 'note', $title);

                            $this->User->Journal->setJournal($total, array(
                                'credit' => $coa_id,
                                'debit' => 'kir_payment_coa_id',
                            ), array(
                                'date' => $kir_payment_date,
                                'document_id' => $id,
                                'truck_id' => $truck_id,
                                'nopol' => $nopol,
                                'title' => $title,
                                'document_no' => $document_no,
                                'type' => 'kir',
                            ));

                            if( !empty($data['KirPayment']['biaya_lain']) ) {
                                $this->User->Journal->setJournal($data['KirPayment']['biaya_lain'], array(
                                    'credit' => $coa_id,
                                    'debit' => 'document_other_payment_coa_id',
                                ), array(
                                    'date' => $kir_payment_date,
                                    'document_id' => $id,
                                    'truck_id' => $truck_id,
                                    'nopol' => $nopol,
                                    'title' => $title,
                                    'document_no' => $document_no,
                                    'type' => 'kir',
                                ));
                            }

                            if( !empty($data['KirPayment']['denda']) ) {
                                $this->User->Journal->setJournal($data['KirPayment']['denda'], array(
                                    'credit' => $coa_id,
                                    'debit' => 'document_denda_payment_coa_id',
                                ), array(
                                    'date' => $kir_payment_date,
                                    'document_id' => $id,
                                    'truck_id' => $truck_id,
                                    'nopol' => $nopol,
                                    'title' => $title,
                                    'document_no' => $document_no,
                                    'type' => 'kir',
                                ));
                            }
                        }

                        $this->params['old_data'] = $kir;
                        $this->params['data'] = $data;

                        $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
                        $this->MkCommon->setCustomFlash(sprintf(__('KIR Truk %s telah %s #%s'), $kir['Kir']['no_pol'], $default_msg, $noref), 'success');
                        $this->Log->logActivity( sprintf(__('KIR Truk %s telah %s #%s'), $kir['Kir']['no_pol'], $default_msg, $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id, $logActionName );
                        $this->redirect(array(
                            'controller' => 'trucks',
                            'action' => 'kir_payments'
                        ));
                    } else {
                        $this->MkCommon->setCustomFlash(sprintf(__('Gagal membayar KIR Truk %s'), $kir['Kir']['no_pol']), 'error'); 
                        $this->Log->logActivity( sprintf(__('Gagal membayar KIR Truk %s'), $kir['Kir']['no_pol']), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
                    }
                } else {
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal membayar KIR Truk %s'), $kir['Kir']['no_pol']), 'error');  
                }
            } else {
                $this->MkCommon->setCustomFlash(__('Mohon pilih No. Pol Truk'), 'error');
            }
        }

        if( !empty($kir) ) {
            $this->request->data['KirPayment']['kir_id'] = $id;
            $this->request->data['KirPayment']['tgl_kir'] = $this->MkCommon->customDate($kir['Kir']['tgl_kir'], 'd/m/Y', '');
            $this->request->data['KirPayment']['from_date'] = !empty($kir['Kir']['from_date'])?date('d/m/Y', strtotime($kir['Kir']['from_date'])):false;
            $this->request->data['KirPayment']['to_date'] = !empty($kir['Kir']['to_date'])?date('d/m/Y', strtotime($kir['Kir']['to_date'])):false;
            $this->request->data['KirPayment']['price'] = $kir['Kir']['price'];
            $this->request->data['KirPayment']['price_estimate'] = $this->MkCommon->convertPriceToString($kir['Kir']['price_estimate']);
            $this->request->data['KirPayment']['denda'] = $this->MkCommon->convertPriceToString($kir['Kir']['denda']);
            $this->request->data['KirPayment']['biaya_lain'] = $this->MkCommon->convertPriceToString($kir['Kir']['biaya_lain']);
            $this->request->data['Kir']['note'] = $this->MkCommon->filterEmptyField($kir, 'Kir', 'note');
        }

        $coas = $this->GroupBranch->Branch->BranchCoa->getCoas();

        $this->MkCommon->_layout_file('select');
        $this->set('active_menu', 'kir_payments');
        $this->set(compact(
            'id', 'kir', 'sub_module_title',
            'coas'
        ));
    }

    public function kir_delete( $id ) {
        $this->loadModel('Kir');
        $kir = $this->Kir->getData('first', array(
            'conditions' => array(
                'Kir.paid <>' => 'full',
                'Kir.rejected' => 0,
                'Kir.id' => $id,
            ),
        ), true, array(
            'status' => 'all',
            // 'branch' => false,
        ));

        if( !empty($kir) ) {
            // $branch_id = $this->MkCommon->filterEmptyField($kir, 'Kir', 'branch_id');            
            // $this->MkCommon->allowPage($branch_id);

            $this->Kir->id = $id;
            $this->Kir->set('status', 0);

            if($this->Kir->save()){
                $this->MkCommon->setCustomFlash(sprintf(__('KIR Truk %s telah berhasil dihapus'), $kir['Kir']['no_pol']), 'success');
                $this->Log->logActivity( sprintf(__('KIR Truk %s telah berhasil dihapus'), $kir['Kir']['no_pol']), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
            } else {
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal menghapus KIR Truk %s'), $kir['Kir']['no_pol']), 'error'); 
                $this->Log->logActivity( sprintf(__('Gagal menghapus KIR Truk %s'), $kir['Kir']['no_pol']), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
            }
        } else {
            $this->MkCommon->setCustomFlash(__('Data KIR tidak ditemukan'), 'error');
        }

        $this->redirect($this->referer());
    }

    public function kir_payment_delete( $id ) {
        $this->loadModel('KirPayment');

        $value = $this->KirPayment->getData('first', array(
            'conditions' => array(
                'KirPayment.id' => $id,
            ),
        ));

        if( !empty($value) ) {
            $total = $this->MkCommon->filterEmptyField($value, 'KirPayment', 'total_pembayaran');
            $coa_id = $this->MkCommon->filterEmptyField($value, 'KirPayment', 'coa_id');
            $kir_payment_date = $this->MkCommon->filterEmptyField($value, 'KirPayment', 'kir_payment_date');
            $denda = $this->MkCommon->filterEmptyField($value, 'KirPayment', 'denda');
            $biaya_lain = $this->MkCommon->filterEmptyField($value, 'KirPayment', 'biaya_lain');

            $nopol = $this->MkCommon->filterEmptyField($value, 'Kir', 'no_pol');
            $truck_id = $this->MkCommon->filterEmptyField($value, 'Kir', 'truck_id');
            $paid = $this->MkCommon->filterEmptyField($value, 'Kir', 'paid');
            $document_no = str_pad($id, 5, '0', STR_PAD_LEFT);

            $this->KirPayment->id = $id;
            $this->KirPayment->set('is_void', 1);
            $this->KirPayment->set('status', 0);

            if($this->KirPayment->save()){
                $this->KirPayment->Kir->id = $value['Kir']['id'];
                $this->KirPayment->Kir->set('paid', 0);
                $this->KirPayment->Kir->save();

                if(!empty($value['Kir']['from_date'])){
                    $this->Truck->id = $value['Kir']['truck_id'];
                    $this->Truck->set('tgl_kir', $value['Kir']['from_date']);
                    $this->Truck->save();
                }

                if( !empty($paid) ) {
                    $title = sprintf(__('pembayaran KIR Truk %s'), $nopol);
                    $title = sprintf(__('<i>Pembatalan</i> %s'), $this->MkCommon->filterEmptyField($value, 'KirPayment', 'note', $title));

                    $this->User->Journal->setJournal($total, array(
                        'credit' => 'kir_payment_coa_id',
                        'debit' => $coa_id,
                    ), array(
                        'date' => $kir_payment_date,
                        'document_id' => $id,
                        'truck_id' => $truck_id,
                        'nopol' => $nopol,
                        'title' => $title,
                        'document_no' => $document_no,
                        'type' => 'kir_void',
                    ));

                    if( !empty($biaya_lain) ) {
                        $this->User->Journal->setJournal($biaya_lain, array(
                            'credit' => 'document_other_payment_coa_id',
                            'debit' => $coa_id,
                        ), array(
                            'date' => $kir_payment_date,
                            'document_id' => $id,
                            'truck_id' => $truck_id,
                            'nopol' => $nopol,
                            'title' => $title,
                            'document_no' => $document_no,
                            'type' => 'kir_void',
                        ));
                    }

                    if( !empty($denda) ) {
                        $this->User->Journal->setJournal($denda, array(
                            'credit' => 'document_denda_payment_coa_id',
                            'debit' => $coa_id,
                        ), array(
                            'date' => $kir_payment_date,
                            'document_id' => $id,
                            'truck_id' => $truck_id,
                            'nopol' => $nopol,
                            'title' => $title,
                            'document_no' => $document_no,
                            'type' => 'kir_void',
                        ));
                    }
                }

                $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
                $this->MkCommon->setCustomFlash(sprintf(__('Pembayaran KIR Truk %s telah berhasil dibatalkan #%s'), $value['Kir']['no_pol'], $noref), 'success');
                $this->Log->logActivity( sprintf(__('Pembayaran KIR Truk %s telah berhasil dibatalkan'), $value['Kir']['no_pol']), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id, 'kir_payment_void' );
            } else {
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal membatalkan pembayaran KIR Truk %s'), $value['Kir']['no_pol']), 'error'); 
                $this->Log->logActivity( sprintf(__('Gagal membatalkan pembayaran KIR Truk %s'), $value['Kir']['no_pol']), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
            }
        } else {
            $this->MkCommon->setCustomFlash(__('Data pembayaran KIR tidak ditemukan'), 'error');
        }

        $this->redirect($this->referer());
    }

    function siup($id = false){
        $this->loadModel('Siup');
        $conditions = array();

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['nopol'])){
                $name = urldecode($refine['nopol']);
                $this->request->data['Truck']['nopol'] = $name;
                $typeTruck = !empty($refine['type'])?$refine['type']:1;
                $this->request->data['Truck']['type'] = $typeTruck;

                if( $typeTruck == 2 ) {
                    $conditionsNopol = array(
                        'Truck.id' => $name,
                    );
                } else {
                    $conditionsNopol = array(
                        'Truck.nopol LIKE' => '%'.$name.'%',
                    );
                }

                $truckSearch = $this->Siup->Truck->getData('list', array(
                    'conditions' => $conditionsNopol,
                    'fields' => array(
                        'Truck.id', 'Truck.id',
                    ),
                ));
                $conditions['Siup.truck_id'] = $truckSearch;
            }
        }
        $this->paginate = $this->Siup->getData('paginate', array(
            'conditions' => $conditions,
            'order'=> array(
                'Siup.status' => 'DESC',
                'Siup.id' => 'DESC',
                'Siup.tgl_kir' => 'DESC',
            ),
        ), true, array(
            'status' => 'all',
        ));
        $siup = $this->paginate('Siup');

        $this->set('active_menu', 'siup');
        $sub_module_title = __('Ijin Usaha');
        $this->set(compact('siup', 'sub_module_title'));
    }

    function siup_add(){
        $this->loadModel('Siup');
        $this->set('active_menu', 'siup');
        $this->set('sub_module_title', 'Tambah Ijin Usaha');
        $this->doSiup();
    }

    function siup_edit($id){
        $this->loadModel('Siup');
        $this->set('sub_module_title', 'Rubah Ijin Usaha Truk');
        $siup = $this->Siup->getData('first', array(
            'conditions' => array(
                'Siup.id' => $id,
            )
        ), true, array(
            'status' => 'all',
        ));

        if(!empty($siup)){
            $this->MkCommon->getLogs($this->paramController, array( 'siup_edit', 'siup_add' ), $id);
            $this->doSiup($id, $siup);
        }else{
            $this->MkCommon->setCustomFlash(__('Ijin Usaha Truk tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'trucks',
                'action' => 'siup'
            ));
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
            $data['Siup']['truck_id'] = !empty($truck['Truck']['id'])?$truck['Truck']['id']:false;
            $data['Siup']['no_pol'] = !empty($truck['Truck']['nopol'])?$truck['Truck']['nopol']:false;
            $data['Siup']['tgl_siup'] = (!empty($data['Siup']['tgl_siup'])) ? $this->MkCommon->getDate($data['Siup']['tgl_siup']) : '';
            $data['Siup']['from_date'] = (!empty($data['Siup']['from_date'])) ? $this->MkCommon->getDate($data['Siup']['from_date']) : '';
            $data['Siup']['to_date'] = (!empty($data['Siup']['to_date'])) ? $this->MkCommon->getDate($data['Siup']['to_date']) : '';
            $data['Siup']['price_estimate'] = !empty($truck['Truck']['siup'])?$this->MkCommon->convertPriceToString($truck['Truck']['siup']):false;
            $data['Siup']['price'] = !empty($data['Siup']['price'])?$this->MkCommon->convertPriceToString($data['Siup']['price']):false;
            $data['Siup']['denda'] = (!empty($data['Siup']['denda'])) ? $this->MkCommon->convertPriceToString($data['Siup']['denda']) : 0;
            $data['Siup']['biaya_lain'] = (!empty($data['Siup']['biaya_lain'])) ? $this->MkCommon->convertPriceToString($data['Siup']['biaya_lain']) : 0;
            $data['Siup']['branch_id'] = Configure::read('__Site.config_branch_id');
            $this->Siup->set($data);

            if( $this->Siup->validates($data) ){
                if( $this->Siup->save($data) ){
                    $id = $this->Siup->id;

                    $this->params['old_data'] = $siup;
                    $this->params['data'] = $data;

                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Ijin Usaha Truk'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s Ijin Usaha Truk #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
                    $this->redirect(array(
                        'controller' => 'trucks',
                        'action' => 'siup'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Ijin Usaha Truk'), $msg), 'error');  
                    $this->Log->logActivity( sprintf(__('Gagal %s Ijin Usaha Truk #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
                }
            }else{
                $text = sprintf(__('Gagal %s Ijin Usaha Truk'), $msg);
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
            'fields' => array(
                'Truck.id', 'Truck.nopol'
            )
        ));

        $this->MkCommon->_layout_file('select');
        $this->set('active_menu', 'siup');
        $this->set(compact(
            'truck_id', 'sub_module_title', 'trucks',
            'truck', 'siup'
        ));
        $this->render('siup_form');
    }

    function siup_payments(){
        $this->loadModel('SiupPayment');
        
        $dateFrom = date('Y-m-d', strtotime('-1 Month'));
        $dateTo = date('Y-m-d');

        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));
        $options =  $this->SiupPayment->_callRefineParams($params, array(
            'limit' => Configure::read('__Site.config_pagination'),
        ));
        $this->paginate = $this->SiupPayment->getData('paginate', $options, true, array(
            'status' => 'all',
        ));
        $siupPayments = $this->paginate('SiupPayment');

        $this->set('active_menu', 'siup_payments');
        $sub_module_title = __('Pembayaran Ijin Usaha');
        $this->set(compact('siupPayments', 'sub_module_title'));
    }

    function siup_payment_add( $siup_id = false ){
        $this->loadModel('SiupPayment');
        $this->loadModel('Siup');
        $siup = false;
        
        if( !empty($siup_id) ) {
            $siup = $this->Siup->getData('first', array(
                'conditions' => array(
                    'Siup.rejected' => 0,
                    'Siup.paid <>' => 'full',
                    'Siup.id' => $siup_id,
                ),
            ));
        }

        $this->doSiupPayment($siup_id, $siup);
        $siups = $this->Siup->getData('list', array(
            'conditions' => array(
                'Siup.paid <>' => 'full',
                'Siup.rejected' => 0,
            ),
            'fields' => array(
                'Siup.id', 'Siup.no_pol'
            )
        ));

        $sub_module_title = __('Pembayaran Ijin Usaha');
        $this->set(compact(
            'siups', 'sub_module_title'
        ));
        $this->render('siup_payment_form');
    }

    public function siup_detail( $id = false ) {
        $this->loadModel('SiupPayment');
        $siup = $this->SiupPayment->getData('first', array(
            'conditions' => array(
                'SiupPayment.id' => $id,
            ),
        ), true, array(
            'status' => 'all',
        ));

        if( !empty($siup) ) {
            $coa_id = $this->MkCommon->filterEmptyField($siup, 'SiupPayment', 'coa_id');
            $siup = $this->SiupPayment->Coa->getMerge( $siup, $coa_id );

            $this->MkCommon->getLogs($this->paramController, array( 'siup_payment_add', 'siup_payment_delete', 'siup_payment_rejected', 'siup_payment_void' ), $id);
            $this->doSiupPayment($id, $siup);
            $this->set('sub_module_title', __('Detail Pembayaran Ijin Usaha'));
        } else {
            $this->MkCommon->setCustomFlash(__('Data Pembayaran Ijin Usaha tidak ditemukan'), 'error');
            $this->redirect($this->referer());
        }
    }

    public function doSiupPayment( $id = false, $siup = false ) {
        if(!empty($this->request->data)){
            if( !empty($this->request->data['SiupPayment']['siup_id']) && !empty($siup) ){
                $data = $this->request->data;
                $siup_id = $this->MkCommon->filterEmptyField($data, 'SiupPayment', 'siup_id');
                $coa_id = $this->MkCommon->filterEmptyField($data, 'SiupPayment', 'coa_id');
                $nopol = $this->MkCommon->filterEmptyField($siup, 'Siup', 'no_pol');
                $truck_id = $this->MkCommon->filterEmptyField($siup, 'Siup', 'truck_id');

                $this->SiupPayment->create();

                $logActionName = false;
                $default_msg = 'dibayar';

                $data['SiupPayment']['user_id'] = $this->user_id;
                $data['SiupPayment']['siup_id'] = $siup_id;
                $data['SiupPayment']['siup_payment_date'] = $siup_payment_date = (!empty($data['SiupPayment']['siup_payment_date'])) ? $this->MkCommon->getDate($data['SiupPayment']['siup_payment_date']) : '';

                $data['Truck']['tgl_siup'] = (!empty($siup['Siup']['to_date'])) ? $this->MkCommon->getDate($siup['Siup']['to_date']) : '';

                if( !empty($data['SiupPayment']['rejected']) ) {
                    $data['Siup']['rejected'] = 1;
                    $logActionName = 'siup_payment_rejected';
                    $default_msg = 'ditolak';
                } else {
                    $data['Siup']['paid'] = 1;
                }

                $data['SiupPayment']['biaya_perpanjang'] = $this->MkCommon->convertPriceToString($siup['Siup']['price']);
                $data['SiupPayment']['denda'] = $this->MkCommon->convertPriceToString($siup['Siup']['denda']);
                $data['SiupPayment']['biaya_lain'] = $this->MkCommon->convertPriceToString($siup['Siup']['biaya_lain']);
                $data['SiupPayment']['total_pembayaran'] = intval($data['SiupPayment']['biaya_perpanjang']) + intval($data['SiupPayment']['denda']) + intval($data['SiupPayment']['biaya_lain']);
                $data['SiupPayment']['branch_id'] = Configure::read('__Site.config_branch_id');

                $this->SiupPayment->set($data);
                $this->Truck->set($data);
                $this->Siup->set($data);
                $this->Truck->id = $siup['Siup']['truck_id'];
                $this->Siup->id = $siup['Siup']['id'];

                if( $this->SiupPayment->validates($data) && $this->Truck->validates($data) && $this->Siup->validates($data) ){
                    if( $this->SiupPayment->save($data) && $this->Truck->save($data) && $this->Siup->save($data) ){
                        $id = $document_id = $this->SiupPayment->id;
                        $document_no = str_pad($this->SiupPayment->id, 5, '0', STR_PAD_LEFT);
                        
                        if( !empty($data['Siup']['paid']) && !empty($data['SiupPayment']['total_pembayaran']) ) {
                            $total = $data['SiupPayment']['total_pembayaran'];
                            $title = sprintf(__('Pembayaran ijin usaha Truk %s'), $nopol);
                            $title = $this->MkCommon->filterEmptyField($data, 'SiupPayment', 'note', $title);

                            $this->User->Journal->setJournal($total, array(
                                'credit' => $coa_id,
                                'debit' => 'siup_payment_coa_id',
                            ), array(
                                'date' => $siup_payment_date,
                                'document_id' => $id,
                                'truck_id' => $truck_id,
                                'nopol' => $nopol,
                                'title' => $title,
                                'document_no' => $document_no,
                                'type' => 'siup',
                            ));

                            if( !empty($data['SiupPayment']['biaya_lain']) ) {
                                $this->User->Journal->setJournal($data['SiupPayment']['biaya_lain'], array(
                                    'credit' => $coa_id,
                                    'debit' => 'document_other_payment_coa_id',
                                ), array(
                                    'date' => $siup_payment_date,
                                    'document_id' => $id,
                                    'truck_id' => $truck_id,
                                    'nopol' => $nopol,
                                    'title' => $title,
                                    'document_no' => $document_no,
                                    'type' => 'siup',
                                ));
                            }

                            if( !empty($data['SiupPayment']['denda']) ) {
                                $this->User->Journal->setJournal($data['SiupPayment']['denda'], array(
                                    'credit' => $coa_id,
                                    'debit' => 'document_denda_payment_coa_id',
                                ), array(
                                    'date' => $siup_payment_date,
                                    'document_id' => $id,
                                    'truck_id' => $truck_id,
                                    'nopol' => $nopol,
                                    'title' => $title,
                                    'document_no' => $document_no,
                                    'type' => 'siup',
                                ));
                            }
                        }

                        $this->params['old_data'] = $siup;
                        $this->params['data'] = $data;

                        $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
                        $this->MkCommon->setCustomFlash(sprintf(__('Ijin Usaha Truk %s telah %s #%s'), $siup['Siup']['no_pol'], $default_msg, $noref), 'success');
                        $this->Log->logActivity( sprintf(__('Ijin Usaha Truk %s telah %s #%s'), $siup['Siup']['no_pol'], $default_msg, $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id, $logActionName );
                        $this->redirect(array(
                            'controller' => 'trucks',
                            'action' => 'siup_payments'
                        ));
                    } else {
                        $this->MkCommon->setCustomFlash(sprintf(__('Gagal membayar Ijin Usaha Truk %s'), $siup['Siup']['no_pol']), 'error');  
                        $this->Log->logActivity( sprintf(__('Gagal membayar Ijin Usaha Truk %s'), $siup['Siup']['no_pol']), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
                    }
                } else {
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal membayar Ijin Usaha Truk %s'), $siup['Siup']['no_pol']), 'error');  
                }
            } else {
                $this->MkCommon->setCustomFlash(__('Mohon pilih No. Pol Truk'), 'error');
            }
        }
        if( !empty($siup) ) {
            $this->request->data['SiupPayment']['siup_id'] = $id;
            $this->request->data['SiupPayment']['tgl_siup'] = $this->MkCommon->customDate($siup['Siup']['tgl_siup'], 'd/m/Y', '');
            $this->request->data['SiupPayment']['from_date'] = !empty($siup['Siup']['from_date'])?date('d/m/Y', strtotime($siup['Siup']['from_date'])):false;
            $this->request->data['SiupPayment']['to_date'] = !empty($siup['Siup']['to_date'])?date('d/m/Y', strtotime($siup['Siup']['to_date'])):false;
            $this->request->data['SiupPayment']['price'] = $siup['Siup']['price'];
            $this->request->data['SiupPayment']['price_estimate'] = $this->MkCommon->convertPriceToString($siup['Siup']['price_estimate']);
            $this->request->data['SiupPayment']['denda'] = $this->MkCommon->convertPriceToString($siup['Siup']['denda']);
            $this->request->data['SiupPayment']['biaya_lain'] = $this->MkCommon->convertPriceToString($siup['Siup']['biaya_lain']);
            $this->request->data['Siup']['note'] = $siup['Siup']['note'];
        }

        $coas = $this->GroupBranch->Branch->BranchCoa->getCoas();

        $this->MkCommon->_layout_file('select');
        $this->set('active_menu', 'siup_payments');
        $this->set(compact(
            'id', 'siup', 'sub_module_title',
            'coas'
        ));
    }

    public function siup_delete( $id ) {
        $this->loadModel('Siup');
        $siup = $this->Siup->getData('first', array(
            'conditions' => array(
                'Siup.paid <>' => 'full',
                'Siup.rejected' => 0,
                'Siup.id' => $id,
            ),
        ));

        if( !empty($siup) ) {
            $this->Siup->id = $id;
            $this->Siup->set('status', 0);

            if($this->Siup->save()){
                $this->MkCommon->setCustomFlash(sprintf(__('Ijin Usaha Truk %s telah berhasil dihapus'), $siup['Siup']['no_pol']), 'success');
                $this->Log->logActivity( sprintf(__('Ijin Usaha Truk %s telah berhasil dihapus'), $siup['Siup']['no_pol']), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
            } else {
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal menghapus Ijin Usaha Truk %s'), $siup['Siup']['no_pol']), 'error');  
                $this->Log->logActivity( sprintf(__('Gagal menghapus Ijin Usaha Truk %s'), $siup['Siup']['no_pol']), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
            }
        } else {
            $this->MkCommon->setCustomFlash(__('Data Ijin Usaha tidak ditemukan'), 'error');
        }

        $this->redirect($this->referer());
    }

    public function siup_payment_delete( $id ) {
        $this->loadModel('SiupPayment');

        $value = $this->SiupPayment->getData('first', array(
            'conditions' => array(
                'SiupPayment.id' => $id,
            ),
        ));

        if( !empty($value) ) {
            $total = $this->MkCommon->filterEmptyField($value, 'SiupPayment', 'total_pembayaran');
            $coa_id = $this->MkCommon->filterEmptyField($value, 'SiupPayment', 'coa_id');
            $siup_payment_date = $this->MkCommon->filterEmptyField($value, 'SiupPayment', 'siup_payment_date');
            $denda = $this->MkCommon->filterEmptyField($value, 'SiupPayment', 'denda');
            $biaya_lain = $this->MkCommon->filterEmptyField($value, 'SiupPayment', 'biaya_lain');

            $nopol = $this->MkCommon->filterEmptyField($value, 'Siup', 'no_pol');
            $truck_id = $this->MkCommon->filterEmptyField($value, 'Siup', 'truck_id');
            $paid = $this->MkCommon->filterEmptyField($value, 'Siup', 'paid');
            $document_no = str_pad($id, 5, '0', STR_PAD_LEFT);

            $this->SiupPayment->id = $id;
            $this->SiupPayment->set('is_void', 1);
            $this->SiupPayment->set('status', 0);

            if($this->SiupPayment->save()){
                $this->SiupPayment->Siup->id = $value['Siup']['id'];
                $this->SiupPayment->Siup->set('paid', 0);
                $this->SiupPayment->Siup->save();

                if(!empty($value['Siup']['from_date'])){
                    $this->Truck->id = $value['Siup']['truck_id'];
                    $this->Truck->set('tgl_siup', $value['Siup']['from_date']);
                    $this->Truck->save();
                }

                if( !empty($paid) ) {
                    $title = sprintf(__('pembayaran ijin usaha Truk %s'), $nopol);
                    $title = sprintf(__('<i>Pembatalan</i> %s'), $this->MkCommon->filterEmptyField($value, 'SiupPayment', 'note', $title));

                    $this->User->Journal->setJournal($total, array(
                        'credit' => 'siup_payment_coa_id',
                        'debit' => $coa_id,
                    ), array(
                        'date' => $siup_payment_date,
                        'document_id' => $id,
                        'truck_id' => $truck_id,
                        'nopol' => $nopol,
                        'title' => $title,
                        'document_no' => $document_no,
                        'type' => 'siup_void',
                    ));

                    if( !empty($biaya_lain) ) {
                        $this->User->Journal->setJournal($biaya_lain, array(
                            'credit' => 'document_other_payment_coa_id',
                            'debit' => $coa_id,
                        ), array(
                            'date' => $siup_payment_date,
                            'document_id' => $id,
                            'truck_id' => $truck_id,
                            'nopol' => $nopol,
                            'title' => $title,
                            'document_no' => $document_no,
                            'type' => 'siup_void',
                        ));
                    }

                    if( !empty($denda) ) {
                        $this->User->Journal->setJournal($denda, array(
                            'credit' => 'document_denda_payment_coa_id',
                            'debit' => $coa_id,
                        ), array(
                            'date' => $siup_payment_date,
                            'document_id' => $id,
                            'truck_id' => $truck_id,
                            'nopol' => $nopol,
                            'title' => $title,
                            'document_no' => $document_no,
                            'type' => 'siup_void',
                        ));
                    }
                }

                $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
                $this->MkCommon->setCustomFlash(sprintf(__('Pembayaran Ijin usaha Truk %s telah berhasil dibatalkan #%s'), $value['Siup']['no_pol'], $noref), 'success');
                $this->Log->logActivity( sprintf(__('Pembayaran Ijin usaha Truk %s telah berhasil dibatalkan'), $value['Siup']['no_pol']), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id, 'siup_payment_void' );
            } else {
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal membatalkan pembayaran Ijin usaha Truk %s'), $value['Siup']['no_pol']), 'error'); 
                $this->Log->logActivity( sprintf(__('Gagal membatalkan pembayaran Ijin usaha Truk %s'), $value['Siup']['no_pol']), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
            }
        } else {
            $this->MkCommon->setCustomFlash(__('Data pembayaran Ijin usaha tidak ditemukan'), 'error');
        }

        $this->redirect($this->referer());
    }

    function alocations($id = false){
        if(!empty($id)){
            $truck = $this->Truck->getTruck($id, array(
                // 'branch' => false,
            ));

            if(!empty($truck)){
                // $branch_id = $this->MkCommon->filterEmptyField($truck, 'Truck', 'branch_id');
                // $this->MkCommon->allowPage($branch_id);

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
                    $id = $this->TruckAlocation->id;
                    $this->params['old_data'] = $data_local;
                    $this->params['data'] = $data;

                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s alokasi Truk'), $msg), 'success');
                    $this->Log->logActivity(sprintf(__('Sukses %s alokasi Truk #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
                    $this->redirect(array(
                        'controller' => 'trucks',
                        'action' => 'alocations',
                        $truck_id
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s alokasi Truk'), $msg), 'error');  
                    $this->Log->logActivity(sprintf(__('Gagal %s alokasi Truk #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
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
                    $id = $this->Direction->id;
                    $this->params['old_data'] = $data_local;
                    $this->params['data'] = $data;

                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Rute Truk'), $msg), 'success');
                    $this->Log->logActivity(sprintf(__('Sukses %s Rute Truk #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
                    $this->redirect(array(
                        'controller' => 'trucks',
                        'action' => 'directions'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Rute Truk'), $msg), 'error');  
                    $this->Log->logActivity(sprintf(__('Gagal %s Rute Truk #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
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
                $this->Log->logActivity(sprintf(__('Sukses merubah status  rute ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                $this->Log->logActivity(sprintf(__('Gagal merubah status  rute ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
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
                    $this->Log->logActivity(sprintf(__('Sukses %s rincian bahan bakar'), $msg), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
                    $this->redirect(array(
                        'controller' => 'trucks',
                        'action' => 'gas_edit'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s rincian bahan bakar'), $msg), 'error');  
                    $this->Log->logActivity(sprintf(__('Gagal %s rincian bahan bakar'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
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
            $truck = $this->Truck->getTruck($truck_id, array(
                // 'branch' => false,
            ));

            if(!empty($truck)){
                $this->loadModel('TruckPerlengkapan');
                $this->loadModel('City');

                $branch_id = $this->MkCommon->filterEmptyField($truck, 'Truck', 'branch_id');
                // $this->MkCommon->allowPage($branch_id);
                
                // $truck = $this->Truck->Asset->getMerge($truck, $truck_id, 'Asset.truck_id');
                $truck = $this->GroupBranch->Branch->getMerge($truck, $branch_id);
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
                                $this->Log->logActivity(sprintf(__('kelengkapan truk berhasil %s #%s'), $message, $this->TruckPerlengkapan->id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $truck_id );
                                $this->redirect(array(
                                    'controller' => 'trucks',
                                    'action' => 'index'
                                ));
                            } else {
                                $this->MkCommon->setCustomFlash(sprintf(__('kelengkapan truk gagal %s'), $message), 'error');
                                $this->Log->logActivity(sprintf(__('kelengkapan truk gagal %s #%s'), $message, $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $truck_id ); 
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
        $this->loadModel('Stnk');
        $conditions = array();

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['nopol'])){
                $name = urldecode($refine['nopol']);
                $this->request->data['Truck']['nopol'] = $name;
                $typeTruck = !empty($refine['type'])?$refine['type']:1;
                $this->request->data['Truck']['type'] = $typeTruck;

                if( $typeTruck == 2 ) {
                    $conditionsNopol = array(
                        'Truck.id' => $name,
                    );
                } else {
                    $conditionsNopol = array(
                        'Truck.nopol LIKE' => '%'.$name.'%',
                    );
                }

                $truckSearch = $this->Stnk->Truck->getData('list', array(
                    'conditions' => $conditionsNopol,
                    'fields' => array(
                        'Truck.id', 'Truck.id',
                    ),
                ));
                $conditions['Stnk.truck_id'] = $truckSearch;
            }
        }
        $this->paginate = $this->Stnk->getData('paginate', array(
            'conditions' => $conditions,
            'order'=> array(
                'Stnk.status' => 'DESC',
                'Stnk.id' => 'DESC',
                'Stnk.tgl_stnk' => 'DESC',
            ),
        ), true, array(
            'status' => 'all',
        ));
        $stnks = $this->paginate('Stnk');

        $this->set('active_menu', 'stnk');
        $sub_module_title = __('STNK');
        $this->set(compact('stnks', 'sub_module_title'));
    }

    function stnk_add(){
        $this->loadModel('Stnk');
        $this->set('active_menu', 'stnk');
        $this->set('sub_module_title', 'Tambah STNK');
        $this->doStnk();
    }

    function stnk_edit($id){
        $this->loadModel('Stnk');
        $this->set('sub_module_title', 'Rubah Perpanjang STNK');
        $Stnk = $this->Stnk->getData('first', array(
            'conditions' => array(
                'Stnk.id' => $id,
            ),
            'contain' => array(
                'Truck'
            ),
        ), true, array(
            'status' => 'all',
        ));

        if(!empty($Stnk)){
            $this->MkCommon->getLogs($this->paramController, array( 'stnk_edit', 'stnk_add' ), $id);
            $this->doStnk($id, $Stnk);
        }else{
            $this->MkCommon->setCustomFlash(__('STNK Truk tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'trucks',
                'action' => 'stnk'
            ));
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
                        'Truck.id' => $name,
                    ),
                ));

                if( !empty($truck) ) {
                    $this->request->data['Stnk']['from_date'] = $this->MkCommon->customDate($truck['Truck']['tgl_stnk'], 'd/m/Y', '');
                    $this->request->data['Stnk']['plat_from_date'] = $this->MkCommon->customDate($truck['Truck']['tgl_stnk_plat'], 'd/m/Y', '');
                    $this->request->data['Stnk']['price_estimate'] = $this->MkCommon->convertPriceToString($truck['Truck']['bbnkb']+$truck['Truck']['pkb'], 0);

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
            $data['Stnk']['truck_id'] = !empty($truck['Truck']['id'])?$truck['Truck']['id']:false;
            $data['Stnk']['no_pol'] = !empty($truck['Truck']['nopol'])?$truck['Truck']['nopol']:false;
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

            $data['Stnk']['price_estimate'] = !empty($truck['Truck'])?$this->MkCommon->convertPriceToString($truck['Truck']['bbnkb']+$truck['Truck']['pkb'], 0):0;
            $data['Stnk']['price'] = $this->MkCommon->convertPriceToString($data['Stnk']['price']);
            $data['Stnk']['denda'] = (!empty($data['Stnk']['denda'])) ? $this->MkCommon->convertPriceToString($data['Stnk']['denda']) : 0;
            $data['Stnk']['biaya_lain'] = (!empty($data['Stnk']['biaya_lain'])) ? $this->MkCommon->convertPriceToString($data['Stnk']['biaya_lain']) : 0;
            $data['Stnk']['branch_id'] = Configure::read('__Site.config_branch_id');

            if( $this->Stnk->validates($data) ){
                if( $this->Stnk->save($data) ){
                    $id = $this->Stnk->id;

                    $this->params['old_data'] = $stnk;
                    $this->params['data'] = $data;

                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s STNK Truk'), $msg), 'success');
                    $this->Log->logActivity(sprintf(__('Sukses %s STNK Truk #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id ); 
                    $this->redirect(array(
                        'controller' => 'trucks',
                        'action' => 'stnk'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s STNK Truk'), $msg), 'error');
                    $this->Log->logActivity(sprintf(__('Gagal %s STNK Truk #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
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
            'fields' => array(
                'Truck.id', 'Truck.nopol'
            )
        ));

        $sub_module_title = __('Perpanjang STNK');
        $this->MkCommon->_layout_file('select');
        $this->set('active_menu', 'stnk');
        $this->set(compact(
            'truck_id', 'sub_module_title', 'trucks',
            'stnk'
        ));
        $this->render('stnk_form');
    }

    public function stnk_delete( $id ) {
        $this->loadModel('Stnk');
        $stnk = $this->Stnk->getData('first', array(
            'conditions' => array(
                'Stnk.paid <>' => 'full',
                'Stnk.rejected' => 0,
                'Stnk.id' => $id,
            ),
        ));

        if( !empty($stnk) && empty($stnk['Stnk']['paid']) && empty($stnk['Stnk']['rejected']) ) {
            $this->Stnk->id = $id;
            $this->Stnk->set('status', 0);

            if($this->Stnk->save()){
                $this->MkCommon->setCustomFlash(sprintf(__('STNK Truk %s telah berhasil dihapus'), $stnk['Stnk']['no_pol']), 'success');
                $this->Log->logActivity(sprintf(__('STNK Truk %s telah berhasil dihapus #%s'), $stnk['Stnk']['no_pol'], $this->Stnk->id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id, 'stnk_payment_void' );
            } else {
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal menghapus STNK Truk %s'), $stnk['Stnk']['no_pol']), 'error');  
                $this->Log->logActivity(sprintf(__('Gagal menghapus STNK Truk %s #%s'), $stnk['Stnk']['no_pol'], $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
            }
        } else {
            $this->MkCommon->setCustomFlash(__('Data STNK tidak ditemukan'), 'error');
        }

        $this->redirect($this->referer());
    }

    public function stnk_payment_delete( $id ) {
        $this->loadModel('StnkPayment');

        $value = $this->StnkPayment->getData('first', array(
            'conditions' => array(
                'StnkPayment.id' => $id,
            ),
        ));
        $additionalTitle = '';

        if( !empty($value) ) {
            $total = $this->MkCommon->filterEmptyField($value, 'StnkPayment', 'total_pembayaran');
            $coa_id = $this->MkCommon->filterEmptyField($value, 'StnkPayment', 'coa_id');
            $stnk_payment_date = $this->MkCommon->filterEmptyField($value, 'StnkPayment', 'stnk_payment_date');
            $denda = $this->MkCommon->filterEmptyField($value, 'StnkPayment', 'denda');
            $biaya_lain = $this->MkCommon->filterEmptyField($value, 'StnkPayment', 'biaya_lain');

            $nopol = $this->MkCommon->filterEmptyField($value, 'Stnk', 'no_pol');
            $truck_id = $this->MkCommon->filterEmptyField($value, 'Stnk', 'truck_id');
            $is_change_plat = $this->MkCommon->filterEmptyField($value, 'Stnk', 'is_change_plat');
            $paid = $this->MkCommon->filterEmptyField($value, 'Stnk', 'paid');
            $document_no = str_pad($id, 5, '0', STR_PAD_LEFT);

            if( !empty($is_change_plat) ) {
                $additionalTitle = 'dan ganti plat ';
            }

            $this->StnkPayment->id = $id;
            $this->StnkPayment->set('is_void', 1);
            $this->StnkPayment->set('status', 0);

            if($this->StnkPayment->save()){
                $this->StnkPayment->Stnk->id = $value['Stnk']['id'];
                $this->StnkPayment->Stnk->set('paid', 0);
                $this->StnkPayment->Stnk->save();

                if(!empty($value['Stnk']['from_date'])){
                    $this->Truck->id = $value['Stnk']['truck_id'];
                    $this->Truck->set('tgl_stnk', $value['Stnk']['from_date']);
                    $this->Truck->save();
                }

                if(!empty($value['Stnk']['plat_from_date']) && $value['Stnk']['plat_from_date'] != '0000-00-00' && !empty($value['Stnk']['is_change_plat'])){
                    $this->Truck->id = $value['Stnk']['truck_id'];
                    $this->Truck->set('tgl_stnk_plat', $value['Stnk']['plat_from_date']);
                    $this->Truck->save();
                }

                if( !empty($paid) ) {
                    $title = sprintf(__('pembayaran STNK %sTruk %s'), $additionalTitle, $nopol);
                    $title = sprintf(__('<i>Pembatalan</i> %s'), $this->MkCommon->filterEmptyField($value, 'StnkPayment', 'note', $title));

                    $this->User->Journal->setJournal($total, array(
                        'credit' => 'stnk_payment_coa_id',
                        'debit' => $coa_id,
                    ), array(
                        'date' => $stnk_payment_date,
                        'document_id' => $id,
                        'truck_id' => $truck_id,
                        'nopol' => $nopol,
                        'title' => $title,
                        'document_no' => $document_no,
                        'type' => 'stnk_void',
                    ));

                    if( !empty($biaya_lain) ) {
                        $this->User->Journal->setJournal($biaya_lain, array(
                            'credit' => 'document_other_payment_coa_id',
                            'debit' => $coa_id,
                        ), array(
                            'date' => $stnk_payment_date,
                            'document_id' => $id,
                            'truck_id' => $truck_id,
                            'nopol' => $nopol,
                            'title' => $title,
                            'document_no' => $document_no,
                            'type' => 'stnk_void',
                        ));
                    }

                    if( !empty($denda) ) {
                        $this->User->Journal->setJournal($denda, array(
                            'credit' => 'document_denda_payment_coa_id',
                            'debit' => $coa_id,
                        ), array(
                            'date' => $stnk_payment_date,
                            'document_id' => $id,
                            'truck_id' => $truck_id,
                            'nopol' => $nopol,
                            'title' => $title,
                            'document_no' => $document_no,
                            'type' => 'stnk_void',
                        ));
                    }
                }

                $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
                $this->MkCommon->setCustomFlash(sprintf(__('Pembayaran STNK Truk %s telah berhasil dibatalkan #%s'), $value['Stnk']['no_pol'], $noref), 'success');
                $this->Log->logActivity( sprintf(__('Pembayaran STNK Truk %s telah berhasil dibatalkan'), $value['Stnk']['no_pol']), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id, 'stnk_payment_void' );
            } else {
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal membatalkan pembayaran STNK Truk %s'), $value['Stnk']['no_pol']), 'error'); 
                $this->Log->logActivity( sprintf(__('Gagal membatalkan pembayaran STNK Truk %s'), $value['Stnk']['no_pol']), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
            }
        } else {
            $this->MkCommon->setCustomFlash(__('Data pembayaran STNK tidak ditemukan'), 'error');
        }

        $this->redirect($this->referer());
    }

    function stnk_payments(){
        $this->loadModel('StnkPayment');
        
        $dateFrom = date('Y-m-d', strtotime('-1 Month'));
        $dateTo = date('Y-m-d');

        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));
        $options =  $this->StnkPayment->_callRefineParams($params, array(
            'limit' => Configure::read('__Site.config_pagination'),
        ));
        
        $this->paginate = $this->StnkPayment->getData('paginate', $options, true, array(
            'status' => 'all',
        ));

        $stnkPayments = $this->paginate('StnkPayment');

        $this->set('active_menu', 'stnk_payments');
        $sub_module_title = __('Pembayaran STNK');
        $this->set(compact('stnkPayments', 'sub_module_title'));
    }

    function stnk_payment_add( $stnk_id = false ){
        $this->loadModel('StnkPayment');
        $this->loadModel('Stnk');
        $stnk = false;
        
        if( !empty($stnk_id) ) {
            $stnk = $this->Stnk->getData('first', array(
                'conditions' => array(
                    'Stnk.rejected' => 0,
                    'Stnk.paid <>' => 'full',
                    'Stnk.id' => $stnk_id,
                ),
            ));
        }

        $this->doStnkPayment($stnk_id, $stnk);
        $stnks = $this->Stnk->getData('list', array(
            'conditions' => array(
                'Stnk.paid <>' => 'full',
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
    }

    public function stnk_detail( $id = false ) {
        $this->loadModel('StnkPayment');
        $stnk = $this->StnkPayment->getData('first', array(
            'conditions' => array(
                'StnkPayment.id' => $id,
            ),
        ), true, array(
            'status' => 'all',
        ));

        if( !empty($stnk) ) {
            $coa_id = $this->MkCommon->filterEmptyField($stnk, 'StnkPayment', 'coa_id');
            $stnk = $this->StnkPayment->Coa->getMerge( $stnk, $coa_id );

            $this->MkCommon->getLogs($this->paramController, array( 'stnk_payment_add', 'stnk_payment_delete', 'stnk_payment_rejected', 'stnk_payment_void' ), $id);
            $this->doStnkPayment($id, $stnk);
            $this->set('sub_module_title', __('Detail Pembayaran STNK'));
        } else {
            $this->MkCommon->setCustomFlash(__('Data Pembayaran STNK tidak ditemukan'), 'error');
            $this->redirect($this->referer());
        }
    }

    public function doStnkPayment( $id = false, $stnk = false ) {
        if(!empty($this->request->data)){
            if( !empty($this->request->data['StnkPayment']['stnk_id']) && !empty($stnk) ){
                $data = $this->request->data;
                $stnk_id = $this->MkCommon->filterEmptyField($data, 'StnkPayment', 'stnk_id');
                $coa_id = $this->MkCommon->filterEmptyField($data, 'StnkPayment', 'coa_id');
                $nopol = $this->MkCommon->filterEmptyField($stnk, 'Stnk', 'no_pol');
                $truck_id = $this->MkCommon->filterEmptyField($stnk, 'Stnk', 'truck_id');

                $this->StnkPayment->create();

                $logActionName = false;
                $default_msg = 'dibayar';
                $additionalTitle = '';

                $data['StnkPayment']['user_id'] = $this->user_id;
                $data['StnkPayment']['stnk_id'] = $stnk_id;
                $data['StnkPayment']['stnk_payment_date'] = $stnk_payment_date = (!empty($data['StnkPayment']['stnk_payment_date'])) ? $this->MkCommon->getDate($data['StnkPayment']['stnk_payment_date']) : '';
                $data['Truck']['tgl_stnk'] = (!empty($stnk['Stnk']['to_date'])) ? $this->MkCommon->getDate($stnk['Stnk']['to_date']) : '';

                if( !empty($stnk['Stnk']['is_change_plat']) ) {
                    $data['Truck']['tgl_stnk_plat'] = (!empty($stnk['Stnk']['plat_to_date'])) ? $this->MkCommon->getDate($stnk['Stnk']['plat_to_date']) : '';
                    $additionalTitle = 'dan ganti plat ';
                }

                if( !empty($data['StnkPayment']['rejected']) ) {
                    $data['Stnk']['rejected'] = 1;
                    $logActionName = 'stnk_payment_rejected';
                    $default_msg = 'ditolak';
                } else {
                    $data['Stnk']['paid'] = 1;
                }

                $data['StnkPayment']['biaya_perpanjang'] = $this->MkCommon->convertPriceToString($stnk['Stnk']['price']);
                $data['StnkPayment']['denda'] = $this->MkCommon->convertPriceToString($stnk['Stnk']['denda']);
                $data['StnkPayment']['biaya_lain'] = $this->MkCommon->convertPriceToString($stnk['Stnk']['biaya_lain']);
                $data['StnkPayment']['total_pembayaran'] = intval($data['StnkPayment']['biaya_perpanjang']) + intval($data['StnkPayment']['denda']) + intval($data['StnkPayment']['biaya_lain']);
                $data['StnkPayment']['branch_id'] = Configure::read('__Site.config_branch_id');

                $this->StnkPayment->set($data);
                $this->Truck->set($data);
                $this->Stnk->set($data);
                $this->Truck->id = $stnk['Stnk']['truck_id'];
                $this->Stnk->id = $stnk['Stnk']['id'];

                if( $this->StnkPayment->validates($data) && $this->Truck->validates($data) && $this->Stnk->validates($data) ){
                    if( $this->StnkPayment->save($data) && $this->Truck->save($data) && $this->Stnk->save($data) ){
                        $id = $document_id = $this->StnkPayment->id;
                        $document_no = str_pad($this->StnkPayment->id, 5, '0', STR_PAD_LEFT);
                        
                        if( !empty($data['Stnk']['paid']) && !empty($data['StnkPayment']['total_pembayaran']) ) {
                            $total = $data['StnkPayment']['total_pembayaran'];
                            $title = sprintf(__('Pembayaran STNK %sTruk %s'), $additionalTitle, $nopol);
                            $title = $this->MkCommon->filterEmptyField($data, 'StnkPayment', 'note', $title);

                            $this->User->Journal->setJournal($total, array(
                                'credit' => $coa_id,
                                'debit' => 'stnk_payment_coa_id',
                            ), array(
                                'date' => $stnk_payment_date,
                                'document_id' => $id,
                                'truck_id' => $truck_id,
                                'nopol' => $nopol,
                                'title' => $title,
                                'document_no' => $document_no,
                                'type' => 'stnk',
                            ));

                            if( !empty($data['StnkPayment']['biaya_lain']) ) {
                                $this->User->Journal->setJournal($data['StnkPayment']['biaya_lain'], array(
                                    'credit' => $coa_id,
                                    'debit' => 'document_other_payment_coa_id',
                                ), array(
                                    'date' => $stnk_payment_date,
                                    'document_id' => $id,
                                    'truck_id' => $truck_id,
                                    'nopol' => $nopol,
                                    'title' => $title,
                                    'document_no' => $document_no,
                                    'type' => 'stnk',
                                ));
                            }

                            if( !empty($data['StnkPayment']['denda']) ) {
                                $this->User->Journal->setJournal($data['StnkPayment']['denda'], array(
                                    'credit' => $coa_id,
                                    'debit' => 'document_denda_payment_coa_id',
                                ), array(
                                    'date' => $stnk_payment_date,
                                    'document_id' => $id,
                                    'truck_id' => $truck_id,
                                    'nopol' => $nopol,
                                    'title' => $title,
                                    'document_no' => $document_no,
                                    'type' => 'stnk',
                                ));
                            }
                        }

                        $this->params['old_data'] = $stnk;
                        $this->params['data'] = $data;

                        $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
                        $this->MkCommon->setCustomFlash(sprintf(__('STNK Truk %s telah %s #%s'), $stnk['Stnk']['no_pol'], $default_msg, $noref), 'success');
                        $this->Log->logActivity(sprintf(__('STNK Truk %s telah %s #%s'), $stnk['Stnk']['no_pol'], $default_msg, $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id, $logActionName );
                        $this->redirect(array(
                            'controller' => 'trucks',
                            'action' => 'stnk_payments'
                        ));
                    } else {
                        $this->MkCommon->setCustomFlash(sprintf(__('Gagal membayar STNK Truk %s'), $stnk['Stnk']['no_pol']), 'error'); 
                        $this->Log->logActivity(sprintf(__('Gagal membayar STNK Truk %s #%s'), $stnk['Stnk']['no_pol'], $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
                    }
                } else {
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal membayar STNK Truk %s'), $stnk['Stnk']['no_pol']), 'error');  
                }
            } else {
                $this->MkCommon->setCustomFlash(__('Mohon pilih No. Pol Truk'), 'error');
            }
        }
        if( !empty($stnk) ) {
            $this->request->data['StnkPayment']['stnk_id'] = $id;
            $this->request->data['StnkPayment']['tgl_bayar'] = $this->MkCommon->customDate($stnk['Stnk']['tgl_bayar'], 'd/m/Y', '');
            $this->request->data['StnkPayment']['from_date'] = date('d/m/Y', strtotime($stnk['Stnk']['from_date']));
            $this->request->data['StnkPayment']['to_date'] = date('d/m/Y', strtotime($stnk['Stnk']['to_date']));
            $this->request->data['StnkPayment']['price'] = $stnk['Stnk']['price'];
            $this->request->data['StnkPayment']['price_estimate'] = $this->MkCommon->convertPriceToString($stnk['Stnk']['price_estimate']);
            $this->request->data['StnkPayment']['denda'] = $this->MkCommon->convertPriceToString($stnk['Stnk']['denda']);
            $this->request->data['StnkPayment']['biaya_lain'] = $this->MkCommon->convertPriceToString($stnk['Stnk']['biaya_lain']);
            $this->request->data['Stnk']['note'] = $stnk['Stnk']['note'];

            if( !empty($stnk['Stnk']['is_change_plat']) ) {
                $this->request->data['StnkPayment']['plat_from_date'] = date('d/m/Y', strtotime($stnk['Stnk']['plat_from_date']));
                $this->request->data['StnkPayment']['plat_to_date'] = date('d/m/Y', strtotime($stnk['Stnk']['plat_to_date']));
            }
        }

        $coas = $this->GroupBranch->Branch->BranchCoa->getCoas();

        $this->MkCommon->_layout_file('select');
        $this->set('active_menu', 'stnk_payments');
        $this->set(compact(
            'id', 'stnk', 'sub_module_title',
            'coas'
        ));
    }

    function reports($data_action = false) {
        $this->set('active_menu', 'reports');
        $this->set('sub_module_title', __('Laporan Truk'));
        $allow_branch_id = Configure::read('__Site.config_allow_branch_id');
        
        $defaul_condition = array(
            'Truck.branch_id' => $allow_branch_id,
        );
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
                $typeTruck = !empty($refine['type'])?$refine['type']:1;

                if( $typeTruck == 2 ) {
                    $conditionsNopol = array(
                        'Truck.id' => $data,
                    );
                } else {
                    $conditionsNopol = array(
                        'Truck.nopol LIKE' => '%'.$data.'%',
                    );
                }

                $truckSearch = $this->Truck->getData('list', array(
                    'conditions' => $conditionsNopol,
                    'fields' => array(
                        'Truck.id', 'Truck.id',
                    ),
                ), true, array(
                    'branch' => false,
                ));
                $defaul_condition['Truck.id'] = $truckSearch;
                $this->request->data['Truck']['nopol'] = $data;
                $this->request->data['Truck']['type'] = $typeTruck;
            }
            if(!empty($refine['name'])){
                $data = urldecode($refine['name']);
                $defaul_condition['CASE WHEN Driver.alias = \'\' THEN Driver.name ELSE CONCAT(Driver.name, \' ( \', Driver.alias, \' )\') END LIKE'] = '%'.$data.'%';
                $this->request->data['Driver']['name'] = $data;
            }
            if(!empty($refine['capacity'])){
                $data = urldecode($refine['capacity']);
                $defaul_condition['Truck.capacity LIKE'] = '%'.$data.'%';
                $this->request->data['Truck']['capacity'] = $data;
            }
            if(!empty($refine['category'])){
                $data = urldecode($refine['category']);
                $defaul_condition['TruckCategory.name LIKE'] = '%'.$data.'%';
                $this->request->data['Truck']['category'] = $data;
            }
            if(!empty($refine['year'])){
                $data = urldecode($refine['year']);
                $defaul_condition['Truck.tahun LIKE'] = '%'.$data.'%';
                $this->request->data['Truck']['year'] = $data;
            }
            if(!empty($refine['alokasi'])){
                $data = urldecode($refine['alokasi']);
                $defaul_condition['CustomerNoType.code LIKE'] = '%'.$data.'%';
                $this->request->data['Truck']['alokasi'] = $data;
            }
            if(!empty($refine['company'])){
                $data = urldecode($refine['company']);
                $defaul_condition['Truck.company_id'] = $data;
                $this->request->data['Truck']['company_id'] = $data;
            }

            // Custom Otorisasi
            $defaul_condition = $this->MkCommon->getConditionGroupBranch( $refine, 'Truck', $defaul_condition, 'conditions' );
        }

        if(!empty($from_date)){
            $defaul_condition['DATE_FORMAT(Truck.created, \'%Y-%m-%d\') >= '] = $from_date;
        }
        if(!empty($to_date)){
            $defaul_condition['DATE_FORMAT(Truck.created, \'%Y-%m-%d\') <= '] = $to_date;
        }

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
                ),
                'CustomerNoType' => array(
                    'className' => 'CustomerNoType',
                    'foreignKey' => false,
                    'conditions' => array(
                        'TruckCustomer.customer_id = CustomerNoType.id',
                    )
                )
            )
        ), false);

        $options = array(
            'conditions' => $defaul_condition,
            'contain' => array(
                'TruckCategory',
                'TruckCustomer',
                'CustomerNoType',
            ),
        );

        if(!empty($refine['sort'])){
            if( $refine['sort'] == 'TruckBrand.name' ) {
                $options['contain'][] = 'TruckBrand';
            }
        }
        if(!empty($refine['name'])){
            $options['contain'][] = 'Driver';
        }

        if( !empty($data_action) ) {
            $trucks = $this->Truck->getData('all', $options, true, array(
                'branch' => false,
            ));
        } else {
            $options['limit'] = 20;
            $options = $this->Truck->getData('paginate', $options, true, array(
                'branch' => false,
            ));
            $this->paginate = $options;
            $trucks = $this->paginate('Truck');
        }

        if( !empty($trucks) ) {
            foreach ($trucks as $key => $truck) {
                $branch_id = !empty($truck['Truck']['branch_id'])?$truck['Truck']['branch_id']:false;
                $driver_id = !empty($truck['Truck']['driver_id'])?$truck['Truck']['driver_id']:false;
                $truck_brand_id = !empty($truck['Truck']['truck_brand_id'])?$truck['Truck']['truck_brand_id']:false;
                $truck_facility_id = !empty($truck['Truck']['truck_facility_id'])?$truck['Truck']['truck_facility_id']:false;
                $company_id = $this->MkCommon->filterEmptyField($truck, 'Truck', 'company_id');

                $truck = $this->Truck->Driver->getMerge( $truck, $driver_id );
                $truck = $this->Truck->TruckBrand->getMerge($truck, $truck_brand_id);
                $truck = $this->Truck->TruckFacility->getMerge($truck, $truck_facility_id);
                $truck = $this->GroupBranch->Branch->getMerge($truck, $branch_id);
                $truck = $this->Truck->Company->getMerge($truck, $company_id);
                $trucks[$key] = $truck;
            }
        }

        $companies = $this->Truck->Company->getData('list');

        $this->set(compact(
            'trucks', 'from_date', 'to_date',
            'data_action', 'companies'
        ));

        if($data_action == 'pdf'){
            $this->layout = 'pdf';
        }else if($data_action == 'excel'){
            $this->layout = 'ajax';
        }
    }

    function facilities(){
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
    }

    function facility_add(){
        $this->loadModel('TruckFacility');
        $this->set('sub_module_title', 'Tambah Fasilitas Truk');
        $this->doFacility();
    }

    function facility_edit($id){
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
                    $id = $this->TruckFacility->id;
                    $this->params['old_data'] = $data_local;
                    $this->params['data'] = $data;

                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Fasilitas Truk'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s Fasilitas Truk #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
                    $this->redirect(array(
                        'controller' => 'trucks',
                        'action' => 'facilities'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Fasilitas Truk'), $msg), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal %s Fasilitas Truk #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
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
                $this->Log->logActivity( sprintf(__('Sukses merubah status Fasilitas Truk ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal merubah status Fasilitas Truk ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Fasilitas Truk tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    public function capacity_report( $data_action = false ) {
        $this->loadModel('TruckCustomer');
        $this->loadModel('Customer');
        $this->set('active_menu', 'capacity_report');
        $this->set('sub_module_title', __('Laporan Truk Per Kapasitas'));

        $allow_branch_id = Configure::read('__Site.config_allow_branch_id');
        $conditions = array(
            'Customer.branch_id' => $allow_branch_id,
        );

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['code'])){
                $value = urldecode($refine['code']);
                $this->request->data['Truck']['customer_code'] = $value;
                $conditions['Customer.code LIKE'] = '%'.$value.'%';
            }

            // Custom Otorisasi
            $conditions = $this->MkCommon->getConditionGroupBranch( $refine, 'Customer', $conditions, 'conditions' );
        }
        
        $options = array(
            'conditions' => $conditions,
        );

        if( !empty($data_action) ) {
            $customers = $this->Customer->getData('all', $options, true, array(
                'branch' => false,
            ));
        } else {
            $options['limit'] = 50;
            $options = $this->Customer->getData('paginate', $options, true, array(
                'branch' => false,
            ));
            $this->paginate = $options;
            $customers = $this->paginate('Customer');
        }

        $capacities = $this->Truck->getData('list', array(
            'conditions' => array(
                'Truck.branch_id' => $allow_branch_id,
            ),
            'group' => array(
                'Truck.capacity',
            ),
            'fields' => array(
                'Truck.id',
                'Truck.capacity',
            ),
            'order' => array(
                'Truck.capacity*1' => 'ASC',
            ),
        ));
        $truckArr = array();

        if( !empty($customers) ) {
            $customerArr = Set::extract('/Customer/id', $customers);
            $conditionsTruck = array(
                'Truck.status' => 1,
                'TruckCustomer.customer_id' => $customerArr,
                'TruckCustomer.primary' => 1,
            );

            if(!empty($this->params['named']['company'])){
                $value = urldecode($this->params['named']['company']);
                $conditionsTruck['Truck.company_id'] = $value;
                $this->request->data['Truck']['company_id'] = $value;
            }

            $trucks = $this->TruckCustomer->getData('all', array(
                'conditions' => $conditionsTruck,
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
            ), true, array(
                'branch' => false,
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

            foreach ($customers as $key => $value) {
                $branch_id = $this->MkCommon->filterEmptyField($value, 'Customer', 'branch_id');
                $value = $this->GroupBranch->Branch->getMerge($value, $branch_id);
                $customers[$key] = $value;
            }
        }

        if(empty($this->params['named']['company'])){
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
            $truckWithoutAlocations = $this->Truck->getData('all', array(
                'conditions' => array(
                    'TruckCustomer.id' => NULL,
                    'Truck.branch_id' => $allow_branch_id,
                ),
                'contain' => array(
                    'TruckCustomer',
                ),
                'group' => array(
                    'Truck.capacity',
                ),
                'fields' => array(
                    'Truck.id',
                    'Truck.capacity',
                    'COUNT(Truck.id) AS cnt',
                ),
            ));

            if( !empty($truckWithoutAlocations) ) {
                foreach ($truckWithoutAlocations as $key => $truck) {
                    if( !empty($truck[0]['cnt']) ) {
                        $customer_id = 0;
                        $capacity = $truck['Truck']['capacity'];
                        $truckArr[$customer_id][$capacity] = $truck[0]['cnt'];
                    }
                }
            }
        }
        
        $companies = $this->Truck->Company->getData('list');

        $this->set(compact(
            'data_action', 'customers', 'capacities',
            'truckArr', 'truckWithoutAlocations',
            'companies'
        ));

        if($data_action == 'pdf'){
            $this->layout = 'pdf';
        }else if($data_action == 'excel'){
            $this->layout = 'ajax';
        }
    }

    public function point_perday_report( $data_action = false ) {
        $this->loadModel('Ttuj');
        $this->loadModel('TtujTipeMotor');
        $this->loadModel('Customer');
        $this->loadModel('CustomerTargetUnitDetail');
        $this->set('active_menu', 'point_perday_report');
        $this->set('sub_module_title', __('Laporan Pencapaian Per Customer Per Hari'));

        $allow_branch_id = Configure::read('__Site.config_allow_branch_id');
        $conditions = array(
            'Customer.branch_id' => $allow_branch_id,
        );
        
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

            // Custom Otorisasi
            $conditions = $this->MkCommon->getConditionGroupBranch( $refine, 'Customer', $conditions, 'conditions' );
        }

        $currentMonth = !empty($currentMonth)?$currentMonth:date('Y-m');
        $lastDay = date('t', strtotime($currentMonth));
        $customers = $this->Customer->getData('all', array(
            'conditions' => $conditions,
            'order' => array(
                'Customer.order_sort' => 'ASC',
                'Customer.order' => 'ASC',
                'Customer.manual_group' => 'ASC',
                'Customer.customer_type_id' => 'DESC',
                'Customer.customer_group_id' => 'ASC',
            ),
        ), true, array(
            'branch' => false,
        ));

        if( !empty($data_action) ) {
            $options['limit'] = Configure::read('__Site.config_pagination_unlimited');
        } else {
            $options['limit'] = 20;
        }

        if( !empty($customers) ) {
            foreach ($customers as $key => $value) {
                $branch_id = $this->MkCommon->filterEmptyField($value, 'Customer', 'branch_id');
                $value = $this->GroupBranch->Branch->getMerge($value, $branch_id);
                
                $customer_group_id = $this->MkCommon->filterEmptyField($value, 'Customer', 'customer_group_id');
                $value = $this->Customer->CustomerGroup->getMerge($value, $customer_group_id);
                $customers[$key] = $value;
            }
        }

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
    }

    public function point_perplant_report( $data_type = 'depo', $data_action = false ) {
        $this->loadModel('City');
        $this->loadModel('Ttuj');
        $this->loadModel('TtujTipeMotor');
        $this->loadModel('Customer');
        $this->loadModel('CustomerTargetUnitDetail');
        $this->set('sub_module_title', __('Laporan Pencapaian Per Point Per Plant'));

        $allow_branch_id = Configure::read('__Site.config_allow_branch_id');
        $conditionsCustomer = array(
            'Customer.customer_type_id' => 2,
            'Customer.branch_id' => $allow_branch_id,
        );
        
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

            // Custom Otorisasi
            $conditionsCustomer = $this->MkCommon->getConditionGroupBranch( $refine, 'Customer', $conditionsCustomer, 'conditions' );
        }

        $currentMonth = !empty($currentMonth)?$currentMonth:date('Y-m');
        $lastDay = date('t', strtotime($currentMonth));

        if( $data_type == 'retail' ) {
            $conditionsCustomer['Customer.customer_type_id'] = 1;
        } else {
            $conditionsCustomer['Customer.customer_type_id'] = 2;
        }

        $options = array(
            'conditions' => $conditionsCustomer,
        );

        if( !empty($data_action) ) {
            $customers = $this->Customer->getData('all', $options, true, array(
                'branch' => false,
            ));
        } else {
            $options['limit'] = 50;
            $this->paginate = $this->Customer->getData('paginate', $options, true, array(
                'branch' => false,
            ));
            $customers = $this->paginate('Customer');
        }

        if( !empty($customers) ) {
            foreach ($customers as $key => $value) {
                $customer_group_id = $this->MkCommon->filterEmptyField($value, 'Customer', 'customer_group_id');
                $branch_id = $this->MkCommon->filterEmptyField($value, 'Customer', 'branch_id');

                $value = $this->GroupBranch->Branch->getMerge($value, $branch_id);
                $value = $this->Customer->CustomerGroup->getMerge($value, $customer_group_id);
                $customers[$key] = $value;
            }
        }
        
        $customerArr = Set::extract('/Customer/id', $customers);
        $group = array(
            'Ttuj.from_city_id',
            'Ttuj.customer_id',
        );

        if( $data_type == 'retail' ) {
            unset($group['Ttuj.from_city_id']);
            $this->set('active_menu', 'retail_point_perplant_report');
        } else {
            $this->set('active_menu', 'point_perplant_report');
        }

        $branch_plant_id = $this->GroupBranch->Branch->getData('list', array(
            'conditions' => array(
                'Branch.is_plant' => 1,
            ),
            'fields' => array(
                'Branch.id', 'Branch.id',
            ),
            'order'=> array(
                'Branch.code' => 'ASC'
            ),
        ));
        $ttujs = $this->TtujTipeMotor->getData('all', array(
            'conditions' => array(
                'TtujTipeMotor.status'=> 1,
                'Ttuj.status'=> 1,
                'Ttuj.is_draft'=> 0,
                'DATE_FORMAT(Ttuj.ttuj_date, \'%Y-%m\')' => $currentMonth,
                'Ttuj.customer_id' => $customerArr,
                'Ttuj.is_retail' => 0,
                'Ttuj.branch_id' => $branch_plant_id,
            ),
            'contain' => array(
                'Ttuj',
            ),
            'order' => array(
                'Ttuj.customer_name' => 'ASC', 
            ),
            'fields' => array(
                'Ttuj.id', 'Ttuj.branch_id',
                'Ttuj.customer_id', 'SUM(TtujTipeMotor.qty) cnt'
            ),
            'group' => $group,
        ), false);
        $dataTtuj = array();
        $targetUnit = array();
        $branches = array();
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
                $branch_id = $value['Ttuj']['branch_id'];

                if( empty($branches[$branch_id]) ) {
                    $value = $this->GroupBranch->Branch->getMerge( $value, $branch_id );

                    if( !empty($value['Branch']['code']) ) {
                        $branches[$branch_id] = $value['Branch']['code'];
                    }
                }

                if( !empty($value[0]['cnt']) ) {
                    $totalMuatan = $value[0]['cnt'];
                }

                if( $data_type == 'retail' ) {
                    $dataTtuj[$customer_id] = $totalMuatan;
                } else {
                    $dataTtuj[$customer_id][$branch_id] = $totalMuatan;
                }
            }
        }

        if( !empty($branches) ) {
            asort($branches);
        }

        if( !empty($currentMonth) ) {
            $this->request->data['Truck']['month'] = date('m', strtotime($currentMonth));
            $this->request->data['Truck']['year'] = date('Y', strtotime($currentMonth));
        }

        $this->set(compact(
            'customers', 'data_action',
            'lastDay', 'currentMonth', 'dataTtuj',
            'targetUnit', 'branches', 'data_type'
        ));

        if($data_action == 'pdf'){
            $this->layout = 'pdf';
        }else if($data_action == 'excel'){
            $this->layout = 'ajax';
        }
    }

    function licenses_report($data_action = false){
        $this->loadModel('Truck');
        $this->loadModel('Customer');

        $allow_branch_id = Configure::read('__Site.config_allow_branch_id');
        $conditions = array(
            'Truck.branch_id' => $allow_branch_id,
        );

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['nopol'])){
                $data = urldecode($refine['nopol']);
                $typeTruck = !empty($refine['type'])?$refine['type']:1;

                if( $typeTruck == 2 ) {
                    $conditionsNopol = array(
                        'Truck.id' => $data,
                    );
                } else {
                    $conditionsNopol = array(
                        'Truck.nopol LIKE' => '%'.$data.'%',
                    );
                }

                $truckSearch = $this->Truck->getData('list', array(
                    'conditions' => $conditionsNopol,
                    'fields' => array(
                        'Truck.id', 'Truck.id',
                    ),
                ), true, array(
                    'branch' => false,
                ));
                $conditions['Truck.id'] = $truckSearch;
                $this->request->data['Truck']['nopol'] = $data;
                $this->request->data['Truck']['type'] = $typeTruck;
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
            if(!empty($refine['company'])){
                $data = urldecode($refine['company']);
                $conditions['Truck.company_id'] = $data;
                $this->request->data['Truck']['company_id'] = $data;
            }
            
            $conditions = $this->MkCommon->getConditionGroupBranch( $refine, 'Truck', $conditions, 'conditions' );
        }
        
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

        $options = array(
            'conditions' => $conditions,
            'contain' => array(
                'TruckCustomer' => array(
                    'CustomerNoType'
                )
            ),
        );

        if( !empty($data_action) ) {
            $trucks = $this->Truck->getData('all', $options, true, array(
                'branch' => false,
            ));
        } else {
            $options['limit'] = Configure::read('__Site.config_pagination');
            $this->paginate = $this->Truck->getData('paginate', $options, true, array(
                'branch' => false,
            ));
            $trucks = $this->paginate('Truck');
        }

        if( !empty($trucks) ) {
            foreach ($trucks as $key => $value) {
                $branch_id = $this->MkCommon->filterEmptyField($value, 'Truck', 'branch_id');
                $value = $this->GroupBranch->Branch->getMerge($value, $branch_id);
                $trucks[$key] = $value;
            }
        }

        $this->loadModel('Customer');
        $customers = $this->Customer->getData('list', array(
            'fields' => array(
                'Customer.id', 'Customer.customer_name_code'
            ),
            'conditions' => array(
                'Customer.branch_id' => $allow_branch_id,
            ),
        ), true, array(
            'branch' => false,
        ));
        $companies = $this->Truck->Company->getData('list');

        $this->set('active_menu', 'licenses_report');
        $sub_module_title = __('Laporan Surat-surat Truk');

        $this->set(compact(
            'trucks', 'customers', 'sub_module_title', 
            'data_action', 'companies'
        ));

        if($data_action == 'pdf'){
            $this->layout = 'pdf';
        }else if($data_action == 'excel'){
            $this->layout = 'ajax';
        }
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
                $dataValidate['TruckCustomer']['branch_id'] = !empty($data['TruckCustomer']['branch_id'][$key])?$data['TruckCustomer']['branch_id'][$key]:false;
                
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
            $this->loadModel('City');
            $this->loadModel('Driver');
            $this->loadModel('Customer');
            $this->loadModel('Perlengkapan');
            App::import('Vendor', 'excelreader'.DS.'excel_reader2');

            $this->set('active_menu', 'trucks');
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
                        $path = APP.'webroot'.DS.'files'.DS.date('Y').DS.date('m').DS;
                        $filenoext = basename ($filename, '.xls');
                        $filenoext = basename ($filenoext, '.XLS');
                        $fileunique = uniqid() . '_' . $filenoext;

                        if( !file_exists($path) ) {
                            mkdir($path, 0755, true);
                        }

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
                                    $branch = $this->GroupBranch->Branch->getData('first', array(
                                        'conditions' => array(
                                            'Branch.code' => $kode_cabang,
                                        ),
                                    ));
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
                                        ),
                                    ), true, array(
                                        'branch' => false,
                                    ));

                                    if( !empty($truckBrand) ) {
                                        $truck_brand_id = $truckBrand['TruckBrand']['id'];
                                    } else {
                                        $truck_brand_id = false;
                                    }
                                    if( !empty($truckCategory) ) {
                                        $truck_category_id = $truckCategory['TruckCategory']['id'];
                                    } else {
                                        $truck_category_id = false;
                                    }
                                    if( !empty($truckFacility) ) {
                                        $truck_facility_id = $truckFacility['TruckFacility']['id'];
                                    } else {
                                        $truck_facility_id = false;
                                    }
                                    if( !empty($company) ) {
                                        $company_id = $company['Company']['id'];
                                    } else {
                                        $company_id = false;
                                    }
                                    if( !empty($driver) ) {
                                        $driver_id = $driver['Driver']['id'];
                                    } else {
                                        $driver_id = false;
                                    }
                                    $branch_id = $this->MkCommon->filterEmptyField($branch, 'Branch', 'id');

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
                                            'kir' => !empty($biaya_kir)?$this->MkCommon->convertPriceToString($biaya_kir):0,
                                            'siup' => !empty($biaya_siup)?$this->MkCommon->convertPriceToString($biaya_siup):0,
                                            'bpkb' => !empty($bpkb)?$this->MkCommon->convertPriceToString($bpkb):0,
                                            'atas_nama' => !empty($atas_nama)?$atas_nama:false,
                                            'no_stnk' => !empty($no_stnk)?$no_stnk:false,
                                            'no_rangka' => !empty($no_rangka)?$no_rangka:false,
                                            'bbnkb' => !empty($biaya_bbnkb)?$this->MkCommon->convertPriceToString($biaya_bbnkb):0,
                                            'pkb' => !empty($biaya_pkb)?$this->MkCommon->convertPriceToString($biaya_pkb):0,
                                            'swdkllj' => !empty($biaya_swdkllj)?$this->MkCommon->convertPriceToString($biaya_swdkllj):0,
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
                                            'branch_id' => $branch_id,
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
                                                ),
                                            ), true, array(
                                                'branch' => false,
                                                'plant' => false,
                                            ));

                                            if( !empty($customer) ) {
                                                $requestData['ROW'.($x-1)]['TruckCustomer']['customer_id'][$i] = $customer['Customer']['id'];
                                                $requestData['ROW'.($x-1)]['TruckCustomer']['branch_id'][$i] = $branch_id;
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
                                            $varJml = !empty($$varJml)?$this->MkCommon->convertPriceToString($$varJml):false;
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

                                        $this->Log->logActivity( __('Sukses upload Truk by Import Excel'), $this->user_data, $this->RequestHandler, $this->params );
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
                                        $error_message .= sprintf(__('Gagal pada baris ke %s : Gagal Upload Data. %s'), $row_submitted, $textError) . '<br>';
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

    function daily_report($data_action = false) {
        $this->loadModel('Ttuj');
        $this->loadModel('City');

        $dateFrom = date('Y-m-d');
        $dateTo = date('Y-m-d');
        $sub_module_title = __('Laporan Harian Kendaraan');
        $allow_branch_id = Configure::read('__Site.config_allow_branch_id');
        $allow_branch = Configure::read('__Site.config_allow_branchs');
        $options = array(
            'conditions' => array(
                'Ttuj.branch_id' => $allow_branch_id,
            ),
            'order' => array(
                'Ttuj.ttuj_date' => 'DESC',
                'Ttuj.id' => 'DESC',
            ),
        );

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['date'])){
                $dateStr = urldecode($refine['date']);
                $date = explode('-', $dateStr);

                if( !empty($date) ) {
                    $date[0] = urldecode($date[0]);
                    $date[1] = urldecode($date[1]);
                    $dateFrom = $this->MkCommon->getDate($date[0]);
                    $dateTo = $this->MkCommon->getDate($date[1]);
                }
                $this->request->data['Ttuj']['date'] = $dateStr;
            }
            if(!empty($refine['nopol'])){
                $data = urldecode($refine['nopol']);
                $typeTruck = !empty($refine['type'])?$refine['type']:1;

                if( $typeTruck == 2 ) {
                    $options['conditions']['Ttuj.truck_id'] = $data;
                } else {
                    $options['conditions']['Ttuj.nopol LIKE'] = '%'.$data.'%';
                }
                $this->request->data['Truck']['nopol'] = $data;
                $this->request->data['Truck']['type'] = $typeTruck;
            }
            if(!empty($refine['company'])){
                $data = urldecode($refine['company']);

                $options['conditions']['Truck.company_id'] = $data;
                $options['contain'][] = 'Truck';

                $this->request->data['Truck']['company_id'] = $data;
            }
            if(!empty($refine['no_ttuj'])){
                $data = urldecode($refine['no_ttuj']);

                $options['conditions']['Ttuj.no_ttuj LIKE'] = '%'.$data.'%';
                $this->request->data['Ttuj']['no_ttuj'] = $data;
            }

            // Custom Otorisasi
            $options = $this->MkCommon->getConditionGroupBranch( $refine, 'Ttuj', $options );
            // $allow_branch = $this->MkCommon->getBranchNameFilter( $refine );
        }

        $params = $this->MkCommon->_callRefineParams($this->params);
        $options =  $this->Ttuj->_callRefineParams($params, $options);

        $options['conditions'] = array_merge($options['conditions'], array(
            'DATE_FORMAT(Ttuj.ttuj_date, \'%Y-%m-%d\') >='=> $dateFrom,
            'DATE_FORMAT(Ttuj.ttuj_date, \'%Y-%m-%d\') <=' => $dateTo,
        ));

        if( !empty($data_action) ) {
            $ttujs = $this->Ttuj->getData('all', $options, true, array(
                'branch' => false,
            ));
        } else {
            $options['limit'] = 20;
            $options = $this->Ttuj->getData('paginate', $options, true, array(
                'branch' => false,
            ));
            $this->paginate = $options;
            $ttujs = $this->paginate('Ttuj');
        }

        $allow_branch = array();

        if( !empty($ttujs) ) {
            $this->loadModel('Driver');

            foreach ($ttujs as $key => $value) {
                $id = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'id');
                $truck_id = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'truck_id');
                $branch_id = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'branch_id');
                $driver_id = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'driver_id');
                $driver_penganti_id = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'driver_penganti_id');
                $is_retail = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'is_retail');

                $value = $this->GroupBranch->Branch->getMerge($value, $branch_id);
                $value = $this->Driver->getMerge($value, $driver_id);
                $value = $this->Driver->getMerge($value, $driver_penganti_id, 'DriverPenganti');
                $value = $this->Truck->getMerge($value, $truck_id);

                if( !empty($is_retail) ) {
                    $value = $this->Ttuj->TtujTipeMotor->getMergeTtujTipeMotor( $value, $id );
                    $value['Ttuj']['to_city_name'] = Set::extract('/TtujTipeMotor/City/name', $value);

                    if( !empty($value['Ttuj']['to_city_name']) ) {
                        $value['Ttuj']['to_city_name'] = array_unique($value['Ttuj']['to_city_name']);
                        $value['Ttuj']['to_city_name'] = implode(', ', $value['Ttuj']['to_city_name']);
                    }
                }

                $value['Ttuj']['total_unit'] = $this->Ttuj->TtujTipeMotor->getTotalMuatan( $id );

                $branch_name = $this->MkCommon->filterEmptyField($value, 'Branch', 'name');
                $allow_branch[$branch_id] = $branch_name;

                $ttujs[$key] = $value;
            }
        }

        if( !empty($dateFrom) && !empty($dateTo) ) {
            $this->request->data['Truck']['date'] = sprintf('%s - %s', date('d/m/Y', strtotime($dateFrom)), date('d/m/Y', strtotime($dateTo)));
            $periode = sprintf('%s - %s', date('d M Y', strtotime($dateFrom)), date('d M Y', strtotime($dateTo)));
        } else {
            $periode = '-';
        }

        $companies = $this->Truck->Company->getData('list');
        $cities = $this->City->getListCities();

        $this->set('active_menu', 'daily_report');
        $this->set('sub_module_title', $sub_module_title);

        $this->set(compact(
            'ttujs', 'from_date', 'to_date', 
            'data_action', 'header_module_title',
            'periode', 'allow_branch', 'companies',
            'cities'
        ));

        if($data_action == 'pdf'){
            $this->layout = 'pdf';
        }else if($data_action == 'excel'){
            $this->layout = 'ajax';
        } else {
            $this->MkCommon->_layout_file(array(
                'freeze',
                'select',
            ));
        }
    }

    public function mutations() {
        $this->loadModel('TruckMutation');

        $options = array(
            'conditions' => array(),
        );
        $dateFrom = date('Y-m-d', strtotime('-1 month'));
        $dateTo = date('Y-m-d');
        
        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['date'])){
                $dateStr = urldecode($refine['date']);
                $date = explode('-', $dateStr);

                if( !empty($date) ) {
                    $date[0] = urldecode($date[0]);
                    $date[1] = urldecode($date[1]);
                    $dateFrom = $this->MkCommon->getDate($date[0]);
                    $dateTo = $this->MkCommon->getDate($date[1]);
                }
                $this->request->data['Truck']['date'] = $dateStr;
            }
            if(!empty($refine['nopol'])){
                $data = urldecode($refine['nopol']);
                $typeTruck = !empty($refine['type'])?$refine['type']:1;

                if( $typeTruck == 2 ) {
                    $options['conditions']['TruckMutation.truck_id'] = $data;
                } else {
                    $options['conditions']['TruckMutation.nopol LIKE'] = '%'.$data.'%';
                }
                $this->request->data['Truck']['nopol'] = $data;
                $this->request->data['Truck']['type'] = $typeTruck;
            }
            if(!empty($refine['no_doc'])){
                $value = urldecode($refine['no_doc']);
                $options['conditions']['TruckMutation.no_doc LIKE'] = '%'.$value.'%';
                $this->request->data['Truck']['no_doc'] = $value;
            }
            if(!empty($refine['description'])){
                $value = urldecode($refine['description']);
                $options['conditions']['TruckMutation.description LIKE'] = '%'.$value.'%';
                $this->request->data['Truck']['description'] = $value;
            }
        }

        $options['conditions'] = array_merge($options['conditions'], array(
            'DATE_FORMAT(TruckMutation.mutation_date, \'%Y-%m-%d\') >='=> $dateFrom,
            'DATE_FORMAT(TruckMutation.mutation_date, \'%Y-%m-%d\') <=' => $dateTo,
        ));

        $this->paginate = $this->TruckMutation->getData('paginate', $options, true, array(
            'status' => 'all',
        ));
        $truckMutations = $this->paginate('TruckMutation');

        if( !empty($truckMutations) ) {
            foreach ($truckMutations as $key => $value) {
                $truck_mutation_id = $this->MkCommon->filterEmptyField($value, 'TruckMutation', 'id');
                $value = $this->TruckMutation->TruckMutationCustomer->getMerge($value, $truck_mutation_id);
                $value = $this->TruckMutation->TruckMutationOldCustomer->getMerge($value, $truck_mutation_id);
                $truckMutations[$key] = $value;
            }
        }

        if( !empty($dateFrom) && !empty($dateTo) ) {
            $this->request->data['Truck']['date'] = sprintf('%s - %s', date('d/m/Y', strtotime($dateFrom)), date('d/m/Y', strtotime($dateTo)));
        }

        $this->set('active_menu', 'mutations');
        $this->set('sub_module_title', __('Data Mutasi Truk'));
        $this->set(compact(
            'truckMutations'
        ));
    }

    function getDataMutation () {
        $this->loadModel('Customer');

        if( !empty($this->request->data) ) {
            $truck_id = !empty($this->request->data['Truck']['truck_id'])?$this->request->data['Truck']['truck_id']:false;
            $truckCustomers = $this->Truck->TruckCustomer->getMergeTruckCustomer(array(), $truck_id);
        }

        $branches = $this->GroupBranch->Branch->getData('list');
        $truckCategories = $this->Truck->TruckCategory->getData('list');
        $truckFacilities = $this->Truck->TruckFacility->getData('list');
        $trucks = $this->Truck->getData('list', array(
            'fields' => array(
                'Truck.id', 'Truck.nopol'
            ),
        ), true, array(
            'branch' => false,
        ));
        $customers = $this->Customer->getData('list', array(
            'fields' => array(
                'Customer.id', 'Customer.customer_name_code'
            ),
        ), true, array(
            'branch' => false,
            'plant' => false,
        ));
        $drivers = $this->Truck->Driver->getData('list', array(
            'conditions' => array(
                'Truck.id' => NULL,
            ),
            'fields' => array(
                'Driver.id', 'Driver.driver_name'
            ),
            'contain' => array(
                'Truck'
            ),
        ), true, array(
            'branch' => false,
        ));

        $this->MkCommon->_layout_file('select');
        $this->set('active_menu', 'mutations');
        $this->set(compact(
            'trucks', 'customers', 'branches',
            'truckCategories', 'truckFacilities',
            'drivers', 'truckCustomers'
        ));
        $this->render('mutation_form');
    }

    public function mutation_add() {
        $this->loadModel('TruckMutation');
        $this->set('sub_module_title', __('Tambah Mutasi Truk'));
        $data = $this->request->data;

        if( !empty($data['TruckMutation']['mutation_date']) ) {
            $data['TruckMutation']['mutation_date'] = $this->MkCommon->getDate($data['TruckMutation']['mutation_date']);
        }

        $result = $this->TruckMutation->doSave($data, false, false, $this);
        $this->MkCommon->setProcessParams($result, array(
            'controller' => 'trucks',
            'action' => 'mutations',
            'admin' => false,
        ));

        if( !empty($data['TruckMutation']['mutation_date']) ) {
            $this->request->data['TruckMutation']['mutation_date'] = $this->MkCommon->getDate($data['TruckMutation']['mutation_date'], true);
        }

        $this->getDataMutation();
    }

    public function mutation_detail( $id = false ) {
        $this->loadModel('TruckMutation');
        $this->set('sub_module_title', __('Edit Mutasi Truk'));
        $truckMutation = $this->TruckMutation->getData('first', array(
            'conditions' => array(
                'TruckMutation.id' => $id,
            ),
        ), true, array(
            'status' => false,
        ));

        if( !empty($truckMutation) ) {
            $truckMutation = $this->TruckMutation->TruckMutationCustomer->getMerge($truckMutation, $id);
            $truckMutation = $this->TruckMutation->TruckMutationOldCustomer->getMerge($truckMutation, $id);
            $this->request->data = $truckMutation;
            $this->request->data['Truck'] = $truckMutation['TruckMutation'];
            $this->request->data['DataMutation'] = $truckMutation['TruckMutation'];

            if( !empty($this->request->data['TruckMutation']['mutation_date']) ) {
                $this->request->data['TruckMutation']['mutation_date'] = $this->MkCommon->getDate($this->request->data['TruckMutation']['mutation_date'], true);
            }
        }

        $this->set(compact(
            'truckMutation', 'id'
        ));
        $this->getDataMutation();
    }

    function mutation_toggle($id = false){
        $this->loadModel('TruckMutation');
        $is_ajax = $this->RequestHandler->isAjax();
        $modelName = 'TruckMutation';
        $action_type = 'mutation';
        $msg = array(
            'msg' => '',
            'type' => 'error'
        );
        $truckMutation = $this->TruckMutation->getData('first', array(
            'conditions' => array(
                'TruckMutation.id' => $id,
            ),
        ));

        if( !empty($truckMutation) ){
            if(!empty($this->request->data)){
                if(!empty($this->request->data['TruckMutation']['canceled_date'])){
                    $this->request->data['TruckMutation']['void_date'] = $this->MkCommon->getDate($this->request->data['TruckMutation']['canceled_date']);
                    $this->request->data['TruckMutation']['status'] = 0;

                    $this->TruckMutation->id = $id;
                    $this->TruckMutation->set($this->request->data);

                    if($this->TruckMutation->save()){
                        $msg = array(
                            'msg' => __('Berhasil melakukan void'),
                            'type' => 'success'
                        );
                        $this->MkCommon->setCustomFlash( $msg['msg'], $msg['type']);  
                        $this->Log->logActivity( sprintf(__('Berhasil melakukan void mutasi truk #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id ); 
                    }else{
                        $this->Log->logActivity( sprintf(__('Gagal melakukan void mutasi truk #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id ); 
                    }
                }else{
                    $msg = array(
                        'msg' => __('Harap masukkan tanggal pembatalan'),
                        'type' => 'error'
                    );
                }
            }

            $this->set('truckMutation', $truckMutation);
        }else{
            $msg = array(
                'msg' => __('Mutasi truk tidak ditemukan'),
                'type' => 'error'
            );
        }

        $canceled_date = $this->MkCommon->filterEmptyField($this->request->data, 'TruckMutation', 'void_date');
        $this->set(compact(
            'msg', 'is_ajax', 'action_type',
            'canceled_date', 'modelName'
        ));
        $this->render('/Elements/blocks/common/form_delete');
    }

    public function add_driver_import( $download = false ) {
        if(!empty($download)){
            $link_url = FULL_BASE_URL . '/files/drivers.xls';
            $this->redirect($link_url);
            exit;
        } else {
            $this->loadModel('Driver');
            App::import('Vendor', 'excelreader'.DS.'excel_reader2');

            $this->set('active_menu', 'drivers');
            $this->set('sub_module_title', __('Import Supir'));

            $urlRedirect = array(
                'action'=>'add_driver_import'
            );

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
                        $this->redirect($urlRedirect);
                    } else {
                        $path = APP.'webroot'.DS.'files'.DS.date('Y').DS.date('m').DS;
                        $filenoext = basename ($filename, '.xls');
                        $filenoext = basename ($filenoext, '.XLS');
                        $fileunique = uniqid() . '_' . $filenoext;

                        if( !file_exists($path) ) {
                            mkdir($path, 0755, true);
                        }

                        $targetdir = $path . $fileunique . $filename;
                         
                        ini_set('memory_limit', '96M');
                        ini_set('post_max_size', '64M');
                        ini_set('upload_max_filesize', '64M');

                        if(!move_uploaded_file($source, $targetdir)) {
                            $this->MkCommon->setCustomFlash(__('Maaf, terjadi kesalahan. Silahkan coba lagi, atau hubungi Admin kami.'), 'error');
                            $this->redirect($urlRedirect);
                        }
                    }
                } else {
                    $this->MkCommon->setCustomFlash(__('Maaf, terjadi kesalahan. Silahkan coba lagi, atau hubungi Admin kami.'), 'error');
                    $this->redirect($urlRedirect);
                }

                $xls_files = glob( $targetdir );

                if(empty($xls_files)) {
                    $this->rmdir_recursive ( $targetdir);
                    $this->MkCommon->setCustomFlash(__('Tidak terdapat file excel atau berekstensi .xls pada file zip Anda. Silahkan periksa kembali.'), 'error');
                    $this->redirect($urlRedirect);
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
                                    $driverRelation = $this->Driver->DriverRelation->find('first', array(
                                        'conditions' => array(
                                            'DriverRelation.name' => $hubungan,
                                            'DriverRelation.status' => 1,
                                        ),
                                    ));
                                    $jenisSim = $this->Driver->JenisSim->find('first', array(
                                        'conditions' => array(
                                            'JenisSim.name' => $jenis_sim,
                                            'JenisSim.status' => 1,
                                        ),
                                    ));
                                    $branch = $this->GroupBranch->Branch->getData('first', array(
                                        'conditions' => array(
                                            'Branch.code' => $kode_cabang,
                                        ),
                                    ));

                                    $branch_id = $this->MkCommon->filterEmptyField($branch, 'Branch', 'id');
                                    $nama_lengkap = !empty($nama_lengkap)?$nama_lengkap:false;
                                    $nama_panggilan = !empty($nama_panggilan)?$nama_panggilan:false;
                                    $no_ktp = !empty($no_ktp)?$no_ktp:false;
                                    $alamat_rumah = !empty($alamat_rumah)?$alamat_rumah:false;
                                    $kota = !empty($kota)?$kota:false;
                                    $provinsi = !empty($provinsi)?$provinsi:false;
                                    $no_hp = !empty($no_hp)?$no_hp:false;
                                    $no_telp = !empty($no_telp)?$no_telp:false;
                                    $tempat_lahir = !empty($tempat_lahir)?$tempat_lahir:false;
                                    $tgl_lahir = !empty($tgl_lahir)?$this->MkCommon->checkdate($tgl_lahir):false;
                                    $jenis_sim_id = $this->MkCommon->filterEmptyField($jenisSim, 'JenisSim', 'id');
                                    $no_sim = !empty($no_sim)?$no_sim:false;
                                    $tgl_berakhir_sim = !empty($tgl_berakhir_sim)?$this->MkCommon->checkdate($tgl_berakhir_sim):false;
                                    $nama_kontak_darurat = !empty($nama_kontak_darurat)?$nama_kontak_darurat:false;
                                    $no_hp_kontak_darurat = !empty($no_hp_kontak_darurat)?$no_hp_kontak_darurat:false;
                                    $no_telp_kontak_darurat = !empty($no_telp_kontak_darurat)?$no_telp_kontak_darurat:false;
                                    $driver_relation_id = $this->MkCommon->filterEmptyField($driverRelation, 'DriverRelation', 'id');
                                    $tgl_penerimaan = !empty($tgl_penerimaan)?$this->MkCommon->checkdate($tgl_penerimaan):false;

                                    $requestData['ROW'.($x-1)] = array(
                                        'Driver' => array(
                                            'branch_id' => $branch_id,
                                            'jenis_sim_id' => $jenis_sim_id,
                                            'driver_relation_id' => $driver_relation_id,
                                            'name' => $nama_lengkap,
                                            'alias' => $nama_panggilan,
                                            'address' => $alamat_rumah,
                                            'city' => $kota,
                                            'provinsi' => $provinsi,
                                            'no_hp' => $no_hp,
                                            'phone' => $no_telp,
                                            'tempat_lahir' => $tempat_lahir,
                                            'birth_date' => $tgl_lahir,
                                            'kontak_darurat_name' => $nama_kontak_darurat,
                                            'kontak_darurat_no_hp' => $no_hp_kontak_darurat,
                                            'kontak_darurat_phone' => $no_telp_kontak_darurat,
                                            'join_date' => $tgl_penerimaan,
                                            'no_sim' => $no_sim,
                                            'identity_number' => $no_ktp,
                                            'expired_date_sim' => $tgl_berakhir_sim,
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
                                    $data['Driver']['no_id'] = $this->Driver->generateNoId();

                                    $this->Driver->create();
                                    
                                    if( $this->Driver->save($data) ){
                                        $this->Log->logActivity( __('Sukses upload Supir by Import Excel'), $this->user_data, $this->RequestHandler, $this->params );
                                        $successfull_row++;
                                    } else {
                                        $validationErrors = $this->Driver->validationErrors;
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
                                        $error_message .= sprintf(__('Gagal pada baris ke %s : Gagal Upload Data. %s'), $row_submitted, $textError) . '<br>';
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
                $this->redirect($urlRedirect);
            }
        }
    }

    function driver_reports($data_action = false) {
        $this->set('active_menu', 'reports');
        $this->set('sub_module_title', __('Laporan Supir'));
        $allow_branch_id = Configure::read('__Site.config_allow_branch_id');
        
        $options = array(
            'conditions' => array(
                'Driver.branch_id' => $allow_branch_id,
            ),
        );
        $from_date = '';
        $to_date = '';

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['nopol'])){
                $data = urldecode($refine['nopol']);
                $typeTruck = !empty($refine['type'])?$refine['type']:1;

                if( $typeTruck == 2 ) {
                    $conditionsNopol = array(
                        'Truck.id' => $data,
                    );
                } else {
                    $conditionsNopol = array(
                        'Truck.nopol LIKE' => '%'.$data.'%',
                    );
                }

                $truckSearch = $this->Truck->getData('list', array(
                    'conditions' => $conditionsNopol,
                    'fields' => array(
                        'Truck.id', 'Truck.driver_id',
                    ),
                ), true, array(
                    'branch' => false,
                ));
                $options['conditions']['Driver.id'] = $truckSearch;
                $this->request->data['Truck']['nopol'] = $data;
                $this->request->data['Truck']['type'] = $typeTruck;
            }

            if(!empty($refine['no_id'])){
                $value = urldecode($refine['no_id']);
                $options['conditions']['Driver.no_id LIKE'] = '%'.$value.'%';
                $this->request->data['Driver']['no_id'] = $value;
            }
            if(!empty($refine['name'])){
                $value = urldecode($refine['name']);
                $options['conditions']['Driver.driver_name LIKE'] = '%'.$value.'%';
                $this->request->data['Driver']['name'] = $value;
            }
            if(!empty($refine['no_truck'])){
                $value = urldecode($refine['no_truck']);

                $options['conditions']['Truck.id'] = NULL;
                $options['contain'][] = 'Truck';

                $this->request->data['Truck']['no_truck'] = $value;
            }

            // Custom Otorisasi
            $options = $this->MkCommon->getConditionGroupBranch( $refine, 'Driver', $options );
        }

        if( !empty($data_action) ) {
            $drivers = $this->Truck->Driver->getData('all', $options, true, array(
                'branch' => false,
            ));
        } else {
            $this->loadModel('Driver');

            $options['limit'] = 20;
            $options = $this->Truck->Driver->getData('paginate', $options, true, array(
                'branch' => false,
            ));

            $this->paginate = $options;
            $drivers = $this->paginate('Driver');
        }

        if( !empty($drivers) ) {
            foreach ($drivers as $key => $value) {
                $id = $this->MkCommon->filterEmptyField($value, 'Driver', 'id');
                $jenis_sim_id = $this->MkCommon->filterEmptyField($value, 'Driver', 'jenis_sim_id');
                $driver_relation_id = $this->MkCommon->filterEmptyField($value, 'Driver', 'driver_relation_id');

                $value = $this->Truck->Driver->JenisSim->getMerge( $value, $jenis_sim_id );
                $value = $this->Truck->Driver->DriverRelation->getMerge( $value, $driver_relation_id );
                $value = $this->Truck->getByDriver( $value, $id );
                $drivers[$key] = $value;
            }
        }

        $this->set(compact(
            'drivers', 'data_action'
        ));

        if($data_action == 'pdf'){
            $this->layout = 'pdf';
        }else if($data_action == 'excel'){
            $this->layout = 'ajax';
        }
    }

    function ttuj_report($data_action = false) {
        $this->loadModel('Ttuj');

        $dateFrom = date('Y-m-d', strtotime('-1 Month'));
        $dateTo = date('Y-m-d');
        $sub_module_title = __('Laporan Biaya Uang Jalan');
        $allow_branch_id = Configure::read('__Site.config_allow_branch_id');
        $allow_branch = Configure::read('__Site.config_allow_branchs');
        $options = array(
            'conditions' => array(
                'Ttuj.branch_id' => $allow_branch_id,
            ),
            'order' => array(
                'Ttuj.ttuj_date' => 'DESC',
                'Ttuj.id' => 'DESC',
            ),
        );

        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));
        $options =  $this->Ttuj->_callRefineParams($params, $options);

        $dateFrom = $this->MkCommon->filterEmptyField($params, 'named', 'DateFrom');
        $dateTo = $this->MkCommon->filterEmptyField($params, 'named', 'DateTo');

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];
            $options = $this->MkCommon->getConditionGroupBranch( $refine, 'Ttuj', $options );
        }

        if( !empty($data_action) ) {
            $ttujs = $this->Ttuj->getData('all', $options, true, array(
                'branch' => false,
            ));
        } else {
            $options['limit'] = 20;
            $options = $this->Ttuj->getData('paginate', $options, true, array(
                'branch' => false,
            ));
            $this->paginate = $options;
            $ttujs = $this->paginate('Ttuj');
        }
        
        $allow_branch = array();

        if( !empty($ttujs) ) {
            foreach ($ttujs as $key => $value) {
                $id = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'id');
                $customer_id = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'customer_id');
                $branch_id = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'branch_id');
                $driver_id = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'driver_id');
                $driver_penganti_id = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'driver_penganti_id');
                $truck_id = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'truck_id');
                $is_retail = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'is_retail');

                $value = $this->Ttuj->Customer->getMerge($value, $customer_id);
                $value = $this->GroupBranch->Branch->getMerge($value, $branch_id);
                $value = $this->Truck->Driver->getMerge($value, $driver_id);
                $value = $this->Truck->Driver->getMerge($value, $driver_penganti_id, 'DriverPenganti');
                $value = $this->Truck->getMerge($value, $truck_id);

                if( !empty($is_retail) ) {
                    $value = $this->Ttuj->TtujTipeMotor->getMergeTtujTipeMotor( $value, $id );
                    $value['Ttuj']['to_city_name'] = Set::extract('/TtujTipeMotor/City/name', $value);

                    if( !empty($value['Ttuj']['to_city_name']) ) {
                        $value['Ttuj']['to_city_name'] = array_unique($value['Ttuj']['to_city_name']);
                        $value['Ttuj']['to_city_name'] = implode(', ', $value['Ttuj']['to_city_name']);
                    }
                }

                $value['Ttuj']['total_unit'] = $this->Ttuj->TtujTipeMotor->getTotalMuatan( $id );

                $branch_name = $this->MkCommon->filterEmptyField($value, 'Branch', 'name');
                $allow_branch[$branch_id] = $branch_name;

                $ttujs[$key] = $value;
            }
        }

        if( !empty($dateFrom) && !empty($dateTo) ) {
            $periode = sprintf('%s - %s', date('d M Y', strtotime($dateFrom)), date('d M Y', strtotime($dateTo)));
        } else {
            $periode = '-';
        }

        $companies = $this->Truck->Company->getData('list');
        $customers = $this->Ttuj->Customer->getData('list', array(
            'fields' => array(
                'Customer.id', 'Customer.customer_name_code'
            ),
        ), true, array(
            'branch' => false,
            'plant' => false,
        ));

        $this->set('active_menu', 'ttuj_report');
        $this->set('sub_module_title', $sub_module_title);

        $this->set(compact(
            'ttujs', 'from_date', 'to_date', 
            'data_action', 'header_module_title',
            'periode', 'allow_branch', 'companies',
            'customers'
        ));

        if($data_action == 'pdf'){
            $this->layout = 'pdf';
        }else if($data_action == 'excel'){
            $this->layout = 'ajax';
        } else {
            $this->MkCommon->_layout_file(array(
                'select',
                'freeze',
            ));
        }
    }

    public function leadtime_report( $data_action = false ) {
        $this->loadModel('Ttuj');
        $this->loadModel('City');

        $module_title = __('Laporan leadtime');
        $values = array();
        $dateFrom = date('Y-m-d', strtotime('-1 Month'));
        $dateTo = date('Y-m-d');

        $this->set('sub_module_title', $module_title);
        $allow_branch_id = Configure::read('__Site.config_allow_branch_id');
        $options =  $this->Ttuj->getData('paginate', array(
            'conditions' => array(
                'Ttuj.is_draft' => 0,
            ),
            'order'=> array(
                'Ttuj.is_pool' => 'ASC',
                'Ttuj.created' => 'DESC',
                'Ttuj.id' => 'DESC',
            ),
        ), true, array(
            'branch' => false,
        ));

        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));
        $dateFrom = $this->MkCommon->filterEmptyField($params, 'named', 'DateFrom');
        $dateTo = $this->MkCommon->filterEmptyField($params, 'named', 'DateTo');
        $options =  $this->Ttuj->_callRefineParams($params, $options);

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            // Custom Otorisasi
            $options = $this->MkCommon->getConditionGroupBranch( $refine, 'Ttuj', $options );
        }

        if( !empty($dateFrom) && !empty($dateTo) ) {
            $module_title .= sprintf(' Periode %s', $this->MkCommon->getCombineDate($dateFrom, $dateTo));
        }

        if( !empty($data_action) ){
            $values = $this->Ttuj->find('all', $options);
        } else {
            $options['limit'] = Configure::read('__Site.config_pagination');
            $this->paginate = $options;
            $values = $this->paginate('Ttuj');
        }

        if( !empty($values) ) {
            foreach ($values as $key => $value) {
                $id = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'id');
                $driver_penganti_id = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'driver_penganti_id');
                $truck_id = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'truck_id');
                $uang_jalan_id = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'truck_id');

                $from_time = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'tgljam_berangkat');
                $to_time = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'tgljam_tiba');

                $from_pool_time = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'tgljam_balik');
                $to_pool_time = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'tgljam_pool');

                $value = $this->Ttuj->Truck->Driver->getMerge($value, $driver_penganti_id, 'DriverPenganti');
                $value = $this->Ttuj->UangJalan->getMerge($value, $uang_jalan_id);
                $value = $this->Ttuj->Truck->getMerge($value, $truck_id);
                $value = $this->Ttuj->getSumUnit( $value, $id );
                $value = $this->Ttuj->Lku->_callTotalLkuFromTtuj( $value, $id );

                if( !empty($to_time) ) {
                    $leadTimeArrive = $this->MkCommon->dateDiff($from_time, $to_time, 'day', true);
                    $value['ArriveLeadTime'] = $leadTimeArrive;
                } else {
                    $leadTimeArrive = $this->MkCommon->dateDiff($from_time, date('Y-m-d H:i:s'), 'day', true);
                    $arrive_lead_time = $this->MkCommon->filterEmptyField($leadTimeArrive, 'total_hour');
                    $target_arrive_lead_time = $this->MkCommon->filterEmptyField($value, 'UangJalan', 'arrive_lead_time');
                    
                    if( $arrive_lead_time > $target_arrive_lead_time ) {
                        $value['ArriveLeadTime'] = $leadTimeArrive;
                    }
                }

                // if( !empty($to_pool_time) ) {
                    $leadTimeBack = $this->MkCommon->dateDiff($from_pool_time, $to_pool_time, 'day', true);
                    $value['BackLeadTime'] = $leadTimeBack;
                // } else {
                //     $leadTimeBack = $this->MkCommon->dateDiff($from_pool_time, date('Y-m-d H:i:s'), 'day', true);
                //     $back_lead_time = $this->MkCommon->filterEmptyField($leadTimeBack, 'total_hour');
                //     $target_back_lead_time = $this->MkCommon->filterEmptyField($value, 'UangJalan', 'back_lead_time');
                    
                //     if( $back_lead_time > $target_back_lead_time ) {
                //         $value['BackLeadTime'] = $leadTimeBack;
                //     }
                // }

                $values[$key] = $value;
            }
        }

        $cities = $this->City->getListCities();
        $customers = $this->Ttuj->Customer->getData('list', array(
            'fields' => array(
                'Customer.id', 'Customer.customer_name_code'
            ),
        ));

        $this->set('active_menu', 'leadtime_report');
        $this->set(compact(
            'values', 'module_title', 'data_action',
            'cities', 'customers'
        ));

        if($data_action == 'pdf'){
            $this->layout = 'pdf';
        }else if($data_action == 'excel'){
            $this->layout = 'ajax';
        } else {
            $this->MkCommon->_layout_file(array(
                'select',
                'freeze',
            ));
        }
    }

    function document_payments(){
        $this->loadModel('DocumentPayment');
        $options = array(
            'order' => array(
                'DocumentPayment.created' => 'DESC',
                'DocumentPayment.id' => 'DESC',
            ),
        );

        $this->set('active_menu', 'document_payments');
        $this->set('sub_module_title', __('Pembayaran Surat-surat Truk'));
        
        $dateFrom = date('Y-m-d', strtotime('-1 Month'));
        $dateTo = date('Y-m-d');

        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));
        $options =  $this->DocumentPayment->_callRefineParams($params, $options);

        $this->paginate = $this->DocumentPayment->getData('paginate', $options);
        $values = $this->paginate('DocumentPayment');

        $this->set(compact(
            'values'
        )); 
    }

    function document_payment_add(){
        $this->loadModel('DocumentPayment');
        $module_title = __('Tambah Pembayaran Surat-surat Truk');
        $this->set('sub_module_title', $module_title);

        $this->doDocumentPayment();
    }

    function document_payment_edit( $id = false ){
        $this->loadModel('DocumentPayment');
        $module_title = __('Edit Pembayaran Surat-surat Truk');
        $this->set('sub_module_title', $module_title);

        $head_office = Configure::read('__Site.config_branch_head_office');

        if( !empty($head_office) ) {
            $elementRevenue = array(
                'branch' => false,
            );
        }

        $value = $this->DocumentPayment->getData('first', array(
            'conditions' => array(
                'DocumentPayment.id' => $id
            ),
        ), $elementRevenue);
        $value = $this->DocumentPayment->DocumentPaymentDetail->getMerge($value, $id);

        $this->doDocumentPayment( $id, $value );
    }

    function doDocumentPayment( $id = false, $value = false ){
        $this->set('active_menu', 'document_payments');

        if(!empty($this->request->data)){
            $data = $this->request->data;
            $data['DocumentPayment']['date_payment'] = !empty($data['DocumentPayment']['date_payment']) ? $this->MkCommon->getDate($data['DocumentPayment']['date_payment']) : '';
            $data['DocumentPayment']['branch_id'] = Configure::read('__Site.config_branch_id');

            $dataAmount = $this->MkCommon->filterEmptyField($data, 'DocumentPaymentDetail', 'amount');
            $flagPaymentDetail = $this->doDocumentPaymentDetail($dataAmount, $data);

            if( !empty($id) ) {
                $this->DocumentPayment->id = $id;
            } else {
                $this->DocumentPayment->create();
            }

            $this->DocumentPayment->set($data);

            if( $this->DocumentPayment->validates() && !empty($flagPaymentDetail) ){
                if($this->DocumentPayment->save()){
                    $document_id = $this->DocumentPayment->id;
                    $flagPaymentDetail = $this->doDocumentPaymentDetail($dataAmount, $data, $document_id);

                    $this->params['old_data'] = $value;
                    $this->params['data'] = $data;

                    $noref = str_pad($document_id, 6, '0', STR_PAD_LEFT);
                    $this->MkCommon->setCustomFlash(sprintf(__('Berhasil melakukan Pembayaran dokumen #%s'), $noref), 'success'); 
                    $this->Log->logActivity( sprintf(__('Berhasil melakukan Pembayaran dokumen #%s'), $document_id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $document_id );
                    
                    $this->redirect(array(
                        'action' => 'document_payments',
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal melakukan Pembayaran dokumen'), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal melakukan Pembayaran dokumen #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
                }
            }else{
                $msgError = array();

                if( !empty($this->DocumentPayment->DocumentPaymentDetail->validationErrors) ) {
                    $errorPaymentDetails = $this->DocumentPayment->DocumentPaymentDetail->validationErrors;

                    foreach ($errorPaymentDetails as $key => $errorPaymentDetail) {
                        if( !empty($errorPaymentDetail) ) {
                            foreach ($errorPaymentDetail as $key => $err_msg) {
                                $msgError[] = $err_msg;
                            }
                        }
                    }
                }

                if( !empty($msgError) ) {
                    $this->MkCommon->setCustomFlash('<ul><li>'.implode('</li><li>', $msgError).'</li></ul>', 'error'); 
                } else if( $flagPaymentDetail ) {
                    $this->MkCommon->setCustomFlash(__('Gagal melakukan Pembayaran dokumen'), 'error'); 
                }
            }

            $this->request->data['DocumentPayment']['date_payment'] = !empty($data['DocumentPayment']['date_payment']) ? $data['DocumentPayment']['date_payment'] : '';
        } else if( !empty($value) ) {
            if( !empty($value['DocumentPaymentDetail']) ) {
                foreach ($value['DocumentPaymentDetail'] as $key => $val) {
                    $document_id = $this->MkCommon->filterEmptyField($val, 'DocumentPaymentDetail', 'document_id');
                    $document_type = $this->MkCommon->filterEmptyField($val, 'DocumentPaymentDetail', 'document_type');
                    $amount = $this->MkCommon->filterEmptyField($val, 'DocumentPaymentDetail', 'amount');
                    $modelName = $this->RjTruck->_callDocumentType($document_type);

                    $val = $this->DocumentPayment->DocumentPaymentDetail->$modelName->getMerge($val, $document_id);

                    $this->request->data['DocumentTruck'][$key]['DocumentTruck'] = $this->MkCommon->filterEmptyField($val, $modelName);
                    $this->request->data['DocumentTruck'][$key]['DocumentTruck']['data_type'] = $document_type;

                    $this->request->data['DocumentPaymentDetail']['amount'][$key] = $amount;
                    $this->request->data['DocumentPaymentDetail']['document_id'][$key] = $document_id;
                    $this->request->data['DocumentPaymentDetail']['document_type'][$key] = $document_type;
                }
            }

            $this->request->data['DocumentPayment'] = $this->MkCommon->filterEmptyField($value, 'DocumentPayment');
        }

        $coas = $this->GroupBranch->Branch->BranchCoa->getCoas();

        $this->MkCommon->_layout_file('select');
        $this->set(compact(
            'id', 'coas'
        ));
        $this->render('document_payment_add');
    }

    function doDocumentPaymentDetail ( $dataAmount, $data, $document_payment_id = false ) {
        $flagPaymentDetail = true;
        $totalPayment = 0;
        $date_payment = $this->MkCommon->filterEmptyField($data, 'DocumentPayment', 'date_payment');
        $data = $this->request->data;

        if( !empty($document_payment_id) ) {
            $this->DocumentPayment->DocumentPaymentDetail->updateAll( array(
                'DocumentPaymentDetail.status' => 0,
            ), array(
                'DocumentPaymentDetail.document_payment_id' => $document_payment_id,
            ));
        }


        if( !empty($dataAmount) ) {
            foreach ($dataAmount as $key => $amount) {
                $document_id = !empty($data['DocumentPaymentDetail']['document_id'][$key])?$data['DocumentPaymentDetail']['document_id'][$key]:false;
                $document_type = !empty($data['DocumentPaymentDetail']['document_type'][$key])?$data['DocumentPaymentDetail']['document_type'][$key]:false;
                $amount = !empty($amount)?$this->MkCommon->convertPriceToString($amount, 0):0;
                $document_type = strtolower($document_type);

                $modelName = $this->RjTruck->_callDocumentType($document_type);
                $value = $this->DocumentPayment->DocumentPaymentDetail->$modelName->getMerge(array(), $document_id);
                
                $truck_id = $this->MkCommon->filterEmptyField($value, $modelName, 'truck_id');
                $price = $this->MkCommon->filterEmptyField($value, $modelName, 'price');
                $denda = $this->MkCommon->filterEmptyField($value, $modelName, 'denda');
                $biaya_lain = $this->MkCommon->filterEmptyField($value, $modelName, 'biaya_lain');
                $document_date = $this->MkCommon->filterEmptyField($value, $modelName, 'to_date');

                $dataPaymentDetail = array(
                    'DocumentPaymentDetail' => array(
                        'truck_id' => $truck_id,
                        'document_id' => $document_id,
                        'document_type' => $document_type,
                        'amount' => $amount,
                    ),
                );

                $totalPayment += $amount;
                $total_dibayar = $this->DocumentPayment->DocumentPaymentDetail->getTotalPayment($document_id, $document_type) + $amount;
                $this->request->data['DocumentTruck'][$key]['DocumentTruck'] = !empty($value[$modelName])?$value[$modelName]:false;
                $this->request->data['DocumentTruck'][$key]['DocumentTruck']['data_type'] = $document_type;

                if( !empty($document_payment_id) ) {
                    $dataPaymentDetail['DocumentPaymentDetail']['document_payment_id'] = $document_payment_id;
                    $total = $price + $denda + $biaya_lain;

                    switch ($document_type) {
                        case 'kir':
                            $fieldName = 'tgl_kir';
                            break;
                        case 'siup':
                            $fieldName = 'tgl_siup';
                            break;
                        default:
                            $fieldName = 'tgl_stnk';
                            break;
                    }
                    
                    if( !empty($total_dibayar) ) {
                        $flagPaid = 'half';

                        if( $total <= $total_dibayar ) {
                            $flagPaid = 'full';
                        }
                    
                        $this->DocumentPayment->DocumentPaymentDetail->$modelName->set('paid', $flagPaid);
                        $this->DocumentPayment->DocumentPaymentDetail->$modelName->id = $document_id;

                        if( $document_type == 'stnk_5_thn' ) {
                            $plat_to_date = $this->MkCommon->filterEmptyField($value, $modelName, 'plat_to_date');
                            $this->DocumentPayment->DocumentPaymentDetail->$modelName->Truck->set('tgl_stnk_plat', $plat_to_date);
                        }

                        $this->DocumentPayment->DocumentPaymentDetail->$modelName->Truck->set($fieldName, $document_date);
                        $this->DocumentPayment->DocumentPaymentDetail->$modelName->Truck->id = $truck_id;
                        $truckSave = $this->DocumentPayment->DocumentPaymentDetail->$modelName->Truck->save();
                        
                        if( !$this->DocumentPayment->DocumentPaymentDetail->$modelName->save() || !$truckSave ) {
                            $this->Log->logActivity( sprintf(__('Gagal mengubah status pembayaran Surat-surat #%s'), $document_id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $document_id );
                        }
                    }
                }

                $this->DocumentPayment->DocumentPaymentDetail->create();
                $this->DocumentPayment->DocumentPaymentDetail->set($dataPaymentDetail);

                if( !empty($document_payment_id) ) {
                    if( !$this->DocumentPayment->DocumentPaymentDetail->save() ) {
                        $flagPaymentDetail = false;
                    }
                } else {
                    if( !$this->DocumentPayment->DocumentPaymentDetail->validates() ) {
                        $flagPaymentDetail = false;
                    }
                }
            }
        } else {
            $flagPaymentDetail = false;
            $this->MkCommon->setCustomFlash(__('Mohon pilih biaya yang akan dibayar.'), 'error'); 
        }

        if( !empty($totalPayment) && !empty($document_payment_id) ) {
            $this->DocumentPayment->id = $document_payment_id;
            $this->DocumentPayment->set('total_payment', $totalPayment);

            if( !$this->DocumentPayment->save() ) {
                $this->Log->logActivity( sprintf(__('Gagal mengubah total pembayaran Surat-surat #%s'), $document_payment_id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $document_payment_id );
            } else {
                $document_no = $this->MkCommon->filterEmptyField($data, 'DocumentPayment', 'nodoc');
                $coa_id = $this->MkCommon->filterEmptyField($data, 'DocumentPayment', 'coa_id');

                $titleJournal = sprintf(__('Pembayaran Surat-surat Truk'));
                $titleJournal = $this->MkCommon->filterEmptyField($data, 'DocumentPayment', 'description', $titleJournal);

                $this->User->Journal->deleteJournal($document_payment_id, array(
                    'document_payment',
                ));
                $this->User->Journal->setJournal($totalPayment, array(
                    'credit' => $coa_id,
                    'debit' => 'document_payment_coa_id',
                ), array(
                    'date' => $date_payment,
                    'document_id' => $document_payment_id,
                    'title' => $titleJournal,
                    'document_no' => $document_no,
                    'type' => 'document_payment',
                ));
            }
        }

        return $flagPaymentDetail;
    }

    function document_payment_detail($id = false){
        $this->loadModel('DocumentPayment');
        $module_title = __('Kas/Bank');
        $elementRevenue = false;
        $head_office = Configure::read('__Site.config_branch_head_office');

        if( !empty($head_office) ) {
            $elementRevenue = array(
                'branch' => false,
            );
        }

        $value = $this->DocumentPayment->getData('first', array(
            'conditions' => array(
                'DocumentPayment.id' => $id
            ),
        ), $elementRevenue);

        $this->set('active_menu', 'document_payments');
        $sub_module_title = $title_for_layout = 'Detail Pembayaran Surat-surat Truk';

        if(!empty($value)){
            $coa_id = $this->MkCommon->filterEmptyField($value, 'DocumentPayment', 'coa_id');

            $value = $this->User->Journal->Coa->getMerge($value, $coa_id);
            $value = $this->DocumentPayment->DocumentPaymentDetail->getMerge($value, $id);

            if( !empty($value['DocumentPaymentDetail']) ) {
                foreach ($value['DocumentPaymentDetail'] as $key => $val) {
                    $document_id = $this->MkCommon->filterEmptyField($val, 'DocumentPaymentDetail', 'document_id');
                    $document_type = $this->MkCommon->filterEmptyField($val, 'DocumentPaymentDetail', 'document_type');
                    $modelName = $this->RjTruck->_callDocumentType($document_type);

                    $val = $this->DocumentPayment->DocumentPaymentDetail->$modelName->getMerge($val, $document_id);

                    $value['DocumentPaymentDetail'][$key] = $val;
                }
            }

            $this->set(compact(
                'value', 'sub_module_title', 'title_for_layout',
                'module_title'
            ));
        }else{
            $this->MkCommon->setCustomFlash(__('Data tidak ditemukan'), 'error');
            $this->redirect($this->referer());
        }
    }

    function document_payment_delete($id = false){
        $this->loadModel('DocumentPayment');
        $is_ajax = $this->RequestHandler->isAjax();
        $msg = array(
            'msg' => '',
            'type' => 'error'
        );
        $value = $this->DocumentPayment->getData('first', array(
            'conditions' => array(
                'DocumentPayment.id' => $id,
            ),
        ));

        if( !empty($value) ){
            if(!empty($this->request->data)){
                $data = $this->request->data;
                $data = $this->MkCommon->dataConverter($data, array(
                    'date' => array(
                        'DocumentPayment' => array(
                            'canceled_date',
                        ),
                    )
                ));

                $value = $this->DocumentPayment->DocumentPaymentDetail->getMerge($value, $id);
                $date_payment = $this->MkCommon->filterEmptyField($value, 'DocumentPayment', 'date_payment');

                if(!empty($data['DocumentPayment']['canceled_date'])){
                    $data['DocumentPayment']['canceled_date'] = $this->MkCommon->filterEmptyField($data, 'DocumentPayment', 'canceled_date');
                    $data['DocumentPayment']['is_canceled'] = 1;

                    $this->DocumentPayment->id = $id;
                    $this->DocumentPayment->set($data);

                    if($this->DocumentPayment->save()){
                        $document_no = $this->MkCommon->filterEmptyField($value, 'DocumentPayment', 'nodoc');
                        $coa_id = $this->MkCommon->filterEmptyField($value, 'DocumentPayment', 'coa_id');

                        if( !empty($value['DocumentPaymentDetail']) ) {
                            foreach ($value['DocumentPaymentDetail'] as $key => $detail) {
                                $document_id = $this->MkCommon->filterEmptyField($detail, 'DocumentPaymentDetail', 'document_id');
                                $document_type = $this->MkCommon->filterEmptyField($detail, 'DocumentPaymentDetail', 'document_type');
                                $total_dibayar = $this->DocumentPayment->DocumentPaymentDetail->getTotalPayment($document_id, $document_type, $id);
                                $flagPaid = 'none';
                                $document_type = strtolower($document_type);

                                if( !empty($total_dibayar) ) {
                                    $flagPaid = 'half';
                                }

                                switch ($document_type) {
                                    case 'kir':
                                        $fieldName = 'tgl_kir';
                                        $modelName = 'Kir';
                                        break;
                                    case 'siup':
                                        $fieldName = 'tgl_siup';
                                        $modelName = 'Siup';
                                        break;
                                    default:
                                        $fieldName = 'tgl_stnk';
                                        $modelName = 'Stnk';
                                        break;
                                }
                                
                                $dataDoc = $this->DocumentPayment->DocumentPaymentDetail->$modelName->getMerge(array(), $document_id);
                                $document_date = $this->MkCommon->filterEmptyField($dataDoc, $modelName, 'from_date');
                                $truck_id = $this->MkCommon->filterEmptyField($dataDoc, $modelName, 'truck_id');

                                if( $document_type == 'stnk_5_thn' ) {
                                    $plat_from_date = $this->MkCommon->filterEmptyField($dataDoc, $modelName, 'plat_from_date');
                                    $this->DocumentPayment->DocumentPaymentDetail->$modelName->Truck->set('tgl_stnk_plat', $plat_from_date);
                                }

                                $this->DocumentPayment->DocumentPaymentDetail->$modelName->Truck->set($fieldName, $document_date);
                                $this->DocumentPayment->DocumentPaymentDetail->$modelName->Truck->id = $truck_id;
                                $truckSave = $this->DocumentPayment->DocumentPaymentDetail->$modelName->Truck->save();
                                    
                                $this->DocumentPayment->DocumentPaymentDetail->$modelName->set('paid', $flagPaid);
                                $this->DocumentPayment->DocumentPaymentDetail->$modelName->id = $document_id;
                                
                                if( !$this->DocumentPayment->DocumentPaymentDetail->$modelName->save() || !$truckSave ) {
                                    $this->Log->logActivity( sprintf(__('Gagal mengubah status pembayaran surat-surat #%s'), $document_id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $document_id );
                                }
                            }
                        }

                        if( !empty($value['DocumentPayment']['total_payment']) ) {
                            $titleJournal = __('pembayaran biaya surat-surat truk');
                            $titleJournal = sprintf(__('<i>Pembatalan</i> %s'), $this->MkCommon->filterEmptyField($value, 'DocumentPayment', 'description', $titleJournal));
                            $totalPayment = $this->MkCommon->filterEmptyField($value, 'DocumentPayment', 'total_payment');

                            $this->User->Journal->setJournal($totalPayment, array(
                                'credit' => 'document_payment_coa_id',
                                'debit' => $coa_id,
                            ), array(
                                'date' => $date_payment,
                                'document_id' => $id,
                                'title' => $titleJournal,
                                'document_no' => $document_no,
                                'type' => 'document_payment_void',
                            ));
                        }

                        $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
                        $msg = array(
                            'msg' => sprintf(__('Berhasil menghapus pembayaran surat-surat truk #%s'), $noref),
                            'type' => 'success'
                        );
                        $this->MkCommon->setCustomFlash( $msg['msg'], $msg['type']);  
                        $this->Log->logActivity( sprintf(__('Berhasil menghapus pembayaran surat-surat truk #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id ); 
                    }else{
                        $this->Log->logActivity( sprintf(__('Gagal menghapus pembayaran surat-surat truk #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id ); 
                    }
                }else{
                    $msg = array(
                        'msg' => __('Harap masukkan tanggal pembatalan pembayaran.'),
                        'type' => 'error'
                    );
                }
            }

            $this->set('value', $value);
        }else{
            $msg = array(
                'msg' => __('Data tidak ditemukan'),
                'type' => 'error'
            );
        }

        $modelName = 'DocumentPayment';
        $canceled_date = !empty($this->request->data['DocumentPayment']['canceled_date']) ? $this->request->data['DocumentPayment']['canceled_date'] : false;
        $this->set(compact(
            'msg', 'is_ajax', 'action_type',
            'canceled_date', 'modelName'
        ));
        $this->render('/Elements/blocks/common/form_delete');
    }
}