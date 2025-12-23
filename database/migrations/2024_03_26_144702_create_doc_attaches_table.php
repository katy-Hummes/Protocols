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
        Schema::create('doc_attaches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('protocol_id'); 
            $table->string('file');
            $table->timestamps();
            $table->foreign('protocol_id')->references('id')->on('protocols')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doc_attaches');
    }
};
