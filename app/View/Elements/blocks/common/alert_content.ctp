<?php 
		$confirm = !empty($confirm)?$confirm:false;
?>
<div class="content-alert">
	<?php 
			echo $this->Html->tag('div', $title_alert, array(
				'class' => 'modal-title'
			));
	?>
	<div class="modal-content">
		<?php
				echo $this->Html->tag('p', $content_alert);
		?>
		<div class="row">
			<?php 
					if( $confirm && !empty($url) ) {
						echo $this->Html->tag('div', $this->Html->link(__('NO'), '#', array(
							'class' => 'btn btn-default rum-btn btn-grey close-modal',
						)), array(
							'class' => 'col-sm-2 pull-right'
						));
						echo $this->Html->tag('div', $this->Html->link(__('YES'), $url, array(
							'class' => 'btn btn-warning rum-btn',
						)), array(
							'class' => 'col-sm-2 pull-right no-pright'
						));
					} else {
						echo $this->Html->tag('div', $this->Html->link(__('Ok'), '#', array(
							'class' => 'btn btn-default rum-btn btn-grey close-modal',
						)), array(
							'class' => 'col-sm-3 pull-right'
						));
					}
			?>
		</div>
	</div>
</div>