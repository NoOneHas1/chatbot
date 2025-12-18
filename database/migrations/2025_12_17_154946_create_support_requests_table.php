<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('support_requests', function (Blueprint $table) {
             $table->id();

            // Identifica al usuario anónimo (chat)
            $table->string('session_id')->index();

            // Estados: waiting | active | closed
            $table->string('status')->default('waiting')->index();

            // Asesor asignado (users.id)
            $table->foreignId('advisor_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // Último mensaje del usuario (para timeout)
            $table->timestamp('last_user_message_at')->nullable();

            $table->timestamps();
            $table->timestamp('closed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_requests');
    }
};
