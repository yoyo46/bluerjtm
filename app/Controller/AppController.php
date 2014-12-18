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
		'MkCommon', 'Auth', 'Acl', 'Session'
	);

	var $helpers = array(
		'Common', 'Html'
	);

	function beforeFilter() {
	    //Configure AuthComponent
		Configure::write('__Site.config_currency_code', 'IDR ');
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

	    $logged_in = $this->MkCommon->loggedIn();
	    $this->user_id = false;
	    $GroupId = false;
	    $User = array();
	    if($logged_in){
			$this->user_id = $this->Auth->user('id');
			$GroupId = $this->Auth->user('group_id');
			$User = $this->Auth->user();
		}

	    $this->set(compact('logged_in', 'GroupId', 'User'));
	}

	function isAuthorized($user) {
	    // return false;
	    return $this->Auth->loggedIn();
	}
}
