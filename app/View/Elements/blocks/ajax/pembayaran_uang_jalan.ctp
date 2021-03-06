<?php 
        $id = $this->Common->filterEmptyField($ttuj, 'Ttuj', 'id');
        $no_ttuj = $this->Common->filterEmptyField($ttuj, 'Ttuj', 'no_ttuj');
        $ttuj_date = $this->Common->filterEmptyField($ttuj, 'Ttuj', 'ttuj_date');
        $nopol = $this->Common->filterEmptyField($ttuj, 'Ttuj', 'nopol');
        $capacity = $this->Common->filterEmptyField($ttuj, 'Ttuj', 'truck_capacity');
        $from_city_name = $this->Common->filterEmptyField($ttuj, 'Ttuj', 'from_city_name');
        $to_city_name = $this->Common->filterEmptyField($ttuj, 'Ttuj', 'to_city_name');
        $note = $this->Common->filterEmptyField($ttuj, 'Ttuj', 'note');
        $tgl_bon_biru = Common::hashEmptyField($ttuj, 'Ttuj.tgl_bon_biru');
        $tgl_bon_biru = Common::formatDate($tgl_bon_biru, 'd/m/Y', '-');

        $data_type = !empty($data_type)?$data_type:false;
        $data_type = $this->Common->filterEmptyField($ttuj, 'Ttuj', 'data_type', $data_type);

        $customer_name_code = $this->Common->filterEmptyField($ttuj, 'Customer', 'code');
        $driver = $this->Common->_callGetDriverCode($ttuj);

        $checkbox = isset($checkbox)?$checkbox:true;
		$alias = sprintf('child-%s-%s', $id, $data_type);
        $addClass = !empty($isAjax)?'hide':'';

        $ttujPayment = $this->Common->filterEmptyField($this->request->data, 'TtujPayment');
        $sisaAmount = $this->Common->getBiayaTtuj( $ttuj, $data_type );
        $amountPayment = !empty($ttujPayment['amount_payment'][$idx])?$ttujPayment['amount_payment'][$idx]:$sisaAmount;

        $no_claim = !empty($ttujPayment['no_claim'][$idx])?$ttujPayment['no_claim'][$idx]:0;
        $stood = !empty($ttujPayment['stood'][$idx])?$ttujPayment['stood'][$idx]:0;
        $lainnya = !empty($ttujPayment['lainnya'][$idx])?$ttujPayment['lainnya'][$idx]:0;
        $titipan = !empty($ttujPayment['titipan'][$idx])?$ttujPayment['titipan'][$idx]:0;
        $claim = !empty($ttujPayment['claim'][$idx])?$ttujPayment['claim'][$idx]:0;
        $unit_claim = !empty($ttujPayment['unit_claim'][$idx])?$ttujPayment['unit_claim'][$idx]:0;
        $debt = !empty($ttujPayment['debt'][$idx])?$ttujPayment['debt'][$idx]:0;
        $potongan_debt = !empty($ttujPayment['potongan_debt'][$idx])?$ttujPayment['potongan_debt'][$idx]:0;
        $debt_note = !empty($ttujPayment['debt_note'][$idx])?$ttujPayment['debt_note'][$idx]:NULL;

        $total = $amountPayment + $no_claim + $stood + $lainnya - $titipan - $claim - $debt;
        $debt_percent = !empty($debt_percent)?$debt_percent:0;

        if( !empty($amountPayment) ) {
            if( !empty($checkbox) ) {
                printf('<tr data-value="%s" data-type="%s" class="child %s">', $alias, $data_type, $alias);

    			$checkboxContent = $this->Form->checkbox('ttuj_checked.', array(
                    'class' => 'check-option',
                    'value' => $id,
                ));
                $checkboxContent .= $this->Form->input('TtujPayment.ttuj_id.', array(
                    'type' => 'hidden',
                    'value' => $id,
                ));

                echo $this->Html->tag('td', $checkboxContent, array(
                    'class' => 'checkbox-action',
                ));
            } else {
                printf('<tr class="child child-%s">', $alias);
            }
?>
    <td>
        <div style="width: 350px;">
            <div class="row">
                <div class="col-sm-6 ttuj-info-item">
                    <?php
                            if( !empty($capacity) ) {
                                $ttuj_capacity = $this->Html->tag('p', __('%s: %s', $this->Html->tag('strong', __('Kap')), $capacity));
                            } else {
                                $ttuj_capacity = '';
                            }

                            echo $this->Html->tag('p', __('%s: %s %s', $this->Html->tag('strong', __('No')), $no_ttuj, $this->Html->link($this->Common->icon('question-circle'), 'javascript:void(0);', array(
                                'escape' => false,
                                'class' => 'popover-hover-top-click',
                                'data-content' => 
                                // $this->Html->tag('p', __('%s: %s', $this->Html->tag('strong', __('Tgl')), date('d M Y', strtotime($ttuj_date)))).
                                $this->Html->tag('p', __('%s: %s', $this->Html->tag('strong', __('Tgl Masuk Bon Biru')), $tgl_bon_biru)).
                                $this->Html->tag('p', __('%s: %s', $this->Html->tag('strong', __('Customer')), $customer_name_code)).
                                $this->Html->tag('p', __('%s: %s', $this->Html->tag('strong', __('Dari')), $from_city_name)).
                                $this->Html->tag('p', __('%s: %s', $this->Html->tag('strong', __('Tujuan')), $to_city_name)).
                                $ttuj_capacity.
                                $this->Html->tag('p', __('%s: %s', $this->Html->tag('strong', __('Jenis')), $this->Common->_callLabelBiayaTtuj($data_type))).
                                $this->Html->tag('p', __('%s: %s', $this->Html->tag('strong', __('Total')), $this->Common->getBiayaTtuj( $ttuj, $data_type, true, false ))),
                                'data-original-title' => $this->Html->tag('strong', __('Info TTUJ')).' <span class=\'pull-right\'><a href=\'javascript:\'><i class=\'popover-close\'>Tutup</i></a></span>',
                                'allow' => true,
                            ))));
                            echo $this->Html->tag('p', __('%s: %s', $this->Html->tag('strong', __('Tgl')), date('d M Y', strtotime($ttuj_date))));
                            
                            // echo $this->Html->tag('p', __('%s: %s', $this->Html->tag('strong', __('Customer')), $customer_name_code));
                            echo $this->Html->tag('p', __('%s: %s - %s', $this->Html->tag('strong', __('Tujuan')), $from_city_name, $to_city_name));
                    ?>
                </div>
                <div class="col-sm-6">
                    
                    <?php
                            echo $this->Html->tag('p', __('%s: %s', $this->Html->tag('strong', __('Supir')), $driver));
                            echo $this->Html->tag('p', __('%s: %s', $this->Html->tag('strong', __('NoPol')), $nopol));
                            echo $this->Html->tag('p', __('%s: %s', $this->Html->tag('strong', __('Ket.')), $note));

                            // if( !empty($capacity) ) {
                            //     echo $this->Html->tag('p', __('%s: %s', $this->Html->tag('strong', __('Kap')), $capacity));
                            // }

                            // echo $this->Html->tag('p', __('%s: %s', $this->Html->tag('strong', __('Jenis')), $this->Common->_callLabelBiayaTtuj($data_type)));
                            // echo $this->Html->tag('p', __('%s: %s', $this->Html->tag('strong', __('Total')), $this->Common->getBiayaTtuj( $ttuj, $data_type, true, false )));
                    ?>
                </div>
            </div>
        </div>
    </td>
    <?php 
    ?>
    <td class="text-right">
    	<?php
                if( !empty($document_info) ) {
                    $amountPayment = $this->Common->getFormatPrice($amountPayment);
                    echo $amountPayment;
                } else {
        			echo $this->Form->input('TtujPayment.amount_payment.',array(
                        'label'=> false,
                        'class'=>'form-control input_price_min sisa-amount text-right',
                        'required' => false,
                        'div' => false,
                        'value' => $amountPayment,
                    ));
                    echo $this->Form->hidden('TtujPayment.ttuj_id.',array(
                        'value'=> $id,
                    ));
                    echo $this->Form->hidden('TtujPayment.data_type.',array(
                        'value'=> $data_type,
                    ));
                    echo $this->Form->hidden('TtujPayment.potongan_debt.', array(
                        'value' => $potongan_debt,
                    ));
                }
		?>
	</td>
    <td class="text-right">
        <?php
                if( !empty($document_info) ) {
                    $no_claim = $this->Common->getFormatPrice($no_claim);
                    echo $no_claim;
                } else {
                    echo $this->Form->input('TtujPayment.no_claim.',array(
                        'label'=> false,
                        'class'=>'form-control input_price_min no-claim text-right',
                        'required' => false,
                        'div' => false,
                        'value' => $no_claim,
                    ));
                }
        ?>
    </td>
    <td class="text-right">
        <?php
                if( !empty($document_info) ) {
                    $stood = $this->Common->getFormatPrice($stood);
                    echo $stood;
                } else {
                    echo $this->Form->input('TtujPayment.stood.',array(
                        'label'=> false,
                        'class'=>'form-control input_price_min stood text-right',
                        'required' => false,
                        'div' => false,
                        'value' => $stood,
                    ));
                }
        ?>
    </td>
    <td class="text-right">
        <?php
                if( !empty($document_info) ) {
                    $lainnya = $this->Common->getFormatPrice($lainnya);
                    echo $lainnya;
                } else {
                    echo $this->Form->input('TtujPayment.lainnya.',array(
                        'label'=> false,
                        'class'=>'form-control input_price_min lainnya text-right',
                        'required' => false,
                        'div' => false,
                        'value' => $lainnya,
                    ));
                }
        ?>
    </td>
    <td class="text-right">
        <?php
                if( !empty($document_info) ) {
                    $titipan = $this->Common->getFormatPrice($titipan);
                    echo $titipan;
                } else {
                    echo $this->Form->input('TtujPayment.titipan.',array(
                        'label'=> false,
                        'class'=>'form-control input_price_min titipan text-right',
                        'required' => false,
                        'div' => false,
                        'value' => $titipan,
                    ));
                }
        ?>
    </td>
    <td class="text-right">
        <?php
                if( !empty($document_info) ) {
                    $claim = $this->Common->getFormatPrice($claim);
                    echo $claim;
                } else {
                    echo $this->Form->input('TtujPayment.claim.',array(
                        'label'=> false,
                        'class'=>'form-control input_price_min claim text-right',
                        'required' => false,
                        'div' => false,
                        'value' => $claim,
                    ));
                }
        ?>
    </td>
     <td class="text-right">
        <?php
                if( !empty($document_info) ) {
                    $debt = $this->Common->getFormatPrice($debt);
                    echo $debt;
                } else {
                    echo $this->Common->_callInputForm('TtujPayment.debt.',array(
                        'class'=>'form-control input_price_min debt text-right',
                        'value' => $debt,
                        'div' => false,
                        'frameClass' => false,
                        'fieldError' => array(
                            'TtujPaymentDetail.'.$idx.'.debt',
                            'TtujPaymentDetail.'.$idx.'.debt_paid',
                        ),
                    ));
                    echo $this->Form->hidden('TtujPayment.debt_percent.', array(
                        'class'=>'debt_percent',
                        'value' => $debt_percent,
                    ));
                }
        ?>
    </td>
    <td class="text-right">
        <?php
                if( !empty($document_info) ) {
                    echo $debt_note;
                } else {
                    echo $this->Form->input('TtujPayment.debt_note.',array(
                        'label'=> false,
                        'class'=>'form-control',
                        'required' => false,
                        'value' => $debt_note,
                        'div' => false,
                    ));
                }
        ?>
    </td>
    <td class="text-right total-trans">
        <?php
                echo Common::getFormatPrice($total);
        ?>
    </td>
    <?php 
            if( empty($document_info) ) {
                echo $this->Html->tag('td', $this->Html->link('<i class="fa fa-times"></i>', 'javascript:', array(
                    'class' => 'delete-document-current btn btn-danger btn-xs',
                    'escape' => false,
                    'data-id' => sprintf('child-%s', $alias),
                )), array(
                    'class' => 'document-table-action '.$addClass,
                ));
            }
    ?>
</tr>
<?php 
        }
?>