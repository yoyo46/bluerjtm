<?php
App::uses('ModelBehavior', 'Model');

class ValidationBehavior extends ModelBehavior {
	function serial_number(Model $model, $data){
		$data = $model->data;
		$is_serial_number = $model->filterEmptyField($data, 'ProductReceiptDetail', 'is_serial_number');
		$serial_number = $model->filterEmptyField($data, 'ProductReceiptDetail', 'serial_number');
		
		if( !empty($is_serial_number) ) {
			if( !empty($serial_number) ) {
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
	}

	public function emptyFill(Model $model, $data, $fieldName){
		if( !empty($data[$fieldName]) ){
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function validateValue(Model $model, $data, $fieldName){
		if( !empty($data[$fieldName]) ){
			return FALSE;
		} else {
			return true;
		}
	}

    public function callNumber (Model $model, $data, $fieldName) {
        $value = Common::hashEmptyField($data, $fieldName);

        if( !empty($value) ) {
            if( is_numeric($value) ) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }
}
