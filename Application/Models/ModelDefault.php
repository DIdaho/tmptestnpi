<?php
/**
 * User: david
 * Date: 16/05/14
 * Time: 14:21
 */

namespace Models;

class ModelDefault {
    /**
     * @var \PDO
     */
    protected $pdo;
    /**
     * current table for crud
     * @var string
     */
    protected $_tableName;
    /**
     * all table field and related information
     * @var bool|array
     */
    protected $_tableDescription=false;
    /**
     * table field name current table collection
     * @var bool|array
     */
    protected $_fieldNameList=false;
    /**
     * primary key field name
     * @var bool|string
     */
    protected $_primaryKeyField=false;
    /**
     * table fields collection json formated
     * @var array
     */
    public $jsonFields=array();

    /**
     * tableName are required for basic crud functionality
     * @param \PDO $pdo
     * @param string $tableName
     */
    public function __construct(\PDO $pdo, $tableName=false){
        $this->pdo = $pdo;
        $this->_tableName = $tableName;
    }

    /**
     * if table name defined, return table primary key field name
     * @return boolean|string
     * @throws \Exception
     */
    public function getPrimaryKeyFieldName()
    {
        if( false === $this->_primaryKeyField){
            $this->_loadTableConfigurationData();
        }
        return $this->_primaryKeyField;
    }

    /**
     * if table name defined, return table fields parameter
     * @return boolean|array
     * @throws \Exception
     */
    public function getTableDescription()
    {
        if( false === $this->_tableDescription ){
            $this->_loadTableConfigurationData();
        }
        return $this->_tableDescription;
    }

    /**
     * if table name defined, return collection table field name
     * @return boolean|array
     * @throws \Exception
     */
    public function getFieldNameList()
    {
        if( false === $this->_fieldNameList ){
            $this->_loadTableConfigurationData();
        }
        return $this->_fieldNameList;
    }

    /**
     * check if table parameter can be loaded.
     * if table name defined make table data configuration load attempt
     * if success return true (or throw exception)
     * if table name aren't defined return false
     *
     * @return bool
     * @throws \Exception
     */
    protected function _checkIfTableParameterDefined(){
        if( !empty($this->_tableName) && is_string($this->_tableName) ){
            if( false === $this->_fieldNameList || false === $this->_tableDescription || false === $this->_primaryKeyField ){
                $this->_loadTableConfigurationData();
            }
            return true;
        }
        return false;
    }

    /**
     * @return \PDO
     */
    public function _getPDO(){
        return $this->pdo;
    }

    /**
     * load table data configuration.
     * if table name aren't defined throw exception
     *
     * @throws \Exception
     */
    protected function _loadTableConfigurationData(){
        if( false !== $this->_tableName && !empty($this->_tableName) ){
            //get table description
            $sql = 'EXPLAIN '.$this->_tableName;
            /**@var $result \PDOStatement */
            $result = $this->query($sql);

            //check if table exist
            if( false === $result ){
                throw new \Exception('Inexistant table, unable to load model configuration. Please use pdo object or correct object parameter');
            }else{
                //parse table config data
                $this->_tableDescription = array();
                while( $field = $result->fetch(\PDO::FETCH_ASSOC) ){
                    $this->_tableDescription[ $field['Field'] ] = $field;
                    if( 'PRI' == $field['Key'] ){
                        $this->_primaryKeyField = $field['Field'];
                    }
                }
                $this->_fieldNameList = array_keys($this->_tableDescription);
            }
        }else{
            throw new \Exception('no table name specified on construct, unable to load model configuration. Please use standard pdo object or correct object parameter');
        }
    }

    /**
     * pdo quote method hook!
     * use to clean data before insert, update
     *
     * @param $data
     * @return mixed|string
     */
    public function cleanData($data){
        if ((!is_numeric($data) ) && is_string($data)) {
            $data = str_replace("\\", '', $data);
            $data = $this->_getPDO()->quote($data, \PDO::PARAM_STR);
        }elseif(empty($data) || null === $data){
            $data = "''";
        }
        return $data;
    }

    /**
     * check if $fieldName is in current table field !
     * (but table name must be defined)
     * @param $fieldName
     * @return bool
     */
    protected function _isTableField($fieldName){
        $this->_checkIfTableParameterDefined();
        if( in_array($fieldName, $this->getFieldNameList(), true ) ){
            return true;
        }
        return false;
    }

    /**
     * check if $fieldName is current table primary key
     * (but table name must be defined)
     * @param $fieldName
     * @return bool
     */
    protected function _isTablePrimaryKey($fieldName){
        $this->_checkIfTableParameterDefined();
        if( strcmp($fieldName, $this->getPrimaryKeyFieldName() ) == 0 ){
            return true;
        }
        return false;
    }

    /**
     * transform array from json field storage to json format
     * must be called before insert/update statement if $this->jsonFields used
     *
     * @param $data
     * @return mixed
     */
    protected function _prepareJsonDataForDBStorage($data){
        if( count($this->jsonFields) > 0){
            foreach($this->jsonFields as $field){
                if( isset($data[$field]) && $this->_isTableField($field) ){
                    $data[$field] = json_encode($data[$field]);
                }
            }
        }
//        var_dump($data);
        return $data;
    }

    /**
     * same functionnality as $this->_prepareJsonDataForDBStorage
     * but work on collection
     *
     * @param $data
     * @return mixed
     */
    protected function _prepareDataSetForJsonify($data){
        if( count($this->jsonFields) > 0){
            while ( list($key, $value) = each($data) ){
                $data[$key] = $this->_prepareDataForJsonify($value);
            }
        }
        return $data;
    }

    /**
     * transform json data in query statement in array
     * must be called after select and before json_encode
     *
     * @param $data
     * @return mixed
     */
    protected function _prepareDataForJsonify($data){
        foreach($this->jsonFields as $field){
            if( isset($data[$field]) ){
                $data[$field] = json_decode($data[$field]);
            }
        }
        return $data;
    }

    /**
     * pdo query hook with error management
     *
     * @param $sql string
     * @return \PDOStatement
     * @throws \Exception
     */
    public function query($sql){
        $queryResult = $this->_getPDO()->query($sql);
        if (!$queryResult) {
            $info = get_class($this).' - '.implode(', ', $this->_getPDO()->errorInfo()).', SQL request : "' . $sql;
            throw new \Exception($info);
        } else {
            return $queryResult;
        }
    }

    /**
     * Basic crud functionality
     * Work only if current object table name attribute is define
     */

    /**
     * fetch all record from current table
     * @return array
     * @throws \Exception
     */
    public function fetchAll(){
        $this->_checkIfTableParameterDefined();
        $sql = "SELECT * FROM `".$this->_tableName."`";
        /*@var $statement \PDOStatement*/
        $statement = $this->query($sql);
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $result = $this->_prepareDataSetForJsonify($result);
        return $result;
    }

    /**
     * fetch one record from current table
     *
     * @param $id
     * @return array
     * @throws \Exception
     */
    public function fetchOne($id){
        $this->_checkIfTableParameterDefined();
        $sql = "SELECT * FROM `".$this->_tableName."` WHERE `".$this->getPrimaryKeyFieldName()."` = ".$this->cleanData($id);
        $statement = $this->query($sql);
        $result = $statement->fetch(\PDO::FETCH_ASSOC);
        $result = $this->_prepareDataForJsonify($result);
        return $result;
    }

    /**
     * delete one record from current table
     * @param $id
     */
    public function delete($id){
        $this->_checkIfTableParameterDefined();
        $sql = "DELETE FROM `".$this->_tableName."` WHERE `".$this->getPrimaryKeyFieldName()."` = ".$this->cleanData($id);
        $this->query($sql);
    }

    /**
     * create a new record in current table
     * return primary key value from created record
     *
     * @param $data
     * @return int
     */
    public function create($data){
        $this->_checkIfTableParameterDefined();

        //check if table contains jsonfield
        if( false !== $this->jsonFields && count($this->jsonFields) > 0 ){
            $data = $this->_prepareJsonDataForDBStorage($data);
        }

        $fields = array();
        $fieldValues = array();

        //parse current table fieldname and omit primary key field
        foreach( $data as $fieldName => $value ){
            if( $this->_isTableField($fieldName) && !$this->_isTablePrimaryKey($fieldName) ){
                array_push( $fields, '`'.$fieldName.'`' );
                array_push( $fieldValues, $this->cleanData($value) );
            }
        }
        $sql = 'INSERT INTO `'.$this->_tableName.'` ('. implode( ',', $fields ) .') VALUES ('. implode( ',', $fieldValues ) . ')';
        $this->query($sql);

        //get new record primary key value
        return $this->_getPDO()->lastInsertId($this->getPrimaryKeyFieldName());
    }

    /**
     * INSET statement with on duplicate key update statement
     * return primary key value from created record
     *
     * @param $data
     * @return int
     */
    public function createOnDuplicateUpdate($data){
        $this->_checkIfTableParameterDefined();

        $fields = array();
        $fieldValues = array();
        $duplicateString = '';

        //parse current table fieldname and omit primary key field
        foreach( $data as $fieldName => $value ){
            if( $this->_isTableField($fieldName) /*&& !$this->_isTablePrimaryKey($fieldName)*/ ){
                array_push( $fields, '`'.$fieldName.'`' );
                array_push( $fieldValues, $this->cleanData($value) );
                $duplicateString .= '`'.$fieldName.'` = VALUES(`'.$fieldName.'`),';
            }
        }
        $duplicateString = rtrim($duplicateString, ',');
        $sql = 'INSERT INTO `'.$this->_tableName.
                '` ('. implode( ',', $fields ) .') VALUES ('. implode( ',', $fieldValues ) . ')'.
                ' ON DUPLICATE KEY UPDATE '.$duplicateString;
        $this->query($sql);

        //get new record primary key value
        return $this->_getPDO()->lastInsertId($this->getPrimaryKeyFieldName());
    }

    /**
     * update a current table record
     *
     * @param $data
     * @return \PDOStatement
     * @throws \Exception
     */
    public function update($data){
        $this->_checkIfTableParameterDefined();

        //check if table contains jsonfield
        if( false !== $this->jsonFields && count($this->jsonFields) > 0 ){
            $data = $this->_prepareJsonDataForDBStorage($data);
        }

        $updateValue = '';
        $id = false;
        $sql = '';
        foreach( $data as $fieldName => $value ){
            if( $this->_isTableField($fieldName) && !$this->_isTablePrimaryKey($fieldName) ){
                $updateValue .= $fieldName.' = '.$this->cleanData($value).',';
            }elseif( $this->_isTablePrimaryKey($fieldName) ){
                $id = $this->cleanData($value);
                $whereConstraint = 'WHERE '.$this->getPrimaryKeyFieldName().' = '.$id;
            }
        }
        $updateValue = rtrim($updateValue, ',');

        if( false === $id || !is_numeric($id) || empty($updateValue) ){
            throw new \Exception('No primary key value defined or empty primary key value or no data!');
        }else{
            $sql = 'UPDATE `'.$this->_tableName.'` SET '. $updateValue.' '.$whereConstraint;
        }
        /* @var \PDOStatement*/
        $this->query($sql);
    }

} 