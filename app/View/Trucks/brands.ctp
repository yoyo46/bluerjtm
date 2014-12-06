<?php 
        $this->Html->addCrumb(__('Dashboard'));
?>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <div class="box-tools">
                        <?php
                            echo $this->Html->link('<i class="fa fa-plus"></i> Add Truck Brand', array(
                                'controller' => 'trucks',
                                'action' => 'brand_add'
                            ), array(
                                'escape' => false,
                                'class' => 'btn btn-app pull-right'
                            ));
                        ?>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body table-responsive no-padding">
                    <table class="table table-hover">
                        <tr>
                            <th>No.</th>
                            <th>Brand</th>
                            <th>Created</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        <?php
                        	$i = 1;
                        	if(!empty($truck_brands)){
    	                    	foreach ($truck_brands as $key => $value) {
    	                    		$value_data = $value['TruckBrand'];
    	                    		$id = $value_data['id'];
                        ?>
                        <tr>
                            <th><?php echo $i++;?></th>
                            <th><?php echo $value_data['name'];?></th>
                            <th><?php echo $this->Common->customDate($value_data['created']);?></th>
                            <th>
                            	<?php 
                            		if(!empty($value_data['status'])){
                            			echo '<span class="label label-success">Active</span>';	
                            		}else{
                            			echo '<span class="label label-danger">Non Active</span>';	
                            		}
                            		
                            	?>
                            </th>
                            <th>
                            	<?php 
                            		echo $this->Html->link('Edit', array(
                            			'controller' => 'trucks',
                            			'action' => 'brand_edit',
                            			$id
                            		), array(
                            			'class' => 'btn btn-primary btn-sm'
                            		));

                            		if(!empty($value_data['status'])){
    	                        		echo $this->Html->link('Disable', array(
    	                        			'controller' => 'trucks',
    	                        			'action' => 'brand_toggle',
    	                        			$id
    	                        		), array(
    	                        			'class' => 'btn btn-danger btn-sm',
    	                        			'title' => 'disable status brand'
    	                        		));
    	                        	}else{
    	                        		echo $this->Html->link('Enable', array(
    	                        			'controller' => 'trucks',
    	                        			'action' => 'brand_toggle',
    	                        			$id
    	                        		), array(
    	                        			'class' => 'btn btn-success btn-sm',
    	                        			'title' => 'enable status brand'
    	                        		));
    	                        	}
                            	?>
                            </th>
                        </tr>
                        <?php
                        		}
                        	}else{
                        ?>
                        <tr><td colspan="5"><?php echo __('Data not found.');?></tr>
                        <?php
                        	}
                        ?>
                    </table>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div>
    </div>
</div>