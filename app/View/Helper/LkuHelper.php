<?php
class LkuHelper extends AppHelper {
	var $helpers = array(
        'Common', 'Html'
    );

    function getCheckStatus ( $data, $modelName ) {
        $status = $this->Common->filterEmptyField($data, $modelName, 'status');
        $paid = $this->Common->filterEmptyField($data, $modelName, 'paid');
        $complete_paid = $this->Common->filterEmptyField($data, $modelName, 'complete_paid');
        $kekurangan_atpm = $this->Common->filterEmptyField($data, $modelName, 'kekurangan_atpm');
        $completed = $this->Common->filterEmptyField($data, $modelName, 'completed');
        $status = $this->Common->filterEmptyField($data, $modelName, 'status');
        $customStatus = '-';

        if( !empty($completed) || !empty($complete_paid) ) {
            $customStatus = $this->Html->tag('span', __('Selesai'), array(
                'class' => 'label label-success',
            ));
        } else if( empty($status) ) {
            $customStatus = $this->Html->tag('span', __('Void'), array(
                'class' => 'label label-danger',
            ));
        } else {
            $customStatus = $this->Html->tag('span', __('Belum'), array(
                'class' => 'label label-default',
            ));
        }

        return $customStatus;
    }

    function _callStatus ( $data, $modelName = 'LkuPayment', $is_html = true ) {
        $status = $this->Common->filterEmptyField($data, $modelName, 'status');
        $is_canceled = $this->Common->filterEmptyField($data, $modelName, 'is_void');
        $canceled_date = $this->Common->filterEmptyField($data, $modelName, 'void_date');
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
}