<?php
        echo $this->element('blocks/debt/search/get_debt', array(
            'urlForm' => array(
                'controller' => 'debt',
                'action' => 'search',
                'get_debt',
                'payment_id' => $payment_id,
                'bypass' => true,
            ),
            'urlReset' => array(
                'controller' => 'debt',
                'action' => 'get_debt',
                'payment_id' => $payment_id,
                'bypass' => true,
            ),
        ));
?>
<div class="box-body table-responsive browse-form">
    <table class="table table-hover">
        <tr>
            <?php 
                    $input_all = $this->Form->checkbox('checkbox_all', array(
                        'class' => 'checkAll'
                    ));
                    echo $this->Html->tag('th', $input_all);

                    echo $this->Html->tag('th', __('No. Doc'));
                    echo $this->Html->tag('th', __('ID Karyawan'));
                    echo $this->Html->tag('th', __('Karyawan'));
                    echo $this->Html->tag('th', __('Kategori'));
                    echo $this->Html->tag('th', __('Ket.'));
                    echo $this->Html->tag('th', __('Tgl Hutang'), array(
                        'class' => 'text-center',
                    ));
                    echo $this->Html->tag('th', __('Total Hutang'), array(
                        'class' => 'text-right',
                    ));
            ?>
        </tr>
        <?php
                if(!empty($values)){
                    foreach ($values as $key => $value) {
                        $id = Common::hashEmptyField($value, 'DebtDetail.id');
                        $nodoc = Common::hashEmptyField($value, 'Debt.nodoc');
                        $debt_id = Common::hashEmptyField($value, 'Debt.id');
                        $document_date = Common::hashEmptyField($value, 'Debt.transaction_date');
                        $note = Common::hashEmptyField($value, 'DebtDetail.note', '-');
                        $total = Common::hashEmptyField($value, 'DebtDetail.total', 0);
                        $last_paid = Common::hashEmptyField($value, 'DebtDetail.last_paid', 0);
                        $total -= $last_paid;
                        
                        $employe_id = Common::hashEmptyField($value, 'ViewStaff.id');
                        $no_id = Common::hashEmptyField($value, 'ViewStaff.no_id', '-');
                        $name_code = Common::hashEmptyField($value, 'ViewStaff.name_code', '-');
                        $employe_name = Common::hashEmptyField($value, 'ViewStaff.full_name', '-');
                        $type = Common::hashEmptyField($value, 'ViewStaff.type');
                        
                        $document_date = $this->Common->formatDate($document_date, 'd M Y');

                        $checkbox = isset($checkbox)?$checkbox:true;
                        $alias = sprintf('child-%s', $id);

                        $debtPayment = Common::hashEmptyField($this->request->data, 'DebtPayment');

                        if( !empty($checkbox) ) {
                            printf('<tr data-value="%s" class="child %s">', $alias, $alias);

                            $checkboxContent = $this->Form->checkbox('debt_checked.', array(
                                'class' => 'check-option',
                                'value' => $id,
                            ));
                            $checkboxContent .= $this->Form->input('DebtPayment.id.', array(
                                'type' => 'hidden',
                                'value' => $id,
                            ));

                            echo $this->Html->tag('td', $checkboxContent, array(
                                'class' => 'checkbox-action',
                            ));
                        } else {
                            printf('<tr class="child child-%s">', $alias);
                        }
                ?>
                    <td><?php echo $nodoc;?></td>
                    <td class="on-remove"><?php echo $no_id;?></td>
                    <td class="hide on-show"><?php echo $name_code;?></td>
                    <td class="on-remove"><?php echo $employe_name;?></td>
                    <td><?php echo $type;?></td>
                    <td><?php echo $note;?></td>
                    <td class="text-center"><?php echo $document_date;?></td>
                    <td class="text-right"><?php echo Common::getFormatPrice($total);?></td>
                    <td class="text-right hide on-show">
                        <?php
                                echo $this->Form->input('DebtPaymentDetail.amount.'.$key,array(
                                    'label'=> false,
                                    'class'=>'form-control input_price_coma text-right sisa-amount',
                                    'data-decimal' => '0',
                                    'required' => false,
                                ));
                                echo $this->Form->hidden('DebtPaymentDetail.debt_id.'.$key,array(
                                    'value'=> $debt_id,
                                ));
                                echo $this->Form->hidden('DebtPaymentDetail.debt_detail_id.'.$key,array(
                                    'value'=> $id,
                                ));
                                echo $this->Form->hidden('DebtPaymentDetail.employe_id.'.$key,array(
                                    'value'=> $employe_id,
                                ));
                        ?>
                    </td>
                    <?php 
                            echo $this->Html->tag('td', $this->Html->link('<i class="fa fa-times"></i>', 'javascript:', array(
                                'class' => 'delete-document-current btn btn-danger btn-xs',
                                'escape' => false,
                                'data-id' => sprintf('child-%s', $alias),
                            )), array(
                                'class' => 'document-table-action hide on-show',
                            ));
                    ?>
                </tr>
        <?php
                    }
                }else{
                    echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                        'class' => 'alert alert-warning text-center',
                        'colspan' => '8'
                    )));
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