<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class People extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    protected $fillable = [
        'name',
        'birth',
        'cpf',
        'sex',
        'city',
        'neighborhood',
        'street',
        'number',
        'complement',
    ];

    public function protocols()
    {
        return $this->hasMany(Protocol::class);
    }
}
