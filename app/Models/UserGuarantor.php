<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserGuarantor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'guarantor_contact_name',
        'relationship_id',
        'guarantor_contact_number',
     ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function relationship()
    {
        return $this->belongsTo(Relationship::class);
    }
}
