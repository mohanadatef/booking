<?php

namespace Modules\Stadium\Repositories;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Basic\Repositories\BasicRepository;
use Modules\Stadium\Models\Pitch;

class PitchRepository extends BasicRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'id', 'stadium_id', 'name'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Pitch::class;
    }

    /**
     * This function returns the searchable relationship fields of a model.
     *
     * return the value of the property `searchRelationShip` of the `model` object.
     */
    public function getFieldsRelationShipSearchable()
    {
        return $this->model->searchRelationShip;
    }

    /**
     * Return searchable fields
     *
     * return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * This function finds and returns data based on the given request parameters and pagination
     * settings.
     *
     * param Request request  is an instance of the Request class in Laravel, which contains
     * the HTTP request information such as the request method, headers, and input data.
     * param pagination A boolean value that determines whether or not to paginate the results. If set
     * to true, the results will be paginated based on the  parameter. If set to false, all
     * results will be returned without pagination.
     * param perPage The number of records to be displayed per page in case of pagination.
     * param get The "get" parameter is used to specify which columns to retrieve from the database.
     * It is an optional parameter and if not provided, all columns will be retrieved. If provided, it
     * should be an array of column names. For example, if you only want to retrieve the "name" and "
     *
     * return The `findBy` function is returning the result of calling the `all` function with the
     * provided parameters. The `all` function is likely a method defined in a parent class or trait,
     * and its behavior is not clear from this code snippet alone.
     */
    public function findBy(Request $request, $pagination = false, $perPage = 10, $get = '')
    {
        return $this->all($request->all(), ['*'], [], [], [], [], [], $get, null, null, $pagination, $perPage);
    }

    /**
     * This PHP function finds a record by its ID and returns it with its translation in a specific
     * language.
     *
     * param id  is the identifier of the record that you want to retrieve from the database. It is
     * used to specify the primary key value of the record you want to find.
     *
     * return The `findOne` function is returning the result of the `find` function with the specified
     * `` parameter, along with the columns specified in the second parameter `['*']` and the
     * related translation language data specified in the third parameter `['translation.language']`.
     */
    public function findOne($id)
    {
        return $this->find($id, ['*']);
    }

    /**
     * This function saves data to the database and updates or creates translations for the data.
     *
     * param Request request  is an instance of the Request class, which contains the data
     * submitted in an HTTP request. It is used to retrieve input data, files, cookies, and other
     * information from the request. In this function,  is used to retrieve the data submitted
     * in the request and to update or create a
     * param id The  parameter is an optional parameter that represents the ID of the data being
     * updated. If it is provided, the function will update the existing data with the given ID. If it
     * is not provided, the function will create a new data entry.
     *
     * return the result of a database transaction. It first modifies the request data by setting the
     * "key" field to its lowercase version. Then, it checks if an ID is provided. If an ID is
     * provided, it retrieves the data with that ID using the "findOne" method. Otherwise, it creates a
     * new data record using the "create" method. After that, it updates or
     */
    public function save(Request $request, $id = null)
    {
        return DB::transaction(function () use ($request, $id) {
            if ($id) {
                $data =  $this->update($request->all(), $id);
            } else {
                $data = $this->create($request->all());
            }
            return $this->find($data->id);
        });
    }

}
