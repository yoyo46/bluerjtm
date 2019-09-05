<?php
class CommonHelper extends AppHelper {
	var $helpers = array(
        'Html', 'Number', 'Paginator',
        'Form', 'Text', 'Time'
    );

    /**
    *
    *   mengambil data photo
    *   @param array $options : 
    *       - boolean thumb : true r false
    *       - string url : custom url
    *       - boolean fullpath : menyajikan full url ke photo
    *       - string size : mengetahui size photo
    *       - string src : nama folder
    *       - boolean watermark : penggunaan watermark
    *       - string forceTimestamp : penggunaan cache watermark
    *   @param array $parameters
    *   @param array $user : data user
    *   @param array $watermark
    *   @return string
    */
    public function photo_thumbnail($options = array(), $parameters = array(), $user = array(), $watermark = false ) {
        if(!isset($options['save_path']) || !$options['save_path']) {
            $options['save_path'] = Configure::read('__Site.profile_photo_folder');         
        }
        $defaultSize = 's';

        if( !empty($options['size']) ) {
            $dimensionList = Configure::read('__Site.dimension');
        }

        $options['thumb'] = isset($options['thumb'])?$options['thumb']:true;
        $options['url'] = (isset($options['url'])) ? $options['url'] : false;
        $options['fullpath'] = isset($options['fullpath'])?$options['fullpath']:false;
        $options['size']  = !empty($options['size'])?$options['size']:$defaultSize;
        $tempSrc  = $options['src'];
        $options['src']  = ( substr($tempSrc, 0, 1) != '/' )?'/'.$tempSrc:$tempSrc;
        $options['src']  = !empty($options['project_path'])?sprintf('/%s%s',$options['project_path'], $options['src']):$options['src'];
        $options['watermark'] = isset($options['watermark'])?$options['watermark']:false;
        $options['forceTimestamp'] = isset($options['forceTimestamp'])?$options['forceTimestamp']:false;

        if( array_key_exists($options['size'], Configure::read('__Site.dimension_profile')) ) {
            $dimensionList = Configure::read('__Site.dimension_profile');
        } else if( array_key_exists($options['size'], Configure::read('__Site.dimension')) ) {
            $dimensionList = Configure::read('__Site.dimension');
        } else {
            $options['size'] = 's';
        }

        if( !empty($options['cache_view_path']) && !empty($options['thumbnail_view_path']) ) {
            $cache_view_path = $options['cache_view_path'];
            $thumbnail_view_path = Configure::read('__Site.thumbnail_view_path');
        } else {
            $cache_view_path = Configure::read('__Site.cache_view_path');
            $thumbnail_view_path = Configure::read('__Site.thumbnail_view_path');
            $options['thumbnail_view_path'] = $thumbnail_view_path;
        }

        list($options['w'], $options['h']) = explode('x', $dimensionList[$options['size']]);
        $dimension = $options['size'];
        $fullsize = Configure::read('__Site.fullsize');
        $fullThumbnailPath = $thumbnail_view_path.DS.$options['save_path'].DS.$fullsize.DS;
        $thumbnailPath = sprintf('/%s/%s%s', $options['save_path'], $dimension, $options['src']);
        $oldSourcePath = sprintf('%s%s%s%s%s', Configure::read('__Site.upload_path'), DS, $options['save_path'], DS, $options['src']);
        $fullPath = sprintf('/%s/%s%s', $options['save_path'], $fullsize, $options['src']);
        $pathToImages = sprintf('%s%s%s', Configure::read('__Site.upload_path'), DS, $options['save_path']);

        if( $options['thumb'] ) {
            $srcImg = $thumbnailPath;
        } else {
            $srcImg = $fullPath;
            $dimension = $fullsize;
        }

        $errorImg = sprintf('%s/errors/error_%s.jpg', $cache_view_path, $dimension);
        $realThumbnailPath = sprintf('%s%s', $thumbnail_view_path, $srcImg);
        $realThumbnailPath = str_replace('/', DS, $realThumbnailPath);
        $timeCache = false;

        if( isset($options['ext']) ) {
            $options['ext'] = $options['ext'];
        } else {
            $options['ext'] = false;
        }

        if( !empty($options['ext']) && $options['ext'] == 'pdf' ) {
            $thumbnail['src'] = sprintf('%s/errors/pdf_%s.png', $cache_view_path, $dimension);
        } else if( substr($options['src'], 0, 4) != 'http' ) {
            $thumbnail = array(
                'src' => sprintf('%s%s', $cache_view_path, $srcImg),
            );

            if( file_exists($realThumbnailPath) ) {
                $timeCache = filemtime($realThumbnailPath);
            }

            if( !empty($tempSrc) && substr($tempSrc, 0, 1) != '/' && file_exists($oldSourcePath) && !file_exists($realThumbnailPath) ) {
                $this->createOldThumbs($pathToImages, $oldSourcePath, $options['src'], $dimension, $options);

                if( !file_exists($realThumbnailPath) ) {
                    $thumbnail['src'] = $errorImg;
                }
            } else if( substr($tempSrc, 0, 1) == '/' && ( !file_exists($realThumbnailPath) || empty($tempSrc) ) ) {
                $errorPath = $cache_view_path.'/'.$dimension;

                if( !empty($options['src']) && file_exists($pathToImages.$options['src']) ) {
                    $timeCache = filemtime($pathToImages.$options['src']);
                    $this->createThumbs($pathToImages, $realThumbnailPath, $options['src'], $dimension, $options);
                } else if( !empty($user) ) {
                    if( isset($user['User']['gender_id']) && $user['User']['gender_id'] == 2 ) 
                        $thumbnail['src'] = sprintf('%s/errors/lady_%s.jpg', $cache_view_path, $dimension);
                    else $thumbnail['src'] = sprintf('%s/errors/gent_%s.jpg', $cache_view_path, $dimension);
                } else {
                    $thumbnail['src'] = $errorImg;
                }
            } else if( !file_exists($realThumbnailPath) || empty($tempSrc) ) {
                if( !empty($user) ) {
                    if( isset($user['User']['gender_id']) && $user['User']['gender_id'] == 2 ) 
                        $thumbnail['src'] = sprintf('%s/errors/lady_%s.jpg', $cache_view_path, $dimension);
                    else $thumbnail['src'] = sprintf('%s/errors/gent_%s.jpg', $cache_view_path, $dimension);
                } else {
                    $thumbnail['src'] = $errorImg;
                }
            }
        } else {
            $thumbnail = array(
                'src' => $options['src'],
            );
        }

        if( !empty($timeCache) && $options['forceTimestamp'] ) {
            if( is_numeric($options['forceTimestamp']) ) {
                $timeCache = md5($options['forceTimestamp']);
            } else {
                $timeCache = md5($timeCache);
            }
            
            $thumbnail['src'] = sprintf('%s?%s', $thumbnail['src'], $timeCache);
        }

        if( $options['fullpath'] ) {
            $thumbnail['src'] = FULL_BASE_URL.$thumbnail['src'];
        }
        
        if( $options['url'] ) {
            return $thumbnail['src'];
        } else {
            $image = $thumbnail['src'];
            if( !empty($options['ext']) && $options['ext'] == 'pdf' ) {
                $image = sprintf('%s/errors/pdf_%s.png', $cache_view_path, $dimension);
            }
            return $this->Html->image($image, $parameters);
        }
    }

    /**
    *
    *   membuat thumbnail dari photo
    *   @param string $pathToImages : :path ke file photo
    *   @param string $pathToThumbs : :path ke file photo thumb
    *   @param string $fileName : nama file
    *   @param array $dimension : dimensi gambar
    *   @param array $options
    *   @return string
    */
    function createThumbs( $pathToImages, $pathToThumbs, $fileName, $dimension, $options )  {
        $thumbWidth = $options['w'];
        $thumbHeight = $options['h'];
        $dir = opendir( $pathToImages );

        $info = pathinfo($pathToImages . $fileName);
        $ext = strtolower($info['extension']);
        $fname = sprintf('%s.%s', $info['filename'], strtolower($info['extension']));
        $pathMakeDir = sprintf('%s%s%s%s%s', $options['thumbnail_view_path'], DS, $options['save_path'], DS, $dimension);
        $srcDate = explode('/', $options['src']);

        if( !file_exists($pathMakeDir) ) {
            mkdir($pathMakeDir, 0755, true);
        }

        if( !empty($options['project_path']) ) {
            $pathMakeDir = $pathMakeDir.DS.$options['project_path'];
            if( !file_exists($pathMakeDir) ) {
                mkdir($pathMakeDir, 0755, true);
            }

            $year = !empty($srcDate[2])?$srcDate[2]:date('Y');
            $month = !empty($srcDate[3])?$srcDate[3]:date('m');
        } else {
            $year = !empty($srcDate[1])?$srcDate[1]:date('Y');
            $month = !empty($srcDate[2])?$srcDate[2]:date('m');
        }

        $tempName = substr($fileName, 1);
        $dirNameArr = explode('/', $tempName);
        $folder_sub_path = '';

        if( count($dirNameArr) >= 3 ) {
            $sub_part = explode('-',$fileName);
            
            if(!empty($sub_part[1])) {
                $folder_sub_path1 = substr($sub_part[1], 0, 1);
            }
            $folder_sub_path = $folder_sub_path1;
        }

        $subDir = $this->makeDir( false, $pathMakeDir, $year, $month, (string)$folder_sub_path );
        $pathMakeDir .= DS.$subDir.$fname;
        $pathMakeDir = str_replace('/', DS, $pathMakeDir);
        $thefile = $pathToImages . $fileName;
        
        if (!is_file($pathMakeDir) === true) {
            if (copy($thefile, $pathMakeDir) === false) {
                echo "Failed to copy $src... Permissions correct?\n";
            }
        }

        if( $options['thumb'] ) {
            App::import('Vendor', 'Thumb', array('file' => 'Thumb'.DS.'ThumbLib.inc.php'));
            $thumb =& $thumb;
            $thumb = PhpThumbFactory::create($pathMakeDir);
            $imgCrop = $thumb->adaptiveResize($thumbWidth, $thumbHeight);

            if($ext == "png"){
                @imagepng($imgCrop->workingImageCopy, $pathMakeDir, 9);
            } elseif($ext == "jpg" || $ext == "jpeg") {
                @imagejpeg($imgCrop->workingImageCopy, $pathMakeDir, 90);
            } elseif($ext == "gif") {
                @imagegif($imgCrop->workingImageCopy, $pathMakeDir);
            }
        }
        closedir( $dir );
    }

    /**
    *
    *   membuat thumbnail dari photo
    *   @param string $pathToImages : :path ke file photo
    *   @param string $pathToThumbs : :path ke file photo thumb
    *   @param string $fileName : nama file
    *   @param array $dimension : dimensi gambar
    *   @param array $options
    *   @return string
    */
    function createOldThumbs( $pathToImages, $pathToThumbs, $fileName, $dimension, $options )  {
        $thumbWidth = $options['w'];
        $thumbHeight = $options['h'];
        $dir = opendir( $pathToImages );

        $info = pathinfo($pathToImages . $fileName);
        $ext = strtolower($info['extension']);
        $pathMakeDir = sprintf('%s%s%s%s%s', $options['thumbnail_view_path'], DS, $options['save_path'], DS, $dimension);

        if( !file_exists($pathMakeDir) ) {
            mkdir($pathMakeDir, 0755, true);
        }

        $pathMakeDir .= DS.$options['src'];

        copy($pathToImages . DS.$fileName, $pathMakeDir);

        if( $options['thumb'] ) {
            App::import('Vendor', 'thumb', array('file' => 'thumb'.DS.'ThumbLib.inc.php'));
            $thumb =& $thumb;
            $thumb = PhpThumbFactory::create($pathMakeDir);
            $imgCrop = $thumb->adaptiveResize($thumbWidth, $thumbHeight);

            if($ext == "png"){
                imagepng($imgCrop->workingImageCopy, $pathMakeDir, 9);
            } elseif($ext == "jpg" || $ext == "jpeg") {
                imagejpeg($imgCrop->workingImageCopy, $pathMakeDir, 90);
            } elseif($ext == "gif") {
                imagegif($imgCrop->workingImageCopy, $pathMakeDir);
            }
        }
        closedir( $dir );
    }

    /**
    *
    *   membuat direktori
    *   @param string $upload_path : :path ke file photo
    *   @param string $thumbnailPath : :path ke file photo thumb
    *   @param string $year : tahun di buat thumb
    *   @param string $month : bulan di buat thumb
    *   @return string
    */
    function makeDir( $upload_path = false, $thumbnailPath = false, $year = false, $month = false, $folder_sub_path = '') {
        $year = !empty($year)?$year:date('Y');
        $month = !empty($month)?$month:date('m');

        if( !empty($upload_path) ) {
            $yearDir = $upload_path.date('Y').DS;
            $monthDir = $yearDir.date('m').DS;

            if( !file_exists($yearDir) ) {
                mkdir($yearDir, 0755, true);
            }
            if( !file_exists($monthDir) ) {
                mkdir($monthDir, 0755, true);
            }

            if($folder_sub_path != '') {
                $subDir = $monthDir.$folder_sub_path.DS;
                if( !file_exists($subDir) ) {
                    mkdir($subDir, 0755, true);
                }
            }
        }
        
        if( !empty($thumbnailPath) ) {
            $yearFullsizeDir = str_replace('/', DS, $thumbnailPath.DS.$year.DS);
            $monthFullsizeDir = $yearFullsizeDir.DS.$month.DS;

            if( !file_exists($yearFullsizeDir) ) {
                mkdir($yearFullsizeDir, 0755, true);
            }
            if( !file_exists($monthFullsizeDir) ) {
                mkdir($monthFullsizeDir, 0755, true);
            }
            $FullsizeDir = str_replace('/', DS, $thumbnailPath.DS);
            if($folder_sub_path != '') {
                $subFullsizeDir = $monthFullsizeDir.$folder_sub_path.DS;
                if( !file_exists($subFullsizeDir) ) {
                    mkdir($subFullsizeDir, 0755, true);
                }
            }
        }

        if($folder_sub_path != '') {
            return sprintf('%s/%s/%s/', $year, $month, $folder_sub_path);
        } else {
            return sprintf('%s/%s/', $year, $month);
        }

    }

	function customDate($dateString, $format = 'd F Y', $result = '') {
        if( !empty($dateString) && $dateString != '0000-00-00' && $dateString != '0000-00-00 00:00:00' ) {
            $result = date($format, strtotime($dateString));
        }

		return $result;
	}

    function combineDate( $fromDate, $toDate, $format = 'd' ) {
        if( $this->customDate($fromDate, $format) == $this->customDate($toDate, $format) ) {
            $result = $this->customDate($fromDate, $format);
        } else {
            if( $format == 'M Y' ) {
                if( $this->customDate($fromDate, 'Y') == $this->customDate($toDate, 'Y') ) {
                    $result = sprintf('%s - %s %s', $this->customDate($fromDate, 'M'), $this->customDate($toDate, 'M'), $this->customDate($fromDate, 'Y'));
                } else {
                    $result = sprintf('%s - %s', $this->customDate($fromDate, $format), $this->customDate($toDate, $format));
                }
            } else {
                $result = sprintf('%s - %s', $this->customDate($fromDate, $format), $this->customDate($toDate, $format));
            }
        }

        return $result;
    }

	/**
	*
	*	filterisasi content tag
	*
	*	@param string string : string
	*	@return string
	*/
	function safeTagPrint($string){
        if( is_string($string) ) {
		  return strip_tags($string);
        } else {
          return $string;
        }
	}

	function generateCoaTree ( $coas, $level = false, $parent = false ) {
		$dataTree = '<ul>';
        if( !empty($coas) ) {
            foreach ($coas as $key => $coa) {
				$dataTree .= '<li class="parent_li">';
                $coa_title = '';
                if(!empty($coa['Coa']['code'])){
                    $codeCoa = $coa['Coa']['code'];

                    if( !empty($parent['Coa']['code']) ) {
                        $codeCoa = sprintf('%s-%s', $parent['Coa']['code'], $codeCoa);
                    }

                    $coa_title = $this->Html->tag('label', $codeCoa);
                }
                $coa_title .= $coa['Coa']['name'];
				$dataTree .= $this->Html->tag('span', $coa_title, array(
                    'title' => $coa_title,
                ));

                $dataTree .= $this->Html->link('<i class="fa fa-plus-circle"></i>', array(
                    'controller' => 'settings',
                    'action' => 'coa_add',
                    $coa['Coa']['id'],
                ), array(
                    'escape' => false,
                    'class' => 'bg-green'
                ));

                $dataTree .= $this->Html->link('<i class="fa fa-pencil-square-o"></i>', array(
                    'controller' => 'settings',
                    'action' => 'coa_edit',
                    $coa['Coa']['id'],
                    $coa['Coa']['parent_id'],
                ), array(
                    'escape' => false,
                    'class' => 'bg-primary',
                    'title' => 'edit'
                ));

                $dataTree .= $this->Html->link('<i class="fa fa-minus-circle"></i>', array(
                    'controller' => 'settings',
                    'action' => 'coa_toggle',
                    $coa['Coa']['id'],
                ), array(
                    'escape' => false,
                    'class' => 'bg-red'
                ), __('Anda yakin ingin menghapus COA ini ?'));

                if( !empty($coa['children']) ) {
                    $parent['Coa'] = $coa['Coa'];
                	$child = $coa['children'];
                    $level = $coa['Coa']['level'];
                	$dataTree .= $this->generateCoaTree($child, $level, $parent);
                }

				$dataTree .= '</li>';
            }
        }
		$dataTree .= '</ul>';
		return $dataTree;
	}

    function pathDirTcpdf () {
        $year = date('Y');
        $month = date('m');
        $day = date('d');
        $path = APP.'Vendor/tcpdf/'.$year;
        
        if( !file_exists($path) ) {
            mkdir($path);
        }

        $path .= '/'.$month;
        if( !file_exists($path) ) {
            mkdir($path);
        }

        return $path;
    }

    
    function toSlug($string, $separator = '-') {
        if( is_string($string) ) {
            return strtolower(Inflector::slug($string, $separator));
        } else {
            return $string;
        }
    }

    function getSorting ( $model = false,  $label = false, $is_print = false, $sorting = true, $options = array() ) {
        $named = $this->params['named'];

        if( !is_array($options) ) {
            $options = array();
        }

        if( !empty($sorting) && !empty($model) && $this->Paginator->hasPage() && empty($is_print) ) {
            return $this->Paginator->sort($model, $label, array_merge($options, array(
                'escape' => false
            )));
        } else {
            return $label;
        }
    }

    function calcFloat ( $total, $float, $format = false ) {
        $result = 0;

        if(!empty($total) && !empty($float)){
            $result = $total * ($float / 100);
        }

        switch ($format) {
            case 'price':
                $result = $this->getFormatPrice($result);
                break;
        }
        
        return $result;
    }

    function konversi($x){
        $x = abs($x);
        $angka = array ("","satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        $temp = "";

        if($x < 12){
            $temp = " ".$angka[$x];
        }else if($x<20){
            $temp = $this->konversi($x - 10)." belas";
        }else if ($x<100){
            $temp = $this->konversi($x/10)." puluh". $this->konversi($x%10);
        }else if($x<200){
            $temp = " seratus".$this->konversi($x-100);
        }else if($x<1000){
            $temp = $this->konversi($x/100)." ratus".$this->konversi($x%100);   
        }else if($x<2000){
            $temp = " seribu".$this->konversi($x-1000);
        }else if($x<1000000){
            $temp = $this->konversi($x/1000)." ribu".$this->konversi($x%1000);   
        }else if($x<1000000000){
            $temp = $this->konversi($x/1000000)." juta".$this->konversi($x%1000000);
        }else if($x<1000000000000){
            $temp = $this->konversi($x/1000000000)." milyar".$this->konversi($x%1000000000);
        }

        return $temp;
    }
  
    function terbilang($x){
        if($x<0){
            $hasil = "minus ".trim($this->konversi(x));
        }else{
            $hasil = trim($this->konversi($x));
            $hasil = ucwords(sprintf(__('%s Rupiah'), $hasil));
        }

        $hasil = $hasil;
        return $hasil;  
    }

    function getDataSetting ( $value, $index, $options = false ) {
        if( !empty($value['Setting'][$index]) ) {
            if( $index == 'logo' ) {
                return $this->photo_thumbnail(array(
                    'save_path' => Configure::read('__Site.profile_photo_folder'), 
                    'src' => $value['Setting'][$index], 
                    'thumb'=>false,
                    'fullPath'=>true,
                ), $options);
            } else {
                return $value['Setting'][$index];
            }
        } else {
            return sprintf(__('Klik %s untuk Pengaturan'), $this->Html->link(__('Disini'), array(
                'controller' => "settings",
                'action' => 'index',
            ), array(
                'target' => 'blank'
            )));
        }
    }

    function getNoRef ( $id, $length = 5, $op = '0', $position = STR_PAD_LEFT ) {
        return str_pad($id, $length, $op, $position);
    }

    function getInvoiceStatus ( $data ) {
        $result = array(
            'class' => 'label label-default',
            'text' => __('Unpaid'),
            'void_date' => '',
        );

        if(!empty($data['Invoice']['is_canceled'])){
            $result = array(
                'class' => 'label label-danger',
                'text' => __('Void'),
                'void_date' => '<br>'.$this->customDate($data['Invoice']['canceled_date'], 'd M Y'),
            );
        }else{
            if( empty($data['Invoice']['complete_paid']) && !empty($data['Invoice']['paid']) ){
                $result = array(
                    'class' => 'label label-primary',
                    'text' => __('Half Paid'),
                    'void_date' => '',
                );
            } else if(!empty($data['Invoice']['complete_paid'])){
                $result = array(
                    'class' => 'label label-success',
                    'text' => __('Paid'),
                    'void_date' => '',
                );
            }
        }

        return $result;
    }

    function getInvoiceStatusContent ( $data ) {
        $statusContent = $this->Html->tag('span', sprintf(__('Unpaid : %s'), $data['InvoiceUnpaid']), array(
            'class' => 'label label-default',
            'style' => 'background-color:#f5f5f5;color:#333;',
        ));
        $statusContent .= $this->Html->tag('span', sprintf(__('Half Paid : %s'), $data['InvoiceHalfPaid']), array(
            'class' => 'label label-primary',
            'style' => 'background-color:#d9edf7;color:#333;',
        ));
        $statusContent .= $this->Html->tag('span', sprintf(__('Paid : %s'), $data['InvoicePaid']), array(
            'class' => 'label label-success',
            'style' => 'background-color:#dff0d8;color:#333;',
        ));
        $statusContent .= $this->Html->tag('span', sprintf(__('Void : %s'), $data['InvoiceVoid']), array(
            'class' => 'label label-danger',
            'style' => 'background-color:#f2dede;color:#333;',
        ));

        return $this->Html->tag('div', $statusContent, array(
            'class' => 'status-content'
        ));
    }

    function fullNameCustomer ( $data, $modelName = 'Customer', $position = 'first' ) {
        $resultCode = '';
        $resultName = '';

        if( !empty($data[$modelName]['code']) ) {
            $resultCode = $data[$modelName]['code'];
        }
        if( !empty($data[$modelName]['name']) ) {
            $resultName = $data[$modelName]['name'];
        }

        switch ($position) {
            case 'last':
                return sprintf('%s - %s', $resultName, $resultCode);
                break;
            
            default:
                return sprintf('%s - %s', $resultCode, $resultName);
                break;
        }
    }

    function getRowCoa ( $coas, $parent = false, $trucks = false ) {
        $dataTree = '';
        if( !empty($coas) ) {
            foreach ($coas as $key => $coa) {
                $id = $coa['Coa']['id'];
                $coa_title = '';
                $codeCoa = '-';
                $uuid = sprintf('truck-%s', String::uuid());

                if(!empty($coa['Coa']['with_parent_code'])){
                    $codeCoa = $coa['Coa']['with_parent_code'];
                } else if(!empty($coa['Coa']['code'])){
                    $codeCoa = $coa['Coa']['code'];

                    if( !empty($parent['Coa']['code']) ) {
                        $codeCoa = sprintf('%s-%s', $parent['Coa']['code'], $codeCoa);
                    }
                }
                $coa_title = $coa['Coa']['name'];

                // $content  = $this->Html->tag('td', $this->Form->checkbox('CashBankDetail.coa_id.', array(
                //     'class' => 'check-option',
                //     'data-allow-multiple' => 'true',
                //     'value' => $id,
                // )), array(
                //     'class' => 'checkbox-detail'
                // ));
                $content  = $this->Html->tag('td', $codeCoa.$this->Form->input('CashBankDetail.coa_id.', array(
                    'type' => 'hidden',
                    'value' => $id
                )));
                $content .= $this->Html->tag('td', $coa_title);
                
                $truck_form = $this->Form->input('CashBankDetail.nopol.', array(
                    'type' => 'text',
                    'class' => 'form-control',
                    'label' => false,
                    'div' => false,
                    'required' => false,
                    'readonly' => true,
                    'id' => $uuid,
                )).$this->Html->link($this->icon('plus-square'), array(
                    'controller'=> 'ajax', 
                    'action' => 'getTrucks',
                    'cashbank',
                    $uuid,
                    'admin' => false,
                ), array(
                    'escape' => false,
                    'allow' => true,
                    'class' => 'ajaxModal browse-docs',
                    'title' => __('Data Truk'),
                    'data-action' => 'browse-form',
                    'data-change' => $uuid,
                ));
                $content .= $this->Html->tag('td', $truck_form, array(
                    'class' => 'action-search pick-truck hide'
                ));

                // $credit_form = $this->Form->input('CashBankDetail.credit.', array(
                //     'type' => 'text',
                //     'class' => 'form-control input_price',
                //     'label' => false,
                //     'div' => false,
                //     'required' => false,
                // ));
                // $content .= $this->Html->tag('td', $credit_form, array(
                //     'class' => 'action-search hide'
                // ));

                $debit_form = $this->Form->input('CashBankDetail.total.', array(
                    'type' => 'text',
                    'class' => 'form-control input_price_coma sisa-amount text-right',
                    'label' => false,
                    'div' => false,
                    'required' => false,
                ));
                $content .= $this->Html->tag('td', $debit_form, array(
                    'class' => 'action-search hide'
                ));

                $content .= $this->Html->tag('td', $this->Html->link('<i class="fa fa-times"></i> Hapus', 'javascript:', array(
                    'class' => 'delete-custom-field btn btn-danger btn-xs',
                    'escape' => false,
                    'action_type' => 'cashbank_first'
                )), array(
                    'class' => 'action-search hide'
                ));
                
                $dataTree .= $this->Html->tag('tr', $content, array(
                    'class' => 'child-search click-child child-search-'.$id,
                    'rel' => $id
                ));

                if( !empty($coa['children']) ) {
                    $parent['Coa'] = $coa['Coa'];
                    $child = $coa['children'];
                    $dataTree .= $this->getRowCoa($child, $parent);
                }
            }
        }
        return $dataTree;
    }

    function getReceiverType ( $type ) {
        $receiver_type = false;

        if( $type == 'Driver' ) {
            $receiver_type = __('(Supir)');
        } else if( $type == 'Employe' ) {
            $receiver_type = __('(Karyawan)');
        } else {
            $receiver_type = sprintf('(%s)', ucwords($type));
        }

        return $receiver_type;
    }

    function printDataTree($data, $level){
        $coa_title = '';
        $coa_id = $data['Coa']['id'];
        if(!empty($data['Coa']['code'])){
            $codeCoa = $data['Coa']['code'];

            if( !empty($parent['Coa']['code']) ) {
                $codeCoa = sprintf('%s-%s', $parent['Coa']['code'], $codeCoa);
            }

            $coa_title = $this->Html->tag('label', $codeCoa);
        }
        $coa_title .= $data['Coa']['name'];
        $dataTree = $this->Html->tag('span', $coa_title, array(
            'title' => $coa_title,
        ));
        $dataTree .= $this->Html->link('<i class="fa fa-plus-circle"></i>', array(
            'controller' => 'settings',
            'action' => 'coa_add',
            $coa_id,
        ), array(
            'escape' => false,
            'class' => 'bg-green'
        ));

        $dataTree .= $this->Html->link('<i class="fa fa-pencil-square-o"></i>', array(
            'controller' => 'settings',
            'action' => 'coa_edit',
            $coa_id,
            $data['Coa']['parent_id'],
        ), array(
            'escape' => false,
            'class' => 'bg-primary',
            'title' => 'edit'
        ));
        
        // $dataTree .= $this->Html->link('<i class="fa fa-minus-circle"></i>', array(
        //     'controller' => 'settings',
        //     'action' => 'coa_toggle',
        //     $coa_id,
        // ), array(
        //     'escape' => false,
        //     'class' => 'bg-red'
        // ), __('Anda yakin ingin menghapus COA ini ?'));

        return $dataTree;
    }

    function productCategoryTree($data){
        $id = $data['ProductCategory']['id'];
        $title = $data['ProductCategory']['name'];
        $dataTree = $this->Html->tag('span', $title, array(
            'title' => $title,
        ));
        $dataTree .= $this->Html->link('<i class="fa fa-plus-circle"></i>', array(
            'action' => 'category_add',
            $id,
        ), array(
            'escape' => false,
            'class' => 'bg-green'
        ));

        $dataTree .= $this->Html->link('<i class="fa fa-pencil-square-o"></i>', array(
            'action' => 'category_edit',
            $id,
            $data['ProductCategory']['parent_id'],
        ), array(
            'escape' => false,
            'class' => 'bg-primary',
            'title' => 'edit'
        ));
        
        $dataTree .= $this->Html->link('<i class="fa fa-minus-circle"></i>', array(
            'action' => 'category_toggle',
            $id,
        ), array(
            'escape' => false,
            'class' => 'bg-red'
        ), __('Anda yakin ingin menghapus grup barang ini ?'));

        return $dataTree;
    }

    function recallProduCategory($data){
        if( !empty($data) ) {
            echo '<ul>';
            foreach ($data as $key => $value_1) {
                echo '<li class="parent_li">';
                echo $this->productCategoryTree($value_1);
                if(!empty($value_1['children'])){
                    $this->recallProduCategory($value_1['children']);
                }
                echo '</li>';
            }
            echo '</ul>';
        }
    }

    function getBiayaTtuj ( $ttuj, $data_type, $format_currency = true, $tampilkan_sisa = true, $modelName = 'Ttuj' ) {
        $total = 0;
        $biaya = 0;

        switch ($data_type) {
            case 'uang_kuli_muat':
                $biaya = $uang_kuli_muat = !empty($ttuj[$modelName]['uang_kuli_muat'])?$ttuj[$modelName]['uang_kuli_muat']:0;
                $uang_kuli_muat_dibayar = !empty($ttuj['uang_kuli_muat_dibayar'])?$ttuj['uang_kuli_muat_dibayar']:0;

                $total = $uang_kuli_muat - $uang_kuli_muat_dibayar;
                break;
            case 'uang_kuli_bongkar':
                $biaya = $uang_kuli_bongkar = !empty($ttuj[$modelName]['uang_kuli_bongkar'])?$ttuj[$modelName]['uang_kuli_bongkar']:0;
                $uang_kuli_bongkar_dibayar = !empty($ttuj['uang_kuli_bongkar_dibayar'])?$ttuj['uang_kuli_bongkar_dibayar']:0;

                $total = $uang_kuli_bongkar - $uang_kuli_bongkar_dibayar;
                break;
            case 'asdp':
                $biaya = $asdp = !empty($ttuj[$modelName]['asdp'])?$ttuj[$modelName]['asdp']:0;
                $asdp_dibayar = !empty($ttuj['asdp_dibayar'])?$ttuj['asdp_dibayar']:0;

                $total = $asdp - $asdp_dibayar;
                break;
            case 'uang_kawal':
                $biaya = $uang_kawal = !empty($ttuj[$modelName]['uang_kawal'])?$ttuj[$modelName]['uang_kawal']:0;
                $uang_kawal_dibayar = !empty($ttuj['uang_kawal_dibayar'])?$ttuj['uang_kawal_dibayar']:0;

                $total = $uang_kawal - $uang_kawal_dibayar;
                break;
            case 'uang_keamanan':
                $biaya = $uang_keamanan = !empty($ttuj[$modelName]['uang_keamanan'])?$ttuj[$modelName]['uang_keamanan']:0;
                $uang_keamanan_dibayar = !empty($ttuj['uang_keamanan_dibayar'])?$ttuj['uang_keamanan_dibayar']:0;

                $total = $uang_keamanan - $uang_keamanan_dibayar;
                break;
            case 'commission':
                $biaya = $commission = !empty($ttuj[$modelName]['commission'])?$ttuj[$modelName]['commission']:0;
                // $commission_extra = !empty($ttuj[$modelName]['commission_extra'])?$ttuj[$modelName]['commission_extra']:0;
                $commission_dibayar = !empty($ttuj['commission_dibayar'])?$ttuj['commission_dibayar']:0;

                // $total = $commission + $commission_extra - $commission_dibayar;
                $total = $commission - $commission_dibayar;
                break;
            case 'uang_jalan_2':
                $biaya = $uang_jalan_2 = !empty($ttuj[$modelName]['uang_jalan_2'])?$ttuj[$modelName]['uang_jalan_2']:0;
                $uang_jalan_2_dibayar = !empty($ttuj['uang_jalan_2_dibayar'])?$ttuj['uang_jalan_2_dibayar']:0;

                $total = $uang_jalan_2 - $uang_jalan_2_dibayar;
                break;
            case 'uang_jalan_extra':
                $biaya = $uang_jalan_extra = !empty($ttuj[$modelName]['uang_jalan_extra'])?$ttuj[$modelName]['uang_jalan_extra']:0;
                $uang_jalan_extra_dibayar = !empty($ttuj['uang_jalan_extra_dibayar'])?$ttuj['uang_jalan_extra_dibayar']:0;

                $total = $uang_jalan_extra - $uang_jalan_extra_dibayar;
                break;
            case 'commission_extra':
                $biaya = $commission_extra = !empty($ttuj[$modelName]['commission_extra'])?$ttuj[$modelName]['commission_extra']:0;
                $commission_extra_dibayar = !empty($ttuj['commission_extra_dibayar'])?$ttuj['commission_extra_dibayar']:0;

                $total = $commission_extra - $commission_extra_dibayar;
                break;
            
            default:
                $biaya = $uang_jalan_1 = !empty($ttuj[$modelName]['uang_jalan_1'])?$ttuj[$modelName]['uang_jalan_1']:0;
                // $uang_jalan_2 = !empty($ttuj[$modelName]['uang_jalan_2'])?$ttuj[$modelName]['uang_jalan_2']:0;
                // $uang_jalan_extra = !empty($ttuj[$modelName]['uang_jalan_extra'])?$ttuj[$modelName]['uang_jalan_extra']:0;
                $uang_jalan_dibayar = !empty($ttuj['uang_jalan_dibayar'])?$ttuj['uang_jalan_dibayar']:0;

                // $total = $uang_jalan_1 + $uang_jalan_2 + $uang_jalan_extra - $uang_jalan_dibayar;
                $total = $uang_jalan_1 - $uang_jalan_dibayar;
                break;
        }

        if( !$tampilkan_sisa ) {
            $total = $biaya;
        }

        if( $format_currency ) {
            return $this->Number->currency($total, '', array(
                'places' => 0,
                'negative' => '-',
            ));
        } else {
            return $total;
        }
    }

    function _callLabelBiayaTtuj ( $type ) {
        switch ($type) {
                case 'asdp':
                    return __('Uang Penyebrangan');
                    break;
                case 'uang_jalan':
                    return __('Uang Jalan 1');
                    break;
                case 'commission':
                    return __('Komisi');
                    break;
                case 'commission_extra':
                    return __('Komisi Extra');
                    break;
                default:
                    return ucwords(str_replace('_', ' ', $type));
                    break;
            }
    }

    function _allowShowColumn ( $modelName, $fieldName ) {
        $_allowShow = isset($this->request->data[$modelName][$fieldName])?$this->request->data[$modelName][$fieldName]:true;
        $result = true;

        if( empty($_allowShow) ) {
            $result = false;
        }

        return $result;
    }

    function _generateShowHideColumn ( $dataColumns, $data_type, $is_print = false, $options = false ) {
        $result = false;
        // Global Attribut
        $_class = !empty($options['class'])?$options['class']:false;
        $_style = !empty($options['style'])?$options['style']:false;

        if( !empty($dataColumns) ) {
            $childArr = array();

            foreach ($dataColumns as $key_field => $dataColumn) {
                $field_model = !empty($dataColumn['field_model'])?$dataColumn['field_model']:false;
                $sorting = isset($dataColumn['sorting'])?$dataColumn['sorting']:true;

                // Get Data Model
                if( is_array($field_model) ) {
                    $arrModel = $field_model;
                    $field_model = $this->filterEmptyField($field_model, 'name');
                    $field_model_options = $this->_callUnset(array(
                        'name',
                    ), $arrModel);
                } else {
                    $field_model_options = array();
                }

                $data_model = explode('.', $field_model);
                $data_model = array_filter($data_model);
                if( !empty($data_model) ) {
                    list($modelName, $fieldName) = $data_model;
                } else {
                    $modelName = false;
                    $fieldName = false;
                }

                $width = !empty($dataColumn['width'])?$dataColumn['width']:false;
                $style = !empty($dataColumn['style'])?$dataColumn['style']:false;
                $name = !empty($dataColumn['name'])?$dataColumn['name']:false;
                $display = isset($dataColumn['display'])?$dataColumn['display']:true;
                $child = !empty($dataColumn['child'])?$dataColumn['child']:false;
                $rowspan = !empty($dataColumn['rowspan'])?$dataColumn['rowspan']:false;
                $colspan = !empty($dataColumn['colspan'])?$dataColumn['colspan']:false;
                $class = !empty($dataColumn['class'])?$dataColumn['class']:false;
                $fix_column = !empty($dataColumn['fix_column'])?$dataColumn['fix_column']:false;
                $data_options = !empty($dataColumn['data-options'])?$dataColumn['data-options']:false;
                $align = !empty($dataColumn['align'])?$dataColumn['align']:false;
                $mainalign = !empty($dataColumn['mainalign'])?$dataColumn['mainalign']:false;
                $rel = !empty($dataColumn['rel'])?$dataColumn['rel']:false;
                $content = false;
                $addClass = '';

                if( !empty($_style) ) {
                    $style .= $_style;
                }

                if( !empty($display) ) {
                    $checked = true;
                } else {
                    $checked = false;
                    $style .= 'display:none;';
                }

                switch ($data_type) {
                    case 'show-hide':
                        $checkbox = $this->Form->checkbox($field_model, array(
                            'data-field' => $key_field,
                            'checked' => $checked,
                        ));
                        $content = $this->Html->tag('li', $this->Html->tag('div', $this->Html->tag('label', $checkbox.$name), array(
                            'class' => 'checkbox',
                        )));
                        break;
                    
                    default:
                        // Set Allow Show Column
                        $allowShow = $this->_allowShowColumn($modelName, $fieldName);

                        if( !empty($allowShow) ) {
                            // Colspan
                            if( !empty($child) && !isset($dataColumn['colspan']) ) {
                                $colspan = count($child);
                            }

                            if( !empty($is_print) ) {
                                $data_options = false;
                            }

                            $content = $this->Html->tag('th', $this->getSorting($field_model, $name, $is_print, $sorting, $field_model_options), array(
                                'class' => sprintf('%s %s %s %s', $addClass, $key_field, $class, $_class),
                                'style' => $style,
                                'colspan' => $colspan,
                                'rowspan' => $rowspan,
                                'data-options' => $data_options,
                                'align' => $align,
                                'mainalign' => $mainalign,
                                'rel' => $rel,
                                'width' => $width,
                            ));

                            if( $fix_column && empty($is_print) ) {
                                $content .= '</tr></thead><thead><tr style="'.$_style.'">';
                            }

                            // Append Child
                            if( !empty($child) ) {
                                if( $is_print == 'pdf' ) {
                                    $options['style'] = 'background-color: #3C8DBC; border-right: 1px solid #FFFFFF; color: #FFFFFF; font-weight: bold;';
                                }

                                $childArr[] = $this->_generateShowHideColumn( $child, $data_type, $is_print, $options );
                            }
                        }

                        break;
                }

                if( !empty($content) ) {
                    $result[] = $content;
                }
            }
        }

        if( is_array($result) ) {
            if( !empty($childArr) && is_array($childArr) ) {
                $result_child = implode('', $childArr);
                $result_child = '</tr><tr style="'.$_style.'">'.$result_child;
                $result[] = $result_child;
            }

            $result = implode('', $result);
        }

        $result = is_array($result)?implode('', $result):$result;

        return $result;
    }

    function _getDataColumn ( $value, $modelName, $fieldName, $options = false ) {
        $default_style = !empty($options['style'])?$options['style']:false;
        $currency = !empty($options['data-currency'])?$options['data-currency']:false;
        $style = false;
        $result = false;

        // Set Allow Show Column
        $allowShow = $this->_allowShowColumn($modelName, $fieldName);

        if( !empty($allowShow) ) {
            $default_style .= $style;
            $options['style'] = $default_style;

            if( empty($options['style']) ) {
                unset($options['style']);
            }

            if( !empty($options['options']) ) {
                $value = !empty($options['options'][$value])?$options['options'][$value]:$value;
            } else if( !empty($currency) ) {
                $value = $this->getFormatPrice($value);
            }

            $result = $this->Html->tag('td', $value, $options);
        }

        return $result;
    }

    function _getShowHideColumn ( $formName, $showHideColumn, $options_form = false ) {
        if( empty($options_form) ) {
            $options_form = array(
                'url'=> $this->Html->url( null, true ), 
                'role' => 'form',
                'inputDefaults' => array('div' => false),
            );
        }

        // Set Form
        $content = $this->Form->create($formName, $options_form);

        // Set Button
        $_caret = $this->Html->tag('span', '', array(
            'class' => 'caret',
        ));
        $_title_button = sprintf(__('Show/Hide Kolom %s'), $_caret);
        $_button = $this->Form->button($_title_button, array(
            'class' => 'btn btn-info dropdown-toggle',
            'data-toggle' => 'dropdown',
        ));
        $contentDiv = $_button;

        // Set UL
        $contentLI = $this->Html->tag('li', __('Kolom Table'));
        $contentLI .= $this->Html->tag('li', '', array(
            'class' => 'divider',
        ));
        $contentLI .= $showHideColumn;
        $contentUL = $this->Html->tag('ul', $contentLI, array(
            'class' => 'dropdown-menu',
            'role' => 'menu',
        ));
        $contentDiv .= $contentUL;

        // Set Content
        $content .= $this->Html->tag('div', $this->Html->tag('div', $contentDiv, array(
            'class' => 'btn-group columnDropdown',
        )), array(
            'class' => 'list-field pull-left',
        ));

        // Set End Form
        $content .= $this->Form->end();

        return $content;
    }

    function _getButtonPostingUnposting ( $value = false, $modelName = 'Revenue', $lblArr = array( 'Posting', 'Unposting' ) ) {
        $posting = false;
        $invoiced = false;
        $_status = true;
        $group_id = Configure::read('__Site.config_group_id');

        if( !empty($value[$modelName]['transaction_status']) && $value[$modelName]['transaction_status'] == 'posting' ) {
            $posting = true;
        }
        if( !empty($value[$modelName]['transaction_status']) && in_array($value[$modelName]['transaction_status'], array( 'invoiced', 'half_invoiced', 'paid', 'half_paid' )) ) {
            $invoiced = true;
        }
        if( isset($value[$modelName]['status']) && empty($value[$modelName]['status']) ) {
            $_status = false;
        }

        echo $this->Form->hidden('transaction_status', array(
            'id' => 'transaction_status'
        ));
        
        if( !$invoiced && $_status ) {
            if( empty($posting) || $group_id == 1 ) {
                echo $this->Form->button(!empty($lblArr[0])?$lblArr[0]:__('Posting'), array(
                    'type' => 'submit',
                    'class'=> 'btn btn-success submit-form btn-lg',
                    'action_type' => 'posting'
                ));

                if( empty($posting) ) {
                    echo $this->Form->button(!empty($lblArr[1])?$lblArr[1]:__('Unposting'), array(
                        'type' => 'submit',
                        'class'=> 'btn btn-primary submit-form',
                        'action_type' => 'unposting'
                    ));
                }
            }
        }
    }

    function _getAllowSave ( $allow_closing, $value = false, $modelName = 'Revenue' ) {
        $posting = false;
        $invoiced = false;
        $_status = true;

        if( !empty($value[$modelName]['transaction_status']) && $value[$modelName]['transaction_status'] == 'posting' ) {
            $posting = true;
        }
        if( !empty($value[$modelName]['transaction_status']) && in_array($value[$modelName]['transaction_status'], array( 'invoiced', 'half_invoiced', 'paid', 'half_paid' )) ) {
            $invoiced = true;
        }
        if( isset($value[$modelName]['status']) && empty($value[$modelName]['status']) ) {
            $_status = false;
        }

        if( !$invoiced && $_status ) {
            if( empty($posting) ) {
                return true;
            }
        }

        return false;
    }

    function _callUnset( $fieldArr, $data ) {
        if( !empty($fieldArr) ) {
            foreach ($fieldArr as $key => $value) {
                if( is_array($value) ) {
                    foreach ($value as $idx => $fieldName) {
                        if( !empty($data[$key][$fieldName]) ) {
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

    function _getPrint ( $options = false, $showHideColumn = false ) {
        $_ajax = isset($options['_ajax'])?$options['_ajax']:false;
        $_excel = isset($options['_excel'])?$options['_excel']:true;
        // $_pdf = isset($options['_pdf'])?$options['_pdf']:true;
        $_attr = isset($options['_attr'])?$options['_attr']:array();
        $_excel_url = isset($options['url_excel'])?$options['url_excel']:false;
        $_added_url = isset($options['added_url'])?$options['added_url']:false;
        $result = false;
        $resultContent = '';
        $default_attr = array(
            'escape' => false,
            'class' => false,
            'data-form' => '.form-search',
            'data-wrapper-write' => '.wrapper-download',
        );
        $urlDefault['controller'] = !empty($this->params['controller'])?$this->params['controller']:false;
        $urlDefault['action'] = $this->action;

        $pass = !empty($this->params['pass'])?$this->params['pass']:array();
        $named = !empty($this->params['named'])?$this->params['named']:array();

        $named = $this->_callUnset(array(
            'page',
        ), $named);
        $sorted = Common::_callSet($named, array(
            'sort',
            'direction',
        ));
        
        $urlDefault = array_merge($urlDefault, $pass);
        $urlDefault = array_merge($urlDefault, $named);
        $wrapperDownload = '';

        if( !empty($_attr) ) {
            $default_attr = array_merge($default_attr, $_attr);
        }

        if( !empty($_excel) ) {
            if( !empty($_excel_url) ) {
                $urlExcel = $_excel_url;
            } else {
                if( !empty($_ajax) ) {
                    $urlExcel = array_merge(array(
                        'controller' => 'reports',
                        'action' => 'generate_excel',
                        $this->action,
                    ), $sorted);
                } else {
                    $urlExcel = $urlDefault;
                    $urlExcel[] = 'excel';
                }
            }
            
            $_excel_attr = $default_attr;

            if( !empty($_ajax) ) {
                $_excel_attr['class'] = $default_attr['class'].' btn btn-success pull-right ajax-link';
            } else {
                $_excel_attr['class'] = $default_attr['class'].' btn btn-success pull-right';
            }

            $result .= $this->Html->link('<i class="fa fa-download"></i> Generate Excel', $urlExcel, $_excel_attr);
            $wrapperDownload = $this->Html->tag('div', '', array(
                'class' => 'wrapper-download',
            ));
        }
        // if( !empty($_pdf) ) {
        //     $urlPdf = $urlDefault;
        //     $urlPdf[] = 'pdf';
        //     $_pdf_attr = $default_attr;
        //     $_pdf_attr['class'] = $default_attr['class'].' btn btn-primary pull-right';
        //     $result .= $this->Html->link('<i class="fa fa-download"></i> Download PDF', $urlPdf, $_pdf_attr);
        // }
        if( !empty($_added_url) ) {
            $_added_text = Common::hashEmptyField($_added_url, 'text');
            $_added_options = Common::hashEmptyField($_added_url, 'options');
            $_added_alert = Common::hashEmptyField($_added_url, 'alert');
            $_added_url = Common::hashEmptyField($_added_url, 'url');

            $result .= $this->Html->link($_added_text, $_added_url, $_added_options, $_added_alert);
        }

        if( !empty($showHideColumn) ) {
            $resultContent .= $this->_getShowHideColumn('Truck', $showHideColumn, array(
                'url'=> $this->Html->url( null, true ), 
                'role' => 'form',
                'inputDefaults' => array('div' => false),
                'id' => 'form-report',
            ));
        }

        $resultContent .= $this->Html->tag('div', $result, array(
            'class' => 'action pull-right',
        ));

        return $this->Html->tag('div', $resultContent.$this->Html->tag('div', '', array(
            'class' => 'clear',
        )).$wrapperDownload, array(
            'class' => 'no-print print-action',
        ));
    }

    function filterEmptyField ( $value, $modelName, $fieldName = false, $empty = false, $removeTag = true, $format = false ) {
        $result = '';
        
        if( empty($fieldName) ) {
            $result = !empty($value[$modelName])?$value[$modelName]:$empty;
        } else {
            $result = !empty($value[$modelName][$fieldName])?$value[$modelName][$fieldName]:$empty;
        }

        if( !empty($removeTag) && !is_array($result) ) {
            $result = $this->safeTagPrint($result);
        }

        if( !empty($result) && $result != $empty ) {
            if( is_array($format) ) {
                if( !empty($format['date']) ) {
                    $format = $format['date'];
                    $result = $this->formatDate($result, $format);
                }
                if( !empty($format['price_to_string']) ) {
                    $empty = isset($format['price_to_string']['empty'])?$format['price_to_string']['empty']:'';
                    $decimal = isset($format['price_to_string']['decimal'])?$format['price_to_string']['decimal']:0;
                    $result = $this->convertPriceToString($result, $empty, $decimal);
                }
                if( !empty($format['price']) ) {
                    $decimal = !empty($format['price']['decimal'])?$format['price']['decimal']:0;
                    $empty = !empty($format['price']['empty'])?$format['price']['empty']:0;

                    $result = $this->getFormatPrice($result, $empty, $decimal);
                }
            } else {
                switch ($format) {
                    case 'EOL':
                        $result = $this->getFormatDesc($result);
                        break;
                }
            }
        }

        return $result;
    }

    function filterIssetField ( $value, $modelName, $fieldName = false, $empty = false, $removeHtml = true ) {
        $result = '';
        
        if( empty($modelName) && $modelName != 0 ) {
            $result = isset($value)?$value:$empty;
        } else if( empty($fieldName) && $fieldName != 0 ) {
            $result = isset($value[$modelName])?$value[$modelName]:$empty;
        } else {
            $result = isset($value[$modelName][$fieldName])?$value[$modelName][$fieldName]:$empty;
        }

        if( isset($removeHtml) && !is_array($result) ) {
            return $this->safeTagPrint($result);
        } else {
            return $result;
        }
    }

    function getMergePrepayment ( $prepayment, $class = false ) {
        $result = false;
        $content = array();
        $id = $this->filterEmptyField($prepayment, 'CashBank', 'id');
        $nodoc = $this->filterEmptyField($prepayment, 'CashBank', 'nodoc');
        $dt = $this->customDate($this->filterEmptyField($prepayment, 'CashBank', 'tgl_cash_bank'), 'd M Y');
        $coa_name = $this->filterEmptyField($prepayment, 'Coa', 'name');
        $receiver_name = $this->filterEmptyField($prepayment, 'Receiver', 'name');
        $description = $this->filterEmptyField($prepayment, 'CashBank', 'description');
        $debit_total = $this->filterEmptyField($prepayment, 'CashBank', 'debit_total', 0);
        $credit_total = $this->filterEmptyField($prepayment, 'CashBank', 'credit_total', 0);
        $noref = str_pad($id, 6, '0', STR_PAD_LEFT);

        $content[] = $this->Html->tag('td', $noref, array(
            'style' => 'text-align:left;',
        ));
        $content[] = $this->Html->tag('td', $nodoc, array(
            'style' => 'text-align:left;',
        ));
        $content[] = $this->Html->tag('td', $dt, array(
            'style' => 'text-align:center;',
        ));
        $content[] = $this->Html->tag('td', $coa_name, array(
            'style' => 'text-align:left;',
        ));
        $content[] = $this->Html->tag('td', $receiver_name, array(
            'style' => 'text-align:left;',
        ));
        $content[] = $this->Html->tag('td', $description, array(
            'style' => 'text-align:left;',
        ));
        $content[] = $this->Html->tag('td', $this->getFormatPrice($debit_total, false, 2), array(
            'style' => 'text-align:right;',
        ));
        $content[] = $this->Html->tag('td', $this->getFormatPrice($credit_total, false, 2), array(
            'style' => 'text-align:right;',
        ));

        // Give Class IF There Not Prepayment IN
        if( !empty($prepayment['PrepaymentIN']) ) {
            $class = 'complete';
        }

        $result .= $this->Html->tag('tr', implode('', $content), array(
            'class' => $class,
        ));

        // Prepayment IN
        if( !empty($prepayment['PrepaymentIN']) ) {
            foreach ($prepayment['PrepaymentIN'] as $key => $prepaymentIN) {
                $result .= $this->getMergePrepayment( $prepaymentIN, $class );
            }
        }

        return $result;
    }

    function getMergeTotalPrepayment ( $prepayment ) {
        $debit_total = $this->filterEmptyField($prepayment, 'CashBank', 'debit_total', 0);
        $credit_total = $this->filterEmptyField($prepayment, 'CashBank', 'credit_total', 0);

        // Prepayment IN
        if( !empty($prepayment['PrepaymentIN']) ) {
            foreach ($prepayment['PrepaymentIN'] as $key => $prepaymentIN) {
                $result = $this->getMergeTotalPrepayment( $prepaymentIN );
                $debit_total += !empty($result['debit_total'])?$result['debit_total']:0;
                $credit_total += !empty($result['credit_total'])?$result['credit_total']:0;
            }
        }

        return array(
            'debit_total' => $debit_total,
            'credit_total' => $credit_total,
        );
    }

    function _callGetNotificationIcon ( $type, $bg = false ) {
        if( !empty($bg) ) {
            switch ($type) {
                case 'success':
                    return $this->icon('check', false, 'i', 'bg-green');
                break;
                case 'danger':
                    return $this->icon('warning', false, 'i', 'bg-red');
                break;
                default:
                    return $this->icon('info-circle', false, 'i', 'bg-aqua');
                break;
            }
        } else {
            switch ($type) {
                case 'success':
                    return $this->icon('check', false, 'i', 'success');
                break;
                case 'danger':
                    return $this->icon('warning', false, 'i', 'danger');
                break;
                default:
                    return $this->icon('info-circle', false, 'i', 'info');
                break;
            }
        }
    }

    function _callNotificationUrl ( $data, $title, $options = false ) {
        $id = $this->filterEmptyField($data, 'Notification', 'id');
        $url = $this->filterEmptyField($data, 'Notification', 'url');
        $read = $this->filterEmptyField($data, 'Notification', 'read');

        if( !empty($url) && !empty($id) ) {
            if( empty($options) ) {
                $options = array(
                    'escape' => false,
                );
            }
            if( empty($options['class']) ) {
                $options['class'] = false;
            }

            if( !empty($read) ) {
                $options['class'] .= ' read';
            }

            return $this->Html->link($title, array(
                'controller' => 'pages',
                'action' => 'referer_notification',
                $id,
                'admin' => false,
            ), $options);
        }
    }

    function getNotif($data, $header = true){
        $id = $this->filterEmptyField($data, 'Notification', 'id');
        $type_notif = $this->filterEmptyField($data, 'Notification', 'type_notif');
        $content_notif = $this->filterEmptyField($data, 'Notification', 'name');
        $url = $this->filterEmptyField($data, 'Notification', 'url');

        $type_notif = $this->_callGetNotificationIcon($type_notif);
        $content = sprintf('%s%s', $type_notif, $this->Html->tag('span', $content_notif));

        if( !empty($url) && !empty($id) ) {
            $content = $this->_callNotificationUrl($data, $content);
        }

        return $this->Html->tag('li', $content);
    }

    function getPaymentNotif($data, $header = true){
        $id = $this->filterEmptyField($data, 'Leasing', 'id');
        $nodoc = $this->filterEmptyField($data, 'Leasing', 'no_contract');
        $to_name = $this->filterEmptyField($data, 'Vendor', 'name');
        $paid_date = $this->filterEmptyField($data, 'LeasingInstallment', 'paid_date');
        $paid_date = $this->formatDate($paid_date, 'd M Y');

        $content = sprintf(__('Pemberitahuan pembayaran Leasing #%s, jatuh tempo pada tanggal %s'), $nodoc, $paid_date);
        $content_url = array(
            'controller' => 'leasings',
            'action' => 'payment_add',
            'admin' => false,
        );

        if( !empty($content) ) {
            if( !empty($content_url) ) {
                $content = $this->Html->link($content, $content_url, array(
                    'escape' => false,
                ));
            }

            return $this->Html->tag('li', $content);
        } else {
            return false;
        }
    }

    /**
    *
    *   function format tanggal
    *   @param string $dateString : tanggal
    *   @param string $format : format tanggal
    *   @return string tanggal
    */
    function formatDate($dateString, $format = false, $empty = '-') {
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

    /**
    *
    *   mengkombinasikan tanggal
    *
    *   @param string $startDate : tanggal awal
    *   @param string $endDate : tanggal akhir
    *   @return string
    */
    function getCombineDate ( $startDate, $endDate, $format = 'long' ) {
        $startDate = !empty($startDate)?strtotime($startDate):null;
        $endDate = !empty($endDate)?strtotime($endDate):null;

        if( !empty($startDate) ) {
            switch ($format) {
                case 'short':
                    if( $startDate == $endDate || empty($endDate) ) {
                        $customDate = date('M Y', $startDate);
                    } else if( date('Y', $startDate) == date('Y', $endDate) ) {
                        $customDate = sprintf('%s - %s', date('M', $startDate), date('M Y', $endDate));
                    } else {
                        $customDate = sprintf('%s - %s', date('M Y', $startDate), date('M Y', $endDate));
                    }
                    break;
                
                default:
                    if( $startDate == $endDate || empty($endDate) ) {
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

    function branchForm($model, $branches, $type_position = 'vertical', $title = false, $class_label = 'col-sm-2', $class_content = 'col-sm-8'){
        $title = !empty($title)?$title:__('Cabang *');

        if($type_position == 'vertical'){
            $content = $this->Form->input($model.'.branch_id',array(
                'label' => $title,
                'empty' => __('Pilih Cabang --'),
                'required' => false,
                'class' => 'form-control change-branch',
                'options' => $branches
            ));
        }else{
            $label = $this->Form->label($model.'.branch_id', $title, array(
                'class'=>'control-label '.$class_label
            )); 

            $content = $label.$this->Html->tag('div', $this->Form->input('branch_id',array(
                'label'=>false,
                'empty' => __('Pilih Cabang --'),
                'required' => false,
                'class' => 'form-control change-branch',
                'options' => $branches
            )), array(
                'class' => $class_content
            ));
        }

        $content .= $this->Form->hidden('Default.branch_id', array(
            'class' => 'default-branch-id',
            'value' => !empty($this->request->data[$model]['branch_id'])?$this->request->data[$model]['branch_id']:false,
        ));

        return $this->Html->tag('div', $content, array(
            'class' => 'form-group'
        ));
    }

    function allowMenu ( $dataMenu ) {
        $allow = false;
        $branchs = Configure::read('__Site.config_branch_id');
        $_allowModule = Configure::read('__Site.config_allow_module');
        $group_id = Configure::read('__Site.config_group_id');

        if( $group_id == 1 ) {
            $allow = true;
        } else if( !empty($dataMenu) ) {

            foreach ($dataMenu as $controller => $action) {
                $findArr = $action;

                if( $this->allowPage( $branchs, $controller, $findArr ) ) {
                    $allow = true;
                }
            }
        }

        return $allow;
    }

    function icon($icon, $content = false, $tag = 'i', $addClass = false) {
        return $this->Html->tag($tag, $content, array(
            'class' => sprintf('fa fa-%s %s', $icon, $addClass),
        ));
    }

    function tag($tag, $addClass = false, $content = false) {
        return $this->Html->tag($tag, $content, array(
            'class' => $addClass,
        ));
    }

    function allowPage ( $branchs, $controllerName, $actionName ) {
        $moduleAllow = Configure::read('__Site.config_allow_module');
        $branchAllow = Configure::read('__Site.Data.Branch.id');
        $result = false;

        if( !is_array($branchs) ) {
            $branchs = array( $branchs );
        }

        if( !empty($branchs) && is_array($branchs) ) {
            foreach ($branchs as $key => $branch_id) {
                if( !empty($moduleAllow[$branch_id]) && in_array($branch_id, $branchAllow) ) {
                    if( !empty($moduleAllow[$branch_id][$controllerName]['action']) ) {
                        $allowAction = $moduleAllow[$branch_id][$controllerName]['action'];

                        if( is_array($actionName) ) {
                            $result = array_intersect($actionName, $allowAction);

                            if( !empty($result) ) {
                                $result = true;
                            }
                        } else if( in_array($actionName, $allowAction) ) {
                            $result = true;
                        }
                    }
                }
            }
        }

        return $result;
    }

    function getCheckboxBranch ( $modelName = 'GroupBranch' ) {
        $result = '';
        $branches = Configure::read('__Site.config_allow_branchs');

        if( !empty($branches) && count($branches) > 1 ) {
            $tmpArr = array();
            $default_options = array(
                'type' => 'checkbox',
                'label'=> false,
                'required' => false,
                'div' => false,
            );

            if( empty($this->request->data[$modelName]['group_branch']) ) {
                $default_options['checked'] = true;
            }

            foreach ($branches as $branch_id => $city_name) {
                $branchCheckboxOptions = $default_options;
                $branchCheckboxOptions['value'] = $branch_id;
                $branchCheckboxOptions['class'] = 'check-branch';

                $tmpArr[] = $this->Html->tag('li', $this->Html->tag('div', $this->Html->tag('label', $this->Form->input($modelName.'.group_branch.'.$branch_id, $branchCheckboxOptions).$city_name), array(
                    'class' => 'checkbox',
                )));
            }

            if( !empty($tmpArr) && count($tmpArr) > 1 ) {
                $btn = $this->Form->button(__('Pilih Cabang ').$this->Html->tag('div', '', array(
                    'class' => 'caret',
                )), array(
                    'class' => 'btn btn-default dropdown-toggle',
                    'data-toggle' => 'dropdown',
                ));
                $headLabel = $this->Html->tag('label', __('Cabang'), array(
                    'class' => 'block'
                ));

                $branchCheckboxOptions = $default_options;
                $branchCheckboxOptions['class'] = 'check-all';

                $headLi = $this->Html->tag('li', $this->Html->tag('li', $this->Html->tag('div', $this->Html->tag('label', $this->Form->input($modelName.'.group_branch.'.$branch_id, $branchCheckboxOptions).__('Check/Uncheck All')), array(
                    'class' => 'checkbox',
                ))));
                $divider = $this->Html->tag('li', '', array(
                    'class' => 'divider',
                ));
                $ulContent = $this->Html->tag('ul', $headLi.$divider.implode('', $tmpArr), array(
                    'class' => 'dropdown-menu parent-check-branch',
                    'role' => 'menu',
                ));

                $result =  $this->Html->tag('div', $headLabel.$this->Html->tag('div', $btn.$ulContent, array(
                    'class' => 'btn-group columnDropdown',
                )), array(
                    'class' => 'list-field',
                ));
            }
        }

        return $result;
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

    function getCheckStatus ( $status, $url = false ) {
        if( !empty($status) ){
            $content = $this->Html->tag('span', $this->icon('check'), array(
                'class' => 'label label-success',
            ));
        }else{
            $content = $this->Html->tag('span', $this->icon('times'), array(
                'class' => 'label label-danger',
            ));
        }

        if( !empty($url) ) {
            $content = $this->Html->link($content, $url, array(
                'escape' => false,
            ));
        }

        return $content;
    }

    function _callStatusApproval ( $status ) {
        switch ($status) {
            case 'approved':
                $content = $this->Html->tag('span', __('Disetujui'), array(
                    'class' => 'label label-success',
                ));
                break;

            case 'rejected':
                $content = $this->Html->tag('span', __('Ditolak'), array(
                    'class' => 'label label-danger',
                ));
                break;

            case 'revised':
                $content = $this->Html->tag('span', __('Direvisi'), array(
                    'class' => 'label label-warning',
                ));
                break;
            
            default:
                $content = $this->Html->tag('span', __('Tertunda'), array(
                    'class' => 'label label-default',
                ));
                break;
        }

        return $content;
    }

    function _callStatusAuth ( $data ) {
        $status = $this->filterEmptyField($data, 'DocumentAuth', 'status_document', '-');

        if( $status != '-' ) {
            switch ($status) {
                case 'approve':
                    $status = __('Disetujui');
                    $labelClass = 'success';
                    break;

                case 'reject':
                    $status = __('Ditolak');
                    $labelClass = 'danger';
                    break;

                case 'revise':
                    $status = __('Revisi');
                    $labelClass = 'warning';
                    break;
                
                default:
                    $status = __('Pending');
                    $labelClass = 'default';
                    break;
            }

            $status = $this->Html->tag('div', ucwords($status), array(
                'class' => sprintf('label label-%s', $labelClass),
            ));
        }

        return $status;
    }

    function buildForm ( $fieldName, $fieldLabel, $options = array(), $position = 'vertical' ) {
        $result = '';
        $labelText = false;
        $fieldDiv = false;
        $id_form = $this->filterEmptyField($options, 'id');
        $size = $this->filterEmptyField($options, 'size');
        $type = $this->filterEmptyField($options, 'type');
        $error = $this->filterEmptyField($options, 'error', false, true);
        $_options = $this->filterEmptyField($options, 'options');
        $description = $this->filterEmptyField($options, 'description');
        $empty = $this->filterEmptyField($options, 'empty');
        $readonly = $this->filterEmptyField($options, 'readonly');
        $disabled = $this->filterEmptyField($options, 'disabled');
        $placeholder = $this->filterEmptyField($options, 'placeholder');
        $addClass = $this->filterEmptyField($options, 'class');
        $classSize = false;

        switch ($size) {
            case 'small':
                $classSize = 'col-sm-3';
                break;

            case 'medium':
                $classSize = 'col-sm-6';
                break;
        }

        switch ($position) {
            case 'horizontal':
                $result .= $this->Form->label($fieldName, $fieldLabel, array(
                    'class' => 'col-sm-3 text-right',
                ));
                $classSize = $this->filterEmptyField($classSize, false, false, 'col-sm-9');

                $fieldDiv = array(
                    'class' => $classSize,
                );
                break;
            
            default:
                $labelText = $fieldLabel;
                break;
        }

        $default_options = array(
            'id' => $id_form,
            'label' => $labelText,
            'required' => false,
            'div' => $fieldDiv,
            'empty' => $empty,
            'readonly' => $readonly,
            'placeholder' => $placeholder,
            'class' => 'form-control '.$addClass,
            'disabled' => $disabled,
        );

        if( !empty($type) ) {
            if( $type == 'checkbox' ) {
                $default_options['class'] = '';
            }

            $default_options['type'] = $type;
        }

        if( !is_array($options) ) {
            $default_options = array_merge_recursive($default_options, $options);
        }

        switch ($type) {
            case 'radio':
                $inputContent = $this->_View->element('blocks/common/forms/multiple_radio', array(
                    'options' => $_options,
                    'fieldName' => $fieldName,
                    'error' => $error,
                    'label' => $labelText,
                ));

                if( $position == 'horizontal' ) {
                    $result =  $this->Html->tag('div', $result.$this->Html->tag('div', $inputContent, array(
                        'class' => $classSize,
                    )), array(
                        'class' => 'form-group',
                    ));
                } else {
                    $result =  $this->Html->tag('div', $inputContent, array(
                        'class' => 'form-group',
                    ));
                }
                break;
            
            default:
                if( !empty($_options) ) {
                    $default_options['options'] = $_options;
                }

                if( !empty($fieldDiv) && !empty($description) ) {
                    $default_options['div'] = false;
                    $inputContent = $this->Html->tag('div', $this->Form->input($fieldName, $default_options).$description, array(
                        'class' => $fieldDiv,
                    ));
                } else {
                    $inputContent = $this->Form->input($fieldName, $default_options).$description;
                }

                $result =  $this->Html->tag('div', $result.$inputContent, array(
                    'class' => 'form-group',
                ));
                break;
        }

        if( $position == 'vertical' && !empty($classSize) ) {
            $result = $this->Html->tag('div', $this->Html->tag('div', $result, array(
                'class' => $classSize,
            )), array(
                'class' => 'row',
            ));
        }

        return $result;
    }

    function buildInputForm ($fieldName, $label = false, $options = false) {
        $default_options = array(
            'label' => $label,
            'fieldName' => $fieldName,
            'frameClass' => 'form-group',
            'labelClass' => false,
            'class' => 'form-control',
            'div' => false,
            'placeholder' => false,
            'options' => false,
        );

        if( !empty($options) ) {
            $default_options = array_merge($default_options, $options);
        }

        return $this->_View->element('blocks/common/forms/input_form', $default_options);
    }

    function buildRadioForm ($fieldName, $label = false, $options = false) {
        $default_options = array(
            'label' => $label,
            'fieldName' => $fieldName,
        );

        if( !empty($options) ) {
            $default_options = array_merge($default_options, $options);
        }

        return $this->_View->element('blocks/common/forms/radio_form', $default_options);
    }

    function getCurrencyPrice ($price) {
        return $this->Number->currency($price, Configure::read('__Site.config_currency_code'), array('places' => 0));
    }

    function getFormatPrice ($price, $empty = 0, $places = 0) {
        if( !empty($price) ) {
            // if( strpos($price,'.') == false ) {
            //     $places = 0;
            // }

            return $this->Number->currency($price, '', array('places' => $places));
        } else {
            return $empty;
        }
    }

    function getFormatDecimal ($price, $empty = 0, $places = 2) {
        if( !empty($price) ) {
            return $this->Number->currency($price, Configure::read('__Site.config_currency_code'), array(
                'places' => $places,
                'thousands' => '.',
                'decimals' => ',',
            ));
        } else {
            return $empty;
        }
    }

    function getFormatDesc ( $value ) {
        return str_replace(PHP_EOL, '<br>', $value);
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

    function _callStaticForm( $label, $value ) {
        $content = $this->Html->tag('label', $label);
        $content .= $this->Html->tag('div', $value);

        return $content;
    }

    function unSlug($string) {
        $string = str_replace(array( '-', '_' ), array( '', '' ), $string);
        $string = ucwords($string);
        
        return $string;
    }

    function link($text, $url, $options = false, $alert = false) {
        $_icon = $this->filterEmptyField($options, 'data-icon');
        $_wrapper = $this->filterEmptyField($options, 'data-wrapper');
        $_wrapper_options = $this->filterEmptyField($options, 'data-wrapper-options');
        $_lbl_active = $this->filterEmptyField($options, 'data-active');
        $_caret = $this->filterEmptyField($options, 'data-caret', false, false, false);
        $_slug = $this->filterEmptyField($options, 'data-slug', false, $text);

        $_tolower_text = strtolower($_slug);
        $_lbl_active = strtolower($_lbl_active);
        $options['escape'] = false;

        if( !empty($_icon) ) {
            $text = sprintf('%s %s', $this->icon($_icon), $text);

            unset($options['data-icon']);
        }
        if( $_lbl_active == $_tolower_text ) {
            if( !empty($_wrapper) ) {
                $_add_class = !empty($_wrapper_options['class'])?$_wrapper_options['class']:false;
                $_add_class .= ' active';
                $_wrapper_options['class'] = $_add_class;
            } else {
                $_add_class = !empty($options['class'])?$options['class']:false;
                $_add_class .= ' active';
                $options['class'] = $_add_class;
            }

            if( isset($options['aria-expanded']) ) {
                $options['aria-expanded'] = 'true';
            }
            if( isset($options['class']) ) {
                $options['class'] = str_replace('collapsed', '', $options['class']);
            }
        }

        if( !empty($_caret) ) {
            $text .= $_caret;
            unset($options['data-caret']);
        }

        if( !empty($_wrapper) ) {
            $default_wrapper_options = false;

            if( !empty($_wrapper_options) ) {
                $default_wrapper_options = $_wrapper_options;
            }
            
            $result = $this->Html->tag($_wrapper, $this->Html->link($text, $url, $options, $alert), $default_wrapper_options);
        } else {
            $result = $this->Html->link($text, $url, $options, $alert);
        }

        return $result;
    }

    function groupMotorTable ( $data, $options = false ) {
        $cnt = $this->filterEmptyField($options, 'cnt', false, 0);

        $value = $this->filterEmptyField($options, 'data');
        $modelName = $this->filterEmptyField($options, 'modelName');
        $fieldName = $this->filterEmptyField($options, 'fieldName');

        for ($i=1; $i <= $cnt; $i++) {
            $key = $i-1;
            $groupMotorName = !empty($value[$key]['GroupMotor']['name'])?$value[$key]['GroupMotor']['name']:'';
            $cost = !empty($value[$key][$modelName][$fieldName])?$value[$key][$modelName][$fieldName]:'';

            $data[] = $groupMotorName;
            $data[] = $cost;
        }

        return $data;
    }

    function clearfix () {
        return $this->Html->tag('div', '', array(
            'class' => 'clear',
        ));
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

    function dateDiff ( $startDate, $endDate, $format = false ) {
        $result = false;
        
        if( !empty($startDate) && !empty($endDate) && $startDate != '0000-00-00 00:00:00' && $endDate != '0000-00-00 00:00:00' ) {
            $from_time = strtotime($startDate);
            $to_time = strtotime($endDate);
            $datediff = intval($to_time - $from_time);
            $total_day = intval($datediff/(60*60*24));
            $total_hour = intval($datediff/(60*60));

            $dateResult = $this->_callDateDiff ( $startDate, $endDate );

            switch ($format) {
                case 'day':

                    $result = $total_day;

                    break;

                default:
                    $result = $dateResult;
                    break;
            }
        }

        return $result;
    }

    function _callDocumentJournal ( $label, $id = false, $type = false, $data_action = true ) {
        if( in_array($type, array( 'asdp', 'asdp_void', 'commission', 'commission_void', 'uang_jalan', 'uang_kuli_muat', 'uang_kuli_bongkar', 'uang_kawal', 'uang_keamanan', 'uang_jalan_void', 'uang_kuli_muat_void', 'uang_kuli_bongkar_void', 'uang_kawal_void', 'uang_keamanan_void' )) ) {
            $urlDefault = array(
                'controller' => 'revenues',
                'action' => 'info_truk',
                'ttuj',
                $id,
                'admin' => false,
            );
        } else if( in_array($type, array( 'in', 'void_in', 'out', 'void_out', 'ppn_out', 'void_ppn_out', 'prepayment_out', 'void_prepayment_out', 'prepayment_in', 'void_prepayment_in' )) ) {
            $urlDefault = array(
                'controller' => 'cashbanks',
                'action' => 'detail',
                $id,
                'admin' => false,
            );
        } else if( in_array($type, array( 'leasing_payment', 'leasing_payment_void' )) ) {
            $urlDefault = array(
                'controller' => 'leasings',
                'action' => 'detail_payment',
                $id,
                'admin' => false,
            );
        } else if( in_array($type, array( 'lku_payment', 'lku_payment_void' )) ) {
            $urlDefault = array(
                'controller' => 'lkus',
                'action' => 'detail_payment',
                $id,
                'admin' => false,
            );
        } else if( in_array($type, array( 'ksu_payment', 'ksu_payment_void' )) ) {
            $urlDefault = array(
                'controller' => 'lkus',
                'action' => 'detail_ksu_payment',
                $id,
                'admin' => false,
            );
        } else if( in_array($type, array( 'invoice', 'invoice_void' )) ) {
            $urlDefault = array(
                'controller' => 'revenues',
                'action' => 'invoice_print',
                $id,
                'print' => 'date',
                'admin' => false,
            );
        } else if( in_array($type, array( 'invoice_payment', 'invoice_payment_void' )) ) {
            $urlDefault = array(
                'controller' => 'revenues',
                'action' => 'detail_invoice_payment',
                $id,
                'admin' => false,
            );
        } else if( in_array($type, array( 'kir', 'kir_void' )) ) {
            $urlDefault = array(
                'controller' => 'trucks',
                'action' => 'kir_detail',
                $id,
                'admin' => false,
            );
        } else if( in_array($type, array( 'siup', 'siup_void' )) ) {
            $urlDefault = array(
                'controller' => 'trucks',
                'action' => 'siup_detail',
                $id,
                'admin' => false,
            );
        } else if( in_array($type, array( 'stnk', 'stnk_void' )) ) {
            $urlDefault = array(
                'controller' => 'trucks',
                'action' => 'stnk_detail',
                $id,
                'admin' => false,
            );
        } else if( in_array($type, array( 'revenue', 'revenue_void' )) ) {
            $urlDefault = array(
                'controller' => 'revenues',
                'action' => 'edit',
                $id,
                'admin' => false,
            );
        } else if( in_array($type, array( 'biaya_ttuj_payment', 'biaya_ttuj_payment_void' )) ) {
            $urlDefault = array(
                'controller' => 'revenues',
                'action' => 'detail_ttuj_payment',
                $id,
                'biaya_ttuj',
                'admin' => false,
            );
        } else if( in_array($type, array( 'uang_Jalan_commission_payment', 'uang_Jalan_commission_payment_void' )) ) {
            $urlDefault = array(
                'controller' => 'revenues',
                'action' => 'detail_ttuj_payment',
                $id,
                'uang_jalan_commission',
                'admin' => false,
            );
        } else if( in_array($type, array( 'general_ledger' )) ) {
            $urlDefault = array(
                'controller' => 'cashbanks',
                'action' => 'general_ledger_detail',
                $id,
                'admin' => false,
            );
        } else if( in_array($type, array( 'debt', 'debt_void' )) ) {
            $urlDefault = array(
                'controller' => 'debt',
                'action' => 'detail',
                $id,
                'admin' => false,
            );
        } else if( in_array($type, array( 'debt_payment', 'debt_payment_void' )) ) {
            $urlDefault = array(
                'controller' => 'debt',
                'action' => 'payment_detail',
                $id,
                'admin' => false,
            );
        } else if( in_array($type, array( 'titipan', 'titipan_void', 'void_titipan' )) ) {
            $urlDefault = array(
                'controller' => 'titipan',
                'action' => 'detail',
                $id,
                'admin' => false,
            );
        } else if( in_array($type, array( 'laka', 'laka_void', 'void_laka' )) ) {
            $urlDefault = array(
                'controller' => 'lakas',
                'action' => 'detail',
                $id,
                'admin' => false,
            );
        } else if( in_array($type, array( 'laka_payment', 'laka_payment_void', 'void_laka_payment' )) ) {
            $urlDefault = array(
                'controller' => 'lakas',
                'action' => 'detail',
                $id,
                'admin' => false,
            );
        }

        if( !empty($urlDefault) && empty($data_action) ) {
            $link = $this->Html->link($label, $urlDefault, array(
                'target' => 'blank',
            ));

            if( !empty($link) ) {
                return $link;
            } else {
                return $label;
            }
        } else {
            return sprintf('#%s', $label);
        }
    }

    function _callTypeJournal ( $type = false ) {
        $is_void = strpos($type, 'void');
        $label = '';

        if( in_array($type, array( 'asdp', 'asdp_void' )) ) {
            $label = __('ASDP');
        } else if( in_array($type, array( 'commission', 'commission_void' )) ) {
            $label = __('Komisi UJ');
        } else if( in_array($type, array( 'uang_jalan', 'uang_jalan_void' )) ) {
            $label = __('Biaya UJ');
        } else if( in_array($type, array( 'uang_kuli_muat', 'uang_kuli_muat_void' )) ) {
            $label = __('Kuli Muat');
        } else if( in_array($type, array( 'uang_kuli_bongkar', 'uang_kuli_bongkar_void' )) ) {
            $label = __('Kuli Bongkar');
        } else if( in_array($type, array( 'uang_kawal', 'uang_kawal_void' )) ) {
            $label = __('Biaya Kawal');
        } else if( in_array($type, array( 'uang_keamanan', 'uang_keamanan_void' )) ) {
            $label = __('Biaya Keamanan');
        } else if( in_array($type, array( 'in', 'void_in' )) ) {
            $label = __('Cash IN');
        } else if( in_array($type, array( 'out', 'void_out' )) ) {
            $label = __('Cash OUT');
        } else if( in_array($type, array( 'ppn_out', 'void_ppn_out' )) ) {
            $label = __('PPN OUT');
        } else if( in_array($type, array( 'prepayment_out', 'void_prepayment_out' )) ) {
            $label = __('Prepayment OUT');
        } else if( in_array($type, array( 'prepayment_in', 'void_prepayment_in' )) ) {
            $label = __('Prepayment IN');
        } else if( in_array($type, array( 'leasing_payment', 'leasing_payment_void' )) ) {
            $label = __('Pembayaran Leasing');
        } else if( in_array($type, array( 'lku_payment', 'lku_payment_void' )) ) {
            $label = __('Pembayaran LKU');
        } else if( in_array($type, array( 'ksu_payment', 'ksu_payment_void' )) ) {
            $label = __('Pembayaran KSU');
        } else if( in_array($type, array( 'invoice', 'invoice_void' )) ) {
            $label = __('Invoice');
        } else if( in_array($type, array( 'invoice_payment', 'invoice_payment_void' )) ) {
            $label = __('Pembayaran Invoice');
        } else if( in_array($type, array( 'kir', 'kir_void' )) ) {
            $label = __('KIR');
        } else if( in_array($type, array( 'siup', 'siup_void' )) ) {
            $label = __('SIUP');
        } else if( in_array($type, array( 'stnk', 'stnk_void' )) ) {
            $label = __('STNK');
        } else if( in_array($type, array( 'revenue', 'revenue_void' )) ) {
            $label = __('Revenue');
        } else if( in_array($type, array( 'biaya_ttuj_payment', 'biaya_ttuj_payment_void' )) ) {
            $label = __('Pembayaran Biaya TTUJ');
        } else if( in_array($type, array( 'uang_Jalan_commission_payment', 'uang_Jalan_commission_payment_void' )) ) {
            $label = __('Pembayaran UJ/Komisi');
        } else if( in_array($type, array( 'general_ledger' )) ) {
            $label = __('Jurnal Umum');
        } else if( in_array($type, array( 'debt', 'debt_void' )) ) {
            $label = __('Hutang Karyawan');
        } else if( in_array($type, array( 'debt_payment', 'debt_payment_void' )) ) {
            $label = __('Pembayaran Hutang Karyawan');
        } else if( in_array($type, array( 'titipan', 'titipan_void', 'void_titipan' )) ) {
            $label = __('Titipan');
        } else if( in_array($type, array( 'laka', 'laka_void', 'void_laka' )) ) {
            $label = __('LAKA');
        } else if( in_array($type, array( 'laka_payment', 'laka_payment_void', 'void_laka_payment' )) ) {
            $label = __('Pembayaran LAKA');
        }

        if( is_numeric($is_void) ) {
            $label = __('Pembatalan %s', $label);
        }

        return $label;
    }

    function array_filter_recursive($input) { 
        foreach ($input as &$value) { 
            if (is_array($value)) { 
                $value = $this->array_filter_recursive($value);
            }
        }

        return array_filter($input);
    }

    function _callStatusVoid ( $value, $modelName = false ) {
        $is_canceled = $this->filterEmptyField($value, $modelName, 'is_canceled');
        $canceled_date = $this->filterEmptyField($value, $modelName, 'canceled_date');

        if(!empty($is_canceled)){
            $statusDoc = $this->Html->tag('span', __('Void'), array(
                'class' => 'label label-danger'
            ));

            if(!empty($canceled_date)){
                $canceled_date = $this->formatDate($canceled_date, 'd M Y');
                $statusDoc .= '<br>'.$canceled_date;
            }
        }else{
            $statusDoc = $this->Html->tag('span', __('Aktif'), array(
                'class' => 'label label-success'
            ));
        }

        return $statusDoc;
    }

    function _callActionButtn ( $value, $modelName = false, $options = array() ) {
        $actionDoc = false;
        $is_canceled = $this->filterEmptyField($value, $modelName, 'is_canceled');
        $canceled_date = $this->filterEmptyField($value, $modelName, 'canceled_date');

        $detail = $this->filterEmptyField($options, 'Detail', 'label');
        $detail_url = $this->filterEmptyField($options, 'Detail', 'url', '#');

        $edit = $this->filterEmptyField($options, 'Edit', 'label');
        $edit_url = $this->filterEmptyField($options, 'Edit', 'url', '#');

        $void = $this->filterEmptyField($options, 'Void', 'label');
        $void_url = $this->filterEmptyField($options, 'Void', 'url', '#');

        if( !empty($detail) ) {
            $actionDoc .= $this->Html->link($detail, $detail_url, array(
                'class' => 'btn btn-info btn-xs'
            ));
        }
        
        if( empty($is_canceled) ){
            if( !empty($edit) ) {
                $actionDoc .= $this->Html->link($edit, $edit_url, array(
                    'class' => 'btn btn-primary btn-xs'
                ));
            }
            
            if( !empty($void) ) {
                $actionDoc .= $this->Html->link($void, $void_url, array(
                    'class' => 'btn btn-danger btn-xs ajaxModal',
                    'data-action' => 'cancel_invoice',
                ));
            }
        }

        return $actionDoc;
    }

    function _callTransactionStatus ( $data, $modelName = false, $fieldName = 'transaction_status' ) {
        $transaction_status = $this->filterEmptyField($data, $modelName, $fieldName);
        $canceled_date = $this->filterEmptyField($data, $modelName, 'canceled_date');

        switch ($transaction_status) {
            case 'paid':
                $customStatus = $this->Html->tag('span', __('Sudah Dibayar'), array(
                    'class' => 'label label-paid',
                ));
                break;

            case 'half_paid':
                $customStatus = $this->Html->tag('span', __('Dibayar Sebagian'), array(
                    'class' => 'label label-paid disabled',
                ));
                break;

            case 'void':
                $customStatus = $this->Html->tag('span', __('Void'), array(
                    'class' => 'label label-danger',
                ));

                if(!empty($canceled_date)){
                    $canceled_date = $this->formatDate($canceled_date, 'd M Y', false);
                    $customStatus .= '<br>'.$canceled_date;
                }
                break;

            case 'sold':
                $customStatus = $this->Html->tag('span', __('Sold'), array(
                    'class' => 'label label-danger',
                ));
                break;

            case 'posting':
                $customStatus = $this->Html->tag('span', __('Commit'), array(
                    'class' => 'label label-primary',
                ));
                break;

            case 'available':
                $customStatus = $this->Html->tag('span', __('Available'), array(
                    'class' => 'label label-success',
                ));
                break;

            case 'unposting':
                $customStatus = $this->Html->tag('span', __('Draft'), array(
                    'class' => 'label label-default',
                ));
                break;

            case 'completed':
                $customStatus = $this->Html->tag('span', __('Complete'), array(
                    'class' => 'label label-success',
                ));
                break;

            case 'finish':
                $customStatus = $this->Html->tag('span', __('Finish'), array(
                    'class' => 'label label-success',
                ));
                break;

            case 'out':
                $customStatus = $this->Html->tag('span', __('Proses'), array(
                    'class' => 'label label-warning',
                ));
                break;

            case 'progress':
                $customStatus = $this->Html->tag('span', __('Pending'), array(
                    'class' => 'label label-warning',
                ));
                break;

            case 'pending':
                $customStatus = $this->Html->tag('span', __('Pending'), array(
                    'class' => 'label label-default',
                ));
                break;

            case 'canceled':
                $customStatus = $this->Html->tag('span', __('Batal'), array(
                    'class' => 'label label-danger',
                ));
                break;

            case 'revised':
                $customStatus = $this->Html->tag('span', __('Direvisi'), array(
                    'class' => 'label label-warning',
                ));
                break;

            case 'rejected':
                $customStatus = $this->Html->tag('span', __('Ditolak'), array(
                    'class' => 'label label-danger',
                ));
                break;

            case 'closed':
                $customStatus = $this->Html->tag('span', __('Closed'), array(
                    'class' => 'label label-dark',
                ));
                break;

            case 'approved':
                $customStatus = $this->Html->tag('span', __('Disetujui'), array(
                    'class' => 'label label-success',
                ));
                break;

            case 'po':
                $customStatus = $this->Html->tag('span', __('PO'), array(
                    'class' => 'label label-pink',
                ));
                break;

            case 'open':
                $customStatus = $this->Html->tag('span', __('Open'), array(
                    'class' => 'label label-default',
                ));
                break;
            
            default:
                $customStatus = $this->Html->tag('span', __('Belum Dibayar'), array(
                    'class' => 'label label-default',
                ));
                break;
        }

        return $customStatus;
    }

    function _callProgressBar ( $status, $progress ) {
        $color = '';

        if($status == 'canceled'){
            $color = ' red';
        }

        $contentProgress = $this->Html->tag('div', sprintf(__('Progress : %s%%'), $progress), array(
            'class' => 'lbl-meter',
        ));
        $contentProgress .= $this->Html->div('meter nostripes'.$color, $this->Html->tag('span', '', array(
            'style' => 'width:'.$progress.'%;'
        )));

        return $this->Html->tag('div', $contentProgress, array(
            'class' => 'relative',
        ));
    }

    function _callPeriodeYear ( $minPeriode = 20, $maxPeriode = null ) {
        $year = array();

        if( !empty($maxPeriode) ) {
            $nowYear = $maxPeriode;
        } else {
            $nowYear = date('Y');
        }

        for ($i=0; $i < $minPeriode; $i++) {
            $value = $nowYear-$i;
            $year[$value] = $value;
        }

        return $year;
    }

    function _callGenerateNoRef( $id ) {
        return str_pad($id, 6, '0', STR_PAD_LEFT);
    }

    function _callListCheckboxCoas (  $coas ) {
        $list = array();
        $default_options = array(
            'type' => 'checkbox',
            'label'=> false,
            'required' => false,
            'div' => false,
        );

        if( !empty($coas) ) {
            foreach ($coas as $key => $coa) {
                if( is_array($coa) ) {
                    $list[] = $this->Html->tag('li', $this->Html->tag('label', $key));
                    $list = array_merge($list, $this->_callListCheckboxCoas($coa));
                } else {
                    $options = $default_options;
                    $options['value'] = $key;
                    $options['class'] = 'check-branch';

                    $list[] = $this->Html->tag('li', $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('coas.'.$key, $options).$coa), array(
                        'class' => 'checkbox',
                    )));
                }
            }
        }

        return $list;
    }

    function _callCheckboxCoas (  $coas ) {
        $btn = $this->Form->button(__('Pilih COA ').$this->Html->tag('div', '', array(
            'class' => 'caret',
        )), array(
            'class' => 'btn btn-default dropdown-toggle',
            'data-toggle' => 'dropdown',
        ));
        $headLabel = $this->Html->tag('label', __('COA'), array(
            'class' => 'block'
        ));


        $options = array(
            'type' => 'checkbox',
            'label'=> false,
            'required' => false,
            'div' => false,
            'class' => 'check-all',
        );
        $headLi = $this->Html->tag('li', $this->Html->tag('li', $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('coaall.', $options).__('Check/Uncheck All')), array(
            'class' => 'checkbox',
        ))));
        $divider = $this->Html->tag('li', '', array(
            'class' => 'divider',
        ));
        $ulContent = $this->Html->tag('ul', $headLi.$divider.implode('', $this->_callListCheckboxCoas($coas)), array(
            'class' => 'dropdown-menu parent-check-branch',
            'role' => 'menu',
        ));

        $result =  $this->Html->tag('div', $headLabel.$this->Html->tag('div', $btn.$ulContent, array(
            'class' => 'btn-group columnDropdown',
        )), array(
            'class' => 'list-field',
        ));

        return $this->Html->tag('div', $result, array(
            'class' => 'form-group'
        ));
    }

    function _callStatusReceipt ( $value, $modelName ) {
        $receipt_status = $this->filterEmptyField($value, $modelName, 'receipt_status');

        switch ($receipt_status) {
            case 'half':
                return $this->Html->tag('span', __('Sebagian'), array(
                    'class' => 'text-orange',
                ));
                break;
            case 'full':
                return $this->Html->tag('span', __('Diterima'), array(
                    'class' => 'text-green',
                ));
                break;
            default:
                return $this->Html->tag('span', __('Belum'), array(
                    'class' => 'text-grey',
                ));
                break;
        }
    }

    function _callStatusRetur ( $value, $modelName ) {
        $retur_status = $this->filterEmptyField($value, $modelName, 'retur_status');

        switch ($retur_status) {
            case 'half':
                return $this->Html->tag('span', __('Sebagian'), array(
                    'class' => 'text-orange',
                ));
                break;
            case 'full':
                return $this->Html->tag('span', __('Retur Semua'), array(
                    'class' => 'text-green',
                ));
                break;
            default:
                return $this->Html->tag('span', __('Belum'), array(
                    'class' => 'text-grey',
                ));
                break;
        }
    }

    // New Input Form - Nanti semua akan diganti kesini
    function _callInputForm ($fieldName, $options = false) {
        $default_options = array(
            'fieldName' => $fieldName,
            'label' => false,
            'frameClass' => 'form-group',
            'class' => 'form-control',
            'div' => false,
            'required' => false,
            'placeholder' => false,
            'text' => false,
            'disabled' => false,
        );

        if( !empty($options) ) {
            $default_options = array_merge($default_options, $options);
        }

        $attributes = $this->_callUnset(array(
            'label',
            'fieldName',
            'frameClass',
            'text',
            'div',
        ), $default_options);
        $default_options['attributes'] = $attributes;

        return $this->_View->element('blocks/common/forms/input', $default_options);
    }

    function _callGetDriver ( $value ) {
        $driver = $this->filterEmptyField($value, 'Driver', 'driver_name');
        $driver = $this->filterEmptyField($value, 'DriverPengganti', 'driver_name', $driver);

        return $driver;
    }

    function _callGetDataDriver ( $value ) {
        $driver = Common::hashEmptyField($value, 'Driver');
        $driver = Common::hashEmptyField($value, 'DriverPengganti', $driver);

        return $driver;
    }

    function _callGetExt ( $file = false ) {
        $fileArr = explode('.', $file);
        return end($fileArr);
    }

    function _getContentType ( $ext = false ) {
        $default_mime = array(
            'gif' => 'image/gif',
            'jpg' => 'image/jpeg', 
            'jpeg' => 'image/jpeg', 
            'png' => 'image/png',
            'pjpeg' => 'image/pjpeg',
            'x-png' => 'image/x-png',
            'pdf' => 'application/pdf',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        );

        if( !empty($ext) ) {
            if( !empty($default_mime[$ext]) ) {
                return $default_mime[$ext];
            } else {
                return 'application/octet-stream';
            }
        } else {
            return $default_mime;
        }
    }

    function printDataTreeCogs($data, $level){
        $cogs_title = '';
        $cogs_id = $data['Cogs']['id'];
        if(!empty($data['Cogs']['code'])){
            $codeCogs = $data['Cogs']['code'];

            if( !empty($parent['Cogs']['code']) ) {
                $codeCogs = sprintf('%s-%s', $parent['Cogs']['code'], $codeCogs);
            }

            $cogs_title = $this->Html->tag('label', $codeCogs);
        }
        $cogs_title .= $data['Cogs']['name'];
        $dataTree = $this->Html->tag('span', $cogs_title, array(
            'title' => $cogs_title,
        ));
        $dataTree .= $this->Html->link('<i class="fa fa-plus-circle"></i>', array(
            'controller' => 'settings',
            'action' => 'cost_center_add',
            $cogs_id,
        ), array(
            'escape' => false,
            'class' => 'bg-green'
        ));

        $dataTree .= $this->Html->link('<i class="fa fa-pencil-square-o"></i>', array(
            'controller' => 'settings',
            'action' => 'cost_center_edit',
            $cogs_id,
            $data['Cogs']['parent_id'],
        ), array(
            'escape' => false,
            'class' => 'bg-primary',
            'title' => 'edit'
        ));
        
        $dataTree .= $this->Html->link('<i class="fa fa-minus-circle"></i>', array(
            'controller' => 'settings',
            'action' => 'cost_center_toggle',
            $cogs_id,
        ), array(
            'escape' => false,
            'class' => 'bg-red'
        ), __('Anda yakin ingin menghapus Cost Center ini ?'));

        return $dataTree;
    }

    function _callNoRefCMS ( $id, $name ) {
        $str = substr($name, 0, 1);
        $str = ucwords($str);

        return __('%s%s', $id, $str);
    }
}