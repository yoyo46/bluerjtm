<?php 
		$dataRequest = $this->request->data;

		$fieldName = !empty($fieldName)?$fieldName:false;
		$frameClass = !empty($frameClass)?$frameClass:false;
		$text = !empty($text)?$text:false;
		$label = !empty($label)?$label:false;
		$div = !empty($div)?$div:false;
		$preview = !empty($preview)?$preview:false;
		
		$attributes = !empty($attributes)?$attributes:array();
		$attributes = $this->Common->_callUnset(array(
			'preview',
		), $attributes);

		$type = $this->Common->filterEmptyField($attributes, 'type'); 
		$fieldError = $this->Common->filterEmptyField($attributes, 'fieldError', false, $fieldName); 
		$fieldErrorMsg = $this->Common->filterEmptyField($attributes, 'fieldErrorMsg'); 
		$show_error = $this->Common->filterIssetField($attributes, 'show_error', false, true); 
		$otherContent = $this->Common->filterIssetField($attributes, 'otherContent', false, false); 

		if(!empty($otherContent)){
			unset($attributes['otherContent']);
		}

		if( is_array($label) ) {
			$labelText = $this->Common->filterEmptyField($label, 'text', false, false, false); 
			$labelPosition = $this->Common->filterEmptyField($label, 'position', false, 'block'); 
			$labelAttributes = $this->Common->_callUnset(array(
				'text',
			), $label);
		} else {
			$labelText = $label;
			$labelAttributes = false;
			$labelPosition = 'block'; 
		}

		if( is_array($div) ) {
			$divClass = $this->Common->filterEmptyField($div, 'class'); 
		} else {
			$divClass = $div;
		}

		$errorMsg = '';
		if($show_error){
			if( !empty($fieldErrorMsg) ) {
				$errorMsg = $this->Html->tag('div', $fieldErrorMsg, array(
	                'class' => 'error-message'
	            ));
			} else if( !empty($fieldError) && $this->Form->isFieldError($fieldError) ) {
	            $errorMsg = $this->Form->error($fieldError, null, array(
	                'class' => 'error-message'
	            ));
	        } 
		}
?>
<div class="<?php echo $frameClass; ?>">
	<?php 
			if( !empty($labelText) && $labelPosition == 'block' ) {
				echo $this->Form->label($fieldName, $labelText, $labelAttributes);
			}

			switch ($type) {
				case 'datetime':
					echo $this->element('blocks/common/forms/inputs/datetime', array(
						'fieldName' => $fieldName,
						'attributes' => $attributes,
						'div' => $div,
						'errorMsg' => $errorMsg,
					));
					break;
				case 'radio':
					echo $this->element('blocks/common/forms/inputs/radio', array(
						'fieldName' => $fieldName,
						'attributes' => $attributes,
						'div' => $div,
						'errorMsg' => $errorMsg,
					));
					break;
				case 'checkbox':
					echo $this->element('blocks/common/forms/inputs/checkbox', array(
						'fieldName' => $fieldName,
						'attributes' => $attributes,
						'div' => $div,
						'labelText' => $labelText,
						'labelPosition' => $labelPosition,
						'labelAttributes' => $labelAttributes,
						'errorMsg' => $errorMsg,
					));
					break;
				case 'checkbox_multiple':
					echo $this->element('blocks/common/forms/inputs/checkbox_multiple', array(
						'fieldName' => $fieldName,
						'attributes' => $attributes,
						'div' => $divClass,
					));
					break;
				
				default:
					$input_form = $this->Form->input($fieldName, array_merge($attributes, array(
						'div' => false,
						'label' => false,
						'error' => false,
					)));

					if( !empty($otherContent) ) {
                        $modelOtherName = $this->Common->filterEmptyField($otherContent, 'modelName');
                        $fieldOtherName = $this->Common->filterEmptyField($otherContent, 'fieldName');
                        $descriptionOther = $this->Common->filterEmptyField($otherContent, 'description');
                        $fieldNameTrigger = $this->Common->filterEmptyField($otherContent, 'fieldNameTrigger');
                        $fieldValueTrigger = $this->Common->filterEmptyField($otherContent, 'fieldValueTrigger');

                        $valueOther = $this->Common->filterEmptyField($dataRequest, $modelOtherName, $fieldNameTrigger);

                        if( $valueOther == $fieldValueTrigger ) {
                            $addClassOther = 'show full-width';
                        } else {
                            $addClassOther = 'hide full-width';
                        }

                        $input_form .= $this->Form->input($modelOtherName.'.'.$fieldOtherName, array(
                            'type' => 'text',
                            'id' => 'other-text',
                            'class' => $addClassOther,
                            'label' => false,
                            'div' => false,
                            'required' => false,
                            'placeholder' => $descriptionOther,
                        ));
                    }

		            $input_form .= $errorMsg;

		            if( $labelPosition == 'inline' ) {
		            	$input_form .= $this->Form->label($fieldName, $labelText, $labelAttributes);
		            }

					if( !empty($divClass) ) {
						echo $this->Html->tag('div', $input_form, array(
							'class' => $divClass,
						));
					} else {
						echo $input_form;
					}
					break;
			}

			if( !empty($text) ) {
				echo $this->Html->tag('span', $text, array(
					'class' => 'small',
				));
			}
	?>
</div>