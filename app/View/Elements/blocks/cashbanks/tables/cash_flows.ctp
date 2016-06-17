<?php
        $idx = 0;
        $grandtotal = 0;
        $transactions = !empty($transactions)?$transactions:false;
        $dateTo = $this->Common->formatDate($dateTo, 'Y-m-d');
        $grandtotal_cashflow = $this->Common->filterEmptyField($data, 'Grandtotal');

        if(!empty($values)){
            foreach ($values as $coa_id => $coa_name) {
                $total = $this->Common->filterEmptyField($transactions, $coa_id, false, 0);

                if( $idx%2 == 0 ) {
                    $style = 'background-color: #d9edf7;';
                } else {
                    $style = 'background-color: #f5f5f5;';
                }

                echo $this->Html->tableCells(array(
                    array(
                        array(
                            $coa_name,
                            array(
                                'style' => sprintf('text-align: left;%s', $style),
                            ),
                        ),
                        array(
                            $this->Common->getFormatPrice($total, 0, 2),
                            array(
                                'style' => sprintf('text-align: right;%s', $style),
                            ),
                        )
                    ),
                ));
                
                $idx++;
                $grandtotal += $total;
            }

            if( $idx%2 == 0 ) {
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

            if( $type == 'out' ) {
                $idx++;

                if( $idx%2 == 0 ) {
                    $style = 'background-color: #d9edf7;';
                } else {
                    $style = 'background-color: #f5f5f5;';
                }
                
                echo $this->Html->tableCells(array(
                    array(
                        array(
                            $this->Html->tag('strong', __('Grandtotal')),
                            array(
                                'style' => 'text-align: right;'.$style,
                            ),
                        ),
                        array(
                            $this->Html->tag('strong', $this->Common->getFormatPrice($grandtotal_cashflow, 0, 2)),
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
        }
?>