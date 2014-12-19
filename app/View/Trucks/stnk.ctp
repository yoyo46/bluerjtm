<?php 
	$this->Html->addCrumb(__('Truk'), array(
		'controller' => 'trucks',
		'action' => 'index'
	));
    $this->Html->addCrumb($sub_module_title);

    echo $this->element('blocks/trucks/info_truck');
?>
<div class="box">
    <div class="box-header">
        <?php 
                echo $this->Html->tag('h3', $sub_module_title, array(
                    'class' => 'box-title'
                ));
        ?>
        <div class="box-tools">
            <?php
                echo $this->Html->link('<i class="fa fa-truck"></i> Perpanjang STNK', array(
                    'controller' => 'trucks',
                    'action' => 'stnk_add',
                    $id
                ), array(
                    'escape' => false,
                    'class' => 'btn btn-app pull-right'
                ));
            ?>
            <div class="clear"></div>
        </div>
    </div>
    <div class="box-body table-responsive">
        <table class="table table-hover">
            <tr>
                <th>ID</th>
                <th>Tanggal Bayar</th>
                <th>Tanggal Berakhir</th>
                <th>Harga</th>
                <th>Prakiraan Harga<br> Perpanjang selanjutnya</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php
                $i = 1;
                if(!empty($stnk)){
                    foreach ($stnk as $key => $value) {
                        $value_truck = $value['Stnk'];
                        $id = $value_truck['id'];
            ?>
            <tr>
                <td><?php echo $id;?></td>
                <td><?php echo $this->Common->customDate($value_truck['tgl_bayar']);?></td>
                <td><?php echo $this->Common->customDate($value_truck['tgl_berakhir']);?></td>
                <td><?php echo $this->Number->currency($value_truck['price'], 'Rp. ');?></td>
                <td><?php echo $this->Number->currency($value_truck['price_next_estimate'], 'Rp. ');?></td>
                <td>
                    <?php
                        if($value_truck['is_change_plat']){
                            echo '<span class="label label-success">Pergantian Plat</span>'; 
                        }else{
                            echo '-';
                        }
                    ?>
                </td>
                <td>
                    <?php
                        echo $this->Html->link('Rubah', array(
                            'controller' => 'trucks',
                            'action' => 'stnk_edit',
                            $value_truck['truck_id'],
                            $id
                        ), array(
                            'class' => 'btn btn-primary btn-xs'
                        ));
                    ?>
                </td>
            </tr>
            <?php
                        }
                    } else {
                         echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                            'class' => 'alert alert-warning text-center',
                            'colspan' => '7'
                        )));
                    }
            ?>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>