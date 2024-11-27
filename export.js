$(document).ready(function () {
  // Inisialisasi DataTable
  var table = $('#dataTable').DataTable({
      buttons: [
          {
              extend: 'print', 
              title: 'Inventory Barang',
              text: 'Print PDF', 
              exportOptions: {  
                columns: ':not(:last-child)'  
            }
          },

          {
            extend: 'excel', 
              title: 'Inventory Barang',
              text: 'Print Excel', 
              exportOptions: {  
                columns: ':not(:last-child)'  
            }
          }
      ],
  });


  // Trigger Print Button dari Tombol Custom
  $('#export-btn').on('click', function () {
      table.button('.buttons-excel').trigger(); // pdf : "buttons-print" excel : "buttons-excel"
  });
});