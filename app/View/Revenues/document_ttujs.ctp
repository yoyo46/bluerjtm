<?php 
        $dataColumns = array(
            'checkbox' => array(
                'name' => $this->Form->checkbox('checkbox_all', array(
                    'class' => 'checkAll'
                )),
                'class' => 'text-center',
            ),
            'nottuj' => array(
                'name' => __('No. Ttuj'),
                'class' => 'text-center',
            ),
            'driver' => array(
                'name' => __('Supir'),
                'class' => 'text-center',
            ),
            'nopol' => array(
                'name' => __('NoPol'),
                'class' => 'text-center',
            ),
            'date' => array(
                'name' => __('Tgl Ttuj'),
                'class' => 'text-center',
            ),
            'date_beragkat' => array(
                'name' => __('Tgl Berangkat'),
                'class' => 'text-center',
            ),
            'from' => array(
                'name' => __('Dari'),
                'class' => 'text-center',
            ),
            'to' => array(
                'name' => __('Tujuan'),
                'class' => 'text-center',
            ),
            'qty' => array(
                'name' => __('Unit'),
                'class' => 'text-center',
            ),
            'qty_sisa' => array(
                'name' => __('Diterima'),
                'class' => 'text-center',
            ),
            'note' => array(
                'name' => __('Keterangan'),
                'class' => 'text-center',
            ),
            'action' => array(
                'name' => __('Action'),
                'class' => 'hide on-show',
            ),
        );
        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );
        // echo $this->element('blocks/ttuj/search_document_ttujs');
        echo $this->element('blocks/ttuj/search_ttuj', array(
            'ajax' => true,
            'status' => false,
            'label_tgl' => __('Tgl Berangkat'),
        ));
?>
<div class="box-body table-responsive browse-form document-ttuj">
    <table class="table table-hover">
        <?php
                if( !empty($fieldColumn) ) {
                    echo $this->Html->tag('thead', $this->Html->tag('tr', $fieldColumn));
                }

                if(!empty($values)){
                    foreach ($values as $key => $value) {
                        $id = $this->Common->filterEmptyField($value, 'Ttuj', 'id');
                        $nopol = $this->Common->filterEmptyField($value, 'Ttuj', 'nopol');
                        $no_ttuj = $this->Common->filterEmptyField($value, 'Ttuj', 'no_ttuj');
                        $from_city_name = $this->Common->filterEmptyField($value, 'Ttuj', 'from_city_name');
                        $to_city_name = $this->Common->filterEmptyField($value, 'Ttuj', 'to_city_name');
                        $driver_name = $this->Common->filterEmptyField($value, 'Ttuj', 'driver_name');
                        $note = $this->Common->filterEmptyField($value, 'Ttuj', 'note');
                        $ttuj_date = $this->Common->filterEmptyField($value, 'Ttuj', 'ttuj_date');
                        $tgljam_berangkat = $this->Common->filterEmptyField($value, 'Ttuj', 'tgljam_berangkat');
                        $driver_name = $this->Common->filterEmptyField($value, 'Ttuj', 'driver_name');

                        $qty = $this->Common->filterEmptyField($value, 'Ttuj', 'qty');
                        $qty_diterima = $this->Common->filterEmptyField($value, 'Ttuj', 'qty_diterima', 0);
                        $qty_sisa = $qty - $qty_diterima;

                        $customer = $this->Common->filterEmptyField($value, 'Customer', 'code');
                        $ttuj_date = $this->Common->formatDate($ttuj_date, 'd/m/Y');
                        $tgljam_berangkat = $this->Common->formatDate($tgljam_berangkat, 'd/m/Y H:i:s');

                        $checkbox = isset($checkbox)?$checkbox:true;
                        $alias = sprintf('child-%s', $id);

                        $contentInput = $this->Form->input('SuratJalanDetail.qty.'.$key,array(
                            'label'=> false,
                            'required' => false,
                            'error' => false,
                            'class'=>'form-control input_number text-center sisa-amount',
                            'value' => $qty_sisa,
                            'data-alert' => __('Silahkan isi jumlah unit yang diterima'),
                        ));
                        $contentInput .= $this->Form->hidden('SuratJalanDetail.ttuj_id.'.$key,array(
                            'value'=> $id,
                        ));

                        if( !empty($checkbox) ) {
                            printf('<tr data-value="%s" class="child %s">', $alias, $alias);

                            $checkboxContent = $this->Form->checkbox('document_checked.', array(
                                'class' => 'check-option',
                                'value' => $id,
                            ));
                            $checkboxContent .= $this->Form->input('SuratJalan.ttuj_id.', array(
                                'type' => 'hidden',
                                'value' => $id,
                            ));

                            echo $this->Html->tag('td', $checkboxContent, array(
                                'class' => 'checkbox-action',
                            ));
                        } else {
                            printf('<tr class="child child-%s">', $alias);
                        }

                        echo $this->Html->tag('td', $no_ttuj);
                        echo $this->Html->tag('td', $driver_name);
                        echo $this->Html->tag('td', $nopol, array(
                            'class' => 'text-center',
                        ));
                        echo $this->Html->tag('td', $ttuj_date, array(
                            'class' => 'text-center',
                        ));
                        echo $this->Html->tag('td', $tgljam_berangkat, array(
                            'class' => 'text-center',
                        ));
                        echo $this->Html->tag('td', $from_city_name);
                        echo $this->Html->tag('td', $to_city_name);
                        echo $this->Html->tag('td', $qty, array(
                            'class' => 'text-center on-remove',
                        ));
                        echo $this->Html->tag('td', $qty_diterima, array(
                            'class' => 'text-center on-remove',
                        ));
                        echo $this->Html->tag('td', $qty_sisa, array(
                            'class' => 'text-center total-value hide on-show',
                            'data-alert' => __('Unit diterima tidak boleh melebihi muatan'),
                        ));
                        echo $this->Html->tag('td', $contentInput, array(
                            'class' => 'hide on-show',
                        ));
                        echo $this->Html->tag('td', $note, array(
                            'class' => 'on-remove',
                        ));
                        echo $this->Html->tag('td', $this->Html->link($this->Common->icon('fa fa-times'), '#', array(
                            'class' => 'delete-document-current btn btn-danger btn-xs',
                            'escape' => false,
                            'data-id' => sprintf('child-%s', $alias),
                            'data-alert' => __('Anda ingin menghapus ttuj ini?'),
                        )), array(
                            'class' => 'text-center document-table-action hide on-show',
                        ));
                ?>
                </tr>
        <?php
                    }
                }else{
                    echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                        'class' => 'alert alert-warning text-center',
                        'colspan' => '12'
                    )));
                }
        ?>
    </table>
</div><!-- /.box-body -->
<?php
        echo $this->element('pagination', array(
            'options' => array(
                'data-action' => $data_action,
                'class' => 'ajaxModal',
                'title' => $title,
            ),
        ));
?>