<?php
        if(!empty($values)){
            $grandtotalArr = array();
            $numbering = !empty($numbering)?$numbering:false;

            $grandtotalRit = 0;
            $grandtotalTargetRit = 0;

            $grandtotalUnit = 0;
            $grandtotalTargetUnit = 0;
            $idx = 1;

            foreach ($values as $key => $value) {
                $sidetotalRit = 0;
                $sidetotalTargetRit = 0;

                $sidetotalUnit = 0;
                $sidetotalTargetUnit = 0;

                $id = $this->Common->filterEmptyField($value, 'Truck', 'id');
                $nopol = $this->Common->filterEmptyField($value, 'Truck', 'nopol');
                $target_rit = $this->Common->filterEmptyField($value, 'Customer', 'target_rit', '-');
                $customer_id = $this->Common->filterEmptyField($value, 'Customer', 'id');
?>
<tr>
    <?php 
            if( !empty($numbering) ) {
                echo $this->Html->tag('td', $idx, array(
                    'style' => 'text-align: center;',
                ));
            }

            echo $this->Html->tag('td', $nopol);

            if( isset($totalCnt) ) {
                for ($i=0; $i <= $totalCnt; $i++) {
                    $month = date('Y-m', mktime(0, 0, 0, $fromMonth+$i, 1, $fromYear));
                    $total_rit = $this->Common->filterEmptyField($cntPencapaian, $id, $month, '-');
                    $total_unit = $this->Common->filterEmptyField($cntUnit, $id, $month, '-');
                    $target_unit = $this->Common->filterEmptyField($targetUnit, $customer_id, $month, '-');

                    $last_target_rit = $this->Common->filterEmptyField($grandtotalArr, 'TargetRit', $month, 0);
                    $last_total_rit = $this->Common->filterEmptyField($grandtotalArr, 'TotalRit', $month, 0);
                    $last_target_unit = $this->Common->filterEmptyField($grandtotalArr, 'TargetUnit', $month, 0);
                    $last_total_unit = $this->Common->filterEmptyField($grandtotalArr, 'TotalUnit', $month, 0);

                    echo $this->Html->tag('td', $target_rit, array(
                        'style' => 'text-align: center;',
                    ));
                    echo $this->Html->tag('td', $total_rit, array(
                        'style' => 'text-align: center;',
                    ));
                    echo $this->Html->tag('td', $target_unit, array(
                        'style' => 'text-align: center;',
                    ));
                    echo $this->Html->tag('td', $total_unit, array(
                        'style' => 'text-align: center;',
                    ));

                    $sidetotalRit += $total_rit;
                    $sidetotalTargetRit += $target_rit;

                    $sidetotalUnit += $total_unit;
                    $sidetotalTargetUnit += $target_unit;

                    $grandtotalArr['TargetRit'][$month] = $last_target_rit + $target_rit;
                    $grandtotalArr['TotalRit'][$month] = $last_total_rit + $total_rit;
                    $grandtotalArr['TargetUnit'][$month] = $last_target_unit + $target_unit;
                    $grandtotalArr['TotalUnit'][$month] = $last_total_unit + $total_unit;
                }
            }

            echo $this->Html->tag('td', $sidetotalTargetRit, array(
                'style' => 'text-align: center;',
            ));
            echo $this->Html->tag('td', $sidetotalRit, array(
                'style' => 'text-align: center;',
            ));
            echo $this->Html->tag('td', $sidetotalTargetUnit, array(
                'style' => 'text-align: center;',
            ));
            echo $this->Html->tag('td', $sidetotalUnit, array(
                'style' => 'text-align: center;',
            ));

            $grandtotalRit += $sidetotalRit;
            $grandtotalTargetRit += $sidetotalTargetRit;

            $grandtotalUnit += $sidetotalUnit;
            $grandtotalTargetUnit += $sidetotalTargetUnit;
    ?>
</tr>
<?php
            $idx++;
        }
?>
<tr>
    <?php 
            if( !empty($numbering) ) {
                $colspan = 2;
            } else {
                $colspan = false;
            }

            echo $this->Html->tag('td', __('Total'), array(
                'colspan' => $colspan,
                'style' => 'text-align: right;font-weight: bold;',
            ));

            if( isset($totalCnt) ) {
                for ($i=0; $i <= $totalCnt; $i++) {
                    $month = date('Y-m', mktime(0, 0, 0, $fromMonth+$i, 1, $fromYear));
                    $target_rit = $this->Common->filterEmptyField($grandtotalArr, 'TargetRit', $month, 0);
                    $total_rit = $this->Common->filterEmptyField($grandtotalArr, 'TotalRit', $month, 0);
                    $target_unit = $this->Common->filterEmptyField($grandtotalArr, 'TargetUnit', $month, 0);
                    $total_unit = $this->Common->filterEmptyField($grandtotalArr, 'TotalUnit', $month, 0);

                    echo $this->Html->tag('td', $target_rit, array(
                        'style' => 'text-align: center;font-weight: bold;',
                    ));
                    echo $this->Html->tag('td', $total_rit, array(
                        'style' => 'text-align: center;font-weight: bold;',
                    ));
                    echo $this->Html->tag('td', $target_unit, array(
                        'style' => 'text-align: center;font-weight: bold;',
                    ));
                    echo $this->Html->tag('td', $total_unit, array(
                        'style' => 'text-align: center;font-weight: bold;',
                    ));
                }
            }

            echo $this->Html->tag('td', $grandtotalTargetRit, array(
                'style' => 'text-align: center;font-weight: bold;',
            ));
            echo $this->Html->tag('td', $grandtotalRit, array(
                'style' => 'text-align: center;font-weight: bold;',
            ));
            echo $this->Html->tag('td', $grandtotalTargetUnit, array(
                'style' => 'text-align: center;font-weight: bold;',
            ));
            echo $this->Html->tag('td', $grandtotalUnit, array(
                'style' => 'text-align: center;font-weight: bold;',
            ));
    ?>
</tr>
<?php
        }
?>