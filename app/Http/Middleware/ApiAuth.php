<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

use Log;

class ApiAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $data = Subscriber::where('api_token', $request->bearerToken())->get();
        if (count($data) > 0) {
            $newRequest = $request->all();
            $agent = new Agent();
            $header = $request->header('platform');
            if (isset($header) && $header != "") {
                $newRequest['plateform'] = $request->header('platform');
            } else {
                if ($agent->isMobile()) {
                    $newRequest['plateform'] = 'app';
                } elseif ($agent->isDesktop()) {
                    $newRequest['plateform'] = 'web';
                } elseif ($agent->isPhone() || $agent->is('iPhone')) {
                    $newRequest['plateform'] = 'iPhone-app';
                } else {
                    $newRequest['plateform'] = 'mobile-app';
                }
            }

            $url = $request->path();
            $array = trans('admin.userActivities');
            $value =  isset($array[$url]) ? $array[$url] : null;
            if ($value != '') {
                $newRequest['user_id'] = $data[0]['id'];
                $newRequest['action'] = $value;
                // $newRequest['ip_address'] = $request->ip();
                $newRequest['payload'] = json_encode($request->all());

                app('App\Http\Controllers\API\SubscriberActivitiesController')->storeActivity($newRequest);
            }

            return $next($request);
        } else {
            return response()->json('Your are not Valid User!Please Enter Valid Token', 401);
        }
    }
}
