<?php

namespace sparkorama\microrm\Models;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

abstract class Data_Model
{

    protected $databaseConnection;
    protected $databaseTable;
    
    protected $idFieldName = 'id';
    protected $createdFieldName = 'created_at';
    protected $updatedFieldName = 'updated_at';
    
    protected $defaultFields = '*';

    protected function baseQuery() {
        $query = \DB::connection($this->databaseConnection)->table($this->databaseTable);
        $query->select(\DB::raw($this->defaultFields));
        return $query;
    }
        
    public function create($data) {
        //Set the created_at field
        if ( ! array_key_exists($this->createdFieldName, $data) or empty($data[$this->createdFieldName])) {
            $data[$this->createdFieldName] = Carbon::now();
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
            throw new Exception($error_message);
        }
    }
    
    public function update($id, $data)
    {
        //Set the updated_at field
        $data[$this->updatedFieldName] = Carbon::now();
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
            throw new Exception($error_message);
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
            throw new Exception($error_message);
        }
    }
        
    public function getByID($id) {
        try {
            $query = $this->baseQuery();
            return $query->where($this->idFieldName, '=', $id)->first();
        } catch (\PDOException $e) {
            $error_message = get_class($this).'::'.__FUNCTION__.' ERROR - '.$e->getMessage();
            throw new Exception($error_message);
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
            throw new Exception($error_message);
        }
    }
    
}