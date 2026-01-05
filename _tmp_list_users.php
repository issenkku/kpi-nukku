<?php
require __DIR__.'/vendor/autoload.php';
$app=require __DIR__.'/bootstrap/app.php';
$app->make("Illuminate\\Contracts\\Console\\Kernel")->bootstrap();
foreach(App\Models\User::all() as $u){
    echo $u->id." ".$u->email." ".($u->department_id??'null')." ".$u->display_name.PHP_EOL;
}
