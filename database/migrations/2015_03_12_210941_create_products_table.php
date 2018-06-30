<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Antvel\Product\Attributes;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id')->unsigned();
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->boolean('status')->default(1);
            $table->enum('type', Attributes::make('type')->keys())->default('item');
            $table->string('name', 100);
            $table->string('description', 500);
            $table->integer('cost');
            $table->integer('price');
            $table->integer('stock')->default(1);
            $table->integer('low_stock')->default(0);
            $table->string('bar_code', 100)->nullable();
            $table->string('brand', 50)->nullable();
            $table->enum('condition', Attributes::make('condition')->keys())->default('new');
            $table->mediumText('tags')->nullable();
            $table->json('features')->nullable();
            $table->double('rate_val', 10, 2)->default(0)->nullable();
            $table->integer('rate_count')->default(0)->nullable();
            $table->integer('sale_counts')->default(0)->unsigned();
            $table->integer('view_counts')->default(0)->unsigned();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('category_id')->references('id')->on('categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
