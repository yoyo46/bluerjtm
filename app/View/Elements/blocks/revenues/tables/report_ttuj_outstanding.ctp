<?php
        if(!empty($values)){
            $grandtotal = 0;
            $totalPaid = 0;

            foreach ($values as $key => $value) {
                $data_type = $this->Common->filterEmptyField($value, 'TtujOutstanding', 'data_type');
                $total = $this->Common->getBiayaTtuj($value, $data_type, false, false, 'TtujOutstanding');
                $type = $this->Common->_callLabelBiayaTtuj($data_type);

                $paid = $this->Common->filterEmptyField($value, 'TtujPayment', 'paid', 0);
                $grandtotal += $total;
                $totalPaid += $paid;

                echo $this->element('blocks/revenues/tables/report_ttuj_outstanding_item', array(
                    'value' => $value,
                    'total' => $total,
                    'paid' => $paid,
                    'type' => $type,
                ));
            }

            $totalSaldo = $grandtotal - $totalPaid;
?>

<tr>
    <?php 
            echo $this->Html->tag('td', '&nbsp;');
            echo $this->Html->tag('td', '&nbsp;');
            echo $this->Html->tag('td', '&nbsp;');
            echo $this->Html->tag('td', '&nbsp;');
            echo $this->Html->tag('td', '&nbsp;');
            echo $this->Html->tag('td', '&nbsp;');
            echo $this->Html->tag('td', '&nbsp;');
            echo $this->Html->tag('td', '&nbsp;');
            echo $this->Html->tag('td', '&nbsp;');
            echo $this->Html->tag('td', $this->Html->tag('strong', __('Total')), array(
                'style' => 'text-align: right;vertical-align: middle;',
            ));
            echo $this->Html->tag('td', $this->Html->tag('strong', $this->Common->getFormatPrice($grandtotal)), array(
                'style' => 'text-align: right;vertical-align: middle;font-weight:bold;',
            ));
            echo $this->Html->tag('td', $this->Html->tag('strong', $this->Common->getFormatPrice($totalPaid)), array(
                'style' => 'text-align: right;vertical-align: middle;font-weight:bold;',
            ));
            echo $this->Html->tag('td', $this->Html->tag('strong', $this->Common->getFormatPrice($totalSaldo)), array(
                'style' => 'text-align: right;vertical-align: middle;font-weight:bold;',
            ));
    ?>
</tr>
<?php
        }
?>