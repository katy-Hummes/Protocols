<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Protocol extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    protected $fillable = [
        'department_id', 
        'people_id',
        'description',
        'date',
        'term',
    ];

    public function docattachs()
    {
        return $this->hasMany(DocAttach::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function people()
    {
        return $this->belongsTo(People::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function latestReport() {
        return $this->hasOne(Report::class)->latest('updated_at');
    }
}
