<?php 
        $frameClass = !empty($frameClass)?$frameClass:'box-footer text-center action';
        $divClass = !empty($divClass)?$divClass:false;
        $submitText = !empty($submitText)?$submitText:__('Simpan');
        $backText = !empty($backText)?$backText:__('Kembali');
        $btnClass = !empty($btnClass)?$btnClass:false;
?>
<div class="<?php echo $frameClass; ?>">
	<?php
    		$content = $this->Form->button($submitText, array(
				'div' => false, 
				'class'=> sprintf('btn btn-success %s', $btnClass),
				'type' => 'submit',
			));

			if( !empty($urlBack) ) {
	    		$content .= $this->Html->link($backText, $urlBack, array(
	    			'escape' => false,
					'class'=> sprintf('btn btn-default %s', $btnClass),
				));
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