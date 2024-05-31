<?php

namespace App\Console\Commands;

use App\Models\Role;
use Illuminate\Console\Command;
use App\Http\Controllers\CategoryController;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ImportCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-categories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $import = new CategoryController();
        $import->importCategories();

        $supplierData = ['PromiData' => 1001, 'FARE' => 1002, 'Sanders IT' => 1003];

        foreach($supplierData as $brand => $code){
            Supplier::create(
                [
                    'name' => $brand,
                    'description' => '',
                    'supplier_code' => $code
                ]
            );
        }

        Role::create(
            [
                'name' => 'Admin'
            ]
        );

        User::create([
            'role_id' => 1,
            'name' => 'Kashif Khan',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin123'),
        ]);
    }
}
