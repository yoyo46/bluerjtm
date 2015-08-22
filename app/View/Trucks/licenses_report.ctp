<?php
        $full_name = !empty($User['Employe']['full_name'])?$User['Employe']['full_name']:false;
	if( empty($data_action) || ( !empty($data_action) && $data_action == 'excel' ) ){
        $tdStyle = '';
        $border = 0;
            
		if( $data_action == 'excel' ) {
	        header('Content-type: application/ms-excel');
	        header('Content-Disposition: attachment; filename='.$sub_module_title.'.xls');
	        $border = 1;
	        $tdStyle = 'text-align: center;';
	    } else {
	    	$this->Html->addCrumb($sub_module_title);
	        echo $this->element('blocks/trucks/search_license_report');
	    }
?>
<section class="content invoice">
    <h2 class="page-header">
        <i class="fa fa-globe"></i> <?php echo $sub_module_title;?>
    </h2>
    <div class="row no-print print-action">
        <div class="col-xs-6 action pull-right">
            <?php
                if( $data_action != 'excel' ) {
                    echo $this->Html->link('<i class="fa fa-download"></i> Download Excel', $this->here.'/excel', array(
                        'escape' => false,
                        'class' => 'btn btn-success pull-right'
                    ));
                    echo $this->Html->link('<i class="fa fa-download"></i> Download PDF', $this->here.'/pdf', array(
                        'escape' => false,
                        'class' => 'btn btn-primary pull-right'
                    ));
                }
            ?>
        </div>
        <div class="col-xs-6">
            <?php
                $statusContent = $this->Html->tag('span', __('<i class="fa fa-check"></i> Aktif'), array(
                    'class' => 'label label-success',
                ));
                $statusContent .= $this->Html->tag('span', __('<i class="fa fa-times"></i> Expired'), array(
                    'class' => 'label label-danger',
                ));
                $statusContent .= $this->Html->tag('span', __('<i class="fa fa-exclamation-triangle"></i> Akan Expired'), array(
                    'class' => 'label label-warning',
                ));

                echo $this->Html->tag('div', $statusContent, array(
                    'class' => 'status-content'
                ));
            ?>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered" border="<?php echo $border; ?>">
            <thead>
                <tr>
                    <?php
                            echo $this->Html->tag('th', $this->Common->getSorting('Truck.nopol', __('Nopol')));
                            echo $this->Html->tag('th', $this->Common->getSorting('CustomerNoType.name', __('Alokasi')));
                            echo $this->Html->tag('th', $this->Common->getSorting('Truck.tgl_stnk', __('STNK 1TH')));
                            echo $this->Html->tag('th', $this->Common->getSorting('Truck.tgl_stnk_plat', __('STNK 5TH')));
                            echo $this->Html->tag('th', $this->Common->getSorting('Truck.tgl_kir', __('KIR')));
                            echo $this->Html->tag('th', $this->Common->getSorting('Truck.tgl_siup', __('IJIN USAHA')));
                            echo $this->Html->tag('th', __('Cabang'));
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                    if(!empty($trucks)){
                        foreach ($trucks as $key => $truck) {
                        	$now_date = date('Y-m-d');
                        	$raw_now_date = strtotime($now_date);
                            $branch = $this->Common->filterEmptyField($truck, 'Branch', 'name');

                            $content = $this->Html->tag('td', $truck['Truck']['nopol']);
                            $customer = '-';
                            if(!empty($truck['TruckCustomer']['CustomerNoType']['code'])){
                            	$customer = $truck['TruckCustomer']['CustomerNoType']['code'];
                            }
                            $content .= $this->Html->tag('td', $customer);

                            $label_stnk_1th = ' - ';
                            if(!empty($truck['Truck']['tgl_stnk'])){
                            	$tgl_stnk_1th = strtotime($truck['Truck']['tgl_stnk']);
	                            $tgl_stnk_1th_expired = strtotime('-1 month', $tgl_stnk_1th);

	                            if($raw_now_date >= $tgl_stnk_1th){
	                            	$label_stnk_1th = $this->Html->tag('span', '<i class="fa fa-times"></i> '.$this->Common->customDate($truck['Truck']['tgl_stnk'], 'd/m/Y'), array(
	                            		'class' => 'label label-danger',
                                        'title' => 'Expired'
	                            	));
	                            }else if($raw_now_date <= $tgl_stnk_1th_expired){
	                            	$label_stnk_1th = $this->Html->tag('span', '<i class="fa fa-check"></i> '.$this->Common->customDate($truck['Truck']['tgl_stnk'], 'd/m/Y'), array(
		                            	'class' => 'label label-success',
                                        'title' => 'Aktif'
		                            ));
	                            }else{
	                            	$label_stnk_1th = $this->Html->tag('span', '<i class="fa fa-exclamation-triangle"></i> '.$this->Common->customDate($truck['Truck']['tgl_stnk'], 'd/m/Y'), array(
		                            	'class' => 'label label-warning',
                                        'title' => 'Akan Expired'
		                            ));
	                            }
                            }

                            $content .= $this->Html->tag('td', $label_stnk_1th);

                            $label_stnk_5th = ' - ';
                            if(!empty($truck['Truck']['tgl_stnk_plat'])){
                                $tgl_stnk_5th = strtotime($truck['Truck']['tgl_stnk_plat']);
                                $tgl_stnk_5th_expired = strtotime('-1 month', $tgl_stnk_5th);

                                if($raw_now_date >= $tgl_stnk_5th){
                                    $label_stnk_5th = $this->Html->tag('span', '<i class="fa fa-times"></i> '.$this->Common->customDate($truck['Truck']['tgl_stnk_plat'], 'd/m/Y'), array(
                                        'class' => 'label label-danger',
                                        'title' => 'Expired'
                                    ));
                                }else if($raw_now_date <= $tgl_stnk_5th_expired){
                                    $label_stnk_5th = $this->Html->tag('span', '<i class="fa fa-check"></i> '.$this->Common->customDate($truck['Truck']['tgl_stnk_plat'], 'd/m/Y'), array(
                                        'class' => 'label label-success',
                                        'title' => 'Aktif'
                                    ));
                                }else{
                                    $label_stnk_5th = $this->Html->tag('span', '<i class="fa fa-exclamation-triangle"</i> '.$this->Common->customDate($truck['Truck']['tgl_stnk_plat'], 'd/m/Y'), array(
                                        'class' => 'label label-warning',
                                        'title' => 'Akan Expired'
                                    ));
                                }
                            }
                            $content .= $this->Html->tag('td', $label_stnk_5th);

                            $label_kir = ' - ';
                            if(!empty($truck['Truck']['tgl_kir'])){
                                $tgl_kir = strtotime($truck['Truck']['tgl_kir']);
                                $tgl_kir_expired = strtotime('-1 month', $tgl_kir);

                                if($raw_now_date >= $tgl_kir){
                                    $label_kir = $this->Html->tag('span', '<i class="fa fa-times"></i> '.$this->Common->customDate($truck['Truck']['tgl_kir'], 'd/m/Y'), array(
                                        'class' => 'label label-danger',
                                        'title' => 'Expired'
                                    ));
                                }else if($raw_now_date <= $tgl_kir_expired){
                                    $label_kir = $this->Html->tag('span', '<i class="fa fa-check"></i> '.$this->Common->customDate($truck['Truck']['tgl_kir'], 'd/m/Y'), array(
                                        'class' => 'label label-success',
                                        'title' => 'Aktif'
                                    ));
                                }else{
                                    $label_kir = $this->Html->tag('span', '<i class="fa fa-exclamation-triangle"></i> '.$this->Common->customDate($truck['Truck']['tgl_kir'], 'd/m/Y'), array(
                                        'class' => 'label label-warning',
                                        'title' => 'Akan Expired'
                                    ));
                                }
                            }
                            $content .= $this->Html->tag('td', $label_kir);

                            $label_siup = ' - ';
                            if(!empty($truck['Truck']['tgl_siup'])){
                                $tgl_siup = strtotime($truck['Truck']['tgl_siup']);
                                $tgl_siup_expired = strtotime('-1 month', $tgl_siup);

                                if($raw_now_date >= $tgl_siup){
                                    $label_siup = $this->Html->tag('span', '<i class="fa fa-times"></i> '.$this->Common->customDate($truck['Truck']['tgl_siup'], 'd/m/Y'), array(
                                        'class' => 'label label-danger',
                                        'title' => 'Expired'
                                    ));
                                }else if($raw_now_date <= $tgl_siup_expired){
                                    $label_siup = $this->Html->tag('span', '<i class="fa fa-check"></i> '.$this->Common->customDate($truck['Truck']['tgl_siup'], 'd/m/Y'), array(
                                        'class' => 'label label-success',
                                        'title' => 'Aktif'
                                    ));
                                }else{
                                    $label_siup = $this->Html->tag('span', '<i class="fa fa-exclamation-triangle"></i> '.$this->Common->customDate($truck['Truck']['tgl_siup'], 'd/m/Y'), array(
                                        'class' => 'label label-warning',
                                        'title' => 'Akan Expired'
                                    ));
                                }
                            }
                            $content .= $this->Html->tag('td', $label_siup);
                            $content .= $this->Html->tag('td', $branch);

                            echo $this->Html->tag('tr', $content);
                        }
                    }else{
                        echo $this->Html->tag('tr', $this->Html->tag('td', __('Data Tidak Ditemukan.'), array(
                            'class' => 'alert-danger text-center',
                            'colspan' => 7
                        )));
                    }
                ?>
            </tbody>
        </table>
        <?php 
                if( $data_action != 'excel' ) {
                    if(empty($trucks)){
                        echo $this->Html->tag('p', __('Data belum tersedia.'), array(
                            'class' => 'alert alert-warning text-center',
                        ));
                    }
        ?>
    </div>
    <?php 
            }

            echo $this->Html->tag('div', sprintf(__('Printed on : %s, by : %s'), date('d F Y'), $this->Html->tag('span', $full_name)), array(
                'style' => 'font-size: 14px;font-style: italic;margin-top: 10px;'
            ));
            
            if( $data_action != 'excel' ) {
                echo $this->Html->tag('div', $this->element('pagination'), array(
                    'class' => 'pagination-report'
                ));
    ?>
</section>
<?php
            }
	   }else{
        App::import('Vendor','xtcpdf');
        ob_end_clean();
        $tcpdf = new XTCPDF();

        $tcpdf->setPrintHeader(false);
        $tcpdf->setPrintFooter(false);

        $textfont = 'freesans';

        $tcpdf->SetAuthor("AcuaSotf");
        $tcpdf->SetAutoPageBreak( true );
        $tcpdf->setHeaderFont(array($textfont,'',10));
        $tcpdf->xheadercolor = array(255,255,255);
        $tcpdf->AddPage();
        // Table with rowspans and THEAD
        $table_tr_head = 'background-color: #3C8DBC; border-right: 1px solid #FFFFFF; color: #FFFFFF; font-weight: bold; padding: 0 10px; text-align: left;';
        $table_th = 'padding-top: 3px';
        $table = 'width:100%;font-size: 24px; border: 1px solid #CCC; border-collapse: collapse; padding: 0; margin: 0;';
        
        $each_loop_message = '';
        $print_label = $this->Html->tag('div', sprintf(__('Printed on : %s, by : %s'), date('d F Y'), $this->Html->tag('span', $full_name)), array(
            'style' => 'font-size: 24px;font-style: italic;margin-top: 10px;'
        ));

        if(!empty($trucks)){
            foreach ($trucks as $truck):
                $now_date = date('Y-m-d');
                $raw_now_date = strtotime($now_date);
                $branch = $this->Common->filterEmptyField($truck, 'Branch', 'name');

                $content = $this->Html->tag('td', $branch);
                $content .= $this->Html->tag('td', $truck['Truck']['nopol']);

                $customer = '-';
                if(!empty($truck['TruckCustomer']['CustomerNoType']['name'])){
                    $customer = $truck['TruckCustomer']['CustomerNoType']['name'];
                }
                $content .= $this->Html->tag('td', $customer);

                $label_stnk_1th = ' - ';
                if(!empty($truck['Truck']['tgl_stnk'])){
                    $tgl_stnk_1th = strtotime($truck['Truck']['tgl_stnk']);
                    $tgl_stnk_1th_expired = strtotime('-1 month', $tgl_stnk_1th);

                    if($raw_now_date >= $tgl_stnk_1th){
                        $label_stnk_1th = $this->Html->tag('span', '<i class="fa fa-times" title="expired"></i> '.$this->Common->customDate($truck['Truck']['tgl_stnk'], 'd/m/Y'), array(
                            'class' => 'label label-danger'
                        ));
                    }else if($raw_now_date <= $tgl_stnk_1th_expired){
                        $label_stnk_1th = $this->Html->tag('span', '<i class="fa fa-check" title="Aktif"></i> '.$this->Common->customDate($truck['Truck']['tgl_stnk'], 'd/m/Y'), array(
                            'class' => 'label label-success'
                        ));
                    }else{
                        $label_stnk_1th = $this->Html->tag('span', '<i class="fa fa-exclamation-triangle" title="Akan Expired"></i> '.$this->Common->customDate($truck['Truck']['tgl_stnk'], 'd/m/Y'), array(
                            'class' => 'label label-warning'
                        ));
                    }
                }

                $content .= $this->Html->tag('td', $label_stnk_1th);

                $label_stnk_5th = ' - ';
                if(!empty($truck['Truck']['tgl_stnk_plat'])){
                    $tgl_stnk_5th = strtotime($truck['Truck']['tgl_stnk_plat']);
                    $tgl_stnk_5th_expired = strtotime('-1 month', $tgl_stnk_5th);

                    if($raw_now_date >= $tgl_stnk_5th){
                        $label_stnk_5th = $this->Html->tag('span', '<i class="fa fa-times" title="expired"></i> '.$this->Common->customDate($truck['Truck']['tgl_stnk_plat'], 'd/m/Y'), array(
                            'class' => 'label label-danger'
                        ));
                    }else if($raw_now_date <= $tgl_stnk_5th_expired){
                        $label_stnk_5th = $this->Html->tag('span', '<i class="fa fa-check" title="Aktif"></i> '.$this->Common->customDate($truck['Truck']['tgl_stnk_plat'], 'd/m/Y'), array(
                            'class' => 'label label-success'
                        ));
                    }else{
                        $label_stnk_5th = $this->Html->tag('span', '<i class="fa fa-exclamation-triangle" title="Akan Expired"></i> '.$this->Common->customDate($truck['Truck']['tgl_stnk_plat'], 'd/m/Y'), array(
                            'class' => 'label label-warning'
                        ));
                    }
                }
                $content .= $this->Html->tag('td', $label_stnk_5th);

                $label_kir = ' - ';
                if(!empty($truck['Truck']['tgl_kir'])){
                    $tgl_kir = strtotime($truck['Truck']['tgl_kir']);
                    $tgl_kir_expired = strtotime('-1 month', $tgl_kir);

                    if($raw_now_date >= $tgl_kir){
                        $label_kir = $this->Html->tag('span', '<i class="fa fa-times" title="expired"></i> '.$this->Common->customDate($truck['Truck']['tgl_kir'], 'd/m/Y'), array(
                            'class' => 'label label-danger'
                        ));
                    }else if($raw_now_date <= $tgl_kir_expired){
                        $label_kir = $this->Html->tag('span', '<i class="fa fa-check" title="Aktif"></i> '.$this->Common->customDate($truck['Truck']['tgl_kir'], 'd/m/Y'), array(
                            'class' => 'label label-success'
                        ));
                    }else{
                        $label_kir = $this->Html->tag('span', '<i class="fa fa-exclamation-triangle" title="Akan Expired"></i> '.$this->Common->customDate($truck['Truck']['tgl_kir'], 'd/m/Y'), array(
                            'class' => 'label label-warning'
                        ));
                    }
                }
                $content .= $this->Html->tag('td', $label_kir);

                $label_siup = ' - ';
                if(!empty($truck['Truck']['tgl_siup'])){
                    $tgl_siup = strtotime($truck['Truck']['tgl_siup']);
                    $tgl_siup_expired = strtotime('-1 month', $tgl_siup);

                    if($raw_now_date >= $tgl_siup){
                        $label_siup = $this->Html->tag('span', '<i class="fa fa-times" title="expired"></i> '.$this->Common->customDate($truck['Truck']['tgl_siup'], 'd/m/Y'), array(
                            'class' => 'label label-danger'
                        ));
                    }else if($raw_now_date <= $tgl_siup_expired){
                        $label_siup = $this->Html->tag('span', '<i class="fa fa-check" title="Aktif"></i> '.$this->Common->customDate($truck['Truck']['tgl_siup'], 'd/m/Y'), array(
                            'class' => 'label label-success'
                        ));
                    }else{
                        $label_siup = $this->Html->tag('span', '<i class="fa fa-exclamation-triangle" title="Akan Expired"></i> '.$this->Common->customDate($truck['Truck']['tgl_siup'], 'd/m/Y'), array(
                            'class' => 'label label-warning'
                        ));
                    }
                }
                $content .= $this->Html->tag('td', $label_siup);

                $each_loop_message .= $this->Html->tag('tr', $content);
            endforeach;
        }else{
            $each_loop_message .= '<tr>
                <td colspan="7">data tidak tersedia</td>
            </tr>';
        }   

$tbl = <<<EOD

      <div class="clearfix container_16" id="content">

        <h2 class="grid_8" style="text-align: center;">$sub_module_title</h2>
        <table cellpadding="2" cellspacing="2" nobr="true" style="$table">
            <tbody>
            <tr style="$table_tr_head">
                <th>Nopol</th>
                <th>Alokasi</th>
                <th>STNK 1TH</th>
                <th>STNK 5TH</th>
                <th>KIR</th>
                <th>IJIN USAHA</th>
            </tr>
          
            $each_loop_message
                                                                        
            </tbody>
        </table> 
        <br>
        $print_label
      </div>
EOD;

        // output the HTML content
        $tcpdf->writeHTML($tbl, true, false, false, false, '');

        $tcpdf->SetTextColor(0, 0, 0);
        $tcpdf->SetFont($textfont,'B',10);

        $path = $this->Common->pathDirTcpdf();
        $filename = 'Laporan_Surat_Truk.pdf';
        $tcpdf->Output($path.'/'.$filename, 'F'); 

        header('Content-type: application/pdf');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        readfile($path.'/'.$filename);
	}
?>