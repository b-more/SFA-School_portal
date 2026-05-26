<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class TrainingMaterialController extends Controller
{
    private function getCourseData(): array
    {
        return [
            1 => [
                'title' => 'Getting Started & System Navigation',
                'lessons' => [
                    ['num' => '1.1', 'title' => 'Logging In & First-Time Setup', 'dur' => '30 min',
                     'objective' => 'Successfully log into the portal and change your default password.',
                     'content' => [
                         ['heading' => 'Accessing the Portal', 'body' => "Follow these steps to access the school portal:\n\n1. Open your web browser (Chrome, Firefox, or Edge recommended)\n2. Type the portal address: https://portal.stfrancisofassisizm.com/admin\n3. Enter your Email Address provided by the school administrator\n4. Enter your Password (default password is given by admin)\n5. Click \"Sign In\""],
                         ['heading' => 'First-Time Password Change', 'body' => "When you log in for the first time, the system will force you to change your password. This is a security measure.\n\n- Your new password must be at least 8 characters long\n- Use a mix of letters, numbers, and special characters\n- Do not reuse passwords from other websites\n- Remember your password — write it down in a secure place if needed\n\nSECURITY WARNING: Never share your login credentials with anyone. Each teacher has their own unique account."],
                         ['heading' => 'Supported Browsers', 'table' => [
                             'headers' => ['Browser', 'Status', 'Notes'],
                             'rows' => [
                                 ['Google Chrome', 'Recommended', 'Best performance and compatibility'],
                                 ['Mozilla Firefox', 'Recommended', 'Excellent alternative'],
                                 ['Microsoft Edge', 'Supported', 'Works well on Windows'],
                                 ['Safari', 'Partial', 'Some features may not display correctly'],
                                 ['Internet Explorer', 'Not Supported', 'Do not use'],
                             ]
                         ]],
                     ],
                     'quiz' => [
                         ['q' => 'What is the correct URL for the school portal?', 'opts' => ['a) www.stfrancisofassisizm.com', 'b) portal.stfrancisofassisizm.com/admin', 'c) stfrancis.school.com', 'd) admin.stfrancisofassisizm.com'], 'ans' => 'b'],
                         ['q' => 'What is the minimum number of characters required for your password?', 'opts' => ['a) 4 characters', 'b) 6 characters', 'c) 8 characters', 'd) 12 characters'], 'ans' => 'c'],
                         ['q' => 'Which browser is NOT supported for use with the portal?', 'opts' => ['a) Chrome', 'b) Firefox', 'c) Internet Explorer', 'd) Edge'], 'ans' => 'c'],
                     ]
                    ],
                    ['num' => '1.2', 'title' => 'Understanding Your Role', 'dur' => '25 min',
                     'objective' => 'Understand the difference between Class Teacher and Subject Teacher roles and their permissions.',
                     'content' => [
                         ['heading' => 'Two Types of Teacher Roles', 'table' => [
                             'headers' => ['Role', 'Description', 'Key Permissions'],
                             'rows' => [
                                 ['Class Teacher', 'Assigned as the main teacher for a specific class section (e.g., Grade 3A)', "- Mark attendance for the class\n- Enter results for ALL subjects\n- Write class teacher comments on report cards\n- View all students in the class"],
                                 ['Subject Teacher', 'Teaches specific subjects in one or more classes (e.g., Maths in Grade 7A & 7B)', "- Enter results for assigned subjects ONLY\n- Create homework for assigned subjects\n- View students in classes they teach\n- Cannot write class teacher comments"],
                             ]
                         ]],
                         ['heading' => 'Important Note', 'body' => 'A teacher can be BOTH a class teacher for one class AND a subject teacher in other classes. For example, you may be the Class Teacher for Grade 5A while also teaching Science in Grade 5B and 6A.'],
                         ['heading' => 'What Teachers Cannot Do', 'body' => "- Access financial or accounting modules (fees, payments)\n- Manage the school timetable (admin function)\n- Edit student personal details (admin function)\n- Access other teachers' records or payslips\n- Change school settings or configurations\n- Delete other teachers' homework or results"],
                     ],
                     'quiz' => [
                         ['q' => 'Who can write class teacher comments on report cards?', 'opts' => ['a) Any teacher', 'b) Only the class teacher for that class', 'c) Only subject teachers', 'd) Only the head teacher'], 'ans' => 'b'],
                         ['q' => 'A Subject Teacher can enter results for which subjects?', 'opts' => ['a) All subjects in the school', 'b) Any subject in their grade', 'c) Only subjects they are assigned to teach', 'd) Only one subject per class'], 'ans' => 'c'],
                         ['q' => 'Can a teacher be both a Class Teacher and Subject Teacher?', 'opts' => ['a) No, you must choose one', 'b) Yes, class teacher for one and subject teacher in others', 'c) Only with admin approval', 'd) Only in primary school'], 'ans' => 'b'],
                     ]
                    ],
                    ['num' => '1.3', 'title' => 'Navigating the Dashboard & Sidebar', 'dur' => '30 min',
                     'objective' => 'Navigate the portal confidently using the sidebar menu and dashboard shortcuts.',
                     'content' => [
                         ['heading' => 'The Sidebar Menu', 'table' => [
                             'headers' => ['Menu Group', 'Items', 'URL'],
                             'rows' => [
                                 ['Dashboard', 'Teacher Dashboard (home page)', '/admin/dashboard'],
                                 ['Academic', 'Daily Attendance', '/admin/mark-attendance'],
                                 ['Academic', 'Enter Results', '/admin/enter-results'],
                                 ['Academic', 'Generate Report Cards', '/admin/generate-report-cards'],
                                 ['Academic Mgmt', 'Report Cards (View/Download)', '/admin/report-cards'],
                                 ['Teaching', 'Homework', '/admin/homework'],
                                 ['Student Mgmt', 'Students', '/admin/students'],
                                 ['Reports', 'Attendance Reports', '/admin/attendance-reports'],
                                 ['Staff Mgmt', 'My Payslips', '/admin/my-payslips'],
                             ]
                         ]],
                         ['heading' => 'Dashboard Quick Action Buttons', 'table' => [
                             'headers' => ['Button', 'What It Does'],
                             'rows' => [
                                 ['Create Homework', 'Opens the homework creation form'],
                                 ['Grade Submissions', 'View homework submissions awaiting grading'],
                                 ['View My Classes', 'See your assigned student lists'],
                                 ['Record Results', 'Go to Enter Results page'],
                             ]
                         ]],
                         ['heading' => 'Dashboard Information Cards', 'table' => [
                             'headers' => ['Card', 'What It Shows'],
                             'rows' => [
                                 ['My Classes', 'All classes you are assigned to'],
                                 ['Student Count', 'Total students across your classes'],
                                 ['Recent Homework', 'Latest homework and submission counts'],
                                 ['Recent Attendance', 'Attendance from last 7 days'],
                                 ['Grading Summary', 'Submissions: graded, ungraded, late'],
                                 ['Upcoming Events', 'School events for your grade level'],
                             ]
                         ]],
                     ],
                     'quiz' => [
                         ['q' => 'Where do you find the "Daily Attendance" option?', 'opts' => ['a) Sidebar > Teaching', 'b) Sidebar > Academic', 'c) Sidebar > Reports', 'd) Sidebar > Staff Management'], 'ans' => 'b'],
                         ['q' => 'Which dashboard card shows ungraded homework submissions?', 'opts' => ['a) My Classes', 'b) Recent Homework', 'c) Grading Summary', 'd) Student Count'], 'ans' => 'c'],
                         ['q' => 'What is the URL for the Enter Results page?', 'opts' => ['a) /admin/results', 'b) /admin/marks', 'c) /admin/enter-results', 'd) /admin/grades'], 'ans' => 'c'],
                     ]
                    ],
                ],
                'test' => [
                    ['q' => 'What should you do the FIRST time you log into the portal?', 'opts' => ['a) Mark attendance', 'b) Change your default password', 'c) Create homework', 'd) View your payslip'], 'ans' => 'b'],
                    ['q' => 'Which is the correct portal URL?', 'opts' => ['a) https://stfrancisofassisizm.com/portal', 'b) https://portal.stfrancisofassisizm.com/admin', 'c) https://admin.stfrancisofassisizm.com', 'd) https://school.stfrancisofassisizm.com'], 'ans' => 'b'],
                    ['q' => 'A Class Teacher can enter results for:', 'opts' => ['a) Only Maths and English', 'b) Only their specialised subjects', 'c) ALL subjects in their assigned class', 'd) No subjects (admin only)'], 'ans' => 'c'],
                    ['q' => 'Where can you find the "Homework" menu?', 'opts' => ['a) Sidebar > Academic', 'b) Sidebar > Reports', 'c) Sidebar > Teaching', 'd) Sidebar > Staff Management'], 'ans' => 'c'],
                    ['q' => 'Can a teacher access the financial system?', 'opts' => ['a) Yes, all teachers', 'b) Only class teachers', 'c) No, restricted to administrators', 'd) Only head teachers'], 'ans' => 'c'],
                    ['q' => 'What does the "Student Count" dashboard card show?', 'opts' => ['a) Total students in the school', 'b) Total students across your assigned classes', 'c) Students absent today', 'd) Students who submitted homework'], 'ans' => 'b'],
                    ['q' => 'The minimum password length is:', 'opts' => ['a) 4 characters', 'b) 6 characters', 'c) 8 characters', 'd) 10 characters'], 'ans' => 'c'],
                    ['q' => 'Which button takes you to results entry?', 'opts' => ['a) Create Homework', 'b) Grade Submissions', 'c) View My Classes', 'd) Record Results'], 'ans' => 'd'],
                    ['q' => 'If someone knows your password, you should:', 'opts' => ['a) Do nothing', 'b) Change it immediately', 'c) Ask them to forget it', 'd) Create a new account'], 'ans' => 'b'],
                    ['q' => 'A Subject Teacher for Maths in Grade 7A and 7B can view students in:', 'opts' => ['a) All school classes', 'b) Only Grade 7A', 'c) Grade 7A and 7B only', 'd) No classes'], 'ans' => 'c'],
                ]
            ],
            2 => [
                'title' => 'Daily Attendance Management',
                'lessons' => [
                    ['num' => '2.1', 'title' => 'Marking Daily Attendance', 'dur' => '30 min',
                     'objective' => 'Mark attendance for your class accurately and efficiently.',
                     'content' => [
                         ['heading' => 'Step-by-Step: How to Mark Attendance', 'body' => "1. Select Class — If you are a class teacher, your class is auto-selected. Subject teachers select from a dropdown.\n2. Select Date — Defaults to today. You can select a past date. Future dates are blocked.\n3. Click \"Mark All Present\" — Marks all students as Present in one click.\n4. Adjust Individual Students — Click the status button next to absent, sick, late, or excused students.\n5. Click \"Submit Attendance\" — A green notification shows the summary."],
                         ['heading' => 'Attendance Status Options', 'table' => [
                             'headers' => ['Status', 'Symbol', 'When to Use'],
                             'rows' => [
                                 ['Present', 'P', 'Student is in school'],
                                 ['Absent', 'X', 'Student not in school, no reason given'],
                                 ['Sick', 'S', 'Student is sick'],
                                 ['Late', 'Y', 'Student arrived after start time'],
                                 ['Excused', 'L', 'Pre-approved absence'],
                             ]
                         ]],
                         ['heading' => 'Time-Saving Tip', 'body' => 'Always start by clicking "Mark All Present" first, then adjust only the few students who are absent, sick, or late. This is much faster than marking individually.'],
                         ['heading' => 'Important Rules', 'body' => "- Attendance should be marked every school day by the class teacher\n- Mark attendance before the first period (ideally before 08:00)\n- You can edit attendance for the same day by resubmitting\n- You cannot mark future dates\n- The system records the exact time attendance was submitted"],
                     ],
                     'quiz' => [
                         ['q' => 'What is the fastest way to mark attendance when most students are present?', 'opts' => ['a) Click Present for each student', 'b) Click "Mark All Present" then adjust absent ones', 'c) Ask admin to mark it', 'd) Leave it blank'], 'ans' => 'b'],
                         ['q' => 'What symbol is used for a student who arrived late?', 'opts' => ['a) L', 'b) X', 'c) Y', 'd) T'], 'ans' => 'c'],
                         ['q' => 'Can you mark attendance for a future date?', 'opts' => ['a) Yes, up to one week', 'b) Yes, next day only', 'c) No, only today or past dates', 'd) Yes, if class teacher'], 'ans' => 'c'],
                     ]
                    ],
                    ['num' => '2.2', 'title' => 'Attendance Reports & Exports', 'dur' => '25 min',
                     'objective' => 'Generate and export attendance reports for analysis and record-keeping.',
                     'content' => [
                         ['heading' => 'Report Types Available', 'table' => [
                             'headers' => ['Report Type', 'What It Shows', 'Best For'],
                             'rows' => [
                                 ['All Attendance', 'Complete records across all dates', 'General overview'],
                                 ['By Class Section', 'Attendance for a specific class', 'Class teacher monthly review'],
                                 ['By Grade', 'Attendance for an entire grade', 'Grade-level analysis'],
                                 ['By Student', 'One student\'s complete history', 'Individual follow-up'],
                                 ['By Status', 'Records with a specific status', 'Finding all absences'],
                                 ['Attendance Summary', 'Statistical overview with %', 'Admin reports and meetings'],
                             ]
                         ]],
                         ['heading' => 'Export Formats', 'table' => [
                             'headers' => ['Export', 'Format', 'Best For'],
                             'rows' => [
                                 ['Monthly Register (PDF)', 'Landscape A4', 'Printing and filing'],
                                 ['Monthly Register (CSV)', 'Excel/CSV', 'Spreadsheet analysis'],
                                 ['Summary PDF', 'PDF', 'Quick statistical summary'],
                             ]
                         ]],
                         ['heading' => 'Attendance Symbols on Reports', 'body' => "P = Present    X = Absent    S = Sick    Y = Late    L = Excused\n\nNote: As a teacher, you can only generate reports for classes you are assigned to."],
                     ],
                     'quiz' => [
                         ['q' => 'Which report type finds all absent students in March?', 'opts' => ['a) By Student', 'b) By Status (filter: Absent)', 'c) Attendance Summary', 'd) By Grade'], 'ans' => 'b'],
                         ['q' => 'Best format for printing a class register to file?', 'opts' => ['a) CSV', 'b) Monthly Register (PDF)', 'c) Summary PDF', 'd) Excel'], 'ans' => 'b'],
                         ['q' => 'What does "L" represent on a register?', 'opts' => ['a) Late', 'b) Left early', 'c) Excused', 'd) Lunch break'], 'ans' => 'c'],
                     ]
                    ],
                ],
                'test' => [
                    ['q' => 'Attendance should ideally be marked:', 'opts' => ['a) At end of day', 'b) Before the first period', 'c) During lunch', 'd) Once a week'], 'ans' => 'b'],
                    ['q' => 'When you click "Submit Attendance":', 'opts' => ['a) Nothing visible', 'b) Green notification shows summary', 'c) Email sent to parents', 'd) Page refreshes'], 'ans' => 'b'],
                    ['q' => 'The symbol "X" means:', 'opts' => ['a) Excused', 'b) Present', 'c) Absent', 'd) Sick'], 'ans' => 'c'],
                    ['q' => 'A class teacher\'s class is _____ in the dropdown:', 'opts' => ['a) Hidden', 'b) Listed last', 'c) Automatically selected', 'd) Greyed out'], 'ans' => 'c'],
                    ['q' => 'Which format allows spreadsheet analysis?', 'opts' => ['a) PDF Summary', 'b) Register PDF', 'c) Monthly Register (CSV)', 'd) Printed register'], 'ans' => 'c'],
                    ['q' => 'Can you edit attendance after submitting?', 'opts' => ['a) No, locked forever', 'b) Yes, by resubmitting same date', 'c) Only admin can', 'd) Only within 10 min'], 'ans' => 'b'],
                    ['q' => 'A sick student should be marked as:', 'opts' => ['a) Absent (X)', 'b) Sick (S)', 'c) Excused (L)', 'd) Late (Y)'], 'ans' => 'b'],
                    ['q' => 'Which report shows percentages?', 'opts' => ['a) By Class Section', 'b) By Student', 'c) Attendance Summary', 'd) All Attendance'], 'ans' => 'c'],
                    ['q' => 'Teachers can generate reports for:', 'opts' => ['a) All school classes', 'b) Only assigned classes', 'c) Only primary classes', 'd) No classes'], 'ans' => 'b'],
                    ['q' => 'Clicking a student\'s status button:', 'opts' => ['a) Opens a dropdown', 'b) Cycles: Present > Absent > Sick > Late > Excused', 'c) Marks absent', 'd) Opens new page'], 'ans' => 'b'],
                ]
            ],
            3 => [
                'title' => 'Academic Results & Grading',
                'lessons' => [
                    ['num' => '3.1', 'title' => 'Entering Student Results', 'dur' => '35 min',
                     'objective' => 'Enter student marks correctly for any exam type.',
                     'content' => [
                         ['heading' => 'Step-by-Step: Entering Results', 'body' => "1. Go to Enter Results — Sidebar > Academic > Enter Results\n2. Select Class — Choose the class section from the dropdown\n3. Select Subject — Shows only subjects assigned to the selected grade\n4. Select Term, Exam Type & Year — Choose the correct combination\n5. Enter Marks — Type marks (0-100) for each student. Use Tab to move between fields.\n6. Review Grades — System auto-calculates grades. Override manually if needed.\n7. Add Comments — Optional feedback per student\n8. Submit — Click \"Submit Results\". System shows saved count."],
                         ['heading' => 'Important', 'body' => "If a student was ABSENT for the exam, leave their marks BLANK. Do NOT enter 0 — that means they sat the exam and scored zero.\n\nTips:\n- Use the \"Clear All\" button to reset and start over\n- You can edit results later by selecting the same class/subject/term/exam type\n- Use Tab to move quickly between fields"],
                     ],
                     'quiz' => [
                         ['q' => 'What should you enter for an absent student?', 'opts' => ['a) 0', 'b) "ABS"', 'c) Leave field blank', 'd) -1'], 'ans' => 'c'],
                         ['q' => 'Valid range for marks?', 'opts' => ['a) 1 to 100', 'b) 0 to 100', 'c) 0 to 50', 'd) 1 to 50'], 'ans' => 'b'],
                         ['q' => 'Can you edit results after submitting?', 'opts' => ['a) No, permanent', 'b) Yes, select same class/subject/term/exam type', 'c) Only admin', 'd) Within 24 hours'], 'ans' => 'b'],
                     ]
                    ],
                    ['num' => '3.2', 'title' => 'Understanding Exam Types & Grading', 'dur' => '25 min',
                     'objective' => 'Know when to use each exam type and understand grade calculation.',
                     'content' => [
                         ['heading' => 'Exam Types', 'table' => [
                             'headers' => ['Exam Type', 'When to Use', 'Example'],
                             'rows' => [
                                 ['Mid-Term', 'Mid-term test results', 'Mid-Term Exam, Week 5 Test'],
                                 ['Final', 'End-of-term main exam', 'Final Exam Term 1'],
                                 ['End of Term Test', 'End-of-term test (separate)', 'Term 2 Closing Test'],
                                 ['Quiz', 'Short class quizzes', 'Weekly Quiz, Pop Quiz'],
                                 ['Assignment', 'Graded homework', 'Research Project, Essay'],
                                 ['Test', 'General class tests', 'Chapter Test, Unit Test'],
                             ]
                         ]],
                         ['heading' => 'Grading Scale', 'table' => [
                             'headers' => ['Mark Range', 'Grade', 'Description'],
                             'rows' => [
                                 ['80 – 100', 'A', 'Excellent'],
                                 ['70 – 79', 'B', 'Very Good'],
                                 ['60 – 69', 'C', 'Good'],
                                 ['50 – 59', 'D', 'Satisfactory'],
                                 ['40 – 49', 'E', 'Fair'],
                                 ['0 – 39', 'F', 'Below Standard'],
                             ]
                         ]],
                         ['heading' => 'Note', 'body' => 'Grades are auto-calculated but can be manually overridden if needed.'],
                     ],
                     'quiz' => [
                         ['q' => 'A student scores 73. Their grade is:', 'opts' => ['a) A', 'b) B', 'c) C', 'd) D'], 'ans' => 'b'],
                         ['q' => 'For a weekly class quiz, select:', 'opts' => ['a) Mid-Term', 'b) Final', 'c) Quiz', 'd) Assignment'], 'ans' => 'c'],
                         ['q' => 'A student with 45 marks gets grade:', 'opts' => ['a) D', 'b) E', 'c) F', 'd) C'], 'ans' => 'b'],
                     ]
                    ],
                    ['num' => '3.3', 'title' => 'Report Cards: Generating, Commenting & Distributing', 'dur' => '30 min',
                     'objective' => 'Generate report cards, write meaningful comments, and distribute to parents.',
                     'content' => [
                         ['heading' => 'Step-by-Step: Generating Report Cards', 'body' => "1. Verify All Results — Ensure ALL subjects have marks entered. Check Results Count = Subject Count.\n2. Go to Generate Report Cards — Sidebar > Academic > Generate Report Cards\n3. Select Class & Term — Choose your class and current term\n4. Add Class Teacher Comments — Click each student to write personalised comments\n5. Generate — \"Generate\" for individual or \"Generate All\" for entire class\n6. Review — Check a few report cards for accuracy"],
                         ['heading' => 'Writing Effective Comments', 'table' => [
                             'headers' => ['Good Comments', 'Poor Comments'],
                             'rows' => [
                                 ['An excellent term. Shows strong analytical skills. Should continue reading widely.', 'Good'],
                                 ['Has improved significantly in English. Needs to work on time management.', 'OK'],
                                 ['A dedicated student who participates actively. Must practise more in Science.', 'Average'],
                             ]
                         ]],
                         ['heading' => 'What the Report Card Contains', 'body' => "- Student personal details (name, admission number, class)\n- Subject-by-subject results (mid-term, end-of-term, average, grade)\n- Overall average and grade\n- Class position (rank out of total students)\n- Class teacher comment and Head teacher comment"],
                         ['heading' => 'SMS Notifications', 'body' => "From the Generate Report Cards page, click \"Send SMS\" to notify parents. The SMS includes student name, rank, and top subject marks.\n\nNote: SMS sending requires available SMS credits. Contact the administrator if credits are low."],
                     ],
                     'quiz' => [
                         ['q' => 'What to check BEFORE generating report cards?', 'opts' => ['a) Students paid fees', 'b) All subjects have results entered', 'c) Timetable is correct', 'd) Parents notified'], 'ans' => 'b'],
                         ['q' => 'Who writes class teacher comments?', 'opts' => ['a) Any teacher', 'b) Only class teacher for that class', 'c) Subject teachers', 'd) Only admin'], 'ans' => 'b'],
                         ['q' => '"Generate All" does what?', 'opts' => ['a) Generates for entire school', 'b) Creates PDF with all students in class', 'c) Sends SMS to all', 'd) Enters marks for all'], 'ans' => 'b'],
                     ]
                    ],
                ],
                'test' => [
                    ['q' => 'URL for Enter Results:', 'opts' => ['a) /admin/results', 'b) /admin/enter-results', 'c) /admin/marks', 'd) /admin/grading'], 'ans' => 'b'],
                    ['q' => 'Student scores 39. Grade is:', 'opts' => ['a) E', 'b) D', 'c) F', 'd) C'], 'ans' => 'c'],
                    ['q' => 'For a graded project, select:', 'opts' => ['a) Quiz', 'b) Test', 'c) Assignment', 'd) Mid-Term'], 'ans' => 'c'],
                    ['q' => 'Only 15 of 20 students have results. You should:', 'opts' => ['a) Generate anyway', 'b) Enter remaining results first', 'c) Leave for next term', 'd) Delete existing'], 'ans' => 'b'],
                    ['q' => '"Final" exam type is for:', 'opts' => ['a) Weekly quizzes', 'b) Homework', 'c) End-of-term main exam', 'd) Class tests'], 'ans' => 'c'],
                    ['q' => 'Report card includes:', 'opts' => ['a) Only final mark', 'b) Marks, grades, average, position, comments', 'c) Only attendance', 'd) Only position'], 'ans' => 'b'],
                    ['q' => 'Student scores 60. Grade is:', 'opts' => ['a) B', 'b) C', 'c) D', 'd) A'], 'ans' => 'b'],
                    ['q' => 'Which is a good comment?', 'opts' => ['a) "OK"', 'b) "Has improved. Should focus on Science."', 'c) "Average"', 'd) "Fine"'], 'ans' => 'b'],
                    ['q' => 'SMS requires:', 'opts' => ['a) Internet only', 'b) SMS credits', 'c) Parent email', 'd) A printer'], 'ans' => 'b'],
                    ['q' => 'Subject Teacher enters results for:', 'opts' => ['a) All subjects', 'b) All grade subjects', 'c) Only assigned subjects in specific classes', 'd) One subject total'], 'ans' => 'c'],
                ]
            ],
            4 => [
                'title' => 'Teaching Tools',
                'lessons' => [
                    ['num' => '4.1', 'title' => 'Creating & Managing Homework', 'dur' => '30 min',
                     'objective' => 'Create homework assignments and manage them effectively.',
                     'content' => [
                         ['heading' => 'Creating Homework', 'body' => "1. Go to Sidebar > Teaching > Homework\n2. Click \"Create Homework\" (top right)\n3. Fill in the required fields\n4. Attach reference materials if needed\n5. Set Status to \"Active\" to make visible to students\n6. Click \"Create\""],
                         ['heading' => 'Homework Form Fields', 'table' => [
                             'headers' => ['Field', 'Description', 'Required'],
                             'rows' => [
                                 ['Title', 'Descriptive name (e.g., "Chapter 5 Exercises")', 'Yes'],
                                 ['Subject', 'From your assigned subjects only', 'Yes'],
                                 ['Grade', 'From your assigned grades only', 'Yes'],
                                 ['Due Date', 'Deadline (default: 1 week from now)', 'Yes'],
                                 ['Instructions', 'Clear, detailed instructions', 'Yes'],
                                 ['Attachments', 'PDFs, images, documents', 'No'],
                                 ['Status', 'Active (visible) or Inactive (hidden)', 'Yes'],
                             ]
                         ]],
                         ['heading' => 'Managing Existing Homework', 'table' => [
                             'headers' => ['Action', 'How'],
                             'rows' => [
                                 ['View Details', 'Click the homework title'],
                                 ['Edit', 'Pencil icon (only your own)'],
                                 ['Delete', 'Delete icon (only your own)'],
                                 ['Filter', 'Filter by status, grade, or subject'],
                                 ['Search', 'Type in the search box'],
                             ]
                         ]],
                         ['heading' => 'Important', 'body' => 'You can only create homework for subjects and grades you are assigned to teach. Setting status to "Inactive" hides homework from students and parents.'],
                     ],
                     'quiz' => [
                         ['q' => 'Where is Homework in the sidebar?', 'opts' => ['a) Academic', 'b) Reports', 'c) Teaching', 'd) Staff Management'], 'ans' => 'c'],
                         ['q' => 'Setting homework to "Inactive" does what?', 'opts' => ['a) Deletes it', 'b) Hides from students/parents', 'c) Marks as graded', 'd) Sends reminder'], 'ans' => 'b'],
                         ['q' => 'Can you create homework for unassigned subjects?', 'opts' => ['a) Yes, any subject', 'b) No, only assigned subjects', 'c) With admin approval', 'd) Only during exams'], 'ans' => 'b'],
                     ]
                    ],
                    ['num' => '4.2', 'title' => 'Viewing & Managing Students', 'dur' => '20 min',
                     'objective' => 'View student information and understand access restrictions.',
                     'content' => [
                         ['heading' => 'What You Can See', 'table' => [
                             'headers' => ['Information', 'Access'],
                             'rows' => [
                                 ['Student name & admission number', 'Visible'],
                                 ['Gender, date of birth', 'Visible'],
                                 ['Class & grade assignment', 'Visible'],
                                 ['Parent/Guardian contact', 'Visible'],
                                 ['Enrollment status', 'Visible'],
                                 ['Fee balances & payments', 'NOT Visible'],
                                 ['Edit/delete student records', 'NOT Allowed'],
                             ]
                         ]],
                         ['heading' => 'Access by Role', 'table' => [
                             'headers' => ['Your Role', 'Students You See'],
                             'rows' => [
                                 ['Class Teacher', 'All students in your assigned class'],
                                 ['Subject Teacher', 'Students in classes where you teach'],
                                 ['Dean / Head Teacher', 'All students in your section'],
                             ]
                         ]],
                     ],
                     'quiz' => [
                         ['q' => 'Can teachers view fee balances?', 'opts' => ['a) Yes', 'b) No, restricted to admins', 'c) Only class teachers', 'd) Only during fee week'], 'ans' => 'b'],
                         ['q' => 'Science teacher for 6A and 6B sees students in:', 'opts' => ['a) All Grade 6', 'b) Only 6A and 6B', 'c) All school', 'd) No classes'], 'ans' => 'b'],
                         ['q' => 'Can a teacher edit a student\'s date of birth?', 'opts' => ['a) Yes', 'b) Only class teachers', 'c) No, only administrators', 'd) With approval'], 'ans' => 'c'],
                     ]
                    ],
                    ['num' => '4.3', 'title' => 'Homework Submissions & Grading', 'dur' => '25 min',
                     'objective' => 'Review, grade, and provide feedback on homework submissions.',
                     'content' => [
                         ['heading' => 'Reviewing Submissions', 'body' => "- Click on any homework to see all submissions\n- System shows which students have submitted and which have not\n- Late submissions are marked with a late indicator\n- You can download attached student files\n- Use the Grade Submissions shortcut on the dashboard"],
                         ['heading' => 'Grading Submissions', 'body' => "- Enter a mark for each submission\n- Add feedback comments for the student\n- Students and parents can see the grade and feedback through their portal"],
                         ['heading' => 'Recommended Workflow', 'body' => "1. Check the dashboard for ungraded submissions\n2. Click \"Grade Submissions\" to go directly to pending work\n3. Review each submission, enter mark, and provide feedback\n4. Students are notified automatically"],
                     ],
                     'quiz' => [
                         ['q' => 'Where to find ungraded submissions quickly?', 'opts' => ['a) Enter Results', 'b) Dashboard > Grade Submissions', 'c) Attendance Reports', 'd) My Payslips'], 'ans' => 'b'],
                         ['q' => 'How are late submissions shown?', 'opts' => ['a) Deleted', 'b) Marked with late indicator', 'c) Hidden', 'd) Get 0 automatically'], 'ans' => 'b'],
                         ['q' => 'Can parents see homework grades?', 'opts' => ['a) No, teachers only', 'b) Yes, through their portal', 'c) Only by email', 'd) Only on report card'], 'ans' => 'b'],
                     ]
                    ],
                ],
                'test' => [
                    ['q' => 'Default homework due date is:', 'opts' => ['a) Tomorrow', 'b) 3 days', 'c) 1 week from now', 'd) End of term'], 'ans' => 'c'],
                    ['q' => 'Who can edit homework?', 'opts' => ['a) Any teacher', 'b) Creator and administrators', 'c) Head teacher only', 'd) No one'], 'ans' => 'b'],
                    ['q' => 'Dean of Secondary sees students in:', 'opts' => ['a) Own class only', 'b) All primary', 'c) All secondary section', 'd) Only Grade 8'], 'ans' => 'c'],
                    ['q' => 'What files can be attached to homework?', 'opts' => ['a) Only PDFs', 'b) Only images', 'c) PDFs, images, documents', 'd) None'], 'ans' => 'c'],
                    ['q' => '"Instructions" field is:', 'opts' => ['a) Optional', 'b) Required', 'c) Auto-filled', 'd) Secondary only'], 'ans' => 'b'],
                    ['q' => 'Teachers search students by:', 'opts' => ['a) Name or admission number', 'b) Fee balance', 'c) Parent occupation', 'd) Address'], 'ans' => 'a'],
                    ['q' => 'To hide homework without deleting:', 'opts' => ['a) Delete it', 'b) Past due date', 'c) Change to "Inactive"', 'd) Remove instructions'], 'ans' => 'c'],
                    ['q' => 'Can you delete another teacher\'s homework?', 'opts' => ['a) True', 'b) False', 'c) Same subject only', 'd) With permission'], 'ans' => 'b'],
                    ['q' => 'Parent contact is visible to:', 'opts' => ['a) No teachers', 'b) Class teachers only', 'c) Teachers with student in their classes', 'd) Head teacher only'], 'ans' => 'c'],
                    ['q' => '"Grade Submissions" button shows:', 'opts' => ['a) Exam grades', 'b) Homework awaiting grading', 'c) Grading scale', 'd) Report templates'], 'ans' => 'b'],
                ]
            ],
            5 => [
                'title' => 'Staff Self-Service & Best Practices',
                'lessons' => [
                    ['num' => '5.1', 'title' => 'My Profile & Password Management', 'dur' => '20 min',
                     'objective' => 'Update your profile information and manage your password securely.',
                     'content' => [
                         ['heading' => 'Accessing Your Profile', 'body' => 'Click your name or avatar in the top-right corner, then select "My Profile".'],
                         ['heading' => 'What You Can Edit', 'table' => [
                             'headers' => ['Section', 'Editable Fields'],
                             'rows' => [
                                 ['Profile Photo', 'Upload photo (auto-cropped to 400x400px)'],
                                 ['Contact Info', 'Email, Phone, Address, City, Province'],
                                 ['Emergency Contact', 'Name, Phone, Relationship'],
                                 ['Next of Kin', 'Name, Phone, Relationship, Address'],
                             ]
                         ]],
                         ['heading' => 'Changing Your Password', 'body' => "1. Click \"Change Password\" on your profile page\n2. Enter your current password\n3. Enter a new password (minimum 8 characters)\n4. Confirm the new password and click \"Save\""],
                         ['heading' => 'Downloadable Documents', 'table' => [
                             'headers' => ['Document', 'Description'],
                             'rows' => [
                                 ['Profile PDF', 'Full staff profile document'],
                                 ['Business Card', 'Professional card with QR code'],
                                 ['Employee ID Card', 'ID card with photo and QR code'],
                             ]
                         ]],
                     ],
                     'quiz' => [
                         ['q' => 'Where to access your profile?', 'opts' => ['a) Sidebar > Staff Management', 'b) Click name/avatar top-right', 'c) Sidebar > Academic', 'd) Dashboard > Settings'], 'ans' => 'b'],
                         ['q' => 'What is required before setting a new password?', 'opts' => ['a) Admin approval', 'b) Current password', 'c) Security questions', 'd) Email verification'], 'ans' => 'b'],
                         ['q' => 'Which document has a QR code?', 'opts' => ['a) Payslip', 'b) Student list', 'c) Employee ID Card', 'd) Timetable'], 'ans' => 'c'],
                     ]
                    ],
                    ['num' => '5.2', 'title' => 'My Payslips & Staff Documents', 'dur' => '20 min',
                     'objective' => 'Access your payslips and download staff documents.',
                     'content' => [
                         ['heading' => 'Viewing Payslips', 'body' => "Location: Sidebar > Staff Management > My Payslips\n\n- Payslips are listed by year and month (most recent first)\n- Click on any entry to view details\n- Payslips are uploaded by the administration\n- Only YOU can see your payslips — no other teacher has access"],
                         ['heading' => 'Available Staff Documents', 'table' => [
                             'headers' => ['Document', 'Where', 'Description'],
                             'rows' => [
                                 ['Profile PDF', 'My Profile', 'Full staff profile for your records'],
                                 ['Business Card', 'My Profile', 'Professional card with QR code'],
                                 ['Employee ID Card', 'My Profile', 'ID card with photo and QR code'],
                             ]
                         ]],
                         ['heading' => 'Discrepancies', 'body' => 'If you notice an error in your payslip, contact the HR/Admin office directly. You cannot edit payslips yourself.'],
                     ],
                     'quiz' => [
                         ['q' => 'Who uploads payslips?', 'opts' => ['a) Teachers themselves', 'b) Administration/HR', 'c) Head teacher', 'd) External company'], 'ans' => 'b'],
                         ['q' => 'Can another teacher see your payslip?', 'opts' => ['a) Yes, all can', 'b) No, private to each user', 'c) Head teacher only', 'd) With your password'], 'ans' => 'b'],
                         ['q' => 'Payslip error? You should:', 'opts' => ['a) Edit it yourself', 'b) Contact HR/Admin', 'c) Ignore it', 'd) Ask a colleague'], 'ans' => 'b'],
                     ]
                    ],
                    ['num' => '5.3', 'title' => 'Best Practices, Security & Troubleshooting', 'dur' => '25 min',
                     'objective' => 'Follow security best practices and resolve common issues.',
                     'content' => [
                         ['heading' => 'Security Best Practices', 'table' => [
                             'headers' => ['#', 'Practice', 'Why It Matters'],
                             'rows' => [
                                 ['1', 'Never share your password', 'Your account tracks all your actions'],
                                 ['2', 'Log out on shared computers', 'Prevents others accessing your account'],
                                 ['3', 'Use a strong password', 'Weak passwords are easily guessed'],
                                 ['4', 'Change password regularly', 'Reduces risk of compromise'],
                                 ['5', 'Don\'t save passwords in public browsers', 'Shared computer security risk'],
                             ]
                         ]],
                         ['heading' => 'Efficiency Best Practices', 'body' => "- Mark attendance daily before the first period\n- Enter results promptly after each assessment\n- Double-check marks before submitting\n- Write meaningful, personalised comments on report cards\n- Keep your profile information current\n- Use Chrome or Firefox for the best experience"],
                         ['heading' => 'Common Issues & Solutions', 'table' => [
                             'headers' => ['Problem', 'Solution'],
                             'rows' => [
                                 ['Can\'t see my class in dropdown', 'Contact admin — Teacher Assignments module'],
                                 ['Can\'t enter results for a subject', 'Need to be assigned as subject teacher'],
                                 ['Report card shows 0 results', 'Enter results via Enter Results first'],
                                 ['Forgot password', 'Contact admin for password reset'],
                                 ['Page not loading', 'Refresh (Ctrl+F5), clear cache, try Chrome'],
                                 ['SMS failed', 'SMS credits may be exhausted — contact admin'],
                             ]
                         ]],
                     ],
                     'quiz' => [
                         ['q' => 'When leaving a shared computer, you should:', 'opts' => ['a) Close the tab', 'b) Log out of the portal', 'c) Turn off monitor', 'd) Nothing'], 'ans' => 'b'],
                         ['q' => 'Can\'t see a subject in Enter Results? Likely cause:', 'opts' => ['a) Subject doesn\'t exist', 'b) Not assigned to teach it', 'c) System broken', 'd) Exam period only'], 'ans' => 'b'],
                         ['q' => 'When to enter results?', 'opts' => ['a) End of year', 'b) When admin asks', 'c) Promptly after each assessment', 'd) During holidays'], 'ans' => 'c'],
                     ]
                    ],
                ],
                'test' => [
                    ['q' => 'Profile is accessed by:', 'opts' => ['a) Sidebar > Settings', 'b) Name/avatar top-right', 'c) Dashboard > Profile', 'd) Sidebar > Academic'], 'ans' => 'b'],
                    ['q' => 'To change password, first enter:', 'opts' => ['a) Email', 'b) Employee ID', 'c) Current password', 'd) Admin password'], 'ans' => 'c'],
                    ['q' => 'Payslips are visible to:', 'opts' => ['a) All teachers', 'b) Only you', 'c) Same department', 'd) Anyone with URL'], 'ans' => 'b'],
                    ['q' => 'Which has a QR code?', 'opts' => ['a) Payslip', 'b) Attendance register', 'c) ID Card and Business Card', 'd) Report card'], 'ans' => 'c'],
                    ['q' => 'Page not loading? Try:', 'opts' => ['a) Wait 24 hours', 'b) Refresh, clear cache, use Chrome', 'c) New account', 'd) Call ISP'], 'ans' => 'b'],
                    ['q' => 'Why NOT save passwords on shared computers?', 'opts' => ['a) Slows computer', 'b) Next person could access your account', 'c) Changes password', 'd) Not possible'], 'ans' => 'b'],
                    ['q' => 'Payslip error? You should:', 'opts' => ['a) Edit in portal', 'b) Contact HR/Admin', 'c) Ignore', 'd) Ask colleague'], 'ans' => 'b'],
                    ['q' => 'Which is NOT a best practice?', 'opts' => ['a) Log out on shared computers', 'b) Share password with colleague', 'c) Use strong passwords', 'd) Change password regularly'], 'ans' => 'b'],
                    ['q' => 'Emergency Contact section has:', 'opts' => ['a) Bank details', 'b) Name, Phone, Relationship', 'c) Tax number', 'd) Salary'], 'ans' => 'b'],
                    ['q' => 'Best time to enter results:', 'opts' => ['a) End of year', 'b) Promptly after assessment', 'c) When generating report cards', 'd) During holidays'], 'ans' => 'b'],
                ]
            ],
        ];
    }

    public function downloadModule(string $module)
    {
        $courseData = $this->getCourseData();
        $module = (int) $module;

        if (!isset($courseData[$module])) {
            abort(404, 'Module not found');
        }

        $moduleData = $courseData[$module];

        $pdf = Pdf::loadView('pdf.training-module', [
            'module' => $module,
            'moduleData' => $moduleData,
            'schoolName' => 'St. Francis of Assisi School',
            'generatedAt' => now()->format('F d, Y'),
        ]);

        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('isHtml5ParserEnabled', true);

        return $pdf->download("Module_{$module}_{$moduleData['title']}.pdf");
    }

    public function downloadAll()
    {
        $courseData = $this->getCourseData();

        $pdf = Pdf::loadView('pdf.training-all-modules', [
            'courseData' => $courseData,
            'schoolName' => 'St. Francis of Assisi School',
            'generatedAt' => now()->format('F d, Y'),
        ]);

        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('isHtml5ParserEnabled', true);

        return $pdf->download("Teacher_Training_Complete_Manual.pdf");
    }

    public function downloadPage()
    {
        $courseData = $this->getCourseData();
        return view('training.downloads', [
            'courseData' => $courseData,
            'schoolName' => 'St. Francis of Assisi School',
        ]);
    }
}
