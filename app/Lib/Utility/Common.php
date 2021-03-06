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
						$result = Common::getFormatPrice($result);
						break;
					case 'unprice':
						$result = Common::_callPriceConverter($result);
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
    	$source = Common::hashEmptyField($options, 'source');
    	$target = Common::hashEmptyField($options, 'target');

    	if(!empty($values)){
    		if(!empty($values[0])){
    			foreach ($values as $key => $value) {
    				$data_source = Common::hashEmptyField($value, $source.'.name');
    				$value[$target]['name'] = $data_source;

    				$values[$key] = $value;
    			}
    		}else{
    			$data_source = Common::hashEmptyField($values, $source.'.name');
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
		$startField = Common::hashEmptyField($options, 'date_from', 'DateFrom');
		$endField = Common::hashEmptyField($options, 'date_to', 'DateTo');

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
		$params = $controller->params->params;
		$named = Common::hashEmptyField($params, 'named');
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
		$filter = Common::hashEmptyField($data, 'Search.filter');
		$sort = Common::hashEmptyField($data, 'Search.sort');
		$excel = Common::hashEmptyField($data, 'Search.excel');
		$min_price = Common::hashEmptyField($data, 'Search.min_price', 0);
		$max_price = Common::hashEmptyField($data, 'Search.max_price', 0);
		$user = Common::hashEmptyField($data, 'Search.user');

		$param_params = $controller->params->params;
		$named = Common::hashEmptyField($param_params, 'named');

		if( !empty($with_param_id) ) {
			$param_id = Common::hashEmptyField($named, 'param_id');

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
		$data = Common::_callUnset($data, array(
			'Search' => array(
				'sort',
				'direction',
				'excel',
				'action',
				'min_price',
				'max_price',
				'colview',
			),
		));

		if( !empty($dateFilter) ) {
			foreach ($dateFilter as $key => $fieldFilter) {
				$date = Common::hashEmptyField($data, 'Search.'.$fieldFilter);
				$fieldFrom = __('%sFrom', ucwords($fieldFilter));
				$fieldTo = __('%sTo', ucwords($fieldFilter));

				$data = Common::_callUnset($data, array(
					'Search' => array(
						$fieldFilter,
					),
				));

				if( empty($date) ) {
					$date_from = Common::hashEmptyField($data, 'Search.'.$fieldFrom);
					$date_to = Common::hashEmptyField($data, 'Search.'.$fieldTo);

					if( !empty($date_from) && !empty($date_to) ) {
						$date = sprintf('%s - %s', $date_from, $date_to);
					}
				}

				if( !empty($date) ) {
					$params = Common::_callConvertDateRange($params, $date, array(
						'date_from' => $fieldFrom,
						'date_to' => $fieldTo,
					));
				}
			}
		}

		$dataSearch = Common::hashEmptyField($data, 'Search');
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
						$params[$fieldName] = rawurlencode(urlencode($value));
					} else {
						$params[$fieldName] = rawurlencode($value);
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
            case 'price':
                if( !in_array($document_type, array( 'eksternal', 'production' )) ) {
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

    public static function _callGeneratePatternCode ( $value, $modelName, $extra_text = '' ) {
        $pattern = Common::hashEmptyField($value, $modelName.'.pattern'.$extra_text);
        $last_number = Common::hashEmptyField($value, $modelName.'.last_number'.$extra_text);
        $min_digit = Common::hashEmptyField($value, $modelName.'.min_digit'.$extra_text);

        return __('%s%s', str_pad($last_number, $min_digit, '0', STR_PAD_LEFT), $pattern);
    }

    public static function getNoRef ( $id, $length = 5, $op = '0', $position = STR_PAD_LEFT ) {
        return str_pad($id, $length, $op, $position);
    }

    public static function _callPriceServiceType () {
        return array(
            'borongan' => __('Borongan'),
            'satuan' => __('Satuan'),
        );
    }

    public static function _callTransactionStatus ( $data, $modelName = false, $fieldName = 'transaction_status' ) {
        $transaction_status = Common::hashEmptyField($data, $modelName.'.'.$fieldName);
        $canceled_date = Common::hashEmptyField($data, $modelName.'.canceled_date');

        switch ($transaction_status) {
            case 'paid':
                $customStatus = __('Sudah Dibayar');
                break;

            case 'half_paid':
                $customStatus = __('Dibayar Sebagian');
                break;

            case 'void':
                $customStatus = __('Void');

                if(!empty($canceled_date)){
                    $canceled_date = Common::formatDate($canceled_date, 'd/m/Y', false);
                    $customStatus .= ' - '.$canceled_date;
                }
                break;

            case 'sold':
                $customStatus = __('Sold');
                break;

            case 'posting':
                $customStatus = __('Commit');
                break;

            case 'available':
                $customStatus = __('Available');
                break;

            case 'unposting':
                $customStatus = __('Draft');
                break;

            case 'completed':
                $customStatus = __('Complete');
                break;

            case 'finish':
                $customStatus = __('Finish');
                break;

            case 'out':
                $customStatus = __('Proses');
                break;

            case 'progress':
                $customStatus = __('Pending');
                break;

            case 'pending':
                $customStatus = __('Pending');
                break;

            case 'canceled':
                $customStatus = __('Batal');
                break;

            case 'revised':
                $customStatus = __('Direvisi');
                break;

            case 'rejected':
                $customStatus = __('Ditolak');
                break;

            case 'closed':
                $customStatus = __('Closed');
                break;

            case 'approved':
                $customStatus = __('Disetujui');
                break;

            case 'po':
                $customStatus = __('PO');
                break;

            case 'open':
                $customStatus = __('Open');
                break;
            
            default:
                $customStatus = __('Belum Dibayar');
                break;
        }

        return $customStatus;
    }

    public static function _callGetDriver ( $value ) {
        $driver = Common::hashEmptyField($value, 'Driver.driver_name');
        $driver = Common::hashEmptyField($value, 'DriverPengganti.driver_name', $driver);

        return $driver;
    }

    public static function _callUrlEncode ( $value, $encode = false ) {
    	$result = rawurlencode(urlencode($value));

    	if( !empty($encode) ) {
    		return urlencode($result);
    	} else {
    		return $result;
    	}
	}

	public static function _callTargetPercentage( $value, $target ){
		$margin = $value / $target;

		if( !empty($margin) ) {
			return round($margin * 100, 2);
		} else {
			return 0;
		}
	}

	public static function getNameFromNumber($num) {
	    $numeric = ($num - 1) % 26;
	    $letter = chr(65 + $numeric);
	    $num2 = intval(($num - 1) / 26);
	    if ($num2 > 0) {
	        return Common::getNameFromNumber($num2) . $letter;
	    } else {
	        return $letter;
	    }
	}

    public static function _callTtujPaid ( $value ) {
        $status_sj = Common::hashEmptyField($value, 'Ttuj.status_sj', 'none');
        $is_invoice = Common::hashEmptyField($value, 'Ttuj.is_invoice');
        $status = Common::hashEmptyField($value, 'Ttuj.status', true, array(
        	'isset' => true,
    	));
        $allowEdit = true;

        if( $status_sj != 'none' || !empty($is_invoice) || empty($status) ) {
            $allowEdit = false;
        }

        return $allowEdit;
    }

    public static function _callInsuranceStatus ( $data ) {
        $status = Common::hashEmptyField($data, 'Insurance.status');
        $start_date = Common::hashEmptyField($data, 'Insurance.start_date');
        $end_date = Common::hashEmptyField($data, 'Insurance.end_date');
        $customStatus = false;
        $customColor = false;
        $nowDate = date('Y-m-d');

    	if( $end_date < $nowDate ) {
            $customStatus = __('Expired');
    		$customColor = 'danger';
        } else if( !empty($status) ) {
            $customStatus = __('Aktif');
    		$customColor = 'success';
        } else if( empty($status) ) {
            $customStatus = __('Void');
        	$customColor = 'danger';
        } else {
            $customStatus = __('Aktif');
    		$customColor = 'success';
        }

        return array(
        	'status' => $customStatus,
        	'color' => $customColor,
    	);
    }

    public static function _callCheckCostCenter ( $data, $label, $modelName = null ) {
		$is_cost_center_readonly = Configure::read('__Site.config_cost_center_readonly');
		$modelName = !empty($modelName)?$modelName:$label;

		if( !empty($is_cost_center_readonly) ) {
    		$current = Configure::read('__Site.Branch.CogsSetting.CogsSetting.'.$label.'.cogs_id');
    		$data[$modelName]['cogs_id'] = $current;
		}

		return $data;
    }

    public static function _callGenerateCogs ( $values ) {
    	$data = array();

        if( !empty($values) ) {
            foreach ($values as $key => $value) {
                $id = Common::hashEmptyField($value, 'CogsSetting.id');
                $label = Common::hashEmptyField($value, 'CogsSetting.label');
                $cogs_id = Common::hashEmptyField($value, 'CogsSetting.cogs_id');

                $data['CogsSetting'][$label]['id'] = $id;
                $data['CogsSetting'][$label]['label'] = $label;
                $data['CogsSetting'][$label]['cogs_id'] = $cogs_id;
            }
        }

        return $data;
    }

    public static function _callStatusRevenue ( $value, $modelName = 'Revenue' ) {
        $result = false;
        $status = Common::hashEmptyField($value, $modelName.'.transaction_status');

        switch ($status) {
            case 'unposting':
                $result = __('Unposting');
                break;
            case 'posting':
                $result = __('Posting');
                break;
            case 'invoiced':
                $result = __('Invoiced');
                break;
            case 'half_invoiced':
                $result = __('Posting');
                break;
        }

        return $result;
    }

    public static function _callGetDataDriver ( $value ) {
        $driver = Common::hashEmptyField($value, 'Driver');
        $driver = Common::hashEmptyField($value, 'DriverPengganti', $driver);

        return $driver;
    }

    public static function _callRevDetailConditions ( $type, $result ) {
    	if( !empty($type) ) {
            switch ($type) {
                case 'angkut':
                    $result['RevenueDetail.tarif_angkutan_type NOT'] = array( 'kuli', 'asuransi' );
                    break;
                
                default:
                    $result['RevenueDetail.tarif_angkutan_type'] = $type;
                    break;
            }
        }

        return $result;
    }

    public static function _callER ( $out, $revenue ) {
    	$er = 0;

    	if( !empty($revenue) ) {
            $er = ($out / $revenue) * 100;

            if( $out > $revenue ) {
                $er = $er*(-1);
            }
        }

        return $er;
    }

    public static function _callTarifExtra ( $price, $qty, $tarif_extra, $tarif_extra_min_capacity, $tarif_extra_per_unit ) {
    	if( $tarif_extra_min_capacity != 0 ) {
            if( $qty > $tarif_extra_min_capacity ) {
                if( $tarif_extra_per_unit != 0 ) {
                    $sisa_muatan = $qty - $tarif_extra_min_capacity;
                    $tarif_extra = $tarif_extra * $sisa_muatan;
                }

                $price = $price + $tarif_extra;
            }
        }

        return $price;
    }
}
