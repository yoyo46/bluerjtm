<?php
class KsuDetail extends AppModel {
	var $name = 'KsuDetail';
	var $validate = array(
        'perlengkapan_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Perlengkapan harap diisi',
            ),
        ),
        // 'no_rangka' => array(
        //     'notempty' => array(
        //         'rule' => array('notempty'),
        //         'message' => 'No. Rangka harap diisi',
        //     ),
        // ),
        'qty' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Jumlah perlengkapan harap diisi',
            ),
        ),
        'price' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Harga Klaim harap diisi',
            ),
        ),
	);

    var $belongsTo = array(
        'Ksu' => array(
            'className' => 'Ksu',
            'foreignKey' => 'ksu_id',
            'conditions' => array(
                'KsuDetail.status' => 1,
            ),
        ),
        'Perlengkapan' => array(
            'className' => 'Perlengkapan',
            'foreignKey' => 'perlengkapan_id',
            'conditions' => array(
                'Perlengkapan.status' => 1,
            ),
        ),
    );

	function getData($find, $options = false){
        $default_options = array(
            'conditions'=> array(
                'KsuDetail.status' => 1,
            ),
            'order'=> array(
                'KsuDetail.created' => 'DESC',
                'KsuDetail.id' => 'DESC',
            ),
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
            if(!empty($options['limit'])){
                $default_options['limit'] = $options['limit'];
            }
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
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