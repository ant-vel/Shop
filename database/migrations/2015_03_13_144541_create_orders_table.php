<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('address_id')->unsigned()->nullable();
            $table->integer('seller_id')->unsigned()->nullable();
            $table->enum('status', ['sent', 'cancelled', 'closed', 'open', 'paid', 'pending', 'received']);
            $table->enum('type', ['cart', 'wishlist', 'order', 'later']);
            $table->string('description', 150)->nullable();
            $table->dateTime('end_date')->nullable(); //cancelled or paid
            $table->integer('rate')->nullable();
            $table->string('rate_comment', 250)->nullable();
            $table->boolean('rate_mail_sent')->default(false);
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('address_id')->references('id')->on('addresses');
            $table->foreign('seller_id')->references('id')->on('users');
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
        Schema::dropIfExists('orders');
    }
}
