<?php
App::uses('AppController', 'Controller');
class LakasController extends AppController {
	public $uses = array();

    public $components = array(
        'RjLaka', 'RjImage'
    );

    function beforeFilter() {
        parent::beforeFilter();
        $this->set('title_for_layout', __('ERP RJTM | Data LAKA'));
        $this->set('module_title', __('LAKA'));
    }

    function search( $index = 'index' ){
        $refine = array();
        if(!empty($this->request->data)) {
            $data = $this->request->data;
            $refine = $this->RjLaka->processRefine($data);
            $params = $this->RjLaka->generateSearchURL($refine);
            $params = $this->MkCommon->getRefineGroupBranch($params, $data);
            $result = $this->MkCommon->processFilter($data);
            $params['action'] = $index;

            $params = array_merge($params, $result);
            $this->redirect($params);
        }
        $this->redirect('/');
    }

	public function index() {
        $this->loadModel('Laka');

		$this->set('active_menu', 'lakas');
		$this->set('sub_module_title', __('Data LAKA'));

        $dateFrom = date('Y-m-d', strtotime('-6 month'));
        $dateTo = date('Y-m-d');

        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));

        $options =  $this->Laka->_callRefineParams($params, array(
            'contain' => array(
                'Ttuj',
            )
        ));
        $this->paginate = $this->Laka->getData('paginate', $options);
        $Lakas = $this->paginate('Laka');

        $this->set('Lakas', $Lakas);
	}

    function detail($id = false){
        if(!empty($id)){
            $Laka = $this->Laka->getLaka($id);

            if(!empty($Laka)){
                $sub_module_title = __('Detail LAKA');
                $this->set(compact('Laka', 'sub_module_title'));
            }else{
                $this->MkCommon->setCustomFlash(__('Laka tidak ditemukan.'), 'error');
                $this->redirect($this->referer());
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Laka tidak ditemukan.'), 'error');
            $this->redirect($this->referer());
        }
    }

    function add(){
        $this->set('sub_module_title', __('Tambah LAKA'));
        $this->DoLaka();
    }

    function edit($id){
        $this->loadModel('Laka');
        $this->set('sub_module_title', 'Rubah LAKA');
        $Laka = $this->Laka->getData('first', array(
            'conditions' => array(
                'Laka.id' => $id
            ),
            'contain' => array(
                'LakaDetail',
                'LakaMedias'
            )
        ));

        if(!empty($Laka)){
            $this->DoLaka($id, $Laka);
        }else{
            $this->MkCommon->setCustomFlash(__('LAKA tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'Lakas',
                'action' => 'index'
            ));
        }
    }

    function DoLaka($id = false, $data_local = false){
        $this->loadModel('Ttuj');
        $this->loadModel('City');
        $this->loadModel('Insurance');

        $driver_pengganti_id = $this->MkCommon->filterEmptyField($data_local, 'Laka', 'change_driver_id');

        if(!empty($this->request->data)){
            $data = $this->request->data;
            $ttuj_data = array();
            $data['Laka']['branch_id'] = Configure::read('__Site.config_branch_id');

            if($id && $data_local){
                $this->Laka->id = $id;
                $msg = 'merubah';
            }else{
                $this->loadModel('Laka');
                $this->Laka->create();
                $msg = 'menambah';
            }

            if(!empty($data['Laka']['truck_id'])){
                $truck = $this->Ttuj->Truck->getData('first', array(
                    'conditions' => array(
                        'Truck.id' => $data['Laka']['truck_id']
                    )
                ), true, array(
                    'plant' => true,
                ));

                if(!empty($truck)){
                    $data['Laka']['nopol'] = $truck['Truck']['nopol'];
                }
            }
            
            if( empty($data['Laka']['completed']) ) {
                $data['Laka']['complete_desc'] = '';
                $data['Laka']['completed_date'] = '';
            } else {
                $data['Laka']['completed_date'] = (!empty($data['Laka']['completed_date'])) ? $this->MkCommon->getDate($data['Laka']['completed_date']) : '';
            }

            if(!empty($data['Laka']['ttuj_id'])){
                $this->loadModel('Ttuj');
                $ttuj_data = $this->Ttuj->getData('first', array(
                    'conditions' => array(
                        'Ttuj.id' => $data['Laka']['ttuj_id']
                    ),
                ), true, array(
                    'status' => 'all',
                    'plant' => true,
                ));
                $ttuj_data = $this->Ttuj->getMergeList($ttuj_data, array(
                    'contain' => array(
                        'DriverPengganti' => array(
                            'uses' => 'Driver',
                            'primaryKey' => 'id',
                            'foreignKey' => 'driver_pengganti_id',
                            'elements' => array(
                                'branch' => false,
                            ),
                        ),
                        'Driver' => array(
                            'elements' => array(
                                'branch' => false,
                            ),
                        ),
                    ),
                ));

                $data['Laka']['change_driver_id'] = $this->MkCommon->filterEmptyField($ttuj_data, 'Ttuj', 'driver_pengganti_id');

                if(!empty($ttuj_data['Driver']['driver_name'])){
                    $data['Laka']['driver_name'] = $ttuj_data['Driver']['driver_name'];
                }

                if(!empty($ttuj_data['DriverPengganti']['driver_name'])){
                    $data['Laka']['change_driver_name'] = $ttuj_data['DriverPengganti']['driver_name'];
                }
            }

            $this->Laka->set($data);

            if($this->Laka->validates($data)){
                $data['LakaDetail']['date_birth'] = (!empty($data['LakaDetail']['date_birth'])) ? $this->MkCommon->getDate($data['LakaDetail']['date_birth']) : '';
                $data['Laka']['tgl_laka'] = (!empty($data['Laka']['tgl_laka'])) ? $this->MkCommon->getDate($data['Laka']['tgl_laka']) : '';
                $data['Laka']['completeness'] = (!empty($data['Laka']['completeness'])) ? serialize($data['Laka']['completeness']) : '';
                $data['Laka']['completeness_insurance'] = (!empty($data['Laka']['completeness_insurance'])) ? serialize($data['Laka']['completeness_insurance']) : '';

                if(!empty($data['LakaMedias']['name'])){
                    foreach ($data['LakaMedias']['name'] as $key => $temp_image) {
                        $uploaded = $this->RjImage->upload($temp_image, '/'.Configure::read('__Site.laka_photo_folder').'/', String::uuid());

                        if(!empty($uploaded)) {
                            if(!$uploaded['error']) {
                                $data['LakaMedias']['name'][$key] = $uploaded['imageName'];
                            }else{
                                unset($data['LakaMedias']['name'][$key]);
                            }
                        }
                    }
                }

                if($this->Laka->save($data)){
                    $laka_id = $this->Laka->id;
                    $noref = str_pad($laka_id, 6, '0', STR_PAD_LEFT);

                    // if(!empty($data_local['Laka']['ttuj_id']) && $data['Laka']['ttuj_id'] != $data_local['Laka']['ttuj_id']){
                    //     $this->Ttuj->id = $data_local['Laka']['ttuj_id'];
                    //     $this->Ttuj->set('is_laka', 0);
                    //     $this->Ttuj->save();
                    // }

                    if(empty($id) && empty($data_local)){
                        $data['LakaDetail']['laka_id'] = $laka_id;
                        $this->Laka->LakaDetail->create();
                    }else{
                        $laka_detail = $this->Laka->LakaDetail->getData('first', array(
                            'conditions' => array(
                                'LakaDetail.laka_id' => $id
                            )
                        ));
                        $this->Laka->LakaDetail->id = $laka_detail['LakaDetail']['id'];
                    }

                    $this->Laka->LakaDetail->set($data);

                    if( $this->Laka->LakaDetail->save() ) {
                        if(!empty($data['LakaMedias']['name'])){
                            foreach ($data['LakaMedias']['name'] as $key => $value) {
                                $this->Laka->LakaMedias->create();
                                $this->Laka->LakaMedias->set(array(
                                    'laka_id' => $laka_id,
                                    'name' => $value,
                                    'status' => 1
                                ));
                                $this->Laka->LakaMedias->save();
                            }
                        }

                        /*kalo belum bongkaran, ttuj jadi ngga aktif*/
                        if(!empty($ttuj_data) && $ttuj_data['Ttuj']['is_bongkaran'] == 0){
                            $this->Ttuj->id = $ttuj_data['Ttuj']['id'];
                            $this->Ttuj->set('is_laka', 1);
                            $this->Ttuj->save();
                        }

                        // if(!empty($data['Laka']['completed']) && !empty($data['Laka']['ttuj_id'])){
                        //     $this->Ttuj->id = $data['Laka']['ttuj_id'];
                        //     $this->Ttuj->set('is_laka', 0);
                        //     $this->Ttuj->save();
                        // }

                        $this->params['old_data'] = $data_local;
                        $this->params['data'] = $data;

                        $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s LAKA #%s'), $msg, $noref), 'success');
                        $this->Log->logActivity( sprintf(__('Berhasil %s LAKA #%s'), $msg, $laka_id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $laka_id );
                        
                        $this->redirect(array(
                            'controller' => 'lakas',
                            'action' => 'index',
                        ));
                    } else {
                        $step = 'step2';
                        $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s LAKA'), $msg), 'error');
                        $this->Log->logActivity( sprintf(__('Gagal %s LAKA #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
                    }
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s LAKA'), $msg), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal %s LAKA #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
                }
            }else{
                $validationErrors = $this->Laka->validationErrors;
                
                if( !empty($validationErrors['truck_id']) || !empty($validationErrors['lokasi_laka']) || !empty($validationErrors['status_muatan']) || !empty($validationErrors['driver_condition']) || !empty($validationErrors['truck_condition']) ) {
                    $step = 'step1';
                } else if( !empty($validationErrors['description_laka']) || !empty($validationErrors['complete_desc']) || !empty($validationErrors['completed_date']) ) {
                    $step = 'step2';
                }

                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s LAKA'), $msg), 'error');
            }

            $this->request->data = $data;
        } else if($id && $data_local){
            $this->request->data= $data_local;
            
            $this->request->data['Laka']['completeness'] = !empty($this->request->data['Laka']['completeness']) ? unserialize($this->request->data['Laka']['completeness']) : '';
            $this->request->data['Laka']['completeness_insurance'] = !empty($this->request->data['Laka']['completeness_insurance']) ? unserialize($this->request->data['Laka']['completeness_insurance']) : '';
            $this->request->data['Laka']['tgl_laka'] = (!empty($this->request->data['Laka']['tgl_laka']) ? $this->MkCommon->customDate($this->request->data['Laka']['tgl_laka'], 'd/m/Y') : '' );
            $this->request->data['LakaDetail']['date_birth'] = (!empty($this->request->data['LakaDetail']['date_birth'])) ? $this->MkCommon->getDate($this->request->data['LakaDetail']['date_birth'], true) : '';

            if( !empty($this->request->data['Laka']['from_city_id']) ) {
                $this->request->data['Laka']['from_city_name'] = $this->City->getCity( $this->request->data['Laka']['from_city_id'], 'name' );
            }

            if( !empty($this->request->data['Laka']['to_city_id']) ) {
                $this->request->data['Laka']['to_city_name'] = $this->City->getCity( $this->request->data['Laka']['to_city_id'], 'name' );
            }

            if(!empty($this->request->data['Laka']['change_driver_id'])){
                $this->loadModel('Driver');
                $driver_change = $this->Driver->getData('first', array(
                    'conditions' => array(
                        'Driver.id' => $this->request->data['Laka']['change_driver_id']
                    )
                ), array(
                    'status' => 'all',
                    'branch' => false,
                ));

                if(!empty($driver_change['Driver']['driver_name'])){
                    $this->request->data['Laka']['change_driver_name'] = $driver_change['Driver']['driver_name'];
                }
            }
        }

        if( !empty($this->request->data['Laka']['completed_date']) ) {
            $this->request->data['Laka']['completed_date'] = date('d/m/Y', strtotime($this->request->data['Laka']['completed_date']));
        }

        $this->loadModel('Truck');
        $this->loadModel('LakaMaterial');
        $this->loadModel('LakaInsurance');

        $conditionsTruck = array(
            'OR' => array(
                array(
                    'Laka.id' => NULL
                ),
                array(
                    'Laka.truck_id' => !empty($data_local['Laka']['truck_id']) ? $data_local['Laka']['truck_id'] : false
                )
            )
        );
        $trucks = $this->Truck->getData('all', array(
            'conditions' => $conditionsTruck,
            'contain' => array(
                'Laka'
            ),
            'order' => array(
                'Truck.nopol' => 'ASC',
            ),
        ), true, array(
            'plant' => true,
        ));
        $result = array();

        if(!empty($trucks)){
            foreach ($trucks as $key => $value) {
                $truck_id = $this->MkCommon->filterEmptyField($value, 'Truck', 'id');
                $driver_id = $this->MkCommon->filterEmptyField($value, 'Truck', 'driver_id');
                $truckName = $this->MkCommon->filterEmptyField($value, 'Truck', 'nopol');

                $value = $this->Truck->Driver->getMerge($value, $driver_id);
                $driver_name = $this->MkCommon->filterEmptyField($value, 'Driver', 'name');

                if( !empty($driver_name) ) {
                    $truckName = sprintf('%s (%s)', $truckName, $driver_name);
                }

                $result[$truck_id] = $truckName;
            }
        }

        $ttujs = array();
        $trucks = $result;
        $material = $this->LakaMaterial->find('list');
        $insurance = $this->Insurance->getData('list', array(
            'status' => 'publish',
        ));
        $driverPenggantis = $this->Truck->Driver->getData('list', array(
            'conditions' => array(
                'OR' => array(
                    'Driver.id' => $driver_pengganti_id,
                    'Truck.id <>' => NULL,
                ),
            ),
            'fields' => array(
                'Driver.id', 'Driver.driver_name'
            ),
            'contain' => array(
                'Truck'
            )
        ), array(
            'plant' => true,
        ));

        if(!empty($this->request->data['Laka']['truck_id'])){
            $ttujs = $this->Ttuj->getData('list', array(
                'conditions' => array(
                    'Ttuj.is_pool <>' => 1,
                    'Ttuj.is_draft' => 0,
                    'Ttuj.truck_id' => $this->request->data['Laka']['truck_id'],
                ),
                'fields' => array(
                    'Ttuj.id', 'Ttuj.no_ttuj'
                ),
            ), true, array(
                'plant' => true,
            ));
        }

        $this->MkCommon->_layout_file('select');
        $this->set('active_menu', 'lakas');
        $this->set(compact(
            'material', 'insurance', 'step', 
            'ttujs', 'driverPenggantis', 'id',
            'trucks'
        ));
        $this->render('laka_form');
    }

    function toggle($id){
        $this->loadModel('Laka');
        $locale = $this->Laka->getData('first', array(
            'conditions' => array(
                'Laka.id' => $id
            )
        ));

        if($locale){
            $value = true;
            if($locale['Laka']['status']){
                $value = false;
            }

            $this->Laka->id = $id;
            $this->Laka->set('status', 0);

            if($this->Laka->save()){
                $noref = str_pad($id, 6, '0', STR_PAD_LEFT);

                if( !empty($locale['Laka']['ttuj_id']) ){
                    $this->loadModel('Ttuj');
                    $this->Ttuj->id = $locale['Laka']['ttuj_id'];
                    $this->Ttuj->set('is_laka', 0);
                    $this->Ttuj->save();
                }

                $this->MkCommon->setCustomFlash(sprintf(__('Sukses menghapus data LAKA #%s'), $noref), 'success');
                $this->Log->logActivity( sprintf(__('Sukses menghapus data LAKA %s'), $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal merubah status LAKA %s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Laka tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    public function reports( $data_action = false ) {
        $this->loadModel('Laka');

        $allow_branch_id = Configure::read('__Site.config_allow_branch_id');
        $dateFrom = date('Y-m-d', strtotime('-1 month'));
        $dateTo = date('Y-m-d');

        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));

        $dateFrom = $this->MkCommon->filterEmptyField($params, 'named', 'DateFrom');
        $dateTo = $this->MkCommon->filterEmptyField($params, 'named', 'DateTo');
        
        $options =  $this->Laka->_callRefineParams($params, array(
            'conditions' => array(
                'Laka.branch_id' => $allow_branch_id,
            ),
            'order' => array(
                'Laka.created' => 'ASC', 
            ),
        ));

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];
            // Custom Otorisasi
            $options = $this->MkCommon->getConditionGroupBranch( $refine, 'Laka', $options );
        }

        if( !empty($data_action) ) {
            $lakas = $this->Laka->getData('all', $options);
        } else {
            $this->paginate = $this->Laka->getData('paginate', $options);
            $lakas = $this->paginate('Laka');
        }

        if( !empty($lakas) ) {
            $this->loadModel('LakaInsurance');

            foreach ($lakas as $key => $value) {
                $ttuj_id = $this->MkCommon->filterEmptyField($value, 'Laka', 'ttuj_id');
                $truck_id = $this->MkCommon->filterEmptyField($value, 'Laka', 'truck_id');
                $branch_id = $this->MkCommon->filterEmptyField($value, 'Laka', 'branch_id');
                $insurances = $this->MkCommon->filterEmptyField($value, 'Laka', 'completeness_insurance');

                $customInsurances = unserialize($insurances);
                $customInsurances = array_filter($customInsurances);
                $customInsurances = array_keys($customInsurances);
                $value['Laka']['insurances'] = $customInsurances;

                $value = $this->Laka->Ttuj->Truck->getMerge($value, $truck_id);
                $category_id = $this->MkCommon->filterEmptyField($value, 'Truck', 'truck_category_id');


                $value = $this->Laka->Ttuj->Truck->TruckCategory->getMerge($value, $category_id);
                $value = $this->Laka->Ttuj->getMerge($value, $ttuj_id);
                $value = $this->GroupBranch->Branch->getMerge($value, $branch_id);

                $lakas[$key] = $value;
            }
        }

        $module_title = __('Laporan Laka');

        if( !empty($dateFrom) && !empty($dateTo) ) {
            $this->request->data['Laka']['date'] = sprintf('%s - %s', date('d/m/Y', strtotime($dateFrom)), date('d/m/Y', strtotime($dateTo)));
            $module_title .= sprintf(' Periode %s', $this->MkCommon->getCombineDate($dateFrom, $dateTo));
        }

        $this->set('sub_module_title', $module_title);
        $this->set('active_menu', 'laka_reports');

        $this->set(compact(
            'lakas', 'cities', 'data_action'
        ));

        if($data_action == 'pdf'){
            $this->layout = 'pdf';
        }else if($data_action == 'excel'){
            $this->layout = 'ajax';
        } else {
            $this->MkCommon->_layout_file('freeze');
        }
    }

    function payments(){
        $this->loadModel('LakaPayment');
        $options = array(
            'order' => array(
                'LakaPayment.created' => 'DESC',
                'LakaPayment.id' => 'DESC',
            ),
        );

        $this->set('active_menu', 'laka_payments');
        $this->set('sub_module_title', __('Pembayaran LAKA'));
        
        $dateFrom = date('Y-m-d', strtotime('-1 Month'));
        $dateTo = date('Y-m-d');

        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));
        $options =  $this->LakaPayment->_callRefineParams($params, $options);

        $this->paginate = $this->LakaPayment->getData('paginate', $options);
        $values = $this->paginate('LakaPayment');

        $this->set(compact(
            'values'
        )); 
    }

    function payment_add(){
        $this->loadModel('LakaPayment');
        $module_title = __('Tambah Pembayaran LAKA');
        $this->set('sub_module_title', $module_title);

        $this->doLakaPayment();
    }

    function payment_edit( $id = false ){
        $this->loadModel('LakaPayment');
        $module_title = __('Edit Pembayaran LAKA');
        $this->set('sub_module_title', $module_title);

        $head_office = Configure::read('__Site.config_branch_head_office');

        if( !empty($head_office) ) {
            $elementRevenue = array(
                'branch' => false,
            );
        } else {
            $elementRevenue = false;
        }

        $value = $this->LakaPayment->getData('first', array(
            'conditions' => array(
                'LakaPayment.id' => $id
            ),
        ), $elementRevenue);
        $value = $this->LakaPayment->LakaPaymentDetail->getMerge($value, $id);

        $this->doLakaPayment( $id, $value );
    }

    function doLakaPayment( $id = false, $value = false ){
        $this->set('active_menu', 'laka_payments');

        if(!empty($this->request->data)){
            $data = $this->request->data;
            $data = $this->MkCommon->dataConverter($data, array(
                'date' => array(
                    'LakaPayment' => array(
                        'date_payment',
                    ),
                )
            ));
            $this->MkCommon->_callAllowClosing($data, 'LakaPayment', 'date_payment');

            $data['LakaPayment']['branch_id'] = Configure::read('__Site.config_branch_id');

            $dataAmount = $this->MkCommon->filterEmptyField($data, 'LakaPaymentDetail', 'amount');
            $flagPaymentDetail = $this->doLakaPaymentDetail($dataAmount, $data);

            if( !empty($id) ) {
                $this->LakaPayment->id = $id;
            } else {
                $this->LakaPayment->create();
            }

            $this->LakaPayment->set($data);

            if( $this->LakaPayment->validates() && !empty($flagPaymentDetail) ){
                if($this->LakaPayment->save()){
                    $laka_id = $this->LakaPayment->id;
                    $flagPaymentDetail = $this->doLakaPaymentDetail($dataAmount, $data, $laka_id);

                    $this->params['old_data'] = $value;
                    $this->params['data'] = $data;

                    $noref = str_pad($laka_id, 6, '0', STR_PAD_LEFT);
                    $this->MkCommon->setCustomFlash(sprintf(__('Berhasil melakukan Pembayaran LAKA #%s'), $noref), 'success'); 
                    $this->Log->logActivity( sprintf(__('Berhasil melakukan Pembayaran LAKA #%s'), $laka_id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $laka_id );
                    
                    $this->redirect(array(
                        'action' => 'payments',
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal melakukan Pembayaran LAKA'), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal melakukan Pembayaran LAKA #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
                }
            }else{
                $msgError = array();

                if( !empty($this->LakaPayment->LakaPaymentDetail->validationErrors) ) {
                    $errorPaymentDetails = $this->LakaPayment->LakaPaymentDetail->validationErrors;

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
                    $this->MkCommon->setCustomFlash(__('Gagal melakukan Pembayaran LAKA'), 'error'); 
                }
            }

            $this->request->data['LakaPayment']['date_payment'] = !empty($data['LakaPayment']['date_payment']) ? $data['LakaPayment']['date_payment'] : '';
        } else if( !empty($value) ) {
            if( !empty($value['LakaPaymentDetail']) ) {
                foreach ($value['LakaPaymentDetail'] as $key => $val) {
                    $laka_id = $this->MkCommon->filterEmptyField($val, 'LakaPaymentDetail', 'laka_id');
                    $amount = $this->MkCommon->filterEmptyField($val, 'LakaPaymentDetail', 'amount');

                    $laka = $this->LakaPayment->LakaPaymentDetail->Laka->getMerge(array(), $laka_id);

                    $this->request->data['Laka'][$key] = $laka;
                    $this->request->data['LakaPaymentDetail']['amount'][$key] = $amount;
                    $this->request->data['LakaPaymentDetail']['laka_id'][$key] = $laka_id;
                }
            }

            $this->request->data['LakaPayment'] = $this->MkCommon->filterEmptyField($value, 'LakaPayment');
        }

        $coas = $this->GroupBranch->Branch->BranchCoa->getCoas();
        $cogs = $this->MkCommon->_callCogsOptGroup('LakaPayment');

        $this->MkCommon->_layout_file('select');
        $this->set(compact(
            'id', 'coas'
        ));
        $this->render('payment_add');
    }

    function doLakaPaymentDetail ( $dataAmount, $data, $laka_payment_id = false ) {
        $flagPaymentDetail = true;
        $totalPayment = 0;
        $date_payment = $this->MkCommon->filterEmptyField($data, 'LakaPayment', 'date_payment');
        $data = $this->request->data;

        if( !empty($laka_payment_id) ) {
            $this->LakaPayment->LakaPaymentDetail->updateAll( array(
                'LakaPaymentDetail.status' => 0,
            ), array(
                'LakaPaymentDetail.laka_payment_id' => $laka_payment_id,
            ));
        }


        if( !empty($dataAmount) ) {
            foreach ($dataAmount as $key => $amount) {
                $laka_id = !empty($data['LakaPaymentDetail']['laka_id'][$key])?$data['LakaPaymentDetail']['laka_id'][$key]:false;
                $amount = !empty($amount)?$this->MkCommon->convertPriceToString($amount, 0):0;

                $value = $this->LakaPayment->LakaPaymentDetail->Laka->getMerge(array(), $laka_id);
                
                $truck_id = $this->MkCommon->filterEmptyField($value, 'Laka', 'truck_id');
                $price = $this->MkCommon->filterEmptyField($value, 'Laka', 'price');
                $denda = $this->MkCommon->filterEmptyField($value, 'Laka', 'denda');
                $biaya_lain = $this->MkCommon->filterEmptyField($value, 'Laka', 'biaya_lain');
                $laka_date = $this->MkCommon->filterEmptyField($value, 'Laka', 'to_date');

                $dataPaymentDetail = array(
                    'LakaPaymentDetail' => array(
                        'truck_id' => $truck_id,
                        'laka_id' => $laka_id,
                        'amount' => $amount,
                    ),
                );

                $totalPayment += $amount;
                $total_dibayar = $this->LakaPayment->LakaPaymentDetail->getTotalPayment($laka_id) + $amount;
                $this->request->data['Laka'][$key]['Laka'] = !empty($value['Laka'])?$value['Laka']:false;

                if( !empty($laka_payment_id) ) {
                    $dataPaymentDetail['LakaPaymentDetail']['laka_payment_id'] = $laka_payment_id;
                    $total = $price + $denda + $biaya_lain;

                    if( !empty($total_dibayar) ) {
                        $flagPaid = 'half';

                        if( $total <= $total_dibayar ) {
                            $flagPaid = 'full';
                        }
                    
                        $this->LakaPayment->LakaPaymentDetail->Laka->set('paid', $flagPaid);
                        $this->LakaPayment->LakaPaymentDetail->Laka->id = $laka_id;

                        if( !$this->LakaPayment->LakaPaymentDetail->Laka->save() ) {
                            $this->Log->logActivity( sprintf(__('Gagal mengubah status pembayaran Surat-surat #%s'), $laka_id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $laka_id );
                        }
                    }
                }

                $this->LakaPayment->LakaPaymentDetail->create();
                $this->LakaPayment->LakaPaymentDetail->set($dataPaymentDetail);

                if( !empty($laka_payment_id) ) {
                    if( !$this->LakaPayment->LakaPaymentDetail->save() ) {
                        $flagPaymentDetail = false;
                    }
                } else {
                    if( !$this->LakaPayment->LakaPaymentDetail->validates() ) {
                        $flagPaymentDetail = false;
                    }
                }
            }
        } else {
            $flagPaymentDetail = false;
            $this->MkCommon->setCustomFlash(__('Mohon pilih biaya yang akan dibayar.'), 'error'); 
        }

        if( !empty($totalPayment) && !empty($laka_payment_id) ) {
            $this->LakaPayment->id = $laka_payment_id;
            $this->LakaPayment->set('total_payment', $totalPayment);

            if( !$this->LakaPayment->save() ) {
                $this->Log->logActivity( sprintf(__('Gagal mengubah total pembayaran Surat-surat #%s'), $laka_payment_id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $laka_payment_id );
            } else {
                $laka_no = $this->MkCommon->filterEmptyField($data, 'LakaPayment', 'nodoc');
                $coa_id = $this->MkCommon->filterEmptyField($data, 'LakaPayment', 'coa_id');

                $titleJournal = sprintf(__('Pembayaran Surat-surat Truk'));
                $titleJournal = $this->MkCommon->filterEmptyField($data, 'LakaPayment', 'description', $titleJournal);

                $this->User->Journal->deleteJournal($laka_payment_id, array(
                    'laka_payment',
                ));
                $this->User->Journal->setJournal($totalPayment, array(
                    'credit' => $coa_id,
                    'debit' => 'laka_payment_coa_id',
                ), array(
                    'date' => $date_payment,
                    'document_id' => $laka_payment_id,
                    'title' => $titleJournal,
                    'document_no' => $laka_no,
                    'type' => 'laka_payment',
                ));
            }
        }

        return $flagPaymentDetail;
    }

    function payment_detail($id = false){
        $this->loadModel('LakaPayment');
        $module_title = __('Kas/Bank');
        $elementRevenue = false;
        $head_office = Configure::read('__Site.config_branch_head_office');

        if( !empty($head_office) ) {
            $elementRevenue = array(
                'branch' => false,
            );
        }

        $value = $this->LakaPayment->getData('first', array(
            'conditions' => array(
                'LakaPayment.id' => $id
            ),
        ), $elementRevenue);

        $this->set('active_menu', 'laka_payments');
        $sub_module_title = $title_for_layout = 'Detail Pembayaran Surat-surat Truk';

        if(!empty($value)){
            $coa_id = $this->MkCommon->filterEmptyField($value, 'LakaPayment', 'coa_id');

            $value = $this->User->Journal->Coa->getMerge($value, $coa_id);
            $value = $this->LakaPayment->LakaPaymentDetail->getMerge($value, $id);

            if( !empty($value['LakaPaymentDetail']) ) {
                foreach ($value['LakaPaymentDetail'] as $key => $val) {
                    $laka_id = $this->MkCommon->filterEmptyField($val, 'LakaPaymentDetail', 'laka_id');
                    $amount = $this->MkCommon->filterEmptyField($val, 'LakaPaymentDetail', 'amount');

                    $laka = $this->LakaPayment->LakaPaymentDetail->Laka->getMerge(array(), $laka_id);

                    $value['Laka'][$key] = $laka;
                    $value['LakaPaymentDetail']['amount'][$key] = $amount;
                    $value['LakaPaymentDetail']['laka_id'][$key] = $laka_id;
                }
            }

            $this->request->data = $value;

            $this->MkCommon->_layout_file('select');
            $this->set('view', true);
            $this->set(compact(
                'value', 'sub_module_title', 'title_for_layout',
                'module_title'
            ));

            $this->render('payment_add');
        }else{
            $this->MkCommon->setCustomFlash(__('Data tidak ditemukan'), 'error');
            $this->redirect($this->referer());
        }
    }

    function payment_delete($id = false){
        $this->loadModel('LakaPayment');
        $is_ajax = $this->RequestHandler->isAjax();
        $msg = array(
            'msg' => '',
            'type' => 'error'
        );
        $value = $this->LakaPayment->getData('first', array(
            'conditions' => array(
                'LakaPayment.id' => $id,
            ),
        ));

        if( !empty($value) ){
            $this->MkCommon->_callAllowClosing($value, 'LakaPayment', 'date_payment');
            
            if(!empty($this->request->data)){
                $data = $this->request->data;
                $data = $this->MkCommon->dataConverter($data, array(
                    'date' => array(
                        'LakaPayment' => array(
                            'canceled_date',
                        ),
                    )
                ));

                $value = $this->LakaPayment->LakaPaymentDetail->getMerge($value, $id);
                $date_payment = $this->MkCommon->filterEmptyField($value, 'LakaPayment', 'date_payment');

                if(!empty($data['LakaPayment']['canceled_date'])){
                    $data['LakaPayment']['canceled_date'] = $this->MkCommon->filterEmptyField($data, 'LakaPayment', 'canceled_date');
                    $data['LakaPayment']['is_canceled'] = 1;

                    $this->LakaPayment->id = $id;
                    $this->LakaPayment->set($data);

                    if($this->LakaPayment->save()){
                        $laka_no = $this->MkCommon->filterEmptyField($value, 'LakaPayment', 'nodoc');
                        $coa_id = $this->MkCommon->filterEmptyField($value, 'LakaPayment', 'coa_id');

                        if( !empty($value['LakaPaymentDetail']) ) {
                            foreach ($value['LakaPaymentDetail'] as $key => $detail) {
                                $laka_id = $this->MkCommon->filterEmptyField($detail, 'LakaPaymentDetail', 'laka_id');
                                $total_dibayar = $this->LakaPayment->LakaPaymentDetail->getTotalPayment($laka_id);
                                $flagPaid = 'none';

                                if( !empty($total_dibayar) ) {
                                    $flagPaid = 'half';
                                }
                                
                                $dataDoc = $this->LakaPayment->LakaPaymentDetail->Laka->getMerge(array(), $laka_id);
                                $laka_date = $this->MkCommon->filterEmptyField($dataDoc, 'Laka', 'from_date');
                                $truck_id = $this->MkCommon->filterEmptyField($dataDoc, 'Laka', 'truck_id');

                                $this->LakaPayment->LakaPaymentDetail->Laka->set('paid', $flagPaid);
                                $this->LakaPayment->LakaPaymentDetail->Laka->id = $laka_id;
                                
                                if( !$this->LakaPayment->LakaPaymentDetail->Laka->save() ) {
                                    $this->Log->logActivity( sprintf(__('Gagal mengubah status pembayaran surat-surat #%s'), $laka_id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $laka_id );
                                }
                            }
                        }

                        if( !empty($value['LakaPayment']['total_payment']) ) {
                            $titleJournal = __('pembayaran biaya surat-surat truk');
                            $titleJournal = sprintf(__('<i>Pembatalan</i> %s'), $this->MkCommon->filterEmptyField($value, 'LakaPayment', 'description', $titleJournal));
                            $totalPayment = $this->MkCommon->filterEmptyField($value, 'LakaPayment', 'total_payment');

                            $this->User->Journal->setJournal($totalPayment, array(
                                'credit' => 'laka_payment_coa_id',
                                'debit' => $coa_id,
                            ), array(
                                'date' => $date_payment,
                                'document_id' => $id,
                                'title' => $titleJournal,
                                'document_no' => $laka_no,
                                'type' => 'laka_payment_void',
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

        $modelName = 'LakaPayment';
        $canceled_date = !empty($this->request->data['LakaPayment']['canceled_date']) ? $this->request->data['LakaPayment']['canceled_date'] : false;
        $this->set(compact(
            'msg', 'is_ajax', 'action_type',
            'canceled_date', 'modelName'
        ));
        $this->render('/Elements/blocks/common/form_delete');
    }
}