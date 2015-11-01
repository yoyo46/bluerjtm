<?php 
        $content = '';
        $showInput = true;
        $label = !empty($label)?$label:false;
        $labelClass = !empty($labelClass)?$labelClass:false;
        $disabled = !empty($disabled)?$disabled:false;
        $readonly = !empty($readonly)?$readonly:false;

        $frameClass = !empty($frameClass)?$frameClass:false;
        $class = isset($class)?$class:false;
        $divClass = !empty($divClass)?$divClass:false;
        $fieldName = !empty($fieldName)?$fieldName:false;
        $textGroup = !empty($textGroup)?$textGroup:false;
        $positionGroup = !empty($positionGroup)?$positionGroup:'right';
        $inputText = !empty($inputText)?$inputText:false;
?>
<div class="<?php echo $frameClass; ?>">
    <?php 
            if( !empty($label) ) {
                echo $this->Form->label($fieldName, $label, array(
                    'class' => $labelClass,
                ));
            }

            $optionsInput = array(
                'class' => $class,
                'label' => false,
                'div' => false,
                'disabled' => $disabled,
                'readonly' => $readonly,
            );

            if( isset($error) ) {
                $optionsInput['error'] = $error;
            }
             if( isset($required) ) {
                $optionsInput['required'] = $required;
            }
            if( !empty($empty) ) {
                $optionsInput['empty'] = $empty;
            }
            if( !empty($id) ) {
                $optionsInput['id'] = $id;
            }
            if( !empty($options) ) {
                $optionsInput['options'] = $options;
            }
            if( !empty($rows) ) {
                $optionsInput['rows'] = $rows;
            }
            if( !empty($title) ) {
                $optionsInput['title'] = $title;
            }
            if( !empty($attributes) ) {
                $optionsInput = array_merge($optionsInput, $attributes);
            }

            if( !empty($inputText) ) {
                $optionsInput['class'] .= ' text-control';

                $content .= $this->Html->tag('div', $inputText, $optionsInput);
            } else {

                if( !empty($type) ) {
                    $optionsInput['type'] = $type;
                }

                $content .= $this->Form->input($fieldName, $optionsInput);
            }

            if( !empty($textGroup)) {
                $contentGroup = $this->Html->tag('div', $textGroup, array(
                    'class' => 'input-group-addon',
                ));

                if( $positionGroup == 'left' ) {
                    $content = $content.$contentGroup;
                } else {
                    $content .= $contentGroup;
                }

                $content = $this->Html->tag('div', $content, array(
                    'class' => 'input-group',
                ));
            }

            if( !empty($divClass) ) {
                $content = $this->Html->tag('div', $content, array(
                    'class' => $divClass,
                ));
            }

            echo $content;
    ?>
</div>