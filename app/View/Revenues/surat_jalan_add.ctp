<?php
		$this->Html->addCrumb(__('Surat Jalan'), array(
			'controller' => 'revenues',
			'action' => 'surat_jalan',
		));
		$this->Html->addCrumb($sub_module_title);

		$titleBrowse = __('Pilih TTUJ');
		$id = !empty($id)?$id:false;

		echo $this->Form->create('SuratJalan', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
    		'autocomplete'=> 'off', 
		));
?>
<div class="box box-primary">
    <?php 
            echo $this->element('blocks/common/box_header', array(
                'title' => __('Informasi Penerimaan'),
            ));
    ?>
    <div class="box-body">
    	<?php 
    			if( !empty($value) ) {
					$id = $this->Common->filterEmptyField($value, 'SuratJalan', 'id');
                    $noref = str_pad($id, 6, '0', STR_PAD_LEFT);


					$contentForm = $this->Html->tag('label', __('No. Referensi'));
					$contentForm .= $this->Html->tag('div', $noref);

					echo $this->Html->tag('div', $contentForm, array(
						'class' => 'form-group',
					));
    			}

				echo $this->Form->input('nodoc',array(
					'label'=> __('No. Dokumen'), 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('No. Dokumen'),
                    'div' => array(
                        'class' => 'form-group',
                    ),
				));

                echo $this->Form->input('tgl_surat_jalan', array(
                    'label'=> __('Tgl Penerimaan *'), 
                    'class'=>'form-control custom-date',
                    'type' => 'text',
                    'required' => false,
                    'div' => array(
                        'class' => 'form-group',
                    ),
                ));

                echo $this->Form->input('note', array(
                    'label'=> __('Keterangan'), 
                    'class'=>'form-control',
                    'type' => 'textarea',
                    'required' => false,
                    'div' => array(
                        'class' => 'form-group',
                    ),
                ));

                if( empty($disabled_edit) ) {
        			$attrBrowse = array(
                        'class' => 'ajaxModal visible-xs browse-docs',
                        'escape' => false,
                        'data-action' => 'browse-check-docs',
                        'data-change' => 'ttuj-info-table',
                        'url' => $this->Html->url( array(
                            'controller'=> 'revenues', 
                            'action' => 'document_ttujs',
                            $id,
                        )),
                        'title' => $titleBrowse,
                    );
    				$attrBrowse['class'] = 'btn bg-maroon ajaxModal';
                    echo $this->Html->tag('div', $this->Html->link('<i class="fa fa-plus-square"></i> '.$titleBrowse, 'javascript:', $attrBrowse), array(
                    	'class' => "form-group",
                	));
                }
        ?>
    </div>
</div>
<?php
		echo $this->element('blocks/revenues/tables/detail_sj');
?>
<div class="box-footer text-center action">
	<?php
    		echo $this->Html->link(__('Kembali'), array(
				'action' => 'surat_jalan', 
			), array(
				'class'=> 'btn btn-default',
			));

            if( empty($disabled_edit) ) {
        		echo $this->Form->button(__('Simpan'), array(
        			'type' => 'submit',
    				'class'=> 'btn btn-success btn-lg',
    			));
            }
	?>
</div>
<?php
		echo $this->Form->end();
?>