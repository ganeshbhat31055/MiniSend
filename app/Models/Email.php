<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    use HasFactory;

    protected $fillable = ['to','from','subject','status'];

    public function body(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(EmailBody::class);
    }

    public function attachments()
    {
        return $this->hasMany(EmailAttachments::class);
    }
}
