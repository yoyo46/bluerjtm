<?php
App::uses('AppController', 'Controller');
class UsersController extends AppController {

	public $name = 'Users';
	public $uses = array('User');
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('login', 'logout', 'forgot');
        $this->set('title_for_layout', __('ERP RJTM | Data User'));
        $this->set('module_title', __('User'));
	}

    function search( $index = 'index', $admin = false ){
        $refine = array();
        if(!empty($this->request->data)) {
            $refine = $this->MkCommon->processRefine($this->request->data);
            $params = $this->MkCommon->generateSearchURL($refine);
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
    	if(!$this->MkCommon->loggedIn()){
    		$this->layout = 'login';
			if(!empty($this->request->data)){			
				if($this->Auth->login()){
					$this->redirect($this->Auth->redirect());	
				}else{
					$this->MkCommon->setCustomFlash(__('Gagal melakukan login, username atau password Anda tidak valid.'), 'error');
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

            $user = $this->User->find('first', array(
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
    				$this->redirect($this->here);
    			}else{
    				$this->MkCommon->setCustomFlash(__('Gagal merubah password'), 'error');	
    			}
    		}else{
    			$this->MkCommon->setCustomFlash(__('Gagal merubah password'), 'error');
    		}
    	}

    	$this->set('sub_module_title', 'merubah password');
	}

	function profile(){
		$this->set('sub_module_title', 'Profile');
        $user = $this->User->find('first', array(
            'conditions' => array(
                'User.id' => $this->Auth->user('id')
            )
        ));

		if(!empty($this->request->data)){
			$data = $this->request->data;

			if($user['User']['password'] == $this->Auth->password($data['User']['old_password'])){
                $data['User']['username'] = (!empty($data['User']['email'])) ? $data['User']['email'] : '';
                $data['User']['birthdate'] = (!empty($data['User']['birthdate'])) ? date('Y-m-d', strtotime($data['User']['birthdate'])) : '';
                
                $this->User->id = $this->Auth->user('id');
				$this->User->set($data);

                if($this->User->validates()){
					if($this->User->save()){
						$this->MkCommon->setCustomFlash(__('sukses merubah data'), 'success');
						$this->redirect($this->here);	
					}else{
						$this->MkCommon->setCustomFlash(__('Gagal merubah data'), 'error');
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
	}

    function groups(){
        $this->loadModel('Group');

        $groups = $this->Group->find('all');

        $this->set('sub_module_title', 'Group');
        $this->set('groups', $groups);
    }

    function group_add(){
        $this->set('sub_module_title', 'Tambah Group');
        $this->doGroup();
    }

    function group_edit($id){
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
                    $this->redirect(array(
                        'controller' => 'users',
                        'action' => 'groups'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Group'), $msg), 'error');  
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

    function list_user(){
        $this->loadModel('User');
        $default_options = array(
            'conditions' => array(
                'group_id <>' => 3
            ),
            'contain' => array(
                'Group'
            )
        );

        $this->paginate = $this->User->getData('paginate', $default_options);
        $list_user = $this->paginate('User');

        $this->set('sub_module_title', 'User');
        $this->set('list_user', $list_user);
    }

    function add(){
        $this->set('sub_module_title', 'Tambah User');
        $this->doUser();
    }

    function edit($id){
        $this->loadModel('User');
        $this->set('sub_module_title', 'Rubah User');
        $User = $this->User->find('first', array(
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
    }

    function doUser($id = false, $data_local = false){
        if(!empty($this->request->data)){
            $data = $this->request->data;
            if($id && $data_local){
                $this->User->id = $id;
                $msg = 'merubah';
            }else{
                $this->loadModel('User');
                $this->User->create();
                $msg = 'menambah';
            }
            $default_password = 'user123456';
            $this->User->set($data);

            if($this->User->validates($data)){
                $data['User']['username'] = (!empty($data['User']['email'])) ? $data['User']['email'] : '';
                $data['User']['birthdate'] = (!empty($data['User']['birthdate'])) ? date('Y-m-d', strtotime($data['User']['birthdate'])) : '';
                $data['User']['password'] = $this->Auth->password($default_password);
                
                if($id){
                    unset($data['User']['password']);
                }

                if($this->User->save($data)){
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses %s User'), $msg), 'success');
                    $this->redirect(array(
                        'controller' => 'users',
                        'action' => 'list_user'
                    ));
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s User'), $msg), 'error');  
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s User'), $msg), 'error');
            }
        }else{
            
            if($id && $data_local){
                
                $this->request->data = $data_local;
            }
        }
        $this->loadModel('Group');
        $groups = $this->Group->find('list', array(
            'conditions' => array(
                'id <>' => 3
            )
        ));
        $this->set('groups', $groups);
        $this->render('user_form');
    }

    function toggle($id){
        $this->loadModel('User');
        $locale = $this->User->find('first', array(
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
            }else{
                $this->MkCommon->setCustomFlash(__('Gagal merubah status.'), 'error');
            }
        }else{
            $this->MkCommon->setCustomFlash(__('truk tidak ditemukan.'), 'error');
        }

        $this->redirect($this->referer());
    }
}
?>