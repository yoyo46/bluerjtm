<?php 
        $this->Html->addCrumb(__('Warna Kalender'), array(
            'action' => 'calendar_colors',
        ));
        echo $this->element('blocks/settings/search_calendar_colors');
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <div class="box-tools">
            <?php
                    echo $this->Html->link('<i class="fa fa-plus"></i> Tambah Warna Kalender', array(
                        'controller' => 'settings',
                        'action' => 'calendar_color_add'
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
                        echo $this->Html->tag('th', $this->Paginator->sort('CalendarColor.name', __('Label'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', __('Warna'), array(
                            'class' => 'text-center'
                        ));
                        echo $this->Html->tag('th', $this->Paginator->sort('CalendarColor.created', __('Dibuat'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', __('Action'));
                ?>
            </tr>
            <?php
                    if(!empty($calendarColors)){
                        foreach ($calendarColors as $key => $value) {
                            $value_data = $value['CalendarColor'];
                            $id = $value_data['id'];
            ?>
            <tr>
                <td><?php echo $value_data['name'];?></td>
                <td class="text-center">
                    <?php
                            echo $this->Html->tag('div', '<i class="fa fa-square"></i>', array(
                                'style' => sprintf('color: %s;', $value['CalendarColor']['hex']),
                                'class' => 'color-calendar',
                            ));
                    ?>
                </td>
                <td><?php echo $this->Common->customDate($value_data['created']);?></td>
                <td class="action">
                    <?php 
                            echo $this->Html->link('Edit', array(
                                'controller' => 'settings',
                                'action' => 'calendar_color_edit',
                                $id
                            ), array(
                                'class' => 'btn btn-primary btn-xs'
                            ));

                            echo $this->Html->link('Hapus', array(
                                'controller' => 'settings',
                                'action' => 'calendar_color_toggle',
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