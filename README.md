# CommPeak Task Code

## Technical Note: Symfony Application with Docker  

### Architecture  
The application consists of two Docker containers:  
1. **Application Container** – Runs PHP with Symfony, based on the official Symfony-recommended Docker setup. The Symfony code was used inside container.  
2. **Database Container** – Runs MySQL as the data storage backend.  

### Frontend Functionality  
- The frontend periodically checks for new data every **60 seconds**.  
- If new data is available, the table displaying call statistics is updated.  

### Backend API  
The backend exposes two API endpoints:  
1. **Data Endpoint** – Returns new calls statistical data in JSON format.  
2. **CSV Import Endpoint** – Recieve new call data as a CSV file.  

## Data Validation and Processing  
- The application validates only the format of uploaded CSV files:  
  - Must be **comma-separated**.  
  - Must contain **exactly 5 fields per row**.  
- No additional validation is performed:  
  - **Duplicate records** are allowed.  
  - **Field type validation** is enforced (e.g., integer, string), but no business logic validation is applied.  
  - No checks are implemented for data correctness beyond structural validation, as this is considered **out of scope** for the task.  

## Phone Number Continent Mapping  
- The first **three digits** of the customer's phone number are used to determine the continent.  
- It is acknowledged that country codes vary in length (**1–4+ digits**), but a **three-digit approach** was chosen for this implementation.  

## Performance Optimization  
- The application utilizes **caching** to reduce:  
  - **Database queries**.  
  - **Third-party API requests**.  


## 📝 Technologies
- PHP 8.3
- Symfony 7.2
- Cache
- MySQL 8.4.4
- Bootstrap
- Jquery 3.x
- Docker

### Clone the Repository:
```
 git clone git@github.com:andr435/commpeak_code.git
```
or
```
 git clone https://github.com/andr435/commpeak_code.git .
```

### 🚀 Usage
Run the docker compose file:
```
docker compose up -d
```
The site will be available on [https://localhost](https://localhost)


## 🔧 Prerequisites
-  Docker installed and running

## 👤 Author
Created by [Andrey Mussatov aka andr435](https://github.com/andr435).
