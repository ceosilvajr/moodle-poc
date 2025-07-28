<?php
// app/Models/UserMoodleToken.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $mobile_user_id
 * @property string $moodle_token
 * @property int|null $moodle_user_id
 * @property string|null $moodle_username
 */
class UserMoodleToken extends Model
{
    use HasFactory;

    // The primary key is 'mobile_user_id' and it's not an auto-incrementing integer.
    protected $primaryKey = 'mobile_user_id';
    public $incrementing = false;
    protected $keyType = 'string';

    // The table associated with the model.
    protected $table = 'user_moodle_tokens';

    // The attributes that are mass assignable.
    protected $fillable = [
        'mobile_user_id',
        'moodle_token',
        'moodle_user_id',
        'moodle_username',
    ];

    // The attributes that should be hidden for serialization.
    protected $hidden = [
        'moodle_token', // Hide token when converting to JSON for security
    ];
}
