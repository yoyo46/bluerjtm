<?php
/**
 * Application model for CakePHP.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model {
	public $recursive = -1;
	public $actsAs = array('Containable');

	function filterEmptyField ( $value, $modelName, $fieldName = false, $empty = false, $options = false ) {
		$type = !empty($options['type'])?$options['type']:'empty';

		switch ($type) {
			case 'isset':
				if( empty($fieldName) && isset($value[$modelName]) ) {
					return $value[$modelName];
				} else {
					return isset($value[$modelName][$fieldName])?$value[$modelName][$fieldName]:$empty;
				}
				break;
			
			default:
				if( empty($fieldName) && !empty($value[$modelName]) ) {
					return $value[$modelName];
				} else {
					return !empty($value[$modelName][$fieldName])?$value[$modelName][$fieldName]:$empty;
				}
				break;
		}
	}

    function convertPriceToString ( $price, $result = '', $places = 0 ) {
        if( !empty($price) ) {
            $resultTmp = str_replace(array(',', ' '), array('', ''), trim($price));
            $resultTmp = sprintf('%.'.$places.'f', $resultTmp);

            if( !empty($resultTmp) ) {
                $result = $resultTmp;
            }
        }

        return $result;
    }
}
