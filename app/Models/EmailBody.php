<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailBody extends Model
{
    use HasFactory;

    protected $fillable = ['email_id','body','html_body'];
}
