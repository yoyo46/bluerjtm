<?php 
        $dataColumns = array(
            'code' => array(
                'name' => __('Kode'),
                'field_model' => 'City.code',
                'display' => true,
            ),
            'name' => array(
                'name' => __('Kota'),
                'field_model' => 'City.name',
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
                            $code = $this->Common->filterEmptyField($value, 'City', 'code');
                            $name = $this->Common->filterEmptyField($value, 'City', 'name');
                            $modified = $this->Common->filterEmptyField($value, 'City', 'modified');
            ?>
            <tr>
                <td><?php echo $code;?></td>
                <td><?php echo $name;?></td>
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
                                'class' => 'btn btn-danger btn-xs trigger-disabled',
                                'data-alert' => __('Anda yakin ingin menghapus data Kota ini?'),
                            ));
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