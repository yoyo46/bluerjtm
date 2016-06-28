<?php
App::uses('Sanitize', 'Utility');
class MkCommonComponent extends Component {
	var $components = array(
		'RequestHandler', 'Email', 'Session'
	); 

	function initialize(Controller $controller, $settings = array()) {
		$this->controller = $controller;
	}
	
	function setCustomFlash($message, $type = 'success', $params=array(), $flash = true) {
        if( $flash ){
            $this->Session->setFlash($message, $type, $params, $type);
        }

        if( $type == 'success' ) {
            $status = 1;
        } else if( $type == 'error_login' ) {
            $status = -1;
        } else {
            $status = 0;
        }

        $this->controller->set('msg', $message);
        $this->controller->set('status', $status);
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

    function customDate($dateString, $format = 'd F Y', $result = '') {
        if( !empty($dateString) && $dateString != '0000-00-00' && $dateString != '0000-00-00 00:00:00' ) {
            $result = date($format, strtotime($dateString));
        }

        return $result;
    }

    function convertPriceToString ( $price, $result = '' ) {
    	if( !empty($price) ) {
    		$resultTmp = str_replace(array(',', ' ', '*'), array('', '', ''), trim($price));

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

    function unSlug($string) {
        return str_replace(array( '_', '-' ), array( ' ', ' ' ), $string);
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

    function filterEmptyField ( $value, $modelName, $fieldName = false, $empty = false, $options = false ) {
        $type = !empty($options['type'])?$options['type']:'empty';
        $result = $empty;

        switch ($type) {
            case 'isset':
                if( empty($fieldName) && isset($value[$modelName]) ) {
                    $result = $value[$modelName];
                } else {
                    $result = isset($value[$modelName][$fieldName])?$value[$modelName][$fieldName]:$empty;
                }
                break;
            
            default:
                if( empty($fieldName) && !empty($value[$modelName]) ) {
                    $result = $value[$modelName];
                } else {
                    $result = !empty($value[$modelName][$fieldName])?$value[$modelName][$fieldName]:$empty;
                }
                break;
        }

        if( !empty($options['date']) ) {
            $format = $options['date'];
            $result = $this->customDate($result, $format);
        }

        return $result;
    }

    function filterIssetField ( $value, $modelName, $fieldName = false, $empty = false ) {
        $result = '';
        
        if( empty($modelName) && !is_numeric($modelName) ) {
            $result = isset($value)?$value:$empty;
        } else if( empty($fieldName) && !is_numeric($fieldName) ) {
            $result = isset($value[$modelName])?$value[$modelName]:$empty;
        } else {
            $result = isset($value[$modelName][$fieldName])?$value[$modelName][$fieldName]:$empty;
        }

        return $result;
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
                $this->setCustomFlash(__('Maaf, silahkan upload file dalam bentuk Excel.'), 'error');
                $this->redirect(array('action'=>'import'));
            } else {
                $path = APP.'webroot'.DS.'files'.DS.date('Y').DS.date('m').DS;
                $filenoext = basename ($filename, '.xls');
                $filenoext = basename ($filenoext, '.XLS');
                $fileunique = uniqid() . '_' . $filenoext;

                if( !file_exists($path) ) {
                    mkdir($path, 0755, true);
                }

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

        if( $jenis_tarif == 'per_truck' ) {
            if( !empty($is_charge) ) {
                $totalResult = $tarif_per_truck;
            }
        } else {
            $totalResult = $total;
        }

        return $totalResult;
    }

    /**
    *
    *   mengkombinasikan tanggal
    *
    *   @param string $startDate : tanggal awal
    *   @param string $endDate : tanggal akhir
    *   @return string
    */
    function getCombineDate ( $startDate, $endDate, $format = 'long', $separator = '-' ) {
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
        Configure::write('__Site.general_photo_folder', 'generals');

        Configure::write('__Site.config_currency_code', 'IDR ');
        Configure::write('__Site.config_currency_second_code', 'Rp ');
        Configure::write('__Site.config_pagination', 20);
        Configure::write('__Site.config_pagination_unlimited', 10000000);
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

        Configure::write('__Site.Journal.Documents', array(
            'in' => __('Penerimaan Kas Masuk'),
            'out' => __('Pembayaran Kas Keluar'),
            'ppn_out' => __('Pembayaran PPN Keluar'),
            'ppn_in' => __('Penerimaan PPN Masuk'),
            'prepayment_out' => __('Pembayaran Prepayment'),
            'prepayment_in' => __('Penerimaan Prepayment'),
        ));

        Configure::write('__Site.Demo.Version', $this->_callDemoVersion());

        if( $this->params['prefix'] == 'bypass' && $this->RequestHandler->isAjax() ) {
            $this->layout = 'ajax';
        }
    }

    function allowPage ( $branchs, $no_exact = false ) {
        $result = true;
        $resultExact = false;
        $group_id = Configure::read('__Site.config_group_id');

        if( $group_id != 1 ) {
            if( !is_array($branchs) ) {
                $branchs = array( $branchs );
            }

            if( is_array($branchs) ) {
                $moduleAllow = Configure::read('__Site.config_allow_module');
                $branchAllow = Configure::read('__Site.Data.Branch.id');

                $branchs = array_values($branchs);
                $controllerName = !empty($this->controller->params['controller'])?$this->controller->params['controller']:false;
                $actionName = $this->controller->action;
                $extendName = !empty($this->controller->params['pass'][0])?$this->controller->params['pass'][0]:false;
                $allowBranch = array_intersect($branchs, $branchAllow);

                if( !empty($allowBranch) && !empty($branchs) ) {
                    foreach ($branchs as $key => $branch_id) {
                        if( !empty($moduleAllow[$branch_id]) ) {
                            if( !empty($moduleAllow[$branch_id][$controllerName]['action']) ) {
                                if( !in_array($actionName, $moduleAllow[$branch_id][$controllerName]['action']) ) {
                                    $result = false;
                                } else if( !empty($moduleAllow[$branch_id][$controllerName]['extends'][$actionName]) ) {
                                    if( !in_array($extendName, $moduleAllow[$branch_id][$controllerName]['extends'][$actionName]) ) {
                                        $result = false;
                                    } else {
                                        $resultExact = true;
                                    }
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
    
    function getRefineGroupBranch ( $data, $refine ) {
        if(!empty($refine)) {
            if( !empty($refine['GroupBranch']['group_branch']) ) {
                if( is_array($refine['GroupBranch']['group_branch']) ) {
                    $group_branch = $refine['GroupBranch']['group_branch'];
                    $group_branch = array_filter($group_branch);
                    $group_branch = array_unique($group_branch);
                    $group_branch = implode(',', $group_branch);
                } else {
                    $group_branch = $refine['GroupBranch']['group_branch'];
                }

                $refine_conditions['GroupBranch']['group_branch'] = $group_branch;
            }
        }

        if(isset($refine_conditions['GroupBranch']) && !empty($refine_conditions['GroupBranch'])) {
            foreach($refine_conditions['GroupBranch'] as $param => $value) {
                if($value) {
                    $data[trim($param)] = rawurlencode($value);
                }
            }
        }

        return $data;
    }
    
    function getConditionGroupBranch ( $refine, $modelName, $options = false, $type = 'options' ) {
        if( !empty($refine['named']) ) {
            $refine = $refine['named'];
        }

        if(!empty($refine['group_branch'])){
            if( !is_array($refine['group_branch']) ) {
                $value = urldecode($refine['group_branch']);
                $value = explode(',', $value);
                $value = array_combine(array_keys(array_flip($value)), $value);
            } else {
                $value = $refine['group_branch'];
                $value = array_filter($value);
            }

            $this->controller->request->data['GroupBranch']['group_branch'] = $value;
            $fieldName = sprintf('%s.branch_id', $modelName);
            $this->allowPage( $value );

            if( $type == 'options' ) {
                $options['conditions'][$fieldName] = $value;
            } else {
                $options[$fieldName] = $value;
            }
        }

        return $options;
    }
    
    function getBranchNameFilter ( $refine ) {
        $data = array();

        if(!empty($refine['group_branch'])){
            if( !is_array($refine['group_branch']) ) {
                $value = urldecode($refine['group_branch']);
                $value = explode(',', $value);
                $value = array_combine(array_keys(array_flip($value)), $value);
            } else {
                $value = $refine['group_branch'];
                $value = array_filter($value);
            }

            $this->Branch = ClassRegistry::init('Branch'); 
            $data = $this->Branch->getData('list', array(
                'conditions' => array(
                    'Branch.id' => $value,
                ),
            ));
        }

        return $data;
    }

    function redirectReferer ( $msg, $status = 'error', $urlRedirect = false, $options = array() ) {
        $flash = $this->filterEmptyField($options, 'flash', false, true, array(
            'type' => 'isset',
        ));
        $paramFlash = $this->filterEmptyField($options, 'paramFlash', false, array());
        $ajaxRedirect = $this->filterEmptyField($options, 'ajaxRedirect');

        $this->setCustomFlash($msg, $status, $paramFlash, $flash);

        if( !$this->RequestHandler->isAjax() || !empty($ajaxRedirect) ) {
            if( !empty($urlRedirect) ) {
                $this->controller->redirect($urlRedirect);
            } else {
                $this->controller->redirect($this->controller->referer());
            }
        }
    }

    function _callProcessLog ( $data ) {
        if ( !empty( $data['Log'] ) ) {
            $this->_saveLog( $data['Log'] );
        }
    }

    function _callProcessNotification ( $data ) {
        if ( !empty( $data['Notification'] ) ) {
            $title = $this->filterEmptyField($data, 'Notification', 'title');
            $user_id = $this->filterEmptyField($data, 'Notification', 'user_id');
            $document_id = $this->filterEmptyField($data, 'Notification', 'document_id');
            $url = $this->filterEmptyField($data, 'Notification', 'url');

            $this->_saveNotification( $title, $user_id, $document_id, $url );
        }
    }

    function setProcessParams ( $data, $urlRedirect = false, $options = array() ) {
        $redirectError = $this->filterEmptyField($options, 'redirectError');
        $noRedirect = $this->filterEmptyField($options, 'noRedirect');
        $ajaxFlash = $this->filterEmptyField($options, 'ajaxFlash');
        $flash = isset($options['flash'])?$options['flash']:true;
        $ajaxRedirect = $this->filterEmptyField($options, 'ajaxRedirect');
        $paramFlash = $this->filterEmptyField($options, 'paramFlash', false, array());

        $this->_callSaveNotification($data);

        if( $this->RequestHandler->isAjax() && !$ajaxFlash ) {
            $flash = false;
        }

        if ( !empty($data['msg']) && !empty($data['status']) ) {
            if ( !empty( $data['Log'] ) ) {
                $this->_saveLog( $data['Log'] );
            }

            if ( !empty( $data['RefreshAuth'] ) ) {
                $user_id = $this->filterEmptyField($data, 'RefreshAuth', 'id');
                $this->RmUser->refreshAuth($user_id);
            }

            if ( ( $data['status'] == 'success' || !empty($redirectError) ) && !$noRedirect ) {
                $this->redirectReferer($data['msg'], $data['status'], $urlRedirect, array(
                    'flash' => $flash,
                    'ajaxRedirect' => $ajaxRedirect,
                ));
            } else {
                if( !empty($data['validationErrors']) ) {
                    $msg = $this->_callMsgValidationErrors($data['validationErrors']);

                    if( !empty($msg) ) {
                        $msg = implode('</li><li>', $msg);
                        $msg = '<ul><li>'.$msg.'</li></ul>';
                    } else {
                        $msg = $data['msg'];
                    }
                } else {
                    $msg = $data['msg'];
                }
                $this->setCustomFlash($msg, $data['status'], $paramFlash, $flash);
            }
        }

        if ( !empty( $data['data'] ) ) {
            $this->controller->request->data = $data['data'];
        }
    }

    function setProcessSave ( $data, $options = array() ) {
        $this->_saveNotification($data);

        if ( !empty($data['msg']) && !empty($data['status']) ) {
            if ( !empty( $data['Log'] ) ) {
                $this->_saveLog( $data['Log'] );
            }

            if ( !empty( $data['RefreshAuth'] ) ) {
                $user_id = $this->filterEmptyField($data, 'RefreshAuth', 'id');
                $this->RmUser->refreshAuth($user_id);
            }
        }

        if ( !empty( $data['data'] ) ) {
            $this->controller->request->data = $data['data'];
        }
    }

    function _layout_file ( $type ) {
        $layout_js = array();
        $layout_css = array();
        $contents = array();

        if( !is_array($type) ) {
            $contents[] = $type;
        } else {
            $contents = $type;
        }

        if( !empty($contents) ) {
            foreach ($contents as $key => $type) {
                switch ($type) {
                    case 'select':
                        $layout_js = array_merge($layout_js, array(
                            'select2.full',
                        ));
                        $layout_css = array_merge($layout_css, array(
                            'select2.min',
                        ));
                        break;
                    case 'freeze':
                        $layout_js = array_merge($layout_js, array(
                            'freeze',
                        ));
                        $layout_css = array_merge($layout_css, array(
                            'freeze',
                        ));
                        break;
                    case 'progressbar':
                        $layout_js = array_merge($layout_js, array(
                            'jquery.progresstimer',
                        ));
                        break;
                    case 'typeahead':
                        $layout_js = array_merge($layout_js, array(
                            'functions/bootstrap-typeahead',
                        ));
                        break;
                }
            }
        }
        
        $this->controller->set(compact(
            'layout_js', 'layout_css'
        ));
    }

    function unsetArr( $data, $unsetData ) {
        foreach ($unsetData as $key => $value) {
            if( is_array($value) ) {
                foreach ($value as $key2 => $value2) {
                    if( isset($data[$key][$value2]) ) {
                        unset($data[$key][$value2]);
                    }
                }
            } else {
                unset($data[$value]);
            }
        }

        return $data;
    }

    function _callConditionPlant ( $conditions, $fieldName ) {
        $current_branch_id = Configure::read('__Site.config_branch_id');
        $current_branch_plant = Configure::read('__Site.config_branch_plant');
        $branch_plant_id = Configure::read('__Site.Branch.Plant.id');

        if( !empty($current_branch_plant) ) {
            $conditions[$fieldName.'.branch_id'] = $branch_plant_id;
        } else {
            $conditions[$fieldName.'.branch_id'] = $current_branch_id;
        }

        return $conditions;
    }

    function getLogs( $paramController, $paramAction, $id ) {
        $this->Log = ClassRegistry::init('Log'); 

        $this->controller->paginate = $this->Log->getLogs( $paramController, $paramAction, $id );
        $logs = $this->controller->paginate('Log');

        if( !empty($logs) ) {
            foreach ($logs as $key => $value) {
                $user_id = $this->filterEmptyField($value, 'Log', 'user_id');
                $value = $this->Log->User->getMerge($value, $user_id);
                $logs[$key] = $value;
            }
        }

        $this->controller->set('logs', $logs);
    }

    function checkAllowFunction( $params ) {
        $allowFunction = false;
        $paramController = !empty($params['controller'])?$params['controller']:false;
        $paramAction = !empty($params['action'])?$params['action']:false;

        $group_id = Configure::read('__Site.config_group_id');
        $current_branch_id = Configure::read('__Site.config_branch_id');
        $_allowModule = Configure::read('__Site.config_allow_module');

        if( $group_id == 1 ) {
            $allowFunction = true;
        } else if( !empty($_allowModule[$current_branch_id][$paramController]['action']) ) {
            $allowAction = $_allowModule[$current_branch_id][$paramController]['action'];

            if( in_array('edit_asset', $allowAction) ) {
                $allowFunction = true;
            } else if( in_array($paramAction, $allowAction) ) {
                $allowFunction = true;
            }
        }

        return $allowFunction;
    }

    function mergeDate ($data, $model, $field, $newField) {
        $date = $this->filterEmptyField($data, $model, $field);

        if( !empty($date) && $date != '0000-00-00' ) {
            $mkDate = strtotime($date);
            $data[$model][$newField]['day'] = date('d', $mkDate);
            $data[$model][$newField]['month'] = date('m', $mkDate);
            $data[$model][$newField]['year'] = date('Y', $mkDate);
        }

        return $data;
    }

    function checkdate($str) {
        $date = date_parse($str);

        if ($date["error_count"] == 0 && checkdate($date["month"], $date["day"], $date["year"])) {
            return $this->customDate($str, 'Y-m-d');
        } else {
            return $this->getDate($str);
        }
    }

    function _date_range_limit($start, $end, $adj, $a, $b, $result) {
        if ($result[$a] < $start) {
            $result[$b] -= intval(($start - $result[$a] - 1) / $adj) + 1;
            $result[$a] += $adj * intval(($start - $result[$a] - 1) / $adj + 1);
        }

        if ($result[$a] >= $end) {
            $result[$b] += intval($result[$a] / $adj);
            $result[$a] -= $adj * intval($result[$a] / $adj);
        }

        return $result;
    }

    function _date_range_limit_days($base, $result) {
        $days_in_month_leap = array(31, 31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
        $days_in_month = array(31, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

        $this->_date_range_limit(1, 13, 12, "m", "y",   $base);

        $year = $base["y"];
        $month = $base["m"];

        if (!$result["invert"]) {
            while ($result["d"] < 0) {
                $month--;
                if ($month < 1) {
                    $month += 12;
                    $year--;
                }

                $leapyear = $year % 400 == 0 || ($year % 100 != 0 && $year % 4 == 0);
                $days = $leapyear ? $days_in_month_leap[$month] : $days_in_month[$month];

                $result["d"] += $days;
                $result["m"]--;
            }
        } else {
            while ($result["d"] < 0) {
                $leapyear = $year % 400 == 0 || ($year % 100 != 0 && $year % 4 == 0);
                $days = $leapyear ? $days_in_month_leap[$month] : $days_in_month[$month];

                $result["d"] += $days;
                $result["m"]--;

                $month++;
                if ($month > 12) {
                    $month -= 12;
                    $year++;
                }
            }
        }

        return $result;
    }

    function _date_normalize($base, $result) {
        $result = $this->_date_range_limit(0, 60, 60, "s", "i", $result);
        $result = $this->_date_range_limit(0, 60, 60, "i", "h", $result);
        $result = $this->_date_range_limit(0, 24, 24, "h", "d", $result);
        $result = $this->_date_range_limit(0, 12, 12, "m", "y", $result);

        $result = $this->_date_range_limit_days($base, $result);

        $result = $this->_date_range_limit(0, 12, 12, "m", "y", $result);

        return $result;
    }

    function _callDateDiff ( $one, $two ) {
        $invert = false;
        $one = strtotime($one);
        $two = strtotime($two);

        if ($one > $two) {
            list($one, $two) = array($two, $one);
            $invert = true;
        }

        $key = array("y", "m", "d", "h", "i", "s");
        $a = array_combine($key, array_map("intval", explode(" ", date("Y m d H i s", $one))));
        $b = array_combine($key, array_map("intval", explode(" ", date("Y m d H i s", $two))));

        $result = array();
        $result["y"] = $b["y"] - $a["y"];
        $result["m"] = $b["m"] - $a["m"];
        $result["d"] = $b["d"] - $a["d"];
        $result["h"] = $b["h"] - $a["h"];
        $result["i"] = $b["i"] - $a["i"];
        $result["s"] = $b["s"] - $a["s"];
        $result["invert"] = $invert ? 1 : 0;
        $result["days"] = intval(abs(($one - $two)/86400));

        if ($invert) {
            $this->_date_normalize($a, $result);
        } else {
            $this->_date_normalize($b, $result);
        }

        return $result;
    }

    function dateDiff ( $startDate, $endDate, $format = false, $tree = false ) {
        $result = false;
        
        if( !empty($startDate) && !empty($endDate) && $startDate != '0000-00-00 00:00:00' && $endDate != '0000-00-00 00:00:00' ) {
            switch ($format) {
                case 'day':
                    $from_time = strtotime($startDate);
                    $to_time = strtotime($endDate);
                    $datediff = intval($to_time - $from_time);
                    $total_day = intval($datediff/(60*60*24));
                    $total_hour = intval($datediff/(60*60));

                    if( !empty($tree) ) {
                        $dateResult = $this->_callDateDiff ( $startDate, $endDate );
                        
                        $result = array(
                            'total_d' => $total_day,
                            'total_hour' => $total_hour,
                        );
                        $h = $this->filterEmptyField($dateResult, 'h');
                        $i = $this->filterEmptyField($dateResult, 'i');

                        if( !empty($total_day) ) {
                            $result['FormatArr']['d'] = sprintf(__('%s Hari'), $total_day);
                        }
                        if( !empty($h) ) {
                            $result['FormatArr']['h'] = sprintf(__('%s Jam'), $h);
                        }
                        if( !empty($i) ) {
                            $result['FormatArr']['i'] = sprintf(__('%s Menit'), $i);
                        }
                    } else {
                        $result = $day;
                    }

                    break;

                default:
                    $result = $this->_callDateDiff ( $startDate, $endDate );
                    break;
            }
        }

        return $result;
    }

    function _callPriceConverter ($price) {
        return trim(str_replace(array( ',' ), array( '' ), $price));
    }

    function _callDateRangeConverter ( $date ) {
        $result = array();

        if(!empty($date)){
            $dateStr = urldecode($date);
            $date = explode('-', $dateStr);

            if( !empty($date) ) {
                $date[0] = urldecode($date[0]);
                $date[1] = urldecode($date[1]);
                $dateStr = sprintf('%s-%s', $date[0], $date[1]);
                $dateFrom = $this->getDate($date[0]);
                $dateTo = $this->getDate($date[1]);
                $result['dateFrom'] = $dateFrom;
                $result['dateTo'] = $dateTo;
            }
        }

        return $result;
    }

    function dataConverter ( $data, $fields, $reverse = false ) {
        if( !empty($fields) ) {
            foreach ($fields as $type => $models) {
                switch ($type) {
                    case 'daterange':
                        if( !empty($models) ) {
                            if( is_array($models) ) {
                                foreach ($models as $modelName => $model) {
                                    if( !empty($model) ) {
                                        if( is_array($model) ) {
                                            foreach ($model as $key => $fieldName) {
                                                if( !empty($data[$modelName][$fieldName]) ) {
                                                    $data[$modelName] = array_merge($data[$modelName], $this->_callDateRangeConverter($data[$modelName][$fieldName]));
                                                }
                                            }
                                        } else {
                                            if( !empty($data[$model]) ) {
                                                $data = array_merge($data, $this->_callDateRangeConverter($data[$model]));
                                            }
                                        }
                                    }
                                }
                            } else {
                                if( !empty($data[$models]) ) {
                                    $data = array_merge($data, $this->_callDateRangeConverter($data, $data[$models]));
                                }
                            }
                        }
                        break;
                    case 'date':
                        if( !empty($models) ) {
                            if( is_array($models) ) {
                                foreach ($models as $modelName => $model) {
                                    if( !empty($model) ) {
                                        if( is_array($model) ) {
                                            foreach ($model as $key => $fieldName) {
                                                if( !empty($data[$modelName][$fieldName]) ) {
                                                    $data[$modelName][$fieldName] = $this->getDate($data[$modelName][$fieldName], $reverse);
                                                }
                                            }
                                        } else {
                                            if( !empty($data[$model]) ) {
                                                $data[$model] = $this->getDate($data[$model], $reverse);
                                            }
                                        }
                                    }
                                }
                            } else {
                                if( !empty($data[$models]) ) {
                                    $data[$models] = $this->getDate($data[$models], $reverse);
                                }
                            }
                        }
                        break;
                    case 'price':
                        if( !empty($models) ) {
                            if( is_array($models) ) {
                                foreach ($models as $modelName => $model) {
                                    if( !empty($model) ) {
                                        if( is_array($model) ) {
                                            foreach ($model as $key => $fieldName) {
                                                if( !empty($data[$modelName][$fieldName]) ) {
                                                    $data[$modelName][$fieldName] = $this->_callPriceConverter($data[$modelName][$fieldName], $reverse);
                                                }
                                            }
                                        } else {
                                            if( !empty($data[$model]) ) {
                                                $data[$model] = $this->_callPriceConverter($data[$model], $reverse);
                                            }
                                        }
                                    }
                                }
                            } else {
                                if( !empty($data[$models]) ) {
                                    $data[$models] = $this->_callPriceConverter($data[$models], $reverse);
                                }
                            }
                        }
                        break;
                }
            }
        }

        return $data;
    }

    function _callMsgValidationErrors ( $validationErrors, $type = 'array' ) {
        $textError = array();

        if( !empty($validationErrors) ) {
            foreach ($validationErrors as $key => $validationError) {
                if( !empty($validationError) ) {
                    foreach ($validationError as $key => $error) {
                        $textError[] = $error;
                    }
                }
            }

            switch ($type) {
                case 'string':
                    $textError = implode(', ', $textError);
                    break;
            }
        }

        return $textError;
    }

    function _callDateView ( $dateFrom, $dateTo ) {
        return sprintf('%s - %s', date('d/m/Y', strtotime($dateFrom)), date('d/m/Y', strtotime($dateTo)));
    }

    function _callSearchNopol ( $conditions, $refine, $fieldName ){
        $nopol = $this->filterEmptyField($refine, 'nopol');

        if(!empty($nopol)){
            $this->Truck = ClassRegistry::init('Truck'); 
            $type = !empty($refine['type'])?$refine['type']:1;

            $nopol = urldecode($nopol);
            $type = urldecode($type);

            if( $type == 2 ) {
                $conditionsNopol = array(
                    'Truck.id' => $nopol,
                );
            } else {
                $conditionsNopol = array(
                    'Truck.nopol LIKE' => '%'.$nopol.'%',
                );
            }

            $truckId = $this->Truck->getData('list', array(
                'conditions' => $conditionsNopol,
                'fields' => array(
                    'Truck.id', 'Truck.id',
                ),
            ), true, array(
                'branch' => false,
            ));
            $conditions[$fieldName] = $truckId;

            $this->controller->request->data['Ttuj']['nopol'] = $nopol;
            $this->controller->request->data['Ttuj']['type'] = $type;
        }

        return $conditions;
    }

    function _callRefineGenerating ( $conditions, $refine, $values ) {
        if( !empty($values) ) {
            foreach ($values as $key => $value) {
                $fieldName = $this->filterEmptyField($value, 'fieldName');
                $modelName = $this->filterEmptyField($value, 'modelName');
                $conditionName = $this->filterEmptyField($value, 'conditionName');
                $operator = $this->filterEmptyField($value, 'operator');
                $keyword = $this->filterEmptyField($value, 'keyword');
                $customKeyword = $this->filterEmptyField($refine, $fieldName);

                if( empty($keyword) ) {
                    $keyword = $customKeyword;
                }

                if(!empty($keyword)){
                    $operator = strtolower($operator);

                    if( is_string($keyword) ) {
                        $keyword = urldecode($keyword);
                    }
                    
                    $this->controller->request->data[$modelName][$fieldName] = $customKeyword;

                    switch ($operator) {
                        case 'like':
                            $conditionName .= ' LIKE';
                            $keyword = '%'.$keyword.'%';
                            break;
                    }

                    $conditions[$conditionName] = $keyword;
                }
            }
        }

        return $conditions;
    }

    function _saveLog( $options = false ){
        $this->Log = ClassRegistry::init('Log'); 

        $activity = $this->filterEmptyField($options, 'activity');
        $old_data = $this->filterEmptyField($options, 'old_data');
        $document_id = $this->filterEmptyField($options, 'document_id');
        $error = $this->filterEmptyField($options, 'error');
        $custom_action = $this->filterEmptyField($options, 'custom_action');

        $log = array();
        $user_id = Configure::read('__Site.config_user_id');

        $controllerName = $this->controller->params['controller'];
        $actionName = $this->controller->params['action'];
        $data = serialize($this->controller->params['data']);
        $named = serialize( $this->controller->params['named'] );

        $old_data = serialize( $old_data );
        $url = $this->RequestHandler->getReferer();

        $ip_address = $this->RequestHandler->getClientIP();

        $user_agents = @get_browser(null, true);
        $browser = !empty($user_agents['browser'])?implode(' ', array($user_agents['browser'], $user_agents['version'])):'';
        $os = !empty($user_agents['platform'])?$user_agents['platform']:'';

        if( empty($custom_action) ) {
            $custom_action = $actionName;
        }
        
        $log['Log']['user_id'] = $user_id;
        $log['Log']['transaction_id'] = $document_id;
        $log['Log']['name'] = $activity;
        $log['Log']['model'] = $controllerName;
        $log['Log']['action'] = $custom_action;
        $log['Log']['real_action'] = $actionName;

        $log['Log']['old_data'] = $old_data;
        $log['Log']['data'] = $data;
        $log['Log']['ip'] = $ip_address;
        $log['Log']['user_agent'] = env('HTTP_USER_AGENT');
        $log['Log']['browser'] = $browser;
        $log['Log']['os'] = $os;
        $log['Log']['from'] = $url;
        $log['Log']['named'] = $named;
        $log['Log']['error'] = !empty($error)?$error:0;
        $log['Log']['bank_activity'] = 1;

        if( $this->Log->doSave($log) ) {
            return true;    
        } else {
            return false;
        }
    }

    function _saveNotification( $options = false){
        $data = array();
        $created_id = Configure::read('__Site.config_user_id');
        $branch_id = Configure::read('__Site.config_branch_id');
        $user_id = $this->filterEmptyField($options, 'user_id');

        App::import('Helper', 'Html');
        $this->Html = new HtmlHelper(new View(null));
        $url = $this->filterEmptyField($options, 'url');

        if( !empty($url) ) {
            $url = $this->Html->url($url);
            $data['Notification']['url'] = $url;
        }
        
        $data['Notification']['action'] = $this->filterEmptyField($options, 'action');
        $data['Notification']['name'] = $this->filterEmptyField($options, 'name');
        $data['Notification']['document_id'] = $this->filterEmptyField($options, 'document_id');
        $data['Notification']['type_notif'] = $this->filterEmptyField($options, 'type_notif', false, 'warning');

        $data['Notification']['branch_id'] = $branch_id ;
        $data['Notification']['created_id'] = $created_id;

        if( !empty($user_id) ) {
            if( is_array($user_id) ) {
                foreach ($user_id as $key => $id) {
                    $data['Notification']['user_id'] = $id;
                    $this->controller->User->Notification->create();
                    $this->controller->User->Notification->doSave($data);
                }
            } else {
                $data['Notification']['user_id'] = $user_id;
                $this->controller->User->Notification->create();
                $this->controller->User->Notification->doSave($data);
            }
        }
    }

    function _callSaveNotification( $data = NULL ){
        $flag = true;

        if( !empty($data['Notification']) ) {
            $dataNotif = $this->filterEmptyField($data, 'Notification');

            if( !empty($dataNotif['name']) ) {
                $notifs = array(
                    array(
                        'Notification' => $dataNotif,
                    ),
                );
            } else {
                $notifs = $dataNotif;
            }

            if( !empty($notifs) ) {
                foreach ($notifs as $key => $notif) {
                    $data['Notification'] = $this->filterEmptyField($notif, 'Notification');

                    if( !$this->controller->User->Notification->doSaveMany($data) ) {
                        $flag = false;
                    }
                }
            }
        }

        return $flag;
    }

    function _callUnset( $fieldArr, $data ) {
        if( !empty($fieldArr) ) {
            foreach ($fieldArr as $key => $value) {
                if( is_array($value) ) {
                    foreach ($value as $idx => $fieldName) {
                        if( isset($data[$key][$fieldName]) ) {
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

    function processFilter ( $data ) {
        $date = $this->filterEmptyField($data, 'Search', 'date');
        $daterange = $this->filterEmptyField($data, 'Search', 'daterange');
        $datettuj = $this->filterEmptyField($data, 'Search', 'datettuj');
        $journalcoa = $this->filterEmptyField($data, 'Search', 'journalcoa');
        $params = array();

        $dateFrom = $this->filterEmptyField($data, 'Search', 'from');
        $monthFrom = $this->filterEmptyField($dateFrom, 'month');
        $yearFrom = $this->filterEmptyField($dateFrom, 'year');

        $dateTo = $this->filterEmptyField($data, 'Search', 'to');
        $monthTo = $this->filterEmptyField($dateTo, 'month');
        $yearTo = $this->filterEmptyField($dateTo, 'year');

        $data = $this->_callUnset(array(
            'Search' => array(
                'date',
                'daterange',
                'datettuj',
                'from',
                'journalcoa',
            ),
        ), $data);
        $dataSearch = $this->filterEmptyField($data, 'Search');

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
                    $pos = strpos($value, '/');

                    if( !empty($pos) ) {
                        $params[$fieldName] = rawurlencode(urlencode($value));
                    } else {
                        $params[$fieldName] = rawurlencode($value);
                    }
                }
            }
        }
        if( !empty($journalcoa) ) {
            if( is_array($journalcoa) ) {
                $journalcoa = implode(',', $journalcoa);
            }

           $params['journalcoa'] = rawurlencode(urlencode($journalcoa));
        }

        if( !empty($date) ) {
            $params['date'] = rawurlencode(urlencode($date));
        }
        if( !empty($datettuj) ) {
            $params['datettuj'] = rawurlencode(urlencode($datettuj));
        }
        if( !empty($daterange) ) {
            $params['daterange'] = rawurlencode(urlencode($daterange));
        }
        if( !empty($monthFrom) && !empty($yearFrom) ) {
            $params['monthFrom'] = urlencode($monthFrom);
            $params['yearFrom'] = urlencode($yearFrom);
        }
        if( !empty($monthTo) && !empty($yearTo) ) {
            $params['monthTo'] = urlencode($monthTo);
            $params['yearTo'] = urlencode($yearTo);
        }
        
        return $params;
    }

    function _callSplitDate ( $date ) {
        $result = array();

        if( !empty($date) ) {
            $dateStr = urldecode($date);
            $date = explode('-', $dateStr);

            if( !empty($date) ) {
                $date[0] = urldecode($date[0]);
                $date[1] = urldecode($date[1]);
                $dateFrom = $this->getDate($date[0]);
                $dateTo = $this->getDate($date[1]);
                $result['DateFrom'] = $dateFrom;
                $result['DateTo'] = $dateTo;
            }
        }
        
        return $result;
    }

    function _callUnSplitDate ( $fromDate, $toDate ) {
        if( !empty($fromDate) && !empty($toDate) ) {
            $fromDate = $this->customDate($fromDate, 'd/m/Y');
            $toDate = $this->customDate($toDate, 'd/m/Y');

            $date = sprintf('%s - %s', $fromDate, $toDate);
        } else {
            $date = false;
        }

        return $date;
    }

    function _callDateRangeFormat ( $result, $daterange, $fieldName = 'date', $fromName = 'DateFrom', $toName = 'DateTo' ) {
        if( !empty($daterange) ) {
            $dateStr = urldecode($daterange);
            $daterange = explode('-', $dateStr);

            if( !empty($daterange) ) {
                $daterange[0] = urldecode($daterange[0]);
                $daterange[1] = urldecode($daterange[1]);
                $dateFrom = $this->getDate($daterange[0]);
                $dateTo = $this->getDate($daterange[1]);
                $result['named'][$fromName] = $dateFrom;
                $result['named'][$toName] = $dateTo;
            }

            if( !empty($dateFrom) && !empty($dateTo) ) {
                $this->controller->request->data['Search'][$fieldName] = sprintf('%s - %s', date('d/m/Y', strtotime($dateFrom)), date('d/m/Y', strtotime($dateTo)));
            }
        }

        return $result;
    }

    function _callRefineParams ( $data, $options = array() ) {
        $result['named'] = $this->filterEmptyField($data, 'named');

        $dateFrom = $this->filterEmptyField($options, 'dateFrom');
        $dateTo = $this->filterEmptyField($options, 'dateTo');

        $monthFrom = $this->filterEmptyField($options, 'monthFrom');
        $monthTo = $this->filterEmptyField($options, 'monthTo');

        $dateFromTtuj = $this->filterEmptyField($options, 'dateFromTtuj');
        $dateToTtuj = $this->filterEmptyField($options, 'dateToTtuj');

        $dateRitaseFrom = $this->filterEmptyField($options, 'dateRitaseFrom');
        $dateRitaseTo = $this->filterEmptyField($options, 'dateRitaseTo');

        $paramMonthFrom = $this->filterEmptyField($result, 'named', 'monthFrom');
        $paramYearFrom = $this->filterEmptyField($result, 'named', 'yearFrom');

        $paramYear = $this->filterEmptyField($options, 'param_year');
        $year = $this->filterEmptyField($result, 'named', 'year');

        $date = $this->filterEmptyField($result, 'named', 'date');
        $datettuj = $this->filterEmptyField($result, 'named', 'datettuj');
        $daterange = $this->filterEmptyField($result, 'named', 'daterange');
        $dateritase = $this->filterEmptyField($result, 'named', 'dateritase');
        $journalcoa = $this->filterEmptyField($result, 'named', 'journalcoa');

        $dataString = $this->_callUnset(array(
            'date',
            'daterange',
            'datettuj',
            'dateritase',
            'to',
            'from',
            'journalcoa',
        ), $result['named']);

        if( !empty($dataString) ) {
            foreach ($dataString as $fieldName => $value) {
                $this->controller->request->data['Search'][$fieldName] = urldecode(rawurldecode($value));
                $result['named'][$fieldName] = urldecode(rawurldecode($value));
            }
        }
        if( !empty($journalcoa) ) {
            $journalcoa = urldecode(rawurldecode($journalcoa));
            $journalcoaArr = explode(',', $journalcoa);

            if( !empty($journalcoaArr) && count($journalcoaArr) > 1 ) {
                $journalcoa = $journalcoaArr;
                $result['named']['journalcoa'] = $journalcoa;
            } else {
                $result['named']['journalcoa'] = $journalcoa;
            }

            $this->controller->request->data['Search']['journalcoa'] = $journalcoa;
        }

        if( !empty($date) ) {
            $result = $this->_callDateRangeFormat($result, $date);
        } else if( !empty($dateFrom) && !empty($dateTo) ) {
            $this->controller->request->data['Search']['date'] = sprintf('%s - %s', date('d/m/Y', strtotime($dateFrom)), date('d/m/Y', strtotime($dateTo)));

            $result['named']['DateFrom'] = $dateFrom;
            $result['named']['DateTo'] = $dateTo;
        }
        if( !empty($datettuj) ) {
            $result = $this->_callDateRangeFormat($result, $datettuj, 'datettuj', 'DateFromTtuj', 'DateToTtuj');
        } else if( !empty($dateFromTtuj) && !empty($dateToTtuj) ) {
            $this->controller->request->data['Search']['date'] = sprintf('%s - %s', date('d/m/Y', strtotime($dateFromTtuj)), date('d/m/Y', strtotime($dateToTtuj)));

            $result['named']['DateFromTtuj'] = $dateFromTtuj;
            $result['named']['DateToTtuj'] = $dateToTtuj;
        }
        if( !empty($dateritase) ) {
            $result = $this->_callDateRangeFormat($result, $dateritase, 'dateritase', 'DateRitaseFrom', 'DateRitaseTo');
        } else if( !empty($dateRitaseFrom) && !empty($dateRitaseTo) ) {
            $this->controller->request->data['Search']['dateritase'] = sprintf('%s - %s', date('d/m/Y', strtotime($dateRitaseFrom)), date('d/m/Y', strtotime($dateRitaseTo)));

            $result['named']['DateRitaseFrom'] = $dateRitaseFrom;
            $result['named']['DateRitaseTo'] = $dateRitaseTo;
        }

        if( !empty($daterange) ) {
            $result = $this->_callDateRangeFormat($result, $daterange, 'daterange', 'DateFromRange', 'DateToRange');
        }
        if( !empty($paramMonthFrom) && !empty($paramYearFrom) ) {
            $monthFrom = sprintf('%s-%s', $paramYearFrom, $paramMonthFrom);
            $monthTo = date('Y-m', strtotime('+12 Month', strtotime($monthFrom)));
        }
        if( !empty($monthFrom) && !empty($monthTo) ) {
            $this->controller->request->data['Search']['from']['month'] = date('m', strtotime($monthFrom));
            $this->controller->request->data['Search']['from']['year'] = date('Y', strtotime($monthFrom));

            $this->controller->request->data['Search']['to']['month'] = date('m', strtotime($monthTo));
            $this->controller->request->data['Search']['to']['year'] = date('Y', strtotime($monthTo));

            $result['named']['MonthFrom'] = $monthFrom;
            $result['named']['MonthTo'] = $monthTo;
        }
        if( !empty($year) ) {
            $paramYear = $year;
        }
        if( !empty($paramYear) ) {
            $this->controller->request->data['Search']['year'] = $paramYear;

            $result['named']['year'] = $paramYear;
        }

        return $result;
    }

    function _callDemoVersion () {
        // if( in_array(FULL_BASE_URL, array( 'http://ww.erprjtm.com', 'http://erp.rjtm.co.id', 'http://yukblanja.com' )) ) {
            return true;
        // } else {
        //     return false;
        // }
    }

    function _callPercentAmount ( $total, $percent ) {
        return $total * ($percent/100);
    }

    function _callPaymentNotifs () {
        $allowModule = Configure::read('__Site.config_allow_module');
        $groupId = Configure::read('__Site.config_group_id');
        $actionController = !empty($allowModule['leasings'])?$allowModule['leasings']:false;
        $actionAllow = !empty($actionController['action'])?$actionController['action']:false;

        if( $groupId == 1 || ( !empty($actionController) && in_array('payments', $actionAllow) ) ) {
            $this->SettingGeneral = ClassRegistry::init('SettingGeneral'); 
            $dataSetting = $this->SettingGeneral->find('first', array(
                'conditions' => array(
                    'SettingGeneral.name' => 'leasing_expired_day',
                ),
            ));
            $leasing_expired_day = $this->filterEmptyField($dataSetting, 'SettingGeneral', 'value');

            if( !empty($leasing_expired_day) ) {
                $options = array(
                    'conditions' => array(
                        'DATEDIFF(LeasingInstallment.paid_date, DATE_FORMAT(NOW(), \'%Y-%m-%d\')) <=' => $leasing_expired_day,
                    ),
                    'contain' => array(
                        'Leasing',
                    ),
                    'group' => array(
                        'LeasingInstallment.leasing_id',
                    ),
                );
                $elements = array(
                    'status' => 'unpaid',
                    'branch' => false,
                );

                $notifications = $this->controller->GroupBranch->Branch->Leasing->LeasingInstallment->getData('all', array_merge($options, array(
                    'limit' => 10,
                )), $elements);
                $notifications = $this->controller->GroupBranch->Branch->Leasing->Vendor->getMerge($notifications, false, 'Leasing');
                $cnt = $this->controller->GroupBranch->Branch->Leasing->LeasingInstallment->getData('count', $options, $elements);

                return array(
                    'notifications' => $notifications,
                    'cnt' => $cnt,
                );
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function array_filter_recursive($input) { 
        foreach ($input as &$value) {
            if (is_array($value)) {
                $value = $this->array_filter_recursive($value);
            }
        }

        return array_filter($input); 
    }

    function _callAllowApproval ( $value, $user_id, $document_id, $document_type ) {
        $approval_id = false;
        $approval_detail_id = false;
        $approval_detail_position_id = false;
        $show_approval = false;
        $dataOtorisasiApproval = false;
        $value = $this->controller->User->getMerge($value, $user_id);
        $user_position_id = $this->filterEmptyField($value, 'Employe', 'employe_position_id');

        $user_otorisasi_approvals = $this->controller->User->Employe->EmployePosition->Approval->getUserOtorisasiApproval($document_type, $user_position_id, false, $document_id);

        if( !empty($user_otorisasi_approvals) ) {
            $position_otorisasi_approvals = Set::extract('/EmployePosition/id', $user_otorisasi_approvals);
        } else {
            $position_otorisasi_approvals = array();
        }

        $approval = $this->controller->user_data;

        $approval_employe_id = $this->filterEmptyField($approval, 'employe_id');
        $approval = $this->controller->User->Employe->getMerge($approval, $approval);

        $approval_position_id = $this->filterEmptyField($approval, 'Employe', 'employe_position_id');
        $idx_arr_otorisasi = array_search($approval_position_id, $position_otorisasi_approvals);
        $show_approval = false;

        if( is_numeric($idx_arr_otorisasi) && !empty($user_otorisasi_approvals[$idx_arr_otorisasi]) ) {
            $dataOtorisasiApproval = $user_otorisasi_approvals[$idx_arr_otorisasi];

            $approval_detail_id = $this->filterEmptyField($dataOtorisasiApproval, 'ApprovalDetailPosition', 'approval_detail_id');
            $approval_detail_position_id = $this->filterEmptyField($dataOtorisasiApproval, 'ApprovalDetailPosition', 'id');

            $approvalDetail = $this->controller->User->Employe->EmployePosition->Approval->ApprovalDetail->getData('first', array(
                'conditions' => array(
                    'ApprovalDetail.id' => $approval_detail_id,
                ),
            ));
            $approval_id = $this->filterEmptyField($approvalDetail, 'ApprovalDetail', 'approval_id');

            $this->DocumentAuth = ClassRegistry::init('DocumentAuth'); 
            $auth = $this->DocumentAuth->getData('first', array(
                'conditions' => array(
                    'DocumentAuth.document_id' => $document_id,
                    'DocumentAuth.approval_id' => $approval_id,
                    'DocumentAuth.approval_detail_id' => $approval_detail_id,
                    'DocumentAuth.approval_detail_position_id' => $approval_detail_position_id,
                ),
            ));

            if( empty($auth) ) {
                $show_approval = in_array($approval_position_id, $position_otorisasi_approvals)?true:false;
            }
        }

        return array(
            'approval_id' => $approval_id,
            'approval_detail_id' => $approval_detail_id,
            'approval_detail_position_id' => $approval_detail_position_id,
            'user_otorisasi_approvals' => $user_otorisasi_approvals,
            'show_approval' => $show_approval,
            'data_otorisasi_approval' => $dataOtorisasiApproval,
        );
    }

    function _callProcessApproval ( $result_approval, $document, $id, $document_type ) {
        $data = $this->controller->request->data;
        $approval_id = $this->filterEmptyField($result_approval, 'approval_id');
        $approval_detail_id = $this->filterEmptyField($result_approval, 'approval_detail_id');
        $approval_detail_position_id = $this->filterEmptyField($result_approval, 'approval_detail_position_id');
        $dataOtorisasiApproval = $this->filterEmptyField($result_approval, 'data_otorisasi_approval');
        $user_otorisasi_approvals = $this->filterEmptyField($result_approval, 'user_otorisasi_approvals');

        $is_priority = $this->filterEmptyField($dataOtorisasiApproval, 'ApprovalDetailPosition', 'is_priority');
        $employe_position_id = $this->filterEmptyField($dataOtorisasiApproval, 'ApprovalDetailPosition', 'employe_position_id');
        $status_document = $this->filterEmptyField($data, 'DocumentAuth', 'status_document');

        $data_arr = false;
        $msgRevision = false;
        $nodoc = $this->filterEmptyField($document, 'SupplierQuotation', 'nodoc');

        $position_approval = $this->controller->User->Employe->EmployePosition->Approval->getPositionPriority($user_otorisasi_approvals);
        $position_priority = $this->filterEmptyField($position_approval, 'Priority');
        $position_normal = $this->filterEmptyField($position_approval, 'Normal');

        $data['DocumentAuth']['document_id'] = $id;
        $data['DocumentAuth']['document_type'] = $document_type;
        $data['DocumentAuth']['approval_id'] = $approval_id;
        $data['DocumentAuth']['approval_detail_id'] = $approval_detail_id;
        $data['DocumentAuth']['approval_detail_position_id'] = $approval_detail_position_id;

        $this->DocumentAuth = ClassRegistry::init('DocumentAuth'); 
        $position_auths = $this->DocumentAuth->getData('all', array(
            'conditions' => array(
                'DocumentAuth.document_id' => $id,
            ),
            'contain' => array(
                'ApprovalDetailPosition',
            ),
        ));
        $position_priority_auth = array();
        $position_normal_auth = array();

        if( !empty($position_auths) ) {
            foreach ($position_auths as $key => $value) {
                if( !empty($value['ApprovalDetailPosition']['employe_position_id']) ) {
                    if( !empty($value['ApprovalDetailPosition']['is_priority']) ) {
                        $position_priority_auth[] = $value['ApprovalDetailPosition']['employe_position_id'];
                    } else {
                        $position_normal_auth[] = $value['ApprovalDetailPosition']['employe_position_id'];
                    }
                }
            }
        }

        $position_priority_auth = array_values($position_priority_auth);

        if( !empty($is_priority) ) {
            $position_priority_auth[] = $employe_position_id;
            $position_priority_auth = array_unique($position_priority_auth);
        } else {
            $position_normal_auth[] = $employe_position_id;
            $position_normal_auth = array_unique($position_normal_auth);
        }

        if( !empty($status_document) ) {
            $this->DocumentAuth->create();
            $this->DocumentAuth->set($data);

            if($this->DocumentAuth->save()){
                $data_arr = array();

                if( $this->checkArrayApproval($position_priority_auth, $position_priority) || ( empty($position_priority) && $this->checkArrayApproval($position_normal_auth, $position_normal) ) ){
                    switch ($status_document) {
                        case 'approve':
                            $msgRevision = sprintf(__('Dokumen dengan No Dokumen %s telah disetujui'), $nodoc);

                            if( $document_type == 'cash_bank' ) {
                                $data_arr = array(
                                    'completed' => 1,
                                    'is_revised' => 0,
                                    'is_rejected' => 0
                                );
                            } else {
                                $data_arr = array(
                                    'approval' => 'approved',
                                );
                            }
                            break;
                        case 'revise':
                            $msgRevision = sprintf(__('Dokumen dengan No Dokumen %s memerlukan resivisi Anda'), $nodoc);

                            if( $document_type == 'cash_bank' ) {
                                $data_arr = array(
                                    'completed' => 0,
                                    'is_revised' => 1,
                                    'is_rejected' => 0
                                );
                            } else {
                                $data_arr = array(
                                    'approval' => 'revised',
                                );
                            }
                            break;
                        case 'reject':
                            $msgRevision = sprintf(__('Dokumen dengan No Dokumen %s telah ditolak'), $nodoc);

                            if( $document_type == 'cash_bank' ) {
                                $data_arr = array(
                                    'completed' => 0,
                                    'is_revised' => 0,
                                    'is_rejected' => 1
                                );
                            } else {
                                $data_arr = array(
                                    'approval' => 'rejected',
                                );
                            }
                            break;
                    }
                }else if($status_document == 'revise'){
                    if( $document_type == 'cash_bank' ) {
                        $data_arr = array(
                            'is_revised' => 1,
                        );
                    } else {
                        $data_arr = array(
                            'approval' => 'revised',
                        );
                    }
                    $msgRevision = sprintf(__('Dokumen dengan No Dokumen %s memerlukan resivisi Anda'), $nodoc);
                }
            }else{
                $this->setCustomFlash('Gagal melakukan Approval.', 'error');
            }
        }else{
            $this->setCustomFlash('Silahkan pilih Status Approval', 'error');
        }

        return array(
            'data' => $data_arr,
            'msg_revision' => $msgRevision,
        );
    }

    function convertDataAutocomplete( $data ) {
        $result = array();

        if( !empty($data) ) {
            foreach ($data as $id => $value) {
                array_push($result, $value);
            }
        }

        return $result;
    }

    function _callBeforeSaveApproval ( $data, $options ) {
        if( !empty($data) ) {
            $user_position_id = $this->filterEmptyField($options, 'user_position_id');
            $document_id = $this->filterEmptyField($options, 'document_id');
            $document_type = $this->filterEmptyField($options, 'document_type');
            $user_id = $this->filterEmptyField($options, 'user_id');
            $nodoc = $this->filterEmptyField($options, 'nodoc');
            $document_url = $this->filterEmptyField($options, 'document_url');
            $document_revised_url = $this->filterEmptyField($options, 'document_revised_url', false, $document_url);

            $dataDocument = $this->controller->User->Employe->EmployePosition->Approval->_callApprovalId($document_type, $user_position_id);

            $data = array_merge_recursive($data, $dataDocument);
            $data['DocumentAuth']['user_id'] = $user_id;
            $data['DocumentAuth']['nodoc'] = $nodoc;
            $data['DocumentAuth']['document_url'] = $document_url;
            $data['DocumentAuth']['document_revised_url'] = $document_revised_url;
            $data['DocumentAuth']['document_id'] = $document_id;
            $data['DocumentAuth']['document_type'] = $document_type;
            $data['DocumentAuth']['user_position_id'] = $user_position_id;
        }

        return $data;
    }

    function _callBeforeViewReport ( $type = false, $options = array() ) {
        switch ($type) {
            case 'pdf':
                $this->controller->layout = 'pdf';
                break;

            case 'excel':
                $this->controller->layout = 'ajax';
                break;
            
            default:
                $layout_file = $this->filterEmptyField($options, 'layout_file');

                if( !empty($layout_file) ) {
                    $this->_layout_file(array(
                        'select',
                        'freeze',
                    ));
                }
                break;
        }
    }

    function _callDataClosing () {
        $this->SettingGeneral = ClassRegistry::init('SettingGeneral'); 
        $value = $this->SettingGeneral->find('first', array(
            'conditions' => array(
                'SettingGeneral.name' => 'lock_closing_bank',
            ),
        ));

        $lbl = $this->filterEmptyField($value, 'SettingGeneral', 'name');
        $val = $this->filterEmptyField($value, 'SettingGeneral', 'value');

        if( !empty($val) ) {
            $closing = $this->controller->User->CoaClosing->getData('first', array(
                'order' => array(
                    'CoaClosing.periode' => 'DESC',
                    'CoaClosing.id' => 'DESC',
                ),
            ));

            $periode = $this->filterEmptyField($closing, 'CoaClosing', 'periode', false, array(
                'date' => 'Y-m',
            ));

            if( !empty($periode) ) {
                $min_date = date('01/m/Y', strtotime($periode. ' + 1 Month'));
                Configure::write('__Site.Closing.min_date', $min_date);
            }

            Configure::write('__Site.Closing.periode', $periode);
        }

        Configure::write(sprintf('__Site.Setting.%s', $lbl), $val);
    }

    function _callAllowClosing ( $data, $modelName, $fieldName = false, $format = 'Y-m', $redirect = true ) {
        $periode_document = $this->filterEmptyField($data, $modelName, $fieldName, false, array(
            'date' => 'Y-m',
        ));
        $lock_if_closing = Configure::read('__Site.Setting.lock_closing_bank');

        if( !empty($lock_if_closing) && !empty($periode_document) ) {
            $periode_closing = Configure::read('__Site.Closing.periode');

            if( $periode_document > $periode_closing ) {
                return true;
            } else {
                if( !empty($redirect) ) {
                    $this->redirectReferer(__('Transaksi utk periode tersebut telah Closing.'), 'error', '/');
                } else {
                    return false;
                }
            }
        } else {
            return true;
        }
    }
}
?>