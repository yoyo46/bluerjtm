<?php
class CashBankHelper extends AppHelper {
	var $helpers = array(
        'Common'
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
}