<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuleDefinition extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $connection = 'server_report';
    protected $table = 'CSCCORE_MODULE_DEFINITION';
    protected $primaryKey = 'CSC_MD_GROUPNAME';

    protected $fillable = [
        'CSC_MD_GROUPNAME',
        'CSC_MD_TABLE',
        'CSC_MD_ALIASNAME',
        'CSC_MD_DESC',
        'CSC_MD_BILLER_COLUMN',
        'CSC_MD_CRITERIA',
        'CSC_MD_FINDCRITERIA',
        'CSC_MD_BANK_CRITERIA',
        'CSC_MD_CENTRAL_CRITERIA',
        'CSC_MD_BANK_COLUMN',
        'CSC_MD_CENTRAL_COLUMN',
        'CSC_MD_TERMINAL_COLUMN',
        'CSC_MD_SUBID_COLUMN',
        'CSC_MD_SUBNAME_COLUMN',
        'CSC_MD_SWITCH_REFNUM_COLUMN',
        'CSC_MD_SWITCH_PAYMENT_REFNUM_COLUMN',
        'CSC_MD_DATE_COLUMN',
        'CSC_MD_NREK_COLUMN',
        'CSC_MD_NBILL_COLUMN',
        'CSC_MD_BILL_AMOUNT_COLUMN',
        'CSC_MD_ADM_AMOUNT_COLUMN',
        'CSC_MD_ADM_AMOUNT_COLUMN_DEDUCTION_0',
        'CSC_MD_ADM_AMOUNT_COLUMN_DEDUCTION_1',
        'CSC_MD_ADM_AMOUNT_COLUMN_DEDUCTION_2',
        'CSC_MD_TABLE_ARCH',
        'CSC_MD_BANK_GROUPBY',
        'CSC_MD_CENTRAL_GROUPBY',
        'CSC_MD_TERMINAL_GROUPBY',
        'CSC_MD_TYPE_TRX',
        'CSC_MD_ISACTIVE',
        'CSC_MD_CREATED_DT',
        'CSC_MD_CREATED_By',
        'CSC_MD_MODIFIED_DT',
        'CSC_MD_MODIFIED_By',
        'CSC_MD_DELETED_DT',
        'CSC_MD_DELETED_By',
    ];

    public function scopeGetData($query)
    {
        return $query->whereNull('CSC_MD_DELETED_DT');
    }

    public function scopeGetTrashData($query)
    {
        return $query->whereNotNull('CSC_MD_DELETED_DT');
    }

    public function scopeTrashGroupName($query, $name)
    {
        return $query->whereNotNull('CSC_MD_DELETED_DT')
        ->where('CSC_MD_GROUPNAME', $name);
    }

    public function scopeGroupName($query, $name)
    {
        return $query->whereNull('CSC_MD_DELETED_DT')
        ->where('CSC_MD_GROUPNAME', $name);
    }

    public function scopeFilterGroupName($query, $id)
    {
        return $query->where('CSC_MD_GROUPNAME', 'LIKE', '%'. $id .'%');
    }

    public function scopeFilterTable($query, $id)
    {
        return $query->where('CSC_MD_TABLE', 'LIKE', '%'. $id .'%');
    }

    public function scopeFilterBank($query, $id)
    {
        return $query->where('CSC_MD_BANK_COLUMN', 'LIKE', '%'. $id .'%');
    }

    public function scopeFilterCentral($query, $id)
    {
        return $query->where('CSC_MD_CENTRAL_COLUMN', 'LIKE', '%'. $id .'%');
    }

    public function scopeFilterTypeTransaction($query, $id)
    {
        return $query->where('CSC_MD_TYPE_TRX', 'LIKE', '%'. $id .'%');
    }

    public function scopeFilterIsActive($query, $id)
    {
        return $query->where('CSC_MD_ISACTIVE', $id);
    }
}
