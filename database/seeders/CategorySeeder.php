<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Category::create([
            "title"=>"Fright",
            "description"=>'comfortable and fast',
            "image"=>'ad.jpg'
        ]);

        Category::create([
            "title"=>"Cars",
            "description"=>'safety and fast',

            "image"=>'fg.jpg']);

            Category::create([
            "title"=>"Tours",
            "description"=>'enjoyment and recreation',

            "image"=>'kh.jpg']);

            Category::create([
            "title"=>"Hotel",
            "description"=>'luxury and extravagance',

            "image"=>'OIP.jpg']);


            }

}
