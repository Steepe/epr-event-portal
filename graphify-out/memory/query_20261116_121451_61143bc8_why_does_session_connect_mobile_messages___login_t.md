---
type: "query"
date: "2026-11-16T12:14:51.344386+00:00"
question: "Why does Session connect Mobile Messages & Login to 34 other communities?"
contributor: "graphify"
outcome: "corrected"
correction: "Session's 80 inferred call edges are a name collision with the session() helper, not real calls."
source_nodes: ["Session", "BaseController", "AuthController"]
---

# Q: Why does Session connect Mobile Messages & Login to 34 other communities?

## Answer

Expanded from original query via vocab: [session, login, logout, auth, attempt, mobile, messages, presence]. The Session god node is the mobile controller class App\Modules\MobileApp\Controllers\Session (Modules/MobileApp/Controllers/Session.php L16), which shows a conference session page. 80 of its 84 edges are INFERRED calls (0.85) that are false: they come from lines calling CodeIgniter's global session() helper (for example Modules/Admin/Controllers/AuthController.php L74, session('admin_id')), which the extractor matched by name to the Session class. The only real reference to the class is the route mobile/session/(:num) -> Session::detail. Session is therefore not an architectural hub; the edges mark where the PHP session is read or written. Checked in code: Admin uses admin_id/admin_role/is_admin; desktop pages gate on logged_in; mobile gates on isLoggedIn; the API login writes both; no code writes the plan or user_timezone session keys that Web controllers read.

## Outcome

- Signal: corrected
- Correction: Session's 80 inferred call edges are a name collision with the session() helper, not real calls.

## Source Nodes

- Session
- BaseController
- AuthController