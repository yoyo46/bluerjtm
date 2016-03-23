<?php 
        $frameClass = !empty($frameClass)?$frameClass:'box-footer text-center action';
        $divClass = !empty($divClass)?$divClass:false;
        $submitText = !empty($submitText)?$submitText:__('Simpan');
        $submitOptions = !empty($submitOptions)?$submitOptions:array();
        $backText = !empty($backText)?$backText:__('Kembali');
        $btnClass = !empty($btnClass)?$btnClass:false;
        $backOptions = !empty($backOptions)?$backOptions:array();
?>
<div class="<?php echo $frameClass; ?>">
	<?php
    		$content = $this->Form->button($submitText, array_merge(array(
				'div' => false, 
				'class'=> sprintf('btn btn-success %s', $btnClass),
				'type' => 'submit',
			), $submitOptions));

			if( !empty($urlBack) ) {
	    		$content .= $this->Html->link($backText, $urlBack, array_merge(array(
	    			'escape' => false,
					'class'=> sprintf('btn btn-default %s', $btnClass),
				), $backOptions));
	    	}

	    	if( !empty($divClass) ) {
	    		echo $this->Html->tag('div', $content, array(
	    			'class' => $divClass,
    			));
	    	} else {
	    		echo $content;
	    	}
	?>
</div>