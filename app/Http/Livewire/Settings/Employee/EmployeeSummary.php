<?php

namespace App\Http\Livewire\Settings\Employee;

use App\Models\User;
use Livewire\Component;
use Illuminate\Http\Request;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;

class EmployeeSummary extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    protected $listeners = [
        'callShowNoticeEvent' => 'showNoticeListener',
    ];

    #[Url(as: 'pp')]
    public $perPage = 10;

    public $employeeId = null;

    public function mount(Request $request)
    {
        $this->employeeId = $request->employeeId ?? null;
    }
    public function showNoticeListener($status, $message)
    {
        session()->flash($status, $message);
    }

    public function resetPassword($employeeId)
    {
        $password = Hash::make("password");
        $userData = User::where('id', $employeeId)->update(['password' => $password]);
        if ($userData) {
            session()->flash('success', 'Password Reset Successfully');
            return redirect()->route('employees.index');
        }
        return session()->flash('error', 'Cannot Reset Password');
    }

    public function render()
    {

        $employees = User::orderBy('name')
            ->paginate($this->perPage, pageName: 'p');

        return view('livewire.settings.employee.employee-summary', [
            'employees' => $employees,
        ])->layout('layouts.admin');
    }

    public function changePageValue($perPageValue)
    {
        $this->perPage = $perPageValue;
        $this->resetPage(pageName: 'p');
    }
}
