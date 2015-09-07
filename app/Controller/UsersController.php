<?php
App::uses('AppController', 'Controller');
class UsersController extends AppController {

	public $name = 'Users';
	public $components = array('RjUser');
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow(
            'login', 'logout',
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
        $this->layout = 'login';

    	if(!$this->MkCommon->loggedIn()){
			if(!empty($this->request->data)){
                // Set Session Name
                $session_name_ip = 'Login.Auth.Block';
                $session_try_login = 'Login.Try';

                // Login Failed

                $session_error = $this->Cookie->read($session_name_ip);
                $get_cookie_session = $this->Cookie->read($session_try_login);
                $get_cookie_session = !empty($get_cookie_session)?$get_cookie_session:0;
                $msgFailedLogin = __('Gagal melakukan login, Anda sudah melakukan 3x percobaan login, silahkan tunggu 1 jam kemudian untuk melakukan login kembali.');

                if( !empty($session_error) ) {
                    $this->MkCommon->setCustomFlash($msgFailedLogin, 'error');
                } else if($this->Auth->login()){
                    $this->Cookie->write($session_name_ip, 0, '1 hour');
                    $this->Cookie->write($session_try_login, 0);
                    $this->redirect($this->Auth->redirect());   
                }else{
                    if($get_cookie_session >= 3){
                        $this->Cookie->write($session_name_ip, 1, '1 hour');
                        $this->MkCommon->setCustomFlash($msgFailedLogin, 'error');
                    }else{
                        $get_cookie_session++;
                        $this->MkCommon->setCustomFlash(__('Gagal melakukan login, username atau password Anda tidak valid.'), 'error');
                        $this->Cookie->write($session_try_login, $get_cookie_session);
                    }
                }

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
            ),
        ));

		$this->set('sub_module_title', 'dashboard');
	}

    function password( $id = false ){
        $this->set('sub_module_title', __('Ganti Password'));
        $user = $this->User->getData('first', array(
            'conditions' => array(
                'User.id' => $id
            )
        ), true, array(
            'status' => 'all',
        ));

        if(!empty($this->request->data)){
            $data = $this->request->data;
            $this->User->id = $id;

            $this->User->set($data);

            if( $this->User->validates() ){
                $data['User']['password'] = !empty($data['User']['new_password']) ? $this->Auth->password($data['User']['new_password']):'';

                if($this->User->save($data)){
                    $id = $this->User->id;

                    $this->MkCommon->setCustomFlash(__('Sukses mengganti password'), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses mengganti password #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
                    $this->redirect(array(
                        'controller' => 'users',
                        'action' => 'list_user',
                        'admin' => false,
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal mengganti password'), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal mengganti password #%s'), $this->User->id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
                }
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal mengganti password'), 'error');
            }
        }
        
        $this->set('active_menu', 'list_user');
    }

	function authorization(){
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
                    $id = $this->User->id;
    				$this->MkCommon->setCustomFlash(__('sukses merubah password'), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses merubah password #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
    				$this->redirect($this->here);
    			}else{
    				$this->MkCommon->setCustomFlash(__('Gagal merubah password'), 'error');	
                    $this->Log->logActivity( sprintf(__('Gagal merubah password #%s'), $this->User->id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
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
                'User.id' => $this->user_id,
            ),
        ));

		if(!empty($this->request->data)){
			$data = $this->request->data;
            $employe_id = !empty($user['Employe']['id'])?$user['Employe']['id']:false;

            $data['User']['username'] = (!empty($data['User']['email'])) ? $data['User']['email'] : '';
            $data['Employe']['birthdate'] = (!empty($data['Employe']['birthdate'])) ? $this->MkCommon->getDate($data['Employe']['birthdate']) : '';
            
            $this->User->id = $this->user_id;
			$this->User->set($data);
            $this->User->Employe->id = $employe_id;
            $this->User->Employe->set($data);

            $userValidates = $this->User->validates();
            $employeValidates = $this->User->Employe->validates();

            if($userValidates && $employeValidates){
				if($this->User->save() && $this->User->Employe->save()){
                    $id = $this->User->id;

                    $this->params['old_data'] = $user;
                    $this->params['data'] = $data;

					$this->MkCommon->setCustomFlash(__('sukses merubah data'), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses merubah data #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
					$this->redirect($this->here);	
				}else{
					$this->MkCommon->setCustomFlash(__('Gagal merubah data'), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal merubah data #%s'), $this->User->id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
				}
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah data'), 'error');
            }
		}else{
            $this->request->data = $user;
        }

        if( !empty($this->request->data['Employe']['birthdate']) && $this->request->data['Employe']['birthdate'] != '0000-00-00' ) {
            $this->request->data['Employe']['birthdate'] = date('d/m/Y', strtotime($this->request->data['Employe']['birthdate']));
        } else {
            $this->request->data['Employe']['birthdate'] = '';
        }
	}

    function groups(){
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

        $this->paginate = $this->Group->getData('paginate', array(
            'conditions' => $conditions,
            'limit' => 20,
        ));
        $groups = $this->paginate('Group');

        $this->set('active_menu', 'groups');
        $this->set('sub_module_title', __('Grup User'));
        $this->set('groups', $groups);
    }

    function group_add(){
        $this->set('sub_module_title', 'Tambah Grup User');
        $this->doGroup();
    }

    function group_edit($id){
        $this->loadModel('Group');
        $this->set('sub_module_title', 'Edit Grup User');
        $Group = $this->Group->getData('first', array(
            'conditions' => array(
                'Group.id' => $id
            )
        ));

        if(!empty($Group)){
            $this->doGroup($id, $Group);
        }else{
            $this->MkCommon->setCustomFlash(__('Grup User tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'users',
                'action' => 'groups'
            ));
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
                    $id = $this->Group->id;

                    $this->params['old_data'] = $data_local;
                    $this->params['data'] = $data;

                    $this->MkCommon->setCustomFlash(__('Berhasil menyimpan Grup User'), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s Grup User #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
                    $this->redirect(array(
                        'controller' => 'users',
                        'action' => 'groups'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(__('Gagal menyimpan Grup User'), 'error');  
                    $this->Log->logActivity( sprintf(__('Gagal %s Grup User #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
                }
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal menyimpan Grup User'), 'error');
            }
        }else{
            
            if($id && $data_local){
                
                $this->request->data = $data_local;
            }
        }

        $this->set('active_menu', 'groups');
        $this->render('group_form');
    }

    function group_toggle( $id = false ){
        $this->loadModel('Group');
        $locale = $this->Group->getData('first', array(
            'conditions' => array(
                'Group.id' => $id,
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
                $this->MkCommon->setCustomFlash(__('Berhasil menghapus Grup User'), 'success');
                $this->Log->logActivity( sprintf(__('Berhasil menghapus Grup User #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal menghapus Grup User'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal menghapus Grup User #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Grup User tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    function list_user(){
        $default_options = array(
            'contain' => array(
                'Group',
            ),
        );

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['name'])){
                $name = urldecode($refine['name']);
                $this->request->data['User']['name'] = $name;
                $default_options['conditions']['CONCAT(Employe.first_name,\' \',Employe.last_name) LIKE '] = '%'.$name.'%';
            }
            if(!empty($refine['email'])){
                $email = urldecode($refine['email']);
                $this->request->data['User']['email'] = $email;
                $default_options['conditions']['User.email LIKE '] = '%'.$email.'%';
            }
            if(!empty($refine['branch'])){
                $value = urldecode($refine['branch']);
                $this->request->data['Employe']['branch_id'] = $value;
                $default_options['conditions']['Employe.branch_id'] = $value;
            }
        }

        $this->paginate = $this->User->getData('paginate', $default_options, true, array(
            'status' => 'all',
        ));
        $list_user = $this->paginate('User');
        $branches = $this->GroupBranch->Branch->getData('list');

        if( !empty($list_user) ) {
            foreach ($list_user as $key => $value) {
                $branch_id = $this->MkCommon->filterEmptyField($value, 'Employe', 'branch_id');
                $value = $this->GroupBranch->Branch->getMerge($value, $branch_id);
                $list_user[$key] = $value;
            }
        }

        $this->set('active_menu', 'list_user');
        $this->set('sub_module_title', 'User');
        $this->set(compact(
            'list_user', 'branches'
        ));
    }

    function add(){
        $this->set('sub_module_title', 'Tambah User');
        $this->doUser();
    }

    function edit($id){
        $this->set('sub_module_title', 'Rubah User');
        $User = $this->User->getData('first', array(
            'conditions' => array(
                'User.id' => $id
            )
        ), true, array(
            'status' => 'all',
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
                $employe_id = (!empty($data['User']['employe_id'])) ? $data['User']['employe_id'] : '';
                $data = $this->User->Employe->getMerge($data, $employe_id);
                $data['User']['username'] = (!empty($data['User']['email'])) ? $data['User']['email'] : '';

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
                    $id = $this->User->id;

                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s User'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s User #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
                    $this->redirect(array(
                        'controller' => 'users',
                        'action' => 'list_user',
                        'admin' => false,
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s User'), $msg), 'error');  
                    $this->Log->logActivity( sprintf(__('Gagal %s User #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s User'), $msg), 'error');
            }

            unset($data['User']['password']);
            unset($data['User']['password_confirmation']);
        } else if($id && $data_local){
            $this->request->data = $data_local;
        }

        $this->loadModel('Group');
        $this->loadModel('Employe');

        $groups = $this->Group->getData('list');
        $branches = $this->GroupBranch->Branch->getData('list');
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
        ));

        $arr_list = array();
        if(!empty($employes)){
            foreach ($employes as $key => $value) {
                $employe_name = $value['Employe']['full_name'];

                if( !empty($value['EmployePosition']['name']) ) {
                    $employe_name = sprintf('%s (%s)', $employe_name, $value['EmployePosition']['name']);
                }

                $arr_list[$value['Employe']['id']] = $employe_name;
            }

            $employes = $arr_list;
        }

        $this->set('active_menu', 'list_user');
        $this->set(compact(
            'branches', 'groups', 'id', 'employes'
        ));
        $this->render('user_form');
    }

    function toggle($id){
        $locale = $this->User->getData('first', array(
            'conditions' => array(
                'User.id' => $id
            ),
        ), true, array(
            'status' => 'all',
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
                $this->Log->logActivity( sprintf(__('Sukses merubah status user #%s'), $this->User->id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal merubah status user #%s'), $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
            }
        }else{
            $this->MkCommon->setCustomFlash(__('User tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
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

        $this->set('active_menu', 'employe_positions');
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
                    $id = $this->EmployePosition->id;
                    $this->params['old_data'] = $data_local;
                    $this->params['data'] = $data;

                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Posisi Karyawan'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s Posisi Karyawan #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
                    $this->redirect(array(
                        'controller' => 'users',
                        'action' => 'employe_positions'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Posisi Karyawan'), $msg), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal %s Posisi Karyawan #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Posisi Karyawan'), $msg), 'error');
            }
        }else{
            
            if($id && $data_local){
                
                $this->request->data = $data_local;
            }
        }

        $this->set('active_menu', 'employe_positions');
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
                $this->MkCommon->setCustomFlash(__('Berhasil menghapus data posisi karyawan'), 'success');
                $this->Log->logActivity( sprintf(__('Berhasil menghapus data posisi karyawan ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal menghapus data posisi karyawan'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal menghapus data posisi karyawan ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Posisi Karyawan tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    function employes(){
        $this->loadModel('Employe');
        $limit = 20;
        $options = array(
            'limit' => $limit,
        );

        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['name'])){
                $name = urldecode($refine['name']);
                $this->request->data['Employe']['name'] = $name;
                $options['conditions']['Employe.full_name LIKE '] = '%'.$name.'%';
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
            if(!empty($refine['branch'])){
                $value = urldecode($refine['branch']);
                $this->request->data['Employe']['branch_id'] = $value;
                $options['conditions']['Employe.branch_id'] = $value;
            }
        }

        $this->paginate = $this->Employe->getData('paginate', $options, true, array(
            'status' => 'all',
        ));
        $employes = $this->paginate('Employe');
        $employe_positions = $this->Employe->EmployePosition->getData('list', array(
            'fields' => array(
                'EmployePosition.id', 'EmployePosition.name'
            )
        ));
        $branches = $this->GroupBranch->Branch->getData('list');

        $this->set('active_menu', 'employes');
        $this->set('sub_module_title', 'Karyawan');
        $this->set(compact(
            'employes', 'employe_positions', 'branches'
        ));
    }

    function employe_add(){
        $this->loadModel('Employe');
        $this->set('sub_module_title', 'Tambah Karyawan');
        $this->doEmploye();
    }

    function employe_edit($id){
        $this->loadModel('Employe');
        $this->set('sub_module_title', 'Rubah Karyawan');
        $Employe = $this->Employe->getData('first', array(
            'conditions' => array(
                'Employe.id' => $id
            ),
        ), true, array(
            'status' => 'all',
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
                    $id = $this->Employe->id;
                    $this->params['old_data'] = $data_local;
                    $this->params['data'] = $data;

                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Karyawan'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s Karyawan #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
                    $this->redirect(array(
                        'controller' => 'users',
                        'action' => 'employes'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Karyawan'), $msg), 'error');
                    $this->Log->logActivity( sprintf(__('Gagal %s Karyawan #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Karyawan'), $msg), 'error');
            }
        }else{
            
            if($id && $data_local){
                $this->request->data = $data_local;
            }
        }

        $employe_positions = $this->Employe->EmployePosition->getData('list', array(
            'fields' => array(
                'EmployePosition.id', 'EmployePosition.name'
            )
        ));
        $branches = $this->GroupBranch->Branch->getData('list');

        $this->set('active_menu', 'employes');
        $this->set(compact(
            'employe_positions', 'branches'
        ));
        $this->render('employe_form');
    }

    function employe_toggle($id){
        $this->loadModel('Employe');
        $locale = $this->Employe->getData('first', array(
            'conditions' => array(
                'Employe.id' => $id
            ),
        ), true, array(
            'status' => 'all',
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
                $this->Log->logActivity( sprintf(__('Sukses merubah status karyawan ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal merubah status karyawan ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Karyawan tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    function action_modules(){
            $this->loadModel('BranchModule');
            $options = array(
                'order' => array(
                    'BranchModule.id' => 'ASC',
                ),
            );

            if(!empty($this->params['named'])){
                $refine = $this->params['named'];

                if(!empty($refine['name'])){
                    $name = urldecode($refine['name']);
                    $this->request->data['BranchModule']['name'] = $name;
                    $options['conditions']['BranchModule.name LIKE '] = '%'.$name.'%';
                }
                if(!empty($refine['parent'])){
                    $parent = urldecode($refine['parent']);
                    $this->request->data['BranchModule']['parent_id'] = $parent;
                    $options['conditions']['BranchModule.parent_id'] = $parent;
                }
            }

            $this->paginate = $this->BranchModule->getData('paginate', $options);
            $action_modules = $this->paginate('BranchModule');

            $parent_group = $this->BranchModule->getParentModule();

            $this->set('active_menu', 'action_modules');
            $this->set('parent_group', $parent_group);
            $this->set('sub_module_title', 'Action Module');
            $this->set('action_modules', $action_modules);
    }

    function action_module_add(){
        $this->loadModel('BranchModule');
        $this->set('sub_module_title', 'Tambah Action Module');
        $this->doBranchModule();
    }

    function action_module_edit($id){
        $this->loadModel('BranchModule');
        $this->set('sub_module_title', 'Rubah Action Module');
        $branch = $this->BranchModule->getData('first', array(
            'conditions' => array(
                'BranchModule.id' => $id
            )
        ));

        if(!empty($branch)){
            $this->doBranchModule($id, $branch);
        }else{
            $this->MkCommon->setCustomFlash(__('Action Module tidak ditemukan'), 'error');  
            $this->redirect(array(
                'controller' => 'users',
                'action' => 'action_modules'
            ));
        }
    }

    function doBranchModule($id = false, $data_local = false){
        if(!empty($this->request->data)){
            $data = $this->request->data;

            if($id && $data_local){
                $this->BranchModule->id = $id;
                $msg = 'merubah';
            }else{
                $this->BranchModule->create();
                $msg = 'menambah';
            }

            $data['BranchModule']['parent_id'] = !empty($data['BranchModule']['parent_id']) ? $data['BranchModule']['parent_id'] : 0; 

            $this->BranchModule->set($data);

            if($this->BranchModule->validates($data)){
                if($this->BranchModule->save($data)){
                    $id = $this->BranchModule->id;

                    $this->params['old_data'] = $data_local;
                    $this->params['data'] = $data;

                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s Action Module'), $msg), 'success');
                    $this->Log->logActivity( sprintf(__('Sukses %s Action Module #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
                    $this->redirect(array(
                        'controller' => 'users',
                        'action' => 'action_modules'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Action Module'), $msg), 'error'); 
                    $this->Log->logActivity( sprintf(__('Gagal %s Action Module #%s'), $msg, $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id );
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Action Module'), $msg), 'error');
            }
        } else if($id && $data_local){
            $this->request->data = $data_local;
        }

        $parent_group = $this->BranchModule->getParentModule();

        $this->loadModel('BranchParentModule');
        $parent_modules = $this->BranchParentModule->getData('list');

        $this->set('active_menu', 'action_modules');
        $this->set('parent_group', $parent_group);
        $this->set('parent_modules', $parent_modules);
        $this->render('action_module_form');
    }

    function action_module_toggle($id){
        $this->loadModel('BranchModule');
        $locale = $this->BranchModule->getData('first', array(
            'conditions' => array(
                'BranchModule.id' => $id
            )
        ));

        if($locale){
            $value = true;
            if($locale['BranchModule']['status']){
                $value = false;
            }

            $this->BranchModule->id = $id;
            $this->BranchModule->set('status', 0);

            if($this->BranchModule->save()){
                $this->MkCommon->setCustomFlash(__('Sukses menghapus data action module.'), 'success');
                $this->Log->logActivity( sprintf(__('Sukses menghapus data action module ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal menghapus data action module.'), 'error');
                $this->Log->logActivity( sprintf(__('Gagal menghapus data action module ID #%s.'), $id), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id ); 
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Action Module tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }

    function authorization_privilage($group_id){
        $this->loadModel('Group');
        $this->loadModel('BranchParentModule');

        $parent_modules = $this->BranchParentModule->getData('all');

        if(!empty($parent_modules)){
            $group = $this->Group->getData('first', array(
                'conditions' => array(
                    'Group.id' => $group_id
                )
            ));

            if(!empty($group)){
                $this->loadModel('BranchModule');

                $GroupBranches = $this->GroupBranch->find('all', array(
                    'conditions' => array(
                        'GroupBranch.group_id' => $group_id,
                    ),
                    'contain' => array(
                        'BranchActionModule',
                        'Branch'
                    )
                ));

                if(!empty($GroupBranches)){
                    foreach ($GroupBranches as $key => $value_k) {
                        if(!empty($value_k['BranchActionModule'])){
                            $data_result_auth = array();
                            foreach ($value_k['BranchActionModule'] as $key_k => $value) {
                                $data_result_auth[$value['branch_module_id']] = $value['is_allow'];
                            }
                            
                            $GroupBranches[$key]['BranchActionModule'] = $data_result_auth;
                        }
                    }
                }

                /*supporting data*/
                $branches = $this->GroupBranch->Branch->getData('list');

                foreach ($parent_modules as $key => $value) {
                    $parent_modules[$key]['child'] = $this->BranchModule->getData('all', array(
                        'conditions' => array(
                            'BranchModule.branch_parent_module_id' => $value['BranchParentModule']['id'],
                            'BranchModule.status' => 1,
                            'BranchModule.parent_id' => 0
                        ),
                        'contain' => array(
                            'BranchChild' => array(
                                'conditions' => array(
                                    'BranchChild.status' => 1
                                ),
                                'order'=> array(
                                    'BranchChild.order' => 'ASC'
                                ),
                            )
                        ),
                        'order' => array(
                            'BranchModule.order' => 'ASC'
                        )
                    ));
                }

                $branch_modules = $parent_modules;

                // debug($branch_modules);die();

                $sub_module_title = sprintf('Otorisasi Group %s', $group['Group']['name']);
                $this->set('active_menu', 'groups');
                $this->set(compact(
                    'branches', 'sub_module_title', 'group_id', 
                    'GroupBranches', 'branch_modules'
                ));
                /*End supporting data*/
            }else{
                $this->redirect($this->referer());
            }
        }
    }

    function change_branch(){
        if(!empty($this->request->data['GroupBranch']['branch_id'])){
            $this->Session->write('user_branch', $this->request->data['GroupBranch']['branch_id']);
        }

        $this->redirect($this->referer());
    }
}
?>