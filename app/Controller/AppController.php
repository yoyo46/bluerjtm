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
		'MkCommon', 'Auth', 'Acl', 'Session', 'RequestHandler'
	);

	var $helpers = array(
		'Common', 'Html'
	);

	var $uses = array(
		'Log'
	);

	function beforeFilter() {
	    //Configure AuthComponent

		Configure::write('__Site.profile_photo_folder', 'users');
		Configure::write('__Site.laka_photo_folder', 'lakas');
		Configure::write('__Site.truck_photo_folder', 'trucks');

		Configure::write('__Site.config_currency_code', 'IDR ');
		Configure::write('__Site.config_pagination', 20);
		Configure::write('__Site.cache_view_path', '/images/view');
		Configure::write('__Site.upload_path', APP.'Uploads');

		Configure::write('__Site.profile_photo_folder', 'users');

		Configure::write('__Site.fullsize', 'fullsize');
		Configure::write('__Site.max_image_size', 5241090);
		Configure::write('__Site.max_image_width', 1000);
		Configure::write('__Site.max_image_height', 667);
		Configure::write('__Site.allowed_ext', array('jpg', 'jpeg', 'png', 'gif'));

		$changePhoto = 'view';
		Configure::write('__Site.thumbnail_display_view_path', sprintf(APP.'webroot'.DS.'images'.DS.'%s', $changePhoto));
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

	    $logged_in = $this->MkCommon->loggedIn();
	    $this->user_id = false;
	    $GroupId = false;
	    $User = array();
	    if($logged_in){
			$this->user_id = $this->Auth->user('id');
			$GroupId = $this->Auth->user('group_id');
			$this->user_data = $User = $this->Auth->user();
		}

	    $this->set(compact('logged_in', 'GroupId', 'User'));
	}

	function isAuthorized($user) {
	    // return false;
	    return $this->Auth->loggedIn();
	}
}
