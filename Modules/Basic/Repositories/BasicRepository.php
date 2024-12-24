<?php

namespace Modules\Basic\Repositories;

use Illuminate\Container\Container as Application;
use Illuminate\Database\Eloquent\Model;

abstract class BasicRepository
{

    /**
     * @var Model
     */
    protected $model;

    /**
     * Configure the Model
     *
     * return string
     */
    abstract public function model();

    /**
     * @var Application
     */
    protected $app;

    /**
     * param Application $app
     *
     * @throws \Exception
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->makeModel();
    }

    /**
     * Make Model instance
     *
     * return Model
     * @throws \Exception
     *
     */
    public function makeModel()
    {
        $model = $this->app->make($this->model());
        if(!$model instanceof Model)
        {
            throw new \Exception("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }
        return $this->model = $model;
    }

    /* The above code is defining an abstract class with two abstract methods `getFieldsSearchable()`
    and `getFieldsRelationShipSearchable()`. These methods are not implemented in the abstract class
    and must be implemented in any concrete class that extends this abstract class. The purpose of
    these methods is to define the fields that can be searched and the relationships that can be
    searched in a database query. */
    abstract public function getFieldsSearchable();

    abstract public function getFieldsRelationShipSearchable();


    /**
     * This is a PHP function that retrieves data from a database table with various options for
     * filtering, sorting, and pagination.
     *
     * param search an array of search criteria to filter the results
     * param column An array of columns to select from the table. If empty or set to ['*'], all
     * columns will be selected.
     * param withRelations An array of relationships to eager load with the query. For example, if a
     * model has a "comments" relationship, passing ['comments'] to this parameter will load all
     * comments associated with the model in a single query.
     * param recursiveRel An array of relationships to be loaded recursively. For example, if a model
     * has a relationship with another model, and that model has a relationship with a third model, and
     * we want to load all three models, we can use recursiveRel to specify the relationships to be
     * loaded recursively.
     * param moreConditionForFirstLevel An array of additional conditions to be applied to the first
     * level of the query. These conditions will be added using the `proccessQuery` method. If there is
     * only one condition, it can be passed as a key-value pair. If there are multiple conditions, they
     * should be passed as an array
     * param pluck An array containing the column to retrieve as the first element and the column to
     * use as the key as the second element. This will return a collection of key-value pairs.
     * param orderBy An array containing the column and order to use for sorting the results. The
     * 'column' key should contain the name of the column to sort by, and the 'order' key should
     * contain either 'asc' or 'desc' to specify the sort order.
     * param get The "get" parameter specifies what type of result should be returned from the query.
     * It can have the following values:
     * param skip The number of records to skip before starting to retrieve data.
     * param limit The maximum number of records to retrieve from the database.
     * param pagination A boolean value indicating whether or not to enable pagination for the query
     * results.
     * param perPage The number of items to be displayed per page when using pagination.
     * param latest A string representing the column to order the results by in descending order. This
     * is typically used to retrieve the latest records based on a timestamp column.
     * param distinct The "distinct" parameter is used to retrieve only unique values from a column in
     * the database table. It is used in conjunction with the "select" method to specify the column for
     * which unique values should be retrieved. If the "distinct" parameter is set to true, the query
     * will return only unique
     * param groupBy The "groupBy" parameter is used to group the results of the query by a specific
     * column or columns. It is used to aggregate data and perform calculations on groups of data. For
     * example, if you have a table of sales data, you could group the data by month to see the total
     * sales for
     *
     * return the result of the query based on the provided parameters. The returned value can be an
     * array of objects, a single object, a count of the query result, or a paginated result.
     */
    public function all(
        $search = [],
        $column = ['*'],
        $withRelations = [],
        $recursiveRel = [],
        $moreConditionForFirstLevel = [],
        $pluck = [],
        $orderBy = [],
        $get = '',
        $skip = null,
        $limit = null,
        $pagination = false,
        $perPage = 0,
        $latest = '',
        $distinct = null,
        $groupBy = null,
        $isDatatable = false
    )
    {
        //todo change
        $query = $this->allQuery($search, $skip, $limit, $latest, $distinct, $groupBy);
        if($recursiveRel != [])
        {
            $query = $this->addRecursiveRelationsToQuery($query, $recursiveRel);
        }
        if($moreConditionForFirstLevel)
        {
            if(count($moreConditionForFirstLevel) == 1)
            {
                $query = self::proccessQuery($query, $moreConditionForFirstLevel);
            }else
            {
                foreach($moreConditionForFirstLevel as $key => $value)
                {
                    $query = self::proccessQuery($query, [$key => $value]);
                }
            }
        }
        if(!empty($orderBy))
        {
            if(isset($orderBy['multiple']) && $orderBy['multiple'] = 1)
            {
                foreach($orderBy['orderBy'] as $order)
                {
                    $query = $query->orderBy($order['column'], $order['order']);
                }
            }else
            {
                $query = $query->orderBy($orderBy['column'], $orderBy['order']);
            }
        }
        if(!empty($withRelations))
        {
            $query = $this->with($query, $withRelations);
        }
        if(!empty($column) && $column != ['*'])
        {
            $query = $query->select($column);
        }
        if($isDatatable)
        {
            $params = $search;
            $page = $params['page'] ?? 1;
            $dtPerPage = $params['per_page'] ?? 10;
            $totalRecords = $query->count();
            $filteredRecords = $query->count();
            $data = $query->skip(($page - 1) * $dtPerPage)->take($dtPerPage)->get();
            $response = [
                "recordsTotal" => $totalRecords,
                "recordsFiltered" => $filteredRecords,
                'data' => $data
            ];
            return $response;
        }
        return $this->returnQuery($pluck, $query, $get, $pagination, $perPage);
    }

    /**
     * This function finds a record by ID with optional columns and related models.
     *
     * param id The ID of the record to be retrieved from the database.
     * param column The columns to retrieve from the database table. By default, it retrieves all
     * columns using the wildcard symbol '*'. However, you can specify specific columns to retrieve by
     * passing an array of column names as the second parameter.
     * param withRelations withRelations is an optional parameter that allows the user to specify any
     * related models that should be eager loaded along with the main model being queried. This can
     * help to reduce the number of database queries needed to retrieve related data, improving
     * performance. The parameter should be an array of relationship names defined on the model
     *
     * return the result of a query to find a record in the database table associated with the model,
     * based on the given ID. The function also allows for specifying which columns to retrieve and
     * which related models to eager load.
     */
    public function find($id, $column = ['*'], $withRelations = [])
    {
        $query = $this->model->newQuery();
        if(!empty($withRelations))
        {
            $query = $this->with($query, $withRelations);
        }
        return $query->find($id, $column);
    }

    /**
     * This function adds eager loading of specified relationships to a given query in PHP.
     *
     * param query This parameter is likely an instance of a query builder or an Eloquent model. It
     * represents the database query that you want to modify by eager loading related data.
     * param withRelations withRelations is an array of relationships that should be eager loaded with
     * the query. Eager loading is a technique in Laravel that allows you to load related models in a
     * single query, instead of making separate queries for each relationship. This can help to improve
     * the performance of your application by reducing the number of
     *
     * return the result of calling the `with` method on the `` object with the ``
     * parameter.
     */
    public function with($query, $withRelations)
    {
        return $query->with($withRelations);
    }

    /**
     * This function creates a new record in the database using the provided request data.
     *
     * param request  is a variable that contains the data sent in the HTTP request. It could
     * be an array or an object that contains the values of the fields submitted in a form or in the
     * body of a request. This function is likely part of a controller in a web application that
     * handles the creation of a new
     *
     * return The `create` method is returning the result of calling the `create` method on the model
     * property with the `` parameter passed in. The specific return value depends on the
     * implementation of the `create` method in the model, but it is likely to be a new instance of the
     * model with the attributes set according to the `` data.
     */
    public function create($request)
    {
        return $this->model->create($request);
    }

    /**
     * This function updates a record in the database and returns the updated record.
     *
     * param request  is a variable that contains the data that is being sent in the HTTP
     * request. It could be data from a form submission or an API call. In this context, it is being
     * used to update the data in the database.
     * param id The  parameter is an optional parameter that represents the unique identifier of
     * the data that needs to be updated. If it is not provided, the function will not be able to find
     * the data to update.
     *
     * return The updated data with the specified ID is being returned.
     */
    public function update($request, $id = null)
    {
        $data = $this->find($id);
        $data->update($request);
        return $this->find($id);
    }

    /**
     * This is a PHP function that builds a query based on search parameters, skip, limit, latest,
     * distinct, and groupBy.
     *
     * param search An array of key-value pairs to search for in the database. The keys represent the
     * fields to search in, and the values represent the search terms.
     * param skip The number of records to skip before starting to return results. It is used for
     * pagination purposes.
     * param limit Limits the number of results returned by the query.
     * param latest A string parameter that, when set to 'latest', orders the query results by the
     * latest created/updated records.
     * param distinct A boolean parameter that determines whether to return only distinct results or
     * not. If set to true, the query will only return unique results. If set to false or null, the
     * query will return all results, including duplicates.
     * param groupBy The "groupBy" parameter is used to group the results of the query by a specific
     * column or set of columns. This is useful when you want to perform aggregate functions on the
     * grouped data, such as counting or summing. The parameter takes a string or an array of strings
     * representing the column(s)
     *
     * return a query builder instance with various conditions applied based on the parameters passed
     * to the function.
     */
    public function allQuery($search = [], $skip = null, $limit = null, $latest = '', $distinct = null, $groupBy = null)
    {
        $query = $this->model->newQuery();
        if(count($search) > 0)
        {
            foreach($search as $key => $value)
            {
                if((!empty($value) && $value != null && $value != "null") || $value === 0 || $value === "0")
                {
                    if(in_array($key, $this->getFieldsSearchable()))
                    {
                        $this->getFieldsSearchableQuery($value, $key, $query);
                    }
                    if(!empty($this->translationKey()) && in_array($key, $this->translationKey()))
                    {
                        $this->translationKeyQuery($value, $key, $query);
                    }
                    if(!empty($this->getFieldsRelationShipSearchable()) && array_key_exists($key,
                            $this->getFieldsRelationShipSearchable()))
                    {
                        $this->getFieldsRelationShipSearchableQuery($value, $key, $query);
                    }
                }
            }
        }
        return $this->returnAllQuery($skip, $query, $limit, $latest, $distinct, $groupBy);
    }

    /**
     * This function adds recursive relations to a query in PHP.
     *
     * param query The query object that will be modified with the recursive relations.
     * param withRecursive An array that contains the recursive relations to be added to the query.
     * Each key in the array represents the name of the relation, and the value is an array that
     * contains the details of the relation, such as the type of relation (normal, whereHas,
     * whereDoesntHave, etc.) and
     *
     * return the modified query with added recursive relations based on the input parameters.
     */
    public function addRecursiveRelationsToQuery($query, $withRecursive)
    {
        foreach($withRecursive as $key => $value)
        {
            if(!isset($value['type']) || $value['type'] == 'normal')
            {
                return $this->typeNormal($key, $query, $value);
            }
            if($value['type'] == 'whereHas')
            {
                return $this->typeWhereHas($key, $query, $value);
            }
            if(in_array($value['type'], ['whereDoesntHave', 'orWhereDoesntHave']))
            {
                return $this->typeWhereDoesntHaveOrWhereDoesntHave($key, $query, $value);
            }
            if($value['type'] == 'orWhereHas')
            {
                return $this->typeOrWhereHas($key, $query, $value);
            }
            if($value['type'] == 'whereCustom')
            {
                $valuee = $value['value'];
              return  $query->where(function($query) use ($valuee)
                {
                    foreach($valuee as  $value)
                    {
                        $this->addRecursiveRelationsToQuery($query, $value);
                    }
                });
            }
            if(in_array($value['type'], ['whereHasMorph', 'orWhereHasMorph']))
            {
                return $this->typeWhereHasMorph($key, $query, $value);
            }
        }
    }

    /**
     * This function processes various types of queries and values to construct a query using Laravel's
     * Eloquent ORM.
     *
     * param q a query builder instance
     * param values An array containing various query parameters such as where clauses, whereBetween
     * clauses, orWhere clauses, etc.
     *
     * return the processed query after applying various conditions and filters based on the values
     * passed as parameters.
     */
    public function proccessQuery($q, $values)
    {
        if(isset($values['where']) && count($values['where']) > 0)
        {
            foreach($values['where'] as $key => $value)
            {
                $q = $this->searchConfigQuery($key, $q, $value);
            }
        }
        if(isset($values['whereBetween']) && count($values['whereBetween']) > 0)
        {
            foreach($values['whereBetween'] as $key => $value)
            {
                $q->whereBetween($key, [$value[0], $value[1]]);
            }
        }
        if(isset($values['orWhereBetween']) && count($values['orWhereBetween']) > 0)
        {
            foreach($values['orWhereBetween'] as $key => $value)
            {
                $q->orwhereBetween($key, [$value[0], $value[1]]);
            }
        }
        if(isset($values['whereQuery']) && count($values['whereQuery']) > 0)
        {
            foreach($values['whereQuery'] as $value)
            {
                $num = 0;
                $q->where(function($query) use ($num, $value)
                {
                    foreach($value as $k => $val)
                    {
                        if($num == 0)
                        {
                            $query = $this->proccessWhere($query, $k, $val);
                        }else
                        {
                            $query = $this->proccessOrWhere($query, $k, $val);
                        }
                        $num++;
                    }
                });
            }
        }
        if(isset($values['whereCustom']) && count($values['whereCustom']) > 0)
        {
            $num = 0;
            $q->where(function($query) use ($num, $values)
            {
                foreach($values['whereCustom'] as $ke => $value)
                {
                    foreach($value as $valC)
                    {
                        if(in_array($ke,
                            ['orWhereDoesntHave', 'whereDoesntHave', 'whereHasMorph', 'orWhereHasMorph', 'whereHas', 'orWhereHas']))
                        {
                            $query = self::addRecursiveRelationsToQuery($query, $valC);
                        }else
                            foreach($valC as $k => $val)
                            {
                                if($ke == 'where')
                                {
                                    if($num == 0)
                                        $query = $this->proccessWhere($query, $k, $val);
                                    else
                                        $query = $this->proccessOrWhere($query, $k, $val);
                                }elseif($ke == 'orWhereNull')
                                {
                                    $query = $this->proccessOrWhereNull($query, $val);
                                }
                                elseif($ke == 'whereNull')
                                {
                                    $query = $this->proccessWhereNull($query, $val);
                                }elseif($ke == 'whereNotNull')
                                {
                                    $query = $this->proccessWhereNotNull($query, $val);
                                }elseif($ke == 'orWhereNotNull')
                                {
                                    $query = $this->proccessOrWhereNotNull($query, $val);
                                }elseif($ke == 'orWhere')
                                {
                                    $query = $this->proccessOrWhere($query, $k, $val);
                                }elseif($ke == 'orWhereIn')
                                {
                                    $query = $this->proccessOrWhereIn($query, $k, $val);
                                }elseif($ke == 'whereIn')
                                {
                                    $query = $this->proccessWhereIn($query, $k, $val);
                                }
                                $num++;
                            }
                    }
                }
            });
        }
        if(isset($values['orWhereNotNull']) && count($values['orWhereNotNull']) > 0)
        {
            $q = $this->whereNotNull($q, $values['orWhereNotNull']);
        }
        if(isset($values['whereNotNull']) && count($values['whereNotNull']) > 0)
        {
            $q = $this->whereNotNull($q, $values['whereNotNull']);
        }
        if(isset($values['whereNull']) && count($values['whereNull']) > 0)
        {
            $q = $this->whereNull($q, $values['whereNull']);
        }
        if(isset($values['orWhereNull']) && count($values['orWhereNull']) > 0)
        {
            $num = 0;
            foreach($values['orWhereNull'] as $column)
            {
                if($num == 0)
                    $q->whereNull($column);
                else
                    $q->orWhereNull($column);
                $num++;
            }
        }
        if(isset($values['orWherePivot']) && count($values['orWherePivot']) > 0)
        {
            foreach($values['orWherePivot'] as $where => $value)
            {
                $q->orWhere($where, $value);
            }
        }
        if(isset($values['whereIn']) && count($values['whereIn']) > 0)
        {
            foreach($values['whereIn'] as $where => $value)
            {
                $q->whereIn($where, $value);
            }
        }
        if(isset($values['whereNotIn']) && count($values['whereNotIn']) > 0)
        {
            foreach($values['whereNotIn'] as $where => $value)
            {
                $q->whereNotIn($where, $value);
            }
        }
        if(isset($values['orWhere']) && count($values['orWhere']) > 0)
        {
            $num = 0;
            foreach($values['orWhere'] as $where => $value)
            {
                $q = $this->proccessOrWhere($q, $where, $value);
            }
        }
        if(isset($values['doesntHave']) && count($values['doesntHave']) > 0)
        {
            foreach($values['doesntHave'] as $val)
            {
                $q->doesntHave($val);
            }
        }
        return $q;
    }

    /**
     * This function adds an "orWhereNull" clause to a given query in PHP.
     *
     * param query This parameter is likely an instance of a query builder class in a PHP framework
     * such as Laravel. It represents a database query that is being constructed and will eventually be
     * executed to retrieve data from a database.
     * param val The name of the column in the database table that should be checked for null values.
     *
     * return a query with an "orWhereNull" clause using the value passed as a parameter.
     */
    public function proccessOrWhereNull($query, $val)
    {
        return $query->orWhereNull($val);
    }

    public function proccessOrWhereNotNull($query, $val)
    {
        return $query->orWhereNotNull($val);
    }
    public function proccessWhereNull($query, $val)
    {
        return $query->whereNull($val);
    }
    public function proccessWhereNotNull($query, $val)
    {
        return $query->whereNotNull($val);
    }
    /**
     * This function processes an "orWhere" query in PHP, allowing for multiple conditions to be passed
     * in an array.
     *
     * param q  is a query builder instance, which is used to build and execute database queries in
     * PHP.
     * param key The column name or field name in the database table.
     * param value The value to be used in the where clause. It can be a single value or an array with
     * two values (operator and value) for comparison.
     *
     * return the modified query builder object `` after adding an `orWhere` clause based on the
     * provided `` and ``.
     */
    public function proccessOrWhere($q, $key, $value)
    {
        if(is_array($value) && count($value) == 2)
        {
            return $q->orWhere($key, $value[0], $value[1]);
        }
        return $q->orWhere($key, $value);
    }

    /**
     * This function adds a where clause to a query builder object to filter out rows where specified
     * columns are not null.
     *
     * param q  is a query builder instance that is used to build and execute database queries. It
     * is likely an instance of a class that extends Laravel's Query Builder.
     * param values  is an array of column names that the query should check for not being
     * null. The function loops through each column name in the array and adds a "whereNotNull" or
     * "orWhereNotNull" clause to the query depending on whether it's the first column or not.
     *
     * return the query builder object `` after adding `whereNotNull` or `orWhereNotNull` conditions
     * for each column in the `` array.
     */
    public function whereNotNull($q, $values)
    {
        $num = 0;
        foreach($values as $column)
        {
            if($num == 0)
                $q->whereNotNull($column);
            else
                $q->orWhereNotNull($column);
            $num++;
        }
        return $q;
    }

    /**
     * This function adds a where clause to a query builder to filter results where specified columns
     * are null.
     *
     * param q  is a query builder instance that is used to build and execute database queries. It
     * is typically an instance of the Illuminate\Database\Query\Builder class in Laravel.
     * param values An array of column names to check for null values in the query.
     *
     * return the modified query builder object `` after applying the `whereNull` or `orWhereNull`
     * conditions on the specified columns in the `` array.
     */
    public function whereNull($q, $values)
    {
        $num = 0;
        foreach($values as $column)
        {
            if($num == 0)
                $q->whereNull($column);
            else
                $q->orWhereNull($column);
            $num++;
        }
        return $q;
    }

    /**
     * This function processes a where clause in a SQL query by checking if the value is an array and
     * adding appropriate conditions.
     *
     * param q  is a query builder object that is used to build and execute database queries.
     * param key The column name or field name in the database table that you want to apply the WHERE
     * clause on.
     * param value The value to be used in the WHERE clause of a database query. It can be a single
     * value or an array with two values (operator and comparison value).
     *
     * return the query builder object `` after applying the `where` clause with the provided ``
     * and ``.
     */
    public function proccessWhere($q, $key, $value)
    {
        if(is_array($value) && count($value) == 2)
        {
            return $q->where($key, $value[0], $value[1]);
        }
        return $q->where($key, $value);
    }


    public function destroy($id)
    {
        $data = $this->find($id);
        if($data instanceof \Illuminate\Database\Eloquent\Collection)
        {
            foreach($data as $record)
            {
                $record->delete();
            }
            return true;
        }
        return $data ? $data->delete() : false;
    }

    /**
     * This function returns data from a query based on various parameters such as pluck, get,
     * pagination, and perPage.
     *
     * param pluck An array containing the column to pluck and the key to use as the resulting array's
     * keys.
     * param query This is a query builder instance that represents a database query.
     * param get This parameter determines what type of result should be returned from the query. It
     * can be set to 'toArray' to return the query result as an array, 'count' to return the count of
     * the query result, 'first' to return the first result of the query, or 'delete' to
     * param pagination A boolean value indicating whether or not to use pagination.
     * param perPage The number of items to be displayed per page when using pagination.
     *
     * return different results based on the input parameters. It can return a plucked collection, an
     * array, a count, the first result, a deleted result, a paginated result, or a collection of
     * results.
     */
    public function returnQuery($pluck, $query, $get, $pagination, $perPage)
    {
        if(!empty($pluck))
        {
            return $query->pluck($pluck[0], $pluck[1]);
        }elseif($get == 'toArray')
        {
            return $query->toArray();
        }elseif($get == 'count')
        {
            return $query->count();
        }elseif($get == 'first')
        {
            return $query->first();
        }elseif($get == 'delete')
        {
            return $query->delete();
        }elseif($pagination == true && $perPage != 0)
        {
            return $query->paginate($perPage);
        }else
        {
            return $query->get();
        }
    }

    /**
     * This function returns a query with searchable fields based on the provided value and key.
     *
     * param value The value to search for in the query.
     * param key The key is a string representing the name of the field being searched.
     * param query This is an instance of the query builder class, which is used to build and execute
     * database queries.
     *
     * return a query object with a where or whereIn clause based on the input parameters.
     */
    public function getFieldsSearchableQuery($value, $key, $query)
    {
        if(isset($this->model->searchConfig) && !is_array($value) && array_key_exists($key,
                $this->model->searchConfig) && !empty($this->model->searchConfig[$key]))
        {
            if($this->model->searchConfig[$key] == 'like' || $this->model->searchConfig[$key] == 'LIKE')
            {
                $condition = $this->model->searchConfig[$key] == 'like' || $this->model->searchConfig[$key] == 'LIKE';
                return $query->where($key, $this->model->searchConfig[$key], $condition ? '%' . $value . '%' : $value);
            }
        }elseif(isset($this->model->searchConfig) && is_array($value) && array_key_exists($key,
                $this->model->searchConfig) && !empty($this->model->searchConfig[$key]))
        {
            if(($this->model->searchConfig[$key] == 'date' || $this->model->searchConfig[$key] == 'date') && is_array($value))
            {
                return $query->whereBetween($key, $value);
            }
        }else
        {
            if(is_array($value))
            {
                return $query->whereIn($key, $value);
            }elseif(strpos($value, ',') !== false)
            {
                return $query->whereIn($key, explode(',', $value));
            }else
            {
                return $query->where($key, $value);
            }
        }
    }

    /**
     * This is a PHP function that queries a database based on a translation key and value.
     *
     * param value The value to search for in the translation table.
     * param key The key is a string that represents the key of the translation that is being queried.
     * It is used to filter the results based on the translation key.
     * param query a query builder instance that is used to build the database query
     */
    public function translationKeyQuery($value, $key, $query)
    {
        $query->whereHas('translation', function($query) use ($key, $value)
        {
            if(is_array($value))
            {
                $query->where('key', $key)->whereIn('value', $value);
            }elseif(strpos($value, ',') !== false)
            {
                $query->whereIn($key, explode(',', $value));
            }elseif(isset($this->model->searchConfig) && !is_array($value) && array_key_exists($key,
                    $this->model->searchConfig) && !empty($this->model->searchConfig[$key]))
            {
                $condition = $this->model->searchConfig[$key] == 'like' || $this->model->searchConfig[$key] == 'LIKE';
                $query->where('key', $key)
                    ->where('value', $this->model->searchConfig[$key], $condition ? '%' . $value . '%' : $value);
            }else
            {
                $query->where('key', $key)->where('value', $value);
            }
        });
    }

    /**
     * This function generates a searchable query for a specific field relationship in a PHP model.
     *
     * param value The value to search for in the relationship field.
     * param key The key is a string that represents the field being searched in the query.
     * param query This is a query builder instance that is used to build and execute database
     * queries.
     */
    public function getFieldsRelationShipSearchableQuery($value, $key, $query)
    {
        $relation = explode("->", $this->model->searchRelationShip[$key]);
        $condition = isset($relation[2]) ? $relation[2] : null;
        if((!empty($value) && $value != null && $value != "null") || $value === 0 || $value === "0")
        {
            $query->whereHas($relation[0], function($query) use ($value, $relation, $condition)
            {
                if(is_array($value))
                {
                    $query->whereIn($relation[1], $value);
                }elseif(strpos($value, ',') !== false)
                {
                    $query->whereIn($relation[1], explode(',', $value));
                }elseif(!isset($relation[2]))
                {
                    $query->where($relation[1], $value);
                }else
                {
                    $query->where($relation[1], $condition, '%' . $value . '%');
                }
            });
        }
    }

    /**
     * This function takes in various parameters and returns a modified query object based on those
     * parameters.
     *
     * param skip The number of records to skip before starting to return results.
     * param query This is a query builder instance that represents the database query to be
     * executed.
     * param limit The maximum number of records to be returned by the query.
     * param latest A string parameter that, when set to 'latest', orders the query by the latest
     * created records first.
     * param distinct This parameter is used to retrieve only distinct values from the specified
     * column(s) in the query result. It can be set to the name of the column or an array of column
     * names. If null, all values will be returned.
     * param groupBy This parameter is used to group the results of the query by a specific column or
     * set of columns. It is commonly used with aggregate functions like COUNT, SUM, AVG, etc. to
     * group the results based on certain criteria.
     *
     * return a modified query object based on the provided parameters.
     */
    public function returnAllQuery($skip, $query, $limit, $latest, $distinct, $groupBy)
    {
        if(!is_null($skip))
        {
            $query->skip($skip);
        }
        if(!is_null($limit))
        {
            $query->limit($limit);
        }
        if($latest == 'latest')
        {
            $query->latest();
        }
        if(!is_null($distinct))
        {
            $query->distinct($distinct);
        }
        if(!is_null($groupBy))
        {
            $query->groupBy($groupBy);
        }
        return $query;
    }

    /**
     * This is a PHP function that processes a query with a given key and value, and adds recursive
     * relations to the query if specified.
     *
     * param key The name of the relation that needs to be loaded.
     * param query This parameter is likely an instance of a query builder or an Eloquent model. It is
     * being passed as an argument to the `typeNormal` function and is used to build a query with
     * additional constraints and relationships.
     * param value The  parameter is an array that contains additional parameters for the query.
     * It may include a 'recursive' key that holds an array of related models to be recursively loaded.
     * The function uses this parameter to modify the query and load the related models.
     */
    public function typeNormal($key, $query, $value)
    {
        return $query->with([$key => function($q) use ($key, $value)
        {
            $q = self::proccessQuery($q, $value);
            if(isset($value['recursive']) && count($value['recursive']) > 0)
                $this->addRecursiveRelationsToQuery($q, $value['recursive']);
        }]);
    }

    /**
     * This is a PHP function that adds a whereHas clause to a query and processes any recursive
     * relations.
     *
     * param key The name of the relationship that the query should check for existence of.
     * param query The  parameter is a query builder instance that represents the current query
     * being built. It is used to add conditions to the query using the whereHas method.
     * param value  is an array that contains additional parameters for the query. It may
     * contain a 'recursive' key which is also an array that specifies the recursive relations to be
     * added to the query. The function uses these parameters to modify the query using the whereHas
     * method.
     *
     * return a modified query object that has a whereHas clause added to it. The whereHas clause is
     * based on the  parameter and uses a closure to apply additional query modifications based on
     * the  parameter. If the  parameter includes a 'recursive' key with an array value,
     * the function also calls another method to add recursive relations to the query.
     */
    public function typeWhereHas($key, $query, $value)
    {
        return $query->whereHas($key, function($q) use ($key, $value)
        {
            $q = self::proccessQuery($q, $value);
            if(isset($value['recursive']) && count($value['recursive']) > 0)
                $this->addRecursiveRelationsToQuery($q, $value['recursive']);
        });
    }

    /**
     * This function adds a where clause to a query based on a given key and value, with the option to
     * include recursive relations.
     *
     * param key The name of the column or attribute being queried in the database table.
     * param query The query parameter is an instance of a query builder class, which is used to build
     * and execute database queries in the application. It is passed as an argument to the
     * typeWhereDoesntHaveOrWhereDoesntHave function, which is a custom function defined in the code.
     * param value  is an array that contains the type of query to be performed (whereDoesntHave
     * or orWhereDoesntHave) and other parameters needed for the query such as recursive relations.
     *
     * return the result of a query that applies a "where doesn't have" or "or where doesn't have"
     * condition to a given key and value. The query is processed using the "proccessQuery" function
     * and, if specified, recursive relations are added to the query using the
     * "addRecursiveRelationsToQuery" function.
     */
    public function typeWhereDoesntHaveOrWhereDoesntHave($key, $query, $value)
    {
        return $query->{$value['type']}($key, function($q) use ($key, $value)
        {
            $q = self::proccessQuery($q, $value);
            if(isset($value['recursive']) && count($value['recursive']) > 0)
                $this->addRecursiveRelationsToQuery($q, $value['recursive']);
        });
    }

    /**
     * This function adds a whereHas clause to a query or an orWhereHas clause if one already exists.
     *
     * param key The name of the relationship that the "orWhereHas" method will use to query related
     * records.
     * param query  is a query builder instance that represents a database query. It is used to
     * build and execute SQL queries against a database.
     * param value  is a variable that contains an array of values that will be used in the
     * function. It may contain a key called "recursive" which is also an array.
     *
     * return a modified query using the `orWhereHas` method and applying additional query processing
     * and recursive relations if specified in the `` parameter.
     */
    public function typeOrWhereHas($key, $query, $value)
    {
        return $query->orWhereHas($key, function($q) use ($key, $value)
        {
            $q = self::proccessQuery($q, $value);
            if(isset($value['recursive']) && count($value['recursive']) > 0)
                $this->addRecursiveRelationsToQuery($q, $value['recursive']);
        });
    }

    /**
     * This is a PHP function that adds a whereHas clause to a query based on a morph type and
     * recursively includes related models.
     *
     * param key The name of the relationship method on the model being queried.
     * param query The query parameter is an instance of the query builder class in Laravel. It is
     * used to build and execute database queries.
     * param value The  parameter is an array that contains information about the relationship
     * being queried. It includes the type of the related model (e.g. App\User), as well as any
     * additional constraints or conditions that should be applied to the query. It may also include
     * information about any recursive relationships that should be included
     *
     * return the result of calling a method on the `` object, which is determined by the value
     * of `['type']`. The method being called takes three arguments: ``, `'*'`, and a closure
     * that takes two arguments `` and ``, and performs some operations on `` based on the
     * `` parameter. If `['recursive
     */
    public function typeWhereHasMorph($key, $query, $value)
    {
        return $query->{$value['type']}($key, '*', function($q, $type) use ($key, $value)
        {
            $q = self::proccessQuery($q, $value);
            if(isset($value['recursive']) && count($value['recursive']) > 0)
                $this->addRecursiveRelationsToQuery($q, $value['recursive']);
        });
    }

    /**
     * This function searches for a specific key in a search configuration and applies a where clause
     * to a query based on the key's value, or applies a default where clause if the key is not found.
     *
     * param key The column name in the database table that is being searched.
     * param q  is a query builder object that is used to build and execute SQL queries. It is
     * likely an instance of a class that extends Laravel's Query Builder.
     * param value The value to search for in the database.
     *
     * return the query object ``.
     */
    public function searchConfigQuery($key, $q, $value)
    {
        if(isset($this->model->searchConfig) && array_key_exists($key,
                $this->model->searchConfig) && !empty($this->model->searchConfig[$key]) && $this->model->searchConfig[$key] != "date")
        {
            $q->where($key, $this->model->searchConfig[$key], '%' . $value . '%');
        }else
        {
            $q = $this->proccessWhere($q, $key, $value);
        }
        return $q;
    }
}
