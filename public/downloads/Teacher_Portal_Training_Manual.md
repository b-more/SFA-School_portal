# ST. FRANCIS OF ASSISI SCHOOL
# TEACHER PORTAL TRAINING MANUAL
### School Management Information System (MIS)
---
**Portal URL**: https://portal.stfrancisofassisi.tech/admin
**Document Version**: 1.0
**Date**: March 2026
**Audience**: All Teaching Staff (Primary & Secondary)

---

## TABLE OF CONTENTS

1. [Getting Started](#1-getting-started)
2. [Teacher Dashboard](#2-teacher-dashboard)
3. [Daily Attendance](#3-daily-attendance)
4. [Entering Results](#4-entering-results)
5. [Homework Management](#5-homework-management)
6. [Report Cards](#6-report-cards)
7. [Attendance Reports](#7-attendance-reports)
8. [Viewing Students](#8-viewing-students)
9. [My Payslips](#9-my-payslips)
10. [My Profile](#10-my-profile)
11. [Quick Reference Guide](#11-quick-reference-guide)
12. [Troubleshooting & FAQs](#12-troubleshooting--faqs)

---

## 1. GETTING STARTED

### 1.1 Logging In

1. Open your web browser (Chrome, Firefox, or Edge recommended)
2. Go to: **https://portal.stfrancisofassisi.tech/admin**
3. Enter your **Email Address** and **Password**
4. Click **Sign In**

> **First-Time Login**: If this is your first time logging in, you will be prompted to change your password. Choose a strong password with at least 8 characters.

### 1.2 Understanding Your Role

As a teacher, the system recognises you in two capacities:

| Role | Description |
|------|-------------|
| **Class Teacher** | You are assigned as the main teacher for a specific class (e.g., Grade 3A). You can mark attendance, enter results for ALL subjects, and write report card comments. |
| **Subject Teacher** | You teach specific subjects in one or more classes (e.g., Mathematics in Grade 7A & 7B). You can enter results only for your assigned subjects. |

### 1.3 Navigation Overview

The left sidebar contains your main menu. As a teacher, you will see these sections:

| Menu Section | Contains |
|-------------|----------|
| **Dashboard** | Your personalised teacher dashboard |
| **Academic** | Daily Attendance, Enter Results, Report Cards |
| **Teaching** | Homework |
| **Reports** | Attendance Reports |
| **Staff Management** | My Payslips |

---

## 2. TEACHER DASHBOARD

**Location**: Sidebar > Dashboard
**URL**: `/admin/dashboard`

Your dashboard is the first page you see after logging in. It gives you a quick overview of your teaching activities.

### 2.1 What You See on the Dashboard

| Section | What It Shows |
|---------|---------------|
| **My Classes** | All classes you are assigned to (as class teacher or subject teacher) |
| **Student Count** | Total number of students across your classes |
| **Recent Homework** | Your most recent homework assignments and submission counts |
| **Recent Attendance** | Attendance records from the last 7 days |
| **Grading Summary** | Total submissions, how many are graded, ungraded, and late |
| **Upcoming Events** | School events relevant to your grade level |

### 2.2 Quick Action Buttons

At the top of the dashboard, you will find shortcut buttons:

| Button | What It Does |
|--------|-------------|
| **Create Homework** | Opens the homework creation form directly |
| **Grade Submissions** | Takes you to homework submissions awaiting marks |
| **View My Classes** | Shows your student lists |
| **Record Results** | Takes you to the Enter Results page |

---

## 3. DAILY ATTENDANCE

**Location**: Sidebar > Academic > Daily Attendance
**URL**: `/admin/mark-attendance`

### 3.1 How to Mark Attendance

**Step 1 - Select Class**
- If you are a **class teacher**, your class is automatically selected
- If you teach multiple classes, select the class from the dropdown

**Step 2 - Select Date**
- The date defaults to **today**
- You can select a past date if you need to mark attendance for a missed day
- You cannot select a future date

**Step 3 - Mark Each Student**
- A list of all students in the class will appear
- Each student starts with no status selected
- Click the status button next to each student's name to cycle through options:

| Status | Symbol | Meaning |
|--------|--------|---------|
| **Present** | P | Student is in school |
| **Absent** | X | Student is not in school |
| **Sick** | S | Student is sick (with or without medical note) |
| **Late** | Y | Student arrived late |
| **Excused** | L | Student has permission to be absent |

**Step 4 - Quick Mark All Present**
- Click **"Mark All Present"** to mark every student as Present at once
- Then adjust individual students who are absent, sick, or late

**Step 5 - Submit**
- Click **"Submit Attendance"**
- A summary will appear showing: Present: X | Absent: X | Sick: X | Late: X | Excused: X
- The attendance is now saved

### 3.2 Important Notes on Attendance

- You can **edit** attendance for the same day by going back and resubmitting
- Attendance should be marked **every school day** by the class teacher
- The system records the **time** attendance was submitted
- If you mark attendance after midday, the system still records it for that date

---

## 4. ENTERING RESULTS

**Location**: Sidebar > Academic > Enter Results
**URL**: `/admin/enter-results`

### 4.1 Understanding Result Types

| Exam Type | When to Use |
|-----------|-------------|
| **Mid-Term** | Mid-term test results |
| **Final** | End-of-term examination results |
| **End of Term Test** | End-of-term test (different from final exam) |
| **Quiz** | Short quizzes or class tests |
| **Assignment** | Graded assignments |
| **Test** | General tests |

### 4.2 How to Enter Results

**Step 1 - Select Class**
- Choose the class section from the dropdown
- **Class Teachers**: Can enter results for ALL subjects in their class
- **Subject Teachers**: Can only enter results for subjects they are assigned to teach

**Step 2 - Select Subject**
- Choose the subject from the dropdown
- The list shows only subjects assigned to the selected grade

**Step 3 - Select Term & Exam Type**
- Select the **Term** (e.g., Term 1, Term 2, Term 3)
- Select the **Exam Type** (e.g., Mid-Term, Final)
- Select the **Academic Year**

**Step 4 - Enter Marks**
- A table of all students in the class appears
- For each student, enter:
  - **Marks** (0 to 100) - Required
  - **Grade** - Auto-calculated based on the mark, but can be manually adjusted
  - **Comment** - Optional feedback for the student

**Step 5 - Submit**
- Click **"Submit Results"**
- The system will show how many records were saved and how many were skipped (blank entries)

### 4.3 Grade Scale (Auto-Calculated)

| Mark Range | Grade |
|-----------|-------|
| 80 - 100 | A |
| 70 - 79 | B |
| 60 - 69 | C |
| 50 - 59 | D |
| 40 - 49 | E |
| 0 - 39 | F |

> **Note**: The grade scale may vary. The system uses the school's configured grading scale.

### 4.4 Tips for Entering Results

- You can **clear all marks** and start over using the "Clear All" button
- If a student was absent for the exam, leave their marks **blank** (do not enter 0)
- You can come back and edit results later by selecting the same class, subject, term, and exam type
- Always double-check your entries before submitting

---

## 5. HOMEWORK MANAGEMENT

**Location**: Sidebar > Teaching > Homework
**URL**: `/admin/homework`

### 5.1 Creating Homework

**Step 1** - Click the **"Create Homework"** button (top right)

**Step 2** - Fill in the form:

| Field | Description | Required |
|-------|-------------|----------|
| **Title** | Name of the homework (e.g., "Chapter 5 Exercises") | Yes |
| **Subject** | Select from your assigned subjects only | Yes |
| **Grade** | Select from your assigned grades only | Yes |
| **Due Date & Time** | When the homework must be submitted (default: 1 week from now) | Yes |
| **Instructions** | Detailed instructions for students | Yes |
| **Attachments** | Upload files (PDFs, images, documents) | No |
| **Status** | Active (visible to students) or Inactive (hidden) | Yes |
| **Academic Year** | Current academic year (auto-selected) | Yes |

**Step 3** - Click **"Create"**

### 5.2 Managing Existing Homework

On the homework list page, you can:

| Action | How |
|--------|-----|
| **View Details** | Click the homework title |
| **Edit** | Click the pencil icon (only your own homework) |
| **Delete** | Click the delete icon (only your own homework) |
| **Filter** | Use filters to find homework by status, grade, or subject |
| **Search** | Type in the search box to find by title |

### 5.3 Viewing Submissions

- Click on a homework assignment to see student submissions
- You can see which students have submitted and which have not
- Late submissions are marked with an indicator
- You can download attached student files

### 5.4 Important Notes

- You can only create homework for **subjects and grades you are assigned to**
- Students and parents can view homework through their own portal access
- Setting status to **Inactive** hides the homework from students

---

## 6. REPORT CARDS

### 6.1 Generating Report Cards

**Location**: Sidebar > Academic > Report Cards (Generate)
**URL**: `/admin/generate-report-cards`

**Step 1 - Select Class**
- Choose your class from the dropdown
- **Class Teachers**: See your assigned class
- **Subject Teachers**: See classes where you teach

**Step 2 - Select Term & Year**
- Select the term and academic year for the report cards

**Step 3 - Review Student Data**
The table shows each student with:

| Column | Meaning |
|--------|---------|
| **Results Count** | How many subjects have marks entered |
| **Subject Count** | Total subjects in the grade |
| **Average** | Overall average mark |
| **Comment Status** | Whether class teacher and head teacher comments are added |
| **Last Generated** | When the report card was last created |

**Step 4 - Add Comments (Class Teachers Only)**
- Click on a student to open the comment modal
- Write your **Class Teacher Comment** (e.g., "Excellent performance. Keep it up.")
- Comments are saved with timestamps
- Head Teacher comments are added by the admin

**Step 5 - Generate PDF**
- Click **"Generate"** next to a student for individual report card
- Click **"Generate All"** for the entire class (creates one PDF with all students)

### 6.2 Viewing & Downloading Report Cards

**Location**: Sidebar > Academic Management > Report Cards
**URL**: `/admin/report-cards`

- Select **Class** and **Term**
- For each student you can:
  - **Preview**: Opens the report card in a new browser tab
  - **Download**: Downloads the PDF file
- **Bulk Download**: Download all report cards for the class as one PDF

### 6.3 What the Report Card Contains

- Student personal details (name, admission number, class)
- Subject-by-subject results (mid-term marks, end-of-term marks, average, grade)
- Overall average and grade
- Class position (rank out of total students)
- Class teacher comment
- Head teacher comment
- School stamp and signature areas

### 6.4 Sending Results via SMS

From the Generate Report Cards page:

1. Click **"Send SMS"**
2. Preview the SMS messages for the first 3 students
3. Confirm to send to all parents
4. SMS includes: Student name, rank, top subject marks

> **Note**: SMS sending requires available SMS credits. Contact the admin if credits are low.

---

## 7. ATTENDANCE REPORTS

**Location**: Sidebar > Reports > Attendance Reports
**URL**: `/admin/attendance-reports`

### 7.1 Generating Reports

**Step 1 - Select Report Type**

| Report Type | What It Shows |
|-------------|---------------|
| **All Attendance** | Complete attendance records |
| **By Class Section** | Attendance for a specific class |
| **By Grade** | Attendance for an entire grade |
| **By Student** | One student's attendance history |
| **By Status** | All records with a specific status (e.g., all absences) |
| **Attendance Summary** | Statistical overview with percentages |

**Step 2 - Apply Filters**
- Select **date range** (From date - To date)
- Select **academic year** and **term**
- Select **grade** and/or **class section**
- Select **status** (if filtering by status)

**Step 3 - View Results**
- The table shows filtered attendance records
- You can sort by any column

### 7.2 Exporting Reports

| Export Option | Format | Description |
|--------------|--------|-------------|
| **Monthly Register (PDF)** | PDF | Landscape A4 register with days 1-31 as columns, students as rows, and status symbols (P, X, S, Y, L) |
| **Monthly Register (CSV)** | CSV/Excel | Same data in spreadsheet format for further analysis |
| **Summary PDF** | PDF | Statistical summary with counts and percentages |

### 7.3 Attendance Symbols in Reports

| Symbol | Meaning |
|--------|---------|
| P | Present |
| X | Absent |
| S | Sick |
| Y | Late |
| L | Excused |

> **Teacher Access**: You can only generate reports for classes you are assigned to.

---

## 8. VIEWING STUDENTS

**Location**: Sidebar > Student Management > Students
**URL**: `/admin/students`

### 8.1 What You Can See

As a teacher, you can view students in your assigned classes:

| Information | Visible |
|------------|---------|
| Student name & admission number | Yes |
| Gender & date of birth | Yes |
| Class & grade | Yes |
| Parent/Guardian contact | Yes |
| Enrollment status | Yes |
| Fee information | No (Admin only) |
| Personal address | Limited |

### 8.2 Filtering & Searching

- **Search**: Type a student's name or admission number in the search box
- **Filter by Grade**: Select a specific grade
- **Filter by Class**: Select a specific class section
- **Filter by Status**: Show only active, inactive, or transferred students

### 8.3 Teacher Restrictions

- **Class Teachers** see all students in their class
- **Subject Teachers** see students in classes where they teach
- **Dean/Head Teachers** see students across their entire section (Primary or Secondary)
- You **cannot** edit student personal details (admin function)
- You **cannot** view fee or financial information

---

## 9. MY PAYSLIPS

**Location**: Sidebar > Staff Management > My Payslips
**URL**: `/admin/my-payslips`

### 9.1 What You See

- A list of your payroll records sorted by year and month (most recent first)
- Each entry shows the pay period (month/year)
- You can view details of each payslip

### 9.2 Important Notes

- Payslips are uploaded by the administration
- If you notice any discrepancy, contact the HR/Admin office
- Payslips are only visible to you (no other teacher can see your payslips)

---

## 10. MY PROFILE

**Location**: Click your name/avatar (top right) > My Profile
**URL**: `/admin/profile`

### 10.1 What You Can Edit

| Section | Editable Fields |
|---------|----------------|
| **Profile Photo** | Upload a new photo (auto-cropped to square) |
| **Contact Info** | Email, Phone number, Address, City, Province |
| **Emergency Contact** | Name, Phone, Relationship |
| **Next of Kin** | Name, Phone, Relationship, Address |

### 10.2 Changing Your Password

1. Click the **"Change Password"** button on your profile page
2. Enter your **current password**
3. Enter your **new password** (minimum 8 characters)
4. Confirm the new password
5. Click **"Save"**

### 10.3 Downloads Available

| Document | Description |
|----------|-------------|
| **Profile PDF** | A full staff profile document |
| **Business Card** | Professional business card with QR code |
| **Employee ID Card** | Staff ID card with photo and QR code |

---

## 11. QUICK REFERENCE GUIDE

### 11.1 Daily Tasks Checklist

| Time | Task | Location |
|------|------|----------|
| Morning (before 08:00) | Mark attendance for your class | Daily Attendance |
| During the day | Check homework submissions | Homework |
| After assessments | Enter student results | Enter Results |
| End of term | Write report card comments | Generate Report Cards |
| End of term | Generate and review report cards | Report Cards |

### 11.2 Keyboard Shortcuts

| Shortcut | Action |
|----------|--------|
| Click status button | Cycle through P > X > S > Y > L |
| Tab | Move to next field (in results entry) |
| Enter | Submit form |

### 11.3 Common Workflows

**Workflow 1: Morning Attendance Routine**
1. Log in to portal
2. Go to Daily Attendance
3. Verify your class is selected and date is today
4. Click "Mark All Present"
5. Change status for absent/late/sick students
6. Click "Submit Attendance"

**Workflow 2: Entering Test Results**
1. Go to Enter Results
2. Select your class, subject, term, and exam type
3. Enter marks for each student (0-100)
4. Review the auto-calculated grades
5. Add comments for notable performances
6. Click "Submit Results"

**Workflow 3: End-of-Term Report Cards**
1. Ensure ALL results are entered for your subjects
2. Go to Generate Report Cards
3. Select your class and the current term
4. Check that results count matches subject count for each student
5. Add class teacher comments for each student
6. Click "Generate All" to create PDF report cards
7. Review a few report cards for accuracy
8. Send SMS notifications to parents (if approved)

**Workflow 4: Creating Homework**
1. Go to Teaching > Homework
2. Click "Create Homework"
3. Fill in title, subject, grade, due date
4. Write clear instructions
5. Attach any reference materials (PDF, images)
6. Set status to "Active"
7. Click "Create"

---

## 12. TROUBLESHOOTING & FAQs

### 12.1 Common Issues

**Q: I cannot see my class in the dropdown.**
A: Your class assignment may not be set up. Contact the administrator to assign you as a class teacher or subject teacher through the **Teacher Assignments** module.

**Q: I cannot enter results for a subject.**
A: You can only enter results for subjects you are assigned to teach. If you should be teaching a subject but it doesn't appear, ask the admin to assign you via **Teacher Assignments**.

**Q: The attendance page shows "No students found."**
A: This means either no students are enrolled in the selected class, or you are not assigned to that class. Contact admin.

**Q: I entered marks but the grades show incorrectly.**
A: Grades are auto-calculated based on the school's grading scale. You can manually override the grade if needed. If the grading scale is wrong, contact admin.

**Q: I cannot edit homework I created.**
A: Make sure you are logged in with the same account that created the homework. Only the creator and admins can edit homework.

**Q: My report card shows 0 results for students.**
A: Results must be entered through the **Enter Results** page first. Make sure you selected the correct term, exam type, and academic year when entering results.

**Q: The SMS notification failed to send.**
A: This is usually because SMS credits are exhausted. Contact admin to top up SMS credits.

**Q: I forgot my password.**
A: Contact the school administrator to reset your password.

**Q: The report card PDF is blank or incomplete.**
A: Ensure results have been entered for the selected term. Check that the correct academic year and term are selected.

**Q: How do I know if attendance was saved?**
A: After submitting, a green notification will appear showing the attendance summary (Present: X, Absent: X, etc.). You can also check the Attendance Reports page to verify.

### 12.2 Best Practices

1. **Mark attendance daily** before the first period
2. **Enter results promptly** after each assessment, not at the end of term
3. **Double-check marks** before submitting (mistakes can be corrected but it's easier to get it right the first time)
4. **Write meaningful comments** on report cards - parents value personalised feedback
5. **Keep your profile updated** with current phone number and emergency contacts
6. **Log out** when leaving a shared computer (click your name > Sign Out)
7. **Use Chrome or Firefox** for the best experience
8. **Do not share your login credentials** with anyone

### 12.3 Getting Help

If you encounter any issues not covered in this manual:

1. Contact the **School Administrator** or **ICT Department**
2. Provide details about:
   - What you were trying to do
   - What error message appeared (if any)
   - The page URL where the issue occurred

---

## APPENDIX A: GLOSSARY

| Term | Meaning |
|------|---------|
| **Academic Year** | The school year (e.g., 2026) |
| **Term** | A division of the academic year (Term 1, Term 2, Term 3) |
| **Class Section** | A specific class (e.g., Grade 3A, Grade 3B) |
| **Grade** | The year/level (e.g., Grade 1, Grade 7, Grade 11) |
| **Subject Teaching** | The assignment of a teacher to teach a specific subject in a specific class |
| **Class Teacher** | The teacher responsible for a specific class section |
| **Report Card** | End-of-term academic report for each student |
| **Admission Number** | Unique identifier for each student |

## APPENDIX B: NAVIGATION MAP

```
Dashboard
|
+-- Academic
|   +-- Daily Attendance (/admin/mark-attendance)
|   +-- Enter Results (/admin/enter-results)
|   +-- Generate Report Cards (/admin/generate-report-cards)
|
+-- Academic Management
|   +-- Report Cards (/admin/report-cards)
|   +-- Results (/admin/results)
|
+-- Teaching
|   +-- Homework (/admin/homework)
|
+-- Student Management
|   +-- Students (/admin/students)
|
+-- Reports
|   +-- Attendance Reports (/admin/attendance-reports)
|
+-- Staff Management
|   +-- My Payslips (/admin/my-payslips)
|
+-- Profile (top-right menu)
    +-- My Profile (/admin/profile)
    +-- Change Password
    +-- Sign Out
```

---

**END OF TRAINING MANUAL**

*St. Francis of Assisi School - School Management Information System*
*For support, contact the School Administrator or ICT Department.*
