<?php

namespace App\Http\Controllers;

use App\Traits\ResponseHandler;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Collection;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;
    use ResponseHandler;

    public function addIndexNumber($data)
    {
        $countData = count($data);
        for ($i=0; $i < $countData; $i++) {
            $data[$i] = collect($data[$i]);
            $data[$i]->put('INDEX_NUMBER', $i);
        }

        return $data;
    }

    public function createPaginate($items, $perPage = 5, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);

        $items = $items instanceof Collection ? $items : Collection::make($items);

        $options['path'] = url()->current();

        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}
