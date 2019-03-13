<div class="checkbox-info-detail <?php echo (!empty($this->request->data['Ttuj'])) ? '' : 'hide';?>">
	<div class="box box-primary">
	    <div class="box-header">
	        <h3 class="box-title"><?php echo __('Detail Biaya Uang Jalan / Komisi'); ?></h3>
	    </div>
	    <div class="box-body table-responsive">
	        <table class="table table-hover">
	        	<thead>
	        		<tr>
	        			<?php 
			                    echo $this->Html->tag('th', __('TTUJ'));
			                    echo $this->Html->tag('th', __('Biaya Dibayar'), array(
			                        'width' => '15%',
			                        'class' => 'text-center',
			                    ));
			                    echo $this->Html->tag('th', __('No Claim'), array(
			                        'width' => '15%',
			                        'class' => 'text-center',
			                    ));
			                    echo $this->Html->tag('th', __('STOOD'), array(
			                        'width' => '15%',
			                        'class' => 'text-center',
			                    ));
			                    echo $this->Html->tag('th', __('Lain-lain'), array(
			                        'width' => '15%',
			                        'class' => 'text-center',
			                    ));
			                    echo $this->Html->tag('th', __('Titipan'), array(
			                        'width' => '15%',
			                        'class' => 'text-center',
			                    ));
			                    echo $this->Html->tag('th', __('Potongan Claim'), array(
			                        'width' => '15%',
			                        'class' => 'text-center',
			                    ));
			                    echo $this->Html->tag('th', __('Unit Claim'), array(
			                        'width' => '8%',
			                        'class' => 'text-center',
			                    ));
			                    echo $this->Html->tag('th', __('Potongan LAKA'), array(
			                        'width' => '15%',
			                        'class' => 'text-center',
			                    ));
			                    echo $this->Html->tag('th', __('Ket. LAKA'), array(
			                        'width' => '15%',
			                        'class' => 'text-center',
			                    ));
			                    echo $this->Html->tag('th', __('Total Transfer'), array(
			                        'width' => '15%',
			                        'class' => 'text-center',
			                    ));
            					
            					if( empty($document_info) ) {
				                    echo $this->Html->tag('th', __('Action'), array(
				                    	'class' => 'action-biaya-ttuj',
			                    	));
				                }
			            ?>
	        		</tr>
	        	</thead>
                <?php
		    			echo $this->element('blocks/revenues/info_ttuj_payment_detail');
		    	?>
	    	</table>
	    </div>
	</div>
</div>