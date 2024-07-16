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
            Schema::create('salariers', function (Blueprint $table) {
                $table->unsignedBigInteger('id')->primary()->autoIncrement();
                $table->string('nom');
                $table->string('prenom');
                $table->string('tel', 10);
                $table->float('salaire_base');
                $table->enum('qualification',['Technicien', 'Technicien Specialise']);
                $table->string('emploi');
                $table->date('date_emboche');
                $table->timestamps();
            });
        }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salariers');
    }
};
