<?php 
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/settings/search_cities');
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <?php 
                if( in_array('insert_cities', $allowModule) ) {
        ?>
        <div class="box-tools">
            <?php
                echo $this->Common->rule_link('<i class="fa fa-plus"></i> Tambah Kota', array(
                    'controller' => 'settings',
                    'action' => 'city_add'
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
                <th>Kota</th>
                <?php
                        echo $this->Html->tag('th', $this->Paginator->sort('City.is_branch', __('Cabang'), array(
                            'escape' => false,
                        )), array(
                            'class' => 'text-center',
                        ));
                        echo $this->Html->tag('th', $this->Paginator->sort('City.is_pool', __('Pool'), array(
                            'escape' => false,
                        )), array(
                            'class' => 'text-center',
                        ));
                ?>
                <th>Dibuat</th>
                <th>Action</th>
            </tr>
            <?php
                    $i = 1;
                    if(!empty($cities)){
                        foreach ($cities as $key => $value) {
                            $value_data = $value['City'];
                            $id = $value_data['id'];

                            $branch = $this->Common->safeTagPrint($value['City']['is_branch']);
                            if($branch){
                                $branch = '<span class="label label-success"><i class="fa fa-check"></i></span>';
                            }else{
                                $branch = '<span class="label label-danger"><i class="fa fa-times"></i></span>';
                            }

                            $pool = $this->Common->safeTagPrint($value['City']['is_pool']);
                            if($pool){
                                $pool = '<span class="label label-success"><i class="fa fa-check"></i></span>';
                            }else{
                                $pool = '<span class="label label-danger"><i class="fa fa-times"></i></span>';
                            }
            ?>
            <tr>
                <td><?php echo $value_data['name'];?></td>
                <td>
                    <?php 
                        echo $this->Common->rule_link($branch, array(
                            'controller' => 'settings',
                            'action' => 'toggle_city',
                            $id,
                            'branch'
                        ), array(
                            'escape' => false
                        ));
                    ?>
                </td>
                <td>
                    <?php 
                        echo $this->Common->rule_link($pool, array(
                            'controller' => 'settings',
                            'action' => 'toggle_city',
                            $id,
                            'pool'
                        ), array(
                            'escape' => false
                        ));
                    ?>
                </td>
                <td><?php echo $this->Common->customDate($value_data['created']);?></td>
                <td class="action">
                    <?php 
                            if( in_array('insert_cities', $allowModule) ) {
                                echo $this->Common->rule_link('Edit', array(
                                    'controller' => 'settings',
                                    'action' => 'city_edit',
                                    $id
                                ), array(
                                    'class' => 'btn btn-primary btn-xs'
                                ));
                            }

                            if( in_array('delete_cities', $allowModule) ) {
                                echo $this->Common->rule_link(__('Hapus'), array(
                                    'controller' => 'settings',
                                    'action' => 'city_toggle',
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
                            'colspan' => '6'
                        )));
                    }
            ?>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>