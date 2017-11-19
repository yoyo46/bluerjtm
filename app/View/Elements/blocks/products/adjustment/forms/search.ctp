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
                        'adjustment',
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
                        echo $this->Common->buildInputForm('nodoc', __('No Dokumen'));
                ?>
            </div>
            <div class="col-sm-6">
                <?php 
                        echo $this->Common->buildInputForm('status', __('Status'), array(
                            'empty' => __('Pilih Status'),
                            'options' => array(
                                'unposting' => 'Draft',
                                'posting' => 'Commit',
                                'void' => __('Void'),
                            ),
                        ));
                        echo $this->Common->buildInputForm('note', __('Keterangan'));
                        echo $this->element('blocks/common/searchs/box_action', array(
                            '_url' => array(
                                'controller' => 'products', 
                                'action' => 'adjustment', 
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