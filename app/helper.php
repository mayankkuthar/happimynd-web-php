<?php

use App\Models\DataContent;

function getCdnLink()
{
    $cdnlink = DataContent::where('title', 'cdnlink')->first();
    if(!$cdnlink){
        return '';
    }
    return $cdnlink->content;
}