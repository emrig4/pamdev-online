<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use App\Models\Resource;

class AcademicResourcesExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting, WithStyles, WithColumnWidths
{
    /**
     * Export all academic resources
     */
    public function collection()
    {
        return Resource::where('is_academic', true)
                     ->orderBy('created_at', 'desc')
                     ->get();
    }

    /**
     * Define headings
     */
    public function headings(): array
    {
        return [
            'filename',
            'title',
            'overview',
            'author',
            'coauthors',
            'type',
            'field',
            'sub_fields',
            'currency',
            'price',
            'preview_limit',
            'slug',
            'is_published',
            'created_at',
            'updated_at'
        ];
    }

    /**
     * Map each row data
     */
    public function map($resource): array
    {
        return [
            $resource->filename ?? '',
            $resource->title ?? '',
            $resource->overview ?? '',
            $resource->author ?? '',
            $resource->coauthors ?? '',
            $resource->type ?? '',
            $resource->field ?? '',
            $resource->sub_fields ?? '',
            $resource->currency ?? 'NGN',
            $resource->price ?? 0,
            $resource->preview_limit ?? 10,
            $resource->slug ?? '',
            $resource->is_published ? 'Yes' : 'No',
            $resource->created_at ? $resource->created_at->format('Y-m-d H:i:s') : '',
            $resource->updated_at ? $resource->updated_at->format('Y-m-d H:i:s') : ''
        ];
    }

    /**
     * Format columns
     */
    public function columnFormats(): array
    {
        return [
            'J' => '0.00', // Price column
            'K' => '0',    // Preview limit column
            'M' => '@',    // Published status as text
            'N' => 'yyyy-mm-dd hh:mm:ss', // Created date
            'O' => 'yyyy-mm-dd hh:mm:ss', // Updated date
        ];
    }

    /**
     * Set column widths
     */
    public function columnWidths(): array
    {
        return [
            'A' => 50, // filename
            'B' => 40, // title
            'C' => 60, // overview
            'D' => 25, // author
            'E' => 25, // coauthors
            'F' => 15, // type
            'G' => 25, // field
            'H' => 30, // sub_fields
            'I' => 10, // currency
            'J' => 12, // price
            'K' => 12, // preview_limit
            'L' => 30, // slug
            'M' => 12, // is_published
            'N' => 20, // created_at
            'O' => 20, // updated_at
        ];
    }

    /**
     * Apply styles to the worksheet
     */
    public function styles(Worksheet $sheet)
    {
        $sheet->getDefaultRowDimension()->setRowHeight(20);
        
        // Header row styling
        $sheet->getStyle('A1:O1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2F5597']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ]
        ]);

        // Data rows styling
        $sheet->getStyle('A2:O10000')->applyFromArray([
            'font' => [
                'size' => 10
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC']
                ]
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_TOP,
                'wrapText' => true
            ]
        ]);

        // Alternate row colors for better readability
        for ($row = 2; $row <= $sheet->getHighestRow(); $row += 2) {
            $sheet->getStyle('A' . $row . ':O' . $row)->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F8F9FA']
                ]
            ]);
        }
    }
}
