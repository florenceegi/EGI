# STEP 2.7 - MANUAL TESTING CHECKLIST

## PA/Enterprise Universal Architecture Validation

**Package**: FlorenceEGI\Testing  
**Author**: Padmin D. Curtis (AI Partner OS3.0)  
**Version**: 1.0.0 (FlorenceEGI - PA/Enterprise Testing)  
**Date**: 2025-10-05  
**Purpose**: Comprehensive manual testing checklist for STEP 2 validation

---

## 📋 OVERVIEW

Testing scope: Validate all STEP 2 implementations before moving to STEP 3.

**Systems to test:**

1. ✅ Service Layer Foundation (EgiService, ViewService)
2. ✅ PA Heritage Views (index, show, create, edit)
3. ✅ AuthRedirectService (login/registration redirects)
4. ✅ ViewService Routing (PA vs Creator view switching)
5. ✅ EgiService Isolation (role-based data filtering)
6. ✅ Authorization System (Spatie permissions + ownership)
7. ⚠️ WCAG 2.1 AA Compliance (accessibility)
8. ⚠️ ULM Audit Logs (GDPR tracking)

---

## 🧪 TEST SUITE 1: AUTHENTICATION & REDIRECTS

### 1.1 AuthRedirectService - Login Redirects

**PA Entity Login:**

-   [ ] Login with PA entity user credentials
-   [ ] **Expected**: Redirect to `/pa/dashboard`
-   [ ] **Verify**: Dashboard displays PA-specific KPIs
-   [ ] **Verify**: Sidebar shows PA navigation items

**Creator Login:**

-   [ ] Login with Creator user credentials
-   [ ] **Expected**: Redirect to `/home` (Creator dashboard)
-   [ ] **Verify**: Dashboard displays Creator-specific content
-   [ ] **Verify**: Sidebar shows Creator navigation items

**Inspector Login:**

-   [ ] Login with Inspector user credentials
-   [ ] **Expected**: Redirect to assigned collections view
-   [ ] **Verify**: Read-only access to heritage items

**Test Result**: ⬜ PASS | ⬜ FAIL | ⬜ BLOCKED

---

### 1.2 AuthRedirectService - Registration Redirects

**PA Entity Registration:**

-   [ ] Register new PA entity user
-   [ ] Complete profile setup
-   [ ] **Expected**: Redirect to `/pa/dashboard` after verification
-   [ ] **Verify**: PA role assigned correctly

**Creator Registration:**

-   [ ] Register new Creator user
-   [ ] Complete profile setup
-   [ ] **Expected**: Redirect to `/home` after verification
-   [ ] **Verify**: Creator role assigned correctly

**Test Result**: ⬜ PASS | ⬜ FAIL | ⬜ BLOCKED

---

## 🧪 TEST SUITE 2: PA HERITAGE CRUD WORKFLOW

### 2.1 PA Heritage List (Index)

**Access:**

-   [ ] Navigate to `/egis` as PA entity
-   [ ] **Expected**: ViewService routes to `egis.pa.index` view
-   [ ] **Verify**: PA brand colors visible (#1B365D, #D4A574, #2D5016)
-   [ ] **Verify**: Terminology "Bene Culturale" (not "Opera")

**Data Isolation:**

-   [ ] Verify only PA-owned collection items visible
-   [ ] **Expected**: EgiService filters by `collection.owner_id = user.id`
-   [ ] **Verify**: Creator-owned items NOT visible
-   [ ] **Verify**: Pagination works correctly (no hidden limits)

**Filters:**

-   [ ] Test search filter (title, artist, description)
-   [ ] Test CoA status filter (verified, pending, no_coa)
-   [ ] Test published status filter
-   [ ] **Verify**: All filters work correctly

**Test Result**: ⬜ PASS | ⬜ FAIL | ⬜ BLOCKED

---

### 2.2 PA Heritage Detail (Show)

**Access:**

-   [ ] Click heritage item from PA index
-   [ ] **Expected**: ViewService routes to `egis.pa.show` view
-   [ ] **Verify**: PA brand design applied
-   [ ] **Verify**: CoA badge visible if CoA exists

**Features:**

-   [ ] Verify image display (main + gallery)
-   [ ] Verify metadata display (title, artist, year, description)
-   [ ] Verify CoA section (if CoA issued)
-   [ ] Verify blockchain verification link (if anchored)
-   [ ] Verify file downloads (PDF, original file)

**Authorization:**

-   [ ] **Verify**: PA can only view own collection items
-   [ ] **Verify**: 403 error if accessing other PA's items

**Test Result**: ⬜ PASS | ⬜ FAIL | ⬜ BLOCKED

---

### 2.3 PA Heritage Create

**Access:**

-   [ ] Navigate to `/egis/create` as PA entity
-   [ ] **Expected**: ViewService routes to `egis.pa.create` view
-   [ ] **Verify**: PA brand design applied
-   [ ] **Verify**: Form shows "Carica Bene Culturale" title

**Form Validation:**

-   [ ] Test title field (required)
-   [ ] Test artist field (required)
-   [ ] Test description field (required)
-   [ ] Test collection selector (shows only PA-owned collections)
-   [ ] Test image upload (file type, size validation)
-   [ ] **Verify**: Proper error messages on validation failure

**Submit:**

-   [ ] Fill all required fields
-   [ ] Upload valid image
-   [ ] Submit form
-   [ ] **Expected**: Heritage item created successfully
-   [ ] **Expected**: Redirect to heritage detail view
-   [ ] **Verify**: Success message displayed
-   [ ] **Verify**: Data saved correctly in database

**Authorization:**

-   [ ] **Verify**: Spatie permission `create_EGI` checked
-   [ ] **Verify**: Non-authenticated users redirected to login

**Test Result**: ⬜ PASS | ⬜ FAIL | ⬜ BLOCKED

---

### 2.4 PA Heritage Edit

**Access:**

-   [ ] Navigate to heritage detail page
-   [ ] Click "Modifica" button
-   [ ] **Expected**: ViewService routes to `egis.pa.edit` view
-   [ ] **Verify**: PA brand design applied
-   [ ] **Verify**: Form pre-filled with existing data

**Authorization:**

-   [ ] **Verify**: EgiService.canManageEgi() checks ownership
-   [ ] **Verify**: PA can edit items in owned collections
-   [ ] **Verify**: PA CANNOT edit items in other collections
-   [ ] **Verify**: 403 error if unauthorized

**Form Features:**

-   [ ] Test title edit
-   [ ] Test artist edit
-   [ ] Test description edit
-   [ ] Test collection change (only owned collections)
-   [ ] Test image replace
-   [ ] **Verify**: CoA badge shown if CoA exists
-   [ ] **Verify**: Cannot edit if CoA locked fields

**Submit:**

-   [ ] Modify fields
-   [ ] Submit form
-   [ ] **Expected**: Heritage item updated successfully
-   [ ] **Expected**: Redirect to heritage detail view
-   [ ] **Verify**: Success message displayed
-   [ ] **Verify**: Changes saved correctly

**Test Result**: ⬜ PASS | ⬜ FAIL | ⬜ BLOCKED

---

## 🧪 TEST SUITE 3: CREATOR HERITAGE ISOLATION

### 3.1 Creator Heritage List

**Access:**

-   [ ] Navigate to `/egis` as Creator
-   [ ] **Expected**: ViewService routes to `egis.index` (default Creator view)
-   [ ] **Verify**: Creator brand design (different from PA)
-   [ ] **Verify**: Terminology "Opera" (not "Bene Culturale")

**Data Isolation:**

-   [ ] **Verify**: Only Creator-owned items visible (user_id match)
-   [ ] **Verify**: PA-owned items NOT visible
-   [ ] **Verify**: Other Creators' items NOT visible

**Test Result**: ⬜ PASS | ⬜ FAIL | ⬜ BLOCKED

---

### 3.2 Creator Heritage Create/Edit

**Access:**

-   [ ] Navigate to `/egis/create` as Creator
-   [ ] **Expected**: Default Creator view (not PA view)
-   [ ] **Verify**: Creator terminology and branding

**Authorization:**

-   [ ] **Verify**: Creator can create own EGIs
-   [ ] **Verify**: Creator can edit own EGIs
-   [ ] **Verify**: Creator CANNOT edit PA EGIs
-   [ ] **Verify**: Creator CANNOT access PA collections

**Test Result**: ⬜ PASS | ⬜ FAIL | ⬜ BLOCKED

---

## 🧪 TEST SUITE 4: VIEW SERVICE ROUTING

### 4.1 PA View Routing

**Test Cases:**

-   [ ] PA entity accesses `/egis` → `egis.pa.index`
-   [ ] PA entity accesses `/egis/create` → `egis.pa.create`
-   [ ] PA entity accesses `/egis/{egi}/edit` → `egis.pa.edit`
-   [ ] PA entity accesses `/egis/{egi}` → `egis.pa.show`

**Verification Method:**
Check `storage/logs/laravel.log` for ViewService log entries:

```
VIEW_SERVICE_RESOLVED: Role-specific view found
user_id: [PA user ID]
role: pa_entity
base_view: index
resolved_view: egis.pa.index
```

**Test Result**: ⬜ PASS | ⬜ FAIL | ⬜ BLOCKED

---

### 4.2 Creator View Routing

**Test Cases:**

-   [ ] Creator accesses `/egis` → `egis.index`
-   [ ] Creator accesses `/egis/create` → `egis.create` (fallback if pa.create doesn't exist for creator)
-   [ ] Creator accesses `/egis/{egi}/edit` → `egis.edit`
-   [ ] Creator accesses `/egis/{egi}` → `egis.show`

**Verification Method:**
Check logs for Creator view resolution.

**Test Result**: ⬜ PASS | ⬜ FAIL | ⬜ BLOCKED

---

## 🧪 TEST SUITE 5: EGI SERVICE ISOLATION

### 5.1 Role-Based Filtering

**PA Entity Filter (applyPAFilters):**

```sql
-- Expected query
SELECT * FROM egis
WHERE EXISTS (
    SELECT * FROM collections
    WHERE collections.id = egis.collection_id
    AND collections.owner_id = [PA user ID]
    AND collections.type = 'artwork'
)
```

**Test:**

-   [ ] PA entity queries `/egis`
-   [ ] **Verify**: Only items in PA-owned collections returned
-   [ ] **Verify**: Query uses `whereHas('collection')` with owner_id filter

**Creator Filter (applyCreatorFilters):**

```sql
-- Expected query
SELECT * FROM egis WHERE user_id = [Creator user ID]
```

**Test:**

-   [ ] Creator queries `/egis`
-   [ ] **Verify**: Only items created by Creator returned
-   [ ] **Verify**: Query uses `where('user_id', user.id)`

**Test Result**: ⬜ PASS | ⬜ FAIL | ⬜ BLOCKED

---

### 5.2 Ownership Authorization (canManageEgi)

**Creator Ownership:**

-   [ ] Creator tries to edit own EGI
-   [ ] **Expected**: `canManageEgi()` returns TRUE (user_id match)
-   [ ] **Expected**: Edit page accessible

**Collection Ownership (PA):**

-   [ ] PA entity tries to edit EGI in owned collection
-   [ ] **Expected**: `canManageEgi()` returns TRUE (collection owner check)
-   [ ] **Expected**: Edit page accessible

**Unauthorized Access:**

-   [ ] PA entity tries to edit EGI in OTHER PA's collection
-   [ ] **Expected**: `canManageEgi()` returns FALSE
-   [ ] **Expected**: 403 error or redirect with error message

-   [ ] Creator tries to edit OTHER Creator's EGI
-   [ ] **Expected**: `canManageEgi()` returns FALSE
-   [ ] **Expected**: 403 error

**Test Result**: ⬜ PASS | ⬜ FAIL | ⬜ BLOCKED

---

## 🧪 TEST SUITE 6: SPATIE PERMISSIONS

### 6.1 EGI CRUD Permissions

**Create Permission:**

-   [ ] User with `create_EGI` permission can access `/egis/create`
-   [ ] User WITHOUT permission gets 403 error
-   [ ] Controller checks: `$user->can('create_EGI')`

**Update Permission:**

-   [ ] User with `update_EGI` permission can edit EGI (if owner)
-   [ ] User WITHOUT permission gets 403 error
-   [ ] Controller checks: `$user->can('update_EGI')`

**Delete Permission:**

-   [ ] User with `delete_EGI` permission can delete EGI (if owner)
-   [ ] User WITHOUT permission gets 403 error
-   [ ] Controller checks: `$user->can('delete_EGI')`

**Test Result**: ⬜ PASS | ⬜ FAIL | ⬜ BLOCKED

---

### 6.2 PA-Specific Permissions

**Check permissions table:**

```sql
SELECT name FROM permissions
WHERE name LIKE 'access_pa%' OR name LIKE '%heritage%';
```

**Expected permissions:**

-   [ ] `access_pa_dashboard` - Access PA dashboard
-   [ ] `manage_heritage` - Manage heritage items
-   [ ] `issue_coa` - Issue CoA certificates
-   [ ] `assign_inspector` - Assign inspectors to collections

**Route Protection:**

-   [ ] Routes in `routes/pa-enterprise.php` use `middleware(['auth'])`
-   [ ] Controllers check appropriate permissions
-   [ ] Unauthorized users redirected with error message

**Test Result**: ⬜ PASS | ⬜ FAIL | ⬜ BLOCKED

---

## 🧪 TEST SUITE 7: WCAG 2.1 AA COMPLIANCE

### 7.1 Keyboard Navigation

**PA Views:**

-   [ ] Tab through all interactive elements
-   [ ] **Verify**: Focus visible on all elements
-   [ ] **Verify**: Tab order logical (top to bottom, left to right)
-   [ ] **Verify**: Can submit form using Enter key
-   [ ] **Verify**: Can close modals using Escape key

**Test Result**: ⬜ PASS | ⬜ FAIL | ⬜ BLOCKED

---

### 7.2 Screen Reader Compatibility

**ARIA Labels:**

-   [ ] Form inputs have proper `<label>` or `aria-label`
-   [ ] Buttons have descriptive text or `aria-label`
-   [ ] Images have `alt` text
-   [ ] Icons have `aria-hidden="true"` or `aria-label`

**Semantic HTML:**

-   [ ] Headings hierarchy correct (h1 → h2 → h3)
-   [ ] Forms use `<form>`, `<fieldset>`, `<legend>` properly
-   [ ] Lists use `<ul>`, `<ol>`, `<li>`
-   [ ] Navigation uses `<nav>` element

**Test Result**: ⬜ PASS | ⬜ FAIL | ⬜ BLOCKED

---

### 7.3 Color Contrast

**PA Brand Colors:**

-   [ ] Text on #1B365D background: contrast ratio ≥ 4.5:1
-   [ ] Text on #D4A574 background: contrast ratio ≥ 4.5:1
-   [ ] Link colors: contrast ratio ≥ 4.5:1
-   [ ] Focus indicators: contrast ratio ≥ 3:1

**Test Tool**: WebAIM Contrast Checker (https://webaim.org/resources/contrastchecker/)

**Test Result**: ⬜ PASS | ⬜ FAIL | ⬜ BLOCKED

---

### 7.4 Responsive Design

**Test Viewports:**

-   [ ] Mobile (375px width)
-   [ ] Tablet (768px width)
-   [ ] Desktop (1920px width)

**Verify:**

-   [ ] Text readable at all sizes
-   [ ] Buttons tappable (min 44x44px touch target)
-   [ ] Forms usable on mobile
-   [ ] No horizontal scroll
-   [ ] Images scale properly

**Test Result**: ⬜ PASS | ⬜ FAIL | ⬜ BLOCKED

---

## 🧪 TEST SUITE 8: ULM AUDIT LOGS

### 8.1 PA Heritage Access Logs

**Scenario: PA views heritage item**

-   [ ] PA entity navigates to heritage detail page
-   [ ] **Expected**: ULM log entry created

**Check log:**

```bash
tail -f storage/logs/laravel.log | grep "EGI_SERVICE"
```

**Expected entry:**

```
[timestamp] local.INFO: EGI_SERVICE_INDEX: EGI list queried
{"user_id":123,"role":"pa_entity","filters":[],"results_count":15,"total":42}
```

**Test Result**: ⬜ PASS | ⬜ FAIL | ⬜ BLOCKED

---

### 8.2 View Service Resolution Logs

**Scenario: View routing decision**

-   [ ] User accesses `/egis`
-   [ ] **Expected**: ViewService logs resolution

**Expected entries:**

```
local.INFO: VIEW_SERVICE_RESOLVED: Role-specific view found
{"user_id":123,"role":"pa_entity","base_view":"index","resolved_view":"egis.pa.index"}
```

**Test Result**: ⬜ PASS | ⬜ FAIL | ⬜ BLOCKED

---

### 8.3 Authorization Failure Logs

**Scenario: Unauthorized access attempt**

-   [ ] PA entity tries to edit another PA's heritage item
-   [ ] **Expected**: EgiService logs denial

**Expected entry:**

```
local.ERROR: EgiService::canManageEgi error
{"user_id":123,"egi_id":456,"error":"Unauthorized"}
```

**Test Result**: ⬜ PASS | ⬜ FAIL | ⬜ BLOCKED

---

## 🧪 TEST SUITE 9: ERROR HANDLING

### 9.1 UEM Error Manager Integration

**Test Cases:**

-   [ ] Submit invalid form data (triggers UEM)
-   [ ] Access non-existent heritage item (404)
-   [ ] Database connection error (500)

**Verify:**

-   [ ] Error messages user-friendly (not technical)
-   [ ] Error codes logged to ULM
-   [ ] Toast notifications display correctly
-   [ ] User redirected appropriately

**Test Result**: ⬜ PASS | ⬜ FAIL | ⬜ BLOCKED

---

### 9.2 GDPR Consent Checks

**PA Data Modification:**

-   [ ] PA entity tries to edit heritage metadata
-   [ ] **Expected**: ConsentService checks for `allow-personal-data-processing`
-   [ ] **Expected**: If no consent, redirect with error

**Audit Trail:**

-   [ ] Successful edit logged to AuditLogService
-   [ ] Log includes: user_id, action, timestamp, fields_changed
-   [ ] Category: `GdprActivityCategory::PERSONAL_DATA_UPDATE`

**Test Result**: ⬜ PASS | ⬜ FAIL | ⬜ BLOCKED

---

## 📊 SUMMARY CHECKLIST

### Code Verification (Automated) ✅

-   [x] **CRUD Routes**: All routes defined in `routes/web.php` and `routes/pa-enterprise.php`
-   [x] **Controller Methods**: EgiController has index, create, store, show, edit, update, destroy
-   [x] **PA Views**: create.blade.php, edit.blade.php, index.blade.php, show.blade.php exist
-   [x] **PA Brand**: Colors #1B365D, #D4A574, #2D5016 applied in views
-   [x] **Terminology**: "Bene Culturale" used in PA views (not "Opera")
-   [x] **AuthRedirectService**: Integrated in AuthenticatedSessionController and RegisteredUserController
-   [x] **ViewService**: getViewForRole() method properly routes by role
-   [x] **EgiService Isolation**: applyPAFilters(), applyCreatorFilters() methods exist
-   [x] **Authorization**: canManageEgi() checks ownership, Spatie permission checks in controllers

### Manual Testing Required ⚠️

-   [ ] **Login Redirects**: PA → dashboard, Creator → home
-   [ ] **Registration Redirects**: PA → dashboard, Creator → home
-   [ ] **PA Heritage List**: Data isolation, filters, pagination
-   [ ] **PA Heritage Detail**: CoA display, blockchain verification
-   [ ] **PA Heritage Create**: Form validation, success flow
-   [ ] **PA Heritage Edit**: Authorization, form pre-fill, update flow
-   [ ] **Creator Isolation**: Cannot see PA items, cannot edit PA collections
-   [ ] **View Service Routing**: Correct views rendered by role
-   [ ] **EgiService Filtering**: Role-based queries work correctly
-   [ ] **Spatie Permissions**: CRUD permissions enforced
-   [ ] **WCAG 2.1 AA**: Keyboard navigation, screen readers, contrast, responsive
-   [ ] **ULM Audit Logs**: Access, resolution, authorization logs created
-   [ ] **Error Handling**: UEM integration, user-friendly messages
-   [ ] **GDPR Compliance**: Consent checks, audit trail

---

## 📝 TEST EXECUTION NOTES

**Tester**: ********\_\_\_********  
**Date**: ********\_\_\_********  
**Environment**: [ ] Local | [ ] Staging | [ ] Production  
**Browser**: [ ] Chrome | [ ] Firefox | [ ] Safari | [ ] Edge

**Issues Found**:

```
Issue #1: [Description]
Severity: [ ] Critical | [ ] High | [ ] Medium | [ ] Low
Reproducible: [ ] Always | [ ] Sometimes | [ ] Once

Issue #2: [Description]
...
```

**Overall Result**:

-   [ ] ✅ ALL TESTS PASSED - Ready for STEP 3
-   [ ] ⚠️ MINOR ISSUES - Fix and retest
-   [ ] ❌ CRITICAL ISSUES - BLOCKED

---

## 🚀 NEXT STEPS

**If ALL TESTS PASS:**

1. Mark STEP 2.7 as ✅ COMPLETED
2. Update TODO_MASTER status
3. Commit test results documentation
4. Proceed to STEP 3: Final Testing & Validation

**If ISSUES FOUND:**

1. Document all issues in GitHub Issues or project tracker
2. Prioritize by severity (Critical → High → Medium → Low)
3. Fix issues in order of priority
4. Re-run affected test suites
5. Update test results

---

**Generated by**: Padmin D. Curtis OS3.0  
**Validation Date**: 2025-10-05  
**FlorenceEGI Version**: 2.0.0 (Universal Architecture)
