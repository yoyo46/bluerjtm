<div class="box">
    <?php
            echo $this->element('blocks/common/searchs/box_header');
    ?>
    <div class="box-body">
        <?php 
                echo $this->Form->create('Search', array(
                    'url'=> $this->Html->url( array(
                        'controller' => 'purchases',
                        'action' => 'search',
                        'purchase_orders',
                    )), 
                ));
        ?>
        <div class="row">
            <div class="col-sm-6">
                <?php 
                        echo $this->Common->buildInputForm('date', __('Tanggal'), array(
                            'textGroup' => $this->Common->icon('calendar'),
                            'positionGroup' => 'positionGroup',
                            'class' => 'form-control pull-right date-range',
                        ));
                        echo $this->Common->buildInputForm('nodoc', __('No PO'));
                        echo $this->Common->buildInputForm('vendor_id', __('Supplier'), array(
                            'empty' => __('- Pilih Supplier -'),
                            'class' => 'form-control chosen-select',
                        ));
                        echo $this->element('blocks/common/searchs/box_action', array(
                            '_url' => array(
                                'controller' => 'purchases', 
                                'action' => 'purchase_orders', 
                            ),
                        ));
                ?>
            </div>
            <div class="col-sm-6">
                <?php 
                        // echo $this->Common->buildInputForm('status', __('Status'), array(
                        //     'empty' => __('Pilih Status'),
                        //     'options' => array(
                        //         'unpaid' => __('Belum dibayar'),
                        //         'half_paid' => __('Dibayar sebagian'),
                        //         'paid' => __('Sudah dibayar'),
                        //     ),
                        // ));
                        echo $this->Common->buildInputForm('status', __('Status'), array(
                            'empty' => __('Pilih Status'),
                            'options' => array(
                                'unposting' => __('Draft'),
                                'posting' => __('Commit'),
                                'approved' => __('Disetujui'),
                                'rejected' => __('Ditolak'),
                                'void' => __('Void'),
                            ),
                        ));
                        echo $this->Common->buildInputForm('payment_status', __('Status Pembayaran'), array(
                            'empty' => __('Pilih Status'),
                            'options' => array(
                                'none' => __('Belum dibayar'),
                                'half_paid' => __('Dibayar sebagian'),
                                'paid' => __('Sudah dibayar'),
                            ),
                        ));
                        echo $this->Common->buildInputForm('receipt_status', __('Status Penerimaan'), array(
                            'empty' => __('Pilih Status'),
                            'options' => array(
                                'none' => __('Belum diterima'),
                                'half' => __('Diterima sebagian'),
                                'full' => __('Sudah Diterima'),
                            ),
                        ));
                        echo $this->Common->buildInputForm('retur_status', __('Status Retur'), array(
                            'empty' => __('Pilih Status'),
                            'options' => array(
                                'none' => __('Belum diretur'),
                                'half' => __('Diretur sebagian'),
                                'full' => __('Sudah Diretur'),
                            ),
                        ));
                ?>
            </div>
        </div>
        <?php 
                echo $this->Form->end();
        ?>
    </div>
</div>