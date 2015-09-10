<?php
class RevenueHelper extends AppHelper {
	var $helpers = array(
        'Common', 'Html'
    );

    function _callStatusTTUJ ( $value ) {
        $status = $this->Common->filterEmptyField($value, 'Ttuj', 'status');
        $is_laka = $this->Common->filterEmptyField($value, 'Ttuj', 'is_laka');
        $is_pool = $this->Common->filterEmptyField($value, 'Ttuj', 'is_pool');
        $is_balik = $this->Common->filterEmptyField($value, 'Ttuj', 'is_balik');
        $is_bongkaran = $this->Common->filterEmptyField($value, 'Ttuj', 'is_bongkaran');
        $is_arrive = $this->Common->filterEmptyField($value, 'Ttuj', 'is_arrive');
        $is_draft = $this->Common->filterEmptyField($value, 'Ttuj', 'is_draft');

        if(empty($status)){
            return __('Void');
        } else if(!empty($is_laka)){
            return __('LAKA');
        } else if(!empty($is_pool)){
            return __('Sampai Pool');
        } else if(!empty($is_balik)){
            return __('Balik');
        } else if(!empty($is_bongkaran)){
            return __('Bongkaran');
        } else if(!empty($is_arrive)){
            return __('Tiba');
        } else if(!empty($is_draft)){
            return __('Draft');
        } else{
            return __('Commit');
        }
    }
}