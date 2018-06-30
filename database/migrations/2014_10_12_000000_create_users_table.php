<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Antvel\Users\Policies\Roles;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name', 60);
            $table->string('last_name', 60);
            $table->string('nickname', 60)->unique();
            $table->string('email', 100)->unique();
            $table->string('password', 60);
            $table->enum('role', Roles::allowed())->default(Roles::default());
            $table->string('phone_number', 20)->nullable();
            $table->enum('gender', ['female', 'male'])->default('male');
            $table->date('birthday')->nullable();
            $table->text('image')->nullable();
            $table->string('facebook', 100)->nullable();
            $table->string('twitter', 100)->nullable();
            $table->string('website', 100)->nullable();
            $table->string('language', 10)->default('en');
            $table->string('time_zone', 60)->nullable();
            $table->string('description', 150)->nullable();
            $table->integer('rate_val')->nullable();
            $table->integer('rate_count')->nullable();
            $table->json('preferences')->nullable()->default(null);
            $table->boolean('verified')->default(false);
            $table->string('confirmation_token', 60)->nullable();

            $table->rememberToken();
            $table->timestamps();
            $table->timestamp('disabled_at')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
