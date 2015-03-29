<div id="data-driver-name"><?php echo $driver_name;?></div>
<div id="data-no-sim"><?php echo $no_sim;?></div>
<div id="data-ttuj-form">
<?php 
		echo $this->Form->input('Laka.ttuj_id',array(
			'label'=> false, 
			'class'=>'form-control',
			'required' => false,
			'empty' => __('Pilih No TTUJ'),
			'options' => $ttujs,
			'id' => 'laka-ttuj-change'
		));
?>
</div>