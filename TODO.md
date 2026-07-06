# TODO - Admin CRUD Completion

## Step 1: Audit + Plan Lock
- [x] Review Admin controllers: Service/Category/Photo/Booking/Contact/Settings
- [x] Review schema + seed + routes
- [x] Confirm deletion behavior (Soft delete)


## Step 2: Core CRUD Helpers (if needed)
- [ ] Check base layout flash output and i18n vars

## Step 3: Implement real DB CRUD per controller
- [x] ServiceController: index/create/edit/store/update/delete (services)
- [ ] CategoryController: index/create/edit/store/update/delete (categories)
- [x] BookingController: index/update (bookings + service join)

- [ ] ContactController: index/markAsRead/delete (contacts)
- [ ] SettingsController: index/update (settings)
- [ ] PhotoController: index/upload/store/delete (photos + filesystem)

## Step 4: UI fixes
- [ ] Remove hardcoded rows/values in Admin index/edit views
- [ ] Use dynamic delete/update forms with correct IDs and CSRF

## Step 5: Testing
- [ ] Run basic local checks: login as admin, CRUD flows
- [ ] Ensure CSRF works for POST routes

