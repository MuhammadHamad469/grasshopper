<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Smmes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\Validator;
use App\Services\SmmeExportService;

class SmmeController extends Controller
{
	protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

	public function index(Request $request)
	{
		$query = Smmes::query();

		// Apply search filter
		if ($request->filled('search')) {
			$search = $request->search;
			$query->where(function ($q) use ($search) {
				$q->where('name', 'like', "%{$search}%");
//						->orWhere('registration_number', 'like', "%{$search}%")
//						->orWhere('grade', 'like', "%{$search}%")
//						->orWhere('status', 'like', "%{$search}%");
			});
		}

		// Apply grade filter
		if ($request->filled('grade')) {
			$query->where('grade', $request->grade);
		}

		// Apply status filter
		if ($request->filled('status')) {
			$query->where('status', $request->status);
		}

		// Apply documents filter
		if ($request->filled('documents')) {
			$query->where('documents_verified', $request->documents);
		}

		// Apply experience range filter
		if ($request->filled('experience_min')) {
			$query->where('years_of_experience', '>=', $request->experience_min);
		}
		if ($request->filled('experience_max')) {
			$query->where('years_of_experience', '<=', $request->experience_max);
		}

		// Apply sorting
		$sortColumn = $request->get('sort', 'name');
		$sortDirection = $request->get('direction', 'asc');
		$allowedColumns = ['name', 'grade', 'status', 'years_of_experience'];

		if (in_array($sortColumn, $allowedColumns)) {
			$query->orderBy($sortColumn, $sortDirection);
		}

		$smmes = $query->paginate(10);

		return view('tenant.smmes.index', compact('smmes'));
	}


	public function create()
	{
		return view('tenant.smmes.create');
	}


	public function store(Request $request)
	{
		$request->merge([
				'documents_verified' => $request->input('documents_verified') == 'true',
		]);
		$validatedData = $request->validate([
				'name' => 'required|max:255',
				'registration_number' => 'required|string',
				'years_of_experience' => 'required|integer',
				'team_composition' => 'required',
				'documents_verified' => 'required|boolean',
				'grade' => 'required|max:255',
				'status' => 'required|in:green,yellow,red',       
				'company_registration' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
				'tax_certificate' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
				'bee_certificate' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
				'company_profile' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
		]);
    // Handle file uploads
    $uploadedFiles = $this->uploadFiles($request);

    // Merge the file paths with the validated data
    $data = array_merge($validatedData, $uploadedFiles);

    // Create the SMME record
    Smmes::create($data);

		return redirect()->route('tenant.smmes.index')->with('success', 'SMME created successfully.');
	}

	private function uploadFiles($request)
    {
        $uploadedFiles = [];

        $documents = [
            'company_registration',
            'tax_certificate',
            'bee_certificate',
            'company_profile',
        ];

        foreach ($documents as $document) {
            if ($request->hasFile($document)) {
                $file = $request->file($document);
                // Use the FileUploadService to upload the file to S3
                $filePath = $this->fileUploadService->upload($file, 'smme/documents');
                $uploadedFiles[$document] = $filePath;
            }
        }
    return $uploadedFiles;
}


	public function show(Smmes $smme)
	{
		return view('tenant.smmes.show', compact('smme'));
	}


	public function edit(Smmes $smme)
	{
		return view('tenant.smmes.edit', compact('smme'));
	}


	public function update(Request $request, Smmes $smme)
	{
		// Merge and validate the request data
		$request->merge([
			'documents_verified' => $request->input('documents_verified') == 'true',
		]);
	
		$validatedData = $request->validate([
			'name' => 'required|max:255',
			'registration_number' => 'required|string',
			'years_of_experience' => 'required|integer',
			'team_composition' => 'required',
			'documents_verified' => 'required|boolean',
			'grade' => 'required|max:255',
			'status' => 'required|in:green,yellow,red',
			'company_registration' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
			'tax_certificate' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
			'bee_certificate' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
			'company_profile' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
		]);
	
		// Debugging: Log validated data
		Log::info("Validated data: " . print_r($validatedData, true));
	
		// Handle file uploads
		$fileFields = [
			'company_registration',
			'tax_certificate',
			'bee_certificate',
			'company_profile',
		];
	
		foreach ($fileFields as $field) {
			if ($request->hasFile($field)) {
				// Delete the old file if it exists
				if ($smme->$field) {
					Storage::delete($smme->$field);
				}
	
				// Store the new file
				$path = $request->file($field)->store('uploads/smmes/documents', 'public');
				$validatedData[$field] = $path; // Save the file path
			} else {
				// Retain the existing file path if no new file is uploaded
				$validatedData[$field] = $smme->$field;
			}
		}
	
		$smme->update($validatedData);

		return redirect()->route('tenant.smmes.index')->with('success', 'SMME updated successfully.');
	}

	public function exportToExcel(Request $request, SmmeExportService $exportService)
    {
        return $exportService->exportToExcel($request);
    }


	public function destroy(Smmes $smme)
	{
		$smme->delete();

		return redirect()->route('tenant.smmes.index')->with('success', 'SMME deleted successfully.');
	}

}