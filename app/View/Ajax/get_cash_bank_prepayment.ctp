<?php 
        echo $this->Form->create('CashBank', array(
            'url'=> $this->Html->url( array(
                'controller' => 'ajax',
                'action' => 'getCashBankPrepayment',
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
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <?php 
                echo $this->Form->input('nodoc',array(
                    'label'=> __('No. Dokumen'),
                    'class'=>'form-control',
                    'required' => false,
                    'placeholder' => __('No. Dokumen')
                ));
            ?>
        </div>
    </div>
    <div class="col-sm-6">
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
                        'title' => $title,
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
        <thead>
            <tr>
                <th>No Dokumen</th>
                <th>Diterima/Dibayar kepada</th>
                <th>Tgl Kas/Bank</th>
            </tr>
        </thead>
        <?php
            if(!empty($cashBanks)){
                foreach ($cashBanks as $key => $value) {
                    $content = $this->Html->tag('td', $value['CashBank']['nodoc']);
                    $content .= $this->Html->tag('td', !empty($value['name_cash']) ? $value['name_cash'] : '-');
                    $content .= $this->Html->tag('td', $this->Common->customDate($value['CashBank']['tgl_cash_bank'], 'd/m/Y'));

                    echo $this->Html->tag('tr', $content, array(
                        'data-value' => $value['CashBank']['id'],
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