<?php 
        $this->Html->addCrumb($sub_module_title);
        // echo $this->element('blocks/ttuj/search_ttuj');
?>
<div class="box box-success">
    <div class="box-header">
        <?php 
                echo $this->Html->tag('h3', $sub_module_title, array(
                    'class' => 'box-title'
                ));

                // if( in_array('insert_revenues', $allowModule) ) {
        ?>
        <div class="box-tools">
            <?php
                echo $this->Html->link('<i class="fa fa-plus"></i> Tambah', array(
                    'controller' => 'spk',
                    'action' => 'internal_add'
                ), array(
                    'escape' => false,
                    'class' => 'btn btn-app btn-success pull-right'
                ));
            ?>
            <div class="clear"></div>
        </div>
        <?php 
                // }
        ?>
    </div>
    <div class="box-body table-responsive">
        <table class="table table-hover">
            <tr>
                <?php 
                        echo $this->Html->tag('th', $this->Paginator->sort('Spk.no_doc', __('No Dokumen'), array(
                            'escape' => false
                        )));

                        echo $this->Html->tag('th', $this->Paginator->sort('Spk.type', __('Jenis'), array(
                            'escape' => false
                        )), array(
                            'class' => 'text-center',
                        ));

                        echo $this->Html->tag('th', $this->Paginator->sort('Truck.nopol', __('NoPol'), array(
                            'escape' => false
                        )), array(
                            'class' => 'text-center',
                        ));

                        echo $this->Html->tag('th', $this->Paginator->sort('Employe.name', __('Kepala Mekanik'), array(
                            'escape' => false
                        )), array(
                            'class' => 'text-center',
                        ));

                        echo $this->Html->tag('th', $this->Paginator->sort('Spk.date_spk', __('Tgl SPK'), array(
                            'escape' => false
                        )), array(
                            'class' => 'text-center',
                        ));
                        echo $this->Html->tag('th', $this->Paginator->sort('Spk.date_target_from', __('Tgl Mulai'), array(
                            'escape' => false
                        )), array(
                            'class' => 'text-center',
                        ));

                        echo $this->Html->tag('th', $this->Paginator->sort('Spk.date_target_to', __('Tgl Selesai'), array(
                            'escape' => false
                        )), array(
                            'class' => 'text-center',
                        ));
                        echo $this->Html->tag('th', $this->Paginator->sort('Spk.status', __('Status'), array(
                            'escape' => false
                        )), array(
                            'class' => 'text-center',
                        ));

                        echo $this->Html->tag('th', $this->Paginator->sort('Ttuj.created', __('Dibuat'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', __('Action'), array(
                            'escape' => false
                        ));
                ?>
            </tr>
            <?php
                    if(!empty($spks)){
                        foreach ($spks as $key => $value) {
                            $id = $value['Spk']['id'];
            ?>
            <tr>
                <td><?php echo $value['Spk']['no_doc'];?></td>
                <td><?php echo ucwords($value['Spk']['type']);?></td>
                <td><?php echo $value['Truck']['nopol'];?></td>
                <td><?php echo $value['Employe']['name'];?></td>
                <td class="text-center">
                    <?php 
                            echo $this->Common->customDate($value['Spk']['date_spk'], 'd/m/Y');
                    ?>
                </td>
                <td class="text-center">
                    <?php 
                            echo $this->Common->customDate($value['Spk']['date_target_from'], 'd/m/Y');
                    ?>
                </td>
                <td class="text-center">
                    <?php 
                            echo $this->Common->customDate($value['Spk']['date_target_to'], 'd/m/Y');
                    ?>
                </td>
                <?php 
                        if(!empty($value['Spk']['status'])){
                            echo $this->Html->tag('td', '<span class="label label-success">Aktif</span>');
                        } else {
                            echo $this->Html->tag('td', '<span class="label label-danger">Non-Aktif</span>');
                        }
                ?>
                <td><?php echo $this->Common->customDate($value['Spk']['created']);?></td>
                <td class="action">
                    <?php
                            // if( in_array(sprintf('update_%s', $active_menu), $allowModule) ) {
                                echo $this->Html->link('Ubah', array(
                                    'controller' => 'spk',
                                    'action' => 'internal_edit',
                                    $id
                                ), array(
                                    'class' => 'btn btn-primary btn-xs'
                                ));
                            // }

                            // if( in_array(sprintf('delete_%s', $active_menu), $allowModule) ) {
                                echo $this->Html->link(__('Batalkan'), array(
                                    'controller' => 'spk',
                                    'action' => 'internal_delete',
                                    $id,
                                ), array(
                                    'class' => 'btn btn-danger btn-xs',
                                ), __('Apakah Anda yakin akan non-aktifkan data ini?'));
                            // }
                    ?>
                </td>
            </tr>
            <?php
                        }
                    }else{
                        echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                            'class' => 'alert alert-warning text-center',
                            'colspan' => '10'
                        )));
                    }
            ?>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>