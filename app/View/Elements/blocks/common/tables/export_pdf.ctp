<?php 
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
    
        $topHeader = !empty($topHeader)?$topHeader:false;
        $contentHeader = !empty($contentHeader)?$contentHeader:false;
        $tableHead = !empty($tableHead)?$tableHead:false;
        $tableBody = !empty($tableBody)?$tableBody:false;
        $full_name = !empty($User['Employe']['full_name'])?$User['Employe']['full_name']:false;
        $date_title = $sub_module_title;
        $filename = !empty($filename)?$filename:$sub_module_title;
        $print_label = $this->Html->tag('div', sprintf(__('Printed on : %s, by : %s'), date('d F Y'), $this->Html->tag('span', $full_name)), array(
            'style' => 'font-size: 24px;font-style: italic;margin-top: 10px;'
        ));

        if( empty($customHeader) ) {
            $customHeader = <<<EOD
            <h2 class="grid_8" style="text-align: center;">$date_title</h2>
EOD;
        }

        if( empty($tableContent) ) {
            $tableContent = <<<EOD
        <table cellpadding="2" cellspacing="2" nobr="true" style="$table">
            <thead>
                <tr style="$table_tr_head">
                    $tableHead
                </tr>
            </thead>
            <tbody>          
                $tableBody
            </tbody>
        </table> 
      </div>
EOD;
        }
$tbl = <<<EOD

      <div class="clearfix container_16" id="content">
        $customHeader
        $tableContent
        <br>
        $print_label
      </div>
EOD;
// echo $tbl;
// die();

        // output the HTML content
        $tcpdf->writeHTML($tbl, true, false, false, false, '');

        $tcpdf->SetTextColor(0, 0, 0);
        $tcpdf->SetFont($textfont,'B',10);

        $path = $this->Common->pathDirTcpdf();
        $filename = $this->Common->toSlug($filename).'.pdf';
        $pathFile = $path.'/'.$filename;
        
        $tcpdf->Output($pathFile, 'F'); 

        header('Content-type: application/pdf');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        readfile($pathFile);
?>