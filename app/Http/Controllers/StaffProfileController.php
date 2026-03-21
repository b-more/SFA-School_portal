<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\SchoolSettings;
use Illuminate\Http\Request;

class StaffProfileController extends Controller
{
    public function show(Employee $employee)
    {
        $settings = SchoolSettings::first();

        return view('staff-profile', [
            'employee' => $employee,
            'settings' => $settings,
        ]);
    }
}
