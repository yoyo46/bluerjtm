<?php 
        $element = 'blocks/revenues/tables/report_surat_jalan';
        $full_name = !empty($User['Employe']['full_name'])?$User['Employe']['full_name']:false;
        $dataColumns = array(
            'noref' => array(
                'name' => __('No. Ref'),
                'style' => 'text-align: center;vertical-align: middle;',
            ),
            'nodoc' => array(
                'name' => __('No. Doc'),
                'style' => 'text-align: left;vertical-align: middle;',
            ),
            'note' => array(
                'name' => __('Keterangan'),
                'style' => 'text-align: left;vertical-align: middle;',
            ),
            'nottuj' => array(
                'name' => __('No. TTUJ'),
                'style' => 'text-align: left;vertical-align: middle;',
            ),
            'date_ttuj' => array(
                'name' => __('Tgl TTUJ'),
                'style' => 'text-align: center;vertical-align: middle;',
            ),
            'Nopol' => array(
                'name' => __('Nopol'),
                'style' => 'text-align: left;vertical-align: middle;',
            ),
            'driver' => array(
                'name' => __('Supir'),
                'style' => 'text-align: left;vertical-align: middle;',
            ),
            'to' => array(
                'name' => __('Tujuan'),
                'style' => 'text-align: left;vertical-align: middle;',
            ),
            'customer' => array(
                'name' => __('Customer'),
                'style' => 'text-align: center;vertical-align: middle;',
            ),
            'note_ttuj' => array(
                'name' => __('Keterangan TTUJ'),
                'style' => 'text-align: left;vertical-align: middle;',
            ),
            'qty_ttuj' => array(
                'name' => __('Qty TTUJ'),
                'style' => 'text-align: center;vertical-align: middle;',
            ),
            'qty_sj' => array(
                'name' => __('Qty Diterima'),
                'style' => 'text-align: center;vertical-align: middle;',
            ),
        );

        if( !empty($data_action) ){
            $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table', true );

            echo $this->element(sprintf('blocks/common/tables/export_%s', $data_action), array(
                'tableHead' => $fieldColumn,
                'tableBody' => $this->element($element),
                'sub_module_title' => $module_title,
                'contentTr' => false,
            ));
        } else {
            $this->Html->addCrumb($sub_module_title);

            $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

            echo $this->element('blocks/revenues/searchs/report_surat_jalan');
?>
<section class="content invoice">
    <h2 class="page-header">
        <i class="fa fa-globe"></i> <?php echo $sub_module_title;?>
    </h2>
    <?php 
                echo $this->Common->_getPrint(array(
                    '_attr' => array(
                        'escape' => false,
                    ),
                ));
    ?>
    <div class="table-responsive">
        <?php 
                if(!empty($values)){
        ?>
        <table id="tt" class="table table-bordered" singleSelect="true">
            <thead frozen="true">
                <tr>
                    <?php
                            if( !empty($fieldColumn) ) {
                                echo $fieldColumn;
                            }
                    ?>
                </tr>
            </thead>
            <?php 
                    echo $this->Html->tag('tbody', $this->element($element));
            ?>
        </table>
        <?php 
                } else {
                    echo $this->Html->tag('p', __('Data belum tersedia.'), array(
                        'class' => 'alert alert-warning text-center',
                    ));
                }
        ?>
    </div><!-- /.box-body -->
    <?php 
            echo $this->Html->tag('div', sprintf(__('Printed on : %s, by : %s'), date('d F Y'), $this->Html->tag('span', $full_name)), array(
                'style' => 'font-size: 14px;font-style: italic;margin-top: 10px;'
            ));
            echo $this->Html->tag('div', $this->element('pagination'), array(
                'class' => 'pagination-report'
            ));
    ?>
</div>
<?php 
        }
?>