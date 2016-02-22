<?php 
        $dataColumns = array(
            'nodoc' => array(
                'name' => __('No Dokumen'),
            ),
            'name' => array(
                'name' => __('Diterima/Dibayar kepada'),
            ),
            'date' => array(
                'name' => __('Tgl Kas/Bank'),
            ),
            'description' => array(
                'name' => __('Keterangan'),
            ),
            'total' => array(
                'name' => __('Total'),
            ),
        );
        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

        echo $this->Form->create('Search', array(
            'url'=> $this->Html->url( array(
                'controller' => 'ajax',
                'action' => 'search',
                'getCashBankPrepayment',
            )), 
            'role' => 'form',
            'inputDefaults' => array('div' => false),
        ));
?>
<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <?php 
                    echo $this->Form->input('date',array(
                        'label'=> __('Tanggal'),
                        'class'=>'form-control date-range',
                        'required' => false,
                    ));
            ?>
        </div>
        <div class="form-group">
            <?php 
                echo $this->Form->input('name',array(
                    'label'=> __('Diterima/Dibayar kepada'),
                    'class'=>'form-control',
                    'required' => false,
                ));
            ?>
        </div>
        <div class="form-group action">
            <?php
                    echo $this->Form->button('<i class="fa fa-search"></i> '.__('Cari'), array(
                        'div' => false, 
                        'class'=> 'btn btn-success btn-sm ajaxModal',
                        'data-action' => $data_action,
                        'data-parent' => true,
                        'title' => $title,
                    ));
                    echo $this->Html->link('<i class="fa fa-refresh"></i> '.__('Reset'), array(
                        'controller' => 'ajax',
                        'action' => 'getCashBankPrepayment',
                    ), array(
                        'escape' => false, 
                        'class'=> 'btn btn-default btn-sm ajaxModal',
                        'data-action' => $data_action,
                        'title' => __('Prepayment IN'),
                    ));
            ?>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <?php 
                echo $this->Form->input('nodoc',array(
                    'label'=> __('No. Dokumen'),
                    'class'=>'form-control on-focus',
                    'required' => false,
                    'placeholder' => __('No. Dokumen')
                ));
            ?>
        </div>
        <div class="form-group">
            <?php 
                echo $this->Form->input('note',array(
                    'type' => 'text',
                    'label'=> __('Keterangan'),
                    'class'=>'form-control',
                    'required' => false,
                ));
            ?>
        </div>
    </div>
</div>
<?php 
        echo $this->Form->end();
?>
<div class="box-body table-responsive browse-form">
    <table class="table table-hover">
        <?php
                if( !empty($fieldColumn) ) {
                    echo $this->Html->tag('thead', $this->Html->tag('tr', $fieldColumn));
                }
                
                if(!empty($cashBanks)){
                    foreach ($cashBanks as $key => $value) {
                        $id = $this->Common->filterEmptyField($value, 'CashBank', 'id');
                        $nodoc = $this->Common->filterEmptyField($value, 'CashBank', 'nodoc');
                        $name = $this->Common->filterEmptyField($value, 'name_cash', false, '-');
                        $tgl_cash_bank = $this->Common->filterEmptyField($value, 'CashBank', 'tgl_cash_bank');
                        $description = $this->Common->filterEmptyField($value, 'CashBank', 'description');
                        $grand_total = $this->Common->filterEmptyField($value, 'CashBank', 'grand_total');

                        $customDate = $this->Common->customDate($tgl_cash_bank, 'd/m/Y');
                        $customGrandTotal = $this->Common->getFormatPrice($grand_total);

                        $content = $this->Html->tag('td', $nodoc);
                        $content .= $this->Html->tag('td', $name);
                        $content .= $this->Html->tag('td', $customDate);
                        $content .= $this->Html->tag('td', $description);
                        $content .= $this->Html->tag('td', $customGrandTotal);

                        echo $this->Html->tag('tr', $content, array(
                            'data-value' => $id,
                            'data-change' => '#'.$data_change,
                        ));
                    }
                }else{
                    $content = $this->Html->tag('td', __('Data tidak ditemukan.'), array(
                        'colspan' => 6,
                        'class' => 'alert alert-danger'
                    ));
                    echo $this->Html->tag('tr', $content);
                }
        ?>
    </table>
</div><!-- /.box-body -->
<?php
        echo $this->element('pagination', array(
            'options' => array(
                'data-action' => $data_action,
                'class' => 'ajaxModal',
            ),
        ));
?>