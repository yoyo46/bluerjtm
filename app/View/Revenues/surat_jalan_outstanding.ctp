<div id="wrapper-sj">
	<div class="box box-primary">
	    <div class="box-header">
	        <h3 class="box-title"><?php printf(__('Informasi Surat Jalan - %s'), !empty($driver['Driver']['name'])?$driver['Driver']['name']:false);?></h3>
	        <div class="pull-right box-tools">
                <a class="btn btn-danger btn-sm" id="sj-remove"><i class="fa fa-times"></i></a>
            </div>
	    </div>
	    <div class="box-body">
			<div class="box-body table-responsive">
			    <table class="table table-hover">
			        <tr>
			            <?php 
			                    echo $this->Html->tag('th', __('Tgl TTUJ'), array(
			                    	'class' => 'text-center',
			                	));
			                    echo $this->Html->tag('th', __('Dari'));
			                    echo $this->Html->tag('th', __('Tujuan'));
			                    echo $this->Html->tag('th', __('Sisa Unit/Muatan'), array(
			                    	'class' => 'text-center',
			                	));
			            ?>
			        </tr>
			        <?php
			                if(!empty($ttujs)){
			                    foreach ($ttujs as $key => $value) {
			                        $id = $value['Ttuj']['id'];
			        ?>
			        <tr>
			            <?php 
			                    echo $this->Html->tag('td', date('d M Y', strtotime($value['Ttuj']['ttuj_date'])), array(
			                        'class' => 'text-center',
			                    ));
			            ?>
			            <td><?php echo $value['Ttuj']['from_city_name'];?></td>
			            <td><?php echo $value['Ttuj']['to_city_name'];?></td>
			            <td class="text-center">
			            	<?php
			            			$sjKembali = !empty($value['SjKembali'])?$value['SjKembali']:0;
			            			$totalMuatan = !empty($value['TotalMuatan'])?$value['TotalMuatan']:0;
			            			$sisa = $totalMuatan - $sjKembali;

			            			if( $sisa <= 0 ) {
			            				echo 0;
			            			} else {
			            				echo $sisa;
			            			}
		        			?>
		        		</td>
			        </tr>
			        <?php
			                    }
			                }
			        ?>
			    </table>
			</div>
	    </div>
	</div>
</div>