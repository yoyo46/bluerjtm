<?php 
        $this->Html->addCrumb(__('Icon Kalender'), array(
            'action' => 'calendar_icons',
        ));
        echo $this->element('blocks/settings/search_calendar_icons');
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <div class="box-tools">
            <?php
                    echo $this->Html->link('<i class="fa fa-plus"></i> Tambah Icon Kalender', array(
                        'controller' => 'settings',
                        'action' => 'calendar_icon_add'
                    ), array(
                        'escape' => false,
                        'class' => 'btn btn-app pull-right'
                    ));
            ?>
        </div>
    </div><!-- /.box-header -->
    <div class="box-body table-responsive">
        <table class="table table-hover">
            <tr>
                <?php
                        echo $this->Html->tag('th', $this->Paginator->sort('CalendarIcon.name', __('Label'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', __('Icon'), array(
                            'class' => 'text-center'
                        ));
                        echo $this->Html->tag('th', $this->Paginator->sort('CalendarIcon.created', __('Dibuat'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', __('Action'));
                ?>
            </tr>
            <?php
                    if(!empty($calendarIcons)){
                        foreach ($calendarIcons as $key => $value) {
                            $value_data = $value['CalendarIcon'];
                            $id = $value_data['id'];
            ?>
            <tr>
                <td><?php echo $value_data['name'];?></td>
                <td class="text-center">
                    <?php
                            if( !empty($value_data['photo']) ){
                                echo $this->Common->photo_thumbnail(array(
                                    'save_path' => Configure::read('__Site.truck_photo_folder'), 
                                    'src' => $value_data['photo'], 
                                    'thumb'=>true,
                                    'size' => 'ps',
                                    'thumb' => true,
                                ), array(
                                    'class' => 'icon-calendar'
                                ));
                            }
                    ?>
                </td>
                <td><?php echo $this->Common->customDate($value_data['created']);?></td>
                <td class="action">
                    <?php 
                            echo $this->Html->link('Edit', array(
                                'controller' => 'settings',
                                'action' => 'calendar_icon_edit',
                                $id
                            ), array(
                                'class' => 'btn btn-primary btn-xs'
                            ));

                            echo $this->Html->link('Hapus', array(
                                'controller' => 'settings',
                                'action' => 'calendar_icon_toggle',
                                $id
                            ), array(
                                'class' => 'btn btn-danger btn-xs trigger-disabled',
                                'title' => 'disable status brand',
                                'data-alert' => __('Apakah Anda yakin akan menghapus warnaini?'),
                            ));
                    ?>
                </td>
            </tr>
            <?php
                        }
                    } else {
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