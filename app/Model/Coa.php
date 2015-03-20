<?php
class Coa extends AppModel {
    public $actsAs = array('Tree');
	var $name = 'Coa';
	var $validate = array(
        // 'code' => array(
        //     'notempty' => array(
        //         'rule' => array('notempty'),
        //         'message' => 'Kode COA harap diisi'
        //     ),
        // ),
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
                'message' => 'Nama COA harap diisi'
			),
		)
	);

    var $belongsTo = array(
        'ParentCoa' => array(
            'className' => 'Coa',
            'foreignKey' => 'parent_id',
            'conditions' => array(
                'ParentCoa.status' => 1,
            ),
            'fields' => '',
            'order' => ''
        )
    );

    var $hasMany = array(
        'ChildCoa' => array(
            'className' => 'AdviceCategory',
            'foreignKey' => 'parent_id',
            'dependent' => false,
            'conditions' => array(
                'ChildCoa.status' => 1,
            ),
            'fields' => '',
            'order' => array(
                'ChildCoa.order' => 'ASC'
            ),
        ),
    );

    function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->virtualFields['coa_name'] = sprintf('CASE WHEN %s.code = \'\' THEN %s.name ELSE CONCAT(%s.code, \' - \', %s.name) END', $this->alias, $this->alias, $this->alias, $this->alias);
    }

	function getData($find, $options = false){
        $default_options = array(
            'conditions'=> array(
                'Coa.status' => 1,
            ),
            'order'=> array(
                'Coa.code' => 'ASC',
                'Coa.id' => 'ASC',
            ),
            'contain' => array(),
            'fields' => array(),
        );

        if(!empty($options)){
            if(!empty($options['conditions'])){
                $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
            }
            if(!empty($options['order'])){
                $default_options['order'] = array_merge($default_options['order'], $options['order']);
            }
            if(!empty($options['contain'])){
                $default_options['contain'] = array_merge($default_options['contain'], $options['contain']);
            }
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
            }
            if(!empty($options['limit'])){
                $default_options['limit'] = $options['limit'];
            }
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }
}
?>