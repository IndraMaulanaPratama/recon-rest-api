<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoreBillerCalendar extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $connection = 'server_report';
    protected $table = 'CSCCORE_BILLER_CALENDAR';
    protected $primaryKey = 'CSC_BC_ID';

    protected $fillable = [
        'CSC_BC_ID',
        'CSC_BC_CALENDAR',
        'CSC_BC_BILLER',
    ];


    public function scopeSearchData($query, $id)
    {
        $query->where('CSC_BC_ID', $id);

        return $query;
    }

    public function scopeSearchByBiller($query, $id)
    {
        return $query->where('CSC_BC_BILLER', $id);
    }

    public function scopeSearchByBillerAndCalendar($query, $calendar, $biller)
    {
        return $query->where(
            [
                'CSC_BC_CALENDAR' => $calendar,
                'CSC_BC_BILLER' => $biller
            ]
        );
    }

    public function scopePaginateData()
    {
        return [
                    'CSC_BC_ID AS ID',
                    'CSC_BC_CALENDAR AS ID_PRODUCT',
                    'CSC_BC_BILLER AS ID_BILLER',
                ];
    }
}
