<?php
App::uses('Controller', 'Controller');
App::uses('Common', 'Utility');

class AppController extends Controller {
	public $components = array(
		'MkCommon', 
		'Auth', 
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
			$this->MkCommon->_callDataClosing();

			Configure::write('__Site.User.employe_position_id', $employe_position_id);
			Configure::write('__Site.config_group_id', $GroupId);
			Configure::write('__Site.config_user_id', $this->user_id);
			Configure::write('__Site.config_user_data', $User);

			// Set Variable Branch
			$my_branch_id = $this->MkCommon->filterEmptyField($User, 'Employe', 'branch_id');
			$my_branch_id = !empty($user_branch)?$user_branch:$my_branch_id;

	    	if( $GroupId == 1 ) {
				$cacheName = 'Branch.List.Admin';
	    	} else {
				$cacheName = 'Branch.List';
	    	}


	    	if( $GroupId == 1 ) {
		    	$list_branches = $this->GroupBranch->Branch->getData('list', array(
					'cache' => __('Branch.Admin'),
		    	));
	    	} else {
		    	$list_branches = $this->GroupBranch->getData('list', array(
		    		'conditions' => array(
		    			'Branch.status' => true,
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
					'cache' => __('Branch.User.%s', $GroupId),
	    		));
	    	}

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
					'cache' => __('Branch.ID.%s', $current_branch_id),
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
                	Configure::write('__Site.Branch.cogs_id', Common::hashEmptyField($branch, 'Branch.cogs_id'));
					Configure::write('__Site.config_branch_plant', $current_branch_plant);
					Configure::write('__Site.config_branch_head_office', $current_branch_head_office);
					Configure::write('__Site.config_cost_center_readonly', Common::hashEmptyField($branch, 'Branch.is_cost_center_readonly'));
				}
			}

			Configure::write('__Site.Data.Branch', $list_branches);
            Configure::write('__Site.Data.Branch.id', array_keys($list_branches));

			/*Auth*/
			$_allowedModule = array(
				'users' => array(
					'change_branch', 'search', 'logout',
					'login', 'dashboard', 'authorization',
					'profile',
				),
				'pages' => array(
					'dashboard', 'notifications',
					'referer_notification', 'bypass_chart_maintenance',
					'bypass_chart_maintenance_laka',
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
					'surat_jalan_outstanding', 'detail_ttuj_payment',
					'bypass_search', 'import_progress', 'import_cancel',
				),
				'cashbanks' => array(
					'search'
				),
				'ttujs' => array(
					'search',
				),
				'reports' => array(
					'generate_excel', 'report_execute',
					'admin_download', 'download',
				),
				'products' => array(
					'expenditure_documents', 'spk_products', 'stocks',
					'receipt_documents', 'receipt_document_products',
					'receipt_choose_documents', 'bypass_receipt_serial_numbers',
					'search',

				),
				'purchases' => array(
					'po_documents', 'search',
				),
				'spk' => array(
					'search',
				),
				'assets' => array(
					'search',
				),
				'insurances' => array(
					'search',
				),
				'lakas' => array(
					'search',
				),
				'debt' => array(
					'bypass_search', 'search',
				),
				'titipan' => array(
					'bypass_search', 'search',
				),
			);
			Configure::write('__Site.allowed_module', $_allowedModule);

			$is_allow = false;
			$current_group_branch_id = false;
			$conditionsBranch = array();
			$group_branches = array();
			
			if( $GroupId != 1 ) {
				$conditionsBranch['GroupBranch.group_id'] = $GroupId;
				$cacheName = __('GroupBranch.%s.%s', $current_branch_id, $GroupId);
			} else {
				$cacheName = __('GroupBranch.admin.%s', $current_branch_id);
			}
			
			$branchCache = Cache::read($cacheName, 'default');

			if( empty($branchCache) ) {
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

			    $city_branches = $this->GroupBranch->Branch->getData('list', array(
			    	'fields' => array(
			    		'Branch.id', 'Branch.city_id',
		    		),
		    	));
		    	$city_branches = array_values($city_branches);

				Cache::write($cacheName, array(
					'group_branches' => $group_branches,
					'_allowModule' => $_allowModule,
					'groupBranch' => $groupBranch,
					'city_branches' => $city_branches,
				), 'default');
		    } else {
				$groupBranch = Hash::get($branchCache, 'groupBranch');
				$group_branches = Hash::get($branchCache, 'group_branches');
				$_allowModule = Hash::get($branchCache, '_allowModule');
				$city_branches = Hash::get($branchCache, 'city_branches');
			}

            Configure::write('__Site.Data.Group.Branch', $group_branches);
			Configure::write('__Site.config_allow_module', $_allowModule);
			Configure::write('__Site.Data.Branch.City.id', $city_branches);

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
			Configure::write('__Site.Spk.type', array(
		    	'internal' => __('Internal'),
		    	'eksternal' => __('Eksternal'),
		    	'wht' => __('WHT'),
		    	'production' => __('Produksi'),
	    	));
			
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

			$notifications = $this->User->Notification->_callNotifications();
			$approval_notifs = $this->User->Notification->_callApprovalNotifs();
			$payment_notifs = $this->MkCommon->_callPaymentNotifs();
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
	    	'isAjax', 'approval_notifs', 'payment_notifs'
    	));
	}

	function isAuthorized($user) {
	    return $this->Auth->loggedIn();
	}
}
