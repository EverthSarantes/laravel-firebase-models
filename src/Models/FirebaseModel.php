<?php

namespace Firebase\Models;

use Firebase\Services\FirebaseService;
use Illuminate\Support\Collection;

/**
 * The FirebaseModel class.
 */
abstract class FirebaseModel
{
    /**
     * The firebase service.
     */
    protected $firebaseService;
    /**
     * The name of the collection in firebase.
     */
    protected $collection;

    /**
     * The attributes of the model.
     */
    protected $attributes;

    /**
     * The constructor.
     * @return void
     */
    public function __construct()
    {
        $this->firebaseService = new FirebaseService();
        $this->attributes = [];
    }

    /**
     * Get an attribute from the model.
     * @param string $property
     * @return mixed
     */
    public function __get($property)
    {
        return $this->attributes[$property] ?? null;
    }

    /**
     * Set an attribute in the model.
     * @param string $property
     * @param mixed $value
     * @return void
     */
    public function __set($property, $value)
    {
        $attrs = $this->attributes;
        $attrs[$property] = $value;
        $this->attributes = $attrs; 
    }

    /**
     * Set the attributes of the model.
     * @param array $values
     * @param string|null $id
     * @return void
     */
    public function setAttrs($values, $id = null) : void
    {
        $this->attributes = $values;
        $this->attributes['id'] = $id;
    }

    /**
     * Get all the records from the collection.
     * @return Collection
     */
    public static function all() : Collection
    {
        $array = new Collection();
        $firebaseService = new FirebaseService();
        $results = $firebaseService->fetchAllRecords((new static())->collection);

        foreach ($results as $id => $value) {
            $model = new static();
            $model->setAttrs($value, $id);
            $array[] = $model;
        }

        return $array;
    }

    /**
     * Find a record by its id.
     * @param string $id
     * @return FirebaseModel|null
     */
    public static function find($id) : ?self
    {
        $firebaseService = new FirebaseService();
        $result = $firebaseService->fetchRecord((new static())->collection, $id);
        if($result === [])
        {
            return null;
        }
        $model = new static();
        $model->setAttrs($result, $id);
        return $model;
    }

    /**
     * Find a record by a key value pair.
     * @param string $key
     * @param mixed $value
     * @return Collection
     */
    public static function where($key, $value) : Collection
    {
        $array = new Collection();
        $firebaseService = new FirebaseService();
        $results = $firebaseService->fetchAllRecordsByQuery((new static())->collection, $key, $value);

        foreach ($results as $id => $value) {
            $model = new static();
            $model->setAttrs($value, $id);
            $array[] = $model;
        }

        return $array;
    }

    /**
     * Create a new record.
     * @param array $data
     * @return FirebaseModel
     */
    public static function create($data) : ?self
    {
        $firebaseService = new FirebaseService();
        $id = $firebaseService->createRecord((new static())->collection, $data);
        $model = new static();
        $model->setAttrs($data, $id);
        return $model;
    }

    /**
     * Update a record by its id.
     * @param string $id
     * @param array $data
     * @return bool
     */
    public static function update($id, $data) : bool
    {
        $firebaseService = new FirebaseService();
        $firebaseService->updateRecord((new static())->collection, $id, $data);
        return true;
    }

    /**
     * Delete a record by its id.
     * @param string $id
     * @return bool
     */
    public static function destroy($id) : bool
    {
        $firebaseService = new FirebaseService();
        $firebaseService->deleteRecord((new static())->collection, $id);
        return true;
    }

    /**
     * Save the model.
     * @return bool
     */
    public function save() : bool
    {
        $id = $this->attributes['id'] ?? null;
        if ($id === null) {
            $id = $this->firebaseService->createRecord($this->collection, $this->attributes);
            $this->attributes['id'] = $id;
        } else {
            $this->firebaseService->updateRecord($this->collection, $id, $this->attributes);
        }
        return true;
    }

    /**
     * Delete the model.
     * @return bool
     */
    public function delete() : bool
    {
        $id = $this->attributes['id'];
        $this->firebaseService->deleteRecord($this->collection, $id);
        return true;
    }

    /**
     * Convert the model atrributes to an array.
     * @return array
     */
    public function toArray() : array
    {
        return $this->attributes;
    }

    /**
     * Convert the model atrributes to a json string.
     * @return string
     */
    public function toJson() : string
    {
        return json_encode($this->attributes);
    }

    /**
     * Get the collection name.
     * @param string $model
     * @param string $related
     * @return FirebaseModel
     */
    public function belongsToOne($model, $related) : FirebaseModel
    {
        $relatedId = $this->attributes[$related] ?? null;
        if ($relatedId === null) {
            return null;
        }

        $relatedModel = new $model();

        $result = $this->firebaseService->fetchRecord($relatedModel->collection, $relatedId);
        $relatedModel->setAttrs($result, $relatedId);

        return $relatedModel;
    }

    /**
     * Get the collection name.
     * @param string $model
     * @param string $related
     * @return Collection
     */
    public function hasMany($model, $related) : Collection
    {
        $relatedModel = new $model();

        $model_collection = $relatedModel->collection;
        $relatedId = $this->attributes['id'];

        $array = new Collection();
        $results = $this->firebaseService->fetchAllRecordsByQuery($model_collection, $related, $relatedId);
        
        foreach ($results as $id => $value) {
            $model = new $model();
            $model->setAttrs($value, $id);
            $array[] = $model;
        }
        return $array;
    }
}