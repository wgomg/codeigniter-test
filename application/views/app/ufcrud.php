<script type='text/javascript'>
    $(document).ready(function() {
        let table = $('table').dataTable({
            ajax: {
                url: 'getUfHistorical',
                dataSrc: ''
            },
            columns: [{
                data: 'value'
            }, {
                data: 'date'
            }, {
                data: 'buttons'
            }],
            order: [
                [1, 'desc']
            ],
            columnDefs: [{
                targets: 1,
                render: $.fn.dataTable.render.moment('YYYY-MM-DD hh:mm:ss.sss', 'DD MMM YYYY')
            }]
        });

        $("table").on('click', 'button.editUf', function() {
            const value = $(this).closest('tr').children()[0].textContent;
            let date = $(this).closest('tr').children()[1].textContent;

            const id = $(this).data('id');

            date = moment(date, 'DD MMM YYYY').format('YYYY-MM-DD');

            $('#value').val(value);
            $('#date').val(date);

            $('#id').val(id);
        });

        $('button#saveUf').on('click', function() {
            const url = <?php echo "'" . site_url('app/saveUf') . "'"; ?>;

            $.post(url, {
                id: $('#id').val(),
                value: $('#value').val(),
                date: $('#date').val()
            }, function(data) {
                let res = $.parseJSON(data);

                if (res.success) {
                    table.api().ajax.reload();
                    $('#ufModal').modal('toggle');

                    alert('Success!');
                } else
                    alert('Something went wrong, please try again');
            });
        });

        $('button#newUf').click(function() {
            $('#id').val('');
            $('#value').val('');
            $('#date').val('');
        });

        $("table").on('click', 'button.deleteUf', function() {
            const id = $(this).data('id');

            const url = <?php echo "'" . site_url('app/deleteUf') . "'"; ?>;

            if (confirm('Are you sure?'))
                $.ajax({
                    type: 'DELETE',
                    url: url + '/' + id,
                    success: function(data) {
                        let res = $.parseJSON(data);

                        if (res.success) {
                            table.api().ajax.reload();
                            alert('Success!');
                        } else
                            alert('Something went wrong, please try again');
                    }
                });
        });
    });
</script>

<div class="container ">
    <div class="m-4 row">
        <h5><?php echo $indicator->name; ?></h5>
        <span class="d-none" id=""></span>
    </div>

    <div class="ml-4">
        <div class="table-responsive">
            <button type="button" id="newUf" data-toggle="modal" data-target="#ufModal" class="btn btn-success btn-sm">Add UF Value</button>
            <br /><br />
            <table id="uf" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Value (<?php echo $indicator->measurement; ?>)</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<?php $this->load->view('app/ufModal', array('indicator' => $indicator)); ?>