<div class="box">
    <?php
            echo $this->element('blocks/common/searchs/box_header');
    ?>
    <div class="box-body">
        <?php 
                echo $this->Form->create('Search', array(
                    'url'=> $this->Html->url( array(
                        'controller' => 'cashbanks',
                        'action' => 'search',
                        'general_ledgers',
                    )), 
                ));
        ?>
        <div class="row">
            <div class="col-sm-4">
                <?php 
                        echo $this->Common->buildInputForm('date', __('Tanggal'), array(
                            'textGroup' => $this->Common->icon('calendar'),
                            'positionGroup' => 'positionGroup',
                            'class' => 'form-control pull-right date-range',
                        ));
                ?>
            </div>
            <div class="col-sm-4">
                <?php 
                        echo $this->Common->buildInputForm('nodoc', __('No Dokumen'));
                ?>
            </div>
            <div class="col-sm-4">
                <?php 
                        echo $this->Common->_callInputForm('status', array(
                            'label' => __('Status Dokumen'),
                            'empty' => __('Pilih Status'),
                            'options' => array(
                                'unposting' => __('Draft'),
                                'posting' => __('Commit'),
                                'void' => __('Void'),
                            ),
                        ));
                ?>
            </div>
        </div>
        <?php 
                echo $this->element('blocks/common/searchs/box_action', array(
                    '_url' => array(
                        'controller' => 'cashbanks', 
                        'action' => 'general_ledgers', 
                    ),
                ));
                echo $this->Form->end();
        ?>
    </div>
</div>