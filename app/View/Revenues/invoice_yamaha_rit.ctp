<?php 
        if( empty($action_print) || $action_print == 'excel' ){

            if( $action_print != 'excel' ) {
?>
<link href='https://fonts.googleapis.com/css?family=Cookie' rel='stylesheet' type='text/css'>
<link href='https://fonts.googleapis.com/css?family=Carter+One' rel='stylesheet' type='text/css'>
<?php
        }
?>
<style type="text/css">
    body {
        margin: 0;
    }
    
    @media print {
        @page :first {
            margin-top: 0;
        }
    }
</style>
<?php
        }
        
        $widthColumn5 = '';
        $widthColumn8 = '';
        $widthColumn10 = '';
        $widthColumn15 = '';
        
        if( empty($action_print) ){
            $widthColumn5 = 'width: 5%;';
            $widthColumn8 = 'width: 8%;';
            $widthColumn10 = 'width: 10%;';
            $widthColumn15 = 'width: 15%;';
            $background = 'background-color: #ccc;';
        } else {
            $background = 'background-color: #3C8DBC;color: #FFF;';
        }

        $dataColumns = array(
            'no' => array(
                'name' => __('No.'),
                'rowspan' => 2,
                'style' => 'text-align: center;vertical-align: middle;'.$widthColumn5.'border-left: 1px solid #000;border-bottom: 1px solid #000;'.$background,
            ),
            'nopol' => array(
                'name' => __('No Truck'),
                'rowspan' => 2,
                'style' => 'text-align: center;vertical-align: middle;border-bottom: 1px solid #000;border-left: 1px solid #000;'.$widthColumn15.$background,
            ),
            'capacity' => array(
                'name' => __('Kap'),
                'rowspan' => 2,
                'style' => 'text-align: center;vertical-align: middle;border-bottom: 1px solid #000;border-left: 1px solid #000;'.$widthColumn8.$background,
            ),
            'date' => array(
                'name' => __('Tanggal'),
                'rowspan' => 2,
                'style' => 'text-align: center;vertical-align: middle;border-bottom: 1px solid #000;border-left: 1px solid #000;'.$widthColumn10.$background,
            ),
            'jml' => array(
                'name' => __('Jumlah'),
                'style' => 'text-align: center;border-bottom: 1px solid #000;border-left: 1px solid #000;'.$widthColumn8.$background,
                'child' => array(
                    'unit' => array(
                        'name' => __('Unit'),
                        'style' => 'text-align: center;border-bottom: 1px solid #000;border-left: 1px solid #000;'.$background,
                    ),
                ),
            ),
            'jumlah' => array(
                'name' => __('Jumlah'),
                'style' => 'text-align: center;border-bottom: 1px solid #000;border-left: 1px solid #000;'.$widthColumn8.$background,
                'child' => array(
                    'unit' => array(
                        'name' => __('RIT'),
                        'style' => 'text-align: center;border-bottom: 1px solid #000;border-left: 1px solid #000;'.$background,
                    ),
                ),
            ),
            'tarif' => array(
                'name' => __('Tarif'),
                'style' => 'text-align: center;border-bottom: 1px solid #000;border-left: 1px solid #000;'.$widthColumn10.$background,
                'child' => array(
                    'unit' => array(
                        'name' => __('Per RIT'),
                        'style' => 'text-align: center;border-bottom: 1px solid #000;border-left: 1px solid #000;'.$background,
                    ),
                ),
            ),
        );
        $noteColumns = array(
            'name' => __('Ket'),
            'style' => 'text-align: center;vertical-align: middle;border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;'.$background,
            'colspan' => 2,
            'rowspan' => 2,
        );

        if( !empty($action_print) ){
            $tmpDataColumns = $dataColumns;
            $tmpDataColumns['note'] = $noteColumns;

            $element = 'blocks/revenues/tables/invoice_yamaha_rit_excel';
            $fieldColumn = $this->Common->_generateShowHideColumn( $tmpDataColumns, 'field-table' );

            $tableHead = $fieldColumn;
            $tableBody = $this->element($element);
            $sub_module_title = __('Faktur Jasa Angkutan');
            $topHeader = $this->element('blocks/common/tables/header_report');
            $contentHeader = $this->element('blocks/revenues/tables/info_invoice');
?>
<style>
    .string{ mso-number-format:\@; }
</style>
<?php 
        $full_name = !empty($User['Employe']['full_name'])?$User['Employe']['full_name']:false;
        $filename = !empty($filename)?$filename:$sub_module_title;
        $filename = $this->Common->toSlug($filename);
        
        $contentHeader = !empty($contentHeader)?$contentHeader:false;
        $topHeader = !empty($topHeader)?$topHeader:false;
        $noHeader = !empty($noHeader)?$noHeader:false;

        $contentTr = isset($contentTr)?$contentTr:true;
        header('Content-type: application/ms-excel');
        header('Content-Disposition: attachment; filename='.$filename.'.xls');
?>
<section class="content invoice">
    <?php 
            echo $topHeader;
            
            if( !empty($customHeader) ) {
                echo $customHeader;
            } else if( !empty($sub_module_title) && empty($noHeader) ) {
    ?>
    <h2 class="page-header" style="text-align: center;">
        <i class="fa fa-globe"></i> <?php echo $sub_module_title;?>
    </h2>
    <?php 
            }

            echo $contentHeader;

    ?>
    <br>
    <table style="width: 100%;" singleSelect="true" border="1">
        <?php
                if( !empty($tableHead) ) {
                    echo $this->Html->tag('thead', $this->Html->tag('tr', $tableHead));
                }
                if( !empty($tableBody) ) {
                    echo $this->Html->tag('tbody', $tableBody);
                }
        ?>
    </table>

    <?php
            if( !empty($settingInvoiceYamahaRits) ) {
                $tmpExtraTarifColumns = $dataColumns;

                foreach ($settingInvoiceYamahaRits as $key => $settingYamahaRits) {
                    $settingYamahaRitName = Common::hashEmptyField($settingYamahaRits, 'SettingInvoiceYamahaRit.name');
                    $settingYamahaRitPercent = Common::hashEmptyField($settingYamahaRits, 'SettingInvoiceYamahaRit.percent');
                    $settingYamahaRitSlug = Common::toSlug($settingYamahaRitName);

                    $tmpDataColumns = $dataColumns;
                    $tmpExtraTarifColumns['tarif_'.$settingYamahaRitSlug] = $tmpDataColumns['tarif_'.$settingYamahaRitSlug] = array(
                        'name' => __('Tarif'),
                        'style' => 'text-align: center;border-bottom: 1px solid #000;border-left: 1px solid #000;'.$widthColumn10.$background,
                        'child' => array(
                            'unit' => array(
                                'name' => __('%s = %s%%', $settingYamahaRitName, $settingYamahaRitPercent),
                                'style' => 'text-align: center;border-bottom: 1px solid #000;border-left: 1px solid #000;'.$background,
                            ),
                        ),
                    );
                    $tmpDataColumns['note'] = $noteColumns;

                    $fieldColumn = $this->Common->_generateShowHideColumn( $tmpDataColumns, 'field-table' );
                    $tableHead = $fieldColumn;
    ?>
    <br><br>
    <table style="width: 100%;" singleSelect="true" border="1">
        <?php
                if( !empty($fieldColumn) ) {
                    echo $this->Html->tag('thead', $this->Html->tag('tr', $fieldColumn));
                }
                
                echo $this->Html->tag('tbody', $this->element($element, array(
                    'settingYamahaRits' => $settingYamahaRits,
                )));
        ?>
    </table>
    <?php
                }

                if( !empty($tmpExtraTarifColumns) ) {
                    $tmpExtraTarifColumns['note'] = $noteColumns;

                    $fieldColumn = $this->Common->_generateShowHideColumn( $tmpExtraTarifColumns, 'field-table' );
                    $tableHead = $fieldColumn;
    ?>
    <br><br>
    <table style="width: 100%;margin-top: 50px;" singleSelect="true" border="1">
        <?php
                if( !empty($fieldColumn) ) {
                    echo $this->Html->tag('thead', $this->Html->tag('tr', $fieldColumn));
                }
                
                echo $this->Html->tag('tbody', $this->element($element, array(
                    'settingYamahaRits' => $settingInvoiceYamahaRits,
                )));
        ?>
    </table>
    <?php
                }
            }
    ?>
    <?php 
            echo $this->Html->tag('div', sprintf(__('Printed on : %s, by : %s'), date('d F Y'), $this->Html->tag('span', $full_name)), array(
                'style' => 'font-size: 14px;font-style: italic;margin-top: 10px;'
            ));
    ?>
</div>
<?php
        } else {
            $dataColumns['note'] = $noteColumns;

            $element = 'blocks/revenues/tables/invoice_yamaha_rit';
            $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

        	$this->Html->addCrumb($sub_module_title);
?>
<section class="content invoice">
    <h2 class="page-header hidden-print">
        <i class="fa fa-globe"></i> <?php echo $sub_module_title;?>
    </h2>

    <?php 
            if( empty($action_print) ) {
                echo $this->Html->tag('div', $this->Html->link('<i class="fa fa-print"></i> print', 'javascript:', array(
                    'class' => 'btn btn-default hidden-print print-window',
                    'escape' => false
                )), array(
                    'class' => 'action-print pull-right',
                ));
                echo $this->Common->_getPrint();
            }
    ?>
    <div class="clear"></div>

    <div class="table-responsive">
        <?php 
                echo $this->element('blocks/common/tables/header_report');
                echo $this->Html->tag('h2', __('Faktur Jasa Angkutan'), array(
                    'style' => 'font-family: \'Cookie\', cursive;text-align: center;font-style: italic;font-size: 32px;margin: 0 0 25px;',
                ));
                echo $this->element('blocks/revenues/tables/info_invoice');
        ?>
        <table class="table uppercase" id="yamaha-rit" style="border-top: 1px solid #000;">
            <?php
                    if( !empty($fieldColumn) ) {
                        echo $this->Html->tag('thead', $this->Html->tag('tr', $fieldColumn));
                    }
                    
                    echo $this->Html->tag('tbody', $this->element($element));
            ?>
        </table>
    </div><!-- /.box-body -->
</section>
<?php 
        }
?>