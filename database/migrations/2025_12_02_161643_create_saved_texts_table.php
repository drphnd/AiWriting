<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // specific command: Schema::create (NOT Schema::table)
        Schema::create('saved_texts', function (Blueprint $table) {
            $table->id();
            
            // This is the new line we needed (Links to the User)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->text('original_text');
            $table->text('generated_text');
            $table->string('type')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('saved_texts');
    }
};