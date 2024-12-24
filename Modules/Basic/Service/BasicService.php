<?php

namespace Modules\Basic\Service;

use Illuminate\Http\Request;

class BasicService
{
    /**
     * This PHP function retrieves data from a repository based on a given ID.
     *
     * param id The parameter "id" is a variable that represents the unique identifier of the data
     * that needs to be retrieved from the repository. The "show" function takes this parameter and
     * uses it to find and return the data associated with that particular ID.
     *
     * return the data of a single record with the given ID from the repository.
     */
    public function show($id)
    {
        $data = $this->repo->findOne($id);
        if ($data) {
            return $data;
        }
        return false;
    }

    /**
     * This function saves data from a request and returns true if successful, false otherwise.
     *
     * param Request request  is an instance of the Request class which contains the data sent
     * by the client in the HTTP request. It can contain data from the request body, query parameters,
     * headers, cookies, and more. In this code snippet, the  parameter is passed to the save()
     * method of the
     *
     * return a boolean value. If the data is successfully saved, it will return true, otherwise it
     * will return false.
     */
    public function store(Request $request)
    {
        $data = $this->repo->save($request);
        if ($data) {
            return $data;
        }
        return false;
    }

    /**
     * This function updates data using a repository and returns the updated data or false.
     *
     * param Request request  is an instance of the Request class in Laravel, which contains
     * all the data that was sent in the HTTP request. It can be used to retrieve input data, files,
     * headers, cookies, and other information related to the request. In this case, it is being used to
     * pass data to the
     * param id  is a parameter that represents the unique identifier of the resource being updated.
     * It is used to identify the specific resource that needs to be updated in the database.
     *
     * return If the `` variable is truthy, it will be returned. Otherwise, `false` will be
     * returned.
     */
    public function update(Request $request, $id)
    {
        $data = $this->repo->save($request, $id);
        if ($data) {
            return $data;
        }
        return false;
    }

    /**
     * It deletes the user's account
     *
     * param Request request The request object
     *
     * return The data is being returned.
     */
    public function destroy(Request $request, $id)
    {
        $data = $this->repo->destroy($request, $id);
        if ($data) {
            return $data;
        }
        return false;
    }

    /**
     * This PHP function updates a value in a repository based on an ID and a key.
     *
     * param id The ID of the record that needs to be updated in the database.
     * param key The key parameter is a string that represents the name of the column in the database
     * table that needs to be updated.
     *
     * return the result of calling the `updateValue` method of the `` object with the `` and
     * `` parameters. The specific data being returned depends on the implementation of the
     * `updateValue` method.
     */
    public function changeStatus($id, $key)
    {
        $data = $this->repo->updateValue($id, $key);
        return $data;
    }

    /**
     * This function toggles the active status of an object and returns a boolean value indicating
     * whether the operation was successful or not.
     *
     * return bool A boolean value is being returned.
     */
    public function toggleActive(): bool
    {
        return $this->repo->toggleActive();
    }
}
