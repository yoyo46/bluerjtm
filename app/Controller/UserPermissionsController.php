<?php
App::uses('AppController', 'Controller');
class UserPermissionsController extends AppController {
    public $helpers = array('Tree');
    public $Permission = null;
    public $uses = array('Module');

    public function  beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('index', 'get_children', 'edit', 'toggle', 'sync');

	   $this->Permission = ClassRegistry::init('Permission');
    }

    private function __acosList(){
        $results = $this->Acl->Aco->find('all',
            array(
                'order' => array('lft' => 'ASC'),
                'recursive' => -1,
                'fields' => array('alias', 'id', 'lft', 'rght', 'parent_id')
            )
        );

        $this->__acos_details($results);
        return $results;
    }

    public function admin_index(){
        $this->set('permissions', true);
        $this->set('results', $this->__acosList());
    }

    public function index(){
        $this->loadModel('Group');
        $groups = $this->Group->getData('list');

        $group_id = !empty($this->request->data['Group']['group_id'])?$this->request->data['Group']['group_id']:0;
        $conditions['ModuleAction.group_id'] = $group_id;
        $modules = $this->Module->find('all', array(
            'conditions'=> array(
                'Module.status'=> 1, 
            ),
            'order' => array(
                'Module.order' => 'ASC',
                'Module.id' => 'ASC',
            ),
            'contain' => array(
                'ModuleAction' => array(
                    'conditions' => $conditions,
                )
            ),
        ));
        $layout_css = array(
            '/css/acl/treeview'
        );
        $layout_js = array(
            '/js/acl/jquery.cookie',
            '/js/acl/treeview',
            '/js/acl/acos',
            '/js/bootstrap',
        );

        $this->set(compact(
            'groups', 'modules', 'group_id',
            'layout_js', 'layout_css'
        ));
    }

    public function sync(){
        $this->layout = 'ajax';

	    Configure::write('debug', 1);

        App::uses('AclExtras', 'Lib');
        $acl = new AclExtras();
        $acl->aco_sync();

        $permissions = ClassRegistry::init('Permission');
        $checkAdminPerm = $permissions->find('count', array('conditions'=>array('aro_id'=>1, 'aco_id'=>1)));
        if($checkAdminPerm <= 0){
           $this->loadModel('Group');
            $group = $this->Group;
            $group->id = 1;
            $this->Acl->allow($group, 'controllers');
        }
        $this->set('results', $this->__acosList());
    }

    public function edit($acoId) {
        $this->layout = "ajax";
        $acoPath = $this->Acl->Aco->getPath($acoId);

        if (!$acoPath) {
            return;
        }

        $aros = array();

        $this->loadModel('Group');

        foreach ($this->Group->getData('all') as $role) {
            $hasAny = array(
                'aco_id' => $acoId,
                'aro_id' => $role['Group']['id'],
                '_create' => 1,
                '_read' => 1,
                '_update' => 1,
                '_delete' => 1
            );
            $aros[$role['Group']['name']] = array(
                'id' => $role['Group']['id'],
                'allowed' => (int)$this->Permission->hasAny($hasAny)
            );
        }

        $results = $this->Acl->Aco->find('all',
            array(
                'order' => array('lft' => 'ASC'),
                'recursive' => -1,
                'fields' => array('alias', 'id', 'lft', 'rght', 'parent_id')
            )
        );

        $this->__acos_details($results);

        $this->set('acoPath', $acoPath);
        $this->set('aros', $aros);
    }

    public function toggle($acoId, $aroId) {
        $this->layout = "ajax";
        if ($aroId != 1) {
            $this->loadModel('Permission');

            $conditions = array(
                'Permission.aco_id' => $acoId,
                'Permission.aro_id' => $aroId,
            );

            if ($this->Permission->hasAny($conditions)) {
                $data = $this->Permission->find('first', array('conditions' => $conditions));

               if ($data['Permission']['_create'] == 1 &&
                    $data['Permission']['_read'] == 1 &&
                    $data['Permission']['_update'] == 1 &&
                    $data['Permission']['_delete'] == 1) {
                    // dari 1 ke 0
                    $data['Permission']['_create'] = 0;
                    $data['Permission']['_read'] = 0;
                    $data['Permission']['_update'] = 0;
                    $data['Permission']['_delete'] = 0;
                    $allowed = 0;
                } else {
                    // dari 0 ke 1
                    $data['Permission']['_create'] = 1;
                    $data['Permission']['_read'] = 1;
                    $data['Permission']['_update'] = 1;
                    $data['Permission']['_delete'] = 1;
                    $allowed = 1;
                }
            } else {
                // buat - CRUD dengan 1
                $data['Permission']['aco_id'] = $acoId;
                $data['Permission']['aro_id'] = $aroId;
                $data['Permission']['_create'] = 1;
                $data['Permission']['_read'] = 1;
                $data['Permission']['_update'] = 1;
                $data['Permission']['_delete'] = 1;
                $allowed = 1;
            }

            $this->Permission->save($data);
            $this->set('allowed', $allowed);
        } else {
            $this->set('allowed', 1);
        }
    }

    private function __acos_details($results) {
        $list = $acosYaml = array();
        App::import('Vendor', 'Spyc');
        foreach ($results as $aco) {
            $list[$aco['Aco']['id']] = $aco['Aco'];

            if (!$aco['Aco']['parent_id']) {
                if (CakePlugin::loaded($aco['Aco']['alias'])) {
                    $ppath = CakePlugin::path($aco['Aco']['alias']);
                    $isField = strpos($ppath, DS . 'Fields' . DS);
                    $isTheme = strpos($ppath, DS . 'Themed' . DS);

                    if ($isField) {
                        $m = array();
                        $m['yaml'] = Spyc::YAMLLoad("{$ppath}{$aco['Aco']['alias']}.yaml");
                    } else {
                        $m = Configure::read('Modules.' . $aco['Aco']['alias']);
                    }

                    if ($isField) {
                        $list[$aco['Aco']['id']]['name'] = __d('locale', 'Field: %s', $m['yaml']['name']);
                    } elseif ($isTheme) {
                        $list[$aco['Aco']['id']]['name'] = __d('locale', 'Theme: %s', $m['yaml']['name']);
                    } else {
                        $list[$aco['Aco']['id']]['name'] = __d('locale', 'Module: %s', $m['yaml']['name']);
                    }

                    $list[$aco['Aco']['id']]['description'] = $m['yaml']['description'];

                    if (file_exists("{$ppath}acos.yaml")) {
                        $acosYaml[$aco['Aco']['id']] = Spyc::YAMLLoad("{$ppath}acos.yaml");
                    }
                } else {
                    $list[$aco['Aco']['id']]['name'] = $aco['Aco']['alias'];
                    $list[$aco['Aco']['id']]['description'] = '';
                }
            } else {
                if (isset($acosYaml[$aco['Aco']['parent_id']])) {
                    $yaml = $acosYaml[$aco['Aco']['parent_id']];

                    $list[$aco['Aco']['id']]['name'] = isset($yaml[$aco['Aco']['alias']]['name']) ? $yaml[$aco['Aco']['alias']]['name'] : $aco['Aco']['alias'];
                    $list[$aco['Aco']['id']]['description'] = isset($yaml[$aco['Aco']['alias']]['description']) ? $yaml[$aco['Aco']['alias']]['description'] : '';
                } elseif (isset($list[$aco['Aco']['parent_id']])) {
                    $controller = $list[$aco['Aco']['parent_id']];
                    $yaml = isset($acosYaml[$controller['parent_id']]) ? $acosYaml[$controller['parent_id']] : array();

                    $list[$aco['Aco']['id']]['name'] = isset($yaml[$controller['alias']]['actions'][$aco['Aco']['alias']]['name']) ? $yaml[$controller['alias']]['actions'][$aco['Aco']['alias']]['name']: $aco['Aco']['alias'];
                    $list[$aco['Aco']['id']]['description'] = isset($yaml[$controller['alias']]['actions'][$aco['Aco']['alias']]['description']) ? $yaml[$controller['alias']]['actions'][$aco['Aco']['alias']]['description'] : '';
                }
            }

            $this->set('acos_details', $list);
        }
    }

    function generate_module( $module_id, $group_id, $action ) {
        $isAjax = $this->RequestHandler->isAjax();
        $moduleAction = $this->Module->ModuleAction->find('first', array(
            'conditions' => array(
                'ModuleAction.module_id' => $module_id,
                'ModuleAction.group_id' => $group_id,
                'ModuleAction.action' => $action,
            )
        ));

        $module = $this->Module->find('first', array(
            'conditions' => array(
                'Module.id' => $module_id,
            )
        ));

        if( !empty($module) ) {
            $idName = $module['Module']['function'];
        } else {
            $idName = false;
        }

        if( !empty($moduleAction) ) {
            $this->Module->ModuleAction->delete( $moduleAction['ModuleAction']['id'] );
            $fa = 'fa fa-times';
        } else {
            $data['ModuleAction']['group_id'] = $group_id;
            $data['ModuleAction']['module_id'] = $module_id;
            $data['ModuleAction']['action'] = $action;
            $this->Module->ModuleAction->save($data);
            $fa = 'fa fa-check';
        }

        if( empty($isAjax) ) {
            $this->redirect(array(
                'action' => 'index',
                '#' => $idName,
            ));
        } else {
            echo $fa;
            die();
        }
    }
}
?>
