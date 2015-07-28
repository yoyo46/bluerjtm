<?php 
        $this->Html->addCrumb(__('Kas/Bank'));
        echo $this->element('blocks/cashbanks/search_index');
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <div class="box-tools">
            <?php
                echo $this->Html->link('<i class="fa fa-plus"></i> Tambah Transaksi Kas/Bank', array(
                    'controller' => 'cashbanks',
                    'action' => 'cashbank_add'
                ), array(
                    'escape' => false,
                    'class' => 'btn btn-app pull-right'
                ));
            ?>
        </div>
    </div><!-- /.box-header -->
    <div class="box-body table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>No Dokumen</th>
                    <th>Diterima/Dibayar kepada</th>
                    <th>Tanggal Transaksi</th>
                    <th style="text-align:center;">Tipe Kas</th>
                    <th style="text-align:center;">Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <?php
                if(!empty($cash_banks)){
                    foreach ($cash_banks as $key => $value) {
                        $content = $this->Html->tag('td', $value['CashBank']['nodoc']);
                        $content .= $this->Html->tag('td', !empty($value['name_cash']) ? $value['name_cash'] : '-');
                        $content .= $this->Html->tag('td', $this->Common->customDate($value['CashBank']['tgl_cash_bank'], 'd/m/Y'));
                        $content .= $this->Html->tag('td', strtoupper(str_replace('_', ' ', $value['CashBank']['receiving_cash_type'])), array(
                            'align' => 'center'
                        ));

                        $status = 'Pending';
                        $class = 'info';
                        if(!empty($value['CashBank']['completed'])){
                            $status = 'Complete';
                            $class = 'success';
                        }else if(!empty($value['CashBank']['is_revised'])){
                            $status = 'Revisi';
                            $class = 'primary';
                        }else if(!empty($value['CashBank']['is_rejected'])){
                            $status = 'Ditolak';
                            $class = 'danger';
                        }

                        $content .= $this->Html->tag('td', '<span class="label label-'.$class.'">'.$status.'</span>', array(
                            'align' => 'center'
                        ));

                        if( !empty($value['CashBank']['is_revised']) ){
                            $link = $this->Html->link(__('Ubah'), array(
                                'controller' => 'cashbanks',
                                'action' => 'cashbank_edit',
                                $value['CashBank']['id'],
                            ), array(
                                'escape' => false,
                                'class' => 'btn btn-info btn-xs'
                            ));
                        } else {
                            $link = $this->Html->link('Detail', array(
                                'controller' => 'cashbanks',
                                'action' => 'detail',
                                $value['CashBank']['id']
                            ), array(
                                'escape' => false,
                                'class' => 'btn btn-primary btn-xs'
                            ));
                        }

                        if( empty($value['CashBank']['completed']) ) {
                            $link .= $this->Html->link('Hapus', array(
                                'controller' => 'cashbanks',
                                'action' => 'cashbank_delete',
                                $value['CashBank']['id']
                            ), array(
                                'escape' => false,
                                'class' => 'btn btn-danger btn-xs'
                            ), __('Anda yakin ingin menghapus data ini?'));
                        }

                        if( in_array($User['id'], $cashbank_auth_id) && empty($value['CashBank']['is_rejected']) && empty($value['CashBank']['completed']) ){
                            $link .= $this->Html->link('Approval', array(
                                'controller' => 'cashbanks',
                                'action' => 'detail',
                                $value['CashBank']['id'],
                                '#' => 'list-approval',
                            ), array(
                                'escape' => false,
                                'class' => 'btn btn-success btn-xs'
                            ));
                        }

                        $content .= $this->Html->tag('td', $link, array(
                            'class' => 'action'
                        ));
                        echo $this->Html->tag('tr', $content);
                    }
                }else{
                    $content = $this->Html->tag('td', __('Data tidak ditemukan.'), array(
                        'colspan' => 6,
                        'class' => 'alert alert-danger'
                    ));
                    echo $this->Html->tag('tr', $content);
                }
            ?>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>