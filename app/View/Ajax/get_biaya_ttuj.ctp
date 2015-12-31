<?php 
        echo $this->Form->create('Search', array(
            'url'=> $this->Html->url(array(
                'controller' => 'ajax',
                'action' => 'search',
                'getBiayaTtuj',
                'action_type' => $action_type,
                'payment_id' => $payment_id,
                'admin' => false,
            )), 
            'role' => 'form',
            'inputDefaults' => array('div' => false),
        ));
?>
<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <?php 
                    echo $this->Form->input('date',array(
                        'label'=> __('Tanggal'),
                        'class'=>'form-control date-range',
                        'required' => false,
                        'placeholder' => __('Tanggal')
                    ));
            ?>
        </div>
        <div class="form-group">
            <?php 
                    echo $this->Form->input('nodoc',array(
                        'label'=> __('No. Doc'),
                        'class'=>'form-control',
                        'required' => false,
                        'placeholder' => __('No. Doc')
                    ));
            ?>
        </div>
        <div class="form-group">
            <?php 
                    echo $this->Form->input('from_city',array(
                        'label'=> __('Kota Asal'),
                        'class'=>'form-control',
                        'required' => false,
                        'empty' => __('Pilih Kota Asal'),
                        'options' => $cities,
                    ));
            ?>
        </div>
        <div class="form-group">
            <?php 
                    echo $this->Form->label('type', __('Truk'));
            ?>
            <div class="row">
                <div class="col-sm-4">
                    <?php 
                            echo $this->Form->input('type',array(
                                'label'=> false,
                                'class'=>'form-control',
                                'required' => false,
                                'empty' => false,
                                'options' => array(
                                    '1' => __('Nopol'),
                                    '2' => __('ID Truk'),
                                ),
                            ));
                    ?>
                </div>
                <div class="col-sm-8">
                    <?php 
                            echo $this->Form->input('nopol',array(
                                'label'=> false,
                                'class'=>'form-control',
                                'required' => false,
                            ));
                    ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <?php 
                    echo $this->Form->input('note',array(
                        'type' => 'text',
                        'label'=> __('Keterangan'),
                        'class'=>'form-control',
                        'required' => false,
                    ));
            ?>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <?php 
                    echo $this->Form->input('driver',array(
                        'label'=> __('Nama Supir'),
                        'class'=>'form-control',
                        'required' => false,
                        'placeholder' => __('Nama Supir')
                    ));
            ?>
        </div>
        <div class="form-group">
            <?php 
                    echo $this->Form->input('customer',array(
                        'label'=> __('Customer'),
                        'class'=>'form-control',
                        'required' => false,
                        'placeholder' => __('Customer')
                    ));
            ?>
        </div>
        <div class="form-group">
            <?php 
                    echo $this->Form->input('to_city',array(
                        'label'=> __('Kota Tujuan'),
                        'class'=>'form-control',
                        'required' => false,
                        'empty' => __('Pilih Kota Tujuan'),
                        'options' => $cities,
                    ));
            ?>
        </div>
        <div class="form-group">
            <div class="row">
                <?php 
                        if( $action_type == 'biaya_ttuj' ) {
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
                        } else {
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
                        }
                ?>
            </div>
        </div>
        <div class="form-group action">
            <?php
                    echo $this->Form->button('<i class="fa fa-search"></i> '.__('Cari'), array(
                        'div' => false, 
                        'class'=> 'btn btn-success btn-sm ajaxModal',
                        'data-action' => $data_action,
                        'data-parent' => true,
                        'title' => $title,
                    ));
                    echo $this->Html->link('<i class="fa fa-refresh"></i> '.__('Reset'), array(
                        'controller' => 'ajax',
                        'action' => 'getBiayaTtuj',
                        $action_type,
                        'payment_id' => $payment_id,
                    ), array(
                        'escape' => false, 
                        'class'=> 'btn btn-default btn-sm ajaxModal',
                        'data-action' => $data_action,
                        'title' => $title,
                    ));
            ?>
        </div>
    </div>
</div>
<?php 
    echo $this->Form->end();
?>
<div class="box-body table-responsive browse-form">
    <table class="table table-hover">
        <tr>
            <?php 
                    $input_all = $this->Form->checkbox('checkbox_all', array(
                        'class' => 'checkAll'
                    ));
                    echo $this->Html->tag('th', $input_all);

                    echo $this->Html->tag('th', __('No TTUJ'));
                    echo $this->Html->tag('th', __('Tgl'), array(
                        'width' => '5%',
                    ));
                    echo $this->Html->tag('th', __('NoPol'));
                    echo $this->Html->tag('th', __('Customer'));
                    echo $this->Html->tag('th', __('Asal'));
                    echo $this->Html->tag('th', __('Tujuan'));
                    echo $this->Html->tag('th', __('Supir'));
                    echo $this->Html->tag('th', __('Jenis'), array(
                        'width' => '15%',
                        'class' => 'text-center',
                    ));
                    echo $this->Html->tag('th', __('Keterangan'));
                    echo $this->Html->tag('th', __('Total'), array(
                        'class' => 'text-center',
                    ));
                    echo $this->Html->tag('th', __('Sisa'), array(
                        'class' => 'text-center',
                    ));
            ?>
        </tr>
        <?php
                if(!empty($ttujs)){
                    foreach ($ttujs as $key => $ttuj) {
                        if( !empty($document_type) ) {
                            $ttujTemp = !empty($this->request->data)?$this->request->data:false;
                        } else {
                            $ttujTemp = !empty($ttuj)?$ttuj:false;
                        }

                        echo $this->element('blocks/ajax/biaya_uang_jalan', array(
                            'ttuj' => $ttuj,
                            'idx' => $key,
                        ));
        ?>
        
        <?php
                    }
                }else{
                    echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                        'class' => 'alert alert-warning text-center',
                        'colspan' => '12'
                    )));
                }
        ?>
    </table>
</div><!-- /.box-body -->
<?php
        echo $this->element('pagination', array(
            'options' => array(
                'data-action' => $data_action,
                'class' => 'ajaxModal',
            ),
        ));
?>