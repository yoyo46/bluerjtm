<?php
        echo $this->element('blocks/ajax/searchs/laka', array(
            'urlForm' => array(
                'controller' => 'ajax',
                'action' => 'search',
                'getLakas',
                'payment_id' => $payment_id,
                'admin' => false,
            ),
            'urlReset' => array(
                'controller' => 'ajax',
                'action' => 'getLakas',
                'payment_id' => $payment_id,
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

                    echo $this->Html->tag('th', __('NoPol'));
                    echo $this->Html->tag('th', __('Supir'));
                    echo $this->Html->tag('th', __('Tgl LAKA'), array(
                        'class' => 'text-center',
                    ));
                    echo $this->Html->tag('th', __('Lokasi LAKA'));
                    echo $this->Html->tag('th', __('Status Muatan'), array(
                        'class' => 'text-center',
                    ));
            ?>
        </tr>
        <?php
                if(!empty($values)){
                    foreach ($values as $key => $value) {
                        $id = $this->Common->filterEmptyField($value, 'Laka', 'id');
                        $document_date = $this->Common->filterEmptyField($value, 'Laka', 'tgl_laka');
                        $lokasi = $this->Common->filterEmptyField($value, 'Laka', 'lokasi_laka');
                        $status_muatan = $this->Common->filterEmptyField($value, 'Laka', 'status_muatan');
                        $last_paid = $this->Common->filterEmptyField($value, 'Laka', 'last_paid', 0);
                        $nopol = $this->Common->filterEmptyField($value, 'Laka', 'nopol');
                        
                        $driver = $this->Common->filterEmptyField($value, 'Laka', 'driver_name');
                        $driver = $this->Common->filterEmptyField($value, 'Laka', 'change_driver_name', $driver);

                        $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
                        $status_muatan = strtoupper($status_muatan);
                        $document_date = $this->Common->formatDate($document_date, 'd M Y');

                        $checkbox = isset($checkbox)?$checkbox:true;
                        $alias = sprintf('child-%s', $id);

                        $lakaPayment = $this->Common->filterEmptyField($this->request->data, 'LakaPayment');

                        if( !empty($checkbox) ) {
                            printf('<tr data-value="%s" class="child %s">', $alias, $alias);

                            $checkboxContent = $this->Form->checkbox('laka_checked.', array(
                                'class' => 'check-option',
                                'value' => $id,
                            ));
                            $checkboxContent .= $this->Form->input('LakaPayment.laka_id.', array(
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
                    <td class="hide on-show"><?php echo $noref;?></td>
                    <td><?php echo $nopol;?></td>
                    <td><?php echo $driver;?></td>
                    <td class="text-center"><?php echo $document_date;?></td>
                    <td><?php echo $lokasi;?></td>
                    <td class="text-center"><?php echo $status_muatan;?></td>
                    <td class="text-right hide on-show">
                        <?php
                                echo $this->Form->input('LakaPaymentDetail.amount.'.$key,array(
                                    'label'=> false,
                                    'class'=>'form-control input_price_coma text-right sisa-amount',
                                    'data-decimal' => '0',
                                    'required' => false,
                                    'value' => $last_paid,
                                ));
                                echo $this->Form->hidden('LakaPaymentDetail.laka_id.'.$key,array(
                                    'value'=> $id,
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
                        'colspan' => '12'
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