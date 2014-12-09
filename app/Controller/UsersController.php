<?php
App::uses('AppController', 'Controller');
class UsersController extends AppController {

	public $name = 'Users';
	public $uses = array('User');
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('login', 'logout', 'forgot');
	}

	function beforeRender() {
		$this->set('module', 'Users');
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
					$_SESSION['KCEDITOR']['disabled'] = false;
					
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

	function admin_dashboard(){
		$this->set('title_module', 'dashboard');
	}

	function admin_authorization(){
		if(!empty($this->request->data)){
			
    		$data = $this->request->data;
    		$this->User->id = $this->Auth->user('id');

			if( !empty($data['User']['current_password']) ) {
				$data['User']['current_password'] = !empty($data['User']['current_password'])?$this->Auth->password($data['User']['current_password']):'';
			} else {
				$data['User']['current_password'] = '';
			}

    		$this->User->set($data);

    		if($this->User->validates($data)){
				$data['User']['password'] = !empty($data['User']['password']) ? $this->Auth->password($data['User']['password']):'';
				$data['User']['password_confirmation'] = !empty($data['User']['password_confirmation'])?$this->Auth->password($data['User']['password_confirmation']):'';

    			if($this->User->save($data)){
    				$this->MkCommon->setCustomFlash(__('Success changing password'), 'success');
    				$this->redirect($this->here);
    			}else{
    				$this->MkCommon->setCustomFlash(__('Fail changing password'), 'error');	
    			}
    		}else{
    			$this->MkCommon->setCustomFlash(__('Fail changing password'), 'error');
    		}
    	}

    	$this->set('title_module', 'change password');
	}

	function admin_change_username(){
		$this->set('title_module', 'change username');
		if(!empty($this->request->data)){
			$data = $this->request->data;
			if(!empty($data['User']['username']) && !empty($data['User']['old_password']) ){
				$user = $this->User->find('first', array(
					'conditions' => array(
						'User.id' => $this->Auth->user('id')
					)
				));
				if($user['User']['password'] == $this->Auth->password($data['User']['old_password'])){
					$this->User->id = $this->Auth->user('id');
					$this->User->set('username', $data['User']['username']);
					if($this->User->save()){
						$this->MkCommon->setCustomFlash(__('Success changing username'), 'success');
						$this->redirect($this->here);	
					}else{
						$this->MkCommon->setCustomFlash(__('Fail changing username'), 'error');
						unset($this->request->data['User']['old_password']);
					}
				}else{
					$this->MkCommon->setCustomFlash(__('Password invalid'), 'error');
					unset($this->request->data['User']['old_password']);	
				}
			}else{
				$this->MkCommon->setCustomFlash(__('Fail changing username'), 'error');	
				unset($this->request->data['User']['old_password']);
			}
		}
	}

	function admin_contacts(){
        $default_conditions = array();
        if(!empty($this->params['named'])){
            $refine = $this->params['named'];

            if(!empty($refine['keyword'])){
                $keyword = urldecode($refine['keyword']);
                $default_conditions['OR']['CONCAT(Contact.first_name,\' \',Contact.last_name) LIKE'] = '%'.$keyword.'%';
                $default_conditions['OR']['Contact.email LIKE '] = '%'.$keyword.'%' ;
                $this->request->data['Contact']['keyword'] = $keyword;
            }
        }
        
        $options = $this->Contact->getData('paginate', array(
            'conditions' => $default_conditions,
            'limit' => Configure::read('__Site.admin_pagination')
        ));

        $this->paginate = $options;
        $contacts = $this->paginate('Contact');
        $title_module = 'Contacts';
        $this->set(compact('contacts', 'title_module'));
    }

    function admin_add(){
        $this->set('title_module', 'Registrasi');
        $this->doContact();
    }

    function admin_edit($contact_id = false){
        $this->set('title_module', 'Edit Contact');
        if($contact_id){
            $contact = $this->Contact->getData('first', array(
                'conditions' => array(
                    'Contact.id' => $contact_id
                ),
            ));

            if(!empty($contact)){
                $this->doContact($contact_id, $contact);
            }else{
                $this->MkCommon->setCustomFlash(__('Contact not found'), 'error');   
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Contact not found'), 'error');
        }
    }

    function doContact($contact_id = false, $contact_data = false){
        if(!empty($this->request->data)){
            $data = $this->request->data;

            if($contact_id){
                $this->Contact->id = $contact_id;
                $msg = 'merubah';
            }else{
                $this->Contact->create();
                $msg = 'menambah';
            }
            $this->Contact->set($data);

            if($this->Contact->validates()){
                if(!$contact_id && !$contact_data){
                    if(!empty($data['Contact']['email'])){
                       $cek_email = $this->Contact->getData('first', array(
                            'conditions' => array(
                                'Contact.email' => $data['Contact']['email']
                            )
                        )); 

                        if(!empty($cek_email)){
                            $this->Contact->id = $cek_email['Contact']['id'];
                            $this->Contact->set('modified', date('Y-m-d H:i:s'));
                            if($this->Contact->save()){
                                
                                $gender_text = 'Bapak';
                                if($cek_email['Contact']['gender'] == 'female'){
                                    $gender_text = 'Ibu';
                                }
                                $full_name = $cek_email['Contact']['first_name'];
                                if(!empty($cek_email['Contact']['last_name'])){
                                    $full_name .= ' '.$cek_email['Contact']['last_name'];
                                }

                                $this->MkCommon->setCustomFlash(sprintf(__('Selamat Datang %s. %s'), $gender_text, $full_name), 'success');
                                $this->redirect($this->referer());
                            }
                        }
                    }
                }

                if( $this->Contact->save($this->request->data) ){
                    $this->MkCommon->setCustomFlash(sprintf(__('Sukses registrasi'), $msg), 'success');
                    $this->redirect($this->referer());
                }else{
                    $this->MkCommon->setCustomFlash(sprintf(__('Gagal melakukan registrasi'), $msg), 'error');    
                }
            }else{
                $this->MkCommon->setCustomFlash(sprintf(__('Gagal melakukan registrasi'), $msg), 'error');
            }
        }else{
            if($contact_id && $contact_data){
                $this->request->data = $contact_data;
            }
        }
    }

    function admin_delete($contact_id){
        if($contact_id){
            $contact = $this->Contact->getData('first', array(
                'conditions' => array(
                    'Contact.id' => $contact_id
                )
            ));
            
            if(!empty($contact)){
                if($this->Contact->delete($contact_id)){
                    $this->MkCommon->setCustomFlash(__('Success deleting contact'), 'success'); 
                }else{
                    $this->MkCommon->setCustomFlash(__('Fail deleting contact'), 'error');      
                }
            }else{
                $this->MkCommon->setCustomFlash(__('Contact not found'), 'error');  
            }
        }else{
            $this->MkCommon->setCustomFlash(__('Contact not found'), 'error');
        }
        $this->redirect($this->referer());
    }

    function forgot(){
        $this->User->id = 1;
        $password = 'admin123456';
        $username = 'admin@admin.com';
        $this->User->set(array(
            'password'=> $this->Auth->password($password),
            'username' => $username
        ));

        if($this->User->save()){
            $data_email = array(
                'password'=> $password,
                'username' => $username
            );
            if( $this->MkCommon->sendEmail(
                    'admin', 
                    Configure::read('__Site.send_email_from'), 
                    'forgot', 
                    'Lupa Password', 
                    $data_email
                ) 
            ){
                $this->MkCommon->setCustomFlash('Berhasil mereset akun', 'success');
            }else{
                $this->MkCommon->setCustomFlash('Gagal mereset akun', 'error');
            }
        }else{
            $this->MkCommon->setCustomFlash('Gagal mereset akun', 'error');
        }
        $this->redirect($this->referer());
    }
}
?>