<?php
        $data = $this->request->data;
        $status = $this->Common->filterEmptyField($data, 'Search', 'status');

        if(!empty($values)){
            $totalPerolehan = 0;
            $totalDeprMonth = array();
            $totalPenyusutan = 0;
            $totalNilaiBuku = 0;
            $totalSisaBulan = 0;
            $totalHargaJual = 0;
            $totalLaba = 0;
            $totalAkPenyusutan = 0;

            foreach ($values as $key => $value) {
                $name = $this->Common->filterEmptyField($value, 'Asset', 'name');
                $note = $this->Common->filterEmptyField($value, 'Asset', 'note');
                $nilai_perolehan = $this->Common->filterEmptyField($value, 'Asset', 'nilai_perolehan');
                $depr_bulan = $this->Common->filterEmptyField($value, 'Asset', 'depr_bulan');
                $month_use = $this->Common->filterEmptyField($value, 'Asset', 'month_use');
                $neraca_date = $this->Common->filterEmptyField($value, 'Asset', 'neraca_date');
                $purchase_date = $this->Common->filterEmptyField($value, 'Asset', 'purchase_date');
                $last_ak_penyusutan = $this->Common->filterEmptyField($value, 'Asset', 'last_ak_penyusutan', 0);
                $assetDeprs = $this->Common->filterEmptyField($value, 'AssetDepr');

                $nilai_perolehan = $this->Common->filterEmptyField($value, 'Asset', 'nilai_perolehan', 0);
                $price_sold = $this->Common->filterEmptyField($value, 'Asset', 'price_sold', 0);
                $nilai_sisa = $this->Common->filterEmptyField($value, 'AssetGroup', 'nilai_sisa', 0);

                $umur_ekonomis = $this->Common->filterEmptyField($value, 'AssetGroup', 'umur_ekonomis', 0) * 12;
                $no_contract = $this->Common->filterEmptyField($value, 'Leasing', 'no_contract', '-');
                // $sisa_bulan = $umur_ekonomis - $month_use;

                $customNeracaDate = $this->Common->formatDate($neraca_date, 'd/m/Y');
                $customNilaiPerolehan = $this->Common->getFormatPrice($nilai_perolehan, 0, 2);
                $customDeprBulan = $this->Common->getFormatPrice($depr_bulan, 0, 2);
                $customPriceSold = $this->Common->getFormatPrice($price_sold, 0, 2);
                $tahun_neraca = $this->Common->formatDate($purchase_date, 'Y');

                $percent = 100/$umur_ekonomis;
                $customPercent = $this->Common->getFormatPrice($percent, 0, 2);
                $totalDepr = 0;

                // if( $sisa_bulan < 0 ) {
                //     $sisa_bulan = 0;
                // }

                $totalPerolehan += $nilai_perolehan;
                // $totalSisaBulan += $sisa_bulan;
?>
<tr>
    <?php 
            echo $this->Html->tag('td', $name);
            echo $this->Html->tag('td', $note);
            echo $this->Html->tag('td', $no_contract);
            echo $this->Html->tag('td', $tahun_neraca, array(
                'style' => 'text-align: center;',
            ));
            echo $this->Html->tag('td', $customNilaiPerolehan, array(
                'style' => 'text-align: right;',
            ));
            echo $this->Html->tag('td', $customDeprBulan, array(
                'style' => 'text-align: right;',
            ));
            echo $this->Html->tag('td', $customNeracaDate, array(
                'style' => 'text-align: center;',
            ));
            echo $this->Html->tag('td', $customPercent, array(
                'style' => 'text-align: center;',
            ));

            for ($i=1; $i <= 12; $i++) { 
                $month = date("m", mktime(0, 0, 0, $i, 10));
                $asset_depr = $this->Common->filterEmptyField($assetDeprs, $month, false, 0);
                $lastTotalDeprMonth = $this->Common->filterEmptyField($totalDeprMonth, $i, false, 0);

                $totalDepr += $asset_depr;
                $totalDeprMonth[$i] = $lastTotalDeprMonth + $asset_depr;

                if( !empty($asset_depr) ) {
                    $asset_depr = $this->Common->getFormatPrice($asset_depr, 0, 2);
                }

                echo $this->Html->tag('td', $asset_depr, array(
                    'style' => 'text-align: right;',
                ));
                
            }

            $total_ak_penyusutan = $totalDepr + $last_ak_penyusutan;
            $nilai_buku = $nilai_perolehan - $nilai_sisa - $total_ak_penyusutan;
            $laba = $price_sold - $nilai_buku;

            $totalPenyusutan += $totalDepr;
            $totalAkPenyusutan += $total_ak_penyusutan;
            $totalNilaiBuku += $nilai_buku;
            $totalHargaJual += $price_sold;
            $totalLaba += $laba;

            $totalDepr = $this->Common->getFormatPrice($totalDepr, 0, 2);
            $akPenyusutan = $this->Common->getFormatPrice($total_ak_penyusutan, 0, 2);
            $nilaiBuku = $this->Common->getFormatPrice($nilai_buku, 0, 2);
            $laba = $this->Common->getFormatPrice($laba, 0, 2);

            if( empty($depr_bulan) ) {
                $sisa_bulan = 0;
            } else {
                $sisa_bulan = $nilai_buku / $depr_bulan;
            }

            $customSisaBulan = $this->Common->getFormatPrice($sisa_bulan);
            $totalSisaBulan += $sisa_bulan;

            echo $this->Html->tag('td', $totalDepr, array(
                'style' => 'text-align: right;',
            ));
            echo $this->Html->tag('td', $akPenyusutan, array(
                'style' => 'text-align: right;',
            ));
            echo $this->Html->tag('td', $nilaiBuku, array(
                'style' => 'text-align: right;',
            ));
            echo $this->Html->tag('td', $customSisaBulan, array(
                'style' => 'text-align: center;',
            ));

            if( $status == 'sold' ) {
                echo $this->Html->tag('td', $customPriceSold, array(
                    'style' => 'text-align: right;',
                ));
                echo $this->Html->tag('td', $laba, array(
                    'style' => 'text-align: right;',
                ));
            }
    ?>
</tr>
<?php
            }

            $totalPerolehan = $this->Common->getFormatPrice($totalPerolehan, 0, 2);
            $totalPenyusutan = $this->Common->getFormatPrice($totalPenyusutan, 0, 2);
            $totalNilaiBuku = $this->Common->getFormatPrice($totalNilaiBuku, 0, 2);
            $totalHargaJual = $this->Common->getFormatPrice($totalHargaJual, 0, 2);
            $totalLaba = $this->Common->getFormatPrice($totalLaba, 0, 2);
            $totalAkPenyusutan = $this->Common->getFormatPrice($totalAkPenyusutan, 0, 2);
            $totalSisaBulan = $this->Common->getFormatPrice($totalSisaBulan);
?>
<tr>
    <?php 
            echo $this->Html->tag('td', '&nbsp;');
            echo $this->Html->tag('td', '&nbsp;');
            echo $this->Html->tag('td', '&nbsp;');
            echo $this->Html->tag('td', $this->Html->tag('strong', __('Total')), array(
                'style' => 'text-align: right;vertical-align: middle;',
            ));
            echo $this->Html->tag('td', $this->Html->tag('strong', $totalPerolehan), array(
                'style' => 'text-align: right;vertical-align: middle;font-weight:bold;',
            ));
            echo $this->Html->tag('td', '&nbsp;');
            echo $this->Html->tag('td', '&nbsp;');
            echo $this->Html->tag('td', '&nbsp;');

            if( !empty($totalDeprMonth) ) {
                foreach ($totalDeprMonth as $key => $depr) {
                    $depr = $this->Common->getFormatPrice($depr, 0, 2);
                    echo $this->Html->tag('td', $this->Html->tag('strong', $depr), array(
                        'style' => 'text-align: right;vertical-align: middle;font-weight:bold;',
                    ));
                }
            }

            echo $this->Html->tag('td', $this->Html->tag('strong', $totalPenyusutan), array(
                'style' => 'text-align: right;vertical-align: middle;font-weight:bold;',
            ));
            echo $this->Html->tag('td', $this->Html->tag('strong', $totalAkPenyusutan), array(
                'style' => 'text-align: right;vertical-align: middle;font-weight:bold;',
            ));
            echo $this->Html->tag('td', $this->Html->tag('strong', $totalNilaiBuku), array(
                'style' => 'text-align: right;vertical-align: middle;font-weight:bold;',
            ));
            echo $this->Html->tag('td', $this->Html->tag('strong', $totalSisaBulan), array(
                'style' => 'text-align: center;vertical-align: middle;font-weight:bold;',
            ));

            if( $status == 'sold' ) {
                echo $this->Html->tag('td', $this->Html->tag('strong', $totalHargaJual), array(
                    'style' => 'text-align: right;vertical-align: middle;font-weight:bold;',
                ));
                echo $this->Html->tag('td', $this->Html->tag('strong', $totalLaba), array(
                    'style' => 'text-align: right;vertical-align: middle;font-weight:bold;',
                ));
            }
    ?>
</tr>
<?php
        }
?>