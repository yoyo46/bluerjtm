<?php
App::uses('AppController', 'Controller');
class TtujsController extends AppController {
    public $uses = array(
        'Ttuj',
    );
    public $components = array(
        'RjTtuj',
    );
    public $helper = array(
        'Ttuj',
    );

    function beforeFilter() {
        parent::beforeFilter();
        $this->set('title_for_layout', __('ERP RJTM | TTUJ'));
    }

    function search( $index = 'index', $id = false, $data_action = false ){
        $refine = array();
        if(!empty($this->request->data)) {
            $data = $this->request->data;
            $result = $this->MkCommon->getRefineGroupBranch(array(), $data);

            $params = $this->MkCommon->processFilter($data);

            if(!empty($id)){
                array_push($params, $id);
            }
            if(!empty($data_action)){
                array_push($params, $data_action);
            }

            $params = array_merge($params, $result);
            $params['action'] = $index;
            $this->redirect($params);
        }
        $this->redirect('/');
    }

    public function report_recap_sj( $data_action = false ) {
        $allow_branch_id = Configure::read('__Site.config_allow_branch_id');
        $dateFrom = date('Y-m-01');
        $dateTo = date('Y-m-t');

        // $this->Ttuj->unBindModel(array(
        //     'hasMany' => array(
        //         'Revenue',
        //     )
        // ));
        // $this->Ttuj->bindModel(array(
        //     'hasOne' => array(
        //         'Revenue' => array(
        //             'className' => 'Revenue',
        //             'foreignKey' => 'ttuj_id',
        //             'conditions' => array(
        //                 'Revenue.status' => 1,
        //             ),
        //         )
        //     )
        // ), false);

        $options = array(
            'conditions' => array(
                // 'Revenue.id NOT' => NULL,
                'Ttuj.branch_id' => $allow_branch_id,
            ),
            // 'contain' => array(
            //     'Revenue',
            // ),
            'order'=> array(
                'Ttuj.id' => 'DESC',
            ),
            'group' => array(
                'Ttuj.id'
            ),
        );

        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));
        $options =  $this->Ttuj->_callRefineParams($params, $options);
        $options = $this->MkCommon->getConditionGroupBranch( $params, 'Ttuj', $options );

        if( !empty($data_action) ){
            $values = $this->Ttuj->getData('all', $options, true, array(
                'status' => 'commit',
            ));
        } else {
            $this->paginate = $this->Ttuj->getData('paginate', array_merge($options, array(
                'limit' => Configure::read('__Site.config_pagination'),
            )), true, array(
                'status' => 'commit',
            ));
            $values = $this->paginate('Ttuj');
        }

        if( !empty($values) ) {
            foreach ($values as $key => $value) {
                $id = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'id');

                $value = $this->Ttuj->getSumUnit($value, $id, false, 'tgl_surat_jalan');
                $value = $this->Ttuj->Revenue->getPaid($value, $id, 'unit');
                $value = $this->Ttuj->Revenue->getPaid($value, $id, 'invoiced');
                $value = $this->Ttuj->Revenue->RevenueDetail->getToCity($value, $id);

                $value = $this->Ttuj->getMergeList($value, array(
                    'contain' => array(
                        'DriverPengganti' => array(
                            'uses' => 'Driver',
                            'primaryKey' => 'id',
                            'foreignKey' => 'driver_pengganti_id',
                            'elements' => array(
                                'branch' => false,
                            ),
                        ),
                        'Driver' => array(
                            'elements' => array(
                                'branch' => false,
                            ),
                        ),
                    ),
                ));

                $values[$key] = $value;
            }
        }

        $this->RjTtuj->_callBeforeViewReportRecapSj($params);
        $this->MkCommon->_callBeforeViewReport($data_action, array(
            'layout_file' => array(
                'select',
                'freeze',
            ),
        ));
        
        $module_title = __('Laporan Rekap Penerimaan Surat Jalan');

        if( !empty($dateFrom) && !empty($dateTo) ) {
            $period_text = sprintf(' Periode %s', $this->MkCommon->getCombineDate($dateFrom, $dateTo));
        } else {
            $period_text = '';
        }

        $this->set(array(
            'values' => $values,
            'data_action' => $data_action,
            'module_title' => $module_title,
            'sub_module_title' => $period_text,
        ));
    }

    public function bon_biru() {
        $this->loadModel('BonBiru');

        $this->set('active_menu', 'bon_biru');
        $this->set('sub_module_title', __('Bon Biru'));

        $params = $this->MkCommon->_callRefineParams($this->params);
        $options =  $this->BonBiru->_callRefineParams($params, array(
            'group' => array(
                'BonBiru.id',
            ),
        ));

        $this->paginate = $this->BonBiru->getData('paginate', $options, array(
            'status' => 'all',
        ));
        $values = $this->paginate('BonBiru');

        if( !empty($values) ) {
            foreach ($values as $key => $value) {
                $id = $this->MkCommon->filterEmptyField($value, 'BonBiru', 'id');

                $value['BonBiru']['cnt_ttuj'] = $this->BonBiru->BonBiruDetail->_callTotalTtujDiterima( $id );

                $values[$key] = $value;
            }
        }

        $this->MkCommon->_layout_file('select');

        $this->set(compact(
            'values'
        ));
    }
    
    function bon_biru_add(){
        $module_title = __('Penerimaan Bon Biru');
        $this->set('sub_module_title', $module_title);

        $this->doBonBiru();
    }

    function bon_biru_edit( $id = false, $disabled_edit = false ){
        $module_title = __('Edit Bon Biru');
        $this->set('sub_module_title', $module_title);

        $head_office = Configure::read('__Site.config_branch_head_office');
        $elementRevenue = false;

        if( !empty($head_office) ) {
            $elementRevenue = array(
                'branch' => false,
            );
        }
        if( !empty($disabled_edit) ) {
            $elementRevenue['status'] = 'all';
        }

        $value = $this->Ttuj->BonBiruDetail->BonBiru->getData('first', array(
            'conditions' => array(
                'BonBiru.id' => $id
            ),
        ), $elementRevenue);
        $value = $this->Ttuj->BonBiruDetail->getMerge($value, $id);

        $this->doBonBiru( $id, $value, $disabled_edit );
    }

    function bon_biru_detail( $id = false ){
        $this->bon_biru_edit($id, true);
    }

    function doBonBiruDetail ( $dataDetail, $data, $bon_biru_id = false, $value = false ) {
        $status = true;
        $tgl_bon_biru = $this->MkCommon->filterEmptyField($data, 'BonBiru', 'tgl_bon_biru');
        $data = $this->request->data;
        $msgError = array();

        if( !empty($bon_biru_id) ) {
            if( !empty($value) ) {
                $ttujsID = Set::extract('/BonBiruDetail/BonBiruDetail/ttuj_id', $value);

                if( !empty($ttujsID) ) {
                    $this->Ttuj->updateAll( array(
                        'Ttuj.status_bon_biru' => "'none'",
                    ), array(
                        'Ttuj.id' => $ttujsID,
                    ));
                }
            }

            $this->Ttuj->BonBiruDetail->updateAll( array(
                'BonBiruDetail.status' => 0,
            ), array(
                'BonBiruDetail.bon_biru_id' => $bon_biru_id,
            ));
        }

        if( !empty($dataDetail) ) {
            foreach ($dataDetail as $key => $ttuj_id) {
                $note = !empty($data['BonBiruDetail']['note'][$key])?$data['BonBiruDetail']['note'][$key]:false;

                $dataBonBiruDetail = array(
                    'BonBiruDetail' => array(
                        'ttuj_id' => $ttuj_id,
                        'note' => $note,
                    ),
                );
                $dataBonBiruDetail = $this->Ttuj->getMerge($dataBonBiruDetail, $ttuj_id);

                $this->request->data['Ttuj'][$key] = $dataBonBiruDetail;

                if( !empty($bon_biru_id) ) {
                    $dataBonBiruDetail['BonBiruDetail']['bon_biru_id'] = $bon_biru_id;
                }

                $this->Ttuj->BonBiruDetail->create();
                $this->Ttuj->BonBiruDetail->set($dataBonBiruDetail);

                if( !empty($bon_biru_id) ) {
                    if( !$this->Ttuj->BonBiruDetail->save() ) {
                        $status = false;
                    } else {
                        $this->Ttuj->set('status_bon_biru', 'full');
                        $this->Ttuj->id = $ttuj_id;
                        $this->Ttuj->save();
                    }
                } else {
                    if( !$this->Ttuj->BonBiruDetail->validates() ) {
                        $errorValidations = $this->Ttuj->BonBiruDetail->validationErrors;

                        if( !empty($errorValidations) ) {
                            foreach ($errorValidations as $key => $error) {
                                if( !empty($error) ) {
                                    foreach ($error as $key => $err_msg) {
                                        $msgError[] = $err_msg;
                                    }
                                }
                            }
                        }

                        $status = false;
                    }
                }
            }
        } else {
            $status = false;
            $this->MkCommon->setCustomFlash(__('Mohon pilih TTUJ.'), 'error'); 
        }

        if( !empty($msgError) ) {
            $msgError = array_unique($msgError);
        }

        return array(
            'status' => $status,
            'msgError' => $msgError,
        );
    }

    function doBonBiru( $id = false, $value = false, $disabled_edit = false ){
        $this->set('active_menu', 'bon_biru');

        if(!empty($this->request->data) && empty($disabled_edit)){
            $data = $this->request->data;
            $data = $this->MkCommon->dataConverter($data, array(
                'date' => array(
                    'BonBiru' => array(
                        'tgl_bon_biru',
                    ),
                )
            ));
            $data['BonBiru']['branch_id'] = Configure::read('__Site.config_branch_id');
            $bonBiruDetails = $this->MkCommon->filterEmptyField($value, 'BonBiruDetail');

            $dataDetail = $this->MkCommon->filterEmptyField($data, 'BonBiruDetail', 'ttuj_id');
            $resutlDetail = $this->doBonBiruDetail($dataDetail, $data, false, $value);
            $flagDetail = $this->MkCommon->filterEmptyField($resutlDetail, 'status');
            $errorDetail = $this->MkCommon->filterEmptyField($resutlDetail, 'msgError');

            if( !empty($id) ) {
                $this->Ttuj->BonBiruDetail->BonBiru->id = $id;
            } else {
                $this->Ttuj->BonBiruDetail->BonBiru->create();
            }

            $this->Ttuj->BonBiruDetail->BonBiru->set($data);

            if( $this->Ttuj->BonBiruDetail->BonBiru->validates() && !empty($flagDetail) ){
                if($this->Ttuj->BonBiruDetail->BonBiru->save()){
                    $document_id = $this->Ttuj->BonBiruDetail->BonBiru->id;
                    $this->doBonBiruDetail($dataDetail, $data, $document_id, $value);

                    $this->params['old_data'] = $value;
                    $this->params['data'] = $data;

                    $noref = str_pad($document_id, 6, '0', STR_PAD_LEFT);
                    $this->MkCommon->setCustomFlash(sprintf(__('Berhasil melakukan penerimaan Bon Biru #%s'), $noref), 'success'); 
                    $this->Log->logActivity( sprintf(__('Berhasil melakukan penerimaan Bon Biru #%s'), $document_id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $document_id );
                    
                    $this->redirect(array(
                        'action' => 'bon_biru',
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal melakukan penerimaan Bon Biru'), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal melakukan penerimaan Bon Biru #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
                }
            }else{
                if( !empty($errorDetail) ) {
                    $this->MkCommon->setCustomFlash('<ul><li>'.implode('</li><li>', $errorDetail).'</li></ul>', 'error'); 
                } else {
                    $this->MkCommon->setCustomFlash(__('Gagal melakukan penerimaan Bon Biru'), 'error'); 
                }
            }
        } else if( !empty($value) ) {
            if( !empty($value['BonBiruDetail']) ) {
                foreach ($value['BonBiruDetail'] as $key => $val) {
                    $ttuj_id = $this->MkCommon->filterEmptyField($val, 'BonBiruDetail', 'ttuj_id');

                    $val = $this->Ttuj->getMerge($val, $ttuj_id);
                    $val = $this->Ttuj->getMergeList($val, array(
                        'contain' => array(
                            'DriverPengganti' => array(
                                'uses' => 'Driver',
                                'primaryKey' => 'id',
                                'foreignKey' => 'driver_pengganti_id',
                                'elements' => array(
                                    'branch' => false,
                                ),
                            ),
                            'Driver' => array(
                                'elements' => array(
                                    'branch' => false,
                                ),
                            ),
                        ),
                    ));

                    $this->request->data['Ttuj'][$key] = $val;
                    $this->request->data['BonBiruDetail']['ttuj_id'][$key] = $ttuj_id;
                }
            }

            $this->request->data['BonBiru'] = $this->MkCommon->filterEmptyField($value, 'BonBiru');
            $this->request->data = $this->MkCommon->dataConverter($this->request->data, array(
                'date' => array(
                    'BonBiru' => array(
                        'tgl_bon_biru',
                    ),
                )
            ), true);
        } else {
            $this->request->data['BonBiru']['tgl_bon_biru'] = date('d/m/Y');
        }

        if( !empty($id) ) {
            $this->MkCommon->getLogs($this->params['controller'], array( 'bon_biru_add', 'bon_biru_edit', 'bon_biru_delete' ), $id);
        }

        $this->MkCommon->_layout_file('select');
        $this->set(compact(
            'id', 'disabled_edit'
        ));
        $this->render('bon_biru_add');
    }

    function bon_biru_delete($id = false){
        $is_ajax = $this->RequestHandler->isAjax();
        $msg = array(
            'msg' => '',
            'type' => 'error'
        );
        $value = $this->Ttuj->BonBiruDetail->BonBiru->getData('first', array(
            'conditions' => array(
                'BonBiru.id' => $id,
            ),
        ));

        if( !empty($value) ){
            if(!empty($this->request->data)){
                $data = $this->request->data;
                $data = $this->MkCommon->dataConverter($data, array(
                    'date' => array(
                        'BonBiru' => array(
                            'canceled_date',
                        ),
                    )
                ));

                $value = $this->Ttuj->BonBiruDetail->getMerge($value, $id);
                $dataDetail = $this->MkCommon->filterEmptyField($value, 'BonBiruDetail');

                if(!empty($data['BonBiru']['canceled_date'])){
                    $data['BonBiru']['canceled_date'] = $this->MkCommon->filterEmptyField($data, 'BonBiru', 'canceled_date');
                    $data['BonBiru']['is_canceled'] = 1;

                    $this->Ttuj->BonBiruDetail->BonBiru->id = $id;
                    $this->Ttuj->BonBiruDetail->BonBiru->set($data);

                    if($this->Ttuj->BonBiruDetail->BonBiru->save()){
                        $this->Ttuj->BonBiruDetail->updateAll(array(
                            'BonBiruDetail.status' => 0,
                        ), array(
                            'BonBiruDetail.bon_biru_id' => $id,
                        ));
                        
                        $this->Ttuj->BonBiruDetail->BonBiru->recoverTtuj($dataDetail);

                        $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
                        $msg = array(
                            'msg' => sprintf(__('Berhasil menghapus Bon Biru #%s'), $noref),
                            'type' => 'success'
                        );
                        $this->MkCommon->setCustomFlash( $msg['msg'], $msg['type']);  
                        $this->Log->logActivity( sprintf(__('Berhasil menghapus Bon Biru #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id ); 
                    }else{
                        $this->Log->logActivity( sprintf(__('Gagal menghapus Bon Biru #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id ); 
                    }
                }else{
                    $msg = array(
                        'msg' => __('Harap masukkan tanggal pembatalan Bon Biru.'),
                        'type' => 'error'
                    );
                }
            }

            $this->set('value', $value);
        }else{
            $msg = array(
                'msg' => __('Data tidak ditemukan'),
                'type' => 'error'
            );
        }

        $modelName = 'BonBiru';
        $canceled_date = !empty($this->request->data['BonBiru']['canceled_date']) ? $this->request->data['BonBiru']['canceled_date'] : false;
        $this->set(compact(
            'msg', 'is_ajax', 'action_type',
            'canceled_date', 'modelName'
        ));
        $this->render('/Elements/blocks/common/form_delete');
    }

    function document_ttujs(  ){
        $this->loadModel('City');
        $named = $this->MkCommon->filterEmptyField($this->params, 'named');
        $title = __('Dokumen TTUJ');

        $params = $this->MkCommon->_callRefineParams($this->params);
        $options =  $this->Ttuj->_callRefineParams($params, array(
            'conditions' => array(
                'Ttuj.is_draft' => 0,
                'Ttuj.status_bon_biru' => array( 'none', 'half' ),
            ),
            'limit' => Configure::read('__Site.config_pagination'),
        ));


        if(!empty($this->params['named'])){
            $refine = $this->params['named'];
            $options = $this->MkCommon->getConditionGroupBranch( $refine, 'Ttuj', $options );
        }

        $this->paginate = $this->Ttuj->getData('paginate', $options, true, array(
            'plant' => true,
        ));
        $values = $this->paginate('Ttuj');

        if( !empty($values) ) {
            foreach ($values as $key => $value) {
                $ttuj_id = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'id');
                $customer_id = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'customer_id');

                $value = $this->Ttuj->getMergeList($value, array(
                    'contain' => array(
                        'DriverPengganti' => array(
                            'uses' => 'Driver',
                            'primaryKey' => 'id',
                            'foreignKey' => 'driver_pengganti_id',
                            'elements' => array(
                                'branch' => false,
                            ),
                        ),
                        'Driver' => array(
                            'elements' => array(
                                'branch' => false,
                            ),
                        ),
                    ),
                ));

                $value = $this->Ttuj->Customer->getMerge($value, $customer_id);
                $values[$key] = $value;
            }
        }

        $customers = $this->Ttuj->Customer->getData('list', array(
            'fields' => array(
                'Customer.id', 'Customer.customer_name_code'
            ),
        ));
        $cities = $this->City->getListCities();

        $data_action = 'browse-check-docs';
        $this->set(compact(
            'data_action', 'title', 'values',
            'customers', 'cities'
        ));
    }

    public function report_bon_biru( $data_action = false ) {
        $module_title = __('Laporan Bon Biru');
        $values = array();

        $this->set('sub_module_title', $module_title);

        $this->Ttuj->unBindModel(array(
            'hasMany' => array(
                'BonBiruDetail'
            )
        ));

        $this->Ttuj->bindModel(array(
            'hasOne' => array(
                'BonBiruDetail' => array(
                    'className' => 'BonBiruDetail',
                    'conditions' => array(
                        'BonBiruDetail.status' => 1,
                    ),
                ),
                'BonBiru' => array(
                    'className' => 'BonBiru',
                    'foreignKey' => false,
                    'conditions' => array(
                        'BonBiru.id = BonBiruDetail.bon_biru_id',
                        'BonBiru.status' => 1,
                        'BonBiru.is_canceled' => 0,
                    ),
                ),
            )
        ), false);

        $options =  $this->Ttuj->getData('paginate', array(
            'contain' => array(
                'BonBiru',
                'BonBiruDetail',
            ),
        ), true, array(
            'status' => 'commit',
            'branch' => false,
            'plant' => false,
        ));

        $params = $this->MkCommon->_callRefineParams($this->params);
        $dateFrom = $this->MkCommon->filterEmptyField($params, 'named', 'DateFrom');
        $dateTo = $this->MkCommon->filterEmptyField($params, 'named', 'DateTo');
        $options =  $this->Ttuj->BonBiruDetail->BonBiru->_callRefineParams($params, $options);
        $options = $this->MkCommon->getConditionGroupBranch( $params, 'Ttuj', $options );

        if( !empty($options['contain']) ) {
            foreach (array_keys($options['contain'], 'Ttuj', true) as $key) {
                unset($options['contain'][$key]);
            }
        }

        if( !empty($dateFrom) && !empty($dateTo) ) {
            $module_title .= sprintf(' Periode %s', $this->MkCommon->getCombineDate($dateFrom, $dateTo));
        }

        if( !empty($data_action) ){
            $values = $this->Ttuj->find('all', $options);
        } else {
            $options['limit'] = Configure::read('__Site.config_pagination');
            $this->paginate = $options;
            $values = $this->paginate('Ttuj');
        }

        if( !empty($values) ) {
            foreach ($values as $key => $value) {
                $ttuj_id = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'id');
                $branch_id = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'branch_id');
                
                $value = $this->GroupBranch->Branch->getMerge($value, $branch_id);
                $value = $this->Ttuj->getMergeList($value, array(
                    'contain' => array(
                        'DriverPengganti' => array(
                            'uses' => 'Driver',
                            'primaryKey' => 'id',
                            'foreignKey' => 'driver_pengganti_id',
                            'elements' => array(
                                'branch' => false,
                            ),
                        ),
                        'Driver' => array(
                            'elements' => array(
                                'branch' => false,
                            ),
                        ),
                    ),
                ));

                $muatan = $this->Ttuj->TtujTipeMotor->getTotalMuatan( $ttuj_id );
                $value['Ttuj']['qty'] = $muatan;

                $values[$key] = $value;
            }
        }

        $customers = $this->Ttuj->Customer->getData('list', array(
            'fields' => array(
                'Customer.id', 'Customer.customer_name_code'
            ),
        ));

        $this->set('active_menu', 'report_bon_biru');
        $this->set(compact(
            'values', 'module_title', 'data_action',
            'customers'
        ));

        if($data_action == 'pdf'){
            $this->layout = 'pdf';
        }else if($data_action == 'excel'){
            $this->layout = 'ajax';
        } else {
            $this->MkCommon->_layout_file(array(
                'freeze',
                'select',
            ));
        }
    }

    public function report_recap_bon_biru( $data_action = false ) {
        $allow_branch_id = Configure::read('__Site.config_allow_branch_id');
        $dateFrom = date('Y-m-01');
        $dateTo = date('Y-m-t');

        $options = array(
            'conditions' => array(
                'Ttuj.branch_id' => $allow_branch_id,
            ),
            'order'=> array(
                'Ttuj.id' => 'DESC',
            ),
            'group' => array(
                'Ttuj.id'
            ),
        );

        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));
        $options =  $this->Ttuj->_callRefineParams($params, $options);
        $options = $this->MkCommon->getConditionGroupBranch( $params, 'Ttuj', $options );

        if( !empty($data_action) ){
            $values = $this->Ttuj->getData('all', $options, true, array(
                'status' => 'commit',
            ));
        } else {
            $this->paginate = $this->Ttuj->getData('paginate', array_merge($options, array(
                'limit' => Configure::read('__Site.config_pagination'),
            )), true, array(
                'status' => 'commit',
            ));
            $values = $this->paginate('Ttuj');
        }

        if( !empty($values) ) {
            foreach ($values as $key => $value) {
                $id = $this->MkCommon->filterEmptyField($value, 'Ttuj', 'id');

                $value = $this->Ttuj->getBonBiru($value, $id);
                $value = $this->Ttuj->Revenue->RevenueDetail->getToCity($value, $id);
                $value = $this->Ttuj->getMergeList($value, array(
                    'contain' => array(
                        'DriverPengganti' => array(
                            'uses' => 'Driver',
                            'primaryKey' => 'id',
                            'foreignKey' => 'driver_pengganti_id',
                            'elements' => array(
                                'branch' => false,
                            ),
                        ),
                        'Driver' => array(
                            'elements' => array(
                                'branch' => false,
                            ),
                        ),
                    ),
                ));

                $values[$key] = $value;
            }
        }

        $this->RjTtuj->_callBeforeViewReportRecapBonBiru($params);
        $this->MkCommon->_callBeforeViewReport($data_action, array(
            'layout_file' => array(
                'select',
                'freeze',
            ),
        ));
        
        $module_title = __('Laporan Rekap Penerimaan Bon Biru');

        if( !empty($dateFrom) && !empty($dateTo) ) {
            $period_text = sprintf(' Periode %s', $this->MkCommon->getCombineDate($dateFrom, $dateTo));
        } else {
            $period_text = '';
        }

        $this->set(array(
            'values' => $values,
            'data_action' => $data_action,
            'module_title' => $module_title,
            'sub_module_title' => $period_text,
        ));
    }
}