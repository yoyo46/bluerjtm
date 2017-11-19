<?php
        $full_name = !empty($User['Employe']['full_name'])?$User['Employe']['full_name']:false;

        if( empty($data_action) || ( !empty($data_action) && $data_action == 'excel' ) ){
            $tdStyle = '';
            $border = 0;
            $tableStyle = '';

            if( $data_action == 'excel' ) {
                header('Content-type: application/ms-excel');
                header('Content-Disposition: attachment; filename='.$sub_module_title.'.xls');
                $border = 1;
                $tdStyle = 'text-align: center;';
                $tableStyle = 'width: 100%;';
            } else {
            	$this->Html->addCrumb($sub_module_title);
                echo $this->element('blocks/revenues/search_report_invoice');
            }
?>
<section class="content invoice">
    <h2 class="page-header" style="<?php echo $tdStyle; ?>">
        <i class="fa fa-globe"></i> <?php echo $sub_module_title;?>
        <?php
                if( !empty($periode) ) {
                    echo $this->Html->tag('div', $periode, array(
                        'class' => 'pull-right',
                    ));
                }
        ?>
        <div class="clear"></div>
    </h2>
    <?php 
            if( $data_action != 'excel' ) {
                echo $this->Common->_getPrint(array(
                    '_attr' => array(
                        'class' => 'ajaxLink',
                        'data-request' => '#form-report',
                    ),
                ));
            }
    ?>
    <div class="table-responsive">
        <table class="table table-bordered sorting" border="<?php echo $border; ?>" style="<?php echo $tableStyle; ?>">
            <thead>
    			<tr>
    				<th rowspan="2" class="text-center text-middle">
    					<?php
        						echo __('Customer');
    					?>
    				</th>
    				<th rowspan="2" class="text-center text-middle"><?php echo __('Saldo Piutang');?></th>
    				<th rowspan="2" class="text-center text-middle"><?php echo __('Current');?></th>
    				<th colspan="3" class="text-center text-middle"><?php echo __('Over Due');?></th>
    			</tr>
    			<tr>
    				<th class="text-center text-middle"><?php echo __('1- 15');?></th>
    				<th class="text-center text-middle"><?php echo __('16 - 30');?></th>
    				<th class="text-center text-middle"><?php echo __('> 30');?></th>
    			</tr>
            </thead>
            <tbody>
                <?php
                        if(!empty($customers)){
                            $total_saldo = 0;
                            $total_current = 0;
                            $total_rev1to15 = 0;
                            $total_rev16to30 = 0;
                            $total_rev30 = 0;
                            foreach ($customers as $key => $value) {
                                $total_pituang = !empty($value['piutang'][0][0]['total_pituang'])?$value['piutang'][0][0]['total_pituang']:0;
                                $current_rev1to15 = !empty($value['current_rev1to15'][0][0]['current_rev1to15'])?$value['current_rev1to15'][0][0]['current_rev1to15']:0;
                                $current_rev16to30 = !empty($value['current_rev16to30'][0][0]['current_rev16to30'])?$value['current_rev16to30'][0][0]['current_rev16to30']:0;
                                $current_rev30 = !empty($value['current_rev30'][0][0]['current_rev30'])?$value['current_rev30'][0][0]['current_rev30']:0;
                                $current = $total_pituang - $current_rev1to15 - $current_rev16to30 - $current_rev30;

                                $id = $this->Common->filterEmptyField($value, 'Customer', 'id');
                                $customer = $this->Common->filterEmptyField($value, 'Customer', 'customer_name_code');

                                $total_saldo += $total_pituang;
                                $total_current += $current;
                                $total_rev1to15 += $current_rev1to15;
                                $total_rev16to30 += $current_rev16to30;
                                $total_rev30 += $current_rev30;

                                $periodDate = rawurlencode(urlencode($this->Common->filterEmptyField($this->request->data, 'Search', 'date')));
                ?>
                <tr>
                    <?php 
                            echo $this->Html->tag('td', $this->Html->link($customer, array(
                                'controller' => 'revenues',
                                'action' => 'invoice_report_detail',
                                $id,
                                'date' => $periodDate,
                                'admin' => false,
                            ), array(
                                'target' => '_blank',
                            )));
                    ?>
                    <td class="text-right"><?php echo $this->Common->getFormatPrice($total_pituang);?></td>
                    <td class="text-right"><?php echo $this->Common->getFormatPrice($current);?></td>
                    <td class="text-right"><?php echo $this->Common->getFormatPrice($current_rev1to15);?></td>
                    <td class="text-right"><?php echo $this->Common->getFormatPrice($current_rev16to30);?></td>
                    <td class="text-right"><?php echo $this->Common->getFormatPrice($current_rev30);?></td>
                </tr>
                <?php
                            }
                ?>
                <tr>
                    <?php
                        echo $this->Html->tag('td', $this->Html->tag('strong', __('Total')), array(
                            'align' => 'right'
                        ));
                        echo $this->Html->tag('td', $this->Common->getFormatPrice($total_saldo), array(
                            'align' => 'right'
                        ));
                        echo $this->Html->tag('td', $this->Common->getFormatPrice($total_current), array(
                            'align' => 'right'
                        ));
                        echo $this->Html->tag('td', $this->Common->getFormatPrice($total_rev1to15), array(
                            'align' => 'right'
                        ));
                        echo $this->Html->tag('td', $this->Common->getFormatPrice($total_rev16to30), array(
                            'align' => 'right'
                        ));
                        echo $this->Html->tag('td', $this->Common->getFormatPrice($total_rev30), array(
                            'align' => 'right'
                        ));
                    ?>
                </tr>
                <?php
                        }
                ?>
            </tbody>
        </table>
        <?php 
                echo $this->Html->tag('div', sprintf(__('Printed on : %s, by : %s'), date('d F Y'), $this->Html->tag('span', $full_name)), array(
                    'style' => 'font-size: 14px;font-style: italic;margin-top: 10px;'
                ));

                if( $data_action != 'excel' ) {
                    if(empty($customers)){
                        echo $this->Html->tag('p', __('Data belum tersedia.'), array(
                            'class' => 'alert alert-warning text-center',
                        ));
                    }
        ?>
    </div><!-- /.box-body -->
    <?php            
            echo $this->Html->tag('div', $this->element('pagination'), array(
                'class' => 'pagination-report'
            ));
    ?>
</section>
<?php
            }
        } else{
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
            $tcpdf->AddPage('L');
            // Table with rowspans and THEAD
            $table_tr_head = 'background-color: #3C8DBC; border-right: 1px solid #FFFFFF; color: #FFFFFF; font-weight: bold; padding: 0 10px; text-align: left;';
            $table_th = 'padding-top: 3px';
            $table = 'width:100%;font-size: 24px; border: 1px solid #CCC; border-collapse: collapse; padding: 0; margin: 0;';
            
            $each_loop_message = '';
            $no = 1;
            $print_label = $this->Html->tag('div', sprintf(__('Printed on : %s, by : %s'), date('d F Y'), $this->Html->tag('span', $full_name)), array(
                'style' => 'font-size: 24px;font-style: italic;margin-top: 10px;'
            ));

            if(!empty($customers)){
                $total_saldo = 0;
                $total_current = 0;
                $total_rev1to15 = 0;
                $total_rev16to30 = 0;
                $total_rev30 = 0;
                foreach ($customers as $key => $value) {
                    $total_pituang = !empty($value['piutang'][0][0]['total_pituang'])?$value['piutang'][0][0]['total_pituang']:0;
                    $current_rev1to15 = !empty($value['current_rev1to15'][0][0]['current_rev1to15'])?$value['current_rev1to15'][0][0]['current_rev1to15']:0;
                    $current_rev16to30 = !empty($value['current_rev16to30'][0][0]['current_rev16to30'])?$value['current_rev16to30'][0][0]['current_rev16to30']:0;
                    $current_rev30 = !empty($value['current_rev30'][0][0]['current_rev30'])?$value['current_rev30'][0][0]['current_rev30']:0;
                    $current = $total_pituang - $current_rev1to15 - $current_rev16to30 - $current_rev30;

                    $total_saldo += $total_pituang;
                    $total_current += $current;
                    $total_rev1to15 += $current_rev1to15;
                    $total_rev16to30 += $current_rev16to30;
                    $total_rev30 += $current_rev30;

                    $content = $this->Html->tag('td', $value['Customer']['customer_name_code'], array(
                        'style' => 'text-align: left; width: 120px;'
                    ));
                    $content .= $this->Html->tag('td', $this->Common->getFormatPrice($total_pituang), array(
                        'style' => 'text-align: right;'
                    ));
                    $content .= $this->Html->tag('td', $this->Common->getFormatPrice($current), array(
                        'style' => 'text-align: right;'
                    ));
                    $content .= $this->Html->tag('td', $this->Common->getFormatPrice($current_rev1to15), array(
                        'style' => 'text-align: right;'
                    ));
                    $content .= $this->Html->tag('td', $this->Common->getFormatPrice($current_rev16to30), array(
                        'style' => 'text-align: right;'
                    ));
                    $content .= $this->Html->tag('td', $this->Common->getFormatPrice($current_rev30), array(
                        'style' => 'text-align: right;'
                    ));

                    $each_loop_message .= $this->Html->tag('tr', $content);
                    $no++;
                }

                $content = $this->Html->tag('td',  __('Total'), array(
                    'style' => 'text-align: left; width: 120px;'
                ));
                $content .= $this->Html->tag('td', $this->Common->getFormatPrice($total_saldo), array(
                    'style' => 'text-align: right;'
                ));
                $content .= $this->Html->tag('td', $this->Common->getFormatPrice($total_current), array(
                    'style' => 'text-align: right;'
                ));
                $content .= $this->Html->tag('td', $this->Common->getFormatPrice($total_rev1to15), array(
                    'style' => 'text-align: right;'
                ));
                $content .= $this->Html->tag('td', $this->Common->getFormatPrice($total_rev16to30), array(
                    'style' => 'text-align: right;'
                ));
                $content .= $this->Html->tag('td', $this->Common->getFormatPrice($total_rev30), array(
                    'style' => 'text-align: right;'
                ));
                $each_loop_message .= $this->Html->tag('tr', $content);
            }else{
                $each_loop_message .= '<tr>
                    <td colspan="7">data tidak tersedia</td>
                </tr>';
            }   

            $date_title = __('Account Receivable Aging Report');
            $total_data = count($customers);
$tbl = <<<EOD

      <div class="clearfix container_16" id="content">

        <h2 class="grid_8" style="text-align: center;">
            $date_title
            <br>
            $periode
        </h2>
        <h4>Total Customer : $total_data</h4>
        <table cellpadding="2" cellspacing="2" nobr="true" style="$table">
            <thead>
                <tr style="$table_tr_head">
                    <th rowspan="2" style="text-align: center; width: 120px;">Customer</th>
                    <th rowspan="2" style="text-align: center;">Saldo Piutang</th>
                    <th rowspan="2" style="text-align: center;">Current</th>
                    <th colspan="3" style="text-align: center;">Over Due</th>
                </tr>
                <tr style="$table_tr_head">
                    <th style="text-align: center; color: #FFFFFF;">1- 15</th>
                    <th style="text-align: center;">16 - 30</th>
                    <th style="text-align: center;">> 30</th>
                </tr>
            </thead>
            <tbody>
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
        $filename = 'Laporan_Truk_'.$date_title.'.pdf';
        $tcpdf->Output($path.'/'.$filename, 'F'); 

        header('Content-type: application/pdf');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        readfile($path.'/'.$filename);
    }
?>