<?php 
        $data_local = !empty($data_local)?$data_local:false;
		$allow_closing = !empty($allow_closing)?$allow_closing:false;

        $ttuj_id = $this->Common->filterEmptyField($data_local, 'Revenue', 'ttuj_id');
?>
<div class="form-group">
    <?php 
    		if( $this->Common->_getAllowSave($allow_closing, $data_local) ) {
				$attrBrowse = array(
	                'class' => 'ajaxModal visible-xs browse-docs',
	                'escape' => false,
	                'title' => __('Data Truk'),
	                'data-action' => 'browse-form',
	                'data-change' => 'truckID',
	                'id' => 'truckBrowse',
	            );
				$urlBrowse = array(
	                'controller'=> 'ajax', 
	                'action' => 'getTrucks',
	                'revenue_manual',
	                $ttuj_id,
	            );
	            echo $this->Form->label('Revenue.truck_id', __('No. Pol * ').$this->Html->link('<i class="fa fa-plus-square"></i>', $urlBrowse, $attrBrowse));
    ?>
    <div class="row">
        <div class="col-sm-10">
        	<?php 
					echo $this->Form->input('Revenue.truck_id',array(
						'label'=> false, 
						'class'=>'form-control truck-revenue-id chosen-select',
						'required' => false,
						'empty' => __('Pilih No. Pol --'),
					));
			?>
        </div>
		<div class="col-sm-2 hidden-xs">
            <?php 
					$attrBrowse['class'] = 'btn bg-maroon ajaxModal';
                    echo $this->Html->link('<i class="fa fa-plus-square"></i>', $urlBrowse, $attrBrowse);
            ?>
        </div>
    </div>
    <?php 
    		} else {
        		$nopol = Common::hashEmptyField($data_local, 'Ttuj.nopol');
        		$nopol = Common::hashEmptyField($data_local, 'Revenue.nopol', $nopol);
        		$truck_id = Common::hashEmptyField($data_local, 'Revenue.truck_id');

				echo $this->Form->input('Revenue.nopol',array(
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'disabled' => true,
					'value' => $nopol,
				));
				echo $this->Form->hidden('Revenue.truck_id', array(
					'value' => $truck_id,
					'class' => 'truck-revenue-id',
				));
    		}
    ?>
</div>
<div class="form-group">
	<?php 
			echo $this->Form->input('Revenue.truck_capacity',array(
				'label'=> __('Kapasitas'), 
				'class'=>'form-control',
				'required' => false,
				'readonly' => true,
				'id' => 'revenue-truck-capacity',
			));
	?>
</div>
<div class="form-group">
	<?php 
			echo $this->Form->label('Revenue.from_city_id', __('Tujuan Dari'));
	?>
	<div class="row">
		<div class="col-sm-6">
			<?php 
					echo $this->Form->input('Revenue.from_city_id',array(
						'label'=> false, 
						'class'=>'form-control chosen-select from-city-revenue-change',
						'required' => false,
						'empty' => __('Dari Kota --'),
						'options' => !empty($toCities)?$toCities:false,
						'id' => 'from-city-revenue-id',
					));
			?>
		</div>
		<div class="col-sm-6">
			<?php 
					echo $this->Form->input('Revenue.to_city_id',array(
						'label'=> false, 
						'class'=>'form-control chosen-select',
						'required' => false,
						'empty' => __('Kota Tujuan --'),
						'options' => !empty($toCities)?$toCities:false,
						'id' => 'to-city-revenue-id',
					));
			?>
		</div>
	</div>
</div>