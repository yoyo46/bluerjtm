<?php
App::uses('AppController', 'Controller');
class LkusController extends AppController {
	public $uses = array();

    public $components = array(
        'RjLku'
    );

    function beforeFilter() {
        parent::beforeFilter();
        $this->set('title_for_layout', __('ERP RJTM | Data Lku'));
        $this->set('module_title', __('Lku'));
    }

    function search( $index = 'index' ){
        $refine = array();
        if(!empty($this->request->data)) {
            $refine = $this->RjLku->processRefine($this->request->data);
            $params = $this->RjLku->generateSearchURL($refine);
            $params['action'] = $index;

            $this->redirect($params);
        }
        $this->redirect('/');
    }

	public function index() {
        $this->loadModel('Lku');
		$this->set('active_menu', 'Lkus');
		$this->set('sub_module_title', __('Data Lku'));
        $conditions = array();
        
        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['no_doc'])){
                $no_doc = urldecode($refine['no_doc']);
                $this->request->data['Lku']['no_doc'] = $no_doc;
                $conditions['Lku.no_doc LIKE '] = '%'.$no_doc.'%';
            }
        }

        $this->paginate = $this->Lku->getData('paginate', array(
            'conditions' => $conditions
        ));
        $Lkus = $this->paginate('Lku');

        $this->set('Lkus', $Lkus);
	}

    function detail($id = false){
        if(!empty($id)){
            $Lku = $this->Lku->getLku($id);

            if(!empty($Lku)){
                $sub_module_title = __('Detail Lku');
                $this->set(compact('Lku', 'sub_module_title'));
            }else{
                $this->MkCommon->setCustomFlash(__('Lku tidak ditemukan.'), 'error');
                $this->redirect($this->referer());
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Lku tidak ditemukan.'), 'error');
            $this->redirect($this->referer());
        }
    }

    function add(){
        $this->set('sub_module_title', __('Tambah Lku'));
        $this->DoLku();
    }

    function edit($id){
        $this->loadModel('Lku');
        $this->set('sub_module_title', 'Rubah Lku');
        $Lku = $this->Lku->getData('first', array(
            'conditions' => array(
                'Lku.id' => $id
            ),
        ));

        if(!empty($Lku)){
            $this->DoLku($id, $Lku);
        }else{
            $this->MkCommon->setCustomFlash(__('Lku tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'Lkus',
                'action' => 'index'
            ));
        }
    }

    function DoLku($id = false, $data_local = false){
        $this->loadModel('Ttuj');
        $this->loadModel('TipeMotor');

        if(!empty($this->request->data)){
            $data = $this->request->data;

            if($id && $data_local){
                $this->Lku->id = $id;
                $msg = 'merubah';
            }else{
                $this->loadModel('Lku');
                $this->Lku->create();
                $msg = 'menambah';
            }
            
            $data['Lku']['tgl_lku'] = (!empty($data['Lku']['tgl_lku'])) ? $this->MkCommon->getDate($data['Lku']['tgl_lku']) : '';
            
            $validate_lku_detail = true;
            $temp_detail = array();
            $total_price = 0;
            if(!empty($data['LkuDetail']['tipe_motor_id'])){
                foreach ($data['LkuDetail']['tipe_motor_id'] as $key => $value) {
                    if( !empty($value) ){
                        $data_detail['LkuDetail'] = array(
                            'tipe_motor_id' => $value,
                            'no_rangka' => (!empty($data['LkuDetail']['no_rangka'][$key])) ? $data['LkuDetail']['no_rangka'][$key] : '',
                            'qty' => (!empty($data['LkuDetail']['qty'][$key])) ? $data['LkuDetail']['qty'][$key] : '',
                            'price' => (!empty($data['LkuDetail']['price'][$key])) ? $data['LkuDetail']['price'][$key] : '',
                        );
                        
                        $temp_detail[] = $data_detail;
                        $this->Lku->LkuDetail->set($data_detail);
                        if( !$this->Lku->LkuDetail->validates() ){
                            $validate_lku_detail = false;
                            break;
                        }else{
                            $total_price += $data_detail['LkuDetail']['qty'] * $data_detail['LkuDetail']['price'];
                        }
                    }
                }
            }
            
            $data['Lku']['total_price'] = $total_price;
            $this->Lku->set($data);

            if($this->Lku->validates($data) && $validate_lku_detail){
                if($this->Lku->save($data)){
                    $lku_id = $this->Lku->id;

                    if($id && $data_local){
                        $this->Lku->LkuDetail->deleteAll(array(
                            'LkuDetail.lku_id' => $lku_id
                        ));
                    }

                    foreach ($temp_detail as $key => $value) {
                        $this->Lku->LkuDetail->create();
                        $value['LkuDetail']['lku_id'] = $lku_id;

                        $this->Lku->LkuDetail->set($value);
                        $this->Lku->LkuDetail->save();
                    }

                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Lku'), $msg), 'success');
                    $this->redirect(array(
                        'controller' => 'Lkus',
                        'action' => 'index',
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Lku'), $msg), 'error');  
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Lku'), $msg), 'error');
            }
        } else if($id && $data_local){
            $this->request->data = $data_local;

            if(!empty($this->request->data['LkuDetail'])){
                foreach ($this->request->data['LkuDetail'] as $key => $value) {
                    $tipe_motor = $this->TipeMotor->getData('first', array(
                        'conditions' => array(
                            'TipeMotor.id' => $value['tipe_motor_id']
                        ),
                        'contain' => array(
                            'ColorMotor'
                        )
                    ));
                    if(!empty($tipe_motor)){
                        $Ttuj_Tipe_Motor = $this->Ttuj->TtujTipeMotor->getData('first', array(
                            'conditions' => array(
                                'TtujTipeMotor.ttuj_id' => $this->request->data['Lku']['ttuj_id'],
                                'TtujTipeMotor.tipe_motor_id' => $value['tipe_motor_id']
                            )
                        ));
                        $this->request->data['LkuDetail'][$key]['TipeMotor'] = array_merge($tipe_motor['TipeMotor'], $Ttuj_Tipe_Motor);
                        $this->request->data['LkuDetail'][$key]['ColorMotor'] = $tipe_motor['ColorMotor'];
                    }
                }
            }
        }

        if(!empty($this->request->data['LkuDetail']['tipe_motor_id'])){
            $temp = array();
            foreach ($this->request->data['LkuDetail']['tipe_motor_id'] as $key => $value) {
                if( !empty($value) ){
                    $temp['LkuDetail'][$key] = array(
                        'tipe_motor_id' => $value,
                        'no_rangka' => (!empty($data['LkuDetail']['no_rangka'][$key])) ? $data['LkuDetail']['no_rangka'][$key] : '',
                        'qty' => (!empty($data['LkuDetail']['qty'][$key])) ? $data['LkuDetail']['qty'][$key] : '',
                        'price' => (!empty($data['LkuDetail']['price'][$key])) ? $data['LkuDetail']['price'][$key] : '',
                    );

                    $tipe_motor = $this->TipeMotor->getData('first', array(
                        'conditions' => array(
                            'TipeMotor.id' => $value
                        ),
                        'contain' => array(
                            'ColorMotor'
                        )
                    ));
                    if(!empty($tipe_motor)){
                        $Ttuj_Tipe_Motor = $this->Ttuj->TtujTipeMotor->getData('first', array(
                            'conditions' => array(
                                'TtujTipeMotor.ttuj_id' => $this->request->data['Lku']['ttuj_id'],
                                'TtujTipeMotor.tipe_motor_id' => $value
                            )
                        ));
                        $temp['LkuDetail'][$key]['TipeMotor'] = array_merge($tipe_motor['TipeMotor'], $Ttuj_Tipe_Motor);
                        $temp['LkuDetail'][$key]['ColorMotor'] = $tipe_motor['ColorMotor'];
                    }
                }
            }

            unset($this->request->data['LkuDetail']);
            $this->request->data['LkuDetail'] = $temp['LkuDetail'];
        }

        $ttujs = $this->Ttuj->getData('list', array(
            'fields' => array(
                'Ttuj.id', 'Ttuj.no_ttuj'
            )
        ));

        if(!empty($this->request->data['Lku']['ttuj_id'])){
            $data_ttuj = $this->Ttuj->getData('first', array(
                'conditions' => array(
                    'Ttuj.id' => $this->request->data['Lku']['ttuj_id']
                ),
                'contain' => array(
                    'UangJalan'
                )
            ));
            
            if(!empty($data_ttuj)){
                if(!empty($data_ttuj['TtujTipeMotor'])){
                    $tipe_motor_list = array();
                    foreach ($data_ttuj['TtujTipeMotor'] as $key => $value) {
                        $tipe_motor = $this->TipeMotor->getData('first', array(
                            'conditions' => array(
                                'TipeMotor.id' => $value['tipe_motor_id']
                            )
                        ));
                        $tipe_motor_list[$tipe_motor['TipeMotor']['id']] = $tipe_motor['TipeMotor']['name'];
                    }
                }
                $this->request->data = array_merge($this->request->data, $data_ttuj);
            }
            
            $this->set('tipe_motor_list', $tipe_motor_list);
        }

        $this->set('active_menu', 'Lkus');
        $this->set('ttujs', $ttujs);
        $this->render('lku_form');
    }

    function toggle($id){
        $this->loadModel('Lku');
        $locale = $this->Lku->getData('first', array(
            'conditions' => array(
                'Lku.id' => $id
            )
        ));

        if($locale){
            $value = true;
            if($locale['Lku']['status']){
                $value = false;
            }

            $this->Lku->id = $id;
            $this->Lku->set('status', 0);

            if($this->Lku->save()){
                $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Lku tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }
}