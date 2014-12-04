<?php
		$close_tag = '';
	    $breadcrumbTemp = '';
	    $htmlbreadcrumb = '';

	    if( $this->Html->getCrumbs() || !empty($module_title) ) {
			echo '<ol class="breadcrumb">';
			$close_tag = '</ol>';
		}
		
	    if($this->Html->getCrumbs()):
	    	$home = __('Home');
	    	$breadcrumbTemp .= $this->Html->getCrumbs('', array(
	    		'text' => '<i class="fa fa-dashboard"></i> '.$home,
	    		'escape' => false
	    	));
	    	$htmlbreadcrumb .= $breadcrumbTemp;
	    endif;

	    echo $htmlbreadcrumb;
	    echo $close_tag;
?>
