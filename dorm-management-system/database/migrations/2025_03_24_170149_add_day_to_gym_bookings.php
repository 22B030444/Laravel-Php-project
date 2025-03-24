<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('gym_bookings', function (Blueprint $table) {
            $table->string('day')->after('sport');
        });
    }

    public function down()
    {
        Schema::table('gym_bookings', function (Blueprint $table) {
            $table->dropColumn('day');
        });
    }

};
