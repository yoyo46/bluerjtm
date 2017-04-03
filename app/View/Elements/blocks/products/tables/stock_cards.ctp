<?php
        $style = 'background-color: #f5f5f5;';
        $ending_stock = array();

        if( !empty($values['LastHistory']['id']) ) {
            $lastHistory = $values['LastHistory'];
            $unit = Common::hashEmptyField($lastHistory, 'ProductUnit.name');
            $start_balance = Common::hashEmptyField($lastHistory, 'ProductHistory.ending', 0);
            $total_begining_balance = Common::hashEmptyField($lastHistory, 'ProductHistory.total_begining_balance');

            if( !empty($start_balance) ) {
                $total_begining_price = $total_begining_balance / $start_balance;
            } else {
                $total_begining_price = 0;
            }
    
            $ending_stock[$total_begining_price]['qty'] = $start_balance;
            $ending_stock[$total_begining_price]['price'] = $total_begining_price;
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
                'colspan' => 3,
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
                
                $ending = 0;
                $grandtotal_ending = 0;

                $url = null;
                $price = null;
                $id = Common::hashEmptyField($value, 'Product.id');
                $unit = Common::hashEmptyField($value, 'ProductUnit.name');

                $transaction_id = Common::hashEmptyField($value, 'ProductHistory.transaction_id');
                $transaction_type = Common::hashEmptyField($value, 'ProductHistory.transaction_type');
                // $balance = Common::hashEmptyField($value, 'ProductHistory.balance');
                $transaction_date = Common::hashEmptyField($value, 'ProductHistory.transaction_date', null, array(
                    'date' => 'd/m/Y',
                ));

                $nopol = Common::hashEmptyField($value, 'Truck.nopol', '-');
                $nodoc = Common::hashEmptyField($value, 'DocumentDetail.Document.nodoc');
                $docid = Common::hashEmptyField($value, 'DocumentDetail.Document.id');
                $qty = Common::hashEmptyField($value, 'ProductHistory.qty');
                // $total_balance_price = $total_begining_price*$balance;

                if( in_array($transaction_type, array( 'product_receipt', 'product_expenditure_void' )) ) {
                    $qty_in = Common::hashEmptyField($value, 'ProductHistory.qty');
                    $price = $price_in = Common::hashEmptyField($value, 'ProductHistory.price');
                    $total_in = $qty_in * $price_in;
                    $url = $this->Html->url(array(
                        'controller' => 'products',
                        'action' => 'receipt_detail',
                        $docid,
                    ), true);
                    $total_ending_price = $price*$qty;
                    // $grandtotal_ending = $total_balance_price + $total_ending_price;
            
                    if( !empty($ending_stock[$price]['qty']) ) {
                        $ending_stock[$price]['qty'] = $ending_stock[$price]['qty'] + $qty;
                    } else {
                        $ending_stock[$price] = array(
                            'qty' => $qty,
                            'price' => $price,
                        );
                    }


                    if( $transaction_type == 'product_expenditure_void' ) {
                        $nodoc = __('%s (Void)', $nodoc);
                    }
                } else if( $transaction_type == 'product_expenditure' ) {
                    $qty_out_tmp = $qty_out = Common::hashEmptyField($value, 'ProductHistory.qty');
                    $price = $price_out = Common::hashEmptyField($value, 'ProductHistory.price');
                    $total_out = $qty_out * $price_out;
                    $url = $this->Html->url(array(
                        'controller' => 'products',
                        'action' => 'expenditure_detail',
                        $docid,
                    ), true);
                    $total_ending_price = $price*$qty;
                    // $grandtotal_ending = $total_balance_price - $total_ending_price;
            
                    if( !empty($ending_stock) ) {
                        foreach ($ending_stock as $key => $stock) {
                            $ending_qty = Common::hashEmptyField($stock, 'qty', 0) - $qty_out_tmp;

                            if( empty($ending_qty) ) {
                                unset($ending_stock[$key]);
                                break;
                            } else if( $ending_qty < 0 ) {
                                unset($ending_stock[$key]);
                                $qty_out_tmp = abs($ending_qty);
                            } else {
                                $ending_stock[$key]['qty'] = $ending_qty;
                                break;
                            }
                        }
                    }
                }

                if( $key%2 == 0 ) {
                    $style = 'background-color: #d9edf7;';
                } else {
                    $style = 'background-color: #f5f5f5;';
                }

                // if( !empty($ending) ) {
                //     $grandtotal_ending_price = $grandtotal_ending / $ending;
                // } else {
                //     $grandtotal_ending_price = 0;
                // }

                // if( !empty($balance) && $total_begining_price != $price ) {
                //     $flag = true;
                //     $rowspan = 3;
                // } else {
                //     $flag = false;
                //     $rowspan = 2;
                // }
                if( !empty($ending_stock) ) {
                    $rowspan = count($ending_stock)+1;
                } else {
                    $rowspan = 2;
                }
?>
<tr>
    <?php 
            echo $this->Html->tag('td', $transaction_date, array(
                'style' => $style,
                'rowspan' => $rowspan,
            ));
            echo $this->Html->tag('td', $this->Html->link($nodoc, $url, array(
                'target' => '_blank',
            )), array(
                'style' => $style,
                'rowspan' => $rowspan,
            ));
            echo $this->Html->tag('td', $nopol, array(
                'style' => $style,
                'rowspan' => $rowspan,
            ));
            echo $this->Html->tag('td', $unit, array(
                'style' => 'text-align: center;'.$style,
                'rowspan' => $rowspan,
            ));
            echo $this->Html->tag('td', $qty_in, array(
                'style' => 'text-align: center;'.$style,
                'rowspan' => $rowspan,
            ));
            echo $this->Html->tag('td', $this->Common->getFormatPrice($price_in, 0, 2), array(
                'style' => 'text-align: right;'.$style,
                'rowspan' => $rowspan,
            ));
            echo $this->Html->tag('td', $this->Common->getFormatPrice($total_in, 0, 2), array(
                'style' => 'text-align: right;'.$style,
                'rowspan' => $rowspan,
            ));
            echo $this->Html->tag('td', $qty_out, array(
                'style' => 'text-align: center;'.$style,
                'rowspan' => $rowspan,
            ));
            echo $this->Html->tag('td', $this->Common->getFormatPrice($price_out, 0, 2), array(
                'style' => 'text-align: right;'.$style,
                'rowspan' => $rowspan,
            ));
            echo $this->Html->tag('td', $this->Common->getFormatPrice($total_out, 0, 2), array(
                'style' => 'text-align: right;'.$style,
                'rowspan' => $rowspan,
            ));

            if( !empty($ending_stock) ) {
                $firstArr = reset($ending_stock);
                $lastArr = $ending_stock;
                array_splice($lastArr, 0, 1);

                $ending_qty = Common::hashEmptyField($firstArr, 'qty', 0);
                $ending_price = Common::hashEmptyField($firstArr, 'price', 0);
                $ending_total = $ending_qty*$ending_price;
                
                $ending += $ending_qty;
                $grandtotal_ending += $ending_total;

                echo $this->Html->tag('td', $ending_qty, array(
                    'style' => 'text-align: center;'.$style,
                ));
                echo $this->Html->tag('td', $this->Common->getFormatPrice($ending_price, 0, 2), array(
                    'style' => 'text-align: right;'.$style,
                ));
                echo $this->Html->tag('td', $this->Common->getFormatPrice($ending_total, 0, 2), array(
                    'style' => 'text-align: right;'.$style,
                ));
            } else {
                $ending += $start_balance;
                $grandtotal_ending += $total_begining_balance;

                if( empty($start_balance) ) {
                    $total_begining_price = 0;
                }

                echo $this->Html->tag('td', $start_balance, array(
                    'style' => 'text-align: center;border-bottom: 1px solid #000;'.$style,
                ));
                echo $this->Html->tag('td', $this->Common->getFormatPrice($total_begining_price, 0, 2), array(
                    'style' => 'text-align: right;border-bottom: 1px solid #000;'.$style,
                ));
                echo $this->Html->tag('td', $this->Common->getFormatPrice($total_begining_balance, 0, 2), array(
                    'style' => 'text-align: right;border-bottom: 1px solid #000;'.$style,
                ));
            }


            // if( !empty($flag) ) {
            //     echo $this->Html->tag('td', $balance, array(
            //         'style' => 'text-align: center;'.$style,
            //     ));
            //     echo $this->Html->tag('td', $this->Common->getFormatPrice($total_begining_price, 0, 2), array(
            //         'style' => 'text-align: right;'.$style,
            //     ));
            //     echo $this->Html->tag('td', $this->Common->getFormatPrice($total_balance_price, 0, 2), array(
            //         'style' => 'text-align: right;'.$style,
            //     ));
            // } else {
            //     echo $this->Html->tag('td', $ending, array(
            //         'style' => 'text-align: center;border-bottom: 1px solid #000;'.$style,
            //     ));
            //     echo $this->Html->tag('td', $this->Common->getFormatPrice($grandtotal_ending_price, 0, 2), array(
            //         'style' => 'text-align: right;border-bottom: 1px solid #000;'.$style,
            //     ));
            //     echo $this->Html->tag('td', $this->Common->getFormatPrice($grandtotal_ending, 0, 2), array(
            //         'style' => 'text-align: right;border-bottom: 1px solid #000;'.$style,
            //     ));
            // }
    ?>
</tr>
<?php
        /*
        if( !empty($flag) ) {
?>
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
<?php 
        }
        */
?>
<?php
        if( !empty($lastArr) ) {
            foreach ($lastArr as $key => $stock) {
                $ending_qty = Common::hashEmptyField($stock, 'qty', 0);
                $ending_price = Common::hashEmptyField($stock, 'price', 0);
                $ending_total = $ending_qty*$ending_price;

                $ending += $ending_qty;
                $grandtotal_ending += $ending_total;
?>
<tr>
    <?php
            echo $this->Html->tag('td', $ending_qty, array(
                'style' => 'text-align: center;'.$style,
            ));
            echo $this->Html->tag('td', $this->Common->getFormatPrice($ending_price, 0, 2), array(
                'style' => 'text-align: right;'.$style,
            ));
            echo $this->Html->tag('td', $this->Common->getFormatPrice($ending_total, 0, 2), array(
                'style' => 'text-align: right;'.$style,
            ));
    ?>
</tr>
<?php
            }
        }
?>
<tr>
    <?php 
            if( !empty($ending) ) {
                $grandtotal_ending_price = $grandtotal_ending / $ending;
            } else {
                $grandtotal_ending_price = 0;
            }

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