<?php
        $cities = !empty($cities)?$cities:false;
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title">Pencarian</h3>
        <div class="box-tools pull-right">
            <button class="btn btn-default btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <?php 
                echo $this->Form->create('Search', array(
                    'url'=> $this->Html->url( array(
                        'controller' => 'revenues',
                        'action' => 'search',
                        'report_ttuj_outstanding'
                    )), 
                    'role' => 'form',
                    'inputDefaults' => array('div' => false),
                    'id' => 'form-search',
                    'autocomplete'=> 'off', 
                ));
        ?>
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    <?php 
                            echo $this->Form->label('date', __('Tanggal TTUJ'));
                    ?>
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <?php 
                                echo $this->Form->input('date',array(
                                    'type' => 'text',
                                    'label'=> false,
                                    'class'=>'form-control pull-right date-range',
                                    'required' => false,
                                ));
                        ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('nodoc',array(
                                'label'=> __('No Dokumen'),
                                'class'=>'form-control',
                                'required' => false,
                            ));
                    ?>
                </div>
                <div class="form-group">
                    <div class="row">
                        <?php 
                                echo $this->Html->tag('div', $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('uang_jalan_1', array(
                                    'type' => 'checkbox',
                                    'label'=> false,
                                    'required' => false,
                                    'value' => 1,
                                    'div' => false,
                                )).__('Uang Jalan ke 1')), array(
                                    'class' => 'checkbox',
                                )), array(
                                    'class' => 'col-sm-6',
                                ));
                                echo $this->Html->tag('div', $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('uang_jalan_2', array(
                                    'type' => 'checkbox',
                                    'label'=> false,
                                    'required' => false,
                                    'value' => 1,
                                    'div' => false,
                                )).__('Uang Jalan ke 2')), array(
                                    'class' => 'checkbox',
                                )), array(
                                    'class' => 'col-sm-6',
                                ));
                                echo $this->Html->tag('div', $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('uang_jalan_extra', array(
                                    'type' => 'checkbox',
                                    'label'=> false,
                                    'required' => false,
                                    'value' => 1,
                                    'div' => false,
                                )).__('Uang Jalan Extra')), array(
                                    'class' => 'checkbox',
                                )), array(
                                    'class' => 'col-sm-6',
                                ));
                                echo $this->Html->tag('div', $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('commission', array(
                                    'type' => 'checkbox',
                                    'label'=> false,
                                    'required' => false,
                                    'value' => 1,
                                    'div' => false,
                                )).__('Komisi')), array(
                                    'class' => 'checkbox',
                                )), array(
                                    'class' => 'col-sm-6',
                                ));
                                echo $this->Html->tag('div', $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('commission_extra', array(
                                    'type' => 'checkbox',
                                    'label'=> false,
                                    'required' => false,
                                    'value' => 1,
                                    'div' => false,
                                )).__('Komisi Extra')), array(
                                    'class' => 'checkbox',
                                )), array(
                                    'class' => 'col-sm-6',
                                ));
                                echo $this->Html->tag('div', $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('uang_kuli_muat', array(
                                    'type' => 'checkbox',
                                    'label'=> false,
                                    'required' => false,
                                    'value' => 1,
                                    'div' => false,
                                )).__('Uang Kuli Muat')), array(
                                    'class' => 'checkbox',
                                )), array(
                                    'class' => 'col-sm-6',
                                ));
                                echo $this->Html->tag('div', $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('uang_kuli_bongkar', array(
                                    'type' => 'checkbox',
                                    'label'=> false,
                                    'required' => false,
                                    'value' => 1,
                                    'div' => false,
                                )).__('Uang Kuli Bongkar')), array(
                                    'class' => 'checkbox',
                                )), array(
                                    'class' => 'col-sm-6',
                                ));
                                echo $this->Html->tag('div', $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('asdp', array(
                                    'type' => 'checkbox',
                                    'label'=> false,
                                    'required' => false,
                                    'value' => 1,
                                    'div' => false,
                                )).__('Uang Penyebrangan')), array(
                                    'class' => 'checkbox',
                                )), array(
                                    'class' => 'col-sm-6',
                                ));
                                echo $this->Html->tag('div', $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('uang_kawal', array(
                                    'type' => 'checkbox',
                                    'label'=> false,
                                    'required' => false,
                                    'value' => 1,
                                    'div' => false,
                                )).__('Uang Kawal')), array(
                                    'class' => 'checkbox',
                                )), array(
                                    'class' => 'col-sm-6',
                                ));
                                echo $this->Html->tag('div', $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('uang_keamanan', array(
                                    'type' => 'checkbox',
                                    'label'=> false,
                                    'required' => false,
                                    'value' => 1,
                                    'div' => false,
                                )).__('Uang Keamanan')), array(
                                    'class' => 'checkbox',
                                )), array(
                                    'class' => 'col-sm-6',
                                ));
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('fromcity',array(
                                'label'=> __('Asal'),
                                'class'=>'form-control chosen-select',
                                'required' => false,
                                'options' => $cities,
                                'empty' => __('Pilih Kota Asal'),
                            ));
                    ?>
                </div>
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('note',array(
                                'label'=> __('Keterangan'),
                                'class'=>'form-control',
                                'required' => false,
                            ));
                    ?>
                </div>
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('status',array(
                                'label'=> __('Status'),
                                'class'=>'form-control',
                                'required' => false,
                                'options' => array(
                                    'paid' => __('Sudah dibayar'),
                                    'unpaid' => __('Belum dibayar'),
                                ),
                                'empty' => __('Semua Status'),
                            ));
                    ?>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('tocity',array(
                                'label'=> __('Tujuan'),
                                'class'=>'form-control chosen-select',
                                'required' => false,
                                'options' => $cities,
                                'empty' => __('Pilih Kota Tujuan'),
                            ));
                    ?>
                </div>
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('nopol',array(
                                'label'=> __('Nopol'),
                                'class'=>'form-control',
                                'required' => false,
                            ));
                    ?>
                </div>
                <div class="form-group">
                    <?php 
                            // Custom Otorisasi
                            echo $this->Common->getCheckboxBranch();
                    ?>
                </div>
                <div class="form-group action">
                    <?php
                            echo $this->Form->button('<i class="fa fa-search"></i> '.__('Submit'), array(
                                'div' => false, 
                                'class'=> 'btn btn-success btn-sm',
                                'type' => 'submit',
                            ));
                            echo $this->Html->link('<i class="fa fa-refresh"></i> '.__('Reset'), array(
                                'action' => 'report_ttuj_outstanding', 
                            ), array(
                                'escape' => false, 
                                'class'=> 'btn btn-default btn-sm',
                            ));
                    ?>
                </div>
            </div>
        </div>
        <?php 
                echo $this->Form->hidden('title',array(
                    'value'=> $sub_module_title,
                ));
                echo $this->Form->end();
        ?>
    </div>
</div>