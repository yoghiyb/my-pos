<template>
  <div class="col-md-12">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Edit Data</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item">
                <a href="#">Home</a>
              </li>
              <li class="breadcrumb-item">
                <router-link to="/product">Produk</router-link>
              </li>
              <li class="breadcrumb-item active">Edit</li>
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
                <form class="needs-validation" novalidate @submit="confirmProduct">
                  <div class="form-group">
                    <label for>Kode Produk</label>
                    <input
                      type="text"
                      name="code"
                      required
                      maxlength="10"
                      class="form-control"
                      v-model="product.code"
                    />
                    <div class="valid-feedback"></div>
                    <div class="invalid-feedback">Kode produk tidak valid!</div>
                  </div>
                  <div class="form-group">
                    <label for>Nama Produk</label>
                    <input
                      type="text"
                      name="name"
                      required
                      class="form-control"
                      v-model="product.name"
                    />
                    <div class="valid-feedback"></div>
                    <div class="invalid-feedback">Nama produk tidak valid!</div>
                  </div>
                  <div class="form-group">
                    <label for>Deskripsi</label>
                    <textarea
                      name="description"
                      id="description"
                      cols="5"
                      rows="5"
                      class="form-control"
                      v-model="product.description"
                    ></textarea>
                    <div class="valid-feedback"></div>
                    <div class="invalid-feedback">Deskripsi produk tidak valid!</div>
                  </div>
                  <div class="form-group">
                    <label for>Stok</label>
                    <input
                      type="number"
                      name="stock"
                      required
                      class="form-control"
                      v-model="product.stock"
                      min="1"
                    />
                    <div class="valid-feedback"></div>
                    <div class="invalid-feedback">Stok produk tidak valid!</div>
                  </div>
                  <div class="form-group">
                    <label for>Harga</label>
                    <input
                      type="number"
                      name="price"
                      required
                      class="form-control"
                      min="1"
                      v-model="product.price"
                    />
                    <div class="valid-feedback"></div>
                    <div class="invalid-feedback">Harga produk tidak valid!</div>
                  </div>
                  <div class="form-group">
                    <label for>Kategori</label>
                    <select
                      name="category_id"
                      id="category_id"
                      required
                      class="form-control"
                      v-model="product.category_id"
                    >
                      <option value>Pilih</option>
                      <option
                        v-for="(category, key) in categories"
                        :key="key"
                        :value="category.id"
                      >{{ category.name }}</option>
                    </select>
                    <div class="valid-feedback"></div>
                    <div class="invalid-feedback">Kategori produk tidak valid!</div>
                  </div>
                  <div class="form-group">
                    <label for>Foto</label>
                    <div class="row">
                      <div class="col-sm-3">
                        <img
                          :src="product.photo != null ? photoUrl : 'http://via.placeholder.com/200x200' "
                          alt="photo product"
                        />
                      </div>
                      <div class="col-sm-9">
                        <div class="custom-file">
                          <input
                            type="file"
                            class="custom-file-input"
                            name="photo"
                            accept=".jpg, .jpeg.png"
                            @change="handlePhoto"
                          />
                          <label
                            class="custom-file-label"
                            for="customFile"
                          >{{ product.photo == null ? 'Choose file...' : product.photo.name }}</label>
                        </div>
                        <div class="valid-feedback"></div>
                        <div class="invalid-feedback">Foto produk tidak valid!</div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <button class="btn btn-primary btn-sm" type="submit">
                      <i class="fas fa-sync"></i> Update
                    </button>
                  </div>
                </form>
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
      categories: [],
      product: {
        id: "",
        code: "",
        name: "",
        description: "",
        stock: 1,
        price: 1,
        category_id: "",
        photo: "",
      },
      photoUrl: "",
    };
  },
  created() {
    this.fetchProduct();
    this.fetchCategories();
  },
  methods: {
    async fetchProduct() {
      try {
        let endpoint = `${BASE_URL}/product/${this.$route.params.id}/edit`;
        let response = await axios.get(endpoint);

        if (response.data.status == "success") {
          this.product = response.data.data;
          this.photoUrl = "/uploads/product/" + this.product.photo;
        }
      } catch (error) {
        console.log(error);
      }
    },
    /**
     * Ambil data kategori
     */
    async fetchCategories() {
      try {
        let endpoint = `${BASE_URL}/categories`;
        let response = await axios.get(endpoint);

        if (response.data.status == "success") {
          this.categories = response.data.data;
        }
      } catch (error) {
        console.log(error);
      }
    },
    /**
     * Validasi form produk
     */
    confirmProduct(event) {
      event.preventDefault();
      if (
        this.product.code.trim() == "" ||
        this.product.name.trim() == "" ||
        this.product.stock < 1 ||
        this.product.price < 1 ||
        this.category_id == ""
      ) {
        event.target.classList.add("was-validated");
        return;
      }

      Swal.fire({
        title: "Apakah anda yakin?",
        text: "Anda akan menyimpan produk baru",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Update",
      }).then((result) => {
        if (result.value) {
          this.updateProduct();
        }
      });
    },
    /**
     * Memperbarui produk
     */
    async updateProduct() {
      try {
        let endpoint = `${BASE_URL}/product/${this.product.id}`;

        let formData = new FormData();

        let body = { ...this.product };

        if (typeof this.product.photo == "string") {
          body.photo = "";
        }

        Object.keys(body).forEach((key, index) => {
          formData.append(key, body[key]);
        });
        formData.append("_method", "PUT");
        // for (var pair of formData.entries()) {
        //   console.log(pair[0] + ", " + pair[1]);
        // }

        let response = await axios.post(endpoint, formData);

        if (response.data.status == "success") {
          this.fetchProduct();
          Swal.fire("Berhasil!", "Data berhasil disimpan.", "success");
        }
      } catch (error) {
        console.log(error);
        Swal.fire("Gagal!", "Data gagal disimpan.", "error");
      }
    },
    /**
     * untuk insert file gambar/foto
     */
    handlePhoto(e) {
      this.product.photo = e.target.files[0];
      this.photoUrl = URL.createObjectURL(e.target.files[0]);
    },
  },
};
</script>

<style scoped>
img {
  max-width: 270px;
}
</style>
