@echo off
rem Dijalankan Windows Task Scheduler tiap 1 menit (spesifikasi teknis bagian 11.1).
rem Memproses antrean analisis (prk_analisis_job) -- tanpa ini, pengajuan baru
rem menumpuk di status 'antre' dan tidak pernah dianalisis otomatis.
cd /d "C:\Cindy\intern\e_prakerin\prakerin"
"C:\tools\php82\php.exe" index.php cli_analisis antrean >> "C:\Cindy\intern\e_prakerin\prakerin\scripts\cli_analisis_antrean.log" 2>&1
