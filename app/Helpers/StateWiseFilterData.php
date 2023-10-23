<?php
namespace App\Helpers;
use App\Models\State;
use App\Models\District;
use App\Models\Block;
use App\Models\Cadre;
use App\Models\Assessment;
use App\Models\Country;
use App\Models\Subscriber;
use App\Models\HealthFacility;
use Log;

class StateWiseFilterData
{
    public static function getStateWiseMasterData()
    {
      $assignedState = \Auth::user()->state;
      $assignedDistrict = \Auth::user()->district;
      $assignedCadre = \Auth::user()->cadre;
      $assignedCountry = \Auth::user()->country;
      $assignedCadreType = \Auth::user()->cadre_type;
      $assignedRoleType = \Auth::user()->role_type;

      Log::info('district ---->');
      Log::info($assignedDistrict);
      Log::info('cadre ---->');
      Log::info($assignedCadre);
      Log::info('state ---->');
      Log::info($assignedState);

      $state = State::orderBy('id');
      $district = District::orderBy('id');
      $block = Block::orderBy('id');
      $cadres = Cadre::orderBy('id');
      $country = Country::orderBy('id');
      $assessment = Assessment::orderBy('id');

      // if($assignedState != '' && $assignedState > 0){
      //   $state = $state->where('id',$assignedState);
      //   $district = $district->where('state_id',$assignedState);
      //   $block = $block->where('state_id',$assignedState);
      //   $assessment = $assessment->whereRaw("find_in_set('".$assignedState."',state_id)");
      // }

      if ($assignedRoleType == 'country_type') {
        if($assignedState != ''){
          $state = $state->whereIn('id',explode(',',$assignedState));
        }
        if( $assignedDistrict != ''){
          $district = $district->whereIn('id',explode(',',$assignedDistrict));
        } 
        if($assignedCadre != ''){
          $cadres = $cadres->whereIn('id', explode(',',$assignedCadre));
        }
        $country = $country->where('id',$assignedCountry);
        if($assignedState != '' && $assignedDistrict != '' ){
          $block = $block->whereIn('state_id',explode(',',$assignedState))->whereIn('district_id',explode(',',$assignedDistrict));
        }
        // $assessment = $assessment->where('country_id',$assignedCountry)->whereRaw("find_in_set('".$assignedCadre."',cadre_id)");
        if( $assignedCadre != '' && ( \Auth::user()->roles[0]['id'] != 1 && \Auth::user()->roles[0]['id'] != 2)){
          $assessment = $assessment->whereRaw("substr('".$assignedCountry."',country_id)")->whereRaw("substr('".$assignedCadre."',cadre_id)")->orWhere('cadre_id',$assignedCadre);//->where('cadre_id','LIKE',"%$assignedCadre%")
        }
      }
      else if ($assignedRoleType == 'state_type') {
        $state = $state->where('id',$assignedState);
        $district = $district->whereIn('id',explode(',',$assignedDistrict));
        $cadres = $cadres->whereIn('id', explode(',',$assignedCadre));
        // $block = $block->where('state_id',$assignedState)->whereIn('district_id',explode(',',$assignedDistrict));
        $assessment = $assessment->whereRaw("substr('".$assignedState."',state_id)")->whereRaw("substr('".$assignedCadre."',cadre_id)")->orWhere('cadre_id',$assignedCadre);//->where('cadre_id','LIKE',"%$assignedCadre%")
      } 
      else if ($assignedRoleType == 'district_type'){
        $state = $state->where('id',$assignedState);
        $district = $district->whereIn('id',explode(',',$assignedDistrict));
        $cadres = $cadres->whereIn('id', explode(',',$assignedCadre));
        $block = $block->where('state_id',$assignedState)->whereIn('district_id',explode(',',$assignedDistrict));
        // $assessment = $assessment->where('district_id','LIKE',"%$assignedDistrict%");//->where('cadre_id','LIKE',"%$assignedCadre%")->
        $assessment = $assessment->whereRaw("substr('".$assignedDistrict."',district_id)")->whereRaw("substr('".$assignedCadre."',cadre_id)")->orWhere('cadre_id',$assignedCadre);//->where('cadre_id','LIKE',"%$assignedCadre%")->
      }

      $state = $state->get(['id','title']);
      $district = $district->get(['id','title']);
      $block = $block->get(['id','title']);
      $cadres = $cadres->get(['id','title','cadre_type']);
      $country = $country->get(['id','title']);
      $assessment = $assessment->get(['id','assessment_title']);
      // $assessment = $assessment->toSql();
      // Log::info($assessment);
      Log::info($state);

      return ['state' => $state, 'district' => $district, 'block' => $block, 'assessment' => $assessment, 'cadres' => $cadres,'country' => $country];
    }

    public static function getStateWiseFilterDataWithSubscriber()
    {
      $assignedState = \Auth::user()->state;
      $assignedDistrict = \Auth::user()->district;
      $assignedCadre = \Auth::user()->cadre;
      $assignedCountry = \Auth::user()->country;
      $assignedCadreType = \Auth::user()->cadre_type;
      $assignedRoleType = \Auth::user()->role_type;

      $state = State::orderBy('id');
      $district = District::orderBy('id');
      $subscriber = Subscriber::orderBy('name');
      $cadres = Cadre::orderBy('id');
      $country = Country::orderBy('id');

      // if($assignedState != '' && $assignedState > 0){
      //   $state = $state->where('id',$assignedState);
      //   $district = $district->where('state_id',$assignedState);
      //   $subscriber->where('state_id',$assignedState);
      //   $cadres = Cadre::where('cadre_type','NOT LIKE', "%National_Level%");
      // }

      if ($assignedRoleType == 'country_type') {
        if($assignedState != ''){
          $state = $state->whereIn('id',explode(',',$assignedState));
        }
        if( $assignedDistrict != ''){
          $district = $district->whereIn('id',explode(',',$assignedDistrict));
        } 
        if($assignedCadre != ''){
          $cadres = $cadres->whereIn('id', explode(',',$assignedCadre));
        }
        // $subscriber = $subscriber->where('country_id',$assignedCountry);
        $country = $country->where('id',$assignedCountry);
      }
      else if ($assignedRoleType == 'state_type') {
        $state = $state->where('id',$assignedState);
        $district = $district->whereIn('id',explode(',',$assignedDistrict));
        $subscriber = $subscriber->where('state_id',$assignedState)->whereIn('district_id',explode(',',$assignedDistrict));
        $cadres = $cadres->whereIn('id', explode(',',$assignedCadre));
      } 
      else if ($assignedRoleType == 'district_type'){
        $state = $state->where('id',$assignedState);
        $district = $district->whereIn('id',explode(',',$assignedDistrict));
        $subscriber = $subscriber->where('state_id',$assignedState)->whereIn('district_id',explode(',',$assignedDistrict));
        $cadres = $cadres->whereIn('id', explode(',',$assignedCadre));
      }

      $state = $state->get(['id','title']);
      $district = $district->get(['id','title','state_id']);
      $subscriber = $subscriber->get(['id','name','phone_no']);
      $cadres = $cadres->get(['id','title','cadre_type']);
      $country = $country->get(['id','title']);

      return ['state' => $state, 'district' => $district, 'subscriber' => $subscriber,'cadres' => $cadres, 'country' => $country];
    }

    public static function getStateWiseFilterDataWithHealthFacility()
    {
      $assignedState = \Auth::user()->state;
      $assignedDistrict = \Auth::user()->district;
      $assignedCadre = \Auth::user()->cadre;
      $assignedCountry = \Auth::user()->country;
      $assignedCadreType = \Auth::user()->cadre_type;
      $assignedRoleType = \Auth::user()->role_type;

      Log::info('cadre details');
      Log::info($assignedCadre);
      Log::info('cadre type details ');
      Log::info($assignedCadreType);
      Log::info('state details ');
      Log::info($assignedState);
      Log::info('district Details');
      Log::info($assignedDistrict);
      Log::info('auth user role');
      Log::info( \Auth::user()->roles[0]['id']);

      $state = State::orderBy('id');
      $district = District::orderBy('id');
      $block = Block::orderBy('id');
      $assessment = Assessment::orderBy('id');
      $subscriber = Subscriber::orderBy('name');
      $cadres = Cadre::orderBy('id');
      $country = Country::orderBy('id');
      $health_facility = HealthFacility::orderBy('id');

      // if($assignedState != '' && $assignedState > 0){
      //   $state = $state->where('id',$assignedState);
      //   $district = $district->where('state_id',$assignedState);
      //   $block = $block->where('state_id',$assignedState);
      //   $assessment = $assessment->whereRaw("find_in_set('".$assignedState."',state_id)");
      //   $subscriber->where('state_id',$assignedState);
      //   $health_facility->where('state_id',$assignedState);
      // }

      if ($assignedRoleType == 'country_type') {
        if($assignedState != ''){
          $state = $state->whereIn('id',explode(',',$assignedState));
        }
        if( $assignedDistrict != ''){
          $district = $district->whereIn('id',explode(',',$assignedDistrict));
        } 
        if($assignedCadre != ''){
          $cadres = $cadres->whereIn('id', explode(',',$assignedCadre));
        }
        if ($assignedState != '' && $assignedDistrict != ''){
          $block = $block->whereIn('state_id',explode(',',$assignedState))->whereIn('district_id',explode(',',$assignedDistrict));
        }
        if($assignedState != '' && $assignedDistrict != ''){
          $health_facility->whereIn('state_id',explode(',',$assignedState))->whereIn('district_id',explode(',',$assignedDistrict));
        }
        if($assignedCadre != '' && ( \Auth::user()->roles[0]['id'] != 1 && \Auth::user()->roles[0]['id'] != 2)){
          Log::info('inside assigned assessment ----->');
          $assessment = $assessment->whereRaw("substr('".$assignedCountry."',country_id)")->whereRaw("substr('".$assignedCadre."',cadre_id)")->orWhere('cadre_id',$assignedCadre);
        }
        $subscriber = $subscriber->where('country_id',$assignedCountry);
        $country = $country->where('id',$assignedCountry);
      }
      else if ($assignedRoleType == 'state_type') {
        $state = $state->where('id',$assignedState);
        $district = $district->whereIn('id',explode(',',$assignedDistrict));
        $block = $block->where('state_id',$assignedState)->whereIn('district_id',explode(',',$assignedDistrict));
        $subscriber = $subscriber->where('state_id',$assignedState)->whereIn('district_id',explode(',',$assignedDistrict));
        $assessment = $assessment->whereRaw("substr('".$assignedState."',state_id)")->whereRaw("substr('".$assignedCadre."',cadre_id)")->orWhere('cadre_id',$assignedCadre);
        $cadres = $cadres->whereIn('id', explode(',',$assignedCadre));
        $health_facility->where('state_id',$assignedState)->whereIn('district_id',explode(',',$assignedDistrict));
      } 
      else if ($assignedRoleType == 'district_type'){
        $state = $state->where('id',$assignedState);
        $district = $district->whereIn('id',explode(',',$assignedDistrict));
        $block = $block->where('state_id',$assignedState)->whereIn('district_id',explode(',',$assignedDistrict));
        $subscriber = $subscriber->where('state_id',$assignedState)->whereIn('district_id',explode(',',$assignedDistrict));
        $cadres = $cadres->whereIn('id', explode(',',$assignedCadre));
        $assessment = $assessment->whereRaw("substr('".$assignedDistrict."',district_id)")->whereRaw("substr('".$assignedCadre."',cadre_id)")->orWhere('cadre_id',$assignedCadre);
        $health_facility->where('state_id',$assignedState)->whereIn('district_id',explode(',',$assignedDistrict));
      }

      $state = $state->get(['id','title']);
      $district = $district->get(['id','title','state_id']);
      $block = $block->get(['id','title','state_id','district_id']);
      $assessment = $assessment->get(['id','assessment_title']);
      $subscriber = $subscriber->get(['id','name']);
      $health_facility = $health_facility->get(['state_id','district_id','block_id','health_facility_code', 'id']);
      $cadres = $cadres->get(['id','title','cadre_type']);
      $country = $country->get(['id','title']);

      return ['state' => $state, 'district' => $district, 'block' => $block, 'assessment' => $assessment,
             'subscriber' => $subscriber, 'health_facility' => $health_facility, 'cadres' => $cadres, 'country' => $country];
    }
}
