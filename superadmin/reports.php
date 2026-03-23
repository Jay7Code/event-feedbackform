<?php
/**
 * ═══════════════════════════════════════════════════════════════
 * ADMIN PRINTABLE REPORTS
 * Event Feedback System
 * ═══════════════════════════════════════════════════════════════
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
    <title>Reports - Super Admin</title>
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
                        serif: ['"Playfair Display"','serif'],
                        sans: ['"Inter"','sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { background: #0f1333; }
        .glass-card { background: rgba(255,255,255,0.05); backdrop-filter: blur(40px); -webkit-backdrop-filter: blur(40px); border: 1px solid rgba(255,255,255,0.10); box-shadow: 0 8px 32px rgba(0,0,0,0.3); }
        .lodge-input { padding: 10px 14px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.15); background: rgba(255,255,255,0.05); color: rgba(255,255,255,0.9); font-family: 'Inter', sans-serif; font-size: 0.8rem; transition: all 0.3s ease; outline: none; }
        .lodge-input:focus { border-color: #C9A96E; background: rgba(255,255,255,0.1); }
        .lodge-input::placeholder { color: rgba(255,255,255,0.3); }
        select.lodge-input option { background-color: #0f1333; color: #ffffff; }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: rgba(15,19,51,0.5); }
        ::-webkit-scrollbar-thumb { background: rgba(201,169,110,0.4); border-radius: 99px; }

        @keyframes fadeUp { 0% { opacity: 0; transform: translateY(20px); } 100% { opacity: 1; transform: translateY(0); } }
        .fade-up { opacity: 0; animation: fadeUp 0.5s ease-out forwards; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .spinner { width: 32px; height: 32px; border: 3px solid rgba(201,169,110,0.2); border-top-color: #C9A96E; border-radius: 50%; animation: spin 0.8s linear infinite; }

        /* ═══ PRINT STYLES (adapted from feedback-form theme) ═══ */
        @media print {
            * { color: #000 !important; background: #fff !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            body { background: #fff !important; font-size: 10pt; font-family: 'Inter', sans-serif; }
            nav, .no-print, footer, #controlsPanel { display: none !important; }
            .glass-card { border: 1px solid #ddd !important; backdrop-filter: none !important; box-shadow: none !important; }
            #reportContent { display: block !important; }
            .print-header { display: block !important; text-align: center; margin-bottom: 30px; }
            .print-header h1 { font-family: 'Playfair Display', serif; font-size: 18pt; color: #1e2a6e !important; margin: 0 0 10px 0; font-weight: 700; letter-spacing: 0.05em; }
            .print-header p { font-size: 10pt; color: #666 !important; margin: 4px 0; }
            .report-table { width: 100%; border-collapse: collapse; margin: 10px 0; }
            .report-table th { background: #1e2a6e !important; color: #fff !important; padding: 8px 12px; text-align: left; font-size: 9pt; text-transform: uppercase; letter-spacing: 0.05em; border: 1px solid #1e2a6e; }
            .report-table td { padding: 6px 12px; border: 1px solid #eee; font-size: 9pt; }
            .report-table tr:nth-child(even) td { background: #f9f9f9 !important; }
            .report-section { page-break-inside: avoid; margin-bottom: 25px; }
            .report-section h3 { font-family: 'Playfair Display', serif; font-size: 14pt; color: #1e2a6e !important; border-bottom: 2px solid #C9A96E; padding-bottom: 8px; margin-bottom: 12px; text-align: left; font-weight: 700; }
            .stat-box { display: inline-block; width: 23%; text-align: center; padding: 12px; border: 1px solid #ddd; border-radius: 8px; margin: 0 0.5%; }
            .stat-box .stat-val { font-size: 22pt; font-weight: 700; color: #1e2a6e !important; }
            .stat-box .stat-label { font-size: 8pt; text-transform: uppercase; letter-spacing: 0.05em; color: #999 !important; }
            .score-excellent { color: #059669 !important; font-weight: 600; }
            .score-good { color: #16a34a !important; font-weight: 600; }
            .score-poor { color: #dc2626 !important; font-weight: 600; }
            .comment-card { border: 1px solid #eee; padding: 10px 14px; margin-bottom: 10px; border-radius: 6px; page-break-inside: avoid; }
            .comment-card .guest-info { font-size: 8pt; color: #888 !important; border-bottom: 1px solid #eee; padding-bottom: 4px; margin-bottom: 6px; }
            .comment-card .comment-text { font-size: 10pt; font-style: italic; color: #333 !important; }
            .print-footer { text-align: center; font-size: 8pt; color: #aaa !important; margin-top: 40px; padding-top: 15px; border-top: 1px solid #ddd; }
            .print-only-table { display: block !important; margin-top: 15px; }
            #reportModal { display: none !important; }
        }

        /* ═══ SCREEN-ONLY REPORT STYLES ═══ */
        @media screen {
            .print-header { display: none; }
            .report-table { width: 100%; border-collapse: separate; border-spacing: 0; }
            .report-table th { background: rgba(201,169,110,0.15); color: rgba(201,169,110,0.9); padding: 12px 16px; text-align: left; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.15em; font-weight: 700; border-bottom: 1px solid rgba(201,169,110,0.2); }
            .report-table td { padding: 10px 16px; border-bottom: 1px solid rgba(255,255,255,0.06); color: rgba(255,255,255,0.7); font-size: 1rem; }
            .report-table tr:hover td { background: rgba(255,255,255,0.03); }
            .report-section h3 { font-family: 'Playfair Display', serif; color: #C9A96E; font-size: 1.4rem; letter-spacing: 0.1em; text-transform: uppercase; font-weight: 600;
                border-bottom: 2px solid #C9A96E; border-image: linear-gradient(to right, rgba(201,169,110,0.8), rgba(201,169,110,0)) 1; padding-bottom: 12px; margin-bottom: 24px; text-align: left; }
            .stat-box { text-align: center; padding: 20px; background: rgba(255,255,255,0.03); border: 1px solid rgba(201,169,110,0.15); border-radius: 12px; }
            .stat-box .stat-val { font-size: 2rem; font-weight: 700; color: rgba(255,255,255,0.9); }
            .stat-box .stat-label { font-size: 0.6rem; text-transform: uppercase; letter-spacing: 0.15em; color: rgba(201,169,110,0.7); font-weight: 600; margin-top: 6px; }
            .score-excellent { color: #10b981; font-weight: 600; }
            .score-good { color: #fbbf24; font-weight: 600; }
            .score-poor { color: #ef4444; font-weight: 600; }

            .comment-card { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); padding: 16px 20px; margin-bottom: 12px; border-radius: 12px; }
            .comment-card .guest-info { font-size: 0.75rem; color: rgba(201,169,110,0.6); border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 8px; margin-bottom: 10px; }
            .comment-card .comment-text { font-size: 1.05rem; font-style: italic; color: rgba(255,255,255,0.6); line-height: 1.6; }
            .print-footer { display: none; }
            .print-only-table { display: none; }

            /* Modal Styles */
            .modal-backdrop { position: fixed; inset: 0; background: rgba(15,19,51,0.85); backdrop-filter: blur(8px); z-index: 9999; display: flex; justify-content: center; align-items: flex-start; opacity: 0; pointer-events: none; transition: all 0.3s ease; padding: 3rem 1rem; overflow-y: auto; }
            .modal-backdrop.active { opacity: 1; pointer-events: auto; }
            .modal-container { background: #0f1333; border: 1px solid rgba(201,169,110,0.3); border-radius: 16px; width: 100%; max-width: 800px; transform: translateY(-20px) scale(0.95); transition: all 0.3s ease; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.8); display: flex; flex-direction: column; overflow: hidden; }
            .modal-backdrop.active .modal-container { transform: translateY(0) scale(1); }
            .modal-header { padding: 20px 24px; border-bottom: 1px solid rgba(255,255,255,0.08); display: flex; justify-content: space-between; align-items: center; background: rgba(255,255,255,0.02); }
            .modal-body { padding: 24px; max-height: 70vh; overflow-y: auto; }
        }
    </style>
</head>
<body class="font-sans text-white min-h-screen">

    <!-- Nav -->
    <nav class="border-b border-white/10 bg-black/20 backdrop-blur-sm sticky top-0 z-50 no-print">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <div>
                <h1 class="font-serif text-xl font-bold text-gold-400">Super Admin Panel</h1>
                <p class="text-white/40 text-[0.65rem] uppercase tracking-widest">Event Feedback System</p>
            </div>
            <div class="flex items-center gap-4">
                <a href="index.php" class="text-sm text-white/50 hover:text-white transition-colors">Manage Admins</a>
                <a href="analytics.php" class="text-sm text-white/50 hover:text-white transition-colors">Analytics</a>
                <a href="reports.php" class="text-sm text-gold-400 hover:text-gold-300 transition-colors font-medium border-b border-gold-400 pb-1">Reports</a>
                <span class="text-white/20">|</span>
                <a href="logout.php" class="text-sm text-red-400 hover:text-red-300 transition-colors">Logout</a>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-6 py-8">
        <!-- Controls -->
        <div class="glass-card rounded-2xl p-6 mb-6 fade-up" style="animation-delay:0.1s" id="controlsPanel">
            <div class="flex flex-wrap items-end justify-between gap-6">
                <!-- Inputs -->
                <div class="flex flex-wrap items-end gap-5">
                    <div class="flex flex-col" style="min-width: 150px;">
                        <label class="block text-[0.65rem] font-bold text-gold-400 uppercase tracking-widest mb-2 whitespace-nowrap">Quick Presets</label>
                        <select id="presetSelect" class="lodge-input w-full px-3 py-2 cursor-pointer">
                            <option value="today">Daily (Today)</option>
                            <option value="yesterday">Yesterday</option>
                            <option value="week">Weekly (7 Days)</option>
                            <option value="month">Monthly (30 Days)</option>
                            <option value="year">Yearly</option>
                            <option value="all">All Time</option>
                            <option value="custom" hidden>Custom Date</option>
                        </select>
                    </div>
                    <div class="flex flex-col" style="min-width: 140px;">
                        <label class="block text-[0.65rem] font-bold text-gold-400 uppercase tracking-widest mb-2 whitespace-nowrap">From</label>
                        <input type="date" id="dateFrom" class="lodge-input w-full px-3 py-2">
                    </div>
                    <div class="flex flex-col" style="min-width: 140px;">
                        <label class="block text-[0.65rem] font-bold text-gold-400 uppercase tracking-widest mb-2 whitespace-nowrap">To</label>
                        <input type="date" id="dateTo" class="lodge-input w-full px-3 py-2">
                    </div>
                </div>
                <!-- Buttons -->
                <div class="flex flex-wrap items-end gap-3 lg:ml-auto">
                    <button id="btnGenerate" class="px-6 py-2.5 rounded-xl font-bold text-sm uppercase tracking-wider transition-all duration-300 hover:shadow-lg hover:-translate-y-0.5" style="background:linear-gradient(135deg,#C9A96E,#b5893a);color:#0f1333;">
                        Generate Report
                    </button>
                    <button id="btnPrint" class="px-6 py-2.5 rounded-xl font-bold text-sm uppercase tracking-wider border border-gold-400/30 text-gold-400 hover:bg-gold-400/10 transition-all flex items-center gap-2" style="visibility:hidden; opacity:0; pointer-events:none;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        Print
                    </button>
                </div>
            </div>
        </div>

        <div id="loadingReport" class="no-print text-center py-16" style="display:none">
            <div class="spinner mx-auto mb-4"></div>
            <p class="text-white/40 text-sm animate-pulse">Generating comprehensive report...</p>
        </div>

        <div id="emptyState" class="no-print glass-card rounded-2xl p-16 text-center fade-up" style="animation-delay:0.2s">
            <div class="w-20 h-20 rounded-full bg-gold-400/10 flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-gold-400/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <h3 class="font-serif text-2xl text-white/80 mb-2">Select a Report Period</h3>
            <p class="text-white/40 text-sm max-w-md mx-auto leading-relaxed">Choose a preset or date range and generate the report to view the printable feedback summary.</p>
        </div>

        <div id="reportContent" style="display:none"></div>
    </div>

    <!-- Data Modal -->
    <div id="reportModal" class="modal-backdrop no-print">
        <div class="modal-container text-white">
            <div class="modal-header">
                <h3 id="modalTitle" class="font-serif text-xl text-gold-400">Details</h3>
                <button type="button" id="closeModalBtn" class="text-white/40 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div id="modalContent" class="modal-body overflow-x-auto text-sm"></div>
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

            fetch('../admin/api/report_data.php?date_from=' + encodeURIComponent(from) + '&date_to=' + encodeURIComponent(to))
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
            var sHtml = '<div class="report-section">';
            sHtml += '<h3>' + title + '</h3>';
            sHtml += '<div class="no-print stat-box" style="padding:16px 20px; text-align:left; display:flex; justify-content:space-between; align-items:center;">';
            sHtml += '<div><div style="font-size:1.1rem;color:rgba(255,255,255,0.9);font-weight:700;">Detailed Breakdown</div><div style="font-size:0.85rem;color:rgba(255,255,255,0.5); margin-top:4px;">' + desc + '</div></div>';
            sHtml += '<button type="button" class="btn-open-modal px-5 py-2.5 rounded-xl bg-gold-400/10 hover:bg-gold-400/20 border border-gold-400/30 text-gold-400 text-xs uppercase tracking-wider font-bold transition-all hover:scale-105 active:scale-95" data-title="' + escapeHtml(title) + '" data-content="' + encodeURIComponent(tblHtml) + '">View Details</button>';
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
            html += '<div class="print-header">';
            html += '<img src="../img/logo.png" alt="John Hay Hotels Logo" style="height:60px; display:block; margin: 0 auto 15px auto;">';
            html += '<h1>Event Feedback Report</h1>';
            html += '<p>Generated: ' + generatedAt + '</p>';
            html += '<p>Period: ' + displayDate(data.date_from) + ' — ' + displayDate(data.date_to) + '</p>';
            html += '</div>';

            // Screen title
            html += '<div class="no-print mb-8">';
            html += '<h3 class="font-serif text-2xl text-white/90 tracking-wide">Report: ' + displayDate(data.date_from) + ' — ' + displayDate(data.date_to) + '</h3>';
            html += '</div>';

            // Summary Stats
            html += '<div class="report-section">';
            html += '<h3>Summary Overview</h3>';
            html += '<div style="display:flex;gap:16px;flex-wrap:wrap;margin-bottom:24px">';
            html += '<div class="stat-box" style="flex:1;min-width:140px"><div class="stat-val">' + (s.total_responses || 0) + '</div><div class="stat-label">Total Submissions</div></div>';
            html += '<div class="stat-box" style="flex:1;min-width:140px"><div class="stat-val ' + scoreClass(s.avg_nps) + '">' + (s.avg_nps !== null ? s.avg_nps : '—') + '<span style="font-size:0.5em;opacity:0.4">/5</span></div><div class="stat-label">Avg. Satisfaction</div></div>';
            html += '<div class="stat-box" style="flex:1;min-width:140px"><div class="stat-val ' + (s.poor > 0 ? 'score-poor' : '') + '">' + (s.poor || 0) + '</div><div class="stat-label">Poor (1-2)</div></div>';
            html += '<div class="stat-box" style="flex:1;min-width:140px"><div class="stat-val ' + (s.good > 0 ? 'score-good' : '') + '">' + (s.good || 0) + '</div><div class="stat-label">Good (3)</div></div>';
            html += '<div class="stat-box" style="flex:1;min-width:140px"><div class="stat-val ' + (s.excellent > 0 ? 'score-excellent' : '') + '">' + (s.excellent || 0) + '</div><div class="stat-label">Excellent (4-5)</div></div>';
            html += '</div></div>';

            // Categories
            var catTable = '<table class="report-table"><thead><tr><th>Category</th><th style="text-align:center">Avg Score</th></tr></thead><tbody>';
            (data.category_averages || []).forEach(function(c) {
                var cls = scoreClass(c.avg);
                catTable += '<tr><td>' + c.label + '</td><td style="text-align:center" class="' + cls + '">' + (c.avg > 0 ? c.avg.toFixed(1) + '/5' : 'N/A') + '</td></tr>';
            });
            catTable += '</tbody></table>';
            html += buildModalSection('Category Ratings', 'Average scores mapped across evaluation criteria.', catTable);

            // Event Breakdown
            if (data.event_breakdown && data.event_breakdown.length > 0) {
                var evtTable = '<table class="report-table"><thead><tr><th>Event Name</th><th style="text-align:center">Responses</th></tr></thead><tbody>';
                data.event_breakdown.forEach(function(e) {
                    evtTable += '<tr><td>' + escapeHtml(e.event_name) + '</td><td style="text-align:center">' + e.count + '</td></tr>';
                });
                evtTable += '</tbody></table>';
                html += buildModalSection('Event Breakdown', 'Quantity of feedback submissions categorized by event.', evtTable);
            }

            // Qualitative Feedback
            var validComments = data.comments || [];
            if (validComments.length > 0) {
                html += '<div class="report-section">';
                html += '<h3>Qualitative Open-Ended Feedback</h3>';
                
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
                        html += '<div class="comment-card">';
                        html += '<div class="guest-info">' + escapeHtml(c.guest_name || 'Anonymous') + ' — ' + escapeHtml(c.event || 'Unknown Event') + ' — Rating: <span class="' + scoreClass(c.overall_rating) + '">' + (c.overall_rating || '—') + '/5</span> — ' + c.created_at.substring(0, 10) + '</div>';
                        
                        if (c.effective) html += '<div class="comment-text mb-2"><strong style="color:rgba(201,169,110,0.9)">What was effective:</strong><br>' + escapeHtml(c.effective).replace(/\n/g, '<br>') + '</div>';
                        if (c.improvement) html += '<div class="comment-text mb-2"><strong style="color:rgba(201,169,110,0.9)">Suggestions:</strong><br>' + escapeHtml(c.improvement).replace(/\n/g, '<br>') + '</div>';
                        if (c.additional) html += '<div class="comment-text"><strong style="color:rgba(201,169,110,0.9)">Additional context:</strong><br>' + escapeHtml(c.additional).replace(/\n/g, '<br>') + '</div>';
                        html += '</div>';
                    }
                    html += '</div>';
                }
                html += '</div>';
                
                if (totalPages > 1) {
                    html += '<div class="no-print mt-6 flex items-center justify-between border-t border-white/5 pt-4">';
                    html += '<div class="text-white/40 text-xs uppercase tracking-widest">Showing page <span id="comments-current-page" class="text-gold-400 font-bold">1</span> of ' + totalPages + '</div>';
                    html += '<div class="flex gap-2">';
                    html += '<button id="btn-prev-comments" class="px-3 py-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-white/70 text-xs uppercase tracking-wider font-semibold disabled:opacity-30 disabled:pointer-events-none transition-colors" disabled>Prev</button>';
                    html += '<button id="btn-next-comments" class="px-3 py-1.5 rounded-lg border border-gold-400/20 bg-gold-400/10 hover:bg-gold-400/20 text-gold-400 text-xs uppercase tracking-wider font-semibold transition-colors">Next</button>';
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
            html += '<div class="print-footer">';
            html += 'Confidential Review Report — ' + generatedAt;
            html += '</div>';

            reportContent.innerHTML = html;
            
            // Modal Logic
            var modal = document.getElementById('reportModal');
            var modalTitle = document.getElementById('modalTitle');
            var modalContent = document.getElementById('modalContent');
            var closeBtn = document.getElementById('closeModalBtn');
            
            closeBtn.onclick = () => { modal.classList.remove('active'); document.body.style.overflow = ''; };
            modal.onclick = (e) => { if(e.target === modal) closeBtn.onclick(); };
            
            document.querySelectorAll('.btn-open-modal').forEach(btn => {
                btn.onclick = function() {
                    modalTitle.innerHTML = this.getAttribute('data-title');
                    modalContent.innerHTML = decodeURIComponent(this.getAttribute('data-content'));
                    modal.classList.add('active');
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
