<?php
/**
 * User: david
 * Date: 16/05/14
 * Time: 14:21
 */

namespace Models;


class ModelDefault {

    protected $pdo;
    protected $_tableName;
    protected $_tableDescription=false;
    protected $_fieldNameList=false;
    protected $_primaryKeyField=false;

    public function __construct($pdo, $tableName=false){
        $this->pdo = $pdo;
        $this->_tableName = $tableName;

        //check if table name define
//        if( !empty($this->_tableName) ){
//            var_dump($this->getTableDescription());
//        }
    }

    /**
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
    protected function _getPDO(){
        return $this->pdo;
    }

    /**
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

    public function cleanData($data){
        if ((!is_numeric($data) ) && is_string($data)) {
            $data = str_replace("\\", '', $data);
            $data = $this->_getPDO()->quote($data, \PDO::PARAM_STR);
        }
        return $data;
    }

    protected function _isTableField($fieldName){
        $this->_checkIfTableParameterDefined();
        if( in_array($fieldName, $this->getFieldNameList() ) ){
            return true;
        }
        return false;
    }

    protected function _isTablePrimaryKey($fieldName){
        $this->_checkIfTableParameterDefined();
        if( strcmp($fieldName, $this->getPrimaryKeyFieldName() ) == 0 ){
            return true;
        }
        return false;
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
     * @return \PDOStatement
     * @throws \Exception
     */
    public function fetchAll(){
        $this->_checkIfTableParameterDefined();
        $sql = "SELECT * FROM `".$this->_tableName."`";
        return $this->query($sql);
    }

    /**
     * @param $id
     * @return \PDOStatement
     * @throws \Exception
     */
    public function fetchOne($id){
        $this->_checkIfTableParameterDefined();
        $sql = "SELECT * FROM `".$this->_tableName."` WHERE `".$this->getPrimaryKeyFieldName()."` = ".$this->cleanData($id);
        $result = $this->query($sql);
        return $result;

    }

    public function delete($id){
        $this->_checkIfTableParameterDefined();
        $sql = "DELETE FROM `".$this->_tableName."` WHERE `".$this->getPrimaryKeyFieldName()."` = ".$this->cleanData($id);
        return $this->query($sql);
    }

    public function create($data){
        $this->_checkIfTableParameterDefined();
        print_r($data);
        $fields = array();
        $fieldValues = array();
        foreach( $data as $fieldName => $value ){
            if( $this->_isTableField($fieldName) && !$this->_isTablePrimaryKey($fieldName) ){
                array_push( $fields, '`'.$fieldName.'`' );
                array_push( $fieldValues, $this->cleanData($value) );
            }
        }
        $sql = 'INSERT INTO `'.$this->_tableName.'` ('. implode( ',', $fields ) .') VALUES ('. implode( ',', $fieldValues ) . ')';
        $this->query($sql);
        return $this->_getPDO()->lastInsertId($this->getPrimaryKeyFieldName());
    }

    public function createOnDuplicateUpdate($data){
        $this->_checkIfTableParameterDefined();
        print_r($data);
        $fields = array();
        $fieldValues = array();
        $duplicateString = '';
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
//        return $sql;
        $this->query($sql);
        return $this->_getPDO()->lastInsertId($this->getPrimaryKeyFieldName());
    }

    public function update($data){
        $this->_checkIfTableParameterDefined();
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
        return $this->query($sql);
    }


//UPDATE `apple_npi`.`npi`
//SET
//`_pk_npi` = {_pk_npi: },
//`npi_label` = {npi_label: },
//`npi_product_level1` = {npi_product_level1: }
//WHERE <{where_condition}>;



} 