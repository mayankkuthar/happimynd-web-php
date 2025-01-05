<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Storage;

class HappiselfLibraryContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'happiself_library_id',
        'content_type',
        'content',
        'deleted_at',
    ];


    public function getContentAttribute($value){
        return Storage::url(config('constants.mediaAssets.happiself_library.folderName').''.$value);
    }
    
}
