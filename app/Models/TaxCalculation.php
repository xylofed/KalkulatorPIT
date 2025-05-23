<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxCalculation extends Model
{
    use HasFactory;

    protected $fillable = [
        'income',
        'expenses',
        'deductions',
        'tax_type',
        'children',
        'social_insurance',
        'health_insurance',
        'is_married',
        'taxable_income',
        'tax_amount',
        'user_id',
    ];

    // Automatyczne rzutowanie kolumn na odpowiednie typy
    protected $casts = [
        'is_married' => 'boolean',
        'income' => 'float',
        'expenses' => 'float',
        'deductions' => 'float',
        'children' => 'integer',
        'social_insurance' => 'float',
        'health_insurance' => 'float',
        'taxable_income' => 'float',
        'tax_amount' => 'float',
    ];

    // Relacja do modelu User
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    // Relacja do historii kalkulacji podatku
    public function history()
    {
        return $this->hasMany(TaxHistory::class);
    }
}
