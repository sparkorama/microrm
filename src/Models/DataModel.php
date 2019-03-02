<?php

namespace sparkorama\microrm\Models;

use sparkorama\microrm\Exceptions\MicrormCreateQueryError;
use sparkorama\microrm\Exceptions\MicrormUpdateQueryError;
use sparkorama\microrm\Exceptions\MicrormDeleteQueryError;
use sparkorama\microrm\Exceptions\MicrormSelectQueryError;
use sparkorama\microrm\Contracts\DataModel as DataModelContract;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

abstract class Data_Model implements DataModelContract
{

    protected $databaseConnection;
    protected $databaseTable;
    
    protected $idFieldName = 'id';
    protected $deletedFieldName = 'deleted';
    protected $createdAtFieldName = 'created_at';
    protected $updatedAtFieldName = 'updated_at';
    protected $deletedAtFieldName = 'deleted_at';
    
    protected $defaultFields = '*';

    protected function baseQuery() {
        $query = \DB::connection($this->databaseConnection)->table($this->databaseTable);
        $query->select(\DB::raw($this->defaultFields));
        return $query;
    }
        
    public function create($data) {
        //Set the created_at field
        if ( ! array_key_exists($this->createdAtFieldName, $data) or empty($data[$this->createdAtFieldName])) {
            $data[$this->createdAtFieldName] = Carbon::now();
        }
        //Unset any fields not in the schema
        $columns = \DB::connection($this->databaseConnection)->getSchemaBuilder()->getColumnListing($this->databaseTable);
        foreach (array_keys($data) as $key) {
            if ( ! in_array($key, $columns)) {
                unset($data[$key]);
            }
        }
        //Try to save it
        try {
            return DB::connection($this->databaseConnection)->table($this->databaseTable)->insertGetId($data);
        } catch (\PDOException $e) {
            $error_message = get_class($this).'::'.__FUNCTION__.' ERROR - '.$e->getMessage();
            throw new MicrormCreateQueryError($error_message);
        }
    }
    
    public function update($id, $data)
    {
        //Set the updated_at field
        $data[$this->updatedAtFieldName] = Carbon::now();
        //Unset any fields not in the schema
        $columns = \DB::connection($this->databaseConnection)->getSchemaBuilder()->getColumnListing($this->databaseTable);
        foreach (array_keys($data) as $key) {
            if ( ! in_array($key, $columns)) {
                unset($data[$key]);
            }
        }
        try {
            $row_count = DB::connection($this->databaseConnection)->table($this->databaseTable)->where($this->idFieldName, '=', $id)->update($data);
            return ($row_count >= 0);
        } catch (\PDOException $e) {
            $error_message = get_class($this).'::'.__FUNCTION__.' ERROR - '.$e->getMessage();
            throw new MicrormUpdateQueryError($error_message);
        }
    }
    
    public function delete($id)
    {
        //Set the flag & timestamp
        $data[$this->deletedFieldName] = true;
        $data[$this->deletedAtFieldName] = Carbon::now();
        //Unset any fields not in the schema
        $columns = \DB::connection($this->databaseConnection)->getSchemaBuilder()->getColumnListing($this->databaseTable);
        foreach (array_keys($data) as $key) {
            if ( ! in_array($key, $columns)) {
                unset($data[$key]);
            }
        }
        try {
            $row_count = DB::connection($this->databaseConnection)->table($this->databaseTable)->where($this->idFieldName, '=', $id)->update($data);
            return ($row_count >= 0);
        } catch (\PDOException $e) {
            $error_message = get_class($this).'::'.__FUNCTION__.' ERROR - '.$e->getMessage();
            throw new MicrormUpdateQueryError($error_message);
        }
    }
    
    public function purge($id)
    {
        try {
            $row_count = DB::connection($this->databaseConnection)->table($this->databaseTable)->where($this->idFieldName, '=', $id)->delete();
            return ($row_count >= 0);
        } catch (\PDOException $e) {
            $error_message = get_class($this).'::'.__FUNCTION__.' ERROR - '.$e->getMessage();
            throw new MicrormDeleteQueryError($error_message);
        }
    }
    
    public function all($order_by = null) {
        try {
            $query = $this->baseQuery();
            if (is_array($order_by)) {
                foreach($order_by as $clause) {
                    $query->orderBy($clause);
                }
            }
            return $query->get();
        } catch (\PDOException $e) {
            $error_message = get_class($this).'::'.__FUNCTION__.' ERROR - '.$e->getMessage();
            throw new MicrormSelectQueryError($error_message);
        }
    }
        
    public function getByID($id) {
        try {
            $query = $this->baseQuery();
            return $query->where($this->idFieldName, '=', $id)->first();
        } catch (\PDOException $e) {
            $error_message = get_class($this).'::'.__FUNCTION__.' ERROR - '.$e->getMessage();
            throw new MicrormSelectQueryError($error_message);
        }
    }

    public function getByField($field, $value) {
        if (empty($field)) {
            return null;
        }
        try {
            $query = $this->baseQuery();
            if (is_null($value)) {
                return $query->whereNull($field)->get();
            }
            else {
                return $query->where(\DB::Raw($field), '=', $value)->get();
            }
        } catch (\PDOException $e) {
            $error_message = get_class($this).'::'.__FUNCTION__.' ERROR - '.$e->getMessage();
            throw new MicrormSelectQueryError($error_message);
        }
    }
    
}