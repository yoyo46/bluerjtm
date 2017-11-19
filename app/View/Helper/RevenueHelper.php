<?php
class RevenueHelper extends AppHelper {
	var $helpers = array(
        'Common', 'Html'
    );

    function _callStatusTTUJ ( $value, $type = 'normal', $tag = false ) {
        $status = $this->Common->filterEmptyField($value, 'Ttuj', 'status');
        $is_laka = $this->Common->filterEmptyField($value, 'Ttuj', 'is_laka');
        $is_pool = $this->Common->filterEmptyField($value, 'Ttuj', 'is_pool');
        $is_balik = $this->Common->filterEmptyField($value, 'Ttuj', 'is_balik');
        $is_bongkaran = $this->Common->filterEmptyField($value, 'Ttuj', 'is_bongkaran');
        $is_arrive = $this->Common->filterEmptyField($value, 'Ttuj', 'is_arrive');
        $is_draft = $this->Common->filterEmptyField($value, 'Ttuj', 'is_draft');
        $is_lku = $this->Common->filterEmptyField($value, 'Lku', 'qty');
        $class = 'default';

        switch ($type) {
            case 'sort':
                if(!empty($is_lku)){
                    $result = __('NG');
                    $class = 'warning';
                } else if(empty($status)){
                    $result = __('Void');
                    $class = 'danger';
                } else if(!empty($is_laka)){
                    $result = __('LAKA');
                    $class = 'danger';
                } else if(!empty($is_pool)){
                    $result = __('Pool');
                    $class = 'success';
                } else if(!empty($is_balik)){
                    $result = __('BB');
                    $class = 'info';
                } else if(!empty($is_bongkaran)){
                    $result = __('SB');
                    $class = 'primary';
                } else if(!empty($is_arrive)){
                    $result = __('AB');
                    $class = 'primary';
                } else {
                    $result = __('BT');
                }
                break;
            
            default:
                if(empty($status)){
                    $result = __('Void');
                    $class = 'danger';
                } else if(!empty($is_laka)){
                    $result = __('LAKA');
                    $class = 'danger';
                } else if(!empty($is_pool)){
                    $result = __('Sampai Pool');
                    $class = 'success';
                } else if(!empty($is_balik)){
                    $result = __('Balik');
                    $class = 'info';
                } else if(!empty($is_bongkaran)){
                    $result = __('Bongkaran');
                    $class = 'primary';
                } else if(!empty($is_arrive)){
                    $result = __('Tiba');
                    $class = 'primary';
                } else if(!empty($is_draft)){
                    $result = __('Draft');
                } else{
                    $result = __('Commit');
                    $class = 'primary';
                }
                break;
        }

        if( !empty($tag) ) {
            return $this->Html->tag('span', $result, array(
                'class' => 'label label-'.$class,
            ));
        } else {
            return $result;
        }
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

    function _callStatusCustom ( $data, $modelName = 'TtujPayment', $is_html = true ) {
        $status = $this->Common->filterEmptyField($data, $modelName, 'status');
        $is_canceled = $this->Common->filterEmptyField($data, $modelName, 'is_canceled');
        $canceled_date = $this->Common->filterEmptyField($data, $modelName, 'canceled_date');
        $transaction_status = $this->Common->filterEmptyField($data, $modelName, 'transaction_status');

        if(!empty($is_canceled)){
            $status = __('Void');
            $class = 'danger';
        } else if(!empty($status)){
            if( $transaction_status == 'posting' ) {
                $status = __('Commit');
                $class = 'success';
            } else {
                $status = __('Draft');
                $class = 'default';
            }
        }else if(!empty($status)){
            $status = __('Non-Aktif');
            $class = 'danger';
        }else {
            $status = __('Draft');
            $class = 'default';
        }

        if( !empty($is_html) ) {
            $result = $this->Html->tag('span', $status, array(
                'class' => sprintf('label label-%s', $class)
            ));

            if(!empty($canceled_date) && !empty($is_canceled)){
                $result .= '<br>'.$this->Common->customDate($canceled_date, 'd/m/Y');
            }

            return $result;
        } else {
            return $status;
        }
    }

    function _callTtujPaid ( $value, $alert = false ) {
        $group_id = Configure::read('__Site.config_group_id');
        // $paid = $this->Common->filterEmptyField($value, 'TtujPayment', 'paid');
        $status_sj = $this->Common->filterEmptyField($value, 'Ttuj', 'status_sj', 'none');
        $is_invoice = $this->Common->filterEmptyField($value, 'Ttuj', 'is_invoice');
        $status = $this->Common->filterIssetField($value, 'Ttuj', 'status', true);
        $allowEdit = true;

        if( !empty($alert) ) {
            if( !empty($is_invoice) ) {
                echo $this->Html->tag('p', __('Invoice untuk TTUJ ini telah terbentuk. Segala perubahan tidak diperbolehkan.'), array(
                    'class' => 'alert alert-warning text-center',
                ));
            }
            // else if( !empty($paid) ) {
            //     echo $this->Html->tag('p', __('Uang jalan/Komisi atau biaya TTUJ ini telah dibayar. Segala perubahan tidak diperbolehkan.'), array(
            //         'class' => 'alert alert-warning text-center',
            //     ));
            // }
            else if( !empty($status_sj != 'none') ) {
                echo $this->Html->tag('p', __('Status surat jalan TTUJ ini sudah diterima. Segala perubahan tidak diperbolehkan.'), array(
                    'class' => 'alert alert-warning text-center',
                ));
            }
        }

        // if( $group_id != 1 ) {
            // if( $status_sj != 'none' || !empty($paid) || !empty($is_invoice) || empty($status) ) {
            if( $status_sj != 'none' || !empty($is_invoice) || empty($status) ) {
                $allowEdit = false;
            }
        // }

        return $allowEdit;
    }
}