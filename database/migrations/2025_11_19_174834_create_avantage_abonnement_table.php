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
        Schema::create('avantage_abonnement', function (Blueprint $table) {
            $table->uuid('id_abonnement');
            $table->uuid('id_avantage');

            $table->foreign('id_abonnement')
                ->references('id')->on('abonnements')
                ->onDelete('cascade');

            $table->foreign('id_avantage')
                ->references('id')->on('avantages')
                ->onDelete('cascade');

            $table->primary(['id_abonnement', 'id_avantage']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avantage_abonnement');
    }
};
