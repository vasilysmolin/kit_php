<?php

namespace App\Http\Controllers\Logs;

use App\Http\Controllers\Controller;
use App\Models\SearchLogs;
use Illuminate\Http\Request;

class SearchLogsController extends Controller
{
    public function index(Request $request)
    {

        $logs = SearchLogs::select('id', 'text', 'type', 'created_at')
//           ->where('type', 'catalog')
           ->get()
           ->groupBy('text')
           ->map(function ($item) {
               return count($item);
           });

        return response()->json($logs);
    }
}
