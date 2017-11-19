<?php 
        $dataColumns = array(
            'progress_bar' => array(
                'name' => __('Progress'),
                'width' => '25%',
            ),
            'Periode' => array(
                'name' => __('Periode'),
                'class' => 'text-center',
                'width' => '10%',
            ),
            'closing_by' => array(
                'name' => __('DiClosing Oleh'),
                'class' => 'text-center',
                'width' => '15%',
            ),
            'created' => array(
                'name' => __('Dibuat'),
                'class' => 'text-center',
                'width' => '10%',
            ),
            'action' => array(
                'name' => __('Action'),
                'class' => 'text-center',
                'width' => '10%',
            ),
        );
        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );
?>
<div class="box">
    <?php 
            echo $this->element('blocks/common/box_header', array(
                'title' => __('Closing Bank'),
            ));
    ?>
    <div class="box-body">
        <?php 
                echo $this->Form->create('Journal', array(
                    'role' => 'form',
                    'inputDefaults' => array('div' => false),
                ));
        ?>
        <div class="row">
            <div class="col-sm-12">
                <?php 
                        echo $this->Form->label('date', __('Periode'));
                ?>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <?php 
                                    echo $this->Form->month('periode', array(
                                        'label'=> false, 
                                        'class'=>'form-control',
                                        'required' => false,
                                        'empty' => false,
                                    ));
                            ?>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <?php 
                                    echo $this->Form->year('periode', 1949, date('Y'), array(
                                        'label'=> false, 
                                        'class'=>'form-control',
                                        'required' => false,
                                        'empty' => false,
                                    ));
                            ?>
                        </div>
                    </div>
                </div>
                <!-- <div class="loading-progress"></div> -->
                <div class="form-group action">
                    <?php
                            echo $this->Form->button('<i class="fa fa-search"></i> '.__('Closing'), array(
                                'div' => false, 
                                'class'=> 'btn btn-success',
                                'type' => 'submit',
                            ));
                    ?>
                </div>
            </div>
        </div>
        <?php 
                echo $this->Form->end();
        ?>
    </div>
</div>
<div class="box">
    <?php 
            echo $this->element('blocks/common/box_header', array(
                'title' => __('Daftar Closing Bank'),
            ));
    ?>
    <div class="box-body table-responsive">
        <table class="table table-hover sorting">
            <?php
                    if( !empty($fieldColumn) ) {
                        echo $this->Html->tag('thead', $this->Html->tag('tr', $fieldColumn));
                    }
            ?>
            <tbody>
            <?php
                    if(!empty($values)){
                        foreach ($values as $key => $value) {
                            $id = $this->Common->filterEmptyField($value, 'CoaClosingQueue', 'id');
                            $name = $this->Common->filterEmptyField($value, 'Employe', 'full_name');
                            $periode = $this->Common->filterEmptyField($value, 'CoaClosingQueue', 'periode');
                            $progress = $this->Common->filterEmptyField($value, 'CoaClosingQueue', 'progress', 0);
                            $transaction_status = $this->Common->filterEmptyField($value, 'CoaClosingQueue', 'transaction_status');
                            $created = $this->Common->filterEmptyField($value, 'CoaClosingQueue', 'created');

                            $periode = $this->Common->formatDate($periode, 'F Y');
                            $created = $this->Common->formatDate($created);
                            $customStatus = $this->Common->_callTransactionStatus($value, 'CoaClosingQueue');
                            $contentProgress = $this->Common->_callProgressBar($transaction_status, $progress);

                            if( $transaction_status == 'completed' ) {
                                $action = false;
                            } else {
                                $action = $this->Html->link(__('Batalkan'), array(
                                    'action' => 'closing_toggle',
                                    $id,
                                    'admin' => false,
                                ), array(
                                    'class' => 'btn btn-danger btn-xs',
                                ), __('Anda yakin ingin proses closing ini?'));
                            }

                            echo $this->Html->tableCells(array(
                                array(
                                    array(
                                        $contentProgress,
                                        array(
                                            'class' => 'text-center text-middle',
                                        ),
                                    ),
                                    array(
                                        $periode,
                                        array(
                                            'class' => 'text-center text-middle',
                                        ),
                                    ),
                                    array(
                                        $name,
                                        array(
                                            'class' => 'text-middle',
                                        ),
                                    ),
                                    array(
                                        $created,
                                        array(
                                            'class' => 'text-center text-middle',
                                        ),
                                    ),
                                    array(
                                        $action,
                                        array(
                                            'class' => 'action text-center text-middle',
                                        ),
                                    ),
                                )
                            ));
                        }
                    } else {
                        echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                            'class' => 'alert alert-warning text-center',
                            'colspan' => '10'
                        )));
                    }
            ?>
            </tbody>
        </table>
    </div>
    <?php echo $this->element('pagination');?>
</div>