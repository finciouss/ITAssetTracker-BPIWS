<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::all();
        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        return view('employees.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_number' => 'required|string|max:50|unique:employees,employee_number',
            'first_name'      => 'required|string|max:100',
            'last_name'       => 'required|string|max:100',
            'department'      => 'required|string|max:100',
            'email'           => 'required|email|max:150|unique:employees,email',
            'user_identifier' => 'nullable|string|max:450',
        ]);

        Employee::create($validated);
        return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
    }

    public function edit(Employee $employee)
    {
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'employee_number' => 'required|string|max:50|unique:employees,employee_number,' . $employee->id,
            'first_name'      => 'required|string|max:100',
            'last_name'       => 'required|string|max:100',
            'department'      => 'required|string|max:100',
            'email'           => 'required|email|max:150|unique:employees,email,' . $employee->id,
            'user_identifier' => 'nullable|string|max:450',
        ]);

        $employee->update($validated);
        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }
}
