<?php
        $this->Html->addCrumb(__('Kas/Bank'), array(
            'action' => 'index'
        ));
        $this->Html->addCrumb($sub_module_title);
        $id = $this->Common->filterEmptyField($cashbank, 'CashBank', 'id');
        $nodoc = $this->Common->filterEmptyField($cashbank, 'CashBank', 'nodoc');
        $receiver = $this->Common->filterEmptyField($cashbank, 'CashBank', 'receiver');
        $tgl = $this->Common->filterEmptyField($cashbank, 'CashBank', 'tgl_cash_bank');
        $type = $this->Common->filterEmptyField($cashbank, 'CashBank', 'receiving_cash_type');
        $description = $this->Common->filterEmptyField($cashbank, 'CashBank', 'description', '-');
        $grand_total = $this->Common->filterEmptyField($cashbank, 'CashBank', 'grand_total', 0);
        $completed = $this->Common->filterEmptyField($cashbank, 'CashBank', 'completed');
        
        $coa_name = $this->Common->filterEmptyField($cashbank, 'Coa', 'coa_name', '-');

        $revenue_id = $this->Common->filterEmptyField($cashbank, 'Revenue', 'id');
        $customDate = $this->Common->formatDate($tgl, 'd/m/Y');
        $customTotal = $this->Common->getFormatPrice($grand_total);
        $customStatus = $this->CashBank->_callStatus($cashbank);
        $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
?>
<div class="row">
    <div class="col-sm-6">
        <div class="box">
            <div class="box-header">
                <?php 
                        echo $this->Html->tag('h3', __('Informasi Kas/Bank'), array(
                            'class' => 'box-title',
                        ));
                ?>
            </div>
            <div class="box-body">
                <dl class="dl-horizontal">
                    <dt><?php echo __('No. Referensi')?></dt>
                    <dd><?php echo $noref;?></dd>
                    <dt><?php echo __('No. Dokumen')?></dt>
                    <dd><?php echo $nodoc;?></dd>
                    <?php 
                            switch ($type) {
                                case 'ppn_in':
                                    if( !empty($revenue_id) ) {
                                        echo $this->Html->tag('dt', __('No. Ref Revenue'));
                                        echo $this->Html->tag('dd', str_pad($revenue_id, 5, '0', STR_PAD_LEFT));
                                    }
                                    break;
                                
                                default:
                                    # code...
                                    break;
                            }
                    ?>
                    <dt><?php echo __('Tipe Kas')?></dt>
                    <dd><?php echo strtoupper(str_replace('_', ' ', $type));?></dd>
                    <dt>
                        <?php 
                                $text = __('Diterima dari');
                                if( $type == 'out' ){
                                    $text = __('Dibayar kepada');
                                }
                                echo $text;
                        ?>
                    </dt>
                    <dd><?php echo $receiver;?></dd>

                    <dt><?php echo __('Account Kas/Bank')?></dt>
                    <dd><?php echo $coa_name;?></dd>

                    <dt><?php echo __('Tgl Kas/Bank')?></dt>
                    <dd><?php echo $customDate;?></dd>

                    <dt><?php echo __('Keterangan')?></dt>
                    <dd><?php echo $description;?></dd>
                    <dt><?php echo __('Status')?></dt>
                    <dd>
                        <?php
                                echo $customStatus;
                        ?>
                    </dd>
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
                            <?php 
                                    echo $this->Html->tag('th', __('Kode Acc'));
                                    echo $this->Html->tag('th', __('Nama Acc'));
                                    echo $this->Html->tag('th', __('Truk'));
                                    echo $this->Html->tag('th', __('Total'), array(
                                        'class' => 'text-center'
                                    ));
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                                if(!empty($cashbank['CashBankDetail'])){
                                    $grand_total = 0;

                                    foreach ($cashbank['CashBankDetail'] as $key => $value) {
                                        $coa_code = $this->Common->filterEmptyField($value, 'Coa', 'code', '-');
                                        $coa_name = $this->Common->filterEmptyField($value, 'Coa', 'name', '-');
                                        $nopol = $this->Common->filterEmptyField($value, 'Truck', 'nopol', '-');
                                        $total = $this->Common->filterEmptyField($value, 'CashBankDetail', 'total');

                                        $customTotal = $this->Common->getFormatPrice($total);
                                        $grand_total += $total;
                        ?>
                        <tr>
                            <?php
                                    echo $this->Html->tag('td', $coa_code);
                                    echo $this->Html->tag('td', $coa_name);
                                    echo $this->Html->tag('td', $nopol);
                                    echo $this->Html->tag('td', $customTotal, array(
                                        'align' => 'right'
                                    ));
                            ?>
                        </tr>
                        <?php
                                    }
                                }
                        ?>
                        <tr>
                            <td align="right" colspan="3" style="font-weight: bold;">Total</td>
                            <td align="right" style="font-weight: bold;">
                                <?php
                                        $customGrandTotal = $this->Common->getFormatPrice($grand_total);
                                        echo $customGrandTotal;
                                ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
        echo $this->element('blocks/cashbanks/tables/list_approvals');
?>