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
            'status' => 'active',
        ));

        if( !empty($value) ) {
            $value = $this->Asset->AssetGroup->PurchaseOrderAsset->getMerge($value, $id);
            $value = $this->Asset->AssetGroup->PurchaseOrderAsset->PurchaseOrder->DocumentAuth->getMerge($value, $id, 'po');

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
            $value = $this->Asset->AssetGroup->PurchaseOrderAsset->PurchaseOrder->DocumentAuth->getMerge($value, $id, 'po');

            $user_id = $this->MkCommon->filterEmptyField($value, 'PurchaseOrder', 'user_id');
            $grandtotal = $this->MkCommon->filterEmptyField($value, 'PurchaseOrder', 'grandtotal');
            $nodoc = $this->MkCommon->filterEmptyField($value, 'PurchaseOrder', 'nodoc');

            $allow_closing = $this->MkCommon->_callAllowClosing($value, 'PurchaseOrder', 'transaction_date', 'Y-m', false);
            
            if( !empty($allow_closing) ) {
                $value = $this->User->getMerge($value, $user_id);
                $user_position_id = $this->MkCommon->filterEmptyField($value, 'Employe', 'employe_position_id');

                $user_otorisasi_approvals = $this->User->Employe->EmployePosition->Approval->getUserOtorisasiApproval('po', $user_position_id, $grandtotal, $id);
                $show_approval = $this->User->Employe->EmployePosition->Approval->_callAuthApproval($user_otorisasi_approvals);
            } else {
                $show_approval = false;
            }

            $data = $this->request->data;

            if( !empty($show_approval) && !empty($data) ) {
                $data = $this->MkCommon->_callBeforeSaveApproval($data, array(
                    'user_id' => $user_id,
                    'nodoc' => $nodoc,
                    'user_position_id' => $user_position_id,
                    'document_id' => $id,
                    'document_type' => 'po',
                    'document_url' => array(
                        'controller' => 'assets',
                        'action' => 'purchase_order_detail',
                        $id,
                        'admin' => false,
                    ),
                    'document_revised_url' => array(
                        'controller' => 'assets',
                        'action' => 'purchase_order_edit',
                        $id,
                        'admin' => false,
                    ),
                ));
                $result = $this->Asset->AssetGroup->PurchaseOrderAsset->PurchaseOrder->doApproval($data, $id);
                $this->MkCommon->setProcessParams($result, array(
                    'controller' => 'purchases',
                    'action' => 'purchase_orders',
                    'admin' => false,
                ));
            }

            $this->request->data = $this->RjAsset->_callBeforeRenderPO($value);

            $this->set('view', 'detail');
            $this->set('active_menu', 'Purchase Order');
            $this->set(compact(
                'value', 'user_otorisasi_approvals', 'show_approval'
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

    public function import( $download = false ) {
        if(!empty($download)){
            $link_url = FULL_BASE_URL . '/files/assets.xls';
            $this->redirect($link_url);
            exit;
        } else {
            App::import('Vendor', 'excelreader'.DS.'excel_reader2');

            $this->set('module_title', __('Asset'));
            $this->set('active_menu', 'assets');
            $this->set('sub_module_title', __('Import Asset'));

            if(!empty($this->request->data)) { 
                $Zipped = $this->request->data['Import']['importdata'];

                if($Zipped["name"]) {
                    $filename = $Zipped["name"];
                    $source = $Zipped["tmp_name"];
                    $type = $Zipped["type"];
                    $name = explode(".", $filename);
                    $accepted_types = array('application/vnd.ms-excel', 'application/ms-excel');

                    if(!empty($accepted_types)) {
                        foreach($accepted_types as $mime_type) {
                            if($mime_type == $type) {
                                $okay = true;
                                break;
                            }
                        }
                    }

                    $continue = strtolower($name[1]) == 'xls' ? true : false;

                    if(!$continue) {
                        $this->MkCommon->setCustomFlash(__('Maaf, silahkan upload file dalam bentuk Excel.'), 'error');
                        $this->redirect(array('action'=>'import'));
                    } else {
                        $path = APP.'webroot'.DS.'files'.DS.date('Y').DS.date('m').DS;
                        $filenoext = basename ($filename, '.xls');
                        $filenoext = basename ($filenoext, '.XLS');
                        $fileunique = uniqid() . '_' . $filenoext;

                        if( !file_exists($path) ) {
                            mkdir($path, 0755, true);
                        }

                        $targetdir = $path . $fileunique . $filename;
                         
                        ini_set('memory_limit', '96M');
                        ini_set('post_max_size', '64M');
                        ini_set('upload_max_filesize', '64M');

                        if(!move_uploaded_file($source, $targetdir)) {
                            $this->MkCommon->setCustomFlash(__('Maaf, terjadi kesalahan. Silahkan coba lagi, atau hubungi Admin kami.'), 'error');
                            $this->redirect(array('action'=>'import'));
                        }
                    }
                } else {
                    $this->MkCommon->setCustomFlash(__('Maaf, terjadi kesalahan. Silahkan coba lagi, atau hubungi Admin kami.'), 'error');
                    $this->redirect(array('action'=>'import'));
                }

                $xls_files = glob( $targetdir );

                if(empty($xls_files)) {
                    $this->MkCommon->setCustomFlash(__('Tidak terdapat file excel atau berekstensi .xls pada file zip Anda. Silahkan periksa kembali.'), 'error');
                    $this->redirect(array('action'=>'import'));
                } else {
                    $uploadedXls = $this->MkCommon->addToFiles('xls', $xls_files[0]);
                    $uploaded_file = $uploadedXls['xls'];
                    $file = explode(".", $uploaded_file['name']);
                    $extension = array_pop($file);
                    
                    if($extension == 'xls') {
                        $dataimport = new Spreadsheet_Excel_Reader();
                        $dataimport->setUTFEncoder('iconv');
                        $dataimport->setOutputEncoding('UTF-8');
                        $dataimport->read($uploaded_file['tmp_name']);
                        
                        if(!empty($dataimport)) {
                            $successfull_row = 0;
                            $failed_row = 0;
                            $row_submitted = 1;
                            $error_message = '';
                            $textError = array();
                            $data = $dataimport;

                            for ($x=2;$x<=count($data->sheets[0]["cells"]); $x++) {
                                $datavar = array();
                                $flag = true;
                                $i = 1;

                                while ($flag) {
                                    if( !empty($data->sheets[0]["cells"][1][$i]) ) {
                                        $variable = $this->MkCommon->toSlug($data->sheets[0]["cells"][1][$i], '_');
                                        $thedata = !empty($data->sheets[0]["cells"][$x][$i])?$data->sheets[0]["cells"][$x][$i]:NULL;
                                        $$variable = trim($thedata);
                                        $datavar[] = trim($thedata);
                                    } else {
                                        $flag = false;
                                    }
                                    $i++;
                                }

                                if(array_filter($datavar)) {
                                    $no_kontrak = !empty($no_kontrak)?$no_kontrak:false;
                                    $kendaraan_truk_merk = !empty($kendaraan_truk_merk)?$kendaraan_truk_merk:false;
                                    $nama_asset_nopol = !empty($nama_asset_nopol)?$nama_asset_nopol:false;
                                    $tahun_perolehan = !empty($tahun_perolehan)?$tahun_perolehan:false;
                                    $bln = !empty($bln)?$bln:false;
                                    $thn = !empty($thn)?$thn:false;
                                    $perolehan_nilai = !empty($perolehan_nilai)?$this->MkCommon->_callPriceConverter($perolehan_nilai):false;
                                    $ak_penyusutan = !empty($ak_penyusutan)?$this->MkCommon->_callPriceConverter($ak_penyusutan):false;
                                    $dep_mth = !empty($dep_mth)?$dep_mth:false;
                                    $terjual = !empty($terjual)?$terjual:false;
                                    $group_asset = !empty($group_asset)?$group_asset:false;
                                    $periode_ak_penyusutan = !empty($periode_ak_penyusutan)?$periode_ak_penyusutan:'2015-12-31';
                                    $periode_ak_penyusutan = $this->MkCommon->customDate($periode_ak_penyusutan, 'Y-m-d');
                                    $note  = array();

                                    if( !empty($no_kontrak) ) {
                                        $note[]  = sprintf(__('No Kontrak: %s'), $no_kontrak);
                                    }
                                    if( !empty($kendaraan_truk_merk) ) {
                                        $note[]  = sprintf(__('Merk Kendaraan / Truk: %s'), $kendaraan_truk_merk);
                                    }
                                    if( !empty($note) ) {
                                        $note = implode(PHP_EOL, $note);
                                    } else {
                                        $note = false;
                                    }
                                    if( !empty($terjual) ) {
                                        $status_document = 'sold';
                                    } else {
                                        $status_document = 'available';
                                    }

                                    // if( !empty($tgl_perolehan) || !empty($tgl_neraca) ) {
                                    //     $tgl_perolehan = $this->MkCommon->customDate($tgl_perolehan, 'd/m/Y');
                                    // } else {
                                        $tgl_perolehan = sprintf('01/%s/%s', $bln, $tahun_perolehan);
                                        $tgl_neraca = sprintf('01/%s/%s', $bln, $thn);
                                    // }

                                    switch ($group_asset) {
                                        case 'INVT':
                                            $asset_group_id = 2;
                                            break;
                                        
                                        default:
                                            $asset_group_id = 1;
                                            break;
                                    }

                                    $dataArr = array(
                                        'Asset' => array(
                                            'name' => $nama_asset_nopol,
                                            'asset_group_id' => $asset_group_id,
                                            'purchase_date' => $tgl_perolehan,
                                            'neraca_date' => $tgl_neraca,
                                            'nilai_perolehan' => $perolehan_nilai,
                                            'depr_bulan' => $dep_mth,
                                            'nilai_buku' => $perolehan_nilai - $ak_penyusutan,
                                            'note' => $note,
                                            'status_document' => $status_document,
                                            'ak_penyusutan' => $ak_penyusutan,
                                        ),
                                        'AssetDepreciation' => array(
                                            array(
                                                'AssetDepreciation' => array(
                                                    'depr_bulan' => $dep_mth,
                                                    'ak_penyusutan' => $ak_penyusutan,
                                                    'periode' => $periode_ak_penyusutan,
                                                ),
                                            ),
                                        ),
                                    );

                                    $dataArr = $this->RjAsset->_callBeforeSave($dataArr);

                                    $result = $this->Asset->doSave($dataArr);
                                    $status = $this->MkCommon->filterEmptyField($result, 'status');
                                    $validationErrors = $this->MkCommon->filterEmptyField($result, 'validationErrors');
                                    $textError = $this->MkCommon->_callMsgValidationErrors($validationErrors, 'string');

                                    $this->MkCommon->setProcessParams($result, false, array(
                                        'flash' => false,
                                        'noRedirect' => true,
                                    ));

                                    if( $status == 'error' ) {
                                        $failed_row++;
                                        $error_message .= sprintf(__('Gagal pada baris ke %s : Gagal Upload Data. %s'), $row_submitted, $textError) . '<br>';
                                    } else {
                                        $successfull_row++;
                                    }
                                    
                                    $row_submitted++;
                                }
                            }
                        }
                    }
                }

                if(!empty($successfull_row)) {
                    $message_import1 = sprintf(__('Import Berhasil: (%s baris), dari total (%s baris)'), $successfull_row, $row_submitted-1);
                    $this->MkCommon->setCustomFlash($message_import1, 'success');
                }
                
                if(!empty($error_message)) {
                    $this->MkCommon->setCustomFlash($error_message, 'error');
                }

                $this->redirect(array('action'=>'import'));
            }
        }
    }
}