<div class="box">
    <?php
            echo $this->element('blocks/common/searchs/box_header');
    ?>
    <div class="box-body">
        <?php 
                echo $this->Form->create('Search', array(
                    'url'=> $this->Html->url( array(
                        'controller' => 'products',
                        'action' => 'search',
                        'receipts',
                    )), 
                ));
        ?>
        <div class="row">
            <div class="col-sm-6">
                <?php 
                        echo $this->Common->buildInputForm('nodoc', __('No Dokumen'));
                        echo $this->Common->buildInputForm('vendor_id', __('Vendor'), array(
                            'empty' => __('- Pilih Vendor -'),
                            'class' => 'form-control chosen-select',
                        ));
                ?>
            </div>
            <div class="col-sm-6">
                <?php 
                        echo $this->Common->buildInputForm('date', __('Tanggal'), array(
                            'textGroup' => $this->Common->icon('calendar'),
                            'positionGroup' => 'positionGroup',
                            'class' => 'form-control pull-right date-range',
                        ));
                        echo $this->Common->buildInputForm('status', __('Status'), array(
                            'empty' => __('Pilih Status'),
                            'options' => array(
                                'unpaid' => __('Belum dibayar'),
                                'half_paid' => __('Dibayar sebagian'),
                                'paid' => __('Sudah dibayar'),
                            ),
                        ));
                        echo $this->element('blocks/common/searchs/box_action', array(
                            '_url' => array(
                                'controller' => 'products', 
                                'action' => 'receipts', 
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