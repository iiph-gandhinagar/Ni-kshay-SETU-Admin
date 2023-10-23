<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MessageNotification\BulkDestroyMessageNotification;
use App\Http\Requests\Admin\MessageNotification\DestroyMessageNotification;
use App\Http\Requests\Admin\MessageNotification\IndexMessageNotification;
use App\Http\Requests\Admin\MessageNotification\StoreMessageNotification;
use App\Http\Requests\Admin\MessageNotification\UpdateMessageNotification;
use App\Models\MessageNotification;
use Brackets\AdminListing\Facades\AdminListing;
use Carbon\Carbon;
use Exception;
use Notification;
use Validator;
use App\Notifications\SmsNotification;
use Brackets\AdminAuth\Models\AdminUser;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Exports\MessageNotificationExport;
use Log;

class MessageNotificationsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexMessageNotification $request
     * @return array|Factory|View
     */
    public function index(IndexMessageNotification $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(MessageNotification::class)->modifyQuery(function ($query) use ($request) {
            if ($request->orderDirection == "") {
                $query->orderBy('created_at', 'desc');
                $request->orderDirection = "asc";
            }
        })->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'user_name', 'phone_no', 'notification_message', 'created_at'],

            // set columns to searchIn
            ['id', 'user_name', 'phone_no', 'notification_message']
        );

        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        return view('admin.message-notification.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.message-notification.create');

        return view('admin.message-notification.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreMessageNotification $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreMessageNotification $request)
    {
        if (isset($request['material'][0])) {
            $filename = storage_path("uploads/") . $request['material'][0]['path'];
            $arrImportadata = $this->csvToArray($filename);

            $count = 0;
            $validationError = 0;
            if (isset($arrImportadata) && !empty($arrImportadata)) {
                $duplicateValues = array_count_values(array_column($arrImportadata, 'phone_no'));

                if (count($arrImportadata) != count($duplicateValues)) {
                    $validationError = 1;
                    $duplicatePhoneNo = array_search('2', $duplicateValues);
                    $errorMsg = $duplicatePhoneNo . ' - This Phone No. appears in your list more than once, So please try again with valid data.!';

                    // return action(['message' => $errorMsg]);
                    return abort(400, $errorMsg);
                    // return ['redirect' => url('admin/message-notifications/create?error='.$errorMsg)];
                }
                foreach ($arrImportadata as $all_request) {

                    $rules = [
                        'user_name' => 'required',
                        'phone_no' => 'required|numeric|regex:/\d{10}$/',
                    ];
                    $messages = [
                        'name.required' => 'There is problem with Name Field. Please try again with valid data.',
                        'phone_no.required' => 'There is problem with Phone no Field. Please try again with valid data.',
                        'phone_no.regex' => 'Phone No should be of 10 digit.',
                        'phone_no.numeric' => 'Phone No Must be a Number.',
                    ];
                    $validator = Validator::make($all_request, $rules, $messages);
                    if ($validator->fails()) {
                        $validationError = 1;
                        $error = $validator->errors();
                        $error = json_decode($error, true);
                        return abort(400, $error['phone_no'][0]);
                    }
                }
                if ($validationError == 0) {
                    $messagesToInsert = [];
                    foreach ($arrImportadata as $message) {
                        $recordToInsert['user_name'] = $message['user_name'];
                        $recordToInsert['phone_no'] = $message['phone_no'];
                        $recordToInsert['notification_message'] = $request['message'];
                        $recordToInsert['created_at'] = now();
                        $recordToInsert['updated_at'] = now();
                        array_push($messagesToInsert, $recordToInsert);
                    }
                    MessageNotification::insert($messagesToInsert);
                    Notification::send(AdminUser::find(1), new SmsNotification($messagesToInsert));
                }
            }
        }

        if ($request->ajax()) {
            return ['redirect' => url('admin/message-notifications'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/message-notifications');
    }

    public function csvToArray($filename = '', $delimiter = ',')
    {

        if (!file_exists($filename) || !is_readable($filename)) {
            return false;
        }

        $header = null;
        $data = array();

        if (($handle = fopen($filename, 'r')) !== false) {
            $counter = 0;
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                if (!$header)
                    $header = $row;
                else
                    $data[] = array_combine($header, $row);
                $counter++;
            }
            fclose($handle);
        }
        return $data;
    }

    /**
     * Display the specified resource.
     *
     * @param MessageNotification $messageNotification
     * @throws AuthorizationException
     * @return void
     */
    public function show(MessageNotification $messageNotification)
    {
        $this->authorize('admin.message-notification.show', $messageNotification);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param MessageNotification $messageNotification
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(MessageNotification $messageNotification)
    {
        $this->authorize('admin.message-notification.edit', $messageNotification);


        return view('admin.message-notification.edit', [
            'messageNotification' => $messageNotification,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateMessageNotification $request
     * @param MessageNotification $messageNotification
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateMessageNotification $request, MessageNotification $messageNotification)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values MessageNotification
        $messageNotification->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/message-notifications'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/message-notifications');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyMessageNotification $request
     * @param MessageNotification $messageNotification
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyMessageNotification $request, MessageNotification $messageNotification)
    {
        $messageNotification->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyMessageNotification $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyMessageNotification $request): Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('messageNotifications')->whereIn('id', $bulkChunk)
                        ->update([
                            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s')
                        ]);

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }

    public function export(): ?BinaryFileResponse
    {
        return Excel::download(new MessageNotificationExport(), 'sample_file.csv');
    }
}
