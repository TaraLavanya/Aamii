<?php

namespace App\Http\Livewire\Settings\Employee;

use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class EmployeeHandler extends Component
{
    public $departments;

    public $employee = [
        'user_id' => '',
        'name' => '',
        'email' => '',
        'is_active' => 1,
    ];

    protected $rules = [
        'employee.user_id' => 'required|string',
        'employee.name' => 'required|string',
        'employee.email' => 'required|string|email',
    ];

    protected $messages = [
        'employee.user_id.required' => 'The employee number filed is required.',
        'employee.name.required' => 'The employee name field is required.',
        'employee.email.required' => 'The email address field is required.',
    ];

    public function mount($employeeId)
    {

        if ($employeeId) {
            $employee = User::find($employeeId);
            if ($employee) {
                $this->employee = $employee->toArray();
            } else {
                return redirect()->back()->with('warning', 'Employee not found');
            }
        }
    }

    public function create()
    {
        $this->validate();

        $employeeEmailExists = User::where('email', $this->employee['email'])->first();

        if ($employeeEmailExists) {
            $this->addError('employee.email', 'Employee email address already exists.');
            return;
        }

        $authorId = auth()->user()->id;
        $this->employee['created_by'] = $authorId;
        $this->employee['updated_by'] = $authorId;


        try {
            $this->employee['password'] = Hash::make(config('app.default_user_password'));
            $employee = User::create($this->employee);

            if ($employee) {
                session()->flash('success', 'Employee created successfully.');
                return redirect()->route('employees.index');
            }
            session()->flash('info', 'Something went wrong employee not created');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function update()
    {
        $this->validate();

        $employeeEmailExists = User::where('email', $this->employee['email'])->where('id', '!=', $this->employee['id'])->first();
        if ($employeeEmailExists) {
            $this->addError('employee.email', 'Employee Email Address already exists.');
            return;
        }

        $authorId = auth()->user()->id;
        $this->employee['updated_by'] = $authorId;

        try {
            $employee = User::find($this->employee['id']);
            $employee->update($this->employee);
            session()->flash('success', 'Employee updated successfully.');
            return redirect()->route('employees.index');
        } catch (\Exception $e) {

            return session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.settings.employee.employee-handler')
            ->layout('layouts.admin');
    }
}
