# minimalCMS

## User Stories
- I as a reader want to view content on the website
- I as an admin/writer want to login/logout on the website
- I as an admin/writer want to add/modify/delete content on the website
  - A simple page
  - A blog entry
- I as an admin want to add/modify/delete a writer/admin to the site (V2)
- I as an admin want to add/modify/delete menu items (V2)
- I as an admin want to reorder menu items
- I as an admin want to install the website
  - Create initial admin user
  - Set up initial database
  
## Models
- User
  - Name
  - Last name
  - Login
  - Password
  - Session ID
  - Session expiration time
  - Role
- Content
  - Title
  - Content
  - Type (Page/Blogentry)
  - Timestamp modified
- Menu entry
  - Label
  - Target (page)
  
## Controllers
- Usercontroller (V2)
  - CRUD
  - Login
  - Logout
  - Get by session ID
- Pagecontroller
  - CRUD
- Blogentrycontroller
  - CRUD

## Services
- Menuentryservice
  - Show
  - Save order
- Permissionservice
  - Is allowed to ...
- Databaseservice
- Factory
  - Get service XYZ
  
## Views
- All blogentries
- Single blogentry/page
- Login
- Dashboard
- Page/blogentry
  - List
  - Create
  - Edit
  - Delete
  
## Page sections
- Header
  - Page name
  - Menu
- Content
- Footer
  
  
  