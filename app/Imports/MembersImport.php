<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;

class MembersImport implements ToCollection, SkipsEmptyRows, WithValidation
{
    /**
    * @param Collection $collection
    */
    use Importable;
    public function collection(Collection $rows)
    {
      return $rows;
    }

    public function rules(): array
    {
        return [
            '*.*' => ['required', 'email', 'unique:users,email'],
        ];
    }
}