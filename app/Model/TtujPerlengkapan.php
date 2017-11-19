<?php
class TtujPerlengkapan extends AppModel {
	var $name = 'TtujPerlengkapan';
	var $validate = array(
		'ttuj_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
                'message' => 'TTUJ harap dipilih.'
			),
		),
        'perlengkapan_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Perlengkapan harap dipilih.'
            ),
        ),
        'qty' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Jumlah perlengkapan harap dipilih.'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'jumlah perlengkapan harus berupa angka'
            ),
        ),
	);
    
    var $belongsTo = array(
        'Ttuj' => array(
            'className' => 'Ttuj',
            'foreignKey' => 'ttuj_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Perlengkapan' => array(
            'className' => 'Perlengkapan',
            'foreignKey' => 'perlengkapan_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
    );

	function getData($find, $options = false){
        $default_options = array(
            'conditions'=> array(
                'TtujPerlengkapan.status' => 1,
            ),
            'contain' => array(),
            'order' => array(),
        );

        if(!empty($options)){
            if(!empty($options['conditions'])){
                $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
            }
            if(!empty($options['order'])){
                $default_options['order'] = $options['order'];
            }
            if(!empty($options['contain'])){
                $default_options['contain'] = array_merge($default_options['contain'], $options['contain']);
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

    function getMerge ( $data = false, $ttuj_id = false ) {
        if( empty($data['TtujPerlengkapan']) ) {
            $default_options = array(
                'conditions' => array(
                    'TtujPerlengkapan.ttuj_id'=> $ttuj_id,
                    'TtujPerlengkapan.status'=> 1,
                ),
                'order' => array(
                    'TtujPerlengkapan.id' => 'ASC',
                ),
            );
            $ttujPerlengkapan = $this->getData('all', $default_options);
            $data['TtujPerlengkapan'] = $ttujPerlengkapan;
        }

        return $data;
    }
}
?>