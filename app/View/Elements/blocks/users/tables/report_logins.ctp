<?php
        if(!empty($values)){
            foreach ($values as $key => $value) {
                $date = $this->Common->filterEmptyField($value, 'LogUserLogin', 'created');
                $browser = $this->Common->filterEmptyField($value, 'LogUserLogin', 'browser');
                $ip = $this->Common->filterEmptyField($value, 'LogUserLogin', 'ip');
                $name = $this->Common->filterEmptyField($value, 'Employe', 'full_name');

                $customDate = $this->Common->formatDate($date);
?>
<tr>
    <?php 
            echo $this->Html->tag('td', $customDate, array(
                'style' => 'text-align: center;'
            ));
            echo $this->Html->tag('td', $name);
            echo $this->Html->tag('td', $browser, array(
                'style' => 'text-align: center;'
            ));
            echo $this->Html->tag('td', $ip, array(
                'style' => 'text-align: center;'
            ));
    ?>
</tr>
<?php
            }
        }
?>