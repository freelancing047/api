<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('client_name', 100);
            $table->text('address1')->nullable();
            $table->text('address2')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->string('phone_no1', 20)->nullable();
            $table->string('phone_no2', 20)->nullable();
            $table->string('zip', 20)->nullable();
            $table->date('start_validity')->nullable();
            $table->date('end_validity')->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Inactive');

            $table->timestamps();
            $table->softDeletes();
        });

        /* add foreign key constraint on users table */
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('client_id')->references('id')->on('clients');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /* drop foreignkey first before removing the table */
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
        });

        Schema::dropIfExists('clients');


    }
}
