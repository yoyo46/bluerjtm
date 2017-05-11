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
                        'retur',
                    )), 
                ));
        ?>
        <div class="row">
            <div class="col-sm-6">
                <?php 
                        echo $this->Common->buildInputForm('nodoc', __('No. Dokumen'));
                        echo $this->Common->buildInputForm('nodocref', __('No. Dok Ref'));
                        echo $this->Common->buildInputForm('vendor_id', __('Supplier'), array(
                            'empty' => __('- Pilih Supplier -'),
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
                                'action' => 'retur', 
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