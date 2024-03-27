@include('layouts.header')
@include('layouts.sidebar')
@include('layouts.navbar')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container-fluid">
    <div class="row ">
        <form id="dateForm" action="{{ route('diagram') }}" method="GET" class="form-inline">
            @csrf <!-- Tambahkan ini untuk melindungi formulir dari serangan CSRF -->
            <label for="selectedDate">Pilih Tanggal:</label>
            <input type="date" id="selectedDate" name="selectedDate" class="form-control mx-2">
            <button type="submit" class="btn btn-outline-primary"><i class="fa-solid fa-magnifying-glass"></i>
                Cari</button>
        </form>
            </div>


    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Diagram Perhari</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="myChart" width="800" height="400"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Diagram Pertahun</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="myLineChart" width="800" height="400"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--PERTAHUN-->
<script>
    var customerCounts = {!! isset($customerCounts) ? json_encode($customerCounts) : '[]' !!};

    var customerNames = Object.keys(customerCounts);
    var customerAppearances = Object.values(customerCounts);

    var ctx1 = document.getElementById('myLineChart').getContext('2d');
    var myLineChart = new Chart(ctx1, {
        type: 'line',
        data: {
            labels: customerNames,
            datasets: [{
                label: 'Jumlah Cs',
                data: customerAppearances,
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
<!--PERTAHUN-->

<!--PERHARI-->
<script>
    var customerCountsPerhari = {!! isset($customerCountsPerhari) ? json_encode($customerCountsPerhari) : '[]' !!};
    var customerNamesPerhari = Object.keys(customerCountsPerhari);
    var customerAppearancesPerhari = Object.values(customerCountsPerhari);
    
    // Generate dynamic colors for each customer
    var dynamicColors = [];
    for (var i = 0; i < customerNamesPerhari.length; i++) {
        var r = Math.floor(Math.random() * 255);
        var g = Math.floor(Math.random() * 255);
        var b = Math.floor(Math.random() * 255);
        dynamicColors.push('rgba(' + r + ', ' + g + ', ' + b + ', 0.5)');
    }

    var ctx2 = document.getElementById('myChart').getContext('2d');
    var myChart = new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: customerNamesPerhari,
            datasets: [{
                label: 'Jumlah Kemunculan',
                data: customerAppearancesPerhari,
                backgroundColor: dynamicColors, 
                borderColor: 'black',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

<!--PERHARI-->

{{-- @include('layouts.footer') --}}
