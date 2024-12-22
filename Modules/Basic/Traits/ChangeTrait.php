<?php

namespace Modules\Basic\Traits;

use Illuminate\Database\Eloquent\Collection;

trait ChangeTrait
{
    /**
     * This function changes the value of a specified key in a collection or single data and updates
     * it.
     * 
     * param datas The data that needs to be updated. It can be either a single instance or a
     * collection of instances.
     * param key The key parameter is a string that represents the name of the column in the database
     * table that needs to be updated. By default, it is set to 'status'.
     * 
     * return the updated data or collection of data with the specified key toggled between 0 and 1.
     */
    public function change($datas, $key = 'status')
    {
        if ($datas instanceof Collection) {
            foreach ($datas as $data) {
                $data[$key] = ($data[$key] == 1 ? 0 : 1);
                $data->update();
            }
        } else {
            $datas[$key] = ($datas[$key] == 1 ? 0 : 1);
            $datas->update();
        }
        return $datas;
    }
}
