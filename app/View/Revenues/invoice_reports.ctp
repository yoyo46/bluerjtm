<?php
	$this->Html->addCrumb($sub_module_title);
    echo $this->element('blocks/revenues/search_report_invoice');
?>
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
				<th rowspan="2" align="center">
					<?php
						echo $this->Paginator->sort('Customer.name', __('Customer'), array(
                            'escape' => false
                        ));
					?>
				</th>
				<th rowspan="2" align="center"><?php echo __('Saldo Piutang');?></th>
				<th rowspan="2" align="center"><?php echo __('Current');?></th>
				<th colspan="3" align="center"><?php echo __('over due');?></th>
			</tr>
			<tr>
				<th align="center"><?php echo __('1- 15');?></th>
				<th align="center"><?php echo __('16 - 30');?></th>
				<th align="center"><?php echo __('> 30');?></th>
			</tr>
            <?php
                    if(!empty($customers)){
                        foreach ($customers as $key => $value) {
            ?>
            <tr>
                <td><?php echo $value['Customer']['name'];?></td>
                <td><?php echo $value['piutang'][0][0]['total_pituang'];?></td>
                <td><?php echo $value['current'][0][0]['current'];?></td>
                <td><?php echo $value['current_rev1to15'][0][0]['current_rev1to15'];?></td>
                <td><?php echo $value['current_rev16to30'][0][0]['current_rev16to30'];?></td>
                <td><?php echo $value['current_rev30'][0][0]['current_rev30'];?></td>
            </tr>
            <?php
                        }
                    }else{
                        echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                            'class' => 'alert alert-warning text-center',
                            'colspan' => '5'
                        )));
                    }
            ?>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>