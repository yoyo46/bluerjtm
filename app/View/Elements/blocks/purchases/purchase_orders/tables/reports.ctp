<?php
        if(!empty($values)){
            $totalQty = 0;
            $totalQtyRetur = 0;
            $totalQtyFinal = 0;
            $totalPrice = 0;
            $totalDisc = 0;
            $totalPpn = 0;
            $totalGrandtotal = 0;

            foreach ($values as $key => $value) {
                $id = Common::hashEmptyField($value, 'PurchaseOrder.id');
                $nodoc = Common::hashEmptyField($value, 'PurchaseOrder.nodoc');
                $transaction_date = Common::hashEmptyField($value, 'PurchaseOrder.transaction_date', '-', false, array(
                    'date' => 'd/m/Y',
                ));
                $ppn_type = Common::hashEmptyField($value, 'PurchaseOrder.ppn_type');
                $etd = Common::hashEmptyField($value, 'PurchaseOrder.etd');
                $top = Common::hashEmptyField($value, 'PurchaseOrder.top');
                $note = Common::hashEmptyField($value, 'PurchaseOrder.note', '-');

                $qty_retur = Common::hashEmptyField($value, 'PurchaseOrderDetail.qty_retur', 0);
                $total_qty = Common::hashEmptyField($value, 'PurchaseOrderDetail.total_qty', 0);
                $total_qty_final = Common::hashEmptyField($value, 'PurchaseOrderDetail.total_qty_final', 0);
                $total_price = Common::hashEmptyField($value, 'PurchaseOrderDetail.total_price');
                $total_disc = Common::hashEmptyField($value, 'PurchaseOrderDetail.total_disc');
                $total_ppn = Common::hashEmptyField($value, 'PurchaseOrderDetail.total_ppn');
                $total_total = Common::hashEmptyField($value, 'PurchaseOrderDetail.total_total');

                $no_sq = Common::hashEmptyField($value, 'SupplierQuotation.nodoc', '-');
                $supplier = Common::hashEmptyField($value, 'Vendor.name');

                $draft_receipt_status = $this->Common->_callStatusReceipt($value, 'PurchaseOrder');
                $draft_retur_status = $this->Common->_callStatusRetur($value, 'PurchaseOrder');
                $transaction_status = $this->Common->_callTransactionStatus($value, 'PurchaseOrder');

                $totalQty += $total_qty;
                $totalQtyFinal += $total_qty_final;
                $totalQtyRetur += $qty_retur;
                $totalPrice += $total_price;
                $totalDisc += $total_disc;
                $totalPpn += $total_ppn;
                $totalGrandtotal += $total_total;

                if( $key%2 == 0 ) {
                    $style = 'background-color: #d9edf7;';
                } else {
                    $style = 'background-color: #f5f5f5;';
                }

                echo $this->Html->tableCells(array(
                    array(
                        array(
                            $this->Html->link($nodoc, array(
                                'controller' => 'purchases',
                                'action' => 'purchase_order_detail',
                                $id,
                            ), array(
                                'target' => '_blank',
                            )),
                            array(
                                'style' => $style,
                            ),
                        ),
                        array(
                            $transaction_date,
                            array(
                                'style' => $style,
                            ),
                        ),
                        array(
                            $supplier,
                            array(
                                'style' => $style,
                            ),
                        ),
                        array(
                            $no_sq,
                            array(
                                'style' => $style,
                            ),
                        ),
                        array(
                            $ppn_type,
                            array(
                                'style' => $style,
                            ),
                        ),
                        array(
                            !empty($etd)?__('%s Hari', $etd):'-',
                            array(
                                'style' => 'text-align: center;'.$style,
                            ),
                        ),
                        array(
                            !empty($top)?__('%s Hari', $top):'-',
                            array(
                                'style' => 'text-align: center;'.$style,
                            ),
                        ),
                        array(
                            $note,
                            array(
                                'style' => $style,
                            ),
                        ),
                        array(
                            $total_qty,
                            array(
                                'style' => 'text-align: center;'.$style,
                            ),
                        ),
                        array(
                            $qty_retur,
                            array(
                                'style' => 'text-align: center;'.$style,
                            ),
                        ),
                        array(
                            $total_qty_final,
                            array(
                                'style' => 'text-align: center;'.$style,
                            ),
                        ),
                        array(
                            $this->Common->getFormatPrice($total_price, 0, 2),
                            array(
                                'style' => 'text-align: right;'.$style,
                            ),
                        ),
                        array(
                            $this->Common->getFormatPrice($total_disc, 0, 2),
                            array(
                                'style' => 'text-align: right;'.$style,
                            ),
                        ),
                        array(
                            $this->Common->getFormatPrice($total_ppn, 0, 2),
                            array(
                                'style' => 'text-align: right;'.$style,
                            ),
                        ),
                        array(
                            $this->Common->getFormatPrice($total_total, 0, 2),
                            array(
                                'style' => 'text-align: right;'.$style,
                            ),
                        ),
                        array(
                            $transaction_status,
                            array(
                                'style' => 'text-align: center;'.$style,
                            ),
                        ),
                        array(
                            $draft_receipt_status,
                            array(
                                'style' => 'text-align: center;'.$style,
                            ),
                        ),
                        array(
                            $draft_retur_status,
                            array(
                                'style' => 'text-align: center;'.$style,
                            ),
                        ),
                    )
                ));
            }

            if( $key%2 == 0 ) {
                $style = 'background-color: #d9edf7;';
            } else {
                $style = 'background-color: #f5f5f5;';
            }

            echo $this->Html->tableCells(array(
                array(
                    '&nbsp;',
                    '&nbsp;',
                    '&nbsp;',
                    '&nbsp;',
                    '&nbsp;',
                    '&nbsp;',
                    '&nbsp;',
                    array(
                        $this->Html->tag('strong', __('Total')),
                        array(
                            'style' => 'text-align: right;'.$style,
                        ),
                    ),
                    array(
                        $this->Html->tag('strong', $totalQty),
                        array(
                            'style' => 'text-align: center;'.$style,
                        ),
                    ),
                    array(
                        $this->Html->tag('strong', $totalQtyRetur),
                        array(
                            'style' => 'text-align: center;'.$style,
                        ),
                    ),
                    array(
                        $this->Html->tag('strong', $totalQtyFinal),
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
                        $this->Html->tag('strong', $this->Common->getFormatPrice($totalDisc, 0, 2)),
                        array(
                            'style' => 'text-align: right;'.$style,
                        ),
                    ),
                    array(
                        $this->Html->tag('strong', $this->Common->getFormatPrice($totalPpn, 0, 2)),
                        array(
                            'style' => 'text-align: right;'.$style,
                        ),
                    ),
                    array(
                        $this->Html->tag('strong', $this->Common->getFormatPrice($totalGrandtotal, 0, 2)),
                        array(
                            'style' => 'text-align: right;'.$style,
                        ),
                    ),
                    '&nbsp;',
                    '&nbsp;',
                    '&nbsp;',
                ),
            ), array(
                'class' => 'tf-total',
            ), array(
                'class' => 'tf-total',
            ));
        }
?>