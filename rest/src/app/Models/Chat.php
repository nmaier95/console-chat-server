<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Chat extends Model
{
    protected $table = 'chat';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'message',
        'user_id',
        'chat_room_id'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['user_id', 'updated_at'];

    /**
     *
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo('App\Models\ChatUser');//, 'user_id', 'id', 'user');//, 'chat_user');
    }
}
