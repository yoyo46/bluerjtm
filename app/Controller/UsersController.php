<?php
App::uses('AppController', 'Controller');
class UsersController extends AppController {

	public $name = 'Users';
    public $uses = array('Contact', 'User');
	public $components = array('RjUser');
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow(
            'login', 'logout', 'forgot',
            'add', 'edit', 'list_user'
        );
        $this->set('title_for_layout', __('ERP RJTM | Data User'));
        $this->set('module_title', __('User'));
	}

    function search( $index = 'index', $admin = false ){
        $refine = array();
        if(!empty($this->request->data)) {
            $refine = $this->RjUser->processRefine($this->request->data);
            $params = $this->RjUser->generateSearchURL($refine);
            $params['action'] = $index;

            if( $admin ) {
                $params['admin'] = true;
            }
            $this->redirect($params);
        }
        $this->redirect('/');
    }

    function login() {
        // debug($this->Auth->password('admin'));die();
        $this->layout = 'login';
    	if(!$this->MkCommon->loggedIn()){
			if(!empty($this->request->data)){
                $emailCache = $this->RequestHandler->getClientIP();
                $session_error = false;
                $session_name_ip = 'login_auth.'.$emailCache;

                if( $this->Cookie->read($session_name_ip) ){
                    $session_error = true;
                }

                // if( !$session_error ){
                    if($this->Auth->login()){
                        $this->redirect($this->Auth->redirect());   
                    }else{
                        $get_cookie_session = $this->Cookie->read('login_'.$emailCache);
                        $get_cookie_session = !empty($get_cookie_session)?$get_cookie_session:0;

                        if($get_cookie_session >= 3){
                            $this->Cookie->write($session_name_ip, 1, '1 hour');
                            $this->MkCommon->setCustomFlash(__('Gagal melakukan login, Anda sudah melakukan 3x percobaan login, silahkan tunggu 1 jam kemudian untuk melakukan login kembali.'), 'error');
                        }else{
                            $this->MkCommon->setCustomFlash(__('Gagal melakukan login, username atau password Anda tidak valid.'), 'error');
                            $get_cookie_session++;
                            $this->Cookie->write('login_'.$emailCache, $get_cookie_session);
                        }
                    }
                // }else{
                //     $this->MkCommon->setCustomFlash(__('Gagal melakukan login, Anda sudah melakukan 3x percobaan login, silahkan tunggu 1 jam kemudian untuk melakukan login kembali.'), 'error');
                // }

                if( !empty($this->request->data['User']['password']) ) {
                    unset($this->request->data['User']['password']);
                }
			}
    	}else{
    		$this->redirect($this->Auth->loginRedirect);
    	}
	}

	function logout() {
		$this->Auth->logout();
		$this->MkCommon->setCustomFlash(__('Anda berhasil Log out.'), 'success');
		$this->redirect($this->Auth->logout());
	}

	function dashboard(){
        $this->loadModel('Ttuj');

        $ttujSJ = $this->Ttuj->getData('count', array(
            'conditions' => array(
                'Ttuj.is_sj_completed' => 0,
                'Ttuj.status' => 1,
            ),
        ), false);

		$this->set('sub_module_title', 'dashboard');
	}

	function authorization(){
        // debug($this->Auth->password('admin'));die();
		if(!empty($this->request->data)){
			
    		$data = $this->request->data;
    		$this->User->id = $this->Auth->user('id');

			if( !empty($data['User']['current_password']) ) {
				$data['User']['current_password'] = !empty($data['User']['current_password'])?$this->Auth->password($data['User']['current_password']):'';
			} else {
				$data['User']['current_password'] = '';
			}

            $user = $this->User->getData('first', array(
                'conditions' => array(
                    'User.id' => $this->Auth->user('id')
                ),
                'fields' => array(
                    'User.password'
                )
            ));

    		$this->User->set($data);

    		if(!empty($data['User']['current_password']) && ($data['User']['current_password'] == $user['User']['password']) ){
				$data['User']['password'] = !empty($data['User']['password']) ? $this->Auth->password($data['User']['password']):'';
				$data['User']['password_confirmation'] = !empty($data['User']['password_confirmation'])?$this->Auth->password($data['User']['password_confirmation']):'';

    			if($this->User->save($data)){
    				$this->MkCommon->setCustomFlash(__('sukses merubah password'), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses merubah password #%s'), $this->User->id), $this->user_data, $this->RequestHandler, $this->params );
    				$this->redirect($this->here);
    			}else{
    				$this->MkCommon->setCustomFlash(__('Gagal merubah password'), 'error');	
                    $this->Log->logActivity( sprintf(__('Gagal merubah password #%s'), $this->User->id), $this->user_data, $this->RequestHandler, $this->params, 1 );
    			}
    		}else{
    			$this->MkCommon->setCustomFlash(__('Gagal merubah password'), 'error');
    		}
    	}

    	$this->set('sub_module_title', 'merubah password');
	}

	function profile(){
		$this->set('sub_module_title', 'Profile');
        $user = $this->User->getData('first', array(
            'conditions' => array(
                'User.id' => $this->Auth->user('id')
            )
        ));

		if(!empty($this->request->data)){
			$data = $this->request->data;

			if($user['User']['password'] == $this->Auth->password($data['User']['old_password'])){
                $data['User']['username'] = (!empty($data['User']['email'])) ? $data['User']['email'] : '';
                $data['User']['birthdate'] = (!empty($data['User']['birthdate'])) ? $this->MkCommon->getDate($data['User']['birthdate']) : '';
                
                $this->User->id = $this->Auth->user('id');
				$this->User->set($data);

                if($this->User->validates()){
					if($this->User->save()){
						$this->MkCommon->setCustomFlash(__('sukses merubah data'), 'success');
                        $this->Log->logActivity( sprintf(__('Sukses merubah data #%s'), $this->User->id), $this->user_data, $this->RequestHandler, $this->params );
						$this->redirect($this->here);	
					}else{
						$this->MkCommon->setCustomFlash(__('Gagal merubah data'), 'error');
                        $this->Log->logActivity( sprintf(__('Gagal merubah data #%s'), $this->User->id), $this->user_data, $this->RequestHandler, $this->params, 1 );
						unset($this->request->data['User']['old_password']);
					}
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal merubah data'), 'error');
                    unset($this->request->data['User']['old_password']);
                }
			}else{
				$this->MkCommon->setCustomFlash(__('Password salah'), 'error');
				unset($this->request->data['User']['old_password']);	
			}
		}else{
            $this->request->data = $user;
        }

        if( !empty($this->request->data['User']['birthdate']) && $this->request->data['User']['birthdate'] != '0000-00-00' ) {
            $this->request->data['User']['birthdate'] = date('d/m/Y', strtotime($this->request->data['User']['birthdate']));
        } else {
            $this->request->data['User']['birthdate'] = '';
        }
	}

    function groups(){
        if( in_array('view_group_user', $this->allowModule) ) {
            $this->loadModel('Group');
            $conditions = array(
                'Group.status' => 1,
            );

            if(!empty($this->params['named'])){
                $refine = $this->params['named'];

                if(!empty($refine['name'])){
                    $name = urldecode($refine['name']);
                    $this->request->data['Group']['name'] = $name;
                    $conditions['Group.name LIKE '] = '%'.$name.'%';
                }
            }

            $groups = $this->Group->find('all', array(
                'conditions' => $conditions,
            ));

            $this->set('active_menu', 'groups');
            $this->set('sub_module_title', 'Group');
            $this->set('groups', $groups);
        } else {
            $this->redirect($this->referer());
        }
    }

    function group_add(){
        if( in_array('insert_group_user', $this->allowModule) ) {
            $this->set('sub_module_title', 'Tambah Group');
            $this->doGroup();
        } else {
            $this->redirect($this->referer());
        }
    }

    function group_edit($id){
        if( in_array('update_group_user', $this->allowModule) ) {
            $this->loadModel('Group');
            $this->set('sub_module_title', 'Rubah Group');
            $Group = $this->Group->find('first', array(
                'conditions' => array(
                    'Group.id' => $id
                )
            ));

            if(!empty($Group)){
                $this->doGroup($id, $Group);
            }else{
                $this->MkCommon->setCustomFlash(__('Group tidak ditemukan'), 'error');  
                $this->redirect(array(
                    'controller' => 'users',
                    'action' => 'groups'
                ));
            }
        } else {
            $this->redirect($this->referer());
        }
    }

    function doGroup($id = false, $data_local = false){
        if(!empty($this->request->data)){
            $data = $this->request->data;
            if($id && $data_local){
                $this->Group->id = $id;
                $msg = 'merubah';
            }else{
                $this->loadModel('Group');
                $this->Group->create();
                $msg = 'menambah';
            }
            $this->Group->set($data);

            if($this->Group->validates($data)){
                if($this->Group->save($data)){
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Group'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s Group #%s'), $msg, $this->Group->id), $this->user_data, $this->RequestHandler, $this->params );
                    $this->redirect(array(
                        'controller' => 'users',
                        'action' => 'groups'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Group'), $msg), 'error');  
                    $this->Log->logActivity( sprintf(__('Gagal %s Group #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1 );
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Group'), $msg), 'error');
            }
        }else{
            
            if($id && $data_local){
                
                $this->request->data = $data_local;
            }
        }

        $this->render('group_form');
    }

    function group_toggle( $id = false ){
        if( in_array('delete_group_user', $this->allowModule) ) {
            $this->loadModel('Group');
            $locale = $this->Group->find('first', array(
                'conditions' => array(
                    'Group.id' => $id,
                    'Group.status' => 1,
                )
            ));

            if( !empty($locale) ){
                $value = true;
                if($locale['Group']['status']){
                    $value = false;
                }

                $this->Group->id = $id;
                $this->Group->set('status', $value);
                if($this->Group->save()){
                    $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses merubah status #%s'), $id), $this->user_data, $this->RequestHandler, $this->params );
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal merubah status #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );
                }
            }else{
                $this->MkCommon->setCustomFlash(__('Group tidak ditemukan.'), 'error');
            }

            $this->redirect($this->referer());
        } else {
            $this->redirect($this->referer());
        }
    }

    function list_user(){
        if( in_array('view_list_user', $this->allowModule) ) {
            $this->loadModel('User');
            $default_options = array(
                'conditions' => array(
                    'User.status' => 1
                ),
                'contain' => array(
                    'Group',
                    'Branch',
                )
            );

            if(!empty($this->params['named'])){
                $refine = $this->params['named'];

                if(!empty($refine['name'])){
                    $name = urldecode($refine['name']);
                    $this->request->data['User']['name'] = $name;
                    $default_options['conditions']['CONCAT(User.first_name,\' \',User.last_name) LIKE '] = '%'.$name.'%';
                }
                if(!empty($refine['email'])){
                    $email = urldecode($refine['email']);
                    $this->request->data['User']['email'] = $email;
                    $default_options['conditions']['User.email LIKE '] = '%'.$email.'%';
                }
            }

            $this->paginate = $this->User->getData('paginate', $default_options);
            $list_user = $this->paginate('User');

            $this->set('active_menu', 'list_user');
            $this->set('sub_module_title', 'User');
            $this->set('list_user', $list_user);
        } else {
            $this->redirect($this->referer());
        }
    }

    function add(){
        if( in_array('insert_list_user', $this->allowModule) ) {
            $this->loadModel('User');
            $this->set('sub_module_title', 'Tambah User');
            $this->doUser();
        } else {
            $this->redirect($this->referer());
        }
    }

    function edit($id){
        if( in_array('update_list_user', $this->allowModule) ) {
            $this->loadModel('User');
            $this->set('sub_module_title', 'Rubah User');
            $User = $this->User->getData('first', array(
                'conditions' => array(
                    'User.id' => $id
                )
            ));

            if(!empty($User)){
                $this->doUser($id, $User);
            }else{
                $this->MkCommon->setCustomFlash(__('User tidak ditemukan'), 'error');  
                $this->redirect(array(
                    'controller' => 'users',
                    'action' => 'list_user'
                ));
            }
        } else {
            $this->redirect($this->referer());
        }
    }

    function doUser($id = false, $data_local = false){
        if(!empty($this->request->data)){
            $data = $this->request->data;

            if($id && $data_local){
                $this->User->id = $id;
                $msg = 'merubah';
            }else{
                $this->User->create();
                $msg = 'menambah';
            }
            $this->User->set($data);

            if($this->User->validates($data)){
                $data['User']['username'] = (!empty($data['User']['email'])) ? $data['User']['email'] : '';
                $data['User']['birthdate'] = (!empty($data['User']['birthdate'])) ? $this->MkCommon->getDate($data['User']['birthdate']) : '';

                if( empty($id) ) {
                    $data['User']['password'] = $this->Auth->password($data['User']['password']);
                    $data['User']['password_confirmation'] = $this->Auth->password($data['User']['password_confirmation']);
                }

                if($this->User->save($data)){
                    $this->loadModel('Employe');
                    if(!empty($data_local['User']['employe_id']) && $data_local['User']['employe_id'] != $data['User']['employe_id']){
                        $this->Employe->updateAll(
                            array(
                                'Employe.is_registered' => 0
                            ),
                            array(
                                'Employe.id' => $data_local['User']['employe_id']
                            )
                        );
                    }
                    
                    $this->Employe->id = $data['User']['employe_id'];
                    $this->Employe->set('is_registered', 1);
                    $this->Employe->save();

                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s User'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s User #%s'), $msg, $this->User->id), $this->user_data, $this->RequestHandler, $this->params );
                    $this->redirect(array(
                        'controller' => 'users',
                        'action' => 'list_user',
                        'admin' => false,
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s User'), $msg), 'error');  
                    $this->Log->logActivity( sprintf(__('Gagal %s User #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1 );
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s User'), $msg), 'error');
            }

            unset($data['User']['password']);
            unset($data['User']['password_confirmation']);
        } else if($id && $data_local){
            $this->request->data = $data_local;

            if( !empty($this->request->data['User']['birthdate']) && $this->request->data['User']['birthdate'] != '0000-00-00' ) {
                $this->request->data['User']['birthdate'] = date('d/m/Y', strtotime($this->request->data['User']['birthdate']));
            } else {
                $this->request->data['User']['birthdate'] = '';
            }
        }

        $this->loadModel('Group');
        $this->loadModel('Branch');
        $groups = $this->Group->find('list', array(
            'conditions' => array(
                'id <>' => 3
            )
        ));
        $branches = $this->Branch->find('list', array(
            'conditions' => array(
                'id <>' => 3
            )
        ));
        $this->set('active_menu', 'list_user');
        
        $this->loadModel('City');
        $branch = $this->City->getData('list', array(
            'conditions' => array(
                'City.status' => 1,
                'City.is_branch' => 1,
            ),
            'fields' => array(
                'City.id', 'City.name'
            )
        ));

        $this->loadModel('Employe');
        $employes = $this->Employe->getData('all', array(
            'conditions' => array(
                'Employe.status' => 1,
                'OR' => array(
                    array(
                        'Employe.is_registered' => 0
                    ),
                    array(
                        'Employe.is_registered' => 1,
                        'Employe.id' => !empty($data_local['User']['employe_id']) ? $data_local['User']['employe_id'] : 0
                    )
                )
            ),
            'contain' => array(
                'EmployePosition'
            )
        ));

        $arr_list = array();
        if(!empty($employes)){
            foreach ($employes as $key => $value) {
                $arr_list[$value['Employe']['id']] = sprintf('%s (%s)', $value['Employe']['name'], $value['EmployePosition']['name']);
            }

            $employes = $arr_list;
        }

        $this->set(compact(
            'branches', 'groups', 'id', 'branch', 'employes'
        ));
        $this->render('user_form');
    }

    function toggle($id){
        if( in_array('view_user_toggle', $this->allowModule) ) {
            $this->loadModel('User');
            $locale = $this->User->getData('first', array(
                'conditions' => array(
                    'User.id' => $id
                )
            ));

            if($locale){
                $value = true;
                if($locale['User']['status']){
                    $value = false;
                }

                $this->User->id = $id;
                $this->User->set('status', $value);
                if($this->User->save()){
                    $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses merubah status user #%s'), $this->User->id), $this->user_data, $this->RequestHandler, $this->params );
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal merubah status user #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );
                }
            }else{
                $this->MkCommon->setCustomFlash(__('User tidak ditemukan.'), 'error');
            }

            $this->redirect($this->referer());
        } else {
            $this->redirect($this->referer());
        }
    }

    function employe_positions(){
        $this->loadModel('EmployePosition');
        $options = array(
            'conditions' => array(
                'status' => 1
            )
        );

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['name'])){
                $name = urldecode($refine['name']);
                $this->request->data['EmployePosition']['name'] = $name;
                $options['conditions']['EmployePosition.name LIKE '] = '%'.$name.'%';
            }
        }

        $this->paginate = $this->EmployePosition->getData('paginate', $options);
        $employe_positions = $this->paginate('EmployePosition');

        $this->set('active_menu', 'employes');
        $this->set('sub_module_title', 'Posisi Karyawan');
        $this->set('employe_positions', $employe_positions);
    }

    function employe_position_add(){
        $this->set('sub_module_title', 'Tambah Posisi Karyawan');
        $this->doEmployePosition();
    }

    function employe_position_edit($id){
        $this->loadModel('EmployePosition');
        $this->set('sub_module_title', 'Rubah Posisi Karyawan');
        $EmployePosition = $this->EmployePosition->getData('first', array(
            'conditions' => array(
                'EmployePosition.id' => $id
            )
        ));

        if(!empty($EmployePosition)){
            $this->doEmployePosition($id, $EmployePosition);
        }else{
            $this->MkCommon->setCustomFlash(__('Posisi Karyawan tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'users',
                'action' => 'employe_positions'
            ));
        }
    }

    function doEmployePosition($id = false, $data_local = false){
        if(!empty($this->request->data)){
            $data = $this->request->data;
            if($id && $data_local){
                $this->EmployePosition->id = $id;
                $msg = 'merubah';
            }else{
                $this->loadModel('EmployePosition');
                $this->EmployePosition->create();
                $msg = 'menambah';
            }
            $this->EmployePosition->set($data);

            if($this->EmployePosition->validates($data)){
                if($this->EmployePosition->save($data)){
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Posisi Karyawan'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s Posisi Karyawan #%s'), $msg, $this->EmployePosition->id), $this->user_data, $this->RequestHandler, $this->params );
                    $this->redirect(array(
                        'controller' => 'users',
                        'action' => 'employe_positions'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Posisi Karyawan'), $msg), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal %s Posisi Karyawan #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1 );
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Posisi Karyawan'), $msg), 'error');
            }
        }else{
            
            if($id && $data_local){
                
                $this->request->data = $data_local;
            }
        }

        $this->set('active_menu', 'employes');
        $this->render('employe_position_form');
    }

    function employe_position_toggle($id){
        $this->loadModel('EmployePosition');
        $locale = $this->EmployePosition->getData('first', array(
            'conditions' => array(
                'EmployePosition.id' => $id
            )
        ));

        if($locale){
            $value = true;
            if($locale['EmployePosition']['status']){
                $value = false;
            }

            $this->EmployePosition->id = $id;
            $this->EmployePosition->set('status', $value);
            if($this->EmployePosition->save()){
                $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
                $this->Log->logActivity( sprintf(__('Sukses merubah status posisi karyawan ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params );
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal merubah status posisi karyawan ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Posisi Karyawan tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    function employes(){
        $this->loadModel('Employe');
        $start = 1;
        $limit = 20;
        $options = array(
            'contain' => array(
                'EmployePosition'
            ),
            'order' => array(
                'Employe.status' => 'DESC',
                'Employe.name' => 'ASC',
            ),
            'limit' => $limit,
        );

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['name'])){
                $name = urldecode($refine['name']);
                $this->request->data['Employe']['name'] = $name;
                $options['conditions']['Employe.name LIKE '] = '%'.$name.'%';
            }
            if(!empty($refine['position'])){
                $position = urldecode($refine['position']);
                $this->request->data['Employe']['employe_position_id'] = $position;
                $options['conditions']['Employe.employe_position_id'] = $position;
            }
            if(!empty($refine['phone'])){
                $phone = urldecode($refine['phone']);
                $this->request->data['Employe']['phone'] = $phone;
                $options['conditions']['Employe.phone LIKE '] = '%'.$phone.'%';
            }

            if(!empty($refine['page'])){
                $start = (($refine['page']-1)*$limit)+1;
            }
        }

        $this->paginate = $this->Employe->getData('paginate', $options, false);
        $employes = $this->paginate('Employe');

        $this->loadModel('EmployePosition');
        $employe_positions = $this->EmployePosition->getData('list', array(
            'fields' => array(
                'EmployePosition.id', 'EmployePosition.name'
            )
        ));
        $this->set('employe_positions', $employe_positions);
        $this->set('active_menu', 'employes');
        $this->set('sub_module_title', 'Karyawan');
        $this->set('employes', $employes);
        $this->set('start', $start);
    }

    function employe_add(){
        $this->set('sub_module_title', 'Tambah Karyawan');
        $this->doEmploye();
    }

    function employe_edit($id){
        $this->loadModel('Employe');
        $this->set('sub_module_title', 'Rubah Karyawan');
        $Employe = $this->Employe->getData('first', array(
            'conditions' => array(
                'Employe.id' => $id
            )
        ));

        if(!empty($Employe)){
            $this->doEmploye($id, $Employe);
        }else{
            $this->MkCommon->setCustomFlash(__('Karyawan tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'users',
                'action' => 'employes'
            ));
        }
    }

    function doEmploye($id = false, $data_local = false){
        if(!empty($this->request->data)){
            $data = $this->request->data;
            if($id && $data_local){
                $this->Employe->id = $id;
                $msg = 'merubah';
            }else{
                $this->loadModel('Employe');
                $this->Employe->create();
                $msg = 'menambah';
            }
            $this->Employe->set($data);

            if($this->Employe->validates($data)){
                if($this->Employe->save($data)){
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Karyawan'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s Karyawan #%s'), $msg, $this->Employe->id), $this->user_data, $this->RequestHandler, $this->params );
                    $this->redirect(array(
                        'controller' => 'users',
                        'action' => 'employes'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Karyawan'), $msg), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal %s Karyawan #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1 );
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Karyawan'), $msg), 'error');
            }
        }else{
            
            if($id && $data_local){
                $this->request->data = $data_local;
            }
        }

        $this->loadModel('EmployePosition');
        $employe_positions = $this->EmployePosition->getData('list', array(
            'fields' => array(
                'EmployePosition.id', 'EmployePosition.name'
            )
        ));
        $this->set('employe_positions', $employe_positions);
        $this->set('active_menu', 'employes');
        $this->render('employe_form');
    }

    function employe_toggle($id){
        $this->loadModel('Employe');
        $locale = $this->Employe->getData('first', array(
            'conditions' => array(
                'Employe.id' => $id
            )
        ));

        if($locale){
            $value = true;
            if($locale['Employe']['status']){
                $value = false;
            }

            $this->Employe->id = $id;
            $this->Employe->set('status', $value);
            if($this->Employe->save()){
                $this->MkCommon->setCustomFlash(__('Sukses merubah status.'), 'success');
                $this->Log->logActivity( sprintf(__('Sukses merubah status karyawan ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params );
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal merubah status karyawan ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1 );        
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Karyawan tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }
}
?>