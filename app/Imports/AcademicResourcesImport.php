<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Resource;

class AcademicResourcesImport implements ToModel, WithHeadingRow, WithChunkReading, WithBatchInserts
{
    protected $results = [
        'success' => [],
        'errors' => []
    ];
    
    protected $totalRows = 0;

    /**
     * Process each row
     */
    public function model(array $row)
    {
        $this->totalRows++;
        
        try {
            // Validate required fields
            $requiredFields = ['filename', 'title', 'overview', 'author', 'type', 'field', 'currency', 'price'];
            foreach ($requiredFields as $field) {
                if (empty($row[$field]) && $row[$field] !== '0') {
                    throw new \Exception("Required field '{$field}' is missing or empty");
                }
            }

            // Generate slug from title
            $slug = Str::slug($row['title']);
            $originalSlug = $slug;
            $counter = 1;
            
            // Ensure slug is unique
            while (Resource::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            // Clean and validate data
            $data = [
                'filename' => $this->cleanText($row['filename']),
                'title' => $this->cleanText($row['title']),
                'overview' => $this->cleanHtml($row['overview']),
                'author' => $this->cleanText($row['author']),
                'coauthors' => isset($row['coauthors']) ? $this->cleanText($row['coauthors']) : null,
                'type' => $this->validateType($row['type']),
                'field' => $this->validateField($row['field']),
                'sub_fields' => isset($row['sub_fields']) ? $this->cleanText($row['sub_fields']) : null,
                'currency' => $this->validateCurrency($row['currency']),
                'price' => $this->validatePrice($row['price']),
                'preview_limit' => isset($row['preview_limit']) ? (int)$row['preview_limit'] : 10,
                'slug' => $slug,
                'is_published' => true, // Academic resources are published by default
                'is_academic' => true, // Mark as academic resource
            ];

            // Create Resource model
            $resource = Resource::create($data);

            // Log successful import
            $this->results['success'][] = [
                'row' => $this->totalRows,
                'title' => $resource->title,
                'message' => 'Successfully imported',
                'id' => $resource->id
            ];

            return $resource;

        } catch (\Exception $e) {
            // Log error
            $this->results['errors'][] = [
                'row' => $this->totalRows,
                'data' => $row,
                'error' => $e->getMessage()
            ];

            // Log the error for debugging
            Log::channel('excel')->error('Academic import error', [
                'row' => $this->totalRows,
                'data' => $row,
                'error' => $e->getMessage(),
                'timestamp' => now()
            ]);

            return null;
        }
    }

    /**
     * Get import results
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * Get total rows processed
     */
    public function getTotalRows()
    {
        return $this->totalRows;
    }

    /**
     * Clean text input
     */
    private function cleanText($value)
    {
        if (empty($value)) return '';
        return trim(strip_tags($value));
    }

    /**
     * Clean HTML content
     */
    private function cleanHtml($value)
    {
        if (empty($value)) return '';
        return strip_tags($value, '<p><br><strong><em><ul><ol><li>');
    }

    /**
     * Validate resource type
     */
    private function validateType($type)
    {
        $allowedTypes = ['project', 'book', 'article', 'tutorial', 'guide', 'manual', 'thesis', 'dissertation', 'journal'];
        $cleanedType = strtolower(trim($type));
        
        if (in_array($cleanedType, $allowedTypes)) {
            return $cleanedType;
        }
        
        return 'project'; // Default to 'project' if not in allowed types
    }

    /**
     * Validate and standardize field
     */
    private function validateField($field)
    {
        $fieldMappings = [
            'accounting' => 'accounting',
            'acc' => 'accounting',
            'engineering' => 'engineering',
            'chemical engineering' => 'chemical-engineering',
            'petroleum engineering' => 'petroleum-engineering',
            'mechanical engineering' => 'mechanical-engineering',
            'electrical engineering' => 'electrical-engineering',
            'civil engineering' => 'civil-engineering',
            'business administration' => 'business-administration',
            'business admin' => 'business-administration',
            'business' => 'business-administration',
            'management' => 'business-administration',
            'computer science' => 'computer-science',
            'computer science and information technology' => 'computer-science',
            'information technology' => 'computer-science',
            'it' => 'computer-science',
            'social and management sciences' => 'social-and-management-sciences',
            'social sciences' => 'social-and-management-sciences',
            'sociology' => 'social-and-management-sciences',
            'psychology' => 'social-and-management-sciences',
            'political science' => 'political-science',
            'political science and international studies' => 'political-science-and-international-studies',
            'political studies' => 'political-science',
            'international relations and diplomacy' => 'international-relations',
            'international relations' => 'international-relations',
            'diplomacy' => 'international-relations',
            'law' => 'law',
            'legal studies' => 'law',
            'common law' => 'law',
            'education' => 'education',
            'educational studies' => 'education',
            'medicine' => 'medicine',
            'medical sciences' => 'medicine',
            'medical and health sciences' => 'medicine',
            'agriculture' => 'agriculture',
            'agricultural studies' => 'agriculture',
            'agriculture economic' => 'agriculture',
            'media and communication studies' => 'media-studies',
            'communication' => 'media-studies',
            'journalism' => 'media-studies',
            'banking and finance' => 'banking',
            'banking' => 'banking',
            'finance' => 'banking',
            'economics and management sciences' => 'economics',
            'economics' => 'economics',
            'public relation' => 'public-relations',
            'public relations' => 'public-relations',
            'pr' => 'public-relations',
        ];

        $cleanedField = strtolower(trim($field));
        
        foreach ($fieldMappings as $key => $value) {
            if (strpos($cleanedField, $key) !== false) {
                return $value;
            }
        }
        
        return str_replace([' ', '-'], '-', $cleanedField);
    }

    /**
     * Validate currency
     */
    private function validateCurrency($currency)
    {
        $allowedCurrencies = ['NGN', 'USD', 'EUR', 'GBP', 'CAD', 'AUD', 'JPY'];
        $cleanedCurrency = strtoupper(trim($currency));
        
        if (in_array($cleanedCurrency, $allowedCurrencies)) {
            return $cleanedCurrency;
        }
        
        return 'NGN';
    }

    /**
     * Validate price
     */
    private function validatePrice($price)
    {
        $numericPrice = is_numeric($price) ? (float)$price : 0;
        
        if ($numericPrice < 0) {
            throw new \Exception("Price cannot be negative");
        }
        
        return $numericPrice;
    }

    /**
     * Batch size for chunk reading
     */
    public function batchSize(): int
    {
        return 100;
    }

    /**
     * Chunk size for processing
     */
    public function chunkSize(): int
    {
        return 100;
    }
}
