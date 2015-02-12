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
                $tempTipeMotor['TtujTipeMotor']['tipe_motor_id'][$key] = $tipeMotor['tipe_motor_id'];
                $tempTipeMotor['TtujTipeMotor']['color_motor_id'][$key] = $tipeMotor['color_motor_id'];
                $tempTipeMotor['TtujTipeMotor']['qty'][$key] = $tipeMotor['qty'];
                $tempTipeMotor['TtujTipeMotor']['city_id'][$key] = $tipeMotor['city_id'];
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
                $tempPerlengkapan['TtujPerlengkapan'][$dataPerlengkapan['perlengkapan_id']] = $dataPerlengkapan['qty'];
            }

            unset($data['TtujPerlengkapan']);
            $data['TtujPerlengkapan'] = $tempPerlengkapan['TtujPerlengkapan'];
        }

        return $data;
    }

    function getDate ( $date ) {
    	$dtString = false;

    	if( !empty($date) ) {
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

    function getUangJalanTipeMotor ( $data ) {
        if( !empty($data['UangJalanTipeMotor']) ) {
            $tempTipeMotor = array();

            foreach ($data['UangJalanTipeMotor'] as $key => $tipeMotor) {
                $tempTipeMotor['UangJalanTipeMotor']['tipe_motor_id'][$key] = $tipeMotor['tipe_motor_id'];
                $tempTipeMotor['UangJalanTipeMotor']['uang_jalan_1'][$key] = $tipeMotor['uang_jalan_1'];
                $tempTipeMotor['UangJalanTipeMotor']['uang_kuli_muat'][$key] = $tipeMotor['uang_kuli_muat'];
                $tempTipeMotor['UangJalanTipeMotor']['uang_kuli_bongkar'][$key] = $tipeMotor['uang_kuli_bongkar'];
            }

            unset($data['UangJalanTipeMotor']);
            $data['UangJalanTipeMotor'] = $tempTipeMotor['UangJalanTipeMotor'];
        }

        return $data;
    }

    function toSlug($string) {
        return strtolower(Inflector::slug($string, '-'));
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
}
?>