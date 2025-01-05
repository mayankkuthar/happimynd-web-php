<?php

namespace App\Models;

use App\Models\Assessment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AssessmentApprove extends Model
{
    use HasFactory, SoftDeletes;


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'assessment_approves';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status',
        'available_date',
        'slot',
        'assessment_id',
        'call_option'
    ];

    public function assessment()
    {
        return $this->belongsTo(Assessment::class, 'assessment_id');
    }

    public function callOption(){
        if ($this->call_option == 2)
        {
            return 'Zoom Call';
        }
        return 'Phone Call';
    }
}
