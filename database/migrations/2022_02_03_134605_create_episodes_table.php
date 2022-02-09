<?php

use App\Models\Podcast;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEpisodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $podcast = new Podcast();
        $podcast_id = $podcast->getKeyName();
        $podcast_table = $podcast->getTable();

        Schema::create('episodes', function (Blueprint $table) use ($podcast_table, $podcast_id) {
            $table->id();
            $table->string('title', 100);
            $table->mediumText('description')->nullable()->default(null);
            $table->float('duration')->default(0);
            $table->foreignIdFor(Podcast::class, 'podcast_id')
                ->index('student_invoice_item_student')
                ->constrained($podcast_table, $podcast_id);
            $table->boolean('published')->default(true);
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
        Schema::dropIfExists('episodes');
    }
}
