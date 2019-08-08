<?php
        $element = 'blocks/cashbanks/tables/journal_rinci_report';
        $dataColumns = array(
            'noref' => array(
                'name' => __('No. Ref'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'noref\',width:100',
            ),
            'date' => array(
                'name' => __('Tgl'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'date\',width:100',
            ),
            'nodoc' => array(
                'name' => __('No. Dokumen'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'nodoc\',width:150',
                'fix_column' => true,
            ),
            'type' => array(
                'name' => __('Tipe Kas'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'type\',width:150',
            ),
            'title' => array(
                'name' => __('Title'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'title\',width:250',
            ),
            'desc' => array(
                'name' => __('Keterangan'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'desc\',width:250',
            ),
            'debit' => array(
                'name' => __('Debit'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'debit\',width:120',
                'align' => 'right',
                'mainalign' => 'center',
            ),
            'credit' => array(
                'name' => __('Kredit'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'credit\',width:120',
                'align' => 'right',
                'mainalign' => 'center',
            ),
            'job' => array(
                'name' => __('Job'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'job\',width:120',
            ),
        );

        if( !empty($data_action) ){
            $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table', true );

            echo $this->element(sprintf('blocks/common/tables/export_%s', $data_action), array(
                'tableHead' => $fieldColumn,
                'tableBody' => $this->element($element),
                'sub_module_title' => $module_title,
            ));
        } else {
            $dataColumns['date']['style'] = 'text-align: center;width: 10%;';
            $dataColumns['date']['title'] = 'text-align: left;width: 15%;';
            $dataColumns['desc']['style'] = 'text-align: left;width: 30%;';
            $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

            echo $this->element('blocks/cashbanks/searchs/journal_rinci_report');
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
        <table id="tt" class="table table-bordered easyui-datagrid" style="width: 100%;height: 550px;" singleSelect="true">
            <?php
                    if( !empty($fieldColumn) ) {
                        echo $this->Html->tag('thead', $this->Html->tag('tr', $fieldColumn), array(
                            'frozen' => 'true',
                        ));
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