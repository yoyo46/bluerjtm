<?php 
		$message = !empty($message)?$message:false;

		if( !empty($message) ) {
			echo $message;
		} else {
			echo __('Terjadi kesalahan! Halaman tidak ditemukan.');
		}
?>