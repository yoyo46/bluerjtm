<div class="box">
    <?php
            echo $this->element('blocks/common/searchs/box_header');
    ?>
    <div class="box-body">
        <?php 
                echo $this->Form->create('Search', array(
                    'url'=> $this->Html->url( array(
                        'controller' => 'spk',
                        'action' => 'search',
                        'spk_reports',
                    )), 
                    'class' => 'form-search',
                ));
        ?>
        <div class="row">
            <div class="col-sm-6">
                <?php 
                        echo $this->Common->buildInputForm('nodoc', __('No SPK'));
                        echo $this->Common->buildInputForm('document_type', __('Jenis SPK'), array(
                            'empty' => __('- Pilih Jenis -'),
                            'class' => 'form-control chosen-select',
                            'options' => array(
                                'internal' => __('Internal'),
                                'eksternal' => __('Eksternal'),
                                'wht' => __('WHT'),
                                'production' => __('Produksi'),
                            ),
                        ));
                        echo $this->Common->buildInputForm('product_code', __('Kode Barang'));
                ?>
            </div>
            <div class="col-sm-6">
                <?php 
                        echo $this->Common->buildInputForm('date', __('Tanggal'), array(
                            'textGroup' => $this->Common->icon('calendar'),
                            'positionGroup' => 'positionGroup',
                            'class' => 'form-control pull-right date-range',
                        ));
                        echo $this->Common->buildInputForm('nopol', __('No Pol'));
                        echo $this->Common->buildInputForm('status', __('Status'), array(
                            'empty' => __('Pilih Status'),
                            'options' => array(
                                'open' => __('Open'),
                                'closed' => __('Closed'),
                                'finish' => __('Finish'),
                            ),
                        ));
                        echo $this->element('blocks/common/searchs/box_action', array(
                            '_url' => array(
                                'controller' => 'spk', 
                                'action' => 'spk_reports', 
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