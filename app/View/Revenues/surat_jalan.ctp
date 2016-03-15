<?php 
        $dataColumns = array(
            'noref' => array(
                'name' => __('No. Referensi'),
                'class' => 'text-center',
            ),
            'nodoc' => array(
                'name' => __('No. Dokumen'),
                'class' => 'text-center',
            ),
            'date' => array(
                'name' => __('Tgl Terima'),
                'class' => 'text-center',
            ),
            'ttuj' => array(
                'name' => __('TTUJ Diterima'),
                'class' => 'text-center',
            ),
            'unit' => array(
                'name' => __('Unit Diterima'),
                'class' => 'text-center',
            ),
            'status' => array(
                'name' => __('Status'),
                'class' => 'text-center',
            ),
            'action' => array(
                'name' => __('Action'),
                'class' => 'text-center',
            ),
        );
        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/ttuj/search_sj');
?>
<div class="box">
    <?php 
            echo $this->element('blocks/common/box_header', array(
                'title' => $sub_module_title,
                '_add' => array(
                    'controller' => 'revenues',
                    'action' => 'surat_jalan_add',
                ),
            ));
    ?>
    <div class="box-body table-responsive">
        <table class="table table-hover">
            <?php
                    if( !empty($fieldColumn) ) {
                        echo $this->Html->tag('thead', $this->Html->tag('tr', $fieldColumn));
                    }

                    if(!empty($values)){
                        foreach ($values as $key => $value) {
                            $id = $this->Common->filterEmptyField($value, 'SuratJalan', 'id');
                            $nodoc = $this->Common->filterEmptyField($value, 'SuratJalan', 'nodoc', '-');
                            $total_qty = $this->Common->filterEmptyField($value, 'SuratJalan', 'qty_unit');
                            $cnt_ttuj = $this->Common->filterEmptyField($value, 'SuratJalan', 'cnt_ttuj');
                            $date = $this->Common->filterEmptyField($value, 'SuratJalan', 'tgl_surat_jalan');

                            $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
                            $date = $this->Common->formatDate($date, 'd/m/Y');
                            $status = $this->Common->_callStatusVoid($value, 'SuratJalan');
                            $actionBtn = $this->Common->_callActionButtn($value, 'SuratJalan', array(
                                'Detail' => array(
                                    'label' => __('Detail'),
                                    'url' => array(
                                        'controller' => 'revenues',
                                        'action' => 'surat_jalan_detail',
                                        $id,
                                    ),
                                ),
                                'Edit' => array(
                                    'label' => __('Edit'),
                                    'url' => array(
                                        'controller' => 'revenues',
                                        'action' => 'surat_jalan_edit',
                                        $id,
                                    ),
                                ),
                                'Void' => array(
                                    'label' => __('Void'),
                                    'url' => array(
                                        'controller' => 'revenues',
                                        'action' => 'surat_jalan_delete',
                                        $id,
                                    ),
                                ),
                            ));
            ?>
            <tr>
                <?php 
                        echo $this->Html->tag('td', $noref);
                        echo $this->Html->tag('td', $nodoc);
                        echo $this->Html->tag('td', $date, array(
                            'class' => 'text-center'
                        ));
                        echo $this->Html->tag('td', $cnt_ttuj, array(
                            'class' => 'text-center'
                        ));
                        echo $this->Html->tag('td', $total_qty, array(
                            'class' => 'text-center'
                        ));
                        echo $this->Html->tag('td', $status, array(
                            'class' => 'text-center'
                        ));
                        echo $this->Html->tag('td', $actionBtn, array(
                            'class' => 'text-center action',
                        ));
                ?>
            </tr>
            <?php
                        }
                    } else {
                        echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                            'class' => 'alert alert-warning text-center',
                            'colspan' => '8'
                        )));
                    }
            ?>
        </table>
    </div>
    <?php echo $this->element('pagination');?>
</div>