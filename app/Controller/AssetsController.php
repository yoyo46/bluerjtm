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
            $data = $this->RjAsset->_callBeforeSaveGroup($data, $id);

            $result = $this->Asset->AssetGroup->doSave($data, $value, $id);
            $this->MkCommon->setProcessParams($result, array(
                'action' => 'groups',
            ));
            $this->request->data = $this->RjAsset->_callBeforeRenderGroup($this->request->data);

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

    function index(){
        $params = $this->MkCommon->_callRefineParams($this->params);
        $options =  $this->Asset->_callRefineParams($params);

        $this->paginate = $this->Asset->getData('paginate', $options);
        $values = $this->paginate('Asset');
        $values = $this->Asset->getDataList($values);

        $this->RjAsset->_callBeforeRender();
        $this->set('active_menu', 'assets');
        $this->set('sub_module_title', __('Asset'));
        $this->set(compact(
            'values'
        ));
    }

    public function add() {
        $this->set('sub_module_title', __('Tambah Asset'));
        $data = $this->request->data;

        if( !empty($data) ) {
            $data = $this->RjAsset->_callBeforeSave($data);

            $result = $this->Asset->doSave($data);
            $this->MkCommon->setProcessParams($result, array(
                'action' => 'index',
            ));
        }

        $this->request->data = $this->RjAsset->_callBeforeRender($this->request->data);
        $this->set('active_menu', 'assets');
    }

    public function edit( $id = false ) {
        $this->set('sub_module_title', __('Edit Asset'));
        $data = $this->request->data;
        $value = $this->Asset->getData('first', array(
            'conditions' => array(
                'Asset.id' => $id,
            ),
        ));

        if( !empty($value) ) {
            $asset_group_id = $this->MkCommon->filterEmptyField($value, 'Asset', 'asset_group_id');
            $value = $this->Asset->AssetGroup->getMerge($value, $asset_group_id);

            $data = $this->request->data;
            $data = $this->RjAsset->_callBeforeSave($data, $id);

            $result = $this->Asset->doSave($data, $value, $id);
            $this->MkCommon->setProcessParams($result, array(
                'action' => 'index',
            ));
            $this->request->data = $this->RjAsset->_callBeforeRender($this->request->data);

            $this->set('active_menu', 'assets');
            $this->set(compact(
                'value'
            ));
            $this->render('add');
        } else {
            $this->MkCommon->setCustomFlash(__('Group tidak ditemukan.'), 'error');
        }
    }

    public function toggle( $id ) {
        $result = $this->Asset->doDelete( $id );
        $this->MkCommon->setProcessParams($result);
    }

    function get_asset_group ( $id = false ) {
        $value = $this->Asset->AssetGroup->getData('first', array(
            'conditions' => array(
                'AssetGroup.id' => $id,
            ),
        ));

        $this->set(compact(
            'value'
        ));

        $this->layout = false;
        $this->render('/Elements/blocks/assets/group');
    }

    function purchase_order_add(){
        $this->set('sub_module_title', __('Tambah PO Asset'));

        $data = $this->request->data;
        $data = $this->RjAsset->_callBeforeSavePO($data);
        $result = $this->Asset->Truck->PurchaseOrderDetail->PurchaseOrder->doSave($data);
        $this->MkCommon->setProcessParams($result, array(
            'controller' => 'purchases',
            'action' => 'purchase_orders',
            'admin' => false,
        ));
        $this->request->data = $this->RjAsset->_callBeforeRenderPO($this->request->data);

        $this->set('active_menu', 'Purchase Order');
        $this->set(compact(
            'vendors'
        ));
    }

    public function purchase_order_edit( $id = false ) {
        $this->set('sub_module_title', __('Edit PO'));

        $value = $this->PurchaseOrder->getData('first', array(
            'conditions' => array(
                'PurchaseOrder.id' => $id,
            ),
        ));

        if( !empty($value) ) {
            $value = $this->PurchaseOrder->PurchaseOrderDetail->getMerge($value, $id);

            $data = $this->request->data;
            $data = $this->RjPurchase->_callBeforeSavePO($data);
            $result = $this->PurchaseOrder->doSave($data, $value, $id);
            $this->MkCommon->setProcessParams($result, array(
                'controller' => 'purchases',
                'action' => 'purchase_orders',
                'admin' => false,
            ));
            $this->request->data = $this->RjPurchase->_callBeforeRenderPO($this->request->data);

            $vendors = $this->PurchaseOrder->Vendor->getData('list');
            $this->set('active_menu', 'Purchase Order');
            $this->set(compact(
                'vendors', 'value'
            ));
            $this->render('purchase_order_add');
        } else {
            $this->MkCommon->setCustomFlash(__('PO tidak ditemukan.'), 'error');
        }
    }

    public function purchase_order_toggle( $id ) {
        $result = $this->PurchaseOrder->doDelete( $id );
        $this->MkCommon->setProcessParams($result);
    }
}