<?php
		if( !empty($checkTtujSameDay) ) {
			$no_ttuj = Common::hashEmptyField($checkTtujSameDay, 'Ttuj.no_ttuj');
			$ttuj_date = Common::hashEmptyField($checkTtujSameDay, 'Ttuj.ttuj_date', '-', array(
				'date' => 'd M Y',
			));
			$from_city_name = Common::hashEmptyField($checkTtujSameDay, 'Ttuj.from_city_name');
			$to_city_name = Common::hashEmptyField($checkTtujSameDay, 'Ttuj.to_city_name');
			$note = Common::hashEmptyField($checkTtujSameDay, 'Ttuj.note', '-');
			
			$hidden_text = $this->Form->hidden('hidden-msg', array(
				'value' => __('TTUJ serupa sudah pernah diinput :TenterTNo TTUJ: %sTenterTTanggal: %sTenterTDari: %sTenterTTujuan: %sTenterTKeteranan Muat: %sTenterTApakah anda yakin akan menyimpan TTUJ ini ?', $no_ttuj, $ttuj_date, $from_city_name, $to_city_name, $note),
				'id' => 'ttuj-alert-msg',
			));
		} else {
			$hidden_text = '';
		}

		echo $this->Html->tag('div', $hidden_text, array(
			'class' => 'wrapper-ttuj-alert-msg',
		));
?>