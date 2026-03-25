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
                        pine: {
                            50: '#f2f7f4', 100: '#e1efe6', 200: '#c4dfcf', 300: '#9bc6b0',
                            400: '#6ba889', 500: '#488c6b', 600: '#356f53', 700: '#2b5843',
                            800: '#1b3a2a', 900: '#153A26',
                        },
                        antique: { 400: '#D4AF37', 500: '#C0A062', 600: '#9E824A' },
                        paper: { DEFAULT: '#FCFBFA', 100: '#F5F3ED', 200: '#EAE6DB' }
                    },
                    fontFamily: {
                        serif: ['"Playfair Display"', 'serif'],
                        sans: ['"Inter"', 'sans-serif'],
                    },
                    boxShadow: {
                        'elegant': '0 10px 40px -10px rgba(21, 58, 38, 0.08)',
                    }
                }
            }
        }
    </script>

    
    <style>
        body {
            background-color: #FCFBFA;
            background-image: url("data:image/svg+xml,%3Csvg width='80' height='80' viewBox='0 0 80 80' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M40 0C17.909 0 0 17.909 0 40c0 22.091 17.909 40 40 40 22.091 0 40-17.909 40-40C80 17.909 62.091 0 40 0zm0 3.2c20.324 0 36.8 16.476 36.8 36.8 0 20.324-16.476 36.8-36.8 36.8-20.324 0-36.8-16.476-36.8-36.8C3.2 19.676 19.676 3.2 40 3.2zm0 14.4c12.371 0 22.4 10.029 22.4 22.4 0 12.371-10.029 22.4-22.4 22.4-12.371 0-22.4-10.029-22.4-22.4 0-12.371 10.029-22.4 22.4-22.4zm0 3.2c-10.604 0-19.2 8.596-19.2 19.2 0 10.604 8.596 19.2 19.2 19.2 10.604 0 19.2-8.596 19.2-19.2 0-10.604-8.596-19.2-19.2-19.2z' fill='%23C0A062' fill-opacity='0.03' fill-rule='evenodd'/%3E%3C/svg%3E");
            color: #333;
        }
        .corner-decor {
            position: fixed; width: 250px; height: 250px; z-index: 0; pointer-events: none;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath fill='none' stroke='%23C0A062' stroke-width='1.5' stroke-opacity='0.3' d='M0,0 C100,0 200,100 200,200'/%3E%3Cpath fill='none' stroke='%23C0A062' stroke-width='1' stroke-opacity='0.2' d='M0,20 C80,20 180,120 180,200'/%3E%3Cpath fill='none' stroke='%23C0A062' stroke-width='0.5' stroke-opacity='0.15' d='M0,40 C60,40 160,140 160,200'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
        }
        .corner-tl { top: 0; left: 0; transform: rotate(0deg); }
        .corner-tr { top: 0; right: 0; transform: rotate(90deg); }
        .corner-br { bottom: 0; right: 0; transform: rotate(180deg); }
        .corner-bl { bottom: 0; left: 0; transform: rotate(270deg); }

        .hotel-section {
            background: #FFFFFF;
            border: 1px solid #EAE6DB;
            border-top: 4px solid #153A26;
            box-shadow: 0 10px 40px -10px rgba(21, 58, 38, 0.05);
        }
        .nav-glass {
            background: rgba(252, 251, 250, 0.9);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #EAE6DB;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        }

        
        
        .glass-card-premium { background: #FFFFFF; border: 1px solid #EAE6DB; border-top: 4px solid #153A26; box-shadow: 0 10px 40px -10px rgba(21, 58, 38, 0.05); }
        
        .lodge-input { width: 100%; padding: 12px 16px; border-radius: 4px; border: 1px solid #EAE6DB; background: transparent; color: #153A26; font-family: 'Inter', sans-serif; font-size: 0.95rem; transition: border-color 0.3s ease; outline: none; }
        .lodge-input::placeholder { color: #A8A39C; font-style: italic; }
        .lodge-input:focus { border-color: #153A26; background: #fff; box-shadow: 0 2px 8px rgba(21,58,38,0.05); }
        .lodge-input::placeholder { color: rgba(255, 255, 255, 0.2); }
        .lodge-input:focus { border-color: #C9A96E; background: rgba(255, 255, 255, 0.06); box-shadow: 0 0 0 4px rgba(201, 169, 110, 0.1); }
        
        .modal-backdrop { position: fixed; inset: 0; z-index: 50; background: rgba(21, 58, 38, 0.4); backdrop-filter: blur(4px); display: none; align-items: center; justify-content: center; }
        .modal-backdrop.show { display: flex; }
        .modal-content { background: #FFFFFF; border: 1px solid #EAE6DB; border-top: 4px solid #153A26; border-radius: 12px; box-shadow: 0 10px 40px -10px rgba(21, 58, 38, 0.15); width: 100%; max-width: 480px; padding: 40px; transform: translateY(20px); opacity: 0; transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
        .modal-backdrop.show .modal-content { opacity: 1; transform: translateY(0); }
        
        @keyframes fadeUp { 0% { opacity: 0; transform: translateY(20px); } 100% { opacity: 1; transform: translateY(0); } }
        .fade-up { opacity: 0; animation: fadeUp 0.6s ease-out forwards; }
        
        .badge-active { background: rgba(52, 211, 153, 0.1); color: #34d399; border: 1px solid rgba(52, 211, 153, 0.2); }
        .badge-inactive { background: rgba(248, 113, 113, 0.1); color: #f87171; border: 1px solid rgba(248, 113, 113, 0.2); }
        .btn-action { padding: 8px 14px; border-radius: 10px; font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; cursor: pointer; transition: all 0.3s ease; border: 1px solid transparent; }
        .btn-activate { background: rgba(52, 211, 153, 0.05); color: #34d399; border-color: rgba(52, 211, 153, 0.2); }
        .btn-activate:hover { background: rgba(52, 211, 153, 0.1); transform: translateY(-1px); }
        .btn-deactivate { background: rgba(248, 113, 113, 0.05); color: #f87171; border-color: rgba(248, 113, 113, 0.2); }
        .btn-deactivate:hover { background: rgba(248, 113, 113, 0.1); transform: translateY(-1px); }
        .btn-reset { background: rgba(201, 169, 110, 0.05); color: #C9A96E; border-color: rgba(201, 169, 110, 0.2); }
        .btn-reset:hover { background: rgba(201, 169, 110, 0.1); transform: translateY(-1px); }
        
        .toast { position: fixed; bottom: 32px; right: 32px; z-index: 1000; padding: 16px 28px; border-radius: 16px; font-size: 0.85rem; font-weight: 600; transform: translateY(100px); opacity: 0; transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); box-shadow: 0 10px 25px rgba(0,0,0,0.3); backdrop-filter: blur(10px); }
        .toast.show { transform: translateY(0); opacity: 1; }
        .toast-success { background: rgba(52, 211, 153, 0.9); border: 1px solid rgba(255,255,255,0.2); color: #064e3b; }
        .toast-error { background: rgba(248, 113, 113, 0.9); border: 1px solid rgba(255,255,255,0.2); color: #7f1d1d; }
    </style>
</head>
<body class="font-sans text-pine-900 min-h-screen relative overflow-x-hidden">
    
    <!-- Elegant Corner Decor Rings -->
    <div class="corner-decor corner-tl hidden md:block"></div>
    <div class="corner-decor corner-tr hidden md:block"></div>
    <div class="corner-decor corner-br hidden md:block"></div>
    <div class="corner-decor corner-bl hidden md:block"></div>
    

    <!-- Nav -->
    <nav class="nav-glass sticky top-0 z-40">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-antique-400 to-antique-500 flex items-center justify-center shadow-lg shadow-antique-400/20">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"/></svg>
                </div>
                <div>
                    <h1 class="font-serif text-xl font-bold text-pine-900 leading-tight">Account Management Portal</h1>
                    <p class="text-antique-400/60 text-[0.65rem] uppercase tracking-[0.2em] font-black">Authorized Personnel Only</p>
                </div>
            </div>
            <div class="flex items-center gap-6">
                <a href="index.php" class="text-xs font-black uppercase tracking-widest text-antique-400 border-b-2 border-antique-400 pb-1">Manage Admins</a>
                <span class="text-pine-700/20">|</span>
                <a href="logout.php" class="px-4 py-2 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 hover:bg-red-500/20 transition-all text-[0.65rem] font-bold uppercase tracking-widest">Logout</a>
            </div>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-6 py-12 relative z-10">
        <div class="flex flex-wrap items-center justify-between gap-6 mb-12 fade-up">
            <div>
                <h2 class="font-serif text-3xl font-bold text-pine-900 tracking-tight">Admin Status Management</h2>
                <p class="text-pine-700/70 text-sm mt-1">Activate or deactivate administrative access levels</p>
            </div>
            <button onclick="openCreateModal()" class="px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest flex items-center gap-3 transition-all duration-500 hover:shadow-xl hover:shadow-antique-400/20 hover:scale-[1.02] active:scale-[0.98] text-white" style="background:linear-gradient(135deg,#C9A96E,#b5893a)">
                <svg class="w-5 h-5 font-bold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Add New Admin
            </button>
        </div>

        <!-- Stats hidden to focus on management -->

        <!-- Table -->
        <div class="glass-card-premium rounded-[2.5rem] overflow-hidden fade-up" style="animation-delay:0.2s">
            <div class="px-10 py-8 border-b border-paper-200 bg-paper-100/30 flex items-center justify-between">
                <div>
                    <h3 class="font-serif text-xl font-bold text-pine-900">Administrator List</h3>
                    <p class="text-pine-700/60 text-[0.65rem] uppercase tracking-widest mt-1 font-bold">Managed administrative profiles and access</p>
                </div>
                <button onclick="loadAdmins()" class="w-10 h-10 rounded-xl bg-paper-100/50 border border-paper-200 flex items-center justify-center text-antique-400/40 hover:text-antique-400 hover:bg-paper-100/50 transition-all hover:rotate-180 duration-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </button>
            </div>
            <div id="adminTableContainer">
                <div class="px-10 py-24 text-center">
                    <div class="inline-block w-8 h-8 border-2 border-antique-400/20 border-t-antique-400 rounded-full animate-spin mb-4"></div>
                    <p class="text-pine-700/70 text-xs font-black uppercase tracking-[0.2em]">Loading Administrators...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal-backdrop" id="createModal">
        <div class="modal-content">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-serif text-xl text-pine-900 tracking-wide">Create New Admin</h3>
                <button onclick="closeCreateModal()" class="text-pine-700/60 hover:text-pine-700"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <form id="createAdminForm" class="space-y-4">
                <div>
                    <label class="block text-[0.65rem] font-bold text-antique-400 uppercase tracking-[0.15em] mb-2">Full Name</label>
                    <input type="text" id="newFullName" placeholder="e.g. John Doe" class="lodge-input">
                </div>
                <div>
                    <label class="block text-[0.65rem] font-bold text-antique-400 uppercase tracking-[0.15em] mb-2">Username *</label>
                    <input type="text" id="newUsername" required placeholder="e.g. johndoe" class="lodge-input">
                </div>
                <div>
                    <label class="block text-[0.65rem] font-bold text-antique-400 uppercase tracking-[0.15em] mb-2">Email Address *</label>
                    <input type="email" id="newEmail" required placeholder="e.g. john@example.com" class="lodge-input">
                </div>
                <div>
                    <label class="block text-[0.65rem] font-bold text-antique-400 uppercase tracking-[0.15em] mb-2">Password *</label>
                    <input type="password" id="newPassword" required placeholder="Min. 6 characters" class="lodge-input">
                </div>
                <div class="pt-4 flex gap-3">
                    <button type="button" onclick="closeCreateModal()" class="flex-1 py-3 rounded-xl font-bold text-sm uppercase tracking-wider border border-paper-200 text-pine-700/80 hover:bg-paper-100/50 hover:text-pine-900 transition-all">Cancel</button>
                    <button type="submit" class="flex-1 py-3 rounded-xl font-bold text-sm uppercase tracking-wider transition-all duration-300 hover:shadow-lg" style="background:#153A26;color:#ffffff">Create Admin</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Reset Modal -->
    <div class="modal-backdrop" id="resetModal">
        <div class="modal-content">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-serif text-xl text-pine-900 tracking-wide">Reset Password</h3>
                    <p class="text-antique-400/80 text-xs mt-1" id="resetAdminLabel"></p>
                </div>
                <button onclick="closeResetModal()" class="text-pine-700/60 hover:text-pine-700"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <form id="resetPasswordForm" class="space-y-4">
                <input type="hidden" id="resetAdminId">
                <div>
                    <label class="block text-[0.65rem] font-bold text-antique-400 uppercase tracking-[0.15em] mb-2">New Password *</label>
                    <input type="password" id="resetNewPassword" required placeholder="Min. 6 characters" class="lodge-input">
                </div>
                <div class="pt-4 flex gap-3">
                    <button type="button" onclick="closeResetModal()" class="flex-1 py-3 rounded-xl font-bold text-sm uppercase tracking-wider border border-paper-200 text-pine-700/80 hover:bg-paper-100/50 transition-all">Cancel</button>
                    <button type="submit" class="flex-1 py-3 rounded-xl font-bold text-sm uppercase tracking-wider transition-all duration-300" style="background:#153A26;color:#ffffff">Reset</button>
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
            const c = document.getElementById('adminTableContainer');
            if (admins.length === 0) {
                c.innerHTML = '<div class="px-10 py-24 text-center"><p class="text-pine-700/60 text-xs font-bold uppercase tracking-widest">No administrative records found.</p></div>';
                return;
            }
            
            let h = '<div class="overflow-x-auto"><table class="w-full text-sm"><thead><tr class="bg-paper-100/30 border-b border-paper-200"><th class="px-10 py-5 text-left text-[0.65rem] font-black text-antique-400/60 uppercase tracking-[0.2em]">Full Name</th><th class="px-10 py-5 text-left text-[0.65rem] font-black text-antique-400/60 uppercase tracking-[0.2em]">Username / Email</th><th class="px-10 py-5 text-center text-[0.65rem] font-black text-antique-400/60 uppercase tracking-[0.2em]">Status</th><th class="px-10 py-5 text-center text-[0.65rem] font-black text-antique-400/60 uppercase tracking-[0.2em]">Created</th><th class="px-10 py-5 text-center text-[0.65rem] font-black text-antique-400/60 uppercase tracking-[0.2em]">Actions</th></tr></thead><tbody class="divide-y divide-paper-200">';
            
            admins.forEach(x => {
                const act = x.is_active == 1;
                const bc = act ? 'badge-active' : 'badge-inactive';
                const bt = act ? 'Active' : 'Inactive';
                const tbc = act ? 'btn-deactivate' : 'btn-activate';
                const tbt = act ? 'Deactivate' : 'Activate';
                const date = x.created_at ? new Date(x.created_at).toLocaleDateString() : 'N/A';
                
                h += `<tr class="hover:bg-paper-100/30 transition-all group">
                    <td class="px-10 py-6">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xs font-black shadow-inner" style="background:rgba(201,169,110,0.05);color:#C9A96E">${escapeHtml(x.full_name || x.username).charAt(0).toUpperCase()}</div>
                            <span class="text-pine-900 font-bold tracking-tight">${escapeHtml(x.full_name || '-')}</span>
                        </div>
                    </td>
                    <td class="px-10 py-6">
                        <div class="flex flex-col">
                            <span class="text-pine-900 font-mono text-xs font-bold">${escapeHtml(x.username)}</span>
                            <span class="text-pine-700/60 text-[0.7rem]">${escapeHtml(x.email || '-')}</span>
                        </div>
                    </td>
                    <td class="px-10 py-6 text-center"><span class="inline-flex px-4 py-1.5 rounded-xl text-[0.6rem] font-black uppercase tracking-widest ${bc}">${bt}</span></td>
                    <td class="px-10 py-6 text-center text-pine-700/60 text-[0.65rem] font-bold">${date}</td>
                    <td class="px-10 py-6 text-center">
                        <div class="flex items-center justify-center gap-3">
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
                }).catch(() => showToast('Operation failed.', 'error'));
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
                  n = document.getElementById('newFullName').value.trim(),
                  e_mail = document.getElementById('newEmail').value.trim();
                  
            if(p.length < 6) return showToast('Password must be at least 6 characters.', 'error');
            
            const fd = new FormData();
            fd.append('action', 'create');
            fd.append('username', u);
            fd.append('password', p);
            fd.append('full_name', n);
            fd.append('email', e_mail);
            
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
            document.getElementById('resetAdminLabel').textContent = 'Identity: ' + un;
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
