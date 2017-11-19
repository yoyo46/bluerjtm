<?php
        $no = 1;
        $beginningBalance = 0;
        $totalDebit = 0;
        $totalCredit = 0;
        // $saldo_awal = $this->Common->filterEmptyField($coa, 'Coa', 'balance');
        $beginingBalance = !empty($beginingBalance)?$beginingBalance:0;
        $customBalance = $beginingBalance;
        $customSaldoAwal = $this->Common->getFormatPrice($beginingBalance, false, 2);
?>
<tr class="beginning">
    <?php
            echo $this->Html->tag('td', $this->Html->tag('i', __('Beginning Balance')), array(
                'colspan' => 7,
            ));
            echo $this->Html->tag('td', $customSaldoAwal, array(
                'style' => 'text-align:right;'
            ));
    ?>
</tr>
<?PHP
        if( !empty($values) ) {
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
                $customDebit = $this->Common->getFormatPrice($debit, false, 2);
                $customCredit = $this->Common->getFormatPrice($credit, false, 2);
                
                $noref = str_pad($document_id, 6, '0', STR_PAD_LEFT);
                $customNoref = $this->Common->_callDocumentJournal( $noref, $document_id, $type, $data_action );

                $totalDebit += $debit;
                $totalCredit += $credit;

                $customBalance += $debit;
                $customBalance -= $credit;

                $customFormatBalance = $this->Common->getFormatPrice($customBalance, false, 2);

                /*
                if( $no == 1 ) {
                    $beginningBalance = $beginingBalance;
?>
<tr class="beginning">
    <?php
            echo $this->Html->tag('td', $this->Html->tag('i', __('Beginning Balance')), array(
                'colspan' => 7,
            ));
            echo $this->Html->tag('td', $customSaldoAwal, array(
                'style' => 'text-align:right;'
            ));
    ?>
</tr>
<?php
            }
            */
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

        $customBeginningBalance = $this->Common->getFormatPrice($beginningBalance, false, 2);
        $customTotalDebit = $this->Common->getFormatPrice($totalDebit, false, 2);
        $customTotalCredit = $this->Common->getFormatPrice($totalCredit, false, 2);
        $customChange = $this->Common->getFormatPrice($change, false, 2);
?>
<tr class="total">
    <?php
            echo $this->Html->tag('td', __('Beginning Balance:'), array(
                'colspan' => 3,
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
            $customFormatBalance = $this->Common->getFormatPrice($customBalance, false, 2);
            
            echo $this->Html->tag('td', $this->Html->tag('strong', __('Ending Balance:')), array(
                'colspan' => 3,
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
<?php 
        } else {
            echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                'class' => 'alert alert-warning text-center',
                'colspan' => '8'
            )));
        }
?>