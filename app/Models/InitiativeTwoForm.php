<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InitiativeTwoForm extends Model
{
    use HasFactory;
    protected $fillable = [
        // Organization fields
        'organization_name', 'organization_city', 'organization_state', 'organization_email',
        'organization_website', 'organization_established_year', 'organization_is_active',
        'organization_presentation_type', 'organization_presentation_frequency',
        'organization_participation_method',
        'organization_year',
        'organization_age',

        // Former Debutante fields
        'debutante_name_at_presentation', 'debutante_escort_name', 'debutante_year_presented',
        'debutante_sponsoring_organization', 'debutante_city', 'debutante_state',

        // Former Beau fields
        'beau_name_at_presentation', 'beau_escort_name', 'beau_year_presented',
        'beau_sponsoring_organization', 'beau_city', 'beau_state','user_id',
    ];
    public function users()
    {
        return $this->belongsTo(User::class,'user_id','id');

    }
}
