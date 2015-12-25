<?php
class RevenueHelper extends AppHelper {
	var $helpers = array(
        'Common', 'Html'
    );

    function _callStatusTTUJ ( $value, $type = 'normal' ) {
        $status = $this->Common->filterEmptyField($value, 'Ttuj', 'status');
        $is_laka = $this->Common->filterEmptyField($value, 'Ttuj', 'is_laka');
        $is_pool = $this->Common->filterEmptyField($value, 'Ttuj', 'is_pool');
        $is_balik = $this->Common->filterEmptyField($value, 'Ttuj', 'is_balik');
        $is_bongkaran = $this->Common->filterEmptyField($value, 'Ttuj', 'is_bongkaran');
        $is_arrive = $this->Common->filterEmptyField($value, 'Ttuj', 'is_arrive');
        $is_draft = $this->Common->filterEmptyField($value, 'Ttuj', 'is_draft');
        $is_lku = $this->Common->filterEmptyField($value, 'Lku', 'qty');

        switch ($type) {
            case 'sort':
                if(!empty($is_lku)){
                    $result = __('NG');
                } else if(empty($status)){
                    $result = __('Void');
                } else if(!empty($is_laka)){
                    $result = __('LAKA');
                } else if(!empty($is_pool)){
                    $result = __('Pool');
                } else if(!empty($is_balik)){
                    $result = __('BB');
                } else if(!empty($is_bongkaran)){
                    $result = __('SB');
                } else if(!empty($is_arrive)){
                    $result = __('AB');
                } else {
                    $result = __('BT');
                }
                break;
            
            default:
                if(empty($status)){
                    $result = __('Void');
                } else if(!empty($is_laka)){
                    $result = __('LAKA');
                } else if(!empty($is_pool)){
                    $result = __('Sampai Pool');
                } else if(!empty($is_balik)){
                    $result = __('Balik');
                } else if(!empty($is_bongkaran)){
                    $result = __('Bongkaran');
                } else if(!empty($is_arrive)){
                    $result = __('Tiba');
                } else if(!empty($is_draft)){
                    $result = __('Draft');
                } else{
                    $result = __('Commit');
                }
                break;
        }

        return $result;
    }

    function _callStatusInvoicePayment ( $data ) {
        $is_canceled = $this->Common->filterEmptyField($data, 'InvoicePayment', 'is_canceled');
        $status = $this->Common->filterEmptyField($data, 'InvoicePayment', 'status');
        $canceled_date = $this->Common->filterEmptyField($data, 'InvoicePayment', 'canceled_date');

        $customDate = $this->Common->formatDate($canceled_date, 'd/m/Y', false);
        $content = false;

        if( empty($is_canceled) ){
            if( !empty($status) ){
                $content .= $this->Html->tag('span', __('Aktif'), array(
                    'class' => 'label label-success'
                ));
            }else{
                $content .= $this->Html->tag('span', __('Non-Aktif'), array(
                    'class' => 'label label-danger'
                ));
            }
        }else{
            $content .= $this->Html->tag('span', __('Void'), array(
                'class' => 'label label-danger'
            ));
            if(!empty($canceled_date)){
                $content .= '<br>'.$customDate;
            }
        }

        return $content;
    }

    function _callStatus ( $value ) {
        $result = false;
        $status = $this->Common->filterEmptyField($value, 'Revenue', 'transaction_status');

        switch ($status) {
            case 'unposting':
                $result = __('Unposting');
                break;
            case 'posting':
                $result = __('Posting');
                break;
            case 'invoiced':
                $result = __('Invoiced');
                break;
            case 'half_invoiced':
                $result = __('Posting');
                break;
        }

        return $result;
    }
}