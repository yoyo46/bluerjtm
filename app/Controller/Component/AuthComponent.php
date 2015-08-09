<?php
App::uses('Component', 'Controller');
App::uses('Router', 'Routing');
App::uses('Security', 'Utility');
App::uses('Debugger', 'Utility');
App::uses('Hash', 'Utility');
App::uses('CakeSession', 'Model/Datasource');
App::uses('BaseAuthorize', 'Controller/Component/Auth');
App::uses('BaseAuthenticate', 'Controller/Component/Auth');
class AuthComponent extends Component {
	// Default Global
	// public $authorize = false;
	// public $loginAction = array(
	// 	'controller' => 'users',
	// 	'action' => 'login',
	// 	'plugin' => null
	// );
	// public $logoutRedirect = null;
	// public $loginRedirect = null;

	// Custom Global
    public $userModel = 'User';
	public $authorize = array(
        'Controller',
        'Actions' => array('actionPath' => 'controllers')
    );
	public $loginAction = array(
        'controller' => 'users',
        'action' => 'login',
        'admin' => false,
        'plugin' => false
    );
    public $logoutRedirect = array(
        'controller' => 'users',
        'action' => 'login',
        'admin' => false,
        'plugin' => false
    );
    public $loginRedirect = array(
        'controller' => 'pages',
        'action' => 'dashboard',
        'admin' => false,
        'plugin' => false
    );

	const ALL = 'all';
	public $components = array('Session', 'RequestHandler');
	public $authenticate = array('Form');
	protected $_authenticateObjects = array();
	protected $_authorizeObjects = array();
	public $ajaxLogin = null;
	public $flash = array(
		'element' => 'default',
		'key' => 'auth',
		'params' => array()
	);
	public static $sessionKey = 'Auth.User';
	protected static $_user = array();
	public $loginAdminRedirect = null;
	public $authError = null;
	public $unauthorizedRedirect = true;
	public $changeEmail = null;
	public $allowedActions = array();
	public $request;
	public $response;
	protected $_methods = array();
	public function initialize(Controller $controller) {
		$this->request = $controller->request;
		$this->response = $controller->response;
		$this->_methods = $controller->methods;

		if (Configure::read('debug') > 0) {
			Debugger::checkSecurityKeys();
		}
	}
	public function startup(Controller $controller) {
		$methods = array_flip(array_map('strtolower', $controller->methods));
		$action = strtolower($controller->request->params['action']);

		$isMissingAction = (
			$controller->scaffold === false &&
			!isset($methods[$action])
		);

		if ($isMissingAction) {
			return true;
		}

		if (!$this->_setDefaults()) {
			return false;
		}

		if ($this->_isAllowed($controller)) {
			return true;
		}

		if (!$this->_getUser()) {
			return $this->_unauthenticated($controller);
		}

		if (empty($this->authorize) || $this->isAuthorized($this->user())) {
			return true;
		}

		$this->Session->setFlash($this->authError, $this->flashElement, array(), 'error');
		$controller->redirect('/');
	}
	protected function _isAllowed(Controller $controller) {
		$action = strtolower($controller->request->params['action']);
		if (in_array($action, array_map('strtolower', $this->allowedActions))) {
			return true;
		}
		return false;
	}
	protected function _unauthenticated(Controller $controller) {
		if (empty($this->_authenticateObjects)) {
			$this->constructAuthenticate();
		}
		$auth = $this->_authenticateObjects[count($this->_authenticateObjects) - 1];
		if ($auth->unauthenticated($this->request, $this->response)) {
			return false;
		}

		if ($this->_isLoginAction($controller)) {
			return true;
		}

		if (!$controller->request->is('ajax')) {
			$this->flash($this->authError);
			$this->Session->write('Auth.redirect', $controller->request->here(false));
			$controller->redirect($this->loginAction);
			return false;
		}
		if (!empty($this->ajaxLogin)) {
			$controller->viewPath = 'Elements';
			echo $controller->render($this->ajaxLogin, $this->RequestHandler->ajaxLayout);
			$this->_stop();
			return false;
		}
		$controller->redirect(null, 403);
		return false;
	}
	protected function _isLoginAction(Controller $controller) {
		$url = '';
		if (isset($controller->request->url)) {
			$url = $controller->request->url;
		}
		$url = Router::normalize($url);
		$loginAction = Router::normalize($this->loginAction);

		if ($loginAction == $url) {
			if (empty($controller->request->data)) {
				if (!$this->Session->check('Auth.redirect') && env('HTTP_REFERER')) {
					$this->Session->write('Auth.redirect', $controller->referer(null, true));
				}
			}
			return true;
		}
		return false;
	}
	protected function _unauthorized(Controller $controller) {
		if ($this->unauthorizedRedirect === false) {
			throw new ForbiddenException($this->authError);
		}

		$this->flash($this->authError);
		if ($this->unauthorizedRedirect === true) {
			$default = '/';
			if (!empty($this->loginRedirect)) {
				$default = $this->loginRedirect;
			}
			$url = $controller->referer($default, true);
		} else {
			$url = $this->unauthorizedRedirect;
		}
		$controller->redirect($url, null, true);
		return false;
	}
	protected function _setDefaults() {
		$defaults = array(
			'logoutRedirect' => $this->loginAction,
			'authError' => __d('cake', 'You are not authorized to access that location.')
		);
		foreach ($defaults as $key => $value) {
			if (!isset($this->{$key}) || $this->{$key} === true) {
				$this->{$key} = $value;
			}
		}
		return true;
	}
	public function isAuthorized($user = null, CakeRequest $request = null) {
		if (empty($user) && !$this->user()) {
			return false;
		}
		if (empty($user)) {
			$user = $this->user();
		}
		if (empty($request)) {
			$request = $this->request;
		}
		if (empty($this->_authorizeObjects)) {
			$this->constructAuthorize();
		}
		foreach ($this->_authorizeObjects as $authorizer) {
			if ($authorizer->authorize($user, $request) === true) {
				return true;
			}
		}
		return false;
	}
	public function constructAuthorize() {
		if (empty($this->authorize)) {
			return;
		}
		$this->_authorizeObjects = array();
		$config = Hash::normalize((array)$this->authorize);
		$global = array();
		if (isset($config[AuthComponent::ALL])) {
			$global = $config[AuthComponent::ALL];
			unset($config[AuthComponent::ALL]);
		}
		foreach ($config as $class => $settings) {
			list($plugin, $class) = pluginSplit($class, true);
			$className = $class . 'Authorize';
			App::uses($className, $plugin . 'Controller/Component/Auth');
			if (!class_exists($className)) {
				throw new CakeException(__d('cake_dev', 'Authorization adapter "%s" was not found.', $class));
			}
			if (!method_exists($className, 'authorize')) {
				throw new CakeException(__d('cake_dev', 'Authorization objects must implement an %s method.', 'authorize()'));
			}
			$settings = array_merge($global, (array)$settings);
			$this->_authorizeObjects[] = new $className($this->_Collection, $settings);
		}
		return $this->_authorizeObjects;
	}
	public function allow($action = null) {
		$args = func_get_args();
		if (empty($args) || $action === null) {
			$this->allowedActions = $this->_methods;
			return;
		}
		if (isset($args[0]) && is_array($args[0])) {
			$args = $args[0];
		}
		$this->allowedActions = array_merge($this->allowedActions, $args);
	}
	public function deny($action = null) {
		$args = func_get_args();
		if (empty($args) || $action === null) {
			$this->allowedActions = array();
			return;
		}
		if (isset($args[0]) && is_array($args[0])) {
			$args = $args[0];
		}
		foreach ($args as $arg) {
			$i = array_search($arg, $this->allowedActions);
			if (is_int($i)) {
				unset($this->allowedActions[$i]);
			}
		}
		$this->allowedActions = array_values($this->allowedActions);
	}
	public function mapActions($map = array()) {
		if (empty($this->_authorizeObjects)) {
			$this->constructAuthorize();
		}
		foreach ($this->_authorizeObjects as $auth) {
			$auth->mapActions($map);
		}
	}
	public function login($user = null, $verify = false) {
		$this->_setDefaults();

		if (empty($user) && !empty($this->request->data)) {
			$user = $this->identify($this->request->data, $verify);
		}
		if ($user) {
			$this->Session->write(self::$sessionKey, $user);
		}
		return $this->loggedIn();
	}
	public function logout( $activity = false ) {
		$this->_setDefaults();
		if (empty($this->_authenticateObjects)) {
			$this->constructAuthenticate();
		}
		$user = $this->user();
		foreach ($this->_authenticateObjects as $auth) {
			$auth->logout($user);
		}
		$this->Session->delete(self::$sessionKey);
		$this->Session->delete('Auth.redirect');

		switch ($activity) {
			case 'edit_email':
				$this->flash['element'] = 'success';
				$this->flash($this->changeEmail);
				break;
		}

		return Router::normalize($this->logoutRedirect);
	}
	public static function user($key = null) {
		if (!empty(self::$_user)) {
			$user = self::$_user;
		} elseif (self::$sessionKey && CakeSession::check(self::$sessionKey)) {
			$user = CakeSession::read(self::$sessionKey);
		} else {
			return null;
		}
		if ($key === null) {
			return $user;
		} else if($key === 'Auth'){
			return CakeSession::read('Auth');
		}
		return Hash::get($user, $key);
	}
	protected function _getUser() {
		$user = $this->user();
		if ($user) {
			$this->Session->delete('Auth.redirect');
			return true;
		}

		if (empty($this->_authenticateObjects)) {
			$this->constructAuthenticate();
		}
		foreach ($this->_authenticateObjects as $auth) {
			$result = $auth->getUser($this->request);
			if (!empty($result) && is_array($result)) {
				self::$_user = $result;
				return true;
			}
		}

		return false;
	}
	public function redirect( $url = null, $group_id = null ) {
		return $this->redirectUrl($url);
	}
	public function redirectUrl( $url = null, $group_id = null ) {
		if ($url !== null) {
			$redir = $url;
			$this->Session->write('Auth.redirect', $redir);
		} elseif ($this->Session->check('Auth.redirect')) {
			$redir = $this->Session->read('Auth.redirect');
			$this->Session->delete('Auth.redirect');

			if (Router::normalize($redir) == Router::normalize($this->loginAction)) {
				$redir = $this->loginRedirect;
			}
		} elseif ( $this->loginRedirect && !empty($group_id) && $group_id > 10 ) {
			$redir = $this->loginAdminRedirect;
		} elseif ($this->loginRedirect) {
			$redir = $this->loginRedirect;
		} else {
			$redir = '/';
		}
		if (is_array($redir)) {
			return Router::url($redir + array('base' => false));
		}
		return $redir;
	}

	function &getModel($name = null) {
		$model = null;
		if (!$name) {
			$name = $this->userModel;
		}
		
		$model = ClassRegistry::init($name);

		if (empty($model)) {
			trigger_error(__('Auth::getModel() - Model is not set or could not be found', true), E_USER_WARNING);
			return null;
		}

		return $model;
	}

	public function identify($data = false, $verify = false) {
		$model = $this->getModel();

		if( !empty($data['User']['username']) && !empty($data['User']['password']) ) {
			if($verify){
				$password = $data['User']['password'];
			}else{
				$password = $this->password($data['User']['password']);
			}
			
			$data = $model->getData('first', array(
				'conditions' => array(
					'OR' => array(
						'User.username' => $data['User']['username'],
						'User.email' => $data['User']['username'],
					),
					'User.password' => $password,
					'User.status' => 1,
				),
				'contain' => false,
				'order' => false,
			));

			if( !empty($data) ) {
				$user = array_merge($data, $data['User']);
				unset($user['User']);
			
				return $user;
			}
		}
		return false;
	}
	public function constructAuthenticate() {
		if (empty($this->authenticate)) {
			return;
		}
		$this->_authenticateObjects = array();
		$config = Hash::normalize((array)$this->authenticate);
		$global = array();
		if (isset($config[AuthComponent::ALL])) {
			$global = $config[AuthComponent::ALL];
			unset($config[AuthComponent::ALL]);
		}
		foreach ($config as $class => $settings) {
			list($plugin, $class) = pluginSplit($class, true);
			$className = $class . 'Authenticate';
			App::uses($className, $plugin . 'Controller/Component/Auth');
			if (!class_exists($className)) {
				throw new CakeException(__d('cake_dev', 'Authentication adapter "%s" was not found.', $class));
			}
			if (!method_exists($className, 'authenticate')) {
				throw new CakeException(__d('cake_dev', 'Authentication objects must implement an %s method.', 'authenticate()'));
			}
			$settings = array_merge($global, (array)$settings);
			$this->_authenticateObjects[] = new $className($this->_Collection, $settings);
		}
		return $this->_authenticateObjects;
	}
	public static function password($password) {
		return Security::hash($password, null, true);
	}
	public function loggedIn() {
		return (boolean)$this->user();
	}
	public function flash($message) {
		if ($message === false) {
			return;
		}
		$this->Session->setFlash($message, $this->flash['element'], $this->flash['params'], $this->flash['key']);
	}

}
