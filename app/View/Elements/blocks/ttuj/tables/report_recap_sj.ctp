<?php
        $idx = 1;

        if(!empty($values)){
            $totalUnit = 0;
            $totalUnitSj = 0;
            $totalUnitSjNotRecipt = 0;

            foreach ($values as $key => $value) {
                $id = $this->Common->filterEmptyField($value, 'Ttuj', 'id');
                $no_ttuj = $this->Common->filterEmptyField($value, 'Ttuj', 'no_ttuj');
                $ttuj_date = $this->Common->filterEmptyField($value, 'Ttuj', 'ttuj_date');
                $nopol = $this->Common->filterEmptyField($value, 'Ttuj', 'nopol');
                $city_name = $this->Ttuj->_callDariTujuan($value);

                $unit = $this->Common->filterEmptyField($value, 'Qty', false, 0);
                $unitSj = $this->Common->filterEmptyField($value, 'QtySJ', false, 0);

                $driver = $this->Common->_callGetDriver($value);
                $tglSJ = $this->Common->filterEmptyField($value, 'SuratJalan', 'tgl_surat_jalan', '-', false, array(
                    'date' => 'd M Y',
                ));

                $unitSjNotRecipt = $unit - $unitSj;

                $totalUnit += $unit;
                $totalUnitSj += $unitSj;
                $totalUnitSjNotRecipt += $unitSjNotRecipt;

                if( $idx%2 == 0 ) {
                    $style = 'background-color: #d9edf7;';
                } else {
                    $style = 'background-color: #f5f5f5;';
                }

                echo $this->Html->tableCells(array(
                    array(
                        array(
                            $this->Html->link($no_ttuj, array(
                                'controller' => 'revenues',
                                'action' => 'info_truk',
                                'ttuj',
                                $id,
                                'full_base' => true,
                            ), array(
                                'target' => '_blank',
                            )),
                            array(
                                'style' => $style,
                            ),
                        ),
                        array(
                            $this->Common->customDate($ttuj_date, 'd M Y'),
                            array(
                                'style' => $style,
                            ),
                        ),
                        array(
                            $city_name,
                            array(
                                'style' => $style,
                            ),
                        ),
                        array(
                            $nopol,
                            array(
                                'style' => $style,
                            ),
                        ),
                        array(
                            $driver,
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
                            $unitSj,
                            array(
                                'style' => 'text-align: center;'.$style,
                            ),
                        ),
                        array(
                            $tglSJ,
                            array(
                                'style' => 'text-align: center;'.$style,
                            ),
                        ),
                        array(
                            $unitSjNotRecipt,
                            array(
                                'style' => 'text-align: center;'.$style,
                            ),
                        ),
                    )
                ));
                
                $idx++;
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
                            'colspan' => 5,
                        ),
                    ),
                    array(
                        $this->Html->tag('strong', $totalUnit),
                        array(
                            'style' => 'text-align: center;'.$style,
                        ),
                    ),
                    array(
                        $this->Html->tag('strong', $totalUnitSj),
                        array(
                            'style' => 'text-align: center;'.$style,
                        ),
                    ),
                    array(
                        '',
                        array(
                            'style' => 'text-align: center;'.$style,
                        ),
                    ),
                    array(
                        $this->Html->tag('strong', $totalUnitSjNotRecipt),
                        array(
                            'style' => 'text-align: center;'.$style,
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