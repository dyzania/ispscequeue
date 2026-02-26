<?php
$pageTitle = 'Ticket History';
require_once __DIR__ . '/../../models/Ticket.php';
include __DIR__ . '/../../includes/admin-layout-header.php';
?>

<!-- Add Export Libraries -->
<script src="https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>

<?php
$ticketModel = new Ticket();

$startDate = $_GET['start_date'] ?? null;
$endDate = $_GET['end_date'] ?? null;

$history = $ticketModel->getGlobalHistory($startDate, $endDate);
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-primary-600 mb-1">Logs & Archive</p>
            <h1 class="text-2xl font-black text-gray-900 font-heading tracking-tight">Ticket History</h1>
        </div>

        <!-- Export Buttons -->
        <div class="flex items-center p-1 bg-white border border-slate-200 rounded-xl shadow-sm">
            <button onclick="exportHistoryToExcel()" class="px-4 py-2 hover:bg-emerald-50 text-emerald-600 rounded-lg text-xs font-black uppercase tracking-widest transition-all flex items-center">
                <i class="fas fa-file-excel mr-2"></i> Excel
            </button>
            <div class="w-px h-4 bg-slate-100 mx-1"></div>
            <button onclick="exportHistoryToPDF()" class="px-4 py-2 hover:bg-rose-50 text-rose-600 rounded-lg text-xs font-black uppercase tracking-widest transition-all flex items-center">
                <i class="fas fa-file-pdf mr-2"></i> PDF
            </button>
            <div class="w-px h-4 bg-slate-100 mx-1"></div>
            <button onclick="exportHistoryToWord()" class="px-4 py-2 hover:bg-blue-50 text-blue-600 rounded-lg text-xs font-black uppercase tracking-widest transition-all flex items-center">
                <i class="fas fa-file-word mr-2"></i> Word
            </button>
        </div>
        
        <!-- Filters -->
        <form method="GET" class="bg-white p-3 rounded-xl shadow-sm border border-slate-100 flex flex-wrap items-center gap-3">
            <div class="flex flex-col">
                <label class="text-[10px] font-black uppercase text-gray-400 mb-1 ml-1">Start Date</label>
                <input type="date" name="start_date" value="<?php echo $startDate; ?>" 
                       class="bg-slate-50 border-none rounded-lg text-xs font-bold focus:ring-primary-500 py-1.5 px-3">
            </div>
            <div class="flex flex-col">
                <label class="text-[10px] font-black uppercase text-gray-400 mb-1 ml-1">End Date</label>
                <input type="date" name="end_date" value="<?php echo $endDate; ?>"
                       class="bg-slate-50 border-none rounded-lg text-xs font-bold focus:ring-primary-500 py-1.5 px-3">
            </div>
            <div class="flex items-end h-full mt-4">
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg font-bold hover:bg-primary-700 transition-all shadow-lg shadow-primary-900/20 text-xs">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
                <?php if ($startDate || $endDate): ?>
                    <a href="history.php" class="ml-2 px-4 py-2 bg-slate-100 text-gray-600 rounded-lg font-bold hover:bg-slate-200 transition-all text-xs">
                        Clear
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl shadow-xl shadow-slate-300/50 border border-slate-300 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-100">
                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-widest text-slate-500 text-center">Ticket</th>
                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-widest text-slate-500 text-center">Customer</th>
                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-widest text-slate-500 text-center">Service</th>
                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-widest text-slate-500 text-center">Window</th>
                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-widest text-slate-500 text-center">Notes</th>
                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-widest text-slate-500 text-center">Proc. Time</th>
                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-widest text-slate-500 text-center">Completed At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php if (empty($history)): ?>
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-slate-400 font-medium text-xs">
                                No tickets found for this period.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($history as $ticket): ?>
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-4 py-3 text-center">
                                    <span class="font-black text-slate-900 text-xs"><?php echo $ticket['ticket_number']; ?></span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <div class="w-6 h-6 rounded bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-[10px] uppercase">
                                            <?php echo substr($ticket['user_name'], 0, 2); ?>
                                        </div>
                                        <span class="font-bold text-slate-700 text-xs"><?php echo $ticket['user_name']; ?></span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-0.5 bg-primary-50 text-primary-600 rounded-md text-[10px] font-black uppercase tracking-wider border border-primary-100">
                                        <?php echo $ticket['service_name']; ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="font-bold text-slate-600 text-xs">
                                        <?php echo $ticket['window_name'] ? "W-{$ticket['window_number']}" : '-'; ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <?php if ($ticket['staff_notes']): ?>
                                        <div class="max-w-[150px] mx-auto text-[10px] font-medium text-slate-500 truncate" title="<?php echo htmlspecialchars($ticket['staff_notes']); ?>">
                                            "<?php echo $ticket['staff_notes']; ?>"
                                        </div>
                                    <?php else: ?>
                                        <span class="text-slate-300 text-[10px]">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 font-bold text-slate-500 text-xs text-center">
                                    <?php echo $ticket['processing_time']; ?>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="text-xs font-bold text-slate-900">
                                        <?php echo date('M d', strtotime($ticket['created_at'])); ?>
                                    </div>
                                    <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest">
                                        <?php echo date('h:i A', strtotime($ticket['created_at'])); ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    const historyData = <?php echo json_encode($history); ?>;

    function exportHistoryToExcel() {
        const worksheet = XLSX.utils.json_to_sheet(historyData.map(h => ({
            'Ticket #': h.ticket_number,
            'Customer': h.user_name,
            'Service': h.service_name,
            'Window': h.window_name ? `W-${h.window_number}` : '-',
            'Notes': h.staff_notes || '-',
            'Processing Time': h.processing_time,
            'Completed At': new Date(h.created_at).toLocaleString()
        })));
        
        const workbook = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(workbook, worksheet, "Ticket History");
        XLSX.writeFile(workbook, `Ticket_History_${new Date().toISOString().split('T')[0]}.xlsx`);
    }

    function exportHistoryToPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('l', 'mm', 'a4');
        
        doc.setFontSize(20);
        doc.text("Ticket History Report", 14, 22);
        doc.setFontSize(10);
        doc.setTextColor(100);
        doc.text(`Generated on: ${new Date().toLocaleString()}`, 14, 30);
        
        const tableBody = historyData.map(h => [
            h.ticket_number,
            h.user_name,
            h.service_name,
            h.window_name ? `W-${h.window_number}` : '-',
            h.staff_notes || '-',
            h.processing_time,
            new Date(h.created_at).toLocaleString()
        ]);
        
        doc.autoTable({
            startY: 40,
            head: [['Ticket #', 'Customer', 'Service', 'Window', 'Notes', 'Proc. Time', 'Completed At']],
            body: tableBody,
            theme: 'striped',
            headStyles: { fillColor: [15, 23, 42], fontWeight: 'bold' },
            styles: { fontSize: 8, cellPadding: 3 }
        });
        
        doc.save(`Ticket_History_${new Date().toISOString().split('T')[0]}.pdf`);
    }

    function exportHistoryToWord() {
        let content = `
            <html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>
            <head><meta charset='utf-8'><title>Ticket History Report</title></head>
            <body style="font-family: Arial, sans-serif;">
                <h1 style="color: #0f172a; text-align: center;">Ticket History Report</h1>
                <p style="color: #64748b; text-align: center;">Report Date: ${new Date().toLocaleString()}</p>
                <hr>
                <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse;">
                    <tr style="background-color: #f8fafc;">
                        <th>Ticket #</th><th>Customer</th><th>Service</th><th>Window</th><th>Notes</th><th>Proc. Time</th><th>Completed At</th>
                    </tr>
                    ${historyData.map(h => `
                        <tr>
                            <td>${h.ticket_number}</td>
                            <td>${h.user_name}</td>
                            <td>${h.service_name}</td>
                            <td>${h.window_name ? `W-${h.window_number}` : '-'}</td>
                            <td>${h.staff_notes || '-'}</td>
                            <td>${h.processing_time}</td>
                            <td>${new Date(h.created_at).toLocaleString()}</td>
                        </tr>
                    `).join('')}
                </table>
            </body>
            </html>
        `;
        
        const blob = new Blob(['\ufeff', content], { type: 'application/msword' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = `Ticket_History_${new Date().toISOString().split('T')[0]}.doc`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>

<?php include __DIR__ . '/../../includes/admin-layout-footer.php'; ?>
