<?php
App::uses('Model', 'Model');

class AppModel extends Model {
	public $recursive = -1;
	public $actsAs = array('Containable', 'Common', 'Approval', 'Validation');

    function find($conditions = null, $fields = array(), $order = null, $recursive = null) {
        $doQuery = true;
        $aliasName = $this->name;
        $primaryKey = $this->primaryKey;

        // Check Type First & kondisi ada PK, tdk perlu pake Order
        $primaryKeyField = __('%s.%s', $aliasName, $primaryKey);

        if( ($conditions == 'first' && !empty($fields['conditions'][$primaryKeyField]) && !empty($fields['order'])) || $conditions == 'count' ) {
            unset($fields['order']);
        }

        // check if we want the cache
        if (!empty($fields['cache'])) {
            $cacheConfig = 'default';

            // check if we have specified a custom config, e.g. different expiry time
            if (!empty($fields['cacheConfig'])) {
                $cacheConfig = $fields['cacheConfig'];
            }

            $cacheName = $fields['cache'];

            // if so, check if the cache exists
            if (($data = Cache::read($cacheName, $cacheConfig)) === false) {
                $data = parent::find($conditions, $fields, $order, $recursive);
                Cache::write($cacheName, $data, $cacheConfig);
            }

            $doQuery = false;
        }

        if ($doQuery) {
            $data = parent::find($conditions, $fields, $order, $recursive);
        }

        return $data;
    }
}
