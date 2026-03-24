<?php
/**
 * ═══════════════════════════════════════════════════════════════
 * ADMIN PRINTABLE REPORTS
 * Event Feedback System
 * ═══════════════════════════════════════════════════════════════
 */
session_start();
require_once __DIR__ . "/../config.php";

if (!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Admin Panel</title>
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
        .lodge-input:focus { border-color: rgba(201,169,110,0.4); background: rgba(255,255,255,0.05); ring: 4px rgba(201,169,110,0.05); }
        select.lodge-input option { background-color: #0f1333; color: #ffffff; }

        /* ═══ PRINT STYLES ═══ */
        @media print {
            * { color: #000 !important; background: #fff !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            body { background: #fff !important; font-size: 10pt; font-family: 'Inter', sans-serif; }
            nav, .no-print, footer, #controlsPanel, .corner-decor { display: none !important; }
            .glass-card-premium { background: #fff !important; border: 1px solid #ddd !important; border-top: 1px solid #ddd !important; box-shadow: none !important; }
            #reportContent { display: block !important; position: static !important; }
            
            .print-header { display: block !important; text-align: center; margin-bottom: 30px; border-bottom: none; }
            .print-header img { height: 80px; margin: 0 auto 20px auto; display: block; }
            .print-header h1 { font-family: 'Playfair Display', serif; font-size: 18pt; color: #153A26 !important; margin: 0 0 10px 0; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em; }
            .print-header p { font-size: 10pt; color: #666 !important; margin: 4px 0; }
            
            .report-table { width: 100%; border-collapse: collapse; margin: 15px 0; }
            .report-table th { background: #153A26 !important; color: #fff !important; padding: 8px 12px; text-align: left; font-size: 9pt; text-transform: uppercase; letter-spacing: 0.05em; }
            .report-table td { padding: 6px 12px; border-bottom: 1px solid #eee; font-size: 9pt; }
            .report-table tr:nth-child(even) td { background: #f9f9f9 !important; }
            
            .report-section { page-break-inside: avoid; margin-bottom: 30px; }
            .report-section h3 { font-family: 'Playfair Display', serif; font-size: 13pt; color: #153A26 !important; border-bottom: 2px solid #C9A96E; padding-bottom: 8px; margin-bottom: 12px; text-align: left; font-weight: 700; }
            
            .stat-box { display: inline-block; width: 18%; text-align: center; padding: 10px; border: 1px solid #ddd; border-radius: 8px; margin: 0 0.5%; }
            .stat-box .stat-val { font-size: 20pt; font-weight: 700; color: #153A26 !important; font-family: 'Playfair Display', serif; }
            .stat-box .stat-label { font-size: 7pt; text-transform: uppercase; letter-spacing: 0.1em; color: #999 !important; }
            
            .score-excellent { color: #059669 !important; font-weight: 600; }
            .score-good { color: #d97706 !important; font-weight: 600; }
            .score-poor { color: #dc2626 !important; font-weight: 600; }
            
            .comment-card { border: 1px solid #eee; padding: 12px 16px; margin-bottom: 12px; border-radius: 4px; page-break-inside: avoid; border-left: 3px solid #C9A96E; }
            .comment-card .guest-info { font-size: 8pt; color: #999 !important; border-bottom: 1px solid #eee; padding-bottom: 4px; margin-bottom: 8px; }
            .comment-card .comment-text { font-size: 9pt; font-style: italic; color: #333 !important; }
            .comment-page { display: block !important; }
            
            .print-footer { text-align: center; font-size: 7pt; color: #aaa !important; margin-top: 30px; padding-top: 10px; border-top: 1px solid #eee; display: block !important; }
            .print-only-table { display: block !important; margin-top: 10px; }
            #reportModal { display: none !important; }
        }
        .report-modal { display: none; position: fixed; inset: 0; z-index: 100; background: rgba(21, 58, 38, 0.4); backdrop-filter: blur(4px); }
        @keyframes fadeUp { 0% { opacity: 0; transform: translateY(20px); } 100% { opacity: 1; transform: translateY(0); } }
        .fade-up { opacity: 0; animation: fadeUp 0.6s ease-out forwards; }
        .spinner { width: 32px; height: 32px; border: 3px solid rgba(201,169,110,0.1); border-top-color: #C9A96E; border-radius: 50%; animation: spin 0.8s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body class="font-sans min-h-screen text-pine-900 relative">
    
    <!-- Elegant Corner Decor Rings -->
    <div class="corner-decor corner-tl hidden md:block"></div>
    <div class="corner-decor corner-tr hidden md:block"></div>
    <div class="corner-decor corner-br hidden md:block"></div>
    <div class="corner-decor corner-bl hidden md:block"></div>
    

    <!-- Nav -->
    <nav class="nav-glass sticky top-0 z-50 no-print">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-antique-400 to-antique-500 flex items-center justify-center shadow-lg shadow-antique-400/20">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 014-4h4m0 0l-4-4m4 4l-4 4m-5 3.5a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/></svg>
                </div>
                <div>
                    <h1 class="font-serif text-xl font-bold text-pine-900 leading-tight">Reports</h1>
                    <p class="text-antique-400/60 text-[0.65rem] uppercase tracking-[0.2em] font-bold">Data Export & Print</p>
                </div>
            </div>
            <div class="flex items-center gap-6">
                <div class="hidden md:flex items-center gap-6">
                    <a href="index.php" class="text-sm text-pine-700/80 hover:text-antique-400 transition-colors font-medium">Dashboard</a>
                    <a href="analytics.php" class="text-sm text-pine-700/80 hover:text-antique-400 transition-colors font-medium">Analytics</a>
                    <a href="reports.php" class="text-sm text-antique-400 transition-colors font-bold border-b-2 border-antique-400 pb-1">Reports</a>
                </div>
                <span class="text-pine-700/20 hidden md:block">|</span>
                <a href="logout.php" class="flex items-center gap-2 px-4 py-2 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 hover:bg-red-500/20 transition-all text-xs font-bold uppercase tracking-wider">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-6 py-8 relative z-10">
        <!-- Controls -->
        <div class="glass-card-premium rounded-3xl p-6 mb-8 fade-up no-print" style="animation-delay:0.1s" id="controlsPanel">
            <div class="flex flex-wrap items-end justify-between gap-6">
                <!-- Inputs -->
                <div class="flex flex-wrap items-end gap-5">
                    <div class="flex flex-col" style="min-width: 170px;">
                        <label class="block text-[0.65rem] font-bold text-antique-400 uppercase tracking-widest mb-2.5 ml-1">Quick Presets</label>
                        <select id="presetSelect" class="lodge-input w-full cursor-pointer">
                            <option value="today">Daily (Today)</option>
                            <option value="yesterday">Yesterday</option>
                            <option value="week">Weekly (7 Days)</option>
                            <option value="month" selected>Monthly (30 Days)</option>
                            <option value="year">Yearly</option>
                            <option value="all">All Time</option>
                            <option value="custom" hidden>Custom Date</option>
                        </select>
                    </div>
                    <div class="flex flex-col" style="min-width: 160px;">
                        <label class="block text-[0.65rem] font-bold text-antique-400 uppercase tracking-widest mb-2.5 ml-1">Date From</label>
                        <input type="date" id="dateFrom" class="lodge-input w-full">
                    </div>
                    <div class="flex flex-col" style="min-width: 160px;">
                        <label class="block text-[0.65rem] font-bold text-antique-400 uppercase tracking-widest mb-2.5 ml-1">Date To</label>
                        <input type="date" id="dateTo" class="lodge-input w-full">
                    </div>
                </div>
                <!-- Buttons -->
                <div class="flex flex-wrap items-end gap-3 lg:ml-auto">
                    <button id="btnGenerate" class="px-8 py-3 rounded-2xl font-bold text-xs uppercase tracking-widest transition-all duration-500 hover:shadow-2xl hover:shadow-antique-400/20 active:scale-95" style="background:#153A26;color:#ffffff;">
                        Generate Report
                    </button>
                    <button id="btnPrint" class="px-6 py-3 rounded-2xl font-bold text-xs uppercase tracking-widest border border-antique-400/20 text-antique-400 hover:bg-antique-400/10 transition-all flex items-center gap-2" style="visibility:hidden; opacity:0; pointer-events:none;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        Print
                    </button>
                </div>
            </div>
        </div>

        <div id="loadingReport" class="no-print text-center py-20" style="display:none">
            <div class="spinner mx-auto mb-6"></div>
            <p class="text-antique-400/40 text-[0.65rem] font-bold uppercase tracking-[0.2em] animate-pulse">Generating comprehensive report...</p>
        </div>

        <div id="emptyState" class="no-print glass-card-premium rounded-3xl p-20 text-center fade-up" style="animation-delay:0.2s">
            <div class="w-20 h-20 rounded-full bg-paper-100/50 flex items-center justify-center mx-auto mb-8 border border-paper-200">
                <svg class="w-10 h-10 text-pine-700/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <h3 class="font-serif text-3xl text-pine-800 mb-3">Select a Report Period</h3>
            <p class="text-pine-700/60 text-sm max-w-sm mx-auto leading-relaxed">Choose a preset or date range and generate the report to view the printable feedback summary.</p>
        </div>

        <div id="reportContent" style="display:none"></div>
    </div>

    <!-- Data Modal -->
    <div id="reportModal" class="report-modal no-print items-center justify-center p-4" style="display:none;">
        <div class="glass-card-premium w-full max-w-4xl max-h-[90vh] rounded-3xl overflow-hidden flex flex-col shadow-2xl relative translate-y-4 opacity-0 transition-all duration-300" id="modalContainer">
            <div class="px-8 py-5 border-b border-paper-200 bg-paper-100/30 flex items-center justify-between">
                <div>
                    <h3 id="modalTitle" class="font-serif text-xl font-bold text-pine-900">Details</h3>
                    <p class="text-antique-400/50 text-[0.65rem] uppercase tracking-widest font-bold mt-0.5">Report Breakdown</p>
                </div>
                <button type="button" id="closeModalBtn" class="w-10 h-10 rounded-xl bg-paper-100/50 hover:bg-paper-100/50 text-pine-700/70 hover:text-pine-900 transition-all flex items-center justify-center border border-paper-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div id="modalContent" class="p-8 overflow-y-auto"></div>
        </div>
    </div>


    <script>
    (function() {
        var dateFrom = document.getElementById('dateFrom');
        var dateTo = document.getElementById('dateTo');
        var btnGenerate = document.getElementById('btnGenerate');
        var btnPrint = document.getElementById('btnPrint');
        var reportContent = document.getElementById('reportContent');
        var loadingEl = document.getElementById('loadingReport');
        var emptyState = document.getElementById('emptyState');

        function formatDate(d) {
            var year = d.getFullYear();
            var month = String(d.getMonth() + 1).padStart(2, '0');
            var day = String(d.getDate()).padStart(2, '0');
            return year + '-' + month + '-' + day;
        }

        function displayDate(dateStr) {
            if (!dateStr || dateStr === '1970-01-01' || dateStr === '') return 'All Time';
            var d = new Date(dateStr + 'T00:00:00');
            var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            return months[d.getMonth()] + ' ' + d.getDate() + ', ' + d.getFullYear();
        }

        function scoreClass(val) {
            if (val >= 4) return 'score-excellent';
            if (val >= 3) return 'score-good';
            return 'score-poor';
        }

        function setPreset(type) {
            var today = new Date();
            var from, to;
            switch (type) {
                case 'today': from = to = formatDate(today); break;
                case 'yesterday': var yd = new Date(today); yd.setDate(yd.getDate() - 1); from = to = formatDate(yd); break;
                case 'week': to = formatDate(today); var w = new Date(today); w.setDate(w.getDate() - 6); from = formatDate(w); break;
                case 'month': to = formatDate(today); var m = new Date(today); m.setDate(m.getDate() - 29); from = formatDate(m); break;
                case 'year': to = formatDate(today); var y = new Date(today); y.setFullYear(y.getFullYear() - 1); from = formatDate(y); break;
                case 'all': from = '2020-01-01'; to = formatDate(today); break; // far past to now
            }
            dateFrom.value = from;
            dateTo.value = to;
        }

        document.getElementById('presetSelect').addEventListener('change', function(e) {
            var preset = e.target.value;
            if (preset === 'custom') return;
            setPreset(preset);
            generateReport(dateFrom.value, dateTo.value);
        });

        dateFrom.addEventListener('change', function() { document.getElementById('presetSelect').value = 'custom'; });
        dateTo.addEventListener('change', function() { document.getElementById('presetSelect').value = 'custom'; });

        btnGenerate.addEventListener('click', function() {
            if (!dateFrom.value || !dateTo.value) { alert('Please select both From and To dates.'); return; }
            generateReport(dateFrom.value, dateTo.value);
        });

        btnPrint.addEventListener('click', function() { window.print(); });

        function generateReport(from, to) {
            emptyState.style.display = 'none';
            btnGenerate.disabled = true; btnGenerate.style.opacity = '0.7'; btnGenerate.innerText = 'Generating...';
            btnPrint.disabled = true; btnPrint.style.opacity = '0.5'; btnPrint.style.pointerEvents = 'none';

            if (reportContent.innerHTML.trim() !== '') {
                reportContent.style.opacity = '0.4'; reportContent.style.pointerEvents = 'none';
                loadingEl.style.display = 'none';
            } else {
                reportContent.style.display = 'none'; loadingEl.style.display = 'block';
            }

            fetch('api/report_data.php?date_from=' + encodeURIComponent(from) + '&date_to=' + encodeURIComponent(to))
                .then(res => res.json())
                .then(data => {
                    if (data.error) { alert(data.error); resetState(); return; }
                    renderReport(data);
                    
                    loadingEl.style.display = 'none';
                    reportContent.style.display = 'block'; reportContent.style.opacity = '1'; reportContent.style.pointerEvents = 'auto';
                    btnPrint.style.visibility = 'visible'; btnPrint.style.opacity = '1'; btnPrint.style.pointerEvents = 'auto';
                    resetState();
                })
                .catch(err => {
                    console.error(err); resetState(); loadingEl.style.display = 'none';
                    if (reportContent.innerHTML.trim() === '') emptyState.style.display = 'block';
                    alert('Failed to generate report. Please try again.');
                });
        }
        
        function resetState() {
            btnGenerate.disabled = false; btnGenerate.style.opacity = '1'; btnGenerate.innerText = 'Generate Report';
            btnPrint.disabled = false; btnPrint.style.opacity = '1'; btnPrint.style.pointerEvents = 'auto';
        }

        function buildModalSection(title, desc, tblHtml) {
            var sHtml = '<div class="report-section mb-12 fade-up">';
            sHtml += '<h3 class="font-serif text-xl font-bold text-pine-900 mb-6 border-b border-antique-400/20 pb-4">' + title + '</h3>';
            sHtml += '<div class="no-print glass-card-premium rounded-2xl p-6 mb-6 flex justify-between items-center bg-paper-100/30">';
            sHtml += '<div><div class="text-sm font-bold text-pine-900">Detailed Breakdown</div><div class="text-xs text-pine-700/70 mt-1">' + desc + '</div></div>';
            sHtml += '<button type="button" class="btn-open-modal px-5 py-2.5 rounded-xl bg-antique-400/5 hover:bg-antique-400/10 border border-antique-400/20 text-antique-400 text-[0.65rem] uppercase tracking-widest font-black transition-all hover:scale-105 active:scale-95" data-title="' + escapeHtml(title) + '" data-content="' + encodeURIComponent(tblHtml) + '">View Details</button>';
            sHtml += '</div>';
            sHtml += '<div class="print-only-table">' + tblHtml + '</div>';
            sHtml += '</div>';
            return sHtml;
        }

        function escapeHtml(str) { return (str || '').toString().replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;"); }

        function renderReport(data) {
            var s = data.summary;
            var now = new Date();
            var generatedAt = now.toLocaleDateString() + ' ' + now.toLocaleTimeString();

            var html = '';

            // Print Header
            html += '<div class="print-header" style="display:none;">';
            html += '<img src="../img/logo.png" alt="John Hay Hotels Logo">';
            html += '<h1>Event Feedback Report</h1>';
            html += '<p>Generated: ' + generatedAt + '</p>';
            html += '<p>Period: ' + displayDate(data.date_from) + ' — ' + displayDate(data.date_to) + '</p>';
            html += '</div>';

            // Screen title
            html += '<div class="no-print mb-10 fade-up">';
            html += '<h2 class="font-serif text-3xl font-bold text-pine-900 tracking-tight">Report Summary</h2>';
            html += '<p class="text-antique-400/50 text-[0.65rem] uppercase tracking-[0.2em] font-bold mt-1">' + displayDate(data.date_from) + ' — ' + displayDate(data.date_to) + '</p>';
            html += '</div>';

            // Summary Stats
            html += '<div class="report-section mb-12 fade-up">';
            html += '<h3 class="font-serif text-xl font-bold text-pine-900 mb-6 border-b border-antique-400/20 pb-4">Executive Overview</h3>';
            html += '<div class="grid grid-cols-2 md:grid-cols-5 gap-6 mb-10">';
            html += '<div class="glass-card-premium rounded-2xl p-6 border-paper-200 text-center"><p class="text-3xl font-serif font-bold text-pine-900">' + (s.total_responses || 0) + '</p><p class="text-[0.6rem] font-bold text-pine-700/60 uppercase tracking-widest mt-2">Submissions</p></div>';
            html += '<div class="glass-card-premium rounded-2xl p-6 border-paper-200 text-center"><p class="text-3xl font-serif font-bold ' + scoreClass(s.avg_nps) + '">' + (s.avg_nps !== null ? s.avg_nps : '—') + '</p><p class="text-[0.6rem] font-bold text-pine-700/60 uppercase tracking-widest mt-2">Avg. Satisfaction</p></div>';
            html += '<div class="glass-card-premium rounded-2xl p-6 border-paper-200 text-center"><p class="text-3xl font-serif font-bold ' + (s.poor > 0 ? 'text-red-400' : 'text-pine-700/40') + '">' + (s.poor || 0) + '</p><p class="text-[0.6rem] font-bold text-pine-700/60 uppercase tracking-widest mt-2">Poor (1-2)</p></div>';
            html += '<div class="glass-card-premium rounded-2xl p-6 border-paper-200 text-center"><p class="text-3xl font-serif font-bold ' + (s.good > 0 ? 'text-yellow-400' : 'text-pine-700/40') + '">' + (s.good || 0) + '</p><p class="text-[0.6rem] font-bold text-pine-700/60 uppercase tracking-widest mt-2">Good (3)</p></div>';
            html += '<div class="glass-card-premium rounded-2xl p-6 border-paper-200 text-center"><p class="text-3xl font-serif font-bold ' + (s.excellent > 0 ? 'text-emerald-400' : 'text-pine-700/40') + '">' + (s.excellent || 0) + '</p><p class="text-[0.6rem] font-bold text-pine-700/60 uppercase tracking-widest mt-2">Excellent (4-5)</p></div>';
            html += '</div></div>';

            // Categories
            var catTable = '<table class="report-table w-full text-sm"><thead><tr class="bg-paper-100/30"><th class="px-6 py-4 text-left text-[0.65rem] font-bold text-antique-400/60 uppercase tracking-widest">Category</th><th class="px-6 py-4 text-center text-[0.65rem] font-bold text-antique-400/60 uppercase tracking-widest">Avg Score</th></tr></thead><tbody class="divide-y divide-paper-200">';
            (data.category_averages || []).forEach(function(c) {
                var cls = scoreClass(c.avg);
                catTable += '<tr class="hover:bg-paper-100/30"><td>' + c.label + '</td><td class="text-center font-black ' + cls + '">' + (c.avg > 0 ? c.avg.toFixed(1) + '/5' : 'N/A') + '</td></tr>';
            });
            catTable += '</tbody></table>';
            html += buildModalSection('Category Ratings', 'Average scores mapped across evaluation criteria.', catTable);

            // Event Breakdown
            if (data.event_breakdown && data.event_breakdown.length > 0) {
                var evtTable = '<table class="report-table w-full text-sm"><thead><tr class="bg-paper-100/30"><th class="px-6 py-4 text-left text-[0.65rem] font-bold text-antique-400/60 uppercase tracking-widest">Event Session Name</th><th class="px-6 py-4 text-center text-[0.65rem] font-bold text-antique-400/60 uppercase tracking-widest">Quantity</th></tr></thead><tbody class="divide-y divide-paper-200">';
                data.event_breakdown.forEach(function(e) {
                    evtTable += '<tr class="hover:bg-paper-100/30"><td>' + escapeHtml(e.event_name) + '</td><td class="text-center font-bold text-pine-800">' + e.count + '</td></tr>';
                });
                evtTable += '</tbody></table>';
                html += buildModalSection('Event Breakdown', 'Quantity of feedback submissions categorized by event.', evtTable);
            }

            // Qualitative Feedback
            var validComments = data.comments || [];
            if (validComments.length > 0) {
                html += '<div class="report-section mb-12 fade-up">';
                html += '<h3 class="font-serif text-xl font-bold text-pine-900 mb-6 border-b border-antique-400/20 pb-4">Qualitative Feedback</h3>';
                
                var maxPerPage = 5;
                var totalPages = Math.ceil(validComments.length / maxPerPage);
                
                html += '<div id="comments-wrapper">';
                for (var p = 1; p <= totalPages; p++) {
                    var displayStyle = p === 1 ? 'block' : 'none';
                    html += '<div class="comment-page" id="comment-page-' + p + '" style="display:' + displayStyle + ';">';
                    
                    var startIdx = (p - 1) * maxPerPage;
                    var endIdx = Math.min(startIdx + maxPerPage, validComments.length);
                    
                    for (var i = startIdx; i < endIdx; i++) {
                        var c = validComments[i];
                        html += '<div class="glass-card-premium rounded-2xl p-6 mb-4 border-paper-200 hover:border-antique-400/20 transition-all">';
                        html += '<div class="flex justify-between items-start mb-4 border-b border-paper-200 pb-3">';
                        html += '<div><span class="text-sm font-bold text-pine-900">' + escapeHtml(c.guest_name || 'Anonymous') + '</span><span class="text-pine-700/40 text-xs mx-2">/</span><span class="text-pine-700/70 text-xs font-medium">' + escapeHtml(c.event || 'Unknown Event') + '</span></div>';
                        html += '<div class="text-[0.65rem] font-black text-antique-400 uppercase tracking-tighter">Rating: <span class="' + scoreClass(c.overall_rating) + '">' + (c.overall_rating || '—') + '/5</span></div>';
                        html += '</div>';
                        
                        if (c.effective) html += '<div class="text-xs mb-3 text-pine-700 leading-relaxed"><strong class="text-antique-400/80 mr-1 uppercase tracking-widest text-[0.6rem]">Effective:</strong> ' + escapeHtml(c.effective).replace(/\n/g, '<br>') + '</div>';
                        if (c.improvement) html += '<div class="text-xs mb-3 text-pine-700 leading-relaxed"><strong class="text-antique-400/80 mr-1 uppercase tracking-widest text-[0.6rem]">Suggestions:</strong> ' + escapeHtml(c.improvement).replace(/\n/g, '<br>') + '</div>';
                        if (c.additional) html += '<div class="text-xs text-pine-700 leading-relaxed"><strong class="text-antique-400/80 mr-1 uppercase tracking-widest text-[0.6rem]">Context:</strong> ' + escapeHtml(c.additional).replace(/\n/g, '<br>') + '</div>';
                        html += '<div class="text-right mt-3"><span class="text-[0.55rem] font-bold text-pine-700/20 uppercase tracking-[0.2em]">' + c.created_at.substring(0, 10) + '</span></div>';
                        html += '</div>';
                    }
                    html += '</div>';
                }
                html += '</div>';
                
                if (totalPages > 1) {
                    html += '<div class="no-print mt-8 flex items-center justify-between border-t border-paper-200 pt-6">';
                    html += '<div class="text-[0.65rem] font-bold text-pine-700/60 uppercase tracking-widest">Showing page <span id="comments-current-page" class="text-antique-400">1</span> of ' + totalPages + '</div>';
                    html += '<div class="flex gap-3">';
                    html += '<button id="btn-prev-comments" class="px-4 py-2 rounded-xl bg-paper-100/50 hover:bg-paper-100/50 text-pine-700 text-xs font-bold uppercase tracking-widest disabled:opacity-30 disabled:pointer-events-none transition-all border border-paper-200" disabled>Prev</button>';
                    html += '<button id="btn-next-comments" class="px-4 py-2 rounded-xl border border-antique-400/20 bg-antique-400/10 hover:bg-antique-400/20 text-antique-400 text-xs font-bold uppercase tracking-widest transition-all">Next</button>';
                    html += '</div>';
                    html += '</div>';
                }
                html += '</div>';

                // Pagination logic
                setTimeout(() => {
                    var cp = 1;
                    var pgText = document.getElementById('comments-current-page');
                    var bPrev = document.getElementById('btn-prev-comments');
                    var bNext = document.getElementById('btn-next-comments');
                    
                    if(bPrev && bNext) {
                        const updateP = () => {
                            for(var p=1; p<=totalPages; p++) document.getElementById('comment-page-'+p).style.display = (p===cp)?'block':'none';
                            pgText.innerText = cp;
                            bPrev.disabled = (cp === 1);
                            bNext.disabled = (cp === totalPages);
                        };
                        bPrev.onclick = () => { if(cp>1){ cp--; updateP(); } };
                        bNext.onclick = () => { if(cp<totalPages){ cp++; updateP(); } };
                    }
                }, 100);
            }

            // Print Footer
            html += '<div class="print-footer text-center pt-20 text-[8pt] text-black/20 hidden">Confidential Feedback Report — ' + generatedAt + '</div>';

            reportContent.innerHTML = html;
            
            // Modal Logic
            var modal = document.getElementById('reportModal');
            var modalContainer = document.getElementById('modalContainer');
            var modalTitle = document.getElementById('modalTitle');
            var modalContent = document.getElementById('modalContent');
            var closeBtn = document.getElementById('closeModalBtn');
            
            const closeModal = () => {
                modalContainer.style.opacity = '0';
                modalContainer.style.transform = 'translateY(1rem)';
                setTimeout(() => {
                    modal.style.display = 'none';
                    document.body.style.overflow = '';
                }, 300);
            };

            closeBtn.onclick = closeModal;
            modal.onclick = (e) => { if(e.target === modal) closeModal(); };
            
            document.querySelectorAll('.btn-open-modal').forEach(btn => {
                btn.onclick = function() {
                    modalTitle.innerHTML = this.getAttribute('data-title');
                    modalContent.innerHTML = decodeURIComponent(this.getAttribute('data-content'));
                    modal.style.display = 'flex';
                    setTimeout(() => {
                        modalContainer.style.opacity = '1';
                        modalContainer.style.transform = 'translateY(0)';
                    }, 10);
                    document.body.style.overflow = 'hidden';
                };
            });
        }


        // Init preset
        setPreset('month');
        generateReport(dateFrom.value, dateTo.value);

    })();
    </script>
</body>
</html>
