<?php
class CashBankHelper extends AppHelper {
	var $helpers = array(
        'Common', 'Html'
    );

    function _callCalcSaldo( $data ) {
        $type = $this->Common->filterEmptyField($data, 'Coa', 'type');
        $debit = $this->Common->filterEmptyField($data, 'Journal', 'debit');
        $credit = $this->Common->filterEmptyField($data, 'Journal', 'credit');
        $saldo_awal = $this->Common->filterEmptyField($data, 'Journal', 'saldo_awal');

        if( $type == 'debit' ) {
            if( !empty($debit) ) {
                $saldo_awal += $debit;
            } else {
                $saldo_awal -= $credit;
            }
        } else {
            if( !empty($credit) ) {
                $saldo_awal += $credit;
            } else {
                $saldo_awal -= $debit;
            }
        }

        return $saldo_awal;
    }

    function _callStatus ( $data, $is_html = true ) {
        $completed = $this->Common->filterEmptyField($data, 'CashBank', 'completed');
        $is_revised = $this->Common->filterEmptyField($data, 'CashBank', 'is_revised');
        $is_rejected = $this->Common->filterEmptyField($data, 'CashBank', 'is_rejected');

        if(!empty($is_rejected)){
            $status = __('Void');
            $class = 'danger';
        } else if(!empty($completed)){
            $status = __('Approve');
            $class = 'success';
        }else if(!empty($is_revised)){
            $status = __('Revisi');
            $class = 'primary';
        }else {
            $status = __('Pending');
            $class = 'info';
        }

        if( !empty($is_html) ) {
            return $this->Html->tag('span', $status, array(
                'class' => sprintf('label label-%s', $class)
            ));
        } else {
            return $status;
        }
    }

    function _callStatusAuth ( $data ) {
        $status = $this->Common->filterEmptyField($data, 'CashBankAuth', 'status_document', '-');

        if( $status != '-' ) {
            switch ($status) {
                case 'approve':
                    $labelClass = 'success';
                    break;

                case 'reject':
                    $labelClass = 'danger';
                    break;

                case 'revise':
                    $labelClass = 'warning';
                    break;
                
                default:
                    $labelClass = 'default';
                    break;
            }

            $status = $this->Html->tag('div', ucwords($status), array(
                'class' => sprintf('label label-%s', $labelClass),
            ));
        }

        return $status;
    }
}