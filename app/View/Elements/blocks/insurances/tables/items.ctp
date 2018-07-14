<?php                        
        $value = !empty($value)?$value:false;
        $tmp_truck_id = !empty($tmp_truck_id)?$tmp_truck_id:false;
        $rowspan = !empty($rowspan)?$rowspan:array();
        $view = !empty($view)?$view:false;

        $id = Common::hashEmptyField($value, 'InsuranceDetail.id');
        $truck_id = Common::hashEmptyField($value, 'InsuranceDetail.truck_id');
        $nopol = Common::hashEmptyField($value, 'InsuranceDetail.nopol');
        $price = Common::hashEmptyField($value, 'InsuranceDetail.price');
        $rate = Common::hashEmptyField($value, 'InsuranceDetail.rate');
        $premi = Common::hashEmptyField($value, 'InsuranceDetail.premi');
        $note = Common::hashEmptyField($value, 'InsuranceDetail.note');
        $condition = Common::hashEmptyField($value, 'InsuranceDetail.condition');

        $capacity = Common::hashEmptyField($value, 'Truck.capacity');
        $year = Common::hashEmptyField($value, 'Truck.tahun');
        $color = Common::hashEmptyField($value, 'Truck.color');
        $no_rangka = Common::hashEmptyField($value, 'Truck.no_rangka');
        $no_machine = Common::hashEmptyField($value, 'Truck.no_machine');
        $brand = Common::hashEmptyField($value, 'TruckBrand.name');
        $category = Common::hashEmptyField($value, 'TruckCategory.name');
        $tmpYear = array();

        if( !empty($year) ) {
            $tmpYear[] = $year;
        }
        if( !empty($color) ) {
            $tmpYear[] = $color;
        }

        $nopolError = $this->Form->error(__('InsuranceDetail.%s.nopol', $idx));
?>
<tr class="pick-document" rel="<?php echo $truck_id; ?>" data-type="single-total">
    <?php
            if( $tmp_truck_id != $truck_id ) {
                $rowspanItem = Common::hashEmptyField($rowspan, $truck_id, 1);
    ?>
    <td class="duplicate-remove duplicate-rowspan" rowspan="<?php echo $rowspanItem; ?>">
        <?php
                if( !empty($brand) ) {
                    echo $this->Html->tag('p', $brand);
                }
                if( !empty($category) ) {
                    echo $this->Html->tag('p', $category);
                }
                if( !empty($tmpYear) ) {
                    echo $this->Html->tag('p', implode(' / ', $tmpYear));
                }
        ?>
    </td>
    <td class="duplicate-remove duplicate-rowspan" rowspan="<?php echo $rowspanItem; ?>">
        <?php
                echo $this->Html->tag('p', $nopol);

                if( !empty($no_rangka) ) {
                    echo $this->Html->tag('p', $no_rangka);
                }
                if( !empty($no_machine) ) {
                    echo $this->Html->tag('p', $no_machine);
                }

                if( !empty($nopolError) ) {
                    echo $nopolError;
                }
        ?>
    </td>
    <?php
            }

            echo $this->Html->tag('td', $this->Common->buildInputForm(sprintf('InsuranceDetail.condition.%s.', $truck_id), false, array(
                'type' => 'text',
                'frameClass' => false,
                'class' => 'duplicate-empty',
                'fieldError' => __('InsuranceDetail.%s.condition', $idx),
                'attributes' => array(
                    'value' => $condition,
                ),
            )));
            echo $this->Html->tag('td', $this->Common->buildInputForm(sprintf('InsuranceDetail.price.%s.', $truck_id), false, array(
                'type' => 'text',
                'frameClass' => false,
                'class' => 'text-right input_price price duplicate-empty',
                'attributes' => array(
                    'data-type' => 'input_price_coma',
                    'value' => $price,
                ),
                'fieldError' => __('InsuranceDetail.%s.price', $idx),
            )));
            echo $this->Html->tag('td', $this->Common->buildInputForm(sprintf('InsuranceDetail.rate.%s.', $truck_id), false, array(
                'type' => 'text',
                'frameClass' => false,
                'class' => 'text-center input_number rate duplicate-empty',
                'attributes' => array(
                    'value' => $rate,
                ),
                'fieldError' => __('InsuranceDetail.%s.rate', $idx),
            )));
            echo $this->Html->tag('td', Common::getFormatPrice($premi, 2), array(
                'class' => 'total price_custom duplicate-empty',
                'rel' => 'total',
            ));
            echo $this->Html->tag('td', $this->Common->buildInputForm(sprintf('InsuranceDetail.note.%s.', $truck_id), false, array(
                'type' => 'text',
                'frameClass' => false,
                'attributes' => array(
                    'value' => $note,
                ),
            )));

            if( empty($view) ) {
    ?>
    <td class="actions text-center">
        <?php
                if( $tmp_truck_id != $truck_id ) {
                    echo $this->Html->link($this->Common->icon('copy'), '#', array(
                        'class' => 'duplicate-tr btn btn-warning btn-xs duplicate-remove',
                        'escape' => false,
                        'title' => 'Duplicate'
                    )).'&nbsp;';
                }

                echo $this->Html->link($this->Common->icon('times'), '#', array(
                    'class' => 'delete-document btn btn-danger btn-xs',
                    'escape' => false,
                ));
        ?>
    </td>
    <?php
            }
    ?>
</tr>