<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MenuItem;

class MenuItemSeeder extends Seeder
{
    public function run()
    {
        // Opciones principales
        $admisiones = MenuItem::create([
            'parent_id' => null,
            'title' => 'Inscripciones',
            'description' => 'Información sobre inscripciones',
            'response' => null,
            'order_index' => 1,
        ]);

        $programas = MenuItem::create([
            'parent_id' => null,
            'title' => 'Programas Académicos',
            'description' => 'Consulta sobre programas de estudio',
            'response' => null,
            'order_index' => 2,
        ]);

        $correo = MenuItem::create([
            'parent_id' => null,
            'title' => 'Correo electrónico',
            'description' => 'Información, preguntas frecuentes o dudas relacionadas con el correo electrónico institucional',
            'response' => null,
            'order_index' => 3,
        ]);

        $contacto = MenuItem::create([
            'parent_id' => null,
            'title' => 'Contacto',
            'description' => 'Información de contacto',
            'response' => 'Puedes contactarnos al correo: contacto@unicatolica.edu.co o al teléfono: +57 123 456 789',
            'order_index' => 4,
        ]);

        // Submenús de inscripciones
        MenuItem::create([
            'parent_id' => $admisiones->id,
            'title' => 'Pregrado',
            'description' => 'Link para inscribirse a pregrado',
            'response' => 'Perfecto! puedes inscribirte a pregrado a través del siguiente enlace: https://inscripciones.unicatolica.edu.co/inscripciones/public/instructivo/pregrado',
            'order_index' => 1,
        ]);

        MenuItem::create([
            'parent_id' => $admisiones->id,
            'title' => 'Posgrado',
            'description' => 'Link para inscribirse a posgrado',
            'response' => 'Perfecto! puedes inscribirte a pregrado a través del siguiente enlace: https://inscripciones.unicatolica.edu.co/inscripciones/public/formulario/2',
            'order_index' => 2,
        ]);

        MenuItem::create([
            'parent_id' => $admisiones->id,
            'title' => 'Educación Continua',
            'description' => 'Link para inscribirse a educación continua',
            'response' => 'Perfecto! puedes inscribirte a pregrado a través del siguiente enlace: https://inscripciones.unicatolica.edu.co/inscripciones/public/formulario/3',
            'order_index' => 3,
        ]);

        // Submenús de correo electronico
        MenuItem::create([
            'parent_id' => $correo->id,
            'title' => 'Recuperar Contraseña',
            'description' => 'Instrucciones para recuperar la contraseña del correo institucional',
            'response' => 'Para recuperar tu contraseña, visita: https://correo.unicatolica.edu.co/recuperar-contraseña y sigue las instrucciones proporcionadas.',
            'order_index' => 1,
        ]);
    }
}

