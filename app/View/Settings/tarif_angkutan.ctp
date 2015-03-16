<?php 
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/settings/search_tarif_angkutan');
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <?php 
                if( in_array('insert_tarif_angkutan', $allowModule) ) {
        ?>
        <div class="box-tools">
            <?php
                echo $this->Html->link('<i class="fa fa-plus"></i> Tambah Tarif Angkutan', array(
                    'controller' => 'settings',
                    'action' => 'tarif_angkutan_add'
                ), array(
                    'escape' => false,
                    'class' => 'btn btn-app pull-right'
                ));
            ?>
        </div>
        <?php 
                }
        ?>
    </div><!-- /.box-header -->
    <div class="box-body table-responsive">
        <table class="table table-hover">
            <tr>
                <th>Nama</th>
                <th>Tipe Tarif</th>
                <th>Dari</th>
                <th>Tujuan</th>
                <th>Customer</th>
                <th>Jenis Unit</th>
                <th>Group Motor</th>
                <th>Kapasitas</th>
                <th>Tarif</th>
                <th>Dibuat</th>
                <th>Action</th>
            </tr>
            <?php
                    $i = 1;
                    if(!empty($tarif_angkutan)){
                        foreach ($tarif_angkutan as $key => $value) {
                            $value_data = $value['TarifAngkutan'];
                            $id = $value_data['id'];
            ?>
            <tr>
                <td><?php echo $value_data['name_tarif'];?></td>
                <td>
                    <?php
                            switch ($value_data['type']) {
                                case 'kuli':
                                    echo __('Kuli Muat');
                                    break;
                                case 'asuransi':
                                    echo __('Asuransi');
                                    break;
                                default:
                                    echo __('Tarif Angkut');
                                    break;
                            };
                    ?>
                </td>
                <td><?php echo $value_data['from_city_name'];?></td>
                <td><?php echo $value_data['to_city_name'];?></td>
                <td><?php echo !empty($value['Customer']['customer_name'])?$value['Customer']['customer_name']:'-';?></td>
                <td>
                    <?php
                            switch ($value_data['jenis_unit']) {
                                case 'per_unit':
                                    echo __('Per Unit');
                                    break;
                                
                                default:
                                    echo __('Per Truk');
                                    break;
                            };
                    ?>
                </td>
                <td><?php echo $value['GroupMotor']['name'];?></td>
                <td><?php echo $value_data['capacity'];?></td>
                <td><?php echo $this->Number->currency($value_data['tarif'], Configure::read('__Site.config_currency_code'), array('places' => 0));?></td>
                <td><?php echo $this->Common->customDate($value_data['created']);?></td>
                <td class="action">
                    <?php 
                            if( in_array('update_tarif_angkutan', $allowModule) ) {
                                echo $this->Html->link('Edit', array(
                                    'controller' => 'settings',
                                    'action' => 'tarif_angkutan_edit',
                                    $id
                                ), array(
                                    'class' => 'btn btn-primary btn-xs'
                                ));
                            }

                            if( in_array('delete_tarif_angkutan', $allowModule) ) {
                                echo $this->Html->link(__('Hapus'), array(
                                    'controller' => 'settings',
                                    'action' => 'tarif_angkutan_toggle',
                                    $id
                                ), array(
                                    'class' => 'btn btn-danger btn-xs',
                                    'title' => 'disable status brand'
                                ), __('Anda yakin ingin menghapus data Kota ini?'));
                            }
                    ?>
                </td>
            </tr>
            <?php
                        }
                    } else {
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