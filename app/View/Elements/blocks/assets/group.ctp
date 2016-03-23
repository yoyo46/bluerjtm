<?php 
		$value = !empty($value)?$value:false;
        $umur_ekonomis = $this->Common->filterEmptyField($value, 'AssetGroup', 'umur_ekonomis');
        $nilai_sisa = $this->Common->filterEmptyField($value, 'AssetGroup', 'nilai_sisa');
?>
<div id="asset-group-content">
	<?php 
			echo $this->Form->hidden('AssetGroup.umur_ekonomis',array(
	            'value'=> $umur_ekonomis,
	            'class' => 'umur_ekonomis',
	        ));
	        echo $this->Form->hidden('AssetGroup.nilai_sisa',array(
	            'value'=> $nilai_sisa,
	            'class' => 'nilai_sisa',
	        ));
	?>
</div>