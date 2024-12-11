$(document).ready(function () {
    // Inisialisasi DataTable
    var table = $('#ExportTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'print', 
                title: 'Inventory Barang',
                text: 'Print PDF', 
            },
        ],
    });

    // Trigger Print Button dari Tombol Custom
    $('#export-btn').on('click', function () {
        table.button('.buttons-print').trigger(); // Trigger tombol print bawaan DataTable
    });
});
