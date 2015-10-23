<?php
        if(!empty($user_otorisasi_approvals)){
?>
<div id="list-approval">
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
                        <th><?php echo __('Posisi yang menyetujui');?></th>
                        <th class="text-center"><?php echo __('Prioritas Approval');?></th>
                        <th class="text-center"><?php echo __('Status');?></th>
                        <th><?php echo __('Keterangan');?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                            foreach ($user_otorisasi_approvals as $key => $value) {
                                $position_name = $this->Common->filterEmptyField($value, 'EmployePosition', 'name');
                                $is_priority = $this->Common->filterEmptyField($value, 'ApprovalDetailPosition', 'is_priority');
                                $description = $this->Common->filterEmptyField($value, 'CashBankAuth', 'description', '-');
                                $status_document = $this->CashBank->_callStatusAuth($value);

                                if( !empty($is_priority) ) {
                                    $labelCheck = $this->Common->icon('check', false, 'i', 'text-green');
                                } else {
                                    $labelCheck = $this->Common->icon('times', false, 'i', 'text-red');
                                }
                    ?>
                    <tr>
                        <?php 
                                echo $this->Html->tag('td', $position_name);
                                echo $this->Html->tag('td', $labelCheck, array(
                                    'class' => 'text-center',
                                ));
                                echo $this->Html->tag('td', $status_document, array(
                                    'class' => 'text-center',
                                ));
                                echo $this->Html->tag('td', $description);
                        ?>
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
?>