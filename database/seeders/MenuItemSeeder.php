<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MenuItem;

class MenuItemSeeder extends Seeder
{
    public function run(): void
    {
       // Menú principal
        $inscripciones = MenuItem::create([
            'label' => 'Inscripciones',
            'tag' => 'inscripciones',
        ]);

        // Submenús
        MenuItem::create([
            'label' => 'Pregrado',
            'tag' => 'inscripciones-pregrado',
            'parent_id' => $inscripciones->id,
            'respuesta' => 'Puedes inscribirte en programas de pregrado a través del siguiente enlace:
            <a href="https://inscripciones.unicatolica.edu.co/inscripciones/public/instructivo/pregrado">Inscripciones Pregrado</a>'
        ]);

        MenuItem::create([
            'label' => 'Posgrado',
            'tag' => 'inscripciones-posgrado',
            'parent_id' => $inscripciones->id,
            'respuesta' => 'Puedes inscribirte en posgrado a través del siguiente enlace:
            <a href="https://inscripciones.unicatolica.edu.co/inscripciones/public/formulario/2">Inscripciones Posgrado</a>'
        ]);

        MenuItem::create([
            'label' => 'Educación continua',
            'tag' => 'inscripciones-continua',
            'parent_id' => $inscripciones->id,
            'respuesta' => 'Puedes inscribirte en programas de educación continua a través del siguiente enlace:
            <a href="https://inscripciones.unicatolica.edu.co/inscripciones/public/formulario/3">Inscripciones Educación Continua</a>'
        ]);
    }
}



