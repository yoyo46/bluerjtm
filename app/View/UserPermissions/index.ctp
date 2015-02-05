<?php
        echo $this->Html->css(array('/css/acl/treeview'));
        echo $this->Html->script(array(
            '/js/acl/jquery.cookie',
            '/js/acl/treeview',
            '/js/acl/acos',
            '/js/bootstrap',
        ));

        echo $this->Form->create('Group', array(
            'url'=> $this->Html->url( null, true ), 
            'inputDefaults' => array('div' => false),
        ));
?>
<div class="form-group">
    <?php
            echo $this->Form->input('group_id', array(
                'class' => 'form-control',
                'label' => __('Pilih Group'),
                'required' => false,
                'div' => false,
                'empty' => __('Pilih Group'),
                'onChange' => 'submit()',
            ));
    ?>
</div>
<?php
        echo $this->Form->end();

        if( !empty($modules) && !empty($this->request->data['Group']['group_id']) ) {
            foreach ($modules as $key => $module) {
                $actionArr = Set::extract('/ModuleAction/action', $module);
?>
<div class="list-module">
    <div class="row">
        <div class="col-sm-4" id="<?php echo $module['Module']['function']; ?>">
            <?php 
                    echo $this->Html->tag('label', $module['Module']['name']);
            ?>
        </div>
        <div class="col-sm-2">
            <?php 
                    $actionName = 'view_'.$module['Module']['function'];

                    if( in_array($actionName, $actionArr) ) {
                        $icon = 'fa-check';
                    } else {
                        $icon = 'fa-times';
                    }

                    echo $this->Html->link('<i class="fa '.$icon.'"></i> View', array(
                        'action' => 'generate_module',
                        $module['Module']['id'],
                        $group_id,
                        $actionName,
                    ), array(
                        'escape' => false
                    ));
            ?>
        </div>
        <?php 
                if( empty($module['Module']['is_report']) ) {
        ?>
        <div class="col-sm-2">
            <?php 
                    $actionName = 'insert_'.$module['Module']['function'];

                    if( in_array($actionName, $actionArr) ) {
                        $icon = 'fa-check';
                    } else {
                        $icon = 'fa-times';
                    }

                    echo $this->Html->link('<i class="fa '.$icon.'"></i> Insert', array(
                        'action' => 'generate_module',
                        $module['Module']['id'],
                        $group_id,
                        $actionName,
                    ), array(
                        'escape' => false
                    ));
            ?>
        </div>
        <div class="col-sm-2">
            <?php 
                    $actionName = 'update_'.$module['Module']['function'];

                    if( in_array($actionName, $actionArr) ) {
                        $icon = 'fa-check';
                    } else {
                        $icon = 'fa-times';
                    }

                    echo $this->Html->link('<i class="fa '.$icon.'"></i> Update', array(
                        'action' => 'generate_module',
                        $module['Module']['id'],
                        $group_id,
                        $actionName,
                    ), array(
                        'escape' => false
                    ));
            ?>
        </div>
        <div class="col-sm-2">
            <?php 
                    $actionName = 'delete_'.$module['Module']['function'];

                    if( in_array($actionName, $actionArr) ) {
                        $icon = 'fa-check';
                    } else {
                        $icon = 'fa-times';
                    }

                    echo $this->Html->link('<i class="fa '.$icon.'"></i> Delete', array(
                        'action' => 'generate_module',
                        $module['Module']['id'],
                        $group_id,
                        $actionName,
                    ), array(
                        'escape' => false
                    ));
            ?>
        </div>
        <?php 
                }
        ?>
    </div>
</div>
<?php 
            }
        }
?>