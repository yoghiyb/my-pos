<template>
  <div class="col-md-12">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Dashboard</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item">Home</li>
              <li class="breadcrumb-item active">Dashboard</li>
            </ol>
          </div>
        </div>
      </div>
    </div>​
    <!-- Main content -->
    <section class="content" id="dw">
      <div class="container-fluid">
        <!-- ABAIKAN DULU LINE INI KARENA AKAN DI MODIFIKASI SELANJUTNYA -->
        <div class="row">
          <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
              <div class="inner">
                <h3>{{ product }}</h3>
                <p>Produk</p>
              </div>
              <div class="icon">
                <i class="ion ion-bag"></i>
              </div>
            </div>
          </div>​
          <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
              <div class="inner">
                <h3>{{ order }}</h3>
                <p>Pesanan</p>
              </div>
              <div class="icon">
                <i class="ion ion-stats-bars"></i>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
              <div class="inner">
                <h3>{{ customer }}</h3>
                <p>Pelanggan</p>
              </div>
              <div class="icon">
                <i class="ion ion-person-add"></i>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
              <div class="inner">
                <h3>{{ user }}</h3>
                <p>Karyawan</p>
              </div>
              <div class="icon">
                <i class="ion ion-pie-graph"></i>
              </div>
            </div>
          </div>
        </div>
        <!-- SAMPAI LINE INI -->

        <!-- YANG PERLU DIPERHATIKAN ADALAH LINE DIBAWAH -->
        <div class="row">
          <!-- CHART.JS MEMINTA ELEMENT YANG MEMILIKI ID dw-chart -->
          <canvas id="my-chart"></canvas>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  data() {
    return {
      product: 0,
      order: 0,
      customer: 0,
      user: 0,
      chartData: {
        type: "line",
        data: {
          labels: [],
          datasets: [
            {
              label: "Total Penjualan",
              data: [],
              backgroundColor: [
                "rgba(71, 183,132,.5)",
                "rgba(71, 183,132,.5)",
                "rgba(71, 183,132,.5)",
                "rgba(71, 183,132,.5)",
                "rgba(71, 183,132,.5)",
                "rgba(71, 183,132,.5)",
                "rgba(71, 183,132,.5)",
              ],
              borderColor: [
                "#47b784",
                "#47b784",
                "#47b784",
                "#47b784",
                "#47b784",
                "#47b784",
                "#47b784",
              ],
              borderWidth: 3,
            },
          ],
        },
        options: {
          responsive: true,
          lineTension: 1,
          scales: {
            yAxes: [
              {
                ticks: {
                  beginAtZero: true,
                  padding: 25,
                },
              },
            ],
          },
        },
      },
    };
  },
  mounted() {
    this.getChartData();
    this.getData();
  },
  methods: {
    /**
     * Membuat chart
     *
     * @param chartId string
     * @param chartData object
     */
    createChart(chartId, chartData) {
      // cari elemen dengan id sesuai parameter
      const ctx = document.getElementById(chartId);
      // mendefinisikan chart.js
      const myChart = new Chart(ctx, {
        type: chartData.type,
        data: chartData.data,
        options: chartData.options,
      });

      return myChart;
    },
    /**
     * Ambil data chart
     */
    getChartData() {
      let endpoint = `${BASE_URL}/chart`;
      axios
        .get(endpoint)
        .then((response) => {
          // jika responya succsess
          if (response.data.status == "success") {
            // looping dengan memisahkan key dan value
            Object.entries(response.data.data).forEach(([key, value]) => {
              // key adalah data tanggal
              // masukkan kedalam labels
              this.chartData.data.labels.push(key);
              // value adalah total pesanan
              // masukkan kedalam datasets
              this.chartData.data.datasets[0].data.push(value);
            });

            this.createChart("my-chart", this.chartData).update();
          }
        })
        .catch((err) => console.log(err));
    },
    getData() {
      let endpoint = `${BASE_URL}/dashboard`;
      axios
        .get(endpoint)
        .then((response) => {
          this.product = response.data.product;
          this.order = response.data.order;
          this.customer = response.data.customer;
          this.user = response.data.user;
        })
        .catch((err) => console.log(err));
    },
  },
};
</script>

<style>
</style>
