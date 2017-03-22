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
                        'supplier_quotations',
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
                        echo $this->Common->buildInputForm('vendor_id', __('Supplier'), array(
                            'empty' => __('- Pilih Supplier -'),
                        ));
                ?>
            </div>
            <div class="col-sm-6">
                <?php 
                        echo $this->Common->buildInputForm('nodoc', __('No Penawaran'));
                        echo $this->Common->buildInputForm('status', __('Status'), array(
                            'empty' => __('- Pilih Status -'),
                            'options' => array(
                                'unposting' => __('Draft'),
                                'posting' => __('Commit'),
                                'approved' => __('Approved'),
                                'rejected' => __('Ditolak'),
                                'revised' => __('Direvisi'),
                                'expired' => __('Expired'),
                                'void' => __('Void'),
                            ),
                        ));
                ?>
            </div>
        </div>
        <?php 
                echo $this->element('blocks/common/searchs/box_action', array(
                    '_url' => array(
                        'controller' => 'purchases', 
                        'action' => 'supplier_quotations', 
                    ),
                ));
                echo $this->Form->end();
        ?>
    </div>
</div>