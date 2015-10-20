<?php
        $full_name = !empty($User['Employe']['full_name'])?$User['Employe']['full_name']:false;
        $dataColumns = array(
            'no' => array(
                'name' => __('No'),
                'style' => 'text-align: center;',
            ),
            'date' => array(
                'name' => __('Date'),
                'style' => 'text-align: center;',
            ),
            'nodoc' => array(
                'name' => __('No Dokumen'),
                'style' => 'text-align: center;',
            ),
            'desc' => array(
                'name' => __('Keterangan'),
                'style' => 'text-align: left; width:40%;',
            ),
            'debit' => array(
                'name' => __('Debit'),
                'style' => 'text-align: right;',
            ),
            'credit' => array(
                'name' => __('Kredit'),
                'style' => 'text-align: right;',
            ),
            'saldo' => array(
                'name' => __('Balance'),
                'style' => 'text-align: right;',
            ),
        );
        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

        $this->Html->addCrumb($module_title);
        echo $this->element('blocks/cashbanks/searchs/ledger_report');
        
        if(!empty($values)){
            if( !empty($data_action) ){
                if( $data_action == 'pdf' ) {
                    echo $this->element('blocks/common/tables/export_pdf', array(
                        'tableHead' => $fieldColumn,
                        'tableBody' => $this->element('blocks/lkus/tables/reports'),
                    ));
                } else {
                    
                }
            } else {
?>
<section class="content invoice" id="ledger-report">
    <h2 class="page-header">
        <i class="fa fa-globe"></i> <?php echo $module_title;?>
    </h2>
    <?php 
            if( !empty($coa_name) ) {
                echo $this->Html->tag('h3', $coa_name);
            }

            echo $this->Common->_getPrint();
    ?>
    <div class="table-responsive">
        <table class="table journal table-no-border red" id="journal-report">
            <?php
                    if( !empty($fieldColumn) ) {
                        echo $this->Html->tag('thead', $this->Html->tag('tr', $fieldColumn));
                    }
            ?>
            <tbody>
                <?php
                        $no = 1;
                        $beginningBalance = 0;
                        $totalDebit = 0;
                        $totalCredit = 0;

                        foreach ($values as $key => $value) {
                            $document_no = $this->Common->filterEmptyField($value, 'Journal', 'document_no');
                            $document_id = $this->Common->filterEmptyField($value, 'Journal', 'document_id');
                            $title = $this->Common->filterEmptyField($value, 'Journal', 'title');
                            $created = $this->Common->filterEmptyField($value, 'Journal', 'created');
                            $type = $this->Common->filterEmptyField($value, 'Journal', 'type');
                            $debit = $this->Common->filterEmptyField($value, 'Journal', 'debit');
                            $credit = $this->Common->filterEmptyField($value, 'Journal', 'credit');
                            $nopol = $this->Common->filterEmptyField($value, 'Journal', 'nopol');
                            $saldo_awal = $this->Common->filterEmptyField($value, 'Journal', 'saldo_awal');

                            $coa = $this->Common->filterEmptyField($value, 'Coa', 'coa_name');
                            $balance = $this->CashBank->_callCalcSaldo($value);

                            $new = sprintf('%s-%s', $type, $document_no);
                            $customCreated = $this->Common->formatDate($created, 'd/m/Y');
                            $customDebit = $this->Common->getFormatPrice($debit, false);
                            $customCredit = $this->Common->getFormatPrice($credit, false);
                            $customSaldoAwal = $this->Common->getFormatPrice($saldo_awal, false);
                            $customBalance = $this->Common->getFormatPrice($balance);

                            $totalDebit += $debit;
                            $totalCredit += $credit;

                            if( $no == 1 ) {
                                $beginningBalance = $saldo_awal;
                ?>
                <tr class="beginning">
                    <?php
                            echo $this->Html->tag('td', $this->Html->tag('i', __('Beginning Balance')), array(
                                'colspan' => 6,
                            ));
                            echo $this->Html->tag('td', $customSaldoAwal, array(
                                'style' => 'text-align:right;'
                            ));
                    ?>
                </tr>
                <?php
                            }
                ?>
                <tr>
                    <?php
                            echo $this->Html->tag('td', $no);
                            echo $this->Html->tag('td', $customCreated);
                            echo $this->Html->tag('td', $document_no);
                            echo $this->Html->tag('td', $title);
                            echo $this->Html->tag('td', $customDebit, array(
                                'style' => 'text-align:right;'
                            ));
                            echo $this->Html->tag('td', $customCredit, array(
                                'style' => 'text-align:right;'
                            ));
                            echo $this->Html->tag('td', $customBalance, array(
                                'style' => 'text-align:right;'
                            ));
                    ?>
                </tr>
                <?php

                            $no++;
                        }
                        
                        $change = $totalDebit - $totalCredit;

                        $customBeginningBalance = $this->Common->getFormatPrice($beginningBalance);
                        $customTotalDebit = $this->Common->getFormatPrice($totalDebit);
                        $customTotalCredit = $this->Common->getFormatPrice($totalCredit);
                        $customChange = $this->Common->getFormatPrice($change);
                ?>
                <tr class="total">
                    <?php
                            echo $this->Html->tag('td', __('Beginning Balance:'), array(
                                'colspan' => 2,
                                'style' => 'text-align:left;font-weight:bold;'
                            ));
                            echo $this->Html->tag('td', $customBeginningBalance, array(
                                'style' => 'text-align:right;'
                            ));
                            echo $this->Html->tag('td', __('Total:'), array(
                                'style' => 'text-align:right;font-weight:bold;'
                            ));
                            echo $this->Html->tag('td', $customTotalDebit, array(
                                'style' => 'text-align:right;'
                            ));
                            echo $this->Html->tag('td', $customTotalCredit, array(
                                'style' => 'text-align:right;'
                            ));
                            echo $this->Html->tag('td', '');
                    ?>
                </tr>
                <tr class="ending">
                    <?php
                            echo $this->Html->tag('td', $this->Html->tag('strong', __('Ending Balance:')), array(
                                'colspan' => 2,
                            ));
                            echo $this->Html->tag('td', $customBalance, array(
                                'style' => 'text-align:right;'
                            ));
                            echo $this->Html->tag('td', __('Change:'), array(
                                'style' => 'text-align:right;font-weight:bold;'
                            ));
                            echo $this->Html->tag('td', $customChange, array(
                                'style' => 'text-align:right;'
                            ));
                            echo $this->Html->tag('td', '', array(
                                'colspan' => 2,
                            ));
                    ?>
                </tr>
            </tbody>
        </table>
    </div><!-- /.box-body -->
</section>
<?php
            }
        }
?>