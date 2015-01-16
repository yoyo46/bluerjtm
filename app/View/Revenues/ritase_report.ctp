<?php 
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/leasings/search_index');
?>
<div class="box box-success">
    <div class="box-header">
        <?php 
                echo $this->Html->tag('h3', $sub_module_title, array(
                    'class' => 'box-title'
                ));
        ?>
    </div>
    <div class="box-body table-responsive">
        <table class="table table-hover table-overflow" style="min-width: 1500px;">
            <tr>
                <?php 
                        echo $this->Html->tag('th', $this->Paginator->sort('Truck.nopol', __('NO. POL'), array(
                            'escape' => false,
                        )), array(
                            'style' => 'width: 100px;',
                            'class' => 'text-center text-middle',
                            'rowspan' => 2,
                        ));
                        echo $this->Html->tag('th', $this->Paginator->sort('Driver.name', __('Supir'), array(
                            'escape' => false
                        )), array(
                            'style' => 'width: 120px;',
                            'class' => 'text-center text-middle',
                            'rowspan' => 2,
                        ));
                        echo $this->Html->tag('th', $this->Paginator->sort('Truck.capacity', __('Kapasitas'), array(
                            'escape' => false
                        )), array(
                            'style' => 'width: 100px;',
                            'class' => 'text-center text-middle',
                            'rowspan' => 2,
                        ));
                        echo $this->Html->tag('th', __('Kapasitas'), array(
                            'style' => 'width: 100px;',
                            'class' => 'text-center text-middle',
                            'rowspan' => 2,
                        ));
                        echo $this->Html->tag('th', __('Total'), array(
                            'style' => 'width: 80px;',
                            'class' => 'text-center text-middle',
                            'rowspan' => 2,
                        ));
                        echo $this->Html->tag('th', $this->Paginator->sort('Ttuj.target_rit', __('Target RIT'), array(
                            'escape' => false
                        )), array(
                            'style' => 'width: 120px;',
                            'class' => 'text-center text-middle',
                            'rowspan' => 2,
                        ));
                        echo $this->Html->tag('th', __('Tujuan'), array(
                            'colspan' => count($cities),
                            'class' => 'text-center',
                        ));
                        echo $this->Html->tag('th', __('Pencapaian (%)'), array(
                            'style' => 'width: 120px;',
                            'class' => 'text-center',
                        ));
                ?>
            </tr>
            <tr>
                <th colspan="6">&nbsp;</th>
                <?php 
                        if( !empty($cities) ) {
                            foreach ($cities as $key => $value) {
                                echo $this->Html->tag('th', $value);
                            }
                        } else {
                            echo '<th>&nbsp;</th>';
                        }
                ?>
                <th>&nbsp;</th>
            </tr>
            <?php
                    if(!empty($trucks)){
                        foreach ($trucks as $key => $value) {
                            $id = $value['Truck']['id'];
            ?>
            <tr>
                <td><?php echo $value['Truck']['nopol'];?></td>
                <td><?php echo $value['Driver']['name'];?></td>
                <td class="text-center"><?php echo $value['Truck']['capacity'];?></td>
                <td class="text-center"><?php echo !empty($value['Customer']['name'])?$value['Customer']['name']:'-';?></td>
                <td class="text-center"><?php echo !empty($value['Total'])?$value['Total']:0;?></td>
                <td class="text-center"><?php echo !empty($value['Customer']['target_rit'])?$value['Customer']['target_rit']:'0';?></td>
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