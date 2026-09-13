-- Philippine HRMS Seed Data
-- NOTE: All government ID values are SAMPLE/TEST DATA only.

SET FOREIGN_KEY_CHECKS = 0;

-- ROLES
INSERT INTO roles (id, name, display_name, description) VALUES
(1, 'super_admin',   'Super Admin',   'Full system access'),
(2, 'hr_admin',      'HR Admin',      'HR module access'),
(3, 'payroll_admin', 'Payroll Admin', 'Payroll module access'),
(4, 'manager',       'Manager',       'Team management access'),
(5, 'employee',      'Employee',      'Self-service access');

-- PERMISSIONS
INSERT INTO permissions (name, module, description) VALUES
('employees.view','employees','View employees'),('employees.create','employees','Create employees'),
('employees.edit','employees','Edit employees'),('employees.delete','employees','Delete employees'),
('departments.manage','departments','Manage departments'),
('positions.manage','positions','Manage positions'),
('branches.manage','branches','Manage branches'),
('attendance.view','attendance','View attendance'),('attendance.manage','attendance','Manage attendance'),
('leave.view','leave','View leave'),('leave.approve','leave','Approve leave'),('leave.manage','leave','Manage leave'),
('holidays.manage','holidays','Manage holidays'),
('payroll.view','payroll','View payroll'),('payroll.process','payroll','Process payroll'),('payroll.approve','payroll','Approve/lock payroll'),
('recruitment.manage','recruitment','Manage recruitment'),
('training.manage','training','Manage training'),
('performance.manage','performance','Manage performance'),
('assets.manage','assets','Manage assets'),
('separation.manage','separation','Manage separation'),
('reports.view','reports','View reports'),
('users.manage','users','Manage users'),
('settings.manage','settings','Manage settings'),
('audit.view','audit','View audit logs');

-- SUPER ADMIN: all permissions
INSERT INTO role_permissions (role_id, permission_id)
SELECT 1, id FROM permissions;

-- HR ADMIN
INSERT INTO role_permissions (role_id, permission_id)
SELECT 2, id FROM permissions WHERE module IN ('employees','departments','positions','attendance','leave','holidays','recruitment','training','performance','reports');

-- PAYROLL ADMIN
INSERT INTO role_permissions (role_id, permission_id)
SELECT 3, id FROM permissions WHERE module IN ('payroll','reports');

-- MANAGER
INSERT INTO role_permissions (role_id, permission_id)
SELECT 4, id FROM permissions WHERE name IN ('employees.view','attendance.view','attendance.manage','leave.view','leave.approve','performance.manage','reports.view');

-- EMPLOYEE
INSERT INTO role_permissions (role_id, permission_id)
SELECT 5, id FROM permissions WHERE name IN ('attendance.view','leave.view','payroll.view');

-- BRANCHES
INSERT INTO branches (id, code, name, address) VALUES
(1,'HQ','Head Office','123 Ayala Avenue, Makati City'),
(2,'CEB','Cebu Branch','45 Colon Street, Cebu City');

-- DEPARTMENTS
INSERT INTO departments (id, code, name) VALUES
(1,'HR','Human Resources'),
(2,'IT','Information Technology'),
(3,'FIN','Finance'),
(4,'ACC','Accounting'),
(5,'OPS','Operations'),
(6,'ADM','Administration'),
(7,'SLS','Sales');

-- POSITIONS
INSERT INTO positions (id, code, name, department_id, salary_min, salary_max) VALUES
(1,'HR-OFF','HR Officer',1,25000,40000),
(2,'IT-STF','IT Staff',2,22000,35000),
(3,'DEV','Software Developer',2,30000,60000),
(4,'ACC-STF','Accountant',4,28000,45000),
(5,'FIN-OFF','Finance Officer',3,28000,45000),
(6,'OPS-STF','Operations Staff',5,20000,30000),
(7,'ADM-ASST','Administrative Assistant',6,18000,26000),
(8,'SLS-REP','Sales Representative',7,20000,35000),
(9,'MGR','Department Manager',NULL,50000,90000);

-- EMPLOYMENT TYPES
INSERT INTO employment_types (id, name, description) VALUES
(1,'Regular','Regular full-time employee'),
(2,'Probationary','Probationary employee'),
(3,'Contractual','Fixed-term contract'),
(4,'Project-Based','Project-based engagement'),
(5,'Part-Time','Part-time employee'),
(6,'Casual','Casual employee');

-- =========================================================
-- EMPLOYEES (SAMPLE DATA)
-- =========================================================
INSERT INTO employees
(id, employee_code, first_name, middle_name, last_name, birth_date, gender, civil_status,
 email, mobile_number, address_line, city, province, postal_code,
 department_id, position_id, branch_id, employment_type_id,
 date_hired, employment_status, basic_salary, pay_frequency) VALUES
(1,'EMP-0001','Juan','Santos','Dela Cruz','1990-05-12','Male','Married','juan.delacruz@example.com','09171234567','12 Mabini St.','Makati','Metro Manila','1200',2,3,1,1,'2020-01-15','Regular',35000.00,'Semi-Monthly'),
(2,'EMP-0002','Maria','Reyes','Santos','1992-08-22','Female','Single','maria.santos@example.com','09181234567','45 Rizal Ave.','Quezon City','Metro Manila','1100',1,1,1,1,'2019-03-01','Regular',32000.00,'Semi-Monthly'),
(3,'EMP-0003','Pedro','Lopez','Reyes','1988-11-30','Male','Married','pedro.reyes@example.com','09191234567','78 Bonifacio St.','Pasig','Metro Manila','1600',4,4,1,1,'2018-06-10','Regular',38000.00,'Semi-Monthly'),
(4,'EMP-0004','Ana','Cruz','Villanueva','1995-02-14','Female','Single','ana.villanueva@example.com','09201234567','22 Katipunan Ave.','Marikina','Metro Manila','1800',2,2,1,1,'2021-09-01','Regular',25000.00,'Semi-Monthly'),
(5,'EMP-0005','Jose','Ramos','Bautista','1985-07-19','Male','Married','jose.bautista@example.com','09211234567','101 Ortigas Center','Mandaluyong','Metro Manila','1550',3,5,1,1,'2017-02-20','Regular',42000.00,'Semi-Monthly'),
(6,'EMP-0006','Liza','Garcia','Mendoza','1993-12-05','Female','Single','liza.mendoza@example.com','09221234567','55 Shaw Blvd.','Mandaluyong','Metro Manila','1550',6,7,1,1,'2022-01-10','Regular',21000.00,'Semi-Monthly'),
(7,'EMP-0007','Mark','Tan','Lim','1991-04-27','Male','Single','mark.lim@example.com','09231234567','88 Session Road','Baguio','Benguet','2600',7,8,1,1,'2021-04-15','Regular',24000.00,'Semi-Monthly'),
(8,'EMP-0008','Grace','Uy','Tan','1994-09-18','Female','Married','grace.tan@example.com','09241234567','14 Colon St.','Cebu City','Cebu','6000',5,6,2,1,'2020-11-03','Regular',23000.00,'Semi-Monthly'),
(9,'EMP-0009','Ramon','Castro','Aquino','1987-06-08','Male','Married','ramon.aquino@example.com','09251234567','33 Aguinaldo Hwy','Dasmariñas','Cavite','4114',5,6,1,1,'2016-08-22','Regular',26000.00,'Semi-Monthly'),
(10,'EMP-0010','Sofia','Bautista','Navarro','1996-03-11','Female','Single','sofia.navarro@example.com','09261234567','77 Marcos Hwy','Antipolo','Rizal','1870',2,3,1,2,'2024-06-01','Probationary',30000.00,'Semi-Monthly');

-- Set a supervisor (HR manager as dept head example)
UPDATE employees SET supervisor_id = 2 WHERE id IN (1,4,10);
UPDATE employees SET supervisor_id = 5 WHERE id IN (3,6);

-- GOVERNMENT IDs (SAMPLE ONLY)
INSERT INTO employee_government_ids (employee_id, sss_number, philhealth_number, pagibig_number, tin_number) VALUES
(1,'34-1234567-8','12-345678901-2','1210-1234-5678','123-456-789-000'),
(2,'34-2345678-9','12-456789012-3','1210-2345-6789','234-567-890-000'),
(3,'34-3456789-0','12-567890123-4','1210-3456-7890','345-678-901-000'),
(4,'34-4567890-1','12-678901234-5','1210-4567-8901','456-789-012-000'),
(5,'34-5678901-2','12-789012345-6','1210-5678-9012','567-890-123-000'),
(6,'34-6789012-3','12-890123456-7','1210-6789-0123','678-901-234-000'),
(7,'34-7890123-4','12-901234567-8','1210-7890-1234','789-012-345-000'),
(8,'34-8901234-5','12-012345678-9','1210-8901-2345','890-123-456-000'),
(9,'34-9012345-6','12-123456789-0','1210-9012-3456','901-234-567-000'),
(10,'34-0123456-7','12-234567890-1','1210-0123-4567','012-345-678-000');

-- USERS
-- Password for all demo accounts: Password123!
-- Hash generated with password_hash('Password123!', PASSWORD_DEFAULT)
INSERT INTO users (id, employee_id, role_id, email, password_hash, full_name) VALUES
(1, NULL, 1, 'admin@example.com',    '$2y$10$e0NRzJ1Q5oQ7bF1sPbV2S.7u3kQ7WlqR5cK9D0V6j6bZ1Xh6qKqLu', 'System Administrator'),
(2, 2,   2, 'hr@example.com',         '$2y$10$e0NRzJ1Q5oQ7bF1sPbV2S.7u3kQ7WlqR5cK9D0V6j6bZ1Xh6qKqLu', 'Maria Santos'),
(3, 3,   3, 'payroll@example.com',    '$2y$10$e0NRzJ1Q5oQ7bF1sPbV2S.7u3kQ7WlqR5cK9D0V6j6bZ1Xh6qKqLu', 'Pedro Reyes'),
(4, 5,   4, 'manager@example.com',    '$2y$10$e0NRzJ1Q5oQ7bF1sPbV2S.7u3kQ7WlqR5cK9D0V6j6bZ1Xh6qKqLu', 'Jose Bautista'),
(5, 1,   5, 'employee@example.com',   '$2y$10$e0NRzJ1Q5oQ7bF1sPbV2S.7u3kQ7WlqR5cK9D0V6j6bZ1Xh6qKqLu', 'Juan Dela Cruz');

-- LEAVE TYPES
INSERT INTO leave_types (id, code, name, default_days, is_paid, requires_attachment) VALUES
(1,'VL','Vacation Leave',15,1,0),
(2,'SL','Sick Leave',15,1,0),
(3,'EL','Emergency Leave',3,1,0),
(4,'SIL','Service Incentive Leave',5,1,0),
(5,'ML','Maternity Leave',105,1,1),
(6,'PL','Paternity Leave',7,1,1),
(7,'SPL','Solo Parent Leave',7,1,1),
(8,'VAWC','VAWC Leave',10,1,1),
(9,'SLW','Special Leave for Women',60,1,1),
(10,'BL','Bereavement Leave',3,1,0);

-- LEAVE BALANCES (current year = 2026)
INSERT INTO leave_balances (employee_id, leave_type_id, year, entitled, used, balance) VALUES
(1,1,2026,15,2,13),(1,2,2026,15,0,15),
(2,1,2026,15,5,10),(2,2,2026,15,1,14),
(3,1,2026,15,3,12),(3,2,2026,15,2,13),
(4,1,2026,15,0,15),(4,2,2026,15,0,15),
(5,1,2026,15,0,15),(5,2,2026,15,0,15),
(6,1,2026,15,0,15),(6,2,2026,15,0,15),
(7,1,2026,15,0,15),(7,2,2026,15,0,15),
(8,1,2026,15,0,15),(8,2,2026,15,0,15),
(9,1,2026,15,0,15),(9,2,2026,15,0,15),
(10,1,2026,15,0,15),(10,2,2026,15,0,15);

-- HOLIDAYS (2026 sample - verify against official proclamations)
INSERT INTO holidays (name, holiday_date, holiday_type, description) VALUES
('New Year\'s Day','2026-01-01','Regular Holiday','Sample data'),
('Maundy Thursday','2026-04-02','Regular Holiday','Sample data'),
('Good Friday','2026-04-03','Regular Holiday','Sample data'),
('Araw ng Kagitingan','2026-04-09','Regular Holiday','Sample data'),
('Labor Day','2026-05-01','Regular Holiday','Sample data'),
('Independence Day','2026-06-12','Regular Holiday','Sample data'),
('National Heroes Day','2026-08-31','Regular Holiday','Sample data'),
('Bonifacio Day','2026-11-30','Regular Holiday','Sample data'),
('Christmas Day','2026-12-25','Regular Holiday','Sample data'),
('Rizal Day','2026-12-30','Regular Holiday','Sample data'),
('EDSA Anniversary','2026-02-25','Special Non-Working Holiday','Sample data'),
('Ninoy Aquino Day','2026-08-21','Special Non-Working Holiday','Sample data'),
('All Saints Day','2026-11-01','Special Non-Working Holiday','Sample data'),
('Feast of the Immaculate Conception','2026-12-08','Special Non-Working Holiday','Sample data'),
('New Year\'s Eve','2026-12-31','Special Non-Working Holiday','Sample data');

-- =========================================================
-- GOVERNMENT CONTRIBUTION TABLES (SAMPLE RATES — VERIFY OFFICIAL VALUES)
-- These are illustrative only and MUST be updated to match official rules.
-- =========================================================
-- SSS (simplified illustrative brackets)
INSERT INTO government_contribution_tables
(contribution_type, effective_from, salary_min, salary_max, employee_rate, employer_rate) VALUES
('SSS','2024-01-01',0,4249.99,0.0450,0.0950),
('SSS','2024-01-01',4250,4749.99,0.0450,0.0950),
('SSS','2024-01-01',4750,5249.99,0.0450,0.0950),
('SSS','2024-01-01',5250,5749.99,0.0450,0.0950),
('SSS','2024-01-01',5750,6249.99,0.0450,0.0950),
('SSS','2024-01-01',6250,6749.99,0.0450,0.0950),
('SSS','2024-01-01',6750,7249.99,0.0450,0.0950),
('SSS','2024-01-01',7250,7749.99,0.0450,0.0950),
('SSS','2024-01-01',7750,8249.99,0.0450,0.0950),
('SSS','2024-01-01',8250,8749.99,0.0450,0.0950),
('SSS','2024-01-01',8750,9249.99,0.0450,0.0950),
('SSS','2024-01-01',9250,9749.99,0.0450,0.0950),
('SSS','2024-01-01',9750,10249.99,0.0450,0.0950),
('SSS','2024-01-01',10250,10749.99,0.0450,0.0950),
('SSS','2024-01-01',10750,11249.99,0.0450,0.0950),
('SSS','2024-01-01',11250,11749.99,0.0450,0.0950),
('SSS','2024-01-01',11750,12249.99,0.0450,0.0950),
('SSS','2024-01-01',12250,12749.99,0.0450,0.0950),
('SSS','2024-01-01',12750,13249.99,0.0450,0.0950),
('SSS','2024-01-01',13250,13749.99,0.0450,0.0950),
('SSS','2024-01-01',13750,14249.99,0.0450,0.0950),
('SSS','2024-01-01',14250,14749.99,0.0450,0.0950),
('SSS','2024-01-01',14750,15249.99,0.0450,0.0950),
('SSS','2024-01-01',15250,15749.99,0.0450,0.0950),
('SSS','2024-01-01',15750,16249.99,0.0450,0.0950),
('SSS','2024-01-01',16250,16749.99,0.0450,0.0950),
('SSS','2024-01-01',16750,17249.99,0.0450,0.0950),
('SSS','2024-01-01',17250,17749.99,0.0450,0.0950),
('SSS','2024-01-01',17750,18249.99,0.0450,0.0950),
('SSS','2024-01-01',18250,18749.99,0.0450,0.0950),
('SSS','2024-01-01',18750,19249.99,0.0450,0.0950),
('SSS','2024-01-01',19250,19749.99,0.0450,0.0950),
('SSS','2024-01-01',19750,20249.99,0.0450,0.0950),
('SSS','2024-01-01',20250,20749.99,0.0450,0.0950),
('SSS','2024-01-01',20750,21249.99,0.0450,0.0950),
('SSS','2024-01-01',21250,21749.99,0.0450,0.0950),
('SSS','2024-01-01',21750,22249.99,0.0450,0.0950),
('SSS','2024-01-01',22250,22749.99,0.0450,0.0950),
('SSS','2024-01-01',22750,23249.99,0.0450,0.0950),
('SSS','2024-01-01',23250,23749.99,0.0450,0.0950),
('SSS','2024-01-01',23750,24249.99,0.0450,0.0950),
('SSS','2024-01-01',24250,24749.99,0.0450,0.0950),
('SSS','2024-01-01',24750,25249.99,0.0450,0.0950),
('SSS','2024-01-01',25250,25749.99,0.0450,0.0950),
('SSS','2024-01-01',25750,26249.99,0.0450,0.0950),
('SSS','2024-01-01',26250,26749.99,0.0450,0.0950),
('SSS','2024-01-01',26750,27249.99,0.0450,0.0950),
('SSS','2024-01-01',27250,27749.99,0.0450,0.0950),
('SSS','2024-01-01',27750,28249.99,0.0450,0.0950),
('SSS','2024-01-01',28250,28749.99,0.0450,0.0950),
('SSS','2024-01-01',28750,29249.99,0.0450,0.0950),
('SSS','2024-01-01',29250,29749.99,0.0450,0.0950),
('SSS','2024-01-01',29750,30249.99,0.0450,0.0950),
('SSS','2024-01-01',30250,30749.99,0.0450,0.0950),
('SSS','2024-01-01',30750,31249.99,0.0450,0.0950),
('SSS','2024-01-01',31250,31749.99,0.0450,0.0950),
('SSS','2024-01-01',31750,32249.99,0.0450,0.0950),
('SSS','2024-01-01',32250,32749.99,0.0450,0.0950),
('SSS','2024-01-01',32750,33249.99,0.0450,0.0950),
('SSS','2024-01-01',33250,33749.99,0.0450,0.0950),
('SSS','2024-01-01',33750,34249.99,0.0450,0.0950),
('SSS','2024-01-01',34250,34749.99,0.0450,0.0950);

-- PhilHealth (5% total, 2.5% employee + 2.5% employer, illustrative floor/ceiling)
INSERT INTO government_contribution_tables
(contribution_type, effective_from, salary_min, salary_max, employee_rate, employer_rate) VALUES
('PhilHealth','2024-01-01',0,9999999.99,0.0250,0.0250);

-- Pag-IBIG (2% employee, 2% employer, ceiling 100)
INSERT INTO government_contribution_tables
(contribution_type, effective_from, salary_min, salary_max, employee_rate, employer_rate, employee_fixed, employer_fixed) VALUES
('Pag-IBIG','2024-01-01',0,1499.99,0.0100,0.0200,0,0),
('Pag-IBIG','2024-01-01',1500,9999999.99,0.0200,0.0200,0,0);

-- BIR WITHHOLDING TAX (Semi-Monthly — ILLUSTRATIVE ONLY)
INSERT INTO tax_tables (tax_type, effective_from, income_min, income_max, base_tax, excess_rate, excess_over) VALUES
('Semi-Monthly','2023-01-01',0,10416.66,0,0,0),
('Semi-Monthly','2023-01-01',10416.67,16666.66,0,0.15,10416.66),
('Semi-Monthly','2023-01-01',16666.67,33333.32,937.50,0.20,16666.66),
('Semi-Monthly','2023-01-01',33333.33,83333.32,4270.83,0.25,33333.32),
('Semi-Monthly','2023-01-01',83333.33,333333.32,16770.83,0.30,83333.32),
('Semi-Monthly','2023-01-01',333333.33,9999999999.99,91770.83,0.35,333333.32);

-- SYSTEM SETTINGS
INSERT INTO system_settings (setting_key, setting_value, setting_group, description) VALUES
('company_name','Demo Philippine Corporation','company','Company name shown on payslips'),
('company_address','123 Ayala Avenue, Makati City, Metro Manila','company','Company address'),
('company_tin','000-000-000-000','company','Company TIN (sample)'),
('payroll_work_hours_per_day','8','payroll','Standard work hours per day'),
('payroll_work_days_per_month','22','payroll','Working days per month'),
('payroll_night_diff_rate','0.10','payroll','Night differential rate'),
('payroll_overtime_rate','1.25','payroll','Overtime multiplier'),
('login_max_attempts','5','security','Max failed login attempts before lockout'),
('login_lockout_minutes','15','security','Lockout duration in minutes');

SET FOREIGN_KEY_CHECKS = 1;