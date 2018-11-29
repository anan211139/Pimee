<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //php artisan migrate:reset
        //php artisan migrate:fresh
        //php artisan db:seed

        Eloquent::unguard();

        //call uses table seeder class
        $this->call([
            ExamTableSeeder::class,
            PrincipleTableSeeder::class,
            PrizesTableSeeder::class,
            SponsorTableSeeder::class
        ]);
        //this message shown in your terminal after running db:seed command
        $this->command->info("Exam & Principle table seeded :)");

        //other fixed table
        DB::table('chapters')->delete();
        DB::table('chapters')->insert([
          ['subject_id' => 1, 'name' => 'สมการ'],
          ['subject_id' => 1, 'name' => 'ห.ร.ม./ค.ร.ม.'],
          ['subject_id' => 2, 'name' => 'Tenses'],
          ['subject_id' => 2, 'name' => 'Comparison'],
          ['subject_id' => 2, 'name' => 'Articles'],
          ['subject_id' => 2, 'name' => 'If clauses'],
          ['subject_id' => 2, 'name' => 'Connective'],
          ['subject_id' => 1, 'name' => 'ตัวประกอบของจำนวนนับ'],
          ['subject_id' => 1, 'name' => 'มุมและส่วนของเส้นตรง'],
          ['subject_id' => 1, 'name' => 'เส้นขนาน'],
          ['subject_id' => 1, 'name' => 'ทิศและแผนผัง'],
          ['subject_id' => 1, 'name' => 'ทศนิยม'],
          ['subject_id' => 1, 'name' => 'รูปสี่เหลี่ยม'],
          ['subject_id' => 1, 'name' => 'รูปวงกลม'],
          ['subject_id' => 1, 'name' => 'รูปเรขาคณิตสามมิติ'],
          ['subject_id' => 1, 'name' => 'ปริมาตรของทรงสี่เหลี่ยมมุมฉาก'],
          ['subject_id' => 1, 'name' => 'สถิติ และ ความน่าจะเป็นเบื้องต้น ']
        ]);
        DB::table('subjects')->delete();
        DB::table('levels')->delete();
        DB::table('types')->delete();
        DB::table('codes')->delete();
        DB::table('subjects')->insert([['name' => 'วิชาคณิตศาสตร์'],['name' => 'วิชาภาษาอังกฤษ']]);
        DB::table('levels')->insert([['name' => 'easy'],['name' => 'medium'],['name' => 'hard']]);
        DB::table('types')->insert([['name' => 'code'],['name' => 'delivery']]);
        DB::table('codes')->insert([['prize_id' => 1,'code' => 'hitherethisisacat'],['prize_id' => 1,'code' => 'a8e3f3f'],
                                    ['prize_id' => 1,'code' => 'asdfghj'],['prize_id' => 2,'code' => 'khunWARI'],
                                    ['prize_id' => 2,'code' => 'khunCHAMILK'],['prize_id' => 3,'code' => 'khunJIAE'],
                                    ['prize_id' => 3,'code' => 'khunICE'],['prize_id' => 4,'code' => 'khunANAN'],
                                    ['prize_id' => 4,'code' => 'khunPEI'],['prize_id' => 5,'code' => 'khunOAT'],
                                    ['prize_id' => 5,'code' => 'khunTON']]);

        DB::table('typereports')->delete();
        DB::table('typereports')->insert([
          ['name' => 'aboutexam'],
          ['name' => 'aboutchatbot'],
          ['name' => 'aboutwebsite']
        ]);
    }
}