<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->string('channel'); // sms, email, push
            $table->string('gateway'); // twilio, firebase, etc.
            $table->string('recipient');
            $table->text('content');
            $table->text('response')->nullable();
            $table->string('status'); // success, failed
            $table->text('error_message')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('notification_logs');
    }
};
