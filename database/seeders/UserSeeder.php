<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Symfony\Component\Console\Output\ConsoleOutput;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user=[
            'name'=>'Alex Daniel',
            'email'=>'alexispincay005@gmnil.com',
            'password'=>'holamundo',
        ];
        $printer= new ConsoleOutput();
        $exists=User::where('email', $user['email'])->first();
        if(!$exists){
            $user=User::create($user);
            $printer->writeln($user->name . ' has been created');
        }else{
            $printer->writeln($user['name'] . ' exists yet');
        }

    }
}
