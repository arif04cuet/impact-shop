<?php

namespace Arstech\Webshop\Models;

use Illuminate\Support\Facades\DB;
use Mmid\Team\Models\Member;
use Model;

/**
 * Model
 */
class Schedule extends Model
{
    use \October\Rain\Database\Traits\Validation;

    protected $jsonable = ['dates'];
    /**
     * @var string The database table used by the model.
     */
    public $table = 'arstech_webshop_schedules';

    /**
     * @var array Validation rules
     */
    public $rules = [
        'location_id' => 'required',
        'max_participants' => 'required',
        'dates.*.start_date' => 'required',
        'dates.*.start_time' => 'required',
        'dates.*.end_time' => 'required',
    ];

    public $belongsTo = [

        'location' => 'Arstech\Webshop\Models\Location',
        'product' => ['OFFLINE\Mall\Models\Product', 'key' => 'course_id', 'otherKey' => 'id']
    ];

    //scopes

    public function scopeAvailable($query)
    {
        return $query->where('is_fully_booked', 0);
    }
    public function getInstructorOptions()
    {
        return Member::where('is_active', 1)->orderBy('firstname', 'asc')->get()
            ->map(function ($member) {
                return [
                    'id' => $member->id,
                    'full_name' => $member->full_name
                ];
            })
            ->pluck('full_name', 'id')
            ->toArray();
    }


    public function findInstructor($id)
    {
        return Member::find($id);
    }



    public static function getSchedulesSortedByDistance($product, $zip_city)
    {
        // $schedule = Schedule::whereHas('location', function ($q) use ($zip_city) {
        //     $q->where('zipcode', 'like', '%' . trim($zip_city) . '%')->orWhere('city', 'like', '%' . trim($zip_city) . '%');
        // }); //->first();

        $location = Location::where('zipcode', 'like', '%' . trim($zip_city) . '%')
            ->orWhere('city', 'like', '%' . trim($zip_city) . '%');

        traceLog($location->toSql(), $location->getBindings());
        // $schedule = $schedule->first();
        $location = $location->first();
        $latitude = $location ? $location->latitude : 0;
        $longitude =  $location ? $location->longitude : 0;

        return self::getSchedulesSortedByDistanceLatLong($product, $latitude, $longitude);
    }


    public static function getSchedulesSortedByDistanceLatLong($product, $latitude, $longitude)
    {

        $haversine = "(6371 * acos(cos(radians($latitude))
                        * cos(radians(arstech_webshop_locations.latitude))
                        * cos(radians(arstech_webshop_locations.longitude)
                        - radians($longitude))
                        + sin(radians($latitude))
                        * sin(radians(arstech_webshop_locations.latitude))))";

        $list = DB::table('arstech_webshop_schedules')
            ->select(['arstech_webshop_schedules.*', 'arstech_webshop_locations.title', 'arstech_webshop_locations.zipcode'])
            ->join('arstech_webshop_locations', 'arstech_webshop_locations.id', '=', 'arstech_webshop_schedules.location_id')
            ->selectRaw("ROUND({$haversine},0) AS distance")
            ->where('course_id', $product->id)
            //->where('is_fully_booked', 0)
            ->orderBy('distance', 'asc')
            ->get()
            ->groupBy('location_id')
            ->toArray();

        //traceLog($list);
        // make online course distance at 0 km
        $list = array_map(function ($item) {

            return array_map(function ($schedule) {
                $schedule->distance = !$schedule->zipcode ? 0 : $schedule->distance;
                $schedule->location = Location::find($schedule->location_id)->toArray();
                return $schedule;
            }, $item);
        }, $list);


        usort($list, function ($a, $b) {

            $a1 = $a[0]->distance;
            $b1 = $b[0]->distance;

            return ($a1 < $b1) ? -1 : 1;
        });


        $list = collect($list)->map(function ($s) {
            $s = collect($s)->map(function ($i) {
                $i->dates = json_decode($i->dates, true);
                return $i;
            })->toArray();
            return $s;
        });

        return $list;
    }
}
