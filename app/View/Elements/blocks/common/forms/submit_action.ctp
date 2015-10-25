<?php 
        $frameClass = !empty($frameClass)?$frameClass:'box-footer text-center action';
        $divClass = !empty($divClass)?$divClass:false;
?>
<div class="<?php echo $frameClass; ?>">
	<?php
    		$content = $this->Form->button(__('Simpan'), array(
				'div' => false, 
				'class'=> 'btn btn-success',
				'type' => 'submit',
			));

			if( !empty($urlBack) ) {
	    		$content .= $this->Html->link(__('Kembali'), $urlBack, array(
					'class'=> 'btn btn-default',
				));
	    	}

	    	if( !empty($divClass) ) {
	    		echo $this->Html->tag('div', $content, array(
	    			'class' => $divClass,
    			));
	    	}
	?>
</div>