<?php 
// app/Rules/NotInProgress.php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Album;

class NotInProgress implements Rule
{
    public function passes($attribute, $value)
    {
       // Check if the record exists
       $record = Album::find($value);

       // If the record doesn't exist, or its status is not in progress, return true
       return !$record || $record->status !== 'in_progress';
    }

    public function message()
    {
        return 'The record is in progress and cannot be updated.';
    }
}
