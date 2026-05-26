# 📊 Primary Teacher Import Template - Quick Guide

## ✅ **Enhanced Excel Template Created!**

### **Download Link:**
```
http://portal.stfrancisofassisizm.com/downloads/templates/Primary_Teacher_Import_Template.xlsx
```

---

## 🎯 **Key Features**

### **1. Smart Dropdowns for Data Validation**
The Excel file includes dropdown menus for:

- ✅ **Status** → active, inactive, suspended
- ✅ **Department** → Primary, ECL (only)
- ✅ **Position** → Teacher, Senior Teacher, Head of Department, Deputy Head Teacher
- ✅ **Qualification** → Certificate/Diploma/Bachelor/Master options
- ✅ **Boolean Fields** → TRUE or FALSE
- ✅ **Grade** → Baby Class, Middle Class, Reception, Grade 1-7
- ✅ **Section** → A, B, C, D
- ✅ **Bank Name** → All major Zambian banks

### **2. Visual Indicators**
- 🟡 **Yellow highlighted fields** = Required (must fill)
- ⬜ **White fields** = Optional
- 📋 **Frozen header rows** = Easy navigation while scrolling

### **3. Built-in Instructions**
- Dedicated "Instructions" sheet with detailed guide
- Field descriptions in row 2 of main sheet
- Helpful prompts when clicking dropdown fields

### **4. Sample Data**
- 3 complete example teachers included
- Demonstrates Primary, ECL, Class Teachers, and Grade Teachers
- Can be modified or deleted

---

## 📝 **Quick Start Guide**

### **Step 1: Download Template**
Click the download link above and open in Microsoft Excel or Google Sheets

### **Step 2: Review Instructions**
Read the "Instructions" sheet (first tab) for detailed guidance

### **Step 3: Fill Data**
- Start from Row 3 (after headers and descriptions)
- Use dropdowns for validated fields
- Fill required fields (highlighted in yellow)

### **Step 4: Save & Import**
- Save as Excel (.xlsx) format
- Import through Admin Panel → Teachers → Import

---

## ⚠️ **Important Rules for Primary Section**

### **✅ DO:**
- Select "Primary" or "ECL" from Department dropdown
- Leave Specialization field EMPTY
- Use TRUE/FALSE for boolean fields
- Select grades from Baby Class to Grade 7 only
- Format dates as YYYY-MM-DD (e.g., 2024-01-15)

### **❌ DON'T:**
- Don't enter "Secondary" as department
- Don't fill Specialization field (for primary teachers)
- Don't use Grade 8-12 (those are for secondary)
- Don't use random date formats
- Don't leave required fields empty

---

## 👥 **Teacher Assignment Types**

### **Class Teacher:**
```
is_class_teacher = TRUE
grade = Grade 5
class_section = A
```
Responsible for: Grade 5A

### **Grade Teacher:**
```
is_grade_teacher = TRUE
grade = Grade 7
class_section = (empty)
```
Responsible for: Entire Grade 7

### **Regular Teacher:**
```
is_class_teacher = FALSE
is_grade_teacher = FALSE
grade = (empty)
class_section = (empty)
```
Responsible for: Teaching specific subjects (assigned later)

---

## 🏫 **Valid Primary Grades**

**ECL (Early Childhood):**
- Baby Class
- Middle Class
- Reception

**Primary:**
- Grade 1
- Grade 2
- Grade 3
- Grade 4
- Grade 5
- Grade 6
- Grade 7

---

## 💡 **Pro Tips**

1. **Bulk Entry:** Copy-paste from existing spreadsheets but verify dropdowns
2. **Email Validation:** Excel will not validate email format - double-check manually
3. **Unique IDs:** Use consistent numbering (TEA001, TEA002, etc.)
4. **Phone Format:** Use international format (+260971234567)
5. **Salary:** Enter as decimal (8500.00 or just 8500)

---

## 🔍 **Common Mistakes to Avoid**

| ❌ Wrong | ✅ Correct |
|----------|-----------|
| grade 5 | Grade 5 |
| Secondary | Primary or ECL |
| Mathematics | (leave empty) |
| 15/01/2024 | 2024-01-15 |
| Yes | TRUE |
| section A | A |

---

## 📞 **Need Help?**

- Check the Instructions sheet in the Excel file
- Review sample data rows (rows 3-5)
- Contact system administrator
- Refer to TEACHER_IMPORT_INSTRUCTIONS.md for detailed info

---

**Template Version:** 2.0 (Enhanced with Dropdowns)
**Last Updated:** October 2025
**For:** St. Francis of Assisi School Portal - Primary Section
