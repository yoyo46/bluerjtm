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

        if(!empty($Lkus)){
            foreach ($Lkus as $key => $Lku) {
                $data = $Lku['Lku'];

                $Lku = $this->Lku->LkuCategory->getMerge($Lku, $data['Lku_category_id']);
                $Lku = $this->Lku->LkuBrand->getMerge($Lku, $data['Lku_brand_id']);
                $Lku = $this->Lku->Company->getMerge($Lku, $data['company_id']);
                $Lku = $this->Lku->Driver->getMerge($Lku, $data['driver_id']);

                $Lkus[$key] = $Lku;
            }
        }

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
            )
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
        if(!empty($this->request->data)){
            $data = $this->request->data;
            $leasing_id = 0;

            if($id && $data_local){
                $this->Lku->id = $id;
                $msg = 'merubah';
            }else{
                $this->loadModel('Lku');
                $this->Lku->create();
                $msg = 'menambah';
            }
            
            $data['Lku']['tgl_lku'] = (!empty($data['Lku']['tgl_lku'])) ? $this->MkCommon->getDate($data['Lku']['tgl_lku']) : '';

            $this->Lku->set($data);

            if($this->Lku->validates($data)){
                if($this->Lku->save($data)){
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
            $this->request->data= $data_local;
        }

        $this->loadModel('Ttuj');
        $ttujs = $this->Ttuj->getData('list', array(
            'fields' => array(
                'Ttuj.id', 'Ttuj.no_ttuj'
            )
        ));

        // $data_ttuj = $this->Ttuj->getData('first', array(
        //     'conditions' => array(
        //         'Ttuj.id' => 3
        //     ),
        //     'contain' => array(
        //         'UangJalan'
        //     )
        // ));
        
        // if(!empty($data_ttuj)){
        //     if(!empty($data_ttuj['TtujTipeMotor'])){
        //         $this->loadModel('TipeMotor');
        //         $tipe_motor_list = array();
        //         foreach ($data_ttuj['TtujTipeMotor'] as $key => $value) {
        //             $tipe_motor = $this->TipeMotor->getData('first', array(
        //                 'conditions' => array(
        //                     'TipeMotor.id' => $value['tipe_motor_id']
        //                 )
        //             ));
        //             $tipe_motor_list[$tipe_motor['TipeMotor']['id']] = $tipe_motor['TipeMotor']['name'];
        //         }
        //     }
        //     $this->request->data = $data_ttuj;
        // }
        
        // $this->set('tipe_motor_list', $tipe_motor_list);

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