<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatTable extends Migration
{
    public static string $tableName = 'chat';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this::$tableName, function (Blueprint $table) {
            $table->string('message');

            $table->foreignId('user_id')
                ->constrained(CreateChatUsers::$tableName);

            $table->foreignId('chat_room_id')
                ->constrained(CreateChatRooms::$tableName)
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this::$tableName);
    }
}
