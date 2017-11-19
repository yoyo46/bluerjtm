<?php 
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/revenues/search_invoices');
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <div class="box-tools">
            <div class="btn-group pull-right">
                <?php 
                        echo $this->Html->tag('button', '<i class="fa fa-plus"></i> Buat Invoice', array(
                            'data-toggle' => 'dropdown',
                            'class' => 'btn btn-app btn-success dropdown-toggle'
                        ));
                ?>
                <ul class="dropdown-menu" role="menu">
                    <?php 
                            echo $this->Html->tag('li', $this->Html->link(__('Invoice Biasa'), array(
                                'controller' => 'revenues',
                                'action' => 'invoice_add'
                            ), array(
                                'escape' => false,
                            )));
                            echo $this->Html->tag('li', '', array(
                                'class' => 'divider',
                            ));
                            echo $this->Html->tag('li', $this->Html->link(__('Invoice Per Tarif'), array(
                                'controller' => 'revenues',
                                'action' => 'invoice_add',
                                'tarif',
                            ), array(
                                'escape' => false,
                            )));
                            echo $this->Html->tag('li', '', array(
                                'class' => 'divider',
                            ));
                            echo $this->Html->tag('li', $this->Html->link(__('Invoice Per Nama Tarif'), array(
                                'controller' => 'revenues',
                                'action' => 'invoice_add',
                                'tarif_name',
                            ), array(
                                'escape' => false,
                            )));
                    ?>
                </ul>
            </div>
        </div>
    </div><!-- /.box-header -->
    <div class="box-body table-responsive">
        <table class="table table-hover">
            <tr>
                <?php
                        echo $this->Html->tag('th', $this->Paginator->sort('Invoice.no_invoice', __('No. Invoice'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Invoice.tarif_type', __('Jenis Tarif'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Invoice.invoice_date', __('Tgl Invoice'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', __('Company'));
                        echo $this->Html->tag('th', __('Customer'));
                        echo $this->Html->tag('th', $this->Paginator->sort('Invoice.period_from', __('Periode Invoice'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', __('Total'));
                        echo $this->Html->tag('th', $this->Paginator->sort('Invoice.complete_paid', __('Status'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Invoice.created', __('Dibuat'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', __('Action'), array(
                            'width' => '10%',
                        ));
                ?>
            </tr>
            <?php
                    $i = 1;
                    if(!empty($invoices)){
                        foreach ($invoices as $key => $value) {
                            $id = $value['Invoice']['id'];
                            $company = $this->Common->filterEmptyField($value, 'Company', 'name', 'RJTM');
                            $periode = $this->Common->filterEmptyField($value, 'Invoice', 'invoice_date', false, false, array(
                                'date' => 'Y-m',
                            ));
            ?>
            <tr>
                <td><?php echo $value['Invoice']['no_invoice'];?></td>
                <td><?php echo ucfirst($value['Invoice']['tarif_type']);?></td>
                <td><?php echo $this->Common->customDate($value['Invoice']['invoice_date'], 'd/m/Y');?></td>
                <td><?php echo $company;?></td>
                <td><?php echo $value['Customer']['customer_name_code'];?></td>
                <td>
                    <?php 
                        echo $this->Common->customDate($value['Invoice']['period_from'], 'd M Y').' s/d '.$this->Common->customDate($value['Invoice']['period_to'], 'd M Y');
                    ?>
                </td>
                <td align="right"><?php echo $this->Number->currency($value['Invoice']['total'], Configure::read('__Site.config_currency_code'), array('places' => 0));?></td>
                <td>
                    <?php 
                            $invoiceStatus = $this->Common->getInvoiceStatus( $value );

                            echo $this->Html->tag('span', $invoiceStatus['text'], array('class' => $invoiceStatus['class']));
                            echo $invoiceStatus['void_date'];
                    ?>
                </td>
                <td><?php echo $this->Time->timeAgoInWords($value['Invoice']['created']);?></td>
                <td class="action">
                    <?php 
                            echo $this->Html->link(__('Print'), array(
                                'controller' => 'ajax',
                                'action' => 'invoice_report',
                                $id,
                            ), array(
                                'class' => 'btn btn-success btn-xs ajaxCustomModal',
                                'title' => __('Laporan Invoice')
                            ));

                            if( empty($value['Invoice']['complete_paid']) && empty($value['Invoice']['paid']) ){
                                if( !empty($value['Invoice']['status']) ){
                                    echo $this->Html->link(__('Void'), array(
                                        'controller' => 'revenues',
                                        'action' => 'invoice_delete',
                                        $id
                                    ), array(
                                        'class' => 'btn btn-danger btn-xs ajaxModal',
                                        'data-action' => 'cancel_invoice',
                                        'title' => __('Void Data Invoice'),
                                        'closing' => true,
                                        'periode' => $periode,
                                    ));
                                }
                            }
                    ?>
                </td>
            </tr>
            <?php
                        }
                    } else {
                        echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                            'class' => 'alert alert-warning text-center',
                            'colspan' => '10'
                        )));
                    }
            ?>
        </table>
    </div>
    <?php echo $this->element('pagination');?>
</div>