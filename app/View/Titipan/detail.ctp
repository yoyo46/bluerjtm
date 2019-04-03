<?php
        $this->Html->addCrumb($module_title, array(
            'action' => 'index'
        ));
        $this->Html->addCrumb($sub_module_title);
        $id = Common::hashEmptyField($value, 'Titipan.id');
        $nodoc = Common::hashEmptyField($value, 'Titipan.nodoc');
        $tgl = Common::hashEmptyField($value, 'Titipan.transaction_date');
        $note = Common::hashEmptyField($value, 'Titipan.note', '-');
        $grand_total = Common::hashEmptyField($value, 'Titipan.total', 0);
        $transaction_status = Common::hashEmptyField($value, 'Titipan.transaction_status');
        $coa_name = Common::hashEmptyField($value, 'Coa.coa_name', '-');
        
        $customDate = $this->Common->formatDate($tgl, 'd M Y');
        $customTotal = $this->Common->getFormatPrice($grand_total, false, 2);
        $customStatus = $this->Common->_callTransactionStatus($value, 'Titipan', 'transaction_status');
        $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
?>
<div class="row">
    <div class="col-sm-6">
        <div class="box">
            <div class="box-header">
                <?php 
                        echo $this->Html->tag('h3', __('Informasi Titipan'), array(
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
                        echo $this->Html->tag('h3', __('Informasi Supir'), array(
                            'class' => 'box-title',
                        ));
                ?>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <?php 
                                    echo $this->Html->tag('th', __('Supir'));
                                    echo $this->Html->tag('th', __('Ket.'));
                                    echo $this->Html->tag('th', __('Total'), array(
                                        'class' => 'text-center'
                                    ));
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                                if(!empty($value['TitipanDetail'])){
                                    $grand_total = 0;

                                    foreach ($value['TitipanDetail'] as $key => $value) {
                                        $name = Common::hashEmptyField($value, 'Driver.driver_name', '-');
                                        $note = Common::hashEmptyField($value, 'TitipanDetail.note');
                                        $total = Common::hashEmptyField($value, 'TitipanDetail.total');

                                        $customTotal = $this->Common->getFormatPrice($total, false, 2);
                                        $grand_total += $total;
                        ?>
                        <tr>
                            <?php
                                    echo $this->Html->tag('td', $name);
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
                            <td align="right" colspan="2" style="font-weight: bold;">Total</td>
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