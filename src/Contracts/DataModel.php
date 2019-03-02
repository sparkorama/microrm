<?php

namespace sparkorama\microrm\Contracts;

interface DataModel
{
    /**
     * Create a new database record
     *
     * @throws \sparkorama\microrm\Exceptions\CreateQueryError
     *
     * @return variant (ID field)
     */
    public function create();

    /**
     * Update an existing database record
     *
     * @throws \sparkorama\microrm\Exceptions\UpdateQueryError
     *
     * @return boolean
     */
    public function update();

    /**
     * Soft delete a database record
     *
     * @throws \sparkorama\microrm\Exceptions\UpdateQueryError
     *
     * @return boolean
     */
    public function delete();

    /**
     * Hard delete a database record
     *
     * @throws \sparkorama\microrm\Exceptions\DeleteQueryError
     *
     * @return boolean
     */
    public function purge();
    
    /**
     * Get all records in the database table
     *
     * @throws \sparkorama\microrm\Exceptions\SelectQueryError
     *
     * @return Illuminate\Support\Collection
     */
    public function all();

    /**
     * Get the record with the specified ID
     *
     * @param var $id
     *
     * @throws \sparkorama\microrm\Exceptions\SelectQueryError
     *
     * @return stdClass
     */
    public function getByID($id);

    /**
     * Get records containing a field with a specified value.
     *
     * @param string $field
     * @param string $value
     *
     * @throws \sparkorama\microrm\Exceptions\SelectQueryError
     *
     * @return Illuminate\Support\Collection
     */
    public function getByField($field, $value);
}