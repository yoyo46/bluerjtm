<div class="wrapper-download">
	<?php         
			if( !empty($value) ) {
				$document_status = !empty($document_status)?$document_status:false;
		        $id = $this->Common->filterEmptyField($value, 'Import', 'id');
		        $total_data = $this->Common->filterEmptyField($value, 'Import', 'total_data');
		        $fetched_data = $this->Common->filterEmptyField($value, 'Import', 'fetched_data');
		        $document_status = $this->Common->filterEmptyField($value, 'Import', 'document_status', $document_status);
				$trigger_interval = 'true';

				if( !empty($limit_import) ) {
					$fetched_data += $limit_import;
				}
				
				$progress = $this->Report->_callPercentage($fetched_data, $total_data);
	?>
	<div id="header-download">
		<div class="head-page">
			<div class="callout callout-info">
			    <?php 
			    		if( !empty($total_data) ) {
			            	echo $this->Html->tag('h4', __('Proses upload %s Data', $this->Common->getFormatPrice($total_data)));
			            }

						if( $document_status == 'completed' && !empty($successfull_row) ) {
			            	echo $this->Html->tag('p', $successfull_row);
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
								$this->Html->tag('p', __('Untuk membatalkan proses %s.', $this->Html->link(__('klik disini'), array(
									'action' => 'import_cancel',
									$id,
								), array(
									'style' => 'color: #f56954;',
								), __('Anda yakin ingin membatalkan proses import ini?'))), array(
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
		</div>
		<?php
				echo $this->Html->link(__('Call Interval'), array(
					'action' => 'import_progress',
					$id,
					'admin' => false,
				), array(
					'class' => 'call-interval ajax-link hide',
					'data-wrapper-write' => '.wrapper-download',
				));
		?>
	</div>
	<?php
			}
	?>
</div>