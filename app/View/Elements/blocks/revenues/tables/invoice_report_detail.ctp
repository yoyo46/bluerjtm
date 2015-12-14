<?php 
        if(!empty($values)){
            $grandtotal = 0;

            foreach ($values as $key => $value) {
                $id = $this->Common->filterEmptyField($value, 'Invoice', 'id');
                $no_invoice = $this->Common->filterEmptyField($value, 'Invoice', 'no_invoice');
                $tarif_type = $this->Common->filterEmptyField($value, 'Invoice', 'tarif_type');
                $invoice_date = $this->Common->filterEmptyField($value, 'Invoice', 'invoice_date');
                $period_from = $this->Common->filterEmptyField($value, 'Invoice', 'period_from');
                $period_to = $this->Common->filterEmptyField($value, 'Invoice', 'period_to');
                $total = $this->Common->filterEmptyField($value, 'Invoice', 'total');

                $grandtotal += $total;
                $customDate = $this->Common->customDate($invoice_date, 'd M Y');
                $customTotal = $this->Common->getFormatPrice($total);
                $customTarifType = ucwords($tarif_type);
                $customPeriod = $this->Common->getCombineDate($period_from, $period_to);

                $content = $this->Html->tag('td', $no_invoice);
                $content .= $this->Html->tag('td', $customTarifType, array(
                    'style' => 'text-align: center;',
                ));
                $content .= $this->Html->tag('td', $customDate, array(
                    'style' => 'text-align: center;',
                ));
                $content .= $this->Html->tag('td', $customPeriod);
                $content .= $this->Html->tag('td', $customTotal, array(
                    'style' => 'text-align: right;',
                ));

                echo $this->Html->tag('tr', $content);
            }

            $grandtotal = $this->Common->getFormatPrice($grandtotal);
            $content = $this->Html->tag('td', __('Total'), array(
                'style' => 'text-align: right;',
                'colspan' => 4,
            ));
            $content .= $this->Html->tag('td', $grandtotal, array(
                'style' => 'text-align: right;',
            ));

            echo $this->Html->tag('tr', $content, array(
                'style' => 'font-weight: bold;'
            ));
        }
?>