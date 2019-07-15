<?php
        $element = 'blocks/debt/tables/debt_card';
        $dataColumns = array(
            'no' => array(
                'name' => __('No'),
                'style' => 'text-align: center;',
            ),
            'date' => array(
                'name' => __('Date'),
            ),
            'nodoc' => array(
                'name' => __('No. Dokumen'),
                'style' => 'text-align: left;',
            ),
            'note' => array(
                'name' => __('Ket.'),
                'style' => 'text-align: left;',
            ),
            'debit' => array(
                'name' => __('Debit'),
                'style' => 'text-align: right;',
            ),
            'credit' => array(
                'name' => __('Kredit'),
                'style' => 'text-align: right;',
            ),
            'saldo' => array(
                'name' => __('Balance'),
                'style' => 'text-align: right;',
            ),
        );
        
        if( empty($data_action) ){
            echo $this->element('blocks/debt/search/debt_card');
        }
        
        if( !empty($data_action) ){
            $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

            echo $this->element(sprintf('blocks/common/tables/export_%s', $data_action), array(
                'tableHead' => $fieldColumn,
                'tableBody' => $this->element($element),
                'sub_module_title' => $module_title,
            ));
        } else {
            $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

            $this->Html->addCrumb($module_title);
?>
<section class="content invoice" id="ledger-report">
    <h2 class="page-header">
        <i class="fa fa-globe"></i> <?php echo $module_title;?>
    </h2>
    <?php 
            echo $this->Common->_getPrint();
    ?>
    <div class="table-responsive">
        <table class="table journal table-no-border red" id="journal-report">
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