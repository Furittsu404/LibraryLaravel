# CLSU-LISO Attendance Management System (AMS)

## System Features Documentation for SRS Generation

---

## 1. SYSTEM OVERVIEW

### 1.1 Project Information

- **System Name**: CLSU-LISO AMS (Attendance Management System)
- **Organization**: Central Luzon State University - Library and Information Services Office (CLSU-LISO)
- **Purpose**: Comprehensive library attendance tracking, user management, and room reservation system
- **Architecture**: Laravel 12.x MVC with Livewire full-stack components
- **Migration Status**: Upgraded from legacy plain PHP system with complete data preservation

### 1.2 System Objectives

1. Track library visitor attendance across multiple library sections
2. Manage user accounts (students, faculty, staff, visitors)
3. Generate comprehensive reports and analytics
4. Facilitate room reservation management
5. Provide real-time statistics and insights
6. Ensure secure access control and data management

---

## 2. USER ROLES AND ACCESS LEVELS

### 2.1 Public Users (Unauthenticated)

- Library visitors (students, faculty, staff, general users)
- Access to:
    - Barcode scanner for attendance logging
    - Student registration page
    - Room reservation booking page

### 2.2 Administrators (Authenticated)

- Library staff and administrators
- Full system access including:
    - Dashboard and analytics
    - User account management
    - Archive management
    - Report generation and export
    - Login history monitoring
    - System settings configuration
    - Room reservation management

---

## 3. CORE FUNCTIONAL MODULES

### 3.1 BARCODE SCANNER & ATTENDANCE SYSTEM

#### 3.1.1 Overview

Public-facing attendance logging system using barcode/ID scanning across multiple library sections.

#### 3.1.2 Supported Library Sections

1. **Entrance** (Main entry point)
2. **Exit** (Checkout point)
3. **Periodicals** (Magazines/journals section)
4. **Humanities** (Humanities collection)
5. **Multimedia** (Digital resources)
6. **Filipiniana** (Philippine collection)
7. **Maker Space** (Creative workspace)
8. **Science & Technology** (Science collection)

#### 3.1.3 Key Features

- **Real-time Scanning**: Instant barcode/ID card scanning
- **Section-Specific Tracking**: Separate login/logout per library section
- **Login Flow**:
    - User scans barcode at any section
    - System validates user account status
    - Records login time and section
    - Updates user status to "inside"
    - Displays welcome message with user info
- **Logout Flow**:
    - User scans at section or at Exit
    - System records logout time
    - Updates user status to "outside"
    - Supports multi-section logout at Exit
- **Section Switching**: Administrators can change active scanner section with password verification

#### 3.1.4 Validation Rules

- **Account Active**: User account must have "active" status
- **Not Expired**: User expiration date must be in the future
- **Not Archived**: User must not be in archive
- **No Duplicate Login**: User cannot login twice in same section simultaneously
- **Exit Validation**: User must be logged in at Entrance before logging out at Exit

#### 3.1.5 Real-time Activity Feed

- Live display of recent logins/logouts
- Shows user name, course, and login time
- Status indicator (Active/Exited)
- Active user count per section
- Auto-refreshes every 5 seconds

#### 3.1.6 Scanner Section Security

- Admin password protection for section changes
- Session-based section persistence
- Password verification API endpoint

---

### 3.2 STUDENT REGISTRATION SYSTEM

#### 3.2.1 Overview

Public self-registration system for new library users.

#### 3.2.2 Registration Form Fields

**Personal Information:**

- Last Name (required)
- First Name (required)
- Middle Name (optional)
- Sex/Gender (required)
- Address (optional)
- Email Address (optional)
- Phone Number (optional)

**Academic Information:**

- User Type (required): Student, Faculty, Staff, Visitor
- Course/Department (required for students)
- Section (optional)

**Account Information:**

- Barcode/Student ID (required, unique)
- Account Status: Automatically set to "active"
- Expiration Date: Set based on system default

#### 3.2.3 Validation Rules

- Barcode must be unique (not exist in Users or Archive)
- Email must be unique if provided
- All required fields must be completed
- Phone number format validation
- Proper data type validation

#### 3.2.4 Post-Registration

- Account created immediately
- User can scan barcode to enter library
- Welcome notification displayed

---

### 3.3 ROOM RESERVATION SYSTEM

#### 3.3.1 Overview

Public room booking system for library study rooms and discussion spaces.

#### 3.3.2 Room Management

**Room Properties:**

- Room name/number
- Capacity (number of people)
- Available facilities/amenities
- Availability status (enabled/disabled)

#### 3.3.3 Reservation Features

**For Students (Public):**

- View available rooms
- Check time slot availability
- Book reservations with details:
    - Room selection
    - Date selection
    - Time slot selection (hourly blocks)
    - Purpose of reservation
    - Number of participants
    - Participant names (comma-separated)
    - Participant IDs/barcodes
- Real-time slot availability checking

**Operating Hours:**

- Daily: 8:00 AM to 5:00 PM
- Hourly time blocks (60-minute intervals)
- No reservations outside operating hours

**Reservation Status:**

- **Pending**: Awaiting admin approval
- **Approved**: Confirmed reservation
- **Cancelled**: Cancelled by admin or system

#### 3.3.4 Admin Reservation Management

- View all reservations in calendar view
- Approve/reject reservation requests
- Edit reservation details
- Cancel reservations
- Block time slots (maintenance, events)
- Unblock previously blocked slots
- Filter by date, room, status

#### 3.3.5 Time Slot Blocking

- Administrators can block specific time slots
- Blocked slots unavailable for booking
- Used for maintenance, special events, closures
- Reason field for blocking

#### 3.3.6 Conflict Prevention

- System prevents double-booking
- Automatic overlap detection
- Real-time availability updates
- Visual calendar interface

---

### 3.4 ADMIN DASHBOARD

#### 3.4.1 Overview

Comprehensive analytics and monitoring dashboard for administrators.

#### 3.4.2 Real-time Statistics Cards

1. **Total Users**: Total registered accounts
2. **Active Users**: Currently inside library
3. **Today's Logins**: Login count for current day
4. **Total Visits**: Cumulative attendance records

#### 3.4.3 Data Visualizations

**Course Distribution Chart:**

- Pie chart showing user distribution by course/department
- Interactive tooltips with percentages
- Color-coded segments

**Gender Demographics:**

- Visual representation of male/female/other distribution
- Percentage calculations
- Comparative bar chart

**Attendance Timeline Graph:**

- Line/bar chart showing daily attendance over time
- Configurable time ranges:
    - Last 7 days
    - Last 30 days
    - Last 3 months
    - Last 6 months
    - Last year
- Trend analysis capability

#### 3.4.4 Live Updates

- Statistics refresh every 10 seconds via Alpine.js polling
- No page refresh required
- Real-time data accuracy

#### 3.4.5 User Interface Features

- Dark mode toggle (persistent via session)
- Responsive design for all screen sizes
- Quick action buttons
- Section indicator display

---

### 3.5 ACCOUNT MANAGEMENT SYSTEM

#### 3.5.1 Overview

Comprehensive CRUD operations for library user accounts.

#### 3.5.2 User Account Fields

**Personal Information:**

- ID (auto-generated)
- Last Name
- First Name
- Middle Name
- Sex/Gender
- Address
- Email
- Phone Number

**Academic/Professional:**

- User Type (Student/Faculty/Staff/Visitor)
- Course/Department
- Section
- Barcode/ID Number (unique identifier)

**Account Status:**

- Account Status (Active/Inactive)
- User Status (Inside/Outside - current location)
- Expiration Date
- Created Date
- Updated Date

#### 3.5.3 Account Operations

**Create New Account:**

- Manual single account creation
- Full form with validation
- Duplicate barcode detection
- Automatic timestamp generation

**Edit Existing Account:**

- Inline editing or modal form
- All fields editable except ID
- Validation on update
- Change tracking

**Delete Account:**

- Soft delete (moves to Archive)
- Confirmation dialog required
- Preserves all historical data
- Can be restored from Archive

**Bulk Import:**

- CSV/Excel file upload
- Column mapping support
- Batch validation
- Error reporting for invalid records
- Rollback on critical errors
- Import summary report

**Bulk Expiration Update:**

- Set expiration date for ALL active accounts
- Date picker interface
- Confirmation required
- Affects all users simultaneously
- Useful for semester/year transitions

#### 3.5.4 Search and Filtering

- Real-time search across all fields
- Filter by:
    - User Type
    - Account Status
    - Gender
    - Course
    - Expiration status
- Sorting by any column
- Pagination controls

#### 3.5.5 Account Status Management

- Toggle Active/Inactive status
- Inactive accounts cannot login
- Status indicator badges
- Bulk status updates

#### 3.5.6 Expiration Management

- Individual expiration date setting
- Bulk expiration date updates
- Automatic expiration checking at login
- Expired accounts cannot scan in
- Visual expiration warnings

---

### 3.6 ARCHIVE MANAGEMENT SYSTEM

#### 3.6.1 Overview

Manages archived (soft-deleted) user accounts with full data preservation.

#### 3.6.2 Archive Features

**Archiving Process:**

- User account moved to Archive table
- All personal data preserved
- Historical attendance data retained
- Account marked as archived
- Cannot login while archived

**Archive Operations:**

1. **View Archived Users**
    - List all archived accounts
    - Search and filter capabilities
    - View full account details

2. **Edit Archived User**
    - Modify archived user information
    - Update personal details
    - Change account properties

3. **Restore User**
    - Move user back to active Users table
    - Automatically set account_status to "active"
    - All data restored
    - User can immediately login
    - Attendance history preserved

4. **Permanent Delete**
    - Completely remove from database
    - Confirmation required
    - IRREVERSIBLE operation
    - Should be used cautiously

5. **Bulk Delete**
    - Select multiple archived users
    - Permanently delete in batch
    - Confirmation prompt
    - Cannot be undone

#### 3.6.3 Archive Table Structure

- Identical to Users table structure
- Separate table for data isolation
- Same fields and relationships
- Preserves all metadata

#### 3.6.4 Use Cases

- **Graduated Students**: Archive when graduating, restore if returning
- **Expired Accounts**: Archive inactive accounts
- **Data Cleanup**: Move old records to archive
- **Account Investigation**: Temporarily archive during issues
- **GDPR Compliance**: Archived users can be permanently deleted

---

### 3.7 REPORTS & ANALYTICS

#### 3.7.1 Overview

Comprehensive reporting system with export capabilities.

#### 3.7.2 Report Types

**Attendance Reports:**

- Date range selection
- Filter by:
    - Library section
    - User type
    - Course/Department
    - Individual user
    - Date range
- Includes:
    - Login timestamps
    - Logout timestamps
    - Duration of stay
    - User demographics
    - Section visited

**User Reports:**

- Active users listing
- Inactive users listing
- Expired accounts
- New registrations (by date)
- User demographics summary

**Statistical Reports:**

- Daily attendance summaries
- Weekly trends
- Monthly comparisons
- Peak hours analysis
- Section usage statistics
- Course distribution
- Gender distribution

#### 3.7.3 Export Formats

**PDF Export:**

- Formatted for printing
- Professional layout
- Headers and footers
- Page numbers
- CLSU-LISO branding
- Table formatting
- Date/time stamps

**Excel/CSV Export:**

- Raw data export
- All fields included
- Suitable for further analysis
- Compatible with Excel, Google Sheets
- UTF-8 encoding
- Proper column headers

#### 3.7.4 Report Generation Features

- Customizable date ranges
- Real-time generation
- Preview before export
- Download to local machine
- Print directly from browser (PDF)

#### 3.7.5 Report Content

- Summary statistics
- Detailed records table
- Graphical representations
- Filtering metadata
- Generation timestamp
- Generated by (admin username)

---

### 3.8 LOGIN HISTORY TRACKING

#### 3.8.1 Overview

Comprehensive attendance history viewer for monitoring and auditing.

#### 3.8.2 History Features

**Display Information:**

- User ID and Name
- Course/Department
- User Type
- Library Section
- Login Time (timestamp)
- Logout Time (timestamp)
- Duration (calculated)
- Status (Inside/Outside)
- Date

**Filtering Options:**

- Date range picker
- Section filter
- User type filter
- Status filter (Inside/Outside)
- User search
- Course filter

**Sorting:**

- Sort by any column
- Ascending/descending
- Multi-column sorting

**Pagination:**

- Configurable page size
- Page navigation
- Total record count

#### 3.8.3 History Data Management

- Read-only view (no editing)
- Real-time updates
- Historical data preservation
- Unlimited retention period

#### 3.8.4 Use Cases

- Audit user attendance
- Verify login/logout times
- Track section usage
- Monitor unusual patterns
- Generate individual user history
- Compliance reporting

---

### 3.9 SETTINGS & CONFIGURATION

#### 3.9.1 Overview

System-wide configuration and settings management.

#### 3.9.2 Configurable Settings

**1. Default Expiration Date**

- Set default expiration for new accounts
- Date picker interface
- Applied to all new registrations
- Can be overridden per user

**2. Automatic Logout Time**

- Configure auto-logout duration
- Time in hours
- Applied to users who forgot to logout
- Runs as scheduled task/cron job
- Default: 24 hours

**3. Admin Account Management**

- Update admin username
- Change admin password
- Password confirmation required
- Secure password hashing (bcrypt)

**4. Scanner Section Password**

- Set password for section switching
- Separate from admin password
- Used by scanner interface
- Prevents unauthorized section changes

**5. Library Operating Hours**

- Configure operating times (future feature)
- Set weekly schedules
- Holiday closures

**6. Room Booking Settings**

- Maximum booking duration
- Advance booking limit
- Cancellation policy
- Minimum booking notice

#### 3.9.3 Settings Storage

- Key-value pair storage
- Database-backed
- Settings model with get/set methods
- Default values fallback

#### 3.9.4 Settings Security

- Only accessible to admins
- Audit logging of changes
- Validation on all updates
- Critical settings require confirmation

---

## 4. TECHNICAL SPECIFICATIONS

### 4.1 Technology Stack

**Backend Framework:**

- Laravel 12.x
- PHP 8.4+
- MySQL/MariaDB database

**Frontend Technologies:**

- Livewire 4.x (full-stack reactivity)
- Alpine.js 3.x (JavaScript interactivity)
- Tailwind CSS 3.x (styling)
- Chart.js (data visualization)
- Vite (asset bundling)

**Development Tools:**

- Composer (PHP dependencies)
- NPM (JavaScript dependencies)
- Laravel Pint (code formatting)

### 4.2 Database Architecture

**Core Tables:**

1. **users** - Active library users
2. **archives** - Archived users
3. **admins** - Administrator accounts
4. **attendance** - Login/logout records
5. **settings** - System configuration
6. **rooms** - Library rooms
7. **room_reservations** - Booking records
8. **blocked_time_slots** - Room availability blocks
9. **sections** - Library sections
10. **admin_history** - Admin activity log

**Key Relationships:**

- Users → Attendance (One-to-Many)
- Users → Room Reservations (One-to-Many)
- Rooms → Room Reservations (One-to-Many)
- Rooms → Blocked Time Slots (One-to-Many)

### 4.3 Security Features

**Authentication:**

- Session-based admin authentication
- Password hashing (bcrypt)
- CSRF protection on all forms
- Middleware-based route protection

**Authorization:**

- Role-based access control
- Route middleware (`auth:admin`)
- Section selection middleware
- Public/private route separation

**Data Protection:**

- SQL injection prevention (Eloquent ORM)
- XSS protection (Blade templating)
- Input validation and sanitization
- Secure password storage

**Session Management:**

- Secure session configuration
- Session timeout
- Remember me functionality
- Session regeneration on login

### 4.4 Performance Optimizations

**Database:**

- Indexed columns (barcode, user_id, dates)
- Query optimization with eager loading
- Pagination for large datasets
- Database connection pooling

**Frontend:**

- Asset minification and compression
- Lazy loading
- Client-side caching
- CDN for static assets

**Caching:**

- Query result caching
- Settings caching
- View caching
- Route caching

### 4.5 API Endpoints

**Public APIs:**

- `POST /scanner/scan` - Process barcode scan
- `GET /scanner/today-logins` - Get recent activity
- `POST /scanner/set-section` - Change scanner section
- `POST /api/verify-admin-password` - Verify scanner password
- `GET /reservations/available-slots` - Get room availability
- `POST /reservations/create` - Create reservation

**Admin APIs:**

- `GET /admin/stats.json` - Dashboard statistics
- `GET /dashboard/{timeframe}` - Chart data
- Various CRUD endpoints for resources

---

## 5. USER INTERFACE/EXPERIENCE

### 5.1 Design Principles

- **Simplicity**: Clean, intuitive interfaces
- **Responsiveness**: Works on all device sizes
- **Accessibility**: WCAG compliance considerations
- **Consistency**: Uniform design language
- **Performance**: Fast load times and interactions

### 5.2 Public-Facing Pages

**Scanner Interface:**

- Large, centered barcode input field
- Real-time feedback on scans
- Visual status messages (success/error/warning)
- Recent activity feed sidebar
- Section indicator
- Registration and reservation links
- Sound feedback on scan events

**Registration Page:**

- Multi-step or single-page form
- Clear field labels and placeholders
- Inline validation feedback
- Progress indicators
- Success confirmation

**Room Reservation Page:**

- Room selection cards with images
- Interactive calendar/grid view
- Time slot availability indicators
- Booking form modal
- Confirmation screen

### 5.3 Admin Interface

**Dashboard:**

- Card-based statistics layout
- Interactive charts
- Dark mode support
- Quick action buttons
- Navigation sidebar
- Section indicator

**Data Tables:**

- Sortable columns
- Pagination controls
- Search and filter tools
- Bulk action checkboxes
- Action buttons (Edit/Delete/View)
- Export buttons

**Forms:**

- Clear labels and validation
- Required field indicators
- Error message display
- Success notifications
- Modal dialogs for confirmations

### 5.4 Responsive Design

- Mobile-friendly layouts
- Touch-optimized controls
- Hamburger menu for mobile
- Adaptive data tables
- Bottom navigation on small screens

### 5.5 Notifications & Feedback

- Toast notifications
- Alert dialogs
- Loading spinners
- Success/error messages
- Confirmation modals
- Progress indicators

---

## 6. DATA FLOW DIAGRAMS

### 6.1 Attendance Logging Flow

```
1. User presents barcode at scanner
2. Scanner sends barcode to backend
3. System validates user account:
   - Check if user exists (Users table)
   - Check if archived (Archives table)
   - Verify account is active
   - Check expiration date
4. Check existing attendance for today:
   - If logged in → Process logout
   - If not logged in → Process login
5. Create/Update attendance record
6. Update user status (inside/outside)
7. Return success/error response
8. Display message to user
9. Update activity feed
```

### 6.2 Room Reservation Flow

```
1. User selects room and date
2. System fetches available slots:
   - Query existing reservations
   - Check blocked time slots
   - Calculate availability
3. User selects time slot
4. User fills reservation form
5. System validates:
   - No overlapping reservations
   - Valid time range
   - Room availability
   - User information
6. Create reservation with "pending" status
7. Admin reviews reservation
8. Admin approves/rejects
9. User notified of decision
10. Approved reservation blocks time slot
```

### 6.3 Account Management Flow

```
CREATE:
1. Admin fills new user form
2. Validate input (unique barcode)
3. Set default expiration date
4. Insert into Users table
5. Display success message

EDIT:
1. Admin clicks edit button
2. Load user data into form
3. Admin modifies fields
4. Validate changes
5. Update Users table
6. Display success message

DELETE (Archive):
1. Admin clicks delete button
2. Confirmation dialog appears
3. Admin confirms
4. Move record from Users to Archives
5. Preserve attendance history
6. Display success message

RESTORE:
1. Admin views archived users
2. Admin clicks restore
3. Confirmation dialog
4. Move record from Archives to Users
5. Set account_status to "active"
6. Display success message
```

---

## 7. BUSINESS RULES & VALIDATION

### 7.1 Account Rules

- Barcode must be unique across Users and Archives
- Email must be unique if provided
- Account status must be "active" to login
- Expired accounts cannot login
- Archived accounts cannot login
- Phone numbers must be in valid format

### 7.2 Attendance Rules

- User cannot login twice in same section
- User must login at Entrance before using Exit
- Exit logout clears all section logins
- Auto-logout after configured hours (default 24)
- Login/logout times must be chronological
- One active session per section per user

### 7.3 Reservation Rules

- Operating hours: 8:00 AM - 5:00 PM
- Minimum booking duration: 1 hour
- Cannot book past dates
- Cannot overlap existing approved reservations
- Cannot book blocked time slots
- Participant count must not exceed room capacity
- Reservations require admin approval

### 7.4 Data Retention

- Attendance records: Permanent retention
- Archived users: Permanent until manually deleted
- Admin history: Permanent retention
- Reservation history: Permanent retention
- Reports: Generated on-demand (not stored)

---

## 8. INTEGRATION POINTS

### 8.1 Barcode Scanner Hardware

- Compatible with USB barcode scanners
- Standard keyboard emulation mode
- Supports various barcode formats
- Real-time input processing

### 8.2 Export File Formats

- PDF generation library (DomPDF/Snappy)
- Excel generation (Maatwebsite Laravel Excel)
- CSV export (built-in Laravel)

### 8.3 Email System (Optional Future Feature)

- Laravel Mail for notifications
- Reservation confirmations
- Account expiration reminders
- Password reset emails

### 8.4 Reporting Tools

- Chart.js for data visualization
- Export libraries for file generation
- Print-friendly CSS for PDF generation

---

## 9. ERROR HANDLING & EDGE CASES

### 9.1 Scanner Error Scenarios

- **Barcode not found**: Display registration prompt
- **Account archived**: Show reactivation message
- **Account inactive**: Display contact librarian message
- **Account expired**: Show renewal required message
- **Already logged in**: Show logout first message
- **Exit without login**: Show login required message

### 9.2 Reservation Error Scenarios

- **Time slot taken**: Refresh availability, suggest alternatives
- **Booking conflict**: Prevent submission, show error
- **Past date selection**: Disable past dates on calendar
- **Room unavailable**: Gray out, show unavailable status
- **Form validation errors**: Inline error messages

### 9.3 Data Integrity

- Transaction rollback on failures
- Foreign key constraints
- Unique constraints on barcodes/emails
- Referential integrity preservation
- Cascade handling for related records

### 9.4 System Failures

- Database connection error handling
- Graceful degradation
- Error logging
- User-friendly error messages
- Admin notification on critical errors

---

## 10. FUTURE ENHANCEMENTS (Roadmap)

### 10.1 Planned Features

1. **Email Notifications**
    - Reservation confirmations
    - Expiration reminders
    - Account status changes

2. **Mobile Application**
    - iOS/Android apps
    - Push notifications
    - Mobile check-in

3. **Self-Service Kiosk**
    - Touchscreen interface
    - QR code scanning
    - Instant registration

4. **Advanced Analytics**
    - Machine learning predictions
    - Peak time forecasting
    - Usage pattern analysis

5. **Student Portal**
    - View own attendance history
    - Manage reservations
    - Update profile

6. **Library Resource Management**
    - Book catalog integration
    - Borrowing system
    - Fine/penalty management

7. **Access Control Integration**
    - Physical door locks
    - Turnstile gates
    - RFID card support

8. **Visitor Management**
    - Visitor registration
    - Temporary passes
    - Escort requirements

### 10.2 Technical Improvements

- API development for mobile apps
- Real-time websocket updates
- Advanced caching strategies
- Load balancing capability
- Multi-language support
- Offline mode support

---

## 11. SYSTEM CONSTRAINTS & LIMITATIONS

### 11.1 Current Limitations

- Single admin account (can be extended)
- Manual approval for all reservations
- Limited to physical library locations
- No integration with existing university systems
- No automated email notifications
- Manual barcode scanner section switching

### 11.2 Technical Constraints

- Requires continuous internet connection
- Browser-based access only (no native apps)
- Barcode scanner hardware required
- Server hosting requirements
- Database size growth over time

### 11.3 Operational Constraints

- Staff training required
- Scanner maintenance needed
- Regular data backups essential
- Admin monitoring required
- Manual conflict resolution

---

## 12. TESTING & QUALITY ASSURANCE

### 12.1 Testing Approach

- Unit tests for models and business logic
- Feature tests for critical workflows
- Browser testing for UI/UX
- Manual testing of scanner hardware
- Load testing for peak usage
- Security vulnerability testing

### 12.2 Quality Metrics

- System uptime: 99.5%+
- Page load time: < 2 seconds
- Scanner response time: < 1 second
- Database query time: < 200ms
- Error rate: < 0.1%

---

## 13. DEPLOYMENT & MAINTENANCE

### 13.1 Deployment Requirements

- PHP 8.4+ server
- MySQL/MariaDB database
- Web server (Apache/Nginx)
- SSL certificate (HTTPS)
- Sufficient storage for data growth
- Regular backup system

### 13.2 Maintenance Tasks

- Daily database backups
- Weekly system health checks
- Monthly security updates
- Quarterly performance optimization
- Annual data archiving
- Log rotation and cleanup

### 13.3 Support & Documentation

- User manuals for administrators
- Scanner operation guide
- Troubleshooting documentation
- FAQ section
- Contact information for support

---

## 14. COMPLIANCE & STANDARDS

### 14.1 Data Privacy

- GDPR considerations for personal data
- Data retention policies
- User consent for data collection
- Right to data deletion (permanent archive delete)
- Data access controls

### 14.2 Security Standards

- OWASP security best practices
- Regular security audits
- Vulnerability patching
- Access logging
- Secure password policies

### 14.3 Accessibility

- WCAG 2.1 Level AA compliance goals
- Keyboard navigation support
- Screen reader compatibility
- Color contrast requirements
- Alternative text for images

---

## 15. GLOSSARY OF TERMS

- **Attendance**: Record of user login/logout in library
- **Archive**: Soft-deleted user accounts with preserved data
- **Barcode**: Unique identifier (Student ID) scanned for attendance
- **Section**: Physical area or collection within the library
- **Active User**: User currently inside the library (logged in)
- **Expiration Date**: Date when user account access expires
- **Room Reservation**: Booking of library study/discussion room
- **Time Slot**: One-hour block of time for reservations
- **Blocked Slot**: Time period unavailable for booking
- **User Status**: Current location status (Inside/Outside)
- **Account Status**: Account active/inactive state
- **User Type**: Category of user (Student/Faculty/Staff/Visitor)

---

## APPENDIX: TECHNICAL NOTES FOR SRS GENERATION

### Data Points for Requirements Specification

**Functional Requirements should cover:**

1. All modules listed in Section 3 (3.1 through 3.9)
2. User interactions and system responses
3. Data validation rules from Section 7
4. Business logic and workflows from Section 6
5. Integration requirements from Section 8

**Non-Functional Requirements should address:**

1. Performance metrics from Section 12.2
2. Security features from Section 4.3
3. Scalability considerations
4. Reliability and availability targets
5. Maintainability requirements

**System Architecture should detail:**

1. Technology stack from Section 4.1
2. Database design from Section 4.2
3. API structure from Section 4.5
4. Security architecture from Section 4.3

**User Interface Requirements should specify:**

1. UI principles from Section 5.1
2. Page layouts from Section 5.2 and 5.3
3. Responsive design requirements from Section 5.4
4. Accessibility requirements from Section 14.3

**Testing Requirements should include:**

1. Testing strategies from Section 12.1
2. Quality metrics from Section 12.2
3. Validation scenarios from Section 9

This documentation provides comprehensive system information for generating a complete Software Requirements Specification (SRS) document. All features are production-ready and currently implemented in the CLSU-LISO AMS system.
