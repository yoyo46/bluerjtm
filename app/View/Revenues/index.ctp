<?php 
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/revenues/search_revenue');
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
                echo $this->Html->link('<i class="fa fa-plus"></i> Tambah Revenue', array(
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
    <div class="box-body table-responsive">
        <table class="table table-hover">
            <tr>
                <?php 
                        echo $this->Html->tag('th', $this->Paginator->sort('Revenue.no_doc', __('No Dokumen'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Ttuj.no_ttuj', __('No TTUJ'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Customer.name', __('Customer'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Revenue.created', __('Dibuat'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', __('Action'), array(
                            'escape' => false
                        ));
                ?>
            </tr>
            <?php
                    if(!empty($revenues)){
                        foreach ($revenues as $key => $value) {
                            $id = $value['Revenue']['id'];
            ?>
            <tr>
                <td><?php echo $value['Revenue']['no_doc'];?></td>
                <td><?php echo $value['Ttuj']['no_ttuj'];?></td>
                <td><?php echo $value['Customer']['name'];?></td>
                <td><?php echo $this->Common->customDate($value['Revenue']['created']);?></td>
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
                                'action' => 'revenue_toggle',
                                $id
                            ), array(
                                'class' => 'btn btn-danger btn-xs',
                                'title' => 'disable status brand'
                            ), __('Apakah Anda yakin akan membatalkan data ini?'));
                    ?>
                </td>
            </tr>
            <?php
                        }
                    }else{
                        echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                            'class' => 'alert alert-warning text-center',
                            'colspan' => '5'
                        )));
                    }
            ?>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>