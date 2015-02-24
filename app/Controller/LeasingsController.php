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
        $this->loadModel('Leasing');
		$this->set('active_menu', 'view_leasing');
		$this->set('sub_module_title', __('Leasing'));

        $conditions = array();
        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['nocontract'])){
                $no_contract = urldecode($refine['nocontract']);
                $this->request->data['Leasing']['no_contract'] = $no_contract;
                $conditions['Leasing.no_contract LIKE '] = '%'.$no_contract.'%';
            }
        }

        $this->paginate = $this->Leasing->getData('paginate', array(
            'conditions' => $conditions,
            'contain' => array(
                'LeasingCompany'
            )
        ));
        $leasings = $this->paginate('Leasing');

        $this->set('leasings', $leasings);
	}

    function detail($id = false){
        if(!empty($id)){
            $truck = $this->Leasing->getLeasing($id);

            if(!empty($truck)){
                $sub_module_title = __('Detail Leasing');
                $this->set(compact('truck', 'sub_module_title'));
            }else{
                $this->MkCommon->setCustomFlash(__('Leasing tidak ditemukan.'), 'error');
                $this->redirect($this->referer());
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Leasing tidak ditemukan.'), 'error');
            $this->redirect($this->referer());
        }
    }

    function add(){
        $this->set('sub_module_title', __('Tambah Leasing'));
        $this->doLeasing();
    }

    function edit($id){
        $this->loadModel('Leasing');
        $this->set('sub_module_title', 'Rubah Leasing');
        $truck = $this->Leasing->getData('first', array(
            'conditions' => array(
                'Leasing.id' => $id
            ),
            'contain' => array(
                'LeasingDetail'
            )
        ));

        if(!empty($truck)){
            $this->doLeasing($id, $truck);
        }else{
            $this->MkCommon->setCustomFlash(__('Leasing tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'trucks',
                'action' => 'index'
            ));
        }
    }

    function doLeasing($id = false, $data_local = false){
        $this->loadModel('Truck');
        $this->loadModel('LeasingDetail');

        $trucks = $this->Truck->getData('list', array(
            'conditions' => array(
                'Truck.status' => 1,
            ),
            'fields' => array(
                'Truck.id', 'Truck.nopol'
            )
        ));

        $truck_leasing = $this->LeasingDetail->find('list', array(
            'fields' => array(
                'LeasingDetail.truck_id'
            ),
            'group' => array(
                'LeasingDetail.truck_id'
            )
        ));

        if(!empty($truck_leasing)){
            foreach ($trucks as $key => $value) {
                if( in_array($key, $truck_leasing) ){
                    unset($trucks[$key]);
                }
            }
        }

        if(!empty($this->request->data)){
            $data = $this->request->data;

            if($id && $data_local){
                $this->Leasing->id = $id;
                $msg = 'merubah';
            }else{
                $this->loadModel('Leasing');
                $this->Leasing->create();
                $msg = 'menambah';
            }

            $data['Leasing']['paid_date'] = (!empty($data['Leasing']['paid_date'])) ? $this->MkCommon->getDate($data['Leasing']['paid_date']) : '';
            $data['Leasing']['date_first_installment'] = (!empty($data['Leasing']['date_first_installment'])) ? $this->MkCommon->getDate($data['Leasing']['date_first_installment']) : '';
            $data['Leasing']['date_last_installment'] = (!empty($data['Leasing']['date_last_installment'])) ? $this->MkCommon->getDate($data['Leasing']['date_last_installment']) : '';

            $data['Leasing']['down_payment'] = !empty($data['Leasing']['down_payment']) ? str_replace(',', '', $data['Leasing']['down_payment']) : '';
            $data['Leasing']['installment'] = !empty($data['Leasing']['installment']) ? str_replace(',', '', $data['Leasing']['installment']) : '';
            $data['Leasing']['installment_rate'] = !empty($data['Leasing']['installment_rate']) ? str_replace(',', '', $data['Leasing']['installment_rate']) : '';

            $validate_leasing_detail = true;
            $temp_detail = array();
            $total_price = 0;
            $truck_collect = array();
            $truck_same = true;
            if(!empty($data['LeasingDetail']['truck_id'])){
                foreach ($data['LeasingDetail']['truck_id'] as $key => $value) {
                    if( !empty($value) && !in_array($value, $truck_collect)){
                        $truck_collect[] = $value;
                        $data_detail['LeasingDetail'] = array(
                            'truck_id' => $value,
                            'price' => (!empty($data['LeasingDetail']['price'][$key])) ? str_replace(',', '', $data['LeasingDetail']['price'][$key]) : '',
                        );
                        
                        $temp_detail[] = $data_detail;
                        $this->LeasingDetail->set($data_detail);
                        if( !$this->LeasingDetail->validates() ){
                            $validate_leasing_detail = false;
                            break;
                        }else{
                            $total_price += $data_detail['LeasingDetail']['price'];
                        }
                    }else{
                        if(in_array($value, $truck_collect)){
                            $truck_same = false;
                        }
                    }
                }
            }else{
                $validate_leasing_detail = false;
            }

            $this->Leasing->set($data);

            if($this->Leasing->validates($data) && $validate_leasing_detail && $truck_same){
                if($this->Leasing->save($data)){
                    $leasing_id = $this->Leasing->id;

                    if($id && $data_local){
                        $this->LeasingDetail->deleteAll(array(
                            'leasing_id' => $leasing_id
                        ));
                    }

                    if(!empty($temp_detail)){
                        foreach ($temp_detail as $key => $value) {
                            $temp_detail[$key]['LeasingDetail']['leasing_id'] = $leasing_id;
                        }

                        $this->LeasingDetail->saveMany($temp_detail);
                    }

                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s leasing'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s leasing'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );
                    $this->redirect(array(
                        'controller' => 'leasings',
                        'action' => 'index'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s leasing'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s leasing'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                }
            }else{
                $text = sprintf(__('Gagal %s leasing'), $msg);
                if(!$validate_leasing_detail){
                    $text .= '<br>* harap isi semua field yang terdapat di leasing detail.';
                }
                if(!$truck_same){
                    $text .= '<br>* Tidak boleh terdapat truk yang sama dalam 1 kontrak leasing.';
                }
                $this->MkCommon->setCustomFlash($text, 'error');
            }
        }else{
            
            if($id && $data_local){
                $this->request->data = $data_local;
                if(!empty($data_local['LeasingDetail'])){
                    foreach ($data_local['LeasingDetail'] as $key => $value) {
                        $truck = $this->Truck->getData('first', array(
                            'conditions' => array(
                                'Truck.id' => $value['truck_id']
                            )
                        ));

                        if(!empty($truck)){
                            $trucks[$value['truck_id']] = $truck['Truck']['nopol'];
                        }
                    }
                }
            }
        
        }

        if(!empty($this->request->data['LeasingDetail']['truck_id'])){
            $temp_arr = array();
            foreach ($this->request->data['LeasingDetail']['truck_id'] as $key => $value) {
                $temp_arr[$key] = array(
                    'truck_id' => $value,
                    'price' => $this->request->data['LeasingDetail']['price'][$key]
                );
            }
            unset($this->request->data['LeasingDetail']);
            $this->request->data['LeasingDetail'] = $temp_arr;
        }

        $this->loadModel('LeasingCompany');
        $leasing_companies = $this->LeasingCompany->find('list', array(
            'conditions' => array(
                'LeasingCompany.status' => 1
            ),
            'fields' => array(
                'LeasingCompany.id', 'LeasingCompany.name'
            )
        ));

        $this->set(compact('leasing_companies', 'trucks'));
        $this->set('active_menu', 'view_leasing');
        $this->render('leasing_form');
    }

    function leasing_companies(){
        if( in_array('view_leasing_companies', $this->allowModule) ) {
            $this->loadModel('LeasingCompany');
            $options = array();

            if(!empty($this->params['named'])){
                $refine = $this->params['named'];

                if(!empty($refine['name'])){
                    $name = urldecode($refine['name']);
                    $this->request->data['LeasingCompany']['name'] = $name;
                    $options['conditions']['LeasingCompany.name LIKE '] = '%'.$name.'%';
                }
            }

            $this->paginate = $this->LeasingCompany->getData('paginate', $options);
            $leasing_companies = $this->paginate('LeasingCompany');

            $this->set('active_menu', 'view_leasing_companies');
            $this->set('sub_module_title', 'Perusahaan Leasing');
            $this->set('leasing_companies', $leasing_companies);
        } else {
            $this->redirect($this->referer());
        }
    }

    function leasing_company_add(){
        if( in_array('insert_leasing_companies', $this->allowModule) ) {
            $this->set('sub_module_title', 'Tambah Perusahaan Leasing');
            $this->doLeasingCompany();
        } else {
            $this->redirect($this->referer());
        }
    }

    function leasing_company_edit($id){
        if( in_array('update_leasing_companies', $this->allowModule) ) {
            $this->loadModel('LeasingCompany');
            $this->set('sub_module_title', 'Rubah Perusahaan Leasing');
            $type_property = $this->LeasingCompany->getData('first', array(
                'conditions' => array(
                    'LeasingCompany.id' => $id
                )
            ));

            if(!empty($type_property)){
                $this->doLeasingCompany($id, $type_property);
            }else{
                $this->MkCommon->setCustomFlash(__('Perusahaan Leasing tidak ditemukan'), 'error');  
                $this->redirect(array(
                    'controller' => 'settings',
                    'action' => 'citys'
                ));
            }
        } else {
            $this->redirect($this->referer());
        }
    }

    function doLeasingCompany($id = false, $data_local = false){
        if(!empty($this->request->data)){
            $data = $this->request->data;
            if($id && $data_local){
                $this->LeasingCompany->id = $id;
                $msg = 'merubah';
            }else{
                $this->loadModel('LeasingCompany');
                $this->LeasingCompany->create();
                $msg = 'menambah';
            }
            $this->LeasingCompany->set($data);

            if($this->LeasingCompany->validates($data)){
                if($this->LeasingCompany->save($data)){
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Perusahaan Leasing'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s Perusahaan Leasing'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 );
                    $this->redirect(array(
                        'controller' => 'leasings',
                        'action' => 'leasing_companies'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Perusahaan Leasing'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s Perusahaan Leasing'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Perusahaan Leasing'), $msg), 'error');
            }
        }else{
            
            if($id && $data_local){
                
                $this->request->data = $data_local;
            }
        }
        
        $this->set('active_menu', 'view_leasing_companies');
        $this->render('leasing_company_form');
    }

    function toggle($id){
        if( in_array('delete_leasing', $this->allowModule) ) {
            $this->loadModel('Leasing');
            $locale = $this->Leasing->getData('first', array(
                'conditions' => array(
                    'Leasing.id' => $id
                )
            ));

            if($locale){
                $value = true;
                if($locale['Leasing']['status']){
                    $value = false;
                }

                $this->Leasing->id = $id;
                $this->Leasing->set('status', $value);
                if($this->Leasing->save()){
                    $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses merubah status Leasing ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal merubah status Leasing ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                }
            }else{
                $this->MkCommon->setCustomFlash(__('Leasing tidak ditemukan.'), 'error');
            }
        }

        $this->redirect($this->referer());
    }

    function leasing_company_toggle($id){
        if( in_array('delete_leasing_companies', $this->allowModule) ) {
            $this->loadModel('LeasingCompany');
            $locale = $this->LeasingCompany->getData('first', array(
                'conditions' => array(
                    'LeasingCompany.id' => $id
                )
            ));

            if($locale){
                $value = true;
                if($locale['LeasingCompany']['status']){
                    $value = false;
                }

                $this->LeasingCompany->id = $id;
                $this->LeasingCompany->set('status', $value);
                if($this->LeasingCompany->save()){
                    $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses merubah status Perusahaan Leasing ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal merubah status Perusahaan Leasing ID #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 ); 
                }
            }else{
                $this->MkCommon->setCustomFlash(__('Perusahaan Leasing tidak ditemukan.'), 'error');
            }
        }

        $this->redirect($this->referer());
    }
}