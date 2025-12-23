<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Report extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'protocol_id',
        'description',
        'status',
    ];

    protected static function boot()
{
    parent::boot();

    static::creating(function ($report) {
        if (empty($report->status)) {
            $report->status = 'A';
        }
    });
}

    public function protocol()
    {
        return $this->belongsTo(Protocol::class);
    }
}
