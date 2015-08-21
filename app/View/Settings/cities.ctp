<?php 
        $dataColumns = array(
            'name' => array(
                'name' => __('Kota'),
                'field_model' => 'City.name',
                'display' => true,
            ),
            'head_office' => array(
                'name' => __('Head Office'),
                'field_model' => 'City.is_head_office',
                'class' => 'text-center',
                'display' => true,
            ),
            'branch' => array(
                'name' => __('Cabang'),
                'field_model' => 'City.is_branch',
                'class' => 'text-center',
                'display' => true,
            ),
            'plant' => array(
                'name' => __('Plant'),
                'field_model' => 'City.is_plant',
                'class' => 'text-center',
                'display' => true,
            ),
            'modified' => array(
                'name' => __('Diubah'),
                'field_model' => 'City.modified',
                'display' => true,
            ),
            'action' => array(
                'name' => __('Action'),
                'field_model' => false,
                'display' => true,
            ),
        );
        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/settings/search_cities');
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <div class="box-tools">
            <?php
                echo $this->Html->link('<i class="fa fa-plus"></i> Tambah', array(
                    'controller' => 'settings',
                    'action' => 'city_add'
                ), array(
                    'escape' => false,
                    'class' => 'btn btn-app pull-right'
                ));
            ?>
        </div>
    </div><!-- /.box-header -->
    <div class="box-body table-responsive">
        <table class="table table-hover sorting">
            <thead>
                <tr>
                    <?php
                            if( !empty($fieldColumn) ) {
                                echo $fieldColumn;
                            }
                    ?>
                </tr>
            </thead>
            <tbody>
            <?php
                    if(!empty($cities)){
                        foreach ($cities as $key => $value) {
                            $id = $this->Common->filterEmptyField($value, 'City', 'id');
                            $name = $this->Common->filterEmptyField($value, 'City', 'name');
                            $modified = $this->Common->filterEmptyField($value, 'City', 'modified');
                            $branch = $this->Common->filterEmptyField($value, 'City', 'is_branch');
                            $plant = $this->Common->filterEmptyField($value, 'City', 'is_plant');
                            $head_office = $this->Common->filterEmptyField($value, 'City', 'is_head_office');

                            $branch = $this->Common->getCheckStatus( $branch );
                            $plant = $this->Common->getCheckStatus( $plant );
                            $head_office = $this->Common->getCheckStatus( $head_office );
            ?>
            <tr>
                <td><?php echo $name;?></td>
                <td class="text-center">
                    <?php 
                            echo $head_office;
                    ?>
                </td>
                <td class="text-center">
                    <?php 
                            echo $branch;
                    ?>
                </td>
                <td class="text-center"><?php echo $plant;?></td>
                <td><?php echo $this->Time->niceShort($modified);?></td>
                <td class="action">
                    <?php 
                            echo $this->Html->link('Edit', array(
                                'controller' => 'settings',
                                'action' => 'city_edit',
                                $id
                            ), array(
                                'class' => 'btn btn-primary btn-xs'
                            ));

                            echo $this->Html->link(__('Hapus'), array(
                                'controller' => 'settings',
                                'action' => 'city_toggle',
                                $id
                            ), array(
                                'class' => 'btn btn-danger btn-xs',
                            ), __('Anda yakin ingin menghapus data Kota ini?'));
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
            </tbody>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>