<?php

namespace App\Models;

use App\Models\City;
use App\Models\Region;
use App\Models\Country;
use App\Models\Department;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'address',
        'zip_code',
        'birthdate',
        'date_hired',
        // foreign keys
        'country_id',
        'region_id',
        'city_id',
        'department_id'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
