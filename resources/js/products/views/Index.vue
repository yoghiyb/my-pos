<template>
  <div class="col-md-12">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Manajemen Produk</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item">
                <a href="#">Home</a>
              </li>
              <li class="breadcrumb-item active">Produk</li>
            </ol>
          </div>
        </div>
      </div>
    </div>​
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-body">
                <button type="button" class="btn btn-primary btn-sm" @click="goToCreateProduct">
                  <i class="fa fa-edit"></i> Tambah
                </button>

                <div class="table-responsive">
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>Nama Produk</th>
                        <th>Stok</th>
                        <th>Harga</th>
                        <th>Kategori</th>
                        <th>Last Update</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(product, key) in products.data" :key="key">
                        <td>
                          <img
                            v-if="product.photo != null"
                            :src="'./uploads/product/' + product.photo"
                            :alt="product.name"
                            width="50px"
                            height="50px"
                          />

                          <img v-else src="http://via.placeholder.com/50x50" :alt="product.name" />
                        </td>
                        <td>
                          <sup class="label label-success">({{ product.code }})</sup>
                          <strong>{{ product.name }}</strong>
                        </td>
                        <td>{{ product.stock }}</td>
                        <td>{{ product.price | currency('Rp. ') }}</td>
                        <td>{{ product.category.name }}</td>
                        <td>{{ product.updated_at }}</td>
                        <td>
                          <button
                            type="button"
                            class="btn btn-warning btn-sm"
                            @click="goToUpdateProduct(product.id)"
                          >
                            <i class="fa fa-edit"></i>
                          </button>
                          <button
                            type="button"
                            class="btn btn-danger btn-sm"
                            @click="confirmDelete(product)"
                          >
                            <i class="fa fa-trash"></i>
                          </button>
                        </td>
                      </tr>
                      <tr v-if="products.data.length == 0">
                        <td colspan="7" class="text-center">Tidak ada data</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
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
      products: {
        data: [],
      },
    };
  },
  created() {
    this.fetchProducts();
  },
  methods: {
    /**
     * Mengambil data produk
     */
    async fetchProducts() {
      try {
        let endpoint = `${BASE_URL}/product`;
        let response = await axios.get(endpoint);

        if (response.data.status == "success") {
          this.products = response.data.data;
        }
      } catch (error) {
        console.log(error);
      }
    },
    goToUpdateProduct(id) {
      this.$router.push({
        name: "ProductUpdate",
        params: { id },
      });
    },
    /**
     * Konfirmasi user sebelum hapus data
     *
     * @param number product_id
     */
    confirmDelete(product) {
      Swal.fire({
        title: "Apakah anda yakin?",
        text: "Anda akan menghapus produk " + product.name,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Delete",
      }).then((result) => {
        if (result.value) {
          this.createProduct(product.id);
        }
      });
    },
    /**
     * Hapus produk berdasarkan id
     *
     * @param number product_id
     */
    async deleteProduct(id) {
      try {
        let endpoint = `${BASE_URL}/product/${id}`;
        let response = await axios.delete(endpoint);

        if (response.data.status == "success") {
          this.fetchProducts();
          Swal.fire("Berhasil!", "Data berhasil dihapus.", "success");
        }
      } catch (error) {
        console.log(error);
        Swal.fire("Gagal!", "Data gagal dihapus.", "error");
      }
    },
    /**
     * Pindah ke halaman buat produk
     */
    goToCreateProduct() {
      this.$router.push({ name: "ProductCreate" });
    },
  },
};
</script>

<style>
</style>
