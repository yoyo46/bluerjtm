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
            'validatePrice' => array(
                'rule' => array('validatePrice'),
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

    function validatePrice($data){
        $result = true;
        if(empty($this->data['Ksu']['kekurangan_atpm'])){
            if(empty($data['price'])){
                $result = false;
            }
        }

        return $result;
    }

	function getData($find, $options = false){
        $default_options = array(
            'conditions'=> array(
                'KsuDetail.status' => 1,
            ),
            'order'=> array(
                'KsuDetail.id' => 'DESC',
            ),
            'contain' => array(),
            'fields' => array(),
            'group' => array(),
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
            if(!empty($options['group'])){
                $default_options['group'] = $options['group'];
            }
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function getMerge ( $data = false, $ksu_id = false ) {
        if( empty($data['KsuDetail']) ) {
            $default_options = array(
                'conditions' => array(
                    'KsuDetail.ksu_id'=> $ksu_id,
                ),
                'order' => array(
                    'KsuDetail.id' => 'ASC',
                ),
            );

            $ksuDetails = $this->getData('all', $default_options);
            $data['KsuDetail'] = $ksuDetails;
        }

        return $data;
    }

    function getGroupMerge ( $data = false, $ksu_id = false ) {
        if( empty($data['KsuDetail']) ) {
            $default_options = array(
                'conditions' => array(
                    'KsuDetail.ksu_id'=> $ksu_id,
                ),
                'contain' => array(
                    'Perlengkapan',
                ),
                'order' => array(
                    'KsuDetail.id' => 'ASC',
                ),
                'fields' => array(
                    'SUM(KsuDetail.qty) AS qty',
                    'SUM(KsuDetail.price) AS price',
                    'Perlengkapan.name',
                ),
                'group' => array(
                    'KsuDetail.perlengkapan_id',
                    'KsuDetail.price',
                ),
            );

            $details = $this->getData('all', $default_options);
            $data['KsuDetail'] = $details;
        }

        return $data;
    }
}
?>