<?php
namespace App\Modules\ReportsUsers\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use GridEncoder;
use Illuminate\Http\Request;
use App\Repositories\AuditRepository as Audit;
use Auth;
use App\Modules\ReportsUsers\Repositories\ReportPermissionsAndRolesByUsersRepository;
use App\Modules\ReportsUsers\Repositories\ReportUsersRepository;

class ReportsUsersController extends Controller
{

    public function report_users()
    {
        $page_title = "Report users";
        $page_description = "Showing a sample report with the users.";
        $page_message = "";

        return view('reports_users::report-users', compact('page_title', 'page_description', 'page_message'));
    }

    public function report_users_data(Request $request)
    {
        GridEncoder::encodeRequestedData(new ReportUsersRepository(new User()), $request->all());
    }

    public function report_perms_and_roles_by_users()
    {
        $page_title = "Report permissions and roles";
        $page_description = "Showing a sample report of the permissions and roles grouped by users.";
        $page_message = "";

        return view('reports_users::report-perms-and-roles-by-users', compact('page_title', 'page_description', 'page_message'));
    }

    public function report_perms_and_roles_by_users_data(Request $request)
    {
        GridEncoder::encodeRequestedData(new ReportPermissionsAndRolesByUsersRepository(), $request->all());
    }

}
