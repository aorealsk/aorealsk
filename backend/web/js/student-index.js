$(function () {
    // Elmentjük a DataTables példányt egy változóba, hogy később hivatkozhassunk rá
    var table = $('.dattable').DataTable({
        "order": [[1, "desc"]],
        "columnDefs": [
            { "orderable": false, "targets": [0, 5] }
        ],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Slovak.json"
        }
    });

    // Eseménykezelő a '+' ikonra. Fontos, hogy a 'tbody'-ra kössük rá,
    // így a DataTables által kezelt (pl. lapozott) sorokon is működni fog.
    $('.dattable tbody').on('click', 'a.details-control', function (e) {
        e.preventDefault();

        var tr = $(this).closest('tr');
        var row = table.row(tr);
        var studentId = $(this).data('id');
        var icon = $(this).find('i');

        if (row.child.isShown()) {
            // Ha a sor már nyitva van, csukjuk be
            row.child.hide();
            tr.removeClass('details-open');
            icon.removeClass('fa-minus-square').addClass('fa-plus-square');
        } else {
            // Ha a sor zárva van, nyissuk ki
            tr.addClass('details-open');
            icon.removeClass('fa-plus-square').addClass('fa-minus-square');

            // Jelenítsünk meg egy "Töltés..." üzenetet, amíg az adatok megérkeznek
            row.child('<div><p><i>Načítavanie...</i></p></div>').show();

            // AJAX hívás a részletes adatokért
            $.ajax({
                url: '/backoffice/students/get-details',
                type: 'GET',
                data: { id: studentId },
                success: function (response) {
                    // Ha sikeres volt a hívás, cseréljük le a "Töltés..." üzenetet
                    // a szerverről kapott HTML tartalomra.
                    row.child(response).show();
                },
                error: function () {
                    row.child('<div><p class="text-danger">Chyba pri načítaní detailov.</p></div>').show();
                }
            });
        }
    });
});