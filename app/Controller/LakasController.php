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
            $refine = $this->RjLaka->processRefine($this->request->data);
            $params = $this->RjLaka->generateSearchURL($refine);
            $params['action'] = $index;

            $this->redirect($params);
        }
        $this->redirect('/');
    }

	public function index() {
        $this->loadModel('Laka');

		$this->set('active_menu', 'lakas');
		$this->set('sub_module_title', __('Data LAKA'));

        $conditions = array();
        $dateFrom = date('Y-m-d', strtotime('-6 month'));
        $dateTo = date('Y-m-d');
        
        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['nopol'])){
                $this->loadModel('Truck');
                $nopol = urldecode($refine['nopol']);
                $typeTruck = !empty($refine['type'])?$refine['type']:1;

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
                $this->request->data['Laka']['type'] = $typeTruck;
                $this->request->data['Laka']['nopol'] = $nopol;
                $conditions['Laka.truck_id'] = $truckSearch;
            }

            if(!empty($refine['date'])){
                $dateStr = urldecode($refine['date']);
                $date = explode('-', $dateStr);

                if( !empty($date) ) {
                    $date[0] = urldecode($date[0]);
                    $date[1] = urldecode($date[1]);
                    $dateStr = sprintf('%s-%s', $date[0], $date[1]);
                    $dateFrom = $this->MkCommon->getDate($date[0]);
                    $dateTo = $this->MkCommon->getDate($date[1]);
                }
            }

            if(!empty($refine['no_ttuj'])){
                $no_ttuj = urldecode($refine['no_ttuj']);
                $this->request->data['Ttuj']['no_ttuj'] = $no_ttuj;
                $conditions['Ttuj.no_ttuj LIKE '] = '%'.$no_ttuj.'%';
            }
        }

        $conditions = array_merge($conditions, array(
            'DATE_FORMAT(Laka.tgl_laka, \'%Y-%m-%d\') >='=> $dateFrom,
            'DATE_FORMAT(Laka.tgl_laka, \'%Y-%m-%d\') <=' => $dateTo,
        ));

        $this->paginate = $this->Laka->getData('paginate', array(
            'conditions' => $conditions,
            'contain' => array(
                'Ttuj'
            )
        ));
        $Lakas = $this->paginate('Laka');

        if( !empty($dateFrom) && !empty($dateTo) ) {
            $this->request->data['Laka']['date'] = sprintf('%s - %s', date('d/m/Y', strtotime($dateFrom)), date('d/m/Y', strtotime($dateTo)));
        }

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

        $driver_penganti_id = $this->MkCommon->filterEmptyField($data_local, 'Laka', 'change_driver_id');

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
                $driver_penganti_id = $this->MkCommon->filterEmptyField($ttuj_data, 'Ttuj', 'driver_penganti_id');
                $driver_name = $this->MkCommon->filterEmptyField($ttuj_data, 'Ttuj', 'driver_name');
                $data['Laka']['change_driver_id'] = $driver_penganti_id;

                if(!empty($driver_name)){
                    $data['Laka']['driver_name'] = $driver_name;
                }

                if(!empty($driver_penganti_id)){
                    $driver = $this->Ttuj->Truck->Driver->getMerge(array(), $driver_penganti_id);

                    if(!empty($driver)){
                        $driver_name = $this->MkCommon->filterEmptyField($driver, 'Driver', 'driver_name');
                        $data['Laka']['change_driver_name'] = $driver_name;
                    }
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

                    if(!empty($data_local['Laka']['ttuj_id']) && $data['Laka']['ttuj_id'] != $data_local['Laka']['ttuj_id']){
                        $this->Ttuj->id = $data_local['Laka']['ttuj_id'];
                        $this->Ttuj->set('is_laka', 0);
                        $this->Ttuj->save();
                    }

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

                        if(!empty($data['Laka']['completed']) && !empty($data['Laka']['ttuj_id'])){
                            $this->Ttuj->id = $data['Laka']['ttuj_id'];
                            $this->Ttuj->set('is_laka', 0);
                            $this->Ttuj->save();
                        }

                        $this->params['old_data'] = $data_local;
                        $this->params['data'] = $data;

                        $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s LAKA'), $msg), 'success');
                        $this->Log->logActivity( sprintf(__('Berhasil %s LAKA #%s'), $msg, $laka_id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $laka_id );
                        
                        $this->redirect(array(
                            'controller' => 'Lakas',
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

            if( !empty($this->request->data['Laka']['completed']) ) {
                $this->request->data['Laka']['completed_date'] = date('d/m/Y', strtotime($this->request->data['Laka']['completed_date']));
            }

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
                ), true, array(
                    'status' => 'all',
                ));

                if(!empty($driver_change['Driver']['driver_name'])){
                    $this->request->data['Laka']['change_driver_name'] = $driver_change['Driver']['driver_name'];
                }
            }
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
        $insurance = $this->LakaInsurance->find('list');
        $driverPengantis = $this->Truck->Driver->getData('list', array(
            'conditions' => array(
                'OR' => array(
                    'Driver.id' => $driver_penganti_id,
                    'Truck.id <>' => NULL,
                ),
            ),
            'fields' => array(
                'Driver.id', 'Driver.driver_name'
            ),
            'contain' => array(
                'Truck'
            )
        ), true, array(
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
            'ttujs', 'driverPengantis', 'id',
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
                if( !empty($locale['Laka']['ttuj_id']) ){
                    $this->loadModel('Ttuj');
                    $this->Ttuj->id = $locale['Laka']['ttuj_id'];
                    $this->Ttuj->set('is_laka', 0);
                    $this->Ttuj->save();
                }

                $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
                $this->Log->logActivity( sprintf(__('Sukses merubah status LAKA %s'), $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
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

        $dateFrom = date('Y-m-d', strtotime('-1 month'));
        $dateTo = date('Y-m-d');
        $allow_branch_id = Configure::read('__Site.config_allow_branch_id');
        $conditions = array(
            'Laka.branch_id' => $allow_branch_id,
        );

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['nopol'])){
                $nopol = urldecode($refine['nopol']);
                $typeTruck = !empty($refine['type'])?$refine['type']:1;

                $this->request->data['Laka']['type'] = $typeTruck;
                $this->request->data['Laka']['nopol'] = $nopol;

                if( $typeTruck == 2 ) {
                    $conditions ['Laka.truck_id'] = $nopol;
                } else {
                    $conditions ['Laka.nopol LIKE'] = '%'.$nopol.'%';
                }
            }

            if(!empty($refine['driver_name'])){
                $driver_name = urldecode($refine['driver_name']);
                $this->request->data['Ttuj']['driver_name'] = $driver_name;
                $conditions['CASE WHEN Driver.alias = \'\' THEN Driver.name ELSE CONCAT(Driver.name, \' ( \', Driver.alias, \' )\') END LIKE'] = '%'.$driver_name.'%';
            }

            if(!empty($refine['status'])){
                $value = urldecode($refine['status']);
                $tmpArry = array(
                    0 => 'active',
                    1 => 'completed',
                );
                $this->request->data['Laka']['status'] = $value;

                if( in_array($value, $tmpArry) ) {
                    $value = array_search($value, $tmpArry);
                    $conditions['Laka.completed'] = $value;
                }
            }

            if(!empty($refine['date'])){
                $dateStr = urldecode($refine['date']);
                $date = explode('-', $dateStr);

                if( !empty($date) ) {
                    $date[0] = urldecode($date[0]);
                    $date[1] = urldecode($date[1]);
                    $dateFrom = $this->MkCommon->getDate($date[0]);
                    $dateTo = $this->MkCommon->getDate($date[1]);
                }
                $this->request->data['Laka']['tgl_laka'] = $dateStr;
            }

            // Custom Otorisasi
            $conditions = $this->MkCommon->getConditionGroupBranch( $refine, 'TruckCustomer', $conditions, 'conditions' );
        }

        $conditions['DATE_FORMAT(Laka.tgl_laka, \'%Y-%m-%d\') >='] = $dateFrom;
        $conditions['DATE_FORMAT(Laka.tgl_laka, \'%Y-%m-%d\') <='] = $dateTo;

        $lakas = $this->Laka->getData('all', array(
            'conditions' => $conditions,
            'order' => array(
                'Laka.created' => 'ASC', 
            ),
        ));

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
        $this->set('active_menu', 'laka_repots');

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
}