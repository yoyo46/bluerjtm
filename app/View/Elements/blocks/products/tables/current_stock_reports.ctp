<?php
        if(!empty($values)){
            $totalQty = 0;
            $totalPrice = 0;
            $grandtotal = 0;

            foreach ($values as $key => $value) {
                $id = Common::hashEmptyField($value, 'Product.id');
                $code = Common::hashEmptyField($value, 'Product.code');
                $name = Common::hashEmptyField($value, 'Product.name');
                $unit = Common::hashEmptyField($value, 'ProductUnit.name');
                $qty = Common::hashEmptyField($value, 'ProductHistory.total_qty', 0);
                $total = Common::hashEmptyField($value, 'ProductHistory.total_balance', 0);

                if( !empty($qty) ) {
                    $price = $total / $qty;
                } else {
                    $price = 0;
                }

                $totalQty += $qty;
                $totalPrice += $price;
                $grandtotal += $total;

                if( $key%2 == 0 ) {
                    $style = 'background-color: #d9edf7;';
                } else {
                    $style = 'background-color: #f5f5f5;';
                }

                echo $this->Html->tableCells(array(
                    array(
                        array(
                            $this->Html->link($code, array(
                                'controller' => 'products',
                                'action' => 'edit',
                                $id,
                            ), array(
                                'target' => '_blank',
                            )),
                            array(
                                'style' => $style,
                            ),
                        ),
                        array(
                            $name,
                            array(
                                'style' => $style,
                            ),
                        ),
                        array(
                            $unit,
                            array(
                                'style' => 'text-align: center;'.$style,
                            ),
                        ),
                        array(
                            $qty,
                            array(
                                'style' => 'text-align: center;'.$style,
                            ),
                        ),
                        array(
                            $this->Common->getFormatPrice($price, 0, 2),
                            array(
                                'style' => 'text-align: right;'.$style,
                            ),
                        ),
                        array(
                            $this->Common->getFormatPrice($total, 0, 2),
                            array(
                                'style' => 'text-align: right;'.$style,
                            ),
                        ),
                    )
                ));
            }

            if( ($key+1)%2 == 0 ) {
                $style = 'background-color: #d9edf7;';
            } else {
                $style = 'background-color: #f5f5f5;';
            }

            echo $this->Html->tableCells(array(
                array(
                    array(
                        $this->Html->tag('strong', __('Total')),
                        array(
                            'style' => 'text-align: right;'.$style,
                            'colspan' => 3,
                        ),
                    ),
                    array(
                        $this->Html->tag('strong', $totalQty),
                        array(
                            'style' => 'text-align: center;'.$style,
                        ),
                    ),
                    array(
                        $this->Html->tag('strong', $this->Common->getFormatPrice($totalPrice, 0, 2)),
                        array(
                            'style' => 'text-align: right;'.$style,
                        ),
                    ),
                    array(
                        $this->Html->tag('strong', $this->Common->getFormatPrice($grandtotal, 0, 2)),
                        array(
                            'style' => 'text-align: right;'.$style,
                        ),
                    ),
                ),
            ), array(
                'class' => 'tf-total',
            ), array(
                'class' => 'tf-total',
            ));
        }
?>