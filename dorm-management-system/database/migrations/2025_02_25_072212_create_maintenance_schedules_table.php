<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('maintenance_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->date('scheduled_date');
            $table->text('description');
            $table->string('status')->default('Scheduled');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('maintenance_schedules');
    }
};
