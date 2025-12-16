<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MenuItem;

class MenuItemSeeder extends Seeder
{
    public function run()
    {
        //==============================
        // Opciones Iniciales del Menú
        //==============================

        //1. Incripciones
        $inscripciones = MenuItem::create([
            'parent_id' => null,
            'title' => 'inscripciones',
            'description' => 'informacion sobre inscripciones',
            'response' => null,
            'order_index' => 1,
        ]);


        //2. Estudiantes
        $estudiantes = MenuItem::create([
            'parent_id' => null,
            'title' => 'Soy estudiante',
            'description' => 'Rol de estudiante, que el usuario es un estudiante',
            'response' => null,
            'order_index' => 2,
        ]);


        //3. Docentes
        $docentes = MenuItem::create([
            'parent_id' => null,
            'title' => 'Soy docente',
            'description' => 'Rol de docente, que el usuario es un docente',
            'response' => null,
            'order_index' => 3,
        ]);


        //4. Egresados
        $egresados = MenuItem::create([
            'parent_id' => null,
            'title' => 'Soy egresado',
            'description' => 'Rol de egresado, que el usuario es un egresado',
            'response' => null,
            'order_index' => 4,
        ]);



        //==============================
        // 1.1 Submenú de inscripciones
        //==============================

        MenuItem::create([
            'parent_id' => $inscripciones->id,
            'title' => 'Pregrado',
            'description' => 'Link para inscribirse a pregrado',
            'response' => 'Perfecto! puedes inscribirte a pregrado a través del siguiente enlace: https://inscripciones.unicatolica.edu.co/inscripciones/public/instructivo/pregrado',
            'order_index' => 1,
        ]);

        MenuItem::create([
            'parent_id' => $inscripciones->id,
            'title' => 'Posgrado',
            'description' => 'Link para inscribirse a posgrado',
            'response' => 'Perfecto! puedes inscribirte a pregrado a través del siguiente enlace: https://inscripciones.unicatolica.edu.co/inscripciones/public/formulario/2',
            'order_index' => 2,
        ]);

        MenuItem::create([
            'parent_id' => $inscripciones->id,
            'title' => 'Educación Continua',
            'description' => 'Link para inscribirse a educación continua',
            'response' => 'Perfecto! puedes inscribirte a pregrado a través del siguiente enlace: https://inscripciones.unicatolica.edu.co/inscripciones/public/formulario/3',
            'order_index' => 3,
        ]);



        //==============================
        // 2.1 Submenú de estudiantes
        //==============================
        $banner_estudiantes =  MenuItem::create([
            'parent_id' => $estudiantes->id,
            'title' => 'Banner',
            'description' => 'Informacion o dudas sobre el aplicativo banner de los estudiantes',
            'response' => null,
            'order_index' => 1,
        ]);

        $correo_estudiantes = MenuItem::create([
            'parent_id' => $estudiantes->id,
            'title' => 'Correo Electrónico',
            'description' => 'Informacion o dudas relacionadas con correo electoronico institucional de los estudiantes',
            'response' => null,
            'order_index' => 2,
        ]);

       



        //==============================
        // 2.1.1 Submenú de banner
        //==============================
        MenuItem::create([
            'parent_id' => $banner_estudiantes->id,
            'title' => 'Matrículas',
            'description' => 'Informacion o dudas sobre matrículas de los estudiantes',
            'response' => null,
            'order_index' => 1,
        ]);

        MenuItem::create([
            'parent_id' => $banner_estudiantes->id,
            'title' => 'Horarios',
            'description' => 'video relacionado con horarios de los estudiantes',
            'response' => 'Muy bien! Para consultar tus horarios, puedes ver el siguiente video y seguir todas las instrucciones: https://www.youtube.com/watch?v=BuUbTRHuvAw',
            'order_index' => 2,
        ]);

        MenuItem::create([
            'parent_id' => $banner_estudiantes->id,
            'title' => 'Subir/Consultar notas',
            'description' => 'video relacionado con subir o consultar notas de los estudiantes',
            'response' => 'Muy bien! Para subir o consultar tus notas, puedes ver el siguiente video: https://www.youtube.com/watch?v=BuUbTRHuvAw',
            'order_index' => 3,
        ]);


        MenuItem::create([
            'parent_id' => $banner_estudiantes->id,
            'title' => 'Olvide mi contraseña',
            'description' => 'Link para recuperar la contraseña del banner',
            'response' => 'Para recuperar tu contraseña, visita: https://login.unicatolica.edu.co/accountrecoveryendpoint/recoveraccountrouter.do?Name=PreLoginRequestProcessor&TARGET=https://servicioestudiantes.unicatolica.edu.co/StudentSelfService/login/cas&commonAuthCallerPath=%2Fcas%2Flogin&forceAuth=true&passiveAuth=false&tenantDomain=carbon.super&sessionDataKey=3b371158-bab2-48be-a537-6fcb0fa2b896&relyingParty=FACULTY-STUDENT-GENERAL&type=cas&sp=FACULTY-STUDENT-GENERAL&isSaaSApp=false&authenticators=BasicAuthenticator:LOCAL&isUsernameRecovery=false&callback=https%3A%2F%2Flogin.unicatolica.edu.co%3A443%2Fauthenticationendpoint%2Flogin.do%3FName%3DPreLoginRequestProcessor%26TARGET%3Dhttps%3A%2F%2Fservicioestudiantes.unicatolica.edu.co%2FStudentSelfService%2Flogin%2Fcas%26commonAuthCallerPath%3D%252Fcas%252Flogin%26forceAuth%3Dtrue%26passiveAuth%3Dfalse%26tenantDomain%3Dcarbon.super%26sessionDataKey%3D3b371158-bab2-48be-a537-6fcb0fa2b896%26relyingParty%3DFACULTY-STUDENT-GENERAL%26type%3Dcas%26sp%3DFACULTY-STUDENT-GENERAL%26isSaaSApp%3Dfalse%26authenticators%3DBasicAuthenticator%3ALOCAL, ingresa tu nombre de usuario y sigue las instrucciones proporcionadas.',
            'order_index' => 4,
        ]);

         MenuItem::create([
            'parent_id' => $banner_estudiantes->id,
            'title' => 'Contactar con un asesor',
            'description' => 'Opción para contactar con un asesor',
            'response' => null,
            'order_index' => 5,
        ]);



        //==============================
        // 2.1.2 Submenú de correo electronico
        //==============================
        MenuItem::create([
            'parent_id' => $correo_estudiantes->id,
            'title' => 'Cambio de contraseña',
            'description' => 'Link para cambiar la contraseña del correo institucional',
            'response' => null,
            'order_index' => 1,
        ]);

        MenuItem::create([
            'parent_id' => $correo_estudiantes->id,
            'title' => '¿Como accedo a mi correo institucional?',
            'description' => 'Guia para acceder al correo institucional',
            'response' => null,
            'order_index' => 2,
        ]);

        MenuItem::create([
            'parent_id' => $correo_estudiantes->id,
            'title' => 'Solicitar o recuperar contraseña',
            'description' => 'guia para solicitar o recuperar la contraseña del correo institucional',
            'response' => null,
            'order_index' => 3,
        ]);

        MenuItem::create([
            'parent_id' => $correo_estudiantes->id,
            'title' => 'Contactar con un asesor',
            'description' => 'Opción para contactar con un asesor',
            'response' => null,
            'order_index' => 4,
        ]);

    }
}

