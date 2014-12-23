<?php 
    $this->Html->addCrumb($sub_module_title);
?>
<div class="box">
    <div class="box-header">
        <?php 
                echo $this->Html->tag('h3', $sub_module_title, array(
                    'class' => 'box-title'
                ));
        ?>
    </div>
    <div class="box-body table-responsive">
        <table class="table table-hover">
            <tr>
                <th>ID</th>
                <th>Truk</th>
                <th>Tanggal Bayar</th>
                <th>Tanggal Berakhir</th>
                <th>Harga</th>
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
                <td><?php echo $value['Truck']['nopol'];?></td>
                <td><?php echo $this->Common->customDate($value_truck['tgl_bayar']);?></td>
                <td><?php echo $this->Common->customDate($value_truck['tgl_berakhir']);?></td>
                <td><?php echo $this->Number->currency($value_truck['price'], 'Rp. ');?></td>
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