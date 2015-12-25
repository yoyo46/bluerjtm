<?php
        if(!empty($values)){
            $old = false;

            foreach ($values as $key => $value) {
                $document_id = $this->Common->filterEmptyField($value, 'Journal', 'document_id');
                $document_no = $this->Common->filterEmptyField($value, 'Journal', 'document_no', $document_id);
                $title = $this->Common->filterEmptyField($value, 'Journal', 'title', false, false);
                $date = $this->Common->filterEmptyField($value, 'Journal', 'date');
                $type = $this->Common->filterEmptyField($value, 'Journal', 'type');
                $debit = $this->Common->filterEmptyField($value, 'Journal', 'debit');
                $credit = $this->Common->filterEmptyField($value, 'Journal', 'credit');
                $nopol = $this->Common->filterEmptyField($value, 'Journal', 'nopol');

                $coa = $this->Common->filterEmptyField($value, 'Coa', 'coa_name');

                $new = sprintf('%s-%s-%s', $document_id, $type, $document_no);
                $customDate = $this->Common->formatDate($date, 'd/m/Y');
                $customDebit = $this->Common->getFormatPrice($debit, false);
                $customCredit = $this->Common->getFormatPrice($credit, false);
                // $customDocumentNo = $this->Common->_callDocumentJournal( $document_no, $document_id, $type );
                $noref = str_pad($document_id, 6, '0', STR_PAD_LEFT);
                $customNoref = $this->Common->_callDocumentJournal( $noref, $document_id, $type );

                if( $new != $old ) {
                    if( !empty($old) ) {
                        echo '<tr><td colspan="6"><hr></td></tr>';
                    }
?>
<tr>
    <?php
            echo $this->Html->tag('td', $customNoref);
            echo $this->Html->tag('td', $customDate);
            echo $this->Html->tag('td', $title, array(
                'style' => 'text-align:left;'
            ));
            echo $this->Html->tag('td', '', array(
                'style' => 'text-align:right;',
                'colspan' => 3,
            ));
    ?>
</tr>
<?php
                }
?>
<tr>
    <?php
            echo $this->Html->tag('td', '&nbsp;');
            echo $this->Html->tag('td', $document_no);
            echo $this->Html->tag('td', $coa, array(
                'style' => 'text-align:left;'
            ));
            echo $this->Html->tag('td', $customDebit, array(
                'style' => 'text-align:right;'
            ));
            echo $this->Html->tag('td', $customCredit, array(
                'style' => 'text-align:right;'
            ));
            echo $this->Html->tag('td', $nopol, array(
                'style' => 'text-align:center;'
            ));
    ?>
</tr>
<?php

                $old = $new;
            }
        } else {
            echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                'class' => 'alert alert-warning text-center',
                'colspan' => 6,
            )));
        }
?>