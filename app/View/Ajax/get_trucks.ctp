<?php 
        echo $this->element('blocks/ajax/search_truck');
?>
<div class="box-body table-responsive browse-form">
    <table class="table table-hover">
        <tr>
            <th>ID</th>
            <th>Cabang</th>
            <th>Nopol</th>
            <th>Supir</th>
            <th>Merek</th>
            <th>Jenis</th>
            <th>Kapasitas</th>
            <th>Pemilik</th>
        </tr>
        <?php
                if(!empty($trucks)){
                    foreach ($trucks as $key => $value) {
                        $id = $this->Common->filterEmptyField($value, 'Truck', 'id');
                        $nopol = $this->Common->filterEmptyField($value, 'Truck', 'nopol');
                        $capacity = $this->Common->filterEmptyField($value, 'Truck', 'capacity');

                        $branch = $this->Common->filterEmptyField($value, 'Branch', 'code');

                        switch ($action_type) {
                            case 'cashbank':
                                $result = $nopol;
                                break;
                            
                            default:
                                $result = $id;
                                break;
                        }
        ?>
        <tr data-value="<?php echo $result;?>" data-text="<?php echo $nopol;?>" data-change="<?php echo $data_change;?>">
            <td><?php echo $id;?></td>
            <td><?php echo $branch;?></td>
            <td><?php echo $nopol;?></td>
            <td><?php echo !empty($value['Driver']['driver_name'])?$value['Driver']['driver_name']:'-';?></td>
            <td><?php echo !empty($value['TruckBrand']['name'])?$value['TruckBrand']['name']:'-';?></td>
            <td><?php echo !empty($value['TruckCategory']['name'])?$value['TruckCategory']['name']:'-';?></td>
            <td><?php echo $capacity;?></td>
            <td><?php echo !empty($value['Company']['name'])?$value['Company']['name']:'-';?></td>
        </tr>
        <?php
                    }
                }else{
                    echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                        'class' => 'alert alert-warning text-center',
                        'colspan' => '9'
                    )));
                }
        ?>
    </table>
</div><!-- /.box-body -->
<?php
        echo $this->element('pagination', array(
            'options' => array(
                'data-action' => $data_action,
                'class' => 'ajaxModal',
                'title' => __('Data Truk'),
            ),
        ));
?>