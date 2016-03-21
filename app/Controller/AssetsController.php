<?php
App::uses('AppController', 'Controller');
class AssetsController extends AppController {
	public $uses = array(
        'Asset',
    );
    public $components = array(
        'RjAsset',
    );

    function beforeFilter() {
        parent::beforeFilter();
        $this->set('title_for_layout', __('ERP RJTM | Asset'));
        $this->set('module_title', __('Asset'));
    }

    function search( $index = 'index', $param_get = false ){
        $refine = array();
        $params = array();

        if(!empty($this->request->data)) {
            $data = $this->request->data;
            $result = $this->MkCommon->processFilter($data);
            $params = $this->MkCommon->getRefineGroupBranch($params, $data);

            $params = array_merge($params, $result);
            $params['action'] = $index;

            if( !empty($param_get) ) {
                $params[] = $param_get;
            }

            $this->redirect($params);
        }
        $this->redirect('/');
    }

    function groups(){
        $this->loadModel('AssetGroup');
        $params = $this->MkCommon->_callRefineParams($this->params);
        $options =  $this->Asset->AssetGroup->_callRefineParams($params);

        $this->paginate = $this->AssetGroup->getData('paginate', $options);
        $values = $this->paginate('AssetGroup');

        $this->set('active_menu', 'asset_groups');
        $this->set('sub_module_title', __('Group'));
        $this->set(compact(
            'values'
        ));
    }

    function group_add(){
        $this->set('sub_module_title', __('Tambah Group'));
        $data = $this->request->data;

        if( !empty($data) ) {
            $data = $this->RjAsset->_callBeforeSaveGroup($data);

            $result = $this->Asset->AssetGroup->doSave($data);
            $this->MkCommon->setProcessParams($result, array(
                'action' => 'groups',
            ));
        }

        $this->request->data = $this->RjAsset->_callBeforeRenderGroup($this->request->data);
        $this->set('active_menu', 'asset_groups');
    }

    public function group_edit( $id = false ) {
        $this->set('sub_module_title', __('Edit Group'));

        $value = $this->Asset->AssetGroup->getData('first', array(
            'conditions' => array(
                'AssetGroup.id' => $id,
            ),
        ));

        if( !empty($value) ) {
            $value = $this->Asset->AssetGroup->AssetGroupCoa->getMerge($value, $id);

            $data = $this->request->data;
            $data = $this->RjPurchase->_callBeforeSaveGroup($data);
            
            $result = $this->Asset->AssetGroup->doSave($data, $value, $id);
            $this->MkCommon->setProcessParams($result, array(
                'action' => 'groups',
            ));
            $this->request->data = $this->RjPurchase->_callBeforeRenderGroup($this->request->data);

            $this->set('active_menu', 'asset_groups');
            $this->set(compact(
                'value'
            ));
            $this->render('group_add');
        } else {
            $this->MkCommon->setCustomFlash(__('Group tidak ditemukan.'), 'error');
        }
    }

    public function group_toggle( $id ) {
        $result = $this->Asset->AssetGroup->doDelete( $id );
        $this->MkCommon->setProcessParams($result);
    }
}