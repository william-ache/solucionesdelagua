<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Soluciones del Agua - ERP</title>
    <!-- Dark Mode Anti-Flash Script -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <!-- Favicon Gota de Agua -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%2322b0ea'%3E%3Cpath d='M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z'/%3E%3C/svg%3E">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            blue: '#005293',
                            light: '#22B0EA'
                        }
                    }
                }
            }
        }
    </script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- ExcelJS and FileSaver para exportaciones Premium -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            window.exportToExcel = async function(data, fields, headers, filename) {
                if (!data || data.length === 0) {
                    Swal.fire({ icon: 'warning', title: 'Sin registros', text: 'No hay datos disponibles para exportar.', confirmButtonColor: '#005293' });
                    return;
                }
                
                Swal.fire({ title: 'Generando Excel', text: 'Aplicando formato corporativo y resolviendo columnas...', allowOutsideClick: false, didOpen: () => { Swal.showLoading() } });

                try {
                    const workbook = new ExcelJS.Workbook();
                    workbook.creator = 'Soluciones del Agua ERP';
                    workbook.created = new Date();
                    const worksheet = workbook.addWorksheet('Reporte', {views:[{showGridLines: false}]});
                    
                    // Add Title
                    worksheet.mergeCells('A1', String.fromCharCode(65 + headers.length - 1) + '1');
                    const titleCell = worksheet.getCell('A1');
                    titleCell.value = 'SICA - Soluciones del Agua';
                    titleCell.font = { name: 'Arial', size: 16, bold: true, color: { argb: 'FFFFFFFF' } };
                    titleCell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF005293' } }; // Brand Blue
                    titleCell.alignment = { vertical: 'middle', horizontal: 'center' };
                    worksheet.getRow(1).height = 30;

                    // Add Subtitle
                    worksheet.mergeCells('A2', String.fromCharCode(65 + headers.length - 1) + '2');
                    const subtitleCell = worksheet.getCell('A2');
                    const dateStr = new Date().toLocaleDateString('es-ES');
                    subtitleCell.value = `Reporte: ${filename} | Generado el: ${dateStr} | Total Registros: ${data.length}`;
                    subtitleCell.font = { name: 'Arial', size: 10, italic: true, color: { argb: 'FF555555' } };
                    subtitleCell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFEEEEEE' } }; // Light Gray
                    subtitleCell.alignment = { vertical: 'middle', horizontal: 'center' };
                    worksheet.getRow(2).height = 20;

                    // Add Headers
                    const headerRow = worksheet.getRow(4);
                    headers.forEach((header, index) => {
                        const cell = headerRow.getCell(index + 1);
                        cell.value = header;
                        cell.font = { name: 'Arial', size: 11, bold: true, color: { argb: 'FF005293' } };
                        cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFF1F5F9' } };
                        cell.border = { bottom: { style: 'medium', color: { argb: 'FFCBD5E1' } } };
                        cell.alignment = { vertical: 'middle', horizontal: 'left' };
                    });
                    
                    // Add Data
                    data.forEach((item, rowIndex) => {
                        const row = worksheet.getRow(5 + rowIndex);
                        fields.forEach((field, colIndex) => {
                            let val = field.split('.').reduce((acc, part) => acc && acc[part], item);
                            if (val === null || val === undefined) val = "";
                            if (typeof val === 'object' && val.name) val = val.name;
                            
                            const cell = row.getCell(colIndex + 1);
                            cell.value = val;
                            cell.font = { name: 'Arial', size: 10, color: { argb: 'FF4A5568' } };
                            cell.border = { bottom: { style: 'thin', color: { argb: 'FFEDF2F7' } } };
                            
                            // Styling for numbers
                            if (typeof val === 'number') {
                                cell.numFmt = '#,##0.00';
                                cell.alignment = { horizontal: 'left' };
                            } else {
                                cell.alignment = { horizontal: 'left' };
                            }
                        });
                    });

                    // Auto-fit Columns width intelligently
                    worksheet.columns.forEach(column => {
                        let maxLength = 0;
                        column["eachCell"]({ includeEmpty: true }, function(cell) {
                            var columnLength = cell.value ? cell.value.toString().length : 10;
                            if (columnLength > maxLength) {
                                maxLength = columnLength;
                            }
                        });
                        column.width = Math.min(maxLength < 15 ? 15 : maxLength + 2, 60);
                    });

                    // Generate File directly bypassing browser blob blocking
                    const buffer = await workbook.xlsx.writeBuffer();
                    const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                    window.saveAs(blob, `${filename}_${new Date().toISOString().slice(0,10)}.xlsx`);
                    
                    Swal.close();
                    window.Toast.fire({ icon: 'success', title: 'Excel generado impecablemente' });
                } catch (error) {
                    console.error('Excel Export Error:', error);
                    Swal.fire({ icon: 'error', title: 'Error interno', text: 'Ocurrió un problema al procesar el archivo Excel.' });
                }
            };

            window.exportToPDF = function(data, fields, headers, title) {
                if (!data || data.length === 0) {
                    Swal.fire({ icon: 'warning', title: 'Sin registros', text: 'No hay datos disponibles para exportar.', confirmButtonColor: '#005293' });
                    return;
                }
                const printWindow = window.open("", "_blank");
                if (!printWindow) {
                    Swal.fire({ icon: 'error', title: 'Bloqueador de Ventanas Emergentes', text: 'Por favor permite ventanas emergentes para poder generar el PDF.', confirmButtonColor: '#005293' });
                    return;
                }
                let tableRows = "";
                data.forEach(item => {
                    tableRows += "<tr>";
                    fields.forEach(field => {
                        let val = field.split('.').reduce((acc, part) => acc && acc[part], item);
                        if (val === null || val === undefined) val = "-";
                        if (typeof val === 'number') {
                            val = val.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        } else if (typeof val === 'object' && val.name) {
                            val = val.name;
                        }
                        tableRows += `<td style="padding: 10px 12px; border-bottom: 1px solid #edf2f7; font-size: 11px; color: #4a5568;">${val}</td>`;
                    });
                    tableRows += "</tr>";
                });
                let headersColumns = headers.map(h => `<th style="padding: 12px; border-bottom: 2px solid #e2e8f0; font-size: 11px; text-align: left; background-color: #f8fafc; color: #2d3748; font-weight: bold; text-transform: uppercase;">${h}</th>`).join("");
                const htmlContent = `
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <title>Vista Previa: ${title}</title>
                        <meta charset="utf-8">
                        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
                        <style>
                            body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #2d3748; padding: 0; margin: 0; background-color: #f1f5f9; }
                            
                            /* Floating Control Bar for screen only */
                            .control-bar {
                                position: fixed;
                                top: 0;
                                left: 0;
                                right: 0;
                                height: 60px;
                                background: linear-gradient(135deg, #005293, #003666);
                                color: white;
                                display: flex;
                                items-center: center;
                                justify-content: space-between;
                                padding: 0 30px;
                                box-shadow: 0 4px 10px rgba(0,0,0,0.15);
                                z-index: 1000;
                                display: flex;
                                align-items: center;
                            }
                            .control-bar h1 {
                                font-size: 15px;
                                margin: 0;
                                font-weight: 700;
                                display: flex;
                                align-items: center;
                                gap: 8px;
                            }
                            .btn-group {
                                display: flex;
                                align-items: center;
                                gap: 12px;
                            }
                            .btn {
                                background-color: rgba(255, 255, 255, 0.12);
                                border: 1px solid rgba(255, 255, 255, 0.2);
                                color: white;
                                padding: 7px 15px;
                                font-size: 11px;
                                font-weight: 700;
                                border-radius: 6px;
                                cursor: pointer;
                                transition: all 0.2s ease;
                                display: flex;
                                align-items: center;
                                gap: 6px;
                                outline: none;
                            }
                            .btn:hover {
                                background-color: white;
                                color: #005293;
                                box-shadow: 0 4px 6px rgba(0,0,0,0.10);
                            }
                            .btn-primary {
                                background-color: #10b981;
                                border-color: #059669;
                            }
                            .btn-primary:hover {
                                background-color: #34d399;
                                color: white;
                            }
                            
                            /* Preview Frame on page */
                            .document-container {
                                margin-top: 60px;
                                padding: 40px 20px;
                                display: flex;
                                justify-content: center;
                                min-height: calc(100vh - 60px);
                                box-sizing: border-box;
                            }
                            .printable-sheet {
                                position: relative;
                                background-color: white;
                                width: 820px;
                                min-height: 1050px;
                                padding: 50px;
                                box-shadow: 0 10px 25px rgba(0,0,0,0.06);
                                border-radius: 8px;
                                box-sizing: border-box;
                                border: 1px solid #e2e8f0;
                                display: flex;
                                flex-direction: column;
                                justify-content: space-between;
                                -webkit-print-color-adjust: exact;
                                print-color-adjust: exact;
                            }
                            .printable-sheet::before {
                                content: '';
                                position: absolute;
                                top: 50%;
                                left: 50%;
                                transform: translate(-50%, -50%);
                                width: 80%;
                                height: 80%;
                                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23005293'%3E%3Cpath d='M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z'/%3E%3C/svg%3E");
                                background-repeat: no-repeat;
                                background-position: center;
                                opacity: 0.03;
                                z-index: 0;
                                pointer-events: none;
                            }
                            .printable-sheet > * {
                                position: relative;
                                z-index: 1;
                            }
                            
                            .header { display: flex; align-items: center; justify-content: space-between; border-bottom: 3px solid #005293; padding-bottom: 15px; margin-bottom: 25px; }
                            .title { font-size: 20px; font-weight: bold; color: #005293; margin: 0; }
                            .meta { font-size: 11px; color: #718096; text-align: right; line-height: 1.4; }
                            table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                            .footer { margin-top: 50px; font-size: 9px; color: #a0aec0; text-align: center; border-top: 1px solid #edf2f7; padding-top: 15px; }

                            /* Media Print Rules: Hides controls entirely when generated */
                            @media print {
                                body { background-color: white; }
                                .control-bar { display: none !important; }
                                .document-container { margin-top: 0 !important; padding: 0 !important; display: block !important; }
                                .printable-sheet { 
                                    width: 100% !important; 
                                    box-shadow: none !important; 
                                    border: none !important; 
                                    padding: 0 !important; 
                                    border-radius: 0 !important; 
                                }
                            }   
                        </style>
                    </head>
                    <body>
                        <div class="control-bar">
                            <h1><i class="fa-solid fa-file-pdf"></i> Vista Previa de Impresión</h1>
                            <div class="btn-group">
                                <button class="btn btn-primary" onclick="window.print()"><i class="fa-solid fa-print"></i> Guardar como PDF / Imprimir</button>
                                <button class="btn" onclick="window.close()"><i class="fa-solid fa-xmark"></i> Cerrar Vista Previa</button>
                            </div>
                        </div>
                        
                        <div class="document-container">
                            <div class="printable-sheet">
                                <div>
                                    <div class="header">
                                        <div>
                                            <h2 class="title">SOLUCIONES DEL AGUA</h2>
                                            <span style="font-size: 10px; color: #718096; font-weight: 500;">Sistema Integrado de Control Administrativo (SICA)</span>
                                        </div>
                                        <div class="meta">
                                            <strong>Reporte:</strong> ${title}<br>
                                            <strong>Fecha Emisión:</strong> ${new Date().toLocaleDateString('es-ES')}<br>
                                            <strong>Registros:</strong> ${data.length}
                                        </div>
                                    </div>
                                    <table>
                                        <thead>
                                            <tr>${headersColumns}</tr>
                                        </thead>
                                        <tbody>
                                            ${tableRows}
                                        </tbody>
                                    </table>
                                </div>
                                <div class="footer">
                                    © ${new Date().getFullYear()} Soluciones del Agua. Documento confidencial generado de forma automatizada y procesado en memoria.
                                </div>
                            </div>
                        </div>
                    </body>
                    </html>
                `;
                printWindow.document.write(htmlContent);
                printWindow.document.close();
            };

            // Dynamic Modal Active Stacking Listener (Centralized MutationObserver)
            const checkActiveModals = () => {
                if (window.modalObserver) {
                    window.modalObserver.disconnect();
                }

                const activeModal = document.querySelector('.fixed.backdrop-blur-sm:not([style*="display: none"]), .fixed.bg-black\\/60:not([style*="display: none"]), .fixed.bg-black\\/50:not([style*="display: none"])');
                const isModalOpen = !!activeModal;
                const header = document.querySelector('header');
                const main = document.querySelector('main');
                
                if (header) {
                    if (isModalOpen) {
                        if (!header.classList.contains('z-10')) {
                            header.classList.remove('z-30');
                            header.classList.add('z-10');
                        }
                    } else {
                        if (!header.classList.contains('z-30')) {
                            header.classList.remove('z-10');
                            header.classList.add('z-30');
                        }
                    }
                }
                if (main) {
                    if (isModalOpen) {
                        if (!main.classList.contains('z-[40]')) {
                            main.classList.remove('z-10');
                            main.classList.add('relative', 'z-[40]');
                        }
                    } else {
                        if (main.classList.contains('z-[40]')) {
                            main.classList.remove('z-[40]');
                            main.classList.add('relative');
                        }
                    }
                }

                if (window.modalObserver) {
                    window.modalObserver.observe(document.body, { attributes: true, subtree: true, attributeFilter: ['style', 'class'] });
                }
            };

            window.modalObserver = new MutationObserver(checkActiveModals);
            window.modalObserver.observe(document.body, { attributes: true, subtree: true, attributeFilter: ['style', 'class'] });
            
            // Safe fallback event observers for immediate execution on click & keypress triggers
            window.addEventListener('click', () => setTimeout(checkActiveModals, 30));
            window.addEventListener('keydown', () => setTimeout(checkActiveModals, 30));
        });
    </script>
    <style>
        [x-cloak] { display: none !important; }

        /* High-fidelity global modal positioning override for premium display */
        .fixed.inset-0.backdrop-blur-sm:not([style*="display: none"]) {
            align-items: flex-start !important;
            padding-top: 4.5rem !important; /* Raised slightly closer to top (72px) for premium height */
            padding-bottom: 2rem !important;
            overflow-y: auto !important;
        }
        
        .fixed.inset-0.backdrop-blur-sm:not([style*="display: none"]) > div {
            margin-top: 0.5rem !important;
            margin-bottom: 0.5rem !important;
            max-height: none !important; /* Eliminates redundant nested scrolling bars */
        }
        
        /* Smooth scrolling globally */
        html, body, .overflow-y-auto, .overflow-auto, main, aside, nav {
            scroll-behavior: smooth !important;
        }

        /* Beautiful Slim & Brand-Colored Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 9999px;
        }

        ::-webkit-scrollbar-thumb {
            background: #0d47a1; /* Brand Blue */
            border-radius: 9999px;
            border: 1px solid #f1f5f9;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #00b0ff; /* Brand Light Blue */
        }

        /* Firefox Support */
        * {
            scrollbar-width: thin;
            scrollbar-color: #0d47a1 #f1f5f9;
        }

        /* Dynamic Dark Mode overrides */
        .dark body {
            background-color: #0f172a !important; /* slate-900 */
            color: #f8fafc !important; /* slate-50 */
        }
        .dark header {
            background-color: #1e293b !important; /* slate-800 */
            border-bottom: 1px solid #334155 !important; /* slate-700 */
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.2) !important;
        }
        .dark header i, .dark header span, .dark header button {
            color: #e2e8f0 !important;
        }
        .dark header input {
            background-color: #334155 !important;
            border-color: #475569 !important;
            color: #f8fafc !important;
        }
        .dark header input::placeholder {
            color: #94a3b8 !important;
        }
        .dark .bg-white {
            background-color: #1e293b !important; /* slate-800 */
            color: #f8fafc !important;
        }
        .dark .border-gray-150, .dark .border-gray-100, .dark .border-gray-200 {
            border-color: #334155 !important; /* slate-700 */
        }
        .dark .border-x, .dark .border-y, .dark .border-t, .dark .border-b {
            border-color: #334155 !important;
        }
        .dark select, .dark input, .dark textarea {
            background-color: #1e293b !important;
            color: #f8fafc !important;
            border-color: #475569 !important;
        }
        .dark select option {
            background-color: #1e293b !important;
            color: #f8fafc !important;
        }
        .dark text-gray-800, .dark .text-gray-800, .dark .text-gray-700, .dark .text-gray-600 {
            color: #e2e8f0 !important; /* slate-200 */
        }
        .dark .text-gray-500, .dark .text-gray-400 {
            color: #94a3b8 !important; /* slate-400 */
        }
        .dark .bg-gray-50, .dark .bg-gray-50\/50, .dark .bg-gray-50\/30 {
            background-color: #0f172a !important; /* slate-900 */
        }
        .dark .hover\:bg-gray-50:hover, .dark .hover\:bg-gray-100:hover, .dark .hover\:bg-gray-50\/50:hover {
            background-color: #334155 !important;
        }
        .dark .divide-y > :not([hidden]) ~ :not([hidden]) {
            border-color: #334155 !important;
        }
        /* Keep buttons text white */
        .dark .text-white {
            color: #ffffff !important;
        }
        .dark .bg-green-50, .dark .bg-green-100 {
            background-color: #064e3b !important;
            color: #34d399 !important;
            border-color: #047857 !important;
        }
        .dark .text-green-700, .dark .text-green-600 {
            color: #34d399 !important;
        }
        .dark .bg-red-50, .dark .bg-red-100 {
            background-color: #7f1d1d !important;
            color: #fca5a5 !important;
            border-color: #b91c1c !important;
        }
        .dark .text-red-700, .dark .text-red-650 {
            color: #fca5a5 !important;
        }
        .dark td {
            color: #cbd5e1 !important; /* slate-350 */
        }
        .dark th {
            color: #ffffff !important;
        }
        .dark .bg-brand-blue {
            background-color: #00457c !important; 
        }
        .dark .shadow-2xl, .dark .shadow-xl {
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5) !important;
        }
        .dark ::-webkit-scrollbar-track {
            background: #1e293b;
        }
        .dark ::-webkit-scrollbar-thumb {
            background: #1e40af;
            border: 1px solid #1e293b;
        }
        .dark * {
            scrollbar-color: #1e40af #1e293b;
        }

        /* High-fidelity Water Droplet Drip Animation */
        @keyframes waterDripDismount {
            0% {
                transform: translateY(-16px) scaleY(1);
                opacity: 0;
            }
            15% {
                opacity: 0.8;
            }
            85% {
                opacity: 0.8;
                transform: translateY(34px) scaleY(1.4);
            }
            100% {
                transform: translateY(38px) scaleY(0.1) scaleX(2.5);
                opacity: 0;
            }
        }
        .water-drip {
            animation: waterDripDismount 2s infinite linear;
            display: inline-block;
            filter: drop-shadow(0 1px 2px rgba(34, 176, 234, 0.45));
        }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased" 
      x-data="{ 
          sidebarOpen: false, 
          sidebarCollapsed: false, 
          currentTime: '', 
          bcvRate: 'Cargando...',
          bcvDate: '',
          theme: localStorage.getItem('theme') || 'light',
          toggleTheme() {
              this.theme = this.theme === 'light' ? 'dark' : 'light';
              localStorage.setItem('theme', this.theme);
              if (this.theme === 'dark') {
                  document.documentElement.classList.add('dark');
              } else {
                  document.documentElement.classList.remove('dark');
              }
          },
          // Global Detail Modal State and Actions
          detailModalOpen: false,
          detailLoading: false,
          detailData: null,
          openDetailModal(model, id) {
              this.detailLoading = true;
              this.detailModalOpen = true;
              this.detailData = null;
              
              fetch('/global-detail?model=' + encodeURIComponent(model) + '&id=' + encodeURIComponent(id))
                  .then(res => res.json())
                  .then(data => {
                      this.detailData = data;
                      this.detailLoading = false;
                  })
                  .catch(err => {
                      console.error(err);
                      this.detailLoading = false;
                      this.detailModalOpen = false;
                      window.Toast.fire({ icon: 'error', title: 'Error al cargar detalles.' });
                  });
          },
          updateTime() {
              const now = new Date();
              this.currentTime = now.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
          },
          fetchBCV() {
              fetch('https://ve.dolarapi.com/v1/dolares/oficial')
                  .then(res => res.json())
                  .then(data => {
                      this.bcvRate = data.promedio ? parseFloat(data.promedio).toFixed(2) : 'N/D';
                      if (data.fechaActualizacion) {
                          const dateObj = new Date(data.fechaActualizacion);
                          const dateStr = dateObj.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit' });
                          const timeStr = dateObj.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit', hour12: true });
                          this.bcvDate = `${dateStr} - ${timeStr}`;
                      }
                  })
                  .catch(err => {
                      this.bcvRate = 'Error';
                      console.error('Error fetching BCV rate:', err);
                  });
          },
          init() {
              this.updateTime();
              setInterval(() => this.updateTime(), 1000);
              this.fetchBCV();
              // Update rate every 15 minutes
              setInterval(() => this.fetchBCV(), 900000);

              // Custom global listener to catch Ver details requests
              window.addEventListener('open-global-detail', (e) => {
                  if (e.detail && e.detail.model && e.detail.id) {
                      this.openDetailModal(e.detail.model, e.detail.id);
                  }
              });
          }
      }">

    <div class="flex h-screen overflow-hidden">

        <!-- Sidebar -->
        <aside class="flex-shrink-0 bg-brand-blue text-white flex flex-col transition-all duration-300 md:translate-x-0 absolute md:relative z-40 h-full shadow-xl"
               :class="{
                   'translate-x-0': sidebarOpen, 
                   '-translate-x-full': !sidebarOpen,
                   'w-64 md:w-64': !sidebarCollapsed,
                   'w-64 md:w-20': sidebarCollapsed
               }">
               
            <!-- Desktop Sidebar Edge Toggle Button -->
            <button @click="sidebarCollapsed = !sidebarCollapsed"
                    class="hidden md:flex absolute -right-3 top-5 bg-white text-brand-blue w-6 h-6 rounded-full items-center justify-center border border-gray-200 shadow-md transition-transform z-50 hover:bg-gray-50 focus:outline-none"
                    title="Alternar Menú Lateral">
                <i class="fa-solid fa-chevron-left text-[10px] transition-transform duration-300" :class="sidebarCollapsed ? 'rotate-180' : ''"></i>
            </button>

            <div class="flex items-center bg-brand-blue shadow-md h-16 shrink-0 border-b border-blue-800 transition-all duration-300"
                 :class="sidebarCollapsed ? 'md:p-3 md:justify-center p-4 justify-between' : 'p-4 justify-between'">
                <span class="text-lg font-bold flex items-center gap-2" :class="sidebarCollapsed ? 'md:justify-center' : ''">
                    <div class="w-10 h-10 rounded-full bg-white overflow-hidden p-0.5 flex-shrink-0 flex items-center justify-center border border-blue-900/40 shadow-inner">
                        <img src="{{ asset('logo.jpg') }}" alt="Logo" class="w-full h-full object-cover rounded-full scale-[1.10]" id="sidebar-logo" onerror="this.style.display='none'; document.getElementById('sidebar-water-fallback').classList.remove('hidden'); document.getElementById('sidebar-water-fallback').classList.add('flex');">
                        <span id="sidebar-water-fallback" class="hidden items-center justify-center text-brand-light text-sm font-bold"><i class="fa-solid fa-water"></i></span>
                    </div>
                    <span :class="sidebarCollapsed ? 'md:hidden' : 'inline'" class="font-sans transition-all duration-300">SDA MYC - ERP</span>
                </span>
                <button @click="sidebarOpen = false" class="md:hidden text-white hover:text-brand-light">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
            
            <nav class="flex-1 overflow-y-auto py-4 transition-all duration-300">
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('dashboard') }}" 
                           class="flex items-center gap-3 py-3 transition-all duration-200 {{ Route::is('dashboard') ? 'bg-white/10 border-l-4 border-brand-light relative overflow-hidden font-bold' : 'text-gray-200 border-l-4 border-transparent hover:bg-white/5 hover:border-brand-light' }}"
                           :class="sidebarCollapsed ? 'md:px-0 md:justify-center px-6' : 'px-6'"
                           title="Dashboard Panel">
                            <i class="fa-solid fa-chart-pie w-5 text-center flex-shrink-0 relative z-10" :class="sidebarCollapsed ? 'text-lg' : ''"></i>
                            <span :class="sidebarCollapsed ? 'md:hidden' : 'inline'" class="font-sans text-sm font-medium relative z-10">Dashboard Panel</span>
                            @if (Route::is('dashboard'))
                                <!-- Subtle animated background for active item -->
                                <div class="absolute inset-0 overflow-hidden pointer-events-none z-0">
                                    <span class="water-drip w-[2px] h-[7px] bg-brand-light/75 absolute left-[25%] rounded-full"></span>
                                    <span class="water-drip w-[2.5px] h-[9px] bg-brand-light/60 absolute left-[55%] rounded-full" style="animation-delay: 0.7s; animation-duration: 2.3s;"></span>
                                    <span class="water-drip w-[2px] h-[6px] bg-brand-light/85 absolute left-[75%] rounded-full" style="animation-delay: 1.4s; animation-duration: 1.7s;"></span>
                                </div>
                            @endif
                        </a>
                    </li>
                    <!-- MENÚ AGRUPADO: DIRECTORIO COMERCIAL -->
                    <li x-data="{ openDirectorio: {{ Route::is('clients.*') || Route::is('suppliers.*') ? 'true' : 'false' }} }">
                        <button @click="openDirectorio = !openDirectorio; if(sidebarCollapsed) sidebarCollapsed = false" 
                                class="w-full flex items-center justify-between py-3 transition-all duration-200 text-gray-200 hover:bg-white/5"
                                :class="sidebarCollapsed ? 'md:px-0 md:justify-center px-6' : 'px-6'"
                                title="Directorio Comercial">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-address-book w-5 text-center flex-shrink-0" :class="sidebarCollapsed ? 'text-lg' : ''"></i>
                                <span :class="sidebarCollapsed ? 'md:hidden' : 'inline'" class="font-sans text-sm font-medium">Directorio Comercial</span>
                            </div>
                            <i class="fa-solid fa-chevron-down text-xs transition-transform duration-300" :class="openDirectorio ? 'rotate-180' : ''" x-show="!sidebarCollapsed"></i>
                        </button>
                        
                        <ul x-show="openDirectorio" x-collapse.duration.300ms class="bg-blue-950/30 border-y border-white/5 py-1" :class="sidebarCollapsed ? 'hidden' : 'block'" x-cloak>
                            <li>
                                <a href="{{ route('clients.index') }}" 
                                   class="flex items-center gap-3 py-2.5 transition-all duration-200 {{ Route::is('clients.*') ? 'bg-white/10 border-l-4 border-brand-light relative overflow-hidden font-bold' : 'text-gray-200 border-l-4 border-transparent hover:bg-white/5 hover:border-brand-light' }} pl-11"
                                   title="Clientes">
                                    <i class="fa-solid fa-users w-4 text-center flex-shrink-0 relative z-10 text-xs text-brand-light"></i>
                                    <span class="font-sans text-[13px] font-medium relative z-10">Clientes</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('suppliers.index') }}" 
                                   class="flex items-center gap-3 py-2.5 transition-all duration-200 {{ Route::is('suppliers.*') ? 'bg-white/10 border-l-4 border-brand-light relative overflow-hidden font-bold' : 'text-gray-200 border-l-4 border-transparent hover:bg-white/5 hover:border-brand-light' }} pl-11"
                                   title="Proveedores">
                                    <i class="fa-solid fa-truck-fast w-4 text-center flex-shrink-0 relative z-10 text-xs text-brand-light"></i>
                                    <span class="font-sans text-[13px] font-medium relative z-10">Proveedores</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <!-- MENÚ AGRUPADO: GESTIÓN -->
                    <li x-data="{ openGestion: {{ Route::is('sales.*') || Route::is('credits.*') || Route::is('tax-payments.*') || Route::is('employees.*') || Route::is('payroll-payments.*') || Route::is('expenses.*') || Route::is('products.*') ? 'true' : 'false' }} }">
                        <button @click="openGestion = !openGestion; if(sidebarCollapsed) sidebarCollapsed = false" 
                                class="w-full flex items-center justify-between py-3 transition-all duration-200 text-gray-200 hover:bg-white/5"
                                :class="sidebarCollapsed ? 'md:px-0 md:justify-center px-6' : 'px-6'"
                                title="Gestión">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-briefcase w-5 text-center flex-shrink-0" :class="sidebarCollapsed ? 'text-lg' : ''"></i>
                                <span :class="sidebarCollapsed ? 'md:hidden' : 'inline'" class="font-sans text-sm font-medium">Gestión Operativa</span>
                            </div>
                            <i class="fa-solid fa-chevron-down text-xs transition-transform duration-300" :class="openGestion ? 'rotate-180' : ''" x-show="!sidebarCollapsed"></i>
                        </button>
                        
                        <ul x-show="openGestion" x-collapse.duration.300ms class="bg-blue-950/30 border-y border-white/5 py-1" :class="sidebarCollapsed ? 'hidden' : 'block'" x-cloak>
                            <li>
                                <a href="{{ route('products.index') }}" 
                                   class="flex items-center gap-3 py-2.5 transition-all duration-200 {{ Route::is('products.*') ? 'bg-white/10 border-l-4 border-brand-light relative overflow-hidden font-bold' : 'text-gray-200 border-l-4 border-transparent hover:bg-white/5 hover:border-brand-light' }} pl-11"
                                   title="Inventario">
                                    <i class="fa-solid fa-boxes-stacked w-4 text-center flex-shrink-0 relative z-10 text-xs text-brand-light"></i>
                                    <span class="font-sans text-[13px] font-medium relative z-10">Inventario</span>
                                </a>
                            </li>
                                <a href="{{ route('sales.index') }}" 
                                   class="flex items-center gap-3 py-2.5 transition-all duration-200 {{ Route::is('sales.*') ? 'bg-white/10 border-l-4 border-brand-light relative overflow-hidden font-bold' : 'text-gray-200 border-l-4 border-transparent hover:bg-white/5 hover:border-brand-light' }} pl-11"
                                   title="Ventas">
                                    <i class="fa-solid fa-cart-shopping w-4 text-center flex-shrink-0 relative z-10 text-xs text-brand-light"></i>
                                    <span class="font-sans text-[13px] font-medium relative z-10">Ventas</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('credits.index') }}" 
                                   class="flex items-center gap-3 py-2.5 transition-all duration-200 {{ Route::is('credits.*') ? 'bg-white/10 border-l-4 border-brand-light relative overflow-hidden font-bold' : 'text-gray-200 border-l-4 border-transparent hover:bg-white/5 hover:border-brand-light' }} pl-11"
                                   title="Créditos">
                                    <i class="fa-solid fa-hand-holding-dollar w-4 text-center flex-shrink-0 relative z-10 text-xs text-brand-light"></i>
                                    <span class="font-sans text-[13px] font-medium relative z-10">Créditos</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('tax-payments.index') }}" 
                                   class="flex items-center gap-3 py-2.5 transition-all duration-200 {{ Route::is('tax-payments.*') ? 'bg-white/10 border-l-4 border-brand-light relative overflow-hidden font-bold' : 'text-gray-200 border-l-4 border-transparent hover:bg-white/5 hover:border-brand-light' }} pl-11"
                                   title="Impuestos">
                                    <i class="fa-solid fa-percent w-4 text-center flex-shrink-0 relative z-10 text-xs text-brand-light"></i>
                                    <span class="font-sans text-[13px] font-medium relative z-10">Impuestos</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('employees.index') }}" 
                                   class="flex items-center gap-3 py-2.5 transition-all duration-200 {{ Route::is('employees.*') || Route::is('payroll-payments.*') ? 'bg-white/10 border-l-4 border-brand-light relative overflow-hidden font-bold' : 'text-gray-200 border-l-4 border-transparent hover:bg-white/5 hover:border-brand-light' }} pl-11"
                                   title="Nómina">
                                    <i class="fa-solid fa-user-tie w-4 text-center flex-shrink-0 relative z-10 text-xs text-brand-light"></i>
                                    <span class="font-sans text-[13px] font-medium relative z-10">Nómina</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('expenses.index') }}" 
                                   class="flex items-center gap-3 py-2.5 transition-all duration-200 {{ Route::is('expenses.*') ? 'bg-white/10 border-l-4 border-brand-light relative overflow-hidden font-bold' : 'text-gray-200 border-l-4 border-transparent hover:bg-white/5 hover:border-brand-light' }} pl-11"
                                   title="Gastos Operativos">
                                    <i class="fa-solid fa-wallet w-4 text-center flex-shrink-0 relative z-10 text-xs text-brand-light"></i>
                                    <span class="font-sans text-[13px] font-medium relative z-10">Gastos Operativos</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="{{ route('cash-closures.history') }}" 
                           class="flex items-center gap-3 py-3 transition-all duration-200 {{ Route::is('cash-closures.*') ? 'bg-white/10 border-l-4 border-brand-light relative overflow-hidden font-bold' : 'text-gray-200 border-l-4 border-transparent hover:bg-white/5 hover:border-brand-light' }}"
                           :class="sidebarCollapsed ? 'md:px-0 md:justify-center px-6' : 'px-6'"
                           title="Cierre de Caja">
                            <i class="fa-solid fa-cash-register w-5 text-center flex-shrink-0 relative z-10" :class="sidebarCollapsed ? 'text-lg' : ''"></i>
                            <span :class="sidebarCollapsed ? 'md:hidden' : 'inline'" class="font-sans text-sm font-medium relative z-10">Cierre de Caja</span>
                            @if (Route::is('cash-closures.*'))
                                <div class="absolute inset-0 overflow-hidden pointer-events-none z-0">
                                    <span class="water-drip w-[2px] h-[7px] bg-brand-light/75 absolute left-[25%] rounded-full"></span>
                                    <span class="water-drip w-[2.5px] h-[9px] bg-brand-light/60 absolute left-[55%] rounded-full" style="animation-delay: 0.7s; animation-duration: 2.3s;"></span>
                                    <span class="water-drip w-[2px] h-[6px] bg-brand-light/85 absolute left-[75%] rounded-full" style="animation-delay: 1.4s; animation-duration: 1.7s;"></span>
                                </div>
                            @endif
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('system-logs.index') }}" 
                           class="flex items-center gap-3 py-3 transition-all duration-200 {{ Route::is('system-logs.*') ? 'bg-white/10 border-l-4 border-brand-light relative overflow-hidden font-bold' : 'text-gray-200 border-l-4 border-transparent hover:bg-white/5 hover:border-brand-light' }}"
                           :class="sidebarCollapsed ? 'md:px-0 md:justify-center px-6' : 'px-6'"
                           title="Bitácora (Logs)">
                            <i class="fa-solid fa-clipboard-list w-5 text-center flex-shrink-0 relative z-10" :class="sidebarCollapsed ? 'text-lg' : ''"></i>
                            <span :class="sidebarCollapsed ? 'md:hidden' : 'inline'" class="font-sans text-sm font-medium relative z-10">Bitácora</span>
                            @if (Route::is('system-logs.*'))
                                <div class="absolute inset-0 overflow-hidden pointer-events-none z-0">
                                    <span class="water-drip w-[2px] h-[7px] bg-brand-light/75 absolute left-[25%] rounded-full"></span>
                                    <span class="water-drip w-[2.5px] h-[9px] bg-brand-light/60 absolute left-[55%] rounded-full" style="animation-delay: 0.7s; animation-duration: 2.3s;"></span>
                                    <span class="water-drip w-[2px] h-[6px] bg-brand-light/85 absolute left-[75%] rounded-full" style="animation-delay: 1.4s; animation-duration: 1.7s;"></span>
                                </div>
                            @endif
                        </a>
                    </li>
                </ul>
            </nav>
            
            <!-- Sidebar Bottom Info Panel -->
            <div :class="sidebarCollapsed ? 'md:hidden' : 'block'" class="p-4 border-t border-blue-800 bg-blue-950/20 flex flex-col gap-2.5 shrink-0 select-none">
                <!-- BCV Exchange Rate Card -->
                <div x-data="{ showCardDetails: false }" class="bg-blue-900/40 rounded-lg p-2.5 border border-blue-700/30 flex flex-col gap-1.5 select-none transition-all duration-300">
                    <div class="flex items-center justify-between text-xs text-blue-200">
                        <span class="font-bold flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span> Dólar BCV
                        </span>
                        <button @click="showCardDetails = !showCardDetails" class="text-blue-400 hover:text-white transition-transform outline-none" :class="showCardDetails ? 'rotate-180' : ''" title="Alternar Vista">
                            <i class="fa-solid fa-chevron-down text-[10px]"></i>
                        </button>
                    </div>
                    
                    <div class="flex items-end justify-between mt-0.5 transition-all duration-300" :class="showCardDetails ? 'border-b border-blue-800/30 pb-1.5' : 'pb-0'">
                        <span class="text-xs text-gray-400">Tasa Oficial</span>
                        <div class="flex items-center gap-1.5 shadow-sm">
                            <span class="text-sm font-black text-brand-light font-mono leading-none animate-fade-in" x-text="'Bs. ' + bcvRate"></span>
                            <button @click="navigator.clipboard.writeText(bcvRate); window.Toast.fire({ icon: 'success', title: 'Tasa copiada: Bs. ' + bcvRate })" 
                                    class="text-[11px] text-blue-300 hover:text-brand-light hover:scale-105 active:scale-95 transition-all outline-none" 
                                    title="Copiar Tasa">
                                <i class="fa-regular fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div x-show="showCardDetails" x-transition.opacity.duration.200ms class="flex flex-col gap-1.5">
                        <div class="flex items-center justify-between text-[9px] text-gray-400 border-b border-blue-800/20 pb-1.5">
                            <span>Actualización</span>
                            <span class="font-medium opacity-80" x-text="bcvDate"></span>
                        </div>
                        <!-- Dynamic Live Hora Local -->
                        <div class="flex items-center justify-between text-[10px] text-blue-200">
                            <span class="font-semibold uppercase tracking-wider text-blue-300/80"><i class="fa-solid fa-clock mr-1"></i> Hora Local</span>
                            <span class="font-bold font-mono text-white text-xs" x-text="currentTime"></span>
                        </div>
                        <!-- Version -->
                        <div class="flex items-center justify-between text-[10px] text-blue-200/60 border-t border-blue-900/30 pt-1.5 mt-1.5">
                            <span class="font-semibold uppercase tracking-wider text-blue-300/60"><i class="fa-solid fa-code-branch mr-1"></i> Versión</span>
                            <span class="font-bold font-mono text-blue-300 text-[10px]">v{{ config('app.version', '1.0.0') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col pt-0 relative overflow-hidden">
            <!-- Navbar -->
            <header class="bg-white shadow flex items-center justify-between p-4 h-16 z-30 w-full shrink-0">
                <div class="flex items-center gap-4">
                    <!-- Mobile Button -->
                    <button @click="sidebarOpen = true" class="md:hidden text-gray-500 hover:text-brand-blue">
                        <i class="fa-solid fa-bars text-xl"></i>
                    </button>
                    <!-- Desktop Sidebar Collapsible Toggle Button (Moved to Sidebar Edge) 
                    <button @click="sidebarCollapsed = !sidebarCollapsed" class="hidden md:flex text-gray-500 hover:text-brand-blue bg-gray-50 hover:bg-gray-100 p-2 rounded-lg transition-colors items-center justify-center border border-gray-200 shadow-sm" title="Alternar menú">
                        <i class="fa-solid fa-bars text-sm"></i>
                    </button>
                    -->
                </div>
                
                <!-- Global Omni-Search Bar -->
                <div class="flex-1 max-w-xs sm:max-w-sm md:max-w-md lg:max-w-lg mx-4 relative hidden sm:block" x-data="{
                    searchQuery: '',
                    results: [],
                    loading: false,
                    showDropdown: false,
                    search() {
                        if (this.searchQuery.trim().length < 1) {
                            this.results = [];
                            this.showDropdown = false;
                            return;
                        }
                        this.loading = true;
                        this.showDropdown = true;
                        fetch('/global-search?q=' + encodeURIComponent(this.searchQuery))
                            .then(res => res.json())
                            .then(data => {
                                this.results = data;
                                this.loading = false;
                            })
                            .catch(err => {
                                console.error(err);
                                this.loading = false;
                            });
                    }
                }">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                        </span>
                        <input type="text"
                                x-model="searchQuery"
                                @input.debounce.300ms="search()"
                                @focus="showDropdown = true"
                                @click.away="showDropdown = false"
                                @keydown.escape.window="showDropdown = false"
                                placeholder="Buscar clientes, ventas, créditos, nómina..."
                                class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-brand-light focus:border-transparent bg-gray-50/50 hover:bg-gray-50 transition-all font-sans text-gray-700">
                        
                        <!-- Dropdown de Resultados -->
                        <div x-show="showDropdown && (results.length > 0 || loading || (searchQuery.trim().length >= 1 && !loading))"
                             x-transition
                             class="absolute left-0 mt-2 w-full bg-white rounded-lg shadow-xl border border-gray-150 py-1.5 z-50 max-h-96 overflow-y-auto"
                             x-cloak>
                            
                            <!-- Indicador de Carga -->
                            <div x-show="loading" class="px-4 py-3 text-xs text-gray-500 flex items-center gap-2">
                                <i class="fa-solid fa-spinner animate-spin text-brand-blue"></i> Buscando coincidencias...
                            </div>
                            
                            <!-- Sin Resultados -->
                            <div x-show="!loading && results.length === 0 && searchQuery.trim().length >= 1" class="px-4 py-3.5 text-xs text-gray-400 flex items-center gap-2">
                                <i class="fa-solid fa-face-frown text-sm"></i> No se encontraron resultados para "<span class="font-semibold text-gray-600" x-text="searchQuery"></span>"
                            </div>
                            
                            <!-- Lista de Resultados -->
                            <div x-show="!loading && results.length > 0">
                                <template x-for="item in results">
                                    <a href="#" @click.prevent="openDetailModal(item.model, item.id); showDropdown = false" class="flex items-start gap-3 px-4 py-2.5 hover:bg-gray-50 transition-colors border-b border-gray-50 last:border-0">
                                        <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-gray-100/80 text-gray-600 flex items-center justify-center mt-0.5 text-xs">
                                            <i :class="item.icon"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="text-xs font-bold text-gray-800 truncate" x-text="item.title"></span>
                                                <span class="text-[8px] font-black uppercase px-2 py-0.5 rounded-full tracking-wider flex-shrink-0 border"
                                                      :class="{
                                                          'bg-emerald-50 text-emerald-700 border-emerald-200/50': item.type === 'Cliente',
                                                          'bg-red-50 text-red-700 border-red-200/50': item.type === 'Crédito / Cuentas por Cobrar',
                                                          'bg-sky-50 text-sky-700 border-sky-200/50': item.type === 'Venta / Facturación',
                                                          'bg-indigo-50 text-indigo-700 border-indigo-200/50': item.type === 'Colaborador / Nómina',
                                                          'bg-orange-50 text-orange-700 border-orange-200/50': item.type === 'Gasto Operativo',
                                                          'bg-amber-50 text-amber-700 border-amber-200/50': item.type === 'Impuestos / Fiscos'
                                                      }"
                                                      x-text="item.type"></span>
                                            </div>
                                            <span class="text-[10px] text-gray-500 truncate block mt-0.5" x-text="item.subtitle"></span>
                                        </div>
                                    </a>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="ml-auto flex items-center gap-3 relative">
                    <!-- Modern Minimalist Theme Toggle Switcher 
                    <button @click="toggleTheme()"
                            class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer items-center rounded-full bg-gray-200 dark:bg-slate-700 transition-colors duration-300 focus:outline-none select-none shadow-inner border border-gray-300 dark:border-slate-600 p-0.5"
                            title="Alternar Modo Claro / Oscuro">
                        <span class="sr-only">Tema</span>
                        <span class="absolute left-1.5 flex items-center justify-center text-[10px] text-amber-400 pointer-events-none transition-opacity duration-300"
                              :class="theme === 'light' ? 'opacity-100' : 'opacity-30'">
                            <i class="fa-solid fa-sun"></i>
                        </span>
                        <span class="absolute right-1.5 flex items-center justify-center text-[10px] text-indigo-300 pointer-events-none transition-opacity duration-300"
                              :class="theme === 'dark' ? 'opacity-100' : 'opacity-30'">
                            <i class="fa-solid fa-moon"></i>
                        </span>
                        <span class="pointer-events-none block h-5 w-5 rounded-full bg-white dark:bg-brand-light shadow-md ring-0 transform transition-transform duration-300 ease-out"
                              :class="theme === 'dark' ? 'translate-x-5' : 'translate-x-0'">
                        </span>
                    </button>
                    -->

                    <div x-data="{ profileDropdownOpen: false }" class="relative">
                        <div @click="profileDropdownOpen = !profileDropdownOpen" class="flex items-center gap-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800 p-1.5 rounded-lg transition-colors select-none">
                            <div class="w-8 h-8 rounded-full bg-brand-blue text-white flex items-center justify-center font-bold font-sans">
                                A
                            </div>
                            <span class="text-sm font-medium text-gray-700 hidden md:inline-block">Admin</span>
                            <i class="fa-solid fa-chevron-down text-[10px] text-gray-500 transition-transform duration-200" :class="{'rotate-180': profileDropdownOpen}"></i>
                        </div>
                        
                        <!-- Dropdown Menu -->
                        <div x-show="profileDropdownOpen" 
                             @click.away="profileDropdownOpen = false"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 top-12 w-48 bg-white rounded-lg shadow-lg border border-gray-150 py-1 z-50"
                             x-cloak>
                            
                            <div class="px-4 py-2 border-b border-gray-100">
                                <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider">Mi Cuenta</p>
                                <p class="text-xs font-bold text-gray-800">Administrador</p>
                            </div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left flex items-center gap-2 px-4 py-2.5 text-xs text-red-600 hover:bg-red-50 transition-colors font-bold">
                                    <i class="fa-solid fa-sign-out-alt"></i> Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-4 md:p-6 bg-gray-50 relative">
                <!-- Background Decorative Water Droplets (Subtle Watermarks) -->
                <div class="absolute inset-0 pointer-events-none overflow-hidden select-none z-0">
                    <!-- Top Perimeter -->
                    <i class="fa-solid fa-droplet text-brand-blue/[0.045] text-[5rem] absolute top-4 left-6"></i>
                    <i class="fa-solid fa-droplet text-brand-light/[0.04] text-[4rem] absolute top-8 left-1/3"></i>
                    <i class="fa-solid fa-droplet text-brand-blue/[0.04] text-[4.5rem] absolute top-12 right-1/3"></i>
                    <i class="fa-solid fa-droplet text-brand-light/[0.05] text-[6rem] absolute top-4 right-8"></i>
                    
                    <!-- Middle Perimeter -->
                    <i class="fa-solid fa-droplet text-brand-blue/[0.045] text-[5.5rem] absolute top-1/3 left-4"></i>
                    <i class="fa-solid fa-droplet text-brand-light/[0.05] text-[6rem] absolute top-1/3 right-4"></i>
                    
                    <!-- Bottom Perimeter -->
                    <i class="fa-solid fa-droplet text-brand-blue/[0.04] text-[5rem] absolute bottom-1/3 left-1/4"></i>
                    <i class="fa-solid fa-droplet text-brand-light/[0.04] text-[5.5rem] absolute bottom-1/3 right-1/4"></i>
                    <i class="fa-solid fa-droplet text-brand-blue/[0.045] text-[6rem] absolute bottom-6 left-8"></i>
                    <i class="fa-solid fa-droplet text-brand-light/[0.05] text-[7rem] absolute bottom-6 right-8"></i>
                </div>
                
                <div class="relative z-10">
                    @yield('content')
                </div>
            </main>
        </div>
        
    </div>

    <!-- Global Details Modal (Breathtaking Design) -->
    <div x-show="detailModalOpen" 
         class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[90] flex items-center justify-center p-4 transition-all duration-300"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         x-cloak>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all border border-gray-150 dark:border-gray-700" 
             @click.away="detailModalOpen = false"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            
            <!-- Header -->
            <div class="bg-brand-blue p-5 text-white flex items-center justify-between border-b border-blue-900/10">
                <div class="flex items-center gap-2">
                    <span class="text-xs uppercase font-extrabold px-2 py-0.5 rounded bg-white/20 text-white tracking-widest" x-text="detailData ? detailData.type : 'Cargando'"></span>
                    <h3 class="text-base font-bold truncate max-w-[240px]" x-text="detailData ? detailData.title : 'Consultando Registro'"></h3>
                </div>
                <button @click="detailModalOpen = false" class="text-white/80 hover:text-white text-lg transition-colors focus:outline-none"><i class="fa-solid fa-times"></i></button>
            </div>
            
            <!-- Content -->
            <div class="p-6">
                <!-- Loading Animation -->
                <div x-show="detailLoading" class="flex flex-col items-center justify-center py-12 gap-3">
                    <i class="fa-solid fa-spinner animate-spin text-4xl text-brand-blue dark:text-brand-light"></i>
                    <p class="text-xs text-gray-500 font-sans">Recuperando información confidencial...</p>
                </div>
                
                <!-- Dynamic Ledger Data -->
                <div x-show="!detailLoading && detailData">
                    <div class="rounded-xl bg-gray-50/50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 divide-y divide-gray-100/60 dark:divide-gray-700/60 overflow-hidden font-sans">
                        <template x-for="(value, key) in (detailData ? detailData.details : {})">
                            <div class="flex items-center justify-between px-4 py-3">
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider" x-text="key"></span>
                                <span class="text-sm font-semibold text-gray-850 dark:text-gray-200" x-text="value"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="bg-gray-50 dark:bg-gray-950 px-6 py-4 flex justify-end border-t border-gray-100 dark:border-gray-700/60">
                <button @click="detailModalOpen = false" class="bg-brand-blue hover:bg-brand-blue/90 text-white font-bold text-xs px-5 py-2.5 rounded-lg transition-all shadow focus:outline-none">Cerrar Detalle</button>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
