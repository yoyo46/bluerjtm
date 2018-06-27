<?php 
        if( !empty($data_action) ){
            $headerRowspan = 2;
        } else {
            $headerRowspan = false;
        }

        $full_name = !empty($User['Employe']['full_name'])?$User['Employe']['full_name']:false;
        $dataColumns = array(
            'coa_name' => array(
                'name' => __('Nama Rekening'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'coa_name\',width:300,styler:cellStyler',
                'align' => 'left',
                'rowspan' => $headerRowspan,
            ),
        );

        if( !empty($dateFrom) && !empty($dateTo) ) {
            $tmpDateFrom = $dateFrom;
            $tmpDateTo = $dateTo;

            while( $tmpDateFrom <= $tmpDateTo ) {
                $fieldName = sprintf('month_%s', $tmpDateFrom);
                $dataColumns[$fieldName] = array(
                    'name' => sprintf('%s %s', $this->Common->formatDate($tmpDateFrom, 'M'), $this->Common->formatDate($tmpDateFrom, 'Y')),
                    'style' => 'text-align: right;vertical-align: middle;',
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
    <div class="row">
        <div class="col-sm-6">
            <?php 
                    echo $this->Html->tag('tbody', $this->element('blocks/cashbanks/tables/balance_sheets_col', array(
                        'values' => $debits,
                        'dataColumns' => $dataColumns,
                        'coa_type' => 'debit',
                    )));
            ?>
        </div>
        <div class="col-sm-6">
            <?php 
                    echo $this->Html->tag('tbody', $this->element('blocks/cashbanks/tables/balance_sheets_col', array(
                        'values' => $credits,
                        'dataColumns' => $dataColumns,
                        'coa_type' => 'credit',
                    )));
            ?>
        </div>
    </div>
    <?php 
            echo $this->Html->tag('div', sprintf(__('Printed on : %s, by : %s'), date('d F Y'), $this->Html->tag('span', $full_name)), array(
                'style' => 'font-size: 14px;font-style: italic;margin-top: 10px;'
            ));
    ?>
</div>
<?php 
        }
?>