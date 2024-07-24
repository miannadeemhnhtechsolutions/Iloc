<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewSubscriptionPlan extends Model
{
    use HasFactory;
    protected $guarded=[];
    public function plan()
    {
        return $this->belongsTo(NewPlan::class, 'new_plan_id','id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'organization_email','email');
    }
    public function payment_details()
    {
        return $this->hasMany(NewPaymentMethod::class,'email','email');

    }
}
