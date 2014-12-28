<?php
App::uses('AppController', 'Controller');
class RevenuesController extends AppController {
	public $uses = array();

    public $components = array(
        'RjRevenue'
    );

    function beforeFilter() {
        parent::beforeFilter();
        $this->set('title_for_layout', __('ERP RJTM | Revenue'));
        $this->set('module_title', __('Revenue'));
    }

    function search( $index = 'index' ){
        $refine = array();
        if(!empty($this->request->data)) {
            $refine = $this->RjRevenue->processRefine($this->request->data);
            $params = $this->RjRevenue->generateSearchURL($refine);
            $params['action'] = $index;

            $this->redirect($params);
        }
        $this->redirect('/');
    }

	public function ttuj() {
        $this->loadModel('Ttuj');
		$this->set('active_menu', 'ttuj');
		$this->set('sub_module_title', __('TTUJ'));

        $conditions = array();
        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['nopol'])){
                $nopol = urldecode($refine['nopol']);
                $this->request->data['Truck']['nopol'] = $nopol;
                $conditions['Truck.nopol LIKE '] = '%'.$nopol.'%';
            }
        }

        $this->paginate = $this->Ttuj->getData('paginate', array(
            'conditions' => $conditions
        ));
        $ttujs = $this->paginate('Ttuj');

        $this->set('ttujs', $ttujs);
	}

    function ttuj_add(){
        $this->loadModel('Ttuj');
        $this->set('sub_module_title', __('Tambah TTUJ'));
        $this->doTTUJ();
    }

    function ttuj_edit($id){
        $this->loadModel('Ttuj');
        $this->set('sub_module_title', 'Rubah TTUJ');
        $ttuj = $this->Ttuj->getData('first', array(
            'conditions' => array(
                'Ttuj.id' => $id
            )
        ));

        if(!empty($ttuj)){
            $this->doTTUJ($id, $ttuj);
        }else{
            $this->MkCommon->setCustomFlash(__('TTUJ tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'revenues',
                'action' => 'ttuj'
            ));
        }
    }

    function saveTtujTipeMotor ( $dataTtujTipeMotor = false, $data = false, $ttuj_id = false ) {
        $result = array(
            'validates' => true,
            'data' => false,
        );

        if( !empty($dataTtujTipeMotor) ) {
            if( !empty($ttuj_id) ) {
                $this->Ttuj->TtujTipeMotor->updateAll( array(
                    'TtujTipeMotor.status' => 0,
                ), array(
                    'TtujTipeMotor.ttuj_id' => $ttuj_id,
                ));
            }

            foreach ($dataTtujTipeMotor as $key => $tipe_motor_id) {
                $dataValidate['TtujTipeMotor']['tipe_motor_id'] = $tipe_motor_id;
                $dataValidate['TtujTipeMotor']['qty'] = !empty($data['TtujTipeMotor']['qty'][$key])?$data['TtujTipeMotor']['qty'][$key]:false;
                $this->Ttuj->TtujTipeMotor->set($dataValidate);

                if( !empty($ttuj_id) ) {
                    $dataValidate['TtujTipeMotor']['ttuj_id'] = $ttuj_id;
                    $this->Ttuj->TtujTipeMotor->create();
                    $this->Ttuj->TtujTipeMotor->save($dataValidate);
                } else {
                    if(!$this->Ttuj->TtujTipeMotor->validates($dataValidate)){
                        $result['validates'] = false;
                    } else {
                        $result['data'][$key] = $dataValidate;
                    }
                }
            }
        }

        return $result;
    }

    function saveTtujPerlengkapan ( $dataTtujPerlengkapan = false, $data = false, $ttuj_id = false ) {
        $result = array(
            'validates' => true,
            'data' => false,
        );

        if( !empty($dataTtujPerlengkapan) ) {
            if( !empty($ttuj_id) ) {
                $this->Ttuj->TtujPerlengkapan->updateAll( array(
                    'TtujPerlengkapan.status' => 0,
                ), array(
                    'TtujPerlengkapan.ttuj_id' => $ttuj_id,
                ));
            }

            foreach ($dataTtujPerlengkapan as $key => $qty) {
                $dataValidate['TtujPerlengkapan']['qty'] = $qty;
                $dataValidate['TtujPerlengkapan']['perlengkapan_id'] = !empty($data['TtujPerlengkapan']['id'][$key])?$data['TtujPerlengkapan']['id'][$key]:false;
                $this->Ttuj->TtujPerlengkapan->set($dataValidate);

                if( !empty($ttuj_id) ) {
                    $dataValidate['TtujPerlengkapan']['ttuj_id'] = $ttuj_id;

                    $this->Ttuj->TtujPerlengkapan->create();
                    $this->Ttuj->TtujPerlengkapan->save($dataValidate);
                } else {
                    if(!$this->Ttuj->TtujPerlengkapan->validates($dataValidate)){
                        $result['validates'] = false;
                    } else {
                        $result['data'][$key] = $dataValidate;
                    }
                }
            }
        }

        return $result;
    }

    function doTTUJ($id = false, $data_local = false){
        $this->loadModel('UangJalan');
        $this->loadModel('TipeMotor');
        $this->loadModel('Perlengkapan');
        $this->loadModel('Truck');
        $step = false;
        $is_draft = isset($data_local['Ttuj']['is_draft'])?$data_local['Ttuj']['is_draft']:true;

        if( !empty($this->request->data) && $is_draft ){
            $data = $this->request->data;
            if($id && $data_local){
                $this->Ttuj->id = $id;
                $msg = 'merubah';
            }else{
                $this->Ttuj->create();
                $msg = 'menambah';
            }

            $customer_id = !empty($data['Ttuj']['customer_id'])?$data['Ttuj']['customer_id']:false;
            $from_city_id = !empty($data['Ttuj']['from_city_id'])?$data['Ttuj']['from_city_id']:false;
            $to_city_id = !empty($data['Ttuj']['to_city_id'])?$data['Ttuj']['to_city_id']:false;
            $truck_id = !empty($data['Ttuj']['truck_id'])?$data['Ttuj']['truck_id']:false;

            $uangJalan = $this->UangJalan->getData('first', array(
                'conditions' => array(
                    'UangJalan.status' => 1,
                    'UangJalan.customer_id' => $customer_id,
                    'UangJalan.from_city_id' => $from_city_id,
                    'UangJalan.to_city_id' => $to_city_id,
                ),
            ));
            $truck = $this->Truck->getData('first', array(
                'conditions' => array(
                    'Truck.status' => 1,
                    'Truck.id' => $truck_id,
                ),
                'fields' => array(
                    'Truck.id', 'Truck.nopol'
                ),
            ));

            $data['Ttuj']['from_city_name'] = !empty($uangJalan['FromCity']['name'])?$uangJalan['FromCity']['name']:0;
            $data['Ttuj']['to_city_name'] = !empty($uangJalan['ToCity']['name'])?$uangJalan['ToCity']['name']:0;
            $data['Ttuj']['customer_name'] = !empty($uangJalan['Customer']['name'])?$uangJalan['Customer']['name']:0;
            $data['Ttuj']['uang_jalan_id'] = !empty($uangJalan['UangJalan']['id'])?$uangJalan['UangJalan']['id']:false;
            $data['Ttuj']['nopol'] = !empty($truck['Truck']['nopol'])?$truck['Truck']['nopol']:false;
            $data['Ttuj']['ttuj_date'] = !empty($data['Ttuj']['ttuj_date'])?date('Y-m-d', strtotime($data['Ttuj']['ttuj_date'])):false;
            $data['Ttuj']['driver_penganti_id'] = !empty($data['Ttuj']['driver_penganti_id'])?$data['Ttuj']['driver_penganti_id']:0;
            $data['Ttuj']['uang_jalan_1'] = !empty($data['Ttuj']['uang_jalan_1'])?str_replace(array(',', ' '), array('', ''), $data['Ttuj']['uang_jalan_1']):0;
            $data['Ttuj']['uang_jalan_2'] = !empty($data['Ttuj']['uang_jalan_2'])?str_replace(array(',', ' '), array('', ''), $data['Ttuj']['uang_jalan_2']):0;
            $data['Ttuj']['uang_kuli_muat'] = !empty($data['Ttuj']['uang_kuli_muat'])?str_replace(array(',', ' '), array('', ''), $data['Ttuj']['uang_kuli_muat']):0;
            $data['Ttuj']['uang_kuli_bongkar'] = !empty($data['Ttuj']['uang_kuli_bongkar'])?str_replace(array(',', ' '), array('', ''), $data['Ttuj']['uang_kuli_bongkar']):0;
            $data['Ttuj']['asdp'] = !empty($data['Ttuj']['asdp'])?str_replace(array(',', ' '), array('', ''), $data['Ttuj']['asdp']):0;
            $data['Ttuj']['uang_kawal'] = !empty($data['Ttuj']['uang_kawal'])?str_replace(array(',', ' '), array('', ''), $data['Ttuj']['uang_kawal']):0;
            $data['Ttuj']['uang_keamanan'] = !empty($data['Ttuj']['uang_keamanan'])?str_replace(array(',', ' '), array('', ''), $data['Ttuj']['uang_keamanan']):0;
            $data['Ttuj']['uang_jalan_extra'] = !empty($data['Ttuj']['uang_jalan_extra'])?str_replace(array(',', ' '), array('', ''), $data['Ttuj']['uang_jalan_extra']):0;
            $data['Ttuj']['min_capacity'] = !empty($data['Ttuj']['min_capacity'])?str_replace(array(',', ' '), array('', ''), $data['Ttuj']['min_capacity']):0;
            $data['Ttuj']['tgljam_berangkat'] = '';

            if( !empty($data['Ttuj']['tgl_berangkat']) ) {
                $data['Ttuj']['tgl_berangkat'] = date('Y-m-d', strtotime($data['Ttuj']['tgl_berangkat']));

                if( !empty($data['Ttuj']['jam_berangkat']) ) {
                    $data['Ttuj']['jam_berangkat'] = date('H:i', strtotime($data['Ttuj']['jam_berangkat']));
                    $data['Ttuj']['tgljam_berangkat'] = sprintf('%s %s', $data['Ttuj']['tgl_berangkat'], $data['Ttuj']['jam_berangkat']);
                }
            }

            $this->Ttuj->set($data);

            if($this->Ttuj->validates($data)){
                if( !empty($data['TtujTipeMotor']['tipe_motor_id']) ) {
                    $dataTtujTipeMotor = array_filter($data['TtujTipeMotor']['tipe_motor_id']);
                    $dataTtujPerlengkapan = array_filter($data['TtujPerlengkapan']['qty']);

                    if( !empty($dataTtujTipeMotor) ) {
                        $result_data = array();
                        $validates = true;
                        $result_data_perlengkapan = array();
                        $validates_perlengkapan = true;

                        $resultTtujTipeMotor = $this->saveTtujTipeMotor($dataTtujTipeMotor, $data);
                        $resultTtujPerlengkapan = $this->saveTtujPerlengkapan($dataTtujPerlengkapan, $data);

                        if( !empty($resultTtujTipeMotor) ) {
                            $result_data = $resultTtujTipeMotor['data'];
                            $validates = $resultTtujTipeMotor['validates'];
                        }
                        if( !empty($resultTtujPerlengkapan) ) {
                            $result_data_perlengkapan = $resultTtujPerlengkapan['data'];
                            $validates_perlengkapan = $resultTtujPerlengkapan['validates'];
                        }
                        
                        if( !empty($validates) && !empty($validates_perlengkapan) ) {
                            if($this->Ttuj->save($data)){
                                $this->saveTtujTipeMotor($dataTtujTipeMotor, $data, $this->Ttuj->id);
                                $this->saveTtujPerlengkapan($dataTtujPerlengkapan, $data, $this->Ttuj->id);

                                $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s TTUJ'), $msg), 'success');
                                $this->redirect(array(
                                    'controller' => 'revenues',
                                    'action' => 'ttuj'
                                ));
                            }else{
                                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Ttuj'), $msg), 'error');  
                            }
                        } else {
                            $step = '#step2';
                            $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Ttuj, Mohon lengkapi muatan truk.'), $msg), 'error');  
                        }
                    } else {
                        $step = '#step2';
                        $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Ttuj, Silahkan masukan muatan truk.'), $msg), 'error');  
                    }
                } else {
                    $step = '#step2';
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Ttuj, Silahkan masukan muatan truk.'), $msg), 'error');  
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Ttuj'), $msg), 'error');
                $step = '#step1';
            }

            if( !empty($data['Ttuj']['customer_id']) ) {
                $fromCities = $this->UangJalan->getKotaAsal($data['Ttuj']['customer_id']);

                if( !empty($data['Ttuj']['from_city_id']) ) {
                    $toCities = $this->UangJalan->getKotaTujuan($data['Ttuj']['customer_id'], $data['Ttuj']['from_city_id']);

                    if( !empty($data['Ttuj']['to_city_id']) ) {
                        $dataTruck = $this->UangJalan->getNopol($data['Ttuj']['customer_id'], $data['Ttuj']['from_city_id'], $data['Ttuj']['to_city_id']);

                        if( !empty($dataTruck) ) {
                            $trucks = $dataTruck['result'];
                            $uangJalan = $dataTruck['uangJalan'];
                            $this->request->data['Ttuj']['uang_jalan_1'] = !empty($uangJalan['UangJalan']['uang_jalan_1'])?number_format($uangJalan['UangJalan']['uang_jalan_1'], 0):false;
                            $this->request->data['Ttuj']['uang_jalan_2'] = !empty($uangJalan['UangJalan']['uang_jalan_2'])?number_format($uangJalan['UangJalan']['uang_jalan_2'], 0):false;
                            $this->request->data['Ttuj']['uang_kuli_muat'] = !empty($uangJalan['UangJalan']['uang_kuli_muat'])?number_format($uangJalan['UangJalan']['uang_kuli_muat'], 0):false;
                            $this->request->data['Ttuj']['uang_kuli_bongkar'] = !empty($uangJalan['UangJalan']['uang_kuli_bongkar'])?number_format($uangJalan['UangJalan']['uang_kuli_bongkar'], 0):false;
                            $this->request->data['Ttuj']['asdp'] = !empty($uangJalan['UangJalan']['asdp'])?number_format($uangJalan['UangJalan']['asdp'], 0):false;
                            $this->request->data['Ttuj']['uang_kawal'] = !empty($uangJalan['UangJalan']['uang_kawal'])?number_format($uangJalan['UangJalan']['uang_kawal'], 0):false;
                            $this->request->data['Ttuj']['uang_keamanan'] = !empty($uangJalan['UangJalan']['uang_keamanan'])?number_format($uangJalan['UangJalan']['uang_keamanan'], 0):false;

                            if( !empty($data['Ttuj']['truck_id']) ) {
                                $truckInfo = $this->Truck->getInfoTruck($data['Ttuj']['truck_id']);
                                $this->request->data['Ttuj']['driver_name'] = !empty($truckInfo['Driver']['name'])?$truckInfo['Driver']['name']:false;
                                $this->request->data['Ttuj']['truck_capacity'] = !empty($truckInfo['Truck']['capacity'])?$truckInfo['Truck']['capacity']:false;
                                $this->request->data['Ttuj']['truck_capacity'] = !empty($truckInfo['Truck']['capacity'])?$truckInfo['Truck']['capacity']:false;
                            }
                        }
                    }
                }
            }

            $this->request->data['Ttuj']['ttuj_date'] = !empty($data['Ttuj']['ttuj_date'])?date('m/d/Y', strtotime($data['Ttuj']['ttuj_date'])):false;

            if( !empty($data['TtujPerlengkapan']['qty']) ) {
                $tempPerlengkapan = array();

                foreach ($data['TtujPerlengkapan']['qty'] as $key => $qty) {
                    if( !empty($qty) ) {
                        $tempPerlengkapan['TtujPerlengkapan'][$data['TtujPerlengkapan']['id'][$key]] = $qty;
                    }
                }

                if( !empty($tempPerlengkapan['TtujPerlengkapan']) ) {
                    $this->request->data['TtujPerlengkapan'] = $tempPerlengkapan['TtujPerlengkapan'];
                }
            }
        }else{
            if($id && $data_local){
                $data_local = $this->MkCommon->getTtujTipeMotor($data_local);
                $data_local = $this->MkCommon->getTtujPerlengkapan($data_local);

                if( !empty($data_local['Ttuj']['tgljam_berangkat']) ) {
                    $data_local['Ttuj']['tgl_berangkat'] = date('m/d/Y', strtotime($data_local['Ttuj']['tgljam_berangkat']));
                    $data_local['Ttuj']['jam_berangkat'] = date('H:i', strtotime($data_local['Ttuj']['tgljam_berangkat']));
                }
                $this->request->data = $data_local;
            }

            if( !empty($this->request->data['Ttuj']['customer_id']) ) {
                $fromCities = $this->UangJalan->getKotaAsal($this->request->data['Ttuj']['customer_id']);

                if( !empty($this->request->data['Ttuj']['from_city_id']) ) {
                    $toCities = $this->UangJalan->getKotaTujuan($this->request->data['Ttuj']['customer_id'], $this->request->data['Ttuj']['from_city_id']);

                    if( !empty($this->request->data['Ttuj']['to_city_id']) ) {
                        $dataTruck = $this->UangJalan->getNopol($this->request->data['Ttuj']['customer_id'], $this->request->data['Ttuj']['from_city_id'], $this->request->data['Ttuj']['to_city_id']);

                        if( !empty($dataTruck) ) {
                            $trucks = $dataTruck['result'];
                        }
                    }
                }
            }
        }

        $customers = $this->Ttuj->Customer->getData('list', array(
            'conditions' => array(
                'Customer.status' => 1
            ),
            'fields' => array(
                'Customer.id', 'Customer.name'
            )
        ));
        $driverPengantis = $this->Ttuj->Truck->Driver->getData('list', array(
            'conditions' => array(
                'Driver.status' => 1,
                'Truck.id <>' => NULL,
            ),
            'fields' => array(
                'Driver.id', 'Driver.name'
            ),
            'contain' => array(
                'Truck'
            )
        ));
        $perlengkapans = $this->Perlengkapan->getData('list', array(
            'fields' => array(
                'Perlengkapan.id', 'Perlengkapan.name',
            ),
        ));
        $tipeMotorsTmp = $this->TipeMotor->getData('all', array(
            'fields' => array(
                'TipeMotor.id', 'TipeMotor.tipe_motor_color',
            ),
        ));
        $tipeMotors = array();

        if( !empty($tipeMotorsTmp) ) {
            foreach ($tipeMotorsTmp as $key => $tipeMotor) {
                $tipeMotors[$tipeMotor['TipeMotor']['id']] = $tipeMotor['TipeMotor']['tipe_motor_color'];
            }
        }

        $this->set('active_menu', 'ttuj');
        $this->set(compact(
            'trucks', 'customers', 'driverPengantis',
            'fromCities', 'toCities', 'uangJalan',
            'tipeMotors', 'perlengkapans', 'step',
            'truckInfo', 'data_local'
        ));
        $this->render('ttuj_form');
    }

    function ttuj_toggle( $id, $action_type = 'status' ){
        $this->loadModel('Ttuj');
        $locale = $this->Ttuj->getData('first', array(
            'conditions' => array(
                'Ttuj.id' => $id
            )
        ));

        if($locale){
            $value = true;
            if($locale['Ttuj']['status']){
                $value = false;
            }

            $this->Ttuj->id = $id;

            switch ($action_type) {
                case 'arrive':
                    $this->Ttuj->set('is_arrive', 0);
                    break;
                
                default:
                    $this->Ttuj->set('status', 0);
                    break;
            }

            if($this->Ttuj->save()){
                $this->MkCommon->setCustomFlash(__('Sukses menghapus ttuj.'), 'success');
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal menghapus ttuj.'), 'error');
            }
        }else{
            $this->MkCommon->setCustomFlash(__('truk tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    public function truk_tiba() {
        $this->loadModel('Ttuj');
        $this->set('active_menu', 'truk_tiba');
        $this->set('sub_module_title', __('Truk Tiba'));
        $conditions = array(
            'Ttuj.is_arrive' => 1,
        );

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['nopol'])){
                $nopol = urldecode($refine['nopol']);
                $this->request->data['Truck']['nopol'] = $nopol;
                $conditions['Truck.nopol LIKE '] = '%'.$nopol.'%';
            }
        }

        $this->paginate = $this->Ttuj->getData('paginate', array(
            'conditions' => $conditions
        ));
        $ttujs = $this->paginate('Ttuj');

        $this->set('ttujs', $ttujs);
        $this->render('ttuj');
    }

    public function truk_tiba_add() {
        $this->loadModel('Ttuj');
        $this->set('sub_module_title', __('Tambah Tiba'));
        $this->set('active_menu', 'truk_tiba');
        $this->doTTUJLanjutan();
    }

    function doTTUJLanjutan( $action_type = 'truk_tiba' ){
        $this->loadModel('TipeMotor');
        $this->loadModel('Perlengkapan');
        $this->loadModel('Truck');

        if( !empty($this->params['named']['no_ttuj']) ) {
            $conditionsDataLocal = array(
                'Ttuj.id' => $this->params['named']['no_ttuj'],
                'Ttuj.is_draft' => 0,
                'Ttuj.status' => 1,
            );

            switch ($action_type) {
                case 'bongkaran':
                    $conditionsDataLocal['Ttuj.is_arrive'] = 1;
                    break;
                
                default:
                    $conditionsDataLocal['Ttuj.is_arrive'] = 0;
                    break;
            }
            $data_local = $this->Ttuj->getData('first', array(
                'conditions' => $conditionsDataLocal
            ));
        }

        if( !empty($this->request->data) && !empty($data_local) ){
            $data = $this->request->data;

            $this->Ttuj->id = $data_local['Ttuj']['id'];

            switch ($action_type) {
                case 'bongkaran':
                    $dataTiba['Ttuj']['is_bongkaran'] = 1;
                    $dataTiba['Ttuj']['tgljam_bongkaran'] = '';
                    $dataTiba['Ttuj']['note_bongkaran'] = !empty($data['Ttuj']['note_bongkaran'])?$data['Ttuj']['note_bongkaran']:'';

                    if( !empty($data['Ttuj']['tgl_bongkaran']) ) {
                        $data['Ttuj']['tgl_bongkaran'] = date('Y-m-d', strtotime($data['Ttuj']['tgl_bongkaran']));

                        if( !empty($data['Ttuj']['jam_bongkaran']) ) {
                            $data['Ttuj']['jam_bongkaran'] = date('H:i', strtotime($data['Ttuj']['jam_bongkaran']));
                            $dataTiba['Ttuj']['tgljam_bongkaran'] = sprintf('%s %s', $data['Ttuj']['tgl_bongkaran'], $data['Ttuj']['jam_bongkaran']);
                        }
                    }
                    $referer = 'bongkaran';
                    break;
                
                default:
                    $dataTiba['Ttuj']['is_arrive'] = 1;
                    $dataTiba['Ttuj']['tgljam_tiba'] = '';
                    $dataTiba['Ttuj']['note_tiba'] = !empty($data['Ttuj']['note_tiba'])?$data['Ttuj']['note_tiba']:'';

                    if( !empty($data['Ttuj']['tgl_tiba']) ) {
                        $data['Ttuj']['tgl_tiba'] = date('Y-m-d', strtotime($data['Ttuj']['tgl_tiba']));

                        if( !empty($data['Ttuj']['jam_tiba']) ) {
                            $data['Ttuj']['jam_tiba'] = date('H:i', strtotime($data['Ttuj']['jam_tiba']));
                            $dataTiba['Ttuj']['tgljam_tiba'] = sprintf('%s %s', $data['Ttuj']['tgl_tiba'], $data['Ttuj']['jam_tiba']);
                        }
                    }
                    $referer = 'truk_tiba';
                    break;
            }

            $data_local = $this->Ttuj->getData('first', array(
                'conditions' => $conditionsDataLocal
            ));

            $this->Ttuj->set($dataTiba);

            if($this->Ttuj->validates($dataTiba)){
                if($this->Ttuj->save($dataTiba)){
                    $this->MkCommon->setCustomFlash(__('Sukses merubah TTUJ'), 'success');
                    $this->redirect(array(
                        'controller' => 'revenues',
                        'action' => $referer
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal merubah Ttuj'), 'error');  
                }
            }
        }

        if( !empty($data_local) ){
            $data_local = $this->MkCommon->getTtujTipeMotor($data_local);
            $data_local = $this->MkCommon->getTtujPerlengkapan($data_local);

            if( !empty($data_local['Ttuj']['tgljam_berangkat']) && $data_local['Ttuj']['tgljam_berangkat'] != '0000-00-00 00:00:00' ) {
                $data_local['Ttuj']['tgl_berangkat'] = date('m/d/Y', strtotime($data_local['Ttuj']['tgljam_berangkat']));
                $data_local['Ttuj']['jam_berangkat'] = date('H:i', strtotime($data_local['Ttuj']['tgljam_berangkat']));
            }
            if( !empty($data_local['Ttuj']['tgljam_tiba']) && $data_local['Ttuj']['tgljam_tiba'] != '0000-00-00 00:00:00' ) {
                $data_local['Ttuj']['tgl_tiba'] = date('m/d/Y', strtotime($data_local['Ttuj']['tgljam_tiba']));
                $data_local['Ttuj']['jam_tiba'] = date('H:i', strtotime($data_local['Ttuj']['tgljam_tiba']));
            }
            $this->request->data = $data_local;
        }

        if( !empty($this->params['named']['no_ttuj']) ) {
            $this->request->data['Ttuj']['no_ttuj'] = $this->params['named']['no_ttuj'];
        }

        $conditionsTtuj = array(
            'Ttuj.status' => 1,
            'Ttuj.is_draft' => 0,
        );

        switch ($action_type) {
            case 'bongkaran':
                $conditionsTtuj['Ttuj.is_arrive'] = 1;
                break;
            
            default:
                $conditionsTtuj['Ttuj.is_arrive'] = 0;
                break;
        }

        $ttujs = $this->Ttuj->getData('list', array(
            'conditions' => $conditionsTtuj,
            'fields' => array(
                'Ttuj.id', 'Ttuj.no_ttuj'
            )
        ));
        $perlengkapans = $this->Perlengkapan->getData('list', array(
            'fields' => array(
                'Perlengkapan.id', 'Perlengkapan.name',
            ),
        ));
        $tipeMotorsTmp = $this->TipeMotor->getData('all', array(
            'fields' => array(
                'TipeMotor.id', 'TipeMotor.tipe_motor_color',
            ),
        ));
        $tipeMotors = array();

        if( !empty($tipeMotorsTmp) ) {
            foreach ($tipeMotorsTmp as $key => $tipeMotor) {
                $tipeMotors[$tipeMotor['TipeMotor']['id']] = $tipeMotor['TipeMotor']['tipe_motor_color'];
            }
        }

        $this->set(compact(
            'ttujs', 'data_local', 'perlengkapans', 
            'tipeMotors', 'action_type'
        ));
        $this->render('ttuj_lanjutan_form');
    }

    public function info_truk_tiba( $ttuj_id = false ) {
        $this->loadModel('Ttuj');
        $this->loadModel('TipeMotor');
        $this->loadModel('Perlengkapan');

        $data_local = $this->Ttuj->getData('first', array(
            'conditions' => array(
                'Ttuj.id' => $ttuj_id,
                'Ttuj.is_draft' => 0,
                'Ttuj.is_arrive' => 1,
                'Ttuj.status' => 1,
            )
        ));

        if( !empty($data_local) ){
            $data_local = $this->MkCommon->getTtujTipeMotor($data_local);
            $data_local = $this->MkCommon->getTtujPerlengkapan($data_local);

            if( !empty($data_local['Ttuj']['tgljam_berangkat']) && $data_local['Ttuj']['tgljam_berangkat'] != '0000-00-00 00:00:00' ) {
                $data_local['Ttuj']['tgl_berangkat'] = date('m/d/Y', strtotime($data_local['Ttuj']['tgljam_berangkat']));
                $data_local['Ttuj']['jam_berangkat'] = date('H:i', strtotime($data_local['Ttuj']['tgljam_berangkat']));
            }
            if( !empty($data_local['Ttuj']['tgljam_tiba']) && $data_local['Ttuj']['tgljam_tiba'] != '0000-00-00 00:00:00' ) {
                $data_local['Ttuj']['tgl_tiba'] = date('m/d/Y', strtotime($data_local['Ttuj']['tgljam_tiba']));
                $data_local['Ttuj']['jam_tiba'] = date('H:i', strtotime($data_local['Ttuj']['tgljam_tiba']));
            }
            $this->request->data = $data_local;
            $perlengkapans = $this->Perlengkapan->getData('list', array(
                'fields' => array(
                    'Perlengkapan.id', 'Perlengkapan.name',
                ),
            ));
            $tipeMotorsTmp = $this->TipeMotor->getData('all', array(
                'fields' => array(
                    'TipeMotor.id', 'TipeMotor.tipe_motor_color',
                ),
            ));
            $tipeMotors = array();

            if( !empty($tipeMotorsTmp) ) {
                foreach ($tipeMotorsTmp as $key => $tipeMotor) {
                    $tipeMotors[$tipeMotor['TipeMotor']['id']] = $tipeMotor['TipeMotor']['tipe_motor_color'];
                }
            }

            $this->set('sub_module_title', __('Informasi Truk Tiba'));
            $this->set('active_menu', 'truk_tiba');
            $this->set(compact(
                'ttujs', 'data_local', 'perlengkapans', 
                'tipeMotors', 'ttuj_id'
            ));
            $this->render('ttuj_lanjutan_form');
        } else {
            $this->MkCommon->setCustomFlash(__('TTUJ tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'revenues',
                'action' => 'truk_tiba'
            ));
        }
    }

    public function bongkaran() {
        $this->loadModel('Ttuj');
        $this->set('active_menu', 'bongkaran');
        $this->set('sub_module_title', __('Bongkaran'));
        $conditions = array(
            'Ttuj.is_arrive' => 1,
            'Ttuj.is_bongkaran' => 1,
        );

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['nopol'])){
                $nopol = urldecode($refine['nopol']);
                $this->request->data['Truck']['nopol'] = $nopol;
                $conditions['Truck.nopol LIKE '] = '%'.$nopol.'%';
            }
        }

        $this->paginate = $this->Ttuj->getData('paginate', array(
            'conditions' => $conditions
        ));
        $ttujs = $this->paginate('Ttuj');

        $this->set('ttujs', $ttujs);
        $this->render('ttuj');
    }

    public function bongkaran_add() {
        $this->loadModel('Ttuj');
        $this->set('sub_module_title', __('Tambah Tiba'));
        $this->set('active_menu', 'bongkaran');
        $this->doTTUJLanjutan( 'bongkaran' );
    }
}