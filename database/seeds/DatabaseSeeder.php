<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            // seedEmbedVideoProduct::class,
        ]);
    }
}


// const text = "The string that I want to truncate!";

// const truncate = (str, len) => str.substring(0, (str + ' ').lastIndexOf(' ', len));

// console.log(truncate(text, 14));
