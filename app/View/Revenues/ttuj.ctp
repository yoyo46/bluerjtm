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
        <div class="box-tools">
            <?php
                    echo $this->Html->link('<i class="fa fa-plus"></i> Tambah TTUJ', array(
                        'controller' => 'revenues',
                        'action' => 'add'
                    ), array(
                        'escape' => false,
                        'class' => 'btn btn-app btn-success pull-right'
                    ));
            ?>
            <div class="clear"></div>
        </div>
    </div>
    <div class="box-body table-responsive no-padding">
        <table class="table table-hover">
            <tr>
                <?php 
                        echo $this->Html->tag('th', $this->Paginator->sort('Ttuj.no_ttuj', __('No TTUJ'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Ttuj.ttuj_date', __('Tgl TTUJ'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Ttuj.nopol', __('No Pol'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Ttuj.customer_name', __('Customer'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Ttuj.from_city_name', __('Dari'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Ttuj.to_city_name', __('Tujuan'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Ttuj.is_draft', __('Status'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Ttuj.created', __('Dibuat'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', __('Action'), array(
                            'escape' => false
                        ));
                ?>
            </tr>
            <?php
                    if(!empty($ttujs)){
                        foreach ($ttujs as $key => $value) {
                            $id = $value['Ttuj']['id'];
            ?>
            <tr>
                <td><?php echo $value['Ttuj']['no_ttuj'];?></td>
                <td><?php echo date('d M Y', strtotime($value['Ttuj']['ttuj_date']));?></td>
                <td><?php echo $value['Ttuj']['nopol'];?></td>
                <td><?php echo $value['Ttuj']['customer_name'];?></td>
                <td><?php echo $value['Ttuj']['from_city_name'];?></td>
                <td><?php echo $value['Ttuj']['to_city_name'];?></td>
                <td>
                    <?php 
                            if(!empty($value['Ttuj']['is_draft'])){
                                echo '<span class="label label-success">Draft</span>'; 
                            }else{
                                echo '<span class="label label-danger">Commit</span>';  
                            }
                    ?>
                </td>
                <td><?php echo $this->Common->customDate($value['Ttuj']['created']);?></td>
                <td class="action">
                    <?php
                            echo $this->Html->link('Rubah', array(
                                'controller' => 'revenues',
                                'action' => 'edit',
                                $id
                            ), array(
                                'class' => 'btn btn-primary btn-xs'
                            ));

                            echo $this->Html->link(__('Hapus'), array(
                                'controller' => 'revenues',
                                'action' => 'delete',
                                $id
                            ), array(
                                'class' => 'btn btn-danger btn-xs',
                                'title' => 'disable status brand'
                            ), __('Apakah Anda yakin akan menghapus ttuj ini?'));
                    ?>
                </td>
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
    <?php echo $this->element('pagination');?>
</div>