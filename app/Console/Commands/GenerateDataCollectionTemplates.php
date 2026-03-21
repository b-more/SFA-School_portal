<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class GenerateDataCollectionTemplates extends Command
{
    protected $signature = 'templates:generate';
    protected $description = 'Generate Excel templates for Parent, Student, and Teacher data collection';

    public function handle()
    {
        $this->info('Generating Excel templates...');

        // Generate Parent Template
        $this->generateParentTemplate();
        $this->info('✓ Parent Information Template created');

        // Generate Student Template
        $this->generateStudentTemplate();
        $this->info('✓ Student Information Template created');

        // Generate Teacher Template
        $this->generateTeacherTemplate();
        $this->info('✓ Teacher Information Template created');

        $this->newLine();
        $this->info('Templates generated successfully in public/templates/');
        $this->info('- Parent_Information_Template.xlsx');
        $this->info('- Student_Information_Template.xlsx');
        $this->info('- Teacher_Information_Template.xlsx');

        return Command::SUCCESS;
    }

    protected function generateParentTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Parent Information');

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(25);
        $sheet->getColumnDimension('J')->setWidth(40);

        // Header row
        $headers = [
            'A1' => 'No.',
            'B1' => 'Full Name *',
            'C1' => 'Email Address',
            'D1' => 'Phone Number *',
            'E1' => 'Alternate Phone',
            'F1' => 'NRC Number',
            'G1' => 'Nationality',
            'H1' => 'Relationship *',
            'I1' => 'Occupation',
            'J1' => 'Physical Address',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Style header row
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E3A8A'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        $sheet->getStyle('A1:J1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Add instructions row
        $sheet->setCellValue('A2', 'Instructions:');
        $sheet->setCellValue('B2', '* Required fields. Relationship options: father, mother, guardian, other');
        $sheet->mergeCells('B2:J2');
        $sheet->getStyle('A2:J2')->applyFromArray([
            'font' => ['italic' => true, 'color' => ['rgb' => '666666']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFF9C4'],
            ],
        ]);

        // Add sample data
        $sampleData = [
            [1, 'John Mwansa', 'john.mwansa@email.com', '0977123456', '0966789012', '123456/78/9', 'Zambian', 'father', 'Business Owner', 'Plot 123, Kabulonga, Lusaka'],
            [2, 'Mary Banda', 'mary.banda@email.com', '0955234567', '', '234567/89/1', 'Zambian', 'mother', 'Nurse', 'House 45, Roma, Lusaka'],
        ];

        $row = 3;
        foreach ($sampleData as $data) {
            $col = 'A';
            foreach ($data as $value) {
                $sheet->setCellValue($col . $row, $value);
                $col++;
            }
            $row++;
        }

        // Add data validation for Relationship column (H)
        for ($i = 3; $i <= 100; $i++) {
            $validation = $sheet->getCell('H' . $i)->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_STOP);
            $validation->setAllowBlank(false);
            $validation->setShowDropDown(true);
            $validation->setFormula1('"father,mother,guardian,other"');
            $validation->setShowErrorMessage(true);
            $validation->setErrorTitle('Invalid Relationship');
            $validation->setError('Please select: father, mother, guardian, or other');
        }

        // Style data rows
        $sheet->getStyle('A3:J100')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
        ]);

        // Freeze header row
        $sheet->freezePane('A3');

        // Save file
        $writer = new Xlsx($spreadsheet);
        $path = public_path('templates');
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }
        $writer->save($path . '/Parent_Information_Template.xlsx');
    }

    protected function generateStudentTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Student Information');

        // Set column widths
        $columns = [
            'A' => 5, 'B' => 25, 'C' => 15, 'D' => 15, 'E' => 20, 'F' => 20,
            'G' => 20, 'H' => 20, 'I' => 15, 'J' => 20, 'K' => 15, 'L' => 20,
            'M' => 20, 'N' => 20, 'O' => 30, 'P' => 40,
        ];

        foreach ($columns as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        // Header row
        $headers = [
            'A1' => 'No.',
            'B1' => 'Full Name *',
            'C1' => 'Gender *',
            'D1' => 'Date of Birth *',
            'E1' => 'Place of Birth',
            'F1' => 'Student ID Number',
            'G1' => 'Grade *',
            'H1' => 'Class Section *',
            'I1' => 'Admission Date *',
            'J1' => 'Parent/Guardian Name *',
            'K1' => 'Parent Phone *',
            'L1' => 'Religious Denomination',
            'M1' => 'Previous School',
            'N1' => 'Smallpox Vaccination',
            'O1' => 'Medical Information',
            'P1' => 'Physical Address',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Style header row
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E3A8A'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        $sheet->getStyle('A1:P1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Add instructions row
        $instructions = '* Required fields. Date format: YYYY-MM-DD (e.g., 2015-03-15). Gender: male/female. Vaccination: Yes/No/Not Sure. Grade: Baby Class, Middle Class, Reception, Grade 1-12';
        $sheet->setCellValue('A2', 'Instructions:');
        $sheet->setCellValue('B2', $instructions);
        $sheet->mergeCells('B2:P2');
        $sheet->getStyle('A2:P2')->applyFromArray([
            'font' => ['italic' => true, 'color' => ['rgb' => '666666'], 'size' => 9],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFF9C4'],
            ],
            'alignment' => ['wrapText' => true],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(40);

        // Add sample data
        $sampleData = [
            [
                1, 'Peter Phiri', 'male', '2015-03-15', 'Lusaka', 'STU-2025-001', 'Grade 1', 'A',
                '2025-01-06', 'John Mwansa', '0977123456', 'Christian', 'ABC Primary School',
                'Yes', 'No known allergies', 'Plot 123, Kabulonga, Lusaka'
            ],
            [
                2, 'Grace Banda', 'female', '2016-07-22', 'Ndola', 'STU-2025-002', 'Baby Class', 'A',
                '2025-01-06', 'Mary Banda', '0955234567', 'Christian', '',
                'Not Sure', '', 'House 45, Roma, Lusaka'
            ],
        ];

        $row = 3;
        foreach ($sampleData as $data) {
            $col = 'A';
            foreach ($data as $value) {
                $sheet->setCellValue($col . $row, $value);
                $col++;
            }
            $row++;
        }

        // Add data validation for Gender column (C)
        for ($i = 3; $i <= 100; $i++) {
            $validation = $sheet->getCell('C' . $i)->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_STOP);
            $validation->setAllowBlank(false);
            $validation->setShowDropDown(true);
            $validation->setFormula1('"male,female"');
            $validation->setShowErrorMessage(true);
            $validation->setErrorTitle('Invalid Gender');
            $validation->setError('Please select: male or female');
        }

        // Add data validation for Vaccination column (N)
        for ($i = 3; $i <= 100; $i++) {
            $validation = $sheet->getCell('N' . $i)->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_STOP);
            $validation->setAllowBlank(true);
            $validation->setShowDropDown(true);
            $validation->setFormula1('"Yes,No,Not Sure"');
            $validation->setShowErrorMessage(true);
            $validation->setErrorTitle('Invalid Vaccination Status');
            $validation->setError('Please select: Yes, No, or Not Sure');
        }

        // Add data validation for Grade column (G)
        $grades = 'Baby Class,Middle Class,Reception,Grade 1,Grade 2,Grade 3,Grade 4,Grade 5,Grade 6,Grade 7,Grade 8,Grade 9,Grade 10,Grade 11,Grade 12';
        for ($i = 3; $i <= 100; $i++) {
            $validation = $sheet->getCell('G' . $i)->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_STOP);
            $validation->setAllowBlank(false);
            $validation->setShowDropDown(true);
            $validation->setFormula1('"' . $grades . '"');
            $validation->setShowErrorMessage(true);
            $validation->setErrorTitle('Invalid Grade');
            $validation->setError('Please select a valid grade');
        }

        // Style data rows
        $sheet->getStyle('A3:P100')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
        ]);

        // Freeze header row
        $sheet->freezePane('A3');

        // Save file
        $writer = new Xlsx($spreadsheet);
        $path = public_path('templates');
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }
        $writer->save($path . '/Student_Information_Template.xlsx');
    }

    protected function generateTeacherTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Teacher Information');

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(5);   // No.
        $sheet->getColumnDimension('B')->setWidth(25);  // Full Name
        $sheet->getColumnDimension('C')->setWidth(30);  // Email
        $sheet->getColumnDimension('D')->setWidth(20);  // Phone Number
        $sheet->getColumnDimension('E')->setWidth(15);  // NRC Number
        $sheet->getColumnDimension('F')->setWidth(15);  // TPIN
        $sheet->getColumnDimension('G')->setWidth(20);  // Qualification
        $sheet->getColumnDimension('H')->setWidth(20);  // Specialization
        $sheet->getColumnDimension('I')->setWidth(15);  // Join Date
        $sheet->getColumnDimension('J')->setWidth(20);  // Bank Name
        $sheet->getColumnDimension('K')->setWidth(20);  // Account Number
        $sheet->getColumnDimension('L')->setWidth(20);  // Bank Branch
        $sheet->getColumnDimension('M')->setWidth(25);  // Grade
        $sheet->getColumnDimension('N')->setWidth(20);  // Class Section
        $sheet->getColumnDimension('O')->setWidth(30);  // Physical Address

        // Header row
        $headers = [
            'No.', 'Full Name *', 'Email Address *', 'Phone Number *', 'NRC Number',
            'TPIN', 'Qualification *', 'Specialization', 'Join Date *', 'Bank Name',
            'Account Number', 'Bank Branch', 'Grade', 'Class Section', 'Physical Address'
        ];

        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $column++;
        }

        // Style header row
        $sheet->getStyle('A1:O1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E3A8A'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Instructions row
        $sheet->setCellValue('A2', 'Instructions: Fields marked with * are required. Use the dropdown menus where provided. Format dates as YYYY-MM-DD (e.g., 2025-01-15).');
        $sheet->mergeCells('A2:O2');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => [
                'italic' => true,
                'size' => 10,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFF9C4'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);

        // Sample data row 1
        $sheet->setCellValue('A3', '1');
        $sheet->setCellValue('B3', 'John Smith');
        $sheet->setCellValue('C3', 'john.smith@school.com');
        $sheet->setCellValue('D3', '+260971234567');
        $sheet->setCellValue('E3', '123456/78/1');
        $sheet->setCellValue('F3', '1001234567');
        $sheet->setCellValue('G3', 'Bachelor of Education');
        $sheet->setCellValue('H3', 'Mathematics');
        $sheet->setCellValue('I3', '2025-01-15');
        $sheet->setCellValue('J3', 'Zanaco Bank');
        $sheet->setCellValue('K3', '1234567890');
        $sheet->setCellValue('L3', 'Lusaka Main');
        $sheet->setCellValue('M3', 'Grade 7');
        $sheet->setCellValue('N3', 'A');
        $sheet->setCellValue('O3', '123 Main Street, Lusaka');

        // Sample data row 2
        $sheet->setCellValue('A4', '2');
        $sheet->setCellValue('B4', 'Mary Johnson');
        $sheet->setCellValue('C4', 'mary.johnson@school.com');
        $sheet->setCellValue('D4', '+260977654321');
        $sheet->setCellValue('E4', '234567/89/1');
        $sheet->setCellValue('F4', '1009876543');
        $sheet->setCellValue('G4', 'Diploma in Primary Education');
        $sheet->setCellValue('H4', '');
        $sheet->setCellValue('I4', '2025-02-01');
        $sheet->setCellValue('J4', 'FNB Bank');
        $sheet->setCellValue('K4', '9876543210');
        $sheet->setCellValue('L4', 'Kabwe Branch');
        $sheet->setCellValue('M4', 'Baby Class');
        $sheet->setCellValue('N4', 'A');
        $sheet->setCellValue('O4', '456 School Road, Kabwe');

        // Add data validation for Grade column (M)
        $grades = 'Baby Class,Middle Class,Reception,Grade 1,Grade 2,Grade 3,Grade 4,Grade 5,Grade 6,Grade 7,Grade 8,Grade 9,Grade 10,Grade 11,Grade 12';
        for ($i = 3; $i <= 100; $i++) {
            $validation = $sheet->getCell('M' . $i)->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_STOP);
            $validation->setAllowBlank(true);
            $validation->setShowDropDown(true);
            $validation->setFormula1('"' . $grades . '"');
            $validation->setShowErrorMessage(true);
            $validation->setErrorTitle('Invalid Grade');
            $validation->setError('Please select a valid grade');
        }

        // Add data validation for Class Section column (N)
        $sections = 'A,B,C,D';
        for ($i = 3; $i <= 100; $i++) {
            $validation = $sheet->getCell('N' . $i)->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_STOP);
            $validation->setAllowBlank(true);
            $validation->setShowDropDown(true);
            $validation->setFormula1('"' . $sections . '"');
            $validation->setShowErrorMessage(true);
            $validation->setErrorTitle('Invalid Section');
            $validation->setError('Please select a valid section (A, B, C, or D)');
        }

        // Style data rows
        $sheet->getStyle('A3:O100')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
        ]);

        // Freeze header row
        $sheet->freezePane('A3');

        // Save file
        $writer = new Xlsx($spreadsheet);
        $path = public_path('templates');
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }
        $writer->save($path . '/Teacher_Information_Template.xlsx');
    }
}
