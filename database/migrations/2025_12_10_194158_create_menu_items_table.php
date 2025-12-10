<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuItemsTable extends Migration
{
    public function up()
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();

            // Texto visible en el botón
            $table->string('label');

            // Clave única para identificar la opción (para buscar BD o submenús)
            $table->string('tag')->unique();

            // Si tiene padre, es un submenú
            $table->unsignedBigInteger('parent_id')->nullable();

            // Relación al propio modelo
            $table->foreign('parent_id')->references('id')->on('menu_items')->onDelete('cascade');

            // Opcional: puedes guardar una respuesta directa aquí
            $table->longText('respuesta')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('menu_items');
    }
}
