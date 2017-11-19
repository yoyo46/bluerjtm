<?php 
        $label = !empty($label)?$label:false;
        $labelClass = !empty($labelClass)?$labelClass:'control-label';

        $fieldName = !empty($fieldName)?$fieldName:false;
        $options = !empty($options)?$options:false;
?>
<div class="form-group">
    <?php 
            if( !empty($label) ) {
                echo $this->Form->label($fieldName, $label, array(
                    'class' => $labelClass,
                ));
            }

            if( !empty($options) ) {
    ?>
    <div class="row">
        <?php 
                foreach ($options as $key => $val) {
                    $value = $this->Common->filterEmptyField($val, 'value');
                    $label = $this->Common->filterEmptyField($val, 'label');
                    $class = $this->Common->filterEmptyField($val, 'class');

                    echo $this->Html->tag('div', $this->Html->tag('div', $this->Html->tag('label', $this->Form->input($fieldName, array(
                        'type' => 'checkbox',
                        'label'=> false,
                        'required' => false,
                        'value' => $value,
                        'div' => false,
                    )).$label), array(
                        'class' => 'checkbox',
                    )), array(
                        'class' => $class,
                    ));
                }
        ?>
    </div>
    <?php 
            }
    ?>
</div>