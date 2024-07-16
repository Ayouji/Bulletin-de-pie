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
        Schema::create('bulletins', function (Blueprint $table) {
            $table->id();
            $table->float('salaire_base');
            $table->float('tcotisPatron');
            $table->float('tcotisSalarier');
            $table->float('total_heure');
            $table->float('import_revenu');
            $table->float('total_net_salary');
            $table->float('total_brut_salary');
            $table->foreignId('salarier_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bulletins');
    }
};
