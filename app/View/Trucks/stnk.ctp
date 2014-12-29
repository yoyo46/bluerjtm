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
        <div class="box-tools">
            <?php
                echo $this->Html->link('<i class="fa fa-plus"></i> Perpanjang STNK', array(
                    'controller' => 'trucks',
                    'action' => 'stnk_add'
                ), array(
                    'escape' => false,
                    'class' => 'btn btn-app pull-right'
                ));
            ?>
        </div>
    </div>
    <div class="box-body table-responsive">
        <table class="table table-hover">
            <tr>
                <?php
                        echo $this->Html->tag('th', $this->Paginator->sort('Stnk.no_pol', __('No. Pol'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Stnk.tgl_bayar', __('Tgl Perpanjang'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Stnk.tgl_berakhir', __('Tgl Expired'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Stnk.price', __('Biaya Perpanjang'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Stnk.paid', __('Status'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Stnk.created', __('Dibuat'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', __('Action'));
                ?>
            </tr>
            <?php
                $i = 1;
                if(!empty($stnks)){
                    foreach ($stnks as $key => $value) {
                        $id = $value['Stnk']['id'];
            ?>
            <tr>
                <td><?php echo $value['Stnk']['no_pol'];?></td>
                <td><?php echo $this->Common->customDate($value['Stnk']['tgl_bayar']);?></td>
                <td><?php echo $this->Common->customDate($value['Stnk']['tgl_berakhir']);?></td>
                <td><?php echo $this->Number->currency($value['Stnk']['price'], 'Rp. ');?></td>
                <td>
                    <?php
                        if($value['Stnk']['is_change_plat']){
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
                                $value['Stnk']['id'],
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