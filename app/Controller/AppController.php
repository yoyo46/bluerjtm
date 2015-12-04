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
		'Log', 'Module', 'GroupBranch', 
		'BranchActionModule', 'User',
	);

	function beforeFilter() {
		// Configure Default
		$this->MkCommon->configureDefaultApp();

    	$isAjax = $this->RequestHandler->isAjax();
	    $logged_in = $this->MkCommon->loggedIn();
	    $this->user_id = false;
	    $GroupId = false;
	    $User = array();

		$_allowModule = array();
		$this->paramController = $paramController = $this->params['controller'];
		$this->paramAction = $paramAction = $this->params['action'];

		$this->Auth->authError = __('Anda tidak mempunyai hak mengakses konten tersebut.');

	    if($logged_in){
			$this->user_id = $this->Auth->user('id');
			$GroupId = $this->Auth->user('group_id');
			$user_branch = $this->Session->read('user_branch');
			$User = $this->Auth->user();
			$current_branch_id = false;

			if( !empty($User['employe_id']) ) {
				$User = $this->User->Employe->getMerge($User, $User['employe_id']);
			}

			// Set Global Variable for User
			$this->user_data = $User;
			$employe_position_id = $this->MkCommon->filterEmptyField($User, 'Employe', 'employe_position_id');

			Configure::write('__Site.User.employe_position_id', $employe_position_id);
			Configure::write('__Site.config_group_id', $GroupId);
			Configure::write('__Site.config_user_id', $this->user_id);

			// Set Variable Branch
			$my_branch_id = $this->MkCommon->filterEmptyField($User, 'Employe', 'branch_id');
			$my_branch_id = !empty($user_branch)?$user_branch:$my_branch_id;
		    $city_branches = $this->GroupBranch->Branch->getData('list', array(
		    	'fields' => array(
		    		'Branch.id', 'Branch.city_id',
	    		),
	    	));

	    	$city_branches = array_values($city_branches);

	    	if( $GroupId == 1 ) {
		    	$list_branches = $this->GroupBranch->Branch->getData('list');
	    	} else {
		    	$list_branches = $this->GroupBranch->getData('list', array(
		    		'conditions' => array(
		    			'GroupBranch.group_id' => $GroupId,
	    			),
	    			'contain' => array(
	    				'Branch',
    				),
    				'fields' => array(
    					'GroupBranch.branch_id', 'Branch.name'
					),
					'group' => array(
						'GroupBranch.branch_id',
					),
	    		));
	    	}

			Configure::write('__Site.Data.Branch', $list_branches);
			Configure::write('__Site.Data.Branch.City.id', $city_branches);
            Configure::write('__Site.Data.Branch.id', array_keys($list_branches));

		    if( !empty($list_branches) ) {
			    if( !empty($list_branches[$my_branch_id]) ) {
					$current_branch_id = $my_branch_id;
			    } else {
					$current_branch_id = key($list_branches);
			    }

			    $branch = $this->GroupBranch->Branch->getData('first', array(
			    	'conditions' => array(
			    		'Branch.id' => $current_branch_id,
		    		),
		    	));

		    	if( !empty($branch) ){
					$current_branch_plant = $this->MkCommon->filterEmptyField($branch, 'Branch', 'is_plant');
					$current_branch_code = $this->MkCommon->filterEmptyField($branch, 'Branch', 'code');
					$current_branch_city_id = $this->MkCommon->filterEmptyField($branch, 'Branch', 'city_id');
					$current_branch_head_office = $this->MkCommon->filterEmptyField($branch, 'Branch', 'is_head_office');
					$branch_plants = $this->GroupBranch->Branch->getPlants($current_branch_plant);

					$branch_cities = $this->GroupBranch->Branch->BranchCity->getMerge($branch, $current_branch_id, 'list');
					$branch_cities = !empty($branch_cities['BranchCity'])?$branch_cities['BranchCity']:false;
					
                	Configure::write('__Site.Branch.code', $current_branch_code);
                	Configure::write('__Site.Branch.City.Bongkar.id', $branch_cities);
                	Configure::write('__Site.Branch.City.id', $current_branch_city_id);
					Configure::write('__Site.config_branch_plant', $current_branch_plant);
					Configure::write('__Site.config_branch_head_office', $current_branch_head_office);
				}
			}

			/*Auth*/
			$_allowedModule = array(
				'users' => array(
					'change_branch', 'search', 'logout',
					'login', 'dashboard', 'authorization',
					'profile'
				),
				'pages' => array(
					'dashboard', 'notifications',
					'referer_notification',
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
					'search', 'ttuj_edit', 'info_truk',
					'surat_jalan_outstanding',
				),
				'cashbanks' => array(
					'search'
				),
			);
			Configure::write('__Site.allowed_module', $_allowedModule);

			$is_allow = false;
			$current_group_branch_id = false;
			$conditionsBranch = array();
			$group_branches = array();

			if( $GroupId != 1 ) {
				$conditionsBranch['GroupBranch.group_id'] = $GroupId;
			}

	    	$groupBranch = $this->GroupBranch->_callAllowListBranch($conditionsBranch);

	    	if( !empty($groupBranch) ) {
	    		foreach ($groupBranch as $key => $value) {
					$_group_branch_id = $this->MkCommon->filterEmptyField($value, 'GroupBranch', 'id');
					$_branch_id = $this->MkCommon->filterEmptyField($value, 'GroupBranch', 'branch_id');
					$_branch_name = $this->MkCommon->filterEmptyField($value, 'Branch', 'name');
					$group_branches[$_branch_id] = $_branch_name;

					if( $_branch_id == $current_branch_id ) {
						$current_group_branch_id = $_group_branch_id ;
					}

					if( !empty($_group_branch_id) ) {
						$branchActionModule = $this->BranchActionModule->getData('all', array(
							'conditions' => array(
								'BranchActionModule.group_branch_id' => $_group_branch_id,
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

								$_allowModule[$_branch_id][$controllerName]['action'][] = $actionName;

								if( !empty($extend_action) ) {
									$_allowModule[$_branch_id][$controllerName]['extends'][$actionName][] = $extend_action;
								}
								if( !empty($allow_function) ) {
									$allow_function = explode(',', $allow_function);
									
									foreach ($allow_function as $key => $function) {
										$functionArr = explode('-', $function);
										$function = !empty($functionArr[0])?$functionArr[0]:$function;
										$controllerName = !empty($functionArr[1])?$functionArr[1]:$controllerName;

										$_allowModule[$_branch_id][$controllerName]['action'][] = $function;
									}
								}
							}
						}
					}
	    		}
	    	}

            Configure::write('__Site.Data.Group.Branch', $group_branches);
			Configure::write('__Site.config_allow_module', $_allowModule);

			if( $GroupId == 1 ) {
				Configure::write('__Site.Allow.All.Branch', array_keys($group_branches));
				$allowBranch = $list_branches;
				$postingUnposting = true;
			} else {
				$allowBranch = $this->MkCommon->allowBranch($group_branches);
				$postingUnpostingArr = !empty($_allowModule[$current_branch_id]['revenues']['action'])?$_allowModule[$current_branch_id]['revenues']['action']:array();

				if( in_array('edit', $postingUnpostingArr) ) {
					$postingUnposting = true;
				} else {
					$postingUnposting = false;
				}
			}

			Configure::write('__Site.config_branch_id', $current_branch_id);
			
			$allowAction = !empty($_allowedModule[$paramController])?$_allowedModule[$paramController]:array();
			$allowPage = in_array($paramAction, $allowAction)?true:false;

			Configure::write('__Site.config_allow_branchs', $allowBranch);
			
			if( !empty($allowBranch) ) {
				Configure::write('__Site.config_allow_branch_id', array_keys($allowBranch));
			}

			if( !empty($allowPage) || $paramController == 'ajax' || $GroupId == 1 ){
				$is_allow = true;
			}else {
				$allowPage = $this->MkCommon->allowPage( $current_branch_id, true );

				if( !empty($allowPage) ){
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

	        // if(isset($this->params['named']['ntf']) && !empty($this->params['named']['ntf'])){
	        // 	$this->User->Notification->notifCheck($this->user_id, $this->params['named']['ntf']);
	        // }

	        // $cacheName = sprintf('LeadTime-%s', $this->user_id);
			// $lead_time_notif = Cache::read($cacheName, 'short');
			
			// if(empty($lead_time_notif)){
			// 	$this->loadModel('Ttuj');
			// 	$overlead_time_destination = $this->Ttuj->getData('all', array(
			// 		'conditions' => array(
			// 			'Ttuj.is_arrive' => 1,
			// 			'Ttuj.arrive_over_time >' => 0,
			// 			'Ttuj.is_pool' => 0,
			// 			'Ttuj.completed' => 0,
			// 		),
			// 	));
			// 	$overlead_time_pool = $this->Ttuj->getData('all', array(
			// 		'conditions' => array(
			// 			'Ttuj.back_orver_time >' => 0,
			// 			'OR' => array(
			// 				array(
			// 					'Ttuj.is_arrive' => 1,
			// 					'Ttuj.is_pool' => 1,
			// 				),
			// 				array(
			// 					'Ttuj.completed' => 1,
			// 				),
			// 			),
			// 		),
			// 		'fields' => array(
			// 			'Ttuj.id', 'Ttuj.nopol', 'Ttuj.no_ttuj'
			// 		)
			// 	));
				
			// 	if(!empty($overlead_time_destination) || !empty($overlead_time_pool)){
			// 		$list_id_user_admin = $this->User->getData('list', array(
			// 			'conditions' => array(
			// 				'User.group_id' => 1,
			// 				'User.status' => 1
			// 			),
			// 			'fields' => array(
			// 				'User.id'
			// 			)
			// 		));

			// 		if(!empty($list_id_user_admin)){
			// 			if(!empty($overlead_time_destination)){
			// 				$ttuj_id = Set::extract('/Ttuj/id', $overlead_time_destination);

			// 				$check_notif = $this->User->Notification->getData('list', array(
			// 					'conditions' => array(
			// 						'Notification.document_id' => $ttuj_id,
			// 						'Notification.action' => 'overlead_time_destination'
			// 					)
			// 				));

			// 				if(empty($check_notif)){
			// 					foreach ($overlead_time_destination as $key => $value) {
			// 						$data_ttuj = !empty($value['Ttuj'])?$value['Ttuj']:false;
			// 						$this->User->Notification->saveData($list_id_user_admin, array(
			// 							'document_id' => $data_ttuj['id'],
			// 							'action' => 'overlead_time_destination',
			// 							'name' => sprintf(__('Truk dengan Nopol %s dengan no TTUJ %s telah melewati lead time tujuan'), $data_ttuj['nopol'], $data_ttuj['no_ttuj']),
			// 							'url' => serialize(array(
			// 								'controller' => 'revenues',
			// 								'action' => 'ttuj_edit',
			// 								$data_ttuj['id']
			// 							)),
			// 							'type_notif' => 'danger',
			// 							'icon_modul' => 'truck',
			// 							'link' => __('Lihat TTUJ')
			// 						));
			// 					}
			// 				}
			// 			}

			// 			if(!empty($overlead_time_pool)){
			// 				$ttuj_id = Set::extract('/Ttuj/id', $overlead_time_pool);

			// 				$check_notif = $this->User->Notification->getData('list', array(
			// 					'conditions' => array(
			// 						'Notification.document_id' => $ttuj_id,
			// 						'Notification.action' => 'overlead_time_pool'
			// 					)
			// 				));

			// 				if(empty($check_notif)){
			// 					foreach ($overlead_time_pool as $key => $value) {
			// 						$data_ttuj = $value['Ttuj'];
			// 						$this->User->Notification->saveData($list_id_user_admin, array(
			// 							'document_id' => $data_ttuj['id'],
			// 							'action' => 'overlead_time_pool',
			// 							'name' => sprintf(__('Truk dengan Nopol %s dengan no TTUJ %s telah melewati lead time balik pool'), $data_ttuj['nopol'], $data_ttuj['no_ttuj']),
			// 							'url' => serialize(array(
			// 								'controller' => 'revenues',
			// 								'action' => 'ttuj_edit',
			// 								$data_ttuj['id']
			// 							)),
			// 							'type_notif' => 'danger',
			// 							'icon_modul' => 'truck',
			// 							'link' => __('Lihat TTUJ')
			// 						));
			// 					}
			// 				}
			// 			}
			// 		}
			// 	}

			// 	Cache::write($cacheName, 1, 'short');
			// }

			$notifications = $this->User->Notification->_callNotifications();
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
	    	'invStatus', 'postingUnposting', 'notifications',
	    	'list_branches', 'current_branch_id',
	    	'isAjax'
    	));
	}

	function isAuthorized($user) {
	    // return false;
	    return $this->Auth->loggedIn();
	}
}
