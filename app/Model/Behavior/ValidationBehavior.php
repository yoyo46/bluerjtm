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

    public function checkImport (Model $model, $data, $fieldName) {
        $value = Common::hashEmptyField($data, $fieldName);
        $is_import = Common::hashEmptyField($model->data, $model->alias.'.is_import');

        if( !empty($value) ) {
        	return true;
        } else {
        	if( !empty($is_import) ) {
        		return true;
        	} else {
        		return false;
        	}
        }
    }

    // public function checkUniqueImport (Model $model, $data, $fieldName) {
    //     $value = Common::hashEmptyField($data, $fieldName);
    //     $is_import = Common::hashEmptyField($model->data, $model->alias.'.is_import');
    //     $flag = false;

    //     if( !empty($value) ) {
    //     	$flag = true;
    //     } else {
    //     	if( !empty($is_import) ) {
    //     		$flag = true;
    //     	}
    //     }

    //     // if( !empty($flag) && empty($is_import) ) {
    //     if( !empty($flag) ) {
    //     	$exist = $model->getUnique(array(), $value, $fieldName);
    //     	debug($exist);die();
    //     }

    //     return $flag;
    // }

    function checkUnique (Model $model, $current, $field) {
        $id = $model->id;
        $id = Common::hashEmptyField($model->data, $model->alias.'.id', $id);

        $val = !empty($model->data[$model->alias][$field])?$model->data[$model->alias][$field]:false;
        $value = $model->getData('count', array(
            'conditions' => array(
                $model->alias.'.'.$field => $val,
                $model->alias.'.id NOT' => $id,
            ),
        ));
        
        if( !empty($value) ) {
            return false;
        } else {
            return true;
        }
    }
}
