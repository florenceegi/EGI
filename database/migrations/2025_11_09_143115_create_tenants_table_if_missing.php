<?php



declare(strictotypes=1);



use Illuminate\Database\Migrations\Migration;



use Illuminate\Database\Schema\Blueprint;



use Illuminate\Support\Facades\Schema;





return new class extends Migration {



    /**



     * @purpose Ensure tenants table exists on environments where NATAN_LOC migrations are not deployed



     */



    public function up(): void



    {



        if (Schema::hasTable('tenants')) {



            return;



        }





        Schema::create('tenants', function (Blueprint $table) {



            $table->bigIncrements('id');



            $table->string('name');



            $table->string('slug')->unique();



            $table->string('code')->nullable()->unique();



            $table->enum('entity_type', ['pa', 'company', 'public_entity', 'other'])->default('pa');



            $table->string('email')->nullable();



            $table->string('phone')->nullable();



            $table->text('address')->nullable();



            $table->string('vat_number')->nullable();



            $table->json('settings')->nullable();



            $table->boolean('is_active')->default(true)->index();



            $table->timestamp('trial_ends_at')->nullable();



            $table->timestamp('subscription_ends_at')->nullable();



            $table->text('notes')->nullable();



            $table->timestamps();



            $table->json('data')->nullable();



            $table->timestamp('deleted_at')->nullable();



        });



    }





    public function down(): void



    {



        // Non rimuoviamo la tabella in down per evitare di cancellare dati



        // Schema::dropIfExists('tenants');



    }



};
