<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ChecklistItemsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('checklist_items')->delete();
        
        \DB::table('checklist_items')->insert(array (
            0 => 
            array (
                'id' => 271,
                'required_items' => '[1]',
                'score' => 2.0,
                'sequence' => NULL,
                'indicator_id' => 9,
                'description' => NULL,
            ),
            1 => 
            array (
                'id' => 272,
                'required_items' => '[2]',
                'score' => 2.0,
                'sequence' => NULL,
                'indicator_id' => 9,
                'description' => NULL,
            ),
            2 => 
            array (
                'id' => 273,
                'required_items' => '[3]',
                'score' => 2.0,
                'sequence' => NULL,
                'indicator_id' => 9,
                'description' => NULL,
            ),
            3 => 
            array (
                'id' => 274,
                'required_items' => '[4]',
                'score' => 2.0,
                'sequence' => NULL,
                'indicator_id' => 9,
                'description' => NULL,
            ),
            4 => 
            array (
                'id' => 275,
                'required_items' => '[5]',
                'score' => 2.0,
                'sequence' => NULL,
                'indicator_id' => 9,
                'description' => NULL,
            ),
            5 => 
            array (
                'id' => 276,
                'required_items' => '[1,2]',
                'score' => 4.0,
                'sequence' => NULL,
                'indicator_id' => 9,
                'description' => NULL,
            ),
            6 => 
            array (
                'id' => 277,
                'required_items' => '[1,3]',
                'score' => 4.0,
                'sequence' => NULL,
                'indicator_id' => 9,
                'description' => NULL,
            ),
            7 => 
            array (
                'id' => 278,
                'required_items' => '[1,4]',
                'score' => 4.0,
                'sequence' => NULL,
                'indicator_id' => 9,
                'description' => NULL,
            ),
            8 => 
            array (
                'id' => 279,
                'required_items' => '[1,5]',
                'score' => 4.0,
                'sequence' => NULL,
                'indicator_id' => 9,
                'description' => NULL,
            ),
            9 => 
            array (
                'id' => 280,
                'required_items' => '[2,3]',
                'score' => 4.0,
                'sequence' => NULL,
                'indicator_id' => 9,
                'description' => NULL,
            ),
            10 => 
            array (
                'id' => 281,
                'required_items' => '[2,4]',
                'score' => 4.0,
                'sequence' => NULL,
                'indicator_id' => 9,
                'description' => NULL,
            ),
            11 => 
            array (
                'id' => 282,
                'required_items' => '[2,5]',
                'score' => 4.0,
                'sequence' => NULL,
                'indicator_id' => 9,
                'description' => NULL,
            ),
            12 => 
            array (
                'id' => 283,
                'required_items' => '[3,4]',
                'score' => 4.0,
                'sequence' => NULL,
                'indicator_id' => 9,
                'description' => NULL,
            ),
            13 => 
            array (
                'id' => 284,
                'required_items' => '[3,5]',
                'score' => 4.0,
                'sequence' => NULL,
                'indicator_id' => 9,
                'description' => NULL,
            ),
            14 => 
            array (
                'id' => 285,
                'required_items' => '[4,5]',
                'score' => 4.0,
                'sequence' => NULL,
                'indicator_id' => 9,
                'description' => NULL,
            ),
            15 => 
            array (
                'id' => 286,
                'required_items' => '[1,2,3]',
                'score' => 6.0,
                'sequence' => NULL,
                'indicator_id' => 9,
                'description' => NULL,
            ),
            16 => 
            array (
                'id' => 287,
                'required_items' => '[1,2,4]',
                'score' => 6.0,
                'sequence' => NULL,
                'indicator_id' => 9,
                'description' => NULL,
            ),
            17 => 
            array (
                'id' => 288,
                'required_items' => '[1,2,5]',
                'score' => 6.0,
                'sequence' => NULL,
                'indicator_id' => 9,
                'description' => NULL,
            ),
            18 => 
            array (
                'id' => 289,
                'required_items' => '[1,3,4]',
                'score' => 6.0,
                'sequence' => NULL,
                'indicator_id' => 9,
                'description' => NULL,
            ),
            19 => 
            array (
                'id' => 290,
                'required_items' => '[1,3,5]',
                'score' => 6.0,
                'sequence' => NULL,
                'indicator_id' => 9,
                'description' => NULL,
            ),
            20 => 
            array (
                'id' => 291,
                'required_items' => '[1,4,5]',
                'score' => 6.0,
                'sequence' => NULL,
                'indicator_id' => 9,
                'description' => NULL,
            ),
            21 => 
            array (
                'id' => 292,
                'required_items' => '[2,3,4]',
                'score' => 6.0,
                'sequence' => NULL,
                'indicator_id' => 9,
                'description' => NULL,
            ),
            22 => 
            array (
                'id' => 293,
                'required_items' => '[2,3,5]',
                'score' => 6.0,
                'sequence' => NULL,
                'indicator_id' => 9,
                'description' => NULL,
            ),
            23 => 
            array (
                'id' => 294,
                'required_items' => '[2,4,5]',
                'score' => 6.0,
                'sequence' => NULL,
                'indicator_id' => 9,
                'description' => NULL,
            ),
            24 => 
            array (
                'id' => 295,
                'required_items' => '[3,4,5]',
                'score' => 6.0,
                'sequence' => NULL,
                'indicator_id' => 9,
                'description' => NULL,
            ),
            25 => 
            array (
                'id' => 296,
                'required_items' => '[1,2,3,4]',
                'score' => 8.0,
                'sequence' => NULL,
                'indicator_id' => 9,
                'description' => NULL,
            ),
            26 => 
            array (
                'id' => 297,
                'required_items' => '[1,2,3,5]',
                'score' => 8.0,
                'sequence' => NULL,
                'indicator_id' => 9,
                'description' => NULL,
            ),
            27 => 
            array (
                'id' => 298,
                'required_items' => '[1,2,4,5]',
                'score' => 8.0,
                'sequence' => NULL,
                'indicator_id' => 9,
                'description' => NULL,
            ),
            28 => 
            array (
                'id' => 299,
                'required_items' => '[1,3,4,5]',
                'score' => 8.0,
                'sequence' => NULL,
                'indicator_id' => 9,
                'description' => NULL,
            ),
            29 => 
            array (
                'id' => 300,
                'required_items' => '[2,3,4,5]',
                'score' => 8.0,
                'sequence' => NULL,
                'indicator_id' => 9,
                'description' => NULL,
            ),
            30 => 
            array (
                'id' => 301,
                'required_items' => '[1,2,3,4,5]',
                'score' => 10.0,
                'sequence' => NULL,
                'indicator_id' => 9,
                'description' => NULL,
            ),
            31 => 
            array (
                'id' => 308,
                'required_items' => '["1","2","3","4","5"]',
                'score' => 25.0,
                'sequence' => NULL,
                'indicator_id' => 11,
                'description' => NULL,
            ),
            32 => 
            array (
                'id' => 309,
                'required_items' => '["1","2","3","4"]',
                'score' => 20.0,
                'sequence' => NULL,
                'indicator_id' => 11,
                'description' => NULL,
            ),
            33 => 
            array (
                'id' => 310,
                'required_items' => '["1","2","3"]',
                'score' => 15.0,
                'sequence' => NULL,
                'indicator_id' => 11,
                'description' => NULL,
            ),
            34 => 
            array (
                'id' => 311,
                'required_items' => '["1","2"]',
                'score' => 10.0,
                'sequence' => NULL,
                'indicator_id' => 11,
                'description' => NULL,
            ),
            35 => 
            array (
                'id' => 312,
                'required_items' => '["1"]',
                'score' => 5.0,
                'sequence' => NULL,
                'indicator_id' => 11,
                'description' => NULL,
            ),
            36 => 
            array (
                'id' => 319,
                'required_items' => '["1","2","3"]',
                'score' => 5.0,
                'sequence' => NULL,
                'indicator_id' => 14,
                'description' => NULL,
            ),
            37 => 
            array (
                'id' => 320,
                'required_items' => '["1","2"]',
                'score' => 2.0,
                'sequence' => NULL,
                'indicator_id' => 14,
                'description' => NULL,
            ),
            38 => 
            array (
                'id' => 321,
                'required_items' => '["1"]',
                'score' => 1.0,
                'sequence' => NULL,
                'indicator_id' => 14,
                'description' => NULL,
            ),
            39 => 
            array (
                'id' => 329,
                'required_items' => '["1","2","3","4"]',
                'score' => 15.0,
                'sequence' => NULL,
                'indicator_id' => 17,
                'description' => NULL,
            ),
            40 => 
            array (
                'id' => 330,
                'required_items' => '["1","2","3"]',
                'score' => 10.0,
                'sequence' => NULL,
                'indicator_id' => 17,
                'description' => NULL,
            ),
            41 => 
            array (
                'id' => 331,
                'required_items' => '["1","2"]',
                'score' => 5.0,
                'sequence' => NULL,
                'indicator_id' => 17,
                'description' => NULL,
            ),
            42 => 
            array (
                'id' => 332,
                'required_items' => '["1"]',
                'score' => 2.0,
                'sequence' => NULL,
                'indicator_id' => 17,
                'description' => NULL,
            ),
            43 => 
            array (
                'id' => 333,
                'required_items' => '["1","2","3","4"]',
                'score' => 10.0,
                'sequence' => NULL,
                'indicator_id' => 18,
                'description' => NULL,
            ),
            44 => 
            array (
                'id' => 334,
                'required_items' => '["1","2","3"]',
                'score' => 7.0,
                'sequence' => NULL,
                'indicator_id' => 18,
                'description' => NULL,
            ),
            45 => 
            array (
                'id' => 335,
                'required_items' => '["1","2"]',
                'score' => 5.0,
                'sequence' => NULL,
                'indicator_id' => 18,
                'description' => NULL,
            ),
            46 => 
            array (
                'id' => 336,
                'required_items' => '["1"]',
                'score' => 3.0,
                'sequence' => NULL,
                'indicator_id' => 18,
                'description' => NULL,
            ),
            47 => 
            array (
                'id' => 345,
                'required_items' => '["1","2","3","4"]',
                'score' => 10.0,
                'sequence' => NULL,
                'indicator_id' => 21,
                'description' => NULL,
            ),
            48 => 
            array (
                'id' => 346,
                'required_items' => '["1","2","3"]',
                'score' => 8.0,
                'sequence' => NULL,
                'indicator_id' => 21,
                'description' => NULL,
            ),
            49 => 
            array (
                'id' => 347,
                'required_items' => '["1","2"]',
                'score' => 5.0,
                'sequence' => NULL,
                'indicator_id' => 21,
                'description' => NULL,
            ),
            50 => 
            array (
                'id' => 348,
                'required_items' => '["1"]',
                'score' => 3.0,
                'sequence' => NULL,
                'indicator_id' => 21,
                'description' => NULL,
            ),
            51 => 
            array (
                'id' => 358,
                'required_items' => '["1","2","3","4","5"]',
                'score' => 15.0,
                'sequence' => NULL,
                'indicator_id' => 24,
                'description' => NULL,
            ),
            52 => 
            array (
                'id' => 359,
                'required_items' => '["1","2","3","4"]',
                'score' => 12.0,
                'sequence' => NULL,
                'indicator_id' => 24,
                'description' => NULL,
            ),
            53 => 
            array (
                'id' => 360,
                'required_items' => '["1","2","3"]',
                'score' => 10.0,
                'sequence' => NULL,
                'indicator_id' => 24,
                'description' => NULL,
            ),
            54 => 
            array (
                'id' => 361,
                'required_items' => '["1","2"]',
                'score' => 5.0,
                'sequence' => NULL,
                'indicator_id' => 24,
                'description' => NULL,
            ),
            55 => 
            array (
                'id' => 362,
                'required_items' => '["1"]',
                'score' => 2.0,
                'sequence' => NULL,
                'indicator_id' => 24,
                'description' => NULL,
            ),
            56 => 
            array (
                'id' => 371,
                'required_items' => '["1","2","3","4","5"]',
                'score' => 25.0,
                'sequence' => NULL,
                'indicator_id' => 27,
                'description' => NULL,
            ),
            57 => 
            array (
                'id' => 372,
                'required_items' => '["1","2","3","4"]',
                'score' => 20.0,
                'sequence' => NULL,
                'indicator_id' => 27,
                'description' => NULL,
            ),
            58 => 
            array (
                'id' => 373,
                'required_items' => '["1","2","3"]',
                'score' => 15.0,
                'sequence' => NULL,
                'indicator_id' => 27,
                'description' => NULL,
            ),
            59 => 
            array (
                'id' => 374,
                'required_items' => '["1","2"]',
                'score' => 10.0,
                'sequence' => NULL,
                'indicator_id' => 27,
                'description' => NULL,
            ),
            60 => 
            array (
                'id' => 382,
                'required_items' => '["1","2","3","4"]',
                'score' => 10.0,
                'sequence' => NULL,
                'indicator_id' => 28,
                'description' => NULL,
            ),
            61 => 
            array (
                'id' => 383,
                'required_items' => '["1","2","3"]',
                'score' => 8.0,
                'sequence' => NULL,
                'indicator_id' => 28,
                'description' => NULL,
            ),
            62 => 
            array (
                'id' => 384,
                'required_items' => '["1","2"]',
                'score' => 5.0,
                'sequence' => NULL,
                'indicator_id' => 28,
                'description' => NULL,
            ),
            63 => 
            array (
                'id' => 385,
                'required_items' => '["1"]',
                'score' => 2.0,
                'sequence' => NULL,
                'indicator_id' => 28,
                'description' => NULL,
            ),
            64 => 
            array (
                'id' => 392,
                'required_items' => '["1","2","3","4"]',
                'score' => 15.0,
                'sequence' => NULL,
                'indicator_id' => 32,
                'description' => NULL,
            ),
            65 => 
            array (
                'id' => 393,
                'required_items' => '["1","2","3"]',
                'score' => 10.0,
                'sequence' => NULL,
                'indicator_id' => 32,
                'description' => NULL,
            ),
            66 => 
            array (
                'id' => 394,
                'required_items' => '["1","2"]',
                'score' => 5.0,
                'sequence' => NULL,
                'indicator_id' => 32,
                'description' => NULL,
            ),
            67 => 
            array (
                'id' => 401,
                'required_items' => '["1","2","3","4"]',
                'score' => 20.0,
                'sequence' => NULL,
                'indicator_id' => 35,
                'description' => NULL,
            ),
            68 => 
            array (
                'id' => 402,
                'required_items' => '["1","2","3"]',
                'score' => 15.0,
                'sequence' => NULL,
                'indicator_id' => 35,
                'description' => NULL,
            ),
            69 => 
            array (
                'id' => 403,
                'required_items' => '["1","2"]',
                'score' => 10.0,
                'sequence' => NULL,
                'indicator_id' => 35,
                'description' => NULL,
            ),
            70 => 
            array (
                'id' => 409,
                'required_items' => '["1"]',
                'score' => 10.0,
                'sequence' => NULL,
                'indicator_id' => 43,
                'description' => NULL,
            ),
            71 => 
            array (
                'id' => 410,
                'required_items' => '["1","2"]',
                'score' => 20.0,
                'sequence' => NULL,
                'indicator_id' => 43,
                'description' => NULL,
            ),
            72 => 
            array (
                'id' => 444,
                'required_items' => '["1"]',
                'score' => 5.0,
                'sequence' => NULL,
                'indicator_id' => 51,
                'description' => NULL,
            ),
            73 => 
            array (
                'id' => 445,
                'required_items' => '["2"]',
                'score' => 10.0,
                'sequence' => NULL,
                'indicator_id' => 51,
                'description' => NULL,
            ),
            74 => 
            array (
                'id' => 451,
                'required_items' => '["1","2","3"]',
                'score' => 10.0,
                'sequence' => NULL,
                'indicator_id' => 56,
                'description' => NULL,
            ),
            75 => 
            array (
                'id' => 452,
                'required_items' => '["1","2"]',
                'score' => 6.0,
                'sequence' => NULL,
                'indicator_id' => 56,
                'description' => NULL,
            ),
            76 => 
            array (
                'id' => 453,
                'required_items' => '["1"]',
                'score' => 3.0,
                'sequence' => NULL,
                'indicator_id' => 56,
                'description' => NULL,
            ),
            77 => 
            array (
                'id' => 313,
                'required_items' => '["1","2","3"]',
                'score' => 5.0,
                'sequence' => NULL,
                'indicator_id' => 12,
                'description' => NULL,
            ),
            78 => 
            array (
                'id' => 314,
                'required_items' => '["1","2"]',
                'score' => 2.0,
                'sequence' => NULL,
                'indicator_id' => 12,
                'description' => NULL,
            ),
            79 => 
            array (
                'id' => 315,
                'required_items' => '["1"]',
                'score' => 1.0,
                'sequence' => NULL,
                'indicator_id' => 12,
                'description' => NULL,
            ),
            80 => 
            array (
                'id' => 322,
                'required_items' => '["1","2","3"]',
                'score' => 5.0,
                'sequence' => NULL,
                'indicator_id' => 15,
                'description' => NULL,
            ),
            81 => 
            array (
                'id' => 323,
                'required_items' => '["1","2"]',
                'score' => 2.0,
                'sequence' => NULL,
                'indicator_id' => 15,
                'description' => NULL,
            ),
            82 => 
            array (
                'id' => 324,
                'required_items' => '["1"]',
                'score' => 1.0,
                'sequence' => NULL,
                'indicator_id' => 15,
                'description' => NULL,
            ),
            83 => 
            array (
                'id' => 337,
                'required_items' => '["1","2","3","4"]',
                'score' => 10.0,
                'sequence' => NULL,
                'indicator_id' => 19,
                'description' => NULL,
            ),
            84 => 
            array (
                'id' => 338,
                'required_items' => '["1","2","3"]',
                'score' => 7.0,
                'sequence' => NULL,
                'indicator_id' => 19,
                'description' => NULL,
            ),
            85 => 
            array (
                'id' => 339,
                'required_items' => '["1","2"]',
                'score' => 5.0,
                'sequence' => NULL,
                'indicator_id' => 19,
                'description' => NULL,
            ),
            86 => 
            array (
                'id' => 340,
                'required_items' => '["1"]',
                'score' => 3.0,
                'sequence' => NULL,
                'indicator_id' => 19,
                'description' => NULL,
            ),
            87 => 
            array (
                'id' => 363,
                'required_items' => '["1","2","3","4","5"]',
                'score' => 10.0,
                'sequence' => NULL,
                'indicator_id' => 25,
                'description' => NULL,
            ),
            88 => 
            array (
                'id' => 364,
                'required_items' => '["1","2","3","4"]',
                'score' => 7.0,
                'sequence' => NULL,
                'indicator_id' => 25,
                'description' => NULL,
            ),
            89 => 
            array (
                'id' => 365,
                'required_items' => '["1","2","3"]',
                'score' => 5.0,
                'sequence' => NULL,
                'indicator_id' => 25,
                'description' => NULL,
            ),
            90 => 
            array (
                'id' => 366,
                'required_items' => '["1","2"]',
                'score' => 3.0,
                'sequence' => NULL,
                'indicator_id' => 25,
                'description' => NULL,
            ),
            91 => 
            array (
                'id' => 386,
                'required_items' => '["1","2","3","4","5"]',
                'score' => 10.0,
                'sequence' => NULL,
                'indicator_id' => 30,
                'description' => NULL,
            ),
            92 => 
            array (
                'id' => 387,
                'required_items' => '["1","2"]',
                'score' => 6.0,
                'sequence' => NULL,
                'indicator_id' => 30,
                'description' => NULL,
            ),
            93 => 
            array (
                'id' => 388,
                'required_items' => '["1"]',
                'score' => 3.0,
                'sequence' => NULL,
                'indicator_id' => 30,
                'description' => NULL,
            ),
            94 => 
            array (
                'id' => 395,
                'required_items' => '["1","2","3","4"]',
                'score' => 15.0,
                'sequence' => NULL,
                'indicator_id' => 33,
                'description' => NULL,
            ),
            95 => 
            array (
                'id' => 396,
                'required_items' => '["1","2"]',
                'score' => 10.0,
                'sequence' => NULL,
                'indicator_id' => 33,
                'description' => NULL,
            ),
            96 => 
            array (
                'id' => 397,
                'required_items' => '["1"]',
                'score' => 5.0,
                'sequence' => NULL,
                'indicator_id' => 33,
                'description' => NULL,
            ),
            97 => 
            array (
                'id' => 404,
                'required_items' => '["1","2","3","4"]',
                'score' => 20.0,
                'sequence' => NULL,
                'indicator_id' => 36,
                'description' => NULL,
            ),
            98 => 
            array (
                'id' => 405,
                'required_items' => '["1","2","3"]',
                'score' => 15.0,
                'sequence' => NULL,
                'indicator_id' => 36,
                'description' => NULL,
            ),
            99 => 
            array (
                'id' => 406,
                'required_items' => '["1","2"]',
                'score' => 10.0,
                'sequence' => NULL,
                'indicator_id' => 36,
                'description' => NULL,
            ),
            100 => 
            array (
                'id' => 411,
                'required_items' => '["1"]',
                'score' => 5.0,
                'sequence' => NULL,
                'indicator_id' => 47,
                'description' => NULL,
            ),
            101 => 
            array (
                'id' => 412,
                'required_items' => '["2"]',
                'score' => 10.0,
                'sequence' => NULL,
                'indicator_id' => 47,
                'description' => NULL,
            ),
            102 => 
            array (
                'id' => 446,
                'required_items' => '["1"]',
                'score' => 10.0,
                'sequence' => NULL,
                'indicator_id' => 52,
                'description' => NULL,
            ),
            103 => 
            array (
                'id' => 447,
                'required_items' => '["2"]',
                'score' => 20.0,
                'sequence' => NULL,
                'indicator_id' => 52,
                'description' => NULL,
            ),
            104 => 
            array (
                'id' => 316,
                'required_items' => '["1","2","3"]',
                'score' => 5.0,
                'sequence' => NULL,
                'indicator_id' => 13,
                'description' => NULL,
            ),
            105 => 
            array (
                'id' => 317,
                'required_items' => '["1","2"]',
                'score' => 2.0,
                'sequence' => NULL,
                'indicator_id' => 13,
                'description' => NULL,
            ),
            106 => 
            array (
                'id' => 318,
                'required_items' => '["1"]',
                'score' => 1.0,
                'sequence' => NULL,
                'indicator_id' => 13,
                'description' => NULL,
            ),
            107 => 
            array (
                'id' => 325,
                'required_items' => '["1","2","3","4"]',
                'score' => 15.0,
                'sequence' => NULL,
                'indicator_id' => 16,
                'description' => NULL,
            ),
            108 => 
            array (
                'id' => 326,
                'required_items' => '["1","2","3"]',
                'score' => 10.0,
                'sequence' => NULL,
                'indicator_id' => 16,
                'description' => NULL,
            ),
            109 => 
            array (
                'id' => 327,
                'required_items' => '["1","2"]',
                'score' => 5.0,
                'sequence' => NULL,
                'indicator_id' => 16,
                'description' => NULL,
            ),
            110 => 
            array (
                'id' => 328,
                'required_items' => '["1"]',
                'score' => 5.0,
                'sequence' => NULL,
                'indicator_id' => 16,
                'description' => NULL,
            ),
            111 => 
            array (
                'id' => 341,
                'required_items' => '["1","2","3","4","5"]',
                'score' => 10.0,
                'sequence' => NULL,
                'indicator_id' => 20,
                'description' => NULL,
            ),
            112 => 
            array (
                'id' => 342,
                'required_items' => '["1","2","3"]',
                'score' => 7.0,
                'sequence' => NULL,
                'indicator_id' => 20,
                'description' => NULL,
            ),
            113 => 
            array (
                'id' => 343,
                'required_items' => '["1","2"]',
                'score' => 5.0,
                'sequence' => NULL,
                'indicator_id' => 20,
                'description' => NULL,
            ),
            114 => 
            array (
                'id' => 344,
                'required_items' => '["1"]',
                'score' => 3.0,
                'sequence' => NULL,
                'indicator_id' => 20,
                'description' => NULL,
            ),
            115 => 
            array (
                'id' => 354,
                'required_items' => '["1","2","3","4","5"]',
                'score' => 15.0,
                'sequence' => NULL,
                'indicator_id' => 23,
                'description' => NULL,
            ),
            116 => 
            array (
                'id' => 355,
                'required_items' => '["1","2","3","4"]',
                'score' => 10.0,
                'sequence' => NULL,
                'indicator_id' => 23,
                'description' => NULL,
            ),
            117 => 
            array (
                'id' => 356,
                'required_items' => '["1","2","3"]',
                'score' => 5.0,
                'sequence' => NULL,
                'indicator_id' => 23,
                'description' => NULL,
            ),
            118 => 
            array (
                'id' => 357,
                'required_items' => '["1","2"]',
                'score' => 2.0,
                'sequence' => NULL,
                'indicator_id' => 23,
                'description' => NULL,
            ),
            119 => 
            array (
                'id' => 367,
                'required_items' => '["1","2","3","4","5"]',
                'score' => 25.0,
                'sequence' => NULL,
                'indicator_id' => 26,
                'description' => NULL,
            ),
            120 => 
            array (
                'id' => 368,
                'required_items' => '["1","2","3","4"]',
                'score' => 20.0,
                'sequence' => NULL,
                'indicator_id' => 26,
                'description' => NULL,
            ),
            121 => 
            array (
                'id' => 369,
                'required_items' => '["1","2","3"]',
                'score' => 15.0,
                'sequence' => NULL,
                'indicator_id' => 26,
                'description' => NULL,
            ),
            122 => 
            array (
                'id' => 370,
                'required_items' => '["1","2"]',
                'score' => 10.0,
                'sequence' => NULL,
                'indicator_id' => 26,
                'description' => NULL,
            ),
            123 => 
            array (
                'id' => 379,
                'required_items' => '["1","2","3"]',
                'score' => 10.0,
                'sequence' => NULL,
                'indicator_id' => 29,
                'description' => NULL,
            ),
            124 => 
            array (
                'id' => 380,
                'required_items' => '["1","2"]',
                'score' => 7.0,
                'sequence' => NULL,
                'indicator_id' => 29,
                'description' => NULL,
            ),
            125 => 
            array (
                'id' => 381,
                'required_items' => '["1"]',
                'score' => 3.0,
                'sequence' => NULL,
                'indicator_id' => 29,
                'description' => NULL,
            ),
            126 => 
            array (
                'id' => 389,
                'required_items' => '["1","2","3"]',
                'score' => 10.0,
                'sequence' => NULL,
                'indicator_id' => 31,
                'description' => NULL,
            ),
            127 => 
            array (
                'id' => 390,
                'required_items' => '["1","2"]',
                'score' => 6.0,
                'sequence' => NULL,
                'indicator_id' => 31,
                'description' => NULL,
            ),
            128 => 
            array (
                'id' => 391,
                'required_items' => '["1"]',
                'score' => 3.0,
                'sequence' => NULL,
                'indicator_id' => 31,
                'description' => NULL,
            ),
            129 => 
            array (
                'id' => 398,
                'required_items' => '["1","2","3"]',
                'score' => 10.0,
                'sequence' => NULL,
                'indicator_id' => 34,
                'description' => NULL,
            ),
            130 => 
            array (
                'id' => 399,
                'required_items' => '["1","2"]',
                'score' => 7.0,
                'sequence' => NULL,
                'indicator_id' => 34,
                'description' => NULL,
            ),
            131 => 
            array (
                'id' => 400,
                'required_items' => '["1"]',
                'score' => 3.0,
                'sequence' => NULL,
                'indicator_id' => 34,
                'description' => NULL,
            ),
            132 => 
            array (
                'id' => 407,
                'required_items' => '["1","2"]',
                'score' => 10.0,
                'sequence' => NULL,
                'indicator_id' => 38,
                'description' => NULL,
            ),
            133 => 
            array (
                'id' => 408,
                'required_items' => '["1"]',
                'score' => 6.0,
                'sequence' => NULL,
                'indicator_id' => 38,
                'description' => NULL,
            ),
            134 => 
            array (
                'id' => 413,
                'required_items' => '[1]',
                'score' => 1.0,
                'sequence' => NULL,
                'indicator_id' => 48,
                'description' => NULL,
            ),
            135 => 
            array (
                'id' => 414,
                'required_items' => '[2]',
                'score' => 1.0,
                'sequence' => NULL,
                'indicator_id' => 48,
                'description' => NULL,
            ),
            136 => 
            array (
                'id' => 415,
                'required_items' => '[3]',
                'score' => 1.0,
                'sequence' => NULL,
                'indicator_id' => 48,
                'description' => NULL,
            ),
            137 => 
            array (
                'id' => 416,
                'required_items' => '[4]',
                'score' => 1.0,
                'sequence' => NULL,
                'indicator_id' => 48,
                'description' => NULL,
            ),
            138 => 
            array (
                'id' => 417,
                'required_items' => '[5]',
                'score' => 1.0,
                'sequence' => NULL,
                'indicator_id' => 48,
                'description' => NULL,
            ),
            139 => 
            array (
                'id' => 418,
                'required_items' => '[1,2]',
                'score' => 2.0,
                'sequence' => NULL,
                'indicator_id' => 48,
                'description' => NULL,
            ),
            140 => 
            array (
                'id' => 419,
                'required_items' => '[1,3]',
                'score' => 2.0,
                'sequence' => NULL,
                'indicator_id' => 48,
                'description' => NULL,
            ),
            141 => 
            array (
                'id' => 420,
                'required_items' => '[1,4]',
                'score' => 2.0,
                'sequence' => NULL,
                'indicator_id' => 48,
                'description' => NULL,
            ),
            142 => 
            array (
                'id' => 421,
                'required_items' => '[1,5]',
                'score' => 2.0,
                'sequence' => NULL,
                'indicator_id' => 48,
                'description' => NULL,
            ),
            143 => 
            array (
                'id' => 422,
                'required_items' => '[2,3]',
                'score' => 2.0,
                'sequence' => NULL,
                'indicator_id' => 48,
                'description' => NULL,
            ),
            144 => 
            array (
                'id' => 423,
                'required_items' => '[2,4]',
                'score' => 2.0,
                'sequence' => NULL,
                'indicator_id' => 48,
                'description' => NULL,
            ),
            145 => 
            array (
                'id' => 424,
                'required_items' => '[2,5]',
                'score' => 2.0,
                'sequence' => NULL,
                'indicator_id' => 48,
                'description' => NULL,
            ),
            146 => 
            array (
                'id' => 425,
                'required_items' => '[3,4]',
                'score' => 2.0,
                'sequence' => NULL,
                'indicator_id' => 48,
                'description' => NULL,
            ),
            147 => 
            array (
                'id' => 426,
                'required_items' => '[3,5]',
                'score' => 2.0,
                'sequence' => NULL,
                'indicator_id' => 48,
                'description' => NULL,
            ),
            148 => 
            array (
                'id' => 427,
                'required_items' => '[4,5]',
                'score' => 2.0,
                'sequence' => NULL,
                'indicator_id' => 48,
                'description' => NULL,
            ),
            149 => 
            array (
                'id' => 428,
                'required_items' => '[1,2,3]',
                'score' => 3.0,
                'sequence' => NULL,
                'indicator_id' => 48,
                'description' => NULL,
            ),
            150 => 
            array (
                'id' => 429,
                'required_items' => '[1,2,4]',
                'score' => 3.0,
                'sequence' => NULL,
                'indicator_id' => 48,
                'description' => NULL,
            ),
            151 => 
            array (
                'id' => 430,
                'required_items' => '[1,2,5]',
                'score' => 3.0,
                'sequence' => NULL,
                'indicator_id' => 48,
                'description' => NULL,
            ),
            152 => 
            array (
                'id' => 431,
                'required_items' => '[1,3,4]',
                'score' => 3.0,
                'sequence' => NULL,
                'indicator_id' => 48,
                'description' => NULL,
            ),
            153 => 
            array (
                'id' => 432,
                'required_items' => '[1,3,5]',
                'score' => 3.0,
                'sequence' => NULL,
                'indicator_id' => 48,
                'description' => NULL,
            ),
            154 => 
            array (
                'id' => 433,
                'required_items' => '[1,4,5]',
                'score' => 3.0,
                'sequence' => NULL,
                'indicator_id' => 48,
                'description' => NULL,
            ),
            155 => 
            array (
                'id' => 434,
                'required_items' => '[2,3,4]',
                'score' => 3.0,
                'sequence' => NULL,
                'indicator_id' => 48,
                'description' => NULL,
            ),
            156 => 
            array (
                'id' => 435,
                'required_items' => '[2,3,5]',
                'score' => 3.0,
                'sequence' => NULL,
                'indicator_id' => 48,
                'description' => NULL,
            ),
            157 => 
            array (
                'id' => 436,
                'required_items' => '[2,4,5]',
                'score' => 3.0,
                'sequence' => NULL,
                'indicator_id' => 48,
                'description' => NULL,
            ),
            158 => 
            array (
                'id' => 437,
                'required_items' => '[3,4,5]',
                'score' => 3.0,
                'sequence' => NULL,
                'indicator_id' => 48,
                'description' => NULL,
            ),
            159 => 
            array (
                'id' => 438,
                'required_items' => '[1,2,3,4]',
                'score' => 4.0,
                'sequence' => NULL,
                'indicator_id' => 48,
                'description' => NULL,
            ),
            160 => 
            array (
                'id' => 439,
                'required_items' => '[1,2,3,5]',
                'score' => 4.0,
                'sequence' => NULL,
                'indicator_id' => 48,
                'description' => NULL,
            ),
            161 => 
            array (
                'id' => 440,
                'required_items' => '[1,2,4,5]',
                'score' => 4.0,
                'sequence' => NULL,
                'indicator_id' => 48,
                'description' => NULL,
            ),
            162 => 
            array (
                'id' => 441,
                'required_items' => '[1,3,4,5]',
                'score' => 4.0,
                'sequence' => NULL,
                'indicator_id' => 48,
                'description' => NULL,
            ),
            163 => 
            array (
                'id' => 442,
                'required_items' => '[2,3,4,5]',
                'score' => 4.0,
                'sequence' => NULL,
                'indicator_id' => 48,
                'description' => NULL,
            ),
            164 => 
            array (
                'id' => 443,
                'required_items' => '[1,2,3,4,5]',
                'score' => 5.0,
                'sequence' => NULL,
                'indicator_id' => 48,
                'description' => NULL,
            ),
            165 => 
            array (
                'id' => 448,
                'required_items' => '["3"]',
                'score' => 15.0,
                'sequence' => NULL,
                'indicator_id' => 54,
                'description' => NULL,
            ),
            166 => 
            array (
                'id' => 449,
                'required_items' => '["2"]',
                'score' => 10.0,
                'sequence' => NULL,
                'indicator_id' => 54,
                'description' => NULL,
            ),
            167 => 
            array (
                'id' => 450,
                'required_items' => '["1"]',
                'score' => 5.0,
                'sequence' => NULL,
                'indicator_id' => 54,
                'description' => NULL,
            ),
            168 => 
            array (
                'id' => 456,
                'required_items' => '["1"]',
                'score' => 5.0,
                'sequence' => NULL,
                'indicator_id' => 4,
                'description' => NULL,
            ),
            169 => 
            array (
                'id' => 462,
                'required_items' => '["1","2","3","4","5"]',
                'score' => 50.0,
                'sequence' => NULL,
                'indicator_id' => 10,
                'description' => NULL,
            ),
            170 => 
            array (
                'id' => 463,
                'required_items' => '["1","2","3","4"]',
                'score' => 30.0,
                'sequence' => NULL,
                'indicator_id' => 10,
                'description' => NULL,
            ),
            171 => 
            array (
                'id' => 464,
                'required_items' => '["1","2","3"]',
                'score' => 20.0,
                'sequence' => NULL,
                'indicator_id' => 10,
                'description' => NULL,
            ),
            172 => 
            array (
                'id' => 465,
                'required_items' => '["1","2"]',
                'score' => 10.0,
                'sequence' => NULL,
                'indicator_id' => 10,
                'description' => NULL,
            ),
            173 => 
            array (
                'id' => 466,
                'required_items' => '["1"]',
                'score' => 5.0,
                'sequence' => NULL,
                'indicator_id' => 10,
                'description' => NULL,
            ),
            174 => 
            array (
                'id' => 467,
                'required_items' => '["1","2","3","4","5"]',
                'score' => 10.0,
                'sequence' => NULL,
                'indicator_id' => 22,
                'description' => NULL,
            ),
            175 => 
            array (
                'id' => 468,
                'required_items' => '["1","2","3","4"]',
                'score' => 8.0,
                'sequence' => NULL,
                'indicator_id' => 22,
                'description' => NULL,
            ),
            176 => 
            array (
                'id' => 469,
                'required_items' => '["1","2","3"]',
                'score' => 6.0,
                'sequence' => NULL,
                'indicator_id' => 22,
                'description' => NULL,
            ),
            177 => 
            array (
                'id' => 470,
                'required_items' => '["1","2"]',
                'score' => 4.0,
                'sequence' => NULL,
                'indicator_id' => 22,
                'description' => NULL,
            ),
            178 => 
            array (
                'id' => 471,
                'required_items' => '["1"]',
                'score' => 2.0,
                'sequence' => NULL,
                'indicator_id' => 22,
                'description' => NULL,
            ),
            179 => 
            array (
                'id' => 472,
                'required_items' => '["1","2","3","4","5"]',
                'score' => 10.0,
                'sequence' => NULL,
                'indicator_id' => 1,
                'description' => NULL,
            ),
            180 => 
            array (
                'id' => 473,
                'required_items' => '["1","2","3","4"]',
                'score' => 5.0,
                'sequence' => NULL,
                'indicator_id' => 1,
                'description' => NULL,
            ),
        ));

        $sequence = \DB::selectOne("SELECT pg_get_serial_sequence(?, 'id') AS seq", ['checklist_items']);
        if ($sequence && !empty($sequence->seq)) {
            $maxId = \DB::table('checklist_items')->max('id');
            $value = $maxId ?? 0;
            $isCalled = $maxId !== null;

            \DB::select("SELECT setval(?, ?, ?)", [$sequence->seq, $value, $isCalled]);
        }


    }
}
