<?php
App::uses('Sanitize', 'Utility');
class MkCommonComponent extends Component {
	var $components = array(
		'RequestHandler', 'Email', 'Session'
	); 

	function initialize(Controller $controller, $settings = array()) {
		$this->controller = $controller;
	}
	
	function setCustomFlash($message, $type = 'success', $params=array(), $ajaxMsg = false) {
		$flashType = $type;

		if( !$ajaxMsg ) {
			$this->Session->setFlash($message, $flashType, $params, $type);

			$this->controller->set('msg', $message);

			if( $type == 'success' ) {
				$status = 1;
			} else {
				$status = 0;
			}
			$this->controller->set('status', $status);
		}
	}

	function loggedIn() {
		$logged_in = false;
		if($this->controller->Auth->user()) {
			$logged_in = true;
		}
		return $logged_in;
	}

	function getDateSelectbox ( $tgl ) {
		$result = false;

		if( !empty($tgl['day']) && !empty($tgl['month']) && !empty($tgl['year']) ) {
			$result = sprintf('%s-%s-%s', $tgl['year'], $tgl['month'], $tgl['day']);
		}

		return $result;
	}

	function getFilePhoto ( $filePhoto ) {
		$result = $filePhoto;

		if( empty($filePhoto['name']) ) {
			$result = false;
		}

		return $result;
	}

    function getTtujTipeMotor ( $data ) {
        if( !empty($data['TtujTipeMotor']) ) {
            $tempTipeMotor = array();

            foreach ($data['TtujTipeMotor'] as $key => $tipeMotor) {
                $tempTipeMotor['TtujTipeMotor']['tipe_motor_id'][$key] = $tipeMotor['TtujTipeMotor']['tipe_motor_id'];
                $tempTipeMotor['TtujTipeMotor']['color_motor_id'][$key] = $tipeMotor['TtujTipeMotor']['color_motor_id'];
                $tempTipeMotor['TtujTipeMotor']['qty'][$key] = $tipeMotor['TtujTipeMotor']['qty'];
                $tempTipeMotor['TtujTipeMotor']['city_id'][$key] = $tipeMotor['TtujTipeMotor']['city_id'];
                $tempTipeMotor['TtujTipeMotor']['city'][$key] = !empty($tipeMotor['City']['name'])?$tipeMotor['City']['name']:false;
            }

            unset($data['TtujTipeMotor']);
            $data['TtujTipeMotor'] = $tempTipeMotor['TtujTipeMotor'];
        }

        return $data;
    }

    function getTtujPerlengkapan ( $data ) {
        if( !empty($data['TtujPerlengkapan']) ) {
            $tempPerlengkapan = array();

            foreach ($data['TtujPerlengkapan'] as $key => $dataPerlengkapan) {
                $tempPerlengkapan['TtujPerlengkapan'][$dataPerlengkapan['TtujPerlengkapan']['perlengkapan_id']] = $dataPerlengkapan['TtujPerlengkapan']['qty'];
            }

            unset($data['TtujPerlengkapan']);
            $data['TtujPerlengkapan'] = $tempPerlengkapan['TtujPerlengkapan'];
        }

        return $data;
    }

    function getDate ( $date, $reverse = false ) {
    	$dtString = false;
        $date = trim($date);

    	if( !empty($date) ) {
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

    function customDate($dateString, $format = 'd F Y', $result = '') {
        if( !empty($dateString) && $dateString != '0000-00-00' && $dateString != '0000-00-00 00:00:00' ) {
            $result = date($format, strtotime($dateString));
        }

        return $result;
    }

    function convertPriceToString ( $price, $result = '' ) {
    	if( !empty($price) ) {
    		$resultTmp = str_replace(array(',', ' '), array('', ''), trim($price));

    		if( !empty($resultTmp) ) {
    			$result = $resultTmp;
    		}
    	}

    	return $result;
    }

    function getUangJalanGroupMotor ( $data ) {
        if( !empty($data['UangJalanTipeMotor']) ) {
            $tempGroupMotor = array();

            foreach ($data['UangJalanTipeMotor'] as $key => $groupMotor) {
                $tempGroupMotor['UangJalanTipeMotor']['group_motor_id'][$key] = $groupMotor['UangJalanTipeMotor'] ['group_motor_id'];
                $tempGroupMotor['UangJalanTipeMotor']['uang_jalan_1'][$key] = $groupMotor['UangJalanTipeMotor'] ['uang_jalan_1'];
            }

            unset($data['UangJalanTipeMotor']);
            $data['UangJalanTipeMotor'] = $tempGroupMotor['UangJalanTipeMotor'];
        }

        if( !empty($data['CommissionGroupMotor']) ) {
            $tempGroupMotor = array();

            foreach ($data['CommissionGroupMotor'] as $key => $groupMotor) {
                $tempGroupMotor['CommissionGroupMotor']['group_motor_id'][$key] = $groupMotor['CommissionGroupMotor']['group_motor_id'];
                $tempGroupMotor['CommissionGroupMotor']['commission'][$key] = $groupMotor['CommissionGroupMotor']['commission'];
            }

            unset($data['CommissionGroupMotor']);
            $data['CommissionGroupMotor'] = $tempGroupMotor['CommissionGroupMotor'];
        }

        if( !empty($data['AsdpGroupMotor']) ) {
            $tempGroupMotor = array();

            foreach ($data['AsdpGroupMotor'] as $key => $groupMotor) {
                $tempGroupMotor['AsdpGroupMotor']['group_motor_id'][$key] = $groupMotor['AsdpGroupMotor']['group_motor_id'];
                $tempGroupMotor['AsdpGroupMotor']['asdp'][$key] = $groupMotor['AsdpGroupMotor']['asdp'];
            }

            unset($data['AsdpGroupMotor']);
            $data['AsdpGroupMotor'] = $tempGroupMotor['AsdpGroupMotor'];
        }

        if( !empty($data['UangKawalGroupMotor']) ) {
            $tempGroupMotor = array();

            foreach ($data['UangKawalGroupMotor'] as $key => $groupMotor) {
                $tempGroupMotor['UangKawalGroupMotor']['group_motor_id'][$key] = $groupMotor['UangKawalGroupMotor']['group_motor_id'];
                $tempGroupMotor['UangKawalGroupMotor']['uang_kawal'][$key] = $groupMotor['UangKawalGroupMotor']['uang_kawal'];
            }

            unset($data['UangKawalGroupMotor']);
            $data['UangKawalGroupMotor'] = $tempGroupMotor['UangKawalGroupMotor'];
        }

        if( !empty($data['UangKeamananGroupMotor']) ) {
            $tempGroupMotor = array();

            foreach ($data['UangKeamananGroupMotor'] as $key => $groupMotor) {
                $tempGroupMotor['UangKeamananGroupMotor']['group_motor_id'][$key] = $groupMotor['UangKeamananGroupMotor']['group_motor_id'];
                $tempGroupMotor['UangKeamananGroupMotor']['uang_keamanan'][$key] = $groupMotor['UangKeamananGroupMotor']['uang_keamanan'];
            }

            unset($data['UangKeamananGroupMotor']);
            $data['UangKeamananGroupMotor'] = $tempGroupMotor['UangKeamananGroupMotor'];
        }

        return $data;
    }

    function getUangKuliGroupMotor ( $data ) {
        if( !empty($data['UangKuliGroupMotor']) ) {
            $tempGroupMotor = array();

            foreach ($data['UangKuliGroupMotor'] as $key => $groupMotor) {
                $tempGroupMotor['UangKuliGroupMotor']['group_motor_id'][$key] = $groupMotor['group_motor_id'];
                $tempGroupMotor['UangKuliGroupMotor']['uang_kuli'][$key] = $groupMotor['uang_kuli'];
            }

            unset($data['UangKuliGroupMotor']);
            $data['UangKuliGroupMotor'] = $tempGroupMotor['UangKuliGroupMotor'];
        }

        return $data;
    }

    function getUangKuliCapacity ( $data ) {
        if( !empty($data['UangKuliCapacity']) ) {
            $tempCapacity = array();

            foreach ($data['UangKuliCapacity'] as $key => $capacity) {
                $tempCapacity['UangKuliCapacity']['capacity'][$key] = $capacity['capacity'];
                $tempCapacity['UangKuliCapacity']['uang_kuli'][$key] = $capacity['uang_kuli'];
            }

            unset($data['UangKuliCapacity']);
            $data['UangKuliCapacity'] = $tempCapacity['UangKuliCapacity'];
        }

        return $data;
    }

    function toSlug($string, $separator = '-') {
        return strtolower(Inflector::slug($string, $separator));
    }

    function deletePathPhoto( $pathfolder = false, $filename = false, $dimensions = false, $deleteUploadFile = true, $project_path = false ) {
        if( !empty($filename) ) {
            if( !empty($project_path) ) {
                $project_path = DS.$project_path;
            } else {
                $project_path = '';
            }

            $path = Configure::read('__Site.thumbnail_view_path').DS.$pathfolder;
            $pathUpload = Configure::read('__Site.upload_path').DS.$pathfolder.$project_path.$filename;
            $pathUpload = str_replace('/', DS, $pathUpload);

            if( $deleteUploadFile && file_exists($pathUpload) ) {
                unlink($pathUpload);
            }

            if( !$dimensions ) {
                $dimensions = array();
                if( $pathfolder == 'users' ) {
                    $dimensions = Configure::read('__Site.dimension_profile');
                }
                $dimensions = array_merge($dimensions, Configure::read('__Site.dimension'));
            }

            foreach ($dimensions as $key => $dimension) {
                $urlPhoto = $path.DS.$key.$project_path.$filename;
                $this->deletePhoto($urlPhoto);
            }

            $urlPhoto = $path.DS.Configure::read('__Site.fullsize').$project_path.$filename;
            $this->deletePhoto($urlPhoto);
        }
    }

    /**
    *
    *   menghapus foto yan ada
    *
    *   @param string $urlPhoto - path sampai ke file tujuan
    */
    function deletePhoto ( $urlPhoto ) {
        $urlPhoto = str_replace('/', DS, $urlPhoto);

        if(file_exists($urlPhoto)) {
            chown($urlPhoto,465);
            unlink($urlPhoto);
        }
    }

    function generateDateTTUJ ( $data_local ) {
        if( !empty($data_local['Ttuj']['tgljam_berangkat']) && $data_local['Ttuj']['tgljam_berangkat'] != '0000-00-00 00:00:00' ) {
            $data_local['Ttuj']['tgl_berangkat'] = date('d/m/Y', strtotime($data_local['Ttuj']['tgljam_berangkat']));
            $data_local['Ttuj']['jam_berangkat'] = date('H:i', strtotime($data_local['Ttuj']['tgljam_berangkat']));
        }
        if( !empty($data_local['Ttuj']['tgljam_tiba']) && $data_local['Ttuj']['tgljam_tiba'] != '0000-00-00 00:00:00' ) {
            $data_local['Ttuj']['tgl_tiba'] = date('d/m/Y', strtotime($data_local['Ttuj']['tgljam_tiba']));
            $data_local['Ttuj']['jam_tiba'] = date('H:i', strtotime($data_local['Ttuj']['tgljam_tiba']));
        }
        if( !empty($data_local['Ttuj']['tgljam_bongkaran']) && $data_local['Ttuj']['tgljam_bongkaran'] != '0000-00-00 00:00:00' ) {
            $data_local['Ttuj']['tgl_bongkaran'] = date('d/m/Y', strtotime($data_local['Ttuj']['tgljam_bongkaran']));
            $data_local['Ttuj']['jam_bongkaran'] = date('H:i', strtotime($data_local['Ttuj']['tgljam_bongkaran']));
        }
        if( !empty($data_local['Ttuj']['tgljam_balik']) && $data_local['Ttuj']['tgljam_balik'] != '0000-00-00 00:00:00' ) {
            $data_local['Ttuj']['tgl_balik'] = date('d/m/Y', strtotime($data_local['Ttuj']['tgljam_balik']));
            $data_local['Ttuj']['jam_balik'] = date('H:i', strtotime($data_local['Ttuj']['tgljam_balik']));
        }
        if( !empty($data_local['Ttuj']['tgljam_pool']) && $data_local['Ttuj']['tgljam_pool'] != '0000-00-00 00:00:00' ) {
            $data_local['Ttuj']['tgl_pool'] = date('d/m/Y', strtotime($data_local['Ttuj']['tgljam_pool']));
            $data_local['Ttuj']['jam_pool'] = date('H:i', strtotime($data_local['Ttuj']['tgljam_pool']));
        }

        return $data_local;
    }

    function getNoInvoice ( $customer ) {
        if( !empty($customer['CustomerGroup']['CustomerGroupPattern']) ) {
            return sprintf('%s%s', str_pad($customer['CustomerGroup']['CustomerGroupPattern']['last_number'], $customer['CustomerGroup']['CustomerGroupPattern']['min_digit'], '0', STR_PAD_LEFT), $customer['CustomerGroup']['CustomerGroupPattern']['pattern']);
        } else if( !empty($customer['CustomerGroupPattern']) ) {
            return sprintf('%s%s', str_pad($customer['CustomerGroupPattern']['last_number'], $customer['CustomerGroupPattern']['min_digit'], '0', STR_PAD_LEFT), $customer['CustomerGroupPattern']['pattern']);
        } else {
            return '';
        }
    }

    function replaceSlash ( $string ) {
        return str_replace('%2F', '/', $string);
    }

    function filterEmptyField ( $value, $modelName, $fieldName, $empty = false ) {
        return !empty($value[$modelName][$fieldName])?$value[$modelName][$fieldName]:$empty;
    }

    function getMimeType( $filename ) {
        $mime_types = array(
            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

        $ext1 = explode('.',$filename);
        $ext2 = strtolower(end($ext1));
        $ext3 = end($ext1);
        if (array_key_exists($ext2, $mime_types)) {
            return $mime_types[$ext2];
        }
        elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
        }
        else {
            return 'application/octet-stream';
        }
    }

    function addToFiles($key, $url) {
        $tempName = tempnam('C:/tmps', 'php_files');
        $originalName = basename(parse_url($url, PHP_URL_PATH));

        $imgRawData = file_get_contents($url);
        file_put_contents($tempName, $imgRawData);

        $_FILES[$key] = array(
            'name' => $originalName,
            'type' => $this->getMimeType($originalName),
            'tmp_name' => $tempName,
            'error' => 0,
            'size' => strlen($imgRawData),
        );
        return $_FILES;
    }

    function _import_excel ( $data ) {
        $Zipped = $data['Import']['importdata'];
        $targetdir = false;

        if($Zipped["name"]) {
            $filename = $Zipped["name"];
            $source = $Zipped["tmp_name"];
            $type = $Zipped["type"];
            $name = explode(".", $filename);
            $accepted_types = array('application/vnd.ms-excel', 'application/ms-excel');

            if(!empty($accepted_types)) {
                foreach($accepted_types as $mime_type) {
                    if($mime_type == $type) {
                        $okay = true;
                        break;
                    }
                }
            }

            $continue = strtolower($name[1]) == 'xls' ? true : false;

            if(!$continue) {
                $this->MkCommon->setCustomFlash(__('Maaf, silahkan upload file dalam bentuk Excel.'), 'error');
                $this->redirect(array('action'=>'import'));
            } else {
                $path = APP.'webroot'.DS.'files'.DS;
                $filenoext = basename ($filename, '.xls');
                $filenoext = basename ($filenoext, '.XLS');
                $fileunique = uniqid() . '_' . $filenoext;

                $targetdir = $path . $fileunique . $filename;
                 
                ini_set('memory_limit', '96M');
                ini_set('post_max_size', '64M');
                ini_set('upload_max_filesize', '64M');

                if(!move_uploaded_file($source, $targetdir)) {
                    $targetdir = false;
                }
            }
        }

        return $targetdir;
    }

    function getChargeTotal ( $total, $tarif_per_truck, $jenis_tarif, $is_charge ) {
        $totalResult = 0;
        $additionalCharge = 0;

        if( $jenis_tarif == 'per_truck' ) {
            if( !empty($is_charge) ) {
                $totalResult = $tarif_per_truck;
                $additionalCharge = $tarif_per_truck;
            }
        } else {
            $totalResult = $total;
        }

        return array(
            'total_tarif' => $totalResult,
            'additional_charge' => $additionalCharge,
        );
    }

    /**
    *
    *   mengkombinasikan tanggal
    *
    *   @param string $startDate : tanggal awal
    *   @param string $endDate : tanggal akhir
    *   @return string
    */
    function getCombineDate ( $startDate, $endDate, $format = 'long' ) {
        $startDate = strtotime($startDate);
        $endDate = strtotime($endDate);

        if( !empty($startDate) && !empty($endDate) ) {
            switch ($format) {
                case 'short':
                    if( $startDate == $endDate ) {
                        $customDate = date('M Y', $startDate);
                    } else if( date('Y', $startDate) == date('Y', $endDate) ) {
                        $customDate = sprintf('%s - %s', date('M', $startDate), date('M Y', $endDate));
                    } else {
                        $customDate = sprintf('%s - %s', date('M Y', $startDate), date('M Y', $endDate));
                    }
                    break;
                
                default:
                    if( $startDate == $endDate ) {
                        $customDate = date('d M Y', $startDate);
                    } else if( date('M Y', $startDate) == date('M Y', $endDate) ) {
                        $customDate = sprintf('%s - %s', date('d', $startDate), date('d M Y', $endDate));
                    } else if( date('Y', $startDate) == date('Y', $endDate) ) {
                        $customDate = sprintf('%s - %s', date('d M', $startDate), date('d M Y', $endDate));
                    } else {
                        $customDate = sprintf('%s - %s', date('d M Y', $startDate), date('d M Y', $endDate));
                    }
                    break;
            }
            return $customDate;
        }
        return false;
    }

    function checkArrayApproval ( $arrKey, $arrayFind ) {
        if( count($arrKey) < count($arrayFind) ) {
            return false;
        } else if( !empty($arrKey) ) {
            foreach ($arrKey as $key => $value) {
                if( !in_array($value, $arrayFind) ) {
                    return false;
                }
            }
        } else {
            return false;
        }

        return true;
    }

    function configureDefaultApp () {
        Configure::write('__Site.profile_photo_folder', 'users');
        Configure::write('__Site.laka_photo_folder', 'lakas');
        Configure::write('__Site.truck_photo_folder', 'trucks');

        Configure::write('__Site.config_currency_code', 'IDR ');
        Configure::write('__Site.config_currency_second_code', 'Rp ');
        Configure::write('__Site.config_pagination', 20);
        Configure::write('__Site.config_pagination_unlimited', 1000);
        Configure::write('__Site.cache_view_path', '/images/view');
        Configure::write('__Site.upload_path', APP.'Uploads');

        Configure::write('__Site.fullsize', 'fullsize');
        Configure::write('__Site.max_image_size', 5241090);
        Configure::write('__Site.max_image_width', 1000);
        Configure::write('__Site.max_image_height', 667);
        Configure::write('__Site.allowed_ext', array('jpg', 'jpeg', 'png', 'gif'));
        Configure::write('__Site.type_lku', array('lku' => 'LKU', 'ksu' => 'KSU'));

        Configure::write('__Site.thumbnail_view_path', APP.'webroot'.DS.'images'.DS.'view');

        $dimensionProfile = array(
            'ps' => '50x50',
            'pm' => '100x100',
            'pl' => '150x150',
            'pxl' => '300x300',
        );
        Configure::write('__Site.dimension_profile', $dimensionProfile);

        $dimensionArr = array(
            's' => '150x84',
            'xsm' => '100x40',
            'xm' => '165x165',
            'xxsm' => '240x96',
            'm' => '300x169',
            'l' => '855x481',
        );
        Configure::write('__Site.dimension', $dimensionArr);
    }

    function allowPage ( $branchs, $no_exact = false ) {
        $result = true;
        $resultExact = false;

        if( !is_array($branchs) ) {
            $branchs = array( $branchs );
        }

        if( is_array($branchs) ) {
            $moduleAllow = Configure::read('__Site.config_allow_module');
            $branchAllow = Configure::read('__Site.Data.Branch.id');

            $branchs = array_values($branchs);
            $controllerName = !empty($this->controller->params['controller'])?$this->controller->params['controller']:false;
            $actionName = $this->controller->action;
            $allowBranch = array_intersect($branchs, $branchAllow);

            if( !empty($allowBranch) && !empty($branchs) ) {
                foreach ($branchs as $key => $branch_id) {
                    if( !empty($moduleAllow[$branch_id]) ) {
                        if( !empty($moduleAllow[$branch_id][$controllerName]['action']) ) {
                            if( !in_array($actionName, $moduleAllow[$branch_id][$controllerName]['action']) ) {
                                $result = false;
                            } else {
                                $resultExact = true;
                            }
                        } else {
                            $result = false;
                        }
                    } else {
                        $result = false;
                    }
                }
            } else {
                $result = false;
            }
        } else {
            $result = false;
        }

        if( !empty($no_exact) ) {
            if( empty($resultExact) ) {
                $this->controller->redirect('/');
            } else {
                return true;
            }
        } else if( empty($result) ) {
            $this->controller->redirect('/');
        } else {
            return true;
        }
    }

    function allowBranch ( $branchs, $controllerName = false, $actionName = false, $key = false ) {
        $result = true;

        if( !is_array($branchs) ) {
            $branchs = array( $branchs );
        }

        if( is_array($branchs) ) {
            $moduleAllow = Configure::read('__Site.config_allow_module');
            $branchAllow = Configure::read('__Site.Data.Branch.id');
            // $branchAllow = array_keys($branchAllow);

            if( empty($controllerName) ) {
                $controllerName = $this->controller->params['controller'];
            }
            if( empty($actionName) ) {
                $actionName = $this->controller->action;
            }

            if( !empty($branchs) ) {
                foreach ($branchs as $branch_id => $branch_name) {
                    if( in_array($branch_id, $branchAllow) ) {
                        if( !empty($moduleAllow[$branch_id]) ) {
                            if( !empty($moduleAllow[$branch_id][$controllerName]['action']) ) {
                                if( !in_array($actionName, $moduleAllow[$branch_id][$controllerName]['action']) ) {
                                    unset($branchs[$branch_id]);
                                }
                            } else {
                                unset($branchs[$branch_id]);
                            }
                        } else {
                            unset($branchs[$branch_id]);
                        }
                    } else {
                        unset($branchs[$branch_id]);
                    }
                }
            } else {
                $branchs = false;
            }
        } else {
            $branchs = false;
        }

        if( !empty($key) ) {
            $branchs = array_keys($branchs);
        }

        return $branchs;
    }
    
    // function getRefineGroupBranch ( $data, $refine ) {
    //     if(!empty($refine)) {
    //         if( !empty($refine['GroupBranch']['group_branch']) ) {
    //             if( is_array($refine['GroupBranch']['group_branch']) ) {
    //                 $group_branch = $refine['GroupBranch']['group_branch'];
    //                 $group_branch = array_filter($group_branch);
    //                 $group_branch = array_unique($group_branch);
    //                 $group_branch = implode(',', $group_branch);
    //             } else {
    //                 $group_branch = $refine['GroupBranch']['group_branch'];
    //             }

    //             $refine_conditions['GroupBranch']['group_branch'] = $group_branch;
    //         }
    //     }

    //     if(isset($refine_conditions['GroupBranch']) && !empty($refine_conditions['GroupBranch'])) {
    //         foreach($refine_conditions['GroupBranch'] as $param => $value) {
    //             if($value) {
    //                 $data[trim($param)] = rawurlencode($value);
    //             }
    //         }
    //     }

    //     return $data;
    // }


    
    // function getConditionGroupBranch ( $refine, $modelName, $options = false, $type = 'options' ) {
    //     if(!empty($refine['group_branch'])){
    //         if( !is_array($refine['group_branch']) ) {
    //             $value = urldecode($refine['group_branch']);
    //             $value = explode(',', $value);
    //             $value = array_combine(array_keys(array_flip($value)), $value);
    //         } else {
    //             $value = $refine['group_branch'];
    //             $value = array_filter($value);
    //         }

    //         $this->controller->request->data['GroupBranch']['group_branch'] = $value;
    //         $fieldName = sprintf('%s.branch_id', $modelName);
    //         $this->allowPage( $value );

    //         if( $type == 'options' ) {
    //             $options['conditions'][$fieldName] = $value;
    //         } else {
    //             $options[$fieldName] = $value;
    //         }
    //     }

    //     return $options;
    // }

    function redirectReferer ( $msg, $status = 'error', $urlRedirect = false ) {
        $this->setCustomFlash($msg, $status);

        if( !empty($urlRedirect) ) {
            $this->controller->redirect($urlRedirect);
        } else {
            $this->controller->redirect($this->controller->referer());
        }
    }

    function setProcessParams ( $data, $urlRedirect = false ) {
        if ( !empty($data['msg']) && !empty($data['status']) ) {
            if ( $data['status'] == 'success' ) {
                $this->redirectReferer($data['msg'], $data['status'], $urlRedirect);
            } else {
                $this->setCustomFlash($data['msg'], $data['status']);
            }
        } else if ( !empty( $data['data'] ) ) {
            $this->controller->request->data = $data['data'];
        }
    }

    function _layout_file ( $type ) {
        switch ($type) {
            case 'select':
                $layout_js = array(
                    'select2.full',
                );
                $layout_css = array(
                    'select2.min',
                );
                break;
        }

        $this->controller->set(compact(
            'layout_js', 'layout_css'
        ));
    }
}
?>