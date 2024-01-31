<?php

namespace Database\Seeders;

use App\Models\TaskStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Symfony\Component\Console\Output\ConsoleOutput;

class TaskStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data=[
            [
                'name'=>'Creada',
                'slug'=>'created'
            ],
            [
                'name'=>'En progreso',
                'slug'=>'in_progress'
            ],
            [
                'name'=>'Finalizada',
                'slug'=>'finished'
            ],
            [
                'name'=>'Cancelada',
                'slug'=>'canceled'
            ]
        ];

        $printer= new ConsoleOutput();

        foreach($data as $cureentStatus){
            $exists=TaskStatus::where('slug', $cureentStatus['slug'])->first();
            if(!$exists){
                $taskStatusCreated=TaskStatus::create($cureentStatus);
                $printer->writeln($taskStatusCreated->name . ' has been created');
            }else{
                $printer->writeln($cureentStatus['name'] . ' exists yet');

            }
        }

    }
}
