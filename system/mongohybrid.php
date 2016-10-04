<?php
/*
*	MongoHybrid
*	-	Easily switch between Schemaless or MongoDB...
*	-	
*/

class MongoHybrid {

    protected $driver;

    public function __construct($server, $options=array()) {

        if(strpos($server, 'mongodb://')===0) {
            $this->driver = new HyMongo($server, $options);
        }

        if(strpos($server, 'schemaless://')===0) {
            $this->driver = new HySchemaless($server, $options);
        }
    }

    public function dropCollection($name, $db = null) {
        return $this->driver->getCollection($name, $db)->drop();
    }

    public function renameCollection($newname, $db = null) {

        return $this->driver->getCollection($name, $db)->renameCollection($newname);
    }

    public function save($collection, &$data) {
        return $this->driver->save($collection, $data);
    }

    public function insert($collection, &$doc) {
        return $this->driver->insert($collection, $doc);
    }


    /*
        simple key-value storage implementation
    */

    /**
     * Get value for specific key
     *
     * @param  string $collection
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    public function getKey($collection, $key, $default = null) {

        $entry = $this->driver->findOne($collection, array('key' => $key) );

        return $entry ? $entry['val'] : $default;
    }

    /**
     * Set value for specific key
     *
     * @param  string $collection
     * @param  string $key
     * @param  mixed $value
     */
    public function setKey($collection, $key, $value) {

        $entry = $this->driver->findOne($collection, array('key' => $key) );

        if ($entry) {
            $entry['val'] = $value;
        } else {
            $entry =array(
                'key' => $key,
                'val' => $value
            );
        }

        return $this->driver->save($collection, $entry);
    }


    /**
     * Delete Key(s)
     *
     * @param  string $collection
     * @param  string $key
     * @return integer
     */
    public function removeKey($collection, $key) {
        return $this->driver->remove($collection, array('key' => (is_array($key) ? array('$in' => $key) : $key)));
    }

    /**
     * Check if key exists
     *  *

     * @param  string $collection @param  string $collection
     * @param  string $key
     */
    public function keyExists($collection, $key) {
        return $this->driver->count( $collection, array('key' => $key) );
    }

    /**
     * Increment value by x
     *
     * @param  string  $collection
     * @param  string  $key
     * @param  integer $by
     * @return integer
     */
    public function incrKey($collection, $key, $by = 1) {

        $current = $this->getKey($collection, $key, 0);
        $newone  = $current + $by;

        $this->setKey($collection, $key, $newone);

        return $newone;
    }

    /**
     * Decrement value by x
     *
     * @param  string  $collection
     * @param  string  $key
     * @param  integer $by
     * @return integer
     */
    public function decrKey($collection, $key, $by = 1) {
        return $this->incr($collection, $key, ($by * -1));
    }

    /**
     * Add item to a value (right)
     *  *

     * @param  string $collection @param  string $collection
     * @param  string $key
     * @param  mixed $value
     * @return integer
     */
    public function rpush($collection, $key, $value) {

        $list = $this->getKey($collection, $key, array() );

        $list[] = $value;

        $this->setKey($collection, $key, $list);

        return count($list);
    }

    /**
     * Add item to a value (left)
     *  *

     * @param  string $collection @param  string $collection
     * @param  string $key
     * @param  mixed $value
     * @return integer
     */
    public function lpush($collection, $key, $value) {

        $list = $this->getKey($collection, $key, array() );

        array_unshift($list, $value);

        $this->setKey($collection, $key, $list);

        return count($list);
    }



    /**
     * Set the value of an element in a list by its index
     *
     * @param  string $collection
     * @param  string $key
     * @param  integer $index
     * @param  mixed $value
     * @return boolean
     */
    public function lset($collection, $key, $index, $value) {

        $list = $this->getKey($collection, $key, array());

        if($index < 0) {
            $index = count($list) - abs(index);
        }

        if(isset($list[$index])){
            $list[index] = $value;
            $this->setKey($collection, $key, $list);

            return true;
        }

        return false;
    }

    /**
     * Get an element from a list by its index
     *
     * @param  string $collection
     * @param  string $key
     * @param  integer $index
     * @return mixed
     */
    public function lindex($collection, $key, $index) {

        $list = $this->getKey($collection, $key, array());

        if($index < 0) {
            $index = count($list) - abs(index);
        }

        return isset($list[$index]) ? $list[$index]:null;
    }

    /**
     * Set the string value of a hash field
     *
     * @param  string $collection
     * @param  string $key
     * @param  string $field
     * @param  mixed $value
     */
    public function hset($collection, $key, $field, $value) {

        $set = $this->getKey($collection, $key, array());

        $set[$field] = $value;
        $this->setKey($collection, $key, $set);
    }

    /**
     * Get the value of a hash field
     *
     * @param  string $collection
     * @param  string $key
     * @param  string $field
     * @param  mixed $default
     * @return mixed
     */
    public function hget($collection, $key, $field, $default=null) {

        $set = $this->getKey($collection, $key, array());

        return isset($set[$field]) ? $set[$field]:$default;
    }

    /**
     * Get all the fields and values in a hash
     *
     * @param  string $collection
     * @param  string $key
     * @return array
     */
    public function hgetall($key) {

        $set = $this->getKey($collection, $key, array());

        return $set;
    }

    /**
     * Determine if a hash field exists
     *
     * @param  string $collection
     * @param  string $key
     * @param  string $field
     * @return boolean
     */
    public function hexists($collection, $key, $field) {

        $set = $this->getKey($collection, $key, array());

        return isset($set[$field]);
    }

    /**
     * Get all the fields in a hash
     *
     * @param  string $collection
     * @param  string $key
     * @return array
     */
    public function hkeys($key) {

        $set = $this->getKey($collection, $key, array());

        return array_keys($set);
    }

    /**
     * Get all the values in a hash
     *
     * @param  string $collection
     * @param  string $key
     * @return array
     */
    public function hvals($key) {

        $set = $this->getKey($collection, $key, array());

        return array_values($set);
    }

    /**
     * Get the number of fields in a hash
     *
     * @param  string $collection
     * @param  string $key
     * @return integer
     */
    public function hlen($key) {

        return count($this->hkeys($key));
    }

    /**
     * Delete one or more hash fields
     *
     * @param  string $collection
     * @param  string $key
     * @return integer
     */
    public function hdel($key) {

        $set = $this->getKey($collection, $key, array());

        if(!count($set)) return 0;

        $fields  = func_get_args();
        $removed = 0;

        for ($i=1; $i<count($fields); $i++){

            $field = $fields[$i];

            if(isset($set[$field])){
                unset($set[$field]);
                $removed++;
            }
        }

        $this->setKey($collection, $key, $set);

        return $removed;
    }

    /**
     * Increment the integer value of a hash field by the given number
     *
     * @param  string  $key
     * @param  string  $field
     * @param  integer $by
     * @return integer
     */
    public function hincrby($collection, $key, $field, $by = 1) {

        $current = $this->hget($collection, $key, $field, 0);
        $newone  = $current+by;

        $this->hset($collection, $key, $field, $newone);

        return $newone;
    }

    /**
     * Get the values of all the given hash fields
     *
     * @param  string $collection
     * @param  string $key
     * @return array
     */
    public function hmget($key) {

        $set     = $this->getKey($collection, $key, array());
        $fields  = func_get_args();
        $values  = array();

        for ($i=1; $i<count($fields); $i++){
            $field = $fields[$i];
            $values[] = isset($set[$field]) ? $set[$field]:null;
        }

        return $values;
    }

    /**
     * Set multiple hash fields to multiple values
     *
     * @param  string $collection
     * @param  string $key
     */
    public function hmset($key) {

        $set     = $this->getKey($collection, $key, array());
        $args    = func_get_args();

        for ($i=1; $i<count($fields); $i++){
            $field = $args[$i];
            $value = isset($args[($i+1)]) ? $args[($i+1)] : null;

            $set[$field] = $value;
            $i = $i + 1;
        }

        $this->setKey($collection, $key, $set);
    }


    public function __call($method, $args) {
        return call_user_func_array( array($this->driver, $method), $args);
    }
}

class ResultSet extends \ArrayObject {

    protected $documents;
    protected $driver;
    protected $cache;

    public function __construct($driver, &$documents) {

        $this->driver = $driver;
        $this->cache  = array();

        parent::__construct($documents);
    }

    public function hasOne($collections) {

        foreach ($this as &$doc) {

            foreach ($collections as $fkey => $collection) {

                if (isset($doc[$fkey]) && $doc[$fkey]) {

                    if (!isset($this->cache[$collection][$doc[$fkey]])) {
                        $this->cache[$collection][$doc[$fkey]] = $this->driver->findOneById($collection, $doc[$fkey]);
                    }

                    $doc[$fkey] = $this->cache[$collection][$doc[$fkey]];
                }
            }
        }

    }

    public function hasMany($collections) {

        foreach ($this as &$doc) {

            if (isset($doc['_id'])) {

                foreach ($collections as $collection => $fkey) {

                    $doc[$collection] = $this->driver->find($collection, 
                    	array('filter' => array($fkey=>$doc['_id']))
                    );
                }
            }
        }
    }

    public function toArray() {
        return $this->getArrayCopy();
    }
}

class HyMongo {

    protected $client;
    protected $options;

    public function __construct($server, $options=array()) {


        $this->client  = new \MongoClient($server, $options);
        $this->db      = $this->client->selectDB($options["db"]);
        $this->options = $options;
    }

    public function getCollection($name, $db = null){

        if($db) {
            $name = "{$db}/{$name}";
        }

        $name = str_replace('/', '_', $name);

        return $this->db->selectCollection($name);
    }

    public function findOneById($collection, $id){

        if(is_string($id)) $id = new \MongoId($id);

        $doc =  $this->getCollection($collection)->findOne( array("_id" => $id) );

        if(isset($doc["_id"])) $doc["_id"] = (string) $doc["_id"];

        return $doc;
    }

    public function findOne($collection, $filter = array()) {

        if(isset($filter["_id"]) && is_string($filter["_id"])) $filter["_id"] = new \MongoId($filter["_id"]);

        $doc =  $this->getCollection($collection)->findOne($filter);

        if(isset($doc["_id"])) $doc["_id"] = (string) $doc["_id"];

        return $doc;
    }

    public function find($collection, $options = array()){

        $filter = isset($options["filter"]) && $options["filter"] ? $options["filter"] : array();
        $limit  = isset($options["limit"])  && $options["limit"]  ? $options["limit"]  : null;
        $sort   = isset($options["sort"])   && $options["sort"]   ? $options["sort"]   : null;
        $skip   = isset($options["skip"])   && $options["skip"]   ? $options["skip"]   : null;

        if($filter && isset($filter["_id"])) {
            $filter["_id"] = new \MongoId($filter["_id"]);
        }

        $cursor = $this->getCollection($collection)->find($filter);

        if($limit) $cursor->limit($limit);
        if($sort)  $cursor->sort($sort);
        if($skip)  $cursor->skip($skip);

        if ($cursor->count()) {

            $docs = array_values(iterator_to_array($cursor));

            foreach ($docs as &$doc) {
                if(isset($doc["_id"])) $doc["_id"] = (string) $doc["_id"];
            }

        } else {

            $docs = array();
        }

        $resultSet = new ResultSet($this, $docs);

        return $resultSet;
    }

    public function insert($collection, &$doc) {

        if(isset($doc["_id"]) && is_string($doc["_id"])) $doc["_id"] = new \MongoId($doc["_id"]);

        $ref = $doc;

        $return = $this->getCollection($collection)->insert($ref);

        if(isset($ref["_id"])) $ref["_id"] = (string) $ref["_id"];

        $doc = $ref;

        return $return;
    }

    public function save($collection, &$data) {

        if(isset($data["_id"]) && is_string($data["_id"])) $data["_id"] = new \MongoId($data["_id"]);

        $ref = $data;

        $return = $this->getCollection($collection)->save($ref);

        if(isset($ref["_id"])) $ref["_id"] = (string) $ref["_id"];

        $data = $ref;

        return $return;
    }


    public function update($collection, $criteria, $data) {

        if(isset($criteria["_id"]) && is_string($criteria["_id"])) $criteria["_id"] = new \MongoId($criteria["_id"]);
        if(isset($data["_id"]) && is_string($data["_id"])) $data["_id"] = new \MongoId($data["_id"]);

        return $this->getCollection($collection)->update($criteria, $data);
    }

    public function remove($collection, $filter=array()) {

        if(isset($filter["_id"]) && is_string($filter["_id"])) $filter["_id"] = new \MongoId($filter["_id"]);

        return $this->getCollection($collection)->remove($filter);
    }

    public function count($collection, $filter=array()) {

        return $this->getCollection($collection)->count($filter);
    }


}

class HySchemaless {

    protected $client;

    public function __construct($server, $options=array()) {

        $this->client = new \Schemaless\Client(str_replace('schemaless://', '', $server));
        $this->db     = $options["db"];
    }

    public function getCollection($name, $db = null){

        if(strpos($name, '/') !== false) {
            list($db, $name) = explode('/', $name, 2);
        }

        if(!$db) {
            $db = $this->db;
        }

        $name = str_replace('/', '_', $name);

        return $this->client->selectCollection($db, $name);
    }

    public function findOne($collection, $filter = array()) {
        return $this->getCollection($collection)->findOne($filter);
    }

    public function findOneById($collection, $id){

        return $this->getCollection($collection)->findOne( array("_id" => $id) );
    }

    public function find($collection, $options = array()){

        $filter = isset($options["filter"]) ? $options["filter"] : null;
        $limit  = isset($options["limit"])  ? $options["limit"] : null;
        $sort   = isset($options["sort"])   ? $options["sort"] : null;
        $skip   = isset($options["skip"])   ? $options["skip"] : null;

        $cursor = $this->getCollection($collection)->find($filter);

        if($limit) $cursor->limit($limit);
        if($sort)  $cursor->sort($sort);
        if($skip)  $cursor->skip($skip);

        $docs      = $cursor->toArray();
        $resultSet = new ResultSet($this, $docs);

        return $resultSet;
    }

    public function insert($collection, &$doc) {
        return $this->getCollection($collection)->insert($doc);
    }

    public function save($collection, &$data) {
        return $this->getCollection($collection)->save($data);
    }

    public function update($collection, $criteria, $data) {
        return $this->getCollection($collection)->update($criteria, $data);
    }

    public function remove($collection, $filter=array()) {
        return $this->getCollection($collection)->remove($filter);
    }

    public function count($collection, $filter=array()) {
        return $this->getCollection($collection)->count($filter);
    }
}

