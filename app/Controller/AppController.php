<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
	public $components = array(
		'MkCommon', 
		'Auth', 
		// 'Acl', 
		'Session', 'RequestHandler', 'Cookie'
	);

	var $helpers = array(
		'Common', 'Html' => array()
	);

	var $uses = array(
		'Log', 'Module', 'Notification', 'GroupBranch', 'BranchActionModule'
	);

	function beforeFilter() {
	    //Configure AuthComponent

		Configure::write('__Site.profile_photo_folder', 'users');
		Configure::write('__Site.laka_photo_folder', 'lakas');
		Configure::write('__Site.truck_photo_folder', 'trucks');

		Configure::write('__Site.config_currency_code', 'IDR ');
		Configure::write('__Site.config_currency_second_code', 'Rp ');
		Configure::write('__Site.config_pagination', 20);
		Configure::write('__Site.config_pagination_unlimited', 1000);
		Configure::write('__Site.cache_view_path', '/images/view');
		Configure::write('__Site.upload_path', APP.'Uploads');

		Configure::write('__Site.fullsize', 'fullsize');
		Configure::write('__Site.max_image_size', 5241090);
		Configure::write('__Site.max_image_width', 1000);
		Configure::write('__Site.max_image_height', 667);
		Configure::write('__Site.allowed_ext', array('jpg', 'jpeg', 'png', 'gif'));
		Configure::write('__Site.type_lku', array('lku' => 'LKU', 'ksu' => 'KSU'));

		Configure::write('__Site.thumbnail_view_path', APP.'webroot'.DS.'images'.DS.'view');

		$dimensionProfile = array(
			'ps' => '50x50',
			'pm' => '100x100',
			'pl' => '150x150',
			'pxl' => '300x300',
		);
		Configure::write('__Site.dimension_profile', $dimensionProfile);

		$dimensionArr = array(
			's' => '150x84',
			'xsm' => '100x40',
			'xm' => '165x165',
			'xxsm' => '240x96',
   			'm' => '300x169',
   			'l' => '855x481',
		);
		Configure::write('__Site.dimension', $dimensionArr);

	    $this->Auth->userModel = 'User';
	    $this->Auth->authorize = array(
	        'Controller',
	        'Actions' => array('actionPath' => 'controllers')
	    );
	    $this->Auth->loginAction = array(
	        'controller' => 'users',
	        'action' => 'login',
	        'admin' => false,
	        'plugin' => false
	    );
	    $this->Auth->logoutRedirect = array(
	        'controller' => 'users',
	        'action' => 'login',
	        'admin' => false,
	        'plugin' => false
	    );
	    $this->Auth->loginRedirect = array(
	        'controller' => 'trucks',
	        'action' => 'index',
	        'admin' => false,
	        'plugin' => false
	    );
	    $invStatus = array(
            'paid' => __('Paid'),
            'unpaid' => __('Unpaid'),
            'halfpaid' => __('Half Paid'),
            'void' => __('Void'),
        );

	    $logged_in = $this->MkCommon->loggedIn();
	    $this->user_id = false;
	    $GroupId = false;
	    $User = array();
	    $allowModule = array();

	    if($logged_in){
			$this->user_id = $this->Auth->user('id');
			$GroupId = $this->Auth->user('group_id');
			$User = $this->user_data = $User = $this->Auth->user();

			/*Auth*/
			$controller_allowed = array(
				'users', 'pages'
			);
			$action_allowed = array(
				'change_branch', 'search', 'logout', 'login', 'dashboard', 'display', 'index',
				'authorization'
			);

			Configure::write('__Site.allowed_controller', $controller_allowed);
			Configure::write('__Site.allowed_action', $action_allowed);

			$_branches = $this->GroupBranch->getData('all', array(
				'conditions' => array(
					'GroupBranch.group_id' => $GroupId
				),
				'contain' => array(
					'City'
				)
			));

			$list_branch = array();
			$this->group_branch_id = $group_branch_id = '';
			$is_allow = false;
			$_branch_action_module = array();
			if(!empty($_branches)){
				foreach ($_branches as $key => $value) {
					if($value['GroupBranch']['city_id'] == $User['branch_id']){
						$group_branch_id = $value['GroupBranch']['id'];
					}
					$list_branch[$value['GroupBranch']['id']] = $value['City']['name'];
				}

				$user_branch = $this->Session->read('user_branch');
				$this->group_branch_id = $group_branch_id = !empty($user_branch)?$user_branch:$group_branch_id;

				if(!empty($group_branch_id)){
					$_branch_action_module = $this->BranchActionModule->getData('all', array(
						'conditions' => array(
							'BranchActionModule.group_branch_id' => $group_branch_id
						),
						'fields' => array(
							'BranchActionModule.is_allow'
						),
						'contain' => array(
							'BranchModule' => array(
								'fields' => array(
									'BranchModule.controller', 'BranchModule.action'
								)
							)
						)
					));
				}
			}

			$this->helpers['Html']['group_id'] = $GroupId;
			if(!empty($_branch_action_module)){
				$this->helpers['Html']['rule_link'] = $_branch_action_module;
			}

			if(in_array($this->params['controller'], $controller_allowed) && in_array($this->params['action'], $action_allowed) || $this->params['controller'] == 'ajax' || $GroupId == 1){
				$is_allow = true;
			}else if(!empty($_branch_action_module)){
				foreach ($_branch_action_module as $key => $value) {
					if($this->params['controller'] == $value['BranchModule']['controller'] && $this->params['action'] == $value['BranchModule']['action'] && $value['BranchActionModule']['is_allow']){
						$is_allow = true;
						break;
					}
				}
			}
			
			if(!$is_allow){
				$this->MkCommon->setCustomFlash('Anda tidak mempunyai hak mengakses konten tersebut.', 'error');
				$this->redirect('/');
			}

			$this->set(compact('list_branch', '_branch_action_module', 'group_branch_id'));
			/*End Auth*/

			$allowModule = $this->Module->ModuleAction->find('list', array(
	            'conditions'=> array(
	                'Module.status'=> 1, 
            		'ModuleAction.group_id' => $GroupId,
	            ),
	            'order' => array(
	                'Module.order' => 'ASC'
	            ),
	            'contain' => array(
	            	'Module'
            	),
            	'fields' => array(
            		'ModuleAction.id', 'ModuleAction.action'
        		),
	        ));

	        if(isset($this->params['named']['ntf']) && !empty($this->params['named']['ntf'])){
	        	$this->Notification->notifCheck($this->user_id, $this->params['named']['ntf']);
	        }

	        $cacheName = sprintf('LeadTime-%s', $this->user_id);
			$lead_time_notif = Cache::read($cacheName, 'short');
			
			if(empty($lead_time_notif)){
				$this->loadModel('Ttuj');
				$overlead_time_destination = $this->Ttuj->getData('list', array(
					'conditions' => array(
						'Ttuj.is_arrive' => 1,
						'Ttuj.arrive_over_time >' => 0,
						'Ttuj.is_pool' => 0,
						'Ttuj.status' => 1
					),
					'fields' => array(
						'Ttuj.id'
					)
				));

				$overlead_time_pool = $this->Ttuj->getData('all', array(
					'conditions' => array(
						'Ttuj.is_arrive' => 1,
						'Ttuj.back_orver_time >' => 0,
						'Ttuj.is_pool' => 1,
						'Ttuj.status' => 1
					),
					'fields' => array(
						'Ttuj.id', 'Ttuj.nopol', 'Ttuj.no_ttuj'
					)
				));
				
				if(!empty($overlead_time_destination) || !empty($overlead_time_pool)){
					$this->loadModel('User');

					$list_id_user_admin = $this->User->getData('list', array(
						'conditions' => array(
							'User.group_id' => 1,
							'User.status' => 1
						),
						'fields' => array(
							'User.id'
						)
					));

					if(!empty($list_id_user_admin)){
						if(!empty($overlead_time_destination)){
							$ttuj_id = Set::extract('/Ttuj/id', $overlead_time_destination);

							$check_notif = $this->Notification->getData('list', array(
								'conditions' => array(
									'Notification.document_id' => $ttuj_id,
									'Notification.action' => 'overlead_time_destination'
								)
							));

							if(empty($check_notif)){
								foreach ($overlead_time_destination as $key => $value) {
									$data_ttuj = $value['Ttuj'];
									$this->Notification->saveData($list_id_user_admin, array(
										'document_id' => $data_ttuj['id'],
										'action' => 'overlead_time_destination',
										'name' => sprintf(__('Truk dengan Nopol %s dengan no TTUJ %s telah melewati lead time tujuan'), $data_ttuj['nopol'], $data_ttuj['no_ttuj']),
										'url' => serialize(array(
											'controller' => 'revenues',
											'action' => 'ttuj_edit',
											$data_ttuj['id']
										)),
										'type_notif' => 'danger',
										'icon_modul' => 'truck',
										'link' => __('Lihat TTUJ')
									));
								}
							}
						}

						if(!empty($overlead_time_pool)){
							$ttuj_id = Set::extract('/Ttuj/id', $overlead_time_pool);

							$check_notif = $this->Notification->getData('list', array(
								'conditions' => array(
									'Notification.document_id' => $ttuj_id,
									'Notification.action' => 'overlead_time_pool'
								)
							));

							if(empty($check_notif)){
								foreach ($overlead_time_pool as $key => $value) {
									$data_ttuj = $value['Ttuj'];
									$this->Notification->saveData($list_id_user_admin, array(
										'document_id' => $data_ttuj['id'],
										'action' => 'overlead_time_pool',
										'name' => sprintf(__('Truk dengan Nopol %s dengan no TTUJ %s telah melewati lead time balik pool'), $data_ttuj['nopol'], $data_ttuj['no_ttuj']),
										'url' => serialize(array(
											'controller' => 'revenues',
											'action' => 'ttuj_edit',
											$data_ttuj['id']
										)),
										'type_notif' => 'danger',
										'icon_modul' => 'truck',
										'link' => __('Lihat TTUJ')
									));
								}
							}
						}
					}
				}

				Cache::write($cacheName, 1, 'short');
			}

			$notifications = $this->Notification->getData('all', array(
				'conditions' => array(
					'Notification.user_id' => $this->user_id,
					'Notification.read' => 0
				)
			));

			$this->set('notifications', $notifications);
		}
		$this->allowModule = $allowModule;

	    $this->set(compact(
	    	'logged_in', 'GroupId', 'User',
	    	'allowModule', 'invStatus'
    	));
	}

	function isAuthorized($user) {
	    // return false;
	    return $this->Auth->loggedIn();
	}
}
