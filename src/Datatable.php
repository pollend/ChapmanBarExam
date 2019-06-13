<?php

namespace App;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Annotation\Groups;

class Datatable
{
    const ASC = 'ASC';
    const DESC = 'DESC';

    /**
     * @var array
     * @Groups({"list"})
     */
    private $columnSort = [];

    /**
     * @var
     * @Groups({"list"})
     */
    private $payload;

    public function __construct()
    {
    }

    public function handleSort(Request $request, $sortColumns, $callback)
    {
        $sort = $request->get('sort', []);
        if (!is_array($sort)) {
            $sort = [$sort];
        }
        foreach ($sortColumns as $column => $target) {
            if (array_key_exists($column, $sort)) {
                switch ($sort[$column]) {
                    case 'ASC':
                        $callback($target, Datatable::ASC);
                        $this->columnSort[$column] = Datatable::ASC;
                        break;
                    case 'DESC':
                        $callback($target, Datatable::DESC);
                        $this->columnSort[$column] = Datatable::DESC;
                        break;
                }
            }
        }
    }

    /**
     * @return array
     */
    public function getColumnSort(): array
    {
        return $this->columnSort;
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
