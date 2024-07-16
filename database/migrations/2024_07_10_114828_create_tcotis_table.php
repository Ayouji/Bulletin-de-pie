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
        Schema::create('tcotis', function (Blueprint $table) {
            $table->id();
            $table->float('cnss');
            $table->float('amo');
            $table->float('Pension');
            $table->float('AMOoblSol');
            $table->float('Taxpro');
            $table->float('Presfamil');
            $table->enum('type',['salarier', 'employeur']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tcotis');
    }
};
