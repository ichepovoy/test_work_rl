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
        
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('phone');
            $table->json('data');
            $table->timestamp('created_at');
            $table->string('status')->default('pending');
            $table->timestamp('updated_at')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
