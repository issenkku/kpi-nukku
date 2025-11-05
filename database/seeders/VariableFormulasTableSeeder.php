<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class VariableFormulasTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('variable_formulas')->delete();
        
        \DB::table('variable_formulas')->insert(array (
            0 => 
            array (
                'variable_id' => 153,
                'formula_id' => 62,
            ),
            1 => 
            array (
                'variable_id' => 154,
                'formula_id' => 62,
            ),
            2 => 
            array (
                'variable_id' => 77,
                'formula_id' => 33,
            ),
            3 => 
            array (
                'variable_id' => 78,
                'formula_id' => 33,
            ),
            4 => 
            array (
                'variable_id' => 89,
                'formula_id' => 37,
            ),
            5 => 
            array (
                'variable_id' => 90,
                'formula_id' => 37,
            ),
            6 => 
            array (
                'variable_id' => 91,
                'formula_id' => 37,
            ),
            7 => 
            array (
                'variable_id' => 92,
                'formula_id' => 38,
            ),
            8 => 
            array (
                'variable_id' => 93,
                'formula_id' => 38,
            ),
            9 => 
            array (
                'variable_id' => 94,
                'formula_id' => 38,
            ),
            10 => 
            array (
                'variable_id' => 95,
                'formula_id' => 39,
            ),
            11 => 
            array (
                'variable_id' => 96,
                'formula_id' => 39,
            ),
            12 => 
            array (
                'variable_id' => 97,
                'formula_id' => 39,
            ),
            13 => 
            array (
                'variable_id' => 98,
                'formula_id' => 40,
            ),
            14 => 
            array (
                'variable_id' => 99,
                'formula_id' => 40,
            ),
            15 => 
            array (
                'variable_id' => 100,
                'formula_id' => 40,
            ),
            16 => 
            array (
                'variable_id' => 101,
                'formula_id' => 41,
            ),
            17 => 
            array (
                'variable_id' => 102,
                'formula_id' => 41,
            ),
            18 => 
            array (
                'variable_id' => 103,
                'formula_id' => 41,
            ),
            19 => 
            array (
                'variable_id' => 110,
                'formula_id' => 44,
            ),
            20 => 
            array (
                'variable_id' => 111,
                'formula_id' => 45,
            ),
            21 => 
            array (
                'variable_id' => 112,
                'formula_id' => 45,
            ),
            22 => 
            array (
                'variable_id' => 113,
                'formula_id' => 45,
            ),
        ));
        
        
    }
}
