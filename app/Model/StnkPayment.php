<?php
class StnkPayment extends AppModel {
	var $name = 'StnkPayment';
	var $validate = array(
        'stnk_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No. Pol Truk harap dipilih'
            ),
        ),
        'user_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Anda tidak memiliki otoritas pada halaman ini'
            ),
        ),
        'stnk_payment_date' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl dibayar harap dipilih'
            ),
        ),
        'coa_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Account Kas/Bank harap dipilih'
            ),
        ),
	);

	var $belongsTo = array(
		'Stnk' => array(
			'className' => 'Stnk',
			'foreignKey' => 'stnk_id',
		),
        'Coa' => array(
            'className' => 'Coa',
            'foreignKey' => 'coa_id',
        ),
	);

    function getData( $find, $options = false, $is_merge = true, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:'active';
        $default_options = array(
            'conditions'=> array(
                'StnkPayment.branch_id' => Configure::read('__Site.config_branch_id'),
            ),
            'order'=> array(
                'StnkPayment.id' => 'DESC',
            ),
            'contain' => array(
                'Stnk'
            ),
            'fields' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'all':
                $default_options['conditions']['StnkPayment.status'] = array( 0, 1 );
                break;

            case 'non-active':
                $default_options['conditions']['StnkPayment.status'] = 0;
                break;
            
            default:
                $default_options['conditions']['StnkPayment.status'] = 1;
                break;
        }

        if($is_merge){
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
        }else{
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
        $dateFrom = !empty($data['named']['DateFrom'])?$data['named']['DateFrom']:false;
        $dateTo = !empty($data['named']['DateTo'])?$data['named']['DateTo']:false;
        $nopol = !empty($data['named']['nopol'])?$data['named']['nopol']:false;
        $type = !empty($data['named']['type'])?$data['named']['type']:1;
        $noref = !empty($data['named']['noref'])?$data['named']['noref']:false;

        if( !empty($dateFrom) || !empty($dateTo) ) {
            if( !empty($dateFrom) ) {
                $default_options['conditions']['DATE_FORMAT(StnkPayment.stnk_payment_date, \'%Y-%m-%d\') >='] = $dateFrom;
            }

            if( !empty($dateTo) ) {
                $default_options['conditions']['DATE_FORMAT(StnkPayment.stnk_payment_date, \'%Y-%m-%d\') <='] = $dateTo;
            }
        }
        if(!empty($nopol)){
            if( $type == 2 ) {
                $conditionsNopol = array(
                    'Truck.id' => $nopol,
                );
            } else {
                $conditionsNopol = array(
                    'Truck.nopol LIKE' => '%'.$nopol.'%',
                );
            }

            $truckSearch = $this->Stnk->Truck->getData('list', array(
                'conditions' => $conditionsNopol,
                'fields' => array(
                    'Truck.id', 'Truck.id',
                ),
            ), true, array(
                'status' => 'all',
                'branch' => false,
            ));

            $default_options['conditions']['Stnk.truck_id'] = $truckSearch;
        }
        if(!empty($noref)){
            $default_options['conditions']['LPAD(StnkPayment.id, 6, 0) LIKE'] = '%'.$noref.'%';
        }
        
        return $default_options;
    }
}
?>