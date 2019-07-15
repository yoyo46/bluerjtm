<?php
        $no = 1;
        $beginningBalance = 0;
        $totalDebit = 0;
        $totalCredit = 0;
        $beginingBalance = !empty($beginingBalance)?$beginingBalance:0;
        $customBalance = $beginingBalance;
        $customSaldoAwal = $this->Common->getFormatPrice($beginingBalance, 0, 2);
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
                $transaction_id = Common::hashEmptyField($value, 'ViewDebtCard.transaction_id');
                $document_no = Common::hashEmptyField($value, 'ViewDebtCard.nodoc');
                $document_id = Common::hashEmptyField($value, 'ViewDebtCard.document_id');
                $date = Common::hashEmptyField($value, 'ViewDebtCard.transaction_date');
                $debit = Common::hashEmptyField($value, 'ViewDebtCard.debit');
                $credit = Common::hashEmptyField($value, 'ViewDebtCard.credit');
                $note = Common::hashEmptyField($value, 'ViewDebtCard.note', '-');

                if( !empty($credit) ) {
                    $type = 'credit';
                    $url = array(
                        'action' => 'detail',
                        $transaction_id,
                    );
                } else {
                    $type = 'debit';
                    $url = array(
                        'action' => 'payment_detail',
                        $transaction_id,
                    );
                }

                $customDate = $this->Common->formatDate($date, 'd M Y');
                $customDebit = $this->Common->getFormatPrice($debit, false, 2);
                $customCredit = $this->Common->getFormatPrice($credit, false, 2);
                
                $noref = str_pad($document_id, 6, '0', STR_PAD_LEFT);

                $totalDebit += $debit;
                $totalCredit += $credit;

                $customBalance -= $debit;
                $customBalance += $credit;

                $customFormatBalance = $this->Common->getFormatPrice($customBalance, false, 2);
                $transaction_id = str_pad($transaction_id, 6, '0', STR_PAD_LEFT);
?>
<tr>
    <?php
            echo $this->Html->tag('td', $no);
            echo $this->Html->tag('td', $customDate);
            echo $this->Html->tag('td', $this->Html->link($transaction_id, $url, array(
                'target' => '_blank',
            )));
            echo $this->Html->tag('td', $document_no);
            echo $this->Html->tag('td', $note);
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
        
        $customTotalDebit = $this->Common->getFormatPrice($totalDebit, false, 2);
        $customTotalCredit = $this->Common->getFormatPrice($totalCredit, false, 2);
?>
<tr class="total">
    <?php
            echo $this->Html->tag('td', __('Total:'), array(
                'style' => 'text-align:right;font-weight:bold;',
                'colspan' => 5,
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
            
            echo $this->Html->tag('td', $this->Html->tag('strong', __('Sisa Hutang:')), array(
                'colspan' => 2,
            ));
            echo $this->Html->tag('td', $customFormatBalance, array(
                'style' => 'text-align:left;'
            ));
            echo $this->Html->tag('td', '', array(
                'colspan' => 4,
            ));
    ?>
</tr>
<?php 
        } else {
            echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                'class' => 'alert alert-warning text-center',
                'colspan' => '10'
            )));
        }
?>