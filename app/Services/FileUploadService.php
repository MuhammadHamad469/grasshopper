<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;

class FileUploadService
{
    protected string $bucket;
    protected string $region;
    protected string $accessKey;
    protected string $secretKey;

    public function __construct()
    {
        $this->bucket = env('AWS_BUCKET') ?? '';
        $this->region = env('AWS_DEFAULT_REGION') ?? '';
        $this->accessKey = env('AWS_ACCESS_KEY_ID') ?? '';
        $this->secretKey = env('AWS_SECRET_ACCESS_KEY') ?? '';

        if (!App::environment(['local', 'dev', 'development'])) {
            if (empty($this->bucket) || empty($this->region) || empty($this->accessKey) || empty($this->secretKey)) {
            }
        }
    }

    public function upload(UploadedFile $file, string $folder = ''): string
    {
        $clientName = env('APP_NAME');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $folderPath = public_path("uploads/{$clientName}" . ($folder ? "/{$folder}" : ''));
        if (!is_dir($folderPath)) {
            mkdir($folderPath, 0777, true);
        }
        $file->move($folderPath, $fileName);
        return "uploads/{$clientName}" . ($folder ? "/{$folder}" : '') . "/{$fileName}";
    }

    public function getUrl(string $filePath): string
    {
        return asset($filePath);
    }
}
