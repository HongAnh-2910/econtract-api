<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run():void
    {
        $permissionArray = [
            'Thành viên'     => 'permission.members',
            'Phòng ban'      => 'permission.dapartments'
        ];
        foreach ($permissionArray as $key => $value)
        {
            Permission::updateOrCreate(
                ['name' => $key],
                ['permission_alias' => $value]
            );
        }
    }
}
