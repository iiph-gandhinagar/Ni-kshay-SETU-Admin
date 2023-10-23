<?php
namespace App\Helpers;

class DateTimeUtils
{
    public static function getDateInddmmmYYYY($date)
    {
      return $date->format('d M Y');
      // return $date->format('d M Y H:i');
    }

    public static function getDateInddmmmYYYYWithTime($date)
    {
      return date('d M Y h:i A',strtotime($date));
    }

    public static function getDateOnly($date)
    {
      $newDate = date("d M Y", strtotime($date));  
      return $newDate;
    }

    public static function getTime($date)
    {
      return date('h:i A',strtotime($date));
    }
    
    public static function getDatewithFullMonthName($date)
    {
      $newDate = date("d F, Y", strtotime($date));  
      return $newDate;
    }

    public static function getMonthOnly($date)
    {
      $newDate = date("M Y", strtotime($date));  
      return $newDate;
    }
}
