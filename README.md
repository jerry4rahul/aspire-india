# Mini-Aspire API
## About Project

The Project includes Loan Functionality where users register themself and submit a loan request. On the other hand, the admin will approve the loan. And once the loan got approved the user can repay their loan instalments.

Few Major Feature are included:

- Auth Functionality
- Customer and Admin Scopes
- The Repayment amount can be more than or equal to the scheduled repayments
- Adjustment of excess amount to their upcoming instalments
- Payment records for the loan

## Requirements
1. PHP (^7.3)
2. Composer

## Installation

1. Create a new database and define the `DB_DATABASE` with the database name in `.env` file
2. Define `APP_URL` in env with server URL
3. Use command `composer install`
4. Use command `php artisan migrate --seed`. This creates a default admin user in the database where `Email = admin@gmail.com` and `Password = password`
5. Create a new variable i.e `PASSPORT_URL` and define the server URL (if localhost tries to run the server with another port and paste the server URL) in `.env` file 
6. Use command `php artisan passport:install`
7. Copy the `Password Grant Client ID` and define `PASSPORT_PASSWORD_CLIENT_ID` with the above-copied value in `.env` file. Also, copy `Password Grant Client Secret` and define `PASSPORT_PASSWORD_CLIENT_SECRET` with the copied value in `.env` file
8. To run the test use `php artisan test`

## Developer
1. [Rahul Kumar Sharma](http://rahulkrsharma.in)
