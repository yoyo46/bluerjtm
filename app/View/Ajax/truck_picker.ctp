<?php 
        $title = !empty($title)?$title:false;
        $dataColumns = array(
            'name' => array(
                'name' => __('ID'),
            ),
            'group' => array(
                'name' => __('Cabang'),
            ),
            'purchase_date' => array(
                'name' => __('Nopol'),
                'class' => 'text-center',
            ),
            'neraca_date' => array(
                'name' => __('Supir'),
            ),
            'nilai' => array(
                'name' => __('Merek'),
            ),
            'depr' => array(
                'name' => __('Jenis'),
            ),
            'ak' => array(
                'name' => __('Kapasitas'),
                'class' => 'text-center',
            ),
            'nilai_buku' => array(
                'name' => __('Pemilik'),
            ),
            // 'action' => array(
            //     'name' => __('Action'),
            // ),
        );
        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

        echo $this->element('blocks/ajax/searchs/trucks');
?>
<div class="box-body table-responsive browse-form">
    <table class="table table-hover">
        <?php
                if( !empty($fieldColumn) ) {
                    echo $this->Html->tag('thead', $this->Html->tag('tr', $fieldColumn));
                }
        ?>
        <?php
                if(!empty($values)){
                    foreach ($values as $key => $value) {
                        $id = $this->Common->filterEmptyField($value, 'Truck', 'id');
                        $nopol = $this->Common->filterEmptyField($value, 'Truck', 'nopol');
                        $capacity = $this->Common->filterEmptyField($value, 'Truck', 'capacity');

                        $company = $this->Common->filterEmptyField($value, 'Company', 'name');
                        $category = $this->Common->filterEmptyField($value, 'TruckCategory', 'name');
                        $brand = $this->Common->filterEmptyField($value, 'TruckBrand', 'name');
                        $driver = $this->Common->filterEmptyField($value, 'Driver', 'driver_name');
                        $branch = $this->Common->filterEmptyField($value, 'Branch', 'code');
                        $attr = array(
                            'data-value' => $$return_value,
                            'data-change' => $target,
                            'data-trigger' => 'change',
                        );

                        echo $this->Html->tableCells(array(
                            array(
                                array(
                                    $id,
                                    array(
                                        'class' => 'text-center',
                                    ),
                                ),
                                $branch,
                                array(
                                    $nopol,
                                    array(
                                        'class' => 'text-center',
                                    ),
                                ),
                                $driver,
                                $brand,
                                $category,
                                array(
                                    $capacity,
                                    array(
                                        'class' => 'text-center',
                                    ),
                                ),
                                $company,
                                // $this->Html->link(__('History Perbaikan'), array(
                                //     'controller' => 'spk',
                                //     'action' => 'history',
                                //     $id,
                                // ), array(
                                //     'target' => '_blank',
                                // )),
                            )
                        ), $attr, $attr);
                    }
                }else{
                    echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                        'class' => 'alert alert-warning text-center',
                        'colspan' => '8'
                    )));
                }
        ?>
    </table>
</div><!-- /.box-body -->
<?php
        echo $this->element('pagination', array(
            'options' => array(
                'data-action' => 'browse-form',
                'class' => 'ajaxModal',
                'title' => $title,
            ),
        ));
?>