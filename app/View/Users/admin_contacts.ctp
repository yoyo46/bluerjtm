<?php
	$this->Html->addCrumb(__('Contacts'));
	echo $this->element('blocks/users/refine');
?>
<div id="button-head-nav">
	<?php
		echo $this->Html->link(__('Add Contact'), 
			array(
				'controller' => 'users',
				'action' => 'add',
				'admin' => true
			),
			array(
				'class' => 'btn btn-success'
			)
		);
		// echo '&nbsp;';
		// echo $this->Html->link(__('Ministry'), 
		// 	array(
		// 		'controller' => 'users',
		// 		'action' => 'ministry_add',
		// 		'admin' => true
		// 	),
		// 	array(
		// 		'class' => 'btn btn-info'
		// 	)
		// );
	?>
</div>
<div class="table-responsive">
	<table class="table table-bordered table-hover table-striped tablesorter">
		<thead>
			<tr>
				<th>
					<?php 
						echo $this->Paginator->sort('Contact.first_name', __('Name'));
					?>
				</th>
				<th>
					<?php 
						echo $this->Paginator->sort('Contact.email', __('Email'));
					?>
				</th>
				<th>
					<?php 
						echo $this->Paginator->sort('Contact.phone_contact', __('No. Telepon'));
					?>
				</th>
				<th>
					<?php 
						echo $this->Paginator->sort('Contact.gender_id', __('Gender'));
					?>
				</th>
				<th>
					<?php 
						echo __('Nama Instansi');
					?>
				</th>
				<th>
					<?php 
						echo $this->Paginator->sort('Contact.modified', __('Last Register'));
					?>
				</th>
				<th><?php echo __('Action');?></th>
			</tr>
		</thead>
		<tbody>
			<?php
				if(!empty($contacts)){
					foreach ($contacts as $key => $value) {
			?>
			<tr>
				<td>
					<?php 
						$full_name = $value['Contact']['first_name'];
						if(!empty($value['Contact']['last_name'])){
							$full_name .= ' '.$value['Contact']['last_name'];
						}

						echo $full_name;
					?>
				</td>
				<td><?php echo $value['Contact']['email'];?></td>
				<td class="txt_center"><?php echo (!empty($value['Contact']['phone_contact'])) ? $value['Contact']['phone_contact'] : ' - ';?></td>
				<td class="txt_center"><?php echo $value['Contact']['gender'];?></td>
				<td class="txt_center"><?php echo (!empty($value['Contact']['instansi'])) ? $value['Contact']['instansi'] : ' - ';?></td>
				<td><?php echo $this->Common->customDate($value['Contact']['modified']);?></td>
				<td class="actions">
					<?php 
						$id = $value['Contact']['id'];
						echo $this->Html->link('<span class="fa fa-pencil-square-o"></span>'.' Edit', 
							array(
								'controller' => 'users',
								'action' => 'edit',
								$id,
								'admin' => true
							),
							array(
								'class' => 'btn btn-info',
								'escape' => false
							)
						);
						echo $this->Html->link('<span class="fa fa-exclamation-circle"><span>'.' Delete', 
							array(
								'controller' => 'users',
								'action' => 'delete',
								$id,
								'admin' => true
							),
							array(
								'class' => 'btn btn-danger',
								'escape' => false
							),
							sprintf(__('Are you sure want to delete %s ?'), $full_name)
						);
					?>
				</td>
			</tr>
			<?php
					}
				}else{
			?>
			<tr>
				<td colspan="7" class="text-center"><?php echo __('contact not found.');?></td>
			</tr>			
			<?php
				}
			?>
		</tbody>
	</table>
	<?php echo $this->element('blocks/pagination'); ?>
</div>
<?php 
	echo $this->Form->create('Contact', array(
		'url'=> array(
			'controller' => 'users',
			'action' => 'read_excel',
			'admin' => true
		), 
		'role' => 'form',
		'inputDefaults' => array('div' => false),
		'type' => 'file',
		'class' => 'form-horizontal'
	));
?>
<div class="form-group">
		<?php
				echo $this->Form->label('excel_read', __('File Excel'), array(
					'class' => 'control-label col-sm-2'
				));
				echo $this->Form->input('excel_read', array(
					'type' => 'file',
					'label'=> false, 
					'div' => array(
						'class' => 'controls col-sm-10',
						'female' => 'Wanita'
					),
				));
		?>
</div>
<div class="form-group btn-submit">
		<?php
				echo $this->Form->button(__('Submit'), array(
					'div' => false, 
					'class'=> 'btn btn-success btn-lg',
					'type' => 'submit',
				));
		?>
</div>
<?php
	echo $this->Form->end();
?>