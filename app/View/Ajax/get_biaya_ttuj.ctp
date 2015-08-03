<?php 
        echo $this->Form->create('Ttuj', array(
            'url'=> $this->Html->url(array(
                'controller' => 'ajax',
                'action' => 'getBiayaTtuj',
                $action_type,
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
                        'placeholder' => __('Dari')
                    ));
            ?>
        </div>
        <div class="form-group">
            <?php 
                    echo $this->Form->input('nottuj',array(
                        'label'=> __('No. Doc'),
                        'class'=>'form-control',
                        'required' => false,
                        'placeholder' => __('No. Doc')
                    ));
            ?>
        </div>
        <div class="form-group">
            <?php 
                    echo $this->Form->input('Ttuj.from_city',array(
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
    </div>
    <div class="col-sm-6">
        <!-- <div class="form-group">
            <?php 
                    // echo $this->Form->input('City.name',array(
                    //     'label'=> __('Tujuan'),
                    //     'class'=>'form-control',
                    //     'required' => false,
                    //     'placeholder' => __('Tujuan')
                    // ));
            ?>
        </div> -->
        <div class="form-group">
            <?php 
                    echo $this->Form->input('Driver.name',array(
                        'label'=> __('Nama Supir'),
                        'class'=>'form-control',
                        'required' => false,
                        'placeholder' => __('Nama Supir')
                    ));
            ?>
        </div>
        <div class="form-group">
            <?php 
                    echo $this->Form->input('Customer.name',array(
                        'label'=> __('Customer'),
                        'class'=>'form-control',
                        'required' => false,
                        'placeholder' => __('Customer')
                    ));
            ?>
        </div>
        <div class="form-group">
            <?php 
                    echo $this->Form->input('Ttuj.to_city',array(
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
                    echo $this->Html->tag('th', __('Total'));
                    echo $this->Html->tag('th', __('Sisa'), array(
                        'width' => '25%',
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

                        switch ($action_type) {
                            case 'biaya_ttuj':
                                if( !empty($ttujTemp['Ttuj']['uang_kuli_muat']) && !empty($ttuj['Ttuj']['uang_kuli_muat']) && $ttuj['Ttuj']['paid_uang_kuli_muat'] != 'full' ) {
                                    echo $this->element('blocks/ajax/biaya_uang_jalan', array(
                                        'ttuj' => $ttuj,
                                        'data_type' => 'uang_kuli_muat',
                                        'idx' => $key,
                                    ));
                                }
                                if( !empty($ttujTemp['Ttuj']['uang_kuli_bongkar']) && !empty($ttuj['Ttuj']['uang_kuli_bongkar']) && $ttuj['Ttuj']['paid_uang_kuli_bongkar'] != 'full' ) {
                                    echo $this->element('blocks/ajax/biaya_uang_jalan', array(
                                        'ttuj' => $ttuj,
                                        'data_type' => 'uang_kuli_bongkar',
                                        'idx' => $key,
                                    ));
                                }
                                if( !empty($ttujTemp['Ttuj']['asdp']) && !empty($ttuj['Ttuj']['asdp']) && $ttuj['Ttuj']['paid_asdp'] != 'full' ) {
                                    echo $this->element('blocks/ajax/biaya_uang_jalan', array(
                                        'ttuj' => $ttuj,
                                        'data_type' => 'asdp',
                                        'idx' => $key,
                                    ));
                                }
                                if( !empty($ttujTemp['Ttuj']['uang_kawal']) && !empty($ttuj['Ttuj']['uang_kawal']) && $ttuj['Ttuj']['paid_uang_kawal'] != 'full' ) {
                                    echo $this->element('blocks/ajax/biaya_uang_jalan', array(
                                        'ttuj' => $ttuj,
                                        'data_type' => 'uang_kawal',
                                        'idx' => $key,
                                    ));
                                }
                                if( !empty($ttujTemp['Ttuj']['uang_keamanan']) && !empty($ttuj['Ttuj']['uang_keamanan']) && $ttuj['Ttuj']['paid_uang_keamanan'] != 'full' ) {
                                    echo $this->element('blocks/ajax/biaya_uang_jalan', array(
                                        'ttuj' => $ttuj,
                                        'data_type' => 'uang_keamanan',
                                        'idx' => $key,
                                    ));
                                }
                                break;
                            
                            default:
                                if( !empty($ttujTemp['Ttuj']['uang_jalan_1']) && !empty($ttuj['Ttuj']['uang_jalan_1']) && $ttuj['Ttuj']['paid_uang_jalan'] != 'full' ) {
                                    echo $this->element('blocks/ajax/biaya_uang_jalan', array(
                                        'ttuj' => $ttuj,
                                        'data_type' => 'uang_jalan',
                                        'idx' => $key,
                                    ));
                                }
                                if( !empty($ttujTemp['Ttuj']['uang_jalan_2']) && !empty($ttuj['Ttuj']['uang_jalan_2']) && $ttuj['Ttuj']['paid_uang_jalan_2'] != 'full' ) {
                                    echo $this->element('blocks/ajax/biaya_uang_jalan', array(
                                        'ttuj' => $ttuj,
                                        'data_type' => 'uang_jalan_2',
                                        'idx' => $key,
                                    ));
                                }
                                if( !empty($ttujTemp['Ttuj']['uang_jalan_extra']) && !empty($ttuj['Ttuj']['uang_jalan_extra']) && $ttuj['Ttuj']['paid_uang_jalan_extra'] != 'full' ) {
                                    echo $this->element('blocks/ajax/biaya_uang_jalan', array(
                                        'ttuj' => $ttuj,
                                        'data_type' => 'uang_jalan_extra',
                                        'idx' => $key,
                                    ));
                                }
                                if( !empty($ttujTemp['Ttuj']['commission']) && !empty($ttuj['Ttuj']['commission']) && $ttuj['Ttuj']['paid_commission'] != 'full' && !empty($ttuj['Ttuj']['is_sj_completed']) ) {
                                    echo $this->element('blocks/ajax/biaya_uang_jalan', array(
                                        'ttuj' => $ttuj,
                                        'data_type' => 'commission',
                                        'idx' => $key,
                                    ));
                                }
                                if( !empty($ttujTemp['Ttuj']['commission_extra']) && !empty($ttuj['Ttuj']['commission_extra']) && $ttuj['Ttuj']['paid_commission_extra'] != 'full' && !empty($ttuj['Ttuj']['is_sj_completed']) ) {
                                    echo $this->element('blocks/ajax/biaya_uang_jalan', array(
                                        'ttuj' => $ttuj,
                                        'data_type' => 'commission_extra',
                                        'idx' => $key,
                                    ));
                                }
                                break;
                        }
        ?>
        
        <?php
                    }
                }else{
                    echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                        'class' => 'alert alert-warning text-center',
                        'colspan' => '11'
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