# 📘 Scholarship Form Generator

This project is a **PHP web application** with HTML and CSS frontend that allows users to fill in a scholarship form and automatically generate a **Word document (.docx)** based on the provided data.

---

## ✨ Features

- 📄 **Dynamic Word document generation** using [PhpOffice/PhpWord](https://github.com/PHPOffice/PHPWord)  
- 📝 **Form sections** for:
  - Financial & Identification Data (IČO, DIČ, Bank, Account Number, IBAN)  
  - Student Information (name, birth date, contact details, address)  
  - Study Program & Educational Program (codes, names, form, length, degree)  
  - Guardian Information (name, email, phone)  
  - Employer Information (company details, bank info, registration numbers, PPV address)  
  - School Information (name, address)  
  - Contract Information (end date of contract)  
  - Agreement Declaration (checkbox for consent)  
- 🎨 **Modern UI design** with custom-styled date picker & checkboxes  
- 🔒 **Validation** – required fields ensure correct input  
- 📥 **Auto-download** of the completed form as `Štipendijný_formulár.docx`  

---

## 🛠️ How It Works

1. User opens the app in a browser.  
2. A structured web form is displayed.  
3. After filling required data, the user clicks **Submit**.  
4. PHP script:
   - Loads `template.docx`  
   - Replaces placeholders with submitted values  
   - Saves result as `Štipendijný_formulár.docx`  
   - Forces download of the completed file  
   - Cleans up temporary file  

---

## 📂 Project Structure

/index.php → Main PHP application (form + backend logic)
/template.docx → Word template with placeholders
/vendor/ → PhpWord library (installed via Composer)


---

## 🎨 Color Palette

- **Dark Blue (Background/Header):** `#164065`  
- **Orange-Red (Buttons):** `#ed4322`  
- **Azure Blue (Icons/Highlights):** `#0c95db`  

---

## 🚀 Requirements

- PHP **8.0+**
- phpoffice/phpword dependency
- [Composer](https://getcomposer.org/) to install dependencies
- Web server (Apache/Nginx) or PHP built-in server

---

## ▶️ Installation

1. Clone this repository or download files. 

2. Install dependencies via Composer:  
   ```bash
   composer require phpoffice/phpword

3. Place your template.docx in the project root.

4. Run local PHP server:

     php -S localhost:8000

5. Open http://localhost:8000 in your browser.

## 📤 Output

   - When the form is submitted, the user automatically downloads: `Štipendijný_formulár.docx` containing all entered data.
    
