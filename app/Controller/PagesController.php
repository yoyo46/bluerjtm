<?php
App::uses('AppController', 'Controller');
class PagesController extends AppController {
	public $uses = array();

    function beforeFilter(){
        parent::beforeFilter();
        $this->Auth->allow(array(
            'redirect',
        ));
    }
	
	public function dashboard() {
		$this->set('active_menu', 'dashboard');
		$this->set('title_for_layout', __('ERP RJTM | Dashboard'));
		$this->set('module_title', __('Dashboard'));
		$this->set('sub_module_title', __('Control panel'));
        $this->loadModel('Ttuj');
        $this->loadModel('Invoice');
        $this->loadModel('Truck');

        $ttujSJ = $this->Ttuj->getData('count', array(
            'conditions' => array(
                'Ttuj.status_sj' => array( 'none', 'half' ),
            ),
        ));
        $invoiceUnPaid = $this->Invoice->getData('count', array(
            'conditions' => array(
                'Invoice.complete_paid' => 0,
                'Invoice.paid' => 0,
                'Invoice.is_canceled' => 0,
            ),
        ));

        $truck_ongoing = $this->Truck->Ttuj->_callTtujOngoing();
        $truckAvailable = $this->Truck->getData('count', array(
            'conditions' => array(
                'Truck.sold' => 0,
                'Truck.id NOT' => $truck_ongoing,
            ),
        ));
        $top_spk = $this->get_top_spk();

        $this->MkCommon->_layout_file('flot');
        $this->set(compact(
        	'ttujSJ', 'invoiceUnPaid', 'truckAvailable',
            'top_spk'
    	));
		$this->render('home');
	}

    function referer_notification ( $id = false ) {
        $value = $this->User->Notification->getData('first', array(
            'conditions' => array(
                'Notification.id' => $id,
            ),
        ));
        $url = $this->MkCommon->filterEmptyField($value, 'Notification', 'url');

        if( !empty($url) ) {
            $url = unserialize($url);
            $this->User->Notification->doRead($id);
            $this->redirect($url);
        } else {
            $this->redirect(array(
                'controller' => 'pages',
                'action' => 'notifications',
                'admin' => false,
            ));
        }
    }

    function notifications(){
        $this->loadModel('Notification');

        $want_to_read_id = array();
        $this->paginate = $this->Notification->getData('paginate', array(
            'conditions' => array(
                'Notification.user_id' => $this->user_id,
                'Notification.action <>' => 'Kas/Bank',
            ),
            'limit' => 20
        ));
        $values = $this->paginate('Notification');

        $this->set('title_for_layout', __('ERP RJTM | Notifikasi'));
        $this->set('module_title', __('Notifikasi'));
        $this->set('values', $values);
    }

    function approval_notifications(){
        $this->loadModel('Notification');

        $want_to_read_id = array();
        $this->paginate = $this->Notification->getData('paginate', array(
            'conditions' => array(
                'Notification.user_id' => $this->user_id,
                'Notification.action' => 'Kas/Bank',
            ),
            'limit' => 20
        ));
        $values = $this->paginate('Notification');

        $this->set('title_for_layout', __('ERP RJTM | Notifikasi Kas/Bank'));
        $this->set('module_title', __('Notifikasi Kas/Bank'));
        $this->set('values', $values);

        $this->render('notifications');
    }

    function get_top_spk ( $year = '2017', $type = null ) {
        $this->loadModel('Spk');

        $this->Spk->ProductExpenditure->ProductExpenditureDetail->ProductHistory->bindModel(array(
            'hasOne' => array(
                'ProductExpenditure' => array(
                    'className' => 'ProductExpenditure',
                    'foreignKey' => false,
                    'conditions' => array(
                        'ProductExpenditure.id = ProductExpenditureDetail.product_expenditure_id',
                        'ProductExpenditure.status' => 1,
                    )
                ),
                'Spk' => array(
                    'className' => 'Spk',
                    'foreignKey' => false,
                    'conditions' => array(
                        'Spk.id = ProductExpenditure.document_id',
                        'Spk.status' => 1,
                    )
                ),
            )
        ), false);

        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => __('%s-01-01', $year),
            'dateTo' => __('%s-12-31', $year),
        ));
        $this->Spk->ProductExpenditure->ProductExpenditureDetail->ProductHistory->virtualFields['grandtotal'] = 'SUM(ProductHistory.qty*ProductHistory.price)';
        $optionTopSpk =  $this->Spk->ProductExpenditure->_callRefineParams($params);
        $optionTopSpk = $this->Spk->ProductExpenditure->getData('paginate', $optionTopSpk, array(
            'status' => 'confirm',
            'branch' => false,
        ));
        $optionTopSpk = Common::_callUnset($optionTopSpk, array(
            'order',
        ));
        $options = array_merge_recursive($optionTopSpk, array(
            'conditions' => array(
                'ProductHistory.transaction_type' => 'product_expenditure',
                // 'DATE_FORMAT(ProductExpenditure.transaction_date, \'%Y\')' => '2017',
                'ProductExpenditure.document_type' => 'internal',
            ),
            'contain' => array(
                'ProductExpenditureDetail',
                'ProductExpenditure',
                'Spk',
            ),
            'order' => array(
                'ProductHistory.grandtotal' => 'DESC',
            ),
            'limit' => 10,
        ));

        switch ($type) {
            case 'group_area':
                $options['group'] = array(
                    'Branch.is_head_office',
                );

                $options['contain'][] = 'Branch';
                break;
            
            case 'laka_group_area':
                $options['group'] = array(
                    'Branch.is_head_office',
                );

                $options['conditions'][]['OR'] = array(
                    array( 'Spk.laka_id <>' => 0, ),
                    array( 'Spk.laka_id <>' => NULL ),
                );
                $options['contain'][] = 'Branch';
                break;

            default:
                $options['group'] = array(
                    'Spk.truck_id',
                );
                break;
        }

        $top_spk = $this->Spk->ProductExpenditure->ProductExpenditureDetail->ProductHistory->getData('all', $options, array(
            'status' => 'active',
            'branch' => false,
        ));
        $top_spk = $this->Spk->getMergeList($top_spk, array(
            'contain' => array(
                'Truck',
            ),
        ));

        return $top_spk;
    }

    function bypass_top_spk ( $year = '2017' ) {
        $top_spk = $this->get_top_spk( $year );

        $this->set(compact(
            'top_spk', 'year'
        ));
        $this->render('/Elements/blocks/pages/top_spk');
    }

    function bypass_chart_maintenance () {
        $this->autoRender = false;

        $data = $this->request->data;
        $year = Common::hashEmptyField($data, 'Search.select_year', date('Y'));

        $values = $this->get_top_spk( $year, 'group_area' );

        if( !empty($values) ) {
            $date = __('01/01/%s - 31/12/%s', $year, $year);
            $date = Common::_callUrlEncode($date, true);
            $result = array(
                'url' => '/products/expenditure_reports/status:posting/date:'.$date,
                'chart' => array(),
            );
            $colors = array(
                '#3c8dbc',
                '#0073b7',
                '#00c0ef',
            );
            $grandtotal = 0;

            foreach ($values as $key => $value) {
                $total = Common::hashEmptyField($value, 'ProductHistory.grandtotal', 0);
                $grandtotal += $total;
            }

            foreach ($values as $key => $value) {
                $is_head_office = Common::hashEmptyField($value, 'Branch.is_head_office');
                $total = Common::hashEmptyField($value, 'ProductHistory.grandtotal', 0);
                $percent = Common::_callTargetPercentage($total, $grandtotal);

                if( !empty($is_head_office) ) {
                    $label = __('HO');
                } else {
                    $label = __('Daerah');
                }

                if( !empty($colors[$key]) ) {
                    $color_code = $colors[$key];
                } else {
                    $color_code = $colors[0];
                }

                $result['chart'][] = array(
                    'label' => __('%s (%s)', $label, Common::getFormatPrice($total)),
                    'data' => $percent,
                    'color' => $color_code,
                );
            }

            return json_encode($result);
        } else {
            return null;
        }
    }

    function bypass_chart_maintenance_laka () {
        $this->autoRender = false;

        $data = $this->request->data;
        $year = Common::hashEmptyField($data, 'Search.select_year', date('Y'));

        $values = $this->get_top_spk( $year, 'laka_group_area' );

        if( !empty($values) ) {
            $date = __('01/01/%s - 31/12/%s', $year, $year);
            $date = Common::_callUrlEncode($date, true);
            $result = array(
                'url' => '/products/expenditure_reports/is_laka:1/status:posting/date:'.$date,
                'chart' => array(),
            );
            $colors = array(
                '#3c8dbc',
                '#0073b7',
                '#00c0ef',
            );
            $grandtotal = 0;

            foreach ($values as $key => $value) {
                $total = Common::hashEmptyField($value, 'ProductHistory.grandtotal', 0);
                $grandtotal += $total;
            }

            foreach ($values as $key => $value) {
                $is_head_office = Common::hashEmptyField($value, 'Branch.is_head_office');
                $total = Common::hashEmptyField($value, 'ProductHistory.grandtotal', 0);
                $percent = Common::_callTargetPercentage($total, $grandtotal);

                if( !empty($is_head_office) ) {
                    $label = __('HO');
                } else {
                    $label = __('Daerah');
                }

                if( !empty($colors[$key]) ) {
                    $color_code = $colors[$key];
                } else {
                    $color_code = $colors[0];
                }

                $result['chart'][] = array(
                    'label' => __('%s (%s)', $label, Common::getFormatPrice($total)),
                    'data' => $percent,
                    'color' => $color_code,
                );
            }

            return json_encode($result);
        } else {
            return null;
        }
    }

    function call_redirect () {
        $params = $this->params->params;
        $slug = Common::hashEmptyField($params, 'slug');

        switch ($slug) {
            case 'webmail':
                $this->redirect('https://iix20.sharehostserver.com:2096/');
                break;
            
            default:
                $this->redirect('/');
                break;
        }
    }
}
