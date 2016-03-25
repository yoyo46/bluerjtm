<?php 
        $cities = !empty($cities)?$cities:false;
?>
<div class="box">
    <?php
            echo $this->element('blocks/common/searchs/box_header');
    ?>
    <div class="box-body">
        <?php 
                echo $this->Form->create('Search', array(
                    'url'=> $this->Html->url( array(
                        'controller' => 'revenues',
                        'action' => 'search',
                        $this->action,
                    )), 
                ));
        ?>
        <div class="row">
            <div class="col-sm-6">
                <?php 
                        echo $this->Common->buildInputForm('nodoc', __('No. Doc'));
                        echo $this->Common->buildInputForm('date', __('Tanggal'), array(
                            'class' => 'date-range form-control',
                        ));
                        echo $this->Common->buildInputForm('status', __('Status Revenue'), array(
                            'empty' => __('Pilih Status Revenue'),
                            'options' => array(
                                'unposting' => 'Unposting',
                                'posting' => 'Posting',
                                'invoiced' => 'Invoiced',
                                'paid' => 'Paid',
                            ),
                        ));
                        echo $this->element('blocks/common/searchs/forms/input_truck');
                ?>
            </div>
            <div class="col-sm-6">
                <?php 
                        echo $this->Common->buildInputForm('nottuj', __('No. TTUJ'));
                        echo $this->Common->buildInputForm('customer', __('Customer'), array(
                            'empty' => __('Pilih Customer'),
                            'options' => $customers,
                            'class' => 'chosen-select form-control',
                        ));
                        echo $this->Common->buildInputForm('noref', __('No. Reference'));
                ?>
                <div class="row">
                    <?php 
                            echo $this->Html->tag('div', $this->Common->buildInputForm('fromcity', __('Dari'), array(
                                'empty' => __('Pilih Kota'),
                                'options' => $cities,
                                'class' => 'chosen-select form-control',
                            )), array(
                                'class' => 'col-sm-6',
                            ));
                            echo $this->Html->tag('div', $this->Common->buildInputForm('tocity', __('Tujuan'), array(
                                'empty' => __('Pilih Kota'),
                                'options' => $cities,
                                'class' => 'chosen-select form-control',
                            )), array(
                                'class' => 'col-sm-6',
                            ));
                    ?>
                </div>
                <?php
                        echo $this->element('blocks/common/forms/submit_action', array(
                            'frameClass' => 'form-group action',
                            'btnClass' => 'btn-sm',
                            'submitText' => sprintf(__('%s Search'), $this->Common->icon('search')),
                            'backText' => sprintf(__('%s Reset'), $this->Common->icon('refresh')),
                            'urlBack' => array(
                                'action' => 'index', 
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