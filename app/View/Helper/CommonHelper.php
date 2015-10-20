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

    function getSorting ( $model = false,  $label = false, $is_print = false, $sorting = true ) {
        $named = $this->params['named'];
        
        if( !empty($sorting) && !empty($model) && $this->Paginator->hasPage() && empty($is_print) ) {
            return $this->Paginator->sort($model, $label, array(
                'escape' => false
            ));
        } else {
            return $label;
        }
    }

    function calcFloat ( $total, $float ) {
        $result = 0;

        if(!empty($total) && !empty($float)){
            $result = $total * ($float / 100);
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
                'void_date' => '<br>'.$this->customDate($data['Invoice']['canceled_date'], 'd/m/Y'),
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

    function getRowCoa ( $coas, $parent = false ) {
        $dataTree = '';
        if( !empty($coas) ) {
            foreach ($coas as $key => $coa) {
                $id = $coa['Coa']['id'];
                $coa_title = '';
                $codeCoa = '-';

                if(!empty($coa['Coa']['with_parent_code'])){
                    $codeCoa = $coa['Coa']['with_parent_code'];
                } else if(!empty($coa['Coa']['code'])){
                    $codeCoa = $coa['Coa']['code'];

                    if( !empty($parent['Coa']['code']) ) {
                        $codeCoa = sprintf('%s-%s', $parent['Coa']['code'], $codeCoa);
                    }
                }
                $coa_title = $coa['Coa']['name'];

                $content  = $this->Html->tag('td', $this->Form->checkbox('CashBankDetail.coa_id.', array(
                    'class' => 'check-option',
                    'value' => $id
                )), array(
                    'class' => 'checkbox-detail'
                ));
                $content  .= $this->Html->tag('td', $codeCoa.$this->Form->input('CashBankDetail.coa_id.', array(
                    'type' => 'hidden',
                    'value' => $id
                )));
                $content .= $this->Html->tag('td', $coa_title);
                
                // $debit_form = $this->Form->input('CashBankDetail.debit.', array(
                //     'type' => 'text',
                //     'class' => 'form-control input_price',
                //     'label' => false,
                //     'div' => false,
                //     'required' => false,
                // ));
                // $content .= $this->Html->tag('td', $debit_form, array(
                //     'class' => 'action-search hide'
                // ));

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
                    'class' => 'form-control input_price',
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
                    'class' => 'child-search child-search-'.$id,
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
        
        $dataTree .= $this->Html->link('<i class="fa fa-minus-circle"></i>', array(
            'controller' => 'settings',
            'action' => 'coa_toggle',
            $coa_id,
        ), array(
            'escape' => false,
            'class' => 'bg-red'
        ), __('Anda yakin ingin menghapus COA ini ?'));

        return $dataTree;
    }

    function getBiayaTtuj ( $ttuj, $data_type, $format_currency = true, $tampilkan_sisa = true ) {
        $total = 0;
        $biaya = 0;

        switch ($data_type) {
            case 'uang_kuli_muat':
                $biaya = $uang_kuli_muat = !empty($ttuj['Ttuj']['uang_kuli_muat'])?$ttuj['Ttuj']['uang_kuli_muat']:0;
                $uang_kuli_muat_dibayar = !empty($ttuj['uang_kuli_muat_dibayar'])?$ttuj['uang_kuli_muat_dibayar']:0;

                $total = $uang_kuli_muat - $uang_kuli_muat_dibayar;
                break;
            case 'uang_kuli_bongkar':
                $biaya = $uang_kuli_bongkar = !empty($ttuj['Ttuj']['uang_kuli_bongkar'])?$ttuj['Ttuj']['uang_kuli_bongkar']:0;
                $uang_kuli_bongkar_dibayar = !empty($ttuj['uang_kuli_bongkar_dibayar'])?$ttuj['uang_kuli_bongkar_dibayar']:0;

                $total = $uang_kuli_bongkar - $uang_kuli_bongkar_dibayar;
                break;
            case 'asdp':
                $biaya = $asdp = !empty($ttuj['Ttuj']['asdp'])?$ttuj['Ttuj']['asdp']:0;
                $asdp_dibayar = !empty($ttuj['asdp_dibayar'])?$ttuj['asdp_dibayar']:0;

                $total = $asdp - $asdp_dibayar;
                break;
            case 'uang_kawal':
                $biaya = $uang_kawal = !empty($ttuj['Ttuj']['uang_kawal'])?$ttuj['Ttuj']['uang_kawal']:0;
                $uang_kawal_dibayar = !empty($ttuj['uang_kawal_dibayar'])?$ttuj['uang_kawal_dibayar']:0;

                $total = $uang_kawal - $uang_kawal_dibayar;
                break;
            case 'uang_keamanan':
                $biaya = $uang_keamanan = !empty($ttuj['Ttuj']['uang_keamanan'])?$ttuj['Ttuj']['uang_keamanan']:0;
                $uang_keamanan_dibayar = !empty($ttuj['uang_keamanan_dibayar'])?$ttuj['uang_keamanan_dibayar']:0;

                $total = $uang_keamanan - $uang_keamanan_dibayar;
                break;
            case 'commission':
                $biaya = $commission = !empty($ttuj['Ttuj']['commission'])?$ttuj['Ttuj']['commission']:0;
                // $commission_extra = !empty($ttuj['Ttuj']['commission_extra'])?$ttuj['Ttuj']['commission_extra']:0;
                $commission_dibayar = !empty($ttuj['commission_dibayar'])?$ttuj['commission_dibayar']:0;

                // $total = $commission + $commission_extra - $commission_dibayar;
                $total = $commission - $commission_dibayar;
                break;
            case 'uang_jalan_2':
                $biaya = $uang_jalan_2 = !empty($ttuj['Ttuj']['uang_jalan_2'])?$ttuj['Ttuj']['uang_jalan_2']:0;
                $uang_jalan_2_dibayar = !empty($ttuj['uang_jalan_2_dibayar'])?$ttuj['uang_jalan_2_dibayar']:0;

                $total = $uang_jalan_2 - $uang_jalan_2_dibayar;
                break;
            case 'uang_jalan_extra':
                $biaya = $uang_jalan_extra = !empty($ttuj['Ttuj']['uang_jalan_extra'])?$ttuj['Ttuj']['uang_jalan_extra']:0;
                $uang_jalan_extra_dibayar = !empty($ttuj['uang_jalan_extra_dibayar'])?$ttuj['uang_jalan_extra_dibayar']:0;

                $total = $uang_jalan_extra - $uang_jalan_extra_dibayar;
                break;
            case 'commission_extra':
                $biaya = $commission_extra = !empty($ttuj['Ttuj']['commission_extra'])?$ttuj['Ttuj']['commission_extra']:0;
                $commission_extra_dibayar = !empty($ttuj['commission_extra_dibayar'])?$ttuj['commission_extra_dibayar']:0;

                $total = $commission_extra - $commission_extra_dibayar;
                break;
            
            default:
                $biaya = $uang_jalan_1 = !empty($ttuj['Ttuj']['uang_jalan_1'])?$ttuj['Ttuj']['uang_jalan_1']:0;
                // $uang_jalan_2 = !empty($ttuj['Ttuj']['uang_jalan_2'])?$ttuj['Ttuj']['uang_jalan_2']:0;
                // $uang_jalan_extra = !empty($ttuj['Ttuj']['uang_jalan_extra'])?$ttuj['Ttuj']['uang_jalan_extra']:0;
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
                $data_model = explode('.', $field_model);
                $data_model = array_filter($data_model);
                if( !empty($data_model) ) {
                    list($modelName, $fieldName) = $data_model;
                } else {
                    $modelName = false;
                    $fieldName = false;
                }

                $style = !empty($dataColumn['style'])?$dataColumn['style']:false;
                $name = !empty($dataColumn['name'])?$dataColumn['name']:false;
                $display = isset($dataColumn['display'])?$dataColumn['display']:true;
                $child = !empty($dataColumn['child'])?$dataColumn['child']:false;
                $rowspan = !empty($dataColumn['rowspan'])?$dataColumn['rowspan']:false;
                $class = !empty($dataColumn['class'])?$dataColumn['class']:false;
                $fix_column = !empty($dataColumn['fix_column'])?$dataColumn['fix_column']:false;
                $data_options = !empty($dataColumn['data-options'])?$dataColumn['data-options']:false;
                $align = !empty($dataColumn['align'])?$dataColumn['align']:false;
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
                            if( !empty($child) ) {
                                $colspan = count($child);
                            } else {
                                $colspan = false;
                            }

                            if( !empty($is_print) ) {
                                $data_options = false;
                            }

                            $content = $this->Html->tag('th', $this->getSorting($field_model, $name, $is_print, $sorting), array(
                                'class' => sprintf('%s %s %s %s', $addClass, $key_field, $class, $_class),
                                'style' => $style,
                                'colspan' => $colspan,
                                'rowspan' => $rowspan,
                                'data-options' => $data_options,
                                'align' => $align,
                            ));

                            if( $fix_column && empty($is_print) ) {
                                $content .= '</tr></thead><thead><tr style="'.$_style.'">';
                            }

                            // Append Child
                            if( !empty($child) ) {
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
                $value = $this->getCurrencyPrice($value);
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

    function _getButtonPostingUnposting ( $revenue = false ) {
        $posting = false;
        $invoiced = false;
        $revenue_status = true;

        if( !empty($revenue['Revenue']['transaction_status']) && $revenue['Revenue']['transaction_status'] == 'posting' ) {
            $posting = true;
        }
        if( !empty($revenue['Revenue']['transaction_status']) && in_array($revenue['Revenue']['transaction_status'], array( 'invoiced', 'half_invoiced' )) ) {
            $invoiced = true;
        }
        if( isset($revenue['Revenue']['status']) && empty($revenue['Revenue']['status']) ) {
            $revenue_status = false;
        }

        if( !$invoiced && $revenue_status ) {
            echo $this->Form->button(__('Posting'), array(
                'type' => 'submit',
                'class'=> 'btn btn-success submit-form btn-lg',
                'action_type' => 'posting'
            ));
            
            echo $this->Form->button(__('Unposting'), array(
                'type' => 'submit',
                'class'=> 'btn btn-primary submit-form',
                'action_type' => 'unposting'
            ));
        }
    }

    function _getPrint ( $options = false, $showHideColumn = false ) {
        $_excel = isset($options['_excel'])?$options['_excel']:true;
        $_pdf = isset($options['_pdf'])?$options['_pdf']:true;
        $_attr = isset($options['_attr'])?$options['_attr']:array();
        $result = false;
        $resultContent = '';
        $default_attr = array(
            'escape' => false,
            'class' => false,
        );
        $urlDefault['controller'] = !empty($this->params['controller'])?$this->params['controller']:false;
        $urlDefault['action'] = $this->action;

        $pass = !empty($this->params['pass'])?$this->params['pass']:array();
        $named = !empty($this->params['named'])?$this->params['named']:array();
        $urlDefault = array_merge($urlDefault, $pass);
        $urlDefault = array_merge($urlDefault, $named);

        if( !empty($_attr) ) {
            $default_attr = array_merge($default_attr, $_attr);
        }

        if( !empty($_excel) ) {
            $urlExcel = $urlDefault;
            $urlExcel[] = 'excel';
            $_excel_attr = $default_attr;
            $_excel_attr['class'] = $default_attr['class'].' btn btn-success pull-right';
            $result .= $this->Html->link('<i class="fa fa-download"></i> Download Excel', $urlExcel, $_excel_attr);
        }
        if( !empty($_pdf) ) {
            $urlPdf = $urlDefault;
            $urlPdf[] = 'pdf';
            $_pdf_attr = $default_attr;
            $_pdf_attr['class'] = $default_attr['class'].' btn btn-primary pull-right';
            $result .= $this->Html->link('<i class="fa fa-download"></i> Download PDF', $urlPdf, $_pdf_attr);
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
        )), array(
            'class' => 'no-print print-action',
        ));
    }

    function filterEmptyField ( $value, $modelName, $fieldName = false, $empty = false, $removeTag = true ) {
        $result = '';
        
        if( empty($modelName) ) {
            $result = !empty($value)?$value:$empty;
        } else if( empty($fieldName) ) {
            $result = !empty($value[$modelName])?$value[$modelName]:$empty;
        } else {
            $result = !empty($value[$modelName][$fieldName])?$value[$modelName][$fieldName]:$empty;
        }

        if( !empty($removeTag) && !is_array($result) ) {
            return $this->safeTagPrint($result);
        } else {
            return $result;
        }
    }

    function getMergePrepayment ( $prepayment, $class = false ) {
        $result = false;
        $content = array();
        $nodoc = $this->filterEmptyField($prepayment, 'CashBank', 'nodoc');
        $dt = $this->customDate($this->filterEmptyField($prepayment, 'CashBank', 'tgl_cash_bank'), 'd M Y');
        $coa_name = $this->filterEmptyField($prepayment, 'Coa', 'name');
        $receiver_name = $this->filterEmptyField($prepayment, 'Receiver', 'name');
        $description = $this->filterEmptyField($prepayment, 'CashBank', 'description');
        $debit_total = $this->filterEmptyField($prepayment, 'CashBank', 'debit_total', 0);
        $credit_total = $this->filterEmptyField($prepayment, 'CashBank', 'credit_total', 0);

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
        $content[] = $this->Html->tag('td', $this->Number->format($debit_total, '', array('places' => 0)), array(
            'style' => 'text-align:right;',
        ));
        $content[] = $this->Html->tag('td', $this->Number->format($credit_total, '', array('places' => 0)), array(
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

    function getNotif($type_notif = false, $data, $header = true){
        $url = 'javascript;';
        if(!empty($data['Notification']['url'])){
            $url = unserialize($data['Notification']['url']);
        }

        $content_notif = $data['Notification']['name'];
        if($header){
            App::uses('TextHelper', 'View/Helper');
            $this->Text = new TextHelper(new View(null));

            $content_notif = $this->Text->truncate($content_notif, 150, 
                array(
                    'ending' => '...',
                    'exact' => false
                )
            );
        }

        if(!empty($type_notif)){
            if($url != 'javascript:'){
                $url = array_merge($url, array(
                    'ntf' => $data['Notification']['id']
                ));
            }

            switch ($type_notif) {
                case 'warning':
                    $type_notif = sprintf('<i class="fa fa-%s warning"></i> ', $data['Notification']['icon_modul']);
                break;
                case 'success':
                    $type_notif = sprintf('<i class="fa fa-%s success"></i> ', $data['Notification']['icon_modul']);
                break;
                case 'danger':
                    $type_notif = sprintf('<i class="fa fa-%s danger"></i> ', $data['Notification']['icon_modul']);
                break;
            }
        }else{
            $type_notif = '';
        }

        $content = $this->Html->link(sprintf('%s%s', $type_notif, $content_notif), $url, array(
            'escape' => false
        ));

        return $this->Html->tag('li', $content);
    }

    /**
    *
    *   function format tanggal
    *   @param string $dateString : tanggal
    *   @param string $format : format tanggal
    *   @return string tanggal
    */
    function formatDate($dateString, $format = false, $separator = '-') {
        if( empty($dateString) || $dateString == '0000-00-00' || $dateString == '0000-00-00 00:00:00') {
            return $separator;
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

    function getCheckboxBranch () {
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

            if( empty($this->request->data['GroupBranch']['group_branch']) ) {
                $default_options['checked'] = true;
            }

            foreach ($branches as $branch_id => $city_name) {
                $branchCheckboxOptions = $default_options;
                $branchCheckboxOptions['value'] = $branch_id;
                $branchCheckboxOptions['class'] = 'check-branch';

                $tmpArr[] = $this->Html->tag('li', $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('GroupBranch.group_branch.'.$branch_id, $branchCheckboxOptions).$city_name), array(
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

                $headLi = $this->Html->tag('li', $this->Html->tag('li', $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('GroupBranch.group_branch.'.$branch_id, $branchCheckboxOptions).__('Check/Uncheck All')), array(
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

    function convertPriceToString ( $price, $result = '' ) {
        if( !empty($price) ) {
            $resultTmp = str_replace(array(',', ' '), array('', ''), trim($price));

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

    function getCurrencyPrice ($price) {
        return $this->Number->currency($price, Configure::read('__Site.config_currency_code'), array('places' => 0));
    }

    function getFormatPrice ($price, $empty = 0) {
        if( !empty($price) ) {
            return $this->Number->currency($price, '', array('places' => 0));
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
}