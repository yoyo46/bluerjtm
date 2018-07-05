<?php 
        $dataColumns = array(
            'check-box' => array(
                'name' => $this->Form->checkbox('checkbox_all', array(
                    'class' => 'checkAll'
                )),
                'class' => 'text-center',
            ),
            'branch' => array(
                'name' => __('Cabang'),
            ),
            'nopol' => array(
                'name' => __('Nopol'),
            ),
            'driver' => array(
                'name' => __('Supir'),
            ),
            'brand' => array(
                'name' => __('Merek'),
            ),
            'category' => array(
                'name' => __('Jenis'),
            ),
            'capacity' => array(
                'name' => __('Kapasitas'),
                'class' => 'text-center',
            ),
            'company' => array(
                'name' => __('Pemilik'),
            ),
        );
        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

        echo $this->element('blocks/insurances/searchs/trucks');
?>
<div id="wrapper-modal-write" class="document-picker">
    <table class="table table-hover">
        <?php
                if( !empty($fieldColumn) ) {
                    echo $this->Html->tag('thead', $this->Html->tag('tr', $fieldColumn));
                }
        ?>
        <tbody>
            <?php
                    if(!empty($values)){
                        foreach ($values as $key => $value) {
                            $id = Common::hashEmptyField($value, 'Truck.id');
                            $nopol = Common::hashEmptyField($value, 'Truck.nopol');
                            $capacity = Common::hashEmptyField($value, 'Truck.capacity');
                            $year = Common::hashEmptyField($value, 'Truck.year');
                            $color = Common::hashEmptyField($value, 'Truck.color');
                            $no_rangka = Common::hashEmptyField($value, 'Truck.no_rangka');
                            $no_machine = Common::hashEmptyField($value, 'Truck.no_machine');

                            $branch = Common::hashEmptyField($value, 'Branch.code');
                            $brand = Common::hashEmptyField($value, 'TruckBrand.name');
                            $category = Common::hashEmptyField($value, 'TruckCategory.name');

                            if( !empty($year) ) {
                                $tmpYear[] = $year;
                            }
                            if( !empty($color) ) {
                                $tmpYear[] = $color;
                            }
            ?>
            <tr class="pick-document" rel="<?php echo $id; ?>" data-type="single-total">
                <?php
                        echo $this->Html->tag('td', $this->Form->checkbox('document_id.'.$id, array(
                            'class' => 'check-option',
                            'value' => $id,
                        )), array(
                            'class' => 'removed check-box text-center',
                        ));
                ?>
                <td class="removed"><?php echo $branch;?></td>
                <td class="removed"><?php echo $nopol;?></td>
                <td class="removed"><?php echo !empty($value['Driver']['driver_name'])?$value['Driver']['driver_name']:'-';?></td>
                <td class="removed"><?php echo $brand;?></td>
                <td class="removed"><?php echo $category;?></td>
                <td class="removed"><?php echo $capacity;?></td>
                <td class="removed"><?php echo !empty($value['Company']['name'])?$value['Company']['name']:'-';?></td>
                <td class="hide duplicate-remove duplicate-rowspan">
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
                <td class="hide duplicate-remove duplicate-rowspan">
                    <?php
                            echo $this->Html->tag('p', $nopol);

                            if( !empty($no_rangka) ) {
                                echo $this->Html->tag('p', $no_rangka);
                            }
                            if( !empty($no_machine) ) {
                                echo $this->Html->tag('p', $no_machine);
                            }
                    ?>
                </td>
                <?php
                        echo $this->Html->tag('td', $this->Common->buildInputForm(sprintf('InsuranceDetail.condition.%s.', $id), false, array(
                            'type' => 'text',
                            'frameClass' => false,
                        )), array(
                            'class' => 'hide',
                        ));
                        echo $this->Html->tag('td', $this->Common->buildInputForm(sprintf('InsuranceDetail.price.%s.', $id), false, array(
                            'type' => 'text',
                            'frameClass' => false,
                            'class' => 'text-right input_price price duplicate-empty',
                            'attributes' => array(
                                'data-type' => 'input_price_coma',
                            ),
                        )), array(
                            'class' => 'hide',
                        ));
                        echo $this->Html->tag('td', $this->Common->buildInputForm(sprintf('InsuranceDetail.rate.%s.', $id), false, array(
                            'type' => 'text',
                            'frameClass' => false,
                            'class' => 'text-center input_number rate duplicate-empty',
                        )), array(
                            'class' => 'hide',
                        ));
                        echo $this->Html->tag('td', '&nbsp;', array(
                            'class' => 'hide total price_custom duplicate-empty',
                            'rel' => 'total',
                        ));
                        echo $this->Html->tag('td', $this->Common->buildInputForm(sprintf('InsuranceDetail.note.%s.', $id), false, array(
                            'type' => 'text',
                            'frameClass' => false,
                        )), array(
                            'class' => 'hide',
                        ));
                ?>
                <td class="actions text-center hide">
                    <?php
                            echo $this->Html->link($this->Common->icon('copy'), '#', array(
                                'class' => 'duplicate-tr btn btn-warning btn-xs duplicate-remove',
                                'escape' => false,
                                'title' => 'Duplicate'
                            )).'&nbsp;';
                            echo $this->Html->link($this->Common->icon('times'), '#', array(
                                'class' => 'delete-document btn btn-danger btn-xs',
                                'escape' => false,
                            ));
                    ?>
                </td>
            </tr>
            <?php
                        }
                    }else{
                        echo $this->Html->tag('tr', $this->Html->tag('td', __('Data tidak ditemukan')) );
                    }
            ?>
        </tbody>
    </table>
</div>
<?php
        echo $this->element('pagination', array(
            'options' => array(
                'urlClass' => 'ajaxCustomModal',
                'title' => $title,
            ),
        ));
?>