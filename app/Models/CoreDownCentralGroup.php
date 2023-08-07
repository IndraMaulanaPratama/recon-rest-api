<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoreDownCentralGroup extends Model
{
    use HasFactory;

    public $timeStamps = false;

    protected $connection = 'server_report';
    // protected $connection = 'devel_report';
    protected $table = 'CSCCORE_DOWN_CENTRAL_GROUP';
    protected $primaryKey = 'CSC_DC_ID';

    public function scopeSearchData($query, $id)
    {
        $query->where('CSC_DC_ID', $id);
        return $query;
    }
}
