<?php 
        $this->Html->addCrumb($sub_module_title);
?>
<div class="row">
    <div class="col-sm-9">
		<div class="box box-success">
		    <div class="box-header">
		        <?php 
		                echo $this->Html->tag('h3', $sub_module_title, array(
		                    'class' => 'box-title'
		                ));
		        ?>
		    </div>
		    <div class="box-body table-responsive">
		        <table class="table table-hover">
		            <tr>
		                <?php 
		                        echo $this->Html->tag('th', __('No TTUJ'));
		                        echo $this->Html->tag('th', __('Tgl TTUJ'));
		                        echo $this->Html->tag('th', __('Customer'));
		                        echo $this->Html->tag('th', __('Dari'));
		                        echo $this->Html->tag('th', __('Tujuan'));
		                        echo $this->Html->tag('th', __('Status'));
		                        echo $this->Html->tag('th', __('Dibuat'));
		                ?>
		            </tr>
		            <?php
		                    if(!empty($ttujs)){
		                        foreach ($ttujs as $key => $value) {
		                            $id = $value['Ttuj']['id'];
		            ?>
		            <tr>
		                <td><?php echo $value['Ttuj']['no_ttuj'];?></td>
		                <?php 
			                    echo $this->Html->tag('td', date('d M Y', strtotime($value['Ttuj']['ttuj_date'])), array(
			                        'class' => 'text-center',
			                    ));
		                ?>
		                <td><?php echo $value['Ttuj']['customer_name'];?></td>
		                <td><?php echo $value['Ttuj']['from_city_name'];?></td>
		                <td><?php echo $value['Ttuj']['to_city_name'];?></td>
		                <?php 
		                        if( !empty($value['Ttuj']['is_invoice']) ) {
		                            echo $this->Html->tag('td', '<span class="label label-code">Invoiced</span>');
		                        } else if(!empty($value['Ttuj']['is_pool'])){
		                            echo $this->Html->tag('td', '<span class="label label-success">Sampai Pool</span>');
		                        } else if(!empty($value['Ttuj']['is_balik'])){
		                            echo $this->Html->tag('td', '<span class="label label-info">Balik</span>');
		                        } else if(!empty($value['Ttuj']['is_bongkaran'])){
		                            echo $this->Html->tag('td', '<span class="label label-warning">Bongkaran</span>');
		                        } else if(!empty($value['Ttuj']['is_arrive'])){
		                            echo $this->Html->tag('td', '<span class="label label-info">Tiba</span>');
		                        } else if(!empty($value['Ttuj']['is_draft'])){
		                            echo $this->Html->tag('td', '<span class="label label-default">Draft</span>');
		                        } else{
		                            echo $this->Html->tag('td', '<span class="label label-primary">Commit</span>');
		                        }
		                ?>
		                <td><?php echo $this->Common->customDate($value['Ttuj']['created']);?></td>
		            </tr>
		            <?php
		                        }
		                    }
		            ?>
		        </table>
		    </div>
		</div>
	</div>
    <div class="col-sm-3">
        <?php 
				echo $this->element('blocks/trucks/info_driver');
        ?>
    </div>
</div>