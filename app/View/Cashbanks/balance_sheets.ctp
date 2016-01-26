<?php 
        if( !empty($data_action) ){
            $headerRowspan = 2;
        } else {
            $headerRowspan = false;
        }

        $element = 'blocks/cashbanks/tables/balance_sheets';
        $full_name = !empty($User['Employe']['full_name'])?$User['Employe']['full_name']:false;
        $dataColumns = array(
            'coa_name' => array(
                'name' => __('Nama Rekening'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'coa_name\',width:300,styler:cellStyler',
                'align' => 'left',
                'rowspan' => $headerRowspan,
                // 'fix_column' => true,
            ),
        );

        if( !empty($dateFrom) && !empty($dateTo) ) {
            $tmpDateFrom = $dateFrom;
            $tmpDateTo = $dateTo;

            while( $tmpDateFrom <= $tmpDateTo ) {
                $fieldName = sprintf('month_%s', $tmpDateFrom);
                $dataColumns[$fieldName] = array(
                    'name' => sprintf('%s<br>%s', $this->Common->formatDate($tmpDateFrom, 'M'), $this->Common->formatDate($tmpDateFrom, 'Y')),
                    'style' => 'text-align: center;vertical-align: middle;',
                    'data-options' => sprintf('field:\'%s\',width:100', $fieldName),
                    'align' => 'right',
                );

                $tmpDateFrom = date('Y-m', strtotime('+1 Month', strtotime($tmpDateFrom)));
            }
        }

        if( !empty($data_action) ){
            $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table', $data_action );

            echo $this->element(sprintf('blocks/common/tables/export_%s', $data_action), array(
                'tableHead' => $fieldColumn,
                'tableBody' => $this->element($element),
                'sub_module_title' => $module_title,
                'contentTr' => false,
            ));
        } else {
            $this->Html->addCrumb($module_title);

            $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );
            $addStyle = 'width: 100%;height: 550px;';
            $addClass = 'easyui-datagrid';

            echo $this->element('blocks/cashbanks/searchs/balance_sheets');
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
        <table id="tt" class="table sorting <?php echo $addClass; ?>" style="<?php echo $addStyle; ?>" singleSelect="true">
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
    ?>
</div>
<?php 
        }
?>