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
}
