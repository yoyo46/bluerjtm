<?php
class ViewRevenueQty extends AppModel {
	var $name = 'ViewRevenueQty';

    var $belongsTo = array(
        'ViewTtujQty' => array(
            'className' => 'ViewTtujQty',
            'foreignKey' => 'ttuj_id',
        ),
        'Ttuj' => array(
            'className' => 'Ttuj',
            'foreignKey' => 'ttuj_id',
        ),
        'CustomerNoType' => array(
            'className' => 'CustomerNoType',
            'foreignKey' => 'customer_id',
        ),
        'Truck' => array(
            'className' => 'Truck',
            'foreignKey' => 'truck_id',
        ),
        'Branch' => array(
            'className' => 'Branch',
            'foreignKey' => 'branch_id',
        ),
    );

    var $hasOne = array(
        'InvoiceDetail' => array(
            'className' => 'InvoiceDetail',
            'foreignKey' => 'revenue_id',
            'dependent' => true,
        ),
    );

	function getData( $find, $options = false, $is_merge = true, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:'active';
        $branch = isset($elements['branch'])?$elements['branch']:true;

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'ViewRevenueQty.id' => 'DESC',
            ),
            'contain' => array(),
            'fields' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'commit':
                $default_options['conditions']['ViewRevenueQty.transaction_status'] = array( 'posting', 'half_invoiced', 'paid' );
                break;
        }

        if( !empty($branch) ) {
            $default_options['conditions']['ViewRevenueQty.branch_id'] = Configure::read('__Site.config_branch_id');
        }

        if(!empty($options) && $is_merge){
            if(!empty($options['conditions'])){
                $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
            }
            if(!empty($options['order'])){
                $default_options['order'] = $options['order'];
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
            if(!empty($options['group'])){
                $default_options['group'] = $options['group'];
            }
            if(!empty($options['offset'])){
                $default_options['offset'] = $options['offset'];
            }
        } else if( !empty($options) ) {
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
        $sort = !empty($data['named']['sort'])?$data['named']['sort']:false;
        $dateFrom = !empty($data['named']['DateFrom'])?$data['named']['DateFrom']:false;
        $dateTo = !empty($data['named']['DateTo'])?$data['named']['DateTo']:false;
        $nopol = !empty($data['named']['nopol'])?$data['named']['nopol']:false;
        $type = !empty($data['named']['type'])?$data['named']['type']:1;
        $customer = !empty($data['named']['customer'])?$data['named']['customer']:false;
        $nodoc = !empty($data['named']['nodoc'])?$data['named']['nodoc']:false;
        $noref = !empty($data['named']['noref'])?$data['named']['noref']:false;
        $nottuj = !empty($data['named']['nottuj'])?$data['named']['nottuj']:false;
        $status = !empty($data['named']['status'])?$data['named']['status']:false;
        $fromcity = !empty($data['named']['fromcity'])?$data['named']['fromcity']:false;
        $tocity = !empty($data['named']['tocity'])?$data['named']['tocity']:false;
        $tocityDetail = !empty($data['named']['tocityDetail'])?$data['named']['tocityDetail']:false;

        if(!empty($fromcity) || !empty($tocity) || $nopol){            
            $default_options['contain'][] = 'ViewTtujQty';
        }

        if( !empty($dateFrom) || !empty($dateTo) ) {
            if( !empty($dateFrom) ) {
                $default_options['conditions']['DATE_FORMAT(ViewRevenueQty.date_revenue, \'%Y-%m-%d\') >='] = $dateFrom;
            }

            if( !empty($dateTo) ) {
                $default_options['conditions']['DATE_FORMAT(ViewRevenueQty.date_revenue, \'%Y-%m-%d\') <='] = $dateTo;
            }
        }
        if(!empty($nopol)){
            if( $type == 2 ) {
                $conditionsNopol = array(
                    'ViewTtujQty.truck_id' => $nopol,
                    'ViewRevenueQty.truck_id' => $nopol,
                );
            } else {
                $conditionsNopol = array(
                    'ViewTtujQty.nopol LIKE' => '%'.$nopol.'%',
                    'ViewRevenueQty.nopol LIKE' => '%'.$nopol.'%',
                );
            }

            $default_options['conditions'][0]['OR'] = $conditionsNopol;
        }
        if(!empty($customer)){
            $default_options['conditions']['ViewRevenueQty.customer_id'] = $customer;
        }
        if(!empty($nodoc)){
            $default_options['conditions']['ViewRevenueQty.no_doc LIKE'] = '%'.$nodoc.'%';
        }
        if(!empty($nottuj)){
            $ttuj = $this->Ttuj->getData('list', array(
                'conditions' => array(
                    'Ttuj.no_ttuj LIKE' => '%'.$nottuj.'%',
                ),
            ), true, array(
                'branch' => false,
            ));
            $default_options['conditions']['ViewRevenueQty.ttuj_id'] = $ttuj;
        }
        if(!empty($noref)){
            $default_options['conditions']['LPAD(ViewRevenueQty.revenue_id, 5, 0) LIKE'] = '%'.$noref.'%';
        }
        if(!empty($status)){
            $default_options['conditions']['ViewRevenueQty.transaction_status'] = $status;
        }
            
        if(!empty($fromcity)){
            $default_options['conditions'][1]['OR']['ViewTtujQty.from_city_id'] = $fromcity;
            $default_options['conditions'][1]['OR']['ViewRevenueQty.from_city_id'] = $fromcity;
        }
        if(!empty($tocity)){
            $default_options['conditions'][2]['OR']['ViewTtujQty.to_city_id'] = $tocity;
            $default_options['conditions'][2]['OR']['ViewRevenueQty.to_city_id'] = $tocity;
        }
        if(!empty($tocityDetail)){
            $default_options['conditions'][]['OR'] = array(
                'ViewTtujQty.to_city_id' => $tocity,
                'ViewRevenueQty.to_city_id' => $tocity,
            );
        }
        
        if( !empty($sort) ) {
            $sortBranch = strpos($sort, 'Branch.');
            $sortCustomerNoType = strpos($sort, 'CustomerNoType.');
            
            if( is_numeric($sortBranch) ) {
                $default_options['contain'][] = 'Branch';
            }
            if( is_numeric($sortCustomerNoType) ) {
                $default_options['contain'][] = 'CustomerNoType';
            }
        }
        
        return $default_options;
    }
}
?>