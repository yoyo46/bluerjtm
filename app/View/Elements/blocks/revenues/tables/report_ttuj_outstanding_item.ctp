<?php 
        $no_ttuj = $this->Common->filterEmptyField($value, 'TtujOutstanding', 'no_ttuj');
        $ttuj_date = $this->Common->filterEmptyField($value, 'TtujOutstanding', 'ttuj_date');
        $nopol = $this->Common->filterEmptyField($value, 'TtujOutstanding', 'nopol');
        $customer = $this->Common->filterEmptyField($value, 'Customer', 'code');
        $from_city_name = $this->Common->filterEmptyField($value, 'TtujOutstanding', 'from_city_name');
        $to_city_name = $this->Common->filterEmptyField($value, 'TtujOutstanding', 'to_city_name');
        $note = $this->Common->filterEmptyField($value, 'TtujOutstanding', 'note');

        $total = !empty($total)?$total:0;
        $paid = !empty($paid)?$paid:0;

        $branch = $this->Common->filterEmptyField($value, 'Branch', 'code');

        $driver_name = $this->Common->filterEmptyField($value, 'TtujOutstanding', 'driver_name');
        $driver_name = $this->Common->filterEmptyField($value, 'DriverPenganti', 'driver_name', $driver_name);

        $customTtujDate = $this->Common->formatDate($ttuj_date, 'd/m/Y');

        $saldo = $total - $paid;
        $type = !empty($type)?$type:false;
?>
<tr>
    <?php 
            echo $this->Html->tag('td', $branch);
            echo $this->Html->tag('td', $no_ttuj);
            echo $this->Html->tag('td', $customTtujDate);
            echo $this->Html->tag('td', $nopol);
            echo $this->Html->tag('td', $customer);
            echo $this->Html->tag('td', $from_city_name);
            echo $this->Html->tag('td', $to_city_name);
            echo $this->Html->tag('td', $driver_name);
            echo $this->Html->tag('td', $note);
            echo $this->Html->tag('td', $type);
            echo $this->Html->tag('td', $this->Common->getFormatPrice($total));
            echo $this->Html->tag('td', $this->Common->getFormatPrice($paid));
            echo $this->Html->tag('td', $this->Common->getFormatPrice($saldo));
    ?>
</tr>