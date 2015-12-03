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
                        echo $this->Common->buildInputForm('nodoc', __('No Dokumen'));
                        echo $this->Common->buildInputForm('vendor_id', __('Vendor'), array(
                            'empty' => __('- Pilih Vendor -'),
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
                ?>
            </div>
        </div>
        <?php 
                echo $this->element('blocks/common/searchs/box_action', array(
                    '_url' => array(
                        'controller' => 'purchases', 
                        'action' => 'purchase_orders', 
                    ),
                ));
                echo $this->Form->end();
        ?>
    </div>
</div>