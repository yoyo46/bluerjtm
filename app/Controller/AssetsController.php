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
            $this->MkCommon->redirectReferer(__('Group tidak ditemukan.'), 'error');
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
            $this->MkCommon->redirectReferer(__('Group tidak ditemukan.'), 'error');
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
        $dataSave = $this->RjAsset->_callBeforeSavePO($data);
        $result = $this->Asset->Truck->PurchaseOrderAsset->PurchaseOrder->doSaveAsset($dataSave);
        $this->MkCommon->setProcessParams($result, array(
            'controller' => 'purchases',
            'action' => 'purchase_orders',
            'admin' => false,
        ));
        $this->request->data = $this->RjAsset->_callBeforeRenderPO($this->request->data);

        $this->set('active_menu', 'Purchase Order');
    }

    public function purchase_order_edit( $id = false ) {
        $this->set('sub_module_title', __('Edit PO'));

        $value = $this->Asset->AssetGroup->PurchaseOrderAsset->PurchaseOrder->getData('first', array(
            'conditions' => array(
                'PurchaseOrder.id' => $id,
            ),
        ), array(
            'status' => 'pending',
        ));

        if( !empty($value) ) {
            $value = $this->Asset->AssetGroup->PurchaseOrderAsset->getMerge($value, $id);

            $data = $this->request->data;
            $dataSave = $this->RjAsset->_callBeforeSavePO($data, $id);
            $result = $this->Asset->AssetGroup->PurchaseOrderAsset->PurchaseOrder->doSaveAsset($dataSave, $value, $id);
            $this->MkCommon->setProcessParams($result, array(
                'controller' => 'purchases',
                'action' => 'purchase_orders',
                'admin' => false,
            ));
            $this->request->data = $this->RjAsset->_callBeforeRenderPO($this->request->data);

            $this->set('active_menu', 'Purchase Order');
            $this->set(compact(
                'value'
            ));
            $this->render('purchase_order_add');
        } else {
            $this->MkCommon->redirectReferer(__('PO tidak ditemukan.'), 'error');
        }
    }

    public function purchase_order_detail( $id ) {
        $this->set('sub_module_title', __('Detail PO'));

        $value = $this->Asset->AssetGroup->PurchaseOrderAsset->PurchaseOrder->getData('first', array(
            'conditions' => array(
                'PurchaseOrder.id' => $id,
            ),
        ));

        if( !empty($value) ) {
            $value = $this->Asset->AssetGroup->PurchaseOrderAsset->getMerge($value, $id);
            $this->request->data = $this->RjAsset->_callBeforeRenderPO($value);

            $this->set('view', 'detail');
            $this->set('active_menu', 'Purchase Order');
            $this->set(compact(
                'value'
            ));
            $this->render('purchase_order_add');
        } else {
            $this->MkCommon->redirectReferer(__('PO tidak ditemukan.'), 'error');
        }
    }

    public function purchase_order_toggle( $id ) {
        $result = $this->PurchaseOrder->doDelete( $id );
        $this->MkCommon->setProcessParams($result);
    }

    public function sells() {
        $this->loadModel('AssetSell');
        $this->set('sub_module_title', 'Penjualan Asset');
        
        $dateFrom = date('Y-m-d', strtotime('-1 Month'));
        $dateTo = date('Y-m-d');

        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));
        $options =  $this->AssetSell->_callRefineParams($params);
        $this->paginate = $this->AssetSell->getData('paginate', $options, array(
            'status' => 'void-active',
        ));
        $values = $this->paginate('AssetSell');
        $values = $this->AssetSell->Coa->getMergeAll($values, 'AssetSell');

        $this->set('active_menu', 'asset_sells');
        $this->set(compact(
            'values'
        ));
    }

    function sell_add(){
        $this->set('sub_module_title', __('Penjualan Asset'));

        $data = $this->request->data;
        $dataSave = $this->RjAsset->_callBeforeSaveSell($data);
        $result = $this->Asset->AssetSellDetail->AssetSell->doSave($dataSave);
        $this->MkCommon->setProcessParams($result, array(
            'controller' => 'assets',
            'action' => 'sells',
            'admin' => false,
        ));
        $this->request->data = $this->RjAsset->_callBeforeRenderSell($this->request->data);

        $this->set('active_menu', 'asset_sells');
    }

    public function sell_edit( $id = false ) {
        $this->set('sub_module_title', __('Edit Penjualan Asset'));

        $value = $this->Asset->AssetSellDetail->AssetSell->getData('first', array(
            'conditions' => array(
                'AssetSell.id' => $id,
            ),
        ), array(
            'status' => 'unposting',
        ));

        if( !empty($value) ) {
            $value = $this->Asset->AssetSellDetail->getMerge($value, $id);
            $asset_id = Set::extract('/AssetSellDetail/AssetSellDetail/asset_id', $value);

            $data = $this->request->data;
            $dataSave = $this->RjAsset->_callBeforeSaveSell($data, $id);
            $result = $this->Asset->AssetSellDetail->AssetSell->doSave($dataSave, $value, $id);
            $this->MkCommon->setProcessParams($result, array(
                'controller' => 'assets',
                'action' => 'sells',
                'admin' => false,
            ));
            $this->request->data = $this->RjAsset->_callBeforeRenderSell($this->request->data, $asset_id);

            $this->set('active_menu', 'asset_sells');
            $this->set(compact(
                'value'
            ));
            $this->render('sell_add');
        } else {
            $this->MkCommon->redirectReferer(__('Penjualan asset tidak ditemukan.'), 'error');
        }
    }

    public function sell_detail( $id ) {
        $this->set('sub_module_title', __('Detail Penjualan Asset'));

        $value = $this->Asset->AssetSellDetail->AssetSell->getData('first', array(
            'conditions' => array(
                'AssetSell.id' => $id,
            ),
        ), array(
            'status' => 'void-active',
        ));

        if( !empty($value) ) {
            $value = $this->Asset->AssetSellDetail->getMerge($value, $id);
            $this->request->data = $this->RjAsset->_callBeforeRenderSell($value);

            $this->set('view', 'detail');
            $this->set('active_menu', 'asset_sells');
            $this->set(compact(
                'value'
            ));
            $this->render('sell_add');
        } else {
            $this->MkCommon->redirectReferer(__('Penjualan asset tidak ditemukan.'), 'error');
        }
    }

    public function sell_toggle( $id ) {
        $is_ajax = $this->RequestHandler->isAjax();
        $action_type = 'asset_sells';
        $msg = array(
            'msg' => '',
            'type' => 'error'
        );
        $value = $this->Asset->AssetSellDetail->AssetSell->getData('first', array(
            'conditions' => array(
                'AssetSell.id' => $id,
            ),
        ));
        $data = $this->request->data;

        if( !empty($value) ) {
            if(!empty($data)){
                $result = $this->Asset->AssetSellDetail->AssetSell->doDelete( $id, $value, $data );
                $msg = array(
                    'msg' => $this->MkCommon->filterEmptyField($result, 'msg'),
                    'type' => $this->MkCommon->filterEmptyField($result, 'status'),
                );
                $this->MkCommon->setProcessParams($result, false, array(
                    'ajaxFlash' => true,
                    'noRedirect' => true,
                ));
            }
        } else {
            $msg = array(
                'msg' => __('Penjualan asset tidak ditemukan'),
                'type' => 'error'
            );
        }

        $modelName = 'AssetSell';
        $canceled_date = $this->MkCommon->filterEmptyField($data, 'AssetSell', 'canceled_date');
        $this->set(compact(
            'msg', 'is_ajax', 'action_type',
            'canceled_date', 'modelName', 'value'
        ));
        $this->render('/Elements/blocks/common/form_delete');
    }

    function asset_documents () {
        $payment_id = $this->MkCommon->filterEmptyField($this->params, 'named', 'payment_id');
        
        $params = $this->MkCommon->_callRefineParams($this->params);
        $options =  $this->Asset->_callRefineParams($params, array(
            'limit' => 10,
        ));

        $this->paginate = $this->Asset->getData('paginate', $options, array(
            'status' => 'available',
        ));
        $values = $this->paginate('Asset');
        $values = $this->Asset->AssetGroup->getMergeAll($values, 'Asset');

        $this->set('module_title', __('Asset'));
        $this->set(compact(
            'values', 'payment_id'
        ));
    }
}