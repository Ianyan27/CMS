<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ContactExistInAnyTable implements Rule {
    public function passes($attribute, $value)
    {
        return DB::table('contacts')->where('contact_pid', $value)->exists() ||
                DB::table('contact_archives')->where('contact_archive_pid', $value)->exists() ||
                DB::table('contact_discards')->where('contact_discard_pid', $value)->exists();
    }

    public function message()
    {
        return 'The selected contact PID does not exist in any valid table.';
    }
}
