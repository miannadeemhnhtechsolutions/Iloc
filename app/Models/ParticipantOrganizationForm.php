<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParticipantOrganizationForm extends Model
{
    use HasFactory;
    protected $guarded=[];

    public function payment()
    {
        return $this->hasOne(NewSubscriptionPlan::class,'email','organization_email');
    }
}
