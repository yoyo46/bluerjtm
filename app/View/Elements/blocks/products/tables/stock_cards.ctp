<?php
        $style = 'background-color: #f5f5f5;';

        if( !empty($values['LastHistory']) ) {
            $lastHistory = $values['LastHistory'];
            $unit = Common::hashEmptyField($lastHistory, 'ProductUnit.name');
            $start_balance = Common::hashEmptyField($lastHistory, 'ProductHistory.ending', 0);
            $total_begining_balance = Common::hashEmptyField($lastHistory, 'ProductHistory.total_begining_balance');

            if( !empty($start_balance) ) {
                $total_begining_price = $total_begining_balance / $start_balance;
            } else {
                $total_begining_price = 0;
            }
        } else {
            $unit = '';
            $start_balance = 0;
            $total_begining_balance = 0;
            $total_begining_price = 0;
        }
?>
<tr>
    <?php 
            echo $this->Html->tag('td', __('OPENING BALANCE'), array(
                'style' => $style,
                'colspan' => 2,
            ));
            echo $this->Html->tag('td', $unit, array(
                'style' => 'text-align: center;'.$style,
            ));
            echo $this->Html->tag('td', '', array(
                'style' => $style,
                'colspan' => 6,
            ));
            echo $this->Html->tag('td', $start_balance, array(
                'style' => 'text-align: center;'.$style,
            ));
            echo $this->Html->tag('td', $this->Common->getFormatPrice($total_begining_price, 0, 2), array(
                'style' => 'text-align: right;'.$style,
            ));
            echo $this->Html->tag('td', $this->Common->getFormatPrice($total_begining_balance, 0, 2), array(
                'style' => 'text-align: right;'.$style,
            ));
    ?>
</tr>
<?php
        if(!empty($values['ProductHistory'])){
            foreach ($values['ProductHistory'] as $key => $value) {
                $qty_in = 0;
                $price_in = 0;
                $total_in = 0;

                $qty_out = 0;
                $price_out = 0;
                $total_out = 0;

                $url = null;
                $price = null;
                $id = Common::hashEmptyField($value, 'Product.id');
                $unit = Common::hashEmptyField($value, 'ProductUnit.name');

                $transaction_id = Common::hashEmptyField($value, 'ProductHistory.transaction_id');
                $transaction_type = Common::hashEmptyField($value, 'ProductHistory.transaction_type');
                $ending = Common::hashEmptyField($value, 'ProductHistory.ending');
                $balance = Common::hashEmptyField($value, 'ProductHistory.balance');
                $transaction_date = Common::hashEmptyField($value, 'ProductHistory.transaction_date', null, array(
                    'date' => 'd/m/Y',
                ));

                $nodoc = Common::hashEmptyField($value, 'DocumentDetail.Document.nodoc');
                $qty = Common::hashEmptyField($value, 'ProductHistory.qty');
                $total_balance_price = $total_begining_price*$balance;

                switch ($transaction_type) {
                    case 'product_receipt':
                        $qty_in = Common::hashEmptyField($value, 'ProductHistory.qty');
                        $price = $price_in = Common::hashEmptyField($value, 'ProductHistory.price');
                        $total_in = $qty_in * $price_in;
                        $url = $this->Html->url(array(
                            'controller' => 'products',
                            'action' => 'receipt_detail',
                            $transaction_id,
                        ), true);
                        $total_ending_price = $price*$qty;
                        $grandtotal_ending = $total_balance_price + $total_ending_price;
                        break;
                    case 'product_expenditure':
                        $qty_out = Common::hashEmptyField($value, 'ProductHistory.qty');
                        $price = $price_out = Common::hashEmptyField($value, 'ProductHistory.price');
                        $total_out = $qty_out * $price_out;
                        $url = $this->Html->url(array(
                            'controller' => 'products',
                            'action' => 'expenditure_detail',
                            $transaction_id,
                        ), true);
                        $total_ending_price = $price*$qty;
                        $grandtotal_ending = $total_balance_price - $total_ending_price;
                        break;
                }

                if( $key%2 == 0 ) {
                    $style = 'background-color: #d9edf7;';
                } else {
                    $style = 'background-color: #f5f5f5;';
                }

                if( !empty($ending) ) {
                    $grandtotal_ending_price = $grandtotal_ending / $ending;
                } else {
                    $grandtotal_ending_price = 0;
                }
?>
<tr>
    <?php 
            echo $this->Html->tag('td', $transaction_date, array(
                'style' => $style,
                'rowspan' => 3,
            ));
            echo $this->Html->tag('td', $this->Html->link($nodoc, $url, array(
                'target' => '_blank',
            )), array(
                'style' => $style,
                'rowspan' => 3,
            ));
            echo $this->Html->tag('td', $unit, array(
                'style' => 'text-align: center;'.$style,
                'rowspan' => 3,
            ));
            echo $this->Html->tag('td', $qty_in, array(
                'style' => 'text-align: center;'.$style,
                'rowspan' => 3,
            ));
            echo $this->Html->tag('td', $this->Common->getFormatPrice($price_in, 0, 2), array(
                'style' => 'text-align: right;'.$style,
                'rowspan' => 3,
            ));
            echo $this->Html->tag('td', $this->Common->getFormatPrice($total_in, 0, 2), array(
                'style' => 'text-align: right;'.$style,
                'rowspan' => 3,
            ));
            echo $this->Html->tag('td', $qty_out, array(
                'style' => 'text-align: center;'.$style,
                'rowspan' => 3,
            ));
            echo $this->Html->tag('td', $this->Common->getFormatPrice($price_out, 0, 2), array(
                'style' => 'text-align: right;'.$style,
                'rowspan' => 3,
            ));
            echo $this->Html->tag('td', $this->Common->getFormatPrice($total_out, 0, 2), array(
                'style' => 'text-align: right;'.$style,
                'rowspan' => 3,
            ));
            echo $this->Html->tag('td', $balance, array(
                'style' => 'text-align: center;'.$style,
            ));
            echo $this->Html->tag('td', $this->Common->getFormatPrice($total_begining_price, 0, 2), array(
                'style' => 'text-align: right;'.$style,
            ));
            echo $this->Html->tag('td', $this->Common->getFormatPrice($total_balance_price, 0, 2), array(
                'style' => 'text-align: right;'.$style,
            ));
    ?>
</tr>
<tr>
    <?php 
            echo $this->Html->tag('td', $qty, array(
                'style' => 'text-align: center;border-bottom: 1px solid #000;'.$style,
            ));
            echo $this->Html->tag('td', $this->Common->getFormatPrice($price, 0, 2), array(
                'style' => 'text-align: right;border-bottom: 1px solid #000;'.$style,
            ));
            echo $this->Html->tag('td', $this->Common->getFormatPrice($total_ending_price, 0, 2), array(
                'style' => 'text-align: right;border-bottom: 1px solid #000;'.$style,
            ));
    ?>
</tr>
<tr>
    <?php 
            echo $this->Html->tag('td', $ending, array(
                'style' => 'text-align: center;font-weight:bold;'.$style,
            ));
            echo $this->Html->tag('td', $this->Common->getFormatPrice($grandtotal_ending_price, 0, 2), array(
                'style' => 'text-align: right;font-weight:bold;'.$style,
            ));
            echo $this->Html->tag('td', $this->Common->getFormatPrice($grandtotal_ending, 0, 2), array(
                'style' => 'text-align: right;font-weight:bold;'.$style,
            ));
    ?>
</tr>
<?php
                $total_begining_price = $grandtotal_ending_price;
            }
        }
?>