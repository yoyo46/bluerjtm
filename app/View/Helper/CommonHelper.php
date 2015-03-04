<?php
class CommonHelper extends AppHelper {
	var $helpers = array(
        'Html', 'Number', 'Paginator'
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
            $thumbnail_view_path = Configure::read('__Site.thumbnail_display_view_path');
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
		return strip_tags($string);
	}

	function generateCoaTree ( $coas, $allowModule ) {
		$dataTree = '<ul>';
        if( !empty($coas) ) {
            foreach ($coas as $key => $coa) {
				$dataTree .= '<li class="parent_li">';
				$dataTree .= $this->Html->tag('span', $coa['Coa']['code'], array(
                    'title' => $coa['Coa']['code'],
                ));
                $dataTree .= $this->Html->link($coa['Coa']['name'], 'javascript:', array(
                    'escape' => false,
                ));

                if( in_array('insert_coas', $allowModule) ) {
                    $dataTree .= $this->Html->link('<i class="fa fa-plus-circle"></i>', array(
                        'controller' => 'settings',
                        'action' => 'coa_add',
                        $coa['Coa']['id'],
                    ), array(
                        'escape' => false,
                        'class' => 'bg-green'
                    ));
                }

                if( in_array('delete_coas', $allowModule) ) {
                    $dataTree .= $this->Html->link('<i class="fa fa-minus-circle"></i>', array(
                        'controller' => 'settings',
                        'action' => 'coa_toggle',
                        $coa['Coa']['id'],
                    ), array(
                        'escape' => false,
                        'class' => 'bg-red'
                    ));
                }

                if( !empty($coa['children']) ) {
                	$child = $coa['children'];
                	$dataTree .= $this->generateCoaTree($child);
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

    
    function toSlug($string) {
        if( is_string($string) ) {
            return strtolower(Inflector::slug($string, '-'));
        } else {
            return $string;
        }
    }

    function getModuleAllow ( $listMenu = false, $allowModule = false ) {
        $flagAllow = false;

        if( !empty($listMenu) ) {
            foreach ($listMenu as $key => $value) {
                if( in_array($value, $allowModule) ) {
                    $flagAllow = true;
                }
            }
        }

        return $flagAllow;
    }



    function getSorting ( $model = false,  $label = false ) {
        $named = $this->params['named'];
        
        if( !empty($named['sort']) && $named['sort'] == $model ) {
            if( !empty($named['direction']) && strtolower($named['direction']) == 'desc' ) {
                $label = sprintf('%s <i class="fa fa-sort-amount-desc"></i>', $label);
            } else {
                $label = sprintf('%s <i class="fa fa-sort-amount-asc"></i>', $label);
            }
        }

        if( $this->Paginator->hasPage() ) {
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
}