<?php
App::uses('AppController', 'Controller');
class TitipanController extends AppController {
	public $uses = array(
        'Titipan',
    );
    public $components = array(
        'RmReport'
    );
    public $module_title = 'Titipan';

    function beforeFilter() {
        parent::beforeFilter();
        $this->set('title_for_layout', __('ERP RJTM | %s', $this->module_title));
        $this->set('module_title', $this->module_title);
        $this->set('active_menu', 'titipan');
    }

    public function search ( $action, $addParam = false ) {
        $params = Common::_search($this, $action, $addParam);
        $this->redirect($params);
    }

    public function bypass_search ( $action, $addParam = false ) {
        $params = Common::_search($this, $action, $addParam);

        $params['bypass'] = true;
        $this->redirect($params);
    }

    function index(){
        $params = $this->params->params;
        $dateFrom = Common::hashEmptyField($params, 'named.DateFrom');
        $dateTo = Common::hashEmptyField($params, 'named.DateTo');

        $params = $this->MkCommon->_callRefineParams($params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));

        $options =  $this->Titipan->_callRefineParams($params, array(
            'limit' => Configure::read('__Site.config_pagination'),
        ));

        $this->paginate = $this->Titipan->getData('paginate', $options);
        $values = $this->paginate('Titipan');
        $values = $this->Titipan->getMergeList($values, array(
            'contain' => array(
                'TtujPayment',
            ),
        ));

        $this->MkCommon->_layout_file(array(
            'select',
        ));

        $coas = $this->User->Journal->Coa->_callOptGroup();
        $this->set('sub_module_title', $this->module_title);
        $this->set('values', $values);
        $this->set('coas', $coas);
    }

    function add(){
        $this->set('sub_module_title', __('Tambah %s', $this->module_title));
        $this->doTitipan();
    }

    function edit($id = NULL){
        $this->set('sub_module_title', __('Ubah %s', $this->module_title));
        $value = $this->Titipan->getData('first', array(
            'conditions' => array(
                'Titipan.id' => $id
            ),
            'contain' => array(
                'TitipanDetail',
            ),
        ), array(
            'status' => 'unposting',
        ));

        if(!empty($value)){
            $this->doTitipan($id, $value);
            $this->render('add');
        }else{
            $this->MkCommon->setCustomFlash(__('%s tidak ditemukan', $this->module_title), 'error');  
            $this->redirect(array(
                'action' => 'index'
            ));
        }
    }

    function doTitipan($id = false, $value = false){
        $grand_total = Common::hashEmptyField($value, 'Titipan.grandtotal');
        $user_id = Common::hashEmptyField($value, 'Titipan.user_id');

        if(!empty($this->request->data)){
            $no_data = false;
            $data = $this->request->data;
            $data = $this->MkCommon->dataConverter($data, array(
                'date' => array(
                    'Titipan' => array(
                        'transaction_date',
                    ),
                )
            ));
            $this->MkCommon->_callAllowClosing($data, 'Titipan.transaction_date');
            $data = Common::_callCheckCostCenter($data, 'Titipan');

            $validate = true;
            $grandtotal = 0;

            $coa_id = Common::hashEmptyField($data, 'Titipan.coa_id');
            $note = Common::hashEmptyField($data, 'Titipan.note');
            $nodoc = Common::hashEmptyField($data, 'Titipan.nodoc');

            $transaction_date = Common::hashEmptyField($data, 'Titipan.transaction_date');
            $transaction_status = Common::hashEmptyField($data, 'Titipan.transaction_status');

            $data['Titipan']['branch_id'] = Configure::read('__Site.config_branch_id');
            $data['Titipan']['user_id'] = $this->user_id;
            $redirectUrl = array(
                'controller' => 'titipan',
                'action' => 'add',
                'admin' => false,
            );

            if( empty($nodoc) ) {
                $data['Titipan']['nodoc'] = $this->Titipan->generateNoDoc();
            }

            if($id && $value){
                $data['Titipan']['id'] = $id;
                $msg = 'mengubah';
            }else{
                $msg = 'menambah';
            }

            if(!empty($data['TitipanDetail']['driver_id'])){
                $arr_list = array();

                foreach ($data['TitipanDetail']['driver_id'] as $key => $driver_id) {
                    $note = (!empty($data['TitipanDetail']['note'][$key])) ? $data['TitipanDetail']['note'][$key] : false;
                    $total_detail = (!empty($data['TitipanDetail']['total'][$key])) ? str_replace(array( ',' ), array( '' ), $data['TitipanDetail']['total'][$key]) : 0;

                    $grandtotal += $total_detail;

                    $arr_list[] = array(
                        'driver_id' => $driver_id,
                        'note' => $note,
                        'total' => $total_detail,
                    );
                }

                $data['TitipanDetail'] = $arr_list;
                $data['Titipan']['grandtotal'] = $grandtotal;
            }else{
                $validate = false;
            }

            $flag = $this->Titipan->saveAll($data, array(
                'validate' => 'only',
                'deep' => true,
            ));

            if( $validate && $flag){
                if(!empty($id)){
                    $this->Titipan->TitipanDetail->deleteAll(array(
                        'TitipanDetail.titipan_id' => $id
                    ));
                }

                if($this->Titipan->saveAll($data, array(
                    'deep' => true,
                ))){
                    $id = $this->Titipan->id;
                    $nodoc = Common::hashEmptyField($data, 'Titipan.nodoc');

                    $coaTitipan = $this->User->Coa->CoaSettingDetail->getMerge(array(), 'Titipan', 'CoaSettingDetail.label');
                    $titipan_coa_id = !empty($coaTitipan['CoaSettingDetail']['coa_id'])?$coaTitipan['CoaSettingDetail']['coa_id']:false;

                    if(!empty($data['TitipanDetail'])){
                        if( !empty($note) ) {
                            $title = $note;
                        } else {
                            $title = __('%s Tgl %s', $this->module_title, Common::formatDate($transaction_date, 'd M Y'));
                        }

                        $coaOptions = array(
                            'document_id' => $id,
                            'title' => $title,
                            'document_no' => $nodoc,
                            'type' => 'titipan',
                            'date' => $transaction_date,
                        );

                        foreach ($data['TitipanDetail'] as $key => $value) {
                            $total = $value['total'];

                            if( $transaction_status == 'posting' && !empty($titipan_coa_id) ) {
                                $this->User->Journal->setJournal($total, array(
                                    'debit' => $titipan_coa_id
                                ), $coaOptions);
                            }
                        }

                        if( $transaction_status == 'posting' && !empty($titipan_coa_id) ) {
                            $this->User->Journal->setJournal($grandtotal, array(
                                'credit' => $coa_id,
                            ), $coaOptions);
                        }
                    }

                    $this->params['old_data'] = $value;
                    $this->params['data'] = $data;

                    $this->Log->logActivity( sprintf(__('Sukses %s %s #%s'), $msg, $this->module_title, $this->Titipan->id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );

                    $noref = str_pad($this->Titipan->id, 6, '0', STR_PAD_LEFT);
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s %s #%s'), $msg, $this->module_title, $noref), 'success');

                    $this->redirect($redirectUrl);
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s %s'), $msg, $this->module_title), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s %s #%s'), $msg, $this->module_title, $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
                }
            }else{
                $text = sprintf(__('Gagal %s %s'), $msg, $this->module_title);
                if($validate){
                    $text .= __(', COA harap di pilih');
                }
                $this->MkCommon->setCustomFlash($text, 'error');
            }
        }else{
            if($id && $value){
                $this->request->data = $data = $value;
                $this->request->data['Titipan']['transaction_date'] = $this->MkCommon->getDate($this->request->data['Titipan']['transaction_date'], true);
            }
        }

        if(!empty($data['TitipanDetail'])){
            $this->loadModel('Coa');

            foreach ($data['TitipanDetail'] as $key => $value) {
                $driver_id = Common::hashEmptyField($value, 'driver_id');
                $value = $this->Titipan->TitipanDetail->Driver->getMerge($value, $driver_id);

                $driver_name = Common::hashEmptyField($value, 'Driver.driver_name');
                $driver_no_id = Common::hashEmptyField($value, 'Driver.no_id');

                if(!empty($driver_name)){
                    $data['TitipanDetail'][$key]['driver_name'] = $driver_name;
                    $data['TitipanDetail'][$key]['driver_no_id'] = $driver_no_id;
                }
            }

            $detail_data['TitipanDetail'] = $data['TitipanDetail'];
            $this->set('detail_data', $detail_data);
        }

        $coas = $this->GroupBranch->Branch->BranchCoa->getCoas();

        if( !empty($id) ) {
            $this->MkCommon->getLogs($this->params['controller'], array( 'edit', 'add', 'delete' ), $id);
        }
        
        $this->MkCommon->_layout_file(array(
            'select',
        ));
        $this->set(compact(
            'coas', 'id', 'value'
        ));
    }

    function detail($id = false){
        $this->set('sub_module_title', __('Detail %s', $this->module_title));
        $value = $this->Titipan->getData('first', array(
            'conditions' => array(
                'Titipan.id' => $id,
            ),
        ), array(
            'branch' => false,
        ));

        if( !empty($value) ) {
            $user_id = Common::hashEmptyField($value, 'Titipan.user_id');
            $coa_id = Common::hashEmptyField($value, 'Titipan.coa_id');

            $transaction_date = Common::hashEmptyField($value, 'Titipan.transaction_date');
            $nodoc = Common::hashEmptyField($value, 'Titipan.nodoc');
            $total = Common::hashEmptyField($value, 'Titipan.grandtotal', 0);
            $note = Common::hashEmptyField($value, 'Titipan.note');
            
            $allow_closing = $this->MkCommon->_callAllowClosing($value, 'Titipan', 'transaction_date', 'Y-m', false);
            
            $value = $this->User->getMerge($value, $user_id);
            $value = $this->Titipan->Coa->getMerge($value, $coa_id);

            $value = $this->Titipan->getMergeList($value, array(
                'contain' => array(
                    'TitipanDetail' => array(
                        'contain' => array(
                            'Driver' => array(
                                'elements' => array(
                                    'branch' => false,
                                ),
                            ),
                        ),
                    ),
                ),
            ));

            $this->MkCommon->getLogs($this->params['controller'], array( 'edit', 'add', 'delete' ), $id);
            $this->set(compact(
                'value'
            ));
        } else {
            $this->MkCommon->setCustomFlash(__('%s tidak ditemukan.', $this->module_title), 'error');
            $this->redirect($this->referer());
        }
    }

    function delete($id = NULL){
        $value = $this->Titipan->getData('first', array(
            'conditions' => array(
                'Titipan.id' => $id,
                'Titipan.ttuj_payment_id' => 0,
                'Titipan.transaction_status <>' => 'void',
            )
        ), array(
            'branch' => false,
        ));

        if(!empty($value)){
            $this->MkCommon->_callAllowClosing($value, 'Titipan', 'transaction_date');

            $value = $this->Titipan->getMergeList($value, array(
                'contain' => array(
                    'TitipanDetail',
                ),
            ));

            $nodoc = Common::hashEmptyField($value, 'Titipan.nodoc');
            $transaction_date = Common::hashEmptyField($value, 'Titipan.transaction_date');
            $document_coa_id = Common::hashEmptyField($value, 'Titipan.coa_id');
            $grand_total = Common::hashEmptyField($value, 'Titipan.grandtotal');
            $note = Common::hashEmptyField($value, 'Titipan.note');

            $this->Titipan->id = $id;
            $this->Titipan->set('transaction_status', 'void');

            if($this->Titipan->save()){
                $coaTitipan = $this->User->Coa->CoaSettingDetail->getMerge(array(), 'Titipan', 'CoaSettingDetail.label');
                $titipan_coa_id = !empty($coaTitipan['CoaSettingDetail']['coa_id'])?$coaTitipan['CoaSettingDetail']['coa_id']:false;

                if( !empty($value['TitipanDetail']) && !empty($titipan_coa_id) ) {
                    foreach ($value['TitipanDetail'] as $key => $value) {
                        $driver_id = Common::hashEmptyField($value, 'TitipanDetail.driver_id');
                        $total = Common::hashEmptyField($value, 'TitipanDetail.total');


                        if( !empty($note) ) {
                            $title = __('<i>Pembatalan</i> ').$note;
                        } else {
                            $title = sprintf(__('<i>Pembatalan</i> %s Tgl %s'), $this->module_title, Common::formatDate($transaction_date, 'd M Y'));
                        }

                        $this->User->Journal->setJournal($total, array(
                            'credit' => $titipan_coa_id,
                        ), array(
                            'document_id' => $id,
                            'title' => $title,
                            'document_no' => $nodoc,
                            'type' => 'void_titipan',
                            'date' => $transaction_date,
                        ));
                    }
                }

                if( !empty($title) ) {
                    $this->User->Journal->setJournal($grand_total, array(
                        'debit' => $document_coa_id,
                    ), array(
                        'document_id' => $id,
                        'title' => $title,
                        'document_no' => $nodoc,
                        'type' => 'void_titipan',
                        'date' => $transaction_date,
                    ));
                }

                $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
                $this->MkCommon->setCustomFlash(sprintf(__('Sukses merubah status %s #%s.'), $this->module_title, $noref), 'success');
                $this->Log->logActivity( sprintf(__('Sukses merubah status %s ID #%s'), $this->module_title, $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal merubah status %s ID #%s'), $this->module_title, $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id ); 
            }
        }else{
            $this->MkCommon->setCustomFlash(__('%s tidak ditemukan.', $this->module_title), 'error');
        }

        $this->redirect($this->referer());
    }

    function bypass_drivers(){
        $this->loadModel('Driver');

        $params = $this->params->params;
        $params = $this->MkCommon->_callRefineParams($params);
        $options =  $this->Driver->_callRefineParams($params, array(
            'limit' => Configure::read('__Site.config_pagination'),
        ));

        $this->paginate = $this->Driver->getData('paginate', $options, array(
            'branch' => false,
        ));
        $values = $this->paginate('Driver');

        $data_action = 'browse-cash-banks';
        $title = __('List Supir');

        $this->set(compact(
            'data_action', 'title', 'values'
        ));
    }

    public function reports() {
        $params = $this->MkCommon->_callRefineParams($this->params);

        $dataReport = $this->RmReport->_callDataTitipan_reports($params, 30, 0, true);
        $values = Common::hashEmptyField($dataReport, 'data');

        $this->RmReport->_callBeforeView($params, __('Titipan'));
        $this->MkCommon->_layout_file(array(
            'select',
        ));
        $this->set(array(
            'values' => $values,
            'active_menu' => 'titipan_reports',
        ));
    }

    public function kartu_titipan( $data_action = NULL ) {
        $dateFrom = date('Y-m-d', strtotime('-1 Month'));
        $dateTo = date('Y-m-d');

        $params = $this->params->params;
        $id = Common::hashEmptyField($params, 'named.id');
        $dateFrom = Common::hashEmptyField($params, 'named.DateFrom', $dateFrom);
        $dateTo = Common::hashEmptyField($params, 'named.DateTo', $dateTo);

        $params = $this->MkCommon->_callRefineParams($params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));

        $dateFrom = Common::hashEmptyField($params, 'named.DateFrom');

        if( !empty($id) ) {
            $this->loadModel('TitipanDetail');

            $options =  $this->Titipan->_callRefineParams($params, array(
                'contain' => array(
                    'Titipan',
                ),
                'conditions' => array(
                    'TitipanDetail.driver_id' => $id,
                ),
                'order' => array(
                    'Titipan.transaction_date' => 'ASC',
                    'TitipanDetail.titipan_id' => 'ASC',
                ),
            ));
            $options = $this->Titipan->getData('paginate', $options, array(
                'status' => 'posting',
            ));
            $values = $this->TitipanDetail->getData('all', $options);

            $this->TitipanDetail->virtualFields['total_total'] = 'SUM(TitipanDetail.total)';
            $summaryOptions = $this->Titipan->getData('paginate', array(
                'conditions' => array(
                    'TitipanDetail.driver_id' => $id,
                    'DATE_FORMAT(Titipan.transaction_date, \'%Y-%m-%d\') <' => $dateFrom,
                ),
                'contain' => array(
                    'Titipan',
                ),
                'group' => array(
                    'TitipanDetail.driver_id',
                ),
            ), array(
                'status' => 'posting',
            ));
            $summaryBalance = $this->TitipanDetail->getData('first', $summaryOptions);
            $beginingBalance = Common::hashEmptyField($summaryBalance, 'TitipanDetail.total_total', 0);

            $this->set(compact(
                'values', 'staff', 'beginingBalance'
            ));

            $dateFrom = $this->MkCommon->filterEmptyField($params, 'named', 'DateFrom');
            $dateTo = $this->MkCommon->filterEmptyField($params, 'named', 'DateTo');
            $module_title = __('Kartu Titipan');

            if( !empty($dateFrom) && !empty($dateTo) ) {
                $module_title .= sprintf(' Periode %s', $this->MkCommon->getCombineDate($dateFrom, $dateTo));
            }

            $this->set(array(
                'active_menu' => 'titipan_reports',
                'values' => $values,
                'module_title' => $module_title,
                'data_action' => $data_action,
                'beginingBalance' => $beginingBalance,
                'id' => $id,
            ));

            if($data_action == 'pdf'){
                $this->layout = 'pdf';
            }else if($data_action == 'excel'){
                $this->layout = 'ajax';
            }
        } else {
            $this->MkCommon->redirectReferer(__('Supir tidak ditemukan'), 'error', array(
                'action' => 'reports',
            ));
        }
    }
}