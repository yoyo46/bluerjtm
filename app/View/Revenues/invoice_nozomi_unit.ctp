<?php 
        if( empty($action_print) || $action_print == 'excel' ){
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

        $element = 'blocks/revenues/tables/invoice_nozomi_unit';
        $widthColumn5 = '';
        $widthColumn8 = '';
        $widthColumn10 = '';
        $widthColumn15 = '';
        $no_invoice = $this->Common->filterEmptyField($value, 'Invoice', 'no_invoice');
        
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
                'style' => 'text-align: center;vertical-align: middle;'.$widthColumn5.'border-left: 1px solid #000;border-bottom: 1px solid #000;'.$background,
            ),
            'nopol' => array(
                'name' => __('No<br>Truk'),
                'style' => 'text-align: center;vertical-align: middle;border-bottom: 1px solid #000;border-left: 1px solid #000;'.$widthColumn15.$background,
            ),
            'jenis_truck' => array(
                'name' => __('Jenis Truk'),
                'style' => 'text-align: center;vertical-align: middle;border-bottom: 1px solid #000;border-left: 1px solid #000;width: 20%;'.$background,
            ),
            'date' => array(
                'name' => __('Tanggal'),
                'style' => 'text-align: center;vertical-align: middle;border-bottom: 1px solid #000;border-left: 1px solid #000;'.$widthColumn10.$background,
            ),
            'jml' => array(
                'name' => __('Unit'),
                'style' => 'text-align: center;vertical-align: middle;border-bottom: 1px solid #000;border-left: 1px solid #000;'.$background,
            ),
            'rate' => array(
                'name' => __('Tarif'),
                'style' => 'text-align: center;vertical-align: middle;border-bottom: 1px solid #000;border-left: 1px solid #000;width: 12%;'.$background,
            ),
            'amount' => array(
                'name' => __('Jumlah<br>(Rp.)'),
                'style' => 'text-align: center;vertical-align: middle;border-bottom: 1px solid #000;border-left: 1px solid #000;width: 12%;'.$background,
            ),
            'note' => array(
                'name' => __('Tujuan'),
                'style' => 'text-align: center;vertical-align: middle;border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;'.$background,
            ),
        );

        if( !empty($action_print) ){
            $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

            echo $this->element(sprintf('blocks/common/tables/export_%s', $action_print), array(
                'tableHead' => $fieldColumn,
                'tableBody' => $this->element($element),
                'sub_module_title' => $sub_module_title,
                'noHeader' => true,
                'topHeader' => $this->element('blocks/revenues/tables/header_report_nozomi'),
                'contentHeader' => $this->Html->tag('div', $this->Html->tag('p', $no_invoice, array(
                    'style' => 'font-size: 14px;margin: 0 0 5px;line-height: 20px;font-weight: 600;text-transform: uppercase;',
                )), array(
                    'style' => 'sub-title" style="margin-bottom: 20px;',
                )),
            ));
        } else {
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
                echo $this->element('blocks/revenues/tables/header_report_nozomi');
                echo $this->Html->tag('div', $this->Html->tag('p', $no_invoice, array(
                    'style' => 'font-size: 14px;margin: 0 0 5px;line-height: 20px;font-weight: 600;text-transform: uppercase;',
                )), array(
                    'style' => 'sub-title" style="margin-bottom: 20px;',
                ));
        ?>
        <table class="table uppercase" id="yamaha-unit" style="border-top: 1px solid #000;">
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