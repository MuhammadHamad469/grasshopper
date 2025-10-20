<?php

namespace App\Http\Controllers\Tenant;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class EmployeeController extends Controller
{
	/**
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$employees = Employee::with('user')->paginate(10);
		return view('employees.index', compact('employees'));
	}

	/**
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		$users = User::all();
		return view('employees.create', compact('users'));
	}

	/**
	 * Store a newly created employee in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
{
    $validated = $this->validateEmployee($request);
    
    // Handle file upload if there's a picture
    if ($request->hasFile('picture')) {
        $validated['picture_path'] = $this->uploadPicture($request);
    }
    
    // First create the employee with basic data
    $employee = Employee::create($validated);
    
    // Then call SimplePay API to create the employee there
    $this->createEmployeeInSimplePay($employee);
    
    return redirect()->route('employees.index')
            ->with('success', 'Employee created successfully.');
}

protected function createEmployeeInSimplePay(Employee $employee)
{
    $client = new \GuzzleHttp\Client();
    
    $data = [
        'first_name' => $employee->user->first_name ?? '',
        'last_name' => $employee->user->last_name ?? '',
        'email' => $employee->email,
        'birthdate' => $employee->date_of_birth ? $employee->date_of_birth->format('Y-m-d') : null,
        'identification_type' => 'rsa_id',
        'identifying_number' => $request->id_number ?? '', // You might need to add this field
        'job_title' => $employee->position,
        'appointment_date' => $employee->start_date ? $employee->start_date->format('Y-m-d') : null,
        'classification_inputs' => [
            'employment_type' => $this->mapEmployeeType($employee->employee_type),
            'is_director' => 'no',
            'uif_exempt_question' => 'no',
        ],
        'bank_account' => [
            'account_number' => $employee->bank_account_number,
            'account_type' => 1, // 1 = Cheque account
            'branch_code' => $request->branch_code ?? '', // You might need to add this field
        ],
        'take_on_attribs' => [
            'code_3601' => $this->calculateMonthlySalary($employee), // Monthly salary
            'code_4141' => '150', // UIF
            'code_4142' => '150', // UIF
        ],
    ];
    
    try {
        $response = $client->post('https://api.payroll.simplepay.cloud/v1/employees', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => env('SIMPLEPAY_API_KEY'),
                'Cookie' => 'country_code=za',
            ],
            'json' => $data,
        ]);
        
        $responseData = json_decode($response->getBody(), true);
        
        // Update the employee with SimplePay ID
        $employee->update([
            'employee_id' => $responseData['employee']['id'],
            'tax_number' => $responseData['employee']['income_tax_number'] ?? null,
        ]);
        
    } catch (\Exception $e) {
        Log::error('Failed to create employee in SimplePay: ' . $e->getMessage());
        // You might want to handle this error differently
    }
}

protected function mapEmployeeType($type)
{
    $mapping = [
        'regular' => 'full_time',
        'contract' => 'fixed_term',
        'part_time' => 'part_time',
        'temporary' => 'casual',
    ];
    
    return $mapping[$type] ?? 'full_time';
}

protected function calculateMonthlySalary(Employee $employee)
{
    // Assuming daily_rate is the base for calculation
    // 22 working days in a month is a common standard
    return $employee->daily_rate * 22;
}

public function syncFromSimplePay(Employee $employee)
{
    if (!$employee->employee_id) {
        return;
    }
    
    $client = new \GuzzleHttp\Client();
    
    try {
        $response = $client->get("https://api.payroll.simplepay.cloud/v1/employees/{$employee->employee_id}", [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => env('SIMPLEPAY_API_KEY'),
                'Cookie' => 'country_code=za',
            ],
        ]);
        
        $responseData = json_decode($response->getBody(), true);
        $simplePayEmployee = $responseData['employee'];
        
        // Map SimplePay data to your database fields
        $updateData = [
            'position' => $simplePayEmployee['job_title'] ?? $employee->position,
            'bank_account_number' => $simplePayEmployee['bank_account']['account_number'] ?? $employee->bank_account_number,
            'tax_number' => $simplePayEmployee['income_tax_number'] ?? $employee->tax_number,
            'date_of_birth' => isset($simplePayEmployee['birthdate']) ? $simplePayEmployee['birthdate'] : $employee->date_of_birth,
            // Add other fields as needed
        ];
        
        $employee->update($updateData);
        
    } catch (\Exception $e) {
        Log::error('Failed to sync employee from SimplePay: ' . $e->getMessage());
    }
}
	/**
	 * Display the specified employee.
	 *
	 * @param  \App\Models\Employee  $employee
	 * @return \Illuminate\Http\Response
	 */
	public function show(Employee $employee)
	{
		return view('employees.show', compact('employee'));
	}

	/**
	 * Show the form for editing the specified employee.
	 *
	 * @param  \App\Models\Employee  $employee
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Employee $employee)
	{
		$users = User::all();
		return view('employees.edit', compact('employee', 'users'));
	}

	/**
	 * Update the specified employee in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \App\Models\Employee  $employee
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, Employee $employee)
	{
		$validated = $this->validateEmployee($request, $employee->id);

		// Handle file upload if there's a new picture
		if ($request->hasFile('picture')) {
			// Delete old picture if it exists
			if ($employee->picture_path) {
				Storage::delete('public/' . $employee->picture_path);
			}

			$validated['picture_path'] = $this->uploadPicture($request);
		}

		$employee->update($validated);

		return redirect()->route('employees.index')
				->with('success', 'Employee updated successfully.');
	}

	/**
	 * Remove the specified employee from storage.
	 *
	 * @param  \App\Models\Employee  $employee
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Employee $employee)
	{
		// Delete employee picture if it exists
		if ($employee->picture_path) {
			Storage::delete('public/' . $employee->picture_path);
		}

		$employee->delete();

		return redirect()->route('employees.index')
				->with('success', 'Employee deleted successfully.');
	}

	/**
	 * Validate employee data.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int|null  $employeeId
	 * @return array
	 */
	private function validateEmployee(Request $request, $employeeId = null)
	{
		$emailRule = 'required|email|unique:employees,email';
		$bankAccountRule = 'nullable|unique:employees,bank_account_number';
		$taxNumberRule = 'nullable|unique:employees,tax_number';

		// If we're updating an existing employee, exclude the current employee from unique checks
		if ($employeeId) {
			$emailRule .= ',' . $employeeId;
			$bankAccountRule .= ',' . $employeeId;
			$taxNumberRule .= ',' . $employeeId;
		}

		return $request->validate([
				'user_id' => 'nullable|exists:users,id',
				'phone_number' => 'nullable|string',
				'email' => $emailRule,
				'emergency_contact_name' => 'nullable|string',
				'emergency_contact_phone' => 'nullable|string',
				'date_of_birth' => 'nullable|date',
				'position' => 'nullable|string',
				'employee_type' => 'required|string',
				'daily_rate' => 'required|numeric|min:0',
				'overtime_rate' => 'nullable|numeric|min:0',
				'leave_days_allowed' => 'integer|min:0',
				'sick_days_allowed' => 'integer|min:0',
				'bank_name' => 'nullable|string',
				'bank_account_number' => $bankAccountRule,
				'tax_number' => $taxNumberRule,
				'picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
				'start_date' => 'nullable|date',
				'end_date' => 'nullable|date|after_or_equal:start_date',
		]);
	}

	/**
	 * Upload employee picture.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return string
	 */
	private function uploadPicture(Request $request)
	{
		$path = $request->file('picture')->store('employee_pictures', 'public');
		return str_replace('public/', '', $path);
	}

	############################

	/**
 * Map SimplePay API response to our database fields
 */
protected function mapSimplePayToLocal(array $simplePayEmployee): array
{
    $bankAccount = $simplePayEmployee['bank_account'] ?? [];
    $payslipData = $simplePayEmployee['take_on_object']['payslip_outputs'] ?? [];

    return [
        'employee_id' => $simplePayEmployee['id'],
        'phone_number' => $simplePayEmployee['cell_no'] ?? null,
        'email' => $simplePayEmployee['email'],
        'date_of_birth' => $simplePayEmployee['birthdate'] ?? null,
        'id_number' => $simplePayEmployee['id_number'] ?? null,
        'position' => $simplePayEmployee['job_title'] ?? 'general',
        'employee_type' => $this->normalizeEmployeeType($simplePayEmployee['classification_inputs']['employment_type'] ?? 'regular'),
        'daily_rate' => $this->calculateDailyRate($simplePayEmployee['take_on_object']['payslip_inputs']['generic_taxable_income'] ?? 0),
        'bank_account_number' => $bankAccount['account_number'] ?? null,
        'tax_number' => $simplePayEmployee['income_tax_number'] ?? null,
        'start_date' => $simplePayEmployee['appointment_date'] ?? null,
        'leave_days_allowed' => $payslipData['annual_leave_entitlement'] ?? 0,
        'leave_days_taken' => $payslipData['annual_leave_taken'] ?? 0,
        'sick_days_allowed' => $payslipData['sick_leave_balance'] ?? 0,
        'sick_days_taken' => $payslipData['sick_leave_taken'] ?? 0,
    ];
}

protected function normalizeEmployeeType(string $apiType): string
{
    $types = [
        'full_time' => 'regular',
        'part_time' => 'part_time',
        'contract' => 'contract',
        'temporary' => 'temporary'
    ];
    return $types[strtolower($apiType)] ?? 'regular';
}

protected function calculateDailyRate(?float $monthlySalary): float
{
    return $monthlySalary ? round($monthlySalary / 22, 2) : 0.00;
}

protected function getBankName(?int $bankId): ?string
{
    // Implement your bank ID to name mapping here
    return null;
}

public function fetchAndSaveFromSimplePay($employeeId)
{
    $client = new \GuzzleHttp\Client();

    try {
        // Fetch from SimplePay API
        $response = $client->get("https://api.payroll.simplepay.cloud/v1/employees/{$employeeId}", [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => env('SIMPLEPAY_API_KEY'),
                'Cookie' => 'country_code=za',
            ],
        ]);

        $responseData = json_decode($response->getBody(), true);
        $simplePayEmployee = $responseData['employee'];

        // Map API data to your DB fields
        $dataToSave = [
            'employee_id' => $simplePayEmployee['id'] ?? null,
            'email' => $simplePayEmployee['email'] ?? null,
            'position' => $simplePayEmployee['job_title'] ?? null,
            'date_of_birth' => $simplePayEmployee['birthdate'] ?? null,
            'bank_account_number' => $simplePayEmployee['bank_account']['account_number'] ?? null,
            'tax_number' => $simplePayEmployee['income_tax_number'] ?? null,
            // add other fields you want to save
        ];

        // Check if employee exists locally by employee_id
        $employee = Employee::where('employee_id', $employeeId)->first();

        if ($employee) {
            // Update existing employee
            $employee->update($dataToSave);
        } else {
            // Create new employee
            Employee::create($dataToSave);
        }

        return response()->json([
            'message' => 'Employee data fetched and saved successfully',
            'data' => $dataToSave,
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Failed to fetch/save employee data: ' . $e->getMessage()
        ], 500);
    }
}
}