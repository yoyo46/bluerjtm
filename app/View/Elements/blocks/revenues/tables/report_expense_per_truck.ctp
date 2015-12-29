<?php
        if(!empty($values)){
            foreach ($values as $key => $value) {
                $nopol = $this->Common->filterEmptyField($value, 'Truck', 'nopol');
                $year = $this->Common->filterEmptyField($value, 'Truck', 'tahun');
                $capacity = $this->Common->filterEmptyField($value, 'Truck', 'capacity');
                $category = $this->Common->filterEmptyField($value, 'TruckCategory', 'name');
                $brand = $this->Common->filterEmptyField($value, 'TruckBrand', 'name');
                $customer = $this->Common->filterEmptyField($value, 'Customer', 'code');
                $total_revenue = $this->Common->filterEmptyField($value, 'Revenue', 'total');
                $biaya_uang_jalan = $this->Common->filterEmptyField($value, 'Ttuj', 'biaya_uang_jalan');
                $total_cashbank = $this->Common->filterEmptyField($value, 'CashBankDetail', 'total_cashbank');

                $total_expense = $biaya_uang_jalan + $total_cashbank;
                $total_gross_profit = $total_revenue - $total_expense;

                if( !empty($total_revenue) ) {
                    $er = ($total_expense/$total_revenue) * 100;
                } else {
                    $er = '';
                }

                $customTotalRevenue = $this->Common->getFormatPrice($total_revenue);
                $customBiayaUangJalan = $this->Common->getFormatPrice($biaya_uang_jalan);
                $customTotalCashBank = $this->Common->getFormatPrice($total_cashbank);
                $customTotalExpense = $this->Common->getFormatPrice($total_expense);
                $customTotalGrossProfit = $this->Common->getFormatPrice($total_gross_profit);
                $customER = $this->Common->getFormatPrice($er, 0, 2);
?>
<tr>
    <?php 
            echo $this->Html->tag('td', $nopol);
            echo $this->Html->tag('td', $year, array(
                'style' => 'text-align: center',
            ));
            echo $this->Html->tag('td', $brand);
            echo $this->Html->tag('td', $category);
            echo $this->Html->tag('td', $capacity, array(
                'style' => 'text-align: center',
            ));
            echo $this->Html->tag('td', $customer);
            echo $this->Html->tag('td', $customTotalRevenue, array(
                'style' => 'text-align: right',
            ));
            echo $this->Html->tag('td', $customBiayaUangJalan, array(
                'style' => 'text-align: right',
            ));
            echo $this->Html->tag('td', '');
            echo $this->Html->tag('td', $customTotalCashBank, array(
                'style' => 'text-align: right',
            ));
            echo $this->Html->tag('td', $customTotalExpense, array(
                'style' => 'text-align: right',
            ));
            echo $this->Html->tag('td', $customTotalGrossProfit, array(
                'style' => 'text-align: right',
            ));
            echo $this->Html->tag('td', $customER, array(
                'style' => 'text-align: right',
            ));
    ?>
</tr>
<?php
            }
        }
?>