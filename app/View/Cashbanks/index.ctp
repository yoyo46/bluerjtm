<?php 
        $this->Html->addCrumb(__('Kas Bank'));
        echo $this->element('blocks/cashbanks/search_index');
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <?php 
                // if( in_array('insert_cash_bank', $allowModule) ) {
        ?>
        <div class="box-tools">
            <?php
                echo $this->Html->link('<i class="fa fa-plus"></i> Tambah Kas Bank', array(
                    'controller' => 'cashbanks',
                    'action' => 'cashbank_add'
                ), array(
                    'escape' => false,
                    'class' => 'btn btn-app pull-right'
                ));
            ?>
        </div>
        <?php 
                // }
        ?>
    </div><!-- /.box-header -->
    <div class="box-body table-responsive">
        <table class="table table-hover">
            <tr>
                <th>No Dokumen</th>
                <th>Tgl Kas Bank</th>
                <th>Tipe Kas</th>
                <th>Action</th>
            </tr>
            <?php
                if(!empty($cash_banks)){
                    foreach ($cash_banks as $key => $value) {
                        $content = $this->Html->tag('td', $value['CashBank']['nodoc']);
                        $content .= $this->Html->tag('td', $this->Common->customDate($value['CashBank']['tgl_cash_bank'], 'd/m/Y'));
                        $content .= $this->Html->tag('td', $value['CashBank']['receiving_cash_type']);

                        $link = '';
                        if($value['CashBank']['is_revised'] && !$value['CashBank']['completed']){
                            $link .= $this->Html->link('Rubah', array(
                                'controller' => 'cashbanks',
                                'action' => 'cashbank_edit',
                                $value['CashBank']['id']
                            ), array(
                                'escape' => false,
                                'class' => 'btn btn-primary btn-xs'
                            ));
                        }

                        if(!$value['CashBank']['completed']){
                            $link .= $this->Html->link('Hapus', array(
                                'controller' => 'cashbanks',
                                'action' => 'cashbank_delete',
                                $value['CashBank']['id']
                            ), array(
                                'escape' => false,
                                'class' => 'btn btn-danger btn-xs'
                            ));
                        }

                        $link .= $this->Html->link('Info dan Approval', array(
                            'controller' => 'cashbanks',
                            'action' => 'detail',
                            $value['CashBank']['id']
                        ), array(
                            'escape' => false,
                            'class' => 'btn btn-info btn-xs'
                        ));

                        $content .= $this->Html->tag('td', $link, array(
                            'class' => 'action'
                        ));
                        echo $this->Html->tag('tr', $content);
                    }
                }else{
                    $content = $this->Html->tag('td', __('Data tidak ditemukan.'), array(
                        'colspan' => 5,
                        'class' => 'alert alert-danger'
                    ));
                    echo $this->Html->tag('tr', $content);
                }
            ?>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>