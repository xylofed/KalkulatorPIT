<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncomeSource extends Model
{
    public function calculations() {
        return $this->hasMany(Calculation::class);
    }
    public function user()
{
    return $this->belongsTo(User::class);
}

public function taxCalculations()
{
    return $this->hasMany(TaxCalculation::class);
}


}
