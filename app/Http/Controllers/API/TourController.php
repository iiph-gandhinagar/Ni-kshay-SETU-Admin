<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Tour;
use App\Models\TourSlide;

class TourController extends BaseController
{
    public function activeTourDetail()
    {
        $active_tours = Tour::where('active', 1)->get(['id', 'title']);
        if (count($active_tours) == 0) {
            $default_tour = Tour::where('default', 1)->get(['id', 'title']);
            $success = true;
            return ['status' => $success, 'data' => $default_tour, 'code' => 200];
        } else {
            $success = true;
            return ['status' => $success, 'data' => $active_tours, 'code' => 200];
        }
    }

    public function activeTourSlide()
    {
        $tour_slide = TourSlide::with(['media'])->whereHas('tour', function ($q) {
            $q->where('active', 1);
        })->with(['tour' => function ($query) {
            $query->where('active', 1);
        }])->get();

        if (count($tour_slide) == 0) {
            $tour_slide = TourSlide::with(['media'])->whereHas('tour', function ($q) {
                $q->where('default', 1);
            })->with(['tour' => function ($query) {
                $query->where('default', 1);
            }])->get();
        }

        $success = true;
        return ['status' => $success, 'data' => $tour_slide, 'code' => 200];
    }
}
