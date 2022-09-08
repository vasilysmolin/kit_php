<?php

namespace App\Http\Controllers\Logs;

use App\Http\Controllers\Controller;
use App\Models\SearchLogs;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SearchLogsController extends Controller
{
    public function index(Request $request)
    {
        $from = Carbon::parse($request->get('dateFrom'))->format('Y-m-d');
        $to = Carbon::parse($request->get('dateTo'))->format('Y-m-d');
        $logs = SearchLogs::select('id', 'text', 'type', 'created_at')
            ->when(!empty($from) && !empty($to), function ($q) use ($from, $to) {
                $q->whereBetween('created_at', [$from, $to]);
            })
//           ->where('type', 'catalog')
           ->get()
           ->groupBy('text')
           ->map(function ($item) {
               return count($item);
           });

        return response()->json($logs);
    }
}
