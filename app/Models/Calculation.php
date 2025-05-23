<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Calculation extends Model
{
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function incomeSource() {
        return $this->belongsTo(IncomeSource::class);
    }


}
