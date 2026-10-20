<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<style>
    .kpi-row { display: flex; gap: 16px; flex-wrap: wrap; }
    .kpi-card { background: #fff; border: 1px solid var(--abu-200); border-radius: 12px; padding: 20px; flex: 1; min-width: 180px; }
    .kpi-top { display: flex; align-items: center; justify-content: space-between; }
    .kpi-label { color: var(--abu-600); font-size: 13px; }
    .kpi-icon { background: var(--hijau-100); border-radius: 8px; padding: 8px; display: flex; }
    .kpi-value { font-size: 32px; font-weight: 700; margin-top: 12px; }
    .kpi-note { color: var(--abu-600); font-size: 12px; margin-top: 4px; }
    table.riwayat { width: 100%; border-collapse: collapse; font-size: 12px; }
    table.riwayat th { text-align: left; color: var(--abu-600); font-weight: 600; padding: 8px 10px; border-bottom: 1px solid var(--abu-200); font-size: 11px; text-transform: uppercase; }
    table.riwayat td { padding: 12px 10px; border-bottom: 1px solid var(--abu-100); vertical-align: top; }
    .tabel-scroll { width: 100%; overflow-x: auto; }
    .btn-primary { background: var(--hijau-700); color: #fff; border: none; border-radius: 8px; padding: 10px 18px; font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-block; }
    .btn-primary:disabled { background: var(--abu-100); color: var(--abu-400); cursor: not-allowed; }
    .btn-outline { background: #fff; border: 1px solid var(--abu-200); color: var(--abu-900); border-radius: 8px; padding: 10px 18px; font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-block; }
    .btn-bahaya { background: var(--merah-700); color: #fff; border: none; border-radius: 8px; padding: 10px 18px; font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-block; }
    .field-label { font-size: 12px; font-weight: 600; margin-bottom: 6px; display: block; }
    .field-input, .field-select, .field-textarea { width: 100%; box-sizing: border-box; border: 1px solid var(--abu-200); border-radius: 8px; padding: 9px 12px; font-size: 12px; font-family: inherit; height: 36px; background: #fff; color: var(--abu-900); }
    .field-input:focus, .field-select:focus, .field-textarea:focus { outline: none; border-color: var(--hijau-700); box-shadow: 0 0 0 3px var(--hijau-100); }
    .field-textarea { min-height: 70px; height: auto; resize: vertical; }
    .field-select { appearance: none; -webkit-appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%23667a73' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 10px center; padding-right: 32px; cursor: pointer; }
    .field-input[type="date"] { cursor: pointer; }
    .field-input[type="date"]::-webkit-calendar-picker-indicator { cursor: pointer; opacity: .6; }

    /* ---- input dengan ikon platform di kiri (link sosmed) ---- */
    .field-icon-wrap { position: relative; }
    .field-icon-wrap .field-icon { position: absolute; left: 11px; top: 50%; transform: translateY(-50%); color: var(--abu-600); display: flex; pointer-events: none; }
    .field-icon-wrap .field-input { padding-left: 36px; }
    .field-note { font-size: 11px; color: var(--abu-600); margin: 4px 0 0; }
    .field-error { font-size: 11px; color: var(--merah-700); margin: 4px 0 0; }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
    .empty-state { text-align: center; font-size: 12px; color: var(--abu-600); padding: 32px 24px; }
    .stepper { display: flex; gap: 10px; margin-bottom: 20px; }
    .stepper-item { display: flex; align-items: center; gap: 8px; font-size: 12px; font-weight: 600; color: var(--abu-600); }
    .stepper-num { width: 28px; height: 28px; border-radius: 100px; background: var(--abu-100); color: var(--abu-600); display: flex; align-items: center; justify-content: center; font-size: 12px; }
    .stepper-item.aktif .stepper-num { background: var(--hijau-700); color: #fff; }
    .stepper-item.aktif { color: var(--abu-900); }
    .stepper-item.selesai .stepper-num { background: var(--hijau-100); color: var(--hijau-700); }
    .berkas-kartu { display: flex; align-items: center; gap: 12px; border: 1px solid var(--abu-200); border-radius: 10px; padding: 12px 14px; margin-bottom: 10px; }
    .berkas-kartu-icon { background: var(--hijau-100); border-radius: 8px; padding: 8px; display: flex; }
    .berkas-kartu-nama { font-size: 12px; font-weight: 600; }
    .berkas-kartu-status { font-size: 11px; color: var(--abu-600); }
    .berkas-kartu-status.ok { color: var(--hijau-700); }
    .timeline { list-style: none; margin: 0; padding: 0; }
    .timeline li { display: flex; gap: 10px; padding-bottom: 18px; position: relative; }
    .timeline li:not(:last-child)::before { content: ''; position: absolute; left: 9px; top: 20px; bottom: 0; width: 1px; background: var(--abu-200); }
    .timeline-dot { width: 20px; height: 20px; border-radius: 100px; background: var(--abu-100); border: 2px solid var(--abu-200); flex-shrink: 0; margin-top: 2px; }
    .timeline li.selesai .timeline-dot { background: var(--hijau-700); border-color: var(--hijau-700); }
    .timeline-judul { font-size: 12px; font-weight: 600; }
    .timeline-sub { font-size: 11px; color: var(--abu-600); }

    /* ---- modal konfirmasi custom (pengganti confirm() bawaan browser) ---- */
    .modal-overlay {
        position: fixed; inset: 0; background: rgba(23,45,39,.45); z-index: 100;
        display: flex; align-items: center; justify-content: center; padding: 20px;
        opacity: 0; transition: opacity .15s ease;
    }
    .modal-overlay.tampil { opacity: 1; }
    .modal-kartu {
        background: #fff; border-radius: 12px; padding: 24px; max-width: 360px; width: 100%;
        box-shadow: 0 8px 24px rgba(0,0,0,.18); transform: translateY(8px); transition: transform .15s ease;
    }
    .modal-overlay.tampil .modal-kartu { transform: translateY(0); }
    .modal-kartu h3 { font-size: 16px; font-weight: 700; color: var(--abu-900); margin: 0; }
    .modal-kartu .footer-aksi { display: flex; gap: 10px; }
    .modal-kartu .footer-aksi button { flex: 1; text-align: center; }
</style>
