<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->increments('id');
            $table->json('email')->unique();
            $table->json('mobile')->unique();
            $table->string('national_id');
            $table->string('preferred_language');
            $table->string('password');
            $table->string('remember_token');
            $table->string('status')->default('lead');
            $table->decimal('price', 12, 4)->nullable();
            $table->enum('gender', ['male', 'female', 'kid'])->default('male'); 
            $table->enum('channel', ['online', 'walkin', 'phone', 'dataentry', 'other']); 
            $table->enum('member_type', ['individual', 'corporate', 'staff'])->default('individual'); 
            $table->string('photo', 191)->nullable(); 
            $table->date('member_since')->nullable(); 
            $table->bigInteger('city_id')->unsigned()->nullable(); 
            $table->string('referred_by', 191)->nullable(); 
            $table->string('arabic_name', 191)->notNullable(); 
            $table->string('english_name', 191)->notNullable(); 
            $table->date('dob')->notNullable(); 
            $table->integer('organization_id')->unsigned()->nullable();
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->bigInteger('client_id')->unsigned()->nullable(); 
            $table->bigInteger('default_branch')->unsigned()->nullable(); 
            $table->string('parent_mobile', 191)->nullable(); 
            $table->string('parent_name', 191)->nullable(); 
            $table->string('referral_code', 191)->nullable(); 
            $table->string('fcm_token', 191)->nullable(); 
            $table->boolean('removed_by_member')->default(0); 
            $table->timestamps(); 
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
        Schema::dropIfExists('members');
    }
}
