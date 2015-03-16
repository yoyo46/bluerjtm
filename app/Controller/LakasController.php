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
        if( in_array('view_lakas', $this->allowModule) ) {
            $this->loadModel('Laka');
    		$this->set('active_menu', 'lakas');
    		$this->set('sub_module_title', __('Data LAKA'));
            $conditions = array();
            
            if(!empty($this->params['named'])){
                $refine = $this->params['named'];

                if(!empty($refine['nopol'])){
                    $nopol = urldecode($refine['nopol']);
                    $this->request->data['Laka']['nopol'] = $nopol;
                    $conditions['Laka.nopol LIKE '] = '%'.$nopol.'%';
                }
            }

            $this->paginate = $this->Laka->getData('paginate', array(
                'conditions' => $conditions
            ));
            $Lakas = $this->paginate('Laka');

            $this->set('Lakas', $Lakas);
        } else {
            $this->redirect($this->referer());
        }
	}

    function detail($id = false){
        if( in_array('view_lakas', $this->allowModule) ) {
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
        } else {
            $this->redirect($this->referer());
        }
    }

    function add(){
        if( in_array('insert_lakas', $this->allowModule) ) {
            $this->set('sub_module_title', __('Tambah LAKA'));
            $this->DoLaka();
        } else {
            $this->redirect($this->referer());
        }
    }

    function edit($id){
        if( in_array('update_lakas', $this->allowModule) ) {
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
        } else {
            $this->redirect($this->referer());
        }
    }

    function DoLaka($id = false, $data_local = false){
        if(!empty($this->request->data)){
            $data = $this->request->data;
            
            if($id && $data_local){
                $this->Laka->id = $id;
                $msg = 'merubah';
            }else{
                $this->loadModel('Laka');
                $this->Laka->create();
                $msg = 'menambah';
            }
            
            $data['Laka']['tgl_laka'] = (!empty($data['Laka']['tgl_laka'])) ? $this->MkCommon->getDate($data['Laka']['tgl_laka']) : '';
            $data['LakaDetail']['date_birth'] = (!empty($data['LakaDetail']['date_birth'])) ? $this->MkCommon->getDate($data['LakaDetail']['date_birth']) : '';

            if( empty($data['Laka']['completed']) ) {
                $data['Laka']['complete_desc'] = '';
                $data['Laka']['completed_date'] = '';
            } else {
                $data['Laka']['completed_date'] = (!empty($data['Laka']['completed_date'])) ? $this->MkCommon->getDate($data['Laka']['completed_date']) : '';
            }

            if(!empty($data['Laka']['ttuj_id'])){
                $this->loadModel('Ttuj');
                $ttuj = $this->Ttuj->find('first', array(
                    'conditions' => array(
                        'Ttuj.id' => $data['Laka']['ttuj_id']
                    )
                ));

                if(!empty($ttuj['Ttuj']['driver_name'])){
                    $data['Laka']['driver_name'] = $ttuj['Ttuj']['driver_name'];
                }
            }
            
            $this->Laka->set($data);

            if($this->Laka->validates($data)){
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

                        $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s LAKA'), $msg), 'success');
                        $this->Log->logActivity( sprintf(__('Berhasil %s LAKA #%s'), $msg, $laka_id), $this->user_data, $this->RequestHandler, $this->params, 1 );
                        
                        $this->redirect(array(
                            'controller' => 'Lakas',
                            'action' => 'index',
                        ));
                    } else {
                        $step = 'step2';
                        $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s LAKA'), $msg), 'error');
                        $this->Log->logActivity( sprintf(__('Gagal %s LAKA'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );
                    }
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s LAKA'), $msg), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal %s LAKA'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );
                }
            }else{
                $validationErrors = $this->Laka->validationErrors;
                
                if( !empty($validationErrors['description_laka']) || !empty($validationErrors['complete_desc']) || !empty($validationErrors['completed_date']) ) {
                    $step = 'step2';
                }

                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s LAKA'), $msg), 'error');
            }
        } else if($id && $data_local){
            $this->request->data= $data_local;
            
            $this->request->data['Laka']['completeness'] = !empty($this->request->data['Laka']['completeness']) ? unserialize($this->request->data['Laka']['completeness']) : '';
            $this->request->data['Laka']['completeness_insurance'] = !empty($this->request->data['Laka']['completeness_insurance']) ? unserialize($this->request->data['Laka']['completeness_insurance']) : '';
            $this->request->data['Laka']['tgl_laka'] = $this->MkCommon->customDate($this->request->data['Laka']['tgl_laka'], 'd/m/Y');

            if( !empty($this->request->data['Laka']['completed']) ) {
                $this->request->data['Laka']['completed_date'] = date('d/m/Y', strtotime($this->request->data['Laka']['completed_date']));
            }
        }

        $this->loadModel('Ttuj');
        $ttujs = $this->Ttuj->getData('all', array(
            'fields' => array(
                'Ttuj.id', 'Ttuj.driver_name', 'Ttuj.no_ttuj'
            ),
            'conditions' => array(
                'Ttuj.is_pool <>' => 1,
                'Ttuj.is_draft' => 0,
                'Ttuj.status' => 1,
            ),
        ));

        $result = array();
        if(!empty($ttujs)){
            foreach ($ttujs as $key => $value) {
                $result[$value['Ttuj']['id']] = sprintf('%s (%s)', $value['Ttuj']['driver_name'], $value['Ttuj']['no_ttuj']);
            }
        }
        $ttujs = $result;

        $this->loadModel('LakaMaterial');
        $this->loadModel('LakaInsurance');

        $material = $this->LakaMaterial->find('list');
        $insurance = $this->LakaInsurance->find('list');

        $this->set(compact('material', 'insurance', 'step'));

        $this->set('active_menu', 'lakas');
        $this->set('ttujs', $ttujs);
        $this->set('id', $id);

        $this->render('laka_form');
    }

    function toggle($id){
        if( in_array('delete_lakas', $this->allowModule) ) {
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
                    $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses merubah status LAKA %s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal merubah status LAKA %s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );
                }
            }else{
                $this->MkCommon->setCustomFlash(__('Laka tidak ditemukan.'), 'error');
            }

            $this->redirect($this->referer());
        } else {
            $this->redirect($this->referer());
        }
    }
}