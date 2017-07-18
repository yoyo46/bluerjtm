<?php
App::uses('AppController', 'Controller');
class SpkController extends AppController {
    public $uses = array(
        'Spk',
    );
    public $components = array(
        'RjSpk'
    );
    public $helpers = array(
        'Spk'
    );

    function beforeFilter() {
        parent::beforeFilter();
        $this->set('title_for_layout', __('ERP RJTM | Gudang'));
        $this->set('module_title', __('Gudang'));
    }

    function search( $index = 'index' ){
        $refine = array();
        if(!empty($this->request->data)) {
            $data = $this->request->data;
            $params = $this->MkCommon->getRefineGroupBranch(array(), $data);
            $result = $this->MkCommon->processFilter($data);
            
            $params = array_merge($params, $result);
            $params['action'] = $index;

            $this->redirect($params);
        }
        $this->redirect('/');
    }

    public function index() {
        $this->set('sub_module_title', __('SPK'));
        
        $dateFrom = date('Y-m-01', strtotime('-1 Month'));
        $dateTo = date('Y-m-d');

        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));
        $role = $this->MkCommon->filterEmptyField($params, 'named', 'status');
        $options =  $this->Spk->_callRefineParams($params);

        $this->paginate = $this->Spk->getData('paginate', $options, array(
            'role' => $role,
            'status' => 'all',
        ));
        $values = $this->paginate('Spk');
        $values = $this->Spk->getMergeList($values, array(
            'contain' => array(
                'Vendor' => array(
                    'elements' => array(
                        'status' => 'all',
                    ),
                ),
                'Employe',
                'Truck',
            ),
        ));

        $this->RjSpk->_callBeforeRender();

        $settings = $this->MkCommon->_callSettingGeneral('Product', 'spk_internal_policy', false);
        $spk_internal_policy = $this->MkCommon->filterEmptyField($settings, 'Product', 'spk_internal_policy');

        $this->set('active_menu', 'spk');
        $this->set(compact(
            'values', 'spk_internal_policy'
        ));
    }

    function add(){
        $this->set('sub_module_title', __('Buat SPK'));

        $data = $this->request->data;

        if( !empty($data) ) {
            $data = $this->RjSpk->_callBeforeSave($data);
            $result = $this->Spk->doSave($data);
            $this->MkCommon->setProcessParams($result, array(
                'controller' => 'spk',
                'action' => 'index',
                'admin' => false,
            ));
        }

        $this->RjSpk->_callSpkBeforeRender($data);

        $this->set(array(
            'active_menu' => 'spk',
        ));
    }

    function edit( $id = false ){
        $this->set('sub_module_title', __('Edit SPK'));

        $value = $this->Spk->getData('first', array(
            'conditions' => array(
                'Spk.id' => $id,
            ),
        ), array(
            'role' => 'open',
        ));

        if( !empty($value) ) {
            $value = $this->Spk->getMergeList($value, array(
                'contain' => array(
                    'SpkProduct' => array(
                        'contain' => array(
                            'SpkProductTire',
                        ),
                    ),
                    'SpkProduction',
                    'SpkMechanic',
                ),
            ));
            $data = $this->request->data;

            if( !empty($data) ) {
                $data = $this->RjSpk->_callBeforeSave($data);
                $result = $this->Spk->doSave($data, $value, $id);
                $this->MkCommon->setProcessParams($result, array(
                    'controller' => 'spk',
                    'action' => 'index',
                    'admin' => false,
                ));
            }

            $this->RjSpk->_callSpkBeforeRender($data, $value);

            $this->set(array(
                'active_menu' => 'spk',
            ));
            $this->render('add');
        } else {
            $this->MkCommon->redirectReferer(__('Penerimaan tidak ditemukan.'), 'error');
        }
    }

    function detail( $id = false ){
        $this->set('sub_module_title', __('Lihat SPK'));

        $value = $this->Spk->getData('first', array(
            'conditions' => array(
                'Spk.id' => $id,
            ),
        ), array(
            'status' => 'all',
        ));

        if( !empty($value) ) {
            $value = $this->Spk->getMergeList($value, array(
                'contain' => array(
                    'SpkProduct',
                    'SpkProduction',
                    'SpkMechanic',
                ),
            ));

            $this->RjSpk->_callSpkBeforeRender(array(), $value);

            $this->set(array(
                'view' => true,
                'active_menu' => 'spk',
            ));
            $this->render('add');
        } else {
            $this->MkCommon->redirectReferer(__('Penerimaan tidak ditemukan.'), 'error');
        }
    }

    public function toggle( $id = false ) {
        $result = $this->Spk->doDelete( $id );
        $this->MkCommon->setProcessParams($result);
    }

    function completed($id = null){
        $is_ajax = $this->RequestHandler->isAjax();
        $msg = array(
            'msg' => '',
            'type' => 'error'
        );
        $value = $this->Spk->getData('first', array(
            'conditions' => array(
                'Spk.id' => $id
            ),
        ));

        if( !empty($value) ){
            $data = $this->request->data;

            if(!empty($data['Spk']['complete_date'])){
                $data = $this->MkCommon->dataConverter($data, array(
                    'date' => array(
                        'Spk' => array(
                            'complete_date',
                        ),
                    )
                ));
                $complete_date = Common::hashEmptyField($data, 'Spk.complete_date');
                $complete_time = Common::hashEmptyField($data, 'Spk.complete_time');

                if( !empty($complete_date) && !empty($complete_time) ) {
                    $this->Spk->set('complete_date', __('%s %s', $complete_date, $complete_time));
                }

                $this->Spk->id = $id;
                $this->Spk->set('transaction_status', 'finish');

                if($this->Spk->save()){
                    $msg = array(
                        'msg' => __('Berhasil mengubah status SPK menjadi selesai.'),
                        'type' => 'success'
                    );
                    $this->MkCommon->setCustomFlash( $msg['msg'], $msg['type']);  
                    $this->Log->logActivity( sprintf(__('Berhasil mengubah status SPK menjadi selesai #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id ); 
                }else{
                    $this->Log->logActivity( sprintf(__('Gagal mengubah status SPK menjadi selesai #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id ); 
                }
            } else {
                $this->request->data['Spk']['complete_date'] = date('d/m/Y');
            }
        }else{
            $msg = array(
                'msg' => __('SPK tidak ditemukan'),
                'type' => 'error'
            );
        }

        $modelName = 'Spk';
        $this->set(array(
            'message_alert' => __('Mohon masukan tanggal SPK ini selesai.'),
            '_flash' => false,
        ));
        $this->set(compact(
            'msg', 'is_ajax',
            'modelName'
        ));
        $this->render('/Elements/blocks/common/only_date');
    }

    public function history( $id = null ) {
        $value = $this->Spk->Truck->getData('first', array(
            'conditions' => array(
                'Truck.id' => $id,
            )
        ));

        if( !empty($value) ) {
            $this->paginate = $this->Spk->getData('paginate', array(
                'conditions' => array(
                    'Spk.truck_id' => $id,
                ),
            ), array(
                'status' => 'active',
            ));
            $values = $this->paginate('Spk');
            $values = $this->Spk->getMergeList($values, array(
                'contain' => array(
                    'Vendor' => array(
                        'elements' => array(
                            'status' => 'all',
                        ),
                    ),
                    'Employe',
                    'Truck',
                    'SpkMechanic' => array(
                        'Employe',
                    ),
                ),
            ));

            $this->set('sub_module_title', __('History perbaikan Truk - %s', Common::hashEmptyField($value, 'Truck.nopol')));
            $this->set('active_menu', 'spk');
            $this->set('values', $values);
        } else {
            $this->MkCommon->setCustomFlash(__('Truk tidak ditemukan.'), 'error');
            $this->redirect($this->referer());
        }
    }

    function driver_truck($nopol = null ) {
        $nopol = urldecode($nopol);
        $value = $this->Spk->Truck->getInfoTruck($nopol, null, 'Truck.nopol');

        $this->request->data['Spk']['driver_id'] = Common::hashEmptyField($value, 'Truck.driver_id');

        $drivers = $this->Spk->Driver->getData('list', array(
            'fields' => array(
                'Driver.id', 'Driver.driver_name'
            ),
        ), array(
            'branch' => false,
        ));
        $this->set(compact(
            'value', 'drivers'
        ));
        $this->render('/Elements/blocks/spk/forms/driver');
    }

    function wheel_position( $id = null, $qty = null ) {
        $this->set(array(
            'id' => $id,
            'qty' => $qty,
        ));
    }
}