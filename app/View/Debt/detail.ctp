<?php
        $this->Html->addCrumb($module_title, array(
            'action' => 'index'
        ));
        $this->Html->addCrumb($sub_module_title);
        $id = Common::hashEmptyField($value, 'Debt.id');
        $nodoc = Common::hashEmptyField($value, 'Debt.nodoc');
        $tgl = Common::hashEmptyField($value, 'Debt.transaction_date');
        $note = Common::hashEmptyField($value, 'Debt.note', '-');
        $grand_total = Common::hashEmptyField($value, 'Debt.total', 0);
        $transaction_status = Common::hashEmptyField($value, 'Debt.transaction_status');
        
        $cogs_name = Common::hashEmptyField($value, 'Cogs.cogs_name', '-');
        $coa_name = Common::hashEmptyField($value, 'Coa.coa_name', '-');

        $customDate = $this->Common->formatDate($tgl, 'd M Y');
        $customTotal = $this->Common->getFormatPrice($grand_total, false, 2);
        $customStatus = $this->Common->_callTransactionStatus($value, 'Debt', 'transaction_status');
        $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
?>
<div class="row">
    <div class="col-sm-6">
        <div class="box">
            <div class="box-header">
                <?php 
                        echo $this->Html->tag('h3', __('Informasi Hutang Karyawan'), array(
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

                    <dt><?php echo __('Account Kas/Bank')?></dt>
                    <dd><?php echo $coa_name;?></dd>

                    <dt><?php echo __('Cost Center')?></dt>
                    <dd><?php echo $cogs_name;?></dd>

                    <dt><?php echo __('Tgl')?></dt>
                    <dd><?php echo $customDate;?></dd>

                    <dt><?php echo __('Keterangan')?></dt>
                    <dd><?php echo $note;?></dd>
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
                        echo $this->Html->tag('h3', __('Informasi Karyawan'), array(
                            'class' => 'box-title',
                        ));
                ?>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <?php 
                                    echo $this->Html->tag('th', __('Karyawan'));
                                    echo $this->Html->tag('th', __('Kategori'));
                                    echo $this->Html->tag('th', __('Ket.'));
                                    echo $this->Html->tag('th', __('Total'), array(
                                        'class' => 'text-center'
                                    ));
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                                if(!empty($value['DebtDetail'])){
                                    $grand_total = 0;

                                    foreach ($value['DebtDetail'] as $key => $value) {
                                        $name = Common::hashEmptyField($value, 'ViewStaff.full_name', '-');
                                        $type = Common::hashEmptyField($value, 'ViewStaff.type', '-');
                                        $note = Common::hashEmptyField($value, 'DebtDetail.note');
                                        $total = Common::hashEmptyField($value, 'DebtDetail.total');

                                        $customTotal = $this->Common->getFormatPrice($total, false, 2);
                                        $grand_total += $total;
                        ?>
                        <tr>
                            <?php
                                    echo $this->Html->tag('td', $name);
                                    echo $this->Html->tag('td', $type);
                                    echo $this->Html->tag('td', $note);
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
                                        $customGrandTotal = $this->Common->getFormatPrice($grand_total, false, 2);
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
        echo $this->Html->tag('div', $this->Html->link(__('Kembali'), array(
            'action' => 'index',
        ), array(
            'class'=> 'btn btn-default',
        )), array(
            'class'=> 'box-footer text-center action',
        ));
?>