<?php 
        $dataColumns = array(
            'noref' => array(
                'name' => __('No. Referensi'),
            ),
            'nodoc' => array(
                'name' => __('No. Dokumen'),
            ),
            'date' => array(
                'name' => __('Tgl Terima'),
                'class' => 'text-center',
            ),
            'ttuj' => array(
                'name' => __('TTUJ Diterima'),
                'class' => 'text-center',
            ),
            'status' => array(
                'name' => __('Status'),
                'class' => 'text-center',
            ),
            'action' => array(
                'name' => __('Action'),
                'class' => 'text-center',
            ),
        );
        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/ttuj/searchs/bon_biru');
?>
<div class="box">
    <?php 
            echo $this->element('blocks/common/box_header', array(
                'title' => $sub_module_title,
                '_add' => array(
                    'controller' => 'ttujs',
                    'action' => 'bon_biru_add',
                ),
            ));
    ?>
    <div class="box-body table-responsive">
        <table class="table table-hover">
            <?php
                    if( !empty($fieldColumn) ) {
                        echo $this->Html->tag('thead', $this->Html->tag('tr', $fieldColumn));
                    }

                    if(!empty($values)){
                        foreach ($values as $key => $value) {
                            $id = $this->Common->filterEmptyField($value, 'BonBiru', 'id');
                            $nodoc = $this->Common->filterEmptyField($value, 'BonBiru', 'nodoc', '-');
                            $cnt_ttuj = $this->Common->filterEmptyField($value, 'BonBiru', 'cnt_ttuj');
                            $date = $this->Common->filterEmptyField($value, 'BonBiru', 'tgl_bon_biru');

                            $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
                            $date = $this->Common->formatDate($date, 'd M Y');
                            $status = $this->Common->_callStatusVoid($value, 'BonBiru');
                            $actionBtn = $this->Common->_callActionButtn($value, 'BonBiru', array(
                                'Detail' => array(
                                    'label' => __('Detail'),
                                    'url' => array(
                                        'controller' => 'ttujs',
                                        'action' => 'bon_biru_detail',
                                        $id,
                                    ),
                                ),
                                'Edit' => array(
                                    'label' => __('Edit'),
                                    'url' => array(
                                        'controller' => 'ttujs',
                                        'action' => 'bon_biru_edit',
                                        $id,
                                    ),
                                ),
                                'Void' => array(
                                    'label' => __('Void'),
                                    'url' => array(
                                        'controller' => 'ttujs',
                                        'action' => 'bon_biru_delete',
                                        $id,
                                    ),
                                    'title' => __('Void Bon Biru'),
                                ),
                            ));
            ?>
            <tr>
                <?php 
                        echo $this->Html->tag('td', $noref);
                        echo $this->Html->tag('td', $nodoc);
                        echo $this->Html->tag('td', $date, array(
                            'class' => 'text-center'
                        ));
                        echo $this->Html->tag('td', $cnt_ttuj, array(
                            'class' => 'text-center'
                        ));
                        echo $this->Html->tag('td', $status, array(
                            'class' => 'text-center'
                        ));
                        echo $this->Html->tag('td', $actionBtn, array(
                            'class' => 'text-center action',
                        ));
                ?>
            </tr>
            <?php
                        }
                    } else {
                        echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                            'class' => 'alert alert-warning text-center',
                            'colspan' => '8'
                        )));
                    }
            ?>
        </table>
    </div>
    <?php echo $this->element('pagination');?>
</div>