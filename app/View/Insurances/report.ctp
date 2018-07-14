<?php 
        $element = 'blocks/insurances/tables/report';
        $full_name = !empty($User['Employe']['full_name'])?$User['Employe']['full_name']:false;
        $dataColumns = array(
            'nodoc' => array(
                'name' => __('No. Polis'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'no_contract\',width:100',
                'align' => 'center',
            ),
            'name' => array(
                'name' => __('Nama Asuransi'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'name\',width:120',
                'align' => 'center',
            ),
            'start_date' => array(
                'name' => __('Tgl Asuransi'),
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'start_date\',width:100',
                'align' => 'center',
                // 'fix_column' => true,
            ),
            'to_name' => array(
                'name' => __('Nama Tertanggung'),
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'to_name\',width:150',
                'align' => 'center',
            ),
            'status' => array(
                'name' => __('Status'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'status\',width:150',
                'align' => 'center',
            ),
            'status_paid' => array(
                'name' => __('Pembayaran'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'status_paid\',width:150',
                'align' => 'center',
            ),
            'grandtotal' => array(
                'name' => __('Total'),
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'grandtotal\',width:120',
                'align' => 'right',
            ),
            'payment' => array(
                'name' => __('Total Pembayaran'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'payment\'',
                'align' => 'center',
            ),
            'sisa' => array(
                'name' => __('Sisa'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'sisa\'',
                'align' => 'center',
            ),
        );

        if( !empty($data_action) ){
            $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table', $data_action );

            echo $this->element(sprintf('blocks/common/tables/export_%s', $data_action), array(
                'tableHead' => $fieldColumn,
                'tableBody' => $this->element($element),
                'sub_module_title' => $module_title,
                'contentTr' => false,
            ));
        } else {
            $this->Html->addCrumb($sub_module_title);

            $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

            echo $this->element('blocks/insurances/searchs/report');
?>
<section class="content invoice">
    <h2 class="page-header">
        <i class="fa fa-globe"></i> <?php echo $sub_module_title;?>
    </h2>
    <?php 
            echo $this->Common->_getPrint(array(
                '_attr' => array(
                    'escape' => false,
                    'class' => 'ajaxLink',
                    'data-form' => '#form-search',
                ),
                '_ajax' => true,
                'url_excel' => array(
                    'controller' => 'reports',
                    'action' => 'generate_excel',
                    'insurances_report',
                ),
            ));
    ?>
    <div class="table-responsive">
        <?php 
                if(!empty($values)){
        ?>
        <table id="tt" class="table table-bordered sorting" singleSelect="true">
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