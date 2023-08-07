<?php

namespace App\Traits;

use App\Models\CoreCalendar;

trait CalendarTraits
{
    use ResponseHandler;

    // Response Calendar Not Found
    public function calendarNotFound()
    {
        return $this->responseNotFound('Data Calendar Not Found');
    }

    // Search Deleted Data Calendar
    public function calendarSearchDeletedData($id)
    {
        // Logic Get Data
        $data = CoreCalendar::searchTrashData($id)->first();

        // Null == false | !Null = data
        return (null == $data) ? false : $data;
    }
}
