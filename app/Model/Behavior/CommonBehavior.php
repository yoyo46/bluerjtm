<?php
App::uses('ModelBehavior', 'Model');

class CommonBehavior extends ModelBehavior {
	function formatConverter ( Model $model, $result, $format ) {
		switch ($format) {
			case 'number':
				$result = $result * 1;
				break;
		}

		return $result;
	}

	function filterEmptyField(Model $model, $value, $modelName, $fieldName = false, $empty = false, $options = false){
		$type = !empty($options['type'])?$options['type']:'empty';
		$formats = !empty($options['format'])?$options['format']:false;
		$result = false;

		switch($type){
			case 'isset':
				if(empty($fieldName) && isset($value[$modelName])){
					$result = $value[$modelName];
				} else {
					$result = isset($value[$modelName][$fieldName])?$value[$modelName][$fieldName]:$empty;
				}
				break;
			
			default:
				if(empty($fieldName) && !empty($value[$modelName])){
					$result = $value[$modelName];
				} else {
					$result = !empty($value[$modelName][$fieldName])?$value[$modelName][$fieldName]:$empty;
				}
				break;
		}

		if( !empty($formats ) ){
			if( is_array($formats) ) {
				foreach ($formats as $key => $format) {
					$result = $this->formatConverter($model, $result, $format);
				}
			} else {
				$result = $this->formatConverter($model, $result, $formats);
			}
		}

		return $result;
	}

    function convertPriceToString ( Model $model, $price, $result = '', $places = 0 ) {
        if( !empty($price) ) {
            $resultTmp = str_replace(array(',', ' '), array('', ''), trim($price));
            $resultTmp = sprintf('%.'.$places.'f', $resultTmp);

            if( !empty($resultTmp) ) {
                $result = $resultTmp;
            }
        }

        return $result;
    }

    function customDate(Model $model, $dateString, $format = 'd F Y', $result = '') {
        if( !empty($dateString) && $dateString != '0000-00-00' && $dateString != '0000-00-00 00:00:00' ) {
            $result = date($format, strtotime($dateString));
        }

        return $result;
    }

	public function callSet( Model $model, $data, $fieldArr ) {
		if( !empty($fieldArr) && !empty($data) ) {
			$data = array_intersect_key($data, array_flip($fieldArr));
		}
		return $data;
	}

	public function callUnset( Model $model, $data = false, $fieldArr = false){
		if(!empty($fieldArr)){
			foreach($fieldArr as $key => $value){
				if(is_array($value)){
					foreach($value as $idx => $fieldName){
						if(!empty($data[$key][$fieldName])){
							unset($data[$key][$fieldName]);
						}
					}
				} else {
					unset($data[$value]);
				}
			}
		}
		return $data;
	}

	public function getMerge( model $model, $data, $modelName,  $id = false, $options = array() ) {
		// $conditions = !empty($options['conditions'])?$options['conditions']:array();
		$options = !empty($options)?$options:array();
		$elements = !empty($options['elements'])?$options['elements']:array();
		$alias = $this->filterEmptyField($model, $options, 'uses');
		$uses = $this->filterEmptyField($model, $options, 'uses', false, $modelName);
		$foreignKey = !empty($options['foreignKey'])?$options['foreignKey']:'id';
		$primaryKey = !empty($options['primaryKey'])?$options['primaryKey']:$foreignKey;
		$position = !empty($options['position'])?$options['position']:'outside';
		$type = !empty($options['type'])?$options['type']:'first';
		$parentModelName = $model->name;

		$optionsModel = $this->callSet($model, $options, array(
			'conditions',
			// 'contain',
			'fields',
			'group',
			'limit',
			'order',
		));

		if(empty($data[$modelName])){

			if(!empty($uses)){
				if( $uses == $model->name ) {
					$model = $model;
				} else {
					$model = $model->$uses;
				}
			}else{
				$model = $model->$modelName;
			}

			$optionsModel['conditions'][sprintf('%s.%s', $uses, $primaryKey)] = $id;
			$value = $model->getData($type, $optionsModel, $elements);

			if(!empty($value)){
				switch ($type) {
					case 'count':
						$data[$modelName] = $value;
						break;
					case 'list':
						$data[$modelName] = $value;
						break;
					
					default:
						if(!empty($alias) ){
							if( !empty($value[$alias]) ) {
								$data[$modelName] = $value[$alias];
							} else if(!empty($value[0])){
								$data[$modelName] = $value;
							}
						}else{
							if(!empty($value[0])){
								$data[$modelName] = $value;
							}else{
								switch ($position) {
									case 'inside':
										if( !empty($parentModelName) ) {
											$parentDataModel = !empty($data[$parentModelName])?$data[$parentModelName]:array();
											$data[$parentModelName] = array_merge($parentDataModel, $value);
										} else {
											$data = array_merge($data, $value);
										}
										break;
									
									default:
										$data = array_merge($data, $value);
										break;
								}
							}
						}
						break;
				}
			}
		}	

		return $data;
	}

	function _callMergeData ( Model $model, $value, $element, $options, $modelName ) {
		$mergeParents = $this->filterEmptyField($model, $element, 'mergeParent', false, array());
		$generateMultiples = $this->filterEmptyField($model, $element, 'generateMultiple', false, array());

		if( !is_array($options) ) {
			$modelName = $uses = $options;
			$optionsParams = array();
		} else {
			$mergeParent = $this->filterEmptyField( $model, $options, 'modelParent');

			$options = $this->callUnset($model, $options, array(
				'modelParent',
			));

			$optionsParams = $options; ## CONDITIONS, ELEMENTS for getData 
			$uses = $this->filterEmptyField($model, $options, 'uses', false, $modelName);

			if( !empty($options) ) {
				$containRecursive = $this->filterEmptyField( $model, $options, 'contain');

				if( empty($containRecursive) && !empty($options[0]) ) {
					$containRecursive = $options;
				}
			}
		}

		$type = $this->filterEmptyField($model, $optionsParams, 'type');
		$forceMerge = $this->filterEmptyField($model, $optionsParams, 'forceMerge');

		if( !empty($mergeParent) ) {
			$modelParent = $this->filterEmptyField($model, $mergeParent, 0);
			$foreignKey = $this->filterEmptyField($model, $mergeParent, 1);
		} else {
			$modelParent = $model->name;
			$foreignKey = 'id';

			if( !empty($options['foreignKey']) && is_array($options) ) {
				$foreignKey = $options['foreignKey'];
			} else if( !empty($model->belongsTo[$uses]['foreignKey']) ) {
				$foreignKey = $model->belongsTo[$uses]['foreignKey'];
				$optionsParams = array_merge($optionsParams, array(
					'foreignKey' => $foreignKey,
					'primaryKey' => 'id',
				));
			} else if( !empty($model->hasOne[$uses]['foreignKey']) ) {
				$foreignKey = 'id';
				$optionsParams = array_merge($optionsParams, array(
					'primaryKey' => $model->hasOne[$uses]['foreignKey'],
					'foreignKey' => $foreignKey,
				));
			} else if( !empty($model->hasMany[$uses]['foreignKey']) ) {
				$primaryKey = $model->hasMany[$uses]['foreignKey'];
				$optionsParams = array_merge($optionsParams, array(
					'foreignKey' => $foreignKey,
					'primaryKey' => $primaryKey,
				));
				$type_custom = 'all';
			}

			if( empty($type) && !empty($type_custom) ) {
				$optionsParams['type'] = $type_custom;
			}
		}

		if( empty($value[$modelName]) || !empty($forceMerge) ){
			if( !empty($value[$modelName]) ) {
				$value = $this->callUnset($model, $value, array(
					$modelName,
				));
			}

			$id = $this->filterEmptyField( $model, $value, $modelParent, $foreignKey);

			if( empty($id) ) {
				$id = $this->filterEmptyField( $model, $value, $foreignKey);
			}

			if( !empty($id) ) {
				## MERGEDATA JIKA DATA YANG INGIN DI MERGE BERSIFAT JAMAK/MULTIPLE 
				## FUNGSI GETMERGE DI MODEL TERSEBUT HARUS DITAMBAHKAN PARAMETER KETIGA FIND = 'ALL/FIRST/ DLL'
				$value = $this->getMerge( $model, $value, $modelName, $id, $optionsParams);
				## KETIKA SUDAH DI BUILD DENGAN FUNGSI GETMERGE UNTUK DATA JAMAK HARUS
				## MODEL => INDEX => MODEL => VALUE, ANDA BISA UBAH DATA DENGAN generateMultiples ATAU mergeParents

				## KETIKA DATA MULTIPlE SUDAH DIBUILD dengan GENERATEMULTIPLE DIBAWAH INI, MENJADI MODEL => IDX => VALUE
				if(in_array( $modelName, $generateMultiples)){
					if(!empty($value[$modelName])){
						if(!empty($value[$modelName][0])){
							$temp_model = array();
							foreach($value[$modelName] AS $key_multiple => $modelParams){
								$temp_model[$key_multiple] = $modelParams[$modelName];
							}
							$value[$modelName] = $temp_model;
						}
						
					}
				## KETIKA DATA MULTIPlE SUDAH DIBUILD dengan MERGEPARENT DIBAWAH INI, MENJADI PARENTMODEL => MODEL => IDX => VALUE
				}elseif(in_array($modelName,$mergeParents)){

					if(!empty($value[$modelName])){

						if(!empty($value[$modelName][0])){
							$temp_model = array();
							foreach($value[$modelName] AS $key_merge => $modelParams){
								$temp_model[$key_merge] = $modelParams[$contain];
							}
							$value[$this->name][$modelName] = $temp_model;
							unset($value[$modelName]);
						}else{
							$value[$this->name][$modelName] = $value[$contain];
							unset($value[$modelName]);
						}
					}
				}
			}
		}

		if(!empty($containRecursive)){
			$valueTemps = array();
			
			if(!empty($value[$modelName])){
				$valueTemps = $this->getMergeList($model->$uses, $value[$modelName], array(
					'contain' => $containRecursive,
				));

				if(!empty($valueTemps)){
					$value = $this->callUnset($model->$uses, $value, array(
						$modelName
					));
					$value[$modelName] = $valueTemps;
				}
			}
		}

		return $value;
	}

	public function getMergeList( Model $model, $values, $options, $element = false){
		$contains = $this->filterEmptyField($model, $options, 'contain');

		if(!empty($values)){
			if(!empty($values[0])){
				foreach($values AS $key => $value){
					foreach($contains AS $modelName => $options){
						$value = $this->_callMergeData($model, $value, $element, $options, $modelName);
					}
					$values[$key] = $value;
				}

			}else{
				foreach($contains AS $modelName => $options){
					$values = $this->_callMergeData($model, $values, $element, $options, $modelName);
				}
			}
		}
		return $values;
	}

    function callSettingGeneral ( Model $model, $labelName = null ) {
        $data = array();

        $this->SettingGeneral = ClassRegistry::init('SettingGeneral'); 
        $conditions = false;

        if( !empty($labelName) ) {
            $conditions['SettingGeneral.name'] = $labelName;
        }

        $values = $this->SettingGeneral->find('all', array(
            'conditions' => $conditions,
        ));
        

        if( !empty($values) ) {
            foreach ($values as $key => $value) {
                $lbl = $this->filterEmptyField($model, $value, 'SettingGeneral', 'name');
                $value = $this->filterEmptyField($model, $value, 'SettingGeneral', 'value');

                $data[$lbl] = $value;
            }
        }

        return $data;
    }

	function merge_options(model $model, $default_options, $options = array()){
		if( !empty($options) ){
			if(!empty($options['conditions'])){
				$default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
			}
			if(!empty($options['order'])){
				$default_options['order'] = $options['order'];
			}
			if( isset($options['contain']) && empty($options['contain']) ) {
				$default_options['contain'] = false;
			} else if(!empty($options['contain'])){
				$default_options['contain'] = array_merge($default_options['contain'], $options['contain']);
			}
			if(!empty($options['limit'])){
				$default_options['limit'] = $options['limit'];
			}
			if(!empty($options['fields'])){
				$default_options['fields'] = $options['fields'];
			}
			if(!empty($options['group'])){
				$default_options['group'] = $options['group'];
			}
			if(!empty($options['offset'])){
				$default_options['offset'] = $options['offset'];
			}
			if(!empty($options['cache'])){
                $default_options['cache'] = $options['cache'];
                    
                if(!empty($options['cacheConfig'])){
                    $default_options['cacheConfig'] = $options['cacheConfig'];
                }
            }
		}
		return $default_options;
	}

	function callFieldOr($field, $value){
		if( !empty($field['OR']) ) {
			$fieldOr = $field['OR'];
			$fields = array();

			unset($field['OR']);

			foreach ($fieldOr as $key => $fieldName) {
				$fields[sprintf('%s LIKE', $fieldName)] = $value;
			}

			$field['OR'] = $fields;
		}

		return $field;
	}
	
	function typeOptionParams($model, $named, $slug, $option){
		$flag = $code = false;
		$type = Common::hashEmptyField($option, 'type');
		$field = Common::hashEmptyField($option, 'field');
		$value = Common::hashEmptyField($named, $slug, false, array(
        	'addslashes' => true,
        	'urldecode_double' => false,
    	));

		if($value){
			switch ($type) {
				case 'like':
					$value = '%'.$value.'%';

					if( !empty($field['OR']) ) {
						$field = $this->callFieldOr($field, $value);
					} else {
						$field = sprintf('%s LIKE', $field);
					}
					break;

				case 'boolean':

					if($value == 'active'){
						$value = true;
						$flag = true;
					}else if($value == 'inactive'){
						$value = false;
						$flag = true;
					}else{
						$value = false;
					}
					break;

				case 'operator':
					$select_field = Common::hashEmptyField($option, 'select_field');
					// $select = Common::hashEmptyField($named, $select_field, false, array(
			  //       	'addslashes' => true,
			  //   	));
			    	
			    	switch ($select_field) {
			    		case 'notequal':
			    			$code = '<>';
			    			break;
			    		case 'more':
			    			$code = '>=';
			    			break;
			    		case 'less':
			    			$code = '<=';
			    			break;
			    	}

					break;
				case 'equal':
					if( !empty($field['OR']) ) {
						$field = $this->callFieldOr($field, $value);
					}
					break;
				case 'parent':
					$sourceField = Common::hashEmptyField($option, 'use.sourceField');
					$val = $value;
					$value = $model->getData('list', array(
						'conditions' => array(
							sprintf('%s like', $sourceField) => '%'.$val.'%',
						),
						'fields' => array('id', 'id'),
					));

					if(!empty($val) && empty($value)){
						$value = $model->getData('list', array(
							'fields' => array('id', 'id'),
						));

						$field = sprintf('%s <>', $field);
					}
					break;
			}
		}

		return array_merge($option, array(
			'flag' => $flag,
			'field' => $field,
			'value' => $value,
			'code' => $code,
		));
	}

	function defaultOptionParams(model $model, $data, $default_options = false, $options = array()){
		$modelName = $model->name;
		$named = Common::hashEmptyField($data, 'named');

		if(!empty($options) && !empty($named)){
			foreach ($options as $slug => $option) {
				$contain_arr = Common::hashEmptyField($option, 'contain');
				$type = Common::hashEmptyField($option, 'type');
				$contain = array();

				if($contain_arr){
					if(!is_array($contain_arr)){
						$contain[] = $contain_arr;
					}else{
						$contain = $contain_arr;
					}
				}

		    	$option = $this->typeOptionParams($model, $named, $slug, $option);
				$field = Common::hashEmptyField($option, 'field');
				$code = Common::hashEmptyField($option, 'code');
				$flag = Common::hashEmptyField($option, 'flag');
				$value = Common::hashEmptyField($option, 'value', false, array(
		        	'addslashes' => true,
		        	'urldecode' => false,
		    	));
				$virtualFields = Common::hashEmptyField($option, 'virtualFields');

		    	if($value || (in_array($type, array('boolean')) && !empty($flag))){
		    		if( is_array($field) ) {
		    			$default_options['conditions'][] = $field;
		    		} else {
		    			if($code){
		    				$field = sprintf('%s %s', $field, $code);	
		    			}
		    			$default_options['conditions'][$field] = $value;
		    		}

			    	if($contain){
			    		if(!empty($default_options['contain'])){
			    			$default_options['contain'] = array_merge($default_options['contain'], $contain);
			    		}else{
			    			$default_options['contain'] = $contain;
			    		}
			    	}

			    	if( !empty($virtualFields) ) {
			    		foreach ($virtualFields as $modelName => $virtuals) {
			    			if( !empty($virtuals) ) {
			    				foreach ($virtuals as $vfield => $nvirtual) {
					    			if( $model->name == $modelName ) {
					    				$model->virtualFields[$vfield] = $nvirtual;
					    			} else {
					    				$model->$modelName->virtualFields[$vfield] = $nvirtual;
					    			}
			    				}
			    			}
			    		}
			    	}
		    	}
			}
		}
		return $default_options;
	}
}
