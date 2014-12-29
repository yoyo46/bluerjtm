<?php
App::uses('AppController', 'Controller');
class LeasingsController extends AppController {
	public $uses = array();

    public $components = array(
        'RjLeasing'
    );

    function beforeFilter() {
        parent::beforeFilter();
        $this->set('title_for_layout', __('ERP RJTM | Leasing'));
        $this->set('module_title', __('Leasing'));
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

	public function index() {
        $this->loadModel('Leasing');
        $this->loadModel('Truck');
		$this->set('active_menu', 'leasings');
		$this->set('sub_module_title', __('Leasing'));

        $conditions = array();
        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['nopol'])){
                $nopol = urldecode($refine['nopol']);
                $this->request->data['Truck']['nopol'] = $nopol;
                $conditions['Truck.nopol LIKE '] = '%'.$nopol.'%';
            }
        }

        $this->paginate = $this->Leasing->getData('paginate', array(
            'conditions' => $conditions
        ));
        $leasings = $this->paginate('Leasing');

        if(!empty($leasings)){
            foreach ($leasings as $key => $leasing) {
                $data = $leasing['Leasing'];

                $truck = $this->Truck->getTruck($data['truck_id']);
                debug($truck);die();

                $trucks[$key] = $truck;
            }
        }

        $this->set('leasings', $leasings);
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
        $truck = $this->Truck->getData('first', array(
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
}