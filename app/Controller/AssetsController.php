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
        $this->MkCommon->_layout_file(array(
            'select',
        ));
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

    public function reports( $data_action = false ) {
        $this->loadModel('Asset');
        $values = array();
        $year = date('Y');
        $allow_branch_id = Configure::read('__Site.config_allow_branch_id');

        $this->Asset->virtualFields['month_use'] = 'TIMESTAMPDIFF(MONTH, Asset.purchase_date, DATE_FORMAT(NOW(), \'%Y-%m-%d\'))';
        $options =  $this->Asset->getData('paginate', array(
            'conditions' => array(
                'Asset.branch_id' => $allow_branch_id,
            ),
            'order' => array(
                'Asset.created' => 'DESC',
                'Asset.id' => 'DESC',
            ),
        ), array(
            'branch' => false,
        ));

        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'param_year' => $year,
        ));
        $year = $this->MkCommon->filterEmptyField($params, 'named', 'year');
        $options =  $this->Asset->_callRefineParams($params, $options);

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            // Custom Otorisasi
            $options = $this->MkCommon->getConditionGroupBranch( $refine, 'Asset', $options );
        }

        if( !empty($data_action) ){
            $values = $this->Asset->find('all', $options);
        } else {
            $options['limit'] = Configure::read('__Site.config_pagination');
            $this->paginate = $options;
            $values = $this->paginate('Asset');
        }

        if( !empty($values) ) {
            foreach ($values as $key => $value) {
                $id = $this->MkCommon->filterEmptyField($value, 'Asset', 'id');
                $truck_id = $this->MkCommon->filterEmptyField($value, 'Asset', 'truck_id');
                $asset_group_id = $this->MkCommon->filterEmptyField($value, 'Asset', 'asset_group_id');
                $status_document = $this->MkCommon->filterEmptyField($value, 'Asset', 'status_document');

                $value = $this->Asset->AssetGroup->getMerge($value, $asset_group_id);
                $value = $this->Asset->Truck->getMerge($value, $truck_id);
                $value = $this->Asset->Truck->LeasingDetail->getMerge($value, $truck_id);
                $leasing_id = $this->MkCommon->filterEmptyField($value, 'LeasingDetail', 'leasing_id');
                $value = $this->Asset->Truck->LeasingDetail->Leasing->getMerge($value, $leasing_id);

                $this->Asset->AssetDepreciation->virtualFields['month'] = 'DATE_FORMAT(AssetDepreciation.periode, \'%m\')';

                $last_depr = $this->Asset->AssetDepreciation->getData('first', array(
                    'conditions' => array(
                        'DATE_FORMAT(AssetDepreciation.periode, \'%Y\')' => $year-1,
                        'AssetDepreciation.asset_id' => $id,
                    ),
                ));

                $value['Asset']['last_ak_penyusutan'] = $this->MkCommon->filterEmptyField($last_depr, 'AssetDepreciation', 'ak_penyusutan');
                $value['AssetDepr'] = $this->Asset->AssetDepreciation->getData('list', array(
                    'conditions' => array(
                        'DATE_FORMAT(AssetDepreciation.periode, \'%Y\')' => $year,
                        'AssetDepreciation.asset_id' => $id,
                    ),
                    'fields' => array(
                        'AssetDepreciation.month', 'AssetDepreciation.depr_bulan',
                    ),
                    'order' => array(
                        'AssetDepreciation.periode' => 'ASC',
                        'AssetDepreciation.id' => 'ASC',
                    ),
                ));

                if( $status_document == 'sold' ) {
                    $assetSell = $this->Asset->AssetSellDetail->getData('first', array(
                        'conditions' => array(
                            'AssetSell.transaction_status' => 'posting',
                            'AssetSellDetail.asset_id' => $id,
                        ),
                        'contain' => array(
                            'AssetSell',
                        ),
                        'order' => array(
                            'AssetSellDetail.id' => 'DESC',
                        ),
                    ));
                    $value['Asset']['price_sold'] = $this->MkCommon->filterEmptyField($assetSell, 'AssetSellDetail', 'price');
                }
                
                $values[$key] = $value;
            }
        }

        $assetGroups = $this->Asset->AssetGroup->getData('list', array(
            'fields' => array(
                'AssetGroup.id', 'AssetGroup.group_name',
            ),
        ));

        $module_title = sprintf(__('Laporan Asset %s'), $year);
        $this->set('sub_module_title', $module_title);
        $this->set('active_menu', 'asset_reports');
        $this->set(compact(
            'values', 'module_title', 'data_action',
            'year', 'assetGroups'
        ));

        if($data_action == 'pdf'){
            $this->layout = 'pdf';
        }else if($data_action == 'excel'){
            $this->layout = 'ajax';
        } else {
            $this->MkCommon->_layout_file(array(
                'select',
                'freeze',
            ));
        }
    }
}