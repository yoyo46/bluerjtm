<?php 
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/insurances/searchs/payment');

        $dataColumns = array(
            'noref' => array(
                'name' => __('No. Referensi'),
                'field_model' => 'InsurancePayment.id',
                'display' => true,
            ),
            'nodoc' => array(
                'name' => __('No. Dokumen'),
                'field_model' => 'InsurancePayment.nodoc',
                'display' => true,
            ),
            'installment' => array(
                'name' => __('Tgl Pembayaran'),
                'field_model' => 'InsurancePayment.payment_date',
                'class' => 'text-center',
                'display' => true,
            ),
            'grandtotal' => array(
                'name' => __('Total Pembayaran'),
                'field_model' => 'InsurancePayment.grandtotal',
                'class' => 'text-center',
                'display' => true,
            ),
            'status' => array(
                'name' => __('Status'),
                'field_model' => 'InsurancePayment.status',
                'class' => 'text-center',
                'display' => true,
            ),
            'created' => array(
                'name' => __('Dibuat'),
                'field_model' => 'InsurancePayment.created',
                'display' => true,
            ),
            'action' => array(
                'name' => __('Action'),
                'field_model' => false,
                'display' => true,
            ),
        );
        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );
?>
<div class="box box-success">
    <div class="box-header">
        <?php 
                echo $this->Html->tag('h3', $sub_module_title, array(
                    'class' => 'box-title'
                ));
        ?>
        <div class="box-tools">
            <?php
                    echo $this->Html->link('<i class="fa fa-plus"></i> Tambah', array(
                        'controller' => 'insurances',
                        'action' => 'payment_add'
                    ), array(
                        'escape' => false,
                        'class' => 'btn btn-app btn-success pull-right'
                    ));
            ?>
            <div class="clear"></div>
        </div>
    </div>
    <div class="box-body table-responsive">
        <table class="table table-hover sorting">
            <?php
                    if( !empty($fieldColumn) ) {
                        echo $this->Html->tag('thead', $this->Html->tag('tr', $fieldColumn));
                    }
            ?>
            <tbody>
            <?php
                    if(!empty($values)){
                        foreach ($values as $key => $value) {
                            $id = $this->Common->filterEmptyField($value, 'InsurancePayment', 'id');
                            $nodoc = $this->Common->filterEmptyField($value, 'InsurancePayment', 'nodoc');
                            $payment_date = $this->Common->filterEmptyField($value, 'InsurancePayment', 'payment_date');
                            $periode = $this->Common->filterEmptyField($value, 'InsurancePayment', 'payment_date', false, false, array(
                                'date' => 'Y-m',
                            ));
                            $grandtotal = $this->Common->filterEmptyField($value, 'InsurancePayment', 'grandtotal');
                            $status = $this->Common->filterEmptyField($value, 'InsurancePayment', 'status');
                            $rejected = $this->Common->filterEmptyField($value, 'InsurancePayment', 'rejected');
                            $created = $this->Common->filterEmptyField($value, 'InsurancePayment', 'created');

                            $customCreated = $this->Time->niceShort($created);
                            $customDate = $this->Common->customDate($payment_date, 'd M Y');
                            $customGrandtotal = $this->Common->getFormatPrice($grandtotal);
                            $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
            ?>
            <tr>
                <?php 
                        echo $this->Html->tag('td', $noref);
                        echo $this->Html->tag('td', $nodoc);
                        echo $this->Html->tag('td', $customDate, array(
                            'class' => 'text-center',
                        ));
                        echo $this->Html->tag('td', $customGrandtotal, array(
                            'class' => 'text-right',
                        ));

                        if( !empty($rejected) ) {
                            $lblStatus = $this->Html->tag('span', __('Void'), array(
                                'class' => 'label label-danger',
                            ));
                        } else {
                            $lblStatus = $this->Html->tag('span', __('Dibayar'), array(
                                'class' => 'label label-success',
                            ));
                        }

                        echo $this->Html->tag('td', $lblStatus, array(
                            'class' => 'text-center',
                        ));
                        echo $this->Html->tag('td', $customCreated);
                ?>
                <td class="action">
                    <?php
                            echo $this->Html->link('Info', array(
                                'controller' => 'insurances',
                                'action' => 'detail_payment',
                                $id
                            ), array(
                                'class' => 'btn btn-info btn-xs'
                            ));

                            if( empty($rejected) ){
                                echo $this->Html->link('Void', array(
                                    'controller' => 'insurances',
                                    'action' => 'payment_delete',
                                    $id
                                ), array(
                                    'class' => 'btn btn-danger btn-xs ajaxModal',
                                    'data-action' => 'submit_form',
                                    'title' => __('Void Pembayaran Asuransi'),
                                    'closing' => true,
                                    'periode' => $periode,
                                ));
                            }
                    ?>
                </td>
            </tr>
            <?php
                        }
                    }else{
                        echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                            'class' => 'alert alert-warning text-center',
                            'colspan' => '9'
                        )));
                    }
            ?>
            </tbody>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>