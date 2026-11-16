# Graph Report - app  (2026-11-16)

## Corpus Check
- 365 files · ~112,030 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 13 file(s) not represented in the graph (top: (none) 10, .zip 2, .css 1)

## Summary
- 1463 nodes · 2387 edges · 244 communities (31 shown, 213 thin omitted)
- Extraction: 91% EXTRACTED · 9% INFERRED · 0% AMBIGUOUS · INFERRED: 226 edges (avg confidence: 0.85)
- Token cost: 60,519 input · 0 output

## Community Hubs (Navigation)
- HTTP Layer & Base Controller
- Services & Portal Access
- Admin & Web Controllers
- Ticket Checkout Flow
- App Configuration
- Password Reset & Video Uploads
- Emergence Funnel API & Mailchimp
- API Data Models
- Module Views & Webinars
- Admin Emergence Booth
- Attendee Connections & Realtime
- Admin Authentication
- Mobile Agenda & Past Conferences
- Web Attendee List Scripts
- Web Realtime Scripts
- API Reference Endpoints
- WebSocket Chat Server
- Conference API
- Payments API (Flutterwave/PayPal)
- Web Emergence Funnel & Countries
- Admin Attendee Management
- Admin Communities & Manager Inbox
- Auth Attendee Model & Services
- Mobile Messages & Login
- Sponsors API & Web
- Admin Conferences
- Mobile Networking Center Scripts
- Users & Points Migrations
- Admin Speakers
- Conference Sessions API
- Payment & Pricing Models
- Mobile Login & Registration
- Web Upgrade Pricing Scripts
- Filter Configuration
- Exhibitors API & Messages
- Web Agenda & Sessions
- Admin Conference Sessions
- Admin Exhibitors
- Admin Speaker Offers
- Admin Sponsors
- API Login & Registration
- Direct Messages API
- Mobile Home & Welcome
- Mobile Notifications
- Mobile Session Detail Scripts
- Web Emergence Booth
- Cache Configuration
- Sessions & Messages Migrations
- Debug Toolbar Configuration
- Admin Portal Messages
- API Auth & Password Reset
- Mobile Networking & Communities
- Web Login & Presence
- Database Config & Mobile Webinars
- Admin User Accounts
- Attendees API & Online State
- Mobile Exhibitors
- Web Country Communities
- Web Profile
- Zoom Session Join
- Exception Handling Config
- User Entity
- API Helper & Web Registration
- Validation Configuration
- Payment Service Library
- Supabase Chat Library
- Admin Webinars Model
- Admin Layout Scripts
- Mobile Profile
- Mobile Sponsors
- Web Session Chat Scripts
- Error Page Debug Script
- Kint Debug Config
- Conference Model & Start Page
- Legacy Mobile Menu
- Web Attendees Pages
- Web Points
- View Configuration
- Web Conference Detail
- Response Format Config
- Image Handling Config
- Session Storage Config
- MIME Types
- User Profiles Migration
- User Integrations Migration
- User Tokens Migration
- Events Migration
- Sessions Migration
- Payments Migration
- Premium Access Migration
- Admin Password Resets Migration
- Attendee Connections Migration
- Attendee Password Resets Migration
- Registered 2026 Migration
- Welcome Video Migration
- Admin Activity Logs Migration
- Speaker Offers Migration
- Emergence Booth Migration
- Admin Portal Messages Migration
- User Presence Migration
- Country Communities Migration
- Zoom Fields Migration
- SSO Service
- Mobile Emergence Booth
- Mobile Booth Chat Scripts
- Mobile Upgrade Scripts
- Web Forgot Password
- Web Communities Chat Scripts
- Web Booth Chat Scripts
- Autoload Config
- Foreign Characters Config
- Publisher Config
- Routing Config
- Logger Config
- Modules Config
- Cookie Config
- Email Config
- Default Home Controller
- Admin Community Chat Scripts
- Admin Booth Chat Scripts
- Admin Message Form Scripts
- Admin Vimeo Preview
- Legacy Mobile Topbar
- DocTypes Config
- Optimize Config
- Paths Config
- Directory Access Guard
- Web Auth Header
- Web Header
- Web Home Header

## God Nodes (most connected - your core abstractions)
1. `BaseController` - 156 edges
2. `module_view()` - 95 edges
3. `Session` - 84 edges
4. `CheckoutController` - 36 edges
5. `EmergenceBoothController` - 26 edges
6. `PaymentsController` - 23 edges
7. `PortalAccessService` - 21 edges
8. `TblUsersModel` - 20 edges
9. `EmergenceFunnelController` - 18 edges
10. `AuthController` - 17 edges

## Surprising Connections (you probably didn't know these)
- `UserService` --references--> `CURLRequest`  [EXTRACTED]
  Services/UserService.php → Config/CURLRequest.php
- `AdminsController` --inherits--> `BaseController`  [EXTRACTED]
  Modules/Admin/Controllers/AdminsController.php → Controllers/BaseController.php
- `AttendeesController` --inherits--> `BaseController`  [EXTRACTED]
  Modules/Admin/Controllers/AttendeesController.php → Controllers/BaseController.php
- `AuthController` --inherits--> `BaseController`  [EXTRACTED]
  Modules/Admin/Controllers/AuthController.php → Controllers/BaseController.php
- `CommunitiesController` --inherits--> `BaseController`  [EXTRACTED]
  Modules/Admin/Controllers/CommunitiesController.php → Controllers/BaseController.php

## Import Cycles
- None detected.

## Communities (244 total, 213 thin omitted)

### Community 0 - "HTTP Layer & Base Controller"
Cohesion: 0.07
Nodes (11): AdminAudit, AdminAuth, ApiAuth, AuthFilter, MobileAuthFilter, MobileRedirect, PremiumAccess, RoleFilter (+3 more)

### Community 1 - "Services & Portal Access"
Cohesion: 0.06
Nodes (11): Services, TestController, asset_url(), Lobby, Upgrade, HomeController, LobbyController, MessageService (+3 more)

### Community 2 - "Admin & Web Controllers"
Cohesion: 0.07
Nodes (13): BaseController, AuditLogsController, DashboardController, Events, Attendees, Explore, PointsController, EnvisionController (+5 more)

### Community 4 - "App Configuration"
Cohesion: 0.11
Nodes (13): App, ContentSecurityPolicy, Cors, CURLRequest, Encryption, Feature, Generators, Honeypot (+5 more)

### Community 5 - "Password Reset & Video Uploads"
Cohesion: 0.14
Nodes (3): AttendeePasswordReset, ChatController, BunnyStreamService

### Community 7 - "API Data Models"
Cohesion: 0.12
Nodes (11): TblCountriesModel, TblEventsModel, TblPointsEarnedModel, TblPointsGuideModel, TblSessionSpeakersModel, TblSpeakersModel, TblTicketPricesModel, TblUserStatusModel (+3 more)

### Community 8 - "Module Views & Webinars"
Cohesion: 0.10
Nodes (5): module_view(), WebinarsController, Auth, PaymentResult, PaymentsController

### Community 10 - "Attendee Connections & Realtime"
Cohesion: 0.17
Nodes (3): ConnectionsController, TblAttendeeConnectionsModel, RealtimeNotifier

### Community 12 - "Mobile Agenda & Past Conferences"
Cohesion: 0.13
Nodes (5): Agenda, PastConference, SpeakersController, SessionModel, SpeakersModel

### Community 14 - "Web Attendee List Scripts"
Cohesion: 0.19
Nodes (15): closeDrawer(), closeProfile(), createAvatar(), detailsFromCard(), isOnline(), loadThread(), normalizeSocialUrl(), openDrawer() (+7 more)

### Community 15 - "Web Realtime Scripts"
Cohesion: 0.18
Nodes (13): appendConnectionRequest(), closeModal(), connectRealtime(), handleRealtimeEvent(), move(), notify(), openModal(), pollPendingConnections() (+5 more)

### Community 16 - "API Reference Endpoints"
Cohesion: 0.14
Nodes (4): CountriesController, SessionSpeakersController, SpeakersController, UsersController

### Community 18 - "Conference API"
Cohesion: 0.12
Nodes (3): ConferenceController, TicketPricesController, TblConferencesModel

### Community 25 - "Sponsors API & Web"
Cohesion: 0.18
Nodes (4): SponsorsController, TblSponsorsModel, SpeakersController, SponsorsController

### Community 27 - "Mobile Networking Center Scripts"
Cohesion: 0.23
Nodes (9): apply(), card(), connect(), el(), fillPerson(), loadChat(), more(), paintButton() (+1 more)

### Community 28 - "Users & Points Migrations"
Cohesion: 0.15
Nodes (3): CreateTblUsers, CreateTblPointsGuideAndEarned, CreateTblEmergenceFunnelSubmissions

### Community 29 - "Admin Speakers"
Cohesion: 0.18
Nodes (3): SpeakersController, ConferenceSessionsModel, SpeakersModel

### Community 30 - "Conference Sessions API"
Cohesion: 0.23
Nodes (3): ConferenceSessionsController, TblConferenceSessionsModel, TblConferenceSponsorsModel

### Community 31 - "Payment & Pricing Models"
Cohesion: 0.18
Nodes (4): TblAttendeePaymentsModel, TblCountryCurrencyModel, TblPremiumPricesModel, PaymentWebhookController

### Community 32 - "Mobile Login & Registration"
Cohesion: 0.21
Nodes (4): RegisterController, AttendeeModel, CountryModel, UserModel

### Community 33 - "Web Upgrade Pricing Scripts"
Cohesion: 0.22
Nodes (12): baseUSDPrice, countryPrices, countrySelect, countryToCurrency, currencyForCountry(), currencyPrices, decideGateway(), displayPrice (+4 more)

### Community 35 - "Exhibitors API & Messages"
Cohesion: 0.21
Nodes (4): ExhibitorsController, TblExhibitorMessagesModel, TblExhibitorsModel, ExhibitorsController

### Community 41 - "API Login & Registration"
Cohesion: 0.42
Nodes (4): LoginController, RegistrationController, TblAttendeesModel, TblUsersModel

### Community 43 - "Mobile Home & Welcome"
Cohesion: 0.27
Nodes (3): Home, Welcome, ConferenceModel

### Community 45 - "Mobile Session Detail Scripts"
Cohesion: 0.29
Nodes (4): escapeHtml(), formatMessage(), load(), render()

### Community 57 - "Mobile Exhibitors"
Cohesion: 0.32
Nodes (3): ExhibitorsController, ExhibitorMessageModel, ExhibitorModel

### Community 69 - "Admin Layout Scripts"
Cohesion: 0.33
Nodes (3): darkIcon, lightIcon, toggleBtn

### Community 73 - "Web Session Chat Scripts"
Cohesion: 0.47
Nodes (3): appendMessage(), loadMessages(), sendMessage()

### Community 74 - "Error Page Debug Script"
Cohesion: 0.53
Nodes (4): getFirstChildWithTagName(), getHash(), init(), showTab()

### Community 78 - "Legacy Mobile Menu"
Cohesion: 0.40
Nodes (4): closeBtn, mobileMenu, openBtn, overlay

### Community 111 - "Mobile Upgrade Scripts"
Cohesion: 0.83
Nodes (3): gatewayName(), paint(), quote()

## Knowledge Gaps
- **1 isolated node(s):** `403 Forbidden Placeholder Page (app/index.html)`
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 438 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **213 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `BaseController` connect `Admin & Web Controllers` to `HTTP Layer & Base Controller`, `Services & Portal Access`, `Ticket Checkout Flow`, `Password Reset & Video Uploads`, `Emergence Funnel API & Mailchimp`, `Module Views & Webinars`, `Admin Emergence Booth`, `Attendee Connections & Realtime`, `Admin Authentication`, `Mobile Agenda & Past Conferences`, `Payments API (Flutterwave/PayPal)`, `Web Emergence Funnel & Countries`, `Admin Attendee Management`, `Admin Communities & Manager Inbox`, `Mobile Messages & Login`, `Sponsors API & Web`, `Admin Conferences`, `Admin Speakers`, `Payment & Pricing Models`, `Mobile Login & Registration`, `Exhibitors API & Messages`, `Web Agenda & Sessions`, `Admin Conference Sessions`, `Admin Exhibitors`, `Admin Speaker Offers`, `Admin Sponsors`, `API Login & Registration`, `Direct Messages API`, `Mobile Home & Welcome`, `Mobile Notifications`, `Web Emergence Booth`, `Admin Portal Messages`, `API Auth & Password Reset`, `Mobile Networking & Communities`, `Web Login & Presence`, `Database Config & Mobile Webinars`, `Admin User Accounts`, `Mobile Exhibitors`, `Web Country Communities`, `Web Profile`, `Zoom Session Join`, `API Helper & Web Registration`, `Admin Webinars Model`, `Mobile Profile`, `Mobile Sponsors`, `Conference Model & Start Page`, `Web Attendees Pages`, `Web Points`, `Web Conference Detail`, `Mobile Emergence Booth`, `Web Forgot Password`, `Default Home Controller`?**
  _High betweenness centrality (0.242) - this node is a cross-community bridge._
- **Are the 94 inferred relationships involving `module_view()` (e.g. with `.create()` and `.edit()`) actually correct?**
  _`module_view()` has 94 INFERRED edges - model-reasoned connections that need verification._
- **What connects `403 Forbidden Placeholder Page (app/index.html)` to the rest of the system?**
  _1 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `HTTP Layer & Base Controller` be split into smaller, more focused modules?**
  _Cohesion score 0.07117255504352278 - nodes in this community are weakly interconnected._
- **Why does `Session` connect `Mobile Messages & Login` to `HTTP Layer & Base Controller`, `Services & Portal Access`, `Admin & Web Controllers`, `Ticket Checkout Flow`, `Password Reset & Video Uploads`, `Module Views & Webinars`, `Admin Emergence Booth`, `Attendee Connections & Realtime`, `Admin Authentication`, `Mobile Agenda & Past Conferences`, `Admin Attendee Management`, `Admin Communities & Manager Inbox`, `Sponsors API & Web`, `Mobile Login & Registration`, `Web Agenda & Sessions`, `Direct Messages API`, `Mobile Home & Welcome`, `Mobile Notifications`, `Web Emergence Booth`, `Admin Portal Messages`, `API Auth & Password Reset`, `Mobile Networking & Communities`, `Web Login & Presence`, `Database Config & Mobile Webinars`, `Attendees API & Online State`, `Mobile Exhibitors`, `Web Country Communities`, `Web Profile`, `Zoom Session Join`, `Mobile Profile`, `Conference Model & Start Page`, `Web Attendees Pages`, `Web Conference Detail`, `Mobile Emergence Booth`?**
  _High betweenness centrality (0.046) - this node is a cross-community bridge._
- **Are the 80 inferred relationships involving `Session` (e.g. with `.__construct()` and `.edit()`) actually correct?**
  _`Session` has 80 INFERRED edges - model-reasoned connections that need verification._
- **Should `Services & Portal Access` be split into smaller, more focused modules?**
  _Cohesion score 0.0641025641025641 - nodes in this community are weakly interconnected._