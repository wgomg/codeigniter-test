<div class="container">

    <div class="m-4 row" id="currentValue">
        <h5 id="label"></h5>
        <h5 id="value"> <span class="text-monospace"></span></h5>
        <span class="d-none" id=""></span>
    </div>

    <div class="ml-4 w-50 d-none" id="datepicker">
        <h5>Generate chart:</h5>

        <div class="d-flex flex-row pl-2">
            <div class="p-1 form-group row flex-fill">
                <label for="from" class="col-sm-2 col-form-label">From: </label>
                <div class="col-sm-10">
                    <input class="form-control" type="date" name="from" id="from" min="1900-01-01">
                </div>
            </div>
            <div class="p-1 form-group row flex-fill">
                <label for="to" class="col-sm-2 col-form-label">To: </label>
                <div class="col-sm-10">
                    <input class="form-control" type="date" name="to" id="to" min="1900-01-01">
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-primary btn-sm btn-block" id="genChart">Generate</button>
    </div>

    <div class="ml-4 d-none" id="chart">
        <canvas></canvas>
    </div>
</div>


<script type='text/javascript'>
    let chart = null;

    $(".dropdown-item").click(function() {
        const selectedInd = $(this).attr('id');

        $.getJSON(`app/getCurrentValue/${selectedInd}`, function(data) {
            $("#currentValue h5#label").text(`[${data.date}] ${data.indicator} `);
            $("#currentValue h5#value span").text(` => ${data.value} ${data.measurement} `);
            $("#currentValue span").attr("id", data.code);

            $("#datepicker").removeClass("d-none");

            if (chart) {
                chart.destroy();
                $("#chart").addClass("d-none");
            }
        });
    });

    $("#genChart").click(function() {
        if (chart) {
            chart.destroy();
            $("#chart").addClass("d-none");
        }

        const indCode = $("#currentValue span").attr("id");
        const from = $('#from').val();
        const to = $('#to').val();

        let url = `app/getHistorical/${indCode}/${from.length > 0 ? `${from}` : '0'}/${to.length > 0 ? `/${to}` : '0'}`;

        $.getJSON(url, function(data) {
            let ctx = $('#chart canvas')[0].getContext('2d');
            ctx.canvas.width = 1000;
            ctx.canvas.height = 300;

            let color = Chart.helpers.color;

            config.data.datasets[0].backgroundColor = color('rgb(255, 99, 132)').alpha(0.5).rgbString();
            config.data.datasets[0].borderColor = 'rgb(255, 99, 132)';


            chart = new Chart(ctx, config);

            $("#chart").removeClass("d-none");
        });
    });
</script>