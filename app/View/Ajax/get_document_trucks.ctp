<?php 
        echo $this->Form->create('Search', array(
            'url'=> $this->Html->url(array(
                'controller' => 'ajax',
                'action' => 'search',
                'getDocumentTrucks',
                'payment_id' => $payment_id,
                'admin' => false,
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
                        'label'=> __('Tanggal Berakhir'),
                        'class'=>'form-control date-range',
                        'required' => false,
                        'placeholder' => __('Tanggal')
                    ));
            ?>
        </div>
        <div class="form-group">
            <?php 
                    echo $this->Form->label('type', __('Truk'));
            ?>
            <div class="row">
                <div class="col-sm-4">
                    <?php 
                            echo $this->Form->input('type',array(
                                'label'=> false,
                                'class'=>'form-control',
                                'required' => false,
                                'empty' => false,
                                'options' => array(
                                    '1' => __('Nopol'),
                                    '2' => __('ID Truk'),
                                ),
                            ));
                    ?>
                </div>
                <div class="col-sm-8">
                    <?php 
                            echo $this->Form->input('nopol',array(
                                'label'=> false,
                                'class'=>'form-control on-focus',
                                'required' => false,
                            ));
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <?php 
                    echo $this->Form->label('document_type', __('Jenis Surat'));
            ?>
            <div class="row">
                <?php 
                        echo $this->Html->tag('div', $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('kir', array(
                            'type' => 'checkbox',
                            'label'=> false,
                            'required' => false,
                            'value' => 1,
                            'div' => false,
                        )).__('KIR')), array(
                            'class' => 'checkbox',
                        )), array(
                            'class' => 'col-sm-6',
                        ));
                        echo $this->Html->tag('div', $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('siup', array(
                            'type' => 'checkbox',
                            'label'=> false,
                            'required' => false,
                            'value' => 1,
                            'div' => false,
                        )).__('SIUP')), array(
                            'class' => 'checkbox',
                        )), array(
                            'class' => 'col-sm-6',
                        ));
                        echo $this->Html->tag('div', $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('stnk', array(
                            'type' => 'checkbox',
                            'label'=> false,
                            'required' => false,
                            'value' => 1,
                            'div' => false,
                        )).__('STNK 1 Thn')), array(
                            'class' => 'checkbox',
                        )), array(
                            'class' => 'col-sm-6',
                        ));
                        echo $this->Html->tag('div', $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('stnk_5_thn', array(
                            'type' => 'checkbox',
                            'label'=> false,
                            'required' => false,
                            'value' => 1,
                            'div' => false,
                        )).__('STNK 5 Thn')), array(
                            'class' => 'checkbox',
                        )), array(
                            'class' => 'col-sm-6',
                        ));
                ?>
            </div>
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
                        'action' => 'getDocumentTrucks',
                        'payment_id' => $payment_id,
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
        <tr>
            <?php 
                    $input_all = $this->Form->checkbox('checkbox_all', array(
                        'class' => 'checkAll'
                    ));
                    echo $this->Html->tag('th', $input_all);

                    echo $this->Html->tag('th', __('NoPol'));
                    echo $this->Html->tag('th', __('Jenis Surat'));
                    echo $this->Html->tag('th', __('Tgl Berakhir'));
                    echo $this->Html->tag('th', __('Tgl Perpanjang'));
                    echo $this->Html->tag('th', __('Biaya'));
                    echo $this->Html->tag('th', __('Denda'));
                    echo $this->Html->tag('th', __('Biaya lain2'));
            ?>
        </tr>
        <?php
                if(!empty($values)){
                    foreach ($values as $key => $value) {
                        $id = $this->Common->filterEmptyField($value, 'DocumentTruck', 'id');
                        $nopol = $this->Common->filterEmptyField($value, 'DocumentTruck', 'no_pol');
                        $data_type = $this->Common->filterEmptyField($value, 'DocumentTruck', 'data_type');
                        $to_date = $this->Common->filterEmptyField($value, 'DocumentTruck', 'to_date');
                        $document_date = $this->Common->filterEmptyField($value, 'DocumentTruck', 'document_date');
                        $price = $this->Common->filterEmptyField($value, 'DocumentTruck', 'price');
                        $denda = $this->Common->filterEmptyField($value, 'DocumentTruck', 'denda');
                        $biaya_lain = $this->Common->filterEmptyField($value, 'DocumentTruck', 'biaya_lain');
                        $price_estimate = $this->Common->filterEmptyField($value, 'DocumentTruck', 'price_estimate');
                        $last_paid = $this->Common->filterEmptyField($value, 'DocumentTruck', 'last_paid', 0);
                        $note = $this->Common->filterEmptyField($value, 'DocumentTruck', 'note');
                        $total = $price + $denda + $biaya_lain - $last_paid;

                        $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
                        $to_date = $this->Common->formatDate($to_date, 'd/m/Y');
                        $document_date = $this->Common->formatDate($document_date, 'd/m/Y');
                        $customPrice = $this->Common->getFormatPrice($price);
                        $customDenda = $this->Common->getFormatPrice($denda);
                        $customBiayaLain = $this->Common->getFormatPrice($biaya_lain);
                        $customPriceEstimate = $this->Common->getFormatPrice($price_estimate);
                        $customTotal = $this->Common->getFormatPrice($total);
                        
                        switch ($data_type) {
                            case 'stnk':
                                $type = __('STNK 1 Thn');
                                break;
                            case 'stnk_5_thn':
                                $type = __('STNK 5 Thn');
                                break;
                            
                            default:
                                $type = ucwords($data_type);
                                break;
                        }

                        $checkbox = isset($checkbox)?$checkbox:true;
                        $alias = sprintf('child-%s-%s', $id, $data_type);

                        $documentPayment = $this->Common->filterEmptyField($this->request->data, 'DocumentPayment');

                        if( !empty($checkbox) ) {
                            printf('<tr data-value="%s" data-type="%s" class="child %s">', $alias, $data_type, $alias);

                            $checkboxContent = $this->Form->checkbox('document_checked.', array(
                                'class' => 'check-option',
                                'value' => $id,
                            ));
                            $checkboxContent .= $this->Form->input('DocumentPayment.document_id.', array(
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
                    <td><?php echo $type;?></td>
                    <td class="text-center"><?php echo $to_date;?></td>
                    <td class="text-center"><?php echo $document_date;?></td>
                    <td class="text-right hide on-show"><?php echo $customPriceEstimate;?></td>
                    <td class="text-right"><?php echo $customPrice;?></td>
                    <td class="text-right"><?php echo $customDenda;?></td>
                    <td class="text-right"><?php echo $customBiayaLain;?></td>
                    <td class="text-right hide on-show">
                        <?php
                                echo $this->Form->input('DocumentPaymentDetail.amount.'.$key,array(
                                    'label'=> false,
                                    'class'=>'form-control input_price text-right sisa-amount',
                                    'required' => false,
                                    'value' => $total,
                                ));
                                echo $this->Form->hidden('DocumentPaymentDetail.document_id.'.$key,array(
                                    'value'=> $id,
                                ));
                                echo $this->Form->hidden('DocumentPaymentDetail.document_type.'.$key,array(
                                    'value'=> $data_type,
                                ));
                        ?>
                    </td>
                    <td class="hide on-show"><?php echo $note;?></td>
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