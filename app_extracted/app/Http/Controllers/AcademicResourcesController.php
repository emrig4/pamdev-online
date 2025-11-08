<?php

namespace App\Http\Controllers;

use App\Imports\AcademicResourcesImport;
use App\Exports\AcademicResourcesTemplateExport;
use App\Exports\AcademicResourcesExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AcademicResourcesController extends Controller
{
    /**
     * Download Academic Resources Excel template (10 columns)
     */
    public function downloadTemplate()
    {
        try {
            return Excel::download(new AcademicResourcesTemplateExport(), 'academic_resources_template.xlsx');
        } catch (\Exception $e) {
            Log::error('Academic template download error: ' . $e->getMessage());
            return back()->with('error', 'Failed to download template. Please try again.');
        }
    }

    /**
     * Show Academic Resources upload form
     */
    public function showUploadForm()
    {
        return view('admin.academic.upload');
    }

    /**
     * Handle Academic Resources Excel file upload
     */
    public function uploadExcel(Request $request)
    {
        // Validate the request
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // Max 10MB for larger files
        ], [
            'excel_file.required' => 'Please select an Excel file to upload.',
            'excel_file.file' => 'Please upload a valid file.',
            'excel_file.mimes' => 'File must be in Excel format (.xlsx, .xls, or .csv).',
            'excel_file.max' => 'File size must be less than 10MB.',
        ]);

        try {
            // Check if file exists and is readable
            if (!$request->file('excel_file')->isValid()) {
                return back()->with('error', 'Invalid file uploaded. Please try again.');
            }

            $file = $request->file('excel_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads/excel', $fileName, 'public');

            // Import the data
            $import = new AcademicResourcesImport();
            Excel::import($import, $file, 'public');

            // Get results
            $results = $import->getResults();
            $successCount = count($results['success']);
            $errorCount = count($results['errors']);

            // Log the upload
            Log::channel('excel')->info("Academic Resources upload", [
                'file_name' => $fileName,
                'total_rows' => $import->getTotalRows(),
                'successful_imports' => $successCount,
                'failed_imports' => $errorCount,
                'user_id' => auth()->id(),
                'timestamp' => now()
            ]);

            // Store results in session for display
            session([
                'academic_upload_results' => [
                    'file_name' => $fileName,
                    'total_rows' => $import->getTotalRows(),
                    'success_count' => $successCount,
                    'error_count' => $errorCount,
                    'success' => $results['success'],
                    'errors' => $results['errors'],
                    'uploaded_at' => now()
                ]
            ]);

            return redirect()->route('admin.academic.results')
                           ->with('upload_complete', true);

        } catch (\Maatwebsite\Excel\Exceptions\NoSuchMappingException $e) {
            Log::error('Excel mapping error: ' . $e->getMessage());
            return back()->with('error', 'Invalid file structure. Please use the provided template.');
        } catch (\Exception $e) {
            Log::error('Academic upload error: ' . $e->getMessage());
            return back()->with('error', 'Upload failed: ' . $e->getMessage());
        }
    }

    /**
     * Show upload results
     */
    public function showResults()
    {
        $results = session('academic_upload_results');
        
        if (!$results) {
            return redirect()->route('admin.academic.upload')
                           ->with('error', 'No upload results found. Please upload a file first.');
        }

        return view('admin.academic.results', compact('results'));
    }

    /**
     * Export all academic resources to Excel
     */
    public function exportAll()
    {
        try {
            return Excel::download(new AcademicResourcesExport(), 'all_academic_resources.xlsx');
        } catch (\Exception $e) {
            Log::error('Academic export error: ' . $e->getMessage());
            return back()->with('error', 'Failed to export resources. Please try again.');
        }
    }

    /**
     * Show academic resources history/management
     */
    public function showHistory()
    {
        // Get recent academic resources
        $resources = \App\Models\Resource::where('is_academic', true)
                        ->orderBy('created_at', 'desc')
                        ->paginate(50);

        // Calculate statistics
        $stats = [
            'total_resources' => \App\Models\Resource::where('is_academic', true)->count(),
            'total_value' => \App\Models\Resource::where('is_academic', true)->sum('price'),
            'by_field' => \App\Models\Resource::where('is_academic', true)
                        ->selectRaw('field, COUNT(*) as count, SUM(price) as total_value')
                        ->groupBy('field')
                        ->get(),
            'by_type' => \App\Models\Resource::where('is_academic', true)
                        ->selectRaw('type, COUNT(*) as count')
                        ->groupBy('type')
                        ->get(),
        ];

        return view('admin.academic.history', compact('resources', 'stats'));
    }

    /**
     * AJAX validation of Excel file before upload
     */
    public function validateFile(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls,csv|max:10240'
            ]);

            $file = $request->file('file');
            
            // Basic file validation
            $validation = [
                'valid' => true,
                'size' => $file->getSize(),
                'extension' => $file->getClientOriginalExtension(),
                'original_name' => $file->getClientOriginalName()
            ];

            return response()->json($validation);

        } catch (\Exception $e) {
            return response()->json([
                'valid' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get statistics for academic resources
     */
    public function getStats()
    {
        try {
            $stats = [
                'total' => \App\Models\Resource::where('is_academic', true)->count(),
                'published' => \App\Models\Resource::where('is_academic', true)->where('is_published', true)->count(),
                'draft' => \App\Models\Resource::where('is_academic', true)->where('is_published', false)->count(),
                'total_value' => \App\Models\Resource::where('is_academic', true)->sum('price'),
                'recent_uploads' => \App\Models\Resource::where('is_academic', true)
                    ->where('created_at', '>=', now()->subDays(7))
                    ->count()
            ];

            return response()->json($stats);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
