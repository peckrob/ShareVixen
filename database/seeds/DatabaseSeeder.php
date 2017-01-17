<?php

use Illuminate\Database\Seeder;
use App\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if ($this->command->confirm('Do you wish to refresh migration before seeding, it will clear all old data ?')) {

            // Call the php artisan migrate:refresh using Artisan
            $this->command->call('migrate:refresh');
            $this->command->line("Data cleared, starting from blank database.");
        }

        $email = $this->command->ask('What would you like your initial admin account email to be?');
        $name = $this->command->ask('What would you like your initial admin account name to be?');
        $password  = $this->command->ask('What would you like your initial admin account password to be?');

        $user = new User;
        $user->email = $email;
        $user->name = $name;
        $user->password = bcrypt($password);
        $user->approved = 1;
        $user->can_upload = 1;
        $user->can_manage = 1;
        $user->can_admin = 1;

        if ($user->save()) {
            $this->command->line("Admin user created.");
        }
    }
}
