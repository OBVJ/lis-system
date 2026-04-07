# LIS Test Scenarios & Demo Walkthrough

Welcome to the LIS (Laboratory Information System) Demo Mode! The system has been freshly seeded with realistic data, including 20 patients, 50 diverse lab requests, and historical insights. Use the configurations below to test and evaluate the entire platform.

---

## 🔐 1. Accessing the System
You can authenticate using any of the pre-configured accounts:
- **Admin Role:** `admin@lis.com` / `123456`
  *(Full access to everything, including settings, user management, and audit logs)*
- **Doctor Role:** `doctor@lis.com` / `123456`
  *(Clinical capabilities: patients, requests, results, reporting)*

---

## 🧪 2. Test Scenario: Patient Registration
**Objective:** Confirm smooth patient onboarding and duplicate-prevention.
1. Log in.
2. Navigate to **Patients** -> **Add Patient**.
3. Create a new patient: 
   - Fill in standard fields (Name, Age, Gender).
   - *Notice:* Assigned/Referring doctors and Patient Type dynamically pull from the `Settings` architecture.
4. Hit **Save**.
5. Expected behavior: You should be redirected back with a `PT-XXXX-NNNN` auto-generated ID, with no duplicate constraint collisions.

---

## 📋 3. Test Scenario: Lab Request Flow
**Objective:** Confirm correct billing calculation and relational saving.
1. Navigate to **Requests** -> **Create Request**.
2. Select your newly created patient in step 1.
3. Select multiple tests (e.g., CBC, Fasting Glucose, HbA1c). 
   - *Notice:* The total price perfectly calculates in real-time.
4. Submit the request.
5. In the **Requests** list, find your new request. By clicking it, verify that all **Workflows** are set to `Pending` and progress steps are visible.
6. Try changing the request status incrementally: *Pending → Sample Collected → In Progress*.

---

## 🔬 4. Test Scenario: Result Entry & Flags
**Objective:** Ensure clinical intelligence accurately interprets high/low thresholds.
1. Go to the **Lab Results** module. Search for requests marked `In Progress`.
2. Click **Enter Results**.
3. Input realistic numbers for your selected tests.
   - Example (Glucose): Enter `250` (expecting High Flag).
   - Example (CBC): Enter `15` (expecting Normal Flag).
4. Save the results.
5. Expected behavior: The system autonomously injects the clinical thresholds `High` or `Low` and deducts inventory supplies auto-linked to the test. Furthermore, the master request status flips to `Completed`.

---

## 🖨️ 5. Test Scenario: Report Generation
**Objective:** Confirm professional, hospital-grade PDF export mapping.
1. Navigate to **Requests** and locate any `Completed` request.
2. Click **Print Report**.
3. The PDF should generate properly tracking the referring doctor digitally, mapping the Patient information, laying out the tests with highlighted `Normal Ranges` vs `Actual Results`, and embedding dynamic Clinical Insights for any `High`/`Low` anomalies.
4. *Verify:* System Audit logs (`Admin -> Audit Logs`) will dynamically catalog that a Request/Patient dataset was touched in prior actions.
