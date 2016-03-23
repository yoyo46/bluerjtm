<div class="box">
    <?php
            echo $this->element('blocks/common/searchs/box_header');
    ?>
    <div class="box-body">
        <?php 
                echo $this->Form->create('Search', array(
                    'url'=> $this->Html->url( array(
                        'action' => 'search',
                        'index',
                    )), 
                    'role' => 'form',
                    'inputDefaults' => array('div' => false),
                ));
        ?>
        <div class="row">
            <div class="col-sm-6">
                <?php 
                        echo $this->Common->buildInputForm('purchase_date', __('Tanggal pembelian *'), array(
                            'type' => 'text',
                            'class' => 'date-range form-control',
                        ));
                        echo $this->Common->buildInputForm('neraca_date', __('Tanggal neraca *'), array(
                            'type' => 'text',
                            'class' => 'date-range form-control',
                        ));
                        echo $this->Common->buildInputForm('id', __('No. Ref'));
                ?>
            </div>
            <div class="col-sm-6">
                <?php 
                        echo $this->Common->buildInputForm('asset_group_id', __('Group Asset'), array(
                            'empty' => __('Pilih Group Asset'),
                            'class' => 'chosen-select form-control',
                        ));
                        echo $this->Common->buildInputForm('name', __('Nama Asset'));
                        echo $this->element('blocks/common/forms/submit_action', array(
                            'frameClass' => 'form-group action',
                            'btnClass' => 'btn-sm',
                            'submitText' => sprintf(__('%s Search'), $this->Common->icon('search')),
                            'backText' => sprintf(__('%s Reset'), $this->Common->icon('refresh')),
                            'urlBack' => array(
                                'action' => 'groups', 
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