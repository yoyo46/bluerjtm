<?php 
        $this->Html->addCrumb($sub_module_title);
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo __('COA');?></h3>
        <?php 
                if( in_array('insert_coas', $allowModule) ) {
        ?>
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
        <?php 
                }
        ?>
    </div><!-- /.box-header -->
    <div class="box-body">
        <div id="wrapper-tree">
            <div class="tree">
                <?php 
                        if( !empty($coas) ) {
                            echo $this->Common->generateCoaTree($coas, $allowModule);
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