<?php
class CustomerNoType extends AppModel {
	var $name = 'CustomerNoType';
    var $useTable = 'customers';

    function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->virtualFields['order_sort'] = sprintf('CASE WHEN %s.order IS NULL THEN 1 ELSE 0 END', $this->alias);
    }
}
?>