<?php
        $this->Html->addCrumb(__('Kas/Bank'), array(
            'action' => 'index'
        ));
        $this->Html->addCrumb($sub_module_title);
        $receiving_cash_type = !empty($cashbank['CashBank']['receiving_cash_type'])?$cashbank['CashBank']['receiving_cash_type']:false;
?>
<div class="row">
    <div class="col-sm-6">
        <div class="box box-primary">
            <div class="box-header">
                <?php 
                        echo $this->Html->tag('h3', __('Informasi Kas/Bank'), array(
                            'class' => 'box-title',
                        ));
                ?>
            </div>
            <div class="box-body">
                <dl class="dl-horizontal">
                    <dt><?php echo __('No. Dokumen')?></dt>
                    <dd><?php echo $cashbank['CashBank']['nodoc'];?></dd>
                    <?php 
                            switch ($receiving_cash_type) {
                                case 'ppn_in':
                                    if( !empty($cashbank['Revenue']) ) {
                                        echo $this->Html->tag('dt', __('No. Ref Revenue'));
                                        echo $this->Html->tag('dd', str_pad($cashbank['Revenue']['id'], 5, '0', STR_PAD_LEFT));
                                    }
                                    break;
                                
                                default:
                                    # code...
                                    break;
                            }
                    ?>
                    <dt><?php echo __('Tipe Kas')?></dt>
                    <dd><?php echo strtoupper(str_replace('_', ' ', $receiving_cash_type));?></dd>
                    <dt>
                        <?php 
                                $text = __('Diterima dari');
                                if(!empty($receiving_cash_type) && $receiving_cash_type == 'out'){
                                    $text = __('Dibayar kepada');
                                }
                                echo $text;
                        ?>
                    </dt>
                    <dd><?php echo !empty($cashbank['CashBank']['receiver'])?$cashbank['CashBank']['receiver']:false;?></dd>
                    <dt><?php echo __('Tanggal Kas/Bank')?></dt>
                    <dd><?php echo $this->Common->customDate($cashbank['CashBank']['tgl_cash_bank'], 'd/m/Y');?></dd>
                    <dt><?php echo __('Keterangan')?></dt>
                    <dd><?php echo !empty($cashbank['CashBank']['description'])?$cashbank['CashBank']['description']:'-';?></dd>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="box box-success">
            <div class="box-header">
                <?php 
                        echo $this->Html->tag('h3', __('Informasi COA'), array(
                            'class' => 'box-title',
                        ));
                ?>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th><?php echo __('Kode Acc');?></th>
                            <th><?php echo __('Nama Acc');?></th>
                            <?php 
                                // echo $this->Html->tag('th', __('Debit'), array(
                                //     'class' => 'text-center'
                                // ));
                                // echo $this->Html->tag('th', __('Kredit'), array(
                                //     'class' => 'text-center'
                                // ));

                                echo $this->Html->tag('th', __('Total'), array(
                                    'class' => 'text-center'
                                ));
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            if(!empty($cashbank['CashBankDetail'])){
                                foreach ($cashbank['CashBankDetail'] as $key => $value) {
                        ?>
                        <tr>
                            <td>
                                <?php
                                    if(!empty($value['Coa']['code'])){
                                        echo $value['Coa']['code'];
                                    }else{
                                        echo '-';
                                    }
                                ?>
                            </td>
                            <td>
                                <?php
                                    if(!empty($value['Coa']['name'])){
                                        echo $value['Coa']['name'];
                                    }else{
                                        echo '-';
                                    }
                                ?>
                            </td>
                            <?php
                                    // $debit = $this->Number->currency($value['debit'], Configure::read('__Site.config_currency_code'), array('places' => 0));

                                    // echo $this->Html->tag('td', $debit, array(
                                    //     'align' => 'right'
                                    // ));

                                    // $credit = $this->Number->currency($value['credit'], Configure::read('__Site.config_currency_code'), array('places' => 0));
                                    // echo $this->Html->tag('td', $debit, array(
                                    //     'align' => 'right'
                                    // ));

                                    $total = $this->Number->currency($value['total'], Configure::read('__Site.config_currency_code'), array('places' => 0));
                                    echo $this->Html->tag('td', $total, array(
                                        'align' => 'right'
                                    ));
                            ?>
                        </tr>
                        <?php
                                }
                            }
                        ?>
                        <tr>
                            <td align="right" colspan="2" style="font-weight: bold;">Total</td>
                            <td align="right" style="font-weight: bold;">
                                <?php
                                    $total = 0;
                                    if(!empty($cashbank['CashBank']['debit_total'])){
                                        $total = $cashbank['CashBank']['debit_total'];
                                    }else{
                                        $total = $cashbank['CashBank']['credit_total'];
                                    }

                                    echo $this->Number->currency($total, Configure::read('__Site.config_currency_code'), array('places' => 0));
                                ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php
        if(!empty($cash_bank_auth_master)){
    ?>
    <div class="col-sm-12" id="list-approval">
        <div class="box box-success">
            <div class="box-header">
                <?php 
                        echo $this->Html->tag('h3', __('Approval Kas/Bank'), array(
                            'class' => 'box-title',
                        ));
                ?>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th><?php echo __('Nama');?></th>
                            <th><?php echo __('Group');?></th>
                            <th width="10%" class="text-center"><?php echo __('level Otorisasi');?></th>
                            <th class="text-center"><?php echo __('Status');?></th>
                            <th><?php echo __('Keterangan');?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach ($cash_bank_auth_master as $key => $value) {
                        ?>
                        <tr>
                            <td><?php echo $value['User']['full_name']?></td>
                            <td><?php echo $value['User']['Group']['name']?></td>
                            <td class="text-center"><?php echo $value['CashBankAuthMaster']['level']?></td>
                            <td class="text-center"><?php echo !empty($value['CashBankAuth']['status_document']) ? ucfirst($value['CashBankAuth']['status_document']) : '-';?></td>
                            <td><?php echo !empty($value['CashBankAuth']['description']) ? $value['CashBankAuth']['description'] : '-';?></td>
                        </tr>
                        <?php
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php
            }

            if( in_array($User['id'], $cashbank_auth_id) && !$cashbank['CashBank']['completed'] && empty($cashBankAuth) ){
    ?>
    <div class="col-sm-12">
        <div class="box box-success">
            <div class="box-header">
                <?php 
                        echo $this->Html->tag('h3', __('Approval Form'), array(
                            'class' => 'box-title',
                        ));
                ?>
            </div>
            <div class="box-body">
                <?php
                    echo $this->Form->create('CashBankAuth', array(
                        'url'=> $this->Html->url( null, true ), 
                        'role' => 'form',
                        'inputDefaults' => array('div' => false),
                    ));
                    echo $this->Form->input('status_document', array(
                        'label' => __('Status Approval *'),
                        'div' => array(
                            'class' => 'form-group'
                        ),
                        'options' => array(
                            // 'pending' => 'Pending',
                            'approve' => 'Setujui',
                            'revise' => 'Direvisi',
                            'reject' => 'Tidak Setuju',
                        ),
                        'class' => 'form-control',
                        'empty' => __('Pilih Status Approval'),
                    ));

                    echo $this->Form->input('cash_bank_auth_master_id', array(
                        'type' => 'hidden',
                        'value' => $cash_bank_master_user['CashBankAuthMaster']['id']
                    ));

                    echo $this->Form->input('description', array(
                        'label' => __('Keterangan'),
                        'div' => array(
                            'class' => 'form-group'
                        ),
                        'type' => 'textarea',
                        'class' => 'form-control'
                    ));
                ?>
                <div class="box-footer text-center action">
                    <?php
                            echo $this->Form->button(__('Simpan'), array(
                                'div' => false, 
                                'class'=> 'btn btn-success',
                                'type' => 'submit',
                            ));
                            echo $this->Html->link(__('Kembali'), array(
                                'action' => 'index', 
                            ), array(
                                'class'=> 'btn btn-default',
                            ));
                    ?>
                </div>
                <?php

                    echo $this->Form->end();
                ?>
            </div>
        </div>
    </div>
    <?php
                $statusApproval = true;
            }
    ?>
</div>
<?php 
        if( empty($statusApproval) ){
            echo $this->Html->tag('div', $this->Html->link(__('Kembali'), array(
                'action' => 'index', 
            ), array(
                'class'=> 'btn btn-default',
            )), array(
                'class'=> 'box-footer text-center action',
            ));
        }
?>