<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;

class UserImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        $collection->skip(1)->each(function ($row) {
            // Verificamos que los campos 'name' y 'email' existan
            if (isset($row[0]) && isset($row[1])) {
                $name = $row[0];
                $email = $row[1];

                // Generamos un password usando el nombre y lo hasheamos
                $password = Hash::make($name);

                // Usamos updateOrCreate para evitar duplicados según el email
                User::updateOrCreate(
                    ['email' => $email], // Campo único
                    [
                        'name' => $name,
                        'email' => $email,
                        'password' => $password,
                    ]
                );
            }
        });

    }
}
