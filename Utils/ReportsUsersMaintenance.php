<?php namespace App\Modules\ReportsUsers\Utils;

use App\Models\Menu;
use App\Models\Permission;
use DB;
use Sroutier\LESKModules\Contracts\ModuleMaintenanceInterface;
use Sroutier\LESKModules\Traits\MaintenanceTrait;

class ReportsUsersMaintenance implements ModuleMaintenanceInterface
{

    use MaintenanceTrait;


    static public function initialize()
    {
        DB::transaction(function () {

            /////////////////////////////////////////////////
            // Build database.
            self::buildDB();

            /////////////////
            // PREPARATION //
            /////////////////
            //----- Find some system permissions.
            $permBasicAuthenticated = Permission::where('name', 'basic-authenticated')->first();
            //----- Find home menu.
            $menuHome = Menu::where('name', 'home')->first();

            /////////////////////////
            // Permissions & Roles //
            /////////////////////////
            //----- Create permissions.
            $permReportUser = self::createPermission( 'reports.users',
                'User report',
                'Access the user report');
            $permReportPermRole = self::createPermission( 'reports.perms-and-roles',
                'Permissions & roles report',
                'Access the permissions and roles by user report');
            // ----- Create roles and assign permissions.
            $roleReportUser = self::createRole("reports.users.viewer",
                "User report viewer",
                "Can view the user report.",
                [$permReportUser->id]);
            $roleReportPermRole = self::createRole("reports.perms-and-roles.viewer",
                "Permissions and roles report viewer",
                "Can view the permissions and roles report.",
                [$permReportPermRole->id]);

            ////////////
            // Routes //
            ////////////
            // ----- Create routes for user report page and data loader.
            $routeReportUser = self::createRoute( 'reports.users',
                'reports/users',
                'App\Modules\ReportsUsers\Http\Controllers\ReportsUsersController@report_users',
                $permReportUser );
            $routeReportUserData = self::createRoute( 'reports.users-data',
                'reports/users-data',
                'App\Modules\ReportsUsers\Http\Controllers\ReportsUsersController@report_users_data',
                $permReportUser,
                'POST' );
            // ----- Create routes for permissions and roles report page and data loader.
            $routeReportPermRole = self::createRoute( 'reports.perms-and-roles-by-users',
                'reports/perms-and-roles-by-users',
                'App\Modules\ReportsUsers\Http\Controllers\ReportsUsersController@report_perms_and_roles_by_users',
                $permReportPermRole );
            $routeReportUserData = self::createRoute( 'reports.perms-and-roles-by-users-data',
                'reports/perms-and-roles-by-users-data',
                'App\Modules\ReportsUsers\Http\Controllers\ReportsUsersController@report_perms_and_roles_by_users_data',
                $permReportPermRole,
                'POST' );

            ///////////
            // Menus //
            ///////////
            // ----- Create menu structure
            $menuReportsContainer = self::createMenu( 'reports.container', 'Reports', 20, 'fa fa-table', $menuHome, true, null, $permBasicAuthenticated );
            $menuReportUsers      = self::createMenu( 'reports.users', 'Users', 0, 'fa fa-user', $menuReportsContainer, false, $routeReportUser );
            $menuReportPermsRoles = self::createMenu( 'reports.perms-and-roles', 'Permissions and roles', 0, 'fa fa-unlock-alt', $menuReportsContainer, false, $routeReportPermRole );

        }); // End of DB::transaction(....)
    }


    static public function unInitialize()
    {
        DB::transaction(function () {

            // ----- Delete menu structure
            self::destroyMenu('reports.perms-and-roles');
            self::destroyMenu('reports.users');
            self::destroyMenu('reports.container');
            // ----- Delete routes
            self::destroyRoute('reports.perms-and-roles-by-users-data');
            self::destroyRoute('reports.perms-and-roles-by-users');
            self::destroyRoute('reports.users-data');
            self::destroyRoute('reports.users');
            // ----- Delete roles
            self::destroyRole('reports.perms-and-roles.viewer');
            self::destroyRole('reports.users.viewer');
            // ----- Delete permissions
            self::destroyPermission('reports.perms-and-roles');
            self::destroyPermission('reports.users');

            self::destroyDB();
        }); // End of DB::transaction(....)
    }


    static public function enable()
    {
        DB::transaction(function () {
            self::enableMenu('reports.perms-and-roles');
            self::enableMenu('reports.users');
        });
    }


    static public function disable()
    {
        DB::transaction(function () {
            self::disableMenu('reports.perms-and-roles');
            self::disableMenu('reports.users');
        });
    }


    static public function buildDB()
    {
        // Build view for the report.
        $sql = "";
        $sql = $sql . "CREATE VIEW v_permissions_and_roles_by_users ";
        $sql = $sql . "AS ";
        $sql = $sql . "select u.id            AS user_id ";
        $sql = $sql . "      ,u.username      AS username ";
        $sql = $sql . "      ,''              AS user_permission ";
        $sql = $sql . "      ,r.display_name  AS role ";
        $sql = $sql . "      ,p1.display_name AS role_permission ";
        $sql = $sql . "from users u ";
        $sql = $sql . "      left join role_user ru ";
        $sql = $sql . "          on  ru.user_id = u.id ";
        $sql = $sql . "      left join roles r ";
        $sql = $sql . "          on ru.role_id = r.id ";
        $sql = $sql . "      left join permission_role pr ";
        $sql = $sql . "          on pr.role_id = r.id ";
        $sql = $sql . "      left join permissions p1 ";
        $sql = $sql . "          on pr.permission_id = p1.id ";
        $sql = $sql . "union ";
        $sql = $sql . "select u2.id AS user_id ";
        $sql = $sql . "      ,u2.username AS username ";
        $sql = $sql . "      ,p2.display_name AS user_permission ";
        $sql = $sql . "      ,'' AS role ";
        $sql = $sql . "      ,'' AS role_permission ";
        $sql = $sql . "from users u2 ";
        $sql = $sql . "      join permission_user pu2 ";
        $sql = $sql . "          on pu2.user_id = u2.id ";
        $sql = $sql . "      join permissions p2 ";
        $sql = $sql . "          on pu2.permission_id = p2.id ";
        $sql = $sql . "order by username ";
        $sql = $sql . "      ,user_permission ";
        $sql = $sql . "      ,role ";
        $sql = $sql . "      ,role_permission ";

        DB::statement($sql);
    }


    static public function destroyDB()
    {
        DB::statement("DROP VIEW IF EXISTS v_permissions_and_roles_by_users");
    }

}
