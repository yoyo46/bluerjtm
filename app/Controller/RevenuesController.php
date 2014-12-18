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
            $refine = $this->RjLeasing->processRefine($this->request->data);
            $params = $this->RjLeasing->generateSearchURL($refine);
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

    function doTTUJ($id = false, $data_local = false){
        if(!empty($this->request->data)){
            $data = $this->request->data;
            if($id && $data_local){
                $this->Ttuj->id = $id;
                $msg = 'merubah';
            }else{
                $this->Ttuj->create();
                $msg = 'menambah';
            }
            
            $this->Ttuj->set($data);

            if($this->Ttuj->validates($data)){
            debug($data);die();
                if($this->Ttuj->save($data)){
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s TTUJ'), $msg), 'success');
                    $this->redirect(array(
                        'controller' => 'revenues',
                        'action' => 'ttuj'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Ttuj'), $msg), 'error');  
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Ttuj'), $msg), 'error');
            }

            if( !empty($data['Ttuj']['customer_id']) ) {
                $this->loadModel('UangJalan');
                $fromCities = $this->UangJalan->getKotaAsal($data['Ttuj']['customer_id']);

                if( !empty($data['Ttuj']['from_city_id']) ) {
                    $toCities = $this->UangJalan->getKotaTujuan($data['Ttuj']['customer_id'], $data['Ttuj']['from_city_id']);

                    if( !empty($data['Ttuj']['to_city_id']) ) {
                        $dataTruck = $this->UangJalan->getNopol($data['Ttuj']['customer_id'], $data['Ttuj']['from_city_id'], $data['Ttuj']['to_city_id']);

                        if( !empty($dataTruck) ) {
                            $trucks = $dataTruck['result'];
                            $uangJalan = $dataTruck['uangJalan'];
                        }
                    }
                }
            }
        }else{
            
            if($id && $data_local){
                $this->request->data = $data_local;
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

        $this->set('active_menu', 'ttuj');
        $this->set(compact(
            'trucks', 'customers', 'driverPengantis',
            'fromCities', 'toCities', 'uangJalan'
        ));
        $this->render('ttuj_form');
    }

    function ttuj_toggle($id){
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
}