<?php
        $ext = $this->Common->_callGetExt($filepath);
        $content_type = $this->Common->_getContentType($ext);
        $filename = String::uuid();

        if( !empty($basename) ) {
                $basename = $this->Common->toSlug($basename);
                $filename = __('%s-%s', $basename, $filename);
        }

        header('Pragma: public');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Content-Type: '.$content_type);
        header("Content-disposition: attachment; filename=\"" . $filename.".".$ext . "\""); 
	header('Content-Transfer-Encoding: binary');
        readfile($filepath);
        exit();
?>
