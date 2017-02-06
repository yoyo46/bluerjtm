<?php         
        $id = $this->Common->filterEmptyField($value, 'Report', 'id');
        $title = $this->Common->filterEmptyField($value, 'Report', 'title');
        $total_data = $this->Common->filterEmptyField($value, 'Report', 'total_data');
        $fetched_data = $this->Common->filterEmptyField($value, 'Report', 'fetched_data');
        $document_status = $this->Common->filterEmptyField($value, 'Report', 'document_status');
		$progress = $this->Report->_callPercentage($fetched_data, $total_data);
		$trigger_interval = 'true';
?>
<div class="wrapper-download">
	<div id="header-download">
		<div class="head-page">
		    <?php 
		            echo $this->Html->tag('h3', __('%s Data ditemukan', $this->Common->getFormatPrice($total_data)));

					if( $document_status == 'completed' ) {
						echo $this->Html->tag('div',
							$this->Html->link(
								$this->Html->tag('span', $this->Common->icon('fa fa-download'), array(
									'class' => 'text-only',
								)).__('Download'), array(
								'controller' => 'reports',
								'action' => 'download',
								$id,
								'admin' => true,
							), array(
								'escape' => false,
							)), array(
							'class' => 'wrapper-progress',
						));
						$trigger_interval = 'false';
					} else if( in_array($document_status, array( 'pending', 'progress' )) ) {
						echo $this->Html->tag('div', 
							$this->Html->tag('div', 
								$this->Html->tag('div', '', array(
									'class' => 'bar',
									'style' => __('width:%s%;', $progress),
								)), array(
								'class' => 'progress progress-success progress-striped active',
							)).
							$this->Html->tag('p', __('Mohon menunggu .. %s%%', $progress), array(
								'class' => 'please-wait',
							)).
							$this->Html->tag('p', __('Link Download akan muncul setelah mencapai 100%', $progress), array(
								'class' => 'please-wait',
							)), array(
							'class' => 'wrapper-progress',
						));
					}

					echo $this->Form->hidden('interval', array(
						'class' => 'trigger-interval',
						'value' => $trigger_interval,
					));
			?>
		</div>
		<?php
				echo $this->Html->link(__('Call Interval'), array(
					'controller' => 'reports',
					'action' => 'report_execute',
					$id,
					'admin' => false,
				), array(
					'class' => 'call-interval ajax-link hide',
					'data-wrapper-write' => '.wrapper-download',
				));
		?>
	</div>
</div>