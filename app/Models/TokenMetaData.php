<?php

namespace App\Models;

use App\Models\Token;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TokenMetaData extends Model
{
    use HasFactory;
    protected $fillable = ['meta_data','organization_id'];
    protected $casts = [
        'meta_data' => 'array'
    ];

    public function setMetaDataAttribute($data)
    {
        $this->attributes['meta_data'] = json_encode($data);
    }

    public function token(){
        return $this->hasMany(Token::class);
    }
}
