<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Modules\Account\Models\Account;

class CreateAccountForAllUsersSeeder extends Seeder
{
    public function run()
    {
        $users = User::whereDoesntHave('account')->get();
        echo "Found " . $users->count() . " users without accounts.\n";

        foreach ($users as $user) {
            $account = Account::create([
                'user_id' => $user->id,
            ]);
            echo "Created account for user: " . $user->email . " (ID: " . $user->id . ")\n";
        }
        echo "Account creation completed!\n";
    }
}
