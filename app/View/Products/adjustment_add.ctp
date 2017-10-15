<?php
		$title = __('Penyesuaian Qty');
		$urlRoot = array(
			'controller' => 'products',
			'action' => 'adjustment',
			'admin' => false,
		);
		$view = !empty($view)?$view:false;
		$value = !empty($value)?$value:false;
		$status = Common::hashEmptyField($value, 'Adjustment.status');

		$this->Html->addCrumb($title, $urlRoot);
		$this->Html->addCrumb($sub_module_title);

		echo $this->Form->create('ProductAdjustment', array(
			'class' => 'adjust-form',
		));
?>
<div class="row">
	<div class="col-sm-6">
		<div class="box box-primary">
		    <?php 
		            echo $this->element('blocks/common/box_header', array(
		                'title' => $title,
		            ));
		    ?>
		    <div class="box-body">
		    	<?php 
						echo $this->Common->buildInputForm('transaction_date', __('Tgl *'), array(
							'type' => 'text',
		                    'textGroup' => $this->Common->icon('calendar'),
		                    'class' => 'form-control pull-right custom-date',
						));
						echo $this->Common->buildInputForm('note', __('Keterangan'));
			    ?>
		    </div>
		</div>
	</div>
</div>
<?php     	
        echo $this->element('blocks/products/adjustment/tables/detail_products');
?>
<div class="box-footer text-center action">
	<?php
			echo $this->Html->link(__('Kembali'), $urlRoot, array(
				'class'=> 'btn btn-default',
			));
			
			if( $view != 'detail' ) {
				$this->Common->_getButtonPostingUnposting( $value, 'ProductAdjustment', array( 'Commit', 'Draft' ) );
			}
	?>
</div>
<?php
		echo $this->Form->hidden('session_id');
		echo $this->Form->end();
?>