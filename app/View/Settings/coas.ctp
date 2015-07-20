<?php 
        $this->Html->addCrumb($sub_module_title);
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo __('COA');?></h3>
        <div class="box-tools">
            <?php
                echo $this->Html->link('<i class="fa fa-plus"></i> Tambah COA', array(
                    'controller' => 'settings',
                    'action' => 'coa_add'
                ), array(
                    'escape' => false,
                    'class' => 'btn btn-app pull-right'
                ));
            ?>
        </div>
    </div><!-- /.box-header -->
    <div class="box-body">
        <div id="wrapper-tree">
            <div class="tree">
                <?php 
                        if( !empty($coas) ) {
                            echo '<ul>';
                            foreach ($coas as $key => $value_1) {
                                echo '<li class="parent_li">';
                                echo $this->Common->printDataTree($value_1, 1);
                                if(!empty($value_1['children'])){
                                    echo '<ul>';
                                    foreach ($value_1['children'] as $key => $value_2) {
                                        echo '<li class="parent_li">';
                                        echo $this->Common->printDataTree($value_2, 2);
                                        if(!empty($value_2['children'])){
                                            echo '<ul>';
                                            foreach ($value_2['children'] as $key => $value_3) {
                                                echo '<li class="parent_li">';
                                                echo $this->Common->printDataTree($value_3, 3);
                                                if(!empty($value_3['children'])){
                                                    echo '<ul>';
                                                    foreach ($value_3['children'] as $key => $value_4) {
                                                        echo $this->Html->tag('li', $this->Common->printDataTree($value_4, 4), array('class' => 'parent_li'));
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