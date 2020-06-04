<?php

use App\Article;
use App\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionsInitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        // Reset cached roles and permissions
        app()[ PermissionRegistrar::class ]->forgetCachedPermissions();

        // create permissions
        Permission::create( [ 'name' => 'edit articles', 'fa_name' => 'ویرایش مقاله' ] );
        Permission::create( [ 'name' => 'delete articles', 'fa_name' => 'حذف مقاله' ] );

        // create roles and assign existing permissions
        $simple = Role::create( [ 'name' => 'simple', 'fa_name' => 'عادی' ] );
        $simple->givePermissionTo( 'edit articles' );
        $simple->givePermissionTo( 'delete articles' );

        $vip = Role::create( [ 'name' => 'vip', 'fa_name' => 'ویژه' ] );
        $vip->givePermissionTo( 'edit articles' );
        $vip->givePermissionTo( 'delete articles' );

        $superVip = Role::create( [ 'name' => 'super-vip', 'fa_name' => 'فوق ویژه' ] );
        $superVip->givePermissionTo( 'edit articles' );
        $superVip->givePermissionTo( 'delete articles' );


        $admin = Role::create( [ 'name' => 'admin', 'fa_name' => 'مدیر' ] );
        $admin->givePermissionTo( 'edit articles' );
        $admin->givePermissionTo( 'delete articles' );


        $superAdmin = Role::create( [ 'name' => 'super-admin', 'fa_name' => 'مدیرکل' ] );
        // gets all permissions via Gate::before rule; see AuthServiceProvider

        // create demo users
        $simpleUser = Factory( App\User::class )
            ->create( [
                          'name' => 'Simple User',
                          'username' => 'simpleuser',
                          'email' => 'simple@user.com',
                          'password' => Hash::make( '123123123' ),

                      ] );
        $simpleUser->assignRole( $simple );


        $vipUser = Factory( App\User::class )
            ->create( [
                          'name' => 'Vip User',
                          'username' => 'vipuser',
                          'email' => 'vip@user.com',
                          'password' => Hash::make( '123123123' ),
                      ] );
        $vipUser->assignRole( $vip );


        $superVipUser = Factory( App\User::class )
            ->create( [
                          'name' => 'Super Vip User',
                          'username' => 'supervipuser',
                          'email' => 'supervip@user.com',
                          'password' => Hash::make( '123123123' ),
                      ] );
        $superVipUser->assignRole( $superVip );


        $superAdminUser = Factory( App\User::class )
            ->create( [
                          'name' => 'Farshid Rezaei',
                          'username' => 'farshidrezaei',
                          'email' => 'farshid@gmail.com',
                          'password' => Hash::make( '123123123' ),
                      ] );
        $superAdminUser->assignRole( $superAdmin );


        Factory( App\User::class, 20 )->create()->each( function ( User $user ) use ( $simple ) {
            $user->assignRole( $simple );


        } );
    }
}
