<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('categories')->delete();
        
        \DB::table('categories')->insert(array (
            0 => 
            array (
                'name' => 'ด้านองค์กรและการบริหารองค์กร',
                'max_score' => 10.0,
                'standard_id' => 1,
            ),
            1 => 
            array (
                'name' => 'ด้านบุคลากร',
                'max_score' => 20.0,
                'standard_id' => 1,
            ),
            2 => 
            array (
                'name' => 'ด้านการจัดการศึกษา',
                'max_score' => 150.0,
                'standard_id' => 1,
            ),
            3 => 
            array (
                'name' => 'ด้านการวิจัยและนวัตกรรมและผลผลิตทางวิชาการ',
                'max_score' => 5.0,
                'standard_id' => 1,
            ),
            4 => 
            array (
                'name' => 'ด้านการบริการวิชาการ/วิชาชีพแก่สังคม',
                'max_score' => 5.0,
                'standard_id' => 1,
            ),
            5 => 
            array (
                'name' => 'ด้านการทำนุบำรุงศิลปะและวัฒนธรรม',
                'max_score' => 5.0,
                'standard_id' => 1,
            ),
            6 => 
            array (
                'name' => 'ด้านนิสิตและนักศึกษา',
                'max_score' => 5.0,
                'standard_id' => 1,
            ),
            7 => 
            array (
                'name' => 'ด้านองค์กรและการบริหารองค์กร',
                'max_score' => 70.0,
                'standard_id' => 2,
            ),
            8 => 
            array (
                'name' => 'ด้านบุคลากร',
                'max_score' => 50.0,
                'standard_id' => 2,
            ),
            9 => 
            array (
                'name' => 'ด้านการจัดการศึกษา',
                'max_score' => 70.0,
                'standard_id' => 2,
            ),
            10 => 
            array (
                'name' => 'ด้านการวิจัยและนวัตกรรมและผลผลิตทางวิชาการ',
                'max_score' => 20.0,
                'standard_id' => 2,
            ),
            11 => 
            array (
                'name' => 'ด้านการบริการวิชาการ/วิชาชีพแก่สังคม',
                'max_score' => 30.0,
                'standard_id' => 2,
            ),
            12 => 
            array (
                'name' => 'ด้านการทำนุบำรุงศิลปะและวัฒนธรรม',
                'max_score' => 10.0,
                'standard_id' => 2,
            ),
            13 => 
            array (
                'name' => 'ด้านนิสิตและนักศึกษา',
                'max_score' => 40.0,
                'standard_id' => 2,
            ),
            14 => 
            array (
                'name' => 'ด้านองค์กรและการบริหารองค์กร',
                'max_score' => 25.0,
                'standard_id' => 3,
            ),
            15 => 
            array (
                'name' => 'ด้านบุคลากร',
                'max_score' => 30.0,
                'standard_id' => 3,
            ),
            16 => 
            array (
                'name' => 'ด้านการจัดการศึกษา',
                'max_score' => 75.0,
                'standard_id' => 3,
            ),
            17 => 
            array (
                'name' => 'ด้านการวิจัยและนวัตกรรมและผลผลิตทางวิชาการ',
                'max_score' => 45.0,
                'standard_id' => 3,
            ),
            18 => 
            array (
                'name' => 'ด้านการบริการวิชาการ/วิชาชีพแก่สังคม',
                'max_score' => 40.0,
                'standard_id' => 3,
            ),
            19 => 
            array (
                'name' => 'ด้านการทำนุบำรุงศิลปะและวัฒนธรรม',
                'max_score' => 15.0,
                'standard_id' => 3,
            ),
            20 => 
            array (
                'name' => 'ด้านนิสิตและนักศึกษา',
                'max_score' => 20.0,
                'standard_id' => 3,
            ),
        ));
        
        
    }
}