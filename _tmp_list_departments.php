<?php
require __DIR__.'/vendor/autoload.php';
$app=require __DIR__.'/bootstrap/app.php';
$app->make("Illuminate\\Contracts\\Console\\Kernel")->bootstrap();
foreach(App\Models\Department::orderBy('id')->get(['id','name']) as $d){ echo $d->id." ".$d->name.PHP_EOL; }
