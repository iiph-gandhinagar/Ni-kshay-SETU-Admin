<p align="center"><a href="https://app.nikshay-setu.in/images/Logo.svg" target="_blank"><img src="https://app.nikshay-setu.in/images/Logo.svg" width="400"></a></p>

<div align="center">

  ![Static Badge](https://img.shields.io/badge/Subscribers-40K-green)
  ![Static Badge](https://img.shields.io/badge/Licence-GPL%203.0-blue)

</div>

# Ni-kshay Setu | Support to End TUberculosis

The Ni-kshay Setu app (https://nikshay-setu.in/), already with 40K subscribers, empowers healthcare providers to make informed decisions and contributes to India's mission to combat tuberculosis. Available on web, Android, and iOS platforms in 8 languages, it offers real-time updates, interactive modules, and personalized insights, revolutionizing TB knowledge management and accessibility across India.

## Table of Contents

1. Introduction
2. Features
3. Technologies Used
4. System Requirements
5. Installation
6. Configuration
7. Usage
8. Contribution Guidelines
9. License

## 1. Introduction

Ni-kshay Setu is a groundbreaking digital solution available as a web application, Android application, and iOS application. With a mission to support healthcare providers in decision-making and transform knowledge into empowerment, this innovative and interactive learning tool is a catalyst in India's journey towards a TB-free nation.
As a comprehensive digital platform, Ni-kshay Setu revolutionizes the way healthcare providers approach TB management. By leveraging cutting-edge technology, it empowers medical professionals with real-time support and evidence-based recommendations, ensuring they have the most up-to-date information at their fingertips.
With an intuitive interface and user-friendly design, Ni-kshay Setu offers a seamless experience across devices, making it accessible to a wide range of users. The web application allows healthcare providers to access the platform from any computer, while the Android and iOS applications provide mobility and convenience for on-the-go professionals.
Through a range of interactive modules, virtual simulations, and case studies, Ni-kshay Setu transforms learning into a dynamic and engaging experience. Healthcare providers can enhance their knowledge and skills by practicing TB case management in a risk-free environment. They can diagnose, prescribe treatment plans, and monitor patient progress, gaining invaluable experience and building their confidence in TB management.

> The Ni-kshay Setu app is part of the 'Closing the Gaps in TB care Cascade (CGC)' project, developed by the Indian Institute of Public Health, Gandhinagar (https://iiphg.edu.in/). This project aims to strengthen health systems' ability to comprehensively monitor and respond to the TB care cascade with quality improvement (QI) interventions. This digital solution is one of the key interventions of the project with the objectives to strengthen the knowledge support system of the health staff in TB patient-centric care and program management of the National TB Elimination Program.

> Technological support for this project is provided by Digiflux Technologies Pvt. Ltd. (https://www.digiflux.io), contributing to the development and implementation of the digital solution.

The publication of this application is made possible by the support of the American People through the United States Agency for International Development (USAID). The contents of this document are the sole responsibility of the CGC Project Team and do not necessarily reflect the views of USAID or the United States Government.


## 2. Features

- **Subscriber Monitoring:** Keep track of Ni-kshay SETU subscribers' progress and activities.
- **Data Visualization:** Visualize data in a user-friendly way for easy analysis, making it simpler to understand complex information.
- **Module Management:** Create and manage various modules, including Diagnosis, Treatment, and other types of content to tailor the platform to your needs.
- **Assessment Creation:** Develop and manage assessments for all subscribers or specific groups, allowing for better evaluation and customization. This can also be done at each state/district levels for their respective programs.
- **Material and Document Management:** Organize and provide access to materials and documents for subscribers, ensuring they have the necessary resources.
- **Leaderboard Progress:** Monitor subscribers' progress using leaderboard parameters, encouraging healthy competition and motivation.
- **Notification System:** Implement a notification system to alert and remind subscribers about important information and activities.
- **Roles and Permissions:** Manage roles and permissions for State and District level administrators to maintain control and security.
- **Master Data Management:** Oversee master data such as states, districts, health facilities, and cadres to ensure accuracy and consistency in the system.
- **Automatic News Feed:** Incorporate an automated news feed from various sources, keeping subscribers informed and updated on relevant news and developments.
- **Central Government Applications:** Add relevant applications related to government programs, enhancing the platform's utility and functionality.
- **Multilingual Support:** Control and manage multiple language support directly from the admin panel, making it easier to serve a diverse user base.
- **Chatbot and Machine Learning:** Manage and control the chatbot, machine learning, and data modeling features, offering users an interactive and intelligent experience within the platform.
- **Managing Health Facilities for T.B. :** Efficiently manage healthcare facilities within the platform, complete with configurable locations (State, District, Village, City including Latitude Longitude) and details about the services available at each location.

## 3. Technologies Used

-   Front-end: HTML, CSS, JavaScript, React JS, VueJS
-   Back-end: Laravel
-   Database: Mysql
-   Data Visualization: Chart.js
-   Notification: Email, SMS, Firebase Push Notification Service

## 4. System Requirements

-   Operating System: Windows, Linux, macOS
-   Laravel 8
-   Php 7.4
-   Mysql
-   Internet connectivity for SMS, Email & Firebase Push notifications

## 5. Installation

There are two type of installation. (We highly recommend using Docker for setting up and running this project. Docker provides a consistent and isolated environment, making it easier to manage dependencies and ensure a smooth installation process.)
>### 5.1. Docker based Installation
>> [!NOTE]
>> **Before You Begin:** Make sure you have Docker & Docker compose installed on your system.

1. Clone the project repository from GitHub: `git clone https://github.com/iiph-gandhinagar/Ni-kshay-SETU-Admin.git`
2. Duplicate the `env.example` file in the project directory, renaming it as `.env`, and customize environment variables, including database credentials as shown below.
    ```DB_HOST=db
    DB_PORT=3306
    DB_DATABASE=ns_db
    DB_USERNAME=ns_user
    DB_PASSWORD='My-secret@123'
3. Change admin crendentials in the file `./database/migrations/2021_06_16_072255_fill_default_admin_user_and_permissions.php` at `'email' => 'admin@company.com'` & `protected $password = 'iiph-digiflux@123';`
4. In the project root directory, run : `docker compose build app`. This command will build the backend application and make a docker image.
5. Run `docker compose up -d`.
6. Run `docker compose exec app composer install`
7. Run `docker compose exec app php artisan key:generate`
8. Run `docker compose exec app php artisan migrate --seed`
7. Open the application in your browser: `http://localhost:8000`

>### 5.2. Regular Installation


1. Clone the project repository from GitHub: `git clone https://github.com/iiph-gandhinagar/Ni-kshay-SETU-Admin.git`
2. Install all the dependencies using composer: `composer install`
3. Duplicate the `env.example` file in the project directory and rename it as `.env`. Change the env varialbes accordingly
4. Generate a new application key: `php artisan key:generate`
5. Change admin crendentials in the file `./database/migrations/2021_06_16_072255_fill_default_admin_user_and_permissions.php` at `'email' => 'admin@company.com'` & `protected $password = 'iiph-digiflux@123';`
5. Run the database migrations with seeder: `php artisan migrate:fresh --seed`
7. Open the application in your browser: `http://localhost:8000`

## 6. Configuration

The application requires certain configuration settings to work correctly. The main configuration file is `.env`. Update the following settings based on your environment:

-   `SECRET_KEY`: A unique secret key for your application.
-   `DEBUG`: Set to `True` for development and `False` for production.
-   `DATABASES`: Configure the MYSQL database settings.
-   `MAIL`: Configure the MAIL settings.
-   `AWS`: Configure the AWS settings.
-   `FIREBASE_CREDENTIALS`: Select the email backend for sending notifications.
-   `IS_SMS_ENABLED`: Enable the sending of SMS notifications by setting the flag to `True`.
-   `SMS_API_KEY_PROMOTION_WITH_NAME`: Select the SMS backend for sending notifications.
-   `OTP_SMS_HEADER`: Set OTP SMS Header.
-   `APP_URL`: http://localhost:8000.
-   `TRAINING_URL`: http://127.0.0.1. This serves as a machine learning-based training endpoint for Chatbot.
-   `TRAINING_URL_TIMEOUT`: 4
-   `GOOGLE_RECAPTCHA_SECRET`: Choose the Google reCAPTCHA code for implementing authentication.
-   `FORNTEND_URL`: Set Front-end URL for Automatic Notification.
-   `BACKEND_URL`: Set Back-end URL for sending notifications.
-   `QUEUE_CONNECTION`: Configure the database to enable queuing functionality.

## 7. Usage

1. Login with the credentials defined as per section 5.1 or 5.2
2. Add other admin/supervisor users in Management > Manage Access section
3. Setup your master data in Master Tables section
4. Start using Materials, Assesment Creation, Learn Case Findings, Patient Management etc. modules
5. If you have subscriber app configured, you may see different reports in Reports section

## 8. Contribution Guidelines

Contributions to Ni-kshay Setu are welcome. If you would like to contribute, please follow these guidelines:

1. Fork the repository.
2. Create a new branch for your feature or bug fix: `git checkout -b feature/your-feature-name`
3. Make your changes and test thoroughly.
4. Commit your changes: `git commit -m (Format---> feat|fix|docs|style|perf|test : feature or bug fixing description)

"Add your commit message"`

5. Push to the branch: `git push origin feature/your-feature-name`
6. Create a pull request on the main repository.
7. Provide a clear description of your changes in the pull request.
8. Ensure your code follows the project's coding conventions and style guidelines.
9. Be open to feedback and iterate on your work if requested.

## 9. License

Ni-kshay Setu project is licensed under the [GNU General Public License, Version 3.0](https://www.gnu.org/licenses/gpl-3.0).

![Static Badge](https://img.shields.io/badge/Licence-GPL%203.0-blue)
