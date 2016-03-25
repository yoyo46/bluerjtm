<?php 
        $dataColumns = array(
            'id' => array(
                'name' => __('No. Ref'),
            ),
            'name' => array(
                'name' => __('Nama'),
            ),
            'group' => array(
                'name' => __('Group Asset'),
                'class' => 'text-center',
            ),
            'purchase_date' => array(
                'name' => __('Tgl pembelian'),
                'class' => 'text-center',
            ),
            'neraca_date' => array(
                'name' => __('Tgl neraca'),
                'class' => 'text-center',
            ),
            'nilai' => array(
                'name' => __('Nilai perolehan'),
                'class' => 'text-center',
            ),
            'depr' => array(
                'name' => __('Depr/bulan'),
                'class' => 'text-center',
            ),
            'ak' => array(
                'name' => __('Ak. Penyusutan'),
                'class' => 'text-center',
            ),
            'nilai_buku' => array(
                'name' => __('Nilai Buku'),
                'class' => 'text-center',
            ),
            'created' => array(
                'name' => __('Dibuat'),
                'class' => 'text-center',
            ),
            'action' => array(
                'name' => __('Action'),
                'class' => 'text-center',
            ),
        );
        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/assets/searchs/index');
?>
<div class="box">
    <?php 
            echo $this->element('blocks/common/box_header', array(
                'title' => $sub_module_title,
                '_add' => array(
                    'action' => 'add',
                ),
            ));
    ?>
    <div class="box-body table-responsive">
        <table class="table table-hover sorting">
            <?php
                    if( !empty($fieldColumn) ) {
                        echo $this->Html->tag('thead', $this->Html->tag('tr', $fieldColumn));
                    }
            ?>
            <tbody>
            <?php
                    if(!empty($values)){
                        foreach ($values as $key => $value) {
                            $id = $this->Common->filterEmptyField($value, 'Asset', 'id');
                            $name = $this->Common->filterEmptyField($value, 'Asset', 'name');
                            $nilai_perolehan = $this->Common->filterEmptyField($value, 'Asset', 'nilai_perolehan');
                            $depr_bulan = $this->Common->filterEmptyField($value, 'Asset', 'depr_bulan');
                            $ak_penyusutan = $this->Common->filterEmptyField($value, 'Asset', 'ak_penyusutan');
                            $nilai_buku = $this->Common->filterEmptyField($value, 'Asset', 'nilai_buku');
                            $purchase_date = $this->Common->filterEmptyField($value, 'Asset', 'purchase_date');
                            $neraca_date = $this->Common->filterEmptyField($value, 'Asset', 'neraca_date');
                            $created = $this->Common->filterEmptyField($value, 'Asset', 'created');

                            $group = $this->Common->filterEmptyField($value, 'AssetGroup', 'group_name');

                            $purchase_date = $this->Common->formatDate($purchase_date, 'd/m/Y');
                            $neraca_date = $this->Common->formatDate($neraca_date, 'd/m/Y');
                            $created = $this->Common->formatDate($created);
                            $nilai_perolehan = $this->Common->getFormatPrice($nilai_perolehan, 0, 2);
                            $depr_bulan = $this->Common->getFormatPrice($depr_bulan, 0, 2);
                            $ak_penyusutan = $this->Common->getFormatPrice($ak_penyusutan, 0, 2);
                            $nilai_buku = $this->Common->getFormatPrice($nilai_buku, 0, 2);
                            $noref = str_pad($id, 6, '0', STR_PAD_LEFT);

                            $action = $this->Html->link(__('Edit'), array(
                                'action' => 'edit',
                                $id,
                                'admin' => false,
                            ), array(
                                'class' => 'btn btn-primary btn-xs'
                            ));
                            $action .= $this->Html->link(__('Hapus'), array(
                                'action' => 'toggle',
                                $id,
                                'admin' => false,
                            ), array(
                                'class' => 'btn btn-danger btn-xs',
                            ), __('Anda yakin ingin menghapus asset ini?'));

                            echo $this->Html->tableCells(array(
                                array(
                                    $noref,
                                    $name,
                                    $group,
                                    array(
                                        $purchase_date,
                                        array(
                                            'class' => 'text-center',
                                        ),
                                    ),
                                    array(
                                        $neraca_date,
                                        array(
                                            'class' => 'text-center',
                                        ),
                                    ),
                                    array(
                                        $nilai_perolehan,
                                        array(
                                            'class' => 'text-right',
                                        ),
                                    ),
                                    array(
                                        $depr_bulan,
                                        array(
                                            'class' => 'text-right',
                                        ),
                                    ),
                                    array(
                                        $ak_penyusutan,
                                        array(
                                            'class' => 'text-right',
                                        ),
                                    ),
                                    array(
                                        $nilai_buku,
                                        array(
                                            'class' => 'text-right',
                                        ),
                                    ),
                                    array(
                                        $created,
                                        array(
                                            'class' => 'text-center',
                                        ),
                                    ),
                                    array(
                                        $action,
                                        array(
                                            'class' => 'action text-center',
                                        ),
                                    ),
                                )
                            ));
                        }
                    } else {
                        echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                            'class' => 'alert alert-warning text-center',
                            'colspan' => '11'
                        )));
                    }
            ?>
            </tbody>
        </table>
    </div>
    <?php echo $this->element('pagination');?>
</div>