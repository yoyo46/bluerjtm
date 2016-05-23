<?php
        $title = !empty($title)?$title:__('Approval Kas/Bank');
        $value = !empty($value)?$value:false;
        $modelName = !empty($modelName)?$modelName:false;
        $urlBack = isset($urlBack)?$urlBack:array(
            'controller' => 'cashbanks', 
            'action' => 'index', 
        );
        $transaction_status = $this->Common->filterEmptyField($value, $modelName, 'transaction_status');
        $description = $this->Common->filterEmptyField($value, 'DocumentAuthCurrent', 'description');

        switch ($transaction_status) {
            case 'revised':
                $contentAlert = $this->Html->tag('h4', __('Revisi Dokumen'));
                $contentAlert .= $this->Html->tag('p', __('Dokumen ini diperlukan revisi, pastikan dokumen telah sesuai dan memenui syarat'));

                if( !empty($description) ) {
                    $contentAlert .= $this->Html->tag('p', sprintf(__('%s: %s'), $this->Html->tag('strong', __('Alasan direvisi')), $this->Common->getFormatDesc($description)));
                }

                echo $this->Html->tag('div', $contentAlert, array(
                    'class' => 'alert alert-warning',
                ));
                break;
            case 'rejected':
                $contentAlert = $this->Html->tag('h4', __('Dokumen Ditolak'));
                $contentAlert .= $this->Html->tag('p', __('Mohon maaf, Dokumen telah ditolak. Ada tidak dapat melanjutkan/mengubah dokumen ini.'));

                if( !empty($description) ) {
                    $contentAlert .= $this->Html->tag('p', sprintf(__('%s: %s'), $this->Html->tag('strong', __('Alasan penolakan')), $this->Common->getFormatDesc($description)));
                }

                echo $this->Html->tag('div', $contentAlert, array(
                    'class' => 'alert alert-danger',
                ));
                break;
        }

        if( !empty($user_otorisasi_approvals) ){
            $dataColumns = array(
                'position' => array(
                    'name' => __('Posisi yang menyetujui'),
                ),
                'priority' => array(
                    'name' => __('Prioritas Approval'),
                    'class' => 'text-center',
                ),
                'status' => array(
                    'name' => __('Status'),
                    'class' => 'text-center',
                ),
                'note' => array(
                    'name' => __('Keterangan'),
                    'class' => 'text-center',
                ),
            );
            $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );
?>
<div id="list-approval">
    <div class="box box-success">
        <?php 
                echo $this->element('blocks/common/box_header', array(
                    'title' => $title,
                ));
        ?>
        <div class="box-body table-responsive">
            <table class="table table-hover">
                <?php
                        if( !empty($fieldColumn) ) {
                            echo $this->Html->tag('thead', $this->Html->tag('tr', $fieldColumn));
                        }
                ?>
                <tbody>
                    <?php
                            foreach ($user_otorisasi_approvals as $key => $val) {
                                $position_name = $this->Common->filterEmptyField($val, 'EmployePosition', 'name');
                                $is_priority = $this->Common->filterEmptyField($val, 'ApprovalDetailPosition', 'is_priority');
                                $description = $this->Common->filterEmptyField($val, 'DocumentAuth', 'description', '-');
                                $status_document = $this->Common->_callStatusAuth($val);

                                if( !empty($is_priority) ) {
                                    $labelCheck = $this->Common->icon('check', false, 'i', 'text-green');
                                } else {
                                    $labelCheck = $this->Common->icon('times', false, 'i', 'text-red');
                                }
                    ?>
                    <tr>
                        <?php 
                                echo $this->Html->tag('td', $position_name);
                                echo $this->Html->tag('td', $labelCheck, array(
                                    'class' => 'text-center',
                                ));
                                echo $this->Html->tag('td', $status_document, array(
                                    'class' => 'text-center',
                                ));
                                echo $this->Html->tag('td', $this->Common->getFormatDesc($description));
                        ?>
                    </tr>
                    <?php
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
        if( !empty($show_approval) && empty($completed) ){
?>
<div class="box box-success">
    <?php 
            echo $this->element('blocks/common/box_header', array(
                'title' => __('Approval Form'),
            ));
    ?>
    <div class="box-body">
        <?php
                echo $this->Form->create('DocumentAuth', array(
                    'url'=> $this->Html->url( null, true ), 
                    'role' => 'form',
                    'inputDefaults' => array('div' => false),
                ));
                echo $this->Form->input('status_document', array(
                    'label' => __('Status Approval *'),
                    'div' => array(
                        'class' => 'form-group'
                    ),
                    'options' => array(
                        'approve' => 'Setujui',
                        'revise' => 'Direvisi',
                        'reject' => 'Ditolak',
                    ),
                    'class' => 'form-control',
                    'empty' => __('Pilih Status Approval'),
                ));

                echo $this->Form->input('description', array(
                    'label' => __('Keterangan'),
                    'div' => array(
                        'class' => 'form-group'
                    ),
                    'type' => 'textarea',
                    'class' => 'form-control'
                ));
        ?>
        <div class="box-footer text-center action">
            <?php
                    echo $this->Form->button(__('Simpan'), array(
                        'div' => false, 
                        'class'=> 'btn btn-success',
                        'type' => 'submit',
                    ));
                    echo $this->Html->link(__('Kembali'), $urlBack, array(
                        'class'=> 'btn btn-default',
                    ));
            ?>
        </div>
        <?php

            echo $this->Form->end();
        ?>
    </div>
</div>
<?php
            } else if( !empty($urlBack) ) {
                echo $this->Html->tag('div', $this->Html->link(__('Kembali'), $urlBack, array(
                    'class'=> 'btn btn-default',
                )), array(
                    'class'=> 'box-footer text-center action',
                ));
            }
        }
?>