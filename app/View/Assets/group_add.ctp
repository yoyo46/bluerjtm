<?php
		$title = __('Group Asset');
		$urlRoot = array(
			'action' => 'groups',
		);
		$value = !empty($value)?$value:false;

		$this->Html->addCrumb($title, $urlRoot);
		$this->Html->addCrumb($sub_module_title);

		echo $this->Form->create('AssetGroup');
?>
<div class="row">
	<div class="col-sm-6">
		<div class="box">
		    <?php 
		            echo $this->element('blocks/common/box_header', array(
		                'title' => $title,
		            ));
		    ?>
		    <div class="box-body">
		    	<?php 
						echo $this->Common->buildInputForm('code', __('Kode *'));
						echo $this->Common->buildInputForm('name', __('Nama Group *'));
						echo $this->Common->buildInputForm('umur_ekonomis', __('Umur Ekonomis *'), array(
							'type' => 'text',
		                    'textGroup' => 'Thn',
		                    'positionGroup' => 'right',
		                    'class' => 'form-control pull-right',
		                ));
						echo $this->Common->buildInputForm('nilai_sisa', __('Nilai Sisa *'));
			    ?>
		    </div>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="box">
		    <?php 
		            echo $this->element('blocks/common/box_header', array(
		                'title' => __('Account'),
		            ));
		    ?>
		    <div class="box-body">
		    	<?php 
						echo $this->Common->buildInputForm('AssetGroupCoa.Asset.coa_id', __('Asset *'), array(
							'empty' => __('- Pilih COA -'),
						));
						echo $this->Common->buildInputForm('AssetGroupCoa.AccumulationDepr.coa_id', __('Accumulation Depr. *'), array(
							'empty' => __('- Pilih COA -'),
						));
						echo $this->Common->buildInputForm('AssetGroupCoa.Depresiasi.coa_id', __('Depresiasi *'), array(
							'empty' => __('- Pilih COA -'),
						));
						echo $this->Common->buildInputForm('AssetGroupCoa.ProfitAsset.coa_id', __('Keuntungan penjualan asset *'), array(
							'empty' => __('- Pilih COA -'),
						));
			    ?>
		    </div>
		</div>
	</div>
</div>
<?php 
        echo $this->element('blocks/common/forms/submit_action', array(
            'urlBack' => $urlRoot,
        ));
		echo $this->Form->end();
?>