# Loan Payment Link Generator — CodeIgniter 4

A small CodeIgniter 4 project that manages loan borrowers and generates unique payment links (full or custom amounts) for each borrower.

---

## 🎯 Features
- Displays a borrower list with loan amount due.
- “Create Link” button for each borrower.
- Modal dialog for selecting:
  - Full payment
  - Custom amount
- Generates a **unique token link** (e.g. `http://localhost:8080/pay/<token>`).
- Stores each link in database (`payment_links` table).
- Includes a simulated payment page (no gateway integration).

---

## 🛠️ Tech Stack
- **Backend:** CodeIgniter 4 (PHP MVC Framework)
- **Frontend:** Bootstrap 5
- **Database:** MySQL
- **Server:** PHP Localhost / XAMPP

---

## ⚙️ Installation Steps

### 1. Clone or Extract
git clone https://github.com/mohit-aggarwal314/LoanLinkGenerator_CI4.git
cd LoanLinkGenerator_CI4

### 2. Install Dependencies
composer install

### 3. Import Database
Create a new database `loan_app` and import the sql file given.

php spark serve
Open http://localhost:8080/loans
