<?php
class CashBankHelper extends AppHelper {
	var $helpers = array(
        'Common', 'Html', 'Form'
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
            $class = 'warning';
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
                    $status = __('Disetujui');
                    $labelClass = 'success';
                    break;

                case 'reject':
                    $status = __('Ditolak');
                    $labelClass = 'danger';
                    break;

                case 'revise':
                    $status = __('Revisi');
                    $labelClass = 'warning';
                    break;
                
                default:
                    $status = __('Pending');
                    $labelClass = 'default';
                    break;
            }

            $status = $this->Html->tag('div', ucwords($status), array(
                'class' => sprintf('label label-%s', $labelClass),
            ));
        }

        return $status;
    }

    function getTruckCashbank ( $value = false ) {
        $uuid = sprintf('truck-%s', String::uuid());

        return $this->Form->input('CashBankDetail.nopol.', array(
            'type' => 'text',
            'id' => $uuid,
            'class' => 'form-control',
            'label' => false,
            'div' => false,
            'required' => false,
            'readonly' => true,
            'value' => $value,
        )).$this->Html->link($this->Common->icon('plus-square'), array(
            'controller'=> 'ajax', 
            'action' => 'getTrucks',
            'cashbank',
            $uuid,
            'admin' => false,
        ), array(
            'escape' => false,
            'class' => 'ajaxModal browse-docs',
            'title' => __('Data Truk'),
            'data-action' => 'browse-form',
            'data-change' => $uuid,
        ));
    }
}