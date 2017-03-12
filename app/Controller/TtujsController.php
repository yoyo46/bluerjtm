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
        $this->loadModel('City');
        $allow_branch_id = Configure::read('__Site.config_allow_branch_id');
        $dateFrom = date('Y-m-01');
        $dateTo = date('Y-m-t');

        $this->Ttuj->unBindModel(array(
            'hasMany' => array(
                'Revenue',
            )
        ));
        $this->Ttuj->bindModel(array(
            'hasOne' => array(
                'Revenue' => array(
                    'className' => 'Revenue',
                    'foreignKey' => 'ttuj_id',
                    'conditions' => array(
                        'Revenue.status' => 1,
                    ),
                )
            )
        ), false);

        $options = array(
            'conditions' => array(
                'Revenue.id NOT' => NULL,
                'Ttuj.branch_id' => $allow_branch_id,
            ),
            'contain' => array(
                'Revenue',
            ),
            'order'=> array(
                'Ttuj.created' => 'DESC',
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
            $values = $this->Ttuj->getData('all', $options, array(
                'status' => 'commit',
            ));
        } else {
            $this->paginate = $this->Ttuj->getData('paginate', array_merge($options, array(
                'limit' => Configure::read('__Site.config_pagination'),
            )), array(
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
}