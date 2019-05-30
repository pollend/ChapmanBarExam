<?php


namespace App\Utility;



use Illuminate\Http\Request;

class Datatable
{
    const ASC = 'ASC';
    const DESC = 'DESC';
    private  $columnSort = [];
    private $payload;
    function __construct()
    {
    }
    public function handleSort(Request $request,$sortColumns = [])
    {
        $sort = json_decode($request->get('sort',[]));
        foreach ($sortColumns as $column)
        {
            if(array_key_exists($column,$sort))
            {
                switch ($sort[$column])
                {
                    case 'ASC':
                        $this->columnSort[$column] = Datatable::ASC;
                        break;
                    case 'DESC':
                        $this->columnSort[$column] = Datatable::DESC;
                        break;
                }
            }
        }
    }
    public function getSort()
    {
        return $this->columnSort;
    }
    public function setPayload($payload)
    {
        $this->payload = $payload;
    }
    public function getPayload()
    {
        return $this->payload;
    }
}