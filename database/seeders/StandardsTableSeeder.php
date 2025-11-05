<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class StandardsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

       
        
        \DB::table('standards')->insert(array (
            0 => 
            array (
                'name' => 'มาตรฐานโครงสร้าง',
            ),
            1 => 
            array (
                'name' => 'มาตรฐานกระบวนการ',
            ),
            2 => 
            array (
                'name' => 'มาตรฐานผลลัพธ์',
            ),
        ));
        
        
    }
}