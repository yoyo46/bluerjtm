<?php
        $years = $this->Common->_callPeriodeYear();
?>
<div class="box">
    <?php
            echo $this->element('blocks/common/searchs/box_header');
    ?>
    <div class="box-body">
        <?php 
                echo $this->Form->create('Search', array(
                    'url'=> $this->Html->url( array(
                        'controller' => 'assets',
                        'action' => 'search',
                        'reports'
                    )), 
                    'role' => 'form',
                    'inputDefaults' => array('div' => false),
                ));
        ?>
        <div class="row">
            <div class="col-sm-6">
                <?php 
                        echo $this->Common->buildInputForm('year', __('Tahun'), array(
                            'empty' => __('Pilih Tahun'),
                            'options' => $years,
                        ));
                        echo $this->Common->buildInputForm('asset_group_id', __('Group Asset'), array(
                            'empty' => __('Pilih Group'),
                        ));
                ?>
            </div>
            <div class="col-sm-6">
                <?php 
                        echo $this->Common->buildInputForm('name', __('Nama Asset'));
                        echo $this->Common->buildInputForm('status', __('Status'), array(
                            'empty' => __('Pilih Status'),
                            'options' => array(
                                'available' => __('Available'),
                                'sold' => __('Sold'),
                            ),
                        ));
                        echo $this->element('blocks/common/forms/submit_action', array(
                            'frameClass' => 'form-group action',
                            'btnClass' => 'btn-sm',
                            'submitText' => sprintf(__('%s Search'), $this->Common->icon('search')),
                            'backText' => sprintf(__('%s Reset'), $this->Common->icon('refresh')),
                            'urlBack' => array(
                                'action' => 'reports', 
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