# Mini-Aspire API
## About Project

Project includes Loan Functionality where users register themself and submit loan request. On the other hand admin will approve the loan. And once the loan got approved the user can repay their loan installments.

Few Major Feature are included:

- Auth Functionality
- Customer and Admin Scopes
- Repayment amount can be more than or equal to schedule repayments
- Adjustment of excess amount to their upcoming installments
- Payment records for the loan

## Requirements
1. PHP (^7.3)
2. Composer

## Installation

1. Create database and define the `DB_DATABASE` with the database name in `.env` file
2. Use command `composer install`
3. Use Command `php artisan migrate --seed`
4. Create new Variable i.e `PASSPORT_URL` and define with the server url (if localhost try to run server with another port and paste the server URL) in `.env` file 
5. Use Command `php artisan passport:install`
6. Copy the `Password Grant Client ID` and define `PASSPORT_PASSWORD_CLIENT_ID` with the above copied value in `.env` file. Also Copy `Password Grant Client Secret` and define `PASSPORT_PASSWORD_CLIENT_SECRET` with the copied value in `.env` file
7. To run the test use `php artisan test`

## Developer
1. [Rahul Kumar Sharma](http://rahulkrsharma.in)
