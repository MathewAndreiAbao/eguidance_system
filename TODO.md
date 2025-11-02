# LavaLust System Refactoring to MVC + ORM Standards

## Overview
Refactor the current LavaLust PHP system to strictly follow LavaLust MVC + ORM + Auth patterns, ensuring clean code structure, ORM-based models, secure controllers, and reusable views.

## Tasks

### Models Refactoring
- [x] Refactor AppointmentModel.php to use ORM methods (all, find, insert, update, delete)
- [x] Refactor ProfileModel.php to use ORM methods
- [x] Refactor UserModel.php to use ORM methods

### Controllers Refactoring
- [x] Update AppointmentController.php to use proper loading and methods
- [x] Update AuthController.php to use proper loading and methods
- [x] Update ProfileController.php to use proper loading and methods

### Libraries Refactoring
- [x] Update Auth.php library to use ORM and include required methods

### Configuration
- [x] Update routes.php to match prompt requirements exactly

### Views Updates
- [ ] Check and update views for html_escape() and site_url() usage

## Implementation Details
- Models must extend Model class and use ORM-style methods
- Controllers must use $this->call->model(), $this->call->library(), filter_io(), $this->call->view(), redirect()
- Auth library must use ORM for registration/login
- All routes must match the prompt specification
- Views must use html_escape() and site_url()

## Testing
- [ ] Test user authentication (register, login, logout)
- [ ] Test appointments CRUD operations
- [ ] Test profile management (view, edit, change password)
- [ ] Verify security implementations
