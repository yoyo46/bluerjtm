<?php
        if(!empty($values)){
            $totalUnit = 0;
            $totalQtyTtuj = 0;
            $totalInvoice = 0;

            foreach ($values as $key => $value) {
                $date = Common::hashEmptyField($value, 'Revenue.date_revenue');
                $unit = Common::hashEmptyField($value, 'qty_unit', '-');
                $total = Common::hashEmptyField($value, 'Revenue.total');

                $customer = Common::hashEmptyField($value, 'Customer.code');
                $no_ttuj = Common::hashEmptyField($value, 'Ttuj.no_ttuj');
                $total_qty = Common::hashEmptyField($value, 'Ttuj.total_qty', '-');
                $branch = Common::hashEmptyField($value, 'Branch.code');

                $no_invoices = Set::extract('/Invoice/Invoice/no_invoice', $value);
                $no_invoice = !empty($no_invoices)?implode(', ', $no_invoices):false;

                $from_city_name = Common::hashEmptyField($value, 'FromCity.name');
                $to_city_name = Common::hashEmptyField($value, 'ToCity.name');
                
                $nopol = Common::hashEmptyField($value, 'Truck.nopol');
                $nopol = Common::hashEmptyField($value, 'Ttuj.nopol', $nopol);
                $nopol = Common::hashEmptyField($value, 'Revenue.nopol', $nopol);

                $status = $this->Revenue->_callStatus($value, 'Ttuj', 'nodoc');
                $customTotal = $this->Common->getFormatPrice($total);
                $customDate = $this->Common->formatDate($date, 'd M Y');

                $totalUnit += $unit;
                $totalQtyTtuj += $total_qty;
                $totalInvoice += $total;
?>
<tr>
    <?php 
            echo $this->Html->tag('td', $customDate, array(
                'style' => 'text-align: center;'
            ));
            echo $this->Html->tag('td', $branch);
            echo $this->Html->tag('td', $customer);
            echo $this->Html->tag('td', $no_ttuj);
            echo $this->Html->tag('td', $nopol);
            echo $this->Html->tag('td', $from_city_name);
            echo $this->Html->tag('td', $to_city_name);
            echo $this->Html->tag('td', $total_qty, array(
                'style' => 'text-align: center;'
            ));
            echo $this->Html->tag('td', $unit, array(
                'style' => 'text-align: center;'
            ));
            echo $this->Html->tag('td', $customTotal, array(
                'style' => 'text-align: right;'
            ));
            echo $this->Html->tag('td', $no_invoice);
            echo $this->Html->tag('td', $status);
    ?>
</tr>
<?php
            }

            $totalUnit = $this->Common->getFormatPrice($totalUnit);
            $totalQtyTtuj = $this->Common->getFormatPrice($totalQtyTtuj);
            $totalInvoice = $this->Common->getFormatPrice($totalInvoice);
?>
<tr style="font-weight: bold;">
    <?php 
            echo $this->Html->tag('td', __('Total'), array(
                'colspan' => 7,
                'style' => 'text-align: right;'
            ));
            echo $this->Html->tag('td', $totalQtyTtuj, array(
                'style' => 'text-align: center;'
            ));
            echo $this->Html->tag('td', $totalUnit, array(
                'style' => 'text-align: center;'
            ));
            echo $this->Html->tag('td', $totalInvoice, array(
                'style' => 'text-align: right;'
            ));
            echo $this->Html->tag('td', '', array(
                'colspan' => 2,
            ));
    ?>
</tr>
<?php
        }
?>