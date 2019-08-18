<?php
        $idx = 1;

        if(!empty($values)){
            foreach ($values as $key => $value) {
                $id = $this->Common->filterEmptyField($value, 'Ttuj', 'id');
                $no_ttuj = $this->Common->filterEmptyField($value, 'Ttuj', 'no_ttuj');
                $ttuj_date = $this->Common->filterEmptyField($value, 'Ttuj', 'ttuj_date');
                $nopol = $this->Common->filterEmptyField($value, 'Ttuj', 'nopol');
                $city_name = $this->Ttuj->_callDariTujuan($value);

                $driver = $this->Common->_callGetDriver($value);
                $tglBonBiru = $this->Common->filterEmptyField($value, 'BonBiru', 'tgl_bon_biru', '-', false, array(
                    'date' => 'd M Y',
                ));

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
                            $tglBonBiru,
                            array(
                                'style' => 'text-align: center;'.$style,
                            ),
                        ),
                    )
                ));
                
                $idx++;
            }
        }
?>