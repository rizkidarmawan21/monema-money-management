<?php

namespace App\Http\Controllers;

use App\Http\Resources\Transaksi\HistoryTransactionListResource;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HistoryController extends AdminBaseController
{
    public function indexHistory()
    {
        return Inertia::render('admin/history/index');
    }

    public function getData(Request $request)
    {
        try {
            $search = $request->search;
            $start_date = $request->start_date;
            $end_date = $request->end_date;

            $query = Transaksi::query();

            $query->when(request('start_date', false) && request('end_date', false), function ($q) use ($start_date, $end_date) {
                $q->whereBetween('created_at', [$start_date, $end_date]);
            });

            $data = $query->paginate(10);

            $result = new HistoryTransactionListResource($data);

            return $this->respond($result);

        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }
}
