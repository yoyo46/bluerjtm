<?php
        if(!empty($values)){
            $totalUnit = 0;
            $totalUnitSj = 0;
            $totalUnitInvoiced = 0;
            $totalUnitSjNotRecipt = 0;
            $totalUnitJSUnInvoiced = 0;
            $totalUnitUnInvoiced = 0;
            $avgLeadSj = 0;
            $avgLeadSjBilling = 0;
            $avgLeadSjInvoiced = 0;
            $cntAvgLeadSj = 0;
            $cntAvgLeadSjBilling = 0;
            $cntAvgLeadSjInvoiced = 0;

            foreach ($values as $key => $value) {
                $id = $this->Common->filterEmptyField($value, 'Ttuj', 'id');
                $no_ttuj = $this->Common->filterEmptyField($value, 'Ttuj', 'no_ttuj');
                $ttuj_date = $this->Common->filterEmptyField($value, 'Ttuj', 'ttuj_date');
                $nopol = $this->Common->filterEmptyField($value, 'Ttuj', 'nopol');
                $city_name = $this->Common->filterEmptyField($value, 'Ttuj', 'to_city_name');

                $dtSj = $this->Common->filterEmptyField($value, 'SuratJalan', 'tgl_surat_jalan', date('Y-m-d'));
                
                $dtInvoice = $this->Common->filterEmptyField($value, 'Invoice', 'invoice_date', date('Y-m-d'));
                $tglInvoiced = $this->Common->filterEmptyField($value, 'Invoice', 'invoice_date', false, false, array(
                    'date' => 'd/m/Y',
                ));

                $unit = $this->Common->filterEmptyField($value, 'Qty', false, 0);
                $unitSj = $this->Common->filterEmptyField($value, 'QtySJ', false, 0);
                $unitInvoiced = $this->Common->filterEmptyField($value, 'unitInvoiced', false, 0);

                $unitSjNotRecipt = $unit - $unitSj;
                $unitJSUnInvoiced = $unitSj - $unitInvoiced;
                $unitUnInvoiced = $unit - $unitInvoiced;

                $str = strtotime($dtSj) - strtotime($ttuj_date);

                if( !empty($value['SuratJalan']['tgl_surat_jalan']) ) {
                    $tglSJ = $this->Common->filterEmptyField($value, 'SuratJalan', 'tgl_surat_jalan', false, false, array(
                        'date' => 'd/m/Y',
                    ));
                    $leadSj = floor($str/3600/24);

                    if( $leadSj < 0 ) {
                        $leadSj -= 1;
                    } else {
                        $leadSj += 1;
                    }

                    $avgLeadSj += $leadSj;
                    $cntAvgLeadSj++;
                } else {
                    $leadSj = 0;
                    $tglSJ = '-';
                }

                if( !empty($value['SuratJalan']['tgl_surat_jalan']) && !empty($value['Invoice']['invoice_date']) ) {
                    $str = strtotime($dtInvoice) - (strtotime($dtSj));
                    $leadSjBilling = floor($str/3600/24);

                    if( $leadSjBilling < 0 ) {
                        $leadSjBilling -= 1;
                    } else {
                        $leadSjBilling += 1;
                    }

                    $avgLeadSjBilling += $leadSjBilling;
                    $cntAvgLeadSjBilling++;
                } else {
                    $leadSjBilling = 0;
                }

                $str = strtotime($dtInvoice) - strtotime($ttuj_date);

                if( !empty($value['Invoice']['invoice_date']) ) {
                    $leadSjInvoiced = floor($str/3600/24);

                    if( $leadSjInvoiced < 0 ) {
                        $leadSjInvoiced -= 1;
                    } else {
                        $leadSjInvoiced += 1;
                    }

                    $avgLeadSjInvoiced += $leadSjInvoiced;
                    $cntAvgLeadSjInvoiced++;
                } else {
                    $leadSjInvoiced = 0;
                }

                $totalUnit += $unit;
                $totalUnitSj += $unitSj;
                $totalUnitInvoiced += $unitInvoiced;
                $totalUnitSjNotRecipt += $unitSjNotRecipt;
                $totalUnitJSUnInvoiced += $unitJSUnInvoiced;
                $totalUnitUnInvoiced += $unitUnInvoiced;

                if( $unitJSUnInvoiced < 0 ) {
                    $addStyleSj = 'background-color:#f2dede;color:#a94442;';
                } else {
                    $addStyleSj = '';
                }

                echo $this->Html->tableCells(array(
                    array(
                        $this->Html->link($no_ttuj, array(
                            'controller' => 'revenues',
                            'action' => 'info_truk',
                            'ttuj',
                            $id,
                        ), array(
                            'target' => '_blank',
                            'full_base' => true,
                        )),
                        $this->Common->customDate($ttuj_date, 'd/m/Y'),
                        $city_name,
                        $nopol,
                        array(
                            $unit,
                            array(
                                'style' => 'text-align: center;',
                            ),
                        ),
                        array(
                            $unitSj,
                            array(
                                'style' => 'text-align: center;',
                            ),
                        ),
                        array(
                            $unitInvoiced,
                            array(
                                'style' => 'text-align: center;',
                            ),
                        ),
                        array(
                            $unitSjNotRecipt,
                            array(
                                'style' => 'text-align: center;',
                            ),
                        ),
                        array(
                            $unitJSUnInvoiced,
                            array(
                                'style' => 'text-align: center;'.$addStyleSj,
                            ),
                        ),
                        array(
                            $unitUnInvoiced,
                            array(
                                'style' => 'text-align: center;',
                            ),
                        ),
                        array(
                            $tglSJ,
                            array(
                                'style' => 'text-align: center;',
                            ),
                        ),
                        array(
                            $tglInvoiced,
                            array(
                                'style' => 'text-align: center;',
                            ),
                        ),
                        array(
                            $leadSj,
                            array(
                                'style' => 'text-align: center;',
                            ),
                        ),
                        array(
                            $leadSjBilling,
                            array(
                                'style' => 'text-align: center;',
                            ),
                        ),
                        array(
                            $leadSjInvoiced,
                            array(
                                'style' => 'text-align: center;',
                            ),
                        ),
                    )
                ));
            }

            if( $totalUnitJSUnInvoiced < 0 ) {
                $addStyleSj = 'background-color:#f2dede;color:#a94442;';
            } else {
                $addStyleSj = '';
            }

            if( !empty($cntAvgLeadSj) ) {
                $avgLeadSj = $avgLeadSj/$cntAvgLeadSj;
            } else {
                $avgLeadSj = 0;
            }

            if( !empty($cntAvgLeadSjBilling) ) {
                $avgLeadSjBilling = $avgLeadSjBilling/$cntAvgLeadSjBilling;
            } else {
                $avgLeadSjBilling = 0;
            }

            if( !empty($cntAvgLeadSjInvoiced) ) {
                $avgLeadSjInvoiced = $avgLeadSjInvoiced/$cntAvgLeadSjInvoiced;
            } else {
                $avgLeadSjInvoiced = 0;
            }

            echo $this->Html->tableCells(array(
                array(
                    '',
                    '',
                    '',
                    array(
                        $this->Html->tag('strong', __('Total')),
                        array(
                            'style' => 'text-align: right;'
                        ),
                    ),
                    array(
                        $this->Html->tag('strong', $totalUnit),
                        array(
                            'style' => 'text-align: center;',
                        ),
                    ),
                    array(
                        $this->Html->tag('strong', $totalUnitSj),
                        array(
                            'style' => 'text-align: center;',
                        ),
                    ),
                    array(
                        $this->Html->tag('strong', $totalUnitInvoiced),
                        array(
                            'style' => 'text-align: center;',
                        ),
                    ),
                    array(
                        $this->Html->tag('strong', $totalUnitSjNotRecipt),
                        array(
                            'style' => 'text-align: center;',
                        ),
                    ),
                    array(
                        $this->Html->tag('strong', $totalUnitJSUnInvoiced),
                        array(
                            'style' => 'text-align: center;'.$addStyleSj,
                        ),
                    ),
                    array(
                        $this->Html->tag('strong', $totalUnitUnInvoiced),
                        array(
                            'style' => 'text-align: center;',
                        ),
                    ),
                    '',
                    array(
                        $this->Html->tag('strong', __('AVG')),
                        array(
                            'style' => 'text-align: right;',
                        ),
                    ),
                    array(
                        $this->Html->tag('strong', round($avgLeadSj, 2)),
                        array(
                            'style' => 'text-align: center;',
                        ),
                    ),
                    array(
                        $this->Html->tag('strong', round($avgLeadSjBilling, 2)),
                        array(
                            'style' => 'text-align: center;',
                        ),
                    ),
                    array(
                        $this->Html->tag('strong', round($avgLeadSjInvoiced, 2)),
                        array(
                            'style' => 'text-align: center;',
                        ),
                    ),
                )
            ));
        }
?>