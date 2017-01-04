<?php 
        if( !empty($coas) ) {
            $coas = array(
                'all' => __('Pilih Semua COA'),
            ) + $coas;
        }
?>
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
                        'cash_flows'
                    )), 
                    'role' => 'form',
                    'inputDefaults' => array('div' => false),
                ));
        ?>
        <div class="row">
            <div class="col-sm-6">
                <?php 
                        echo $this->Common->buildInputForm('date', __('Tgl TTUJ'), array(
                            'class'=>'form-control date-range-custom',
                            'attributes' => array(
                                'data-limit' => '30',
                            ),
                        ));
                ?>
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('journalcoa',array(
                                'label'=> __('COA'),
                                'class'=>'form-control chosen-select',
                                'required' => false,
                                // 'empty' => __('Pilih COA'),
                                'options' => !empty($coas)?$coas:false,
                                'multiple' => true,
                            ));
                    ?>
                </div>
                <?php 
                        // echo $this->Common->_callCheckboxCoas($coas);
                ?>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <?php 
                            // Custom Otorisasi
                            echo $this->Common->getCheckboxBranch();
                    ?>
                </div>
                <?php 
                        echo $this->element('blocks/common/searchs/box_action', array(
                            '_url' => array(
                                'controller' => 'cashbanks', 
                                'action' => 'cash_flows', 
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