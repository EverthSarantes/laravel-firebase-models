<?php

namespace Firebase\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Database;

/**
 * The FirebaseService class.
 */
class FirebaseService
{
    /**
     * The firebase database.
     */
    protected $database;

    /**
     * The constructor.
     * @return void
     */
    public function __construct()
    {
        $factory = (new Factory)->withServiceAccount(config('firebase.firebase_credentials'))
        ->withDatabaseUri(config('firebase.firebase_database_url'));
        $this->database = $factory->createDatabase();
    }

    /**
     * Fetch a record from the database.
     * @param string $collection
     * @param string $id
     * @return array
     */
    public function fetchRecord($collection, $id) : array
    {
        return $this->database->getReference("$collection/$id")->getValue() ?? [];
    }

    /**
     * Fetch all records from the database.
     * @param string $collection
     * @return array
     */
    public function fetchAllRecords($collection) : array
    {
        return $this->database->getReference($collection)->getValue() ?? [];
    }

    /**
     * Fetch all records from the database by query.
     * @param string $collection
     * @param string $key
     * @param string $value
     * @return array
     */
    public function fetchAllRecordsByQuery($collection, $key, $value) : array
    {
        return $this->database->getReference($collection)->orderByChild($key)->equalTo($value)->getValue();
    }

    /**
     * Create a record in the database.
     * @param string $collection
     * @param array $data
     * @return string
     */
    public function createRecord($collection, $data) : string
    {
        return $this->database->getReference($collection)->push($data);
    }

    /**
     * Update a record in the database.
     * @param string $collection
     * @param string $id
     * @param array $data
     * @return void
     */
    public function updateRecord($collection, $id, $data) : void
    {
        $this->database->getReference("$collection/$id")->update($data);
    }

    /**
     * Delete a record from the database.
     * @param string $collection
     * @param string $id
     * @return void
     */
    public function deleteRecord($collection, $id) : void
    {
        $this->database->getReference("$collection/$id")->remove();
    }
}