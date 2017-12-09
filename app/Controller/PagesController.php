<?php
App::uses('AppController', 'Controller');
class PagesController extends AppController {
	public $uses = array();
	
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

    function get_top_spk ( $year = '2017' ) {
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

        $top_spk = $this->Spk->ProductExpenditure->ProductExpenditureDetail->ProductHistory->getData('all', array_merge_recursive($optionTopSpk, array(
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
            'group' => array(
                'Spk.truck_id',
            ),
            'order' => array(
                'ProductHistory.grandtotal' => 'DESC',
            ),
            'limit' => 10,
        )), array(
            'status' => 'active',
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
}
