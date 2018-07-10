<?php 
        $this->Html->addCrumb($sub_module_title);
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo __('Cost Center');?></h3>
        <div class="box-tools">
            <div class="btn-group pull-right">
                <?php 
                        echo $this->Html->tag('button', '<i class="fa fa-plus"></i> Tambah', array(
                            'data-toggle' => 'dropdown',
                            'class' => 'btn btn-app btn-success dropdown-toggle'
                        ));
                ?>
                <ul class="dropdown-menu" role="menu">
                    <?php 
                            echo $this->Html->tag('li', $this->Html->link(__('Cost Center'), array(
                                'controller' => 'settings',
                                'action' => 'cogs_add'
                            ), array(
                                'escape' => false,
                            )));
                            echo $this->Html->tag('li', '', array(
                                'class' => 'divider',
                            ));
                            echo $this->Html->tag('li', $this->Html->link(__('Import Excel'), array(
                                'controller' => 'settings',
                                'action' => 'cogs_import'
                            ), array(
                                'escape' => false,
                            )));
                    ?>
                </ul>
            </div>
        </div>
    </div><!-- /.box-header -->
    <div class="box-body">
        <div id="wrapper-tree">
            <div class="tree">
                <?php 
                        if( !empty($values) ) {
                            echo '<ul>';
                            foreach ($values as $key => $value_1) {
                                echo '<li class="parent_li">';
                                echo $this->Common->printDataTreeCogs($value_1, 1);
                                if(!empty($value_1['children'])){
                                    echo '<ul>';
                                    foreach ($value_1['children'] as $key => $value_2) {
                                        echo '<li class="parent_li">';
                                        echo $this->Common->printDataTreeCogs($value_2, 2);
                                        if(!empty($value_2['children'])){
                                            echo '<ul>';
                                            foreach ($value_2['children'] as $key => $value_3) {
                                                echo '<li class="parent_li">';
                                                echo $this->Common->printDataTreeCogs($value_3, 3);
                                                if(!empty($value_3['children'])){
                                                    echo '<ul>';
                                                    foreach ($value_3['children'] as $key => $value_4) {
                                                        echo $this->Html->tag('li', $this->Common->printDataTreeCogs($value_4, 4), array('class' => 'parent_li'));
                                                    }
                                                    echo '</ul>';
                                                }
                                                echo '</li>';
                                            }
                                            echo '</ul>';
                                        }
                                        echo '</li>';
                                    }
                                    echo '</ul>';
                                }
                                echo '</li>';
                            }
                            echo '</ul>';
                        } else {
                            echo $this->Html->tag('div', __('Data belum tersedia'), array(
                                'class' => 'alert alert-warning'
                            ));
                        }
                ?>
            </div>
        </div>
    </div>
</div>