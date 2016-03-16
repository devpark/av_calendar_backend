<?php

namespace App\Modules\CalendarAvailability\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;
use DB;

trait CalendarAvailable
{
    /**
     * Column in database for object id
     *
     * @var string
     */
    protected static $calendarObjectIdColumn = 'user_id';

    /**
     * Get availabilities for selected objects in selected days
     *
     * @param int|array $objects
     * @param string|array $days
     *
     * @return Collection
     */
    public static function getForObjectsAndDays($objects, $days)
    {
        return self::getForObjectsAndDaysQuery($objects, $days)->get();
    }

    /**
     * Delete availabilities for selected objects in selected days
     *
     * @param int|array $objects
     * @param string|array $days
     */
    public static function deleteForObjectsAndDays($objects, $days)
    {
        self::getForObjectsAndDaysQuery($objects, $days)->delete();
    }

    /**
     * Get query to get availabilities for objects in given days
     *
     * @param int|array $objects
     * @param string|array $days
     *
     * @return Builder
     */
    protected static function getForObjectsAndDaysQuery($objects, $days)
    {
        return self::whereIn(self::$calendarObjectIdColumn, (array)$objects)
            ->whereIn('day', (array)$days)
            ->orderBy(self::$calendarObjectIdColumn, 'ASC')
            ->orderBy('day', 'ASC')
            ->orderBy('time_start', 'ASC');
    }

    /**
     * Set new calendar availabilities for object in selected day
     *
     * @param int $objectId
     * @param string $day
     * @param array $data
     */
    public static function add($objectId, $day, array $data)
    {
        DB::transaction(function () use ($objectId, $day, $data) {
            // first remove existing entries for object in this day
            self::deleteForObjectsAndDays($objectId, $day);

            // now, add new ones
            foreach ($data as $availability) {
                self::create(array_merge($availability,
                    [
                        self::$calendarObjectIdColumn => $objectId,
                        'day' => $day,
                    ]));
            }
        });
    }
}
