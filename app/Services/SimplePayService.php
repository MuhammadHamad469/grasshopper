<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class SimplePayService
{
    protected $apiKey;
    protected $baseUrl;
    protected $clientId;
    protected $client;

    public function __construct()
    {
        $this->apiKey   = config('services.simplepay.api_key');
        $this->baseUrl  = config('services.simplepay.base_url');
        $this->clientId = config('services.simplepay.client_id');

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout'  => 10.0,
        ]);
    }

    public function getAllEmployees()
{
    try {
        $url = "{$this->baseUrl}/clients/{$this->clientId}/employees";

        $response = $this->client->request('GET', $url, [
            'headers' => [
                'Content-Type'  => 'application/json',
                'Authorization' => $this->apiKey,
                'Cookie'        => 'country_code=za',
            ],
        ]);

        return json_decode($response->getBody(), true);
    } catch (RequestException $e) {
        return [
            'error'   => true,
            'message' => $e->getMessage(),
            'status'  => $e->getCode(),
        ];
    }
}
/**
 * Sync all employees from SimplePay
 */
public function syncAllFromSimplePay(Request $request)
{
    try {
        $client = new \GuzzleHttp\Client();
        $apiKey = config('services.simplepay.api_key');
        $clientId = config('services.simplepay.client_id');
        
        $response = $client->get("https://api.payroll.simplepay.cloud/v1/clients/{$clientId}/employees", [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => $apiKey,
                'Cookie' => 'country_code=za',
            ],
        ]);
        
        $employeesData = json_decode($response->getBody(), true);
        $employees = $employeesData['data'] ?? $employeesData['employees'] ?? $employeesData;
        
        if (!is_array($employees)) {
            throw new \Exception('Invalid employee data format from SimplePay');
        }
        
        $synced = 0;
        $skipped = 0;
        
        foreach ($employees as $simplePayEmployee) {
            try {
                $employeeData = $this->mapSimplePayToLocal($simplePayEmployee['employee']);
                
                if (empty($employeeData['employee_id']) || empty($employeeData['email'])) {
                    $skipped++;
                    continue;
                }
                
                Employee::updateOrCreate(
                    ['employee_id' => $employeeData['employee_id']],
                    $employeeData
                );
                
                $synced++;
            } catch (\Exception $e) {
                Log::error("Failed to sync employee {$employeeData['employee_id']}: " . $e->getMessage());
                $skipped++;
            }
        }
        
        return redirect()->route('employees.index')
            ->with('success', "Sync completed: {$synced} employees updated, {$skipped} skipped");
            
    } catch (\Exception $e) {
        Log::error('SimplePay sync failed: ' . $e->getMessage());
        return redirect()->route('employees.index')
            ->with('error', 'Failed to sync with SimplePay: ' . $e->getMessage());
    }
}
}