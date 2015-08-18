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
		'Log', 'Module', 'Notification', 
		'GroupBranch', 'BranchActionModule',
		'User'
	);

	function beforeFilter() {
		// Configure Default
		$this->MkCommon->configureDefaultApp();

	    $logged_in = $this->MkCommon->loggedIn();
	    $this->user_id = false;
	    $GroupId = false;
	    $User = array();
		$_allowModule = array();
		$list_branch = array();
		$allowBranch = array();
		$this->allowBranch = false;
		$key_branch = array();
		$paramController = $this->params['controller'];
		$paramAction = $this->params['action'];

		$this->Auth->authError = __('Anda tidak mempunyai hak mengakses konten tersebut.');

	    if($logged_in){
			$this->user_id = $this->Auth->user('id');
			$GroupId = $this->Auth->user('group_id');
			$User = $this->Auth->user();

			if( !empty($User['employe_id']) ) {
				$User = $this->User->Employe->getMerge($User, $User['employe_id']);
			}

			$this->user_data = $User;
			/*Auth*/
			$_allowedModule = array(
				'users' => array(
					'change_branch', 'search', 'logout',
					'login', 'dashboard', 'authorization',
					'profile'
				),
				'pages' => array(
					'dashboard'
				),
				'settings' => array(
					'search',
				),
				'trucks' => array(
					'search',
				),
				'leasings' => array(
					'search',
				),
				'revenues' => array(
					'search',
				),
			);
			// $controller_allowed = array(
			// 	'users', 'pages', 'settings'
			// );
			// $action_allowed = array(
			// 	'change_branch', 'search', 'logout', 'login', 'dashboard', 'display', 'index',
			// 	'authorization', 'profile'
			// );
			$allowness_extend = array(
				'ttuj_payments', 'ttuj_payment_add', 'ttuj_payment_delete', 'detail_ttuj_payment'
			);

			Configure::write('__Site.allowed_module', $_allowedModule);
			// Configure::write('__Site.allowed_controller', $controller_allowed);
			// Configure::write('__Site.allowed_action', $action_allowed);
			Configure::write('__Site.allowed_extend', $allowness_extend);
			Configure::write('__Site.config_group_id', $GroupId);

			$conditionsBranch = array();

			if( $GroupId != 1 ) {
				$conditionsBranch = array(
					'GroupBranch.group_id' => $GroupId
				);
			}

			$_branches = $this->GroupBranch->getData('all', array(
				'conditions' => $conditionsBranch,
				'contain' => array(
					'City'
				)
			));

			$group_branch_id = '';
			$branch_city_id = '';
			$branch_city_plant = false;
			$branch_city_head_office = false;
			$is_allow = false;
			$first_branch_id = false;

			if(!empty($_branches)){
				$branch_id = $this->MkCommon->filterEmptyField($User, 'Employe', 'branch_id');
				$user_branch = $this->Session->read('user_branch');
				$branch_id = $branch_city_id = !empty($user_branch)?$user_branch:$branch_id;

				foreach ($_branches as $key => $value) {
					$city_id = $this->MkCommon->filterEmptyField($value, 'City', 'id');
					$city_name = $this->MkCommon->filterEmptyField($value, 'City', 'name');
					$city_plant = $this->MkCommon->filterEmptyField($value, 'City', 'is_plant');
					$city_head_office = $this->MkCommon->filterEmptyField($value, 'City', 'is_head_office');
					$group_branch_city_id = $this->MkCommon->filterEmptyField($value, 'GroupBranch', 'city_id');
					$id_group_branch = $this->MkCommon->filterEmptyField($value, 'GroupBranch', 'id');

					if( empty($key) ) {
						$first_branch_id = $group_branch_city_id;
					}

					if($group_branch_city_id == $branch_id){
						$group_branch_id = $value['GroupBranch']['id'];
						$branch_city_id = $city_id;
						$branch_city_plant = $city_plant;
						$branch_city_head_office = $city_head_office;
					}

					$branchActionModule = $this->BranchActionModule->getData('all', array(
						'conditions' => array(
							'BranchActionModule.group_branch_id' => $id_group_branch,
							'BranchActionModule.is_allow' => 1,
							'BranchModule.status' => 1,
						),
						'contain' => array(
							'BranchModule',
						)
					));

					if( !empty($branchActionModule) ) {
						foreach ($branchActionModule as $key => $value) {
							$controllerName = $this->MkCommon->filterEmptyField($value, 'BranchModule', 'controller');
							$actionName = $this->MkCommon->filterEmptyField($value, 'BranchModule', 'action');
							$extend_action = $this->MkCommon->filterEmptyField($value, 'BranchModule', 'extend_action');
							$allow_function = $this->MkCommon->filterEmptyField($value, 'BranchModule', 'allow_function');

							// if( $controllerName == 'trucks' && $actionName == 'index' ) {
							// 	debug($value);die();
							// }

							$_allowModule[$city_id][$controllerName]['action'][] = $actionName;

							if( !empty($extend_action) ) {
								$_allowModule[$city_id][$controllerName]['extends'][$actionName] = $extend_action;
							}
							if( !empty($allow_function) ) {
								$allow_function = explode(',', $allow_function);
								
								foreach ($allow_function as $key => $function) {
									$functionArr = explode('-', $function);
									$function = !empty($functionArr[0])?$functionArr[0]:$function;
									$controllerName = !empty($functionArr[1])?$functionArr[1]:$controllerName;

									$_allowModule[$city_id][$controllerName]['action'][] = $function;
								}
							}
						}
					}

					if($paramController == 'revenues' && $paramAction == 'index'){
						// 327 = module revenue
						$another_rule = $this->BranchActionModule->getRuleByModule(327, $group_branch_id);
					}

					$list_branch[$city_id] = $city_name;
				}

				if( empty($branch_city_id) ) {
					$branch_city_id = $first_branch_id;
				}

				// $group_branch_id = !empty($user_branch)?$user_branch:$group_branch_id;
				$branch_city_id = !empty($user_branch)?$user_branch:$branch_city_id;
			}

			if( !empty($list_branch) ) {
				$key_branch = array_keys($list_branch);
			}

			Configure::write('__Site.config_allow_module', $_allowModule);
			Configure::write('__Site.config_branch_id', $branch_city_id);
			Configure::write('__Site.config_branch_plant', $branch_city_plant);
            Configure::write('__Site.config_list_branch_id', $key_branch);
			Configure::write('__Site.config_branch_city_head_office', $branch_city_head_office);
			// Configure::write('__Site.config_allow_branch_id', $list_branch);

			$this->list_branch = $list_branch;
			// $this->helpers['Html']['group_id'] = $GroupId;
			$allowBranch = $this->MkCommon->allowBranch($list_branch);

			if( !empty($allowBranch) ) {
                // $this->allowBranch = array_keys($allowBranch);
				// Configure::write('__Site.config_branch_id', $this->allowBranch);
				Configure::write('__Site.config_allow_branchs', $allowBranch);
			}
			
			// if(!empty($_allowModule)){
			// 	$this->helpers['Html']['rule_link'] = $_allowModule;
			// }

			$allowAction = !empty($_allowedModule[$paramController])?$_allowedModule[$paramController]:array();
			$allowPage = in_array($paramAction, $allowAction)?true:false;

			// if(in_array($paramController, $controller_allowed) && in_array($paramAction, $action_allowed) || $paramController == 'ajax' || $GroupId == 1){
			if( !empty($allowPage) || $paramController == 'ajax' || $GroupId == 1 ){
				$is_allow = true;
			}else {
				// $allowPage = $this->MkCommon->allowPage( $key_branch, true );
				$allowPage = $this->MkCommon->allowPage( $branch_city_id, true );
				$extend_name = !empty($allowed_module[$paramController]['extends'][$paramAction])?$allowed_module[$paramController]['extends'][$paramAction]:false;
				$extend_param = !empty($this->params['pass'][0]) ? $this->params['pass'][0] : '';

				if(!empty($extend_param) && !empty($allowPage) && $extend_name == $extend_param ) {
					$is_allow = true;
				} else if( !empty($allowPage) ){
					$is_allow = true;
				}
			}

			if( !empty($this->request->data['Default']['branch_id']) ) {
				$change_branch = $this->request->data['Default']['branch_id'];

				if( empty($allowBranch[$change_branch]) ) {
					$is_allow = false;
				}
			}
			
			if(!$is_allow){
				$this->MkCommon->setCustomFlash($this->Auth->authError, 'error');
				$this->redirect('/');
			}
			/*End Auth*/

	        if(isset($this->params['named']['ntf']) && !empty($this->params['named']['ntf'])){
	        	$this->Notification->notifCheck($this->user_id, $this->params['named']['ntf']);
	        }

	        $cacheName = sprintf('LeadTime-%s', $this->user_id);
			// $lead_time_notif = Cache::read($cacheName, 'short');
			
			if(empty($lead_time_notif)){
				$this->loadModel('Ttuj');
				$overlead_time_destination = $this->Ttuj->getData('all', array(
					'conditions' => array(
						'Ttuj.is_arrive' => 1,
						'Ttuj.arrive_over_time >' => 0,
						'Ttuj.is_pool' => 0,
					),
				));
				$overlead_time_pool = $this->Ttuj->getData('all', array(
					'conditions' => array(
						'Ttuj.is_arrive' => 1,
						'Ttuj.back_orver_time >' => 0,
						'Ttuj.is_pool' => 1,
					),
					'fields' => array(
						'Ttuj.id', 'Ttuj.nopol', 'Ttuj.no_ttuj'
					)
				));
				
				if(!empty($overlead_time_destination) || !empty($overlead_time_pool)){
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
									$data_ttuj = !empty($value['Ttuj'])?$value['Ttuj']:false;
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
		} else if( $paramAction != 'login' && $paramController == 'users' ) {
			$this->redirect('/');
		}

		$invStatus = array(
            'paid' => __('Paid'),
            'unpaid' => __('Unpaid'),
            'halfpaid' => __('Half Paid'),
            'void' => __('Void'),
        );

	    $this->set(compact(
	    	'logged_in', 'GroupId', 'User',
	    	'invStatus',
	    	'another_rule', 'notifications',
	    	'list_branch', 'group_branch_id',
	    	'allowBranch', 'branch_city_id'
    	));
	}

	function isAuthorized($user) {
	    // return false;
	    return $this->Auth->loggedIn();
	}
}
