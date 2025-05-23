<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TaxCalculation;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaxHistory extends Model
{

    use HasFactory;
    protected $table = 'tax_history';

   protected $fillable = [
    'tax_calculation_id',
    'user_id',
    'action',
    'previous_values',
    'new_values',
];

protected $casts = [
    'previous_values' => 'array',
    'new_values' => 'array',
];


    /**
     * RELACJA DO TAX CALCULATION - POPRAWIONA WERSJA
     */
    public function taxCalculation()
    {
        return $this->belongsTo(TaxCalculation::class, 'tax_calculation_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Akcesor zwracający sformatowane wartości z previous_values.
     *
     * @return string
     */
    public function getFormattedPreviousValuesAttribute()
    {
        if (!is_array($this->previous_values)) {
            return '';
        }

        $values = $this->previous_values;
        $formatted = [];

        if (isset($values['tax_amount'])) {
            $formatted[] = 'Kwota podatku: ' . number_format($values['tax_amount'], 2, ',', ' ') . ' zł';
        }

        if (isset($values['income'])) {
            $formatted[] = 'Przychód: ' . number_format($values['income'], 2, ',', ' ') . ' zł';
        }

        if (isset($values['expenses'])) {
            $formatted[] = 'Koszty: ' . number_format($values['expenses'], 2, ',', ' ') . ' zł';
        }

        if (isset($values['deductions'])) {
            $formatted[] = 'Ulgi: ' . number_format($values['deductions'], 2, ',', ' ') . ' zł';
        }

        if (isset($values['tax_type'])) {
            $formatted[] = 'Typ podatku: ' . ucfirst($values['tax_type']);
        }

        if (isset($values['children'])) {
            $formatted[] = 'Dzieci: ' . $values['children'];
        }

        if (isset($values['is_married'])) {
            $formatted[] = 'Małżeństwo: ' . ($values['is_married'] ? 'Tak' : 'Nie');
        }

        return implode(', ', $formatted);
    }

    /**
     * Mutator zapisujący previous_values jako JSON, jeśli przekazano tablicę.
     *
     * @param array|string $value
     * @return void
     */
    public function setPreviousValuesAttribute($value)
    {
        $this->attributes['previous_values'] = is_array($value)
            ? json_encode($value)
            : $value;
    }
}
