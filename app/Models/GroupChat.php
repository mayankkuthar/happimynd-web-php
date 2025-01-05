<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupChat extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_unread_message',
        'psychologist_id',
        'psychologist_unread_message',
        'group_id',
        'last_message_deliver_at',
        'group_active_for_chat',
        'assigned_date_time',
        'language',
    ];  


    protected $appends = ['type' , 'organization'];

    public function getTypeAttribute(){
        $user_detail = User::where('id' ,  $this->user_id)->first();
        if($user_detail->isOrganizationUser()){
            return 'B2B';
        }else{
            return 'B2C';
        }
    }


    public function getOrganizationAttribute(){
        $user_detail = User::where('id' ,  $this->user_id)->first();
        if($user_detail->isOrganizationUser()){
            return $user_detail->userToken->token->organization()->withTrashed()->first()->name;
        }else{
            return 'Individual';
        }
    }


    public function user(){
        return $this->belongsTo(User::class)->select('id','username','nickname','email' , 'gender') ;
    }

    public function psychologist(){
        return $this->belongsTo(Psychologist::class);
    }

    
}
