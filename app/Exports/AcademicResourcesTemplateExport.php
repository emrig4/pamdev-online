<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class AcademicResourcesTemplateExport implements FromCollection, WithHeadings, WithColumnFormatting, WithStyles
{
    /**
     * Return sample data for template
     */
    public function collection()
    {
        return collect([
            [
                'filename' => 'ACADEMIA/THE_CHANGING_PATTERN_OF_IGBO_TRADITIONAL_MARRIAGE_CUSTOMS.pdf',
                'title' => 'THE CHANGING PATTERN OF IGBO TRADITIONAL MARRIAGE CUSTOMS',
                'overview' => 'A comprehensive study examining the evolution of traditional marriage customs among the Igbo people of Nigeria, exploring cultural changes and their impact on modern society.',
                'author' => 'ONOJA OCHE BEN',
                'type' => 'project',
                'field' => 'social-and-management-sciences',
                'sub_fields' => 'anthropology, cultural studies',
                'currency' => 'NGN',
                'price' => 5000,
                'preview_limit' => 15
            ],
            [
                'filename' => 'ACADEMIA/A_COMPARATIVE_STUDY_OF_STAFF_TURNOVER_IN_ENUGU_URBAN.pdf',
                'title' => 'ANALYSIS OF THE USE OF SOCIAL MEDIA NETWORKS BY STUDENTS OF AFE BABALOLA UNIVERSITY',
                'overview' => 'This study examines the impact of social media on student academic performance, analyzing usage patterns and their correlation with academic achievements.',
                'author' => 'ONASOGA KOLADE ADEDOYIN',
                'type' => 'project',
                'field' => 'media-studies',
                'sub_fields' => 'digital communication, educational psychology',
                'currency' => 'NGN',
                'price' => 3500,
                'preview_limit' => 10
            ],
            [
                'filename' => 'ACADEMIA/AN_APPRAISAL_OF_NIGERIA-CHINA_RELATIONS.pdf',
                'title' => 'AN APPRAISAL OF NIGERIA-CHINA POLITICAL AND ECONOMIC RELATIONS FROM 1999-2014',
                'overview' => 'An in-depth analysis of the political and economic relationships between Nigeria and China from 1999 to 2014, examining trade patterns and diplomatic evolution.',
                'author' => 'AGBAIM CHUKWUEBUKA OBICHUKWU',
                'type' => 'project',
                'field' => 'international-relations',
                'sub_fields' => 'diplomacy, international trade',
                'currency' => 'NGN',
                'price' => 4500,
                'preview_limit' => 12
            ],
            [
                'filename' => 'ACADEMIA/TRANSESTERIFICATION_OF_WASTE_VEGETABLE_OIL.pdf',
                'title' => 'TRANSESTERIFICATION OF WASTE VEGETABLE OIL USING ANTHILL AS CATALYST',
                'overview' => 'Research on biodiesel production using anthill as a catalyst, exploring sustainable energy solutions and waste management strategies.',
                'author' => 'KANU, OLANMA VICTORIA',
                'type' => 'project',
                'field' => 'chemical-engineering',
                'sub_fields' => 'renewable energy, environmental engineering',
                'currency' => 'NGN',
                'price' => 4000,
                'preview_limit' => 20
            ],
            [
                'filename' => 'ACADEMIA/CASH_AND_CREDIT_MANAGEMENT_IN_BANKS.pdf',
                'title' => 'CASH AND CREDIT MANAGEMENT AS A PANACEA FOR ILLIQUIDITY IN COMMERCIAL BANKS IN NIGERIA',
                'overview' => 'Comprehensive analysis of cash and credit management practices in Nigerian commercial banks and their impact on liquidity management.',
                'author' => 'ADEYEMI OLUWAGBEMISOLA ADEKIITAN',
                'type' => 'project',
                'field' => 'accounting',
                'sub_fields' => 'banking finance, risk management',
                'currency' => 'NGN',
                'price' => 3800,
                'preview_limit' => 8
            ]
        ]);
    }

    /**
     * Define column headings
     */
    public function headings(): array
    {
        return [
            'filename',
            'title',
            'overview',
            'author',
            'type',
            'field',
            'sub_fields',
            'currency',
            'price',
            'preview_limit'
        ];
    }

    /**
     * Format columns
     */
    public function columnFormats(): array
    {
        return [
            'I' => '0.00', // Price column
            'J' => '0',    // Preview limit column
        ];
    }

    /**
     * Apply styles to the worksheet
     */
    public function styles(Worksheet $sheet)
    {
        $sheet->getDefaultRowDimension()->setRowHeight(30);
        
        // Header row styling
        $sheet->getStyle('A1:J1')->applyFromArray([
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
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        // Data rows styling
        $sheet->getStyle('A2:J1000')->applyFromArray([
            'font' => [
                'size' => 11
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

        // Auto-size columns
        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
        foreach ($columns as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Special formatting for overview column (C) - make it wider
        $sheet->getColumnDimension('C')->setWidth(50);
        $sheet->getColumnDimension('D')->setWidth(25); // author column
        $sheet->getColumnDimension('B')->setWidth(35); // title column
    }
}
