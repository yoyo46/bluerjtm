<?php
        if(!empty($values)){
            $totalUnit = 0;
            $totalUnitSj = 0;
            $totalUnitSjNotRecipt = 0;

            foreach ($values as $key => $value) {
                $id = $this->Common->filterEmptyField($value, 'Ttuj', 'id');
                $no_ttuj = $this->Common->filterEmptyField($value, 'Ttuj', 'no_ttuj');
                $ttuj_date = $this->Common->filterEmptyField($value, 'Ttuj', 'ttuj_date');
                $nopol = $this->Common->filterEmptyField($value, 'Ttuj', 'nopol');
                $driver_name = $this->Common->filterEmptyField($value, 'Ttuj', 'driver_name');
                $city_name = $this->Ttuj->_callDariTujuan($value);

                $unit = $this->Common->filterEmptyField($value, 'Qty', false, 0);
                $unitSj = $this->Common->filterEmptyField($value, 'QtySJ', false, 0);

                $tglSJ = $this->Common->filterEmptyField($value, 'SuratJalan', 'tgl_surat_jalan', '-', false, array(
                    'date' => 'd/m/Y',
                ));

                $unitSjNotRecipt = $unit - $unitSj;

                $totalUnit += $unit;
                $totalUnitSj += $unitSj;
                $totalUnitSjNotRecipt += $unitSjNotRecipt;

                echo $this->Html->tableCells(array(
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
                        $this->Common->customDate($ttuj_date, 'd/m/Y'),
                        $city_name,
                        $nopol,
                        $driver_name,
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
                            $tglSJ,
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
                    )
                ));
            }

            echo $this->Html->tableCells(array(
                array(
                    array(
                        $this->Html->tag('strong', __('Total')),
                        array(
                            'style' => 'text-align: right;',
                            'colspan' => 5,
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
                    '',
                    array(
                        $this->Html->tag('strong', $totalUnitSjNotRecipt),
                        array(
                            'style' => 'text-align: center;',
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