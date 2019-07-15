<?php
        $no = 1;
        $grandtotal = 0;
        $beginingBalance = !empty($beginingBalance)?$beginingBalance:0;
        $customBalance = $beginingBalance;
        $customSaldoAwal = $this->Common->getFormatPrice($beginingBalance, 0, 2);
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
<?PHP
        if( !empty($values) ) {
            foreach ($values as $key => $value) {
                $transaction_id = Common::hashEmptyField($value, 'Titipan.id');
                $document_no = Common::hashEmptyField($value, 'Titipan.nodoc');
                $document_id = Common::hashEmptyField($value, 'Titipan.document_id');
                $date = Common::hashEmptyField($value, 'Titipan.transaction_date');
                $total = Common::hashEmptyField($value, 'TitipanDetail.total');

                $note = Common::hashEmptyField($value, 'Titipan.note', '-');
                $note = Common::hashEmptyField($value, 'TitipanDetail.note', $note);

                $customDate = $this->Common->formatDate($date, 'd M Y');
                $customTotal = $this->Common->getFormatPrice($total, false, 2);
                
                $noref = str_pad($transaction_id, 6, '0', STR_PAD_LEFT);

                $grandtotal += $total;

                $customBalance += $total;

                $customFormatBalance = $this->Common->getFormatPrice($customBalance, false, 2);
?>
<tr>
    <?php
            echo $this->Html->tag('td', $no);
            echo $this->Html->tag('td', $customDate);
            echo $this->Html->tag('td', $this->Html->link($noref, array(
                'action' => 'detail',
                $transaction_id,
            ), array(
                'target' => '_blank',
            )));
            echo $this->Html->tag('td', $document_no);
            echo $this->Html->tag('td', $note);
            echo $this->Html->tag('td', $customTotal, array(
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
        
        $customGrandtotal = $this->Common->getFormatPrice($grandtotal, false, 2);
?>
<tr class="total">
    <?php
            echo $this->Html->tag('td', __('Total:'), array(
                'style' => 'text-align:right;font-weight:bold;',
                'colspan' => 5,
            ));
            echo $this->Html->tag('td', $customGrandtotal, array(
                'style' => 'text-align:right;'
            ));
            echo $this->Html->tag('td', '');
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