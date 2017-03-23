<?php
App::uses('CakeText', 'Utility');

class Common {
	public static function hashEmptyField($value, $path, $empty = false, $options = false){
		$types = !empty($options['type'])?$options['type']:'empty';
		$date = !empty($options['date'])?$options['date']:false;
		$urldecode = isset($options['urldecode'])?$options['urldecode']:true;
		$addslashes = isset($options['addslashes'])?$options['addslashes']:false;
		$isset = isset($options['isset'])?$options['isset']:false;
		$strict = isset($options['strict'])?$options['strict']:false;
		$result = $empty;

		if( !empty($types) ) {
			if( !is_array($types) ) {
				$types = array(
					$types,
				);
			}

			if( !empty($value) ) {
				if( !empty($isset) ) {
					$resultTmp = $result = Hash::get($value, $path, $empty);
				} else {
					$resultTmp = $result = Hash::get($value, $path);

					if( empty($result) ) {
						$result = $empty;
					}
				}

				if( !empty($strict) && empty($result) ) {
					$result = $empty;
				}
			} else {
				$result = $empty;
			}

			foreach ($types as $key => $type) {
				switch($type){
					case 'slug':
						$result = $this->toSlug($result);
						break;
					case 'strip_tags':
						$result = Common::safeTagPrint($result);
						break;
					case 'unserialize':
						$result = unserialize($result);
						break;
					case 'htmlentities':
						$result = htmlentities($result);
						break;
					case 'EOL':
						$result = str_replace(PHP_EOL, '<br>', $result);
						break;
					case 'trailing_slash':
						$last_char = substr($result, -1);

						if( $last_char === '/' ) {
							$result = rtrim($result, $last_char);
						}
						break;
					case 'currency':
						$result = $this->getFormatPrice($result);
						break;
				}
			}
		}

		if( !empty($date) ) {
			$format = $date;
			$result = Common::formatDate($result, $format);
		}
		if( is_string($result) && $urldecode ) {
			$result = trim(urldecode($result));

			if( !empty($addslashes) ) {
				$result = addslashes($result);
			}
		}

		return $result;
	}

	public static function toSlug($data, $fields = false, $glue = '-') {
		if( !empty($data) ) {
			if( !is_array($data) ) {
				$data = strtolower(Inflector::slug($data, $glue));
			} else {
				foreach ($fields as $key => $value) {
					if( is_array($value) ) {
						foreach ($value as $idx => $fieldName) {
							if( !empty($data[$key][$fieldName]) ) {
								$data[$key][$fieldName] = strtolower(Inflector::slug($data[$key][$fieldName], $glue));
							}
						}
					} else {
						$data[$value] = strtolower(Inflector::slug($data[$value], $glue));
					}
				}
			}
		}

		return $data;
	}

	public static function currentDate( $formatDate = 'Y-m-d H:i:s' ){
		return date($formatDate);
	}

	public static function getFormatPrice($price, $places = 0, $empty = 0){
		App::uses('CakeNumber', 'Utility'); 
		if( !empty($price) ) {
			return CakeNumber::currency($price, '', array('places' => $places));
		} else {
			return $empty;
		}
	}

	public static function safeTagPrint($string){
		if( is_string($string) ) {
			return strip_tags($string);
		} else {
			return $string;
		}
	}

	public static function formatDate($dateString, $format = false, $empty = '-') {
		if( empty($dateString) || $dateString == '0000-00-00' || $dateString == '0000-00-00 00:00:00') {
			return $empty;
		} else {
			if( !empty($format) ) {
				return date($format, strtotime($dateString));
			} else {
				return $this->Time->niceShort(strtotime($dateString));
			}
		}
	}

	public static function dataConverter( $data, $fields, $reverse = false, $round = 0 ) {
		if( !empty($data) && !empty($fields) ) {
			foreach ($fields as $type => $models) {
				$data = Common::_converterLists($type, $data, $models, $reverse, $round);
			}
		}
		return $data;
	}

	public static function _converterLists($type, $data, $models, $reverse = false, $round = 0){
    	if(!empty($type) && !empty($data) && !empty($models)){
    		if(is_array($models)){
    			foreach($models AS $loop => $model){
 	   				if(!empty($model) || $model === 0){ 	   					
	 	   				if( is_array($model) && !empty($data[$loop]) ){
	 	   					if(is_numeric($loop)){
	 	   						foreach($data AS $key => $dat){
	 	   							if(is_array($model) && !empty($dat)){

	 	   								$data[$key] = Common::_converterLists($type, $data[$key], $model, $reverse, $round);
	 	   							}
	 	   						}
	 	   					}else{	 	   						
	 	   						$data[$loop] = Common::_converterLists($type, $data[$loop], $models[$loop], $reverse, $round);
	 	   					}
	 	   				} else if( !is_array($model) ) {	 	
	 	   				   					
	 	   					if(in_array($type, array('unset', 'array_filter'))){
	 	   						if($type == 'array_filter'){
	 	   							$data[$model] = array_filter($data[$model]);
	 	   							if(empty($data[$model])){
	 	   								unset($data[$model]);
	 	   							}
	 	   						}else{
	 	   							unset($data[$model]);
	 	   						}

	 	   					} else if( !empty($data[$model]) ) {	 	   						
	 	   						$data[$model] = Common::_generateType($type, $data[$model], $reverse, $round);
	 	   					}
	 	   				}
	 	   			}
	    		}
    		}else{
    			if(in_array($type, array('unset', 'array_filter'))){
    				if($type == 'array_filter'){
						$data[$models] = array_filter($data[$models]);
						if(empty($data[$models])){
							unset($data[$models]);
						}
					}else{
						unset($data[$models]);
					}
    			}else{
    				$data[$models] = Common::_generateType($type, $data[$models], $reverse, $round);
    			}
    		}
    	}
    	return $data;
    }

    public static function _generateType($type, $data, $reverse, $round){
    	switch($type){
			case 'date' : 
			$data = Common::getDate($data, $reverse);
			break;
		case 'price' : 
			$data = Common::_callPriceConverter($data, $reverse);
			break;
		case 'round' : 
			$data = Common::_callRoundPrice($data, $round);
			break;
		case 'url' : 
			$data = Common::wrapWithHttpLink($data, $reverse);
			break;
		case 'auth_password' : 
			$data = $this->Auth->password($data);
			break;
        case 'daterange':
			$data = Common::_callDateRangeConverter($data);
            break;
        case 'toslug':
			$data = strtolower(Inflector::slug($data, $round));
            break;
        case 'year':
            $data = intval($data);
            $data = !empty($data)?$data:false;
            break;
		## ADA CASE BARU TAMBAHKAN DISINI, ANDA HANYA MEMBUAT $this->FUNCTION yang anda inginkan tanpa merubah flow dari
		## function dataConverter dan _converterLists
		}
		return $data;
    }

    public static function getDate ( $date, $reverse = false ) {
		$dtString = false;
		$date = trim($date);
		if( !empty($date) && $date != '0000-00-00' ) {
			if($reverse){
				$dtString = date('d/m/Y', strtotime($date));
			}else{
				$dtArr = explode('/', $date);

				if( count($dtArr) == 3 ) {
					$dtString = date('Y-m-d', strtotime(sprintf('%s-%s-%s', $dtArr[2], $dtArr[1], $dtArr[0])));
				} else {
					$dtArr = explode('-', $date);

					if( count($dtArr) == 3 ) {
						$dtString = date('Y-m-d', strtotime(sprintf('%s-%s-%s', $dtArr[2], $dtArr[1], $dtArr[0])));
					}
				}
			}
		}
			
		return $dtString;
	}

	public static function _callPriceConverter ($price) {
		$price = Common::safeTagPrint($price);
		return trim(str_replace(array( ',', 'Rp.', 'Rp ' ), array( '', '', '' ), $price));
	}

	function _callRoundPrice($price, $round = 0){
    	if(isset($price)){
    		return round($price, $round);
    	}else{
    		return $empty;
    	}
    }

    function wrapWithHttpLink( $url ){
		$result		= $url;
		$textUrl	= 'http://';
		$textUrls	= 'https://';

		if( !empty($url) ) {
			$flag = array();

			if( strpos($url, $textUrl) === false && substr($url, 0, 7) != $textUrl ) {
				$flag[] = true;
			}
			if( strpos($url, $textUrls) === false && substr($url, 0, 8) != $textUrls ) {
				$flag[] = true;
			}

			if( count($flag) == 2 ) {
				$result = sprintf("%s%s", $textUrl, $url);
			}
		}

		return $result;
	}

	public static function _callDateRangeConverter ( $daterange, $fieldName = 'date', $fromName = 'start_date', $toName = 'end_date' ) {
    	$result = array();

        if( !empty($daterange) ) {
            $dateStr = urldecode($daterange);
            $daterange = explode('-', $dateStr);

            if( !empty($daterange) ) {
                $daterange[0] = urldecode($daterange[0]);
                $daterange[1] = urldecode($daterange[1]);
                $dateFrom = Common::getDate($daterange[0]);
                $dateTo = Common::getDate($daterange[1]);
                $result[$fromName] = $dateFrom;
                $result[$toName] = $dateTo;
            }
        }

        return $result;
    }

    public static function insertField($values, $options = array()){
    	$source = Common::filterEmptyField($options, 'source');
    	$target = Common::filterEmptyField($options, 'target');

    	if(!empty($values)){
    		if(!empty($values[0])){
    			foreach ($values as $key => $value) {
    				$data_source = Common::filterEmptyField($value, $source, 'name');
    				$value[$target]['name'] = $data_source;

    				$values[$key] = $value;
    			}
    		}else{
    			$data_source = Common::filterEmptyField($values, $source, 'name');
				$values[$target]['name'] = $data_source;
    		}
    	}
    	return $values;
    }

	public static function _callUnset( $data, $fieldArr , $removeField = false) {
		if( !empty($fieldArr) ) {
			foreach ($fieldArr as $key => $value) {
				if( is_array($value) ) {
					foreach ($value as $idx => $fieldName) {
						if( isset($data[$key][$fieldName]) ) {
							unset($data[$key][$fieldName]);
						}else{
							if($removeField){
								unset($data[$key][$fieldName]);
							}
						}
					}
				} else {
					unset($data[$value]);
				}
			}
		}
		return $data;
	}

	public static function _callSet( $data, $fieldArr ) {
		if( !empty($fieldArr) && !empty($data) ) {
			$data = array_intersect_key($data, array_flip($fieldArr));
		}
		return $data;
	}

	public static function _callConvertDateRange ( $params, $date, $options = array() ) {
		$startField = Common::filterEmptyField($options, 'date_from', false, 'date_from');
		$endField = Common::filterEmptyField($options, 'date_to', false, 'date_to');

		$date = urldecode($date);
		$dateArr = explode(' - ', $date);

		if( !empty($dateArr) && count($dateArr) == 2 ) {
			$fromDate = !empty($dateArr[0])?Common::getDate($dateArr[0]):false;
			$toDate = !empty($dateArr[1])?Common::getDate($dateArr[1]):false;

			$params[$startField] = $fromDate;
			$params[$endField] = $toDate;
		}

		return $params;
	}
	
	public static function _search($controller, $action, $_admin = true, $addParam = false){
		$data = $controller->request->data;
		$named = Common::filterEmptyField($controller->params, 'named');
		$params = array(
			'action' => $action,
			$addParam,
			'admin' => $_admin,
		);

		if( !empty($named) ) {
			$params = array_merge($params, $named);
		}

		return Common::processSorting($controller, $params, $data);
	}

	public static function processSorting ( $controller, $params, $data, $with_param_id = true, $param_id_only = false, $redirect = true ) {
		$filter = Common::filterEmptyField($data, 'Search', 'filter');
		$sort = Common::filterEmptyField($data, 'Search', 'sort');
		$excel = Common::filterEmptyField($data, 'Search', 'excel');
		$min_price = Common::filterEmptyField($data, 'Search', 'min_price', 0);
		$max_price = Common::filterEmptyField($data, 'Search', 'max_price', 0);
		$user = Common::filterEmptyField($data, 'Search', 'user');

		$named = Common::filterEmptyField($controller->params, 'named');

		if( !empty($with_param_id) ) {
			$param_id = Common::filterEmptyField($named, 'param_id');

			if( is_array($param_id) ) {
				$params = array_merge($params, $param_id);
			} else {
				$params[] = $param_id;
			}
		}

		if( !empty($param_id_only) ) {
			return $params;
		}

		if(!empty($data['Search']['change_url'])){
			unset($data['Search']['change_url']);
		}

		$dateFilter = array(
			'date',
			'modified',
			'last_login',
		);
		$data = Common::_callUnset(array(
			'Search' => array(
				'sort',
				'direction',
				'excel',
				'action',
				'min_price',
				'max_price',
				'colview',
			),
		), $data);

		if( !empty($dateFilter) ) {
			foreach ($dateFilter as $key => $fieldFilter) {
				$date = Common::filterEmptyField($data, 'Search', $fieldFilter);
				$fieldFrom = __('%s_from', $fieldFilter);
				$fieldTo = __('%s_to', $fieldFilter);

				$data = Common::_callUnset(array(
					'Search' => array(
						$fieldFilter,
					),
				), $data);

				if( empty($date) ) {
					$date_from = Common::filterEmptyField($data, 'Search', $fieldFrom);
					$date_to = Common::filterEmptyField($data, 'Search', $fieldTo);

					if( !empty($date_from) && !empty($date_to) ) {
						$date = sprintf('%s - %s', $date_from, $date_to);
					}
				}

				if( !empty($date) ) {
					$params = $this->_callConvertDateRange($params, $date, array(
						'date_from' => $fieldFrom,
						'date_to' => $fieldTo,
					));
				}
			}
		}

		$dataSearch = Common::filterEmptyField($data, 'Search');
		if( isset($dataSearch['keyword']) ) {
			$dataSearch['keyword'] = urlencode(trim($dataSearch['keyword']));
		}
		
		if( !empty($dataSearch) ) {
			foreach ($dataSearch as $fieldName => $value) {
				if( is_array($value) ) {
					$value = array_filter($value);

					if( !empty($value) ) {
						$result = array();

						foreach ($value as $id => $boolean) {
							if( !empty($id) ) {
								$result[] = $id;
							}
						}

						$value = implode(',', $result);
					}
				}

				if( !empty($value) ) {
					if( !is_array($value) ) {
						$params[$fieldName] = urlencode($value);
					} else {
						$params[$fieldName] = $value;
					}
				}
			}
		}
		if( !empty($filter) ) {
			$filterArr = strpos($filter, '.');

			if( !empty($filterArr) ) {
				$sort = $filter;
			}
		}

		if( !empty($sort) ) {
			$dataArr = explode('-', $sort);

			if( !empty($dataArr) && count($dataArr) == 2 ) {
				$sort = !empty($dataArr[0])?$dataArr[0]:false;
				$direction = !empty($dataArr[1])?$dataArr[1]:false;

				$sortLower = strtolower($sort);
				$directionLower = strtolower($direction);

				if( !in_array($direction, array( 'asc', 'desc' )) ) {
					$params[$sort] = $direction;
				} else {
					$params['sort'] = $sort;
					$params['direction'] = $direction;
				}
			}
		}

		if( !empty($excel) ) {
			$params['export'] = 'excel';
		}
		if( !empty($min_price) || !empty($max_price) ) {
			$min_price = $this->_callPriceConverter($min_price);
			$max_price = $this->_callPriceConverter($max_price);

			if( empty($max_price) ) {
				$price = $min_price;
			} else {
				$price = sprintf('%s-%s', $min_price, $max_price);
			}

			$params['price'] = $price;
		}

		if(!empty($user)){
			$params['user'] = $user;
		}

		return $params;
	}

    public static function getCombineDate ( $startDate, $endDate, $format = 'long', $separator = '-' ) {
        $startDate = strtotime($startDate);
        $endDate = strtotime($endDate);

        if( !empty($startDate) && !empty($endDate) ) {
            switch ($format) {
                case 'short':
                    if( $startDate == $endDate ) {
                        $customDate = date('M Y', $startDate);
                    } else if( date('Y', $startDate) == date('Y', $endDate) ) {
                        $customDate = sprintf('%s %s %s', date('M', $startDate), $separator, date('M Y', $endDate));
                    } else {
                        $customDate = sprintf('%s %s %s', date('M Y', $startDate), $separator, date('M Y', $endDate));
                    }
                    break;
                
                default:
                    if( $startDate == $endDate ) {
                        $customDate = date('d M Y', $startDate);
                    } else if( date('M Y', $startDate) == date('M Y', $endDate) ) {
                        $customDate = sprintf('%s %s %s', date('d', $startDate), $separator, date('d M Y', $endDate));
                    } else if( date('Y', $startDate) == date('Y', $endDate) ) {
                        $customDate = sprintf('%s %s %s', date('d M', $startDate), $separator, date('d M Y', $endDate));
                    } else {
                        $customDate = sprintf('%s %s %s', date('d M Y', $startDate), $separator, date('d M Y', $endDate));
                    }
                    break;
            }
            return $customDate;
        }
        return false;
    }

    public static function _callDisplayToggle ( $type, $value, $boolean = null ) {
        $document_type = Common::hashEmptyField($value, 'Spk.document_type');
        $result = '';
        
        switch ($type) {
            case 'mechanic':
                if( !in_array($document_type, array( 'internal', 'production' )) ) {
                    $result = 'hide';
                }
                break;
            case 'wht':
                if( !in_array($document_type, array( 'wht' )) ) {
                    $result = 'hide';
                }
                break;
            case 'eksternal':
                if( !in_array($document_type, array( 'eksternal' )) ) {
                    $result = 'hide';
                }
                break;
            case 'production':
                if( !in_array($document_type, array( 'production' )) ) {
                    $result = 'hide';
                }
                break;
            case 'non-production':
                if( !in_array($document_type, array( 'internal' )) ) {
                    $result = 'hide';
                }
                break;
        }

        if( !empty($boolean) ) {
        	if( $result == 'hide' ) {
        		$result = false;
        	} else {
        		$result = true;
        	}
        }

        return $result;
    }
}
