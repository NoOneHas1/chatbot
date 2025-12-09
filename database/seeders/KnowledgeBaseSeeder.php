<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KnowledgeBase;

class KnowledgeBaseSeeder extends Seeder
{
    public function run(): void
    {
        KnowledgeBase::create([
            'titulo' => 'Préstamo de libros en biblioteca',
            'contenido' => 'Los estudiantes pueden solicitar libros por un periodo de 15 días, con opción de renovación si no hay reservas.',
            'tags' => 'biblioteca, prestamos, libros',
        ]);

        KnowledgeBase::create([
            'titulo' => 'Proceso de admisión',
            'contenido' => 'La inscripción a programas de pregrado se realiza en línea. Se debe diligenciar el formulario y adjuntar documentos escaneados.',
            'tags' => 'admisiones, inscripcion, pregrado',
        ]);

        KnowledgeBase::create([
            'titulo' => 'Plataforma académica',
            'contenido' => 'Los estudiantes pueden consultar notas, horarios y material de clase en la plataforma académica en línea.',
            'tags' => 'plataforma, notas, horarios',
        ]);

        KnowledgeBase::create([
            'titulo' => 'Correo institucional',
            'contenido' => 'Cada estudiante recibe un correo institucional con dominio @unicatolica.edu.co para comunicaciones oficiales.',
            'tags' => 'correo, institucional, estudiantes',
        ]);

        KnowledgeBase::create([
            'titulo' => 'Atención a estudiantes',
            'contenido' => 'La oficina de bienestar universitario ofrece asesoría psicológica, actividades culturales y deportivas.',
            'tags' => 'bienestar, estudiantes, asesorias',
        ]);
    }
}
