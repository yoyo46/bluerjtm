<?php 
class RjImageComponent extends Component {
    var $helpers = array('Session', 'Text');

	/**
	* Proses Upload Photo, Video, Rar
	*
	* @param array $uploadedInfo - data file berupa name, type, tmp_name, error, size
	*		array name - Nama file
	*		string type - Tipe file
	*		string tmp_name - Direktori penyimpanan dilocal
	*		boolean error - status upload file, True terjadi error, False tidak terjadi Error
	*		number size - Besar ukuran file
	* @param string $uploadTo - Nama Path Folder
	* @param string $prefix - Uniq content untuk penamaan file
	* @param string $options - Opsi tambahan parameter
	*		boolean favicon - True file berupa favicon, allow extension ico
	*		number max_size - Maksimal ukuran foto yang diupload
	*		number max_width - Maksimal lebar foto yang diupload
	*		number max_height - Maksimal panjang foto yang diupload
	*		array allowed_ext - Extension foto yang diperbolehkan
	*		boolean prefix_as_name - True menggunakan Prefix sebagai Nama file
	*		boolean rar - Allow extension rar
	* @param array $allow_only_favicon - Allow extension ico
	* @param array $allow_only_mimefavicon - Allow extension icon, 'image/vnd.microsoft.icon', 'image/ico', 'image/icon', 'text/ico', 'application/ico'
	* @param array $default_mime - Default allow extension file, 'image/gif', 'image/jpeg', 'image/png', 'image/pjpeg', 'image/x-png'
	* @param array $allowed_ext - Allow extension file, 'jpg', 'jpeg', 'png', 'gif', 'ico'
	* @param array $allowed_mime - allow extension file, 'image/gif', 'image/jpeg', 'image/png', 'image/pjpeg', 'image/x-png', 'image/vnd.microsoft.icon', 'image/ico', 'image/icon', 'text/ico', 'application/ico', 'image/x-icon'
	* @param array $result - hasil validasi foto berupa error, message
	*		boolean error - status upload file, True terjadi error, False tidak terjadi Error
	*		string message - Notifikasi pesan alert
	* @param array $baseuploadpath - Direktori upload file
	* @param array $thumbnailPath - Direktori upload file Thumbnail
	* @param array $upload_dir - Direktori path upload
	* @param array $upload_dir - Direktori path upload
	* @param array $file_info - Informasi File yang diupload
	*		string dirname - Direktori file
	*		string basename - Nama file beserta extension
	*		string extension - Extension file
	*		string filename - Nama file tanpa extension
	* @param string $filename - Nama file dari prefix dan hasil extension $file_info
	* @param string $upload_path - Direktori folder yang akan diupload
	* @param string $upload_sub_path - Direktori folder year[yyyy]/month[mm]
	* @param string $fullsizePath - Direktori path untuk file ukuran asli
	* @param string $project_path - Direktori path sesuai dengan nama project ( Microsite )
	* @param string $uploadSource - Direktori folder yang akan diupload sudah berikut $upload_sub_path
	* @param string $imagePath - Full Path direktori untuk file ukuran sebenarnya
	* @param array $sizes - Ukuran file yang diupload berupa Panjang dan Lebar
	* @param number $width - Ukuran lebar file yang diupload
	* @param number $height - Ukuran panjang file yang diupload
	* @param number $scale - Hasil skala file yang diupload
	* @param string $uploaded - resize file sesuai dengan ukuran maksimal file
	* @param array $dimensionList - Daftar dimensi yang akan dibuatkan thumbnail
	* @param array $generateThumb - Proses pembuatan thumbnail sesuai dengan dimensi yang sudah ditentukan
	* @return array $result
	*		boolean error - status Proses upload file, True terjadi error, False tidak terjadi Error
	*		string imageName - Nama file
	*		string imageWidth - Lebar file
	*		string imageHeight - Panjang file
	*		string imageSize - Ukuran file
	*/
    function upload($uploadedInfo, $uploadTo, $prefix, $options = array(),$fromurl=false){
    	App::import('Vendor', 'Thumb', array('file' => 'Thumb'.DS.'ThumbLib.inc.php'));
		
		$allow_only_favicon = array('ico');
		$allowed_ext = Configure::read('__Site.allowed_ext');
		$allow_only_mimefavicon = array('image/vnd.microsoft.icon', 'image/ico', 'image/icon', 'text/ico', 'application/ico',
				'image/x-icon');
		$default_mime = array('image/gif', 'image/jpeg', 'image/png', 'image/pjpeg', 'image/x-png', 'application/pdf');
		$allowed_mime = $default_mime;
		$is_upload_cdn = (isset($options['is_upload_cdn']) ) ? $options['is_upload_cdn'] : true;
		$is_banner = ((!empty($options['banner'])) ? true : false);
		$upload_with_l = ((isset($options['upload_with_l'])) ? $options['upload_with_l'] : true);
		$is_mobile = (isset($options['is_mobile'])) ? $options['is_mobile'] : false;

		if(!empty($options) && !empty($options['favicon'])) {
			$allowed_ext = $allow_only_favicon;
			$allowed_mime = $allow_only_mimefavicon;
		}

    	$this->options = array(
			'max_size' => Configure::read('__Site.max_image_size'),
			'max_width' => Configure::read('__Site.max_image_width'),
			'max_height' => Configure::read('__Site.max_image_height'),
			'allowed_ext' => $allowed_ext,
			'allowed_mime' => $allowed_mime,
			'prefix_as_name'=> true,
			'rar' => false
		);

		foreach($options as $key=>$value) {
			$this->options[$key] = $value;
		}

		$result = $this->validateFile($uploadedInfo);
		/**
		* @REST API berguna untuk return value berupa json yang akan digunakan oleh Aplikasi Mobile
		*
		* Di comment sementara sampai Development V3 selesai
		*/
		if(!$result['error'] || $this->Rest->isActive()) {
		// if( !$result['error'] ) {
			$baseuploadpath = Configure::read('__Site.upload_path');

			$project_path = '';
			$upload_sub_path = '';
			$file_info = pathinfo($uploadedInfo["name"]);
			$upload_dir = str_replace("/", DS, $uploadTo);
			$upload_path = $baseuploadpath.$upload_dir;
			$filename = $prefix.'.'.$file_info['extension'];
			$file_info['extension'] = strtolower($file_info['extension']);
			$file_info['filename'] = strtolower($file_info['filename']);
			$thumbnailPath = Configure::read('__Site.thumbnail_view_path').$uploadTo;
			$fullsizePath = $thumbnailPath.Configure::read('__Site.fullsize');

			if($this->options['rar'] == false){
				if( !empty($this->options['project_path']) ) {
					$project_path = $this->options['project_path'].DS;
					$fullsizePath = sprintf('%s/%s', $fullsizePath, $this->options['project_path']);
					$upload_path = $upload_path.$project_path;

					if( !file_exists($upload_path) ) {
						mkdir($upload_path, 0755);
						chmod($upload_path, 755);
					}
				}
			
				if( !file_exists($fullsizePath) ) {
					mkdir($fullsizePath, 0755);
					chmod($fullsizePath, 755);
				}

				$folder_sub_path = $this->generateSubPathFolder($filename);
				$upload_sub_path = $this->makeDir( $upload_path, $fullsizePath.DS, $folder_sub_path );
				$fullsizePath .= DS.$upload_sub_path;
				$fullsizePath = str_replace('/', DS, $fullsizePath);

				if( empty($upload_sub_path) ) {
					return false;
				}
			}
			
			if($this->options['rar'] == false){
				$upload_path .= $upload_sub_path;
				$upload_path = str_replace('/', DS, $upload_path);
			}

			if (file_exists($upload_path.$filename)) {
				$i = 0;
				while (file_exists($upload_path.$file_filename.$i.'.'.$file_info['extension'])) {
					$i++;
				}
				$filename = $file_filename.$i.'.'.$file_info['extension'];
			}
			
			$uploadSource = $upload_path.$filename;
			$fullsizePath = $fullsizePath.$filename;

			if(empty($uploadedInfo)) {
				return false;
			} else if (isset($uploadedInfo['name'])){
				if( !move_uploaded_file( $uploadedInfo["tmp_name"], $uploadSource ) ){
					if( $fromurl ) {
						$tempName = tempnam($uploadSource, 'php_files');
						$imgRawData = file_get_contents($uploadedInfo["tmp_name"]);
		   				file_put_contents($uploadSource, $imgRawData);
					}
				} else {
					chmod($uploadSource, 755);
				}

				if($this->options['rar'] == false){
					$name_with_sub_name = $upload_sub_path.$filename;
					$name_db = str_replace(DS, '/', '/'.$name_with_sub_name);
					$imagePath = Configure::read('__Site.cache_view_path').str_replace(DS, '/', $upload_dir.Configure::read('__Site.fullsize').DS.$project_path.$name_with_sub_name);

					if( in_array($file_info['extension'], Configure::read('__Site.allowed_ext')) ) {
						$sizes = getimagesize($uploadSource);
						$width = $sizes[0];
						$height = $sizes[1];
						$scale = $this->getScale( $width, $height, $this->options['max_width'], $this->options['max_height'] );
						
						$uploaded = '';
						if(!$is_banner){
							$uploaded = $this->resizeImage($uploadSource, $width, $height, $scale, $options);
						}

						if( str_replace(DS, '', $upload_dir) == Configure::read('__Site.profile_photo_folder') ) {
							$dimensionList = Configure::read('__Site.dimension_profile');
						} else {
							$name_dir = str_replace(DS, '', $upload_dir);
							if($is_banner){
								$name_dir = 'slider_banner';
							}

							$dimensionList = $this->rulesDimensionImage($name_dir);
						}

						if( !empty($dimensionList) && !empty($uploaded) ) {
							foreach ($dimensionList as $key => $dimension) {
								$folder_sub_path = $this->generateSubPathFolder($filename);
								$generateThumb = $this->createThumbs($upload_path, $thumbnailPath, $filename, $key, $dimension, $folder_sub_path);
							}
						}
						copy($uploadSource, $fullsizePath);

						$result = array(
							'error' => 0,
							'imagePath' => $imagePath, 
							'imageName' => $name_db, 
							'imageWidth' => ceil($width * $scale), 
							'imageHeight' => ceil($height * $scale),
							'imageSize' => filesize($fullsizePath)
						);
					} else {
						copy($uploadSource, $fullsizePath);

						$result = array(
							'error' => 0,
							'imageName' => str_replace(DS, '/', '/'.$upload_sub_path.$filename), 
							'imagePath' => $imagePath, 
							'imageSize' => filesize($fullsizePath)
						);
					}
				}else{
					$result = array(
						'error' => 0,
						'imageName' => str_replace(DS, '/', '/'.$upload_sub_path.$filename), 
					);
				}
			}
		}
        return $result;
    }

	/**
	* Proses Pembuatan Thumbnail
	*
	* @param string $pathToImages - Direktori folder yang akan diupload
	* @param array $pathToThumbs - Direktori upload file Thumbnail
	* @param string $filename - Nama file
	* @param string $keyDimension - ID Dimensi
	* @param string $dimension - ukuran Dimensi
	* @param string $thumbWidth - Lebar Dimensi
	* @param string $thumbHeight - Panjang Dimensi
	* @param string $dir - open direktori folder
	* @param array $info - Informasi File
	*		string dirname - Direktori file
	*		string basename - Nama file beserta extension
	*		string extension - Extension file
	*		string filename - Nama file tanpa extension
	* @param string $upload_sub_path - Direktori folder year[yyyy]/month[mm]
	* @param string $sourcePath - Direktori folder yang akan diupload sudah berikut $upload_sub_path
	* @param Object $this->thumb - Open Library Thumb
	* @param Object $imgCrop - Proses thumbnail sesuai dengan ukuran dimensi
	*/
    function createThumbs( $pathToImages, $pathToThumbs, $Realname, $keyDimension, $dimension, $folder_sub_path = '' )  {
    	list($thumbWidth, $thumbHeight) = explode('x', $dimension);
        $dir = opendir( $pathToImages );

        // parse path for the extension
        $info = pathinfo($pathToImages . $Realname);
        $info['extension'] = strtolower($info['extension']);
        $pathToThumbs = sprintf('%s%s%s', $pathToThumbs, $keyDimension, DS);
        $pathToThumbs = str_replace('/', DS, $pathToThumbs);
        if( !empty($this->options['project_path']) ) {
	        if( !file_exists($pathToThumbs) ) {
				mkdir($pathToThumbs, 0755);
				chmod($pathToThumbs, 755);
			}
			$pathToThumbs .= $this->options['project_path'].DS;
		}

        if( !file_exists($pathToThumbs) ) {
			mkdir($pathToThumbs, 0755);
			chmod($pathToThumbs, 755);
		}

		if( !empty($Realname) ) {
			$upload_sub_path = $this->makeDir( false, $pathToThumbs, $folder_sub_path );
			$pathToThumbs .= $upload_sub_path.$info['basename'];
			$pathToThumbs = str_replace('/', DS, $pathToThumbs);
		}

		$sourcePath = str_replace('/', DS, $pathToImages . $Realname);
		copy($sourcePath, $pathToThumbs);

		if($info['extension'] != "ico"){
			$this->thumb = PhpThumbFactory::create($pathToThumbs);
			$imgCrop = $this->thumb->adaptiveResize($thumbWidth, $thumbHeight);
		}

		if($info['extension'] == "png"){
            imagepng($imgCrop->workingImageCopy, $pathToThumbs, 9);
        } elseif($info['extension'] == "jpg" || $info['extension'] == "jpeg") {
            imagejpeg($imgCrop->workingImageCopy, $pathToThumbs, 90);
        } elseif($info['extension'] == "gif") {
            imagegif($imgCrop->workingImageCopy, $pathToThumbs);
        }

        closedir( $dir );
    }

	/**
	* Scaling Foto
	*
	* @param number $w - Lebar Foto
	* @param number $h - Panjang Foto
	* @param number max_w - Maksimal Lebar Foto
	* @param number max_h - Maksimal Panjang Foto
	* @param number wscale - Lebar Skala
	* @param number hscale - Panjang Skala
	* @param number scale - Skala Foto
	* @return number - Skala Foto
	*/
    function getScale ( $w, $h, $max_w, $max_h ) {
    	if (($w > $max_w) && ($h > $max_h)){
			$wscale = $max_w/$w;
			$hscale = $max_h/$h;
			if($wscale <= $hscale) {
				$scale = $wscale;	
			} else {
				$scale = $hscale;	
			}
		} elseif ($w > $max_w){
			$scale = $max_w/$w;
		} elseif ($h > $max_h){
			$scale = $max_h/$h;
		} else {
			$scale = 1;
		}
		return $scale;
    }

	/**
	* Craete Direktori
	*
	* @param string $upload_path - Direktori folder yang akan diupload
	* @param array $thumbnailPath - Direktori upload file Thumbnail
	* @param string $year - Tahun Upload
	* @param string $month - Bulan Upload
	* @param string $yearDir - Direktori Folder Tahun
	* @param string $monthDir - Direktori Folder Bulan
	* @param string $yearFullsizeDir - Direktori Folder Tahun untuk file ukuran sebenarnya
	* @param string $monthFullsizeDir - Direktori Folder Bulan untuk file ukuran sebenarnya
	* @return string - Direktori Folder File
	*/
    function makeDir( $upload_path = false, $thumbnailPath = false, $folder_sub_path = '' ) {
    	$year = date('Y');
    	$month = date('m');

    	if( !empty($upload_path) ) {
	    	$yearDir = $upload_path.date('Y').DS;
	    	$monthDir = $yearDir.date('m').DS;

	    	if( !file_exists($yearDir) ) {
	    		mkdir($yearDir, 0755);
				chmod($yearDir, 755);
	    	}
	    	if( !file_exists($monthDir) ) {
	    		mkdir($monthDir, 0755);
				chmod($monthDir, 755);
	    	}

	    	if($folder_sub_path != '') {
		    	$subDir = $monthDir.$folder_sub_path.DS;
		    	if( !file_exists($subDir) ) {
		    		mkdir($subDir, 0755, true);
					chmod($subDir, 755);
		    	}
		    }
	    	
    	}
    	
    	if( !empty($thumbnailPath) ) {
    		$yearFullsizeDir = str_replace('/', DS, $thumbnailPath.$year.DS);
    		$monthFullsizeDir = $yearFullsizeDir.$month.DS;

    		if( !file_exists($yearFullsizeDir) ) {
    			mkdir($yearFullsizeDir, 0755);
				chmod($yearFullsizeDir, 755);
    		}
    		if( !file_exists($monthFullsizeDir) ) {
    			mkdir($monthFullsizeDir, 0755);
				chmod($monthFullsizeDir, 755);
    		}
    		$FullsizeDir = str_replace('/', DS, $thumbnailPath.DS);
    		if($folder_sub_path != '') {
    			$subFullsizeDir = $monthFullsizeDir.$folder_sub_path.DS;
	    		if( !file_exists($subFullsizeDir) ) {
	    			mkdir($subFullsizeDir, 0755, true);
					chmod($subFullsizeDir, 755);
	    		}
	    	}
		}

		if($folder_sub_path != '') {
			return sprintf('%s/%s/%s/', $year, $month, $folder_sub_path);
		} else {
			return sprintf('%s/%s/', $year, $month);
		}
    }

	/**
	* Validasi File
	*
	* @param array $file - data file berupa name, type, tmp_name, error, size
	*		array name - Nama file
	*		string type - Tipe file
	*		string tmp_name - Direktori penyimpanan dilocal
	*		boolean error - status upload file, True terjadi error, False tidak terjadi Error
	*		number size - Besar ukuran file
	* @param array $error - hasil validasi foto berupa error, message
	*		boolean error - status upload file, True terjadi error, False tidak terjadi Error
	*		string message - Notifikasi pesan alert
	* @param array $file_info - Informasi File yang diupload
	*		string dirname - Direktori file
	*		string basename - Nama file beserta extension
	*		string extension - Extension file
	*		string filename - Nama file tanpa extension
	* @return array - hasil validasi foto berupa error, message
	*/
	function validateFile($file) {
		$error = array(
			'error' => 0,
			'message' => ''
		);

		if($file['error'] != 0) {
			$error = array(
				'error' => 1,
				'message' => __('File tidak valid')
			);
		} else {
			$file_info = pathinfo($file["name"]);
			$file_info['extension'] = strtolower($file_info['extension']);
			
			if(!in_array($file_info['extension'], $this->options['allowed_ext'])) {
				$error = array(
					'error' => 1,
					'message' => sprintf(__('Mohon hanya mengunggah file berekstensi %s'), implode(', ', $this->options['allowed_ext']))
				);
			} else if(!in_array($file['type'], $this->options['allowed_mime'])) {
				$error = array(
					'error' => 1,
					'message' => sprintf(__('Mohon hanya mengunggah file berekstensi %s'), implode(', ', $this->options['allowed_ext']))
				);
			} else if($file['size'] > $this->options['max_size']) {
				$error = array(
					'error' => 1,
					'message' => sprintf(__('Besar file maksimum adalah %s'), $this->format_size($this->options['max_size'], 'MB'))
				);
			}
		}
		return $error;
	}

	/**
	* Resize Foto
	*
	* @param string $image - Direktori folder yang akan diupload
	* @param number $width - Ukuran lebar file yang diupload
	* @param number $height - Ukuran panjang file yang diupload
	* @param number scale - Skala Foto
	* @param string $options - Opsi tambahan parameter
	*		boolean favicon - True file berupa favicon, allow extension ico
	*		number max_size - Maksimal ukuran foto yang diupload
	*		number max_width - Maksimal lebar foto yang diupload
	*		number max_height - Maksimal panjang foto yang diupload
	*		array allowed_ext - Extension foto yang diperbolehkan
	*		boolean prefix_as_name - True menggunakan Prefix sebagai Nama file
	*		boolean rar - Allow extension rar
	* @param number $newImageWidth - Ukuran lebar yang akan diresize
	* @param number $newImageHeight - Ukuran panjang yang akan diresize
	* @param string $newImage - Generate image sesuai dengan panjang dan lebar yang telah diresize
	* @param array $file_info - Informasi File yang diupload
	*		string dirname - Direktori file
	*		string basename - Nama file beserta extension
	*		string extension - Extension file
	*		string filename - Nama file tanpa extension
	* @param string $white - Alokasi warna Putih
	* @param string $source - hasil resize foto
	* @return string - hasil resize foto
	*/
    function resizeImage( $image, $width, $height, $scale, $options=array() ) {
        $newImageWidth = ceil($width * $scale);
        $newImageHeight = ceil($height * $scale);
        $newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
		
		$file_info = pathinfo($image);
		$file_info['extension'] = strtolower($file_info['extension']);
		// $mime_type = mime_content_type($image);
		
		if((isset($this->options['career']) && $this->options['career'] == 1) || (isset($this->options['excess']) && $this->options['excess'] == 1) || (isset($this->options['transparent']) && $this->options['transparent'] == 1)) {
			$white = imagecolorallocate($newImage, 238, 238, 238);
		} else {
			$white = imagecolorallocate($newImage, 255, 255, 255);
		}

        $source = "";

        if($file_info['extension'] == "png"){
 			//imagecolortransparent($newImage, $this->color['black']);
			imagefilledrectangle($newImage, 0, 0, $newImageWidth, $newImageHeight, $white);
			$source = @imagecreatefrompng($image);
        } elseif ($file_info['extension'] == "jpg" || $file_info['extension'] == "jpeg"){
            $source = @imagecreatefromjpeg($image);
        } elseif ($file_info['extension'] == "gif"){
            $source = @imagecreatefromgif($image);
            // imagejpeg($source);
        }

        if( $source ) {
	        if($file_info['extension'] == "gif" or $file_info['extension'] == "png"){
			    imagecolortransparent($newImage, imagecolorallocatealpha($newImage, 0, 0, 0, 127));
			    imagealphablending($newImage, false);
			    imagesavealpha($newImage, true);
		  	}
		  	if($file_info['extension'] != 'ico'){
	        	@imagecopyresampled($newImage,$source,0,0,0,0,$newImageWidth,$newImageHeight,$width,$height);
		  	}
	        if($file_info['extension'] == "png"){
	            imagepng($newImage,$image, 9);
	        } elseif ($file_info['extension'] == "jpg" || $file_info['extension'] == "jpeg"){
	            imagejpeg($newImage, $image, 90);
	        } elseif ($file_info['extension'] == "gif"){
	            imagegif($newImage,$image);
	            // imagejpeg($newImage);
	        }

        	return $image;
	    } else {
	    	return false;
	    }
    }

	/**
	* Resize Thumbnail Foto
	*
	* @param string $thumb_image_name - Direktori folder Thumbnail yang akan diupload
	* @param string $image - Direktori folder yang akan diupload
	* @param number $width - Ukuran lebar file yang diupload
	* @param number $height - Ukuran panjang file yang diupload
	* @param number $start_width - Posisi Lebar Crop Foto
	* @param number $start_height - Posisi Panjang Crop Foto
	* @param number scale - Skala Foto
	* @param string $options - Opsi tambahan parameter
	*		boolean favicon - True file berupa favicon, allow extension ico
	*		number max_size - Maksimal ukuran foto yang diupload
	*		number max_width - Maksimal lebar foto yang diupload
	*		number max_height - Maksimal panjang foto yang diupload
	*		array allowed_ext - Extension foto yang diperbolehkan
	*		boolean prefix_as_name - True menggunakan Prefix sebagai Nama file
	*		boolean rar - Allow extension rar
	* @param number $newImageWidth - Ukuran lebar yang akan diresize
	* @param number $newImageHeight - Ukuran panjang yang akan diresize
	* @param string $newImage - Generate image sesuai dengan panjang dan lebar yang telah diresize
	* @param array $file_info - Informasi File yang diupload
	*		string dirname - Direktori file
	*		string basename - Nama file beserta extension
	*		string extension - Extension file
	*		string filename - Nama file tanpa extension
	* @param string $white - Alokasi warna Putih
	* @param string $source - hasil resize foto
	* @return string - hasil resize foto
	*/
    function resizeThumbnailImage($thumb_image_name, $image, $width, $height, $start_width, $start_height, $scale){
        $newImageWidth = ceil($width * $scale);
        $newImageHeight = ceil($height * $scale);
        $newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
			
		$file_info = pathinfo($image);
		$file_info['extension'] = strtolower($file_info['extension']);
		
		$white = imagecolorallocate($newImage, 255, 255, 255);
		
        $source = "";
        if($file_info['extension'] == "png"){
			//imagecolortransparent($newImage, $black);		
			imagefilledrectangle($newImage, 0, 0, $newImageWidth, $newImageHeight, $white);
            $source = imagecreatefrompng($image);
        } elseif($file_info['extension'] == "jpg" || $file_info['extension'] == "jpeg"){
            $source = imagecreatefromjpeg($image);
        } elseif($file_info['extension'] == "gif"){
            $source = imagecreatefromgif($image);
        }
        imagecopyresampled($newImage,$source,0,0,$start_width,$start_height,$newImageWidth,$newImageHeight,$width,$height);
        if($file_info['extension'] == "png"){
            imagepng($newImage,$thumb_image_name, 9);
        }elseif($file_info['extension'] == "jpg" || $file_info['extension'] == "jpeg"){
            imagejpeg($newImage,$thumb_image_name, 90);
        }elseif($file_info['extension'] == "gif"){
            imagegif($newImage,$thumb_image_name);
        }
        return $thumb_image_name;
    }

	/**
	* Crop Foto
	*
	* @param string $thumb_width - Lebar Thumbnail
	* @param number $x1 - Posisi Lebar Crop Foto
	* @param number $y1 - Posisi Panjang Crop Foto
	* @param number $x2 - Posisi Lebar Crop Foto
	* @param number $y2 - Posisi Panjang Crop Foto
	* @param number $w - Lebar Foto
	* @param number $h - Panjang Foto
	* @param array $thumbLocation - Foto Thumbnail berupa name, type, tmp_name, error, size
	*		array name - Nama Foto
	*		string type - Tipe Foto
	*		string tmp_name - Direktori penyimpanan dilocal
	*		boolean error - status upload Foto, True terjadi error, False tidak terjadi Error
	*		number size - Besar ukuran Foto
	* @param array $imageLocation - Foto berupa name, type, tmp_name, error, size
	*		array name - Nama Foto
	*		string type - Tipe Foto
	*		string tmp_name - Direktori penyimpanan dilocal
	*		boolean error - status upload Foto, True terjadi error, False tidak terjadi Error
	*		number size - Besar ukuran Foto
	* @param string $uploadTo - Nama Path Folder
	* @param number $w_img - Ukuran lebar foto
	* @param number $h_img - Ukuran panjang foto
	* @param number $scale - Skala Foto
	* @param string $sourcePath - Direktori folder Foto
	* @param string $source_image - Direktori folder untuk foto ukuran sebenernya yang akan diupload
	* @param string $target_thumb - Direktori folder untuk foto thumbnail
	* @param string $target_image - Direktori folder untuk foto
	* @param string $image - Direktori File foto
	* @param array $sizeImg - Ukuran file yang diupload berupa Panjang dan Lebar
	* @param number $w_scale - Ukuran Skala lebar foto 
	* @param number $h_scale - Ukuran Skala panjang foto
	* @param string $cropped - Proses Crop Foto
	* @param array $photo_dimension - Daftar dimensi yang akan dibuatkan thumbnail
	* @param array $thumbnailPath - Direktori upload foto Thumbnail
	* @return string - Direktori File foto
	*/
    function cropImage($thumb_width, $x1, $y1, $x2, $y2, $w, $h, $thumbLocation, $imageLocation, $uploadTo, $w_img, $h_img){
    	if( !empty($w) ) {
	    	App::import('Vendor', 'Thumb', array('file' => 'Thumb'.DS.'ThumbLib.inc.php'));
			
	        $scale = $thumb_width/$w;
	        $sourcePath = Configure::read('__Site.cache_view_path').'/'.$uploadTo;
	        $source_image = str_replace($sourcePath.'/'.Configure::read('__Site.fullsize'), Configure::read('__Site.upload_path').'/'.$uploadTo,$imageLocation);
			$target_thumb = str_replace($sourcePath, Configure::read('__Site.thumbnail_view_path').'/'.$uploadTo,$imageLocation);
			$target_image = $source_image;
			$image = str_replace($sourcePath.'/'.Configure::read('__Site.fullsize').'/', '',$imageLocation);

			$target_image = str_replace("/", DS,$target_image);
			$source_image = str_replace("/", DS,$source_image);
			$sizeImg = getimagesize($target_image);

			if( !empty($sizeImg[0]) && !empty($sizeImg[1]) ) {
				$w_scale = $sizeImg[0]/$w_img;
				$h_scale = $sizeImg[1]/$h_img;

				$x1 = ceil($x1 * $w_scale);
				$y1 = ceil($y1 * $h_scale);
				$w = ceil($w * $w_scale);
				$h = ceil($h * $h_scale);
			}

	        $cropped = $this->resizeThumbnailImage($target_image, $source_image,$w,$h,$x1,$y1,$scale);
			$cropped = str_replace(Configure::read('__Site.upload_path'), Configure::read('__Site.cache_view_path'), $cropped);
			$cropped = str_replace('\\', '/', $cropped);

			if( $uploadTo == Configure::read('__Site.profile_photo_folder') ) {
				$photo_dimension = Configure::read('__Site.dimension_profile');
			} else {
				$photo_dimension = Configure::read('__Site.dimension');
			}

			$thumbnailPath = Configure::read('__Site.thumbnail_view_path').DS.$uploadTo.DS;
			$file_info = pathinfo($source_image);
			copy($source_image, $target_thumb);

			if( !empty($photo_dimension) ) {
				foreach ($photo_dimension as $key => $dimension) {
					$folder_sub_path = $this->generateSubPathFolder($file_info['basename']);					
					$this->createThumbs($file_info['dirname'].DS, $thumbnailPath, $file_info['basename'], $key, $dimension, $folder_sub_path);
				}
			}

	        return '/'.$image;
	    } else {
	    	return false;
	    }
    }

	/**
	* Check Format Ukuran File
	*
	* @param number file_size - Maksimal ukuran foto yang diupload
	* @param string $sizetype - Tipe Ukuran Foto
	* @param number $filesize - Convert berdasarkan Tipe Ukuran File
	* @return number - Convert Ukuran File
	*/
	function format_size($file_size, $sizetype) {
		switch(strtolower($sizetype)){
			case "kb":
				$filesize = $file_size * .0009765625; // bytes to KB
			break;
			case "mb":
				$filesize = $file_size * .0009765625 * .0009765625; // bytes to MB
			break;
			case "gb":
				$filesize = $file_size * .0009765625 * .0009765625 * .0009765625; // bytes to GB
			break;
		}
		if($filesize <= 0){
			$filesize = 0;
		} else {
			$filesize = round($filesize, 2).' '.$sizetype;
		}
		return $filesize;
	}
	
	/**
	* Check Format Ukuran File
	*
	* @param string image - Nama File
	* @param string $path - Nama Path Folder
	*/
	function delete($image, $path) {
		if($path) {
			$path = $path.DS;
		}
        @unlink(Configure::read('__Site.upload_path').DS.$path.$image);
        // @unlink(Configure::read('__Site.thumbnail_upload_path').DS.$path.$image);
	}

	/**
	*
	*	generate subfolder
	* 	ketentuan xxxxx-[x]xxxx-xxxxxx satu huruf di tali ke 2
	*	@param string $filename : nama file
	*	@return string
	*/
    function generateSubPathFolder($filename) {
    	$folder_sub_path = '';
    	$sub_part = explode('-',$filename);
    	$folder_sub_path1 = 'a';
    	$folder_sub_path2 = 'a';
    	
    	if(!empty($sub_part[1])) {
			$folder_sub_path1 = substr($sub_part[1], 0, 1);
		}

		if(!empty($sub_part[3])) {
			$folder_sub_path2 = substr($sub_part[3], 1, 1);
		}

		$folder_sub_path = $folder_sub_path1;
    	
    	return (string)$folder_sub_path;
    }

    /**
	*
	*	aturan dalam mengupload gambar
	*	@param string $directory_name : nama directory gambar
	*	@return array
	*/
    function rulesDimensionImage($directory_name){
    	$result = array();
    	// Configure::read('__Site.logo_photo_folder')
    	if($directory_name == 'logos') {
    		$result = array(
				'xsm' => '100x40',
				'xm' => '165x165',
				'xxsm' => '240x96'
			);
    	} else if($directory_name == 'slider_banner'){
    		$result = array(
				'ls' => '960x190'
			);
    	} else {
    		$result = array(
				's' => '150x84',
				'm' => '300x169',
				'l' => '855x481'
			);
    	}

    	return $result;
    }
}
?>