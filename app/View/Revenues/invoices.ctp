<?php 
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/revenues/search_invoices');
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <?php 
                // if( in_array('insert_invoices', $allowModule) ) {
        ?>
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
                    ?>
                </ul>
            </div>
        </div>
        <?php 
                // }
        ?>
    </div><!-- /.box-header -->
    <div class="box-body table-responsive">
        <table class="table table-hover">
            <tr>
                <?php
                        echo $this->Html->tag('th', $this->Paginator->sort('Invoice.no_invoice', __('No. Invoice'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Invoice.invoice_date', __('Tgl Invoice'), array(
                            'escape' => false
                        )));
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
            ?>
            <tr>
                <td><?php echo $value['Invoice']['no_invoice'];?></td>
                <td><?php echo $this->Common->customDate($value['Invoice']['invoice_date'], 'd/m/Y');?></td>
                <td><?php echo $value['Customer']['name'];?></td>
                <td>
                    <?php 
                        echo $this->Common->customDate($value['Invoice']['period_from'], 'd M Y').' s/d '.$this->Common->customDate($value['Invoice']['period_to'], 'd M Y');
                    ?>
                </td>
                <td align="right"><?php echo $this->Number->currency($value['Invoice']['total'], Configure::read('__Site.config_currency_code'), array('places' => 0));?></td>
                <td>
                    <?php 
                            if(!empty($value['Invoice']['complete_paid'])){
                                $class_status = 'label label-success';
                                $status = __('Paid');
                            } else {
                                $class_status = 'label label-primary';
                                $status = __('Unpaid');
                            }

                            echo $this->Html->tag('span', $status, array('class' => $class_status));
                    ?>
                </td>
                <td><?php echo $this->Time->timeAgoInWords($value['Invoice']['created']);?></td>
                <td class="action">
                    <?php 
                            echo $this->Html->link(__('Print Detail'), array(
                                'controller' => 'revenues',
                                'action' => 'invoice_print',
                                $id
                            ), array(
                                'class' => 'btn btn-primary btn-xs'
                            ));
                            echo $this->Html->link(__('Print Per Tanggal'), array(
                                'controller' => 'revenues',
                                'action' => 'invoice_print',
                                $id,
                                'print' => 'date',
                            ), array(
                                'class' => 'btn btn-success btn-xs'
                            ));
                            echo $this->Html->link(__('Print Header'), array(
                                'controller' => 'revenues',
                                'action' => 'invoice_print',
                                $id,
                                'print' => 'header',
                            ), array(
                                'class' => 'btn btn-info btn-xs'
                            ));

                            // echo $this->Html->link(__('Hapus'), array(
                            //     'controller' => 'revenues',
                            //     'action' => 'invoice_toggle',
                            //     $id
                            // ), array(
                            //     'class' => 'btn btn-danger btn-xs',
                            //     'title' => 'Hapus Data Invoice'
                            // ), __('Anda yakin ingin menghapus data Invoice ini?'));
                    ?>
                </td>
            </tr>
            <?php
                        }
                    } else {
                        echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                            'class' => 'alert alert-warning text-center',
                            'colspan' => '5'
                        )));
                    }
            ?>
        </table>
    </div>
    <?php echo $this->element('pagination');?>
</div>