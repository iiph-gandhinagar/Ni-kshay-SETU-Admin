<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ActivityLog\IndexActivityLog;
use App\Models\ActivityLog;
use Brackets\AdminListing\Facades\AdminListing;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;

class ActivityLogController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexActivityLog $request
     * @return array|Factory|View
     */
    public function index(IndexActivityLog $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(ActivityLog::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'log_name', 'description', 'subject_type', 'subject_id', 'causer_type', 'causer_id', 'properties', 'created_at'],

            // set columns to searchIn
            ['id', 'log_name', 'description', 'subject_type', 'causer_type', 'properties'],
            function ($query) use ($request) {
                $query->with(['admin_user']);
            }
        );
        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        return view('admin.activity-log.index', ['data' => $data]);
    }
}
