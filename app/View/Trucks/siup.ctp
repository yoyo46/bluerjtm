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
                echo $this->Html->link('<i class="fa fa-truck"></i> Perpanjang SIUP', array(
                    'controller' => 'trucks',
                    'action' => 'siup_add',
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
                <th>Tanggal SIUP</th>
                <th>Tanggal SIUP Lanjutan</th>
                <th>Biaya SIUP</th>
                <th>prakiraan Biaya <br>SIUP selanjutnya</th>
                <th>Status Bayar</th>
                <th>Action</th>
            </tr>
            <?php
                $i = 1;
                if(!empty($siup)){
                    foreach ($siup as $key => $value) {
                        $value_truck = $value['Siup'];
                        $id = $value_truck['id'];
            ?>
            <tr>
                <td><?php echo $id;?></td>
                <td><?php echo $this->Common->customDate($value_truck['tgl_siup'], 'd-m-Y');?></td>
                <td><?php echo $this->Common->customDate($value_truck['tgl_next_siup']);?></td>
                <td>
                    <?php echo $this->Number->currency($value_truck['price'], 'Rp. ');?>
                </td>
                <td>
                    <?php echo $this->Number->currency($value_truck['price_next_estimate'], 'Rp. ');?>
                </td>
                <td>
                    <?php 
                        if(!empty($value_truck['paid'])){
                            echo '<span class="label label-success">Sudah Bayar</span>'; 
                        }else{
                            echo '<span class="label label-danger">Belum Bayar</span>';  
                        }
                    ?>
                </td>
                <td>
                    <?php
                        echo $this->Html->link('Rubah', array(
                            'controller' => 'trucks',
                            'action' => 'siup_edit',
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
                            'colspan' => '3'
                        )));
                    }
            ?>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>