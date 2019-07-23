<div id="wrapper-modal-write" class="document-picker">
    <?php 
            $dataColumns = array(
                'check-box' => array(
                    'name' => $this->Form->checkbox('checkbox_all', array(
                        'class' => 'checkAll'
                    )),
                    'class' => 'text-center',
                ),
                'id' => array(
                    'name' => __('No. Ref'),
                ),
                'nodoc' => array(
                    'name' => __('No. Dokumen'),
                ),
                'date' => array(
                    'name' => __('Tgl pembayaran'),
                ),
                'note' => array(
                    'name' => __('Ket.'),
                ),
                'total' => array(
                    'name' => __('Total'),
                    'class' => 'text-right',
                ),
            );

            $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );
            echo $this->element('blocks/revenues/searchs/bypass_ttuj_payments');
    ?>
    <div class="box-body table-responsive">
        <table class="table table-hover">
            <?php
                    if( !empty($fieldColumn) ) {
                        echo $this->Html->tag('thead', $this->Html->tag('tr', $fieldColumn));
                    }
            ?>
            <tbody>
                <?php
                        if(!empty($values)){
                            foreach ($values as $key => $value) {
                                $id = Common::hashEmptyField($value, 'TtujPayment.id');
                                $nodoc = Common::hashEmptyField($value, 'TtujPayment.nodoc');
                                $total_payment = Common::hashEmptyField($value, 'TtujPayment.total_payment');
                                $date_payment = Common::hashEmptyField($value, 'TtujPayment.date_payment');
                                $note = Common::hashEmptyField($value, 'TtujPayment.description', '-');
                                $noref = str_pad($id, 6, '0', STR_PAD_LEFT);

                                $data_value = $nodoc;
                ?>
                <tr class="pick-document" rel="<?php echo $id; ?>" data-table="ttuj_payment" data-type="select-multiple">
                    <?php
                            echo $this->Html->tag('td', $this->Form->checkbox('document_id.'.$id, array(
                                'class' => 'check-option',
                                'value' => $data_value,
                            )), array(
                                'class' => 'removed check-box text-center',
                            ));
                            echo $this->Html->tag('td', $noref);
                            echo $this->Html->tag('td', $nodoc);
                            echo $this->Html->tag('td', Common::formatDate($date_payment, 'd M Y'));
                            echo $this->Html->tag('td', $note);
                            echo $this->Html->tag('td', Common::getFormatPrice($total_payment), array(
                                'class' => 'text-right',
                            ));
                    ?>
                </tr>
                <?php
                        }
                    }else{
                        echo $this->Html->tag('tr', $this->Html->tag('td', __('Data tidak ditemukan'), array(
                            'colspan' => 7,
                            'class' => 'text-center alert alert-warning',
                        )));
                    }
                ?>
            </tbody>
        </table>
    </div>
    <?php
            echo $this->element('pagination', array(
                'options' => array(
                    'urlClass' => 'ajaxCustomModal',
                    'urlTitle' => __('Daftar Barang'),
                ),
            ));
    ?>
</div>