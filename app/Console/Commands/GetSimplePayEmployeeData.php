<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class GetSimplePayEmployeeData extends Command
{
    protected $signature = 'sync:simplepay {employeeId}';
    protected $description = 'Fetch employee data from SimplePay and save to local DB';

    public function handle()
    {
        $employeeId = $this->argument('employeeId');
        $client = new Client();
    
        try {
            $response = $client->get("https://api.payroll.simplepay.cloud/v1/employees/{$employeeId}", [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => env('SIMPLEPAY_API_KEY'),
                    'Cookie' => 'country_code=za',
                ],
            ]);
    
            $responseData = json_decode($response->getBody(), true);
            $simplePayEmployee = $responseData['employee'];
            $bankAccount = $simplePayEmployee['bank_account'] ?? [];
            $payslipOutputs = $simplePayEmployee['take_on_object']['payslip_outputs'] ?? [];
            $payslipInputs = $simplePayEmployee['take_on_object']['payslip_inputs'] ?? [];
            $physicalAddress = $simplePayEmployee['physical_address'] ?? [];
            $classificationInputs = $simplePayEmployee['classification_inputs'] ?? [];

            // Map all fields from API to database
            $dataToSave = [
                // Personal Information
                'employee_id' => $simplePayEmployee['id'] ?? null,
                'first_name' => $simplePayEmployee['first_name'] ?? null,
                'last_name' => $simplePayEmployee['last_name'] ?? null,
                'date_of_birth' => $simplePayEmployee['birthdate'] ?? null,
                'id_number' => $simplePayEmployee['id_number'] ?? $simplePayEmployee['identifying_number'] ?? null,
                'gender' => $simplePayEmployee['gender'] ?? null,
                
                // Contact Information
                'phone_number' => $simplePayEmployee['cell_no'] ?? $simplePayEmployee['bus_tel_no'] ?? null,
                'email' => $simplePayEmployee['email'] ?? null,
                'street_address' => $physicalAddress['street_or_farm_name'] ?? null,
                'suburb' => $physicalAddress['suburb_or_district'] ?? null,
                'city' => $physicalAddress['city_or_town'] ?? null,
                'postal_code' => $physicalAddress['code'] ?? null,
                
                // Employment Details
                'position' => $simplePayEmployee['job_title'] ?? 'general',
                'employee_type' => $this->mapEmployeeType($classificationInputs['employment_type'] ?? 'regular'),
                'start_date' => $simplePayEmployee['appointment_date'] ?? null,
                'is_director' => $classificationInputs['is_director'] === 'yes',
                
                // Financial Information
                'bank_name' => $this->getBankName($bankAccount['bank_id'] ?? null),
                'bank_account_number' => $bankAccount['account_number'] ?? null,
                'branch_code' => $bankAccount['branch_code'] ?? null,
                'monthly_salary' => $payslipInputs['generic_taxable_income'] ?? 0,
                'tax_number' => $simplePayEmployee['income_tax_number'] ?? null,
                'payment_method' => $simplePayEmployee['payment_method'] ?? null,
                
                // Leave Balances
                'annual_leave_entitlement' => $payslipInputs['annual_leave_entitlement'] ?? 0,
                'annual_leave_balance' => $payslipOutputs['annual_leave_balance'] ?? 0,
                'sick_leave_balance' => $payslipOutputs['sick_leave_balance'] ?? 0,
                'compassionate_leave_balance' => $payslipOutputs['compassionate_leave_balance'] ?? 0,
                
                // Calculated fields
                'daily_rate' => $this->calculateDailyRate($payslipInputs['generic_taxable_income'] ?? 0),
            ];
    
            // Try to find by employee_id first
            $employee = Employee::where('employee_id', $employeeId)->first();
    
            // If not found by employee_id, try by email
            if (!$employee && !empty($dataToSave['email'])) {
                $employee = Employee::where('email', $dataToSave['email'])->first();
            }
    
            if ($employee) {
                // Update existing employee
                $employee->update($dataToSave);
                $this->info("Employee {$employeeId} updated successfully.");
            } else {
                // Create new employee with required defaults
                $newEmployeeData = array_merge($dataToSave, [
                    'emergency_contact_name' => null,
                    'emergency_contact_phone' => null,
                    'overtime_rate' => 0.00,
                    'days_absent' => 0,
                    'days_present' => 0,
                    'picture_path' => null,
                    'end_date' => null,
                ]);
                
                Employee::create($newEmployeeData);
                $this->info("Employee {$employeeId} created successfully.");
            }
    
        } catch (\Exception $e) {
            $this->error('Failed to sync employee: ' . $e->getMessage());
            Log::error('SyncSimplePayEmployee error: ' . $e->getMessage());
            return 1;
        }
    
        return 0;
    }

    protected function mapEmployeeType(?string $apiType): string
    {
        $mapping = [
            'full_time' => 'regular',
            'part_time' => 'part_time',
            'contract' => 'contract',
            'temporary' => 'temporary',
            'casual' => 'temporary',
            'fixed_term' => 'contract'
        ];
        
        return $mapping[strtolower($apiType)] ?? 'regular';
    }

    protected function calculateDailyRate(?float $monthlySalary): float
    {
        return $monthlySalary ? round($monthlySalary / 22, 2) : 0.00;
    }

    protected function getBankName(?int $bankId): ?string
    {
        $bankMapping = [
            2081327592 => 'Standard Bank',
            // Add more bank mappings as needed
        ];
        
        return $bankMapping[$bankId] ?? null;
    }
}