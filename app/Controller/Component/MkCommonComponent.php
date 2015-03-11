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
                $tempGroupMotor['UangJalanTipeMotor']['group_motor_id'][$key] = $groupMotor['group_motor_id'];
                $tempGroupMotor['UangJalanTipeMotor']['uang_jalan_1'][$key] = $groupMotor['uang_jalan_1'];
            }

            unset($data['UangJalanTipeMotor']);
            $data['UangJalanTipeMotor'] = $tempGroupMotor['UangJalanTipeMotor'];
        }

        if( !empty($data['UangExtraGroupMotor']) ) {
            $tempGroupMotor = array();

            foreach ($data['UangExtraGroupMotor'] as $key => $groupMotor) {
                $tempGroupMotor['UangExtraGroupMotor']['group_motor_id'][$key] = $groupMotor['group_motor_id'];
                $tempGroupMotor['UangExtraGroupMotor']['uang_jalan_extra'][$key] = $groupMotor['uang_jalan_extra'];
                $tempGroupMotor['UangExtraGroupMotor']['min_capacity'][$key] = $groupMotor['min_capacity'];
            }

            unset($data['UangExtraGroupMotor']);
            $data['UangExtraGroupMotor'] = $tempGroupMotor['UangExtraGroupMotor'];
        }

        if( !empty($data['CommissionGroupMotor']) ) {
            $tempGroupMotor = array();

            foreach ($data['CommissionGroupMotor'] as $key => $groupMotor) {
                $tempGroupMotor['CommissionGroupMotor']['group_motor_id'][$key] = $groupMotor['group_motor_id'];
                $tempGroupMotor['CommissionGroupMotor']['commission'][$key] = $groupMotor['commission'];
            }

            unset($data['CommissionGroupMotor']);
            $data['CommissionGroupMotor'] = $tempGroupMotor['CommissionGroupMotor'];
        }

        if( !empty($data['CommissionExtraGroupMotor']) ) {
            $tempGroupMotor = array();

            foreach ($data['CommissionExtraGroupMotor'] as $key => $groupMotor) {
                $tempGroupMotor['CommissionExtraGroupMotor']['group_motor_id'][$key] = $groupMotor['group_motor_id'];
                $tempGroupMotor['CommissionExtraGroupMotor']['commission'][$key] = $groupMotor['commission'];
                $tempGroupMotor['CommissionExtraGroupMotor']['min_capacity'][$key] = $groupMotor['min_capacity'];
            }

            unset($data['CommissionExtraGroupMotor']);
            $data['CommissionExtraGroupMotor'] = $tempGroupMotor['CommissionExtraGroupMotor'];
        }

        if( !empty($data['AsdpGroupMotor']) ) {
            $tempGroupMotor = array();

            foreach ($data['AsdpGroupMotor'] as $key => $groupMotor) {
                $tempGroupMotor['AsdpGroupMotor']['group_motor_id'][$key] = $groupMotor['group_motor_id'];
                $tempGroupMotor['AsdpGroupMotor']['asdp'][$key] = $groupMotor['asdp'];
            }

            unset($data['AsdpGroupMotor']);
            $data['AsdpGroupMotor'] = $tempGroupMotor['AsdpGroupMotor'];
        }

        if( !empty($data['UangKawalGroupMotor']) ) {
            $tempGroupMotor = array();

            foreach ($data['UangKawalGroupMotor'] as $key => $groupMotor) {
                $tempGroupMotor['UangKawalGroupMotor']['group_motor_id'][$key] = $groupMotor['group_motor_id'];
                $tempGroupMotor['UangKawalGroupMotor']['uang_kawal'][$key] = $groupMotor['uang_kawal'];
            }

            unset($data['UangKawalGroupMotor']);
            $data['UangKawalGroupMotor'] = $tempGroupMotor['UangKawalGroupMotor'];
        }

        if( !empty($data['UangKeamananGroupMotor']) ) {
            $tempGroupMotor = array();

            foreach ($data['UangKeamananGroupMotor'] as $key => $groupMotor) {
                $tempGroupMotor['UangKeamananGroupMotor']['group_motor_id'][$key] = $groupMotor['group_motor_id'];
                $tempGroupMotor['UangKeamananGroupMotor']['uang_keamanan'][$key] = $groupMotor['uang_keamanan'];
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

        return $data_local;
    }

    function getNoInvoice ( $customer ) {
        return sprintf('%s%s', str_pad($customer['CustomerPattern']['last_number'], $customer['CustomerPattern']['min_digit'], '0', STR_PAD_LEFT), $customer['CustomerPattern']['pattern']);
    }
}
?>