<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class FormulasTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('formulas')->delete();
        
        \DB::table('formulas')->insert(array (
            0 => 
            array (
                'id' => 19,
                'condition' => 'input_1*defined_1/input_2',
                'created_at' => '2025-09-15 13:37:35',
                'updated_at' => '2025-09-15 13:37:35',
                'indicator_id' => 3,
            ),
            1 => 
            array (
                'id' => 33,
                'condition' => 'IF(input_1/input_2 >= 1/6, 15,
IF(input_1/input_2 >= 1/7, 10,
IF(input_1/input_2 >= 1/8, 5,
0)))',
                        'created_at' => '2025-09-16 03:40:32',
                        'updated_at' => '2025-09-16 03:40:32',
                        'indicator_id' => 6,
                    ),
                    2 => 
                    array (
                        'id' => 34,
                    'condition' => 'IF((input_1/input_2*defined_1)=100,15,
IF((input_1/input_2*defined_1)>=90,10,
IF((input_1/input_2*defined_1)>=80,5,0)))',
                        'created_at' => '2025-09-16 03:49:26',
                        'updated_at' => '2025-09-16 03:49:26',
                        'indicator_id' => 7,
                    ),
                    3 => 
                    array (
                        'id' => 36,
                    'condition' => 'IF((input_1/input_2*defined_1)>=91,5,
IF((input_1/input_2*defined_1)>=81,3,
IF((input_1/input_2*defined_1)>=71,2,
IF((input_1/input_2*defined_1)>=61,1,0)
)
)
)',
            'created_at' => '2025-09-16 06:09:14',
            'updated_at' => '2025-09-16 06:09:14',
            'indicator_id' => 39,
        ),
        4 => 
        array (
            'id' => 37,
        'condition' => 'IF((input_1/input_2*defined_1)>=91,5,
IF((input_1/input_2*defined_1)>=81,3,
IF((input_1/input_2*defined_1)>=71,2,
IF((input_1/input_2*defined_1)>=61,1,0)
)
)
)',
'created_at' => '2025-09-16 06:12:27',
'updated_at' => '2025-09-16 06:12:27',
'indicator_id' => 40,
),
5 => 
array (
'id' => 38,
'condition' => 'IF((input_1/input_2*defined_1)>=97,5,
IF((input_1/input_2*defined_1)>=95,3,0)
)',
'created_at' => '2025-09-16 06:15:18',
'updated_at' => '2025-09-16 06:15:18',
'indicator_id' => 41,
),
6 => 
array (
'id' => 39,
'condition' => 'IF((input_1/input_2*defined_1)>=90,5,
IF((input_1/input_2*defined_1)>=85,3,0)
)',
'created_at' => '2025-09-16 06:18:44',
'updated_at' => '2025-09-16 06:18:44',
'indicator_id' => 42,
),
7 => 
array (
'id' => 40,
'condition' => 'IF((input_1/input_2*defined_1)=100,10,
IF((input_1/input_2*defined_1)>=95,5,0)
)',
'created_at' => '2025-09-16 06:31:19',
'updated_at' => '2025-09-16 06:31:19',
'indicator_id' => 44,
),
8 => 
array (
'id' => 41,
'condition' => 'IF((input_1/input_2*defined_1)>=90,40,
IF((input_1/input_2*defined_1)>=80,30,
IF((input_1/input_2*defined_1)>=70,20,
IF((input_1/input_2*defined_1)>=60,10,0)
)
)
)',
'created_at' => '2025-09-16 06:36:00',
'updated_at' => '2025-09-16 06:36:00',
'indicator_id' => 45,
),
9 => 
array (
'id' => 42,
'condition' => 'IF((input_1/input_2*defined_1)=100,10,
IF((input_1/input_2*defined_1)>=95,8,
IF((input_1/input_2*defined_1)>=90,6,
IF((input_1/input_2*defined_1)>=85,4,
IF((input_1/input_2*defined_1)>=80,2,0)
)
)
)
)',
'created_at' => '2025-09-16 06:38:36',
'updated_at' => '2025-09-16 06:38:36',
'indicator_id' => 46,
),
10 => 
array (
'id' => 44,
'condition' => 'IF(input_1=1,2,
IF(input_1=2,4,
IF(input_1=3,6,
IF(input_1=4,8,
IF(input_1=5,10,0)
)
)
)
)',
'created_at' => '2025-09-16 06:51:18',
'updated_at' => '2025-09-16 06:51:18',
'indicator_id' => 50,
),
11 => 
array (
'id' => 45,
'condition' => 'IF((input_1/input_2*defined_1)>=90,20,
IF((input_1/input_2*defined_1)>=80,15,
IF((input_1/input_2*defined_1)>=70,10,
IF((input_1/input_2*defined_1)>=60,5,0)
)
)
)',
'created_at' => '2025-09-16 06:59:33',
'updated_at' => '2025-09-16 06:59:33',
'indicator_id' => 53,
),
12 => 
array (
'id' => 46,
'condition' => 'IF((input_1/input_2*defined_1)>=100,10,IF((input_1/input_2)*defined_1>0,5,0))',
'created_at' => '2025-09-16 07:22:18',
'updated_at' => '2025-09-16 07:22:18',
'indicator_id' => 55,
),
13 => 
array (
'id' => 62,
'condition' => 'IF((input_1/input_2)*15 >= 15, 15, 0)',
'created_at' => '2025-09-16 08:11:41',
'updated_at' => '2025-09-16 08:11:41',
'indicator_id' => 5,
),
14 => 
array (
'id' => 1,
'condition' => '(((input_1 / input_2) * 100) / 40) * 10',
'created_at' => '2025-09-30 03:54:32',
'updated_at' => '2025-09-30 03:54:32',
'indicator_id' => 2,
),
15 => 
array (
'id' => 2,
'condition' => 'IF((((input_1/input_2)*100)/70) * 25>=70,25,0)',
'created_at' => '2025-09-30 03:57:19',
'updated_at' => '2025-09-30 03:57:19',
'indicator_id' => 49,
),
16 => 
array (
'id' => 3,
'condition' => 'IF(input_1>1,5,0)',
'created_at' => '2025-09-30 04:00:02',
'updated_at' => '2025-09-30 04:00:02',
'indicator_id' => 37,
),
        ));

        $sequence = \DB::selectOne("SELECT pg_get_serial_sequence(?, 'id') AS seq", ['formulas']);
        if ($sequence && !empty($sequence->seq)) {
            $maxId = \DB::table('formulas')->max('id');
            $value = $maxId ?? 0;
            $isCalled = $maxId !== null;

            \DB::select("SELECT setval(?, ?, ?)", [$sequence->seq, $value, $isCalled]);
        }


    }
}
