<?php
App::uses('ModelBehavior', 'Model');

class CommonBehavior extends ModelBehavior {
	function filterEmptyField(Model $model, $value, $modelName, $fieldName = false, $empty = false, $options = false){
		$type = !empty($options['type'])?$options['type']:'empty';

		switch($type){
			case 'isset':
				if(empty($fieldName) && isset($value[$modelName])){
					return $value[$modelName];
				} else {
					return isset($value[$modelName][$fieldName])?$value[$modelName][$fieldName]:$empty;
				}
				break;
			
			default:
				if(empty($fieldName) && !empty($value[$modelName])){
					return $value[$modelName];
				} else {
					return !empty($value[$modelName][$fieldName])?$value[$modelName][$fieldName]:$empty;
				}
				break;
		}

		if( !empty($result) && is_string($result) ) {
			$result = urldecode($result);

			if( !empty($trim) ) {
				$result = trim($result);
			}
		}
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
}
