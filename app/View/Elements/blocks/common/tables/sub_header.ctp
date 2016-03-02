<?php 
        $contentLabel = $labelName;
        $contentLabel .= $this->Html->tag('span', ':', array(
            'style' => 'float: right;margin-right: 3px;',
        ));
        $contentLabel .= $this->Html->tag('span', '', array(
            'style' => 'clear: both;',
        ));

        $contentP = $this->Html->tag('label', $contentLabel, array(
            'style' => 'width: 140px;font-weight: 600;display: inline-block;',
        ));
        $contentP .= $this->Html->tag('span', $value);
        echo $this->Html->tag('p', $contentP, array(
            'style' => 'margin: 0;',
        ));
?>