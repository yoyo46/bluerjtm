<div class="box">
    <?php
            echo $this->element('blocks/common/searchs/box_header');
    ?>
    <div class="box-body">
        <?php 
                echo $this->Form->create('Search', array(
                    'url'=> $this->Html->url( array(
                        'controller' => 'ttujs',
                        'action' => 'search',
                        'report_recap_sj'
                    )), 
                    'role' => 'form',
                    'inputDefaults' => array('div' => false),
                ));
        ?>
        <div class="row">
            <div class="col-sm-4">
                <?php 
                        echo $this->Common->buildInputForm('date', __('Tgl Laporan'), array(
                            'class'=>'form-control date-range',
                        ));
                        echo $this->Common->buildInputForm('nodoc', __('No. TTUJ'));
                        echo $this->Common->buildInputForm('customerid', __('Customer'), array(
                            'empty' => __('Pilih Customer'),
                            'options' => $customers,
                            'class' => 'form-control chosen-select',
                        ));
                ?>
                <?php 
                        echo $this->element('blocks/common/searchs/box_action', array(
                            '_url' => array(
                                'controller' => 'ttujs', 
                                'action' => 'report_recap_sj', 
                            ),
                        ));
                ?>
            </div>
            <div class="col-sm-4">
                <?php 
                        echo $this->Common->buildInputForm('daterange', __('Tgl Sj Kembali'), array(
                            'class'=>'form-control date-range',
                        ));
                ?>
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
                <?php 
                        echo $this->Common->buildInputForm('driver', __('Nama Supir'));
                ?>
            </div>
            <div class="col-sm-4">
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
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('status',array(
                                'label'=> __('Status Surat Jalan'),
                                'class'=>'form-control',
                                'required' => false,
                                'empty' => __('Pilih Status'),
                                'options' => array(
                                    'sj_pending' => __('Surat jalan Belum Diterima'),
                                    'sj_receipt' => __('Surat jalan Diterima'),
                                ),
                            ));
                    ?>
                </div>
                <div class="form-group">
                    <?php 
                            // Custom Otorisasi
                            echo $this->Common->getCheckboxBranch();
                    ?>
                </div>
            </div>
        </div>
        <?php 
                echo $this->Form->end();
        ?>
    </div>
</div>