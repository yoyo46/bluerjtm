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
        
        $dateFrom = date('Y-m-d', strtotime('-1 Month'));
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
                    'SpkProduct',
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
}