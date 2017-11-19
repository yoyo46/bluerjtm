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

    function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->virtualFields['status_paid'] = 'CASE WHEN Siup.paid = \'none\' THEN 0 WHEN Siup.paid = \'half\' THEN 1 ELSE 2 END';
    }

    function getData( $find, $options = false, $is_merge = true, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:'active';
        $default_options = array(
            'conditions'=> array(
                'Siup.branch_id' => Configure::read('__Site.config_branch_id'),
                'Siup.no_pol <>' => '',
            ),
            'order'=> array(
                'Siup.rejected' => 'ASC',
                'Siup.status_paid' => 'ASC',
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

    public function _callRefineParams( $data = '', $default_options = false ) {
        $nopol = !empty($data['named']['nopol'])?$data['named']['nopol']:false;
        $type = !empty($data['named']['type'])?$data['named']['type']:1;
        $driver = !empty($data['named']['driver'])?$data['named']['driver']:false;

        if(!empty($nopol)){
            if( $type == 2 ) {
                $default_options['conditions']['Truck.id'] = $nopol;
            } else {
                $default_options['conditions']['Truck.nopol LIKE'] = '%'.$nopol.'%';
            }
        }
        if(!empty($driver)){
            $drivers = $this->Truck->Driver->getData('list', array(
                'conditions' => array(
                    'Driver.name LIKE' => '%'.$driver.'%',
                ),
                'fields' => array(
                    'Driver.id', 'Driver.id'
                ),
            ), array(
                'branch' => false,
            ));
           $default_options['conditions']['Truck.driver_id'] = $drivers;
        }
        
        return $default_options;
    }

    function getMerge($data, $id){
        if(empty($data['Siup'])){
            $data_merge = $this->find('first', array(
                'conditions' => array(
                    'Siup.id' => $id
                ),
            ));

            if(!empty($data_merge)){
                $data = array_merge($data, $data_merge);
            }
        }

        return $data;
    }
}
?>