<?php
class Siup extends AppModel {
    var $name = 'Siup';
    var $validate = array(
        'truck_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Truk harap diisi'
            ),
        ),
        'no_pol' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Truk harap diisi'
            ),
        ),
        'tgl_siup' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tanggal Ijin Usaha harap diisi'
            ),
        ),
        'price' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Biaya Ijin Usaha harap diisi'
            ),
        ),
        'from_date' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl Berakhir Ijin Usaha harap diisi pada data Truk'
            ),
        ),
        'to_date' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl Berakhir Ijin Usaha harap diisi pada data Truk'
            ),
        ),
    );

    var $belongsTo = array(
        'Truck' => array(
            'className' => 'Truck',
            'foreignKey' => 'truck_id',
        )
    );

    function getData( $find, $options = false, $is_merge = true, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:'active';
        $default_options = array(
            'conditions'=> array(
                'Siup.branch_id' => Configure::read('__Site.config_branch_id'),
            ),
            'order'=> array(
                'Siup.rejected' => 'ASC',
                'Siup.paid' => 'ASC',
                'Siup.tgl_siup' => 'DESC',
            ),
            'contain' => array(
                'Truck'
            ),
            'fields' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'all':
                $default_options['conditions']['Siup.status'] = array( 0, 1 );
                break;

            case 'non-active':
                $default_options['conditions']['Siup.status'] = 0;
                break;
            
            default:
                $default_options['conditions']['Siup.status'] = 1;
                break;
        }

        if(!empty($options) && $is_merge){
            if(!empty($options['conditions'])){
                $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
            }
            if(!empty($options['order'])){
                $default_options['order'] = $options['order'];
            }
            if( isset($options['contain']) && empty($options['contain']) ) {
                $default_options['contain'] = false;
            } else if(!empty($options['contain'])){
                $default_options['contain'] = array_merge($default_options['contain'], $options['contain']);
            }
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
            }
            if(!empty($options['limit'])){
                $default_options['limit'] = $options['limit'];
            }
            if(!empty($options['group'])){
                $default_options['group'] = $options['group'];
            }
        } else {
            $default_options = $options;
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