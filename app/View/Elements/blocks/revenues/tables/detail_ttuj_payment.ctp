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
			                    echo $this->Html->tag('th', __('No TTUJ'));
			                    echo $this->Html->tag('th', __('Tgl'), array(
			                        'width' => '5%',
			                    ));
			                    echo $this->Html->tag('th', __('NoPol'));
			                    echo $this->Html->tag('th', __('Customer'));
			                    echo $this->Html->tag('th', __('Dari'));
			                    echo $this->Html->tag('th', __('Tujuan'));
			                    echo $this->Html->tag('th', __('Supir'));
			                    echo $this->Html->tag('th', __('Jenis'), array(
			                        'width' => '5%',
			                        'class' => 'text-center',
			                    ));
			                    echo $this->Html->tag('th', __('Keterangan'));
			                    echo $this->Html->tag('th', __('Total'), array(
			                        'class' => 'text-center',
			                    ));
			                    echo $this->Html->tag('th', __('Biaya Dibayar'), array(
			                        'width' => '10%',
			                        'class' => 'text-center',
			                    ));
            					
            					if( empty($invoice) ) {
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