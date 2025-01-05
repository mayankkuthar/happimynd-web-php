<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DurationType extends Model
{
    use HasFactory;
    protected $fillable = [
        'type',
        'value',
        'frequency',
    ];

    public const TYPES = [
        'onetime' => 1,
        'session' => 2,
        'year' => 3,
        'month' => 4,
    ];

    public const TYPES_ACCESS = [
        1 => 'Onetime pay',
        2 => 'session',
        3 => 'year access',
        4 => 'month access',
    ];

    public function getNameAttribute()
    {
        if ($this->attributes['type'] == 1) {
            return self::TYPES_ACCESS[$this->attributes['type']];
        }
        return $this->attributes['frequency'] . ' ' . self::TYPES_ACCESS[$this->attributes['type']];
    }

    public function printType()
    {
        return self::TYPES_ACCESS[$this->attributes['type']];
    }

    public function scopeOfTypeYear($query)
    {
        return $query->where('type', self::TYPES['year']);
    }

    public function scopeOfTypeSession($query, $noOfSession)
    {
        return $query->where('type', self::TYPES['session'])->where('frequency', $noOfSession);
    }

    public function scopeOfSessionType($query)
    {
        return $this->where('type', self::TYPES['session']);
    }

    public function scopeOfTypeOnetimePay($query)
    {
        return $query->where('type', self::TYPES['onetime']);
    }
}
