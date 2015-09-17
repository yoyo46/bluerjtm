<?php 
        echo $this->Form->create('Invoice', array(
            'url'=> $this->Html->url( array(
                'controller' => 'ajax',
                'action' => 'getInfoInvoicePaymentDetail',
                $id
            )), 
            'role' => 'form',
            'inputDefaults' => array('div' => false),
        ));
?>
<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <?php 
                    echo $this->Form->input('date_from',array(
                        'label'=> __('Dari Tanggal'),
                        'class'=>'form-control custom-date',
                        'required' => false,
                        'placeholder' => __('Dari')
                    ));
            ?>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <?php 
                    echo $this->Form->input('date_to',array(
                        'label'=> __('Sampai Tanggal'),
                        'class'=>'form-control custom-date',
                        'required' => false,
                        'placeholder' => __('Sampai')
                    ));
            ?>
        </div>
    </div>
</div>
<div class="form-group action">
    <?php
            echo $this->Form->button('<i class="fa fa-search"></i> '.__('Cari'), array(
                'div' => false, 
                'class'=> 'btn btn-success btn-sm ajaxModal',
                'data-parent' => true,
                'title' => 'Invoice Customer',
                'data-action' => $data_action,
                'title' => $title
            ));
    ?>
</div>
<?php 
    echo $this->Form->end();
?>
<div class="box-body table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <?php
                    $input_all = $this->Form->checkbox('checkbox_all', array(
                        'class' => 'checkAll'
                    ));
                    echo $this->Html->tag('th', $input_all);
                ?>
                <th><?php echo __('No.Invoice');?></th>
                <th><?php echo __('Tgl Invoice');?></th>
                <th class="text-center"><?php echo __('Periode');?></th>
                <th class="text-center"><?php echo __('Total');?></th>
                <th class="text-center"><?php echo __('Telah Dibayar');?></th>
            </tr>
        </thead>
        <tbody class="ttuj-info-table">
            <?php
                $total = $i = 0;

                if(!empty($invoices)){
                    foreach ($invoices as $key => $value) {
                        $invoice = $value['Invoice'];
                        $total = !empty($invoice['total'])?$invoice['total']:0;
                        $totalPaid = !empty($value['invoice_has_paid'])?$value['invoice_has_paid']:0;
                        $sisaPembayaran = $total - $totalPaid;
            ?>
            <tr class="child-search child-search-<?php echo $invoice['id'];?>" rel="<?php echo $invoice['id'];?>">
                <td class="checkbox-detail">
                    <?php
                        echo $this->Form->checkbox('invoice_id.', array(
                            'class' => 'check-option',
                            'value' => $id
                        ));
                    ?>
                </td>
                <td>
                    <?php
                        echo $invoice['no_invoice'];

                        echo $this->Form->input('InvoicePaymentDetail.invoice_id.'.$invoice['id'], array(
                            'type' => 'hidden',
                            'value' => $invoice['id']
                        ));
                    ?>
                </td>
                <td>
                    <?php
                            echo $this->Common->customDate($invoice['invoice_date']);
                    ?>
                </td>
                <td class="text-center">
                    <?php
                            printf('%s s/d %s', $this->Common->customDate($invoice['period_from'], 'd/m/Y'), $this->Common->customDate($invoice['period_to'], 'd/m/Y'));
                    ?>
                </td>
                <td class="text-right">
                    <?php
                        echo $this->Number->currency($total, Configure::read('__Site.config_currency_code'), array('places' => 0));
                    ?>
                </td>
                <td class="text-right">
                    <?php
                            if(!empty($totalPaid)){
                                echo $this->Number->currency($totalPaid, Configure::read('__Site.config_currency_code'), array('places' => 0)); 
                            }else{
                                echo '-';
                            }
                    ?>
                </td>
                <td class="text-right action-search hide" valign="top">
                    <?php
                        echo $this->Form->input('InvoicePaymentDetail.price_pay.'.$invoice['id'], array(
                            'type' => 'text',
                            'label' => false,
                            'div' => false,
                            'required' => false,
                            'class' => 'form-control input_price document-pick-price',
                            'value' => (!empty($this->request->data['InvoicePaymentDetail']['price_pay'][$invoice['id']])) ? $this->request->data['InvoicePaymentDetail']['price_pay'][$invoice['id']] : $sisaPembayaran
                        ));

                        if(!empty($this->request->data['InvoicePaymentDetail']['price_pay'][$key])){
                            $total += str_replace(',', '', $this->request->data['InvoicePaymentDetail']['price_pay'][$key]);
                        }
                    ?>
                </td>
                <td class="action-search hide">
                    <?php
                        echo $this->Html->link('<i class="fa fa-times"></i> Hapus', 'javascript:', array(
                            'class' => 'delete-custom-field btn btn-danger btn-xs',
                            'escape' => false,
                            'action_type' => 'invoice_first'
                        ));
                    ?>
                </td>
            </tr>
            <?php
                    }
                }else{
                    echo $this->Html->tag('tr', $this->Html->tag('td', __('Data tidak ditemukan')) );
                }
            ?>
        </tbody>
    </table>
</div>