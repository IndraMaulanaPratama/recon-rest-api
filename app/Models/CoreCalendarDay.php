<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoreCalendarDay extends Model
{
    use HasFactory;
    public $timestamps = false;

    // protected $connection = 'devel_report';
    protected $connection = 'server_report';
    protected $table = 'CSCCORE_CALENDAR_DAYS';
    protected $primaryKey = 'CSC_CD_ID';

    protected $fillable = [
        'CSC_CD_ID',
        'CSC_CD_CALENDAR',
        'CSC_CD_DATE',
        'CSC_CD_DESC',
    ];

    public function scopeSearchData($query, $id)
    {
        return $query->where('CSC_CD_ID', $id);
    }

    public function scopeSearchCalendar($query, $id)
    {
        return $query->where('CSC_CD_CALENDAR', $id);
    }

    public function scopeCheckData($query, $calendar, $date)
    {
        return $query->where(
            [
                'CSC_CD_CALENDAR' => $calendar,
                'CSC_CD_DATE' => $date,
            ]
        );
    }
}
