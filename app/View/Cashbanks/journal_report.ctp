<?php
        $element = 'blocks/cashbanks/tables/journal_report';
        $dataColumns = array(
            'noref' => array(
                'name' => __('No. Ref'),
                'style' => 'text-align: left;',
            ),
            'date' => array(
                'name' => __('Tgl'),
                'style' => 'text-align: center;',
            ),
            'desc' => array(
                'name' => __('Keterangan'),
                'style' => 'text-align: center;',
            ),
            'debit' => array(
                'name' => __('Debit'),
                'style' => 'text-align: right;',
            ),
            'credit' => array(
                'name' => __('Kredit'),
                'style' => 'text-align: right;',
            ),
            'job' => array(
                'name' => __('Job'),
                'style' => 'text-align: center;',
            ),
        );

        if( !empty($data_action) ){
            $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

            echo $this->element(sprintf('blocks/common/tables/export_%s', $data_action), array(
                'tableHead' => $fieldColumn,
                'tableBody' => $this->element($element),
                'sub_module_title' => $module_title,
            ));
        } else {
            $dataColumns['date']['style'] = 'text-align: center;width: 15%;';
            $dataColumns['desc']['style'] = 'text-align: left;width: 40%;';
            $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

            echo $this->element('blocks/cashbanks/searchs/journal_report');
        	$this->Html->addCrumb($sub_module_title);
?>
<section class="content invoice">
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
    <?php echo $this->element('pagination');?>
</section>
<?php 
        }
?>