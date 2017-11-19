<?php 
        if( empty($action_print) || $action_print == 'excel' ){
?>
<link href='https://fonts.googleapis.com/css?family=Cookie' rel='stylesheet' type='text/css'>
<link href='https://fonts.googleapis.com/css?family=Carter+One' rel='stylesheet' type='text/css'>
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

        $element = 'blocks/revenues/tables/invoice_yamaha_unit';
        $widthColumn5 = '';
        $widthColumn8 = '';
        $widthColumn10 = '';
        $widthColumn15 = '';
        $background = '';
        
        if( empty($action_print) ){
            $widthColumn5 = 'width: 5%;';
            $widthColumn8 = 'width: 8%;';
            $widthColumn10 = 'width: 10%;';
            $widthColumn15 = 'width: 15%;';
        } else {
            $background = 'background-color: #3C8DBC;color: #FFF;';
        }

        $dataColumns = array(
            'no' => array(
                'name' => __('No.'),
                'style' => 'text-align: center;vertical-align: middle;'.$widthColumn5.'border-left: 1px solid #000;border-bottom: 1px solid #000;'.$background,
            ),
            'nopol' => array(
                'name' => __('No Truck'),
                'style' => 'text-align: center;vertical-align: middle;border-bottom: 1px solid #000;border-left: 1px solid #000;'.$widthColumn15.$background,
            ),
            'capacity' => array(
                'name' => __('Kap'),
                'style' => 'text-align: center;vertical-align: middle;border-bottom: 1px solid #000;border-left: 1px solid #000;'.$widthColumn8.$background,
            ),
            'date' => array(
                'name' => __('Date'),
                'style' => 'text-align: center;vertical-align: middle;border-bottom: 1px solid #000;border-left: 1px solid #000;'.$widthColumn10.$background,
            ),
            'jml' => array(
                'name' => __('Unit'),
                'style' => 'text-align: center;vertical-align: middle;border-bottom: 1px solid #000;border-left: 1px solid #000;'.$background,
            ),
            'rate' => array(
                'name' => __('Rate'),
                'style' => 'text-align: center;vertical-align: middle;border-bottom: 1px solid #000;border-left: 1px solid #000;'.$background,
            ),
            'amount' => array(
                'name' => __('Amount'),
                'style' => 'text-align: center;vertical-align: middle;border-bottom: 1px solid #000;border-left: 1px solid #000;'.$background,
            ),
            'note' => array(
                'name' => __('Ket'),
                'style' => 'text-align: center;vertical-align: middle;border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;'.$background,
                'colspan' => 2,
            ),
        );

        if( !empty($action_print) ){
            $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

            echo $this->element(sprintf('blocks/common/tables/export_%s', $action_print), array(
                'tableHead' => $fieldColumn,
                'tableBody' => $this->element($element),
                'sub_module_title' => __('Faktur Jasa Angkutan'),
                'topHeader' => $this->element('blocks/common/tables/header_report'),
                'contentHeader' => $this->element('blocks/revenues/tables/info_invoice'),
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
                echo $this->element('blocks/common/tables/header_report');
                echo $this->Html->tag('h2', __('Faktur Jasa Angkutan'), array(
                    'style' => 'font-family: \'Cookie\', cursive;text-align: center;font-style: italic;font-size: 32px;margin: 0 0 25px;',
                ));
                echo $this->element('blocks/revenues/tables/info_invoice');
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