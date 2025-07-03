<?php
// admin_header.php
if (!isset($_SESSION)) session_start();
?>
<style>
.admin-sidebar {
  position: fixed;
  top: 0; left: 0; bottom: 0;
  width: 220px;
  background: #1b5e20;
  color: #fff;
  box-shadow: 2px 0 16px #0002;
  z-index: 1040;
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  transition: left 0.2s;
}
.admin-sidebar .sidebar-header {
  padding: 1.2rem 1rem 1rem 1rem;
  display: flex;
  align-items: center;
  gap: 12px;
  border-bottom: 1px solid #fff2;
}
.admin-sidebar .sidebar-header img {
  width: 40px; height: 40px; border-radius: 50%; box-shadow: 0 2px 8px #0002;
}
.admin-sidebar nav ul {
  list-style: none;
  padding: 0; margin: 0;
  margin-top: 1.5rem;
}
.admin-sidebar nav ul li {
  margin-bottom: 0.5rem;
}
.admin-sidebar nav ul li a {
  display: flex;
  align-items: center;
  gap: 10px;
  color: #fff;
  text-decoration: none;
  font-weight: 500;
  padding: 0.7rem 1.2rem;
  border-radius: 8px 0 0 8px;
  transition: background 0.15s;
}
.admin-sidebar nav ul li a.active, .admin-sidebar nav ul li a:hover {
  background: #388e3c;
  color: #fff;
}
@media (max-width: 900px) {
  .admin-sidebar { left: -220px; }
  .admin-sidebar.show { left: 0; }
}
.admin-sidebar-toggle {
  position: fixed;
  top: 18px; left: 18px;
  z-index: 1100;
  background: #1b5e20;
  color: #fff;
  border: none;
  border-radius: 50%;
  width: 40px; height: 40px;
  display: flex; align-items: center; justify-content: center;
  box-shadow: 0 2px 8px #0002;
}
@media (min-width: 901px) {
  .admin-sidebar-toggle { display: none; }
}
.admin-main-content {
  margin-left: 220px;
  padding: 0;
  min-height: 100vh;
  transition: margin-left 0.2s;
}
@media (max-width: 900px) {
  .admin-main-content { margin-left: 0; }
}
</style>
<!-- Sidebar Toggle Button (mobile) -->
<button class="admin-sidebar-toggle" id="sidebarToggle" aria-label="Open menu">
  <span style="font-size:1.5rem;line-height:1;">&#9776;</span>
</button>
<!-- Sidebar -->
<aside class="admin-sidebar" id="adminSidebar">
  <div class="sidebar-header">
    <img src="../assets/img/admin_logo.png" alt="Admin">
    <span class="fw-bold" style="font-size:1.2rem;letter-spacing:1px;">Admin Panel</span>
  </div>
  <nav>
    <ul>
      <li><a href="index.php"><span>&#128200;</span> Dashboard</a></li>
      <li><a href="manage_users.php"><span>&#128100;</span> Manage Users</a></li>
      <li><a href="manage_assistants.php"><span>&#129489;</span> Manage Assistants</a></li>
      <li><a href="../logout.php"><span>&#128682;</span> Logout</a></li>
    </ul>
  </nav>
</aside>
<script>
const sidebar = document.getElementById('adminSidebar');
const toggleBtn = document.getElementById('sidebarToggle');
function toggleSidebar() {
  sidebar.classList.toggle('show');
}
toggleBtn.addEventListener('click', toggleSidebar);
document.addEventListener('click', function(e) {
  if (window.innerWidth <= 900 && sidebar.classList.contains('show')) {
    if (!sidebar.contains(e.target) && e.target !== toggleBtn) {
      sidebar.classList.remove('show');
    }
  }
});
</script>
<!-- Usage: On every admin page, wrap main content in <div class="admin-main-content"> ... </div> --> 