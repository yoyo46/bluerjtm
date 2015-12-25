<?php
        $no = 1;
        $beginningBalance = 0;
        $totalDebit = 0;
        $totalCredit = 0;
        $saldo_awal = $this->Common->filterEmptyField($coa, 'Coa', 'balance');
        $customBalance = $saldo_awal;

        foreach ($values as $key => $value) {
            $document_no = $this->Common->filterEmptyField($value, 'Journal', 'document_no');
            $document_id = $this->Common->filterEmptyField($value, 'Journal', 'document_id');
            $title = $this->Common->filterEmptyField($value, 'Journal', 'title', false, false);
            $date = $this->Common->filterEmptyField($value, 'Journal', 'date');
            $type = $this->Common->filterEmptyField($value, 'Journal', 'type');
            $debit = $this->Common->filterEmptyField($value, 'Journal', 'debit');
            $credit = $this->Common->filterEmptyField($value, 'Journal', 'credit');
            $nopol = $this->Common->filterEmptyField($value, 'Journal', 'nopol');
            // $saldo_awal = $this->Common->filterEmptyField($value, 'Journal', 'saldo_awal');

            $coa = $this->Common->filterEmptyField($value, 'Coa', 'coa_name');
            $balance = $this->CashBank->_callCalcSaldo($value);

            $new = sprintf('%s-%s', $type, $document_no);
            $customDate = $this->Common->formatDate($date, 'd/m/Y');
            $customDebit = $this->Common->getFormatPrice($debit, false);
            $customCredit = $this->Common->getFormatPrice($credit, false);
            $customSaldoAwal = $this->Common->getFormatPrice($saldo_awal);
            
            $noref = str_pad($document_id, 6, '0', STR_PAD_LEFT);
            $customNoref = $this->Common->_callDocumentJournal( $noref, $document_id, $type );

            $totalDebit += $debit;
            $totalCredit += $credit;

            $customBalance += $debit;
            $customBalance -= $credit;

            $customFormatBalance = $this->Common->getFormatPrice($customBalance);

            if( $no == 1 ) {
                $beginningBalance = $saldo_awal;
?>
<tr class="beginning">
    <?php
            echo $this->Html->tag('td', $this->Html->tag('i', __('Beginning Balance')), array(
                'colspan' => 6,
            ));
            echo $this->Html->tag('td', $customSaldoAwal, array(
                'style' => 'text-align:right;'
            ));
    ?>
</tr>
<?php
            }
?>
<tr>
    <?php
            echo $this->Html->tag('td', $no);
            echo $this->Html->tag('td', $customDate);
            echo $this->Html->tag('td', $customNoref);
            echo $this->Html->tag('td', $document_no);
            echo $this->Html->tag('td', $title);
            echo $this->Html->tag('td', $customDebit, array(
                'style' => 'text-align:right;'
            ));
            echo $this->Html->tag('td', $customCredit, array(
                'style' => 'text-align:right;'
            ));
            echo $this->Html->tag('td', $customFormatBalance, array(
                'style' => 'text-align:right;'
            ));
    ?>
</tr>
<?php

            $no++;
        }
        
        $change = $totalDebit - $totalCredit;

        $customBeginningBalance = $this->Common->getFormatPrice($beginningBalance);
        $customTotalDebit = $this->Common->getFormatPrice($totalDebit);
        $customTotalCredit = $this->Common->getFormatPrice($totalCredit);
        $customChange = $this->Common->getFormatPrice($change);
?>
<tr class="total">
    <?php
            echo $this->Html->tag('td', __('Beginning Balance:'), array(
                'colspan' => 2,
                'style' => 'text-align:left;font-weight:bold;'
            ));
            echo $this->Html->tag('td', $customBeginningBalance, array(
                'style' => 'text-align:right;'
            ));
            echo $this->Html->tag('td', __('Total:'), array(
                'style' => 'text-align:right;font-weight:bold;'
            ));
            echo $this->Html->tag('td', $customTotalDebit, array(
                'style' => 'text-align:right;'
            ));
            echo $this->Html->tag('td', $customTotalCredit, array(
                'style' => 'text-align:right;'
            ));
            echo $this->Html->tag('td', '');
    ?>
</tr>
<tr class="ending">
    <?php
            $customFormatBalance = $this->Common->getFormatPrice($customBalance);
            
            echo $this->Html->tag('td', $this->Html->tag('strong', __('Ending Balance:')), array(
                'colspan' => 2,
            ));
            echo $this->Html->tag('td', $customFormatBalance, array(
                'style' => 'text-align:right;'
            ));
            echo $this->Html->tag('td', __('Change:'), array(
                'style' => 'text-align:right;font-weight:bold;'
            ));
            echo $this->Html->tag('td', $customChange, array(
                'style' => 'text-align:right;'
            ));
            echo $this->Html->tag('td', '', array(
                'colspan' => 2,
            ));
    ?>
</tr>