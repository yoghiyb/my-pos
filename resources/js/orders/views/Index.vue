<template>
  <div class="col-md-12">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Transaksi</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item">Home</li>
              <li class="breadcrumb-item active">Transaksi</li>
            </ol>
          </div>
        </div>
      </div>
    </div>​
    <section class="content" id="dw">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-8">
            <div class="card">
              <div v-if="isCheckOut" class="card-header">
                <h4 class="card-title">Data Pelanggan</h4>
              </div>
              <div class="card-body">
                <div v-if="!isCheckOut" class="row">
                  <div class="col-md-4">
                    <!-- SUBMIT DIJALANKAN KETIKA TOMBOL DITEKAN -->
                    <form @submit.prevent="addProductToCart">
                      <div class="form-group">
                        <label for>Produk</label>
                        <select
                          name="product_id"
                          id="product_id"
                          v-model="cart.product_id"
                          class="form-control"
                          required
                          width="100%"
                        >
                          <option value>Pilih</option>
                          <option
                            v-for="(product, key) in products"
                            :key="key"
                            :value="product.id"
                          >{{ product.code }} - {{ product.name }}</option>
                        </select>
                      </div>
                      <div class="form-group">
                        <label for>Qty</label>
                        <input
                          type="number"
                          name="qty"
                          v-model="cart.qty"
                          id="qty"
                          value="1"
                          min="1"
                          class="form-control"
                        />
                      </div>
                      <div class="form-group">
                        <button
                          type="submit"
                          class="btn btn-primary btn-sm"
                          :disabled="submitCart && shoppingCart.length <= 0"
                        >
                          <i class="fa fa-shopping-cart"></i>
                          {{ submitCart ? 'Loading...':'Ke Keranjang' }}
                        </button>
                      </div>
                    </form>
                  </div>

                  <!-- MENAMPILKAN DETAIL PRODUCT -->
                  <div class="col-md-5">
                    <h4>Detail Produk</h4>
                    <div v-if="product.name">
                      <table class="table table-stripped">
                        <tr>
                          <th>Kode</th>
                          <td>:</td>
                          <td>{{ product.code }}</td>
                        </tr>
                        <tr>
                          <th width="3%">Produk</th>
                          <td width="2%">:</td>
                          <td>{{ product.name }}</td>
                        </tr>
                        <tr>
                          <th>Harga</th>
                          <td>:</td>
                          <td>{{ product.price | currency('Rp. ') }}</td>
                        </tr>
                      </table>
                    </div>
                  </div>

                  <!-- MENAMPILKAN IMAGE DARI PRODUCT -->
                  <div class="col-md-3" v-if="product.photo">
                    <img
                      :src="'/uploads/product/' + product.photo"
                      height="150px"
                      width="150px"
                      :alt="product.name"
                    />
                  </div>
                </div>
                <div v-else>
                  <div v-if="message" class="alert alert-success">
                    Transaksi telah disimpan, Invoice:
                    <strong>#{{ message }}</strong>
                  </div>
                  <div class="form-group">
                    <label for>Email</label>
                    <input
                      type="email"
                      name="email"
                      v-model="customer.email"
                      class="form-control"
                      @keyup.enter.prevent="searchCustomer"
                      required
                    />
                    <div class="valid-feedback"></div>
                    <div class="invalid-feedback">Email harus diisi!</div>
                    <p>Tekan enter untuk mengecek email.</p>
                    <!-- EVENT KETIKA TOMBOL ENTER DITEKAN, MAKA AKAN MEMANGGIL METHOD searchCustomer dari Vuejs -->
                  </div>

                  <!-- JIKA formCustomer BERNILAI TRUE, MAKA FORM AKAN DITAMPILKAN -->
                  <div v-if="formCustomer">
                    <div class="form-group">
                      <label for>Nama Pelanggan</label>
                      <input
                        type="text"
                        name="name"
                        v-model="customer.name"
                        :disabled="resultStatus"
                        class="form-control"
                        required
                      />
                      <div class="valid-feedback"></div>
                      <div class="invalid-feedback">Nama harus diisi!</div>
                    </div>
                    <div class="form-group">
                      <label for>Alamat</label>
                      <textarea
                        name="address"
                        class="form-control"
                        :disabled="resultStatus"
                        v-model="customer.address"
                        cols="5"
                        rows="5"
                        required
                      ></textarea>
                      <div class="valid-feedback"></div>
                      <div class="invalid-feedback">Alamat harus diisi!</div>
                    </div>
                    <div class="form-group">
                      <label for>No Telp</label>
                      <input
                        type="text"
                        name="phone"
                        v-model="customer.phone"
                        :disabled="resultStatus"
                        class="form-control"
                        required
                      />
                      <div class="valid-feedback"></div>
                      <div class="invalid-feedback">No telp harus diisi!</div>
                    </div>
                  </div>
                </div>
              </div>
              <div v-if="isCheckOut" class="card-footer text-muted">
                <!-- JIKA VALUE DARI errorMessage ada, maka alert danger akan ditampilkan -->
                <div v-if="errorMessage" class="alert alert-danger">{{ errorMessage }}</div>
                <!-- JIKA TOMBOL DITEKAN MAKA AKAN MEMANGGIL METHOD sendOrder -->
                <button
                  class="btn btn-primary btn-sm float-right"
                  :disabled="submitForm"
                  @click.prevent="sendOrder"
                >{{ submitForm ? 'Loading...':'Order Now' }}</button>
              </div>
            </div>
          </div>

          <!-- MENAMPILKAN LIST PRODUCT YANG ADA DI KERANJANG -->
          <div class="col-md-4">
            <div class="card">
              <div class="card-body">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>Produk</th>
                      <th>Harga</th>
                      <th>Qty</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <!-- MENGGUNAKAN LOOPING VUEJS -->
                    <tr v-for="(row, index) in shoppingCart" :key="index">
                      <td>{{ row.name }} ({{ row.code }})</td>
                      <td>{{ row.price | currency('Rp. ') }}</td>
                      <td>{{ row.qty }}</td>
                      <td>
                        <!-- EVENT ONCLICK UNTUK MENGHAPUS CART -->
                        <button
                          @click.prevent="removeProductFromCart(index)"
                          class="btn btn-danger btn-sm"
                        >
                          <i class="fa fa-trash"></i>
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="card-footer text-muted">
                <button
                  v-if="!isCheckOut"
                  type="button"
                  class="btn btn-info btn-sm float-right"
                  @click="handleCheckOut()"
                >Checkout</button>
                <button
                  v-else
                  type="button"
                  class="btn btn-secondary btn-sm float-right"
                  @click="handleCheckOut()"
                >Kembali</button>
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
      products: [],
      product: {
        id: "",
        price: "",
        name: "",
        photo: "",
      },
      cart: {
        product_id: "",
        qty: 1,
      },
      shoppingCart: [],
      submitCart: false,
      customer: {
        email: "",
      },
      isCheckOut: false,
      formCustomer: false,
      resultStatus: false,
      submitForm: false,
      errorMessage: "",
      message: "",
    };
  },
  created() {
    this.fetchProdcuts();
  },
  watch: {
    "product.id": function () {
      if (this.product.id) {
        this.getProduct();
      }
    },
    "customer.email": function () {
      this.formCustomer = false;
      if (this.customer.name != "") {
        this.customer = {
          name: "",
          phone: "",
          address: "",
        };
      }
    },
  },
  mounted() {
    $("#product_id").on("change", () => {
      this.product.id = $("#product_id").val();
    });

    this.getCart();
  },
  methods: {
    /**
     * Ambil list produk untuk select
     */
    async fetchProdcuts() {
      try {
        let endpoint = `${BASE_URL}/products`;
        // kirim request ke server untuk mendapatkan list produk
        let response = await axios.get(endpoint);
        // jika respon status "success"
        if (response.data.status == "success") {
          // masukkan list produk ke state products
          this.products = response.data.data;
        }
      } catch (error) {
        console.log(error);
      }
    },
    /**
     * Ambil produk sesuai id yang diminta (Untuk menampilkan detail produk)
     */
    getProduct() {
      let endpoint = `${BASE_URL}/product/${this.product.id}`;
      // kirim request untuk mendapatkan detail produk
      axios
        .get(endpoint)
        .then((response) => {
          // jika respon status "success"
          if (response.data.status == "success") {
            // masukkan detail produk kedalam state product
            this.product = response.data.data;
          }
        })
        .catch((err) => {
          console.log(err);
        });
    },
    /**
     * Memasukkan produk kedalam keranjangn
     */
    addProductToCart() {
      // kondisi
      this.submitCart = true;

      let endpoint = `${BASE_URL}/cart`;
      // kirim request untuk menambahkan produk kedalam keranjang
      axios
        .post(endpoint, this.cart)
        .then((response) => {
          this.shoppingCart = response.data;
          this.resetForm();
        })
        .catch((err) => {
          console.log(err);
        });
    },
    /**
     * Ambil data keranjang
     */
    getCart() {
      let endpoint = `${BASE_URL}/cart`;
      // kirim request ke server untuk mendapatkan data keranjang
      axios
        .get(endpoint)
        .then((response) => {
          // masukkan data keranjang kedalam state shoppingCart
          this.shoppingCart = response.data;
        })
        .catch((err) => console.log(err));
    },
    /**
     * Hapus produk dari keranjang
     */
    removeProductFromCart(id) {
      // sweetalert2 untuk melakukan konfirmasi penghapusan produk dari keranjang
      Swal.fire({
        title: "Kamu Yakin?",
        text: "Kamu Tidak Dapat Mengembalikan Tindakan Ini!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Iya, Lanjutkan!",
        cancelButtonText: "Tidak, Batalkan!",
      }).then((result) => {
        // jika iya
        if (result.value) {
          let endpoint = `${BASE_URL}/cart/${id}`;
          // kirim request ke server untuk menghapus produk dari keranjang berdasarkan id yang dikirim
          axios
            .delete(endpoint)
            .then((response) => {
              // jika status dari server "success"
              if (response.data.status == "success") {
                // panggil kembali data keranjang
                this.getCart();
              }
            })
            // jika terjadi kesalahan (error)
            .catch((err) => {
              console.log(error);
            });
        }
      });
    },
    searchCustomer() {
      let endpoint = `${BASE_URL}/customer/search`;
      let body = {
        email: this.customer.email,
      };
      axios
        .post(endpoint, body)
        .then((response) => {
          if (response.data.status == "success") {
            this.customer = response.data.data;
            this.resultStatus = true;
          }
          this.formCustomer = true;
        })
        .catch((err) => {
          console.log(err);
        });
    },
    sendOrder(event) {
      // mengosongkan state errorMessage dan message
      this.errorMessage = "";
      this.message = "";

      //jika var customer.email dan kawan-kawannya tidak kosong
      if (
        this.customer.email != "" &&
        this.customer.name != "" &&
        this.customer.phone != "" &&
        this.customer.address != ""
      ) {
        //maka akan menampilkan kotak dialog konfirmasi
        Swal.fire({
          title: "Kamu Yakin?",
          text: "Kamu Tidak Dapat Mengembalikan Tindakan Ini!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Iya, Lanjutkan!",
          cancelButtonText: "Tidak, Batalkan!",
          showCloseButton: true,
          showLoaderOnConfirm: true,
          preConfirm: () => {
            return new Promise((resolve) => {
              setTimeout(() => {
                resolve();
              }, 2000);
            });
          },
          allowOutsideClick: () => !this.$swal.isLoading(),
        }).then((result) => {
          //jika di setujui
          if (result.value) {
            //maka submitForm akan di-set menjadi true sehingga menciptakan efek loading
            this.submitForm = true;

            let endpoint = `${BASE_URL}/checkout`;
            axios
              .post(endpoint, this.customer)
              .then((response) => {
                setTimeout(() => {
                  //jika responsenya berhasil, maka cart di-reload
                  this.getCart();
                  //message di-set untuk ditampilkan
                  this.message = response.data.message;
                  //form customer dikosongkan
                  this.customer = {
                    name: "",
                    phone: "",
                    address: "",
                  };
                  //submitForm kembali di-set menjadi false
                  this.submitForm = false;
                }, 1000);
              })
              .catch((error) => {
                console.log(error);
              });
          }
        });
      } else {
        //jika form kosong, maka error message ditampilkan
        this.errorMessage = "Masih ada inputan yang kosong!";
        // event.target.classList.add("was-validated");
        // return;
      }
    },
    handleCheckOut() {
      this.isCheckOut = !this.isCheckOut;
    },
    /**
     * Mengosongkan form seperti semula
     */
    resetForm() {
      this.cart.product_id = "";
      this.cart.qty = 1;
      this.product = {
        id: "",
        price: "",
        name: "",
        photo: "",
      };
      $("#product_id").val("");
      this.submitCart = false;
    },
  },
};
</script>

<style>
</style>
