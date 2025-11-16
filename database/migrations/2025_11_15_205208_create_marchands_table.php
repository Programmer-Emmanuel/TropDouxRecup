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
        Schema::create('marchands', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nom_marchand');
            $table->string('email_marchand')->unique();
            $table->string('tel_marchand')->unique();
            $table->string('image_marchand')->nullable();
            $table->integer('solde_marchand')->default(0);
            $table->string('password_marchand');
            $table->string('code_otp')->nullable();
            $table->boolean('is_verify')->default(false);
            $table->dateTime('otp_expire_at')->nullable();

            $table->uuid('id_commune')->nullable();
            
            $table->uuid('id_abonnement')->nullable();

            $table->foreign('id_commune')
                ->references('id')
                ->on('communes')
                ->onDelete('set null');
                
            $table->foreign('id_abonnement')
                ->references('id')
                ->on('abonnements')
                ->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marchands');
    }
};
