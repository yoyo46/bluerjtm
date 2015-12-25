<?php
        if(!empty($values)){
            foreach ($values as $key => $value) {
                $date = $this->Common->filterEmptyField($value, 'Revenue', 'date_revenue');
                $unit = $this->Common->filterEmptyField($value, 'qty_unit');
                $total = $this->Common->filterEmptyField($value, 'Revenue', 'total');

                $customer = $this->Common->filterEmptyField($value, 'Customer', 'code');
                $no_ttuj = $this->Common->filterEmptyField($value, 'Ttuj', 'no_ttuj');
                $nopol = $this->Common->filterEmptyField($value, 'Ttuj', 'nopol');
                $from_city_name = $this->Common->filterEmptyField($value, 'Ttuj', 'from_city_name');
                $to_city_name = $this->Common->filterEmptyField($value, 'Ttuj', 'to_city_name');
                $no_invoice = $this->Common->filterEmptyField($value, 'Invoice', 'no_invoice');

                $status = $this->Revenue->_callStatus($value, 'Ttuj', 'nodoc');
                $customDate = $this->Common->formatDate($date, 'd/m/Y');
?>
<tr>
    <?php 
            echo $this->Html->tag('td', $customDate, array(
                'style' => 'text-align: center;'
            ));
            echo $this->Html->tag('td', $customer);
            echo $this->Html->tag('td', $no_ttuj);
            echo $this->Html->tag('td', $nopol);
            echo $this->Html->tag('td', $from_city_name);
            echo $this->Html->tag('td', $to_city_name);
            echo $this->Html->tag('td', $unit);
            echo $this->Html->tag('td', $total, array(
                'style' => 'text-align: right;'
            ));
            echo $this->Html->tag('td', $no_invoice);
            echo $this->Html->tag('td', $status);
    ?>
</tr>
<?php
            }
        }
?>