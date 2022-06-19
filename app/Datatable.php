<?php

namespace Datatable;
use App;

class Datatable 
{
    private $columns = [];
    private $model = null;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function setColumnsOrder($columns)
    {
        $this->columns = $columns;
    }

    public function make($request,$callback,$filterLogic = null)
    {
        $filters = $request->only("filter");
        $totalData = $this->model->count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $this->columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $results = $this->model;
        if(@$filters["filter"])
            if($filterLogic)
                $results = $filterLogic($results,@$filters["filter"]);
        $totalFiltered = $results->count();
        $results = $results
            ->offset($start)
            ->limit($limit)
            ->orderByRaw($order." ".$dir)
            ->get();
        $data = [];
        if(!empty($results))
        {
            foreach ($results as $row)
            {
                $data[] = $callback($row);
            }
        }
        $json_data = array(
            "draw"            => intval($request->input('draw')),  
            "recordsTotal"    => intval($totalData),  
            "recordsFiltered" => intval($totalFiltered), 
            "data"            => $data   
        );
        return response()->json($json_data);
    }
}
