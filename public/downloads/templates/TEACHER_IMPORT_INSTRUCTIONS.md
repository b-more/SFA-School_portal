# Teacher Import Template - Instructions

## 📋 Overview
This template allows you to bulk import teacher data into the St. Francis of Assisi School Portal system.

---

## 📊 Column Descriptions

### REQUIRED COLUMNS ✅

| Column | Description | Example | Rules |
|--------|-------------|---------|-------|
| **name** | Full name of teacher | John Mwale | Must not be empty |
| **email** | Email address | john.mwale@stfrancisofassisizm.com | Must be unique, valid email format |
| **phone** | Phone number | +260971234567 | Zambian format recommended |
| **employee_id** | Unique employee identifier | TEA001 | Must be unique |

### ACCOUNT INFORMATION

| Column | Description | Example | Default |
|--------|-------------|---------|---------|
| **username** | Login username | john.mwale | Auto-generated from name if empty |
| **password** | Login password | password123 | "password" if empty |
| **status** | Account status | active | active |

**Valid status values:** active, inactive, suspended

### EMPLOYMENT DETAILS

| Column | Description | Example | Notes |
|--------|-------------|---------|-------|
| **department** | Teacher department | Primary | ECL, Primary, or Secondary |
| **position** | Job position | Senior Teacher | Optional |
| **qualification** | Education qualification | Bachelor of Education | Optional |
| **specialization** | Subject specialization | Mathematics | Required for Secondary teachers only |
| **join_date** | Employment start date | 2024-01-15 | Format: YYYY-MM-DD |
| **basic_salary** | Monthly salary | 8500.00 | Decimal format |

### IDENTITY & BANKING

| Column | Description | Example | Notes |
|--------|-------------|---------|-------|
| **nrc** | National Registration Card | 123456/78/1 | Zambian format |
| **tpin** | Tax PIN | 1234567890 | 10 digits |
| **account_number** | Bank account number | 0123456789 | Optional |
| **bank_name** | Bank name | Zanaco | Optional |
| **bank_branch** | Bank branch | Cairo Road | Optional |

### CONTACT INFORMATION

| Column | Description | Example | Notes |
|--------|-------------|---------|-------|
| **address** | Physical address | Plot 123, Kabulonga, Lusaka | Full address recommended |

### TEACHER ASSIGNMENTS

| Column | Description | Example | Rules |
|--------|-------------|---------|-------|
| **is_active** | Is teacher currently active? | TRUE | TRUE/FALSE, 1/0, YES/NO |
| **is_class_teacher** | Assigned as class teacher? | TRUE | TRUE/FALSE, 1/0, YES/NO |
| **is_grade_teacher** | Assigned as grade teacher? | FALSE | TRUE/FALSE, 1/0, YES/NO |
| **grade** | Assigned grade | Grade 5 | Must match existing grade exactly |
| **class_section** | Class section | A | Only if is_class_teacher = TRUE |

---

## 🎯 Important Rules

### 1. Teacher Types

**Primary Teachers:**
- Department: "Primary" or "ECL"
- Specialization: Leave EMPTY
- Grades: Baby Class, Middle Class, Reception, Grade 1-7
- Usually assigned as class teachers

**Secondary Teachers:**
- Department: "Secondary"
- Specialization: REQUIRED (e.g., Mathematics, Physics, Chemistry, Biology, English, etc.)
- Grades: Grade 8-12
- May be grade teachers or subject teachers

### 2. Valid Grade Names (Case Sensitive!)
```
Baby Class
Middle Class
Reception
Grade 1
Grade 2
Grade 3
Grade 4
Grade 5
Grade 6
Grade 7
Grade 8
Grade 9
Grade 10
Grade 11
Grade 12
```

### 3. Valid Class Sections
```
A, B, C, D, etc.
```

### 4. Boolean Values
Any of these formats work:
- TRUE / FALSE
- true / false
- 1 / 0
- YES / NO
- yes / no

### 5. Date Format
Always use: **YYYY-MM-DD**
- ✅ Correct: 2024-01-15
- ❌ Wrong: 15/01/2024, 01-15-2024, Jan 15 2024

### 6. Decimal Numbers
Use dot (.) for decimals:
- ✅ Correct: 8500.00, 12500
- ❌ Wrong: 8,500.00, 8500,00

---

## 📝 Example Rows

### Example 1: Primary Class Teacher
```csv
John Mwale,john.mwale@stfrancisofassisizm.com,+260971234567,TEA001,john.mwale,password123,active,Primary,Teacher,Diploma in Primary Education,,2024-01-15,8500.00,123456/78/1,1234567890,0123456789,Zanaco,Cairo Road,"Plot 123, Kabulonga, Lusaka",TRUE,TRUE,FALSE,Grade 5,A
```

### Example 2: Secondary Subject Teacher (Grade Teacher)
```csv
Sarah Tembo,sarah.tembo@stfrancisofassisizm.com,+260975111005,TEA002,sarah.tembo,password123,active,Secondary,Senior Teacher,PhD in Mathematics,Mathematics,2023-09-01,15000.00,234567/89/1,2345678901,9876543210,Stanbic,Longacres,"Plot 456, Roma, Lusaka",TRUE,FALSE,TRUE,Grade 11,
```

### Example 3: ECL Teacher
```csv
Mary Banda,mary.banda@stfrancisofassisizm.com,+260975111001,TEA003,mary.banda,password123,active,ECL,Teacher,Certificate in Early Childhood Education,,2023-05-10,7500.00,345678/90/1,3456789012,1234567890,Zanaco,Woodlands,"Plot 789, Chilenje, Lusaka",TRUE,TRUE,FALSE,Baby Class,A
```

---

## ⚠️ Common Mistakes to Avoid

1. **Duplicate Emails or Employee IDs**
   - Each email and employee_id must be unique

2. **Wrong Grade Names**
   - Use exact names from the valid list above
   - "Grade 5" not "grade 5" or "5th Grade"

3. **Secondary Teachers Without Specialization**
   - All secondary teachers MUST have a specialization

4. **Wrong Date Format**
   - Always use YYYY-MM-DD format

5. **Addresses with Commas**
   - Enclose addresses in quotes if they contain commas
   - Example: "Plot 123, Kabulonga, Lusaka"

6. **Empty Required Fields**
   - name, email, phone, employee_id must have values

---

## 🚀 Import Process

1. **Download Template**
   - Use `teacher_import_template.csv` for examples
   - Use `teacher_import_blank_template.csv` for clean start

2. **Fill in Data**
   - Open in Excel, Google Sheets, or any spreadsheet software
   - Fill in teacher information row by row

3. **Validate Data**
   - Check for duplicate emails and employee IDs
   - Verify all required fields are filled
   - Ensure grade names match exactly

4. **Save as CSV**
   - Save the file as CSV (Comma Separated Values)
   - Make sure encoding is UTF-8

5. **Import to System**
   - Upload through the admin panel
   - Review any error messages
   - Fix errors and re-upload if needed

---

## 📞 Support

If you encounter any issues:
1. Check this instruction file
2. Review the example template
3. Contact system administrator

---

**Generated for:** St. Francis of Assisi School Portal
**Version:** 1.0
**Last Updated:** October 2025
