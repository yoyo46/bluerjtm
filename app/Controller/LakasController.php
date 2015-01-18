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
		$this->set('active_menu', 'Lakas');
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
                'LakaDetail'
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
            
            if(!empty($data['Laka']['ttuj_id'])){
                $this->loadModel('Ttuj');
                $ttuj = $this->Ttuj->find('first', array(
                    'conditions' => array(
                        'Ttuj.id' => $data['Laka']['ttuj_id']
                    )
                ));

                if(!empty($ttuj['Ttuj']['driver_name'])){
                    $data['Laka']['Laka_name'] = $ttuj['Ttuj']['driver_name'];
                }
            }

            if(!empty($data['Laka']['ilustration_photo']['name']) && is_array($data['Laka']['ilustration_photo'])){
                $temp_image = $data['Laka']['ilustration_photo'];
                $data['Laka']['ilustration_photo'] = $data['Laka']['ilustration_photo']['name'];
            }else{
                if($id && $data_local){
                    unset($data['Laka']['ilustration_photo']);
                }else{
                    $data['Laka']['ilustration_photo'] = '';
                }
            }
            
            $this->Laka->set($data);

            if($this->Laka->validates($data)){
                
                $data['Laka']['completeness'] = (!empty($data['Laka']['completeness'])) ? serialize($data['Laka']['completeness']) : '';
                $data['Laka']['completeness_insurance'] = (!empty($data['Laka']['completeness_insurance'])) ? serialize($data['Laka']['completeness_insurance']) : '';

                if(!empty($temp_image) && is_array($temp_image)){
                    $uploaded = $this->RjImage->upload($temp_image, '/'.Configure::read('__Site.laka_photo_folder').'/', String::uuid());
                    if(!empty($uploaded)) {
                        if($uploaded['error']) {
                            $this->RmCommon->setCustomFlash($uploaded['message'], 'error');
                        } else {
                            $data['Laka']['ilustration_photo'] = $uploaded['imageName'];
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
                    $this->Laka->LakaDetail->save();

                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s LAKA'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Berhasil %s LAKA #%s'), $msg, $laka_id), $this->user_data, $this->RequestHandler, $this->params, 1 );
                    
                    $this->redirect(array(
                        'controller' => 'Lakas',
                        'action' => 'index',
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s LAKA'), $msg), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal %s LAKA'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s LAKA'), $msg), 'error');
            }
        } else if($id && $data_local){
            $this->request->data= $data_local;
            
            $this->request->data['Laka']['completeness'] = !empty($this->request->data['Laka']['completeness']) ? unserialize($this->request->data['Laka']['completeness']) : '';
            $this->request->data['Laka']['completeness_insurance'] = !empty($this->request->data['Laka']['completeness_insurance']) ? unserialize($this->request->data['Laka']['completeness_insurance']) : '';
        }

        $this->loadModel('Ttuj');
        $ttujs = $this->Ttuj->getData('all', array(
            'fields' => array(
                'Ttuj.id', 'Ttuj.driver_name', 'Ttuj.no_ttuj'
            )
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

        $this->set(compact('material', 'insurance'));

        $this->set('active_menu', 'Lakas');
        $this->set('ttujs', $ttujs);
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
    }
}