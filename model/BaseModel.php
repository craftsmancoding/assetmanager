<?php
/**
 * BaseModel : our little abstraction for a simpler UI
 * 
 * I'm simplifiying the interface a bit (I hope) with the underlying xPDO objects
 * As much as I like the cleaner interface offered by static functions, it just doesn't work 
 * well here because we need to inject the containing MODX object as a dependency.  So the
 * children of BaseModel let us:
 *      1. access object attributes directly (without the $obj->get() and $obj->set() functions)
 *      2. offer shorthand ways of retrieval/searching
 *
 * This is a bit tricky because we already have an ORM layer: e.g. its Product class already
 * handles newObject, save, etc -- we can't just extend it for the features we want because xpdo
 * changes the exact class used depending on the database driver.  So we end up with this weird
 * hybrid class that implements many of the same functions xpdo (e.g. toArray) and passes them
 * through to the ORM object underneath. 
 *
 * WARNING: confusion can arrise where it's not clear whether you've got a Assman\Asset
 * or an xPDO \Asset class on your hands because they look and act very similarly.  
 *
 * php ../repoman/repoman schema:parse . --model=assman --table_prefix=ass_ --overwrite 
 *
 * Beware late static bindings!
 * See http://stackoverflow.com/questions/10504129/when-using-self-parent-static-and-how
 */
namespace Assman;
class BaseModel {

    public $modx;
    public $pk = 'id'; // Define the primary key for the table object defined in $xclass
    
    // true if the object is new and un-persisted.
    // false if the object has been saved
    //private $is_new; // use $this->modelObj->isNew()
    
    private $previous_vals = array();
    
    // Used for new/save ops
    public $modelObj = 'xPDOObject'; 
    
    //public static $xclass; // The classname for xPDO when referencing objects of this class
    //public static $default_sort_col;
    //public static $default_sort_dir = 'ASC';

    // Any array keys that define a control parameter and not a filter parameter
    public $control_params = array('limit','offset','sort','dir','select');
    
    // Store any validation errors here
    public $errors = array();

    // Search columns
    public $search_columns = array(); 

    /**
     * Special words reserved for use by the Foxycart- and local-API
     *
     * From https://wiki.foxycart.com/v/1.1/cheat_sheet
     * 1:, 2:, 3:, 4:, etc
     * h: session variables
     */
    public $reserved_words = array(
        // Foxycart reserves some words
        'name','price','image','url','code','quantity','quantity_max','quantity_min',
        'category','weight','length','width','height','shipto','id',
        'discount_quantity_amount','discount_quantity_percentage','discount_price_amount',
        'discount_price_percentage','allunits','incremental','repeat','single','sub_frequency',
        'sub_startdate','sub_enddate','sub_token','sub_cancel','cart','empty','coupon','redirect',
        'output','callback','fc_auth_token','customer_email','customer_first_name','shipping_first_name',
        'customer_last_name','shipping_last_name','customer_address1','shipping_address1','customer_address2',
        'shipping_address2','customer_phone','shipping_phone','customer_company','shipping_company',
        'customer_city','shipping_city','customer_postal_code','shipping_postal_code','customer_state',
        'shipping_state','customer_country','shipping_country',
        // Plus any fields that are columns of the main products table, including calc'd columns!
        'product_id','store_id','parent_id','template_id','asset_id','name','title','description',
        'content','type','sku','sku_vendor','variant_matrix','alias','uri','track_inventory','qty_inventory',
        'qty_alert','qty_min','qty_max','qty_backorder_max','price','price_strike_thru','price_sale',
        'weight','sale_start','sale_end','category','is_active','in_menu','billing_unit','billing_interval',
        'duration_unit','duration_interval','user_group_id','role_id','author_id','timestamp_created','timestamp_modified',
        'seq', 'calculated_price','cache_lifetime'
    );
    
    /** 
     * We set $this->modelObj here instead of extending the base xpdoObject class because
     * xpdo abstracts the database at run-time and the exact class instantiated depends on 
     * the type of database used.
     *
     * @param object $modx
     * @param object $xpdo (optional: pass this class an existing xpdo object to endow it with special methods) 
     *
     */
    public function __construct(\modX &$modx, &$obj=null) {
        $this->modx =& $modx;
        if ($obj) {
            if (!is_a($obj, $this->xclass)) {
                throw new \Exception('Invalid object type.');
            }
            $this->modelObj =& $obj;

        }
        else {
            $this->modelObj = $modx->newObject($this->xclass); 
        }
        
/*
        if (!$this->modelObj->isNew()) {
            $this->previous_vals = $this->modelObj->toArray();
        }
*/
    }

    /**
     * Hot potato
     */
    public function __call($name, $args) { 
        return call_user_func_array(array($this->modelObj,$name), $args);
    }
    
    /**
     * 
     */
    public function __get($key) {
        return $this->modelObj->get($key);
    }
        
    /**
     *
     *
     */
    public function __set($key, $value) {
        return $this->modelObj->set($key,$value);
    }

    /**
     * 
     */
    public function __isset($key) {
        $attributes = $this->modx->getFields($this->xclass);
        return array_key_exists($key, $attributes);
    }

    /**
     * 
     */
    public function __unset($key) {
        return $this->modelObj->set($key,null);
    }
    
    /**
     *
     */
    public function __toString() {
        return print_r($this->modelObj->toArray(),true);
    }
    
    /**
     * Retrive "all" records matching the filter $args.
     *
     * We use getIterator, but we have to work around the "feature" (bug?) that 
     * it will not return an empty array if it has no results. See
     * https://github.com/modxcms/revolution/issues/11373
     *
     * @param array $arguments (including filters)
     * @param boolean $debug
     * @return mixed xPDO iterator (i.e. a collection, but memory efficient) or SQL query string
     */
    public function all($args,$debug=false) {
    
        // If you get this error: "Call to a member function getOption() on a non-object", it could mean:
        // 1) you tried to call this method statically, e.g. Product::all()
        // 2) you forgot to initialize the class and pass a modx instance to the contructor (dependency injection!)
        $limit = (int) $this->modx->getOption('limit',$args,$this->modx->getOption('assman.default_per_page','',$this->modx->getOption('default_per_page')));
        $offset = (int) $this->modx->getOption('offset',$args,0);
        $sort = $this->quoteSort($this->modx->getOption('sort',$args,$this->default_sort_col));
        $dir = $this->modx->getOption('dir',$args,$this->default_sort_dir);
        $select_cols = $this->modx->getOption('select',$args);
        
        // Clear out non-filter criteria
        $args = self::getFilters($args); 
            
        $criteria = $this->modx->newQuery($this->xclass);

        if ($args) {
            if (isset($args['searchterm'])) {
                $searchterm = $args['searchterm'];
                unset($args['searchterm']);
                $search_c = array();
                $first = array_shift($this->search_columns);
                $search_c[$first.':LIKE'] = '%'.$searchterm.'%';
                foreach ($this->search_columns as $c) {
                    $search_c['OR:'.$c.':LIKE'] = '%'.$searchterm.'%'; 
                }
                $criteria->where($search_c);
            }
            else {
                $criteria->where($args);
            }
        }
        

        if ($args) {
            $criteria->where($args);
        }
        
        if ($limit) {
            $criteria->limit($limit, $offset); 
            $criteria->sortby($sort,$dir);
        }
    
        if ($debug) {
            $criteria->prepare();
            return $criteria->toSQL();
        }

        // Both array and string input seem to work
        if (!empty($select_cols)) {
            $criteria->select($select_cols);
        }
        // Workaround for issue https://github.com/modxcms/revolution/issues/11373
        $collection = $this->modx->getIterator($this->xclass,$criteria);
        foreach ($collection as $c) {
            $collection->rewind();           
            return $collection;
        }
        return array();
    }


    /**
     * Edit a collection of records. E.g. this can be used to handle a complex form allowing 
     * the user to edit multiple records at once.
     * 
     * @param array $records
     * @return boolean
     */
    public function bulkEdit($records) {
    
    }
    
    /**
     * Given a possibly deeply nested array, this flattens it to simple key/value pairs
     * @param array $array
     * @param string $prefix (needed for recursion)
     * @return array
     */
    public function flattenArray(array $array,$prefix='',$separator='.') {
        $result = array();
        foreach ($array as $key => $value)
        {
            if (is_array($value))
                $result = array_merge($result, $this->flattenArray($value, $prefix . $key . $separator));
            else
                $result[$prefix . $key] = $value;
        }   
        return $result;
    }
    
    /**
     * Convert data in an indexed structure to a recordset.
     * This is necessary when processing forms with multiple records of data:
     *
     *     Record 1:
     *      <input name="x[]" value="A"/>
     *      <input name="y[]" value="B">
     *     Record 2:
     *      <input name="x[]" value="C"/>
     *      <input name="y[]" value="D"/>
     * 
     * Data arrives indexed like this:
     *      array( 'x' => array(0=>A, 1=>C) ),
     *      array( 'y' => array(0=>B, 1=>D) ),
     *
     * Whereas we want it formatted as a recordset like this:
     *      array(
     *          array('x'=>'A', 'y' => 'B'),
     *          array('x'=>'C', 'y' => 'D'),
     *     )
     *
     * This function converts the format.  It also handles simple hashes: in that case,
     * the simple hash is "wrapped" in array, thus returning a "record-set" with 1 record.
     *
     * @param array $indexed array
     * @return array record set
     */
    public static function indexedToRecordset(array $indexed) {
        $out = array();
        foreach($indexed as $k => $v) {
            $v = (array) $v;
            foreach ($v as $i => $v2) {
                $out[$i][$k] = $v[$i];
            }
        }
        return $out;        
    }
    
    /**
     * 
     * @param array $args
     * @return integer
     */
    public function count($args) {
        if(!isset($args['limit'])) $args['limit'] = 0;
        // Clear out non-filter criteria
        $args = $this->getFilters($args); 
        
        $criteria = $this->modx->newQuery($this->xclass);
        if ($args) {
            $criteria->where($args);
        }
        return $this->modx->getCount($this->xclass,$criteria);
    }

    /**
     * Delete an object by its primary key
     */    
    public static function delete(int $id) {
        if ($Obj = $this->find($id)) {
            return $Obj->remove();
        }
        else {
            throw new \Exception('Object not found.');
        }
    }
    
    /**
     * Retrieve a single object by its primary key id -- we pass this back to the constructor
     * so we can return an instance of this class. (The "get" function is reserved for the single
     * object, so we can't use it to operate on a collection).
     *
     * @param integer $id
     * @return mixed
     */    
    public function find($id) {
        if ($obj = $this->modx->getObject($this->xclass, $id)) {
            $classname = '\\Assman\\'.$this->xclass;        
            return new $classname($this->modx,$obj);
        }
        return false;
    }
        
    /**
     * Return any validation errors
     */
    public function getErrors() {
        return $this->errors;
    }
    
    /**
     * Remove any "control" arguments and return only "filter" arguments
     * with some convenience bits for searches. Controls are things like limit, offset,
     * or other things that control HOW the results are returned whereas filters determine
     * WHAT gets returned.
     *
     * @param array
     * @return array
     */
    public function getFilters($array) {
        foreach ($this->control_params as $p) {
            unset($array[$p]);
        }
        
        foreach ($array as $k => $v) {
            // For convenience, we add in the %'s
            if (strtoupper(substr($k,-5)) == ':LIKE') $array[$k] = '%'.$v.'%';
            if (strtoupper(substr($k,-9)) == ':NOT LIKE') $array[$k] = '%'.$v.'%';
            if (strtoupper(substr($k,-12)) == ':STARTS WITH') {
                unset($array[$k]);
                $array[substr($k,0,-12).':LIKE'] = $v.'%';
            }
            if (strtoupper(substr($k,-10)) == ':ENDS WITH') {
                unset($array[$k]);
                $array[substr($k,0,-10).':LIKE'] = '%'.$v;
            }

            // Remove any simple array stuff
            if (is_integer($k)) unset($array[$k]);
        }
        return $array;
    }
    
    /**
     * Like "all", but this limits the result to a single record: the first record matching
     * the given filter $args.  This does set the modelObj.
     *
     * @param array $arguments (including filters)
     * @param boolean $debug
     * @return mixed object instance of this class or false
     */
    public function one($args,$debug=false) {
        // If you get this error: "Call to a member function getOption() on a non-object", it could mean:
        // 1) you tried to call this method statically, e.g. Product::all()
        // 2) you forgot to initialize the class and pass a modx instance to the contructor (dependency injection!)
        
        // Might as well leave these in... 
        $offset = (int) $this->modx->getOption('offset',$args,0);
        $sort = $this->modx->getOption('sort',$args,$this->default_sort_col);
        $dir = $this->modx->getOption('dir',$args,$this->default_sort_dir);
        $select_cols = $this->modx->getOption('select',$args);
        
        // Clear out non-filter criteria
        $args = self::getFilters($args); 

        $criteria = $this->modx->newQuery($this->xclass);

        if ($args) {
            $criteria->where($args);
        }
        
        if ($sort) {
            $criteria->sortby($sort,$dir);
        }
    
        if ($debug) {
            $criteria->prepare();
            return $criteria->toSQL();
        }

        // Both array and string input seem to work
        if (!empty($select_cols)) {
            $criteria->select($select_cols);
        }

        if ($obj = $this->modx->getObject($this->xclass,$criteria)) {
            $classname = '\\Assman\\'.$this->xclass;        
            return new $classname($this->modx, $obj); 
        }
        
        return false;
    }

    /**
     * Some strings like "group" will fail if you try to use them as a sort column, e.g.
     *      SELECT * FROM table ORDER BY group ASC LIMIT 20 
     * So this will properly quote a SQL column. 
     *      group --> `group`
     *      `group` --> `group` (unchanged)
     *      tbl.col --> `tbl`.`col`
     */
    public function quoteSort($str) {
        if (!is_scalar($str)) {
            throw new \Exception('quoteSort expects string');
        }
        $parts = explode('.',$str);
        $parts = array_map(function($v){ return '`'.trim($v,'`').'`'; }, $parts);
        return implode('.',$parts);
    }
        
    /**
     * Save the update with a couple UI enhancements:
     *
     * - any validation errors stored in $this->errors
     * - timestamp our updates: some classes will persist this, some will ignore it. meh.
     *
     * @return mixed integer false on fail
     */
    public function save() {
        $this->modelObj->set('timestamp_modified', date('Y-m-d H:i:s'));
        $result = $this->modelObj->save();
        if (!$result) {
            $validator = $this->modelObj->getValidator();
            if ($validator->validate() == false) {
                $messages = $validator->getMessages();
                foreach ($messages as $m) {
                    $this->errors[$m['field']] = $m['message'];
                }
            }
        }
        return $result; 
    }
 
    /**
     *
     *
     */
/*
    public function set($key, $value) {
        return $this->modelObj->set($key,$value);
    }   
*/ 
}
/*EOF*/