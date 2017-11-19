<?php 
        $class = !empty($class)?$class:false;
?>
<div class="form-group">
    <?php 
            echo $this->Form->label('type', __('Truk'));
    ?>
    <div class="row">
        <div class="col-sm-4">
            <?php 
                    echo $this->Form->input('type',array(
                        'label'=> false,
                        'class'=>'form-control',
                        'required' => false,
                        'empty' => false,
                        'options' => array(
                            '1' => __('Nopol'),
                            '2' => __('ID Truk'),
                        ),
                    ));
            ?>
        </div>
        <div class="col-sm-8">
            <?php 
                    echo $this->Form->input('nopol',array(
                        'label'=> false,
                        'class'=>'form-control '.$class,
                        'required' => false,
                    ));
            ?>
        </div>
    </div>
</div>