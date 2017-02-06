<?php 
        $title = !empty($title)?$title:false;
        $return_value = !empty($return_value)?$return_value:false;
        echo $this->Form->create('Search', array(
            'url'=> $this->Html->url( array(
                'controller' => 'ajax',
                'action' => 'search',
                'truck_picker',
                'return_value' => $return_value,
                'admin' => false,
            )), 
            'role' => 'form',
            'inputDefaults' => array('div' => false),
        ));
?>
<div class="row">
    <div class="col-sm-6">
        <?php 
                echo $this->element('blocks/common/searchs/forms/input_truck', array(
                    'class' => 'on-focus',
                ));
                echo $this->element('blocks/common/forms/submit_action', array(
                    'frameClass' => 'form-group action',
                    'btnClass' => 'btn-sm',
                    'submitText' => sprintf(__('%s Search'), $this->Common->icon('search')),
                    'backText' => sprintf(__('%s Reset'), $this->Common->icon('refresh')),
                    'urlBack' => array(
                        'controller' => 'ajax',
                        'action' => 'truck_picker',
                        'return_value' => $return_value,
                    ),
                    'submitOptions' => array(
                        'class'=> 'btn btn-success btn-sm ajaxModal',
                        'data-action' => 'browse-form',
                        'data-parent' => true,
                        'title' => $title,
                    ),
                    'backOptions' => array(
                        'class'=> 'btn btn-default btn-sm ajaxModal',
                        'data-action' => 'browse-form',
                        'title' => $title,
                    ),
                ));
        ?>
    </div>
    <div class="col-sm-6">
        <?php 
                echo $this->Common->buildInputForm('driver', __('Nama Supir *'));
        ?>
    </div>
</div>
<?php 
        echo $this->Form->end();
?>