<?php
/**
 * SUPER ADMIN DASHBOARD
 * Event Feedback System
 */
session_start();
require_once __DIR__ . "/../config.php";

if (!isset($_SESSION["superadmin_logged_in"]) || $_SESSION["superadmin_logged_in"] !== true) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin - Event Feedback System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: { 800: '#1e2a6e', 900: '#1a2255', 950: '#0f1333' },
                        gold: { 400: '#C9A96E', 500: '#b5893a' }
                    },
                    fontFamily: {
                        serif: ['"Playfair Display"', 'serif'],
                        sans: ['"Inter"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { background: #0f1333; }
        .glass-card { background: rgba(255,255,255,0.05); backdrop-filter: blur(40px); -webkit-backdrop-filter: blur(40px); border: 1px solid rgba(255,255,255,0.10); box-shadow: 0 8px 32px rgba(0,0,0,0.3); }
        .lodge-input { width: 100%; padding: 12px 16px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.15); background: rgba(255,255,255,0.05); color: rgba(255,255,255,0.9); font-family: 'Inter', sans-serif; font-size: 0.85rem; transition: all 0.3s ease; outline: none; }
        .lodge-input::placeholder { color: rgba(255,255,255,0.3); }
        .lodge-input:focus { border-color: #C9A96E; background: rgba(255,255,255,0.1); box-shadow: 0 0 0 3px rgba(201,169,110,0.15); }
        
        .modal-backdrop { position: fixed; inset: 0; z-index: 50; background: rgba(0,0,0,0.7); backdrop-filter: blur(4px); display: none; align-items: center; justify-content: center; }
        .modal-backdrop.show { display: flex; }
        .modal-content { background: #161c40; border: 1px solid rgba(255,255,255,0.1); border-radius: 20px; box-shadow: 0 25px 60px rgba(0,0,0,0.6); width: 100%; max-width: 460px; padding: 32px; }
        
        @keyframes fadeUp { 0% { opacity: 0; transform: translateY(20px); } 100% { opacity: 1; transform: translateY(0); } }
        .fade-up { opacity: 0; animation: fadeUp 0.5s ease-out forwards; }
        
        .badge-active { background: rgba(16,185,129,0.15); color: #10b981; border: 1px solid rgba(16,185,129,0.3); }
        .badge-inactive { background: rgba(239,68,68,0.15); color: #ef4444; border: 1px solid rgba(239,68,68,0.3); }
        .btn-action { padding: 6px 12px; border-radius: 8px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; cursor: pointer; transition: all 0.3s ease; border: 1px solid transparent; }
        .btn-activate { background: rgba(16,185,129,0.1); color: #10b981; border-color: rgba(16,185,129,0.3); }
        .btn-activate:hover { background: rgba(16,185,129,0.2); }
        .btn-deactivate { background: rgba(239,68,68,0.1); color: #ef4444; border-color: rgba(239,68,68,0.3); }
        .btn-deactivate:hover { background: rgba(239,68,68,0.2); }
        .btn-reset { background: rgba(201,169,110,0.1); color: #C9A96E; border-color: rgba(201,169,110,0.3); }
        .btn-reset:hover { background: rgba(201,169,110,0.2); }
        
        .toast { position: fixed; bottom: 24px; right: 24px; z-index: 100; padding: 14px 24px; border-radius: 12px; font-size: 0.85rem; font-weight: 500; transform: translateY(100px); opacity: 0; transition: all 0.4s ease; box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
        .toast.show { transform: translateY(0); opacity: 1; }
        .toast-success { background: #10b981; color: #fff; }
        .toast-error { background: #ef4444; color: #fff; }
    </style>
</head>
<body class="font-sans text-white min-h-screen">
    <!-- Nav -->
    <nav class="border-b border-white/10 bg-black/20 backdrop-blur-sm sticky top-0 z-40">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <div>
                <h1 class="font-serif text-xl font-bold text-gold-400">Super Admin Panel</h1>
                <p class="text-white/40 text-[0.65rem] uppercase tracking-widest">Event Feedback System</p>
            </div>
            <div class="flex items-center gap-4">
                <a href="index.php" class="text-sm text-gold-400 hover:text-gold-300 transition-colors font-medium border-b border-gold-400 pb-1">Manage Admins</a>
                <a href="analytics.php" class="text-sm text-white/50 hover:text-white transition-colors">Analytics</a>
                <a href="reports.php" class="text-sm text-white/50 hover:text-white transition-colors">Reports</a>
                <span class="text-white/20">|</span>
                <a href="logout.php" class="text-sm text-red-400 hover:text-red-300 transition-colors">Logout</a>
            </div>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-6 py-10">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8 fade-up">
            <div>
                <h2 class="font-serif text-2xl text-white/90 tracking-wide">Admin Account Management</h2>
                <p class="text-white/40 text-sm mt-1">Create, activate, and deactivate administrator accounts</p>
            </div>
            <button onclick="openCreateModal()" class="px-6 py-3 rounded-xl font-semibold text-sm uppercase tracking-wider flex items-center gap-2 transition-all duration-300 hover:shadow-lg hover:shadow-gold-400/20 hover:-translate-y-0.5" style="background:linear-gradient(135deg,#C9A96E,#b5893a);color:#0f1333">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                Create New Admin
            </button>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8 fade-up" style="animation-delay:0.1s">
            <div class="glass-card rounded-xl p-5 border-l-4 border-l-gold-400">
                <div class="flex items-center gap-3 mb-3">
                    <span class="text-[0.65rem] font-bold text-gold-400 uppercase tracking-[0.15em]">Total Admins</span>
                </div>
                <p class="text-3xl font-bold text-white/90" id="statTotal">-</p>
            </div>
            <div class="glass-card rounded-xl p-5 border-l-4 border-l-emerald-400">
                <div class="flex items-center gap-3 mb-3">
                    <span class="text-[0.65rem] font-bold text-emerald-400 uppercase tracking-[0.15em]">Active</span>
                </div>
                <p class="text-3xl font-bold text-emerald-400" id="statActive">-</p>
            </div>
            <div class="glass-card rounded-xl p-5 border-l-4 border-l-red-400">
                <div class="flex items-center gap-3 mb-3">
                    <span class="text-[0.65rem] font-bold text-red-400 uppercase tracking-[0.15em]">Deactivated</span>
                </div>
                <p class="text-3xl font-bold text-red-400" id="statInactive">-</p>
            </div>
        </div>

        <!-- Table -->
        <div class="glass-card rounded-2xl overflow-hidden fade-up" style="animation-delay:0.2s">
            <div class="px-6 py-5 border-b border-white/[0.08] flex items-center justify-between">
                <h3 class="font-serif text-white/80 text-lg tracking-wide">Administrator Accounts</h3>
                <button onclick="loadAdmins()" class="text-gold-400/60 hover:text-gold-400 transition-colors text-xs font-bold uppercase tracking-wider flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Refresh
                </button>
            </div>
            <div id="adminTableContainer">
                <div class="px-6 py-16 text-center">
                    <p class="text-white/40 text-sm animate-pulse">Loading admin accounts...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal-backdrop" id="createModal">
        <div class="modal-content">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-serif text-xl text-white tracking-wide">Create New Admin</h3>
                <button onclick="closeCreateModal()" class="text-white/30 hover:text-white/60"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <form id="createAdminForm" class="space-y-4">
                <div>
                    <label class="block text-[0.65rem] font-bold text-gold-400 uppercase tracking-[0.15em] mb-2">Full Name</label>
                    <input type="text" id="newFullName" placeholder="e.g. John Doe" class="lodge-input">
                </div>
                <div>
                    <label class="block text-[0.65rem] font-bold text-gold-400 uppercase tracking-[0.15em] mb-2">Username *</label>
                    <input type="text" id="newUsername" required placeholder="e.g. johndoe" class="lodge-input">
                </div>
                <div>
                    <label class="block text-[0.65rem] font-bold text-gold-400 uppercase tracking-[0.15em] mb-2">Password *</label>
                    <input type="password" id="newPassword" required placeholder="Min. 6 characters" class="lodge-input">
                </div>
                <div class="pt-4 flex gap-3">
                    <button type="button" onclick="closeCreateModal()" class="flex-1 py-3 rounded-xl font-bold text-sm uppercase tracking-wider border border-white/10 text-white/50 hover:bg-white/5 hover:text-white transition-all">Cancel</button>
                    <button type="submit" class="flex-1 py-3 rounded-xl font-bold text-sm uppercase tracking-wider transition-all duration-300 hover:shadow-lg" style="background:linear-gradient(135deg,#C9A96E,#b5893a);color:#0f1333">Create Admin</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Reset Modal -->
    <div class="modal-backdrop" id="resetModal">
        <div class="modal-content">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-serif text-xl text-white tracking-wide">Reset Password</h3>
                    <p class="text-gold-400/80 text-xs mt-1" id="resetAdminLabel"></p>
                </div>
                <button onclick="closeResetModal()" class="text-white/30 hover:text-white/60"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <form id="resetPasswordForm" class="space-y-4">
                <input type="hidden" id="resetAdminId">
                <div>
                    <label class="block text-[0.65rem] font-bold text-gold-400 uppercase tracking-[0.15em] mb-2">New Password *</label>
                    <input type="password" id="resetNewPassword" required placeholder="Min. 6 characters" class="lodge-input">
                </div>
                <div class="pt-4 flex gap-3">
                    <button type="button" onclick="closeResetModal()" class="flex-1 py-3 rounded-xl font-bold text-sm uppercase tracking-wider border border-white/10 text-white/50 hover:bg-white/5 transition-all">Cancel</button>
                    <button type="submit" class="flex-1 py-3 rounded-xl font-bold text-sm uppercase tracking-wider transition-all duration-300" style="background:linear-gradient(135deg,#C9A96E,#b5893a);color:#0f1333">Reset</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Toast -->
    <div class="toast" id="toast"></div>

    <script>
        function showToast(msg, type) {
            const t = document.getElementById('toast');
            t.textContent = msg;
            t.className = 'toast toast-' + (type || 'success') + ' show';
            setTimeout(() => t.classList.remove('show'), 3500);
        }

        const escapeHtml = (unsafe) => {
            return (unsafe || '').replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
        };

        window.loadAdmins = function() {
            fetch('api/admins.php').then(r => r.json()).then(d => {
                if (d.error) return showToast(d.error, 'error');
                renderAdmins(d.admins || []);
            }).catch(() => showToast('Failed to load admins.', 'error'));
        };

        function renderAdmins(admins) {
            let ac = 0, ic = 0;
            admins.forEach(x => x.is_active == 1 ? ac++ : ic++);
            document.getElementById('statTotal').textContent = admins.length;
            document.getElementById('statActive').textContent = ac;
            document.getElementById('statInactive').textContent = ic;
            
            const c = document.getElementById('adminTableContainer');
            if (admins.length === 0) {
                c.innerHTML = '<div class="px-6 py-16 text-center"><p class="text-white/40 text-sm">No admin accounts found.</p></div>';
                return;
            }
            
            let h = '<div class="overflow-x-auto"><table class="w-full text-sm"><thead><tr class="border-b border-white/[0.08]"><th class="px-6 py-4 text-left text-[0.65rem] font-bold text-gold-400 uppercase tracking-widest">Administrator</th><th class="px-6 py-4 text-left text-[0.65rem] font-bold text-gold-400 uppercase tracking-widest">Username</th><th class="px-6 py-4 text-center text-[0.65rem] font-bold text-gold-400 uppercase tracking-widest">Status</th><th class="px-6 py-4 text-center text-[0.65rem] font-bold text-gold-400 uppercase tracking-widest">Created</th><th class="px-6 py-4 text-center text-[0.65rem] font-bold text-gold-400 uppercase tracking-widest">Actions</th></tr></thead><tbody>';
            
            admins.forEach(x => {
                const act = x.is_active == 1;
                const bc = act ? 'badge-active' : 'badge-inactive';
                const bt = act ? 'Active' : 'Deactivated';
                const tbc = act ? 'btn-deactivate' : 'btn-activate';
                const tbt = act ? 'Deactivate' : 'Activate';
                const date = x.created_at ? new Date(x.created_at).toLocaleDateString() : 'N/A';
                
                h += `<tr class="border-b border-white/[0.04] hover:bg-white/[0.02] transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold" style="background:rgba(201,169,110,0.1);color:#C9A96E">${escapeHtml(x.full_name || x.username).charAt(0).toUpperCase()}</div>
                            <span class="text-white/80 font-medium">${escapeHtml(x.full_name || '-')}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4"><span class="text-white/50 font-mono text-sm">${escapeHtml(x.username)}</span></td>
                    <td class="px-6 py-4 text-center"><span class="inline-block px-3 py-1 rounded-full text-[0.65rem] font-bold uppercase tracking-wider ${bc}">${bt}</span></td>
                    <td class="px-6 py-4 text-center text-white/40 text-xs">${date}</td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <button onclick="toggleAdmin(${x.id})" class="btn-action ${tbc}">${tbt}</button>
                            <button onclick="openResetModal(${x.id}, '${escapeHtml(x.username)}')" class="btn-action btn-reset">Reset Password</button>
                        </div>
                    </td>
                </tr>`;
            });
            h += '</tbody></table></div>';
            c.innerHTML = h;
        }

        window.toggleAdmin = function(id) {
            if(!confirm('Are you sure you want to change this admin\'s status?')) return;
            const fd = new FormData();
            fd.append('action', 'toggle_status');
            fd.append('admin_id', id);
            fetch('api/admins.php', { method: 'POST', body: fd })
                .then(r => r.json()).then(d => {
                    if (d.error) return showToast(d.error, 'error');
                    showToast(d.message, 'success');
                    loadAdmins();
                }).catch(() => showToast('Failed to update.', 'error'));
        };

        window.openCreateModal = () => {
            document.getElementById('createModal').classList.add('show');
            document.getElementById('createAdminForm').reset();
        };
        window.closeCreateModal = () => document.getElementById('createModal').classList.remove('show');

        document.getElementById('createAdminForm').addEventListener('submit', e => {
            e.preventDefault();
            const u = document.getElementById('newUsername').value.trim(),
                  p = document.getElementById('newPassword').value.trim(),
                  n = document.getElementById('newFullName').value.trim();
                  
            if(p.length < 6) return showToast('Password must be at least 6 characters.', 'error');
            
            const fd = new FormData();
            fd.append('action', 'create');
            fd.append('username', u);
            fd.append('password', p);
            fd.append('full_name', n);
            
            fetch('api/admins.php', { method: 'POST', body: fd })
                .then(r => r.json()).then(d => {
                    if (d.error) return showToast(d.error, 'error');
                    showToast(d.message, 'success');
                    closeCreateModal();
                    loadAdmins();
                }).catch(() => showToast('Failed to create admin.', 'error'));
        });

        window.openResetModal = (id, un) => {
            document.getElementById('resetModal').classList.add('show');
            document.getElementById('resetAdminId').value = id;
            document.getElementById('resetAdminLabel').textContent = 'For: ' + un;
            document.getElementById('resetNewPassword').value = '';
        };
        window.closeResetModal = () => document.getElementById('resetModal').classList.remove('show');

        document.getElementById('resetPasswordForm').addEventListener('submit', e => {
            e.preventDefault();
            const id = document.getElementById('resetAdminId').value,
                  p = document.getElementById('resetNewPassword').value.trim();
                  
            if(p.length < 6) return showToast('Password must be at least 6 characters.', 'error');
            
            const fd = new FormData();
            fd.append('action', 'reset_password');
            fd.append('admin_id', id);
            fd.append('new_password', p);
            
            fetch('api/admins.php', { method: 'POST', body: fd })
                .then(r => r.json()).then(d => {
                    if (d.error) return showToast(d.error, 'error');
                    showToast(d.message, 'success');
                    closeResetModal();
                }).catch(() => showToast('Failed to reset password.', 'error'));
        });

        document.addEventListener('DOMContentLoaded', loadAdmins);
    </script>
</body>
</html>
