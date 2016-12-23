<?php 
        $title = !empty($title)?$title:false;
        $tax = !empty($tax)?$tax:false;
        $data = $this->request->data;

        $ppn = $this->Common->filterEmptyField($tax, 'Invoice', 'ppn');
        $pph = $this->Common->filterEmptyField($tax, 'Invoice', 'pph');

        echo $this->Form->create('Search', array(
            'url'=> $this->Html->url( array(
                'controller' => 'ajax',
                'action' => 'search',
                'getInfoInvoicePaymentDetail',
                'customer_id' => $id,
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
                        'label'=> __('Tgl Invoice'),
                        'class'=>'form-control date-range',
                        'required' => false,
                        'placeholder' => __('Tgl Invoice')
                    ));
            ?>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <?php 
                    echo $this->Form->input('nodoc',array(
                        'label'=> __('No.Invoice'),
                        'class'=>'form-control on-focus',
                        'required' => false,
                        'placeholder' => __('No.Invoice')
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
            echo $this->Html->link('<i class="fa fa-refresh"></i> '.__('Reset'), array(
                'controller' => 'ajax',
                'action' => 'getInfoInvoicePaymentDetail',
                $id,
            ), array(
                'escape' => false, 
                'class'=> 'btn btn-default btn-sm ajaxModal',
                'data-action' => $data_action,
                'title' => $title,
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

                        if( $total > $totalPaid ) {
                            $sisaPembayaran = (!empty($data['InvoicePaymentDetail']['price_pay'][$invoice['id']])) ? $data['InvoicePaymentDetail']['price_pay'][$invoice['id']] : $sisaPembayaran;

                            $ppnTotal = $this->Common->calcFloat($total, $ppn);
                            $pphTotal = $this->Common->calcFloat($total, $pph);
                            $totalPayment = $sisaPembayaran + $ppnTotal;

                            $customPpnTotal = $this->Common->getFormatPrice($ppnTotal);
                            $customPphTotal = $this->Common->getFormatPrice($pphTotal);
                            $customTotalPayment = $this->Common->getFormatPrice($totalPayment);
            ?>
            <tr class="child-search pick-document child-search-<?php echo $invoice['id'];?>" rel="<?php echo $invoice['id'];?>">
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
                            printf('%s s/d %s', $this->Common->customDate($invoice['period_from'], 'd M Y'), $this->Common->customDate($invoice['period_to'], 'd M Y'));
                    ?>
                </td>
                <td class="text-right total-payment">
                    <?php
                            echo $this->Common->getFormatPrice($total);
                    ?>
                </td>
                <td class="text-right">
                    <?php
                            if(!empty($totalPaid)){
                                echo $this->Common->getFormatPrice($totalPaid);
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
                            'class' => 'form-control input_price document-pick-price text-right',
                            'value' => $sisaPembayaran,
                        ));

                        if(!empty($this->request->data['InvoicePaymentDetail']['price_pay'][$key])){
                            $total += str_replace(',', '', $this->request->data['InvoicePaymentDetail']['price_pay'][$key]);
                        }
                    ?>
                </td>
                <td class="text-right hide row action-search" valign="top">
                    <?php
                            echo $this->Form->input('InvoicePaymentDetail.ppn.'.$invoice['id'], array(
                                'type' => 'text',
                                'label' => false,
                                'div' => 'col-sm-5 no-padding',
                                'required' => false,
                                'class' => 'form-control input_number text-center tax-percent',
                                'placeholder' => '%',
                                'data-type' => 'percent',
                                'rel' => 'ppn',
                                'value' => $ppn,
                            ));
                            echo $this->Form->input('InvoicePaymentDetail.ppn_total.'.$invoice['id'], array(
                                'type' => 'text',
                                'label' => false,
                                'div' => 'col-sm-7 no-padding',
                                'required' => false,
                                'class' => 'form-control text-right tax-nominal',
                                'placeholder' => 'Rp.',
                                'data-decimal' => '0',
                                'data-type' => 'nominal',
                                'rel' => 'ppn',
                                'value' => $customPpnTotal,
                            ));
                    ?>
                </td>
                <td class="text-right hide row action-search" valign="top">
                    <?php
                            echo $this->Form->input('InvoicePaymentDetail.pph.'.$invoice['id'], array(
                                'type' => 'text',
                                'label' => false,
                                'div' => 'col-sm-5 no-padding',
                                'required' => false,
                                'class' => 'form-control input_number text-center tax-percent',
                                'placeholder' => '%',
                                'data-type' => 'percent',
                                'rel' => 'pph',
                                'value' => $pph,
                            ));
                            echo $this->Form->input('InvoicePaymentDetail.pph_total.'.$invoice['id'], array(
                                'type' => 'text',
                                'label' => false,
                                'div' => 'col-sm-7 no-padding',
                                'required' => false,
                                'class' => 'form-control text-right tax-nominal',
                                'placeholder' => 'Rp.',
                                'data-decimal' => '0',
                                'data-type' => 'nominal',
                                'rel' => 'pph',
                                'value' => $customPphTotal,
                            ));
                    ?>
                </td>
                <?php 
                        echo $this->Html->tag('td', $customTotalPayment, array(
                            'class' => 'text-right hide total-document action-search',
                        ));
                ?>
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
                    }
                }else{
                    echo $this->Html->tag('tr', $this->Html->tag('td', __('Data tidak ditemukan')) );
                }
            ?>
        </tbody>
    </table>
</div>