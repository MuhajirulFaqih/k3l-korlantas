<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Seeder;

class DemoNewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('news')->truncate();

        \App\Models\News::insert([
            [ 
                'id_post' => 1, 
                'date' => \Carbon\Carbon::now(), 
                'content' => 'Lorem, ipsum dolor sit amet consectetur adipisicing elit. Consequatur laboriosam ullam suscipit numquam tempora alias sit accusamus, quam voluptatem debitis dignissimos quidem quasi et nihil vel quaerat exercitationem officia. Repellendus.',
                'title' => 'Apa itu lorem ipsum', 
                'image' => null,
                'slug' => 'apa_itu_lorem_ipsum', 
                'categories' => 'Berita',
            ],
        ]);
    }
}
