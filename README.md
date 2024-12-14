
# JSON Deduplication Program (PHP)

This program deduplicates JSON records based on specific rules, ensuring the most recent and relevant data is retained. It also generates a log of all changes made during the deduplication process.

---

## Demo Video

[![Demo Video](https://img.youtube.com/vi/NUaqfZWKgLc/0.jpg)](https://youtu.be/NUaqfZWKgLc?feature=shared)

Click the image above to watch the demo.

---

## Features
- Deduplicates records based on `_id` and `email`.
- Prioritizes records with the latest `entryDate`.
- Logs all field updates, including the old and new values, for traceability.
- Outputs:
  - A deduplicated JSON file (`deduplicated_leads.json`).
  - A change log file (`change_log.json`).

---

## Requirements
- **PHP**: Version 7.x or above.
- **JSON File**: Input file should be in JSON format and follow the required structure.

---

## Usage
### **1. Clone the repository**
```bash
git clone <repository-link>
cd <repository-folder>
```

### **2. Add your input file**
- Place the input JSON file in the same directory and name it `leads.json`. The file should have a structure like:
```json
{
    "leads": [
        {
            "_id": "1",
            "email": "user1@example.com",
            "firstName": "John",
            "lastName": "Smith",
            "address": "123 Street",
            "entryDate": "2024-12-01T12:00:00+00:00"
        },
        {
            "_id": "1",
            "email": "user1@example.com",
            "firstName": "John",
            "lastName": "Doe",
            "address": "456 Street",
            "entryDate": "2024-12-02T12:00:00+00:00"
        }
    ]
}
```

### **3. Run the program**
```bash
php main.php
```

---

## Outputs
### **1. `deduplicated_leads.json`**
- Contains the deduplicated records.
- Example:
  ```json
  {
      "leads": [
          {
              "_id": "1",
              "email": "user1@example.com",
              "firstName": "John",
              "lastName": "Doe",
              "address": "456 Street",
              "entryDate": "2024-12-02T12:00:00+00:00"
          }
      ]
  }
  ```

### **2. `change_log.json`**
- Logs changes made during deduplication.
- Example:
  ```json
  [
      {
          "field": "lastName",
          "from": "Smith",
          "to": "Doe"
      },
      {
          "field": "address",
          "from": "123 Street",
          "to": "456 Street"
      },
      {
          "field": "entryDate",
          "from": "2024-12-01T12:00:00+00:00",
          "to": "2024-12-02T12:00:00+00:00"
      }
  ]
  ```

---

## Explanation
### **Deduplication Rules**
- Records with the same `_id` or `email` are considered duplicates.
- The record with the most recent `entryDate` is prioritized.
- If `entryDate` values are identical, the last record in the input list is retained.

### **Logs**
- Tracks changes for each updated field, showing:
  - `field`: The name of the modified field.
  - `from`: The original value.
  - `to`: The updated value.

---

## Customization
- To change input/output file paths, modify the file paths in the `main()` function in `main.php`.
