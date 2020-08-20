<template>
  <div class="col-md-12">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">List Transaksi</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item">Home</li>
              <li class="breadcrumb-item active">Order</li>
            </ol>
          </div>
        </div>
      </div>
    </div>​
    <section class="content" id="dw">
      <div class="container-fluid">
        <div class="row">
          <!-- FORM UNTUK FILTER DATA -->
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <div class="card-title">Filter Transaksi</div>
              </div>
              <div class="card-body">
                <form @submit.prevent="fetchOrder">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for>Mulai Tanggal</label>
                        <input
                          type="text"
                          name="start_date"
                          class="form-control"
                          id="start_date"
                          v-model="filter.start_date"
                        />
                      </div>
                      <div class="form-group">
                        <label for>Sampai Tanggal</label>
                        <input
                          type="text"
                          name="end_date"
                          class="form-control"
                          id="end_date"
                          v-model="filter.end_date"
                        />
                      </div>
                      <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-sm">Cari</button>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for>Pelanggan</label>
                        <select
                          name="customer_id"
                          class="form-control"
                          v-model="filter.customer_id"
                        >
                          <option value>Pilih</option>
                          <option
                            v-for="(customer,key) in data.customers"
                            :key="key"
                            :value="customer.id"
                          >{{ customer.name }} - {{ customer.email }}</option>
                        </select>
                      </div>
                      <div class="form-group">
                        <label for>Kasir</label>
                        <select name="user_id" class="form-control" v-model="filter.user_id">
                          <option value>Pilih</option>
                          <option
                            v-for="(user, key) in data.users"
                            :key="key"
                            :value="user.id"
                          >{{ user.name }} - {{ user.email }}</option>
                        </select>
                      </div>
                    </div>
                  </div>
                </form>​
              </div>
            </div>
          </div>

          <!-- FORM UNTUK MENAMPILKAN DATA TRANSAKSI -->
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <div class="card-title">Data Transaksi</div>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-4">
                    <div class="small-box bg-info">
                      <div class="inner">
                        <h3>{{ data.sold }}</h3>
                        <p>Item Terjual</p>
                      </div>
                      <div class="icon">
                        <i class="ion ion-bag"></i>
                      </div>
                    </div>
                  </div>
                  <div class="col-4">
                    <div class="small-box bg-success">
                      <div class="inner">
                        <h3>{{ data.total | currency('Rp. ') }}</h3>
                        <p>Total Omset</p>
                      </div>
                      <div class="icon">
                        <i class="ion ion-stats-bars"></i>
                      </div>
                    </div>
                  </div>
                  <div class="col-4">
                    <div class="small-box bg-primary">
                      <div class="inner">
                        <h3>{{ data.total_customer }}</h3>
                        <p>Total pelanggan</p>
                      </div>
                      <div class="icon">
                        <i class="ion ion-stats-bars"></i>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- TABLE UNTUK MENAMPILKAN DATA TRANSAKSI -->
                <div class="table-responsive">
                  <table class="table table-hover table-bordered">
                    <thead>
                      <tr>
                        <th>Invoice</th>
                        <th>Pelanggan</th>
                        <th>No Telp</th>
                        <th>Total Belanja</th>
                        <th>Kasir</th>
                        <th>Tgl Transaksi</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      <!-- LOOPING MENGGUNAKAN FORELSE, DIRECTIVE DI LARAVEL 5.6 -->

                      <tr v-for="(order, key) in data.orders" :key="key">
                        <td>
                          <strong>#{{ order.invoice }}</strong>
                        </td>
                        <td>{{ order.customer.name }}</td>
                        <td>{{ order.customer.phone }}</td>
                        <td>{{ order.total | currency('Rp. ')}}</td>
                        <td>{{ order.user.name }}</td>
                        <td>{{ order.created_at }}</td>
                        <td>
                          <button
                            type="button"
                            @click="generatePdf(order)"
                            class="btn btn-primary btn-sm"
                          >
                            <i class="fa fa-print"></i>
                          </button>
                          <a href="#" target="_blank" class="btn btn-success btn-sm">
                            <i class="fas fa-file-excel"></i>
                          </a>
                        </td>
                      </tr>
                      <tr v-if="data.orders && data.orders.length <= 0">
                        <td class="text-center" colspan="7">Tidak ada data transaksi</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <!-- KOTAK UNTUK MENAMPILKAN TOTAL DATA -->
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  data() {
    return {
      data: "",
      filter: {
        start_date: "",
        end_date: "",
        customer_id: "",
        user_id: "",
      },
    };
  },
  created() {
    this.fetchOrder();
  },
  mounted() {
    var vm = this;
    $("#start_date").datepicker({
      autoclose: true,
      format: "yyyy-mm-dd",
      onSelect: function (dateText) {
        vm.filter.start_date = dateText;
      },
    });

    $("#end_date").datepicker({
      autoclose: true,
      format: "yyyy-mm-dd",
      onSelect: function (dateText) {
        vm.filter.end_date = dateText;
      },
    });
  },
  methods: {
    /**
     *
     */
    fetchOrder() {
      let endpoint = `${BASE_URL}/order/management`;
      axios
        .post(endpoint, this.filter)
        .then((response) => {
          this.data = response.data;
        })
        .catch((err) => console.log(err));
    },
    generatePdf(order) {
      let endpoint = `${BASE_URL}/order/pdf/${order.invoice}`;
      axios
        .get(endpoint, { responseType: "blob" })
        .then((response) => {
          const url = window.URL.createObjectURL(new Blob([response.data]));
          const link = document.createElement("a");
          link.href = url;
          link.id = "genPdf";
          link.setAttribute("download", `${order.invoice}.pdf`); //or any other extension
          document.body.appendChild(link);
          link.click();
          $("a").remove("#genPdf");
        })
        .catch((err) => console.log(err));
    },
  },
};
</script>

<style>
</style>
